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
