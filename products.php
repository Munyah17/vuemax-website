<?php
require_once __DIR__ . '/includes/config.php';

$pageTitle = 'Products Vuemax | Fencing, Steel & Hardware Solutions';
$pageDesc = 'Browse Vuemax\'s full range of fencing, steel and general hardware products. Quality materials, nationwide delivery across Zimbabwe.';
$active = '';

/* ---------- Catalogue: DB-driven with static fallback ----------
   Cards render identically either way; the fallback list mirrors
   the seeded catalogue so the page works with no DB connection. */
$CAT_LABELS = ['fencing' => 'Fencing', 'steel' => 'Steel', 'hardware' => 'Hardware'];
$catNames = ['fencing' => 'Fencing Solutions', 'steel' => 'Steel Products', 'hardware' => 'General Hardware'];

$items = [
 ['slug'=>'diamond-mesh','name'=>'Diamond Mesh 50x50 (2mm)','cat'=>'fencing','sub'=>'diamond-mesh','price'=>65,'unit'=>'30m roll','desc'=>'50x50mm aperture, 2mm wire. 30m rolls, heights 1.0m to 3.0m.','img'=>'assets/img/products/diamond-mesh-2.jpg','badge'=>'Best Seller','featured'=>1,'reviews'=>120,'id'=>1],
 ['slug'=>'diamond-mesh-50x50-2-5mm','name'=>'Diamond Mesh 50x50 (2.5mm)','cat'=>'fencing','sub'=>'diamond-mesh','price'=>85,'unit'=>'30m roll','desc'=>'50x50mm aperture, 2.5mm wire. 30m rolls, heights 1.0m to 3.0m.','img'=>'assets/img/products/diamond-mesh-2.jpg','badge'=>null,'featured'=>0,'reviews'=>110,'id'=>2],
 ['slug'=>'diamond-mesh-50x50-3-15mm','name'=>'Diamond Mesh 50x50 (3.15mm)','cat'=>'fencing','sub'=>'diamond-mesh','price'=>150,'unit'=>'30m roll','desc'=>'50x50mm aperture, heavy 3.15mm wire. 30m rolls, heights 1.0m to 3.0m.','img'=>'assets/img/products/diamond-mesh-2.jpg','badge'=>null,'featured'=>0,'reviews'=>95,'id'=>3],
 ['slug'=>'diamond-mesh-30x30-2-5mm','name'=>'Diamond Mesh 30x30 (2.5mm)','cat'=>'fencing','sub'=>'diamond-mesh','price'=>110,'unit'=>'30m roll','desc'=>'Tighter 30x30mm aperture, 2.5mm wire. 30m rolls, heights 1.0m to 3.0m.','img'=>'assets/img/products/diamond-mesh.jpg','badge'=>null,'featured'=>0,'reviews'=>60,'id'=>4],
 ['slug'=>'diamond-mesh-70x70-2mm','name'=>'Diamond Mesh 70x70 (2mm)','cat'=>'fencing','sub'=>'diamond-mesh','price'=>60,'unit'=>'30m roll','desc'=>'Wider 70x70mm aperture, 2mm wire. 30m rolls, heights 1.0m to 3.0m.','img'=>'assets/img/products/diamond-mesh.jpg','badge'=>null,'featured'=>0,'reviews'=>0,'id'=>25],
 ['slug'=>'diamond-mesh-70x70-2-5mm','name'=>'Diamond Mesh 70x70 (2.5mm)','cat'=>'fencing','sub'=>'diamond-mesh','price'=>55,'unit'=>'30m roll','desc'=>'Wider 70x70mm aperture, 2.5mm wire. 30m rolls, heights 1.0m to 3.0m.','img'=>'assets/img/products/diamond-mesh.jpg','badge'=>null,'featured'=>0,'reviews'=>0,'id'=>21],
 ['slug'=>'diamond-mesh-70x70-3-15mm','name'=>'Diamond Mesh 70x70 (3.15mm)','cat'=>'fencing','sub'=>'diamond-mesh','price'=>124,'unit'=>'30m roll','desc'=>'Wider 70x70mm aperture, heavy 3.15mm wire. 30m rolls, heights 1.0m to 3.0m.','img'=>'assets/img/products/diamond-mesh.jpg','badge'=>null,'featured'=>0,'reviews'=>0,'id'=>22],
 ['slug'=>'diamond-mesh-80x80-2mm','name'=>'Diamond Mesh 80x80 (2mm)','cat'=>'fencing','sub'=>'diamond-mesh','price'=>40,'unit'=>'30m roll','desc'=>'Wide 80x80mm aperture, 2mm wire — most economical. 30m rolls, heights 1.0m to 3.0m.','img'=>'assets/img/products/diamond-mesh.jpg','badge'=>null,'featured'=>0,'reviews'=>0,'id'=>26],
 ['slug'=>'diamond-mesh-80x80-2-5mm','name'=>'Diamond Mesh 80x80 (2.5mm)','cat'=>'fencing','sub'=>'diamond-mesh','price'=>55,'unit'=>'30m roll','desc'=>'Wide 80x80mm aperture, 2.5mm wire — most economical. 30m rolls, heights 1.0m to 3.0m.','img'=>'assets/img/products/diamond-mesh.jpg','badge'=>null,'featured'=>0,'reviews'=>0,'id'=>23],
 ['slug'=>'diamond-mesh-80x80-3-15mm','name'=>'Diamond Mesh 80x80 (3.15mm)','cat'=>'fencing','sub'=>'diamond-mesh','price'=>100,'unit'=>'30m roll','desc'=>'Wide 80x80mm aperture, heavy 3.15mm wire. 30m rolls, heights 1.0m to 3.0m.','img'=>'assets/img/products/diamond-mesh.jpg','badge'=>null,'featured'=>0,'reviews'=>0,'id'=>24],
 ['slug'=>'game-fence','name'=>'Game Fence','cat'=>'fencing','sub'=>'game-fence','price'=>280,'unit'=>'roll','desc'=>'Heavy-duty fencing for wildlife, farms and large properties. Built for strength.','img'=>'assets/img/products/game-fence.jpg','badge'=>null,'featured'=>1,'reviews'=>86,'id'=>5],
 ['slug'=>'barbed-wire','name'=>'Barbed Wire 25 kg','cat'=>'fencing','sub'=>'barbed-wire','price'=>38,'unit'=>'roll','desc'=>'High-tensile barbed wire for perimeter security and farm protection. 25 kg roll, also available in 50 kg.','img'=>'assets/img/products/barbed-wire.jpg','badge'=>null,'featured'=>1,'reviews'=>74,'id'=>6],
 ['slug'=>'barbed-wire-50kg','name'=>'Barbed Wire 50 kg','cat'=>'fencing','sub'=>'barbed-wire','price'=>75,'unit'=>'roll','desc'=>'High-tensile barbed wire for perimeter security and farm protection. 50 kg roll, also available in 25 kg.','img'=>'assets/img/products/barbed-wire-50kg.jpg','badge'=>null,'featured'=>0,'reviews'=>60,'id'=>7],
 ['slug'=>'chicken-mesh','name'=>'Chicken Mesh','cat'=>'fencing','sub'=>'chicken-mesh','price'=>32,'unit'=>'roll','desc'=>'Lightweight galvanised mesh for poultry runs and small animal enclosures.','img'=>'https://images.unsplash.com/photo-1767416171650-4bff1da861fe?auto=format&fit=crop&w=600&q=80','badge'=>null,'featured'=>1,'reviews'=>62,'id'=>8],
 ['slug'=>'field-fence','name'=>'Field Fence','cat'=>'fencing','sub'=>'field-fence','price'=>180,'unit'=>'roll','desc'=>'General agricultural fencing for livestock and crop protection.','img'=>'https://images.unsplash.com/photo-1566780856910-f0cc7a8fb0c1?auto=format&fit=crop&w=600&q=80','badge'=>null,'featured'=>1,'reviews'=>51,'id'=>9],
 ['slug'=>'welded-mesh','name'=>'Welded Mesh','cat'=>'fencing','sub'=>'welded-mesh','price'=>null,'unit'=>'roll','desc'=>'Galvanised welded mesh with evenly welded intersections — for fencing, security, enclosures and fabrication.','img'=>'assets/img/products/welded-mesh.jpg','badge'=>null,'featured'=>1,'reviews'=>58,'id'=>10],
 ['slug'=>'razor-wire','name'=>'Razor Wire','cat'=>'fencing','sub'=>'razor-wire','price'=>95,'unit'=>'roll','desc'=>'Enhanced perimeter security for high-security installations.','img'=>'https://images.unsplash.com/photo-1759614539716-01f836befe88?auto=format&fit=crop&w=800&q=80','badge'=>null,'featured'=>0,'reviews'=>40,'id'=>11],
 ['slug'=>'fence-posts','name'=>'Fence Posts','cat'=>'fencing','sub'=>'fence-posts','price'=>8,'unit'=>'piece','desc'=>'Wooden, steel and concrete posts available in multiple heights.','img'=>'assets/img/products/round-pole-75mm.jpg','badge'=>null,'featured'=>0,'reviews'=>38,'id'=>12],
 ['slug'=>'checkered-plate-3mm','name'=>'Checkered Plate Galvanised 3mm','cat'=>'steel','sub'=>'steel-sheets','price'=>122,'unit'=>'sheet (2.4m x 1.2m)','desc'=>'Durable galvanised steel plate with raised checkered pattern for enhanced grip and slip resistance.','img'=>'assets/img/products/checkered-plate-3mm.jpg','badge'=>null,'featured'=>1,'reviews'=>44,'id'=>13],
 ['slug'=>'galvanised-round-pole-75mm','name'=>'Galvanised Round Pole 75mm','cat'=>'steel','sub'=>'steel-tubing','price'=>36,'unit'=>'length (6m)','desc'=>'Heavy-duty galvanised steel round pole for fencing, structural supports and agricultural applications.','img'=>'assets/img/products/round-pole-75mm.jpg','badge'=>null,'featured'=>1,'reviews'=>41,'id'=>14],
 ['slug'=>'galvanised-round-pole-32mm','name'=>'Galvanised Round Pole 32mm','cat'=>'steel','sub'=>'steel-tubing','price'=>25,'unit'=>'length (6m)','desc'=>'Galvanised steel round pole, 32mm x 2mm x 6m. For fencing, supports and general fabrication.','img'=>'assets/img/products/round-pole-32mm.jpg','badge'=>null,'featured'=>0,'reviews'=>33,'id'=>15],
 ['slug'=>'galvanised-round-pole-38mm','name'=>'Galvanised Round Pole 38mm','cat'=>'steel','sub'=>'steel-tubing','price'=>28,'unit'=>'length (6m)','desc'=>'Galvanised steel round pole, 38mm x 2mm x 6m. For fencing, supports and general fabrication.','img'=>'assets/img/products/round-pole-38mm.jpg','badge'=>null,'featured'=>0,'reviews'=>31,'id'=>16],
 ['slug'=>'square-tubes','name'=>'Square Tubes','cat'=>'steel','sub'=>'steel-tubing','price'=>null,'unit'=>'length','desc'=>'Steel square tubes for structural work, fabrication, gates, frames and roofing supports. Various sizes.','img'=>'assets/img/products/square-tubes.jpg','badge'=>null,'featured'=>0,'reviews'=>27,'id'=>17],
 ['slug'=>'angle-irons','name'=>'Angle Irons','cat'=>'steel','sub'=>'structural','price'=>null,'unit'=>'length','desc'=>'Steel angle sections for structural support, frames, brackets and fabrication. Various sizes.','img'=>'assets/img/products/angle-irons.jpg','badge'=>null,'featured'=>0,'reviews'=>24,'id'=>18],
 ['slug'=>'deformed-bars','name'=>'Deformed Bars','cat'=>'steel','sub'=>'rebar','price'=>null,'unit'=>'length','desc'=>'High-strength ribbed reinforcement bars for concrete slabs, columns, beams and foundations.','img'=>'assets/img/products/deformed-bars.jpg','badge'=>null,'featured'=>0,'reviews'=>29,'id'=>19],
 ['slug'=>'gate-locks','name'=>'Gate Locks & Hinges','cat'=>'hardware','sub'=>'gate-hardware','price'=>18,'unit'=>'set','desc'=>'Heavy-duty locks, hinges and latches for gates and security doors.','img'=>'https://images.unsplash.com/photo-1554863885-e3a33dd1bc82?auto=format&fit=crop&w=800&q=80','badge'=>null,'featured'=>1,'reviews'=>35,'id'=>20],
];

