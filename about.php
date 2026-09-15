<?php
require_once __DIR__ . '/includes/config.php';

$pageTitle = 'About Us Vuemax | Fencing, Steel & Hardware Zimbabwe';
$pageDesc = 'Vuemax Industries is a leading supplier of quality fencing, steel and hardware products in Zimbabwe. Learn about our story, mission and values.';
$active = 'about';

$extraCss = <<<'CSS'
/* ============================================================
 PAGE HERO
 ============================================================ */
.page-hero{
 background:linear-gradient(90deg,rgba(10,29,51,.94) 0%,rgba(10,29,51,.82) 55%,rgba(10,29,51,.65) 100%),
 url('https://images.unsplash.com/photo-1500382017468-9049fed747ef?auto=format&fit=crop&w=1920&q=80') center/cover no-repeat;
 color:var(--white);padding:56px 0 64px;position:relative;
}
.page-hero .breadcrumbs{font-size:13px;color:rgba(255,255,255,.7);margin-bottom:14px;}
.page-hero .breadcrumbs a:hover{color:var(--amber);}
.page-hero .breadcrumbs .sep{margin:0 8px;opacity:.5;}
.page-hero h1{
 font-family:'Playfair Display',serif;
 font-size:clamp(28px,3.6vw,44px);font-weight:700;
 line-height:1.12;letter-spacing:-.01em;margin-bottom:14px;
}
.page-hero h1 .accent{color:var(--amber);}
.page-hero p{
 color:rgba(255,255,255,.85);font-size:16px;
 line-height:1.65;max-width:660px;
}
.page-hero .script-note{
 position:absolute;right:60px;bottom:44px;
 font-family:'Caveat',cursive;font-size:32px;
 color:var(--amber);opacity:.9;line-height:1;
 text-align:right;transform:rotate(-4deg);
}

/* ============================================================
 STORY SECTION (image + text)
 ============================================================ */
.story-section{padding:72px 0;}
.story-grid{
 display:grid;grid-template-columns:1fr 1fr;
 gap:56px;align-items:center;
}
.story-img{
 aspect-ratio:4/3;border-radius:var(--radius-card);
 background:url('https://images.unsplash.com/photo-1621905251189-08b45d6a269e?auto=format&fit=crop&w=1000&q=80') center/cover no-repeat;
 position:relative;overflow:hidden;
 box-shadow:var(--shadow-lift);
}
.story-img::after{
 content:"";position:absolute;inset:0;
 background:linear-gradient(180deg,transparent 55%,rgba(10,29,51,.55));
}
.story-img .script-overlay{
 position:absolute;right:24px;bottom:20px;
 font-family:'Caveat',cursive;font-size:26px;
 color:var(--amber);opacity:.95;line-height:1;
 text-align:right;transform:rotate(-4deg);z-index:1;
}
.story-content .eyebrow{
 font-size:12px;letter-spacing:.16em;text-transform:uppercase;
 color:var(--amber);font-weight:700;margin-bottom:12px;
 display:inline-block;
}
.story-content h2{
 font-family:'Playfair Display',serif;
 font-size:clamp(24px,2.6vw,34px);font-weight:700;
 color:var(--navy);line-height:1.2;margin-bottom:18px;
 letter-spacing:-.01em;
}
.story-content p{
 font-size:15px;color:var(--text);line-height:1.75;
 margin-bottom:16px;
}
.story-content p:last-of-type{margin-bottom:24px;}

/* ============================================================
 MISSION / VISION
 ============================================================ */
.mv-section{padding:0 0 72px;}
.mv-grid{
 display:grid;grid-template-columns:1fr 1fr;gap:24px;
}
.mv-card{
 background:var(--white);border:1px solid var(--border);
 border-radius:var(--radius-card);padding:32px;
 box-shadow:var(--shadow-card);transition:.2s;
}
.mv-card:hover{box-shadow:var(--shadow-lift);transform:translateY(-2px);}
.mv-card .ico{
 width:52px;height:52px;border-radius:12px;
 background:#FEF6E4;color:var(--navy);
 display:flex;align-items:center;justify-content:center;
 margin-bottom:18px;
}
.mv-card h3{
 font-family:'Playfair Display',serif;
 font-size:22px;font-weight:700;color:var(--navy);
 margin-bottom:10px;line-height:1.2;
}
.mv-card p{
 font-size:14.5px;color:var(--text);line-height:1.7;
}

/* ============================================================
 VALUES
 ============================================================ */
