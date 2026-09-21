<?php
require_once __DIR__ . '/includes/config.php';

$pageTitle = 'Vuemax Fencing, Steel & Hardware Solutions | Zimbabwe';
$pageDesc = 'Vuemax supplies quality fencing, steel and hardware products across Zimbabwe. Get an instant quote with our online calculator.';
$active = 'home';

/* ---------- Hero slides ----------
   Up to 5 banners managed in /admin → Banner Manager.
   Falls back to the default hero when the table is missing/empty. */
$heroSlides = [];
if ($pdo) {
    try {
        $heroSlides = $pdo->query(
            'SELECT eyebrow, title, accent, description, image
             FROM banners WHERE is_active = 1 ORDER BY sort_order, id LIMIT 5'
        )->fetchAll();
    } catch (Throwable $e) { $heroSlides = []; }
}
if (!$heroSlides) {
    $heroSlides = [[
        'eyebrow'     => "Zimbabwe's Trusted Partner",
        'title'       => 'Smarter Fencing Quotes.',
        'accent'      => 'Faster Decisions.',
        'description' => 'Accurate bills of quantities and cost estimates in minutes. Fencing, steel and hardware for homes, farms, businesses and security projects across Zimbabwe.',
        'image'       => site_image('home-hero', 'https://images.unsplash.com/photo-1500382017468-9049fed747ef?auto=format&fit=crop&w=1200&q=80'),
    ]];
}
$extraHead = '<link rel="preload" as="image" href="' . e($heroSlides[0]['image']) . '" fetchpriority="high">';

$extraCss = <<<'CSS'
/* ---------- HERO SLIDER (homepage) ---------- */
.hero{
 position:relative;
 color:var(--white);
 overflow:hidden;
}
.hero::after{
 content:"";position:absolute;left:0;right:0;bottom:0;height:80px;
 background:linear-gradient(180deg,transparent,rgba(10,29,51,.35));
 pointer-events:none;z-index:2;
}
.hero-slides{display:grid;}
.hero-slide{
 grid-area:1/1;
 padding:56px 0 48px;
 background:
 linear-gradient(155deg,rgba(10,29,51,.95) 0%,rgba(10,29,51,.8) 55%,rgba(14,39,69,.62) 100%),
 var(--hero-img) center/cover no-repeat;
 opacity:0;visibility:hidden;
 transition:opacity .7s var(--ease), visibility .7s;
 display:flex;align-items:center;
}
.hero-slide.active{opacity:1;visibility:visible;z-index:1;}
.hero-inner{
 position:relative;z-index:1;
 max-width:680px;margin:0 auto;
 text-align:center;
}
.hero .eyebrow{
 display:inline-block;
 font-size:11px;letter-spacing:.15em;text-transform:uppercase;
 color:var(--amber);font-weight:600;
 margin-bottom:14px;
 padding:6px 12px;border-radius:999px;
 background:rgba(245,183,49,.12);
 border:1px solid rgba(245,183,49,.32);
}
.hero h1{
 font-family:'Playfair Display',serif;
 font-size:clamp(30px,7vw,50px);
 line-height:1.08;font-weight:700;letter-spacing:-.01em;
 margin-bottom:16px;
}
.hero h1 .accent{color:var(--amber);display:block;}
.hero p{
 font-size:15px;line-height:1.65;
 color:rgba(255,255,255,.88);
 margin-bottom:26px;
 max-width:560px;margin-left:auto;margin-right:auto;
}
.hero-cta{display:flex;flex-direction:column;gap:10px;align-items:center;}
.hero-cta .btn{width:100%;}