$subNames = [];
if ($pdo) {
 try {
 $rows = $pdo->query(
 "SELECT p.id, p.slug, p.name, p.short_desc, p.unit, p.price_usd, p.image,
 p.badge, p.is_featured, p.reviews_count,
 c.slug AS cat_slug, c.name AS cat_name, sc.slug AS sub_slug, sc.name AS sub_name
 FROM products p
 JOIN subcategories sc ON sc.id = p.subcategory_id
 JOIN categories c ON c.id = sc.category_id
 WHERE p.is_active = 1 AND sc.is_active = 1 AND c.is_active = 1
 ORDER BY p.is_featured DESC, p.reviews_count DESC, p.sort_order ASC"
 )->fetchAll();
 if ($rows) {
 $items = array_map(function ($r) {
 return [
 'slug' => $r['slug'], 'name' => $r['name'],
 'cat' => $r['cat_slug'], 'sub' => $r['sub_slug'],
 'price' => $r['price_usd'] !== null ? (float)$r['price_usd'] : null,
 'unit' => $r['unit'] ?: 'item', 'desc' => $r['short_desc'],
 'img' => $r['image'], 'badge' => $r['badge'],
 'featured' => (int)$r['is_featured'], 'reviews' => (int)$r['reviews_count'],
 'id' => (int)$r['id'],
 ];
 }, $rows);
 }
 // real category + subcategory names for labels
 $cn = [];
 foreach ($pdo->query('SELECT slug, name FROM categories WHERE is_active = 1 ORDER BY sort_order') as $c) {
 $cn[$c['slug']] = $c['name'];
 }
 if ($cn) $catNames = $cn;
 foreach ($pdo->query('SELECT s.slug, s.name FROM subcategories s JOIN categories c ON c.id = s.category_id WHERE c.slug = \'fencing\' AND s.is_active = 1') as $s) {
 $subNames[$s['slug']] = $s['name'];
 }
 } catch (Throwable $e) { /* keep static fallback */ }
}

