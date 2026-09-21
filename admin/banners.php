<?php
require_once __DIR__ . '/inc.php';
require_admin();

/* ---------- Banner Manager ----------
   Manages the homepage hero slider (max 5 slides).
   Each slide: eyebrow, title, accent line, description, background image.
   Images upload to assets/img/uploads/ or can be an external URL. */

$MAX_SLIDES = 5;
$flash = '';

function banner_redirect($qs) { header('Location: banners.php' . $qs); exit; }

/* ---------- Handle POST actions ---------- */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    if (!$pdo) banner_redirect('?err=db');
    $action = $_POST['action'] ?? '';

    /* ---- delete ---- */
    if ($action === 'delete') {
        $id = (int)($_POST['id'] ?? 0);
        $st = $pdo->prepare('SELECT image FROM banners WHERE id = ?');
        $st->execute([$id]);
        $img = $st->fetchColumn();
        $pdo->prepare('DELETE FROM banners WHERE id = ?')->execute([$id]);
        if ($img && strpos($img, 'assets/img/uploads/banner-') === 0) {
            @unlink(__DIR__ . '/../' . $img);
        }
        banner_redirect('?ok=deleted');
    }

    /* ---- toggle active ---- */
    if ($action === 'toggle') {
        $id = (int)($_POST['id'] ?? 0);
        $pdo->prepare('UPDATE banners SET is_active = 1 - is_active WHERE id = ?')->execute([$id]);
        banner_redirect('?ok=toggled');
    }

    /* ---- save (insert or update) ---- */
    if ($action === 'save') {
        $id      = (int)($_POST['id'] ?? 0);
        $eyebrow = trim($_POST['eyebrow'] ?? '');
        $title   = trim($_POST['title'] ?? '');
        $accent  = trim($_POST['accent'] ?? '');
        $desc    = trim($_POST['description'] ?? '');
        $imgUrl  = trim($_POST['image_url'] ?? '');
        $sort    = (int)($_POST['sort_order'] ?? 0);
        $active  = isset($_POST['is_active']) ? 1 : 0;

        if ($title === '' || $desc === '') banner_redirect('?err=required');

        // enforce slide cap on new banners
        if (!$id) {
            $count = (int)$pdo->query('SELECT COUNT(*) FROM banners')->fetchColumn();
            if ($count >= $MAX_SLIDES) banner_redirect('?err=max');
        }

        // image: uploaded file wins, else pasted URL, else keep existing
        $image = null;
        if (!empty($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $f = $_FILES['image'];
            if ($f['size'] > 4 * 1024 * 1024) banner_redirect('?err=toobig');
            $info = @getimagesize($f['tmp_name']);
            $allowed = [IMAGETYPE_JPEG => 'jpg', IMAGETYPE_PNG => 'png', IMAGETYPE_WEBP => 'webp', IMAGETYPE_GIF => 'gif'];
            if (!$info || !isset($allowed[$info[2]])) banner_redirect('?err=type');
            $dir = __DIR__ . '/../assets/img/uploads';
            if (!is_dir($dir)) mkdir($dir, 0755, true);
            $fname = 'banner-' . ($id ?: time()) . '.' . $allowed[$info[2]];
            if (!move_uploaded_file($f['tmp_name'], $dir . '/' . $fname)) banner_redirect('?err=move');
            $image = 'assets/img/uploads/' . $fname;
        } elseif ($imgUrl !== '') {
            $image = $imgUrl;
        }

        if ($id) {
            if ($image !== null) {
                $st = $pdo->prepare('UPDATE banners SET eyebrow=?, title=?, accent=?, description=?, image=?, sort_order=?, is_active=? WHERE id=?');
                $st->execute([$eyebrow, $title, $accent, $desc, $image, $sort, $active, $id]);
            } else {
                $st = $pdo->prepare('UPDATE banners SET eyebrow=?, title=?, accent=?, description=?, sort_order=?, is_active=? WHERE id=?');
                $st->execute([$eyebrow, $title, $accent, $desc, $sort, $active, $id]);
            }
        } else {
            if ($image === null) banner_redirect('?err=noimg');
            $st = $pdo->prepare('INSERT INTO banners (eyebrow, title, accent, description, image, sort_order, is_active) VALUES (?,?,?,?,?,?,?)');
            $st->execute([$eyebrow, $title, $accent, $desc, $image, $sort, $active]);
        }
        banner_redirect('?ok=saved');
    }
    banner_redirect('');
}

