<?php
require_once __DIR__ . '/includes/config.php';

$pageTitle = 'Instant Fence Quote Calculator Vuemax | Zimbabwe';
$pageDesc = 'Get an accurate bill of quantities and estimated cost in minutes. Enter your project details and our system calculates the materials you need.';
$active = '';

$extraCss = <<<'CSS'
/* ============================================================
 CALCULATOR PAGE HERO
 ============================================================ */
.calc-hero{
 background:linear-gradient(90deg,rgba(10,29,51,.94) 0%,rgba(10,29,51,.78) 55%,rgba(10,29,51,.6) 100%),
 var(--hero-img, url('https://images.unsplash.com/photo-1500382017468-9049fed747ef?auto=format&fit=crop&w=1920&q=80')) center/cover no-repeat;
 color:var(--white);padding:56px 0 64px;position:relative;
}
.calc-hero .breadcrumbs{font-size:13px;color:rgba(255,255,255,.7);margin-bottom:14px;}
.calc-hero .breadcrumbs a:hover{color:var(--amber);}
.calc-hero .breadcrumbs .sep{margin:0 8px;opacity:.5;}
.calc-hero h1{
 font-family:'Playfair Display',serif;
 font-size:clamp(28px,3.6vw,44px);font-weight:700;
 line-height:1.12;letter-spacing:-.01em;margin-bottom:14px;
}
.calc-hero h1 .accent{color:var(--amber);}
.calc-hero p{
 color:rgba(255,255,255,.85);font-size:16px;
 line-height:1.65;max-width:660px;margin-bottom:28px;
}
.calc-hero .hero-badges{
 display:flex;gap:22px;flex-wrap:wrap;margin-top:22px;
}
.calc-hero .badge{
 display:flex;align-items:center;gap:8px;
 font-size:13.5px;color:rgba(255,255,255,.9);
}
.calc-hero .badge svg{color:var(--amber);flex-shrink:0;}
.calc-hero .script-note{
 position:absolute;right:60px;bottom:40px;
 font-family:'Caveat',cursive;font-size:30px;
 color:var(--amber);opacity:.9;line-height:1;
 text-align:right;transform:rotate(-4deg);
}

/* ============================================================
 STEPPER
 ============================================================ */
.stepper-section{
 background:var(--white);border-bottom:1px solid var(--border);
 padding:28px 0;
}
.stepper{
 display:flex;align-items:center;justify-content:space-between;
 max-width:920px;margin:0 auto;position:relative;
}
.stepper::before{
 content:"";position:absolute;top:22px;left:8%;right:8%;height:2px;
 background:var(--border);z-index:0;
}
.stepper-progress{
 position:absolute;top:22px;left:8%;height:2px;
 background:var(--amber);z-index:1;transition:width .35s ease;
 width:0%;
}
.step{
 display:flex;flex-direction:column;align-items:center;gap:10px;
 position:relative;z-index:2;flex:1;
}
.step .num{
 width:44px;height:44px;border-radius:50%;
 background:var(--white);border:2px solid var(--border);
 color:var(--muted);font-weight:700;font-size:15px;
 display:flex;align-items:center;justify-content:center;
 transition:.25s ease;
}
.step .label{
 font-size:13px;font-weight:500;color:var(--muted);
 text-align:center;line-height:1.3;
}
.step .label strong{display:block;color:var(--navy);font-weight:600;margin-bottom:2px;}
.step.active .num{background:var(--amber);border-color:var(--amber);color:var(--navy);}
.step.active .label{color:var(--navy);}
.step.active .label strong{color:var(--navy);font-weight:700;}
.step.done .num{background:var(--navy);border-color:var(--navy);color:var(--white);}
.step.done .label{color:var(--navy);}

/* ============================================================
 MAIN LAYOUT
 ============================================================ */
.calc-layout{
 display:grid;grid-template-columns:1fr 400px;
 gap:32px;padding:44px 0 72px;align-items:start;
}

/* Left: form panels */
.form-panel{
 background:var(--white);border:1px solid var(--border);
 border-radius:var(--radius-card);padding:32px;
 box-shadow:var(--shadow-card);
}
.panel-head{
 display:flex;align-items:flex-start;gap:16px;
 padding-bottom:22px;margin-bottom:26px;
 border-bottom:1px solid var(--border);
}
.panel-head .ico{
 width:48px;height:48px;border-radius:12px;
 background:var(--amber-soft);color:var(--navy);
 display:flex;align-items:center;justify-content:center;
 flex-shrink:0;
}
.panel-head h2{
 font-family:'Playfair Display',serif;
 font-size:24px;font-weight:700;color:var(--navy);
 line-height:1.2;margin-bottom:4px;
}
.panel-head p{font-size:13.5px;color:var(--muted);line-height:1.5;}

