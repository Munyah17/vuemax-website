<?php
/**
 * Admin bootstrap — session + auth helpers + SB Admin layout chrome.
 * Included by every file in /admin.
 */

require_once __DIR__ . '/../includes/config.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function is_admin() {
    return !empty($_SESSION['admin_id']);
}

function require_admin() {
    if (!is_admin()) {
        header('Location: login.php');
        exit;
    }
}

function csrf_token() {
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(24));
    }
    return $_SESSION['csrf'];
}

function csrf_check() {
    $t = $_POST['csrf'] ?? '';
    if (!$t || !hash_equals($_SESSION['csrf'] ?? '', $t)) {
        http_response_code(403);
        exit('Invalid request token.');
    }
}

/* ---------------- SB Admin layout ---------------- */

function admin_head($title) {
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
<meta name="robots" content="noindex,nofollow">
<title><?= e($title) ?> — Vuemax Admin</title>
<link href="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/style.min.css" rel="stylesheet">
<link href="assets/sb-styles.css" rel="stylesheet">
<script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
</head>
<body class="sb-nav-fixed">
<?php
}

function admin_nav($active) {
    $user = e($_SESSION['admin_user'] ?? 'Admin');
    $link = function($href, $icon, $label, $key) use ($active) {
        $cls = $active === $key ? 'nav-link active' : 'nav-link';
        return '<a class="' . $cls . '" href="' . $href . '"><div class="sb-nav-link-icon"><i class="' . $icon . '"></i></div>' . $label . '</a>';
    };
    // Collapsible menu group: auto-expands when a child is active.
    $group = function($id, $icon, $label, $items) use ($active) {
        $open = false;
        $links = '';
        foreach ($items as $it) {
            [$href, $text, $key] = $it;
            if ($active === $key) $open = true;
            $cls = $active === $key ? 'nav-link active' : 'nav-link';
            $links .= '<a class="' . $cls . '" href="' . $href . '">' . $text . '</a>';
        }
        return '<a class="nav-link' . ($open ? '' : ' collapsed') . '" href="#" data-bs-toggle="collapse" data-bs-target="#' . $id . '" aria-expanded="' . ($open ? 'true' : 'false') . '" aria-controls="' . $id . '">'
            . '<div class="sb-nav-link-icon"><i class="' . $icon . '"></i></div>' . $label
            . '<div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div></a>'
            . '<div class="collapse' . ($open ? ' show' : '') . '" id="' . $id . '" data-bs-parent="#sidenavAccordion">'
            . '<nav class="sb-sidenav-menu-nested nav">' . $links . '</nav></div>';
    };
    $mod = function($m) { return 'module.php?m=' . $m; };
?>
<nav class="sb-topnav navbar navbar-expand navbar-dark bg-dark">
    <a class="navbar-brand ps-3" href="index.php"><i class="fas fa-cube me-2"></i>Vuemax Back Office</a>
    <button class="btn btn-link btn-sm order-1 order-lg-0 me-4 me-lg-0" id="sidebarToggle" href="#"><i class="fas fa-bars"></i></button>
    <div class="d-none d-md-inline-block ms-auto me-3">
        <a class="btn btn-outline-light btn-sm" href="../index.php" target="_blank"><i class="fas fa-external-link-alt me-1"></i>View Site</a>
    </div>
    <ul class="navbar-nav ms-auto ms-md-0 me-3 me-lg-4">
        <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" id="navbarDropdown" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false"><i class="fas fa-user fa-fw"></i></a>
            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                <li><span class="dropdown-item-text small text-muted">Signed in as <strong><?= $user ?></strong></span></li>
                <li><hr class="dropdown-divider"></li>
                <li><a class="dropdown-item" href="logout.php">Logout</a></li>
            </ul>
        </li>
    </ul>
</nav>
<div id="layoutSidenav">
    <div id="layoutSidenav_nav">
        <nav class="sb-sidenav accordion sb-sidenav-dark" id="sidenavAccordion">
            <div class="sb-sidenav-menu">
                <div class="nav">
                    <div class="sb-sidenav-menu-heading">Core</div>
                    <?= $link('index.php', 'fas fa-tachometer-alt', 'Dashboard', 'dash') ?>
                    <?= $link('module.php?m=analytics', 'fas fa-chart-line', 'Visitor Analytics', 'mod-analytics') ?>
                    <?= $link('module.php?m=tasks', 'fas fa-tasks', 'Tasks &amp; Reminders', 'mod-tasks') ?>
                    <?= $link('module.php?m=notifications', 'fas fa-bell', 'Notifications', 'mod-notifications') ?>

                    <div class="sb-sidenav-menu-heading">Sales</div>
                    <?= $group('grpSales', 'fas fa-cash-register', 'Sales', [
                        ['orders.php', 'Orders &amp; Delivery', 'orders'],
                        ['quotes.php', 'Quotes', 'quotes'],
                        [$mod('pos'), 'Point of Sale', 'mod-pos'],
                        [$mod('invoicing'), 'Invoicing', 'mod-invoicing'],
                        [$mod('quotations'), 'Quotations', 'mod-quotations'],
                        [$mod('receipting'), 'Receipting &amp; Printing', 'mod-receipting'],
                        [$mod('payments'), 'Payment Methods', 'mod-payments'],
                    ]) ?>
                    <?= $link('messages.php', 'fas fa-comments', 'Messages &amp; Chat', 'messages') ?>

                    <div class="sb-sidenav-menu-heading">Catalog</div>
                    <?= $group('grpCatalog', 'fas fa-boxes', 'Catalog', [
                        ['products.php', 'Products', 'products'],
                        [$mod('bom'), 'BOM / BOQ', 'mod-bom'],
                        [$mod('requisitions'), 'Requisitions', 'mod-requisitions'],
                        [$mod('purchases'), 'Purchases', 'mod-purchases'],
                    ]) ?>

                    <div class="sb-sidenav-menu-heading">People</div>
                    <?= $group('grpPeople', 'fas fa-users', 'People', [
                        ['customers.php', 'Customers', 'customers'],
                        [$mod('clients'), 'Clients Database', 'mod-clients'],
                        [$mod('staff'), 'Staff Management', 'mod-staff'],
                        [$mod('hr'), 'HR &amp; Payroll', 'mod-hr'],
                        ['users.php', 'Admin Users', 'users'],
                    ]) ?>

                    <div class="sb-sidenav-menu-heading">Site &amp; Marketing</div>
                    <?= $group('grpSite', 'fas fa-globe', 'Site &amp; Marketing', [
                        ['banners.php', 'Banner Manager', 'banners'],
                        ['images.php', 'Image Manager', 'images'],
                        ['faqs.php', 'FAQs', 'faqs'],
                        [$mod('branding'), 'Branding', 'mod-branding'],
                    ]) ?>

                    <div class="sb-sidenav-menu-heading">Finance</div>
                    <?= $group('grpFinance', 'fas fa-coins', 'Finance', [
                        [$mod('revenue'), 'Revenue', 'mod-revenue'],
                        [$mod('ledger'), 'Creditors &amp; Debtors', 'mod-ledger'],
                        [$mod('reports'), 'Reports', 'mod-reports'],
                    ]) ?>

                    <div class="sb-sidenav-menu-heading">System</div>
                    <?= $group('grpSystem', 'fas fa-cog', 'System', [
                        [$mod('settings'), 'Settings', 'mod-settings'],
                        [$mod('config'), 'Configurations', 'mod-config'],
                    ]) ?>
                    <a class="nav-link" href="../index.php" target="_blank">
                        <div class="sb-nav-link-icon"><i class="fas fa-external-link-alt"></i></div>
                        View Site
                    </a>
                </div>
            </div>
            <div class="sb-sidenav-footer">
                <div class="small">Logged in as:</div>
                <?= $user ?>
            </div>
        </nav>
    </div>
    <div id="layoutSidenav_content">
        <main>
            <div class="container-fluid px-4">
<?php
}

