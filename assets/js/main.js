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
