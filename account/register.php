<?php
require_once __DIR__ . '/inc.php';

if (is_customer()) { header('Location: index.php'); exit; }

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    cust_csrf_check();
    $name  = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $comp  = trim($_POST['company'] ?? '');
    $pass  = (string)($_POST['password'] ?? '');
    $pass2 = (string)($_POST['password2'] ?? '');

    if (!$pdo) { $error = 'Service temporarily unavailable — please try later.'; }
    elseif (!$name || !$email || !$pass) { $error = 'Name, email and password are required.'; }
    elseif ($pass !== $pass2) { $error = 'Passwords do not match.'; }
    elseif (strlen($pass) < 8) { $error = 'Password must be at least 8 characters.'; }
    else {
        try {
            $pdo->prepare('INSERT INTO customers (name,email,phone,company,password_hash) VALUES (?,?,?,?,?)')
                ->execute([$name, $email, $phone, $comp, password_hash($pass, PASSWORD_DEFAULT)]);
            $id = (int)$pdo->lastInsertId();
            session_regenerate_id(true);
            $_SESSION['cust_id']   = $id;
            $_SESSION['cust_name'] = $name;
            header('Location: index.php');
            exit;
        } catch (Throwable $e) {
            $error = str_contains($e->getMessage(), 'Duplicate') ? 'An account with that email already exists.' : 'Could not create account — try again.';
        }
    }
}

$pageTitle = 'Create Account';
$pageDesc  = 'Create a Vuemax customer account.';
$active    = 'account';
$extraCss  = <<<'CSS'
.auth-wrap{max-width:460px;margin:56px auto;padding:0 20px;}
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
    <form class="auth-card" method="post" action="register.php">
        <h1>Create Account</h1>
        <p class="sub">Track your orders and deliveries</p>
        <?php if ($error): ?><div class="auth-err"><?= e($error) ?></div><?php endif; ?>
        <input type="hidden" name="csrf" value="<?= e(cust_csrf_token()) ?>">
        <label>Full name *</label>
        <input name="name" value="<?= e($_POST['name'] ?? '') ?>" required>
        <label>Email *</label>
        <input type="email" name="email" value="<?= e($_POST['email'] ?? '') ?>" required>
        <label>Phone</label>
        <input name="phone" value="<?= e($_POST['phone'] ?? '') ?>">
        <label>Company (optional)</label>
        <input name="company" value="<?= e($_POST['company'] ?? '') ?>">
        <label>Password * (min 8 chars)</label>
        <input type="password" name="password" autocomplete="new-password" required>
        <label>Confirm password *</label>
        <input type="password" name="password2" autocomplete="new-password" required>
        <button class="btn btn-primary" type="submit">Create Account</button>
        <a class="auth-link" href="login.php">Already have an account? Sign in</a>
    </form>
</div>
<?php require __DIR__ . '/../includes/footer.php'; ?>
