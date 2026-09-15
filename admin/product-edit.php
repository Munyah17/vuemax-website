<?php
require_once __DIR__ . '/inc.php';
require_admin();

if ($pdo === null) { header('Location: products.php'); exit; }

$id = (int)($_GET['id'] ?? $_POST['id'] ?? 0);
$isNew = !$id && isset($_GET['new']);

$flash = '';
$err = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    try {
        if (($_POST['act'] ?? '') === 'create') {
            $slug = preg_replace('/[^a-z0-9-]/', '', strtolower(trim($_POST['slug'] ?? '')));
            if (!$slug) $slug = 'product-' . time();
            $pdo->prepare('INSERT INTO products (subcategory_id, slug, name, unit) VALUES (?,?,?,?)')
                ->execute([(int)$_POST['subcategory_id'], $slug, trim($_POST['name'] ?? '') ?: 'New Product', 'roll']);
            header('Location: product-edit.php?id=' . $pdo->lastInsertId());
            exit;
        }
        $price = trim($_POST['price_usd'] ?? '');
        $pdo->prepare('UPDATE products SET subcategory_id=?, slug=?, name=?, short_desc=?, long_desc=?,
                       unit=?, price_usd=?, roll_metres=?, top_wire_rate=?, gate_price=?, install_rate=?,
                       image=?, badge=?, rating=?, reviews_count=?, sort_order=?, is_featured=?, is_active=?
                       WHERE id=?')->execute([
            (int)$_POST['subcategory_id'],
            preg_replace('/[^a-z0-9-]/', '', strtolower(trim($_POST['slug'] ?? ''))),
            trim($_POST['name'] ?? ''),
            trim($_POST['short_desc'] ?? ''),
            trim($_POST['long_desc'] ?? ''),
            trim($_POST['unit'] ?? 'roll') ?: 'roll',
            $price === '' ? null : (float)$price,
            $_POST['roll_metres']   === '' ? null : (float)$_POST['roll_metres'],
            $_POST['top_wire_rate'] === '' ? null : (float)$_POST['top_wire_rate'],
            $_POST['gate_price']    === '' ? null : (float)$_POST['gate_price'],
            $_POST['install_rate']  === '' ? null : (float)$_POST['install_rate'],
            trim($_POST['image'] ?? ''),
            trim($_POST['badge'] ?? '') ?: null,
            $_POST['rating'] === '' ? null : (float)$_POST['rating'],
            (int)($_POST['reviews_count'] ?? 0),
            (int)($_POST['sort_order'] ?? 0),
            isset($_POST['is_featured']) ? 1 : 0,
            isset($_POST['is_active']) ? 1 : 0,
            $id,
        ]);

        // Specs — one per line: "Label | Value"
        $pdo->prepare('DELETE FROM product_specs WHERE product_id=?')->execute([$id]);
        $lines = preg_split('/\r?\n/', trim($_POST['specs'] ?? ''));
        $i = 0;
        $stSpec = $pdo->prepare('INSERT INTO product_specs (product_id, label, value, sort_order) VALUES (?,?,?,?)');
        foreach ($lines as $ln) {
            $ln = trim($ln);
            if ($ln === '') continue;
            [$lbl, $val] = array_pad(explode('|', $ln, 2), 2, '');
            $stSpec->execute([$id, trim($lbl), trim($val), ++$i]);
        }

        // Features — one per line
        $pdo->prepare('DELETE FROM product_features WHERE product_id=?')->execute([$id]);
        $lines = preg_split('/\r?\n/', trim($_POST['features'] ?? ''));
        $i = 0;
        $stFeat = $pdo->prepare('INSERT INTO product_features (product_id, text, sort_order) VALUES (?,?,?)');
        foreach ($lines as $ln) {
            $ln = trim($ln);
            if ($ln === '') continue;
            $stFeat->execute([$id, $ln, ++$i]);
        }

        $flash = 'Saved.';
    } catch (Throwable $e) {
        $err = str_contains($e->getMessage(), 'Duplicate') ? 'Slug already in use by another product.' : 'Save failed.';
    }
}