.values-section{
 background:var(--white);border-top:1px solid var(--border);
 border-bottom:1px solid var(--border);padding:72px 0;
}
.values-section .section-head{
 text-align:center;margin-bottom:44px;
}
.values-section .section-head h2{
 font-family:'Playfair Display',serif;
 font-size:clamp(24px,2.6vw,34px);font-weight:700;
 color:var(--navy);line-height:1.2;margin-bottom:10px;
 letter-spacing:-.01em;
}
.values-section .section-head p{
 color:var(--muted);font-size:15px;max-width:560px;
 margin:0 auto;line-height:1.6;
}
.values-grid{
 display:grid;grid-template-columns:repeat(4,1fr);gap:24px;
}
.value-card{
 text-align:center;padding:0 12px;
}
.value-card .ico{
 width:56px;height:56px;border-radius:14px;
 background:#FEF6E4;color:var(--navy);
 display:flex;align-items:center;justify-content:center;
 margin:0 auto 16px;
}
.value-card h3{
 font-size:15.5px;font-weight:600;color:var(--navy);
 margin-bottom:8px;
}
.value-card p{
 font-size:13.5px;color:var(--muted);line-height:1.6;
}

/* ============================================================
 WHY CHOOSE US
 ============================================================ */
.why-section{padding:72px 0;}
.why-grid{
 display:grid;grid-template-columns:1fr 1fr;gap:56px;
 align-items:center;
}
.why-img{
 aspect-ratio:4/3;border-radius:var(--radius-card);
 background:url('https://images.unsplash.com/photo-1565793298595-6a879b1d9492?auto=format&fit=crop&w=1000&q=80') center/cover no-repeat;
 position:relative;overflow:hidden;
 box-shadow:var(--shadow-lift);
}
.why-img::after{
 content:"";position:absolute;inset:0;
 background:linear-gradient(180deg,transparent 55%,rgba(10,29,51,.55));
}
.why-img .script-overlay{
 position:absolute;right:24px;bottom:20px;
 font-family:'Caveat',cursive;font-size:26px;
 color:var(--amber);opacity:.95;line-height:1;
 text-align:right;transform:rotate(-4deg);z-index:1;
}
.why-content .eyebrow{
 font-size:12px;letter-spacing:.16em;text-transform:uppercase;
 color:var(--amber);font-weight:700;margin-bottom:12px;
 display:inline-block;
}
.why-content h2{
 font-family:'Playfair Display',serif;
 font-size:clamp(24px,2.6vw,34px);font-weight:700;
 color:var(--navy);line-height:1.2;margin-bottom:18px;
 letter-spacing:-.01em;
}
.why-list{list-style:none;}
.why-list li{
 display:flex;gap:14px;align-items:flex-start;
 padding:14px 0;border-bottom:1px solid var(--border);
}
.why-list li:last-child{border-bottom:none;}
.why-list .ico{
 width:36px;height:36px;border-radius:9px;
 background:#FEF6E4;color:var(--navy);flex-shrink:0;
 display:flex;align-items:center;justify-content:center;
}
.why-list strong{
 display:block;font-size:14.5px;font-weight:600;
 color:var(--navy);margin-bottom:3px;
}
.why-list p{
 font-size:13.5px;color:var(--muted);line-height:1.55;
}

/* ============================================================
 COVERAGE / PROVINCES
 ============================================================ */
.coverage-section{
 background:var(--navy);color:var(--white);
 padding:72px 0;
}
.coverage-head{text-align:center;margin-bottom:44px;}
.coverage-head .eyebrow{
 font-size:12px;letter-spacing:.16em;text-transform:uppercase;
 color:var(--amber);font-weight:700;margin-bottom:12px;
 display:inline-block;
}
.coverage-head h2{
 font-family:'Playfair Display',serif;
 font-size:clamp(24px,2.6vw,34px);font-weight:700;
 line-height:1.2;margin-bottom:12px;letter-spacing:-.01em;
}
.coverage-head h2 .accent{color:var(--amber);}
.coverage-head p{
 color:rgba(255,255,255,.82);font-size:15.5px;
 line-height:1.6;max-width:560px;margin:0 auto;
}
.provinces-grid{
 display:grid;grid-template-columns:repeat(5,1fr);gap:16px;
}
.province{
 background:rgba(255,255,255,.06);
 border:1px solid rgba(255,255,255,.1);
 border-radius:var(--radius-card);
 padding:20px 14px;text-align:center;
 transition:.2s;
}
.province:hover{background:rgba(245,183,49,.12);border-color:rgba(245,183,49,.4);}
.province .dot{
 width:8px;height:8px;border-radius:50%;
 background:var(--amber);margin:0 auto 12px;
 box-shadow:0 0 0 4px rgba(245,183,49,.15);
}
.province strong{
 display:block;font-size:13.5px;font-weight:600;
 color:var(--white);line-height:1.3;
}

