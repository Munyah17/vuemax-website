<?php
require_once __DIR__ . '/inc.php';
require_admin();

/* ---------- Module scaffold ----------
   Sidebar links for modules that aren't built yet land here.
   Each entry: [Title, icon, short description of what it will do]. */
$MODULES = [
    'analytics'     => ['Visitor Analytics', 'fas fa-chart-line', 'Page views, traffic sources and popular products across the site.'],
    'tasks'         => ['Tasks & Reminders', 'fas fa-tasks', 'Assign follow-ups, set due dates and track reminders for the team.'],
    'notifications' => ['Notifications', 'fas fa-bell', 'System alerts — new quotes, orders, messages and low-stock warnings.'],
    'pos'           => ['Point of Sale', 'fas fa-cash-register', 'Walk-in sales counter. Admins only — sell stock, take payment, print a receipt (ESC/POS / XPrinter supported).'],
    'invoicing'     => ['Invoicing', 'fas fa-file-invoice', 'Create and send invoices, track payment status and due dates.'],
    'quotations'    => ['Quotations', 'fas fa-file-signature', 'Formal quotations issued to clients — convert accepted quotes to orders.'],
    'receipting'    => ['Receipting & Receipt Printing', 'fas fa-receipt', 'Print receipts for POS and online sales via ESC/POS, XPrinter and standard printer drivers.'],
    'payments'      => ['Payment Methods', 'fas fa-credit-card', 'Configure payment channels — Paynow (Zimbabwe), EcoCash, bank transfer, cash on delivery.'],
    'bom'           => ['BOM / BOQ', 'fas fa-clipboard-list', 'Bills of materials and bills of quantities generated from quotes and projects.'],
    'requisitions'  => ['Requisitions', 'fas fa-clipboard-check', 'Internal stock and purchase requests from staff.'],
    'purchases'     => ['Purchases', 'fas fa-shopping-cart', 'Supplier purchase orders and stock receiving.'],
    'clients'       => ['Clients Database', 'fas fa-address-book', 'Marketing database — client contacts, segments and campaign lists.'],
    'staff'         => ['Staff Management', 'fas fa-id-badge', 'Staff records, roles and assignments.'],
    'hr'            => ['HR & Payroll', 'fas fa-user-tie', 'Leave, attendance, payroll runs and payslips.'],
    'branding'      => ['Branding', 'fas fa-palette', 'Logos, banners, contact details and branch information shown across the site.'],
    'revenue'       => ['Revenue', 'fas fa-chart-pie', 'Revenue dashboards — sales by period, product and channel.'],
    'ledger'        => ['Creditors & Debtors', 'fas fa-balance-scale', 'Who you owe and who owes you — supplier balances and customer accounts.'],
    'reports'       => ['Reports', 'fas fa-file-alt', 'Sales, stock and customer reports with export.'],
    'settings'      => ['Settings', 'fas fa-sliders-h', 'General site settings — contact details, delivery fees, quote validity.'],
    'config'        => ['Configurations', 'fas fa-tools', 'Technical configuration — payment gateway keys, email, integrations.'],
];

$m = preg_replace('/[^a-z0-9\-]/', '', strtolower($_GET['m'] ?? ''));
$mod = $MODULES[$m] ?? null;

