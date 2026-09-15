<?php
require_once __DIR__ . '/inc.php';
require_admin();

$noDb = ($pdo === null);

$nProducts = admin_table_count($pdo, 'products');
$nImages   = admin_table_count($pdo, 'site_images');
$nQuotes   = admin_table_count($pdo, 'quotes');
$nMsgs     = admin_table_count($pdo, 'contacts');
$nNewMsgs  = null;
$recent    = [];
if ($pdo) {
    try { $nNewMsgs = (int)$pdo->query("SELECT COUNT(*) FROM contacts WHERE status='new'")->fetchColumn(); } catch (Throwable $e) {}
    try { $recent = $pdo->query('SELECT ref, customer_name, fence_type, total_usd, created_at FROM quotes ORDER BY id DESC LIMIT 8')->fetchAll(); } catch (Throwable $e) {}
}

admin_head('Dashboard');
admin_nav('dash');
?>
<h1 class="mt-4">Dashboard</h1>
<ol class="breadcrumb mb-4">
    <li class="breadcrumb-item active">Dashboard</li>
</ol>

<?php if ($noDb): ?>
<div class="alert alert-warning">
    <strong>Database not connected.</strong> Set the credentials in <code>includes/config.php</code>
    and import <code>sql/schema.sql</code> via phpMyAdmin. Management features require MySQL.
</div>
<?php endif; ?>

<div class="row">
    <?= admin_stat_card('bg-primary', 'Products',   $nProducts === null ? '—' : $nProducts, 'fas fa-boxes', 'products.php', 'View products') ?>
    <?= admin_stat_card('bg-success', 'Quotes',     $nQuotes === null ? '—' : $nQuotes,     'fas fa-file-invoice-dollar', 'quotes.php', 'View quotes') ?>
    <?= admin_stat_card('bg-warning', 'Messages',   $nMsgs === null ? '—' : $nMsgs . ($nNewMsgs ? ' (' . $nNewMsgs . ' new)' : ''), 'fas fa-envelope', 'messages.php', 'View messages') ?>
    <?= admin_stat_card('bg-dark',    'Image Slots',$nImages === null ? '—' : $nImages,     'fas fa-images', 'images.php', 'Manage images') ?>
</div>

<div class="row">
    <div class="col-xl-6">
        <div class="card mb-4">
            <div class="card-header"><i class="fas fa-bolt me-1"></i> Quick Actions</div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a class="btn btn-outline-dark" href="images.php"><i class="fas fa-images me-2"></i>Replace a site image</a>
                    <a class="btn btn-outline-dark" href="products.php"><i class="fas fa-boxes me-2"></i>Review product catalog</a>
                    <a class="btn btn-outline-dark" href="../estimator.php" target="_blank"><i class="fas fa-robot me-2"></i>Test the AI estimator</a>
                    <a class="btn btn-outline-dark" href="../index.php" target="_blank"><i class="fas fa-external-link-alt me-2"></i>Open public site</a>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-6">
        <div class="card mb-4">
            <div class="card-header"><i class="fas fa-info-circle me-1"></i> System</div>
            <div class="card-body">
                <table class="table table-sm table-borderless mb-0">
                    <tr><td class="text-muted">Database</td><td><?= $noDb ? '<span class="badge bg-danger">not connected</span>' : '<span class="badge bg-success">connected</span>' ?></td></tr>
                    <tr><td class="text-muted">PHP</td><td><?= PHP_VERSION ?></td></tr>
                    <tr><td class="text-muted">Estimator engine</td><td>Groq (gpt-oss-120b) with rules fallback</td></tr>
                    <tr><td class="text-muted">Image uploads dir</td><td><code>assets/img/uploads/</code></td></tr>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="card mb-4">
    <div class="card-header"><i class="fas fa-table me-1"></i> Latest Quotes</div>
    <div class="card-body">
        <?php if (!$recent): ?>
            <p class="text-muted mb-0">No quotes yet — they appear here once the calculator/estimator saves to MySQL.</p>
        <?php else: ?>
        <table class="table table-striped table-sm mb-0">
            <thead><tr><th>Ref</th><th>Customer</th><th>Fence Type</th><th class="text-end">Total</th><th>Date</th></tr></thead>
            <tbody>
            <?php foreach ($recent as $q): ?>
                <tr>
                    <td><code><?= e($q['ref']) ?></code></td>
                    <td><?= e($q['customer_name'] ?: '—') ?></td>
                    <td><?= e($q['fence_type'] ?: '—') ?></td>
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
admin_footer();