$totalCount = count($items);
$catCounts = array_count_values(array_column($items, 'cat'));
$subCounts = [];
foreach ($items as $it) {
 if ($it['cat'] === 'fencing' && $it['sub'] !== '') {
 $subCounts[$it['sub']] = ($subCounts[$it['sub']] ?? 0) + 1;
 if (!isset($subNames[$it['sub']])) $subNames[$it['sub']] = ucwords(str_replace('-', ' ', $it['sub']));
 }
}
arsort($subCounts);

$extraCss = <<<'CSS'
/* ---------- PAGE HERO (small) ---------- */
.page-hero{
 background:linear-gradient(90deg,rgba(10,29,51,.92) 0%,rgba(10,29,51,.75) 55%,rgba(10,29,51,.55) 100%),
 url('https://images.unsplash.com/photo-1621905251189-08b45d6a269e?auto=format&fit=crop&w=1920&q=80') center/cover no-repeat;
 color:var(--white);padding:56px 0;
}
.breadcrumbs{font-size:13px;color:rgba(255,255,255,.7);margin-bottom:14px;}
.breadcrumbs a:hover{color:var(--amber);}
.breadcrumbs .sep{margin:0 8px;opacity:.5;}
.page-hero h1{
 font-family:'Playfair Display',serif;
 font-size:clamp(28px,3.4vw,42px);
 font-weight:700;line-height:1.15;margin-bottom:12px;
 letter-spacing:-.01em;
}
.page-hero h1 .accent{color:var(--amber);}
.page-hero p{color:rgba(255,255,255,.85);font-size:16px;max-width:620px;line-height:1.6;}

