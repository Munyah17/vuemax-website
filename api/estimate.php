<?php
/**
 * ============================================================
 * VUEMAX API POST AI-style estimate
 * ============================================================
 * File: /api/estimate.php
 *
 * Accepts a plain-English project description and returns a
 * structured estimate: recommended fence type, computed BOQ,
 * and an estimated price range.
 *
 * Phase 1: rule-based extraction (keywords, regex)
 * Phase 2 (later): swap the internals to call an LLM (OpenAI,
 * Claude, Gemini) without touching the frontend.
 *
 * Request body:
 * {
 * "text": "I need to fence a school perimeter of about 800m
 * in Harare using 1.8m diamond mesh with a gate and top wire."
 * }
 *
 * Response:
 * {
 * "ok": true,
 * "estimate": {
 * "fence_key": "diamond-mesh",
 * "product": {
 * "slug": "diamond-mesh",
 * "name": "Diamond Mesh",
 * "desc": "A durable and cost-effective solution...",
 * "roll_metres": 30, "price_usd": 120, "post_price": 8,
 * "top_wire_rate": 0.8, "gate_price": 180, "install_rate": 3.5
 * },
 * "project": {
 * "perimeter": 800, "corners": 4, "height": 1.8, "spacing": 2.5
 * },
 * "options": { "topWire": true, "gate": true, "install": false, "concrete": false },
 * "items": [
 * { "name": "Diamond Mesh (1.8m)", "qty": "27 rolls", "total": 3240 },
 * ...
 * ],
 * "total": 6300.00,
 * "range": { "low": 5670.00, "high": 7245.00 }
 * }
 * }
 * ============================================================
 */

require_once __DIR__ . '/config.php';
require_method('POST');

/* ---------- Read body ---------- */
$body = read_json_body();
$text = clean_str(g($body, 'text', ''), 500);

if (mb_strlen($text) < 10) {
 json_error('Please describe your project in at least a few words.', 400);
}

/* ============================================================
 RULES ENGINE
 ============================================================ */

$t = mb_strtolower($text);

/* ---------- 1. Detect fence type ----------
 Pull all products from the DB with their calculator rates,
 then score each against keyword hits in the text. */