/* Slider controls */
.hero-dots{
 position:absolute;left:0;right:0;bottom:22px;z-index:3;
 display:flex;justify-content:center;gap:8px;
}
.hero-dot{
 width:9px;height:9px;border-radius:999px;
 background:rgba(255,255,255,.35);border:none;cursor:pointer;
 transition:background .2s,width .25s var(--ease);padding:0;
}
.hero-dot.active{background:var(--amber);width:26px;}
.hero-arrow{
 position:absolute;top:50%;transform:translateY(-50%);z-index:3;
 width:42px;height:42px;border-radius:50%;
 background:rgba(255,255,255,.12);border:1px solid rgba(255,255,255,.25);
 color:var(--white);display:none;align-items:center;justify-content:center;
 cursor:pointer;transition:background .2s;
 -webkit-backdrop-filter:blur(4px);backdrop-filter:blur(4px);
}
.hero-arrow:hover{background:rgba(245,183,49,.85);color:var(--navy);border-color:transparent;}
.hero-arrow.prev{left:18px;}
.hero-arrow.next{right:18px;}
.hero-script{
 display:none;
 position:absolute;right:40px;bottom:52px;z-index:3;
 font-family:'Caveat',cursive;font-size:34px;
 color:var(--amber);opacity:.9;line-height:1;
 text-align:right;transform:rotate(-4deg);
 pointer-events:none;
}

@media (min-width:640px){
 .hero-slide{padding:70px 0 64px;}
 .hero-cta{flex-direction:row;flex-wrap:wrap;justify-content:center;}
 .hero-cta .btn{width:auto;}
}
@media (min-width:900px){
 .hero-script{display:block;}
 .hero-arrow{display:flex;}
}
@media (min-width:1024px){
 .hero-slide{padding:96px 0 88px;min-height:540px;}
 .hero h1{font-size:clamp(36px,4.4vw,56px);}
 .hero p{font-size:17px;margin-bottom:34px;}
}
@media (min-width:1280px){
 .hero-slide{padding:110px 0 96px;}
 .hero-script{right:60px;bottom:64px;font-size:40px;}
}
CSS;

$extraJs = <<<'JS'
/* ---------- Hero slider ---------- */
(function(){
 var hero = document.getElementById('heroSlider');
 if (!hero) return;
 var slides = hero.querySelectorAll('.hero-slide');
 if (slides.length < 2) return;
 var dots = hero.querySelectorAll('.hero-dot');
 var cur = 0, timer = null;

 function go(n){
 cur = (n + slides.length) % slides.length;
 slides.forEach(function(s, i){ s.classList.toggle('active', i === cur); });
 dots.forEach(function(d, i){ d.classList.toggle('active', i === cur); });
 }
 function play(){ timer = setInterval(function(){ go(cur + 1); }, 6000); }
 function stop(){ if (timer) clearInterval(timer); timer = null; }

 var prev = document.getElementById('heroPrev');
 var next = document.getElementById('heroNext');
 if (prev) prev.addEventListener('click', function(){ go(cur - 1); stop(); play(); });
 if (next) next.addEventListener('click', function(){ go(cur + 1); stop(); play(); });
 dots.forEach(function(d){
 d.addEventListener('click', function(){ go(parseInt(d.dataset.slide, 10)); stop(); play(); });
 });
 hero.addEventListener('mouseenter', stop);
 hero.addEventListener('mouseleave', play);
 play();
})();