/* Trusted-by clients */
.clients-section{padding:64px 0;}
.clients-head{text-align:center;margin-bottom:36px;}
.clients-head h2{
 font-family:'Playfair Display',serif;font-size:clamp(26px,3.4vw,36px);
 font-weight:700;color:var(--navy);margin-bottom:10px;
}
.clients-head h2 .accent{color:var(--amber);}
.clients-head p{color:var(--muted);font-size:15px;max-width:620px;margin:0 auto;}
.clients-grid{
 display:grid;grid-template-columns:repeat(5,1fr);gap:14px;
}
.client-chip{
 background:var(--white);border:1px solid var(--border);
 border-radius:var(--radius-card);padding:18px 14px;
 text-align:center;font-size:13.5px;font-weight:600;color:var(--navy);
 transition:.2s;
}
.client-chip .ico{
 width:34px;height:34px;margin:0 auto 10px;border-radius:9px;
 background:var(--navy);color:var(--amber);
 display:flex;align-items:center;justify-content:center;
}
.client-chip:hover{border-color:var(--amber);transform:translateY(-2px);}

/* Coverage stats */
.coverage-stats{
 display:grid;grid-template-columns:repeat(3,1fr);
 gap:20px;margin-top:48px;
 max-width:800px;margin-left:auto;margin-right:auto;
}
.cs-item{
 text-align:center;padding:22px 14px;
 border:1px solid rgba(255,255,255,.1);
 border-radius:var(--radius-card);
 background:rgba(255,255,255,.04);
}
.cs-item .num{
 font-family:'Playfair Display',serif;
 font-size:32px;font-weight:700;color:var(--amber);
 line-height:1;margin-bottom:6px;
}
.cs-item .lbl{
 font-size:12.5px;color:rgba(255,255,255,.75);
 font-weight:500;letter-spacing:.02em;
}

/* Responsive */
@media (max-width:1024px){
 .story-grid{grid-template-columns:1fr;gap:36px;}
 .mv-grid{grid-template-columns:1fr;}
 .values-grid{grid-template-columns:repeat(2,1fr);}
 .why-grid{grid-template-columns:1fr;gap:36px;}
 .provinces-grid{grid-template-columns:repeat(3,1fr);}
 .clients-grid{grid-template-columns:repeat(3,1fr);}
}
@media (max-width:980px){
 .page-hero .script-note{display:none;}
}
@media (max-width:600px){
 .values-grid{grid-template-columns:1fr;}
 .provinces-grid{grid-template-columns:repeat(2,1fr);}
 .coverage-stats{grid-template-columns:1fr;}
}
CSS;

require __DIR__ . '/includes/header.php';
?>

<!-- ===================== PAGE HERO ===================== -->
<section class="page-hero" style="--hero-img:url('<?= e(site_image('about-hero', 'https://images.unsplash.com/photo-1500382017468-9049fed747ef?auto=format&fit=crop&w=1920&q=80')) ?>')">
 <div class="container">
 <div class="breadcrumbs">
 <a href="index.php">Home</a>
 <span class="sep">›</span>
 <span>About</span>
 </div>
 <h1>About <span class="accent">Vuemax Industries</span></h1>
 <p>Quality fencing, steel and hardware solutions for a stronger Zimbabwe delivered with expertise, honesty and lasting value.</p>
 </div>
 <div class="script-note">Building<br>a Safer<br>Zimbabwe</div>
</section>

<!-- ===================== STORY ===================== -->
<section class="story-section">
 <div class="container">
 <div class="story-grid">
 <div class="story-img reveal" style="background-image:url('<?= e(site_image('about-story', 'https://images.unsplash.com/photo-1621905251189-08b45d6a269e?auto=format&fit=crop&w=1000&q=80')) ?>')">
 <div class="script-overlay">Built on<br>Trust.</div>
 </div>

 <div class="story-content reveal reveal-d1">
 <span class="eyebrow">Our Story</span>
 <h2>Zimbabwe's Trusted Manufacturer &amp; Supplier of Steel, Wire &amp; Fencing</h2>
 <p>VueMax Industries is a Zimbabwean manufacturing and supply company specialising in the production and supply of steel, wire and fencing products for residential, commercial, industrial, agricultural and infrastructure projects.</p>
 <p>Our product range includes barbed wire, welded mesh, diamond mesh, game and field fencing, clear-view fencing, galvanised plain wire, galvanised fencing posts, steel products, nails, angle irons, tubes, plates and other general hardware and steel supplies.</p>
 <p>From individual homeowners to large commercial and agricultural operations across all ten provinces, every order we deliver strengthens our reputation as a partner who shows up, delivers on time, and stands behind the product.</p>
 <a href="installations.php" class="btn btn-navy">
 See Our Work
 <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M5 12h14M13 5l7 7-7 7"/></svg>
 </a>
 </div>
 </div>
 </div>
