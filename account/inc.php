<?php
/**
 * Customer portal bootstrap — separate session keys from /admin.
 * Customers can: view own orders + tracking, edit profile, change password.
 * Customers cannot: see other customers' data, modify orders, access /admin.
 */

require_once __DIR__ . '/../includes/config.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function is_customer() {
    return !empty($_SESSION['cust_id']);
}

function require_customer() {
    if (!is_customer()) {
        header('Location: login.php');
        exit;
    }
}

function cust_csrf_token() {
    if (empty($_SESSION['cust_csrf'])) {
        $_SESSION['cust_csrf'] = bin2hex(random_bytes(24));
    }
    return $_SESSION['cust_csrf'];
}

function cust_csrf_check() {
    $t = $_POST['csrf'] ?? '';
    if (!$t || !hash_equals($_SESSION['cust_csrf'] ?? '', $t)) {
        http_response_code(403);
        exit('Invalid request token.');
    }
}

function current_customer() {
    global $pdo;
    static $c = false;
    if ($c === false) {
        $c = null;
        if ($pdo && is_customer()) {
            try {
                $st = $pdo->prepare('SELECT * FROM customers WHERE id=? AND status="active"');
                $st->execute([$_SESSION['cust_id']]);
                $c = $st->fetch() ?: null;
            } catch (Throwable $e) { $c = null; }
        }
    }
    return $c;
}
