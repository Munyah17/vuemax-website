/* ============================================================
   VUEMAX — SHARED JS
   Drawer, header state, footer year, scroll reveal.
   Loaded with `defer` on every page.
   ============================================================ */
(function(){
  'use strict';

  /* ---------- Dynamic footer year ---------- */
  var yearEl = document.getElementById('footerYear');
  if (yearEl) yearEl.textContent = new Date().getFullYear();

  /* ---------- Mobile drawer ---------- */
  var hamburger = document.getElementById('hamburger');
  var drawer    = document.getElementById('drawer');
  var backdrop  = document.getElementById('drawerBackdrop');
  var closeBtn  = document.getElementById('drawerClose');

  function openDrawer(){
    drawer.classList.add('open');
    backdrop.classList.add('open');
    document.body.classList.add('drawer-open');
    drawer.setAttribute('aria-hidden', 'false');
    hamburger.setAttribute('aria-expanded', 'true');
  }
  function closeDrawer(){
    drawer.classList.remove('open');
    backdrop.classList.remove('open');
    document.body.classList.remove('drawer-open');
    drawer.setAttribute('aria-hidden', 'true');
    hamburger.setAttribute('aria-expanded', 'false');
  }

  if (hamburger) hamburger.addEventListener('click', openDrawer);
  if (closeBtn)  closeBtn.addEventListener('click', closeDrawer);
  if (backdrop)  backdrop.addEventListener('click', closeDrawer);

  document.addEventListener('keydown', function(e){
    if (e.key === 'Escape' && drawer && drawer.classList.contains('open')) closeDrawer();
  });

  if (drawer) {
    drawer.querySelectorAll('a').forEach(function(a){
      a.addEventListener('click', function(){ closeDrawer(); });
    });
  }

  /* ---------- Header shadow on scroll ---------- */
  var header = document.querySelector('.site-header');
  if (header) {
    var onScroll = function(){
      header.classList.toggle('scrolled', window.scrollY > 8);
    };
    window.addEventListener('scroll', onScroll, { passive: true });
    onScroll();
  }

  /* ---------- Scroll reveal ---------- */
  var revealEls = document.querySelectorAll('.reveal');
  if (revealEls.length) {
    if ('IntersectionObserver' in window) {
      var io = new IntersectionObserver(function(entries){
        entries.forEach(function(entry){
          if (entry.isIntersecting) {
            entry.target.classList.add('in');
            io.unobserve(entry.target);
          }
        });
      }, { threshold: 0.12, rootMargin: '0px 0px -30px 0px' });
      revealEls.forEach(function(el){ io.observe(el); });
    } else {
      revealEls.forEach(function(el){ el.classList.add('in'); });
    }
  }
})();

/* ============================================================
   SHARED: add a product to the sessionStorage quote.
   Used by product-card "Get Quote" / "Buy Now" buttons and by
   product-detail.php. Returns the updated quote object.
   ============================================================ */
window.vuemaxAddToQuote = function(item, project){
  var quote = null;
  try { quote = JSON.parse(sessionStorage.getItem('vuemax_quote') || 'null'); } catch(e){}
  if (quote && Array.isArray(quote.items)){
    quote.items.push(item);
    delete quote.saved; // contents changed — save again on quote-results
  } else {
    quote = {
      ref: 'VX-PD-' + Math.floor(1000 + Math.random() * 8999),
      source: 'product',
      createdAt: new Date().toISOString(),
      customer: {},
      project: project || {},
      options: {},
      items: [item]
    };
  }
  try { sessionStorage.setItem('vuemax_quote', JSON.stringify(quote)); } catch(e){}
  return quote;
};

/* Product-card buttons: .js-buy (add + go to quote) and .js-quote (add + confirm) */
(function(){
  function cardItem(btn){
    var unit = parseFloat(btn.getAttribute('data-price'));
    if (isNaN(unit)) unit = null;
    var u = btn.getAttribute('data-unit') || 'item';
    return {
      name: btn.getAttribute('data-name') || 'Product',
      spec: 'Per ' + u,
      qty: '1 × ' + u,
      unit: unit,
      total: unit === null ? null : Math.round(unit * 100) / 100
    };
  }
  document.addEventListener('click', function(e){
    var buy = e.target.closest('.js-buy');
    var quo = e.target.closest('.js-quote');
    var btn = buy || quo;
    if (!btn) return;
    e.preventDefault();
    var item = cardItem(btn);
    window.vuemaxAddToQuote(item, { type: btn.getAttribute('data-slug') || '', typeName: item.name });
    if (buy){
      btn.innerHTML = '✓ Added — opening quote…';
      setTimeout(function(){ window.location.href = btn.getAttribute('data-quote-url') || 'quote-results.php'; }, 400);
    } else {
      var orig = btn.innerHTML;
      btn.innerHTML = '✓ Added to quote';
      btn.classList.add('added');
      setTimeout(function(){ btn.innerHTML = orig; btn.classList.remove('added'); }, 1600);
    }
  });
})();
