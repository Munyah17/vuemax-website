<?php
require_once __DIR__ . '/includes/config.php';

$pageTitle = 'FAQ — Vuemax | Fencing, Steel & Hardware Zimbabwe';
$pageDesc  = 'Answers to common questions about Vuemax fencing, steel, hardware, delivery, payment and installation across Zimbabwe.';
$active    = 'faq';

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
  line-height:1.65;max-width:620px;
}
.page-hero .script-note{
  position:absolute;right:60px;bottom:44px;
  font-family:'Caveat',cursive;font-size:32px;
  color:var(--amber);opacity:.9;line-height:1;
  text-align:right;transform:rotate(-4deg);
}

/* ============================================================
   SEARCH BAR
   ============================================================ */
.faq-search-strip{
  background:var(--white);border-bottom:1px solid var(--border);
  padding:20px 0;
}
.search-inner{
  display:flex;align-items:center;gap:14px;
  background:var(--bg);border:1.5px solid var(--border);
  border-radius:var(--radius-card);
  padding:12px 18px;max-width:640px;margin:0 auto;
  transition:.15s;
}
.search-inner:focus-within{
  border-color:var(--navy);background:var(--white);
  box-shadow:0 0 0 3px rgba(14,39,69,.08);
}
.search-inner svg{color:var(--muted);flex-shrink:0;}
.search-inner input{
  width:100%;border:none;background:transparent;
  font-size:14.5px;color:var(--navy);outline:none;
  font-family:inherit;
}
.search-inner input::placeholder{color:#9CA3AF;}
.search-clear{
  width:26px;height:26px;border-radius:50%;
  display:none;align-items:center;justify-content:center;
  color:var(--muted);background:transparent;
  transition:.15s;flex-shrink:0;
}
.search-clear:hover{background:var(--border);color:var(--navy);}
.search-clear.visible{display:flex;}

/* ============================================================
   MAIN LAYOUT
   ============================================================ */
.faq-layout{
  display:grid;grid-template-columns:260px 1fr;
  gap:44px;padding:52px 0 72px;align-items:start;
}

/* Sidebar */
.faq-sidebar{position:sticky;top:96px;}
.sidebar-block{
  background:var(--white);border:1px solid var(--border);
  border-radius:var(--radius-card);padding:20px;
  margin-bottom:18px;
}
.sidebar-block h4{
  font-size:11px;font-weight:700;color:var(--navy);
  letter-spacing:.08em;text-transform:uppercase;
  padding-bottom:12px;margin-bottom:14px;
  border-bottom:1px solid var(--border);
}
.cat-list{list-style:none;}
.cat-list li{margin-bottom:2px;}
.cat-list button{
  display:flex;justify-content:space-between;align-items:center;
  width:100%;padding:9px 12px;border-radius:8px;
  font-size:13.5px;color:var(--text);font-weight:500;
  text-align:left;transition:.15s;cursor:pointer;
  font-family:inherit;
}
.cat-list button:hover{background:var(--bg);color:var(--navy);}
.cat-list button.active{
  background:var(--navy);color:var(--white);font-weight:600;
}
.cat-list .count{
  font-size:11px;font-weight:600;padding:2px 8px;
  border-radius:999px;background:var(--bg);color:var(--muted);
}
.cat-list button.active .count{
  background:rgba(245,183,49,.22);color:var(--amber);
}

/* Sidebar help card */
.help-card{
  background:linear-gradient(135deg,var(--navy) 0%,var(--navy-deep) 100%);
  border-radius:var(--radius-card);padding:22px;
  color:var(--white);
}
.help-card .ico{
  width:40px;height:40px;border-radius:10px;
  background:rgba(245,183,49,.15);color:var(--amber);
  display:flex;align-items:center;justify-content:center;
  margin-bottom:12px;
}
.help-card h4{
  font-size:15px;font-weight:700;color:var(--white);
  margin-bottom:6px;letter-spacing:.01em;
}
.help-card p{
  font-size:12.5px;color:rgba(255,255,255,.75);
  line-height:1.55;margin-bottom:16px;
}
.help-card .btn{
  width:100%;justify-content:center;font-size:13.5px;
  padding:10px 16px;
}

/* ============================================================
   FAQ LIST
   ============================================================ */
.faq-main{}
.faq-head-row{
  display:flex;justify-content:space-between;align-items:center;
  gap:16px;margin-bottom:24px;flex-wrap:wrap;
}
.faq-head-row h2{
  font-family:'Playfair Display',serif;
  font-size:clamp(22px,2.4vw,28px);font-weight:700;
  color:var(--navy);line-height:1.2;
  letter-spacing:-.01em;
}
.result-count{
  font-size:13.5px;color:var(--muted);
}
.result-count strong{color:var(--navy);font-weight:600;}

.faq-list{list-style:none;display:flex;flex-direction:column;gap:12px;}

.faq-item{
  background:var(--white);border:1px solid var(--border);
  border-radius:var(--radius-card);overflow:hidden;
  transition:.2s;
}
.faq-item:hover{border-color:#D1D5DB;}
.faq-item.open{
  border-color:var(--navy);
  box-shadow:0 4px 14px rgba(15,39,69,.06);
}

.faq-question{
  width:100%;text-align:left;
  display:flex;justify-content:space-between;align-items:center;
  gap:20px;padding:20px 24px;
  font-size:15.5px;font-weight:600;color:var(--navy);
  line-height:1.4;transition:.15s;cursor:pointer;
  font-family:inherit;
}
.faq-question:hover{background:#FAFBFC;}
.faq-item.open .faq-question{background:transparent;}

.faq-question .icon-wrap{
  width:30px;height:30px;border-radius:50%;
  background:var(--bg);color:var(--navy);flex-shrink:0;
  display:flex;align-items:center;justify-content:center;
  transition:.25s ease;
}
.faq-item.open .icon-wrap{
  background:var(--amber);color:var(--navy);
  transform:rotate(180deg);
}

/* Answer */
.faq-answer{
  max-height:0;overflow:hidden;
  transition:max-height .35s ease;
}
.faq-item.open .faq-answer{max-height:800px;}
.faq-answer-inner{
  padding:0 24px 24px;font-size:14.5px;
  line-height:1.75;color:var(--text);
  border-top:1px solid var(--border);
  padding-top:18px;margin-top:0;
}
.faq-answer-inner p{margin-bottom:12px;}
.faq-answer-inner p:last-child{margin-bottom:0;}
.faq-answer-inner ul{
  list-style:none;margin:12px 0;
}
.faq-answer-inner ul li{
  display:flex;gap:10px;align-items:flex-start;
  padding:6px 0;font-size:14px;line-height:1.6;
}
.faq-answer-inner ul li::before{
  content:"✓";color:var(--green-text);
  font-weight:700;flex-shrink:0;margin-top:1px;
}

/* No results */
.no-results{
  display:none;text-align:center;padding:64px 24px;
  background:var(--white);border:1px solid var(--border);
  border-radius:var(--radius-card);
}
.no-results.active{display:block;}
.no-results .icon{
  width:56px;height:56px;border-radius:50%;
  background:var(--bg);color:var(--muted);
  display:flex;align-items:center;justify-content:center;
  margin:0 auto 16px;
}
.no-results h3{
  font-size:15.5px;font-weight:600;color:var(--navy);
  margin-bottom:6px;
}
.no-results p{
  font-size:13.5px;color:var(--muted);
  margin-bottom:20px;max-width:380px;
  margin-left:auto;margin-right:auto;line-height:1.55;
}

/* ============================================================
   STILL HAVE QUESTIONS
   ============================================================ */
.still-questions{
  background:var(--white);border-top:1px solid var(--border);
  border-bottom:1px solid var(--border);padding:56px 0;
}
.sq-grid{
  display:grid;grid-template-columns:1fr 2fr;gap:48px;
  align-items:center;
}
.sq-left .eyebrow{
  font-size:12px;letter-spacing:.16em;text-transform:uppercase;
  color:var(--amber);font-weight:700;margin-bottom:12px;
  display:inline-block;
}
.sq-left h2{
  font-family:'Playfair Display',serif;
  font-size:clamp(22px,2.6vw,32px);font-weight:700;
  color:var(--navy);line-height:1.2;margin-bottom:12px;
}
.sq-left p{
  font-size:14.5px;color:var(--muted);line-height:1.65;
}
.sq-options{
  display:grid;grid-template-columns:repeat(2,1fr);gap:14px;
}
.sq-card{
  background:var(--bg);border:1px solid var(--border);
  border-radius:var(--radius-card);padding:20px;
  transition:.2s;display:flex;gap:14px;
  align-items:flex-start;
}
.sq-card:hover{
  transform:translateY(-3px);
  border-color:var(--navy);
  background:var(--white);
  box-shadow:var(--shadow-lift);
}
.sq-card .ico{
  width:40px;height:40px;border-radius:10px;
  background:#FEF6E4;color:var(--navy);flex-shrink:0;
  display:flex;align-items:center;justify-content:center;
}
.sq-card.whatsapp .ico{background:#DCFCE7;color:#15803D;}
.sq-card strong{
  display:block;font-size:14px;font-weight:600;
  color:var(--navy);margin-bottom:3px;
}
.sq-card span{
  display:block;font-size:12.5px;color:var(--muted);
  line-height:1.5;
}

/* Responsive */
@media (max-width:1024px){
  .faq-layout{grid-template-columns:1fr;gap:24px;}
  .faq-sidebar{position:static;}
  .sq-grid{grid-template-columns:1fr;gap:28px;}
}
@media (max-width:980px){
  .page-hero .script-note{display:none;}
}
@media (max-width:600px){
  .sq-options{grid-template-columns:1fr;}
  .faq-question{padding:16px 18px;font-size:14.5px;}
  .faq-answer-inner{padding-left:18px;padding-right:18px;}
}
CSS;

$extraJs = <<<'JS'
/* ============================================================
   FAQ PAGE — ACCORDION + CATEGORY FILTER + SEARCH
   ============================================================ */
(function(){

  const items        = document.querySelectorAll('#faqList .faq-item');
  const catBtns      = document.querySelectorAll('#catList button');
  const searchInput  = document.getElementById('faqSearch');
  const searchClear  = document.getElementById('searchClear');
  const noResults    = document.getElementById('noResults');
  const visibleCount = document.getElementById('visibleCount');
  const listTitle    = document.getElementById('listTitle');
  const totalCount   = items.length;

  document.getElementById('countAll').textContent = totalCount;

  let currentCat = 'all';
  let currentQuery = '';

  /* ---------- Accordion ---------- */
  items.forEach(item => {
    const btn = item.querySelector('.faq-question');
    btn.addEventListener('click', () => {
      const isOpen = item.classList.contains('open');
      // Close all others (single-open accordion)
      items.forEach(x => x.classList.remove('open'));
      if (!isOpen) item.classList.add('open');
    });
  });

  /* ---------- Category filter ---------- */
  catBtns.forEach(btn => {
    btn.addEventListener('click', () => {
      catBtns.forEach(b => b.classList.remove('active'));
      btn.classList.add('active');
      currentCat = btn.dataset.cat;
      listTitle.textContent = btn.textContent.trim().replace(/\s+\d+$/, '');
      applyFilters();
    });
  });

  /* ---------- Search ---------- */
  function normalize(s){ return (s || '').toLowerCase().trim(); }

  searchInput.addEventListener('input', () => {
    currentQuery = normalize(searchInput.value);
    searchClear.classList.toggle('visible', currentQuery.length > 0);
    applyFilters();
  });

  searchClear.addEventListener('click', () => {
    searchInput.value = '';
    currentQuery = '';
    searchClear.classList.remove('visible');
    applyFilters();
    searchInput.focus();
  });

  /* ---------- Combined filter ---------- */
  function applyFilters(){
    let visible = 0;
    items.forEach(item => {
      const cat = item.dataset.cat;
      const text = normalize(item.textContent);

      const matchCat = (currentCat === 'all') || (cat === currentCat);
      const matchQ   = !currentQuery || text.includes(currentQuery);

      if (matchCat && matchQ){
        item.style.display = '';
        visible++;
        // If searching, auto-open the matching item
        if (currentQuery) item.classList.add('open');
        else item.classList.remove('open');
      } else {
        item.style.display = 'none';
        item.classList.remove('open');
      }
    });

    visibleCount.textContent = visible;
    noResults.classList.toggle('active', visible === 0);
  }

  /* ---------- Deep link via ?q=slug or ?cat=general ---------- */
  const params = new URLSearchParams(window.location.search);
  const q = params.get('q');
  const c = params.get('cat');

  if (c){
    const btn = document.querySelector(`#catList button[data-cat="${c}"]`);
    if (btn){
      catBtns.forEach(b => b.classList.remove('active'));
      btn.classList.add('active');
      currentCat = c;
      listTitle.textContent = btn.textContent.trim().replace(/\s+\d+$/, '');
    }
  }
  if (q){
    searchInput.value = q;
    currentQuery = normalize(q);
    searchClear.classList.add('visible');
  }
  if (c || q) applyFilters();

})();
JS;

require __DIR__ . '/includes/header.php';
?>

<!-- ===================== PAGE HERO ===================== -->
<section class="page-hero">
  <div class="container">
    <div class="breadcrumbs">
      <a href="index.php">Home</a>
      <span class="sep">›</span>
      <span>FAQ</span>
    </div>
    <h1>Frequently Asked <span class="accent">Questions</span></h1>
    <p>Find quick answers to common questions about our products, delivery, payment, installation and more. Can't find what you're looking for? Just ask us directly.</p>
  </div>
  <div class="script-note">Your Questions.<br>Answered.</div>
</section>

<!-- ===================== SEARCH ===================== -->
<div class="faq-search-strip">
  <div class="container">
    <div class="search-inner">
      <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
      <input type="text" id="faqSearch" placeholder="Search questions... (e.g. delivery, payment, SABS)">
      <button class="search-clear" id="searchClear" aria-label="Clear search">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
      </button>
    </div>
  </div>
</div>

<!-- ===================== MAIN LAYOUT ===================== -->
<div class="container">
  <div class="faq-layout">

    <!-- SIDEBAR -->
    <aside class="faq-sidebar">

      <div class="sidebar-block">
        <h4>Categories</h4>
        <ul class="cat-list" id="catList">
          <li><button class="active" data-cat="all">All Questions <span class="count" id="countAll">14</span></button></li>
          <li><button data-cat="general">General <span class="count">3</span></button></li>
          <li><button data-cat="products">Products <span class="count">4</span></button></li>
          <li><button data-cat="delivery">Delivery <span class="count">2</span></button></li>
          <li><button data-cat="payment">Payment &amp; Pricing <span class="count">3</span></button></li>
          <li><button data-cat="installation">Installation <span class="count">2</span></button></li>
        </ul>
      </div>

      <div class="help-card">
        <div class="ico">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
        </div>
        <h4>Still have questions?</h4>
        <p>Our team is ready to help with anything not covered here.</p>
        <a href="contact.php" class="btn btn-amber">
          Contact Us
          <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M5 12h14M13 5l7 7-7 7"/></svg>
        </a>
      </div>

    </aside>

    <!-- MAIN FAQ LIST -->
    <main class="faq-main">

      <div class="faq-head-row">
        <h2 id="listTitle">All Questions</h2>
        <div class="result-count">
          Showing <strong id="visibleCount">14</strong> of <strong>14</strong> questions
        </div>
      </div>

      <ul class="faq-list" id="faqList">

        <!-- ============ GENERAL ============ -->
        <li class="faq-item" data-cat="general">
          <button class="faq-question">
            <span>Who is Vuemax and where are you located?</span>
            <span class="icon-wrap">
              <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="6 9 12 15 18 9"/></svg>
            </span>
          </button>
          <div class="faq-answer">
            <div class="faq-answer-inner">
              <p>Vuemax Investments is a Zimbabwean supplier of quality fencing, steel and hardware products. We've been serving homes, farms, businesses and institutions across all ten provinces for over 15 years.</p>
              <p>Our main showroom and warehouse is located at <strong>103 Willowvale Road, Harare</strong>. We also deliver nationwide.</p>
            </div>
          </div>
        </li>

        <li class="faq-item" data-cat="general">
          <button class="faq-question">
            <span>What areas of Zimbabwe do you serve?</span>
            <span class="icon-wrap">
              <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="6 9 12 15 18 9"/></svg>
            </span>
          </button>
          <div class="faq-answer">
            <div class="faq-answer-inner">
              <p>We serve customers in all ten provinces of Zimbabwe:</p>
              <ul>
                <li>Harare &amp; Bulawayo metropolitan</li>
                <li>Manicaland, Mashonaland Central, East &amp; West</li>
                <li>Masvingo, Midlands</li>
                <li>Matabeleland North &amp; South</li>
              </ul>
              <p>Delivery fees may vary depending on distance and order size — contact us for a quote.</p>
            </div>
          </div>
        </li>

        <li class="faq-item" data-cat="general">
          <button class="faq-question">
            <span>Are your products SABS compliant?</span>
            <span class="icon-wrap">
              <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="6 9 12 15 18 9"/></svg>
            </span>
          </button>
          <div class="faq-answer">
            <div class="faq-answer-inner">
              <p>Yes. Our fencing products — including diamond mesh, game fence and barbed wire — are manufactured to SABS standards. This ensures consistent quality, correct wire gauge and proper galvanisation for Zimbabwe's climate.</p>
              <p>If you need specific SABS certification for a commercial tender, let us know and we'll provide documentation.</p>
            </div>
          </div>
        </li>

        <!-- ============ PRODUCTS ============ -->
        <li class="faq-item" data-cat="products">
          <button class="faq-question">
            <span>What is the best fence for a farm?</span>
            <span class="icon-wrap">
              <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="6 9 12 15 18 9"/></svg>
            </span>
          </button>
          <div class="faq-answer">
            <div class="faq-answer-inner">
              <p>It depends on what you're protecting against and your livestock:</p>
              <ul>
                <li><strong>Game fence</strong> — best for wildlife and large properties, heavy-duty and long-lasting</li>
                <li><strong>Field fence</strong> — ideal for cattle, goats and general livestock</li>
                <li><strong>Diamond mesh</strong> — great for crop protection and mixed-use boundaries</li>
                <li><strong>Barbed wire</strong> — affordable perimeter addition for livestock deterrence</li>
              </ul>
              <p>Tell us your farm size and livestock type and we'll recommend the right solution.</p>
            </div>
          </div>
        </li>

        <li class="faq-item" data-cat="products">
          <button class="faq-question">
            <span>How long will my fence last?</span>
            <span class="icon-wrap">
              <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="6 9 12 15 18 9"/></svg>
            </span>
          </button>
          <div class="faq-answer">
            <div class="faq-answer-inner">
              <p>Our galvanised fencing typically lasts <strong>10–20 years</strong> depending on the environment, install method and maintenance. Coastal or high-rainfall areas may reduce lifespan slightly, which is why we recommend hot-dip galvanised products for those locations.</p>
              <p>Concrete footings and proper tensioning at install time significantly extend lifespan.</p>
            </div>
          </div>
        </li>

        <li class="faq-item" data-cat="products">
          <button class="faq-question">
            <span>Do you sell fencing accessories and posts?</span>
            <span class="icon-wrap">
              <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="6 9 12 15 18 9"/></svg>
            </span>
          </button>
          <div class="faq-answer">
            <div class="faq-answer-inner">
              <p>Yes — we stock the complete fencing kit:</p>
              <ul>
                <li>Fence posts (steel, timber and concrete) in multiple heights</li>
                <li>Top wire (barbed or razor)</li>
                <li>Binding wire and tensioning wire</li>
                <li>Gates, hinges, locks and latches</li>
                <li>Concrete footings and accessories</li>
              </ul>
            </div>
          </div>
        </li>

        <li class="faq-item" data-cat="products">
          <button class="faq-question">
            <span>Can you supply steel and general hardware too?</span>
            <span class="icon-wrap">
              <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="6 9 12 15 18 9"/></svg>
            </span>
          </button>
          <div class="faq-answer">
            <div class="faq-answer-inner">
              <p>Absolutely. While fencing is our flagship division, we also supply:</p>
              <ul>
                <li><strong>Steel products</strong> — tubing, sheets, rebar, structural sections</li>
                <li><strong>General hardware</strong> — tools, fixings, gate hardware, fasteners</li>
              </ul>
              <p>If you need both fencing and building materials, we can consolidate your order and deliver together.</p>
            </div>
          </div>
        </li>

        <!-- ============ DELIVERY ============ -->
        <li class="faq-item" data-cat="delivery">
          <button class="faq-question">
            <span>Do you deliver nationwide?</span>
            <span class="icon-wrap">
              <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="6 9 12 15 18 9"/></svg>
            </span>
          </button>
          <div class="faq-answer">
            <div class="faq-answer-inner">
              <p>Yes. We deliver to all ten provinces of Zimbabwe. Delivery cost is calculated based on distance from Harare and total order weight/volume.</p>
              <p>For larger orders, we offer free or discounted delivery — ask us when requesting a quote.</p>
            </div>
          </div>
        </li>

        <li class="faq-item" data-cat="delivery">
          <button class="faq-question">
            <span>How long does delivery take?</span>
            <span class="icon-wrap">
              <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="6 9 12 15 18 9"/></svg>
            </span>
          </button>
          <div class="faq-answer">
            <div class="faq-answer-inner">
              <p>Typical delivery timelines:</p>
              <ul>
                <li>Harare metro — 1–2 business days</li>
                <li>Bulawayo, Mutare, Gweru, Masvingo — 2–4 business days</li>
                <li>Remote and rural areas — 3–7 business days</li>
              </ul>
              <p>We'll confirm an exact delivery date when you order.</p>
            </div>
          </div>
        </li>

        <!-- ============ PAYMENT ============ -->
        <li class="faq-item" data-cat="payment">
          <button class="faq-question">
            <span>What payment methods do you accept?</span>
            <span class="icon-wrap">
              <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="6 9 12 15 18 9"/></svg>
            </span>
          </button>
          <div class="faq-answer">
            <div class="faq-answer-inner">
              <p>We accept:</p>
              <ul>
                <li>Cash (USD and ZWL)</li>
                <li>EcoCash</li>
                <li>Bank transfer (USD and ZWL accounts)</li>
                <li>Point-of-sale card payments at our Harare showroom</li>
              </ul>
              <p>For large orders we can arrange staged or deposit-based payment terms.</p>
            </div>
          </div>
        </li>

        <li class="faq-item" data-cat="payment">
          <button class="faq-question">
            <span>Do you offer bulk discounts?</span>
            <span class="icon-wrap">
              <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="6 9 12 15 18 9"/></svg>
            </span>
          </button>
          <div class="faq-answer">
            <div class="faq-answer-inner">
              <p>Yes. We offer tiered discounts on bulk orders — typically from 5% for medium orders up to 15%+ for very large projects. Contact us with your quantities and we'll send a tailored quotation.</p>
            </div>
          </div>
        </li>

        <li class="faq-item" data-cat="payment">
          <button class="faq-question">
            <span>How accurate is the online quote calculator?</span>
            <span class="icon-wrap">
              <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="6 9 12 15 18 9"/></svg>
            </span>
          </button>
          <div class="faq-answer">
            <div class="faq-answer-inner">
              <p>Our calculator provides a strong working estimate based on real product pricing and standard material ratios. For most standard rectangular sites, the estimate is accurate to within <strong>±10%</strong>.</p>
              <p>Final pricing is confirmed by our team after reviewing terrain, access and specific requirements. For unusual shapes or sites with difficult access, we recommend a site visit for the most accurate quote.</p>
            </div>
          </div>
        </li>

        <!-- ============ INSTALLATION ============ -->
        <li class="faq-item" data-cat="installation">
          <button class="faq-question">
            <span>Do you install the fence, or just supply?</span>
            <span class="icon-wrap">
              <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="6 9 12 15 18 9"/></svg>
            </span>
          </button>
          <div class="faq-answer">
            <div class="faq-answer-inner">
              <p>Both. You can:</p>
              <ul>
                <li>Buy materials only and install yourself (or with your own team)</li>
                <li>Have us supply and install using our experienced installation crews</li>
              </ul>
              <p>Our install teams work nationwide. Pricing depends on perimeter length, terrain and access.</p>
            </div>
          </div>
        </li>

        <li class="faq-item" data-cat="installation">
          <button class="faq-question">
            <span>How long does installation take?</span>
            <span class="icon-wrap">
              <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="6 9 12 15 18 9"/></svg>
            </span>
          </button>
          <div class="faq-answer">
            <div class="faq-answer-inner">
              <p>Installation timelines vary with site size and complexity:</p>
              <ul>
                <li>Residential (100–200m) — 1–2 days</li>
                <li>Commercial (200–500m) — 2–4 days</li>
                <li>Farm perimeter (500m+) — 4–10 days</li>
              </ul>
              <p>We'll give you a firm timeline as part of your quotation.</p>
            </div>
          </div>
        </li>

      </ul>

      <!-- No results -->
      <div class="no-results" id="noResults">
        <div class="icon">
          <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
        </div>
        <h3>No matching questions</h3>
        <p>We couldn't find an answer matching that. Try a different search, or ask us directly.</p>
        <a href="contact.php" class="btn btn-amber">
          Ask Us Directly
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M5 12h14M13 5l7 7-7 7"/></svg>
        </a>
      </div>

    </main>

  </div>
</div>

<!-- ===================== STILL HAVE QUESTIONS ===================== -->
<section class="still-questions">
  <div class="container">
    <div class="sq-grid">
      <div class="sq-left reveal">
        <span class="eyebrow">Still Have Questions?</span>
        <h2>We're One Message Away</h2>
        <p>Whatever you need — a product recommendation, a bulk quote, or just advice on the right fence for your site — our team is ready.</p>
      </div>

      <div class="sq-options">
        <a href="contact.php" class="sq-card reveal">
          <div class="ico">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
          </div>
          <div>
            <strong>Send a message</strong>
            <span>We reply within 24 hours on business days.</span>
          </div>
        </a>

        <a href="tel:+263784689857" class="sq-card reveal reveal-d1">
          <div class="ico">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
          </div>
          <div>
            <strong>Call us</strong>
            <span>Mon–Fri 8am–5pm · Sat 8am–12pm</span>
          </div>
        </a>

        <a href="https://wa.me/263784689857" target="_blank" rel="noopener" class="sq-card whatsapp reveal reveal-d2">
          <div class="ico">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M20.52 3.48A11.94 11.94 0 0 0 12.04 0C5.5 0 .2 5.3.2 11.84c0 2.09.55 4.13 1.6 5.93L0 24l6.4-1.68a11.83 11.83 0 0 0 5.64 1.44h.01c6.53 0 11.84-5.3 11.84-11.84 0-3.16-1.23-6.13-3.37-8.44z"/></svg>
          </div>
          <div>
            <strong>WhatsApp us</strong>
            <span>Fastest response — chat now.</span>
          </div>
        </a>

        <a href="calculator.php" class="sq-card reveal reveal-d3">
          <div class="ico">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="4" y="2" width="16" height="20" rx="2"/><line x1="8" y1="6" x2="16" y2="6"/><line x1="8" y1="10" x2="16" y2="10"/><line x1="8" y1="14" x2="12" y2="14"/></svg>
          </div>
          <div>
            <strong>Try the calculator</strong>
            <span>Get an instant estimate online.</span>
          </div>
        </a>
      </div>
    </div>
  </div>
</section>

<!-- ===================== CTA BAND ===================== -->
<section class="section section-tight">
  <div class="container">
    <div class="cta-band reveal">
      <h2>Ready to Get Started?</h2>
      <p>Get an instant estimate with our online calculator, or talk to our team directly.</p>
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
