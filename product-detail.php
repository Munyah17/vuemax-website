<?php
require_once __DIR__ . '/includes/config.php';

$pageTitle = 'Diamond Mesh — Vuemax | Fencing Solutions Zimbabwe';
$pageDesc  = 'Diamond Mesh fencing — versatile, durable and cost-effective. Galvanised wire available in multiple heights. SABS compliant.';
$active    = '';

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
   PRODUCT DETAIL — INTERACTIONS
   ============================================================ */
(function(){
  /* ---- Read slug from URL and update breadcrumb + title ---- */
  const params = new URLSearchParams(window.location.search);
  const slug = params.get('slug');
  if (slug) {
    const pretty = slug.replace(/-/g,' ').replace(/\b\w/g, c => c.toUpperCase());
    document.title = pretty + ' — Vuemax | Fencing Solutions Zimbabwe';
    document.getElementById('crumbProduct').textContent = pretty;
    document.getElementById('productName').textContent = pretty;
  }

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
      <span class="current" id="crumbProduct">Diamond Mesh</span>
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
             style="background-image:url('https://images.unsplash.com/photo-1621905251189-08b45d6a269e?auto=format&fit=crop&w=1200&q=80')">
          <span class="badge-pop">Best Seller</span>
        </div>
        <div class="gallery-thumbs" id="galleryThumbs">
          <div class="gallery-thumb active" style="background-image:url('https://images.unsplash.com/photo-1621905251189-08b45d6a269e?auto=format&fit=crop&w=400&q=80')"></div>
          <div class="gallery-thumb" style="background-image:url('https://images.unsplash.com/photo-1500382017468-9049fed747ef?auto=format&fit=crop&w=400&q=80')"></div>
          <div class="gallery-thumb" style="background-image:url('https://images.unsplash.com/photo-1518709268805-4e9042af9f23?auto=format&fit=crop&w=400&q=80')"></div>
          <div class="gallery-thumb" style="background-image:url('https://images.unsplash.com/photo-1533154683836-84ea7a0bc310?auto=format&fit=crop&w=400&q=80')"></div>
        </div>
      </div>

      <!-- RIGHT: INFO -->
      <div class="product-info">
        <h1 id="productName">Diamond Mesh</h1>

        <div class="product-meta">
          <span class="stars">★★★★★</span>
          <span>4.8 (120 reviews)</span>
          <span class="divider"></span>
          <span>SKU: VX-DM-180</span>
          <span class="divider"></span>
          <span style="color:var(--green-text);font-weight:600;">In Stock</span>
        </div>

        <p class="tagline">
          Versatile, durable and cost-effective fencing for homes, farms and businesses.
          Hot-dip galvanised wire — available in multiple heights and roll lengths.
        </p>

        <!-- Pricing -->
        <div class="price-block">
          <div class="price-row primary">
            <span class="label">From</span>
            <span class="value">$120.00</span>
          </div>
          <div class="price-row">
            <span class="label">Per</span>
            <span class="value">Roll (30 m)</span>
          </div>
          <div class="price-row">
            <span class="label">Heights Available</span>
            <span class="value">1.2 m · 1.5 m · 1.8 m · 2.1 m</span>
          </div>
          <div class="price-row">
            <span class="label">Wire Diameter</span>
            <span class="value">2.5 mm</span>
          </div>
          <div class="price-note">Prices are estimates and may vary based on order volume and location.</div>
        </div>

        <!-- Height option -->
        <div class="option-group">
          <label for="heightOpts">Select Height</label>
          <div class="option-pills" id="heightOpts">
            <button class="option-pill">1.2 m</button>
            <button class="option-pill">1.5 m</button>
            <button class="option-pill active">1.8 m</button>
            <button class="option-pill">2.1 m</button>
          </div>
        </div>

        <!-- Roll length option -->
        <div class="option-group">
          <label for="lengthOpts">Roll Length</label>
          <div class="option-pills" id="lengthOpts">
            <button class="option-pill active">30 m</button>
            <button class="option-pill">50 m</button>
            <button class="option-pill">100 m</button>
          </div>
        </div>

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
      <h3>About Diamond Mesh</h3>
      <p>Our diamond mesh fencing is designed for maximum strength and durability, making it ideal for wildlife enclosures, farms and large properties. Manufactured to SABS standards, it offers exceptional protection and long service life even in harsh conditions.</p>
      <p>Each roll is hot-dip galvanised to resist rust and corrosion — perfect for Zimbabwe's varied climate. The interlocking diamond pattern provides flexibility and impact resistance while maintaining clear visibility through the fence line.</p>
      <p>Available in four standard heights and three roll lengths, diamond mesh can be combined with steel or wooden posts, top wire, and accessories to build a complete fencing solution tailored to your project.</p>
    </div>

    <!-- Specs -->
    <div class="tab-panel" id="tab-specs">
      <h3>Technical Specifications</h3>
      <table class="specs-table">
        <tr><td>Material</td><td>Hot-dip galvanised steel wire</td></tr>
        <tr><td>Mesh Aperture</td><td>50 mm × 50 mm</td></tr>
        <tr><td>Wire Diameter</td><td>2.5 mm</td></tr>
        <tr><td>Roll Length</td><td>30 m / 50 m / 100 m</td></tr>
        <tr><td>Available Heights</td><td>1.2 m · 1.5 m · 1.8 m · 2.1 m</td></tr>
        <tr><td>Finish</td><td>Hot-dip galvanised</td></tr>
        <tr><td>Standard</td><td>SABS 1587</td></tr>
        <tr><td>Warranty</td><td>10 years against manufacturing defects</td></tr>
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
        <div class="thumb" style="background-image:url('https://images.unsplash.com/photo-1587293852726-70cdb56c2866?auto=format&fit=crop&w=600&q=80')"></div>
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
        <div class="thumb" style="background-image:url('https://images.unsplash.com/photo-1533154683836-84ea7a0bc310?auto=format&fit=crop&w=600&q=80')"></div>
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
        <div class="thumb" style="background-image:url('https://images.unsplash.com/photo-1595278069441-2cf29f8005a4?auto=format&fit=crop&w=600&q=80')"></div>
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
        <div class="thumb" style="background-image:url('https://images.unsplash.com/photo-1518709268805-4e9042af9f23?auto=format&fit=crop&w=600&q=80')"></div>
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