/* ---------- CATEGORY TABS STRIP ---------- */
.cat-tabs{background:var(--white);border-bottom:1px solid var(--border);}
.cat-tabs-inner{display:flex;gap:10px;padding:16px 0;overflow-x:auto;
 scrollbar-width:none;}
.cat-tabs-inner::-webkit-scrollbar{display:none;}
.cat-tab{
 display:inline-flex;align-items:center;gap:8px;
 padding:10px 18px;border-radius:var(--radius-pill);
 font-size:14px;font-weight:500;color:var(--text);
 border:1px solid var(--border);background:var(--white);
 white-space:nowrap;transition:.15s;cursor:pointer;
}
.cat-tab:hover{border-color:var(--navy);color:var(--navy);}
.cat-tab.active{background:var(--navy);color:var(--white);border-color:var(--navy);font-weight:600;}
.cat-tab .count{
 font-size:11px;font-weight:600;padding:2px 8px;border-radius:999px;
 background:var(--bg);color:var(--muted);margin-left:2px;
}
.cat-tab.active .count{background:rgba(245,183,49,.22);color:var(--amber);}

/* ---------- MAIN LAYOUT: sidebar + content ---------- */
.products-layout{
 display:grid;grid-template-columns:1fr;gap:36px;
 padding:44px 0 72px;
}
.sidebar{position:sticky;top:96px;align-self:start;}
.filter-block{
 background:var(--white);border:1px solid var(--border);
 border-radius:var(--radius-card);padding:20px;margin-bottom:18px;
}
.filter-block h4{
 font-size:13px;font-weight:700;color:var(--navy);
 letter-spacing:.08em;text-transform:uppercase;
 padding-bottom:12px;margin-bottom:14px;
 border-bottom:1px solid var(--border);
}
.filter-list{list-style:none;}
.filter-list li{margin-bottom:4px;}
.filter-list a{
 display:flex;justify-content:space-between;align-items:center;
 padding:8px 10px;border-radius:6px;font-size:14px;color:var(--text);
 transition:.15s;
}
.filter-list a:hover{background:var(--bg);color:var(--navy);}
.filter-list a.active{background:var(--navy);color:var(--white);font-weight:600;}
.filter-list a.active .count{color:var(--amber);}
.filter-list .count{font-size:12px;color:var(--muted);font-weight:500;}

