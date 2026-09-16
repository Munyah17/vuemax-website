<?php
require_once __DIR__ . '/includes/config.php';

/* ---------- Resolve product by ?slug= ----------
   Primary: MySQL `products` + `product_specs`.
   Fallback: static map below so the page works offline/file:// too. */
$slug = preg_replace('/[^a-z0-9-]/', '', strtolower($_GET['slug'] ?? 'diamond-mesh'));
if ($slug === '') $slug = 'diamond-mesh';

// [name, price|null, unit label, short, long, image slot key, fallback image, specs[]]
$PD = [
 'diamond-mesh' => ['Diamond Mesh 50x50 (2mm)', 65.00, '30m roll',
   'Versatile, durable and cost-effective fencing. 50x50mm aperture, 2mm wire, 30m rolls.',
   'Diamond mesh fencing designed for maximum strength and durability. 50x50mm aperture, 2mm wire gauge, hot-dip galvanised to resist rust and corrosion. Each roll is 30m long, available in heights from 1.0m to 3.0m. Also stocked in 2.5mm and 3.15mm gauges and 30x30mm aperture.',
   'prod-diamond-mesh', 'assets/img/products/diamond-mesh-2.jpg',
   [['Mesh Aperture','50 mm × 50 mm'],['Wire Gauge','2.0 mm'],['Roll Length','30 m'],['Heights','1.0 m – 3.0 m'],['Finish','Hot-dip galvanised']]],
 'diamond-mesh-50x50-2-5mm' => ['Diamond Mesh 50x50 (2.5mm)', 85.00, '30m roll',
   'Versatile, durable fencing. 50x50mm aperture, 2.5mm wire, 30m rolls.',
   'Diamond mesh fencing, 50x50mm aperture, 2.5mm wire gauge, hot-dip galvanised. Each roll is 30m long, available in heights from 1.0m to 3.0m. Also stocked in 2mm and 3.15mm gauges and 30x30mm aperture.',
   'prod-diamond-25', 'assets/img/products/diamond-mesh-2.jpg',
   [['Mesh Aperture','50 mm × 50 mm'],['Wire Gauge','2.5 mm'],['Roll Length','30 m'],['Heights','1.0 m – 3.0 m'],['Finish','Hot-dip galvanised']]],
 'diamond-mesh-50x50-3-15mm' => ['Diamond Mesh 50x50 (3.15mm)', 150.00, '30m roll',
   'Heavy-duty diamond mesh. 50x50mm aperture, 3.15mm wire, 30m rolls.',
   'Heavy-duty diamond mesh fencing, 50x50mm aperture, 3.15mm wire gauge, hot-dip galvanised. Each roll is 30m long, available in heights from 1.0m to 3.0m. Also stocked in 2mm and 2.5mm gauges and 30x30mm aperture.',
   'prod-diamond-315', 'assets/img/products/diamond-mesh-2.jpg',
   [['Mesh Aperture','50 mm × 50 mm'],['Wire Gauge','3.15 mm'],['Roll Length','30 m'],['Heights','1.0 m – 3.0 m'],['Finish','Hot-dip galvanised']]],
 'diamond-mesh-30x30-2-5mm' => ['Diamond Mesh 30x30 (2.5mm)', 110.00, '30m roll',
   'Tighter-mesh diamond fence. 30x30mm aperture, 2.5mm wire, 30m rolls.',
   'Diamond mesh fencing with a tighter 30x30mm aperture, 2.5mm wire gauge, hot-dip galvanised. Each roll is 30m long, available in heights from 1.0m to 3.0m. Also stocked in 50x50mm aperture in 2mm, 2.5mm and 3.15mm gauges.',
   'prod-diamond-30x30', 'assets/img/products/diamond-mesh.jpg',
   [['Mesh Aperture','30 mm × 30 mm'],['Wire Gauge','2.5 mm'],['Roll Length','30 m'],['Heights','1.0 m – 3.0 m'],['Finish','Hot-dip galvanised']]],
 'game-fence' => ['Game Fence', 280.00, 'roll',
   'Heavy-duty fencing for wildlife, farms and large properties.',
   'Manufactured from high-tensile galvanised wire, our game fence is built to withstand the demands of wildlife and livestock enclosures. Ideal for game reserves, large farms and perimeter security.',
   'prod-game-fence', 'https://images.unsplash.com/photo-1518709268805-4e9042af9f23?auto=format&fit=crop&w=1200&q=80',
   [['Material','High-tensile galvanised wire'],['Roll Length','50 m'],['Finish','Hot-dip galvanised']]],
 'barbed-wire' => ['Barbed Wire 25 kg Roll', 38.00, 'roll',
   'High-tensile, high-security barbed wire for perimeter and farm protection. 25 kg roll.',
   'Hot-dip galvanised barbed wire with 3-strand twist, supplied in a 25 kg roll. Also available in a 50 kg roll. Perfect for farm perimeter security, commercial sites and any application requiring a low-cost but effective deterrent.',
   'prod-barbed-wire', 'assets/img/products/barbed-wire.jpg',
   [['Roll Weight','25 kg'],['Also Available','50 kg roll'],['Strand Twist','3-strand'],['Finish','Hot-dip galvanised']]],
 'barbed-wire-50kg' => ['Barbed Wire 50 kg Roll', 75.00, 'roll',
   'High-tensile, high-security barbed wire for perimeter and farm protection. 50 kg roll.',
   'Hot-dip galvanised barbed wire with 3-strand twist, supplied in a 50 kg roll. Also available in a 25 kg roll. Perfect for farm perimeter security, commercial sites and any application requiring a low-cost but effective deterrent.',
   'prod-barbed-wire-50', 'assets/img/products/barbed-wire-50kg.jpg',
   [['Roll Weight','50 kg'],['Also Available','25 kg roll'],['Strand Twist','3-strand'],['Finish','Hot-dip galvanised']]],
 'chicken-mesh' => ['Chicken Mesh', 32.00, 'roll',
   'Lightweight, galvanised mesh for poultry runs and small animal enclosures.',
   'Fine-gauge galvanised chicken mesh ideal for poultry runs, garden enclosures and small animal protection. Lightweight yet durable, resistant to rust and easy to install.',
   'prod-chicken-mesh', 'https://images.unsplash.com/photo-1767416171650-4bff1da861fe?auto=format&fit=crop&w=1200&q=80',
   [['Material','Galvanised steel wire'],['Finish','Hot-dip galvanised']]],
 'welded-mesh' => ['Welded Mesh', null, 'roll',
   'High-quality galvanised welded mesh with evenly welded intersections for strength and long life.',
   'High-quality galvanised welded mesh manufactured from durable steel wire, with evenly welded intersections for strength, stability, and long-lasting performance. Ideal for fencing, security applications, construction, animal enclosures, and general fabrication projects.',
   'prod-welded-mesh', 'assets/img/products/welded-mesh.jpg',
   [['Material','Hot-dip galvanised steel wire'],['Pattern','Evenly welded square intersections'],['Supply','Rolls — various heights and apertures'],['Uses','Fencing, security, enclosures, construction, fabrication'],['Pricing','Supplied on request — contact us with your spec']]],
 'field-fence' => ['Field Fence', 180.00, 'roll',
   'General agricultural fencing for livestock and crop protection.',
   'Versatile field fencing designed for livestock containment and crop protection. Galvanised woven wire construction stands up to Zimbabwean conditions season after season.',
   'prod-field-fence', 'https://images.unsplash.com/photo-1566780856910-f0cc7a8fb0c1?auto=format&fit=crop&w=1200&q=80',
   [['Material','Galvanised woven wire'],['Roll Length','50 m']]],
 'razor-wire' => ['Razor Wire', 95.00, 'roll',
   'Enhanced perimeter security for high-risk installations.',
   'Concertina razor wire for maximum perimeter security. Razor-sharp blades mounted on a galvanised core, ideal for prisons, banks, warehouses and high-risk commercial properties.',
   'prod-razor-wire', 'https://images.unsplash.com/photo-1759614539716-01f836befe88?auto=format&fit=crop&w=800&q=80',
   [['Type','Concertina razor wire'],['Core','Galvanised'],['Roll Length','50 m']]],
 'fence-posts' => ['Fence Posts', 8.00, 'piece',
   'Galvanised steel posts — corner, standard and supporter posts matched to fence height.',
   'assets/img/products/diamond-mesh.jpg',
   'prod-fence-posts', 'assets/img/products/round-pole-75mm.jpg',
   [['For 1.2 m fence — 1.8 m posts','Corner $16 · Standard $8 · Supporter $12'],
    ['For 1.5 m fence — 2.0 m posts','Corner $13 · Standard $9 · Supporter $13'],
    ['For 2.1 m fence — 2.6 m posts','Corner $26 · Standard $16 · Supporter $13'],
    ['For 2.4 m fence — 3.0 m posts','Corner $33 · Standard $18 · Supporter $15'],
    ['For 2.5 m fence — 3.0 m posts','Corner $33 · Standard $18 · Supporter $15'],
    ['For 3.0 m fence — 3.6 m posts','Corner $40 · Standard $20 · Supporter $16'],
    ['Placement','Standards every 5 m · 1 corner post per corner · 2 supporters per corner'],
    ['Finish','Galvanised']]],
 'binding-wire' => ['Binding Wire & Clamps', 25.00, 'lot',
   'Binding wire, tensioning wire, clamps and tensioners for installation.',
   'Everything you need to secure and tension your fence line — galvanised binding wire, tension wire, wire clamps and turnbuckles for a professional finish.',
   'prod-binding-wire', 'https://images.unsplash.com/photo-1533154683836-84ea7a0bc310?auto=format&fit=crop&w=1200&q=80',
   [['Contents','Binding wire, clamps, tensioners'],['Finish','Galvanised']]],
 'checkered-plate-3mm' => ['Checkered Plate Galvanised 3mm', 122.00, 'sheet',
   'Galvanised checkered steel plate, 3mm thick, 2.4m × 1.2m sheet.',
   'Galvanised checkered (tread) plate, 3mm thickness, supplied as 2.4m × 1.2m sheets. Anti-slip raised pattern — ideal for flooring, walkways, ramps, trailers and industrial platforms.',
   'prod-checkered-plate', 'assets/img/products/checkered-plate-3mm.jpg',
   [['Thickness','3 mm'],['Sheet Size','2.4 m × 1.2 m'],['Finish','Galvanised, checkered pattern']]],
 'galvanised-round-pole-75mm' => ['Galvanised Round Pole 75mm', 36.00, 'length (6m)',
   'Galvanised round steel pole, 75mm diameter × 2mm wall × 6000mm.',
   'Galvanised round steel pole — 75mm outside diameter, 2mm wall thickness, 6000mm standard length. Suitable for fencing posts, structural supports, gates and general fabrication.',
   'prod-round-pole-75', 'assets/img/products/round-pole-75mm.jpg',
   [['Diameter','75 mm'],['Wall Thickness','2 mm'],['Length','6000 mm'],['Finish','Galvanised']]],
 'galvanised-round-pole-32mm' => ['Galvanised Round Pole 32mm', 25.00, 'length (6m)',
   'Galvanised round steel pole, 32mm diameter × 2mm wall × 6000mm.',
   'Galvanised round steel pole — 32mm outside diameter, 2mm wall thickness, 6000mm standard length. Suitable for fencing posts, structural supports, gates and general fabrication.',
   'prod-round-pole-32', 'assets/img/products/round-pole-32mm.jpg',
   [['Diameter','32 mm'],['Wall Thickness','2 mm'],['Length','6000 mm'],['Finish','Galvanised']]],
 'galvanised-round-pole-38mm' => ['Galvanised Round Pole 38mm', 28.00, 'length (6m)',
   'Galvanised round steel pole, 38mm diameter × 2mm wall × 6000mm.',
   'Galvanised round steel pole — 38mm outside diameter, 2mm wall thickness, 6000mm standard length. Suitable for fencing posts, structural supports, gates and general fabrication.',
   'prod-round-pole-38', 'assets/img/products/round-pole-38mm.jpg',
   [['Diameter','38 mm'],['Wall Thickness','2 mm'],['Length','6000 mm'],['Finish','Galvanised']]],
 'square-tubes' => ['Square Tubes', null, 'length',
   'Steel square tubes in various sizes and thicknesses. Supplied on request.',
   'Steel square tubes for gates, frames, structural work and fabrication. Available in various sizes and thicknesses. Not a stock item — supplied on request.',
   'prod-square-tubes', 'assets/img/products/square-tubes.jpg',
   [['Type','Square hollow section'],['Availability','Supplied on request']]],
 'angle-irons' => ['Angle Irons', null, 'length',
   'Steel angle sections in various sizes. Supplied on request.',
   'Strong and versatile steel angle sections designed for structural support and general fabrication. Ideal for frames, brackets, supports, fencing, construction and engineering applications. Available in various sizes and thicknesses. Not a stock item — supplied on request.',
   'prod-angle-irons', 'assets/img/products/angle-irons.jpg',
   [['Type','Equal/unequal angle'],['Availability','Supplied on request']]],
 'deformed-bars' => ['Deformed Bars', null, 'length',
   'High-strength ribbed reinforcement bars for reinforced concrete. Supplied on request.',
   'High-strength steel reinforcement bars with a ribbed surface designed to provide excellent bonding with concrete. Ideal for reinforced concrete structures, foundations, columns, beams, slabs and general construction projects. Not a stock item — supplied on request.',
   'prod-deformed-bars', 'assets/img/products/deformed-bars.jpg',
   [['Type','Deformed (ribbed) rebar'],['Availability','Supplied on request']]],
];

