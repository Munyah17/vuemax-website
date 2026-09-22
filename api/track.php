<?php
/**
 * ============================================================
 * VUEMAX API — visitor analytics beacon
 * ============================================================
 * File: /api/track.php
 *
 * Receives a lightweight page-view ping from the front-end
 * (navigator.sendBeacon in includes/footer.php) and logs it
 * into `page_views` for the admin Visitor Analytics module.
 *
 * Privacy: the raw IP is never stored — we keep a daily-rotating
 * SHA-256 hash so "unique visitors per day" can be counted
 * without retaining identifiable addresses.
 * ============================================================
 */

require_once __DIR__ . '/config.php';

/* ---------- Read payload (JSON body or form fields) ---------- */
$body = read_json_body();

$path  = clean_str(g($body, 'path', ''), 255);
$title = clean_str(g($body, 'title', ''), 200);
$ref   = clean_str(g($body, 'ref', ''), 255);
$slug  = clean_str(g($body, 'slug', ''), 120);

/* Normalise: keep path only, drop the scheme/host if a full URL was sent */
if ($path !== '') {
    $p = parse_url($path, PHP_URL_PATH);
    $q = parse_url($path, PHP_URL_QUERY);
    $path = ($p ?: '/') . ($q ? '?' . $q : '');
}

/* Skip noise: admin area, api calls, missing path */
if ($path === '' || preg_match('#^/(admin|api)(/|$)#i', $path)) {
    json_response(['ok' => true, 'skipped' => true]);
}

/* Referrer: keep only external referrers (internal nav is noise) */
$refHost = $ref ? parse_url($ref, PHP_URL_HOST) : null;
$siteHost = parse_url(SITE_URL, PHP_URL_HOST);
if ($refHost && $siteHost && strcasecmp($refHost, $siteHost) === 0) {
    $ref = '';
}

/* ---------- Device class (cheap UA sniff) ---------- */
$ua = substr($_SERVER['HTTP_USER_AGENT'] ?? '', 0, 250);
$device = 'desktop';
if (preg_match('/tablet|ipad/i', $ua)) {
    $device = 'tablet';
} elseif (preg_match('/mobile|android|iphone|ipod|phone/i', $ua)) {
    $device = 'mobile';
}

/* ---------- Visitor hash (daily rotation, no raw IP stored) ---------- */
$ip = $_SERVER['REMOTE_ADDR'] ?? '';
$visitor = hash('sha256', $ip . '|' . date('Y-m-d') . '|vuemax-analytics');

/* ---------- Insert ---------- */
if ($pdo) {
    try {
        $pdo->prepare('
            INSERT INTO page_views (path, title, referrer, product_slug, device, visitor, user_agent)
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ')->execute([
            $path,
            $title ?: null,
            $ref ?: null,
            $slug ?: null,
            $device,
            $visitor,
            $ua ?: null,
        ]);
    } catch (Throwable $e) {
        /* Table may not exist yet — fail silently, tracking must never break the site */
    }
}

json_response(['ok' => true]);