/* ---------- Content header: result count + sort ---------- */
.content-head{
 display:flex;justify-content:space-between;align-items:center;
 gap:16px;margin-bottom:22px;flex-wrap:wrap;
}
.result-count{font-size:14px;color:var(--muted);}
.result-count strong{color:var(--navy);font-weight:600;}
.sort-wrap{display:flex;align-items:center;gap:10px;}
.sort-wrap label{font-size:13px;color:var(--muted);}
.sort-select{
 padding:8px 34px 8px 12px;border:1px solid var(--border);
 border-radius:var(--radius-input);background:var(--white)
 url("data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%236B7280' stroke-width='2.5'><polyline points='6 9 12 15 18 9'/></svg>")
 no-repeat right 12px center;
 appearance:none;-webkit-appearance:none;
 font-size:13.5px;font-weight:500;color:var(--navy);
 cursor:pointer;transition:.15s;min-width:170px;
}
.sort-select:focus{outline:none;border-color:var(--navy);}

/* Listing grid: 1 → 2 → 3 columns (inside sidebar layout) */
.products-layout .product-grid{grid-template-columns:1fr;}

/* Card tag badge (listing only) */
.product-card .thumb{position:relative;}
.product-card .tag{
 position:absolute;top:12px;left:12px;
 background:var(--amber);color:var(--navy);
 font-size:10.5px;font-weight:700;letter-spacing:.08em;
 text-transform:uppercase;padding:5px 9px;border-radius:5px;
}