$prod  = $PD[$slug] ?? null;
$specs = $prod ? $prod[7] : [];
$badge = null; $rating = null; $reviews = 0;

// Prefer live DB when connected
if ($pdo) {
    try {
        $st = $pdo->prepare('SELECT * FROM products WHERE slug = ? AND is_active = 1 LIMIT 1');
        $st->execute([$slug]);
        $row = $st->fetch();
        if ($row) {
            $prod = [$row['name'], $row['price_usd'] !== null ? (float)$row['price_usd'] : null,
                     $row['unit'], $row['short_desc'], $row['long_desc'],
                     'prod-' . $slug, $row['image'], $prod ? $prod[7] : []];
            $badge = $row['badge']; $rating = (float)$row['rating']; $reviews = (int)$row['reviews_count'];
            $st = $pdo->prepare('SELECT label, value FROM product_specs WHERE product_id = ? ORDER BY sort_order');
            $st->execute([$row['id']]);
            $specs = array_map(fn($r) => [$r['label'], $r['value']], $st->fetchAll());
        }
    } catch (Throwable $e) { /* keep fallback */ }
}

if (!$prod) {
    // generic render for catalog items without a full record
    $pretty = ucwords(str_replace('-', ' ', $slug));
    $prod = [$pretty, null, 'item',
        'Quality product supplied by Vuemax.',
        $pretty . ' — supplied by Vuemax Industries. Contact us for specifications, pricing and availability.',
        'prod-' . $slug, 'https://images.unsplash.com/photo-1565793298595-6a879b1d9492?auto=format&fit=crop&w=1200&q=80',
        [['Availability','Contact us for specs & pricing']]];
}

