<?php
require_once __DIR__ . '/inc.php';
require_admin();

$rows = [];
$noDb = ($pdo === null);
if ($pdo) {
    try {
        $rows = $pdo->query('SELECT id, ref, source, customer_name, customer_phone, fence_type, perimeter, total_usd, created_at FROM quotes ORDER BY id DESC')->fetchAll();
    } catch (Throwable $e) { $noDb = true; }
}

admin_head('Quotes');
admin_nav('quotes');
?>
<h1 class="mt-4">Quotes</h1>
<ol class="breadcrumb mb-4">
    <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
    <li class="breadcrumb-item active">Quotes</li>
</ol>

<?php if ($noDb): ?>
<div class="alert alert-warning"><strong>Database not connected.</strong> Import <code>sql/schema.sql</code> first.</div>
<?php endif; ?>

<div class="card mb-4">
    <div class="card-header"><i class="fas fa-file-invoice-dollar me-1"></i> Saved quotes <span class="badge bg-secondary ms-2"><?= count($rows) ?></span></div>
    <div class="card-body">
        <?php if (!$rows): ?>
            <p class="text-muted mb-0">No quotes saved yet.</p>
        <?php else: ?>
        <table id="datatablesSimple" class="table table-striped table-sm">
            <thead><tr><th>Ref</th><th>Source</th><th>Customer</th><th>Phone</th><th>Fence</th><th class="text-end">Perim.</th><th class="text-end">Total</th><th>Date</th></tr></thead>
            <tbody>
            <?php foreach ($rows as $q): ?>
                <tr>
                    <td><code><?= e($q['ref']) ?></code></td>
                    <td><span class="badge bg-<?= $q['source'] === 'estimator' ? 'info' : 'secondary' ?>"><?= e($q['source']) ?></span></td>
                    <td><?= e($q['customer_name'] ?: '—') ?></td>
                    <td><?= e($q['customer_phone'] ?: '—') ?></td>
                    <td><?= e($q['fence_type'] ?: '—') ?></td>
                    <td class="text-end"><?= $q['perimeter'] !== null ? (float)$q['perimeter'] . ' m' : '—' ?></td>
                    <td class="text-end"><?= $q['total_usd'] !== null ? usd($q['total_usd']) : '—' ?></td>
                    <td><?= e(substr($q['created_at'], 0, 10)) ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        <?php endif; ?>
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
