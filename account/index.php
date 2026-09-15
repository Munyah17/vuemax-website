<?php
require_once __DIR__ . '/inc.php';
require_customer();

$cust = current_customer();
if (!$cust) { session_destroy(); header('Location: login.php'); exit; }

$orders = [];
if ($pdo) {
    try {
        $st = $pdo->prepare('SELECT o.*, (SELECT COUNT(*) FROM order_items i WHERE i.order_id=o.id) AS items_n
                             FROM orders o WHERE o.customer_id=? ORDER BY o.id DESC');
        $st->execute([$cust['id']]);
        $orders = $st->fetchAll();
    } catch (Throwable $e) {}
}

$statusBadge = ['pending'=>'#6B7280','confirmed'=>'#0EA5E9','processing'=>'#3B82F6','packed'=>'#F59E0B','shipped'=>'#3B82F6','out_for_delivery'=>'#0EA5E9','delivered'=>'#059669','cancelled'=>'#111827'];

$pageTitle = 'My Account';
$pageDesc  = 'Your Vuemax orders and deliveries.';
$active    = 'account';
$extraCss  = <<<'CSS'
.acct{max-width:1080px;margin:40px auto 70px;padding:0 20px;}
.acct-head{display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;margin-bottom:22px;}
.acct-head h1{font-size:24px;}
.acct-head .who{font-size:13px;color:#6B7280;}
.acct-card{background:#fff;border:1px solid #E7E2DA;border-radius:14px;padding:22px;margin-bottom:18px;}
.acct-card h2{font-size:15px;margin-bottom:14px;padding-bottom:10px;border-bottom:2px solid #F4F1EC;}
.acct-table{width:100%;font-size:13.5px;border-collapse:collapse;}
.acct-table th{text-align:left;font-size:11px;text-transform:uppercase;letter-spacing:.05em;color:#9CA3AF;padding:8px 10px;border-bottom:1px solid #EEE;}
.acct-table td{padding:10px;border-bottom:1px solid #F4F1EC;}
.badge{display:inline-block;padding:3px 10px;border-radius:20px;font-size:11px;font-weight:700;color:#fff;text-transform:capitalize;}
.acct-empty{color:#6B7280;font-size:14px;}
.acct-grid{display:grid;grid-template-columns:1fr 2fr;gap:18px;align-items:start;}
@media (max-width:800px){.acct-grid{grid-template-columns:1fr;}}
.acct-prof div{padding:7px 0;border-bottom:1px solid #F4F1EC;font-size:13.5px;}
.acct-prof .lbl{font-size:10.5px;text-transform:uppercase;letter-spacing:.06em;color:#9CA3AF;}
CSS;
$base = '../';
require __DIR__ . '/../includes/header.php';
?>
<div class="acct">
    <div class="acct-head">
        <div>
            <h1>Hello, <?= e($cust['name']) ?></h1>
            <div class="who"><?= e($cust['email']) ?><?= $cust['company'] ? ' · ' . e($cust['company']) : '' ?></div>
        </div>
        <div>
            <a class="btn btn-outline" href="profile.php">Edit Profile</a>
            <a class="btn btn-outline" href="logout.php">Sign Out</a>
        </div>
    </div>

    <div class="acct-grid">
        <div class="acct-card acct-prof">
            <h2>Profile</h2>
            <div><div class="lbl">Name</div><?= e($cust['name']) ?></div>
            <div><div class="lbl">Email</div><?= e($cust['email']) ?></div>
            <div><div class="lbl">Phone</div><?= e($cust['phone'] ?: '—') ?></div>
            <div><div class="lbl">Company</div><?= e($cust['company'] ?: '—') ?></div>
            <div><div class="lbl">Delivery address</div><?= e($cust['address'] ?: '—') ?></div>
        </div>

        <div class="acct-card">
            <h2>My Orders</h2>
            <?php if (!$orders): ?>
                <p class="acct-empty">No orders yet — they'll appear here once Vuemax processes your purchase. <a href="../calculator.php">Get a quote</a>.</p>
            <?php else: ?>
            <table class="acct-table">
                <thead><tr><th>Order</th><th>Items</th><th>Status</th><th>Payment</th><th style="text-align:right">Total</th><th></th></tr></thead>
                <tbody>
                <?php foreach ($orders as $o): ?>
                <tr>
                    <td><code><?= e($o['order_no']) ?></code><br><span style="font-size:11px;color:#9CA3AF"><?= e(substr($o['created_at'],0,10)) ?></span></td>
                    <td><?= (int)$o['items_n'] ?></td>
                    <td><span class="badge" style="background:<?= $statusBadge[$o['status']] ?? '#6B7280' ?>"><?= e(str_replace('_',' ',$o['status'])) ?></span></td>
                    <td><?= e($o['payment_status']) ?></td>
                    <td style="text-align:right"><?= $o['total_usd'] !== null ? usd($o['total_usd']) : '—' ?></td>
                    <td><a href="order.php?id=<?= (int)$o['id'] ?>">Track</a></td>
                </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php require __DIR__ . '/../includes/footer.php'; ?>