[$pName, $pPrice, $pUnit, $pShort, $pLong, $pImgKey, $pImgFallback] = $prod;
$mainImg = site_image($pImgKey, $pImgFallback);
// second gallery image: <basename>-2.<ext> if it exists alongside the main image
$img2 = null;
if ($mainImg && strpos($mainImg, 'http') !== 0) {
    $cand = preg_replace('/\.(jpe?g|png|webp)$/i', '-2.$1', $mainImg);
    if ($cand !== $mainImg && file_exists(__DIR__ . '/' . $cand)) $img2 = $cand;
    // if the main image IS the "-2" file, offer the base file as the second shot
    if (!$img2 && preg_match('/-2\.(jpe?g|png|webp)$/i', $mainImg)) {
        $base = preg_replace('/-2\.(jpe?g|png|webp)$/i', '.$1', $mainImg);
        if (file_exists(__DIR__ . '/' . $base)) $img2 = $base;
    }
}
$onRequest = ($pPrice === null);
$sku = 'VX-' . strtoupper(preg_replace('/[^A-Z0-9]/', '', substr(md5($slug), 0, 6)));

$pageTitle = $pName . ' | Vuemax';
$pageDesc  = mb_substr(strip_tags($pShort), 0, 150);
$active = '';

$extraCss = <<<'CSS'
/* ---------- BREADCRUMB STRIP ---------- */
.breadcrumb-strip{
 background:var(--white);border-bottom:1px solid var(--border);
 padding:14px 0;
}
.breadcrumbs{font-size:13px;color:var(--muted);}
.breadcrumbs a{color:var(--muted);transition:.15s;}
.breadcrumbs a:hover{color:var(--navy);}
.breadcrumbs .sep{margin:0 8px;opacity:.5;}
.breadcrumbs .current{color:var(--navy);font-weight:600;}

