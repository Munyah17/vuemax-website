<?php
require_once __DIR__ . '/includes/config.php';

$pageTitle = 'AI Fencing Estimator Vuemax | Describe Your Project';
$pageDesc = 'Describe your fencing project in plain words and our AI recommends the best solution with estimated materials and costs.';
$active = '';

$extraCss = <<<'CSS'
/* ---------- ESTIMATOR HERO ---------- */
.est-hero .ai-tag{
 display:inline-flex;align-items:center;gap:8px;
 background:rgba(245,183,49,.15);border:1px solid rgba(245,183,49,.4);
 color:var(--amber);padding:6px 14px;border-radius:999px;
 font-size:12px;font-weight:700;letter-spacing:.08em;text-transform:uppercase;
 margin-bottom:16px;
}
.est-hero .ai-tag .spark{
 width:7px;height:7px;border-radius:50%;background:var(--amber);
 box-shadow:0 0 0 4px rgba(245,183,49,.2);
}
.est-hero p{margin-bottom:0;}

/* ---------- MAIN LAYOUT ---------- */
.est-layout{
 display:grid;grid-template-columns:1fr;
 gap:24px;padding:44px 0 72px;align-items:start;
}

/* ---- Left: input panel ---- */
.input-panel{
 background:var(--white);border:1px solid var(--border);
 border-radius:var(--radius-card);box-shadow:var(--shadow-card);
 overflow:hidden;
}
.panel-head{
 padding:24px 24px 20px;border-bottom:1px solid var(--border);
 display:flex;gap:14px;align-items:flex-start;
}
.panel-head .ico{
 width:44px;height:44px;border-radius:12px;
 background:var(--amber-soft);color:var(--navy);
 display:flex;align-items:center;justify-content:center;
 flex-shrink:0;
}
.panel-head h2{
 font-family:'Playfair Display',serif;
 font-size:22px;font-weight:700;color:var(--navy);
 line-height:1.2;margin-bottom:4px;
}
.panel-head p{font-size:13.5px;color:var(--muted);line-height:1.5;}
.panel-body{padding:22px;}