$fencing_products = [];
if ($pdo) {
 try {
 $stmt = $pdo->query("
 SELECT
 p.id, p.slug, p.name, p.short_desc,
 p.price_usd, p.roll_metres, p.post_price,
 p.top_wire_rate, p.gate_price, p.install_rate,
 c.slug AS category_slug
 FROM products p
 JOIN subcategories sc ON sc.id = p.subcategory_id
 JOIN categories c ON c.id = sc.category_id
 WHERE c.slug = 'fencing'
 AND p.is_active = 1
 AND p.roll_metres IS NOT NULL
 ORDER BY p.sort_order ASC
 ");
 $fencing_products = $stmt->fetchAll();
 } catch (Throwable $e) {
 $fencing_products = [];
 }
}

/* Fallback catalogue — mirrors sql/schema.sql seed prices so the
 estimator still works when MySQL is not connected. */
if (empty($fencing_products)) {
 $fencing_products = [
 ['slug'=>'diamond-mesh','name'=>'Diamond Mesh 50x50 (2mm)','short_desc'=>'Versatile, durable fencing for homes, farms and businesses.','price_usd'=>65.00,'roll_metres'=>30,'post_price'=>8.00,'top_wire_rate'=>0.80,'gate_price'=>180.00,'install_rate'=>3.50],
 ['slug'=>'game-fence','name'=>'Game Fence','short_desc'=>'Heavy-duty fencing for wildlife, farms and large properties.','price_usd'=>280.00,'roll_metres'=>50,'post_price'=>12.00,'top_wire_rate'=>1.10,'gate_price'=>220.00,'install_rate'=>4.00],
 ['slug'=>'barbed-wire','name'=>'Barbed Wire 50 kg Roll','short_desc'=>'High-tensile barbed wire for perimeter and farm protection.','price_usd'=>75.00,'roll_metres'=>700,'post_price'=>8.00,'top_wire_rate'=>0.60,'gate_price'=>160.00,'install_rate'=>1.50],
 ['slug'=>'chicken-mesh','name'=>'Chicken Mesh','short_desc'=>'Lightweight galvanised mesh for poultry runs and small enclosures.','price_usd'=>32.00,'roll_metres'=>30,'post_price'=>6.00,'top_wire_rate'=>0.50,'gate_price'=>140.00,'install_rate'=>2.00],
 ['slug'=>'field-fence','name'=>'Field Fence','short_desc'=>'General agricultural fencing for livestock and crop protection.','price_usd'=>180.00,'roll_metres'=>50,'post_price'=>10.00,'top_wire_rate'=>0.90,'gate_price'=>200.00,'install_rate'=>3.00],
 ['slug'=>'razor-wire','name'=>'Razor Wire','short_desc'=>'Maximum-security installation for high-risk properties.','price_usd'=>95.00,'roll_metres'=>50,'post_price'=>14.00,'top_wire_rate'=>1.40,'gate_price'=>260.00,'install_rate'=>4.50],
 ];
}

/* ============================================================
 GROQ AI EXTRACTION
 Ask the LLM to parse the request into structured params.
 Any failure falls back to the rules engine below.
 ============================================================ */

function groq_extract($text, $products) {
 if (!defined('GROQ_API_KEY') || !GROQ_API_KEY || !function_exists('curl_init')) return null;

 $slugs = implode(', ', array_column($products, 'slug'));
 $sys = "You are a fencing project parser for a Zimbabwean fencing supplier. "
 . "Return ONLY compact JSON with these keys: "
 . "fence_slug (exactly one of: $slugs; pick the best match for the described use), "
 . "perimeter_m (number; metres of fencing needed. Convert hectares/acres to perimeter assuming a roughly square plot; use 100 if unknown), "
 . "height_m (number between 0.5 and 4; typical fence height in metres), "
 . "top_wire (bool), gate (bool), install (bool), concrete (bool). "
 . "Set install true if they ask us to erect/build the fence; concrete true if they mention concrete or cement bases.";

 $payload = json_encode([
 'model' => GROQ_MODEL,
 'messages' => [
 ['role' => 'system', 'content' => $sys],
 ['role' => 'user', 'content' => $text],
 ],
 'temperature' => 0.1,
 'response_format' => ['type' => 'json_object'],
 ]);

 $ch = curl_init('https://api.groq.com/openai/v1/chat/completions');
 curl_setopt_array($ch, [
 CURLOPT_RETURNTRANSFER => true,
 CURLOPT_POST => true,
 CURLOPT_HTTPHEADER => [
 'Content-Type: application/json',
 'Authorization: Bearer ' . GROQ_API_KEY,
 ],
 CURLOPT_POSTFIELDS => $payload,
 CURLOPT_TIMEOUT => 15,
 ]);
 $res = curl_exec($ch);
 $code = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
 curl_close($ch);
 if ($code !== 200 || !$res) return null;

 $j = json_decode($res, true);
 $p = json_decode($j['choices'][0]['message']['content'] ?? '', true);
 if (!is_array($p)) return null;

 $per = (int) ($p['perimeter_m'] ?? 0);
 $h = (float) ($p['height_m'] ?? 0);
 return [
 'fence_slug' => is_string($p['fence_slug'] ?? null) ? $p['fence_slug'] : null,
 'perimeter' => $per >= 10 ? $per : null,
 'height' => ($h >= 0.5 && $h <= 4.0) ? $h : null,
 'options' => [
 'topWire' => !empty($p['top_wire']),
 'gate' => !empty($p['gate']),
 'install' => !empty($p['install']),
 'concrete' => !empty($p['concrete']),
 ],
 ];
}

$ai = groq_extract($text, $fencing_products);

/* Keyword map per fence slug this is the "AI brain" for now.
 Phase 2 would replace this with an LLM call that returns a slug. */
$keyword_map = [
 'diamond-mesh' => ['diamond', 'residential', 'home', 'house', 'plot', 'boundary', 'general', 'school', 'estate', 'townhouse'],
 'game-fence' => ['game', 'wildlife', 'farm', 'hectare', 'hectares', 'ha', 'large', 'agricultural', 'livestock', 'reserve', 'ranch'],
 'barbed-wire' => ['barbed', 'perimeter', 'cheap', 'budget', 'top wire'],
 'chicken-mesh' => ['chicken', 'poultry', 'birds', 'run', 'coop', 'garden', 'small', 'pet', 'rabbit'],
 'field-fence' => ['field', 'cattle', 'cows', 'goats', 'crop', 'agricultural', 'rural', 'livestock'],
 'razor-wire' => ['razor', 'security', 'prison', 'commercial', 'industrial', 'warehouse', 'upgrade', 'high', 'bank', 'mine'],
];

/* Score */
$scores = [];
foreach ($fencing_products as $p) {
 $slug = $p['slug'];
 $score = 0;

 if (mb_strpos($t, mb_strtolower($p['name'])) !== false) $score += 5;

 if (isset($keyword_map[$slug])) {
 foreach ($keyword_map[$slug] as $kw) {
 if (mb_strpos($t, $kw) !== false) $score += 1;
 }
 }
 $scores[$slug] = $score;
}

/* Pick the highest-scoring; ties favour earlier sort_order (already ordered) */
$best_product = $fencing_products[0];
$best_score = -1;
foreach ($fencing_products as $p) {
 $s = $scores[$p['slug']] ?? 0;
 if ($s > $best_score) {
 $best_score = $s;
 $best_product = $p;
 }
}

/* AI override: if Groq returned a valid fence slug, trust it */
if ($ai && $ai['fence_slug']) {
 foreach ($fencing_products as $fp) {
 if ($fp['slug'] === $ai['fence_slug']) { $best_product = $fp; break; }
 }
}

/* ---------- 2. Extract perimeter ---------- */

function detect_perimeter($text) {
 $t = mb_strtolower($text);

 // Direct metres / km
 if (preg_match('/(\d+(?:\.\d+)?)\s*(m|metres?|meters?|km)\b/', $t, $m)) {
 $n = (float) $m[1];
 if ($m[2] === 'km') $n *= 1000;
 return (int) round($n);
 }

 // Hectares (assumes roughly square plot)
 if (preg_match('/(\d+(?:\.\d+)?)\s*(hectares?|ha)\b/', $t, $m)) {
 $ha = (float) $m[1];
 $sqm = $ha * 10000;
 return (int) round(sqrt($sqm) * 4);
 }

 // Acres
 if (preg_match('/(\d+(?:\.\d+)?)\s*(acres?)\b/', $t, $m)) {
 $ac = (float) $m[1];
 $sqm = $ac * 4046.86;
 return (int) round(sqrt($sqm) * 4);
 }

 // Fallback reasonable default for an average residential/farm job
 return 100;
}

$perimeter = ($ai && $ai['perimeter']) ? $ai['perimeter'] : detect_perimeter($text);

/* ---------- 3. Extract height ---------- */

function detect_height($text, $default) {
 $t = mb_strtolower($text);

 // "1.8m", "1.8 m high", "height of 2.1", "2.1 metres tall"
 $patterns = [
 '/(\d+(?:\.\d+)?)\s*m\b(?!\w)/',
 '/height\s+of\s+(\d+(?:\.\d+)?)/',
 '/(\d+(?:\.\d+)?)\s*metres?\s*(?:high|tall)/',
 ];
 foreach ($patterns as $p) {
 if (preg_match($p, $t, $m)) {
 $h = (float) $m[1];
 if ($h >= 0.5 && $h <= 4.0) return $h;
 }
 }
 return $default;
}

/* Default height depends on fence type */
$default_heights = [
 'diamond-mesh' => 1.8,
 'game-fence' => 1.8,
 'barbed-wire' => 1.5,
 'chicken-mesh' => 1.2,
 'field-fence' => 1.2,
 'razor-wire' => 2.1,
];
$default_h = $default_heights[$best_product['slug']] ?? 1.8;
$height = ($ai && $ai['height']) ? $ai['height'] : detect_height($text, $default_h);

/* ---------- 4. Detect options ---------- */

function detect_options($text) {
 $t = mb_strtolower($text);
 return [
 'topWire' => (bool) preg_match('/top\s*wire|barbed\s*top|top\s*barbed|with\s+top/', $t) || true,
 'gate' => (bool) preg_match('/\bgate\b|entrance|access|door/', $t),
 'install' => (bool) preg_match('/install|labour|labor|erect|build\s+for\s+me/', $t),
 'concrete' => (bool) preg_match('/concrete|cement|footing|base/', $t),
 ];
}

$options = $ai ? $ai['options'] : detect_options($text);

/* Barbed wire is already barbed — never add a top-wire line,
   whichever engine detected the options. */
$is_barbed = in_array($best_product['slug'], ['barbed-wire', 'barbed-wire-50kg'], true);
if ($is_barbed) $options['topWire'] = false;

/* Barbed-wire quotes use the standard 50kg roll (700m) — keep the
   detected slug for assist mode, but price off the 50kg product. */
$price_product = $best_product;
if ($best_product['slug'] === 'barbed-wire') {
 foreach ($fencing_products as $fp) {
 if ($fp['slug'] === 'barbed-wire-50kg') { $price_product = $fp; break; }
 }
}

$spacing = 5.0; // Client rule: standard posts every 5 m
$corners = 4; // Assume rectangular site

/* ---------- Assist mode ----------
   Used by the calculator's "AI Quick-Fill": return the parsed
   project parameters only (no pricing), so the form stays
   deterministic and reviewable by the user. */
if (!empty($body['assist'])) {
 json_response([
 'ok' => true,
 'parsed' => [
 'fence_slug' => $best_product['slug'],
 'perimeter' => $perimeter,
 'corners' => $corners,
 'height' => $height,
 'spacing' => $spacing,
 'options' => $options,
 ],
 'confidence' => $ai ? 'high' : ($best_score >= 5 ? 'high' : ($best_score >= 2 ? 'medium' : 'low')),
 'engine' => $ai ? 'groq:' . GROQ_MODEL : 'rules',
 ]);
}

/* ---------- 5. Compute BOQ ---------- */

$items = [];

/* Fence rolls — barbed wire is quoted per line (strand):
   total wire = perimeter × lines, sold in 50kg rolls (~700m),
   rounded up to the nearest half roll. */
$strands = 5;
if ($is_barbed && preg_match('/(\d+)\s*(?:lines?|strands?)/', $t, $sm)) {
 $strands = max(1, (int) $sm[1]);
}
$wire_len = $is_barbed ? $perimeter * $strands : $perimeter;
$roll_metres = (float) $price_product['roll_metres'];
$rolls = $roll_metres > 0
 ? ($is_barbed ? ceil($wire_len / $roll_metres * 2) / 2 : (int) ceil($wire_len / $roll_metres))
 : 0;
$roll_cost = $rolls * (float) $price_product['price_usd'];
if ($rolls > 0) {
 $items[] = [
 'name' => $price_product['name'] . ($is_barbed ? ' — ' . $strands . ' lines' : ' (' . number_format($height, 1) . 'm)'),
 'spec' => number_format($roll_metres, 0) . ' m rolls · galvanised',
 'qty' => $rolls . ' roll' . ($rolls > 1 ? 's' : '') . ($is_barbed ? ' (' . number_format($wire_len) . 'm wire)' : ''),
 'unit' => (float) $price_product['price_usd'],
 'total' => round($roll_cost, 2),
 ];
}

/* Posts — client model: each fence height maps to a post set;
   standard posts every 5 m, corner posts per corner, 2 supporter
   (stay) posts per corner post. */
$POST_SETS = [
 ['h'=>1.2,'len'=>1.8,'corner'=>16,'standard'=>8, 'supporter'=>12],
 ['h'=>1.5,'len'=>2.0,'corner'=>13,'standard'=>9, 'supporter'=>13],
 ['h'=>2.1,'len'=>2.6,'corner'=>26,'standard'=>16,'supporter'=>13],
 ['h'=>2.4,'len'=>3.0,'corner'=>33,'standard'=>18,'supporter'=>15],
 ['h'=>2.5,'len'=>3.0,'corner'=>33,'standard'=>18,'supporter'=>15],
 ['h'=>3.0,'len'=>3.6,'corner'=>40,'standard'=>20,'supporter'=>16],
];
$ps = $POST_SETS[count($POST_SETS) - 1];
foreach ($POST_SETS as $s) { if ($s['h'] >= $height - 0.001) { $ps = $s; break; } }

$standards  = (int) ceil($perimeter / 5.0);
$supporters = $corners * 2;
$posts      = $standards + $corners + $supporters;

if ($standards > 0) {
 $items[] = [
 'name' => 'Standard Posts (' . number_format($ps['len'], 1) . 'm)',
 'spec' => 'Every 5 m along fence line',
 'qty' => $standards . ' pcs',
 'unit' => $ps['standard'],
 'total' => round($standards * $ps['standard'], 2),
 ];
}
if ($corners > 0) {
 $items[] = [
 'name' => 'Corner Posts (' . number_format($ps['len'], 1) . 'm)',
 'spec' => 'Reinforced · one per corner',
 'qty' => $corners . ' pcs',
 'unit' => $ps['corner'],
 'total' => round($corners * $ps['corner'], 2),
 ];
}
if ($supporters > 0) {
 $items[] = [
 'name' => 'Supporter Posts (' . number_format($ps['len'], 1) . 'm)',
 'spec' => 'Stay posts · 2 per corner',
 'qty' => $supporters . ' pcs',
 'unit' => $ps['supporter'],
 'total' => round($supporters * $ps['supporter'], 2),
 ];
}

/* Top wire */
if ($options['topWire']) {
 $tw_rate = (float) ($price_product['top_wire_rate'] ?? 0);
 $tw_total = round($perimeter * $tw_rate, 2);
 if ($tw_total > 0) {
 $items[] = [
 'name' => 'Top Wire (barbed)',
 'spec' => '3 strands · galvanised',
 'qty' => $perimeter . ' m',
 'unit' => $tw_rate,
 'total' => $tw_total,
 ];
 }
}

/* Gate */
if ($options['gate']) {
 $gate_price = (float) ($price_product['gate_price'] ?? 0);
 if ($gate_price > 0) {
 $items[] = [
 'name' => 'Access Gate (4m)',
 'spec' => 'Vehicle gate · hinges + lock',
 'qty' => '1 set',
 'unit' => $gate_price,
 'total' => $gate_price,
 ];
 }
}

/* Concrete footings */
if ($options['concrete'] && $posts > 0) {
 $concrete_per_post = 4.0;
 $cc_total = $posts * $concrete_per_post;
 $items[] = [
 'name' => 'Concrete Post Bases',
 'spec' => 'Cement footings',
 'qty' => $posts . ' posts',
 'unit' => $concrete_per_post,
 'total' => round($cc_total, 2),
 ];
}

/* Installation */
if ($options['install']) {
 $ins_rate = (float) ($price_product['install_rate'] ?? 0);
 $ins_total = round($perimeter * $ins_rate, 2);
 if ($ins_total > 0) {
 $items[] = [
 'name' => 'Installation (labour)',
 'spec' => 'Professional install · cleanup included',
 'qty' => $perimeter . ' m',
 'unit' => $ins_rate,
 'total' => $ins_total,
 ];
 }
}

/* Accessories (flat) */
$items[] = [
 'name' => 'Binding Wire & Accessories',
 'spec' => 'Wire · clamps · tensioners',
 'qty' => '1 lot',
 'unit' => 60.00,
 'total' => 60.00,
];

/* ---------- 6. Total & range ---------- */

$total = 0.0;
foreach ($items as $it) $total += $it['total'];

$low = (int) (round($total * 0.90 / 10) * 10);
$high = (int) (round($total * 1.15 / 10) * 10);

/* ---------- 7. Human-friendly description ---------- */

$descriptions = [
 'diamond-mesh' => 'A durable and cost-effective solution for your project. Ideal for security and long-term use.',
 'game-fence' => 'Heavy-duty fencing for wildlife, farms and large properties. Built for strength and long-term performance.',
 'barbed-wire' => 'High-tensile barbed wire great for perimeter and farm protection at a low cost per metre.',
 'chicken-mesh' => 'Lightweight, galvanised mesh for poultry runs and small animal enclosures.',
 'field-fence' => 'General agricultural fencing for livestock, crop protection and rural properties.',
 'razor-wire' => 'Maximum-security installation for commercial, industrial and high-risk properties.',
];

/* ---------- 8. Respond ---------- */

json_response([
 'ok' => true,
 'estimate' => [
 'fence_key' => $best_product['slug'],
 'product' => [
 'slug' => $price_product['slug'],
 'name' => $price_product['name'],
 'desc' => $descriptions[$best_product['slug']] ?? $price_product['short_desc'],
 'roll_metres' => (float) $price_product['roll_metres'],
 'price_usd' => (float) $price_product['price_usd'],
 'post_price' => (float) $price_product['post_price'],
 'top_wire_rate' => (float) $price_product['top_wire_rate'],
 'gate_price' => (float) $price_product['gate_price'],
 'install_rate' => (float) $price_product['install_rate'],
 ],
 'project' => [
 'perimeter' => $perimeter,
 'corners' => $corners,
 'height' => $height,
 'spacing' => $spacing,
 'lines' => $is_barbed ? $strands : null,
 ],
 'options' => $options,
 'items' => $items,
 'total' => round($total, 2),
 'range' => [
 'low' => $low,
 'high' => $high,
 ],
 'confidence' => $ai ? 'high' : ($best_score >= 5 ? 'high' : ($best_score >= 2 ? 'medium' : 'low')),
 'engine' => $ai ? 'groq:' . GROQ_MODEL : 'rules',
 ],
]);