/* ---------- Product Hero (image + info) ---------- */
.product-hero{padding:44px 0 56px;}
.product-hero-grid{
 display:grid;grid-template-columns:1fr;
 gap:32px;align-items:flex-start;
}

/* Left: gallery */
.gallery{position:sticky;top:96px;}
.gallery-main{
 aspect-ratio:4/3;background:#eef1f4 center/cover no-repeat;
 border-radius:var(--radius-card);overflow:hidden;
 border:1px solid var(--border);position:relative;
}
.gallery-main .badge-pop{
 position:absolute;top:16px;left:16px;
 background:var(--amber);color:var(--navy);
 font-size:11px;font-weight:700;letter-spacing:.08em;
 text-transform:uppercase;padding:6px 12px;border-radius:6px;
}
.gallery-thumbs{
 display:grid;grid-template-columns:repeat(4,1fr);gap:10px;
 margin-top:14px;
}
.gallery-thumb{
 aspect-ratio:1/1;background:#eef1f4 center/cover no-repeat;
 border-radius:8px;cursor:pointer;border:2px solid transparent;
 transition:.15s;
}
.gallery-thumb:hover{border-color:var(--border);}
.gallery-thumb.active{border-color:var(--amber);}

/* Right: info */
.product-info h1{
 font-family:'Playfair Display',serif;
 font-size:clamp(28px,3.2vw,40px);
 font-weight:700;color:var(--navy);line-height:1.15;
 letter-spacing:-.01em;margin-bottom:10px;
}
.product-meta{
 display:flex;align-items:center;gap:14px;
 margin-bottom:18px;flex-wrap:wrap;
 font-size:13.5px;color:var(--muted);
}
.product-meta .stars{color:var(--amber);font-size:15px;letter-spacing:1px;}
.product-meta .divider{width:1px;height:14px;background:var(--border);}

.product-info .tagline{
 font-size:16px;color:var(--text);line-height:1.65;
 margin-bottom:22px;
}

/* Pricing block */
.price-block{
 background:var(--white);border:1px solid var(--border);
 border-radius:var(--radius-card);padding:20px;
 margin-bottom:22px;
}
.price-row{
 display:flex;justify-content:space-between;align-items:baseline;
 padding:10px 0;border-bottom:1px dashed var(--border);
 font-size:14.5px;
}
.price-row:last-of-type{border-bottom:none;}
.price-row .label{color:var(--muted);}
.price-row .value{font-weight:600;color:var(--navy);}
.price-row.primary{padding-top:4px;}
.price-row.primary .value{font-size:26px;font-family:'Playfair Display',serif;color:var(--navy);}
.price-note{font-size:12.5px;color:var(--muted);margin-top:10px;}