/* ---------- PAGINATION ---------- */
.pagination{
 display:flex;justify-content:center;gap:6px;
 margin-top:44px;
}
.pagination a{
 min-width:38px;height:38px;padding:0 12px;
 display:inline-flex;align-items:center;justify-content:center;
 border:1px solid var(--border);background:var(--white);
 border-radius:var(--radius-input);
 font-size:14px;font-weight:500;color:var(--text);
 transition:.15s;
}
.pagination a:hover{border-color:var(--navy);color:var(--navy);}
.pagination a.active{background:var(--navy);color:var(--white);border-color:var(--navy);font-weight:600;}

/* ---------- HELP STRIP ---------- */
.help-strip{
 background:var(--white);border-top:1px solid var(--border);
 border-bottom:1px solid var(--border);
 padding:32px 0;margin-top:16px;
}
.help-grid{display:grid;grid-template-columns:1fr;gap:24px;}
.help-item{display:flex;align-items:center;gap:12px;}
.help-icon{
 width:44px;height:44px;border-radius:10px;
 background:var(--amber-soft);color:var(--navy);flex-shrink:0;
 display:flex;align-items:center;justify-content:center;
}
.help-item strong{display:block;font-size:13.5px;font-weight:600;color:var(--navy);}
.help-item span{display:block;font-size:12px;color:var(--muted);}

/* ---------- Responsive ---------- */
@media (min-width:640px){
 .products-layout .product-grid{grid-template-columns:repeat(2,1fr);}
 .help-grid{grid-template-columns:repeat(2,1fr);}
}
@media (min-width:1024px){
 .products-layout{grid-template-columns:260px 1fr;}
 .products-layout .product-grid{grid-template-columns:repeat(3,1fr);}
}
@media (max-width:1023px){
 .sidebar{position:static;}
}
@media (min-width:900px){
 .help-grid{grid-template-columns:repeat(4,1fr);}
}
CSS;

$extraJs = <<<'JS'
/* ============================================================
 CATEGORY / SUBCATEGORY FILTERING + SORTING
 ============================================================ */
(function(){
 const tabs = document.querySelectorAll('#catTabs .cat-tab');
 const sideLinks = document.querySelectorAll('#sidebarCats a');
 const subLinks = document.querySelectorAll('#sidebarSubs a');
 const grid = document.getElementById('productGrid');
 const cards = [...grid.querySelectorAll('.product-card')];
 const visCount = document.getElementById('visCount');
 const sortSel = document.getElementById('sortBy');

 let curCat = 'all';
 let curSub = '';

 function applyFilter(){
 let vis = 0;
 cards.forEach(card => {
 const ok = (curCat === 'all' || card.dataset.cat === curCat)
 && (curSub === '' || card.dataset.sub === curSub);
 card.style.display = ok ? '' : 'none';
 if (ok) vis++;
 });
 if (visCount) visCount.textContent = vis;

 tabs.forEach(t => t.classList.toggle('active', t.dataset.cat === curCat));
 sideLinks.forEach(a => a.classList.toggle('active', a.dataset.cat === curCat));
 subLinks.forEach(a => a.classList.toggle('active', a.dataset.sub === curSub && curSub !== ''));
 }

 function setCat(cat){ curCat = cat; curSub = ''; applyFilter(); }
 function setSub(sub){ curSub = sub; if (sub) curCat = 'all'; applyFilter(); }

 tabs.forEach(t => t.addEventListener('click', () => setCat(t.dataset.cat)));
 sideLinks.forEach(a => a.addEventListener('click', e => { e.preventDefault(); setCat(a.dataset.cat); }));
 subLinks.forEach(a => a.addEventListener('click', e => { e.preventDefault(); setSub(a.dataset.sub === curSub ? '' : a.dataset.sub); }));

 /* --- Sort --- */
 if (sortSel) sortSel.addEventListener('change', () => {
 const mode = sortSel.value;
 const sorted = [...cards].sort((a, b) => {
 const pa = parseFloat(a.dataset.price), pb = parseFloat(b.dataset.price);
 switch (mode){
 case 'price-asc': return (isNaN(pa) ? 1e9 : pa) - (isNaN(pb) ? 1e9 : pb);
 case 'price-desc': return (isNaN(pb) ? -1 : pb) - (isNaN(pa) ? -1 : pa);
 case 'newest': return (b.dataset.id || 0) - (a.dataset.id || 0);
 case 'name': return a.dataset.name.localeCompare(b.dataset.name);
 default: // popular: featured first, then reviews
 return (b.dataset.featured - a.dataset.featured) || (b.dataset.reviews - a.dataset.reviews);
 }
 });
 sorted.forEach(c => grid.appendChild(c));
 });

 // URL params: ?category=x and/or ?sub=y
 const params = new URLSearchParams(window.location.search);
 if (params.get('sub')) { curSub = params.get('sub'); curCat = 'all'; }
 else if (params.get('category')) { curCat = params.get('category'); }
 applyFilter();
})();
JS;

