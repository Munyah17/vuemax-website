<?php
require_once __DIR__ . '/inc.php';
require_admin();

$rows  = [];
$noDb  = ($pdo === null);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $pdo) {
    csrf_check();
    if (($_POST['act'] ?? '') === 'toggle') {
        $pdo->prepare('UPDATE products SET is_active = 1 - is_active WHERE id=?')->execute([(int)$_POST['id']]);
    }
    header('Location: products.php');
    exit;
}

if ($pdo) {
    try {
        $rows = $pdo->query('
            SELECT p.id, p.name, p.slug, p.unit, p.price_usd, p.is_active, p.is_featured,
                   s.name AS subcat, c.name AS cat
            FROM products p
            JOIN subcategories s ON s.id = p.subcategory_id
            JOIN categories c ON c.id = s.category_id
            ORDER BY c.id, s.id, p.sort_order')->fetchAll();
    } catch (Throwable $e) { $noDb = true; }
}

admin_head('Products');
admin_nav('products');
?>
<h1 class="mt-4">Products</h1>
<ol class="breadcrumb mb-4">
    <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
    <li class="breadcrumb-item active">Products</li>
</ol>

<?php if ($noDb): ?>
<div class="alert alert-warning"><strong>Database not connected.</strong> Import <code>sql/schema.sql</code> first.</div>
<?php endif; ?>

<div class="card mb-4">
    <div class="card-header"><i class="fas fa-boxes me-1"></i> Catalog <span class="badge bg-secondary ms-2"><?= count($rows) ?></span>
        <a href="product-edit.php?new=1" class="btn btn-sm btn-dark float-end"><i class="fas fa-plus"></i> New product</a></div>
    <div class="card-body">
        <table id="datatablesSimple" class="table table-striped table-sm">
            <thead><tr><th style="width:1%">#</th><th>Product</th><th>Category</th><th>Subcategory</th><th style="width:1%" class="text-nowrap">Unit</th><th class="text-end text-nowrap" style="width:1%">Price (USD)</th><th style="width:1%">Status</th><th style="width:1%"></th></tr></thead>
            <tbody>
            <?php foreach ($rows as $r): ?>
                <tr class="<?= $r['is_active'] ? '' : 'table-secondary text-muted' ?>">
                    <td><?= (int)$r['id'] ?></td>
                    <td><?= e($r['name']) ?><?= $r['is_featured'] ? ' <span class="badge bg-warning text-dark">featured</span>' : '' ?></td>
                    <td><?= e($r['cat']) ?></td>
                    <td><?= e($r['subcat']) ?></td>
                    <td class="text-nowrap"><?= e($r['unit']) ?></td>
                    <td class="text-end text-nowrap"><?= $r['price_usd'] !== null ? usd($r['price_usd']) : '<em>on request</em>' ?></td>
                    <td class="text-nowrap"><?= $r['is_active'] ? '<span class="badge bg-success">active</span>' : '<span class="badge bg-secondary">hidden</span>' ?></td>
                    <td class="text-nowrap">
                        <a class="btn btn-sm btn-outline-dark" href="product-edit.php?id=<?= (int)$r['id'] ?>" title="Edit"><i class="fas fa-edit"></i></a>
                        <form method="post" class="d-inline">
                            <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
                            <input type="hidden" name="act" value="toggle">
                            <input type="hidden" name="id" value="<?= (int)$r['id'] ?>">
                            <button class="btn btn-sm btn-outline-<?= $r['is_active'] ? 'warning' : 'success' ?>" title="<?= $r['is_active'] ? 'Hide' : 'Show' ?>"><i class="fas fa-<?= $r['is_active'] ? 'eye-slash' : 'eye' ?>"></i></button>
                        </form>
                        <a class="btn btn-sm btn-outline-secondary" href="../product-detail.php?slug=<?= urlencode($r['slug']) ?>" target="_blank" title="View"><i class="fas fa-external-link-alt"></i></a>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php
admin_footer(<<<'HTML'
<script src="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/umd/simple-datatables.min.js" crossorigin="anonymous"></script>
<script>
window.addEventListener('DOMContentLoaded', () => {
    const t = document.getElementById('datatablesSimple');
    if (t) new simpleDatatables.DataTable(t);
});
</script>
HTML);