/* Options */
.option-group{margin-bottom:22px;}
.option-group label{
 display:block;font-size:13px;font-weight:600;
 color:var(--navy);margin-bottom:8px;letter-spacing:.02em;
}
.option-pills{display:flex;gap:8px;flex-wrap:wrap;}
.option-pill{
 padding:9px 16px;border:1.5px solid var(--border);
 border-radius:var(--radius-input);background:var(--white);
 font-size:13.5px;font-weight:500;color:var(--text);
 cursor:pointer;transition:.15s;
}
.option-pill:hover{border-color:var(--navy);}
.option-pill.active{border-color:var(--navy);background:var(--navy);color:var(--white);font-weight:600;}

/* Quantity + Add to quote row */
.qty-row{
 display:flex;gap:12px;margin-bottom:14px;
 align-items:stretch;flex-wrap:wrap;
}
.qty-input{
 display:inline-flex;align-items:center;
 border:1.5px solid var(--border);border-radius:var(--radius-input);
 overflow:hidden;background:var(--white);
}
.qty-input button{
 width:42px;height:48px;display:flex;align-items:center;justify-content:center;
 color:var(--navy);font-size:20px;transition:.15s;
}
.qty-input button:hover{background:var(--bg);}
.qty-input input{
 width:64px;height:48px;border:none;text-align:center;
 font-size:15px;font-weight:600;color:var(--navy);
 background:transparent;
}
.qty-input input:focus{outline:none;}
.qty-row .btn{flex:1;justify-content:center;min-width:200px;height:48px;}

/* Action buttons row */
.actions-row{display:flex;gap:12px;margin-bottom:26px;flex-wrap:wrap;}
.actions-row .btn{height:48px;justify-content:center;flex:1;min-width:180px;}

/* Trust chips */
.trust-chips{
 display:grid;grid-template-columns:1fr;gap:10px;
 padding-top:22px;border-top:1px solid var(--border);
}
.trust-chip{
 display:flex;align-items:center;gap:10px;
 font-size:13px;color:var(--text);font-weight:500;
}
.trust-chip .dot{
 width:26px;height:26px;border-radius:50%;
 background:var(--green-bg);color:var(--green-text);
 display:flex;align-items:center;justify-content:center;
 flex-shrink:0;font-size:13px;
}

/* ---------- Info tabs (Description / Specs / Applications) ---------- */
.info-tabs-section{
 background:var(--white);border-top:1px solid var(--border);
 border-bottom:1px solid var(--border);padding:56px 0;
}
.info-tabs-head{
 display:flex;gap:4px;border-bottom:1px solid var(--border);
 margin-bottom:36px;overflow-x:auto;scrollbar-width:none;
}
.info-tabs-head::-webkit-scrollbar{display:none;}
.info-tab{
 padding:14px 22px;font-size:14.5px;font-weight:500;
 color:var(--muted);border-bottom:2px solid transparent;
 transition:.15s;white-space:nowrap;cursor:pointer;
 margin-bottom:-1px;
}
.info-tab:hover{color:var(--navy);}
.info-tab.active{color:var(--navy);font-weight:600;border-bottom-color:var(--amber);}

.tab-panel{display:none;animation:fade .25s ease;}
.tab-panel.active{display:block;}
@keyframes fade{from{opacity:0;transform:translateY(4px);}to{opacity:1;transform:translateY(0);}}

.tab-panel h3{
 font-family:'Playfair Display',serif;
 font-size:22px;font-weight:700;color:var(--navy);
 margin-bottom:14px;
}
.tab-panel p{
 font-size:15px;color:var(--text);line-height:1.75;
 margin-bottom:14px;max-width:820px;
}

/* Specs table */
.specs-table{width:100%;border-collapse:collapse;max-width:820px;}
.specs-table tr{border-bottom:1px solid var(--border);}
.specs-table tr:last-child{border-bottom:none;}
.specs-table td{padding:14px 0;font-size:14.5px;vertical-align:top;}
.specs-table td:first-child{
 width:auto;color:var(--muted);font-weight:500;
 display:block;padding-bottom:2px;
}
.specs-table td:last-child{
 color:var(--navy);font-weight:600;
 display:block;padding-top:0;padding-bottom:12px;
}

/* Applications list */
.apps-list{list-style:none;max-width:820px;}
.apps-list li{
 display:flex;gap:12px;align-items:flex-start;
 padding:12px 0;font-size:15px;color:var(--text);
 border-bottom:1px solid var(--border);
}
.apps-list li:last-child{border-bottom:none;}
.apps-list li .check{
 width:22px;height:22px;border-radius:50%;
 background:var(--green-bg);color:var(--green-text);
 display:flex;align-items:center;justify-content:center;
 flex-shrink:0;font-size:12px;margin-top:2px;
}