require __DIR__ . '/includes/header.php';
?>

<!-- ===================== PAGE HERO ===================== -->
<section class="page-hero" style="--hero-img:url('<?= e(site_image('products-hero', 'https://images.unsplash.com/photo-1621905251189-08b45d6a269e?auto=format&fit=crop&w=1920&q=80')) ?>')">
 <div class="container">
 <div class="breadcrumbs">
 <a href="index.php">Home</a>
 <span class="sep">›</span>
 <span>Products</span>
 </div>
 <h1>Our <span class="accent">Products</span></h1>
 <p>High-quality fencing, steel and hardware solutions for every need. Quality materials, expert advice and nationwide delivery across Zimbabwe.</p>
 </div>
</section>

<!-- ===================== CATEGORY TABS ===================== -->
<div class="cat-tabs">
 <div class="container">
 <div class="cat-tabs-inner" id="catTabs">
 <button class="cat-tab active" data-cat="all">
 All Products <span class="count"><?= $totalCount ?></span>
 </button>
 <?php foreach ($catNames as $cslug => $cname): if (!isset($catCounts[$cslug])) continue; ?>
 <button class="cat-tab" data-cat="<?= e($cslug) ?>">
 <?= e($cname) ?> <span class="count"><?= $catCounts[$cslug] ?></span>
 </button>
 <?php endforeach; ?>
 </div>
 </div>
</div>