function admin_footer($extra = '') {
?>
            </div>
        </main>
        <footer class="py-4 bg-light mt-auto">
            <div class="container-fluid px-4">
                <div class="d-flex align-items-center justify-content-between small">
                    <div class="text-muted">Copyright &copy; Vuemax Industries <?= date('Y') ?></div>
                    <div><a href="../index.php" target="_blank">vuemax.co.zw</a></div>
                </div>
            </div>
        </footer>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
<script src="assets/sb-scripts.js"></script>
<?= $extra ?>
</body>
</html>
<?php
}

/* Small helpers */

function admin_stat_card($color, $label, $value, $icon, $href = null, $hrefLabel = 'View') {
    $link = $href
        ? '<a class="small text-white stretched-link" href="' . e($href) . '">' . e($hrefLabel) . '</a><div class="small text-white"><i class="fas fa-angle-right"></i></div>'
        : '';
    return '<div class="col-xl-3 col-md-6"><div class="card ' . $color . ' text-white mb-4">'
        . '<div class="card-body d-flex align-items-center justify-content-between"><div>'
        . '<div class="small text-white-50 text-uppercase">' . e($label) . '</div>'
        . '<div class="fs-4 fw-bold">' . e($value) . '</div></div>'
        . '<i class="' . $icon . ' fa-2x text-white-50"></i></div>'
        . ($link ? '<div class="card-footer d-flex align-items-center justify-content-between">' . $link . '</div>' : '')
        . '</div></div>';
}

function admin_table_count($pdo, $table) {
    if (!$pdo) return null;
    try {
        $st = $pdo->query('SELECT COUNT(*) FROM `' . $table . '`');
        return (int)$st->fetchColumn();
    } catch (Throwable $e) { return null; }
}
