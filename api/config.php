<?php
/**
 * ============================================================
 * VUEMAX API CONFIGURATION
 * ============================================================
 * File: /api/config.php
 *
 * ⚠️ EDIT THE FIVE VALUES BELOW WHEN YOU DEPLOY TO cPANEL ⚠️
 *
 * On cPanel, your database name and user will usually be
 * prefixed with your cPanel account username, e.g.:
 *
 * DB name: vuemax_user_main
 * DB user: vuemax_user_admin
 *
 * Create these in cPanel → MySQL Databases, then paste them
 * below and update the password.
 * ============================================================
 */

// ============ EDIT THESE FIVE LINES ON DEPLOY ============

$DB_HOST = 'localhost'; // Usually 'localhost' on cPanel / XAMPP
$DB_NAME = 'vuemax_db'; // XAMPP: 'vuemax_db'. cPanel: usually prefixed with your account name
$DB_USER = 'root';      // XAMPP default. cPanel: your DB user
$DB_PASS = '';          // XAMPP default is blank. cPanel: your DB password
$DB_CHARSET = 'utf8mb4';

// AI estimator (Groq, OpenAI-compatible). Free key: https://console.groq.com
// The key lives in api/secrets.php (gitignored) — copy secrets.example.php.
if (file_exists(__DIR__ . '/secrets.php')) {
    require_once __DIR__ . '/secrets.php';
}
if (!defined('GROQ_API_KEY')) {
    define('GROQ_API_KEY', getenv('GROQ_API_KEY') ?: '');
}
define('GROQ_MODEL', 'openai/gpt-oss-120b');

// ==========================================================
// DO NOT EDIT BELOW THIS LINE UNLESS YOU KNOW WHAT YOU'RE DOING
// ==========================================================

/* ---------- Site constants (used for emails / links) ---------- */
define('SITE_NAME', 'Vuemax Industries');
define('SITE_URL', 'https://vuemax.co.zw'); // Change to your real domain
define('SITE_EMAIL', 'sales@vuemax.co.zw');
define('SITE_PHONE', '0784 689 857');
define('SITE_ADDRESS', '103 Willowvale Rd, Harare, Zimbabwe');

/* ---------- Error reporting ----------
 Set DEBUG to false when going live to avoid leaking SQL errors. */
define('DEBUG', true);
if (DEBUG) {
 ini_set('display_errors', 1);
 error_reporting(E_ALL);
} else {
 ini_set('display_errors', 0);
 error_reporting(0);
}

/* ---------- CORS + JSON headers ----------
 Since HTML and API live on the same domain, CORS is open here
 only to make local testing easy. Tighten this on production if needed. */
header('Content-Type: application/json; charset=utf-8');
header('X-Content-Type-Options: nosniff');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Pre-flight
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
 http_response_code(204);
 exit;
}

/* ---------- PDO connection ---------- */
$pdo = null;
$db_error = null;

try {
 $dsn = "mysql:host={$DB_HOST};dbname={$DB_NAME};charset={$DB_CHARSET}";
 $pdo = new PDO($dsn, $DB_USER, $DB_PASS, [
 PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
 PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
 PDO::ATTR_EMULATE_PREPARES => false,
 PDO::ATTR_STRINGIFY_FETCHES => false,
 ]);
} catch (PDOException $e) {
 $db_error = DEBUG ? $e->getMessage() : 'Database connection failed.';
 // Do NOT die here individual endpoints decide how to respond.
}

/* ============================================================
 HELPER FUNCTIONS
 ============================================================ */

/**
 * Send a JSON response and exit.
 */
function json_response($data, $status = 200) {
 http_response_code($status);
 echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
 exit;
}

/**
 * Send a JSON error response and exit.
 */
function json_error($message, $status = 400, $extra = []) {
 json_response(array_merge(['ok' => false, 'error' => $message], $extra), $status);
}

/**
 * Read JSON body from POST request (for fetch() with Content-Type: application/json).
 * Falls back to $_POST if body is form-encoded.
 */
function read_json_body() {
 $raw = file_get_contents('php://input');
 if ($raw === false || $raw === '') return $_POST;
 $decoded = json_decode($raw, true);
 return is_array($decoded) ? $decoded : $_POST;
}

/**
 * Safe string getter with default.
 */
function g($arr, $key, $default = null) {
 return isset($arr[$key]) ? $arr[$key] : $default;
}

/**
 * Escape output (for use in HTML if needed most endpoints just return JSON).
 */
function e($s) {
 return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8');
}

/**
 * Format a price in USD.
 */
function usd($n) {
 return '$' . number_format((float)$n, 2);
}

/**
 * Require DB call this at the top of any endpoint that needs MySQL.
 * Fails cleanly with a JSON error if the connection is broken.
 */
function require_db($pdo, $db_error) {
 if (!$pdo) {
 json_error('Database unavailable. ' . ($db_error ?: ''), 503);
 }
}

/**
 * Require a specific HTTP method, or fail.
 */
function require_method($method) {
 if (strtoupper($_SERVER['REQUEST_METHOD']) !== strtoupper($method)) {
 json_error('Method not allowed. Expected ' . strtoupper($method) . '.', 405);
 }
}

/**
 * Basic input sanitation trim and strip null bytes.
 */
function clean_str($s, $max = 500) {
 $s = trim((string)$s);
 $s = str_replace("\0", '', $s);
 if (mb_strlen($s) > $max) $s = mb_substr($s, 0, $max);
 return $s;
}