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
CSS;

$extraJs = <<<'JS'
/* ============================================================
 CALCULATOR STATE + LOGIC
 ============================================================ */

/* --- Product catalogue (Phase 2 will replace with fetch('/api/products.php')) --- */
const CATALOG = {
 'diamond-mesh': { name:'Diamond Mesh', roll:65, rollMetres:30, topWirePerM:0.8, gatePrice:180, installPerM:3.5, concretePerPost:4 },
 'game-fence': { name:'Game Fence', roll:280, rollMetres:50, topWirePerM:1.1, gatePrice:220, installPerM:4.0, concretePerPost:5 },
 'barbed-wire': { name:'Barbed Wire (25kg)', roll:38, rollMetres:100,topWirePerM:0.6, gatePrice:160, installPerM:2.5, concretePerPost:4 },
 'chicken-mesh': { name:'Chicken Mesh', roll:32, rollMetres:30, topWirePerM:0.5, gatePrice:140, installPerM:2.0, concretePerPost:3 },
 'field-fence': { name:'Field Fence', roll:180, rollMetres:50, topWirePerM:0.9, gatePrice:200, installPerM:3.0, concretePerPost:4 },
 'razor-wire': { name:'Razor Wire', roll:95, rollMetres:50, topWirePerM:1.4, gatePrice:260, installPerM:4.5, concretePerPost:5 }
};

/* --- Post system (client pricing model) ---
   Each fence height uses a post set: corner posts at each corner,
   standard posts spaced along the line, and 2 supporter (stay)
   posts per corner post. */
const POST_SETS = [
 { h:1.2, len:1.8, corner:16, standard:8, supporter:12 },
 { h:1.5, len:2.0, corner:13, standard:9, supporter:13 },
 { h:2.1, len:2.6, corner:26, standard:16, supporter:13 },
 { h:2.4, len:3.0, corner:33, standard:18, supporter:15 },
 { h:2.5, len:3.0, corner:33, standard:18, supporter:15 },
 { h:3.0, len:3.6, corner:40, standard:20, supporter:16 }
];
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
 const type = typeRadio ? typeRadio.value : 'diamond-mesh';

 const opts = {
 topWire: document.getElementById('optTopWire').checked,
 gate: document.getElementById('optGate').checked,
 install: document.getElementById('optInstall').checked,
 concrete: document.getElementById('optConcrete').checked
 };

 return { perimeter, corners, height, spacing, type, opts };
}

/* --- Compute BOQ --- */
function computeBOQ(){
 const v = readInputs();
 const p = CATALOG[v.type];
 if (!p || v.perimeter <= 0) return { items:[], total:0, v, p };

 const items = [];

 // Fence rolls
 const rollArea = p.rollMetres; // linear metres coverage per roll (approx)
 const rolls = Math.ceil(v.perimeter / rollArea);
 const rollCost = rolls * p.roll;
 items.push({
 name: p.name + ' (' + v.height.toFixed(1) + 'm)',
 qty: rolls + ' roll' + (rolls>1?'s':''),
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

document.querySelectorAll('input[name="fenceType"]').forEach(r => {
 r.addEventListener('change', () => {
 document.querySelectorAll('.type-card').forEach(c => c.classList.remove('selected'));
 if (r.checked) r.closest('.type-card').classList.add('selected');
 render();
 });
});

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
 const radio = document.querySelector(`input[name="fenceType"][value="${q.fence_slug}"]`);
 if (radio){
 radio.checked = true;
 document.querySelectorAll('.type-card').forEach(c => c.classList.remove('selected'));
 radio.closest('.type-card').classList.add('selected');
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
 createdAt: new Date().toISOString(),
 customer: { name, contact, notes: document.getElementById('custNotes').value.trim() },
 project: {
 perimeter: v.perimeter, corners: v.corners, height: v.height,
 spacing: v.spacing, type: v.type, typeName: p ? p.name : 'Fencing'
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
 <div class="thumb" style="background-image:url('<?= e(site_image('prod-diamond-mesh', 'assets/img/products/diamond-mesh.jpg')) ?>')"></div>
 <strong>Diamond Mesh</strong>
 <span>Popular · durable</span>
 </label>
 <label class="type-card">
 <input type="radio" name="fenceType" value="game-fence">
 <div class="thumb" style="background-image:url('<?= e(site_image('prod-game-fence', 'https://images.unsplash.com/photo-1702641397914-30fbd18c0d93?auto=format&fit=crop&w=400&q=80')) ?>')"></div>
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
