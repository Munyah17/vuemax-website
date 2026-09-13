<?php
/**
 * ============================================================
 * VUEMAX API — POST AI-style estimate
 * ============================================================
 * File: /api/estimate.php
 *
 * Accepts a plain-English project description and returns a
 * structured estimate: recommended fence type, computed BOQ,
 * and an estimated price range.
 *
 * Phase 1: rule-based extraction (keywords, regex)
 * Phase 2 (later): swap the internals to call an LLM (OpenAI,
 *                  Claude, Gemini) without touching the frontend.
 *
 * Request body:
 * {
 *   "text": "I need to fence a school perimeter of about 800m
 *            in Harare using 1.8m diamond mesh with a gate and top wire."
 * }
 *
 * Response:
 * {
 *   "ok": true,
 *   "estimate": {
 *     "fence_key": "diamond-mesh",
 *     "product": {
 *       "slug": "diamond-mesh",
 *       "name": "Diamond Mesh",
 *       "desc": "A durable and cost-effective solution...",
 *       "roll_metres": 30, "price_usd": 120, "post_price": 8,
 *       "top_wire_rate": 0.8, "gate_price": 180, "install_rate": 3.5
 *     },
 *     "project": {
 *       "perimeter": 800, "corners": 4, "height": 1.8, "spacing": 2.5
 *     },
 *     "options": { "topWire": true, "gate": true, "install": false, "concrete": false },
 *     "items": [
 *       { "name": "Diamond Mesh (1.8m)", "qty": "27 rolls", "total": 3240 },
 *       ...
 *     ],
 *     "total": 6300.00,
 *     "range": { "low": 5670.00, "high": 7245.00 }
 *   }
 * }
 * ============================================================
 */

require_once __DIR__ . '/config.php';
require_db($pdo, $db_error);
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

