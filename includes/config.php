<?php
/**
 * Vuemax — Database configuration
 * File: /public_html/includes/config.php
 *
 * Edit the credentials below to match your cPanel MySQL database.
 */

// ---- EDIT THESE ----
$DB_HOST = 'localhost';
$DB_NAME = 'vuemax_db';      // your cPanel DB name (often prefixed, e.g. user_vuemax)
$DB_USER = 'vuemax_user';    // your cPanel DB user
$DB_PASS = 'CHANGE_ME';      // your cPanel DB password
// --------------------

try {
    $pdo = new PDO(
        "mysql:host={$DB_HOST};dbname={$DB_NAME};charset=utf8mb4",
        $DB_USER,
        $DB_PASS,
        [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ]
    );
} catch (PDOException $e) {
    // In production, log this instead of echoing. For now, silent fail
    // so pages still render with fallback content.
    $pdo = null;
}

/**
 * Small helper: safe HTML escape
 */
function e($s) { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }

/**
 * Small helper: format USD price
 */
function usd($n) { return '$' . number_format((float)$n, 2); }