/* ---------- Related Products ---------- */
.related{padding:72px 0;}
.related .section-head-row{
 display:flex;flex-direction:column;
 margin-bottom:36px;gap:16px;
}
.related .section-head{margin-bottom:0;}
.related .section-head .link-more{align-self:flex-start;}

/* ---------- CTA wrap ---------- */
.cta-wrap{padding:0 24px 72px;}

/* ---------- Responsive ---------- */
@media (min-width:640px){
 .trust-chips{grid-template-columns:repeat(2,1fr);}
 .specs-table td:first-child{width:220px;display:table-cell;padding-bottom:14px;}
 .specs-table td:last-child{display:table-cell;padding-top:14px;padding-bottom:14px;}
 .related .section-head-row{flex-direction:row;justify-content:space-between;align-items:flex-end;gap:24px;flex-wrap:wrap;}
}
@media (min-width:1024px){
 .product-hero-grid{grid-template-columns:1fr 1fr;gap:56px;}
}
@media (max-width:1023px){
 .gallery{position:static;}
}
CSS;

$extraJs = <<<'JS'
/* ============================================================
 PRODUCT DETAIL INTERACTIONS
 ============================================================ */
(function(){
 /* ---- Gallery thumb switching ---- */
 const main = document.getElementById('galleryMain');
 document.querySelectorAll('#galleryThumbs .gallery-thumb').forEach(t => {
 t.addEventListener('click', () => {
 const bg = t.style.backgroundImage;
 main.style.backgroundImage = bg.replace('w=400','w=1200');
 document.querySelectorAll('#galleryThumbs .gallery-thumb').forEach(x => x.classList.remove('active'));
 t.classList.add('active');
 });
 });

 /* ---- Option pill selection ---- */
 ['heightOpts','lengthOpts'].forEach(id => {
 const wrap = document.getElementById(id);
 if (!wrap) return;
 wrap.querySelectorAll('.option-pill').forEach(pill => {
 pill.addEventListener('click', () => {
 wrap.querySelectorAll('.option-pill').forEach(p => p.classList.remove('active'));
 pill.classList.add('active');
 });
 });
 });

 /* ---- Quantity stepper ---- */
 const qty = document.getElementById('qtyValue');
 document.getElementById('qtyMinus').addEventListener('click', () => {
 let v = Math.max(1, parseInt(qty.value || '1', 10) - 1);
 qty.value = v;
 });
 document.getElementById('qtyPlus').addEventListener('click', () => {
 let v = Math.min(999, parseInt(qty.value || '1', 10) + 1);
 qty.value = v;
 });
 qty.addEventListener('input', () => {
 qty.value = qty.value.replace(/[^0-9]/g,'').slice(0,3) || '1';
 });

 /* ---- Add to quote (visual feedback for now) ---- */
 document.getElementById('addToQuote').addEventListener('click', function(){
 const original = this.innerHTML;
 this.innerHTML = '✓ Added to Quote';
 this.style.background = '#15803D';
 this.style.color = '#fff';
 setTimeout(() => {
 this.innerHTML = original;
 this.style.background = '';
 this.style.color = '';
 }, 1600);
 // Phase 2: POST to /api/quote-save.php
 });

 /* ---- Info tabs ---- */
 const tabs = document.querySelectorAll('.info-tab');
 tabs.forEach(tab => {
 tab.addEventListener('click', () => {
 tabs.forEach(t => t.classList.remove('active'));
 tab.classList.add('active');
 document.querySelectorAll('.tab-panel').forEach(p => p.classList.remove('active'));
 document.getElementById('tab-' + tab.dataset.tab).classList.add('active');
 });
 });
})();
JS;

require __DIR__ . '/includes/header.php';
?>

<!-- ===================== BREADCRUMBS ===================== -->
<div class="breadcrumb-strip">
 <div class="container">
 <div class="breadcrumbs">
 <a href="index.php">Home</a>
 <span class="sep">›</span>
 <a href="products.php">Products</a>
 <span class="sep">›</span>
 <a href="products.php?category=fencing">Fencing</a>
 <span class="sep">›</span>
 <span class="current" id="crumbProduct"><?= e($pName) ?></span>
 </div>
 </div>
</div>