</section>

<!-- ===================== MISSION / VISION ===================== -->
<section class="mv-section">
 <div class="container">
 <div class="mv-grid">
 <div class="mv-card reveal">
 <div class="ico">
 <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><circle cx="12" cy="12" r="6"/><circle cx="12" cy="12" r="2"/></svg>
 </div>
 <h3>Our Mission</h3>
 <p>To provide high-quality fencing, steel and hardware products across Zimbabwe, while delivering outstanding service and value. We build long-term relationships by being reliable, transparent and customer-focused in everything we do.</p>
 </div>

 <div class="mv-card reveal reveal-d1">
 <div class="ico">
 <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2l3 7h7l-5.5 4.5L18 21l-6-4-6 4 1.5-7.5L2 9h7z"/></svg>
 </div>
 <h3>Our Vision</h3>
 <p>To be the most trusted supplier of fencing, steel and hardware in Zimbabwe recognised for quality, innovation, service excellence and deep commitment to customer satisfaction across every province we serve.</p>
 </div>
 </div>
 </div>
</section>

<!-- ===================== VALUES ===================== -->
<section class="values-section">
 <div class="container">
 <div class="section-head reveal">
 <h2>What We Stand For</h2>
 <p>Four principles that guide every project, every order, every interaction.</p>
 </div>

 <div class="values-grid">
 <div class="value-card reveal">
 <div class="ico">
 <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 6L9 17l-5-5"/></svg>
 </div>
 <h3>Quality</h3>
 <p>SABS-compliant materials tested for Zimbabwe's toughest conditions.</p>
 </div>

 <div class="value-card reveal reveal-d1">
 <div class="ico">
 <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M13 2 3 14h9l-1 8 10-12h-9l1-8z"/></svg>
 </div>
 <h3>Reliability</h3>
 <p>On-time delivery and installation every order, every time.</p>
 </div>

 <div class="value-card reveal reveal-d2">
 <div class="ico">
 <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/></svg>
 </div>
 <h3>Expertise</h3>
 <p>Over 15 years of hands-on experience across every kind of project.</p>
 </div>

 <div class="value-card reveal reveal-d3">
 <div class="ico">
 <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
 </div>
 <h3>Trust</h3>
 <p>Transparent pricing, honest advice and full accountability.</p>
 </div>
 </div>
 </div>
</section>

<!-- ===================== WHY CHOOSE US ===================== -->
<section class="why-section">
 <div class="container">
 <div class="why-grid">
 <div class="why-img reveal" style="background-image:url('<?= e(site_image('about-why', 'https://images.unsplash.com/photo-1565793298595-6a879b1d9492?auto=format&fit=crop&w=1000&q=80')) ?>')">
 <div class="script-overlay">Stronger<br>Together.</div>
 </div>

 <div class="why-content reveal reveal-d1">
 <span class="eyebrow">Why Choose Vuemax</span>
 <h2>The Vuemax Difference</h2>

 <ul class="why-list">
 <li>
 <div class="ico">
 <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 6L9 17l-5-5"/></svg>
 </div>
 <div>
 <strong>Premium Quality Materials</strong>
 <p>SABS-compliant galvanised fencing, steel and hardware built for lasting performance.</p>
 </div>
 </li>

 <li>
 <div class="ico">
 <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
 </div>
 <div>
 <strong>Nationwide Delivery</strong>
 <p>We deliver to all 10 provinces in Zimbabwe from Harare to Bulawayo, Mutare, Masvingo and beyond.</p>
 </div>
 </li>

 <li>
 <div class="ico">
 <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/></svg>
 </div>
 <div>
 <strong>Expert Guidance</strong>
 <p>From product selection to installation, our team advises you at every step of the project.</p>
 </div>
 </li>

 <li>
 <div class="ico">
 <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 1v22M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
 </div>
 <div>
 <strong>Competitive Pricing</strong>
 <p>Direct supplier pricing with bulk discounts available for large projects.</p>
 </div>
 </li>
 </ul>
 </div>
 </div>
 </div>
</section>

