<?php
require_once __DIR__ . '/inc.php';
require_admin();

$noDb = ($pdo === null);
$flash = '';

// Create a new order for a customer
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $pdo) {
    csrf_check();
    if (($_POST['act'] ?? '') === 'create') {
        $cid  = (int)($_POST['customer_id'] ?? 0);
        $addr = trim($_POST['delivery_address'] ?? '');
        try {
            if ($cid) {
                $yr = date('Y');
                $next = (int)$pdo->query("SELECT COUNT(*)+1 FROM orders")->fetchColumn();
                $no = sprintf('VO-%s-%04d', $yr, $next);
                $pdo->prepare('INSERT INTO orders (order_no, customer_id, delivery_address) VALUES (?,?,?)')->execute([$no, $cid, $addr]);
                $oid = (int)$pdo->lastInsertId();
                $pdo->prepare('INSERT INTO order_events (order_id, status, note) VALUES (?,?,?)')->execute([$oid, 'pending', 'Order created by admin']);
                header('Location: order-view.php?id=' . $oid);
                exit;
            }
        } catch (Throwable $e) { header('Location: orders.php?err=db'); exit; }
        header('Location: orders.php?err=customer'); exit;
    }
}

$rows = [];
$customers = [];
$filterC = isset($_GET['customer']) ? (int)$_GET['customer'] : 0;
if ($pdo) {
    try {
        $customers = $pdo->query("SELECT id, name, email FROM customers WHERE status='active' ORDER BY name")->fetchAll();
        $sql = 'SELECT o.*, c.name AS cust, c.email AS cust_email,
                       (SELECT COUNT(*) FROM order_items i WHERE i.order_id=o.id) AS items_n
                FROM orders o JOIN customers c ON c.id = o.customer_id';
        if ($filterC) { $sql .= ' WHERE o.customer_id=' . $filterC; }
        $sql .= ' ORDER BY o.id DESC';
        $rows = $pdo->query($sql)->fetchAll();
    } catch (Throwable $e) { $noDb = true; }
}

$statusBadge = ['pending'=>'secondary','confirmed'=>'info','processing'=>'primary','packed'=>'warning','shipped'=>'primary','out_for_delivery'=>'info','delivered'=>'success','cancelled'=>'dark'];
$payBadge = ['unpaid'=>'danger','deposit'=>'warning','paid'=>'success'];

if (isset($_GET['ok']))  $flash = 'ok:' . $_GET['ok'];
if (isset($_GET['err'])) $flash = 'err:' . $_GET['err'];

admin_head('Orders');
admin_nav('orders');
?>
<h1 class="mt-4">Orders &amp; Delivery</h1>
<ol class="breadcrumb mb-4">
    <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
    <li class="breadcrumb-item active">Orders</li>
</ol>

<?php if ($noDb): ?><div class="alert alert-warning"><strong>Database not connected.</strong> Import <code>sql/schema.sql</code> first.</div><?php endif; ?>
<?php if ($flash): [$k,$v]=explode(':',$flash,2); if($k==='ok'): ?><div class="alert alert-success py-2">Order <?= e($v) ?>.</div>
<?php else: ?><div class="alert alert-danger py-2"><?= e(['customer'=>'Pick a customer.','db'=>'Database error.'][$v] ?? 'Error') ?></div><?php endif; endif; ?>

<div class="row">
    <div class="col-xl-3">
        <div class="card mb-4">
            <div class="card-header"><i class="fas fa-plus me-1"></i> New Order</div>
            <div class="card-body">
                <form method="post">
                    <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
                    <input type="hidden" name="act" value="create">
                    <label class="small text-muted">Customer *</label>
                    <select class="form-select mb-2" name="customer_id" required>
                        <option value="">— choose —</option>
                        <?php foreach ($customers as $c): ?>
                        <option value="<?= (int)$c['id'] ?>"><?= e($c['name']) ?> (<?= e($c['email']) ?>)</option>
                        <?php endforeach; ?>
                    </select>
                    <label class="small text-muted">Delivery address</label>
                    <input class="form-control mb-3" name="delivery_address" placeholder="Site / address">
                    <button class="btn btn-dark w-100">Create Order</button>
                    <div class="small text-muted mt-2">Add items and update status on the order page.</div>
                </form>
            </div>
        </div>
    </div>
    <div class="col-xl-9">
        <div class="card mb-4">
            <div class="card-header"><i class="fas fa-truck me-1"></i> Orders <span class="badge bg-secondary ms-2"><?= count($rows) ?></span>
                <?php if ($filterC): ?><a class="small ms-3" href="orders.php">clear filter</a><?php endif; ?>
            </div>
            <div class="card-body">
                <?php if (!$rows): ?><p class="text-muted mb-0">No orders yet.</p><?php else: ?>
                <table id="datatablesSimple" class="table table-striped table-sm">
                    <thead><tr><th>Order</th><th>Customer</th><th>Items</th><th>Status</th><th>Payment</th><th class="text-end">Total</th><th>ETA</th><th></th></tr></thead>
                    <tbody>
                    <?php foreach ($rows as $o): ?>
                    <tr>
                        <td><a href="order-view.php?id=<?= (int)$o['id'] ?>"><code><?= e($o['order_no']) ?></code></a></td>
                        <td><?= e($o['cust']) ?><br><span class="small text-muted"><?= e($o['cust_email']) ?></span></td>
                        <td><?= (int)$o['items_n'] ?></td>
                        <td><span class="badge bg-<?= $statusBadge[$o['status']] ?? 'secondary' ?>"><?= e(str_replace('_',' ',$o['status'])) ?></span></td>
                        <td><span class="badge bg-<?= $payBadge[$o['payment_status']] ?? 'secondary' ?>"><?= e($o['payment_status']) ?></span></td>
                        <td class="text-end"><?= $o['total_usd'] !== null ? usd($o['total_usd']) : '—' ?></td>
                        <td class="small"><?= e($o['eta'] ?: '—') ?></td>
                        <td><a class="btn btn-sm btn-dark" href="order-view.php?id=<?= (int)$o['id'] ?>">Manage</a></td>
                    </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
                <?php endif; ?>
            </div>
        </div>
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
