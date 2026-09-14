<?php
require_once __DIR__ . '/includes/config.php';

$pageTitle = 'Products Vuemax | Fencing, Steel & Hardware Solutions';
$pageDesc = 'Browse Vuemax\'s full range of fencing, steel and general hardware products. Quality materials, nationwide delivery across Zimbabwe.';
$active = '';

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
 CATEGORY TAB + SIDEBAR SWITCHING
 (visual only for now Phase 2 will swap to fetch('/api/products.php'))
 ============================================================ */
(function(){
 const tabs = document.querySelectorAll('#catTabs .cat-tab');
 const sideLinks = document.querySelectorAll('#sidebarCats a');
 const cards = document.querySelectorAll('#productGrid .product-card');

 function setActive(cat){
 // Tabs
 tabs.forEach(t => t.classList.toggle('active', t.dataset.cat === cat));
 // Sidebar
 sideLinks.forEach(a => a.classList.toggle('active', a.dataset.cat === cat));

 // Filter product cards by data-cat (fallback: show all if no match)
 cards.forEach(card => {
 const cardCat = (card.querySelector('.cat')?.textContent || '').toLowerCase();
 if (cat === 'all') {
 card.style.display = '';
 } else {
 const show =
 (cat === 'fencing' && cardCat === 'fencing') ||
 (cat === 'steel' && cardCat === 'steel') ||
 (cat === 'hardware' && cardCat === 'hardware');
 card.style.display = show ? '' : 'none';
 }
 });
 }

 tabs.forEach(t => t.addEventListener('click', () => setActive(t.dataset.cat)));
 sideLinks.forEach(a => a.addEventListener('click', (e) => {
 e.preventDefault();
 setActive(a.dataset.cat);
 }));

 // If URL has ?category=xxx, apply that filter on load
 const params = new URLSearchParams(window.location.search);
 const initial = params.get('category');
 if (initial && ['fencing','steel','hardware'].includes(initial)) {
 setActive(initial);
 }
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
 All Products <span class="count">30</span>
 </button>
 <button class="cat-tab" data-cat="fencing">
 Fencing Solutions <span class="count">12</span>
 </button>
 <button class="cat-tab" data-cat="steel">
 Steel Products <span class="count">12</span>
 </button>
 <button class="cat-tab" data-cat="hardware">
 General Hardware <span class="count">6</span>
 </button>
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
 <li><a href="#" data-cat="all" class="active">All Products <span class="count">30</span></a></li>
 <li><a href="#" data-cat="fencing">Fencing Solutions <span class="count">12</span></a></li>
 <li><a href="#" data-cat="steel">Steel Products <span class="count">12</span></a></li>
 <li><a href="#" data-cat="hardware">General Hardware <span class="count">6</span></a></li>
 </ul>
 </div>

 <div class="filter-block">
 <h4>Fencing Types</h4>
 <ul class="filter-list">
 <li><a href="#">Diamond Mesh <span class="count">3</span></a></li>
 <li><a href="#">Game Fence <span class="count">2</span></a></li>
 <li><a href="#">Barbed Wire <span class="count">2</span></a></li>
 <li><a href="#">Chicken Mesh <span class="count">2</span></a></li>
 <li><a href="#">Field Fence <span class="count">1</span></a></li>
 <li><a href="#">Razor Wire <span class="count">1</span></a></li>
 <li><a href="#">Fence Posts <span class="count">1</span></a></li>
 </ul>
 </div>

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
 Showing <strong>1 9</strong> of <strong>24</strong> products
 </div>
 <div class="sort-wrap">
 <label for="sortBy">Sort by:</label>
 <select class="sort-select" id="sortBy">
 <option>Most Popular</option>
 <option>Price: Low to High</option>
 <option>Price: High to Low</option>
 <option>Newest First</option>
 <option>Name: A Z</option>
 </select>
 </div>
 </div>

 <div class="product-grid" id="productGrid">

 <!-- 1 -->
 <a class="product-card" href="product-detail.php?slug=diamond-mesh">
 <div class="thumb" style="background-image:url('<?= e(site_image('prod-diamond-mesh', 'https://images.unsplash.com/photo-1621905251189-08b45d6a269e?auto=format&fit=crop&w=600&q=80')) ?>')">
 <span class="tag">Best Seller</span>
 </div>
 <div class="body">
 <span class="cat">Fencing</span>
 <h3>Diamond Mesh</h3>
 <p>Versatile, durable and cost-effective fencing for homes, farms and businesses.</p>
 <div class="price">From $120 <span>/ roll</span></div>
 <span class="more">View Details
 <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M5 12h14M13 5l7 7-7 7"/></svg>
 </span>
 </div>
 </a>

 <!-- 2 -->
 <a class="product-card" href="product-detail.php?slug=game-fence">
 <div class="thumb" style="background-image:url('<?= e(site_image('prod-game-fence', 'https://images.unsplash.com/photo-1518709268805-4e9042af9f23?auto=format&fit=crop&w=600&q=80')) ?>')"></div>
 <div class="body">
 <span class="cat">Fencing</span>
 <h3>Game Fence</h3>
 <p>Heavy-duty fencing for wildlife, farms and large properties. Built for strength.</p>
 <div class="price">From $280 <span>/ roll</span></div>
 <span class="more">View Details
 <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M5 12h14M13 5l7 7-7 7"/></svg>
 </span>
 </div>
 </a>

 <!-- 3 -->
 <a class="product-card" href="product-detail.php?slug=barbed-wire">
 <div class="thumb" style="background-image:url('<?= e(site_image('prod-barbed-wire', 'https://images.unsplash.com/photo-1533154683836-84ea7a0bc310?auto=format&fit=crop&w=600&q=80')) ?>')"></div>
 <div class="body">
 <span class="cat">Fencing</span>
 <h3>Barbed Wire</h3>
 <p>High-tensile barbed wire for perimeter security and farm protection.</p>
 <div class="price">From $45 <span>/ roll</span></div>
 <span class="more">View Details
 <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M5 12h14M13 5l7 7-7 7"/></svg>
 </span>
 </div>
 </a>

 <!-- 4 -->
 <a class="product-card" href="product-detail.php?slug=chicken-mesh">
 <div class="thumb" style="background-image:url('<?= e(site_image('prod-chicken-mesh', 'https://images.unsplash.com/photo-1548550023-2bdb3c5beed7?auto=format&fit=crop&w=600&q=80')) ?>')"></div>
 <div class="body">
 <span class="cat">Fencing</span>
 <h3>Chicken Mesh</h3>
 <p>Lightweight galvanised mesh for poultry runs and small animal enclosures.</p>
 <div class="price">From $32 <span>/ roll</span></div>
 <span class="more">View Details
 <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M5 12h14M13 5l7 7-7 7"/></svg>
 </span>
 </div>
 </a>

 <!-- 5 -->
 <a class="product-card" href="product-detail.php?slug=field-fence">
 <div class="thumb" style="background-image:url('<?= e(site_image('prod-field-fence', 'https://images.unsplash.com/photo-1500595046743-cd271d694d30?auto=format&fit=crop&w=600&q=80')) ?>')"></div>
 <div class="body">
 <span class="cat">Fencing</span>
 <h3>Field Fence</h3>
 <p>General agricultural fencing for livestock and crop protection.</p>
 <div class="price">From $180 <span>/ roll</span></div>
 <span class="more">View Details
 <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M5 12h14M13 5l7 7-7 7"/></svg>
 </span>
 </div>
 </a>

 <!-- 6 -->
 <a class="product-card" href="product-detail.php?slug=razor-wire">
 <div class="thumb" style="background-image:url('<?= e(site_image('prod-razor-wire', 'https://images.unsplash.com/photo-1595278069441-2cf29f8005a4?auto=format&fit=crop&w=600&q=80')) ?>')"></div>
 <div class="body">
 <span class="cat">Fencing</span>
 <h3>Razor Wire</h3>
 <p>Enhanced perimeter security for high-security installations.</p>
 <div class="price">From $95 <span>/ roll</span></div>
 <span class="more">View Details
 <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M5 12h14M13 5l7 7-7 7"/></svg>
 </span>
 </div>
 </a>

 <!-- 7 -->
 <a class="product-card" href="product-detail.php?slug=fence-posts">
 <div class="thumb" style="background-image:url('<?= e(site_image('prod-fence-posts', 'https://images.unsplash.com/photo-1587293852726-70cdb56c2866?auto=format&fit=crop&w=600&q=80')) ?>')"></div>
 <div class="body">
 <span class="cat">Fencing</span>
 <h3>Fence Posts</h3>
 <p>Wooden, steel and concrete posts available in multiple heights.</p>
 <div class="price">From $8 <span>/ piece</span></div>
 <span class="more">View Details
 <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M5 12h14M13 5l7 7-7 7"/></svg>
 </span>
 </div>
 </a>

 <!-- 8 -->
 <a class="product-card" href="product-detail.php?slug=steel-tubing">
 <div class="thumb" style="background-image:url('<?= e(site_image('prod-steel-tubing', 'https://images.unsplash.com/photo-1565793298595-6a879b1d9492?auto=format&fit=crop&w=600&q=80')) ?>')"></div>
 <div class="body">
 <span class="cat">Steel</span>
 <h3>Steel Tubing</h3>
 <p>Square and rectangular steel tubing for fabrication, gates and frames.</p>
 <div class="price">From $12 <span>/ meter</span></div>
 <span class="more">View Details
 <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M5 12h14M13 5l7 7-7 7"/></svg>
 </span>
 </div>
 </a>

 <!-- 9a -->
 <a class="product-card" href="product-detail.php?slug=checkered-plate-3mm">
 <div class="thumb" style="background-image:url('<?= e(site_image('prod-checkered-plate', 'assets/img/products/checkered-plate-3mm.jpg')) ?>')"></div>
 <div class="body">
 <span class="cat">Steel</span>
 <h3>Checkered Plate Galvanised 3mm</h3>
 <p>Durable galvanised steel plate with raised checkered pattern for enhanced grip and slip resistance.</p>
 <div class="price">$122 <span>/ sheet (2.4m x 1.2m)</span></div>
 <span class="more">View Details
 <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M5 12h14M13 5l7 7-7 7"/></svg>
 </span>
 </div>
 </a>

 <!-- 9b -->
 <a class="product-card" href="product-detail.php?slug=galvanised-round-pole-75mm">
 <div class="thumb" style="background-image:url('<?= e(site_image('prod-round-pole-75', 'assets/img/products/round-pole-75mm.jpg')) ?>')"></div>
 <div class="body">
 <span class="cat">Steel</span>
 <h3>Galvanised Round Pole 75mm</h3>
 <p>Heavy-duty galvanised steel round pole for fencing, structural supports and agricultural applications.</p>
 <div class="price">$36 <span>/ length (6m)</span></div>
 <span class="more">View Details
 <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M5 12h14M13 5l7 7-7 7"/></svg>
 </span>
 </div>
 </a>

 <!-- 9c -->
 <a class="product-card" href="product-detail.php?slug=galvanised-round-pole-32mm">
 <div class="thumb" style="background-image:url('<?= e(site_image('prod-round-pole-32', 'assets/img/products/round-pole-32mm.jpg')) ?>')"></div>
 <div class="body">
 <span class="cat">Steel</span>
 <h3>Galvanised Round Pole 32mm</h3>
 <p>Galvanised steel round pole, 32mm x 2mm x 6m. For fencing, supports and general fabrication.</p>
 <div class="price">$25 <span>/ length (6m)</span></div>
 <span class="more">View Details
 <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M5 12h14M13 5l7 7-7 7"/></svg>
 </span>
 </div>
 </a>

 <!-- 9d -->
 <a class="product-card" href="product-detail.php?slug=galvanised-round-pole-38mm">
 <div class="thumb" style="background-image:url('<?= e(site_image('prod-round-pole-38', 'assets/img/products/round-pole-38mm.jpg')) ?>')"></div>
 <div class="body">
 <span class="cat">Steel</span>
 <h3>Galvanised Round Pole 38mm</h3>
 <p>Galvanised steel round pole, 38mm x 2mm x 6m. For fencing, supports and general fabrication.</p>
 <div class="price">$28 <span>/ length (6m)</span></div>
 <span class="more">View Details
 <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M5 12h14M13 5l7 7-7 7"/></svg>
 </span>
 </div>
 </a>

 <!-- 9e -->
 <a class="product-card" href="product-detail.php?slug=square-tubes">
 <div class="thumb" style="background-image:url('<?= e(site_image('prod-square-tubes', 'assets/img/products/square-tubes.jpg')) ?>')"></div>
 <div class="body">
 <span class="cat">Steel</span>
 <h3>Square Tubes</h3>
 <p>Steel square tubes for structural work, fabrication, gates, frames and roofing supports. Various sizes.</p>
 <div class="price">Price on <span>request</span></div>
 <span class="more">View Details
 <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M5 12h14M13 5l7 7-7 7"/></svg>
 </span>
 </div>
 </a>

 <!-- 9f -->
 <a class="product-card" href="product-detail.php?slug=angle-irons">
 <div class="thumb" style="background-image:url('<?= e(site_image('prod-angle-irons', 'assets/img/products/angle-irons.jpg')) ?>')"></div>
 <div class="body">
 <span class="cat">Steel</span>
 <h3>Angle Irons</h3>
 <p>Steel angle sections for structural support, frames, brackets and fabrication. Various sizes.</p>
 <div class="price">Price on <span>request</span></div>
 <span class="more">View Details
 <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M5 12h14M13 5l7 7-7 7"/></svg>
 </span>
 </div>
 </a>

 <!-- 9 -->
 <a class="product-card" href="product-detail.php?slug=gate-locks">
 <div class="thumb" style="background-image:url('<?= e(site_image('prod-gate-locks', 'https://images.unsplash.com/photo-1581147036324-c17ac41dfa6c?auto=format&fit=crop&w=600&q=80')) ?>')"></div>
 <div class="body">
 <span class="cat">Hardware</span>
 <h3>Gate Locks &amp; Hinges</h3>
 <p>Heavy-duty locks, hinges and latches for gates and security doors.</p>
 <div class="price">From $18 <span>/ set</span></div>
 <span class="more">View Details
 <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M5 12h14M13 5l7 7-7 7"/></svg>
 </span>
 </div>
 </a>

 </div>

 <!-- PAGINATION -->
 <div class="pagination">
 <a href="#" class="active">1</a>
 <a href="#">2</a>
 <a href="#">3</a>
 <a href="#">Next ›</a>
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
