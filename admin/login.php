<?php
require_once __DIR__ . '/inc.php';

if (is_admin()) { header('Location: index.php'); exit; }

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $user = trim($_POST['username'] ?? '');
    $pass = (string)($_POST['password'] ?? '');

    $row = null;
    if ($pdo) {
        try {
            $st = $pdo->prepare('SELECT id, username, password_hash, is_active FROM admin_users WHERE (username = ? OR email = ?) LIMIT 1');
            $st->execute([$user, $user]);
            $row = $st->fetch();
        } catch (Throwable $e) { /* table missing */ }
    }

    if ($row && (int)$row['is_active'] === 1 && password_verify($pass, $row['password_hash'])) {
        session_regenerate_id(true);
        $_SESSION['admin_id']   = (int)$row['id'];
        $_SESSION['admin_user'] = $row['username'];
        try { $pdo->prepare('UPDATE admin_users SET last_login = NOW() WHERE id = ?')->execute([$row['id']]); } catch (Throwable $e) {}
        header('Location: index.php');
        exit;
    }
    $error = $pdo === null
        ? 'Database unavailable — check includes/config.php credentials.'
        : 'Incorrect username or password.';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="robots" content="noindex">
<title>Admin Login — Vuemax</title>
<style>
  *{margin:0;padding:0;box-sizing:border-box;}
  body{font-family:'Segoe UI',system-ui,sans-serif;background:#0A1D33;min-height:100vh;display:flex;align-items:center;justify-content:center;padding:20px;}
  .card{background:#fff;border-radius:16px;padding:36px 32px;width:100%;max-width:360px;box-shadow:0 24px 60px rgba(0,0,0,.4);}
  .mark{width:44px;height:44px;border-radius:10px;background:#0A1D33;color:#F2A33C;font-size:24px;font-weight:800;display:flex;align-items:center;justify-content:center;margin:0 auto 14px;}
  h1{font-size:20px;text-align:center;color:#0A1D33;margin-bottom:4px;}
  .sub{font-size:12.5px;color:#6B7280;text-align:center;margin-bottom:22px;}
  label{display:block;font-size:12px;font-weight:600;color:#374151;margin-bottom:5px;}
  input{width:100%;padding:11px 12px;border:1.5px solid #E5E7EB;border-radius:8px;font-size:14px;margin-bottom:14px;outline:none;}
  input:focus{border-color:#F2A33C;}
  button{width:100%;padding:12px;background:#F2A33C;border:0;border-radius:8px;font-size:14px;font-weight:700;color:#0A1D33;cursor:pointer;}
  button:hover{background:#E0952E;}
  .err{background:#FEF2F2;border:1px solid #FECACA;color:#B91C1C;font-size:12.5px;padding:9px 12px;border-radius:8px;margin-bottom:14px;}
  .back{display:block;text-align:center;margin-top:16px;font-size:12px;color:#6B7280;text-decoration:none;}
  .back:hover{color:#0A1D33;}
</style>
</head>
<body>
  <form class="card" method="post" action="login.php">
    <div class="mark">V</div>
    <h1>Vuemax Admin</h1>
    <p class="sub">Sign in to manage site images</p>
    <?php if ($error): ?><div class="err"><?= e($error) ?></div><?php endif; ?>
    <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
    <label for="u">Username or Email</label>
    <input id="u" name="username" autocomplete="username" required>
    <label for="p">Password</label>
    <input id="p" type="password" name="password" autocomplete="current-password" required>
    <button type="submit">Sign In</button>
    <a class="back" href="../index.php">&larr; Back to website</a>
  </form>
</body>
</html>