<!-- ===================== MAIN LAYOUT ===================== -->
<div class="container">
 <div class="products-layout">

 <!-- SIDEBAR -->
 <aside class="sidebar">
 <div class="filter-block">
 <h4>Categories</h4>
 <ul class="filter-list" id="sidebarCats">
 <li><a href="#" data-cat="all" class="active">All Products <span class="count"><?= $totalCount ?></span></a></li>
 <?php foreach ($catNames as $cslug => $cname): if (!isset($catCounts[$cslug])) continue; ?>
 <li><a href="#" data-cat="<?= e($cslug) ?>"><?= e($cname) ?> <span class="count"><?= $catCounts[$cslug] ?></span></a></li>
 <?php endforeach; ?>
 </ul>
 </div>

 <?php if ($subCounts): ?>
 <div class="filter-block">
 <h4>Fencing Types</h4>
 <ul class="filter-list" id="sidebarSubs">
 <?php foreach ($subCounts as $sslug => $n): ?>
 <li><a href="#" data-sub="<?= e($sslug) ?>"><?= e($subNames[$sslug] ?? ucwords(str_replace('-', ' ', $sslug))) ?> <span class="count"><?= $n ?></span></a></li>
 <?php endforeach; ?>
 </ul>
 </div>
 <?php endif; ?>

 <div class="filter-block">
 <h4>Need Help?</h4>
 <p style="font-size:13.5px;color:var(--muted);margin-bottom:14px;">
 Not sure which product fits your project? Talk to our team.
 </p>
 <a href="contact.php" class="btn btn-outline-navy" style="width:100%;justify-content:center;">
 Contact Us
 </a>
 </div>
 </aside>

 <!-- CONTENT -->
 <main>
 <div class="content-head">
 <div class="result-count">
 Showing <strong id="visCount"><?= $totalCount ?></strong> of <strong><?= $totalCount ?></strong> products
 </div>
 <div class="sort-wrap">
 <label for="sortBy">Sort by:</label>
 <select class="sort-select" id="sortBy">
 <option value="popular">Most Popular</option>
 <option value="price-asc">Price: Low to High</option>
 <option value="price-desc">Price: High to Low</option>
 <option value="newest">Newest First</option>
 <option value="name">Name: A Z</option>
 </select>
 </div>
 </div>

 <div class="product-grid" id="productGrid">
 <?php foreach ($items as $it): ?>
 <div class="product-card"
 data-cat="<?= e($it['cat']) ?>" data-sub="<?= e($it['sub']) ?>"
 data-price="<?= $it['price'] === null ? '' : e($it['price']) ?>"
 data-name="<?= e(strtolower($it['name'])) ?>"
 data-featured="<?= (int)$it['featured'] ?>" data-reviews="<?= (int)$it['reviews'] ?>"
 data-id="<?= (int)$it['id'] ?>">
 <a class="thumb" href="product-detail.php?slug=<?= e($it['slug']) ?>" style="background-image:url('<?= e(site_image('prod-' . $it['slug'], $it['img'] ?: 'assets/img/products/diamond-mesh.jpg')) ?>')" aria-label="<?= e($it['name']) ?>">
 <?php if ($it['badge']): ?><span class="tag"><?= e($it['badge']) ?></span><?php endif; ?>
 </a>
 <div class="body">
 <span class="cat"><?= e($CAT_LABELS[$it['cat']] ?? ucfirst($it['cat'])) ?></span>
 <h3><a href="product-detail.php?slug=<?= e($it['slug']) ?>"><?= e($it['name']) ?></a></h3>
 <p><?= e($it['desc']) ?></p>
 <div class="price"><?php if ($it['price'] === null): ?>Supplied on request<?php else: ?>From $<?= e(rtrim(rtrim(number_format($it['price'], 2), '0'), '.')) ?> <span>/ <?= e($it['unit']) ?></span><?php endif; ?></div>
 <div class="card-actions">
 <button type="button" class="btn btn-navy btn-sm js-buy"
 data-name="<?= e($it['name']) ?>" data-slug="<?= e($it['slug']) ?>"
 data-unit="<?= e($it['unit']) ?>" data-price="<?= $it['price'] === null ? '' : e($it['price']) ?>">Buy Now</button>
 <button type="button" class="btn btn-outline-navy btn-sm js-quote"
 data-name="<?= e($it['name']) ?>" data-slug="<?= e($it['slug']) ?>"
 data-unit="<?= e($it['unit']) ?>" data-price="<?= $it['price'] === null ? '' : e($it['price']) ?>">Get Quote</button>
 </div>
 </div>
 </div>
 <?php endforeach; ?>
 </div>
 </main>

 </div>
</div>

<!-- ===================== HELP STRIP ===================== -->
<section class="help-strip">
 <div class="container">
 <div class="help-grid">
 <div class="help-item reveal">
 <div class="help-icon">🚚</div>
 <div><strong>Nationwide Delivery</strong><span>Across Zimbabwe</span></div>
 </div>
 <div class="help-item reveal reveal-d1">
 <div class="help-icon">✅</div>
 <div><strong>Quality Assured</strong><span>SABS Compliant</span></div>
 </div>
 <div class="help-item reveal reveal-d2">
 <div class="help-icon">👥</div>
 <div><strong>Expert Support</strong><span>From Quote to Installation</span></div>
 </div>
 <div class="help-item reveal reveal-d3">
 <div class="help-icon">⚡</div>
 <div><strong>Fast Quotations</strong><span>Results in Seconds</span></div>
 </div>
 </div>
 </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