/* Textarea */
.prompt-wrap{
 border:1.5px solid var(--border);border-radius:var(--radius-card);
 padding:16px;background:var(--white);transition:border-color .15s, box-shadow .15s;
 position:relative;
}
.prompt-wrap:focus-within{
 border-color:var(--navy);
 box-shadow:0 0 0 3px rgba(14,39,69,.08);
}
.prompt-wrap textarea{
 width:100%;border:none;outline:none;resize:vertical;
 font-size:15px;line-height:1.6;color:var(--navy);
 min-height:120px;background:transparent;font-family:inherit;
}
.prompt-wrap textarea::placeholder{color:#9CA3AF;}
.prompt-counter{
 text-align:right;font-size:12px;color:var(--muted);
 margin-top:6px;font-weight:500;
}
.prompt-actions{
 display:flex;flex-direction:column;gap:10px;
 margin-top:16px;
}
.prompt-actions .btn{width:100%;justify-content:center;}

/* Quick start chips */
.quick-section{margin-top:26px;}
.quick-label{
 font-size:12px;font-weight:700;color:var(--navy);
 letter-spacing:.06em;text-transform:uppercase;
 margin-bottom:12px;display:flex;justify-content:space-between;
 align-items:baseline;gap:10px;flex-wrap:wrap;
}
.quick-label span{
 font-size:11.5px;color:var(--muted);
 letter-spacing:0;text-transform:none;font-weight:500;
}
.quick-chips{
 display:grid;grid-template-columns:repeat(2,1fr);gap:10px;
}
.chip{
 border:1.5px solid var(--border);background:var(--white);
 border-radius:var(--radius-card);padding:14px 10px;
 cursor:pointer;transition:border-color .18s, transform .18s, box-shadow .18s;
 text-align:center;
}
.chip:hover{border-color:var(--navy);transform:translateY(-2px);box-shadow:var(--shadow-card);}
.chip .ico{
 width:34px;height:34px;border-radius:9px;
 background:var(--amber-soft);color:var(--navy);
 display:flex;align-items:center;justify-content:center;
 margin:0 auto 8px;
}
.chip strong{
 display:block;font-size:12.5px;font-weight:600;
 color:var(--navy);line-height:1.3;margin-bottom:2px;
}
.chip span{
 display:block;font-size:10.5px;color:var(--muted);
 line-height:1.4;
}

/* Example prompts */
.examples{margin-top:24px;}
.example-list{list-style:none;}
.example-list li{margin-bottom:6px;}
.example-list a{
 display:block;padding:9px 14px;border-radius:8px;
 font-size:13.5px;color:var(--text);
 background:var(--bg);transition:background .15s, color .15s;
 cursor:pointer;
}
.example-list a:hover{background:#F1EDE6;color:var(--navy);}
.example-list a::before{
 content:"→";color:var(--amber);font-weight:700;margin-right:10px;
}

/* ---- Right: AI Result panel ---- */
.result-panel{
 background:var(--white);border:1px solid var(--border);
 border-radius:var(--radius-card);box-shadow:var(--shadow-card);
 overflow:hidden;
}
.result-head{
 background:linear-gradient(90deg,#ECFDF5 0%,#F0FDF4 100%);
 border-bottom:1px solid var(--green-line);
 padding:14px 20px;
 display:flex;justify-content:space-between;align-items:center;
}
.result-head h3{
 font-size:13.5px;font-weight:700;color:var(--green-text);
 letter-spacing:.03em;display:flex;align-items:center;gap:8px;
}
.result-head .pulse{
 width:8px;height:8px;border-radius:50%;background:var(--green-text);
 animation:pulse 1.8s infinite;
}
@keyframes pulse{
 0%{box-shadow:0 0 0 0 rgba(21,128,61,.5);}
 70%{box-shadow:0 0 0 8px rgba(21,128,61,0);}
 100%{box-shadow:0 0 0 0 rgba(21,128,61,0);}
}
.result-head .status{
 font-size:11px;font-weight:600;color:var(--green-text);
 background:var(--green-bg);padding:3px 9px;border-radius:999px;
 letter-spacing:.03em;
}

.result-empty{padding:52px 32px;text-align:center;}
.result-empty .icon{
 width:64px;height:64px;border-radius:50%;
 background:var(--bg);color:var(--muted);
 display:flex;align-items:center;justify-content:center;
 margin:0 auto 18px;
}
.result-empty h4{font-size:15px;font-weight:600;color:var(--navy);margin-bottom:6px;}
.result-empty p{font-size:13px;color:var(--muted);line-height:1.55;max-width:280px;margin:0 auto;}

.result-loading{padding:56px 32px;text-align:center;display:none;}
.result-loading .spinner{
 width:44px;height:44px;border-radius:50%;
 border:3px solid var(--border);border-top-color:var(--amber);
 animation:spin .8s linear infinite;
 margin:0 auto 18px;
}
@keyframes spin{to{transform:rotate(360deg);}}
.result-loading h4{font-size:14px;font-weight:600;color:var(--navy);margin-bottom:6px;}
.result-loading p{font-size:12.5px;color:var(--muted);}

.result-body{padding:20px;display:none;}
.result-body.active{display:block;}

.result-type{
 background:var(--green-bg);border:1px solid var(--green-line);
 border-radius:var(--radius-card);padding:16px;
 margin-bottom:18px;position:relative;
}
.result-type .top-line{
 display:flex;justify-content:space-between;align-items:center;
 margin-bottom:10px;
}
.result-type .label-tiny{
 font-size:10.5px;font-weight:700;color:var(--green-text);
 letter-spacing:.08em;text-transform:uppercase;
}
.result-type .badge-best{
 background:var(--green-text);color:#fff;
 font-size:10px;font-weight:700;letter-spacing:.06em;
 padding:3px 8px;border-radius:4px;text-transform:uppercase;
}
.result-type .type-name{
 font-family:'Playfair Display',serif;
 font-size:22px;font-weight:700;color:var(--navy);
 line-height:1.2;margin-bottom:6px;
}
.result-type .type-desc{font-size:13px;color:#374151;line-height:1.5;}

.result-section{margin-bottom:20px;}
.result-section:last-child{margin-bottom:0;}
.section-label{
 font-size:11px;font-weight:700;color:var(--navy);
 letter-spacing:.06em;text-transform:uppercase;
 margin-bottom:10px;
 padding-bottom:8px;border-bottom:1px solid var(--border);
}

.mini-boq{list-style:none;}
.mini-boq li{
 display:flex;justify-content:space-between;align-items:center;
 padding:8px 0;font-size:13px;border-bottom:1px dashed var(--border);
}
.mini-boq li:last-child{border-bottom:none;}
.mini-boq .desc{display:flex;align-items:center;gap:8px;flex:1;min-width:0;}
.mini-boq .dot{
 width:5px;height:5px;border-radius:50%;background:var(--amber);
 flex-shrink:0;
}
.mini-boq .name{color:var(--navy);font-weight:500;}
.mini-boq .qty{color:var(--muted);font-size:12px;white-space:nowrap;padding-left:8px;}
.mini-boq .cost{color:var(--navy);font-weight:600;font-size:12.5px;white-space:nowrap;padding-left:10px;}

.price-range{
 background:var(--navy);color:var(--white);
 border-radius:var(--radius-card);padding:18px;
 margin-bottom:18px;
}
.price-range .lbl{
 font-size:11px;font-weight:700;color:rgba(255,255,255,.6);
 letter-spacing:.08em;text-transform:uppercase;margin-bottom:6px;
}
.price-range .val{
 font-family:'Playfair Display',serif;
 font-size:26px;font-weight:700;color:var(--amber);line-height:1.1;
}
.price-range .sub{font-size:11.5px;color:rgba(255,255,255,.6);margin-top:6px;}

.next-steps{list-style:none;}
.next-steps li{
 display:flex;gap:10px;align-items:flex-start;
 padding:7px 0;font-size:12.5px;color:var(--text);
 line-height:1.5;
}
.next-steps .num{
 width:18px;height:18px;border-radius:50%;
 background:var(--navy);color:var(--white);
 font-size:10px;font-weight:700;
 display:flex;align-items:center;justify-content:center;
 flex-shrink:0;margin-top:2px;
}

.result-ctas{
 padding:16px 20px 20px;border-top:1px solid var(--border);
 background:var(--bg);
 display:flex;flex-direction:column;gap:10px;
}
.result-ctas .btn{width:100%;justify-content:center;}

/* ---------- HOW IT WORKS ---------- */
.how-section{
 background:var(--white);border-top:1px solid var(--border);
 border-bottom:1px solid var(--border);padding:64px 0;
}
.how-section .section-head{text-align:center;margin-bottom:44px;}
.how-section .section-head p{margin:0 auto;}
.how-grid{
 display:grid;grid-template-columns:1fr;gap:28px;
 max-width:980px;margin:0 auto;
}
.how-card{text-align:center;position:relative;padding:0 12px;}
.how-card .num{
 width:52px;height:52px;border-radius:50%;
 background:var(--amber);color:var(--navy);
 display:flex;align-items:center;justify-content:center;
 font-family:'Playfair Display',serif;font-weight:700;font-size:22px;
 margin:0 auto 18px;
 box-shadow:0 6px 16px rgba(245,183,49,.35);
}
.how-card h3{font-size:16px;font-weight:600;color:var(--navy);margin-bottom:8px;}
.how-card p{font-size:13.5px;color:var(--muted);line-height:1.6;}

/* ---------- WHY ---------- */
.why-section{padding:72px 0;}
.why-grid{
 display:grid;grid-template-columns:1fr;gap:32px;
 align-items:center;
}
.why-img{
 aspect-ratio:4/3;border-radius:var(--radius-card);
 background:url('https://images.unsplash.com/photo-1621905251189-08b45d6a269e?auto=format&fit=crop&w=1000&q=80') center/cover no-repeat;
 position:relative;overflow:hidden;
 box-shadow:var(--shadow-lift);
}
.why-img::after{
 content:"";position:absolute;inset:0;
 background:linear-gradient(180deg,transparent 55%,rgba(10,29,51,.5));
}
.why-img .script-overlay{
 position:absolute;right:24px;bottom:20px;
 font-family:'Caveat',cursive;font-size:26px;
 color:var(--amber);opacity:.95;line-height:1;
 text-align:right;transform:rotate(-4deg);z-index:1;
}
.why-content .eyebrow{
 font-size:12px;letter-spacing:.16em;text-transform:uppercase;
 color:var(--amber-hover);font-weight:700;margin-bottom:12px;
 display:inline-block;
}
.why-content h2{
 font-family:'Playfair Display',serif;
 font-size:clamp(24px,2.6vw,34px);font-weight:700;
 color:var(--navy);line-height:1.2;margin-bottom:16px;
 letter-spacing:-.01em;
}
.why-content p{font-size:15px;color:var(--text);line-height:1.7;margin-bottom:20px;}
.why-list{list-style:none;}
.why-list li{
 display:flex;gap:12px;align-items:flex-start;
 padding:10px 0;font-size:14px;color:var(--text);
 line-height:1.55;
}
.why-list .check{
 width:22px;height:22px;border-radius:50%;
 background:var(--green-bg);color:var(--green-text);
 display:flex;align-items:center;justify-content:center;
 flex-shrink:0;font-size:12px;margin-top:1px;
}

/* ---------- RESPONSIVE ---------- */
@media (min-width:640px){
 .prompt-actions{flex-direction:row;justify-content:space-between;align-items:center;}
 .prompt-actions .btn{width:auto;min-width:180px;}
 .quick-chips{grid-template-columns:repeat(3,1fr);}
 .panel-body{padding:26px 28px;}
}
@media (min-width:900px){
 .how-grid{grid-template-columns:repeat(3,1fr);gap:24px;}
}
@media (min-width:1024px){
 .est-layout{grid-template-columns:1fr 440px;gap:32px;}
 .result-panel{position:sticky;top:96px;}
 .quick-chips{grid-template-columns:repeat(5,1fr);}
 .why-grid{grid-template-columns:1fr 1fr;gap:56px;}
}
CSS;

/* Hydrate the fallback catalogue rates + post sets from the DB so
   the offline path tracks admin price edits too. The API path
   (api/estimate.php) is already DB-driven; this covers the
   rules-engine fallback below. */
$ratesJson = '[]';
$postSetsJson = '[]';
if ($pdo) {
 try {
 $rates = new stdClass();
 $qr = $pdo->query(
 "SELECT p.slug, p.price_usd, p.roll_metres, p.post_price, p.top_wire_rate, p.gate_price, p.install_rate
 FROM products p
 JOIN subcategories sc ON sc.id = p.subcategory_id
 JOIN categories c ON c.id = sc.category_id
 WHERE c.slug = 'fencing' AND p.is_active = 1
 AND p.roll_metres IS NOT NULL AND p.price_usd IS NOT NULL
 ORDER BY p.sort_order"
 );
 foreach ($qr as $r) {
 $rates->{$r['slug']} = [
 'rollPrice' => (float)$r['price_usd'],
 'rollMetres' => (float)$r['roll_metres'],
 'postPrice' => (float)$r['post_price'],
 'topWirePerM' => (float)$r['top_wire_rate'],
 'gatePrice' => (float)$r['gate_price'],
 'installPerM' => (float)$r['install_rate'],
 ];
 }
 // Per-height roll prices for diamond-mesh variants (product_specs "Height X m")
 $hst = $pdo->query(
 "SELECT p.slug, s.label, s.value
 FROM product_specs s
 JOIN products p ON p.id = s.product_id
 WHERE s.label LIKE 'Height %'"
 );
 foreach ($hst as $r) {
 if (isset($rates->{$r['slug']})
 && preg_match('/([\d.]+)/', $r['label'], $hm)
 && preg_match('/([\d.,]+)/', $r['value'], $vm)) {
 $rates->{$r['slug']}['heights'][number_format((float) $hm[1], 1)] = (float) str_replace(',', '', $vm[1]);
 }
 }
 // Barbed-wire quotes use the standard 50kg roll (700m)
 if (isset($rates->{'barbed-wire-50kg'})) $rates->{'barbed-wire'} = $rates->{'barbed-wire-50kg'};
 if (count(get_object_vars($rates))) $ratesJson = json_encode($rates, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

 $sets = [];
 foreach ($pdo->query('SELECT fence_height, post_length, corner_price, standard_price, supporter_price FROM post_sets ORDER BY sort_order') as $r) {
 $sets[] = [
 'h' => (float)$r['fence_height'], 'len' => (float)$r['post_length'],
 'corner' => (float)$r['corner_price'], 'standard' => (float)$r['standard_price'],
 'supporter' => (float)$r['supporter_price'],
 ];
 }
 if ($sets) $postSetsJson = json_encode($sets);
 } catch (Throwable $e) { /* keep fallbacks */ }
}

/* Diamond-mesh aperture × wire-gauge matrix (same map as calculator) */
$meshVariants = [
 '50x50' => ['2' => 'diamond-mesh', '2.5' => 'diamond-mesh-50x50-2-5mm', '3.15' => 'diamond-mesh-50x50-3-15mm'],
 '30x30' => ['2.5' => 'diamond-mesh-30x30-2-5mm'],
 '70x70' => ['2' => 'diamond-mesh-70x70-2mm', '2.5' => 'diamond-mesh-70x70-2-5mm', '3.15' => 'diamond-mesh-70x70-3-15mm'],
 '80x80' => ['2' => 'diamond-mesh-80x80-2mm', '2.5' => 'diamond-mesh-80x80-2-5mm', '3.15' => 'diamond-mesh-80x80-3-15mm'],
];

$extraJs = 'const DB_RATES = ' . $ratesJson . ";\n"
 . 'const DB_POST_SETS = ' . $postSetsJson . ";\n"
 . 'const MESH_VARIANTS = ' . json_encode($meshVariants) . ";\n"
 . <<<'JS'
/* ============================================================
 AI ESTIMATOR RULES ENGINE
 ============================================================ */

/* ---- Product catalogue (rates hydrated from DB when available) ---- */
const CATALOG = {
 'diamond-mesh': {
 name:'Diamond Mesh',
 desc:'A durable and cost-effective solution for your project. Ideal for security and long-term use.',
 rollPrice: 65, rollMetres: 30, postPrice: 8, topWirePerM: 0.8, gatePrice: 180, installPerM: 3.5,
 heights: {'1.0':65,'1.2':75,'1.5':90,'1.8':110,'2.0':130,'2.1':200,'2.4':220,'2.5':235,'3.0':270},
 keywords: ['diamond', 'residential', 'home', 'house', 'plot', 'boundary', 'general', 'school'],
 bestHeight: 1.8
 },
 /* Variant slugs share the family keywords but are only picked via the
 aperture/gauge remap in detectFenceType — never by keyword score. */
 'diamond-mesh-50x50-2-5mm': {
 name:'Diamond Mesh 50x50 (2.5mm)',
 desc:'50x50mm aperture with heavier 2.5mm wire — stronger and longer-lasting.',
 rollPrice: 85, rollMetres: 30, postPrice: 8, topWirePerM: 0.8, gatePrice: 180, installPerM: 3.5,
 heights: {'1.0':85,'1.2':105,'1.5':130,'1.8':150,'2.0':168,'2.1':225,'2.4':250,'2.5':265,'3.0':300},
 keywords: [],
 bestHeight: 1.8
 },
 'diamond-mesh-50x50-3-15mm': {
 name:'Diamond Mesh 50x50 (3.15mm)',
 desc:'Heavy-duty 50x50mm mesh with 3.15mm wire — maximum strength for high-security sites.',
 rollPrice: 150, rollMetres: 30, postPrice: 8, topWirePerM: 0.8, gatePrice: 180, installPerM: 3.5,
 heights: {'1.0':150,'1.2':180,'1.5':230,'1.8':270,'2.0':300,'2.1':375,'2.4':420,'2.5':440,'3.0':505},
 keywords: [],
 bestHeight: 2.1
 },
 'diamond-mesh-30x30-2-5mm': {
 name:'Diamond Mesh 30x30 (2.5mm)',
 desc:'Tighter 30x30mm aperture with 2.5mm wire — harder to climb or breach.',
 rollPrice: 110, rollMetres: 30, postPrice: 8, topWirePerM: 0.8, gatePrice: 180, installPerM: 3.5,
 heights: {'1.0':110,'1.2':133,'1.5':165,'1.8':185,'2.0':205,'2.1':223,'2.4':250,'2.5':270,'3.0':350},
 keywords: [],
 bestHeight: 1.8
 },
 'diamond-mesh-70x70-2mm': {
 name:'Diamond Mesh 70x70 (2mm)',
 desc:'Wider 70x70mm aperture with 2mm wire — economical for boundary and farm fences.',
 rollPrice: 60, rollMetres: 30, postPrice: 8, topWirePerM: 0.8, gatePrice: 180, installPerM: 3.5,
 heights: {'1.0':60,'1.2':65,'1.5':70,'1.8':80,'2.0':90,'2.1':95,'2.4':110,'2.5':115,'3.0':135},
 keywords: [],
 bestHeight: 1.8
 },
 'diamond-mesh-70x70-2-5mm': {
 name:'Diamond Mesh 70x70 (2.5mm)',
 desc:'Wider 70x70mm aperture with 2.5mm wire — economical for boundary and farm fences.',
 rollPrice: 55, rollMetres: 30, postPrice: 8, topWirePerM: 0.8, gatePrice: 180, installPerM: 3.5,
 heights: {'1.0':55,'1.2':72,'1.5':90,'1.8':105,'2.0':110,'2.1':120,'2.4':135,'2.5':140,'3.0':190},
 keywords: [],
 bestHeight: 1.8
 },
 'diamond-mesh-70x70-3-15mm': {
 name:'Diamond Mesh 70x70 (3.15mm)',
 desc:'Wider 70x70mm aperture with heavy 3.15mm wire — strong yet economical.',
 rollPrice: 124, rollMetres: 30, postPrice: 8, topWirePerM: 0.8, gatePrice: 180, installPerM: 3.5,
 heights: {'1.0':124,'1.2':140,'1.5':180,'1.8':220,'2.0':250,'2.1':270,'2.4':300,'2.5':320,'3.0':400},
 keywords: [],
 bestHeight: 2.1
 },
 'diamond-mesh-80x80-2mm': {
 name:'Diamond Mesh 80x80 (2mm)',
 desc:'Wide 80x80mm aperture with 2mm wire — the most economical diamond mesh option.',
 rollPrice: 40, rollMetres: 30, postPrice: 8, topWirePerM: 0.8, gatePrice: 180, installPerM: 3.5,
 heights: {'1.0':40,'1.2':45,'1.5':56,'1.8':70,'2.0':83,'2.1':88,'2.4':95,'2.5':100,'3.0':145},
 keywords: [],
 bestHeight: 1.8
 },
 'diamond-mesh-80x80-2-5mm': {
 name:'Diamond Mesh 80x80 (2.5mm)',
 desc:'Wide 80x80mm aperture with 2.5mm wire — the most economical diamond mesh option.',
 rollPrice: 55, rollMetres: 30, postPrice: 8, topWirePerM: 0.8, gatePrice: 180, installPerM: 3.5,
 heights: {'1.0':55,'1.2':65,'1.5':80,'1.8':93,'2.0':100,'2.1':105,'2.4':130,'2.5':130,'3.0':170},
 keywords: [],
 bestHeight: 1.8
 },
 'diamond-mesh-80x80-3-15mm': {
 name:'Diamond Mesh 80x80 (3.15mm)',
 desc:'Wide 80x80mm aperture with heavy 3.15mm wire — for large boundary fences.',
 rollPrice: 100, rollMetres: 30, postPrice: 8, topWirePerM: 0.8, gatePrice: 180, installPerM: 3.5,
 heights: {'1.0':100,'1.2':140,'1.5':160,'1.8':180,'2.0':210,'2.1':240,'2.4':260,'2.5':270,'3.0':350},
 keywords: [],
 bestHeight: 1.8
 },
 'game-fence': {
 name:'Game Fence',
 desc:'Heavy-duty fencing for wildlife, farms and large properties. Built for strength and long-term performance.',
 rollPrice: 280, rollMetres: 50, postPrice: 12, topWirePerM: 1.1, gatePrice: 220, installPerM: 4.0,
 keywords: ['game', 'wildlife', 'farm', 'hectare', 'large', 'agricultural', 'livestock'],
 bestHeight: 1.8
 },
 'barbed-wire': {
 name:'Barbed Wire (50kg roll)',
 desc:'High-tensile barbed wire great for perimeter and farm protection at a low cost per metre. Standard 50kg roll (~700m), priced per line.',
 rollPrice: 75, rollMetres: 700, postPrice: 8, topWirePerM: 0.6, gatePrice: 160, installPerM: 1.5,
 keywords: ['barbed', 'perimeter', 'farm', 'security', 'cheap', 'budget'],
 bestHeight: 1.5
 },
 'chicken-mesh': {
 name:'Chicken Mesh',
 desc:'Lightweight, galvanised mesh for poultry runs and small animal enclosures.',
 rollPrice: 32, rollMetres: 30, postPrice: 6, topWirePerM: 0.5, gatePrice: 140, installPerM: 2.0,
 keywords: ['chicken', 'poultry', 'birds', 'run', 'coop', 'garden', 'small', 'pet'],
 bestHeight: 1.2
 },
 'field-fence': {
 name:'Field Fence',
 desc:'General agricultural fencing for livestock, crop protection and rural properties.',
 rollPrice: 180, rollMetres: 50, postPrice: 10, topWirePerM: 0.9, gatePrice: 200, installPerM: 3.0,
 keywords: ['field', 'cattle', 'livestock', 'crop', 'agricultural', 'rural', 'cows', 'goats'],
 bestHeight: 1.2
 },
 'razor-wire': {
 name:'Razor Wire (Security)',
 desc:'Maximum-security installation for commercial, industrial and high-risk properties.',
 rollPrice: 95, rollMetres: 50, postPrice: 14, topWirePerM: 1.4, gatePrice: 260, installPerM: 4.5,
 keywords: ['razor', 'security', 'prison', 'commercial', 'industrial', 'warehouse', 'upgrade', 'high'],
 bestHeight: 2.1
 }
};

/* ---- Post system (client pricing model) ---- */
const POST_SETS = DB_POST_SETS.length ? DB_POST_SETS : [
 { h:1.2, len:1.8, corner:16, standard:8, supporter:12 },
 { h:1.5, len:2.0, corner:13, standard:9, supporter:13 },
 { h:2.1, len:2.6, corner:26, standard:16, supporter:13 },
 { h:2.4, len:3.0, corner:33, standard:18, supporter:15 },
 { h:2.5, len:3.0, corner:33, standard:18, supporter:15 },
 { h:3.0, len:3.6, corner:40, standard:20, supporter:16 }
];

/* Overlay DB-hydrated rates onto the static fallback catalogue */
Object.keys(DB_RATES).forEach(k => {
 if (CATALOG[k]) Object.assign(CATALOG[k], DB_RATES[k]);
});

function postSet(height){
 const s = POST_SETS.filter(x => x.h >= height - 0.001).sort((a,b) => a.h - b.h);
 return s.length ? s[0] : POST_SETS[POST_SETS.length - 1];
}

/* ---- State ---- */
let currentEstimate = null;

/* ---- Character counter ---- */
const promptEl = document.getElementById('prompt');
const charCountEl = document.getElementById('charCount');
promptEl.addEventListener('input', () => {
 charCountEl.textContent = promptEl.value.length;
});

/* ---- Quick-start chips ---- */
function fillPrompt(kind){
 const templates = {
 residential: 'I need to fence my residential property about 60m perimeter using 1.8m diamond mesh with a single pedestrian gate.',
 farm: 'Fence a 5-hectare farm perimeter with game fence at 1.8m height, including corner posts and a vehicle access gate.',
 commercial: 'Secure a commercial warehouse with about 200m of boundary fencing. Need high-security razor wire with top wire and heavy posts.',
 poultry: 'Build a chicken run for about 200 birds using 1.2m galvanised chicken mesh with light steel posts.',
 security: 'Upgrade my existing boundary fence add razor wire, increase height to 2.1m, and include a new gate with heavy hinges.'
 };
 setPrompt(templates[kind] || '');
}

/* ---- Example prompts ---- */
function setPrompt(text){
 promptEl.value = text;
 charCountEl.textContent = text.length;
 promptEl.focus();
}

/* ---- Rules engine ---- */
function detectFenceType(text){
 const t = text.toLowerCase();

 let best = null;
 let bestScore = 0;

 for (const key of Object.keys(CATALOG)){
 const p = CATALOG[key];
 let score = 0;
 p.keywords.forEach(kw => {
 if (t.includes(kw)) score += 1;
 });
 if (t.includes(p.name.toLowerCase())) score += 5;

 if (score > bestScore){
 bestScore = score;
 best = key;
 }
 }

 if (!best) best = 'diamond-mesh';

 /* Diamond-mesh aperture / wire-gauge mentions map onto the specific
 priced variant (e.g. "2.5mm diamond mesh" → diamond-mesh-50x50-2-5mm). */
 if (best.indexOf('diamond-mesh') === 0){
 let ap = '50x50';
 if (/30\s*[x×]\s*30/.test(t)) ap = '30x30';
 else if (/70\s*[x×]\s*70/.test(t)) ap = '70x70';
 else if (/80\s*[x×]\s*80/.test(t)) ap = '80x80';
 let wire = '2';
 if (/3\.15\s*mm/.test(t)) wire = '3.15';
 else if (/2\.5\s*mm/.test(t)) wire = '2.5';
 else if (/\b2\s*mm\b/.test(t)) wire = '2';
 const wires = MESH_VARIANTS[ap] || {};
 const slug = wires[wire] || wires[Object.keys(wires)[0]];
 if (slug && CATALOG[slug]) best = slug;
 }

 return { key: best, product: CATALOG[best] };
}

function detectPerimeter(text){
 const t = text.toLowerCase();
 const numMatch = t.match(/(\d+(?:\.\d+)?)\s*(m|metres?|meters?|km)\b/);
 if (numMatch){
 let n = parseFloat(numMatch[1]);
 if (numMatch[2] === 'km') n *= 1000;
 return Math.round(n);
 }

 const haMatch = t.match(/(\d+(?:\.\d+)?)\s*(hectare|hectares|ha)\b/);
 if (haMatch){
 const ha = parseFloat(haMatch[1]);
 const sqm = ha * 10000;
 const side = Math.sqrt(sqm);
 return Math.round(side * 4);
 }
 const acMatch = t.match(/(\d+(?:\.\d+)?)\s*(acre|acres)\b/);
 if (acMatch){
 const ac = parseFloat(acMatch[1]);
 const sqm = ac * 4046.86;
 const side = Math.sqrt(sqm);
 return Math.round(side * 4);
 }

 return 100;
}

function detectHeight(text, defaultHeight){
 const t = text.toLowerCase();
 const m = t.match(/(\d+(?:\.\d+)?)\s*m\b(?!\w)/) ||
 t.match(/height\s+of\s+(\d+(?:\.\d+)?)/) ||
 t.match(/(\d+(?:\.\d+)?)\s*metres?\s*(?:high|tall)/);
 if (m){
 const h = parseFloat(m[1]);
 if (h >= 0.5 && h <= 4) return h;
 }
 return defaultHeight;
}

function detectOptions(text){
 const t = text.toLowerCase();
 return {
 topWire: /top\s*wire|barbed\s*top|top\s*barbed/.test(t) || true, // default on
 gate: /gate|entrance|access|door/.test(t),
 install: /install|labour|labor|erect|build\s+for\s+me/.test(t),
 concrete: /concrete|cement|footing|base/.test(t)
 };
}

/* ---- Main estimator ---- */
function generateEstimate(){
 const text = promptEl.value.trim();

 if (text.length < 10){
 promptEl.focus();
 promptEl.style.borderColor = '#DC2626';
 setTimeout(() => promptEl.style.borderColor = '', 1200);
 return;
 }

 document.getElementById('resultEmpty').style.display = 'none';
 document.getElementById('resultLoading').style.display = 'block';
 document.getElementById('resultBody').classList.remove('active');
 document.getElementById('resultStatus').textContent = 'Analysing...';

 // Try the Groq-powered API first; fall back to the local rules engine.
 fetch('api/estimate.php', {
 method: 'POST',
 headers: { 'Content-Type': 'application/json' },
 body: JSON.stringify({ text: text })
 })
 .then(r => r.json())
 .then(d => {
 if (!d || !d.ok || !d.estimate) throw new Error('api unavailable');
 renderEstimate(mapApiEstimate(d.estimate));
 })
 .catch(() => {
 const result = computeEstimate(text);
 renderEstimate(result);
 });
}

/* Map the API response onto the shape renderEstimate() expects */
function mapApiEstimate(est){
 return {
 fenceKey: est.fence_key,
 product: {
 name: est.product.name,
 desc: est.product.desc,
 rollMetres: est.product.roll_metres,
 rollPrice: est.product.price_usd,
 postPrice: est.product.post_price,
 topWirePerM: est.product.top_wire_rate,
 gatePrice: est.product.gate_price,
 installPerM: est.product.install_rate,
 bestHeight: est.project.height
 },
 perimeter: est.project.perimeter,
 height: est.project.height,
 opts: est.options,
 items: est.items.map(it => ({ name: it.name, qty: it.qty, cost: it.total })),
 total: est.total,
 low: est.range.low,
 high: est.range.high,
 lines: est.project.lines || null
 };
}

function computeEstimate(text){
 const { key, product } = detectFenceType(text);
 const perimeter = detectPerimeter(text);
 const height = detectHeight(text, product.bestHeight);
 const opts = detectOptions(text);

 const items = [];

 /* Barbed wire is quoted per line (strand): total wire =
 perimeter × lines, sold in 50kg rolls (~700m), rounded up
 to the nearest half roll. */
 const isBarbed = key === 'barbed-wire';
 let strands = 5;
 if (isBarbed){
 const sm = (text || '').toLowerCase().match(/(\d+)\s*(?:lines?|strands?)/);
 if (sm) strands = Math.max(1, parseInt(sm[1], 10));
 }
 const wireLen = isBarbed ? perimeter * strands : perimeter;
 const rolls = isBarbed
 ? Math.ceil(wireLen / product.rollMetres * 2) / 2
 : Math.ceil(wireLen / product.rollMetres);
 // Diamond mesh rolls are priced per height — pick the nearest
 // stocked height at or above the requested one.
 let rollPrice = product.rollPrice;
 if (product.heights){
 const hk = Object.keys(product.heights).map(Number).sort((a, b) => a - b);
 const chosen = hk.find(h => h >= height - 0.001) || hk[hk.length - 1];
 if (chosen != null) rollPrice = product.heights[chosen.toFixed(1)];
 }
 const rollCost = rolls * rollPrice;
 items.push({
 name: product.name + (isBarbed ? ' — ' + strands + ' lines' : ' (' + height.toFixed(1) + 'm)'),
 qty: rolls + ' roll' + (rolls > 1 ? 's' : '') + ' @ $' + rollPrice + (isBarbed ? ' (' + wireLen.toLocaleString() + 'm wire)' : ''),
 cost: rollCost
 });

 // Post system: standards every 5m, corner posts per corner, 2 supporters per corner
 const ps = postSet(height);
 const standards = Math.ceil(perimeter / 5);
 const supporters = 8; // 2 per corner x 4 corners
 if (standards > 0) items.push({ name: `Standard Posts (${ps.len}m)`, qty: standards + ' pcs', cost: standards * ps.standard });
 items.push({ name: `Corner Posts (${ps.len}m)`, qty: '4 pcs', cost: 4 * ps.corner });
 items.push({ name: `Supporter Posts (${ps.len}m)`, qty: supporters + ' pcs', cost: supporters * ps.supporter });

 // barbed wire is already barbed — no top-wire add-on
 if (opts.topWire && !isBarbed){
 const twCost = Math.round(perimeter * product.topWirePerM * 100) / 100;
 items.push({
 name: 'Top Wire (Razor/Barbed)',
 qty: perimeter + ' m',
 cost: twCost
 });
 }

 if (opts.gate){
 items.push({
 name: 'Gate (Double Swing)',
 qty: '1 set',
 cost: product.gatePrice
 });
 }

 if (opts.install){
 const insCost = Math.round(perimeter * product.installPerM * 100) / 100;
 items.push({
 name: 'Installation (labour)',
 qty: perimeter + ' m',
 cost: insCost
 });
 }

 items.push({
 name: 'Binding Wire & Accessories',
 qty: '1 lot',
 cost: 60
 });

 const total = items.reduce((s, i) => s + i.cost, 0);
 const low = Math.round(total * 0.9 / 10) * 10;
 const high = Math.round(total * 1.15 / 10) * 10;

 return {
 fenceKey: key,
 product, perimeter, height, opts, items,
 total, low, high,
 lines: isBarbed ? strands : null
 };
}

/* ---- Render ---- */
function renderEstimate(result){
 document.getElementById('resultLoading').style.display = 'none';

 document.getElementById('resTypeName').textContent = result.product.name;
 document.getElementById('resTypeDesc').textContent = result.product.desc;

 const boq = document.getElementById('resBOQ');
 boq.innerHTML = result.items.map(it => `
 <li>
 <div class="desc"><span class="dot"></span><span class="name">${it.name}</span></div>
 <span class="qty">${it.qty}</span>
 <span class="cost">$${Number(it.cost || 0).toLocaleString('en-US', {maximumFractionDigits: 0})}</span>
 </li>
 `).join('');

 document.getElementById('resPriceRange').textContent =
 '$' + result.low.toLocaleString() + ' – $' + result.high.toLocaleString();

 document.getElementById('resultBody').classList.add('active');
 document.getElementById('resultStatus').textContent = 'Generated';

 currentEstimate = result;
}

/* ---- Actions ---- */
function useCalculator(){
 window.location.href = 'calculator.php';
}

function getFullQuote(){
 if (!currentEstimate){
 promptEl.focus();
 return;
 }
 const q = {
 ref: 'VX-AI-' + Math.floor(1000 + Math.random() * 8999),
 source: 'estimator',
 createdAt: new Date().toISOString(),
 project: {
 perimeter: currentEstimate.perimeter,
 corners: 4,
 height: currentEstimate.height,
 spacing: 2.5,
 type: currentEstimate.fenceKey,
 typeName: currentEstimate.product.name,
 lines: currentEstimate.lines || null
 },
 options: currentEstimate.opts,
 items: currentEstimate.items.map(it => ({
 name: it.name,
 spec: '',
 qty: it.qty,
 total: it.cost
 }))
 };
 try { sessionStorage.setItem('vuemax_quote', JSON.stringify(q)); } catch(e){}
 window.location.href = 'quote-results.php';
}
JS;

require __DIR__ . '/includes/header.php';
?>

<!-- ===================== HERO ===================== -->
<section class="page-hero est-hero" style="--hero-img:url('<?= e(site_image('est-hero', 'https://images.unsplash.com/photo-1500382017468-9049fed747ef?auto=format&fit=crop&w=1920&q=80')) ?>')">
 <div class="container">
 <div class="breadcrumbs">
 <a href="index.php">Home</a>
 <span class="sep">›</span>
 <span>AI Estimator</span>
 </div>

 <div class="ai-tag">
 <span class="spark"></span> AI Powered Estimator
 </div>

 <h1>Describe Your Project. <span class="accent">Get a Smart Fence Estimate.</span></h1>
 <p>Tell us what you need in plain language and our AI will generate a detailed estimate with recommended materials, quantities and costs in seconds.</p>
 </div>
 <div class="script-note">Your Project.<br>Our Expertise.<br>Powered by AI.</div>
</section>

<!-- ===================== MAIN LAYOUT ===================== -->
<div class="container">
 <div class="est-layout">

 <!-- ============ LEFT: INPUT ============ -->
 <main>
 <div class="input-panel reveal">
 <div class="panel-head">
 <div class="ico">
 <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2v4M12 18v4M4.93 4.93l2.83 2.83M16.24 16.24l2.83 2.83M2 12h4M18 12h4M4.93 19.07l2.83-2.83M16.24 7.76l2.83-2.83"/></svg>
 </div>
 <div>
 <h2>Tell Our AI About Your Project</h2>
 <p>Describe your fencing project in a few words property size, purpose, location, fence type, gates, etc.</p>
 </div>
 </div>

 <div class="panel-body">

 <div class="prompt-wrap">
 <textarea id="prompt"
 maxlength="500"
 placeholder="e.g. I need to fence a school perimeter of about 800m in Harare using 1.8m diamond mesh with a gate and top wire."></textarea>
 <div class="prompt-counter"><span id="charCount">0</span>/500</div>
 </div>

 <div class="prompt-actions">
 <button class="btn btn-outline-navy" onclick="useCalculator()">
 <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="4" y="2" width="16" height="20" rx="2"/><line x1="8" y1="6" x2="16" y2="6"/><line x1="8" y1="10" x2="16" y2="10"/></svg>
 Use Calculator Instead
 </button>
 <button class="btn btn-amber" id="generateBtn" onclick="generateEstimate()">
 <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 2v4M12 18v4M4.93 4.93l2.83 2.83M16.24 16.24l2.83 2.83M2 12h4M18 12h4M4.93 19.07l2.83-2.83M16.24 7.76l2.83-2.83"/></svg>
 Generate Estimate
 </button>
 </div>

 <!-- Quick start chips -->
 <div class="quick-section">
 <div class="quick-label">
 Or try a quick start
 <span>Click a suggestion to get started</span>
 </div>
 <div class="quick-chips">
 <button class="chip" onclick="fillPrompt('residential')">
 <div class="ico">
 <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
 </div>
 <strong>Residential</strong>
 <span>Home boundary</span>
 </button>
 <button class="chip" onclick="fillPrompt('farm')">
 <div class="ico">
 <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 20h18M4 20V8l4-3 4 3v12M12 20V8l4-3 4 3v12"/></svg>
 </div>
 <strong>Farm Perimeter</strong>
 <span>Agriculture & land</span>
 </button>
 <button class="chip" onclick="fillPrompt('commercial')">
 <div class="ico">
 <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="7" width="18" height="13" rx="1"/><path d="M8 7V5a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>
 </div>
 <strong>Commercial</strong>
 <span>Business & warehouses</span>
 </button>
 <button class="chip" onclick="fillPrompt('poultry')">
 <div class="ico">
 <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="9"/><path d="M9 12h6M12 9v6"/></svg>
 </div>
 <strong>Poultry</strong>
 <span>Chicken runs & livestock</span>
 </button>
 <button class="chip" onclick="fillPrompt('security')">
 <div class="ico">
 <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
 </div>
 <strong>Security Upgrade</strong>
 <span>Add height or razor wire</span>
 </button>
 </div>
 </div>

 <!-- Example prompts -->
 <div class="examples">
 <div class="quick-label">Or try an example</div>
 <ul class="example-list">
 <li><a onclick="setPrompt('Fence a 2-acre residential plot with diamond mesh and a gate')">Fence a 2-acre residential plot with diamond mesh and a gate</a></li>
 <li><a onclick="setPrompt('Game fence for 10 hectares with 1.8m height, access gate')">Game fence for 10 hectares with 1.8m height, access gate</a></li>
 <li><a onclick="setPrompt('Chicken run for 200 birds, 1.2m high galvanised mesh')">Chicken run for 200 birds, 1.2m high galvanised mesh</a></li>
 <li><a onclick="setPrompt('Upgrade an existing boundary fence with razor wire and top wire')">Upgrade an existing boundary fence with razor wire and top wire</a></li>
 </ul>
 </div>

 </div>
 </div>
 </main>

 <!-- ============ RIGHT: RESULT ============ -->
 <aside class="result-panel reveal reveal-d1">
 <div class="result-head">
 <h3><span class="pulse"></span> Your AI Estimate</h3>
 <span class="status" id="resultStatus">Awaiting Input</span>
 </div>

 <!-- Empty state -->
 <div class="result-empty" id="resultEmpty">
 <div class="icon">
 <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M12 2v4M12 18v4M4.93 4.93l2.83 2.83M16.24 16.24l2.83 2.83M2 12h4M18 12h4M4.93 19.07l2.83-2.83M16.24 7.76l2.83-2.83"/></svg>
 </div>
 <h4>No estimate yet</h4>
 <p>Describe your project on the left and click "Generate Estimate" to see recommendations here.</p>
 </div>

 <!-- Loading state -->
 <div class="result-loading" id="resultLoading">
 <div class="spinner"></div>
 <h4>Analysing your project...</h4>
 <p>Matching requirements with our catalogue.</p>
 </div>

 <!-- Result body -->
 <div class="result-body" id="resultBody">

 <div class="result-type">
 <div class="top-line">
 <span class="label-tiny">Recommended Fence Type</span>
 <span class="badge-best">Best Fit</span>
 </div>
 <div class="type-name" id="resTypeName">Diamond Mesh Fence</div>
 <div class="type-desc" id="resTypeDesc">A durable and cost-effective solution for your project. Ideal for security and long-term use.</div>
 </div>

 <div class="result-section">
 <div class="section-label">Key Materials (BOQ Summary)</div>
 <ul class="mini-boq" id="resBOQ"></ul>
 </div>

 <div class="price-range">
 <div class="lbl">Estimated Budget Range</div>
 <div class="val" id="resPriceRange">$0 – $0</div>
 <div class="sub">Estimated total (ZWL equivalent available)</div>
 </div>

 <div class="result-section">
 <div class="section-label">Suggested Next Steps</div>
 <ol class="next-steps" id="resNextSteps">
 <li><span class="num">1</span> Speak to our team for a detailed quote</li>
 <li><span class="num">2</span> Schedule a site visit (optional)</li>
 <li><span class="num">3</span> Confirm materials and installation dates</li>
 <li><span class="num">4</span> Get your final quotation</li>
 </ol>
 </div>

 </div>

 <!-- Result CTAs -->
 <div class="result-ctas">
 <button class="btn btn-amber" onclick="getFullQuote()">
 Get Full Quote
 <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M5 12h14M13 5l7 7-7 7"/></svg>
 </button>
 <a href="contact.php" class="btn btn-navy">
 <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
 Talk to Our Team
 </a>
 </div>
 </aside>

 </div>
</div>

<!-- ===================== HOW IT WORKS ===================== -->
<section class="how-section">
 <div class="container">
 <div class="section-head reveal">
 <h2>How It Works</h2>
 <p>Three steps from your description to a professional materials estimate.</p>
 </div>

 <div class="how-grid">
 <div class="how-card reveal">
 <div class="num">1</div>
 <h3>Tell us about your project</h3>
 <p>Describe your needs in plain language or pick a quick-start suggestion above.</p>
 </div>
 <div class="how-card reveal reveal-d1">
 <div class="num">2</div>
 <h3>AI calculates materials</h3>
 <p>We determine exact quantities and suggest products based on your requirements.</p>
 </div>
 <div class="how-card reveal reveal-d2">
 <div class="num">3</div>
 <h3>Get your estimate</h3>
 <p>Receive a detailed BOQ with estimated costs. Refine with our team anytime.</p>
 </div>
 </div>
 </div>
</section>

<!-- ===================== WHY ===================== -->
<section class="why-section">
 <div class="container">
 <div class="why-grid">
 <div class="why-img reveal" style="background-image:url('<?= e(site_image('est-why', 'https://images.unsplash.com/photo-1621905251189-08b45d6a269e?auto=format&fit=crop&w=1000&q=80')) ?>')">
 <div class="script-overlay">Smart Fencing.<br>Better Decisions.</div>
 </div>

 <div class="why-content reveal reveal-d1">
 <span class="eyebrow">Why Use the AI Estimator</span>
 <h2>Accurate. Fast. Reliable.</h2>
 <p>Our estimator combines years of fencing project data with intelligent analysis to give you a solid starting estimate no waiting for callbacks, no guesswork.</p>

 <ul class="why-list">
 <li><span class="check">✓</span> Uses real product specifications and Zimbabwe-market pricing</li>
 <li><span class="check">✓</span> Considers terrain, height, corners and special requirements</li>
 <li><span class="check">✓</span> Provides detailed BOQ with quantities and material breakdown</li>
 <li><span class="check">✓</span> You can adjust and refine the estimate at any time with our team</li>
 </ul>
 </div>
 </div>
 </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
