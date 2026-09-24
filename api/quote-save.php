<?php
/**
 * ============================================================
 * VUEMAX API POST save a quote
 * ============================================================
 * File: /api/quote-save.php
 *
 * Accepts a JSON body (or form-encoded) with the quote payload
 * from calculator.html or estimator.html, persists it into
 * `quotes` + `quote_items`, and returns the generated reference.
 *
 * Request body example:
 * {
 * "source": "calculator",
 * "customer": {
 * "name": "John Moyo",
 * "phone": "0784 000 000",
 * "email": "john@example.com",
 * "notes": "Fence the back garden"
 * },
 * "project": {
 * "perimeter": 800,
 * "corners": 4,
 * "height": 1.8,
 * "spacing": 2.5,
 * "type": "diamond-mesh",
 * "typeName": "Diamond Mesh"
 * },
 * "options": { "topWire": true, "gate": true, "install": false, "concrete": false },
 * "items": [
 * { "name": "Diamond Mesh (1.8m)", "spec": "30 m rolls · galvanised",
 * "qty": "27 rolls", "unit": 120.00, "total": 3240.00 },
 * ...
 * ],
 * "total": 6300.00
 * }
 *
 * Response:
 * {
 * "ok": true,
 * "ref": "VX-2024-00042",
 * "token": "3f1c2b8e-...", // opaque token for retrieval
 * "total": 6300.00
 * }
 * ============================================================
 */

require_once __DIR__ . '/config.php';
require_db($pdo, $db_error);
require_method('POST');

/* ---------- Read body ---------- */
$body = read_json_body();

if (!is_array($body) || empty($body)) {
 json_error('Empty or invalid request body.', 400);
}

/* ---------- Extract & sanitize ---------- */
$source = clean_str(g($body, 'source', 'calculator'), 20);
if (!in_array($source, ['calculator', 'estimator', 'admin', 'product'], true)) {
 $source = 'calculator';
}

$customer = is_array(g($body, 'customer', [])) ? $body['customer'] : [];
$project = is_array(g($body, 'project', [])) ? $body['project'] : [];
$options = is_array(g($body, 'options', [])) ? $body['options'] : [];
$items = is_array(g($body, 'items', [])) ? $body['items'] : [];

$cust_name = clean_str(g($customer, 'name', ''), 160);
$cust_phone = clean_str(g($customer, 'phone', ''), 60);
$cust_email = clean_str(g($customer, 'email', ''), 160);
$cust_notes = clean_str(g($customer, 'notes', ''), 2000);

$perimeter = (float) g($project, 'perimeter', 0);
$corners = (int) g($project, 'corners', 0);
$fence_type = clean_str(g($project, 'type', ''), 80);
$height = (float) g($project, 'height', 0);
$spacing = (float) g($project, 'spacing', 0);

/* ---------- Validation ---------- */
if (empty($items)) {
 json_error('Quote must include at least one line item.', 400);
}
// Perimeter is optional — product-level quotes have no fence perimeter.

/* ---------- Generate reference ---------- */
/* Format: VX-YYYY-##### (5-digit sequence within the year) */
$year = date('Y');

