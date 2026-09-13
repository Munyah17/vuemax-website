<?php
require_once __DIR__ . '/includes/config.php';

$pageTitle = 'Your Quote — Vuemax';
$pageDesc  = 'Your instant fencing quote from Vuemax — bill of quantities and estimated pricing.';
$active    = '';

$extraCss = <<<'CSS'
.quote-results{padding:56px 0;}
.qr-wrap{max-width:820px;margin:0 auto;}
.qr-head{text-align:center;margin-bottom:32px;}
.qr-head .kicker{
  display:inline-block;font-size:11px;letter-spacing:.16em;text-transform:uppercase;
  color:var(--amber-hover);font-weight:700;margin-bottom:10px;
}
.qr-head h1{
  font-family:'Playfair Display',serif;
  font-size:clamp(26px,5.5vw,38px);color:var(--navy);
  line-height:1.15;margin-bottom:10px;
}
.qr-head p{color:var(--muted);font-size:14.5px;}
.qr-card{
  background:var(--white);border:1px solid var(--border);
  border-radius:var(--radius-card);
  box-shadow:var(--shadow-lift);
  overflow:hidden;margin-bottom:22px;
}
.qr-card-head{
  background:linear-gradient(140deg,var(--navy) 0%,var(--navy-deep) 100%);
  color:var(--white);padding:22px 26px;
  display:flex;flex-wrap:wrap;gap:14px;align-items:center;justify-content:space-between;
}
.qr-ref{font-size:13px;color:rgba(255,255,255,.75);}
.qr-ref strong{display:block;font-size:19px;color:var(--amber);letter-spacing:.04em;font-family:'Playfair Display',serif;}
.qr-date{font-size:12.5px;color:rgba(255,255,255,.65);text-align:right;}
.qr-body{padding:24px 26px;}
.qr-meta{
  display:grid;grid-template-columns:1fr;gap:14px;
  padding-bottom:18px;margin-bottom:18px;
  border-bottom:1px solid var(--border);
}
.qr-meta .m{font-size:13px;}
.qr-meta .m span{display:block;color:var(--muted);font-size:11px;letter-spacing:.08em;text-transform:uppercase;font-weight:600;margin-bottom:2px;}
.qr-meta .m strong{color:var(--navy);font-weight:600;}
.qr-table{width:100%;border-collapse:collapse;font-size:13.5px;}
.qr-table th{
  text-align:left;font-size:11px;letter-spacing:.08em;text-transform:uppercase;
  color:var(--muted);font-weight:600;padding:8px 0;
  border-bottom:2px solid var(--navy);
}
.qr-table th:last-child,.qr-table td:last-child{text-align:right;}
.qr-table th:nth-child(2),.qr-table td:nth-child(2){text-align:center;white-space:nowrap;}
.qr-table td{padding:10px 0;border-bottom:1px dashed var(--border);vertical-align:top;}
.qr-table .qty{color:var(--muted);}
.qr-total-row{
  display:flex;justify-content:space-between;align-items:baseline;
  padding-top:16px;margin-top:4px;
  border-top:2px solid var(--navy);
  font-weight:700;color:var(--navy);font-size:15px;
}
.qr-total-row .amt{font-family:'Playfair Display',serif;font-size:26px;}
.qr-note{
  font-size:12.5px;color:var(--muted);line-height:1.6;
  background:var(--amber-soft);border:1px solid #F3E2B0;
  border-radius:var(--radius-input);padding:14px 16px;margin-top:20px;
}
.qr-actions{display:flex;flex-direction:column;gap:10px;margin-top:24px;}
.qr-empty{
  text-align:center;padding:60px 20px;
}
.qr-empty h2{
  font-family:'Playfair Display',serif;color:var(--navy);
  font-size:24px;margin-bottom:10px;
}
.qr-empty p{color:var(--muted);font-size:14.5px;margin-bottom:22px;}
@media (min-width:640px){
  .qr-meta{grid-template-columns:repeat(3,1fr);}
  .qr-actions{flex-direction:row;justify-content:center;}
}
@media print{
  .topbar,.site-header,.drawer,.drawer-backdrop,.site-footer,.qr-actions{display:none !important;}
  body{background:#fff;}
  .qr-card{box-shadow:none;}
}
CSS;

$extraJs = <<<'JS'
/* ---------- Quote results: render from sessionStorage ---------- */
(function(){
  function esc(s){
    return String(s == null ? '' : s).replace(/[&<>"']/g, function(c){
      return {'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c];
    });
  }
  function money(n){ return '$' + Number(n || 0).toLocaleString('en-US', {minimumFractionDigits:0, maximumFractionDigits:2}); }

  var raw = null;
  try { raw = sessionStorage.getItem('vuemax_quote'); } catch(e){}

  var quote = null;
  try { quote = raw ? JSON.parse(raw) : null; } catch(e){}

  var wrap = document.getElementById('qrWrap');
  if (!wrap) return;

  if (!quote || !Array.isArray(quote.items) || !quote.items.length){
    wrap.innerHTML =
      '<div class="qr-empty">'
      + '<h2>No Quote Found</h2>'
      + '<p>Generate a quote with the calculator first, then come back here.</p>'
      + '<a href="calculator.php" class="btn btn-amber">Open Calculator</a>'
      + '</div>';
    return;
  }

  var created = '';
  try { created = new Date(quote.createdAt).toLocaleString('en-ZW', {dateStyle:'medium', timeStyle:'short'}); } catch(e){}

  var proj = quote.project || {};
  var projBits = [];
  if (proj.perimeter) projBits.push(esc(proj.perimeter) + ' m perimeter');
  if (proj.height)    projBits.push(esc(proj.height) + ' m high');
  if (proj.typeName)  projBits.push(esc(proj.typeName));

  var rows = '';
  var total = 0;
  quote.items.forEach(function(it){
    var line = Number(it.total || 0);
    total += line;
    rows += '<tr>'
      + '<td>' + esc(it.name) + (it.spec ? '<br><span class="qty">' + esc(it.spec) + '</span>' : '') + '</td>'
      + '<td class="qty">' + esc(it.qty) + '</td>'
      + '<td><strong>' + money(line) + '</strong></td>'
      + '</tr>';
  });

  wrap.innerHTML =
    '<div class="qr-card">'
    + '<div class="qr-card-head">'
    +   '<div class="qr-ref">Quote Reference<strong>' + esc(quote.ref || 'VX-QUOTE') + '</strong></div>'
    +   '<div class="qr-date">' + esc(created) + '</div>'
    + '</div>'
    + '<div class="qr-body">'
    +   '<div class="qr-meta">'
    +     '<div class="m"><span>Customer</span><strong>' + esc((quote.customer && quote.customer.name) || '—') + '</strong></div>'
    +     '<div class="m"><span>Contact</span><strong>' + esc((quote.customer && quote.customer.contact) || '—') + '</strong></div>'
    +     '<div class="m"><span>Project</span><strong>' + (projBits.join(' · ') || '—') + '</strong></div>'
    +   '</div>'
    +   '<table class="qr-table">'
    +   '<thead><tr><th>Item</th><th>Qty</th><th>Total</th></tr></thead>'
    +   '<tbody>' + rows + '</tbody>'
    +   '</table>'
    +   '<div class="qr-total-row"><span>Estimated Total</span><span class="amt">' + money(total) + '</span></div>'
    +   '<div class="qr-note">This is an estimate based on current list prices. Final pricing is confirmed after a site assessment. Quotes are valid for 14 days.</div>'
    + '</div>'
    + '</div>';
})();
JS;

require __DIR__ . '/includes/header.php';
?>

<section class="quote-results">
  <div class="container qr-wrap">
    <div class="qr-head">
      <span class="kicker">Instant Estimate</span>
      <h1>Your Fencing Quote</h1>
      <p>Review your bill of quantities below. Save or print it, or send it to our team to confirm.</p>
    </div>

    <div id="qrWrap">
      <div class="qr-empty">
        <h2>Loading your quote…</h2>
      </div>
    </div>

    <div class="qr-actions">
      <button class="btn btn-navy" onclick="window.print()">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect x="6" y="14" width="12" height="8"/></svg>
        Print / Save PDF
      </button>
      <a href="contact.php" class="btn btn-amber">Send to Vuemax</a>
      <a href="calculator.php" class="btn btn-outline-navy">New Quote</a>
    </div>
  </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
