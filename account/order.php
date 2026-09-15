<?php
require_once __DIR__ . '/inc.php';
require_customer();

$cust = current_customer();
$id   = (int)($_GET['id'] ?? 0);
$order = $items = $events = null;

if ($pdo && $cust) {
    try {
        // strictly scoped to the logged-in customer
        $st = $pdo->prepare('SELECT * FROM orders WHERE id=? AND customer_id=?');
        $st->execute([$id, $cust['id']]);
        $order = $st->fetch();
        if ($order) {
            $st = $pdo->prepare('SELECT * FROM order_items WHERE order_id=? ORDER BY sort_order, id');
            $st->execute([$id]); $items = $st->fetchAll();
            $st = $pdo->prepare('SELECT * FROM order_events WHERE order_id=? AND is_public=1 ORDER BY id');
            $st->execute([$id]); $events = $st->fetchAll();
        }
    } catch (Throwable $e) {}
}
if (!$order) { header('Location: index.php'); exit; }

$PIPE = ['pending','confirmed','processing','packed','shipped','out_for_delivery','delivered'];
$curIdx = array_search($order['status'], $PIPE, true);
$statusBadge = ['pending'=>'#6B7280','confirmed'=>'#0EA5E9','processing'=>'#3B82F6','packed'=>'#F59E0B','shipped'=>'#3B82F6','out_for_delivery'=>'#0EA5E9','delivered'=>'#059669','cancelled'=>'#111827'];

$pageTitle = 'Order ' . $order['order_no'];
$pageDesc  = 'Order tracking and details.';
$active    = 'account';
$extraCss  = <<<'CSS'
.acct{max-width:980px;margin:40px auto 70px;padding:0 20px;}
.acct-card{background:#fff;border:1px solid #E7E2DA;border-radius:14px;padding:22px;margin-bottom:18px;}
.acct-card h2{font-size:15px;margin-bottom:14px;padding-bottom:10px;border-bottom:2px solid #F4F1EC;}
.acct-table{width:100%;font-size:13.5px;border-collapse:collapse;}
.acct-table th{text-align:left;font-size:11px;text-transform:uppercase;letter-spacing:.05em;color:#9CA3AF;padding:8px 10px;border-bottom:1px solid #EEE;}
.acct-table td{padding:10px;border-bottom:1px solid #F4F1EC;}
.pipe{display:flex;flex-wrap:wrap;gap:6px;align-items:center;}
.pipe .step{padding:6px 12px;border-radius:20px;font-size:12px;font-weight:600;background:#F4F1EC;color:#9CA3AF;}
.pipe .step.done{background:#059669;color:#fff;}
.pipe .step.cur{background:#F2A33C;color:#0A1D33;}
.pipe .arrow{color:#D1D5DB;}
.tl{border-left:3px solid #F4F1EC;margin-left:8px;padding-left:18px;}
.tl .ev{position:relative;padding:0 0 16px;font-size:13.5px;}
.tl .ev:before{content:'';position:absolute;left:-24px;top:4px;width:9px;height:9px;border-radius:50%;background:#F2A33C;border:2px solid #fff;box-shadow:0 0 0 2px #F4F1EC;}
.tl .ev .st{font-weight:700;text-transform:capitalize;}
.tl .ev .ts{font-size:11px;color:#9CA3AF;}
.badge{display:inline-block;padding:3px 10px;border-radius:20px;font-size:11px;font-weight:700;color:#fff;text-transform:capitalize;}
.acct-grid{display:grid;grid-template-columns:1fr 1fr;gap:18px;align-items:start;}
@media (max-width:800px){.acct-grid{grid-template-columns:1fr;}}
CSS;
$base = '../';
require __DIR__ . '/../includes/header.php';
?>
<div class="acct">
    <p><a href="index.php">&larr; My orders</a></p>

    <div class="acct-card">
        <h2><code><?= e($order['order_no']) ?></code>
            <span class="badge" style="background:<?= $statusBadge[$order['status']] ?? '#6B7280' ?>"><?= e(str_replace('_',' ',$order['status'])) ?></span>
        </h2>
        <?php if ($order['status'] !== 'cancelled'): ?>
        <div class="pipe">
        <?php foreach ($PIPE as $i => $s): ?>
            <span class="step <?= $curIdx !== false && $i < $curIdx ? 'done' : ($i === $curIdx ? 'cur' : '') ?>"><?= e(str_replace('_',' ',$s)) ?></span>
            <?= $i < count($PIPE)-1 ? '<span class="arrow">&rarr;</span>' : '' ?>
        <?php endforeach; ?>
        </div>
        <?php else: ?><p>Order cancelled — contact Vuemax with questions.</p><?php endif; ?>
        <div style="margin-top:16px;font-size:13px;color:#6B7280;">
            <?php if ($order['delivery_address']): ?><i>Deliver to:</i> <?= e($order['delivery_address']) ?> · <?php endif; ?>
            <?php if ($order['tracking_ref']): ?><i>Tracking:</i> <code><?= e($order['tracking_ref']) ?></code> · <?php endif; ?>
            <?php if ($order['eta']): ?><i>ETA:</i> <?= e($order['eta']) ?><?php endif; ?>
            <?php if ($order['driver_phone']): ?> · <i>Driver:</i> <?= e($order['driver_phone']) ?><?php endif; ?>
        </div>
    </div>

    <div class="acct-grid">
        <div class="acct-card">
            <h2>Items</h2>
            <table class="acct-table">
                <thead><tr><th>Item</th><th style="text-align:right">Qty</th><th style="text-align:right">Total</th></tr></thead>
                <tbody>
                <?php foreach ($items as $it): ?>
                <tr><td><?= e($it['name']) ?><?= $it['spec'] ? '<br><span style="font-size:11px;color:#9CA3AF">' . e($it['spec']) . '</span>' : '' ?></td>
                    <td style="text-align:right"><?= (float)$it['qty'] ?></td>
                    <td style="text-align:right"><?= $it['total'] !== null ? usd($it['total']) : '—' ?></td></tr>
                <?php endforeach; ?>
                </tbody>
                <tfoot>
                <tr><td colspan="2" style="text-align:right;color:#6B7280">Subtotal</td><td style="text-align:right"><?= usd($order['subtotal_usd'] ?? 0) ?></td></tr>
                <tr><td colspan="2" style="text-align:right;color:#6B7280">Delivery</td><td style="text-align:right"><?= usd($order['delivery_usd'] ?? 0) ?></td></tr>
                <tr><td colspan="2" style="text-align:right;font-weight:700">Total</td><td style="text-align:right;font-weight:700"><?= usd($order['total_usd'] ?? 0) ?></td></tr>
                <tr><td colspan="2" style="text-align:right;color:#6B7280">Payment</td><td style="text-align:right"><?= e($order['payment_status']) ?></td></tr>
                </tfoot>
            </table>
        </div>
        <div class="acct-card">
            <h2>Tracking</h2>
            <div class="tl">
            <?php foreach (array_reverse($events) as $ev): ?>
                <div class="ev">
                    <div class="st"><?= e(str_replace('_',' ',$ev['status'])) ?></div>
                    <?php if ($ev['note']): ?><div><?= e($ev['note']) ?></div><?php endif; ?>
                    <div class="ts"><?= e(substr($ev['created_at'],0,16)) ?></div>
                </div>
            <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>
<?php require __DIR__ . '/../includes/footer.php'; ?>