try {
 // Get the highest sequence for this year
 $stmt = $pdo->prepare("
 SELECT COUNT(*) AS n
 FROM quotes
 WHERE ref LIKE :prefix
 ");
 $stmt->execute([':prefix' => "VX-{$year}-%"]);
 $seq = ((int) $stmt->fetchColumn()) + 1;

 // Handle rare collision (if a quote is deleted) by bumping until free
 do {
 $ref = sprintf('VX-%s-%05d', $year, $seq);
 $check = $pdo->prepare("SELECT 1 FROM quotes WHERE ref = :ref LIMIT 1");
 $check->execute([':ref' => $ref]);
 if ($check->fetchColumn()) {
 $seq++;
 $ref = null;
 }
 } while ($ref === null);
} catch (PDOException $e) {
 json_error('Failed to generate quote reference.', 500, DEBUG ? ['detail' => $e->getMessage()] : []);
}

/* ---------- Opaque token (for retrieval by anyone with the link) ---------- */
$token = bin2hex(random_bytes(16)); // 32 hex chars

/* ---------- Totals ---------- */
$computed_total = 0.0;
foreach ($items as $it) {
 if (is_array($it) && isset($it['total'])) {
 $computed_total += (float) $it['total'];
 }
}
// Prefer the total the client sent (they may have applied a range)
$client_total = isset($body['total']) ? (float) $body['total'] : null;
$total_usd = $client_total !== null ? $client_total : $computed_total;

/* ---------- Options JSON ---------- */
$options_json = json_encode([
 'topWire' => !empty($options['topWire']),
 'gate' => !empty($options['gate']),
 'install' => !empty($options['install']),
 'concrete' => !empty($options['concrete']),
], JSON_UNESCAPED_UNICODE);

/* ---------- Client info ---------- */
$ip = $_SERVER['REMOTE_ADDR'] ?? null;
$ua = substr($_SERVER['HTTP_USER_AGENT'] ?? '', 0, 250);

/* ---------- Begin transaction ---------- */
try {
 $pdo->beginTransaction();

 /* Insert quote */
 $stmt = $pdo->prepare("
 INSERT INTO quotes
 (ref, source, customer_name, customer_phone, customer_email, customer_notes,
 perimeter, corners, fence_type, height, post_spacing,
 options, total_usd, total_zwl, status, ip_address, user_agent)
 VALUES
 (:ref, :source, :cname, :cphone, :cemail, :cnotes,
 :perim, :corners, :ftype, :height, :spacing,
 :options, :total, NULL, 'new', :ip, :ua)
 ");
 $stmt->execute([
 ':ref' => $ref,
 ':source' => $source,
 ':cname' => $cust_name ?: null,
 ':cphone' => $cust_phone ?: null,
 ':cemail' => $cust_email ?: null,
 ':cnotes' => $cust_notes ?: null,
 ':perim' => $perimeter > 0 ? $perimeter : null,
 ':corners' => $corners ?: null,
 ':ftype' => $fence_type ?: null,
 ':height' => $height > 0 ? $height : null,
 ':spacing' => $spacing > 0 ? $spacing : null,
 ':options' => $options_json,
 ':total' => $total_usd,
 ':ip' => $ip,
 ':ua' => $ua,
 ]);

 $quote_id = (int) $pdo->lastInsertId();

 /* Insert line items */
 $item_stmt = $pdo->prepare("
 INSERT INTO quote_items
 (quote_id, name, spec, qty, unit_price, total, sort_order)
 VALUES
 (:qid, :name, :spec, :qty, :unit, :total, :sort)
 ");

 $sort = 0;
 foreach ($items as $it) {
 if (!is_array($it)) continue;
 $name = clean_str(g($it, 'name', ''), 200);
 if ($name === '') continue;

 $item_stmt->execute([
 ':qid' => $quote_id,
 ':name' => $name,
 ':spec' => clean_str(g($it, 'spec', ''), 255) ?: null,
 ':qty' => clean_str(g($it, 'qty', ''), 60) ?: null,
 ':unit' => isset($it['unit']) ? (float) $it['unit'] : null,
 ':total' => isset($it['total']) ? (float) $it['total'] : null,
 ':sort' => $sort++,
 ]);
 }

 $pdo->commit();
} catch (PDOException $e) {
 if ($pdo->inTransaction()) $pdo->rollBack();
 json_error('Failed to save quote.', 500, DEBUG ? ['detail' => $e->getMessage()] : []);
}

/* ---------- Notify sales team ----------
 A quote only helps if someone sees it — email sales@ with the
 full details. Failures are swallowed so the API still responds. */
try {
    $lines = [];
    $lines[] = "New quote submitted on vuemax.co.zw";
    $lines[] = "";
    $lines[] = "Reference : {$ref}";
    $lines[] = "Source    : {$source}";
    $lines[] = "Customer  : " . ($cust_name ?: '—');
    $lines[] = "Phone     : " . ($cust_phone ?: '—');
    $lines[] = "Email     : " . ($cust_email ?: '—');
    if ($cust_notes) $lines[] = "Notes     : {$cust_notes}";
    $lines[] = "";
    $lines[] = "Project   : " . ($fence_type ?: '—')
             . ($perimeter > 0 ? " · {$perimeter}m perimeter" : '')
             . ($height > 0 ? " · {$height}m high" : '');
    $lines[] = "";
    $lines[] = "Items:";
    foreach ($items as $it) {
        if (!is_array($it) || empty($it['name'])) continue;
        $lines[] = " - {$it['name']} x" . ($it['qty'] ?? '?')
                 . " — " . (isset($it['total']) ? '$' . number_format((float)$it['total'], 2) : 'POA');
    }
    $lines[] = "";
    $lines[] = "Estimated total: $" . number_format($total_usd, 2);
    $lines[] = "";
    $lines[] = "View in admin: https://vuemax.co.zw/admin/quote-view.php?id={$quote_id}";

    @mail(
        'sales@vuemax.co.zw',
        "New website quote {$ref} — " . ($cust_name ?: 'customer'),
        implode("\r\n", $lines),
        "From: Vuemax Website <noreply@vuemax.co.zw>\r\n"
    );
} catch (Throwable $e) { /* mail unavailable — quote is still in the DB */ }

/* ---------- Optionally store token in a lightweight lookup table ----------
 For now we derive the token from quote id via a hash so we don't need
 an extra table. quote-get.php will accept either ?ref= or ?token=. */
$retrieval_token = substr(hash_hmac('sha256', $ref, 'vuemax-token-secret'), 0, 32);

/* ---------- Respond ---------- */
json_response([
 'ok' => true,
 'ref' => $ref,
 'token' => $retrieval_token,
 'total' => $total_usd,
]);