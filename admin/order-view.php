<?php
require_once __DIR__ . '/inc.php';
require_admin();

$noDb = ($pdo === null);
$id   = (int)($_GET['id'] ?? $_POST['id'] ?? 0);
if (!$id || !$pdo) { header('Location: orders.php'); exit; }

$PIPE = ['pending','confirmed','processing','packed','shipped','out_for_delivery','delivered'];

function recalc($pdo, $id) {
    $pdo->prepare('UPDATE orders o SET subtotal_usd=(SELECT COALESCE(SUM(total),0) FROM order_items WHERE order_id=?), total_usd=(SELECT COALESCE(SUM(total),0) FROM order_items WHERE order_id=?)+COALESCE(o.delivery_usd,0) WHERE o.id=?')
        ->execute([$id, $id, $id]);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $act = $_POST['act'] ?? '';
    try {
        if ($act === 'status') {
            $st   = $_POST['status'] ?? '';
            $note = trim($_POST['note'] ?? '');
            $pub  = isset($_POST['is_public']) ? 1 : 0;
            if (in_array($st, [...$PIPE, 'cancelled'], true)) {
                $pdo->prepare('UPDATE orders SET status=? WHERE id=?')->execute([$st, $id]);
                $pdo->prepare('INSERT INTO order_events (order_id,status,note,is_public) VALUES (?,?,?,?)')->execute([$id, $st, $note ?: null, $pub]);
            }
        }
        if ($act === 'payment') {
            $ps = in_array($_POST['payment_status'] ?? '', ['unpaid','deposit','paid'], true) ? $_POST['payment_status'] : 'unpaid';
            $pdo->prepare('UPDATE orders SET payment_status=? WHERE id=?')->execute([$ps, $id]);
        }
        if ($act === 'delivery') {
            $pdo->prepare('UPDATE orders SET delivery_address=?, tracking_ref=?, driver_phone=?, eta=?, admin_notes=?, delivery_usd=? WHERE id=?')
                ->execute([
                    trim($_POST['delivery_address'] ?? ''), trim($_POST['tracking_ref'] ?? ''),
                    trim($_POST['driver_phone'] ?? ''), $_POST['eta'] ?: null,
                    trim($_POST['admin_notes'] ?? ''), (float)($_POST['delivery_usd'] ?? 0), $id]);
            recalc($pdo, $id);
        }
        if ($act === 'add_item') {
            $name = trim($_POST['name'] ?? '');
            if ($name) {
                $qty   = max(0.01, (float)($_POST['qty'] ?? 1));
                $price = (float)($_POST['unit_price'] ?? 0);
                $pdo->prepare('INSERT INTO order_items (order_id,product_id,name,spec,qty,unit_price,total) VALUES (?,?,?,?,?,?,?)')
                    ->execute([$id, (int)($_POST['product_id'] ?? 0) ?: null, $name, trim($_POST['spec'] ?? ''), $qty, $price, $qty * $price]);
                recalc($pdo, $id);
            }
        }
        if ($act === 'del_item') {
            $pdo->prepare('DELETE FROM order_items WHERE id=? AND order_id=?')->execute([(int)$_POST['item_id'], $id]);
            recalc($pdo, $id);
        }
    } catch (Throwable $e) { header('Location: order-view.php?id=' . $id . '&err=db'); exit; }
    header('Location: order-view.php?id=' . $id);
    exit;
}

$order = $items = $events = $products = null;
try {
    $st = $pdo->prepare('SELECT o.*, c.name AS cust, c.email AS cust_email, c.phone AS cust_phone, c.company FROM orders o JOIN customers c ON c.id=o.customer_id WHERE o.id=?');
    $st->execute([$id]); $order = $st->fetch();
    $st = $pdo->prepare('SELECT * FROM order_items WHERE order_id=? ORDER BY sort_order, id'); $st->execute([$id]); $items = $st->fetchAll();
    $st = $pdo->prepare('SELECT * FROM order_events WHERE order_id=? ORDER BY id DESC'); $st->execute([$id]); $events = $st->fetchAll();
    $products = $pdo->query('SELECT id, name, price_usd FROM products WHERE is_active=1 ORDER BY name')->fetchAll();
} catch (Throwable $e) { $noDb = true; }
if (!$order) { header('Location: orders.php'); exit; }

$curIdx = array_search($order['status'], $PIPE, true);
$statusBadge = ['pending'=>'secondary','confirmed'=>'info','processing'=>'primary','packed'=>'warning','shipped'=>'primary','out_for_delivery'=>'info','delivered'=>'success','cancelled'=>'dark'];

admin_head('Order ' . $order['order_no']);
admin_nav('orders');
?>
<h1 class="mt-4"><code><?= e($order['order_no']) ?></code> <span class="badge bg-<?= $statusBadge[$order['status']] ?? 'secondary' ?>"><?= e(str_replace('_',' ',$order['status'])) ?></span></h1>
<ol class="breadcrumb mb-4">
    <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="orders.php">Orders</a></li>
    <li class="breadcrumb-item active"><?= e($order['order_no']) ?></li>
