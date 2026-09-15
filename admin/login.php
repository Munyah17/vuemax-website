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
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
<meta name="robots" content="noindex,nofollow">
<title>Admin Login — Vuemax</title>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Playfair+Display:wght@700&display=swap" rel="stylesheet">
<style>
*{margin:0;padding:0;box-sizing:border-box;}
body{font-family:'Inter',sans-serif;min-height:100vh;display:flex;}

/* ---------- Left brand panel ---------- */
.brand-panel{
    width:44%;min-height:100vh;
    background:linear-gradient(160deg,#0E2745 0%,#132f55 55%,#0a1c33 100%);
    display:flex;flex-direction:column;
    padding:40px 48px;position:relative;overflow:hidden;
}
.brand-panel::before{
    content:'';position:absolute;bottom:-120px;left:-120px;width:380px;height:380px;
    background:radial-gradient(circle,rgba(242,163,60,.14),transparent 70%);
}
.brand-top{display:flex;align-items:center;gap:12px;color:#fff;}
.brand-top .mark{
    width:40px;height:40px;border-radius:11px;background:#fff;
    display:flex;align-items:center;justify-content:center;overflow:hidden;flex-shrink:0;
}
.brand-top .mark img{width:32px;height:32px;object-fit:contain;}
.brand-top strong{display:block;font-size:15px;font-weight:700;letter-spacing:.02em;}
.brand-top span{display:block;font-size:11.5px;color:rgba(255,255,255,.6);margin-top:1px;}
.brand-mid{flex:1;display:flex;align-items:center;justify-content:center;}
.logo-card{
    background:#fff;border-radius:20px;padding:44px 56px;
    box-shadow:0 30px 60px rgba(0,0,0,.35);
    display:flex;align-items:center;justify-content:center;
}
.logo-card img{width:300px;max-width:34vw;height:auto;display:block;}
.brand-bottom{color:rgba(255,255,255,.45);font-size:12px;}

/* ---------- Right form panel ---------- */
.form-panel{
    flex:1;min-height:100vh;background:#f5f6f8;
    display:flex;align-items:center;justify-content:center;padding:40px 24px;
}
.form-box{width:100%;max-width:380px;}
.form-box h1{font-family:'Playfair Display',serif;font-size:30px;font-weight:700;color:#0E2745;margin-bottom:6px;}
.form-box .sub{font-size:14px;color:#6b7280;margin-bottom:32px;}
.field{margin-bottom:20px;}
.field label{display:block;font-size:13px;font-weight:600;color:#111827;margin-bottom:8px;}
.field .in-wrap{position:relative;}
.field input{
    width:100%;padding:13px 14px;border:1.5px solid #e2e5ea;border-radius:10px;
    background:#fff;font:inherit;font-size:14px;color:#111827;outline:none;transition:.15s;
}
.field input:focus{border-color:#0E2745;box-shadow:0 0 0 3px rgba(14,39,69,.08);}
.field input::placeholder{color:#adb3bd;}
.eye-btn{
    position:absolute;right:12px;top:50%;transform:translateY(-50%);
    background:none;border:none;cursor:pointer;color:#9ca3af;padding:4px;display:flex;
}
.eye-btn:hover{color:#0E2745;}
.row-aux{display:flex;align-items:center;justify-content:space-between;margin-bottom:26px;}
.remember{display:flex;align-items:center;gap:8px;font-size:13.5px;color:#374151;cursor:pointer;}
.remember input{width:16px;height:16px;accent-color:#0E2745;cursor:pointer;}
.forgot{font-size:13.5px;color:#0E2745;text-decoration:none;font-weight:500;}
.forgot:hover{text-decoration:underline;}
.btn-sign{
    width:100%;padding:14px;border:none;border-radius:10px;cursor:pointer;
    background:#0E2745;color:#fff;font:inherit;font-size:15px;font-weight:600;
    transition:.2s;
}
.btn-sign:hover{background:#16365f;box-shadow:0 8px 20px rgba(14,39,69,.25);}
.login-err{
    background:#fef2f2;border:1px solid #fecaca;color:#b91c1c;
    font-size:13px;padding:11px 14px;border-radius:10px;margin-bottom:20px;
}
.trust-line{text-align:center;font-size:12.5px;color:#9ca3af;margin-top:36px;}
.trust-line a{color:#0E2745;text-decoration:none;font-weight:500;}
.trust-line a:hover{text-decoration:underline;}

@media (max-width:820px){
    body{flex-direction:column;}
    .brand-panel{width:100%;min-height:0;padding:28px 24px 40px;}
    .logo-card{padding:28px 32px;}
    .logo-card img{width:200px;max-width:60vw;}
    .brand-bottom{display:none;}
    .form-panel{min-height:0;padding:44px 24px 56px;}
}
</style>
</head>
<body>

<div class="brand-panel">
    <div class="brand-top">
        <div class="mark"><img src="../assets/img/logo.png" alt="Vuemax"></div>
        <div>
            <strong>VUEMAX INDUSTRIES</strong>
            <span>Steel. Wire. Fencing.</span>
        </div>
    </div>
    <div class="brand-mid">
        <div class="logo-card"><img src="../assets/img/logo.png" alt="VueMax Industries"></div>
    </div>
    <div class="brand-bottom">Back-office control panel</div>
</div>

<div class="form-panel">
    <div class="form-box">
        <h1>Welcome Back</h1>
        <p class="sub">Sign in to your account</p>

        <?php if ($error): ?><div class="login-err"><?= e($error) ?></div><?php endif; ?>

        <form method="post" action="login.php">
            <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
            <div class="field">
                <label for="inputUser">Email or Username</label>
                <div class="in-wrap">
                    <input id="inputUser" name="username" type="text" placeholder="Enter your email or username" autocomplete="username" required>
                </div>
            </div>
            <div class="field">
                <label for="inputPassword">Password</label>
                <div class="in-wrap">
                    <input id="inputPassword" name="password" type="password" placeholder="Enter your password" autocomplete="current-password" required>
                    <button type="button" class="eye-btn" onclick="var i=document.getElementById('inputPassword');i.type=i.type==='password'?'text':'password';this.style.color=i.type==='text'?'#0E2745':'';" aria-label="Show password">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                    </button>
                </div>
            </div>
            <div class="row-aux">
                <label class="remember"><input type="checkbox" name="remember" checked> Remember me</label>
                <a class="forgot" href="../contact.php">Forgot password?</a>
            </div>
            <button type="submit" class="btn-sign">Sign In</button>
        </form>

        <div class="trust-line">Trusted by teams for secure operations across Zimbabwe.<br><br><a href="../index.php">&larr; Back to website</a></div>
    </div>
</div>

</body>
</html>