/* ---------- Visitor Analytics — real implementation ---------- */
if ($m === 'analytics') {
    admin_head('Visitor Analytics');
    admin_nav('mod-analytics');

    $hasTable = false;
    $stats = ['today'=>0,'week'=>0,'month'=>0,'total'=>0,'uniq_week'=>0];
    $daily = $topPages = $topRefs = $topProducts = $recent = [];
    if ($pdo) {
        try {
            $pdo->query('SELECT 1 FROM page_views LIMIT 1');
            $hasTable = true;
            $stats['today']     = (int)$pdo->query("SELECT COUNT(*) FROM page_views WHERE created_at >= CURDATE()")->fetchColumn();
            $stats['week']      = (int)$pdo->query("SELECT COUNT(*) FROM page_views WHERE created_at >= CURDATE() - INTERVAL 7 DAY")->fetchColumn();
            $stats['month']     = (int)$pdo->query("SELECT COUNT(*) FROM page_views WHERE created_at >= CURDATE() - INTERVAL 30 DAY")->fetchColumn();
            $stats['total']     = (int)$pdo->query("SELECT COUNT(*) FROM page_views")->fetchColumn();
            $stats['uniq_week'] = (int)$pdo->query("SELECT COUNT(DISTINCT visitor) FROM page_views WHERE created_at >= CURDATE() - INTERVAL 7 DAY")->fetchColumn();
            $daily = $pdo->query("SELECT DATE(created_at) d, COUNT(*) n FROM page_views WHERE created_at >= CURDATE() - INTERVAL 13 DAY GROUP BY DATE(created_at) ORDER BY d")->fetchAll();
            $topPages = $pdo->query("SELECT path, COUNT(*) n FROM page_views GROUP BY path ORDER BY n DESC LIMIT 10")->fetchAll();
            $topRefs  = $pdo->query("SELECT referrer, COUNT(*) n FROM page_views WHERE referrer IS NOT NULL AND referrer <> '' GROUP BY referrer ORDER BY n DESC LIMIT 8")->fetchAll();
            $topProducts = $pdo->query("SELECT product_slug, COUNT(*) n FROM page_views WHERE product_slug IS NOT NULL AND product_slug <> '' GROUP BY product_slug ORDER BY n DESC LIMIT 8")->fetchAll();
            $recent = $pdo->query("SELECT path, device, referrer, created_at FROM page_views ORDER BY id DESC LIMIT 15")->fetchAll();
        } catch (Throwable $e) { $hasTable = false; }
    }
    $maxDay = 1;
    foreach ($daily as $d) { $maxDay = max($maxDay, (int)$d['n']); }
    ?>
<h1 class="mt-4">Visitor Analytics</h1>
<ol class="breadcrumb mb-4">
    <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
    <li class="breadcrumb-item active">Visitor Analytics</li>
</ol>

<?php if (!$hasTable): ?>
<div class="alert alert-warning">
    <strong>Analytics table missing.</strong> Run this in phpMyAdmin on <code>vuemaxco_db</code>, then reload:
    <pre class="mt-2 mb-0 small">CREATE TABLE `page_views` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `path` VARCHAR(255) NOT NULL,
  `title` VARCHAR(200) DEFAULT NULL,
  `referrer` VARCHAR(255) DEFAULT NULL,
  `product_slug` VARCHAR(120) DEFAULT NULL,
  `device` VARCHAR(20) NOT NULL DEFAULT 'desktop',
  `visitor` CHAR(64) NOT NULL,
  `user_agent` VARCHAR(255) DEFAULT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_pv_created` (`created_at`),
  KEY `idx_pv_path` (`path`),
  KEY `idx_pv_visitor` (`visitor`, `created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;</pre>
</div>
<?php else: ?>

<div class="row">
    <?= admin_stat_card('bg-primary',   'Views today',        number_format($stats['today']),     'fas fa-eye') ?>
    <?= admin_stat_card('bg-success',   'Views — 7 days',     number_format($stats['week']),      'fas fa-chart-line') ?>
    <?= admin_stat_card('bg-warning',   'Unique visitors — 7d', number_format($stats['uniq_week']), 'fas fa-users') ?>
    <?= admin_stat_card('bg-dark',      'All-time views',     number_format($stats['total']),     'fas fa-database') ?>
</div>

<div class="row">
    <div class="col-xl-8">
        <div class="card mb-4">
            <div class="card-header"><i class="fas fa-chart-bar me-1"></i> Daily views — last 14 days</div>
            <div class="card-body">
                <?php if (!$daily): ?><p class="text-muted mb-0">No traffic recorded yet — views appear here as visitors browse the site.</p><?php else: ?>
                <div class="d-flex align-items-end gap-1" style="height:160px;">
                    <?php foreach ($daily as $d): $h = max(4, round(((int)$d['n'] / $maxDay) * 100)); ?>
                    <div class="flex-fill text-center" title="<?= e($d['d']) ?> — <?= (int)$d['n'] ?> views">
                        <div class="small text-muted"><?= (int)$d['n'] ?></div>
                        <div class="bg-primary rounded-top mx-auto" style="height:<?= $h ?>%;min-height:4px;width:70%;"></div>
                        <div class="small text-muted" style="font-size:.65rem;"><?= e(date('d M', strtotime($d['d']))) ?></div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header"><i class="fas fa-file me-1"></i> Top pages</div>
            <div class="card-body p-0">
                <table class="table table-sm mb-0">
                    <thead><tr><th class="ps-3">Path</th><th class="text-end pe-3">Views</th></tr></thead>
                    <tbody>
                    <?php foreach ($topPages as $p): ?>
                        <tr><td class="ps-3"><code><?= e($p['path']) ?></code></td><td class="text-end pe-3"><?= (int)$p['n'] ?></td></tr>
                    <?php endforeach; ?>
                    <?php if (!$topPages): ?><tr><td class="ps-3 text-muted">No data yet.</td></tr><?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-xl-4">
        <div class="card mb-4">
            <div class="card-header"><i class="fas fa-box me-1"></i> Most-viewed products</div>
            <div class="card-body p-0">
                <table class="table table-sm mb-0">
                    <tbody>
                    <?php foreach ($topProducts as $p): ?>
                        <tr><td class="ps-3"><a href="../product-detail.php?slug=<?= urlencode($p['product_slug']) ?>" target="_blank"><?= e($p['product_slug']) ?></a></td><td class="text-end pe-3"><?= (int)$p['n'] ?></td></tr>
                    <?php endforeach; ?>
                    <?php if (!$topProducts): ?><tr><td class="ps-3 text-muted">No product views yet.</td></tr><?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header"><i class="fas fa-external-link-alt me-1"></i> Traffic sources</div>
            <div class="card-body p-0">
                <table class="table table-sm mb-0">
                    <tbody>
                    <?php foreach ($topRefs as $r): ?>
                        <tr><td class="ps-3 text-truncate" style="max-width:220px;"><?= e(parse_url($r['referrer'], PHP_URL_HOST) ?: $r['referrer']) ?></td><td class="text-end pe-3"><?= (int)$r['n'] ?></td></tr>
                    <?php endforeach; ?>
                    <?php if (!$topRefs): ?><tr><td class="ps-3 text-muted">No external referrers yet.</td></tr><?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="card mb-4">
    <div class="card-header"><i class="fas fa-clock me-1"></i> Recent visits</div>
    <div class="card-body p-0">
        <table class="table table-sm table-striped mb-0">
            <thead><tr><th class="ps-3">Time</th><th>Page</th><th>Device</th><th>Came from</th></tr></thead>
            <tbody>
            <?php foreach ($recent as $r): ?>
                <tr>
                    <td class="ps-3 text-nowrap small"><?= e($r['created_at']) ?></td>
                    <td><code><?= e($r['path']) ?></code></td>
                    <td><span class="badge bg-secondary"><?= e($r['device']) ?></span></td>
                    <td class="small text-muted text-truncate" style="max-width:200px;"><?= e($r['referrer'] ?: '— direct —') ?></td>
                </tr>
            <?php endforeach; ?>
            <?php if (!$recent): ?><tr><td class="ps-3 text-muted">No visits recorded yet.</td></tr><?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?php endif; ?>
<?php
    admin_footer();
    exit;
}

admin_head($mod ? $mod[0] : 'Module');
admin_nav('mod-' . $m);
?>
<h1 class="mt-4"><?= e($mod ? $mod[0] : 'Module') ?></h1>
<ol class="breadcrumb mb-4">
    <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
    <li class="breadcrumb-item active"><?= e($mod ? $mod[0] : 'Module') ?></li>
</ol>

<?php if ($mod): ?>
<div class="card mb-4">
    <div class="card-body text-center py-5">
        <i class="<?= e($mod[1]) ?> fa-3x text-muted mb-3"></i>
        <h4 class="mb-2"><?= e($mod[0]) ?></h4>
        <p class="text-muted mb-0" style="max-width:520px;margin:0 auto;"><?= e($mod[2]) ?></p>
        <div class="alert alert-secondary d-inline-block mt-4 mb-0 py-2 px-3 small">
            <i class="fas fa-hard-hat me-1"></i> This module is scaffolded and ready for development.
        </div>
    </div>
</div>
<?php else: ?>
<div class="alert alert-warning">Unknown module. Pick one from the sidebar.</div>
<?php endif; ?>

<?php
admin_footer();
