<?php
require_once __DIR__ . '/includes/config.php';

$pageTitle = 'Vuemax — Fencing, Steel & Hardware Solutions | Zimbabwe';
$pageDesc  = 'Vuemax supplies quality fencing, steel and hardware products across Zimbabwe. Get an instant quote with our online calculator.';
$active    = 'home';

$extraCss = <<<'CSS'
/* ---------- HERO (homepage) ---------- */
.hero{
  position:relative;
  min-height:auto;
  padding:56px 0 48px;
  background:
    linear-gradient(155deg,rgba(10,29,51,.95) 0%,rgba(10,29,51,.8) 55%,rgba(14,39,69,.62) 100%),
    url('https://images.unsplash.com/photo-1500382017468-9049fed747ef?auto=format&fit=crop&w=1200&q=80') center/cover no-repeat;
  color:var(--white);
  overflow:hidden;
}
.hero::after{
  content:"";position:absolute;left:0;right:0;bottom:0;height:80px;
  background:linear-gradient(180deg,transparent,rgba(10,29,51,.35));
  pointer-events:none;
}
.hero-inner{position:relative;z-index:1;max-width:640px;}
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
  max-width:520px;
}
.hero-cta{display:flex;flex-direction:column;gap:10px;}
.hero-cta .btn{width:100%;}
.hero-script{
  display:none;
  position:absolute;right:40px;bottom:40px;z-index:1;
  font-family:'Caveat',cursive;font-size:34px;
  color:var(--amber);opacity:.9;line-height:1;
  text-align:right;transform:rotate(-4deg);
  pointer-events:none;
}

@media (min-width:640px){
  .hero{padding:70px 0 60px;}
  .hero-cta{flex-direction:row;flex-wrap:wrap;}
  .hero-cta .btn{width:auto;}
}
@media (min-width:900px){
  .hero-script{display:block;}
}
@media (min-width:1024px){
  .hero{padding:96px 0 84px;min-height:540px;display:flex;align-items:center;}
  .hero h1{font-size:clamp(36px,4.4vw,56px);}
  .hero p{font-size:17px;margin-bottom:34px;}
}
@media (min-width:1280px){
  .hero{padding:110px 0 92px;}
  .hero-script{right:60px;bottom:60px;font-size:40px;}
}
CSS;

$extraJs = <<<'JS'
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
    ms = ms || 5000;
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
        var tools = '';
        if (slug === 'fencing') {
          tools = '<div class="fencing-tools">'
            + '<a href="calculator.php" class="btn btn-outline-navy btn-sm" onclick="event.stopPropagation();">Instant Quote Calculator</a>'
            + '<a href="estimator.php" class="btn btn-outline-navy btn-sm" onclick="event.stopPropagation();">AI Project Estimator</a>'
            + '</div>';
        }
        html += '<a class="cat-card" href="products.php?category=' + esc(c.slug) + '">'
          + '<div class="thumb" style="background-image:url(\'' + esc(c.image || '') + '\')">' + badge + '</div>'
          + '<div class="body"><h3>' + esc(c.name) + '</h3>'
          + '<p>' + esc(c.description || c.tagline || '') + '</p>'
          + tools
          + '<span class="more" style="margin-top:14px;">Explore ' + esc(c.name.split(' ')[0]) + arrow + '</span>'
          + '</div></a>';
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
        html += '<a class="product-card" href="product-detail.php?slug=' + esc(p.slug) + '">'
          + '<div class="thumb" style="background-image:url(\'' + esc(p.image || '') + '\')"></div>'
          + '<div class="body"><span class="cat">' + esc(catLabel) + '</span>'
          + '<h3>' + esc(p.name) + '</h3>'
          + '<p>' + esc(p.short_desc || '') + '</p>'
          + price
          + '<span class="more">View Details' + arrow + '</span>'
          + '</div></a>';
      });
      if (html) grid.innerHTML = html;
    })
    .catch(function(){});
})();
JS;

require __DIR__ . '/includes/header.php';
?>

