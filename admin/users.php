<?php
require_once __DIR__ . '/inc.php';
require_admin();

$noDb = ($pdo === null);
$flash = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $pdo) {
    csrf_check();
    $act = $_POST['act'] ?? '';
    try {
        if ($act === 'create') {
            $user  = trim($_POST['username'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $pass  = (string)($_POST['password'] ?? '');
            $role  = in_array($_POST['role'] ?? '', ['admin','editor','viewer'], true) ? $_POST['role'] : 'editor';
            if (!$user || !$email || !$pass) { header('Location: users.php?err=required'); exit; }
            $pdo->prepare('INSERT INTO admin_users (username,email,password_hash,role) VALUES (?,?,?,?)')
                ->execute([$user, $email, password_hash($pass, PASSWORD_DEFAULT), $role]);
            header('Location: users.php?ok=created'); exit;
        }
        if ($act === 'deactivate' || $act === 'activate' || $act === 'delete') {
            $tid = (int)($_POST['id'] ?? 0);
            // protected accounts can never be deleted or deactivated
            $st = $pdo->prepare('SELECT is_protected FROM admin_users WHERE id=?'); $st->execute([$tid]);
            $target = $st->fetch();
            if (!$target) { header('Location: users.php?err=missing'); exit; }
            if ($target['is_protected']) { header('Location: users.php?err=protected'); exit; }
            if ($tid === (int)$_SESSION['admin_id'] && $act !== 'activate') { header('Location: users.php?err=self'); exit; }
            if ($act === 'delete') {
                $pdo->prepare('DELETE FROM admin_users WHERE id=?')->execute([$tid]);
            } else {
                $pdo->prepare('UPDATE admin_users SET is_active=? WHERE id=?')->execute([$act === 'activate' ? 1 : 0, $tid]);
            }
            header('Location: users.php?ok=' . $act . 'd'); exit;
        }
        if ($act === 'resetpw') {
            $tid  = (int)($_POST['id'] ?? 0);
            $pass = (string)($_POST['password'] ?? '');
            if ($pass) {
                $pdo->prepare('UPDATE admin_users SET password_hash=? WHERE id=?')->execute([password_hash($pass, PASSWORD_DEFAULT), $tid]);
            }
            header('Location: users.php?ok=reset'); exit;
        }
    } catch (Throwable $e) {
        header('Location: users.php?err=' . (str_contains($e->getMessage(), 'Duplicate') ? 'dupe' : 'db')); exit;
    }
}

$rows = [];
if ($pdo) {
    try { $rows = $pdo->query('SELECT * FROM admin_users ORDER BY is_protected DESC, id')->fetchAll(); }
    catch (Throwable $e) { $noDb = true; }
}
if (isset($_GET['ok']))  $flash = 'ok:' . $_GET['ok'];
if (isset($_GET['err'])) $flash = 'err:' . $_GET['err'];

admin_head('Admin Users');
admin_nav('users');
?>
<h1 class="mt-4">Admin Users</h1>
<ol class="breadcrumb mb-4">
    <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
    <li class="breadcrumb-item active">Admin Users</li>
</ol>

<?php if ($noDb): ?><div class="alert alert-warning"><strong>Database not connected.</strong></div><?php endif; ?>
<?php if ($flash): [$k,$v]=explode(':',$flash,2);
    if ($k==='ok'): ?><div class="alert alert-success py-2">Done: <?= e($v) ?></div>
<?php else: ?><div class="alert alert-danger py-2"><?= e([
    'protected' => 'That account is protected — it can never be deleted or deactivated.',
    'self'      => 'You cannot deactivate or delete your own account.',
    'required'  => 'Username, email and password are required.',
    'dupe'      => 'Username or email already exists.',
    'missing'   => 'User not found.'][$v] ?? 'Error') ?></div><?php endif; ?>
<?php endif; ?>

<div class="row">
    <div class="col-xl-4">
        <div class="card mb-4">
            <div class="card-header"><i class="fas fa-user-plus me-1"></i> New Admin</div>
            <div class="card-body">
                <form method="post">
                    <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
                    <input type="hidden" name="act" value="create">
                    <div class="mb-2"><label class="small text-muted">Username *</label><input class="form-control" name="username" required></div>
                    <div class="mb-2"><label class="small text-muted">Email *</label><input class="form-control" type="email" name="email" required></div>
                    <div class="mb-2"><label class="small text-muted">Password *</label><input class="form-control" type="text" name="password" required autocomplete="off"></div>
                    <div class="mb-3"><label class="small text-muted">Role</label>
                        <select class="form-select" name="role">
                            <option value="editor">Editor — manage content &amp; orders</option>
                            <option value="viewer">Viewer — read-only</option>
                            <option value="admin">Admin — full access</option>
                        </select></div>
                    <button class="btn btn-dark w-100">Create Admin</button>
                </form>
            </div>
        </div>
    </div>
    <div class="col-xl-8">
        <div class="card mb-4">
            <div class="card-header"><i class="fas fa-user-shield me-1"></i> Accounts <span class="badge bg-secondary ms-2"><?= count($rows) ?></span></div>
            <div class="card-body">
                <table class="table table-striped table-sm">
                    <thead><tr><th>User</th><th>Email</th><th>Role</th><th>Last login</th><th>Status</th><th></th></tr></thead>
                    <tbody>
                    <?php foreach ($rows as $u): $prot = (int)$u['is_protected'] === 1; ?>
                    <tr>
                        <td><strong><?= e($u['username']) ?></strong>
                            <?= $prot ? '<span class="badge bg-dark ms-1"><i class="fas fa-lock"></i> protected</span>' : '' ?>
                            <?= $u['id'] == $_SESSION['admin_id'] ? '<span class="badge bg-info ms-1">you</span>' : '' ?></td>
                        <td class="small"><?= e($u['email']) ?></td>
                        <td><span class="badge bg-secondary"><?= e($u['role']) ?></span></td>
                        <td class="small"><?= e($u['last_login'] ? substr($u['last_login'],0,16) : 'never') ?></td>
                        <td><span class="badge bg-<?= $u['is_active'] ? 'success' : 'danger' ?>"><?= $u['is_active'] ? 'active' : 'disabled' ?></span></td>
                        <td class="text-nowrap">
                            <!-- reset password -->
                            <form method="post" class="d-inline">
                                <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
                                <input type="hidden" name="act" value="resetpw">
                                <input type="hidden" name="id" value="<?= (int)$u['id'] ?>">
                                <input name="password" placeholder="new pw" class="form-control form-control-sm d-inline" style="width:90px;display:inline" required>
                                <button class="btn btn-sm btn-outline-dark py-0" title="Set password"><i class="fas fa-key"></i></button>
                            </form>
                            <?php if (!$prot && $u['id'] != $_SESSION['admin_id']): ?>
                            <form method="post" class="d-inline">
                                <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
                                <input type="hidden" name="id" value="<?= (int)$u['id'] ?>">
                                <input type="hidden" name="act" value="<?= $u['is_active'] ? 'deactivate' : 'activate' ?>">
                                <button class="btn btn-sm btn-outline-<?= $u['is_active'] ? 'warning' : 'success' ?> py-0" title="<?= $u['is_active'] ? 'Deactivate' : 'Activate' ?>"><i class="fas fa-<?= $u['is_active'] ? 'ban' : 'check' ?>"></i></button>
                            </form>
                            <form method="post" class="d-inline" onsubmit="return confirm('Delete this admin account?')">
                                <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
                                <input type="hidden" name="id" value="<?= (int)$u['id'] ?>">
                                <input type="hidden" name="act" value="delete">
                                <button class="btn btn-sm btn-outline-danger py-0" title="Delete"><i class="fas fa-trash"></i></button>
                            </form>
                            <?php endif; ?>
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
admin_footer();