</ol>

<?php if (isset($_GET['err'])): ?><div class="alert alert-danger py-2">Database error.</div><?php endif; ?>

<!-- STATUS PIPELINE -->
<div class="card mb-4">
    <div class="card-header"><i class="fas fa-truck me-1"></i> Status Pipeline — click to advance (writes a tracking event the customer sees)</div>
    <div class="card-body">
        <div class="d-flex flex-wrap gap-1 mb-3">
        <?php foreach ($PIPE as $i => $s): ?>
            <button type="button" class="btn btn-sm <?= $curIdx !== false && $i <= $curIdx ? 'btn-success' : 'btn-outline-secondary' ?>" onclick="setStatus('<?= $s ?>')"><?= e(ucfirst(str_replace('_',' ',$s))) ?></button>
            <?php if ($i < count($PIPE) - 1): ?><i class="fas fa-angle-right align-self-center text-muted"></i><?php endif; ?>
        <?php endforeach; ?>
            <span class="mx-2"></span>
            <button type="button" class="btn btn-sm btn-outline-danger" onclick="setStatus('cancelled')">Cancel order</button>
        </div>
        <form method="post" class="row g-2 align-items-center" id="statusForm">
            <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
            <input type="hidden" name="id" value="<?= $id ?>">
            <input type="hidden" name="act" value="status">
            <input type="hidden" name="status" id="statusField">
            <div class="col-md-6"><input class="form-control form-control-sm" name="note" placeholder="Tracking note (e.g. 'Left depot, ETA Thursday')"></div>
            <div class="col-auto"><div class="form-check"><input class="form-check-input" type="checkbox" name="is_public" id="pub" checked><label class="form-check-label small" for="pub">Visible to customer</label></div></div>
            <div class="col-auto"><button class="btn btn-sm btn-dark" id="statusBtn" disabled>Update status</button></div>
        </form>
        <script>function setStatus(s){document.getElementById('statusField').value=s;document.getElementById('statusBtn').disabled=false;document.getElementById('statusBtn').textContent='Set "'+s.replace(/_/g,' ')+'"';}</script>
    </div>
</div>

