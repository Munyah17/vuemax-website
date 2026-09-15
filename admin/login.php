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
<link href="assets/sb-styles.css" rel="stylesheet">
<script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
</head>
<body class="bg-dark">
<div id="layoutAuthentication">
    <div id="layoutAuthentication_content">
        <main>
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-lg-5">
                        <div class="card shadow-lg border-0 rounded-lg mt-5">
                            <div class="card-header">
                                <h3 class="text-center fw-semibold my-2"><i class="fas fa-cube me-2"></i>Vuemax Admin</h3>
                            </div>
                            <div class="card-body">
                                <?php if ($error): ?><div class="alert alert-danger py-2 small"><?= e($error) ?></div><?php endif; ?>
                                <form method="post" action="login.php">
                                    <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
                                    <div class="form-floating mb-3">
                                        <input class="form-control" id="inputUser" name="username" type="text" placeholder="username or email" autocomplete="username" required>
                                        <label for="inputUser">Username or Email</label>
                                    </div>
                                    <div class="form-floating mb-3">
                                        <input class="form-control" id="inputPassword" name="password" type="password" placeholder="Password" autocomplete="current-password" required>
                                        <label for="inputPassword">Password</label>
                                    </div>
                                    <div class="d-flex align-items-center justify-content-between mt-4 mb-0">
                                        <a class="small" href="../index.php">&larr; Back to website</a>
                                        <button type="submit" class="btn btn-dark">Login</button>
                                    </div>
                                </form>
                            </div>
                            <div class="card-footer text-center py-3">
                                <div class="small text-muted">Vuemax Industries — Back Office</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
<script src="assets/sb-scripts.js"></script>
</body>
</html>