<!-- ===================== HERO ===================== -->
<section class="hero">
  <div class="container">
    <div class="hero-inner">
      <span class="eyebrow">Zimbabwe's Trusted Partner</span>
      <h1>
        Smarter Fencing Quotes.
        <span class="accent">Faster Decisions.</span>
      </h1>
      <p>Accurate bills of quantities and cost estimates in minutes. Fencing, steel and hardware — for homes, farms, businesses and security projects across Zimbabwe.</p>
      <div class="hero-cta">
        <a href="calculator.php" class="btn btn-amber">
          Get Instant Quote
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M5 12h14M13 5l7 7-7 7"/></svg>
        </a>
        <a href="estimator.php" class="btn btn-ghost">Try AI Estimator</a>
      </div>
    </div>
  </div>
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
        <p>Three core divisions — one trusted supplier. Whatever the project, Vuemax has the products and expertise to deliver.</p>
      </div>
      <a href="products.php" class="link-more">
        View All
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M5 12h14M13 5l7 7-7 7"/></svg>
      </a>
    </div>

    <div class="cat-grid" id="catGrid">
      <a class="cat-card reveal" href="products.php?category=fencing">
        <div class="thumb" style="background-image:url('https://images.unsplash.com/photo-1621905251189-08b45d6a269e?auto=format&fit=crop&w=800&q=80')">
          <span class="badge">Most Popular</span>
        </div>
        <div class="body">
          <h3>Fencing Solutions</h3>
          <p>Diamond mesh, game fence, barbed wire, chicken mesh, posts and accessories — for homes, farms and commercial sites.</p>
          <div class="fencing-tools">
            <a href="calculator.php" class="btn btn-outline-navy btn-sm" onclick="event.stopPropagation();">
              Instant Quote Calculator
            </a>
            <a href="estimator.php" class="btn btn-outline-navy btn-sm" onclick="event.stopPropagation();">
              AI Project Estimator
            </a>
          </div>
          <span class="more" style="margin-top:14px;">
            Explore Fencing
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M5 12h14M13 5l7 7-7 7"/></svg>
          </span>
        </div>
      </a>

      <a class="cat-card reveal reveal-d1" href="products.php?category=steel">
        <div class="thumb" style="background-image:url('https://images.unsplash.com/photo-1565793298595-6a879b1d9492?auto=format&fit=crop&w=800&q=80')"></div>
        <div class="body">
          <h3>Steel Products</h3>
          <p>Steel sheets, tubing, rebar and structural sections for construction, fabrication and industrial projects.</p>
          <span class="more" style="margin-top:14px;">
            Explore Steel
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M5 12h14M13 5l7 7-7 7"/></svg>
          </span>
        </div>
      </a>

      <a class="cat-card reveal reveal-d2" href="products.php?category=hardware">
        <div class="thumb" style="background-image:url('https://images.unsplash.com/photo-1581147036324-c17ac41dfa6c?auto=format&fit=crop&w=800&q=80')"></div>
        <div class="body">
          <h3>General Hardware</h3>
          <p>Tools, fixings, gate hardware and everyday essentials for tradesmen, farms and DIY.</p>
          <span class="more" style="margin-top:14px;">
            Explore Hardware
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M5 12h14M13 5l7 7-7 7"/></svg>
          </span>
        </div>
      </a>
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
      <a class="product-card reveal" href="product-detail.php?slug=diamond-mesh">
        <div class="thumb" style="background-image:url('https://images.unsplash.com/photo-1621905251189-08b45d6a269e?auto=format&fit=crop&w=600&q=80')"></div>
        <div class="body">
          <span class="cat">Fencing</span>
          <h3>Diamond Mesh</h3>
          <p>Versatile, durable fencing for homes, farms and businesses.</p>
          <div class="price">From $120 <span>/ roll</span></div>
          <span class="more">View Details
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M5 12h14M13 5l7 7-7 7"/></svg>
          </span>
        </div>
      </a>

      <a class="product-card reveal reveal-d1" href="product-detail.php?slug=game-fence">
        <div class="thumb" style="background-image:url('https://images.unsplash.com/photo-1518709268805-4e9042af9f23?auto=format&fit=crop&w=600&q=80')"></div>
        <div class="body">
          <span class="cat">Fencing</span>
          <h3>Game Fence</h3>
          <p>Heavy-duty fencing for wildlife, farms and large properties.</p>
          <div class="price">From $280 <span>/ roll</span></div>
          <span class="more">View Details
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M5 12h14M13 5l7 7-7 7"/></svg>
          </span>
        </div>
      </a>

      <a class="product-card reveal reveal-d2" href="product-detail.php?slug=steel-tubing">
        <div class="thumb" style="background-image:url('https://images.unsplash.com/photo-1565793298595-6a879b1d9492?auto=format&fit=crop&w=600&q=80')"></div>
        <div class="body">
          <span class="cat">Steel</span>
          <h3>Steel Tubing</h3>
          <p>Square and rectangular tubing for gates, frames and fabrication.</p>
          <div class="price">From $12 <span>/ metre</span></div>
          <span class="more">View Details
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M5 12h14M13 5l7 7-7 7"/></svg>
          </span>
        </div>
      </a>

      <a class="product-card reveal reveal-d3" href="product-detail.php?slug=gate-locks">
        <div class="thumb" style="background-image:url('https://images.unsplash.com/photo-1581147036324-c17ac41dfa6c?auto=format&fit=crop&w=600&q=80')"></div>
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
            <span class="value">100 m · $1,200</span>
          </div>
          <div class="qp-row">
            <span class="label">Steel Posts</span>
            <span class="value">28 pcs · $420</span>
          </div>
          <div class="qp-row">
            <span class="label">Top Wire</span>
            <span class="value">100 m · $80</span>
          </div>
          <div class="qp-row">
            <span class="label">Binding Wire</span>
            <span class="value">5 kg · $35</span>
          </div>
          <div class="qp-row">
            <span class="label">Accessories</span>
            <span class="value">1 set · $65</span>
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
      <p>Talk to our team for a detailed quote, site visit, or product advice — or generate an instant estimate online.</p>
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