<div class="row">
    <div class="col-xl-8">
        <!-- ITEMS -->
        <div class="card mb-4">
            <div class="card-header"><i class="fas fa-list me-1"></i> Items</div>
            <div class="card-body">
                <table class="table table-sm">
                    <thead><tr><th>Item</th><th>Spec</th><th class="text-end">Qty</th><th class="text-end">Unit</th><th class="text-end">Total</th><th></th></tr></thead>
                    <tbody>
                    <?php foreach ($items as $it): ?>
                    <tr>
                        <td><?= e($it['name']) ?></td>
                        <td class="small text-muted"><?= e($it['spec']) ?></td>
                        <td class="text-end"><?= (float)$it['qty'] ?></td>
                        <td class="text-end"><?= $it['unit_price'] !== null ? usd($it['unit_price']) : '—' ?></td>
                        <td class="text-end"><?= $it['total'] !== null ? usd($it['total']) : '—' ?></td>
                        <td><form method="post" class="d-inline"><input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>"><input type="hidden" name="id" value="<?= $id ?>"><input type="hidden" name="act" value="del_item"><input type="hidden" name="item_id" value="<?= (int)$it['id'] ?>"><button class="btn btn-sm btn-outline-danger py-0"><i class="fas fa-times"></i></button></form></td>
                    </tr>
                    <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                    <tr><td colspan="4" class="text-end text-muted">Subtotal</td><td class="text-end"><?= usd($order['subtotal_usd'] ?? 0) ?></td><td></td></tr>
                    <tr><td colspan="4" class="text-end text-muted">Delivery</td><td class="text-end"><?= usd($order['delivery_usd'] ?? 0) ?></td><td></td></tr>
                    <tr><td colspan="4" class="text-end fw-bold">Total</td><td class="text-end fw-bold"><?= usd($order['total_usd'] ?? 0) ?></td><td></td></tr>
                    </tfoot>
                </table>
                <form method="post" class="row g-2 align-items-end border-top pt-3">
                    <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
                    <input type="hidden" name="id" value="<?= $id ?>">
                    <input type="hidden" name="act" value="add_item">
                    <div class="col-md-3"><label class="small text-muted">Product (or type name)</label>
                        <select class="form-select form-select-sm" id="prodPick" onchange="fillProd(this)"><option value="">— pick catalog —</option>
                        <?php foreach ($products as $p): ?><option value="<?= (int)$p['id'] ?>" data-name="<?= e($p['name']) ?>" data-price="<?= e($p['price_usd']) ?>"><?= e($p['name']) ?></option><?php endforeach; ?></select>
                    </div>
                    <input type="hidden" name="product_id" id="prodId">
                    <div class="col-md-3"><label class="small text-muted">Name *</label><input class="form-control form-control-sm" name="name" id="itemName" required></div>
                    <div class="col-md-2"><label class="small text-muted">Spec</label><input class="form-control form-control-sm" name="spec" placeholder="e.g. 1.8m x 30m"></div>
                    <div class="col-md-1"><label class="small text-muted">Qty</label><input class="form-control form-control-sm" type="number" step="0.01" name="qty" value="1" required></div>
                    <div class="col-md-2"><label class="small text-muted">Unit $</label><input class="form-control form-control-sm" type="number" step="0.01" name="unit_price" id="itemPrice"></div>
                    <div class="col-md-1"><button class="btn btn-sm btn-dark w-100">Add</button></div>
                </form>
                <script>function fillProd(sel){const o=sel.options[sel.selectedIndex];document.getElementById('prodId').value=sel.value;if(o.dataset.name){document.getElementById('itemName').value=o.dataset.name;document.getElementById('itemPrice').value=o.dataset.price||'';}}</script>
            </div>
        </div>
        <!-- TIMELINE -->
        <div class="card mb-4">
            <div class="card-header"><i class="fas fa-history me-1"></i> Tracking Timeline</div>
            <ul class="list-group list-group-flush">
            <?php foreach ($events as $ev): ?>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <div><span class="badge bg-<?= $statusBadge[$ev['status']] ?? 'secondary' ?> me-2"><?= e(str_replace('_',' ',$ev['status'])) ?></span><?= e($ev['note'] ?? '') ?>
                        <?= $ev['is_public'] ? '' : ' <span class="badge bg-dark">internal</span>' ?></div>
                    <span class="small text-muted"><?= e(substr($ev['created_at'],0,16)) ?></span>
                </li>
            <?php endforeach; ?>
            </ul>
        </div>
    </div>
    <div class="col-xl-4">
        <!-- CUSTOMER -->
        <div class="card mb-4">
            <div class="card-header"><i class="fas fa-user me-1"></i> Customer</div>
            <div class="card-body small">
                <div><strong><?= e($order['cust']) ?></strong><?= $order['company'] ? ' — ' . e($order['company']) : '' ?></div>
                <div class="text-muted"><?= e($order['cust_email']) ?><?= $order['cust_phone'] ? ' · ' . e($order['cust_phone']) : '' ?></div>
                <a class="btn btn-sm btn-outline-dark mt-2" href="customers.php?edit=<?= (int)$order['customer_id'] ?>">Edit customer</a>
            </div>
        </div>
        <!-- PAYMENT -->
        <div class="card mb-4">
            <div class="card-header"><i class="fas fa-money-bill me-1"></i> Payment</div>
            <div class="card-body">
                <div class="btn-group w-100" role="group">
                <?php foreach (['unpaid','deposit','paid'] as $ps): ?>
                    <form method="post"><input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>"><input type="hidden" name="id" value="<?= $id ?>"><input type="hidden" name="act" value="payment"><input type="hidden" name="payment_status" value="<?= $ps ?>">
                    <button class="btn btn-sm <?= $order['payment_status']===$ps ? 'btn-dark' : 'btn-outline-secondary' ?>"><?= ucfirst($ps) ?></button></form>
                <?php endforeach; ?>
                </div>
            </div>
        </div>
        <!-- DELIVERY -->
        <div class="card mb-4">
            <div class="card-header"><i class="fas fa-map-marker-alt me-1"></i> Delivery</div>
            <div class="card-body">
                <form method="post">
                    <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
                    <input type="hidden" name="id" value="<?= $id ?>">
                    <input type="hidden" name="act" value="delivery">
                    <label class="small text-muted">Delivery address</label>
                    <input class="form-control form-control-sm mb-2" name="delivery_address" value="<?= e($order['delivery_address']) ?>">
                    <label class="small text-muted">Tracking ref</label>
                    <input class="form-control form-control-sm mb-2" name="tracking_ref" value="<?= e($order['tracking_ref']) ?>">
                    <label class="small text-muted">Driver / courier phone</label>
                    <input class="form-control form-control-sm mb-2" name="driver_phone" value="<?= e($order['driver_phone']) ?>">
                    <label class="small text-muted">ETA</label>
                    <input class="form-control form-control-sm mb-2" type="date" name="eta" value="<?= e($order['eta']) ?>">
                    <label class="small text-muted">Delivery fee ($)</label>
                    <input class="form-control form-control-sm mb-2" type="number" step="0.01" name="delivery_usd" value="<?= e($order['delivery_usd']) ?>">
                    <label class="small text-muted">Internal notes (never shown to customer)</label>
                    <textarea class="form-control form-control-sm mb-3" name="admin_notes" rows="2"><?= e($order['admin_notes']) ?></textarea>
                    <button class="btn btn-dark w-100 btn-sm">Save delivery details</button>
                </form>
            </div>
        </div>
    </div>
</div>
<?php
admin_footer();