/* Form rows */
.form-grid{
 display:grid;grid-template-columns:1fr 1fr;gap:20px;
 margin-bottom:22px;
}
.form-grid.single{grid-template-columns:1fr;}
.field{display:flex;flex-direction:column;}
.field label{
 font-size:13px;font-weight:600;color:var(--navy);
 margin-bottom:8px;letter-spacing:.01em;
}
.field label .req{color:#DC2626;margin-left:2px;}
.field .hint{font-size:12px;color:var(--muted);margin-top:6px;line-height:1.5;}

.input-wrap{position:relative;display:flex;align-items:center;}
.input-wrap input,
.input-wrap select,
.field textarea{
 width:100%;padding:12px 14px;
 border:1.5px solid var(--border);border-radius:var(--radius-input);
 font-size:14.5px;color:var(--navy);background:var(--white);
 transition:.15s;
}
.input-wrap input:focus,
.input-wrap select:focus,
.field textarea:focus{
 outline:none;border-color:var(--navy);
 box-shadow:0 0 0 3px rgba(14,39,69,.08);
}
.input-wrap select{
 padding-right:38px;appearance:none;-webkit-appearance:none;
 background-image:url("data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%236B7280' stroke-width='2.5'><polyline points='6 9 12 15 18 9'/></svg>");
 background-repeat:no-repeat;background-position:right 14px center;
 cursor:pointer;
}
.input-wrap .suffix{
 position:absolute;right:14px;font-size:13.5px;
 color:var(--muted);font-weight:500;pointer-events:none;
}
.input-wrap.has-suffix input{padding-right:44px;}

/* Checkbox cards */
.opt-cards{display:grid;grid-template-columns:1fr 1fr;gap:14px;margin-bottom:22px;}
.opt-card{
 border:1.5px solid var(--border);background:var(--white);
 border-radius:var(--radius-card);padding:16px;
 cursor:pointer;transition:.18s ease;position:relative;
 display:flex;gap:12px;align-items:flex-start;
}
.opt-card:hover{border-color:var(--navy);}
.opt-card input{position:absolute;opacity:0;pointer-events:none;}
.opt-card .chk{
 width:20px;height:20px;border-radius:5px;border:1.5px solid var(--border);
 background:var(--white);flex-shrink:0;
 display:flex;align-items:center;justify-content:center;
 transition:.18s;margin-top:2px;
}
.opt-card .chk svg{opacity:0;transition:.15s;}
.opt-card strong{
 display:block;font-size:14px;font-weight:600;color:var(--navy);
 margin-bottom:2px;
}
.opt-card span{display:block;font-size:12.5px;color:var(--muted);line-height:1.4;}
.opt-card input:checked ~ .chk{background:var(--navy);border-color:var(--navy);}
.opt-card input:checked ~ .chk svg{opacity:1;}
.opt-card.selected{border-color:var(--navy);background:#F8FAFC;}

/* Fence type cards */
.type-cards{display:grid;grid-template-columns:repeat(3,1fr);gap:12px;margin-bottom:22px;}
.type-card{
 border:1.5px solid var(--border);background:var(--white);
 border-radius:var(--radius-card);padding:16px 12px;
 cursor:pointer;text-align:center;transition:.18s ease;
 position:relative;
}
.type-card:hover{border-color:var(--navy);}
.type-card input{position:absolute;opacity:0;pointer-events:none;}
.type-card .thumb{
 aspect-ratio:4/3;border-radius:8px;background:#eef1f4 center/cover no-repeat;
 margin-bottom:10px;
}
.type-card strong{display:block;font-size:13.5px;font-weight:600;color:var(--navy);}
.type-card span{display:block;font-size:11.5px;color:var(--muted);margin-top:2px;}
.type-card.selected{border-color:var(--navy);background:#F8FAFC;}
.type-card.selected::after{
 content:"";position:absolute;top:10px;right:10px;
 width:20px;height:20px;border-radius:50%;background:var(--navy);
 background-image:url("data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='white' stroke-width='3'><polyline points='20 6 9 17 4 12'/></svg>");
 background-repeat:no-repeat;background-position:center;
}

/* Form footer */
.form-footer{
 display:flex;justify-content:space-between;align-items:center;
 gap:14px;padding-top:22px;margin-top:8px;
 border-top:1px solid var(--border);
}
.form-footer .btn{min-width:150px;justify-content:center;}

/* ============================================================
 LIVE ESTIMATE PANEL (right rail)
 ============================================================ */
.estimate-panel{
 background:var(--white);border:1px solid var(--border);
 border-radius:var(--radius-card);box-shadow:var(--shadow-card);
 position:sticky;top:96px;overflow:hidden;
}
.estimate-head{
 background:linear-gradient(90deg,#ECFDF5 0%,#F0FDF4 100%);
 border-bottom:1px solid var(--green-line);
 padding:16px 20px;
 display:flex;justify-content:space-between;align-items:center;
}
.estimate-head h3{
 font-size:14px;font-weight:700;color:var(--green-text);
 letter-spacing:.02em;display:flex;align-items:center;gap:8px;
}
.estimate-head .pulse{
 width:8px;height:8px;border-radius:50%;background:var(--green-text);
 animation:pulse 1.8s infinite;
}
@keyframes pulse{
 0%{box-shadow:0 0 0 0 rgba(21,128,61,.5);}
 70%{box-shadow:0 0 0 8px rgba(21,128,61,0);}
 100%{box-shadow:0 0 0 0 rgba(21,128,61,0);}
}
.estimate-head .status{
 font-size:11px;font-weight:600;color:var(--green-text);
 background:var(--green-bg);padding:3px 9px;border-radius:999px;
 letter-spacing:.03em;
}

.estimate-body{padding:20px;}

/* Summary mini-grid at top */
.estimate-summary{
 display:grid;grid-template-columns:1fr 1fr;gap:10px;
 padding-bottom:16px;margin-bottom:16px;
 border-bottom:1px dashed var(--border);
}
.sum-item{
 background:var(--bg);border-radius:8px;padding:10px 12px;
}
.sum-item .lbl{font-size:11px;color:var(--muted);font-weight:500;text-transform:uppercase;letter-spacing:.04em;}
.sum-item .val{font-size:14.5px;color:var(--navy);font-weight:700;margin-top:3px;}

/* BOQ line items */
.boq-title{
 font-size:12px;font-weight:700;color:var(--navy);
 letter-spacing:.06em;text-transform:uppercase;
 margin-bottom:12px;
}
.boq-list{list-style:none;}
.boq-item{
 display:flex;justify-content:space-between;align-items:center;
 padding:9px 0;border-bottom:1px dashed var(--border);
 font-size:13.5px;
}
.boq-item:last-child{border-bottom:none;}
.boq-item .desc{display:flex;align-items:center;gap:10px;flex:1;min-width:0;}
.boq-item .dot{
 width:6px;height:6px;border-radius:50%;
 background:var(--amber);flex-shrink:0;
}
.boq-item .name{color:var(--navy);font-weight:500;line-height:1.3;}
.boq-item .qty{color:var(--muted);font-size:12.5px;margin-left:auto;padding-right:10px;white-space:nowrap;}
.boq-item .price{color:var(--navy);font-weight:700;white-space:nowrap;}
.boq-empty{
 text-align:center;padding:24px 12px;color:var(--muted);font-size:13px;
}

/* Estimate total */
.estimate-total{
 display:flex;justify-content:space-between;align-items:baseline;
 padding-top:16px;margin-top:10px;
 border-top:2px solid var(--navy);
}
.estimate-total .lbl{
 font-size:13px;font-weight:700;color:var(--navy);
 letter-spacing:.02em;text-transform:uppercase;
}
.estimate-total .val{
 font-family:'Playfair Display',serif;
 font-size:26px;font-weight:700;color:var(--navy);
}
.estimate-note{
 font-size:11.5px;color:var(--muted);line-height:1.5;
 margin-top:8px;
}

/* AI Quick-Fill */
.ai-assist{
 background:linear-gradient(135deg,#0E2745,#16294B);
 border-radius:var(--radius-card);padding:20px 22px;margin-bottom:24px;
 position:relative;overflow:hidden;
}
.ai-assist::before{
 content:'';position:absolute;top:-40px;right:-40px;width:140px;height:140px;
 background:radial-gradient(circle,rgba(245,183,49,.22),transparent 70%);
}
.ai-head{display:flex;align-items:center;gap:10px;margin-bottom:12px;color:#fff;}
.ai-head svg{color:var(--amber);flex-shrink:0;}
.ai-head strong{font-size:14.5px;font-weight:700;}
.ai-head .ai-tag{
 font-size:10px;font-weight:700;letter-spacing:.08em;text-transform:uppercase;
 background:rgba(245,183,49,.2);color:var(--amber);padding:3px 9px;border-radius:20px;
 margin-left:auto;
}
.ai-assist textarea{
 width:100%;min-height:64px;resize:vertical;
 border:1.5px solid rgba(255,255,255,.18);border-radius:10px;
 background:rgba(255,255,255,.08);color:#fff;
 padding:11px 13px;font:inherit;font-size:13.5px;line-height:1.5;
 outline:none;
}
.ai-assist textarea::placeholder{color:rgba(255,255,255,.45);}
.ai-assist textarea:focus{border-color:var(--amber);}
.ai-actions{display:flex;align-items:center;gap:12px;margin-top:12px;flex-wrap:wrap;}
.ai-status{font-size:12.5px;line-height:1.5;color:rgba(255,255,255,.75);}
.ai-status.ok{color:#7DD3A8;}
.ai-status.err{color:#FCA5A5;}
.ai-fill-btn{
 display:inline-flex;align-items:center;gap:8px;
 background:var(--amber);color:var(--navy);
 border:none;border-radius:10px;padding:10px 18px;
 font:inherit;font-size:13px;font-weight:700;cursor:pointer;
 transition:.2s;
}
.ai-fill-btn:hover{transform:translateY(-1px);box-shadow:0 6px 16px rgba(245,183,49,.35);}
.ai-fill-btn:disabled{opacity:.6;cursor:wait;transform:none;box-shadow:none;}

/* Estimate footer CTAs */
.estimate-ctas{
 padding:16px 20px 20px;border-top:1px solid var(--border);
 background:#F8FAFC;
 display:flex;flex-direction:column;gap:10px;
}
.estimate-ctas .btn{width:100%;justify-content:center;}

/* ============================================================
 TRUST STRIP (bottom)
 ============================================================ */
.bottom-trust{
 background:var(--white);border-top:1px solid var(--border);
 border-bottom:1px solid var(--border);padding:32px 0;
}
.bottom-trust-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:24px;}
.bt-item{display:flex;align-items:center;gap:12px;}
.bt-icon{
 width:42px;height:42px;border-radius:10px;
 background:var(--amber-soft);color:var(--navy);flex-shrink:0;
 display:flex;align-items:center;justify-content:center;
}
.bt-item strong{display:block;font-size:13.5px;font-weight:600;color:var(--navy);}
.bt-item span{display:block;font-size:12px;color:var(--muted);}

/* Responsive */
@media (max-width:1024px){
 .calc-layout{grid-template-columns:1fr;gap:24px;}
 .estimate-panel{position:static;}
 .type-cards{grid-template-columns:repeat(2,1fr);}
 .bottom-trust-grid{grid-template-columns:repeat(2,1fr);}
}
@media (max-width:980px){
 .calc-hero .script-note{display:none;}
}
@media (max-width:600px){
 .form-grid{grid-template-columns:1fr;}
 .opt-cards{grid-template-columns:1fr;}
 .type-cards{grid-template-columns:1fr;}
 .form-panel{padding:22px;}
 .bottom-trust-grid{grid-template-columns:1fr;}
 .stepper .label{font-size:11px;}
 .step .num{width:36px;height:36px;font-size:13px;}
}

/* ---------- MESH VARIANT SELECTORS (aperture + wire gauge) ---------- */
.mesh-opts{
 display:none;margin-top:18px;padding:18px;
 border:1px solid var(--border);border-radius:var(--radius-input);
 background:#f8fafc;
}
.mesh-opts.show{display:grid;grid-template-columns:1fr;gap:14px;}
.mesh-opts label{
 display:block;font-size:13px;font-weight:600;
 color:var(--navy);margin-bottom:8px;letter-spacing:.02em;
}
.pill-row{display:flex;gap:8px;flex-wrap:wrap;}
.calc-pill{
 padding:8px 15px;border:1.5px solid var(--border);
 border-radius:999px;background:var(--white);
 font-size:13px;font-weight:500;color:var(--text);
 cursor:pointer;transition:.15s;
}
.calc-pill:hover{border-color:var(--navy);}
.calc-pill.active{border-color:var(--navy);background:var(--navy);color:var(--white);font-weight:600;}
.calc-pill.disabled{opacity:.38;cursor:not-allowed;text-decoration:line-through;}
.calc-pill.disabled:hover{border-color:var(--border);}
.opt-card.disabled{opacity:.45;pointer-events:none;}
@media (min-width:720px){
 .mesh-opts.show{grid-template-columns:1fr 1fr;}
 .mesh-opts .mesh-note{grid-column:1/-1;}
}
CSS;

/* Hydrate the calculator catalogue + post sets from the DB when
   available — admin price edits then flow straight into quotes.
   The literals below mirror the seeded rates as the no-DB fallback. */
$catalogJson = <<<'JSON'
{"diamond-mesh":{"name":"Diamond Mesh 50x50 (2mm)","roll":65,"rollMetres":30,"topWirePerM":0.8,"gatePrice":180,"installPerM":3.5,"concretePerPost":4,"heights":{"1.0":65,"1.2":75,"1.5":90,"1.8":110,"2.0":130,"2.1":200,"2.4":220,"2.5":235,"3.0":270}},"diamond-mesh-50x50-2-5mm":{"name":"Diamond Mesh 50x50 (2.5mm)","roll":85,"rollMetres":30,"topWirePerM":0.8,"gatePrice":180,"installPerM":3.5,"concretePerPost":4,"heights":{"1.0":85,"1.2":105,"1.5":130,"1.8":150,"2.0":168,"2.1":225,"2.4":250,"2.5":265,"3.0":300}},"diamond-mesh-50x50-3-15mm":{"name":"Diamond Mesh 50x50 (3.15mm)","roll":150,"rollMetres":30,"topWirePerM":0.8,"gatePrice":180,"installPerM":3.5,"concretePerPost":4,"heights":{"1.0":150,"1.2":180,"1.5":230,"1.8":270,"2.0":300,"2.1":375,"2.4":420,"2.5":440,"3.0":505}},"diamond-mesh-30x30-2-5mm":{"name":"Diamond Mesh 30x30 (2.5mm)","roll":110,"rollMetres":30,"topWirePerM":0.8,"gatePrice":180,"installPerM":3.5,"concretePerPost":4,"heights":{"1.0":110,"1.2":133,"1.5":165,"1.8":185,"2.0":205,"2.1":223,"2.4":250,"2.5":270,"3.0":350}},"diamond-mesh-70x70-2mm":{"name":"Diamond Mesh 70x70 (2mm)","roll":60,"rollMetres":30,"topWirePerM":0.8,"gatePrice":180,"installPerM":3.5,"concretePerPost":4,"heights":{"1.0":60,"1.2":65,"1.5":70,"1.8":80,"2.0":90,"2.1":95,"2.4":110,"2.5":115,"3.0":135}},"diamond-mesh-70x70-2-5mm":{"name":"Diamond Mesh 70x70 (2.5mm)","roll":55,"rollMetres":30,"topWirePerM":0.8,"gatePrice":180,"installPerM":3.5,"concretePerPost":4,"heights":{"1.0":55,"1.2":72,"1.5":90,"1.8":105,"2.0":110,"2.1":120,"2.4":135,"2.5":140,"3.0":190}},"diamond-mesh-70x70-3-15mm":{"name":"Diamond Mesh 70x70 (3.15mm)","roll":124,"rollMetres":30,"topWirePerM":0.8,"gatePrice":180,"installPerM":3.5,"concretePerPost":4,"heights":{"1.0":124,"1.2":140,"1.5":180,"1.8":220,"2.0":250,"2.1":270,"2.4":300,"2.5":320,"3.0":400}},"diamond-mesh-80x80-2mm":{"name":"Diamond Mesh 80x80 (2mm)","roll":40,"rollMetres":30,"topWirePerM":0.8,"gatePrice":180,"installPerM":3.5,"concretePerPost":4,"heights":{"1.0":40,"1.2":45,"1.5":56,"1.8":70,"2.0":83,"2.1":88,"2.4":95,"2.5":100,"3.0":145}},"diamond-mesh-80x80-2-5mm":{"name":"Diamond Mesh 80x80 (2.5mm)","roll":55,"rollMetres":30,"topWirePerM":0.8,"gatePrice":180,"installPerM":3.5,"concretePerPost":4,"heights":{"1.0":55,"1.2":65,"1.5":80,"1.8":93,"2.0":100,"2.1":105,"2.4":130,"2.5":130,"3.0":170}},"diamond-mesh-80x80-3-15mm":{"name":"Diamond Mesh 80x80 (3.15mm)","roll":100,"rollMetres":30,"topWirePerM":0.8,"gatePrice":180,"installPerM":3.5,"concretePerPost":4,"heights":{"1.0":100,"1.2":140,"1.5":160,"1.8":180,"2.0":210,"2.1":240,"2.4":260,"2.5":270,"3.0":350}},"game-fence":{"name":"Game Fence","roll":280,"rollMetres":50,"topWirePerM":1.1,"gatePrice":220,"installPerM":4,"concretePerPost":5},"barbed-wire":{"name":"Barbed Wire (50kg roll)","roll":75,"rollMetres":700,"topWirePerM":0.6,"gatePrice":160,"installPerM":1.5,"concretePerPost":4},"chicken-mesh":{"name":"Chicken Mesh","roll":32,"rollMetres":30,"topWirePerM":0.5,"gatePrice":140,"installPerM":2,"concretePerPost":3},"field-fence":{"name":"Field Fence","roll":180,"rollMetres":50,"topWirePerM":0.9,"gatePrice":200,"installPerM":3,"concretePerPost":4},"razor-wire":{"name":"Razor Wire","roll":95,"rollMetres":50,"topWirePerM":1.4,"gatePrice":260,"installPerM":4.5,"concretePerPost":5}}
JSON;
$postSetsJson = <<<'JSON'
[{"h":1.2,"len":1.8,"corner":16,"standard":8,"supporter":12},{"h":1.5,"len":2,"corner":13,"standard":9,"supporter":13},{"h":2.1,"len":2.6,"corner":26,"standard":16,"supporter":13},{"h":2.4,"len":3,"corner":33,"standard":18,"supporter":15},{"h":2.5,"len":3,"corner":33,"standard":18,"supporter":15},{"h":3,"len":3.6,"corner":40,"standard":20,"supporter":16}]
JSON;

if ($pdo) {
 try {
 // per-post concrete rates aren't a DB column — keep the per-slug statics
 $concrete = ['diamond-mesh'=>4,'diamond-mesh-50x50-2-5mm'=>4,'diamond-mesh-50x50-3-15mm'=>4,'diamond-mesh-30x30-2-5mm'=>4,'diamond-mesh-70x70-2-5mm'=>4,'diamond-mesh-70x70-3-15mm'=>4,'diamond-mesh-80x80-2-5mm'=>4,'diamond-mesh-80x80-3-15mm'=>4,'game-fence'=>5,'barbed-wire'=>4,'chicken-mesh'=>3,'field-fence'=>4,'razor-wire'=>5];
 $cat = [];
 $qr = $pdo->query(
 "SELECT p.slug, p.name, p.price_usd, p.roll_metres, p.top_wire_rate, p.gate_price, p.install_rate
 FROM products p
 JOIN subcategories sc ON sc.id = p.subcategory_id
 JOIN categories c ON c.id = sc.category_id
 WHERE c.slug = 'fencing' AND p.is_active = 1
 AND p.roll_metres IS NOT NULL AND p.price_usd IS NOT NULL
 ORDER BY p.sort_order"
 );
 foreach ($qr as $r) {
 $cat[$r['slug']] = [
 'name' => $r['name'],
 'roll' => (float)$r['price_usd'],
 'rollMetres' => (float)$r['roll_metres'],
 'topWirePerM' => (float)$r['top_wire_rate'],
 'gatePrice' => (float)$r['gate_price'],
 'installPerM' => (float)$r['install_rate'],
 'concretePerPost' => $concrete[$r['slug']] ?? 4,
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
 if (isset($cat[$r['slug']])
 && preg_match('/([\d.]+)/', $r['label'], $hm)
 && preg_match('/([\d.,]+)/', $r['value'], $vm)) {
 $cat[$r['slug']]['heights'][number_format((float) $hm[1], 1)] = (float) str_replace(',', '', $vm[1]);
 }
 }
 // Barbed-wire quotes use the standard 50kg roll (700m)
 if (isset($cat['barbed-wire-50kg'])) $cat['barbed-wire'] = $cat['barbed-wire-50kg'];
 if ($cat) $catalogJson = json_encode($cat, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

 $sets = [];
 foreach ($pdo->query('SELECT fence_height, post_length, corner_price, standard_price, supporter_price FROM post_sets ORDER BY sort_order') as $r) {
 $sets[] = [
 'h' => (float)$r['fence_height'], 'len' => (float)$r['post_length'],
 'corner' => (float)$r['corner_price'], 'standard' => (float)$r['standard_price'],
 'supporter' => (float)$r['supporter_price'],
 ];
 }
 if ($sets) $postSetsJson = json_encode($sets);
 } catch (Throwable $e) { /* keep fallback JSON */ }
}

/* Diamond-mesh aperture × wire-gauge matrix — each combo maps to its
   own catalog entry (and its own roll price). */
$meshVariants = [
 '50x50' => ['2' => 'diamond-mesh', '2.5' => 'diamond-mesh-50x50-2-5mm', '3.15' => 'diamond-mesh-50x50-3-15mm'],
 '30x30' => ['2.5' => 'diamond-mesh-30x30-2-5mm'],
 '70x70' => ['2' => 'diamond-mesh-70x70-2mm', '2.5' => 'diamond-mesh-70x70-2-5mm', '3.15' => 'diamond-mesh-70x70-3-15mm'],
 '80x80' => ['2' => 'diamond-mesh-80x80-2mm', '2.5' => 'diamond-mesh-80x80-2-5mm', '3.15' => 'diamond-mesh-80x80-3-15mm'],
];

$extraJs = 'const CATALOG = ' . $catalogJson . ";\n"
 . 'const POST_SETS = ' . $postSetsJson . ";\n"
 . 'const MESH_VARIANTS = ' . json_encode($meshVariants) . ";\n"
 . <<<'JS'
/* ============================================================
 CALCULATOR STATE + LOGIC
 ============================================================ */

/* --- Post system (client pricing model) ---
   Each fence height uses a post set: corner posts at each corner,
   standard posts spaced along the line, and 2 supporter (stay)
   posts per corner post. */
function postSet(height){
 const s = POST_SETS.filter(x => x.h >= height - 0.001).sort((a,b) => a.h - b.h);
 return s.length ? s[0] : POST_SETS[POST_SETS.length - 1];
}

let currentStep = 1;
const TOTAL_STEPS = 4;

/* --- Step navigation --- */
function goStep(n){
 n = Math.max(1, Math.min(TOTAL_STEPS, n));
 currentStep = n;

 // Panels
 for (let i = 1; i <= TOTAL_STEPS; i++){
 const p = document.getElementById('panel-' + i);
 p.style.display = (i === n) ? 'block' : 'none';
 }

 // Stepper state
 document.querySelectorAll('#stepper .step').forEach(s => {
 const sn = parseInt(s.dataset.step, 10);
 s.classList.toggle('active', sn === n);
 s.classList.toggle('done', sn < n);
 });

 // Progress bar
 const pct = ((n - 1) / (TOTAL_STEPS - 1)) * 84; // 84% is the span between first & last
 document.getElementById('stepperProgress').style.width = pct + '%';

 window.scrollTo({ top: 180, behavior: 'smooth' });
}

/* --- Read current form values --- */
function readInputs(){
 const perimeter = Math.max(0, parseFloat(document.getElementById('perimeter').value || 0));
 const corners = parseInt(document.getElementById('corners').value || 0, 10);
 const height = Math.max(0, parseFloat(document.getElementById('height').value || 0));
 const spacing = Math.max(1, parseFloat(document.getElementById('spacing').value || 2.5));
 const typeRadio = document.querySelector('input[name="fenceType"]:checked');
 let type = typeRadio ? typeRadio.value : 'diamond-mesh';
 if (type === 'diamond-mesh'){
 const apEl = document.querySelector('#calcAperture .calc-pill.active');
 const wrEl = document.querySelector('#calcWire .calc-pill.active');
 const ap = apEl ? apEl.dataset.ap : '50x50';
 const wr = wrEl ? wrEl.dataset.wire : '2';
 type = (MESH_VARIANTS[ap] || {})[wr] || 'diamond-mesh';
 }

 const linesEl = document.querySelector('#calcLines .calc-pill.active');
 const lines = Math.max(1, parseInt(linesEl ? linesEl.dataset.lines : '5', 10) || 5);

 const opts = {
 // barbed wire is already barbed — no top-wire add-on
 topWire: document.getElementById('optTopWire').checked && type !== 'barbed-wire',
 gate: document.getElementById('optGate').checked,
 install: document.getElementById('optInstall').checked,
 concrete: document.getElementById('optConcrete').checked
 };

 return { perimeter, corners, height, spacing, type, lines, opts };
}

/* --- Compute BOQ --- */
function computeBOQ(){
 const v = readInputs();
 const p = CATALOG[v.type] || CATALOG['diamond-mesh'];
 if (!p || v.perimeter <= 0) return { items:[], total:0, v, p };

 const items = [];

 // Fence rolls — barbed wire is quoted per line (strand):
 // total wire = perimeter × lines, sold in 50kg rolls (~700m),
 // rounded up to the nearest half roll.
 const isBarbed = v.type === 'barbed-wire';
 const wireLen = isBarbed ? v.perimeter * v.lines : v.perimeter;
 const rolls = isBarbed
 ? Math.ceil(wireLen / p.rollMetres * 2) / 2
 : Math.ceil(wireLen / p.rollMetres);
 // Diamond mesh rolls are priced per height — e.g. 50x50 2.5mm at
 // 2.1m is $225/roll, not the $85 base (1.0m) price.
 const rollPrice = (p.heights && p.heights[v.height.toFixed(1)] != null)
 ? p.heights[v.height.toFixed(1)]
 : p.roll;
 const rollCost = rolls * rollPrice;
 items.push({
 name: p.name + (isBarbed ? ' — ' + v.lines + ' lines' : ' (' + v.height.toFixed(1) + 'm)'),
 qty: rolls + ' roll' + (rolls>1?'s':'') + ' @ $' + rollPrice + (isBarbed ? ' (' + wireLen.toLocaleString() + 'm wire)' : ''),
 price: rollCost
 });

 // Posts — client model: standards every X m, corner posts at
 // each corner, 2 supporter posts per corner post.
 const ps = postSet(v.height);
 const standards = Math.ceil(v.perimeter / v.spacing);
 const supporters = v.corners * 2;
 const posts = standards + v.corners + supporters;

 if (standards > 0){
 items.push({
 name: `Standard Posts (${ps.len}m) — every ${v.spacing}m`,
 qty: standards + ' pcs @ $' + ps.standard,
 price: standards * ps.standard
 });
 }
 if (v.corners > 0){
 items.push({
 name: `Corner Posts (${ps.len}m)`,
 qty: v.corners + ' pcs @ $' + ps.corner,
 price: v.corners * ps.corner
 });
 }
 if (supporters > 0){
 items.push({
 name: `Supporter Posts (${ps.len}m) — 2 per corner`,
 qty: supporters + ' pcs @ $' + ps.supporter,
 price: supporters * ps.supporter
 });
 }

 // Top wire
 if (v.opts.topWire){
 const twCost = Math.round(v.perimeter * p.topWirePerM * 100) / 100;
 items.push({
 name: 'Top Wire (barbed)',
 qty: v.perimeter + ' m',
 price: twCost
 });
 }

 // Gate
 if (v.opts.gate){
 items.push({ name:'Access Gate (4m)', qty:'1 set', price:p.gatePrice });
 }

 // Concrete
 if (v.opts.concrete){
 const ccCost = posts * p.concretePerPost;
 items.push({
 name:'Concrete Post Bases',
 qty: posts + ' posts',
 price: ccCost
 });
 }

 // Installation
 if (v.opts.install){
 const insCost = Math.round(v.perimeter * p.installPerM * 100) / 100;
 items.push({
 name:'Installation (labour)',
 qty: v.perimeter + ' m',
 price: insCost
 });
 }

 const total = items.reduce((s, i) => s + i.price, 0);
 return { items, total, v, p };
}

/* --- Render --- */
function render(){
 const { items, total, v, p } = computeBOQ();

 // Summary mini-grid
 document.getElementById('sumPerimeter').textContent = (v.perimeter || 0) + ' m';
 document.getElementById('sumCorners').textContent = v.corners || '0';
 document.getElementById('sumType').textContent = p ? p.name : '—';
 document.getElementById('sumHeight').textContent = (v.height || 0).toFixed(1) + ' m';

 // BOQ list
 const list = document.getElementById('boqList');
 if (items.length === 0){
 list.innerHTML = '<li class="boq-empty">Enter your project details to see the bill of quantities.</li>';
 } else {
 list.innerHTML = items.map(it => `
 <li class="boq-item">
 <div class="desc"><span class="dot"></span><span class="name">${it.name}</span></div>
 <span class="qty">${it.qty}</span>
 <span class="price">$${it.price.toLocaleString('en-US', {minimumFractionDigits:2, maximumFractionDigits:2})}</span>
 </li>
 `).join('');
 }

 // Total
 document.getElementById('grandTotal').textContent =
 '$' + total.toLocaleString('en-US', {minimumFractionDigits:2, maximumFractionDigits:2});
}

/* --- Live listeners --- */
['perimeter','corners','height','spacing',
 'optTopWire','optGate','optInstall','optConcrete'].forEach(id => {
 const el = document.getElementById(id);
 if (el) el.addEventListener('input', render);
});

/* syncTypeUI: show the right variant controls per fence type —
 diamond mesh gets aperture/wire pills, barbed wire gets the
 lines selector, and barbed wire disables the top-wire option
 (the wire is already barbed). */
function syncTypeUI(){
 const r = document.querySelector('input[name="fenceType"]:checked');
 const val = r ? r.value : 'diamond-mesh';
 document.getElementById('meshOpts').classList.toggle('show', val === 'diamond-mesh');
 const barb = document.getElementById('barbOpts');
 if (barb) barb.classList.toggle('show', val === 'barbed-wire');
 const tw = document.getElementById('optTopWire');
 if (tw){
 const card = tw.closest('.opt-card');
 if (val === 'barbed-wire'){
 tw.checked = false; tw.disabled = true;
 if (card){ card.classList.remove('selected'); card.classList.add('disabled'); }
 } else {
 tw.disabled = false;
 if (card) card.classList.remove('disabled');
 }
 }
}

document.querySelectorAll('input[name="fenceType"]').forEach(r => {
 r.addEventListener('change', () => {
 document.querySelectorAll('.type-card').forEach(c => c.classList.remove('selected'));
 if (r.checked) r.closest('.type-card').classList.add('selected');
 syncTypeUI();
 render();
 });
});

/* ---- Barbed-wire lines (strands) pills ---- */
(function(){
 const lWrap = document.getElementById('calcLines');
 if (!lWrap) return;
 lWrap.querySelectorAll('.calc-pill').forEach(b => b.addEventListener('click', () => {
 lWrap.querySelectorAll('.calc-pill').forEach(x => x.classList.remove('active'));
 b.classList.add('active');
 render();
 }));
})();

/* ---- Diamond-mesh aperture / wire pills ---- */
(function(){
 const apWrap = document.getElementById('calcAperture');
 const wWrap = document.getElementById('calcWire');
 if (!apWrap || !wWrap) return;

 /* setMeshPills(ap, wire): activate the aperture pill, update which
 wire gauges are stocked for it, and activate the closest wire. */
 window.setMeshPills = function(ap, wire){
 apWrap.querySelectorAll('.calc-pill').forEach(x =>
 x.classList.toggle('active', x.dataset.ap === ap));
 const wires = MESH_VARIANTS[ap] || {};
 wWrap.querySelectorAll('.calc-pill').forEach(x =>
 x.classList.toggle('disabled', !wires[x.dataset.wire]));
 if (!wires[wire]) wire = Object.keys(wires)[0];
 wWrap.querySelectorAll('.calc-pill').forEach(x =>
 x.classList.toggle('active', x.dataset.wire === wire));
 };

 apWrap.querySelectorAll('.calc-pill').forEach(b => b.addEventListener('click', () => {
 const curWire = wWrap.querySelector('.calc-pill.active');
 window.setMeshPills(b.dataset.ap, curWire ? curWire.dataset.wire : '2');
 render();
 }));
 wWrap.querySelectorAll('.calc-pill').forEach(b => b.addEventListener('click', () => {
 if (b.classList.contains('disabled')) return;
 wWrap.querySelectorAll('.calc-pill').forEach(x => x.classList.remove('active'));
 b.classList.add('active');
 render();
 }));
})();

// Opt-card visual selected state
document.querySelectorAll('.opt-card input').forEach(cb => {
 cb.addEventListener('change', () => {
 cb.closest('.opt-card').classList.toggle('selected', cb.checked);
 });
});

/* --- AI Quick-Fill: parse free text into form fields (Groq, rules fallback) --- */
document.getElementById('aiFill').addEventListener('click', async function(){
 const txt = document.getElementById('aiText').value.trim();
 const status = document.getElementById('aiStatus');
 if (txt.length < 10){
 status.className = 'ai-status err';
 status.textContent = 'Describe your project in a few words first.';
 return;
 }
 this.disabled = true;
 status.className = 'ai-status';
 status.textContent = 'Thinking…';
 try {
 const res = await fetch('api/estimate.php', {
 method: 'POST',
 headers: { 'Content-Type': 'application/json' },
 body: JSON.stringify({ text: txt, assist: true })
 });
 const j = await res.json();
 if (!j.ok) throw new Error(j.error || 'Could not understand that.');
 const q = j.parsed;

 // Apply to fields
 if (q.perimeter) document.getElementById('perimeter').value = q.perimeter;
 if (q.spacing) document.getElementById('spacing').value = q.spacing;
 if (q.corners != null){
 const cSel = document.getElementById('corners');
 const opts = [...cSel.options].map(o => parseInt(o.value, 10));
 cSel.value = opts.reduce((a, b) => Math.abs(b - q.corners) < Math.abs(a - q.corners) ? b : a);
 }
 if (q.height){
 const hSel = document.getElementById('height');
 const hOpts = [...hSel.options].map(o => parseFloat(o.value));
 hSel.value = hOpts.reduce((a, b) => Math.abs(b - q.height) < Math.abs(a - q.height) ? b : a);
 }
 if (q.fence_slug){
 let radio = document.querySelector(`input[name="fenceType"][value="${q.fence_slug}"]`);
 // variant slugs (e.g. diamond-mesh-30x30-2-5mm) map onto the
 // Diamond Mesh radio + its aperture/wire pills
 if (!radio){
 for (const ap in MESH_VARIANTS){
 for (const w in MESH_VARIANTS[ap]){
 if (MESH_VARIANTS[ap][w] === q.fence_slug){
 radio = document.querySelector('input[name="fenceType"][value="diamond-mesh"]');
 if (window.setMeshPills) window.setMeshPills(ap, w);
 }
 }
 }
 }
 if (radio){
 radio.checked = true;
 document.querySelectorAll('.type-card').forEach(c => c.classList.remove('selected'));
 radio.closest('.type-card').classList.add('selected');
 syncTypeUI();
 }
 }
 ['topWire','gate','install','concrete'].forEach(k => {
 const id = 'opt' + k.charAt(0).toUpperCase() + k.slice(1);
 const cb = document.getElementById(id);
 if (cb && q.options){
 cb.checked = !!q.options[k];
 cb.closest('.opt-card')?.classList.toggle('selected', cb.checked);
 }
 });
 render();

 const p = CATALOG[q.fence_slug];
 const bits = [];
 if (p) bits.push(p.name);
 bits.push(q.perimeter + 'm perimeter', q.height + 'm high');
 if (q.corners) bits.push(q.corners + ' corners');
 const extras = [];
 if (q.options?.topWire) extras.push('top wire');
 if (q.options?.gate) extras.push('gate');
 if (q.options?.install) extras.push('installation');
 if (q.options?.concrete) extras.push('concrete');
 if (extras.length) bits.push('with ' + extras.join(' + '));

 status.className = 'ai-status ok';
 status.textContent = 'Filled: ' + bits.join(' · ') + (j.engine === 'rules' ? ' (offline mode)' : '');
 } catch (e) {
 status.className = 'ai-status err';
 status.textContent = e.message || 'Could not parse — fill the form manually.';
 }
 this.disabled = false;
});

/* --- Download BOQ (visual placeholder for now) --- */
function downloadBOQ(){
 const { items, total, v, p } = computeBOQ();
 if (items.length === 0){ alert('Enter your project details first.'); return; }

 let txt = 'VUEMAX BILL OF QUANTITIES\n';
 txt += '================================\n\n';
 txt += `Project: ${p.name} fencing, ${v.perimeter}m perimeter, ${v.height}m height\n`;
 txt += `Corners: ${v.corners} Post spacing: ${v.spacing}m\n\n`;
 items.forEach(i => {
 txt += `- ${i.name}\n Qty: ${i.qty} Price: $${i.price.toFixed(2)}\n`;
 });
 txt += `\nESTIMATED TOTAL: $${total.toFixed(2)}\n`;
 txt += `\nPrices are estimates. Contact sales@vuemax.co.zw for an official quote.\n`;

 const blob = new Blob([txt], { type:'text/plain' });
 const url = URL.createObjectURL(blob);
 const a = document.createElement('a');
 a.href = url;
 a.download = 'vuemax-quote.txt';
 a.click();
 URL.revokeObjectURL(url);
}

/* --- Generate Final Quote --- */
document.getElementById('generateQuote').addEventListener('click', function(){
 const name = document.getElementById('custName').value.trim();
 const contact = document.getElementById('custContact').value.trim();
 if (!name || !contact){
 alert('Please enter your name and a phone or email.');
 return;
 }

 // Build the quote object from current state
 const { items, total, v, p } = computeBOQ();
 const quote = {
 ref: 'VX-' + new Date().getFullYear() + '-' + Math.floor(10000 + Math.random() * 89999),
 source: 'calculator',
 createdAt: new Date().toISOString(),
 customer: { name, contact, notes: document.getElementById('custNotes').value.trim() },
 project: {
 perimeter: v.perimeter, corners: v.corners, height: v.height,
 spacing: v.spacing, type: v.type, typeName: p ? p.name : 'Fencing',
 lines: v.type === 'barbed-wire' ? v.lines : null
 },
 options: v.opts,
 items: items.map(it => ({
 name: it.name,
 spec: '',
 qty: it.qty,
 total: it.price
 }))
 };

 // Persist so quote-results.php can read it
 try { sessionStorage.setItem('vuemax_quote', JSON.stringify(quote)); } catch(e){}

 const orig = this.innerHTML;
 this.innerHTML = '✓ Redirecting…';
 this.style.background = '#15803D';
 this.style.color = '#fff';

 setTimeout(() => {
 window.location.href = 'quote-results.php';
 }, 600);
});

/* --- Initial render --- */
render();
JS;

require __DIR__ . '/includes/header.php';
?>

<!-- ===================== PAGE HERO ===================== -->
<section class="calc-hero" style="--hero-img:url('<?= e(site_image('calc-hero', 'https://images.unsplash.com/photo-1500382017468-9049fed747ef?auto=format&fit=crop&w=1920&q=80')) ?>')">
 <div class="container">
 <div class="breadcrumbs">
 <a href="index.php">Home</a>
 <span class="sep">›</span>
 <span>Instant Quote</span>
 </div>
 <h1>Instant Fence <span class="accent">Quote Calculator</span></h1>
 <p>Get an accurate bill of quantities and estimated cost in minutes. Enter a few details about your project and our system automatically calculates the materials you need.</p>

 <div class="hero-badges">
 <div class="badge">
 <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M13 2 3 14h9l-1 8 10-12h-9l1-8z"/></svg>
 Auto BOQ in seconds
 </div>
 <div class="badge">
 <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="4" y="2" width="16" height="20" rx="2"/><line x1="8" y1="6" x2="16" y2="6"/><line x1="8" y1="10" x2="16" y2="10"/></svg>
 Accurate cost estimates
 </div>
 <div class="badge">
 <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
 Takes under 2 minutes
 </div>
 <div class="badge">
 <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
 Download or share quote
 </div>
 </div>
 </div>
 <div class="script-note">Fast.<br>Accurate.<br>Reliable.</div>
</section>

<!-- ===================== STEPPER ===================== -->
<div class="stepper-section">
 <div class="container">
 <div class="stepper" id="stepper">
 <div class="stepper-progress" id="stepperProgress"></div>

 <div class="step active" data-step="1">
 <div class="num">1</div>
 <div class="label"><strong>Project Size</strong>Your property details</div>
 </div>
 <div class="step" data-step="2">
 <div class="num">2</div>
 <div class="label"><strong>Fence Type</strong>Choose materials</div>
 </div>
 <div class="step" data-step="3">
 <div class="num">3</div>
 <div class="label"><strong>Options</strong>Add extras</div>
 </div>
 <div class="step" data-step="4">
 <div class="num">4</div>
 <div class="label"><strong>Summary</strong>Review & generate</div>
 </div>
 </div>
 </div>
</div>

<!-- ===================== MAIN LAYOUT ===================== -->
<div class="container">
 <div class="calc-layout">

 <!-- LEFT: FORM PANELS -->
 <div>

 <!-- ================= STEP 1 ================= -->
 <div class="form-panel" id="panel-1">
 <div class="panel-head">
 <div class="ico">
 <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="4" y="2" width="16" height="20" rx="2"/><line x1="8" y1="6" x2="16" y2="6"/><line x1="8" y1="10" x2="16" y2="10"/><line x1="8" y1="14" x2="12" y2="14"/></svg>
 </div>
 <div>
 <h2>Project Details</h2>
 <p>Tell us about your fencing project so we can calculate the bill of quantities.</p>
 </div>
 </div>

 <!-- AI Quick-Fill -->
 <div class="ai-assist">
 <div class="ai-head">
 <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2l1.8 5.2L19 9l-5.2 1.8L12 16l-1.8-5.2L5 9l5.2-1.8L12 2zm7 10l.9 2.6L22.5 15.5l-2.6.9L19 19l-.9-2.6-2.6-.9 2.6-.9L19 12zM6 14l.9 2.6L9.5 17.5l-2.6.9L6 21l-.9-2.6-2.6-.9L5.1 16.6 6 14z"/></svg>
 </div>
 <textarea id="aiText" placeholder="Describe your project in one sentence — e.g. &quot;200m barbed wire fence, 2.1m high, around my farm plot with a gate and installation&quot;"></textarea>
 <div class="ai-actions">
 <button type="button" class="ai-fill-btn" id="aiFill">
 <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2l1.8 5.2L19 9l-5.2 1.8L12 16l-1.8-5.2L5 9l5.2-1.8L12 2z"/></svg>
 Fill my quote
 </button>
 <div class="ai-status" id="aiStatus"></div>
 </div>
 </div>

 <div class="form-grid">
 <div class="field">
 <label>Total perimeter length <span class="req">*</span></label>
 <div class="input-wrap has-suffix">
 <input type="number" id="perimeter" value="800" min="1" step="1" placeholder="e.g. 800">
 <span class="suffix">m</span>
 </div>
 <span class="hint">The total distance around the area you want to fence.</span>
 </div>

 <div class="field">
 <label>Number of corners <span class="req">*</span></label>
 <div class="input-wrap">
 <select id="corners">
 <option value="0">Straight (no corners)</option>
 <option value="4" selected>4 corners</option>
 <option value="6">6 corners</option>
 <option value="8">8 corners</option>
 <option value="10">10 corners</option>
 </select>
 </div>
 <span class="hint">Most rectangular properties have 4 corners.</span>
 </div>
 </div>

 <div class="form-footer">
 <span></span>
 <button class="btn btn-amber" onclick="goStep(2)">
 Next: Fence Type
 <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M5 12h14M13 5l7 7-7 7"/></svg>
 </button>
 </div>
 </div>

 <!-- ================= STEP 2 ================= -->
 <div class="form-panel" id="panel-2" style="display:none;">
 <div class="panel-head">
 <div class="ico">
 <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M3 9h18M3 15h18M9 3v18M15 3v18"/></svg>
 </div>
 <div>
 <h2>Fence Type &amp; Height</h2>
 <p>Choose your fencing material and the height you need. This determines the quantities calculated.</p>
 </div>
 </div>

 <div class="type-cards" id="typeCards">
 <label class="type-card selected">
 <input type="radio" name="fenceType" value="diamond-mesh" checked>
 <div class="thumb" style="background-image:url('<?= e(site_image('prod-diamond-mesh', 'assets/img/products/diamond-mesh-2.jpg')) ?>')"></div>
 <strong>Diamond Mesh</strong>
 <span>Popular · durable</span>
 </label>
 <label class="type-card">
 <input type="radio" name="fenceType" value="game-fence">
 <div class="thumb" style="background-image:url('<?= e(site_image('prod-game-fence', 'assets/img/products/game-fence.jpg')) ?>')"></div>
 <strong>Game Fence</strong>
 <span>Heavy-duty wildlife</span>
 </label>
 <label class="type-card">
 <input type="radio" name="fenceType" value="barbed-wire">
 <div class="thumb" style="background-image:url('<?= e(site_image('prod-barbed-wire', 'assets/img/products/barbed-wire.jpg')) ?>')"></div>
 <strong>Barbed Wire</strong>
 <span>High security</span>
 </label>
 <label class="type-card">
 <input type="radio" name="fenceType" value="chicken-mesh">
 <div class="thumb" style="background-image:url('<?= e(site_image('prod-chicken-mesh', 'https://images.unsplash.com/photo-1767416171650-4bff1da861fe?auto=format&fit=crop&w=400&q=80')) ?>')"></div>
 <strong>Chicken Mesh</strong>
 <span>Poultry & small animals</span>
 </label>
 <label class="type-card">
 <input type="radio" name="fenceType" value="field-fence">
 <div class="thumb" style="background-image:url('<?= e(site_image('prod-field-fence', 'https://images.unsplash.com/photo-1566780856910-f0cc7a8fb0c1?auto=format&fit=crop&w=400&q=80')) ?>')"></div>
 <strong>Field Fence</strong>
 <span>Agricultural</span>
 </label>
 <label class="type-card">
 <input type="radio" name="fenceType" value="razor-wire">
 <div class="thumb" style="background-image:url('<?= e(site_image('prod-razor-wire', 'https://images.unsplash.com/photo-1759614539716-01f836befe88?auto=format&fit=crop&w=800&q=80')) ?>')"></div>
 <strong>Razor Wire</strong>
 <span>Max security</span>
 </label>
 </div>

 <!-- Diamond-mesh variants: each aperture + wire gauge has its own roll price -->
 <div class="mesh-opts show" id="meshOpts">
 <div>
 <label>Aperture (hole size)</label>
 <div class="pill-row" id="calcAperture">
 <button type="button" class="calc-pill active" data-ap="50x50">50 × 50 mm</button>
 <button type="button" class="calc-pill" data-ap="30x30">30 × 30 mm</button>
 <button type="button" class="calc-pill" data-ap="70x70">70 × 70 mm</button>
 <button type="button" class="calc-pill" data-ap="80x80">80 × 80 mm</button>
 </div>
 </div>
 <div>
 <label>Wire diameter</label>
 <div class="pill-row" id="calcWire">
 <button type="button" class="calc-pill active" data-wire="2">2mm</button>
 <button type="button" class="calc-pill" data-wire="2.5">2.5mm</button>
 <button type="button" class="calc-pill" data-wire="3.15">3.15mm</button>
 </div>
 </div>
 <span class="hint mesh-note">Each aperture and wire gauge carries its own price per 30m roll — the totals update automatically.</span>
 </div>

 <!-- Barbed wire: number of lines (strands) multiplies the wire length -->
 <div class="mesh-opts" id="barbOpts">
 <div>
 <label>Number of lines (strands)</label>
 <div class="pill-row" id="calcLines">
 <button type="button" class="calc-pill" data-lines="3">3 lines</button>
 <button type="button" class="calc-pill" data-lines="4">4 lines</button>
 <button type="button" class="calc-pill active" data-lines="5">5 lines</button>
 <button type="button" class="calc-pill" data-lines="6">6 lines</button>
 <button type="button" class="calc-pill" data-lines="8">8 lines</button>
 </div>
 </div>
 <span class="hint mesh-note">Rolls needed = perimeter × lines ÷ 700m per 50kg roll (rounded up to the nearest half roll).</span>
 </div>

 <div class="form-grid">
 <div class="field">
 <label>Fence height <span class="req">*</span></label>
 <div class="input-wrap has-suffix">
 <select id="height">
 <option value="1.2">1.2 m</option>
 <option value="1.5">1.5 m</option>
 <option value="2.1" selected>2.1 m</option>
 <option value="2.4">2.4 m</option>
 <option value="2.5">2.5 m</option>
 <option value="3.0">3.0 m</option>
 </select>
 <span class="suffix">m</span>
 </div>
 <span class="hint">Post size is matched automatically.</span>
 </div>

 <div class="field">
 <label>Standard post spacing <span class="req">*</span></label>
 <div class="input-wrap has-suffix">
 <input type="number" id="spacing" value="5" min="1" step="0.5" placeholder="e.g. 5">
 <span class="suffix">m</span>
 </div>
 <span class="hint">Standard posts are placed every 5m.</span>
 </div>
 </div>

 <div class="form-footer">
 <button class="btn btn-outline-navy" onclick="goStep(1)">
 <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
 Back
 </button>
 <button class="btn btn-amber" onclick="goStep(3)">
 Next: Options
 <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M5 12h14M13 5l7 7-7 7"/></svg>
 </button>
 </div>
 </div>

 <!-- ================= STEP 3 ================= -->
 <div class="form-panel" id="panel-3" style="display:none;">
 <div class="panel-head">
 <div class="ico">
 <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 2.83-2.83l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg>
 </div>
 <div>
 <h2>Additional Options</h2>
 <p>Add extras to improve security and functionality. All optional.</p>
 </div>
 </div>

 <div class="opt-cards">
 <label class="opt-card selected">
 <input type="checkbox" id="optTopWire" checked>
 <span class="chk">
 <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg>
 </span>
 <div>
 <strong>Top wire (barbed)</strong>
 <span>3 strands along the top for added height and security.</span>
 </div>
 </label>

 <label class="opt-card selected">
 <input type="checkbox" id="optGate" checked>
 <span class="chk">
 <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg>
 </span>
 <div>
 <strong>Include gate</strong>
 <span>1 × 4m vehicle access gate with hinges and lock.</span>
 </div>
 </label>

 <label class="opt-card">
 <input type="checkbox" id="optInstall">
 <span class="chk">
 <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg>
 </span>
 <div>
 <strong>Installation (labour)</strong>
 <span>Professional install by our team. Includes setup and cleanup.</span>
 </div>
 </label>

 <label class="opt-card">
 <input type="checkbox" id="optConcrete">
 <span class="chk">
 <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg>
 </span>
 <div>
 <strong>Concrete post bases</strong>
 <span>Cement footings for every post extra durability.</span>
 </div>
 </label>
 </div>

 <div class="form-footer">
 <button class="btn btn-outline-navy" onclick="goStep(2)">
 <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
 Back
 </button>
 <button class="btn btn-amber" onclick="goStep(4)">
 Review Summary
 <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M5 12h14M13 5l7 7-7 7"/></svg>
 </button>
 </div>
 </div>

 <!-- ================= STEP 4 ================= -->
 <div class="form-panel" id="panel-4" style="display:none;">
 <div class="panel-head">
 <div class="ico">
 <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 11 12 14 22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg>
 </div>
 <div>
 <h2>Review Your Quote</h2>
 <p>Check the details below. When you're happy, generate your final quote or request an official quotation.</p>
 </div>
 </div>

 <div class="form-grid">
 <div class="field">
 <label>Your name</label>
 <div class="input-wrap">
 <input type="text" id="custName" placeholder="e.g. John Moyo">
 </div>
 </div>
 <div class="field">
 <label>Phone or email</label>
 <div class="input-wrap">
 <input type="text" id="custContact" placeholder="e.g. 0784 000 000">
 </div>
 </div>
 </div>

 <div class="form-grid single">
 <div class="field">
 <label>Additional notes (optional)</label>
 <textarea id="custNotes" rows="3" placeholder="Location, special requirements, timeframe..." style="resize:vertical;"></textarea>
 </div>
 </div>

 <div class="form-footer">
 <button class="btn btn-outline-navy" onclick="goStep(3)">
 <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
 Back
 </button>
 <button class="btn btn-amber" id="generateQuote">
 Generate Final Quote
 <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M5 12h14M13 5l7 7-7 7"/></svg>
 </button>
 </div>
 </div>

 </div>

 <!-- RIGHT: LIVE ESTIMATE -->
 <aside class="estimate-panel">
 <div class="estimate-head">
 <h3><span class="pulse"></span> Live Estimate</h3>
 <span class="status">Auto BOQ</span>
 </div>

 <div class="estimate-body">
 <div class="estimate-summary">
 <div class="sum-item">
 <div class="lbl">Perimeter</div>
 <div class="val" id="sumPerimeter">800 m</div>
 </div>
 <div class="sum-item">
 <div class="lbl">Corners</div>
 <div class="val" id="sumCorners">4</div>
 </div>
 <div class="sum-item">
 <div class="lbl">Fence Type</div>
 <div class="val" id="sumType">Diamond Mesh</div>
 </div>
 <div class="sum-item">
 <div class="lbl">Height</div>
 <div class="val" id="sumHeight">1.8 m</div>
 </div>
 </div>

 <div class="boq-title">Bill of Quantities</div>
 <ul class="boq-list" id="boqList">
 <!-- Populated by JS -->
 </ul>

 <div class="estimate-total">
 <span class="lbl">Estimated Total</span>
 <span class="val" id="grandTotal">$0.00</span>
 </div>
 <p class="estimate-note">Prices are estimates and may vary based on location, terrain and specific requirements.</p>
 </div>

 <div class="estimate-ctas">
 <button class="btn btn-outline-navy" onclick="downloadBOQ()">
 <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
 Download BOQ
 </button>
 <button class="btn btn-navy" onclick="goStep(4)">
 Request Final Quote
 <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M5 12h14M13 5l7 7-7 7"/></svg>
 </button>
 </div>
 </aside>

 </div>
</div>

<!-- ===================== BOTTOM TRUST ===================== -->
<section class="bottom-trust">
 <div class="container">
 <div class="bottom-trust-grid">
 <div class="bt-item">
 <div class="bt-icon">🚚</div>
 <div><strong>Nationwide Delivery</strong><span>Across Zimbabwe</span></div>
 </div>
 <div class="bt-item">
 <div class="bt-icon">✅</div>
 <div><strong>Quality Assured</strong><span>SABS Compliant</span></div>
 </div>
 <div class="bt-item">
 <div class="bt-icon">👥</div>
 <div><strong>Expert Support</strong><span>From Quote to Installation</span></div>
 </div>
 <div class="bt-item">
 <div class="bt-icon">⚡</div>
 <div><strong>Fast Quotations</strong><span>Results in Seconds</span></div>
 </div>
 </div>
 </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
