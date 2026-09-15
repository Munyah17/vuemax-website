<?php
/**
 * Vuemax Database configuration
 * File: /public_html/includes/config.php
 *
 * Edit the credentials below to match your cPanel MySQL database.
 */

// ---- EDIT THESE ----
$DB_HOST = 'localhost';
$DB_NAME = 'vuemax_db'; // your cPanel DB name (often prefixed, e.g. user_vuemax)
$DB_USER = 'vuemax_user'; // your cPanel DB user
$DB_PASS = 'CHANGE_ME'; // your cPanel DB password
// --------------------

try {
 $pdo = new PDO(
 "mysql:host={$DB_HOST};dbname={$DB_NAME};charset=utf8mb4",
 $DB_USER,
 $DB_PASS,
 [
 PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
 PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
 PDO::ATTR_EMULATE_PREPARES => false,
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

/**
 * Image registry looks up an image slot in the site_images table so
 * admins can swap any image from /admin. Falls back to the bundled
 * default when the DB is unavailable or the slot is unset.
 */
function site_image($key, $fallback = '') {
 static $map = null;
 global $pdo;
 if ($map === null) {
 $map = [];
 if ($pdo) {
 try {
 foreach ($pdo->query('SELECT img_key, path FROM site_images') as $r) {
 if ($r['path'] !== '') $map[$r['img_key']] = $r['path'];
 }
 } catch (Throwable $e) { /* table missing use fallbacks */ }
 }
 }
 return $map[$key] ?? $fallback;
}

/**
 * Renders the logo mark — an uploaded image if an admin set the 'logo'
 * slot, otherwise the default V emblem.
 */
function logo_mark_html() {
 global $base;
 $img = site_image('logo', 'assets/img/logo.png');
 if ($img) {
 $src = ($img && strpos($img, 'http') !== 0) ? ($base ?? '') . $img : $img;
 return '<img class="logo-mark logo-img" src="' . e($src) . '" alt="Vuemax Industries">';
 }
 return '<div class="logo-mark">V</div>';
}