<!-- ===================== PRODUCT HERO ===================== -->
<section class="product-hero">
 <div class="container">
 <div class="product-hero-grid">

 <!-- LEFT: GALLERY -->
 <div class="gallery">
 <div class="gallery-main" id="galleryMain"
 style="background-image:url('<?= e($mainImg) ?>')">
 <?php if ($badge): ?><span class="badge-pop"><?= e($badge) ?></span><?php endif; ?>
 </div>
 <div class="gallery-thumbs" id="galleryThumbs">
 <div class="gallery-thumb active" style="background-image:url('<?= e($mainImg) ?>')"></div>
 <?php if ($img2): ?><div class="gallery-thumb" style="background-image:url('<?= e($img2) ?>')"></div><?php endif; ?>
 <div class="gallery-thumb" style="background-image:url('<?= e(site_image('pd-gallery-2', 'https://images.unsplash.com/photo-1500382017468-9049fed747ef?auto=format&fit=crop&w=400&q=80')) ?>')"></div>
 <div class="gallery-thumb" style="background-image:url('<?= e(site_image('pd-gallery-3', 'https://images.unsplash.com/photo-1518709268805-4e9042af9f23?auto=format&fit=crop&w=400&q=80')) ?>')"></div>
 <div class="gallery-thumb" style="background-image:url('<?= e(site_image('pd-gallery-4', 'https://images.unsplash.com/photo-1533154683836-84ea7a0bc310?auto=format&fit=crop&w=400&q=80')) ?>')"></div>
 </div>
 </div>

 <!-- RIGHT: INFO -->
 <div class="product-info">
 <h1 id="productName"><?= e($pName) ?></h1>

 <div class="product-meta">
 <?php if ($rating): ?>
 <span class="stars">★★★★★</span>
 <span><?= $rating ?> (<?= $reviews ?> reviews)</span>
 <span class="divider"></span>
 <?php endif; ?>
 <span>SKU: <?= $sku ?></span>
 <span class="divider"></span>
 <span style="color:var(--green-text);font-weight:600;"><?= $onRequest ? 'Supplied on Request' : 'In Stock' ?></span>
 </div>

 <p class="tagline"><?= e($pShort) ?></p>

 <!-- Pricing -->
 <div class="price-block">
 <div class="price-row primary">
 <?php if ($onRequest): ?>
 <span class="label">Price</span>
 <span class="value">Supplied on request</span>
 <?php else: ?>
 <span class="label"><?= str_starts_with($pShort, 'Versatile') || str_contains($pShort, 'heights') ? 'From' : 'Price' ?></span>
 <span class="value"><?= usd($pPrice) ?></span>
 <?php endif; ?>
 </div>
 <div class="price-row">
 <span class="label">Per</span>
 <span class="value"><?= e(ucfirst($pUnit)) ?></span>
 </div>
 <?php foreach (array_slice($specs, 0, 4) as $sp): if (stripos($sp[0], 'height') === 0) continue; ?>
 <div class="price-row">
 <span class="label"><?= e($sp[0]) ?></span>
 <span class="value"><?= e($sp[1]) ?></span>
 </div>
 <?php endforeach; ?>
 <div class="price-note"><?= $onRequest ? 'Contact us with your sizes and quantities for a quotation.' : 'Prices are estimates and may vary based on order volume and location.' ?></div>
 </div>

 <?php $heightSpecs = array_values(array_filter($specs, fn($s) => stripos($s[0], 'Height') === 0)); ?>
 <?php if ($heightSpecs): ?>
 <!-- Height option (priced per height) -->
 <div class="option-group">
 <label for="heightOpts">Select Height (price per roll)</label>
 <div class="option-pills" id="heightOpts">
 <?php foreach ($heightSpecs as $i => $h): ?>
 <button class="option-pill <?= $i === 0 ? 'active' : '' ?>"><?= e(str_replace('Height ', '', $h[0])) ?> — <?= e($h[1]) ?></button>
 <?php endforeach; ?>
 </div>
 </div>
 <?php endif; ?>

 <!-- Qty + Add to quote -->
 <div class="qty-row">
 <div class="qty-input">
 <button type="button" id="qtyMinus">−</button>
 <input type="text" id="qtyValue" value="1" inputmode="numeric">
 <button type="button" id="qtyPlus">+</button>
 </div>
 <button class="btn btn-amber" id="addToQuote">
 Add to Quote
 <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M5 12h14M13 5l7 7-7 7"/></svg>
 </button>
 </div>

 <!-- Secondary actions -->
 <div class="actions-row">
 <a href="calculator.php" class="btn btn-navy">
 Full Quote Calculator
 </a>
 <a href="contact.php" class="btn btn-outline-navy">
 Request a Call
 </a>
 </div>

 <!-- Trust chips -->
 <div class="trust-chips">
 <div class="trust-chip"><span class="dot">✓</span> SABS Compliant</div>
 <div class="trust-chip"><span class="dot">✓</span> Weather Resistant</div>
 <div class="trust-chip"><span class="dot">✓</span> 10+ Year Lifespan</div>
 <div class="trust-chip"><span class="dot">✓</span> Bulk Order Discounts</div>
 </div>

 </div>
 </div>
 </div>
</section>