$p = null; $specsTxt = ''; $featsTxt = ''; $subcats = [];
try {
    $st = $pdo->prepare('SELECT * FROM products WHERE id=?'); $st->execute([$id]);
    $p = $st->fetch();
    if ($p) {
        $st = $pdo->prepare('SELECT label, value FROM product_specs WHERE product_id=? ORDER BY sort_order'); $st->execute([$id]);
        foreach ($st->fetchAll() as $s) $specsTxt .= $s['label'] . ' | ' . $s['value'] . "\n";
        $st = $pdo->prepare('SELECT text FROM product_features WHERE product_id=? ORDER BY sort_order'); $st->execute([$id]);
        $featsTxt = implode("\n", array_column($st->fetchAll(), 'text'));
    }
    $subcats = $pdo->query('SELECT s.id, s.name, c.name AS cat FROM subcategories s JOIN categories c ON c.id=s.category_id ORDER BY c.id, s.id')->fetchAll();
} catch (Throwable $e) { $err = 'Database error.'; }
if (!$p && !$isNew) { header('Location: products.php'); exit; }

/* New-product mini form */
if ($isNew):
admin_head('New Product');
admin_nav('products');
?>
<h1 class="mt-4">New Product</h1>
<ol class="breadcrumb mb-4"><li class="breadcrumb-item"><a href="index.php">Dashboard</a></li><li class="breadcrumb-item"><a href="products.php">Products</a></li><li class="breadcrumb-item active">New</li></ol>
<div class="card mb-4" style="max-width:640px">
    <div class="card-body">
        <form method="post">
            <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
            <input type="hidden" name="act" value="create">
            <div class="mb-2"><label class="small text-muted">Name</label><input class="form-control" name="name" required></div>
            <div class="mb-2"><label class="small text-muted">Slug (URL — leave blank to auto-generate)</label><input class="form-control" name="slug"></div>
            <div class="mb-3"><label class="small text-muted">Category / subcategory</label>
                <select class="form-select" name="subcategory_id">
                <?php foreach ($subcats as $s): ?><option value="<?= (int)$s['id'] ?>"><?= e($s['cat']) ?> — <?= e($s['name']) ?></option><?php endforeach; ?>
                </select></div>
            <button class="btn btn-dark">Create &amp; Edit</button>
            <a class="btn btn-link" href="products.php">Cancel</a>
        </form>
    </div>
</div>
<?php
admin_footer();
exit;
endif;

admin_head('Edit: ' . $p['name']);
admin_nav('products');
?>
<h1 class="mt-4">Edit Product</h1>
<ol class="breadcrumb mb-4">
    <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="products.php">Products</a></li>
    <li class="breadcrumb-item active"><?= e($p['name']) ?></li>
</ol>

<?php if ($flash): ?><div class="alert alert-success py-2"><?= e($flash) ?></div><?php endif; ?>
<?php if ($err): ?><div class="alert alert-danger py-2"><?= e($err) ?></div><?php endif; ?>

<form method="post">
<input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
<input type="hidden" name="id" value="<?= $id ?>">

