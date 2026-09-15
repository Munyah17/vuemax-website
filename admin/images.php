<?php
require_once __DIR__ . '/inc.php';
require_admin();

$images = [];
$noDb   = ($pdo === null);
if ($pdo) {
    try {
        $images = $pdo->query('SELECT img_key, label, page, path, updated_at FROM site_images ORDER BY page, img_key')->fetchAll();
    } catch (Throwable $e) { $noDb = true; }
}

$groups = [];
foreach ($images as $im) { $groups[$im['page'] ?: 'Other'][] = $im; }

$flash = '';
if (isset($_GET['ok']))  $flash = 'ok:' . $_GET['ok'];
if (isset($_GET['err'])) $flash = 'err:' . $_GET['err'];

admin_head('Image Manager');
admin_nav('images');
?>
<h1 class="mt-4">Image Manager</h1>
<ol class="breadcrumb mb-4">
    <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
    <li class="breadcrumb-item active">Image Manager</li>
</ol>

<?php if ($noDb): ?>
<div class="alert alert-warning"><strong>Database not connected.</strong> Import <code>sql/schema.sql</code> and set credentials in <code>includes/config.php</code>.</div>
<?php endif; ?>

<?php if ($flash):
    [$kind, $val] = explode(':', $flash, 2);
    if ($kind === 'ok'): ?>
    <div class="alert alert-success py-2">Image updated: <strong><?= e($val) ?></strong></div>
<?php else:
    $msgs = ['db' => 'Database error', 'nofile' => 'No file received', 'toobig' => 'File too large (max 4 MB)', 'type' => 'Only JPG, PNG, WebP or GIF allowed', 'move' => 'Could not save the file']; ?>
    <div class="alert alert-danger py-2"><?= e($msgs[$val] ?? 'Upload failed') ?></div>
<?php endif; ?>
<?php endif; ?>

<div class="alert alert-secondary py-2 small mb-4">
    Upload a replacement for any slot below — JPG, PNG, WebP or GIF, max 4 MB. Changes apply site-wide immediately.
</div>

<?php foreach ($groups as $page => $items): ?>
<div class="card mb-4">
    <div class="card-header"><i class="fas fa-folder-open me-1"></i> <?= e($page) ?></div>
    <div class="card-body">
        <div class="row g-3">
        <?php foreach ($items as $im):
            $isUpload = $im['path'] && strpos($im['path'], 'assets/img/uploads/') === 0;
            $thumb = $im['path'] ? '../' . ltrim($im['path'], '/') : '';
            if ($im['path'] && strpos($im['path'], 'http') === 0) $thumb = $im['path'];
        ?>
            <div class="col-xl-3 col-md-4 col-sm-6" id="<?= e($im['img_key']) ?>">
                <div class="card h-100 border">
                    <div class="card-img-top" style="height:110px;background:#e9ecef center/cover no-repeat<?= $thumb ? ';background-image:url(\'' . e($thumb) . '\')' : '' ?>"></div>
                    <div class="card-body p-2">
                        <div class="small fw-semibold"><?= e($im['label']) ?></div>
                        <div class="text-muted" style="font-size:11px;font-family:Consolas,monospace"><?= e($im['img_key']) ?><?= $isUpload ? ' · custom' : '' ?></div>
                        <form method="post" action="upload.php" enctype="multipart/form-data" class="mt-2 d-flex gap-1">
                            <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
                            <input type="hidden" name="key" value="<?= e($im['img_key']) ?>">
                            <input type="file" name="image" accept="image/*" required class="form-control form-control-sm" style="font-size:11px">
                            <button type="submit" class="btn btn-dark btn-sm">Replace</button>
                        </form>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
        </div>
    </div>
</div>
<?php endforeach; ?>
<?php
admin_footer();
