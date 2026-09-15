<?php
require_once __DIR__ . '/inc.php';
require_customer();

$cust = current_customer();
if (!$cust) { session_destroy(); header('Location: login.php'); exit; }

$flash = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $pdo) {
    cust_csrf_check();
    $act = $_POST['act'] ?? '';
    try {
        if ($act === 'profile') {
            $pdo->prepare('UPDATE customers SET name=?, phone=?, company=?, address=? WHERE id=?')
                ->execute([trim($_POST['name'] ?? '') ?: $cust['name'], trim($_POST['phone'] ?? ''), trim($_POST['company'] ?? ''), trim($_POST['address'] ?? ''), $cust['id']]);
            $flash = 'ok:Profile updated.';
        }
        if ($act === 'password') {
            $cur = (string)($_POST['current'] ?? '');
            $new = (string)($_POST['new'] ?? '');
            $cfm = (string)($_POST['confirm'] ?? '');
            if (!password_verify($cur, $cust['password_hash'])) { $flash = 'err:Current password is incorrect.'; }
            elseif (strlen($new) < 8) { $flash = 'err:New password must be at least 8 characters.'; }
            elseif ($new !== $cfm) { $flash = 'err:New passwords do not match.'; }
            else {
                $pdo->prepare('UPDATE customers SET password_hash=? WHERE id=?')->execute([password_hash($new, PASSWORD_DEFAULT), $cust['id']]);
                $flash = 'ok:Password changed.';
            }
        }
    } catch (Throwable $e) { $flash = 'err:Could not save — try again.'; }
    $cust = null; // refetch
    $st = $pdo->prepare('SELECT * FROM customers WHERE id=?'); $st->execute([$_SESSION['cust_id']]);
    $cust = $st->fetch();
}

$pageTitle = 'Edit Profile';
$pageDesc  = 'Update your Vuemax account details.';
$active    = 'account';
$extraCss  = <<<'CSS'
.acct{max-width:720px;margin:40px auto 70px;padding:0 20px;}
.acct-card{background:#fff;border:1px solid #E7E2DA;border-radius:14px;padding:22px;margin-bottom:18px;}
.acct-card h2{font-size:15px;margin-bottom:14px;padding-bottom:10px;border-bottom:2px solid #F4F1EC;}
.acct-card label{display:block;font-size:12px;font-weight:600;color:#374151;margin-bottom:5px;}
.acct-card input{width:100%;padding:10px 12px;border:1.5px solid #E5E7EB;border-radius:8px;font-size:14px;margin-bottom:14px;outline:none;}
.acct-card input:focus{border-color:#F2A33C;}
.flash{padding:10px 14px;border-radius:8px;font-size:13px;margin-bottom:18px;}
.flash.ok{background:#ECFDF5;border:1px solid #A7F3D0;color:#047857;}
.flash.err{background:#FEF2F2;border:1px solid #FECACA;color:#B91C1C;}
CSS;
$base = '../';
require __DIR__ . '/../includes/header.php';
?>
<div class="acct">
    <p><a href="index.php">&larr; My account</a></p>
    <?php if ($flash): [$k,$m]=explode(':',$flash,2); ?>
        <div class="flash <?= $k ?>"><?= e($m) ?></div>
    <?php endif; ?>

    <div class="acct-card">
        <h2>Profile</h2>
        <form method="post">
            <input type="hidden" name="csrf" value="<?= e(cust_csrf_token()) ?>">
            <input type="hidden" name="act" value="profile">
            <label>Name</label><input name="name" value="<?= e($cust['name']) ?>" required>
            <label>Phone</label><input name="phone" value="<?= e($cust['phone']) ?>">
            <label>Company</label><input name="company" value="<?= e($cust['company']) ?>">
            <label>Delivery address</label><input name="address" value="<?= e($cust['address']) ?>">
            <button class="btn btn-primary">Save Profile</button>
        </form>
    </div>

    <div class="acct-card">
        <h2>Change Password</h2>
        <form method="post">
            <input type="hidden" name="csrf" value="<?= e(cust_csrf_token()) ?>">
            <input type="hidden" name="act" value="password">
            <label>Current password</label><input type="password" name="current" autocomplete="current-password" required>
            <label>New password (min 8 chars)</label><input type="password" name="new" autocomplete="new-password" required>
            <label>Confirm new password</label><input type="password" name="confirm" autocomplete="new-password" required>
            <button class="btn btn-primary">Change Password</button>
        </form>
    </div>
</div>
<?php require __DIR__ . '/../includes/footer.php'; ?>