try {
    $stmt = $pdo->query("
        SELECT
            p.id, p.slug, p.name, p.short_desc,
            p.price_usd, p.roll_metres, p.post_price,
            p.top_wire_rate, p.gate_price, p.install_rate,
            c.slug AS category_slug
        FROM products p
        JOIN subcategories sc ON sc.id = p.subcategory_id
        JOIN categories    c  ON c.id  = sc.category_id
        WHERE c.slug = 'fencing'
          AND p.is_active = 1
          AND p.roll_metres IS NOT NULL
        ORDER BY p.sort_order ASC
    ");
    $fencing_products = $stmt->fetchAll();
} catch (PDOException $e) {
    json_error('Failed to load product catalogue.', 500, DEBUG ? ['detail' => $e->getMessage()] : []);
}

if (empty($fencing_products)) {
    json_error('No fencing products available.', 503);
}

/* Keyword map per fence slug — this is the "AI brain" for now.
   Phase 2 would replace this with an LLM call that returns a slug. */
$keyword_map = [
    'diamond-mesh' => ['diamond', 'residential', 'home', 'house', 'plot', 'boundary', 'general', 'school', 'estate', 'townhouse'],
    'game-fence'   => ['game', 'wildlife', 'farm', 'hectare', 'hectares', 'ha', 'large', 'agricultural', 'livestock', 'reserve', 'ranch'],
    'barbed-wire'  => ['barbed', 'perimeter', 'cheap', 'budget', 'top wire'],
    'chicken-mesh' => ['chicken', 'poultry', 'birds', 'run', 'coop', 'garden', 'small', 'pet', 'rabbit'],
    'field-fence'  => ['field', 'cattle', 'cows', 'goats', 'crop', 'agricultural', 'rural', 'livestock'],
    'razor-wire'   => ['razor', 'security', 'prison', 'commercial', 'industrial', 'warehouse', 'upgrade', 'high', 'bank', 'mine'],
];

/* Score */
$scores = [];
foreach ($fencing_products as $p) {
    $slug  = $p['slug'];
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
$best_score   = -1;
foreach ($fencing_products as $p) {
    $s = $scores[$p['slug']] ?? 0;
    if ($s > $best_score) {
        $best_score   = $s;
        $best_product = $p;
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
        $ha  = (float) $m[1];
        $sqm = $ha * 10000;
        return (int) round(sqrt($sqm) * 4);
    }

    // Acres
    if (preg_match('/(\d+(?:\.\d+)?)\s*(acres?)\b/', $t, $m)) {
        $ac  = (float) $m[1];
        $sqm = $ac * 4046.86;
        return (int) round(sqrt($sqm) * 4);
    }

    // Fallback — reasonable default for an average residential/farm job
    return 100;
}

$perimeter = detect_perimeter($text);

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
    'game-fence'   => 1.8,
    'barbed-wire'  => 1.5,
    'chicken-mesh' => 1.2,
    'field-fence'  => 1.2,
    'razor-wire'   => 2.1,
];
$default_h = $default_heights[$best_product['slug']] ?? 1.8;
$height    = detect_height($text, $default_h);

/* ---------- 4. Detect options ---------- */

function detect_options($text) {
    $t = mb_strtolower($text);
    return [
        'topWire'  => (bool) preg_match('/top\s*wire|barbed\s*top|top\s*barbed|with\s+top/', $t) || true,
        'gate'     => (bool) preg_match('/\bgate\b|entrance|access|door/', $t),
        'install'  => (bool) preg_match('/install|labour|labor|erect|build\s+for\s+me/', $t),
        'concrete' => (bool) preg_match('/concrete|cement|footing|base/', $t),
    ];
}

$options = detect_options($text);

/* ---------- 5. Compute BOQ ---------- */

$spacing = 2.5; // Standard post spacing
$corners = 4;   // Assume rectangular site

$items = [];

/* Fence rolls */
$roll_metres = (float) $best_product['roll_metres'];
$rolls       = $roll_metres > 0 ? (int) ceil($perimeter / $roll_metres) : 0;
$roll_cost   = $rolls * (float) $best_product['price_usd'];
if ($rolls > 0) {
    $items[] = [
        'name'  => $best_product['name'] . ' (' . number_format($height, 1) . 'm)',
        'spec'  => number_format($roll_metres, 0) . ' m rolls · galvanised',
        'qty'   => $rolls . ' roll' . ($rolls > 1 ? 's' : ''),
        'unit'  => (float) $best_product['price_usd'],
        'total' => round($roll_cost, 2),
    ];
}

/* Posts */
$posts      = (int) ceil($perimeter / $spacing) + $corners;
$post_price = (float) ($best_product['post_price'] ?? 0);
$post_cost  = $posts * $post_price;
if ($post_cost > 0) {
    $items[] = [
        'name'  => 'Steel Posts (' . number_format($height, 1) . 'm)',
        'spec'  => 'Standard gauge · 2.5m spacing',
        'qty'   => $posts . ' pcs',
        'unit'  => $post_price,
        'total' => round($post_cost, 2),
    ];
}

/* Corner posts (heavier, 1.5× price) */
if ($corners > 0 && $post_price > 0) {
    $cp_price = round($post_price * 1.5, 2);
    $cp_total = $cp_price * $corners;
    $items[] = [
        'name'  => 'Corner Posts',
        'spec'  => 'Heavier gauge · reinforced',
        'qty'   => $corners . ' pcs',
        'unit'  => $cp_price,
        'total' => round($cp_total, 2),
    ];
}

/* Top wire */
if ($options['topWire']) {
    $tw_rate  = (float) ($best_product['top_wire_rate'] ?? 0);
    $tw_total = round($perimeter * $tw_rate, 2);
    if ($tw_total > 0) {
        $items[] = [
            'name'  => 'Top Wire (barbed)',
            'spec'  => '3 strands · galvanised',
            'qty'   => $perimeter . ' m',
            'unit'  => $tw_rate,
            'total' => $tw_total,
        ];
    }
}

/* Gate */
if ($options['gate']) {
    $gate_price = (float) ($best_product['gate_price'] ?? 0);
    if ($gate_price > 0) {
        $items[] = [
            'name'  => 'Access Gate (4m)',
            'spec'  => 'Vehicle gate · hinges + lock',
            'qty'   => '1 set',
            'unit'  => $gate_price,
            'total' => $gate_price,
        ];
    }
}

/* Concrete footings */
if ($options['concrete'] && $posts > 0) {
    $concrete_per_post = 4.0;
    $cc_total = $posts * $concrete_per_post;
    $items[] = [
        'name'  => 'Concrete Post Bases',
        'spec'  => 'Cement footings',
        'qty'   => $posts . ' posts',
        'unit'  => $concrete_per_post,
        'total' => round($cc_total, 2),
    ];
}

/* Installation */
if ($options['install']) {
    $ins_rate  = (float) ($best_product['install_rate'] ?? 0);
    $ins_total = round($perimeter * $ins_rate, 2);
    if ($ins_total > 0) {
        $items[] = [
            'name'  => 'Installation (labour)',
            'spec'  => 'Professional install · cleanup included',
            'qty'   => $perimeter . ' m',
            'unit'  => $ins_rate,
            'total' => $ins_total,
        ];
    }
}

/* Accessories (flat) */
$items[] = [
    'name'  => 'Binding Wire & Accessories',
    'spec'  => 'Wire · clamps · tensioners',
    'qty'   => '1 lot',
    'unit'  => 60.00,
    'total' => 60.00,
];

/* ---------- 6. Total & range ---------- */

$total = 0.0;
foreach ($items as $it) $total += $it['total'];

$low  = (int) (round($total * 0.90 / 10) * 10);
$high = (int) (round($total * 1.15 / 10) * 10);

/* ---------- 7. Human-friendly description ---------- */

$descriptions = [
    'diamond-mesh' => 'A durable and cost-effective solution for your project. Ideal for security and long-term use.',
    'game-fence'   => 'Heavy-duty fencing for wildlife, farms and large properties. Built for strength and long-term performance.',
    'barbed-wire'  => 'High-tensile barbed wire — great for perimeter and farm protection at a low cost per metre.',
    'chicken-mesh' => 'Lightweight, galvanised mesh for poultry runs and small animal enclosures.',
    'field-fence'  => 'General agricultural fencing for livestock, crop protection and rural properties.',
    'razor-wire'   => 'Maximum-security installation for commercial, industrial and high-risk properties.',
];

/* ---------- 8. Respond ---------- */

json_response([
    'ok' => true,
    'estimate' => [
        'fence_key' => $best_product['slug'],
        'product' => [
            'slug'          => $best_product['slug'],
            'name'          => $best_product['name'],
            'desc'          => $descriptions[$best_product['slug']] ?? $best_product['short_desc'],
            'roll_metres'   => (float) $best_product['roll_metres'],
            'price_usd'     => (float) $best_product['price_usd'],
            'post_price'    => (float) $best_product['post_price'],
            'top_wire_rate' => (float) $best_product['top_wire_rate'],
            'gate_price'    => (float) $best_product['gate_price'],
            'install_rate'  => (float) $best_product['install_rate'],
        ],
        'project' => [
            'perimeter' => $perimeter,
            'corners'   => $corners,
            'height'    => $height,
            'spacing'   => $spacing,
        ],
        'options' => $options,
        'items'   => $items,
        'total'   => round($total, 2),
        'range'   => [
            'low'  => $low,
            'high' => $high,
        ],
        'confidence' => $best_score >= 5 ? 'high' : ($best_score >= 2 ? 'medium' : 'low'),
    ],
]);