<!-- ===================== INFO TABS ===================== -->
<section class="info-tabs-section">
 <div class="container">
 <div class="info-tabs-head">
 <button class="info-tab active" data-tab="desc">Description</button>
 <button class="info-tab" data-tab="specs">Specifications</button>
 <button class="info-tab" data-tab="apps">Applications</button>
 </div>

 <!-- Description -->
 <div class="tab-panel active" id="tab-desc">
 <h3>About <?= e($pName) ?></h3>
 <?php foreach (preg_split('/\n\s*\n|(?<=\.)\s+(?=Also |Not a )/', $pLong) as $para): ?>
 <p><?= e(trim($para)) ?></p>
 <?php endforeach; ?>
 </div>

 <!-- Specs -->
 <div class="tab-panel" id="tab-specs">
 <h3>Technical Specifications</h3>
 <table class="specs-table">
 <?php foreach ($specs as $sp): ?>
 <tr><td><?= e($sp[0]) ?></td><td><?= e($sp[1]) ?></td></tr>
 <?php endforeach; ?>
 </table>
 </div>

 <!-- Applications -->
 <div class="tab-panel" id="tab-apps">
 <h3>Common Applications</h3>
 <ul class="apps-list">
 <li><span class="check">✓</span> Residential boundary fencing for homes and townhouses</li>
 <li><span class="check">✓</span> Farm perimeter fencing for livestock and crop protection</li>
 <li><span class="check">✓</span> Wildlife enclosures and game reserves</li>
 <li><span class="check">✓</span> Commercial property and warehouse security</li>
 <li><span class="check">✓</span> School and institutional perimeter fencing</li>
 <li><span class="check">✓</span> Temporary site enclosures for construction projects</li>
 <li><span class="check">✓</span> Garden and pet enclosures</li>
 </ul>
 </div>
 </div>
</section>

<!-- ===================== RELATED PRODUCTS ===================== -->
<section class="related">
 <div class="container">
 <div class="section-head-row reveal">
 <div class="section-head">
 <h2>You Might Also Need</h2>
 <p>Complete your fencing project with these complementary products.</p>
 </div>
 <a href="products.php" class="link-more">
 View All Products
 <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M5 12h14M13 5l7 7-7 7"/></svg>
 </a>
 </div>

 <div class="product-grid">
 <a class="product-card reveal" href="product-detail.php?slug=fence-posts">
 <div class="thumb" style="background-image:url('<?= e(site_image('prod-fence-posts', 'assets/img/products/round-pole-75mm.jpg')) ?>')"></div>
 <div class="body">
 <span class="cat">Fencing</span>
 <h3>Fence Posts</h3>
 <p>Wooden, steel and concrete posts in multiple heights.</p>
 <span class="more">View Details
 <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M5 12h14M13 5l7 7-7 7"/></svg>
 </span>
 </div>
 </a>

 <a class="product-card reveal reveal-d1" href="product-detail.php?slug=barbed-wire">
 <div class="thumb" style="background-image:url('<?= e(site_image('prod-barbed-wire', 'assets/img/products/barbed-wire.jpg')) ?>')"></div>
 <div class="body">
 <span class="cat">Fencing</span>
 <h3>Barbed Wire</h3>
 <p>High-tensile barbed wire for perimeter security.</p>
 <span class="more">View Details
 <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M5 12h14M13 5l7 7-7 7"/></svg>
 </span>
 </div>
 </a>

 <a class="product-card reveal reveal-d2" href="product-detail.php?slug=razor-wire">
 <div class="thumb" style="background-image:url('<?= e(site_image('prod-razor-wire', 'https://images.unsplash.com/photo-1759614539716-01f836befe88?auto=format&fit=crop&w=800&q=80')) ?>')"></div>
 <div class="body">
 <span class="cat">Fencing</span>
 <h3>Razor Wire</h3>
 <p>Enhanced security for high-risk installations.</p>
 <span class="more">View Details
 <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M5 12h14M13 5l7 7-7 7"/></svg>
 </span>
 </div>
 </a>

 <a class="product-card reveal reveal-d3" href="product-detail.php?slug=game-fence">
 <div class="thumb" style="background-image:url('<?= e(site_image('prod-game-fence', 'https://images.unsplash.com/photo-1702641397914-30fbd18c0d93?auto=format&fit=crop&w=600&q=80')) ?>')"></div>
 <div class="body">
 <span class="cat">Fencing</span>
 <h3>Game Fence</h3>
 <p>Heavy-duty fencing for wildlife and large properties.</p>
 <span class="more">View Details
 <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M5 12h14M13 5l7 7-7 7"/></svg>
 </span>
 </div>
 </a>
 </div>
 </div>
</section>

<!-- ===================== CTA BAND ===================== -->
<section class="cta-wrap">
 <div class="cta-band reveal">
 <h2>Ready to Order?</h2>
 <p>Get an instant quote with quantities and pricing, or talk to our team for bulk pricing and installation.</p>
 <div class="btns">
 <a href="calculator.php" class="btn btn-amber">
 Get Instant Quote
 <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M5 12h14M13 5l7 7-7 7"/></svg>
 </a>
 <a href="contact.php" class="btn btn-ghost">Talk to Our Team</a>
 </div>
 </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