/* ---------- Load banners ---------- */
$banners = [];
$noDb = ($pdo === null);
$tableMissing = false;
if ($pdo) {
    try {
        $banners = $pdo->query('SELECT * FROM banners ORDER BY sort_order, id')->fetchAll();
    } catch (Throwable $e) { $tableMissing = true; }
}
$edit = null;
if (isset($_GET['edit'])) {
    foreach ($banners as $b) { if ((int)$b['id'] === (int)$_GET['edit']) $edit = $b; }
}
if (isset($_GET['ok']))  $flash = 'ok:' . $_GET['ok'];
if (isset($_GET['err'])) $flash = 'err:' . $_GET['err'];

admin_head('Banner Manager');
admin_nav('banners');
?>
<h1 class="mt-4">Banner Manager</h1>
<ol class="breadcrumb mb-4">
    <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
    <li class="breadcrumb-item active">Banner Manager</li>
</ol>

<?php if ($noDb): ?>
<div class="alert alert-warning"><strong>Database not connected.</strong> Set credentials in <code>includes/config.php</code>.</div>
<?php elseif ($tableMissing): ?>
<div class="alert alert-warning">
    <strong>The <code>banners</code> table doesn't exist yet.</strong> Run this in phpMyAdmin → your database → SQL:
    <pre class="mt-2 mb-0 p-2 bg-white border rounded small">CREATE TABLE `banners` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `eyebrow` VARCHAR(120) DEFAULT NULL,
  `title` VARCHAR(200) NOT NULL,
  `accent` VARCHAR(200) DEFAULT NULL,
  `description` TEXT NOT NULL,
  `image` VARCHAR(255) NOT NULL,
  `sort_order` INT NOT NULL DEFAULT 0,
  `is_active` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;</pre>
</div>
<?php endif; ?>

<?php if ($flash):
    [$kind, $val] = explode(':', $flash, 2);
    if ($kind === 'ok'):
        $okMsgs = ['saved' => 'Banner saved.', 'deleted' => 'Banner deleted.', 'toggled' => 'Banner status updated.']; ?>
    <div class="alert alert-success py-2"><?= e($okMsgs[$val] ?? 'Done.') ?></div>
<?php else:
    $msgs = ['db' => 'Database error', 'required' => 'Title and description are required', 'max' => 'Maximum of 5 slides reached — delete one first', 'noimg' => 'An image (upload or URL) is required', 'nofile' => 'No file received', 'toobig' => 'File too large (max 4 MB)', 'type' => 'Only JPG, PNG, WebP or GIF allowed', 'move' => 'Could not save the file']; ?>
    <div class="alert alert-danger py-2"><?= e($msgs[$val] ?? 'Error') ?></div>
<?php endif; ?>
<?php endif; ?>

<div class="alert alert-secondary py-2 small mb-4">
    These banners power the homepage hero slider — up to <strong><?= $MAX_SLIDES ?> slides</strong>, shown in sort order. Each slide needs a background image, title and description. Recommended image size: 1600×900 or larger.
</div>