<div class="row">
    <div class="col-xl-8">
        <div class="card mb-4">
            <div class="card-header"><i class="fas fa-box me-1"></i> Product details</div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-8"><label class="small text-muted">Name</label><input class="form-control" name="name" value="<?= e($p['name']) ?>" required></div>
                    <div class="col-md-4"><label class="small text-muted">Slug (URL)</label><input class="form-control" name="slug" value="<?= e($p['slug']) ?>" required></div>
                    <div class="col-md-6"><label class="small text-muted">Category / subcategory</label>
                        <select class="form-select" name="subcategory_id">
                        <?php foreach ($subcats as $s): ?>
                            <option value="<?= (int)$s['id'] ?>" <?= $s['id'] == $p['subcategory_id'] ? 'selected' : '' ?>><?= e($s['cat']) ?> — <?= e($s['name']) ?></option>
                        <?php endforeach; ?>
                        </select></div>
                    <div class="col-md-3"><label class="small text-muted">Unit</label>
                        <select class="form-select" name="unit">
                        <?php foreach (['roll','piece','length','sheet','set','lot','litre','pack','metre','item'] as $u): ?>
                            <option value="<?= $u ?>" <?= $p['unit'] === $u ? 'selected' : '' ?>><?= $u ?></option>
                        <?php endforeach; ?>
                        </select></div>
                    <div class="col-md-3"><label class="small text-muted">Badge (optional)</label><input class="form-control" name="badge" value="<?= e($p['badge']) ?>" placeholder="e.g. Best Seller"></div>
                    <div class="col-12"><label class="small text-muted">Short description (cards)</label><input class="form-control" name="short_desc" value="<?= e($p['short_desc']) ?>"></div>
                    <div class="col-12"><label class="small text-muted">Long description (detail page)</label><textarea class="form-control" name="long_desc" rows="4"><?= e($p['long_desc']) ?></textarea></div>
                    <div class="col-12"><label class="small text-muted">Image path or URL</label><input class="form-control" name="image" value="<?= e($p['image']) ?>" placeholder="assets/img/products/… or https://…">
                        <div class="form-text">Also manageable via <a href="images.php">Images</a> — product card slots are named <code>prod-…</code>.</div></div>
                </div>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header"><i class="fas fa-list me-1"></i> Specifications &amp; features</div>
            <div class="card-body">
                <label class="small text-muted">Specs — one per line, <code>Label | Value</code> (e.g. <code>Height 1.8 m | $186</code>)</label>
                <textarea class="form-control mb-3 font-monospace small" name="specs" rows="9"><?= e($specsTxt) ?></textarea>
                <label class="small text-muted">Features — one per line</label>
                <textarea class="form-control small" name="features" rows="5"><?= e($featsTxt) ?></textarea>
            </div>
        </div>
    </div>

    <div class="col-xl-4">
        <div class="card mb-4">
            <div class="card-header"><i class="fas fa-tag me-1"></i> Pricing &amp; visibility</div>
            <div class="card-body">
                <div class="mb-2"><label class="small text-muted">Price (USD) — blank = supplied on request</label><input class="form-control" type="number" step="0.01" name="price_usd" value="<?= e($p['price_usd']) ?>"></div>
                <div class="mb-2"><label class="small text-muted">Sort order</label><input class="form-control" type="number" name="sort_order" value="<?= (int)$p['sort_order'] ?>"></div>
                <div class="row g-2 mb-3">
                    <div class="col-6"><label class="small text-muted">Rating</label><input class="form-control" type="number" step="0.1" max="5" name="rating" value="<?= e($p['rating']) ?>"></div>
                    <div class="col-6"><label class="small text-muted">Reviews</label><input class="form-control" type="number" name="reviews_count" value="<?= (int)$p['reviews_count'] ?>"></div>
                </div>
                <div class="form-check form-switch mb-2"><input class="form-check-input" type="checkbox" name="is_featured" id="feat" <?= $p['is_featured'] ? 'checked' : '' ?>><label class="form-check-label" for="feat">Featured on homepage</label></div>
                <div class="form-check form-switch"><input class="form-check-input" type="checkbox" name="is_active" id="act" <?= $p['is_active'] ? 'checked' : '' ?>><label class="form-check-label" for="act">Active (visible on site)</label></div>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header"><i class="fas fa-calculator me-1"></i> Quote-calculator rates <span class="small text-muted">(fencing only)</span></div>
            <div class="card-body">
                <div class="mb-2"><label class="small text-muted">Roll coverage (m)</label><input class="form-control" type="number" step="0.01" name="roll_metres" value="<?= e($p['roll_metres']) ?>"></div>
                <div class="mb-2"><label class="small text-muted">Top wire $/m</label><input class="form-control" type="number" step="0.01" name="top_wire_rate" value="<?= e($p['top_wire_rate']) ?>"></div>
                <div class="mb-2"><label class="small text-muted">Gate price $</label><input class="form-control" type="number" step="0.01" name="gate_price" value="<?= e($p['gate_price']) ?>"></div>
                <div class="mb-2"><label class="small text-muted">Install $/m</label><input class="form-control" type="number" step="0.01" name="install_rate" value="<?= e($p['install_rate']) ?>"></div>
            </div>
        </div>

        <button class="btn btn-dark w-100 mb-2">Save all changes</button>
        <a class="btn btn-outline-secondary w-100" href="../product-detail.php?slug=<?= urlencode($p['slug']) ?>" target="_blank">View on site</a>
    </div>
</div>
</form>
<?php
admin_footer();