/* ---------- Homepage: API-driven categories + featured products ---------- */
(function(){
 var isHTTP = location.protocol === 'http:' || location.protocol === 'https:';
 if (!isHTTP) return;

 var API = 'api/';

 function esc(s){
 return String(s == null ? '' : s).replace(/[&<>"']/g, function(c){
 return {'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c];
 });
 }

 function getJSON(url, ms){
 ms = ms || 3000;
 return new Promise(function(resolve, reject){
 var ctrl = new AbortController();
 var t = setTimeout(function(){ ctrl.abort(); }, ms);
 fetch(url, { signal: ctrl.signal, headers: { 'Accept': 'application/json' } })
 .then(function(r){ clearTimeout(t); return r.json(); })
 .then(resolve)
 .catch(function(e){ clearTimeout(t); reject(e); });
 });
 }

 var arrow = ' <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M5 12h14M13 5l7 7-7 7"/></svg>';

 getJSON(API + 'categories.php?featured=1&with_counts=1')
 .then(function(data){
 if (!data || !data.ok || !Array.isArray(data.categories) || !data.categories.length) return;
 var grid = document.getElementById('catGrid');
 if (!grid) return;

 var order = ['fencing','steel','hardware'];
 var bySlug = {};
 data.categories.forEach(function(c){ bySlug[c.slug] = c; });

 var html = '';
 order.forEach(function(slug){
 var c = bySlug[slug];
 if (!c) return;
 var badge = (slug === 'fencing') ? '<span class="badge">Most Popular</span>' : '';
 var toolMap = {
 fencing: [['calculator.php','Instant Quote Calculator'],['estimator.php','AI Project Estimator']],
 steel: [['products.php?category=steel','Browse Steel Products'],['contact.php','Request Bulk Pricing']],
 hardware: [['products.php?category=hardware','Browse Hardware'],['contact.php','Request a Quote']]
 };
 var tools = '';
 if (toolMap[slug]) {
 tools = '<div class="fencing-tools">' + toolMap[slug].map(function(t){
 return '<a href="' + t[0] + '" class="btn btn-outline-navy btn-sm">' + t[1] + '</a>';
 }).join('') + '</div>';
 }
 html += '<div class="cat-card">'
 + '<div class="thumb" style="background-image:url(\'' + esc(c.image || '') + '\')">' + badge + '</div>'
 + '<div class="body"><h3>' + esc(c.name) + '</h3>'
 + '<p>' + esc(c.description || c.tagline || '') + '</p>'
 + tools
 + '<a class="more card-cover" style="margin-top:14px;" href="products.php?category=' + esc(c.slug) + '">Explore ' + esc(c.name.split(' ')[0]) + arrow + '</a>'
 + '</div></div>';
 });
 if (html) grid.innerHTML = html;
 })
 .catch(function(){});

 getJSON(API + 'products.php?featured=1&per_page=4&sort=popular')
 .then(function(data){
 if (!data || !data.ok || !Array.isArray(data.products) || !data.products.length) return;
 var grid = document.getElementById('productGrid');
 if (!grid) return;

 var html = '';
 data.products.forEach(function(p){
 var catLabel = (p.category && p.category.name) ? p.category.name : '';
 var price = (p.price_usd != null)
 ? '<div class="price">From $' + Number(p.price_usd).toLocaleString('en-US') + ' <span>/ ' + esc(p.unit || '') + '</span></div>'
 : '';
 var attrs = ' data-name="' + esc(p.name) + '" data-slug="' + esc(p.slug) + '"'
 + ' data-unit="' + esc(p.unit || 'item') + '"'
 + ' data-price="' + (p.price_usd != null ? esc(p.price_usd) : '') + '"';
 html += '<div class="product-card">'
 + '<a class="thumb" href="product-detail.php?slug=' + esc(p.slug) + '" style="background-image:url(\'' + esc(p.image || '') + '\')"></a>'
 + '<div class="body"><span class="cat">' + esc(catLabel) + '</span>'
 + '<h3><a href="product-detail.php?slug=' + esc(p.slug) + '">' + esc(p.name) + '</a></h3>'
 + '<p>' + esc(p.short_desc || '') + '</p>'
 + price
 + '<div class="card-actions">'
 + '<button type="button" class="btn btn-navy btn-sm js-buy"' + attrs + '>Buy Now</button>'
 + '<button type="button" class="btn btn-outline-navy btn-sm js-quote"' + attrs + '>Get Quote</button>'
 + '</div>'
 + '</div></div>';
 });
 if (html) grid.innerHTML = html;
 })
 .catch(function(){});
})();
JS;

require __DIR__ . '/includes/header.php';
?>

<!-- ===================== HERO SLIDER ===================== -->
<section class="hero" id="heroSlider">
 <div class="hero-slides">
 <?php foreach ($heroSlides as $i => $s): ?>
 <div class="hero-slide<?= $i === 0 ? ' active' : '' ?>" style="--hero-img:url('<?= e($s['image']) ?>')">
 <div class="container">
 <div class="hero-inner">
 <?php if (!empty($s['eyebrow'])): ?><span class="eyebrow"><?= e($s['eyebrow']) ?></span><?php endif; ?>
 <h1>
 <?= e($s['title']) ?>
 <?php if (!empty($s['accent'])): ?><span class="accent"><?= e($s['accent']) ?></span><?php endif; ?>
 </h1>
 <p><?= e($s['description']) ?></p>
 <div class="hero-cta">
 <a href="calculator.php" class="btn btn-amber">
 Get Instant Quote
 <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M5 12h14M13 5l7 7-7 7"/></svg>
 </a>
 <a href="estimator.php" class="btn btn-ghost">Try AI Estimator</a>
 </div>
 </div>
 </div>
 </div>
 <?php endforeach; ?>
 </div>

 <?php if (count($heroSlides) > 1): ?>
 <button class="hero-arrow prev" id="heroPrev" aria-label="Previous slide">
 <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M15 18l-6-6 6-6"/></svg>
 </button>
 <button class="hero-arrow next" id="heroNext" aria-label="Next slide">
 <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M9 6l6 6-6 6"/></svg>
 </button>
 <div class="hero-dots" id="heroDots">
 <?php foreach ($heroSlides as $i => $s): ?>
 <button class="hero-dot<?= $i === 0 ? ' active' : '' ?>" data-slide="<?= $i ?>" aria-label="Slide <?= $i + 1 ?>"></button>
 <?php endforeach; ?>
 </div>
 <?php endif; ?>

 <div class="hero-script">Built<br>for a Stronger<br>Zimbabwe</div>
</section>

<!-- ===================== TRUST STRIP ===================== -->
<section class="trust-strip">
 <div class="container">
 <div class="trust-grid">
 <div class="trust-item reveal">
 <div class="trust-icon">
 <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
 </div>
 <div><strong>Nationwide Delivery</strong><span>Across Zimbabwe</span></div>
 </div>
 <div class="trust-item reveal reveal-d1">
 <div class="trust-icon">
 <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 6L9 17l-5-5"/></svg>
 </div>
 <div><strong>Quality Assured</strong><span>SABS Compliant</span></div>
 </div>
 <div class="trust-item reveal reveal-d2">
 <div class="trust-icon">
 <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/></svg>
 </div>
 <div><strong>Expert Support</strong><span>Quote to Installation</span></div>
 </div>
 <div class="trust-item reveal reveal-d3">
 <div class="trust-icon">
 <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M13 2 3 14h9l-1 8 10-12h-9l1-8z"/></svg>
 </div>
 <div><strong>Fast Quotations</strong><span>Results in Seconds</span></div>
 </div>
 </div>
 </div>
</section>

<!-- ===================== FEATURE TILES ===================== -->
<section class="section">
 <div class="container">
 <div class="feature-tiles">
 <a href="calculator.php" class="feature-tile reveal">
 <div class="ico">
 <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="4" y="2" width="16" height="20" rx="2"/><line x1="8" y1="6" x2="16" y2="6"/><line x1="8" y1="10" x2="16" y2="10"/><line x1="8" y1="14" x2="12" y2="14"/></svg>
 </div>
 <div>
 <h3>Instant BOQ Calculator</h3>
 <p>Detailed bill of quantities in seconds.</p>
 </div>
 </a>
 <a href="estimator.php" class="feature-tile reveal reveal-d1">
 <div class="ico">
 <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2v4M12 18v4M4.93 4.93l2.83 2.83M16.24 16.24l2.83 2.83M2 12h4M18 12h4M4.93 19.07l2.83-2.83M16.24 7.76l2.83-2.83"/></svg>
 </div>
 <div>
 <h3>AI Project Estimator</h3>
 <p>Describe it in words, get a smart estimate.</p>
 </div>
 </a>
 <a href="products.php" class="feature-tile reveal reveal-d2">
 <div class="ico">
 <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
 </div>
 <div>
 <h3>Quality Materials</h3>
 <p>Fencing, steel and hardware for every job.</p>
 </div>
 </a>
 </div>
 </div>
</section>

<!-- ===================== WHAT WE SUPPLY ===================== -->
<section class="section section-tight">
 <div class="container">
 <div class="section-head-row reveal">
 <div class="section-head" style="margin-bottom:0;">
 <span class="kicker">Our Divisions</span>
 <h2>What We Supply</h2>
 <p>Three core divisions one trusted supplier. Whatever the project, Vuemax has the products and expertise to deliver.</p>
 </div>
 <a href="products.php" class="link-more">
 View All
 <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M5 12h14M13 5l7 7-7 7"/></svg>
 </a>
 </div>

 <div class="cat-grid" id="catGrid">
 <div class="cat-card reveal">
 <div class="thumb" style="background-image:url('<?= e(site_image('cat-fencing', 'https://images.unsplash.com/photo-1640538336739-ce6ee5ee853e?auto=format&fit=crop&w=800&q=80')) ?>')">
 <span class="badge">Most Popular</span>
 </div>
 <div class="body">
 <h3>Fencing Solutions</h3>
 <p>Diamond mesh, game fence, barbed wire, chicken mesh, posts and accessories for homes, farms and commercial sites.</p>
 <div class="fencing-tools">
 <a href="calculator.php" class="btn btn-outline-navy btn-sm">
 Instant Quote Calculator
 </a>
 <a href="estimator.php" class="btn btn-outline-navy btn-sm">
 AI Project Estimator
 </a>
 </div>
 <a class="more card-cover" style="margin-top:14px;" href="products.php?category=fencing">
 Explore Fencing
 <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M5 12h14M13 5l7 7-7 7"/></svg>
 </a>
 </div>
 </div>

 <div class="cat-card reveal reveal-d1">
 <div class="thumb" style="background-image:url('<?= e(site_image('cat-steel', 'assets/img/products/square-tubes.jpg')) ?>')"></div>
 <div class="body">
 <h3>Steel Products</h3>
 <p>Steel sheets, tubing, rebar and structural sections for construction, fabrication and industrial projects.</p>
 <div class="fencing-tools">
 <a href="products.php?category=steel" class="btn btn-outline-navy btn-sm">
 Browse Steel Products
 </a>
 <a href="contact.php" class="btn btn-outline-navy btn-sm">
 Request Bulk Pricing
 </a>
 </div>
 <a class="more card-cover" style="margin-top:14px;" href="products.php?category=steel">
 Explore Steel
 <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M5 12h14M13 5l7 7-7 7"/></svg>
 </a>
 </div>
 </div>

 <div class="cat-card reveal reveal-d2">
 <div class="thumb" style="background-image:url('<?= e(site_image('cat-hardware', 'https://images.unsplash.com/photo-1581147036324-c17ac41dfa6c?auto=format&fit=crop&w=800&q=80')) ?>')"></div>
 <div class="body">
 <h3>General Hardware</h3>
 <p>Tools, fixings, gate hardware and everyday essentials for tradesmen, farms and DIY.</p>
 <div class="fencing-tools">
 <a href="products.php?category=hardware" class="btn btn-outline-navy btn-sm">
 Browse Hardware
 </a>
 <a href="contact.php" class="btn btn-outline-navy btn-sm">
 Request a Quote
 </a>
 </div>
 <a class="more card-cover" style="margin-top:14px;" href="products.php?category=hardware">
 Explore Hardware
 <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M5 12h14M13 5l7 7-7 7"/></svg>
 </a>
 </div>
 </div>
 </div>
 </div>
</section>

<!-- ===================== FEATURED PRODUCTS ===================== -->
<section class="section section-tight">
 <div class="container">
 <div class="section-head-row reveal">
 <div class="section-head" style="margin-bottom:0;">
 <span class="kicker">Best Sellers</span>
 <h2>Popular Right Now</h2>
 <p>A quick look at our most-requested products across fencing, steel and hardware.</p>
 </div>
 <a href="products.php" class="link-more">
 View All Products
 <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M5 12h14M13 5l7 7-7 7"/></svg>
 </a>
 </div>

 <div class="product-grid" id="productGrid">
 <div class="product-card reveal">
 <a class="thumb" href="product-detail.php?slug=diamond-mesh" style="background-image:url('<?= e(site_image('prod-diamond-mesh', 'assets/img/products/diamond-mesh-2.jpg')) ?>')" aria-label="Diamond Mesh"></a>
 <div class="body">
 <span class="cat">Fencing</span>
 <h3><a href="product-detail.php?slug=diamond-mesh">Diamond Mesh</a></h3>
 <p>Versatile, durable fencing for homes, farms and businesses.</p>
 <div class="price">From $65 <span>/ roll</span></div>
 <div class="card-actions">
 <button type="button" class="btn btn-navy btn-sm js-buy" data-name="Diamond Mesh 50x50 (2mm)" data-slug="diamond-mesh" data-unit="30m roll" data-price="65">Buy Now</button>
 <button type="button" class="btn btn-outline-navy btn-sm js-quote" data-name="Diamond Mesh 50x50 (2mm)" data-slug="diamond-mesh" data-unit="30m roll" data-price="65">Get Quote</button>
 </div>
 </div>
 </div>

 <div class="product-card reveal reveal-d1">
 <a class="thumb" href="product-detail.php?slug=game-fence" style="background-image:url('<?= e(site_image('prod-game-fence', 'assets/img/products/game-fence.jpg')) ?>')" aria-label="Game Fence"></a>
 <div class="body">
 <span class="cat">Fencing</span>
 <h3><a href="product-detail.php?slug=game-fence">Game Fence</a></h3>
 <p>Heavy-duty fencing for wildlife, farms and large properties.</p>
 <div class="price">From $280 <span>/ roll</span></div>
 <div class="card-actions">
 <button type="button" class="btn btn-navy btn-sm js-buy" data-name="Game Fence" data-slug="game-fence" data-unit="roll" data-price="280">Buy Now</button>
 <button type="button" class="btn btn-outline-navy btn-sm js-quote" data-name="Game Fence" data-slug="game-fence" data-unit="roll" data-price="280">Get Quote</button>
 </div>
 </div>
 </div>

 <div class="product-card reveal reveal-d2">
 <a class="thumb" href="product-detail.php?slug=square-tubes" style="background-image:url('<?= e(site_image('prod-square-tubes', 'assets/img/products/square-tubes.jpg')) ?>')" aria-label="Square Tubes"></a>
 <div class="body">
 <span class="cat">Steel</span>
 <h3><a href="product-detail.php?slug=square-tubes">Square Tubes</a></h3>
 <p>Steel square tubes for gates, frames, structural work and fabrication.</p>
 <div class="price">Supplied on <span>request</span></div>
 <div class="card-actions">
 <button type="button" class="btn btn-navy btn-sm js-buy" data-name="Square Tubes" data-slug="square-tubes" data-unit="length" data-price="">Buy Now</button>
 <button type="button" class="btn btn-outline-navy btn-sm js-quote" data-name="Square Tubes" data-slug="square-tubes" data-unit="length" data-price="">Get Quote</button>
 </div>
 </div>
 </div>

 <div class="product-card reveal reveal-d3">
 <a class="thumb" href="product-detail.php?slug=gate-locks" style="background-image:url('<?= e(site_image('prod-gate-locks', 'https://images.unsplash.com/photo-1554863885-e3a33dd1bc82?auto=format&fit=crop&w=800&q=80')) ?>')" aria-label="Gate Locks and Hinges"></a>
 <div class="body">
 <span class="cat">Hardware</span>
 <h3><a href="product-detail.php?slug=gate-locks">Gate Locks &amp; Hinges</a></h3>
 <p>Heavy-duty locks, hinges and latches for gates and security doors.</p>
 <div class="price">From $18 <span>/ set</span></div>
 <div class="card-actions">
 <button type="button" class="btn btn-navy btn-sm js-buy" data-name="Gate Locks & Hinges" data-slug="gate-locks" data-unit="set" data-price="18">Buy Now</button>
 <button type="button" class="btn btn-outline-navy btn-sm js-quote" data-name="Gate Locks & Hinges" data-slug="gate-locks" data-unit="set" data-price="18">Get Quote</button>
 </div>
 </div>
 </div>
 </div>
 </div>
</section>

<!-- ===================== QUOTE BAND ===================== -->
<section class="section section-tight">
 <div class="container">
 <div class="quote-band reveal">
 <div class="quote-band-inner">
 <div>
 <h2>Get Your Fencing Quote <span class="accent">in Seconds.</span></h2>
 <p>Enter a few details about your project and get an instant bill of quantities with estimated pricing. Simple. Accurate. Reliable.</p>
 <a href="calculator.php" class="btn btn-amber btn-block" style="width:100%;">
 Start Calculator
 <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M5 12h14M13 5l7 7-7 7"/></svg>
 </a>
 <div class="script-note">Fast · Accurate · Reliable</div>
 </div>

 <div class="quote-preview">
 <h4>Your Quote (Sample)</h4>
 <div class="qp-row">
 <span class="label">Diamond Mesh (1.8m)</span>
 <span class="value">100 m ? $1,200</span>
 </div>
 <div class="qp-row">
 <span class="label">Steel Posts</span>
 <span class="value">28 pcs ? $420</span>
 </div>
 <div class="qp-row">
 <span class="label">Top Wire</span>
 <span class="value">100 m ? $80</span>
 </div>
 <div class="qp-row">
 <span class="label">Binding Wire</span>
 <span class="value">5 kg ? $35</span>
 </div>
 <div class="qp-row">
 <span class="label">Accessories</span>
 <span class="value">1 set ? $65</span>
 </div>
 <div class="qp-total">
 <span>Estimated Total</span>
 <span class="amt">$1,800</span>
 </div>
 </div>
 </div>
 </div>
 </div>
</section>

<!-- ===================== STATS ===================== -->
<section class="section section-tight">
 <div class="container">
 <div class="section-head reveal">
 <span class="kicker">Why Vuemax</span>
 <h2>Trusted by Farmers, Businesses &amp; Homeowners</h2>
 <p>Delivering quality fencing, steel and hardware across Zimbabwe.</p>
 </div>

 <div class="stats-grid">
 <div class="stat reveal">
 <div class="num">15<span class="plus">+</span></div>
 <div class="lbl">Years of Experience</div>
 </div>
 <div class="stat reveal reveal-d1">
 <div class="num">500<span class="plus">+</span></div>
 <div class="lbl">Projects Delivered</div>
 </div>
 <div class="stat reveal reveal-d2">
 <div class="num">10</div>
 <div class="lbl">Provinces Covered</div>
 </div>
 <div class="stat reveal reveal-d3">
 <div class="num">98<span class="plus">%</span></div>
 <div class="lbl">Customer Satisfaction</div>
 </div>
 </div>
 </div>
</section>

<!-- ===================== CTA BAND ===================== -->
<section class="section section-tight">
 <div class="container">
 <div class="cta-band reveal">
 <h2>Ready to Start Your Project?</h2>
 <p>Talk to our team for a detailed quote, site visit, or product advice or generate an instant estimate online.</p>
 <div class="btns">
 <a href="calculator.php" class="btn btn-amber">
 Get Instant Quote
 <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M5 12h14M13 5l7 7-7 7"/></svg>
 </a>
 <a href="contact.php" class="btn btn-ghost">Talk to Our Team</a>
 </div>
 </div>
 </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