<div class="row">
    <!-- ============ ADD / EDIT FORM ============ -->
    <div class="col-lg-4">
        <div class="card mb-4">
            <div class="card-header"><i class="fas fa-<?= $edit ? 'edit' : 'plus' ?> me-1"></i> <?= $edit ? 'Edit Slide #' . (int)$edit['id'] : 'Add Slide' ?></div>
            <div class="card-body">
                <form method="post" action="banners.php" enctype="multipart/form-data">
                    <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
                    <input type="hidden" name="action" value="save">
                    <input type="hidden" name="id" value="<?= $edit ? (int)$edit['id'] : 0 ?>">

                    <div class="mb-2">
                        <label class="form-label small fw-semibold">Eyebrow <span class="text-muted">(small label, optional)</span></label>
                        <input type="text" name="eyebrow" class="form-control form-control-sm" maxlength="120"
                               value="<?= e($edit['eyebrow'] ?? '') ?>" placeholder="Zimbabwe's Trusted Partner">
                    </div>
                    <div class="mb-2">
                        <label class="form-label small fw-semibold">Title *</label>
                        <input type="text" name="title" class="form-control form-control-sm" maxlength="200" required
                               value="<?= e($edit['title'] ?? '') ?>" placeholder="Smarter Fencing Quotes.">
                    </div>
                    <div class="mb-2">
                        <label class="form-label small fw-semibold">Accent line <span class="text-muted">(amber second line, optional)</span></label>
                        <input type="text" name="accent" class="form-control form-control-sm" maxlength="200"
                               value="<?= e($edit['accent'] ?? '') ?>" placeholder="Faster Decisions.">
                    </div>
                    <div class="mb-2">
                        <label class="form-label small fw-semibold">Description *</label>
                        <textarea name="description" class="form-control form-control-sm" rows="3" required
                                  placeholder="Accurate bills of quantities and cost estimates in minutes…"><?= e($edit['description'] ?? '') ?></textarea>
                    </div>
                    <div class="mb-2">
                        <label class="form-label small fw-semibold">Background image <?= $edit ? '(leave blank to keep current)' : '*' ?></label>
                        <input type="file" name="image" accept="image/*" class="form-control form-control-sm mb-1">
                        <input type="url" name="image_url" class="form-control form-control-sm"
                               value="<?= e($edit && strpos($edit['image'], 'http') === 0 ? $edit['image'] : '') ?>"
                               placeholder="…or paste an image URL">
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label small fw-semibold">Sort order</label>
                            <input type="number" name="sort_order" class="form-control form-control-sm" min="0"
                                   value="<?= e($edit['sort_order'] ?? count($banners) + 1) ?>">
                        </div>
                        <div class="col-6 d-flex align-items-end">
                            <div class="form-check">
                                <input type="checkbox" name="is_active" class="form-check-input" id="bActive"
                                       <?= !$edit || $edit['is_active'] ? 'checked' : '' ?>>
                                <label class="form-check-label small" for="bActive">Active (show on site)</label>
                            </div>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-dark btn-sm w-100"><?= $edit ? 'Save Changes' : 'Add Slide' ?></button>
                    <?php if ($edit): ?><a href="banners.php" class="btn btn-outline-secondary btn-sm w-100 mt-2">Cancel edit</a><?php endif; ?>
                </form>
            </div>
        </div>
    </div>

    <!-- ============ EXISTING SLIDES ============ -->
    <div class="col-lg-8">
        <div class="card mb-4">
            <div class="card-header"><i class="fas fa-images me-1"></i> Slides (<?= count($banners) ?>/<?= $MAX_SLIDES ?>)</div>
            <div class="card-body">
                <?php if (!$banners): ?>
                    <p class="text-muted mb-0">No banners yet — the homepage shows the default hero until you add one.</p>
                <?php endif; ?>
                <div class="row g-3">
                <?php foreach ($banners as $b):
                    $thumb = $b['image'];
                    if ($thumb && strpos($thumb, 'http') !== 0) $thumb = '../' . ltrim($thumb, '/');
                ?>
                    <div class="col-md-6">
                        <div class="card h-100 border <?= $b['is_active'] ? '' : 'opacity-50' ?>">
                            <div class="card-img-top" style="height:130px;background:#0E2745 center/cover no-repeat<?= $thumb ? ';background-image:url(\'' . e($thumb) . '\')' : '' ?>"></div>
                            <div class="card-body p-3">
                                <div class="d-flex justify-content-between align-items-start mb-1">
                                    <strong class="small"><?= e($b['title']) ?></strong>
                                    <span class="badge bg-<?= $b['is_active'] ? 'success' : 'secondary' ?>"><?= $b['is_active'] ? 'active' : 'hidden' ?></span>
                                </div>
                                <?php if ($b['accent']): ?><div class="small text-warning"><?= e($b['accent']) ?></div><?php endif; ?>
                                <div class="text-muted" style="font-size:12px;"><?= e(mb_strimwidth($b['description'], 0, 90, '…')) ?></div>
                                <div class="text-muted mt-1" style="font-size:11px;">Order: <?= (int)$b['sort_order'] ?></div>
                                <div class="d-flex gap-1 mt-2">
                                    <a href="banners.php?edit=<?= (int)$b['id'] ?>" class="btn btn-outline-dark btn-sm"><i class="fas fa-edit"></i> Edit</a>
                                    <form method="post" action="banners.php" class="d-inline">
                                        <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
                                        <input type="hidden" name="action" value="toggle">
                                        <input type="hidden" name="id" value="<?= (int)$b['id'] ?>">
                                        <button class="btn btn-outline-secondary btn-sm"><?= $b['is_active'] ? 'Hide' : 'Show' ?></button>
                                    </form>
                                    <form method="post" action="banners.php" class="d-inline" onsubmit="return confirm('Delete this slide?');">
                                        <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="id" value="<?= (int)$b['id'] ?>">
                                        <button class="btn btn-outline-danger btn-sm"><i class="fas fa-trash"></i></button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
admin_footer();
