<?php
require_once __DIR__ . '/inc.php';

if (is_customer()) { header('Location: index.php'); exit; }

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    cust_csrf_check();
    $email = trim($_POST['email'] ?? '');
    $pass  = (string)($_POST['password'] ?? '');

    $row = null;
    if ($pdo) {
        try {
            $st = $pdo->prepare('SELECT id, name, password_hash, status FROM customers WHERE email = ? LIMIT 1');
            $st->execute([$email]);
            $row = $st->fetch();
        } catch (Throwable $e) { /* table missing */ }
    }

    if ($row && $row['status'] === 'active' && password_verify($pass, $row['password_hash'])) {
        session_regenerate_id(true);
        $_SESSION['cust_id']   = (int)$row['id'];
        $_SESSION['cust_name'] = $row['name'];
        try { $pdo->prepare('UPDATE customers SET last_login = NOW() WHERE id=?')->execute([$row['id']]); } catch (Throwable $e) {}
        header('Location: index.php');
        exit;
    }
    $error = $pdo === null
        ? 'Service temporarily unavailable — please try later.'
        : ($row && $row['status'] !== 'active' ? 'Your account is suspended — contact Vuemax.' : 'Incorrect email or password.');
}

$pageTitle = 'Customer Login';
$pageDesc  = 'Sign in to your Vuemax account to track orders.';
$active    = 'account';
$extraCss  = <<<'CSS'
.auth-wrap{max-width:420px;margin:56px auto;padding:0 20px;}
.auth-card{background:var(--white,#fff);border:1px solid var(--border,#E7E2DA);border-radius:16px;padding:34px 30px;box-shadow:0 16px 40px rgba(10,29,51,.08);}
.auth-card h1{font-size:22px;text-align:center;margin-bottom:4px;}
.auth-card .sub{font-size:12.5px;color:#6B7280;text-align:center;margin-bottom:22px;}
.auth-card label{display:block;font-size:12px;font-weight:600;color:#374151;margin-bottom:5px;}
.auth-card input{width:100%;padding:11px 12px;border:1.5px solid #E5E7EB;border-radius:8px;font-size:14px;margin-bottom:14px;outline:none;}
.auth-card input:focus{border-color:#F2A33C;}
.auth-card .btn{width:100%;justify-content:center;}
.auth-err{background:#FEF2F2;border:1px solid #FECACA;color:#B91C1C;font-size:12.5px;padding:9px 12px;border-radius:8px;margin-bottom:14px;}
.auth-link{display:block;text-align:center;margin-top:16px;font-size:12.5px;color:#6B7280;}
CSS;
$base = '../';
require __DIR__ . '/../includes/header.php';
?>
<div class="auth-wrap">
    <form class="auth-card" method="post" action="login.php">
        <h1>Customer Login</h1>
        <p class="sub">Track orders and deliveries</p>
        <?php if ($error): ?><div class="auth-err"><?= e($error) ?></div><?php endif; ?>
        <input type="hidden" name="csrf" value="<?= e(cust_csrf_token()) ?>">
        <label>Email address</label>
        <input type="email" name="email" autocomplete="email" required>
        <label>Password</label>
        <input type="password" name="password" autocomplete="current-password" required>
        <button class="btn btn-primary" type="submit">Sign In</button>
        <a class="auth-link" href="register.php">No account yet? Create one</a>
    </form>
</div>
<?php require __DIR__ . '/../includes/footer.php'; ?>
