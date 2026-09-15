<?php
require_once __DIR__ . '/inc.php';
require_admin();

$noDb = ($pdo === null);
$flash = '';

// Create / update / suspend
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $pdo) {
    csrf_check();
    $act = $_POST['act'] ?? '';
    try {
        if ($act === 'save') {
            $id    = (int)($_POST['id'] ?? 0);
            $name  = trim($_POST['name'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $phone = trim($_POST['phone'] ?? '');
            $comp  = trim($_POST['company'] ?? '');
            $addr  = trim($_POST['address'] ?? '');
            $pass  = (string)($_POST['password'] ?? '');
            if (!$name || !$email) { header('Location: customers.php?err=required'); exit; }
            if ($id) {
                $pdo->prepare('UPDATE customers SET name=?, email=?, phone=?, company=?, address=? WHERE id=?')
                    ->execute([$name, $email, $phone, $comp, $addr, $id]);
                if ($pass !== '') {
                    $pdo->prepare('UPDATE customers SET password_hash=? WHERE id=?')
                        ->execute([password_hash($pass, PASSWORD_DEFAULT), $id]);
                }
                header('Location: customers.php?ok=updated'); exit;
            }
            if ($pass === '') { header('Location: customers.php?err=pass'); exit; }
            $pdo->prepare('INSERT INTO customers (name, email, phone, company, address, password_hash) VALUES (?,?,?,?,?,?)')
                ->execute([$name, $email, $phone, $comp, $addr, password_hash($pass, PASSWORD_DEFAULT)]);
            header('Location: customers.php?ok=created'); exit;
        }
        if ($act === 'status') {
            $id = (int)($_POST['id'] ?? 0);
            $st = ($_POST['status'] ?? '') === 'suspended' ? 'suspended' : 'active';
            $pdo->prepare('UPDATE customers SET status=? WHERE id=?')->execute([$st, $id]);
            header('Location: customers.php?ok=status'); exit;
        }
    } catch (Throwable $e) {
        header('Location: customers.php?err=' . (str_contains($e->getMessage(), 'Duplicate') ? 'dupe' : 'db')); exit;
    }
}

$rows = [];
if ($pdo) {
    try {
        $rows = $pdo->query('SELECT c.*, (SELECT COUNT(*) FROM orders o WHERE o.customer_id = c.id) AS orders_n, (SELECT COALESCE(SUM(total_usd),0) FROM orders o WHERE o.customer_id = c.id AND status != \'cancelled\') AS spent FROM customers c ORDER BY c.id DESC')->fetchAll();
    } catch (Throwable $e) { $noDb = true; }
}
$edit = null;
if (isset($_GET['edit']) && $pdo) {
    try {
        $st = $pdo->prepare('SELECT * FROM customers WHERE id=?'); $st->execute([(int)$_GET['edit']]);
        $edit = $st->fetch();
    } catch (Throwable $e) {}
}
if (isset($_GET['ok']))  $flash = 'ok:' . $_GET['ok'];
if (isset($_GET['err'])) $flash = 'err:' . $_GET['err'];

admin_head('Customers');
admin_nav('customers');
?>
<h1 class="mt-4">Customers</h1>
<ol class="breadcrumb mb-4">
    <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
    <li class="breadcrumb-item active">Customers</li>
</ol>

<?php if ($noDb): ?><div class="alert alert-warning"><strong>Database not connected.</strong> Import <code>sql/schema.sql</code> first.</div><?php endif; ?>
<?php if ($flash): [$k,$v] = explode(':',$flash,2);
    if ($k==='ok'): ?><div class="alert alert-success py-2">Customer <?= e($v) ?>.</div>
<?php else: ?><div class="alert alert-danger py-2"><?= e(['required'=>'Name and email are required.','pass'=>'Set a password for new customers.','dupe'=>'Email already registered.'][$v] ?? 'Error') ?></div><?php endif; ?>
<?php endif; ?>

<div class="row">
    <div class="col-xl-4">
        <div class="card mb-4">
            <div class="card-header"><i class="fas fa-user-plus me-1"></i> <?= $edit ? 'Edit: ' . e($edit['name']) : 'New Customer' ?></div>
            <div class="card-body">
                <form method="post">
                    <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
                    <input type="hidden" name="act" value="save">
                    <input type="hidden" name="id" value="<?= $edit ? (int)$edit['id'] : 0 ?>">
                    <div class="mb-2"><label class="small text-muted">Name *</label><input class="form-control" name="name" value="<?= e($edit['name'] ?? '') ?>" required></div>
                    <div class="mb-2"><label class="small text-muted">Email *</label><input class="form-control" type="email" name="email" value="<?= e($edit['email'] ?? '') ?>" required></div>
                    <div class="mb-2"><label class="small text-muted">Phone</label><input class="form-control" name="phone" value="<?= e($edit['phone'] ?? '') ?>"></div>
                    <div class="mb-2"><label class="small text-muted">Company</label><input class="form-control" name="company" value="<?= e($edit['company'] ?? '') ?>"></div>
                    <div class="mb-2"><label class="small text-muted">Address</label><input class="form-control" name="address" value="<?= e($edit['address'] ?? '') ?>"></div>
                    <div class="mb-3"><label class="small text-muted"><?= $edit ? 'New password (leave blank to keep)' : 'Password *' ?></label><input class="form-control" type="text" name="password" <?= $edit ? '' : 'required' ?> autocomplete="off"></div>
                    <button class="btn btn-dark w-100"><?= $edit ? 'Save Changes' : 'Create Customer' ?></button>
                    <?php if ($edit): ?><a class="btn btn-link w-100" href="customers.php">Cancel</a><?php endif; ?>
                </form>
            </div>
        </div>
    </div>
    <div class="col-xl-8">
        <div class="card mb-4">
            <div class="card-header"><i class="fas fa-users me-1"></i> Customers <span class="badge bg-secondary ms-2"><?= count($rows) ?></span></div>
            <div class="card-body">
                <table id="datatablesSimple" class="table table-striped table-sm">
                    <thead><tr><th>#</th><th>Name</th><th>Contact</th><th>Orders</th><th class="text-end">Total spend</th><th>Status</th><th></th></tr></thead>
                    <tbody>
                    <?php foreach ($rows as $r): ?>
                    <tr>
                        <td><?= (int)$r['id'] ?></td>
                        <td><strong><?= e($r['name']) ?></strong><?php if ($r['company']): ?><br><span class="small text-muted"><?= e($r['company']) ?></span><?php endif; ?></td>
                        <td class="small"><?= e($r['email']) ?><?php if ($r['phone']): ?><br><?= e($r['phone']) ?><?php endif; ?></td>
                        <td><a href="orders.php?customer=<?= (int)$r['id'] ?>"><?= (int)$r['orders_n'] ?> order<?= $r['orders_n'] == 1 ? '' : 's' ?></a></td>
                        <td class="text-end"><?= usd($r['spent']) ?></td>
                        <td><span class="badge bg-<?= $r['status']==='active' ? 'success' : 'dark' ?>"><?= e($r['status']) ?></span></td>
                        <td class="text-nowrap">
                            <a class="btn btn-sm btn-outline-dark" href="customers.php?edit=<?= (int)$r['id'] ?>"><i class="fas fa-edit"></i></a>
                            <form method="post" class="d-inline">
                                <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
                                <input type="hidden" name="act" value="status">
                                <input type="hidden" name="id" value="<?= (int)$r['id'] ?>">
                                <input type="hidden" name="status" value="<?= $r['status']==='active' ? 'suspended' : 'active' ?>">
                                <button class="btn btn-sm btn-outline-<?= $r['status']==='active' ? 'danger' : 'success' ?>" title="<?= $r['status']==='active' ? 'Suspend' : 'Reactivate' ?>"><i class="fas fa-<?= $r['status']==='active' ? 'ban' : 'check' ?>"></i></button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
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