<!-- ===================== TRUSTED BY ===================== -->
<section class="clients-section">
 <div class="container">
 <div class="clients-head reveal">
 <span class="eyebrow">Who We Serve</span>
 <h2>Trusted <span class="accent">Across Zimbabwe</span></h2>
 <p>A selection of the clients and organisations that trust VueMax Industries for their steel, wire and fencing requirements.</p>
 </div>
 <div class="clients-grid">
 <div class="client-chip reveal"><div class="ico"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 21h18M5 21V7l7-4 7 4v14M9 9h1m4 0h1M9 13h1m4 0h1M9 17h1m4 0h1"/></svg></div>Construction Companies</div>
 <div class="client-chip reveal"><div class="ico"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 12l9-9 9 9M5 10v10h5v-6h4v6h5V10"/></svg></div>Property Developers</div>
 <div class="client-chip reveal"><div class="ico"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 21c-4 0-7-3-7-7V6l7-3 7 3v8c0 4-3 7-7 7z"/></svg></div>Farmers &amp; Agriculture</div>
 <div class="client-chip reveal"><div class="ico"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/></svg></div>Mining Companies</div>
 <div class="client-chip reveal"><div class="ico"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg></div>Security Companies</div>
 <div class="client-chip reveal"><div class="ico"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 2l1.5 6h9L18 2M4 8h16l-1 14H5L4 8z"/></svg></div>Hardware &amp; Retailers</div>
 <div class="client-chip reveal"><div class="ico"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 21h18M4 18V9l8-6 8 6v9M9 21v-6h6v6"/></svg></div>Government &amp; Institutions</div>
 <div class="client-chip reveal"><div class="ico"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 10L12 5 2 10l10 5 10-5zM6 12v5c0 1.7 2.7 3 6 3s6-1.3 6-3v-5"/></svg></div>Schools &amp; Universities</div>
 <div class="client-chip reveal"><div class="ico"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14.7 6.3a4 4 0 1 0-5.4 5.4L2 19l3 3 7.3-7.3a4 4 0 0 0 2.4-8.4z"/></svg></div>Contractors &amp; Installers</div>
 <div class="client-chip reveal"><div class="ico"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 12l9-9 9 9M5 10v10h14V10"/></svg></div>Homeowners &amp; Property Owners</div>
 </div>
 </div>
</section>

<!-- ===================== COVERAGE ===================== -->
<section class="coverage-section">
 <div class="container">
 <div class="coverage-head reveal">
 <span class="eyebrow">Where We Operate</span>
 <h2>Coverage <span class="accent">Across Zimbabwe</span></h2>
 <p>We deliver and install in every province. Wherever your project is, we can get there.</p>
 </div>

 <div class="provinces-grid">
 <div class="province reveal"><div class="dot"></div><strong>Harare</strong></div>
 <div class="province reveal"><div class="dot"></div><strong>Bulawayo</strong></div>
 <div class="province reveal"><div class="dot"></div><strong>Manicaland</strong></div>
 <div class="province reveal"><div class="dot"></div><strong>Mashonaland Central</strong></div>
 <div class="province reveal"><div class="dot"></div><strong>Mashonaland East</strong></div>
 <div class="province reveal"><div class="dot"></div><strong>Mashonaland West</strong></div>
 <div class="province reveal"><div class="dot"></div><strong>Masvingo</strong></div>
 <div class="province reveal"><div class="dot"></div><strong>Matabeleland North</strong></div>
 <div class="province reveal"><div class="dot"></div><strong>Matabeleland South</strong></div>
 <div class="province reveal"><div class="dot"></div><strong>Midlands</strong></div>
 </div>

 <div class="coverage-stats">
 <div class="cs-item reveal">
 <div class="num">10</div>
 <div class="lbl">Provinces Covered</div>
 </div>
 <div class="cs-item reveal reveal-d1">
 <div class="num">500+</div>
 <div class="lbl">Projects Delivered</div>
 </div>
 <div class="cs-item reveal reveal-d2">
 <div class="num">15+</div>
 <div class="lbl">Years of Service</div>
 </div>
 </div>
 </div>
</section>

<!-- ===================== CTA BAND ===================== -->
<section class="section section-tight">
 <div class="container">
 <div class="cta-band reveal">
 <h2>Let's Build Something Lasting</h2>
 <p>Talk to our team about your project, or get an instant estimate with our online tools.</p>
 <div class="btns">
 <a href="calculator.php" class="btn btn-amber">
 Get Instant Quote
 <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M5 12h14M13 5l7 7-7 7"/></svg>
 </a>
 <a href="contact.php" class="btn btn-ghost">
 Talk to Our Team
 </a>
 </div>
 </div>
 </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
