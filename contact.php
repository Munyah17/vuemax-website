<?php
require_once __DIR__ . '/includes/config.php';

$pageTitle = 'Contact Us Vuemax | Fencing, Steel & Hardware Zimbabwe';
$pageDesc = 'Get in touch with Vuemax Investments. Call, email, WhatsApp or send us a message we respond within 24 hours.';
$active = 'contact';

$extraCss = <<<'CSS'
/* ============================================================
 CONTACT page-specific styles
 ============================================================ */
.btn-whatsapp{background:#25D366;color:#fff;}
.btn-whatsapp:hover{background:#1DAA54;transform:translateY(-1px);}

/* ---------- PAGE HERO ---------- */
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

/* ---------- CONTACT METHOD CARDS ---------- */
.contact-cards-section{
 padding:56px 0 0;
}
.contact-cards-grid{
 display:grid;grid-template-columns:repeat(4,1fr);gap:20px;
}
.contact-card{
 background:var(--white);border:1px solid var(--border);
 border-radius:var(--radius-card);padding:24px;
 transition:.2s;box-shadow:var(--shadow-card);
 text-align:center;display:block;
}
.contact-card:hover{transform:translateY(-4px);box-shadow:var(--shadow-lift);}
.contact-card .ico{
 width:52px;height:52px;border-radius:14px;
 background:#FEF6E4;color:var(--navy);
 display:flex;align-items:center;justify-content:center;
 margin:0 auto 14px;
}
.contact-card.whatsapp .ico{background:#DCFCE7;color:#15803D;}
.contact-card h3{
 font-size:15px;font-weight:600;color:var(--navy);
 margin-bottom:6px;letter-spacing:.01em;
}
.contact-card p{
 font-size:13px;color:var(--muted);
 line-height:1.55;margin-bottom:12px;
}
.contact-card .link{
 font-size:13px;font-weight:600;color:var(--navy);
 display:inline-flex;align-items:center;gap:6px;
 padding-top:12px;border-top:1px solid var(--border);
 width:100%;justify-content:center;
}
.contact-card:hover .link svg{transform:translateX(3px);}
.contact-card .link svg{transition:.2s;}

/* ---------- MAIN LAYOUT (form + map) ---------- */
.contact-layout{
 display:grid;grid-template-columns:1.15fr 1fr;
 gap:32px;padding:56px 0 72px;align-items:start;
}

/* ---- Form panel ---- */
.form-panel{
 background:var(--white);border:1px solid var(--border);
 border-radius:var(--radius-card);box-shadow:var(--shadow-card);
 overflow:hidden;
}
.form-head{
 padding:28px 32px 22px;border-bottom:1px solid var(--border);
}
.form-head h2{
 font-family:'Playfair Display',serif;
 font-size:24px;font-weight:700;color:var(--navy);
 line-height:1.2;margin-bottom:6px;
}
.form-head p{
 font-size:14px;color:var(--muted);line-height:1.55;
 max-width:520px;
}

.form-body{padding:28px 32px 32px;}

.form-grid{
 display:grid;grid-template-columns:1fr 1fr;gap:18px;
 margin-bottom:18px;
}
.form-grid.single{grid-template-columns:1fr;}

.field{display:flex;flex-direction:column;}
.field label{
 font-size:13px;font-weight:600;color:var(--navy);
 margin-bottom:8px;letter-spacing:.01em;
}
.field label .req{color:#DC2626;margin-left:2px;}
.field .hint{font-size:12px;color:var(--muted);margin-top:6px;line-height:1.5;}

.input-wrap{position:relative;display:flex;align-items:center;}
.input-wrap input,
.input-wrap select,
.field textarea{
 width:100%;padding:12px 14px;
 border:1.5px solid var(--border);border-radius:var(--radius-input);
 font-size:14.5px;color:var(--navy);background:var(--white);
 transition:.15s;font-family:inherit;
}
.input-wrap input:focus,
.input-wrap select:focus,
.field textarea:focus{
 outline:none;border-color:var(--navy);
 box-shadow:0 0 0 3px rgba(14,39,69,.08);
}
.input-wrap select{
 padding-right:38px;appearance:none;-webkit-appearance:none;
 background-image:url("data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%236B7280' stroke-width='2.5'><polyline points='6 9 12 15 18 9'/></svg>");
 background-repeat:no-repeat;background-position:right 14px center;
 cursor:pointer;
}
.field textarea{
 resize:vertical;min-height:120px;line-height:1.6;
}
.input-wrap .flag{
 position:absolute;left:14px;
 font-size:14px;pointer-events:none;font-weight:500;
 color:var(--muted);
}
.input-wrap.with-flag input{padding-left:52px;}

/* Checkbox */
.checkbox-wrap{
 display:flex;gap:10px;align-items:flex-start;
 margin-bottom:22px;cursor:pointer;
 font-size:13px;color:var(--text);line-height:1.55;
}
.checkbox-wrap input{position:absolute;opacity:0;pointer-events:none;}
.checkbox-wrap .chk{
 width:18px;height:18px;border-radius:5px;
 border:1.5px solid var(--border);background:var(--white);
 flex-shrink:0;margin-top:2px;
 display:flex;align-items:center;justify-content:center;
 transition:.15s;
}
.checkbox-wrap .chk svg{opacity:0;transition:.15s;}
.checkbox-wrap input:checked ~ .chk{background:var(--navy);border-color:var(--navy);}
.checkbox-wrap input:checked ~ .chk svg{opacity:1;}

/* Submit row */
.submit-row{
 display:flex;justify-content:space-between;align-items:center;
 gap:16px;padding-top:22px;margin-top:8px;
 border-top:1px solid var(--border);flex-wrap:wrap;
}
.submit-note{
 display:flex;align-items:center;gap:8px;
 font-size:12.5px;color:var(--muted);
 line-height:1.5;flex:1;min-width:200px;
}
.submit-note svg{color:var(--green-text);flex-shrink:0;}
.submit-row .btn{min-width:180px;justify-content:center;}

/* ---- Right rail (info + map) ---- */
.rail{display:flex;flex-direction:column;gap:20px;position:sticky;top:96px;}

/* Info block */
.info-block{
 background:var(--white);border:1px solid var(--border);
 border-radius:var(--radius-card);box-shadow:var(--shadow-card);
 overflow:hidden;
}
.info-block-head{
 padding:20px 24px;border-bottom:1px solid var(--border);
 display:flex;align-items:center;gap:12px;
}
.info-block-head .ico{
 width:36px;height:36px;border-radius:10px;
 background:#FEF6E4;color:var(--navy);flex-shrink:0;
 display:flex;align-items:center;justify-content:center;
}
.info-block-head h3{
 font-size:15px;font-weight:700;color:var(--navy);
 letter-spacing:.01em;
}
.info-list{padding:8px 0;}
.info-item{
 display:flex;gap:14px;align-items:flex-start;
 padding:14px 24px;border-bottom:1px solid var(--border);
}
.info-item:last-child{border-bottom:none;}
.info-item .ico{
 width:32px;height:32px;border-radius:8px;
 background:#FEF6E4;color:var(--navy);flex-shrink:0;
 display:flex;align-items:center;justify-content:center;
 margin-top:2px;
}
.info-item .label{
 font-size:11px;font-weight:700;color:var(--muted);
 letter-spacing:.06em;text-transform:uppercase;
 margin-bottom:3px;
}
.info-item .value{
 font-size:14px;color:var(--navy);font-weight:600;
 line-height:1.45;word-break:break-word;
}
.info-item .value a:hover{color:var(--amber);}
.info-item .sub{
 font-size:12px;color:var(--muted);margin-top:3px;
 font-weight:400;
}

/* Hours block */
.hours-list{list-style:none;padding:8px 0;}
.hours-row{
 display:flex;justify-content:space-between;align-items:center;
 padding:11px 24px;font-size:13.5px;
 border-bottom:1px dashed var(--border);
}
.hours-row:last-child{border-bottom:none;}
.hours-row .day{color:var(--navy);font-weight:500;}
.hours-row .time{color:var(--muted);font-weight:500;}
.hours-row.today{background:#FEF6E4;}
.hours-row.today .day,
.hours-row.today .time{color:var(--navy);font-weight:700;}
.hours-row .today-tag{
 font-size:10px;font-weight:700;color:var(--navy);
 background:var(--amber);padding:2px 6px;border-radius:4px;
 letter-spacing:.06em;margin-left:8px;
}

/* Map */
.map-block{
 background:var(--white);border:1px solid var(--border);
 border-radius:var(--radius-card);overflow:hidden;
 box-shadow:var(--shadow-card);
}
.map-embed{
 width:100%;height:280px;border:none;display:block;
 filter:grayscale(.15) contrast(1.05);
}
.map-footer{
 padding:14px 20px;display:flex;justify-content:space-between;
 align-items:center;gap:12px;flex-wrap:wrap;
 border-top:1px solid var(--border);
}
.map-footer .addr{
 font-size:13px;color:var(--navy);font-weight:500;
 display:flex;align-items:center;gap:8px;
}
.map-footer .addr svg{color:var(--amber);flex-shrink:0;}
.map-footer a.directions{
 font-size:12.5px;font-weight:600;color:var(--navy);
 display:inline-flex;align-items:center;gap:5px;
 padding:6px 12px;border:1px solid var(--border);
 border-radius:6px;transition:.15s;
}
.map-footer a.directions:hover{border-color:var(--navy);background:var(--bg);}

/* ---------- FAQ TEASER ---------- */
.faq-teaser{
 background:var(--white);border-top:1px solid var(--border);
 border-bottom:1px solid var(--border);padding:56px 0;
}
.faq-teaser-grid{
 display:grid;grid-template-columns:1fr 2fr;gap:48px;
 align-items:center;
}
.faq-teaser h2{
 font-family:'Playfair Display',serif;
 font-size:clamp(22px,2.4vw,30px);font-weight:700;
 color:var(--navy);line-height:1.2;margin-bottom:10px;
}
.faq-teaser p{
 font-size:14px;color:var(--muted);line-height:1.6;
 margin-bottom:18px;
}
.faq-mini-list{
 display:grid;grid-template-columns:1fr 1fr;gap:12px;
}
.faq-mini-item{
 background:var(--bg);border-radius:var(--radius-card);
 padding:14px 18px;font-size:13.5px;color:var(--navy);
 font-weight:500;line-height:1.5;
 display:flex;align-items:center;gap:10px;
 transition:.15s;
}
.faq-mini-item:hover{background:#EEF2F7;}
.faq-mini-item::before{
 content:"?";width:22px;height:22px;border-radius:50%;
 background:var(--amber);color:var(--navy);
 font-size:12px;font-weight:700;
 display:flex;align-items:center;justify-content:center;
 flex-shrink:0;
}

/* ---------- CTA WRAP ---------- */
.cta-wrap{padding:72px 24px 0;}

/* Success message */
.form-success{
 display:none;padding:32px;
 background:var(--green-bg);border:1px solid var(--green-line);
 border-radius:var(--radius-card);margin:28px 32px;
 align-items:flex-start;gap:16px;
}
.form-success.active{display:flex;}
.form-success .ico{
 width:44px;height:44px;border-radius:50%;
 background:var(--green-text);color:#fff;flex-shrink:0;
 display:flex;align-items:center;justify-content:center;
}
.form-success h4{
 font-size:15px;font-weight:700;color:var(--green-text);
 margin-bottom:4px;
}
.form-success p{
 font-size:13.5px;color:#14532D;line-height:1.55;
}

/* Responsive */
@media (max-width:1024px){
 .contact-layout{grid-template-columns:1fr;gap:24px;}
 .rail{position:static;}
 .contact-cards-grid{grid-template-columns:repeat(2,1fr);}
 .faq-teaser-grid{grid-template-columns:1fr;gap:24px;}
}
@media (max-width:980px){
 .page-hero .script-note{display:none;}
}
@media (max-width:600px){
 .form-grid{grid-template-columns:1fr;}
 .contact-cards-grid{grid-template-columns:1fr;}
 .faq-mini-list{grid-template-columns:1fr;}
 .form-head{padding:22px 22px 18px;}
 .form-body{padding:22px;}
 .submit-row{flex-direction:column;align-items:stretch;}
 .submit-row .btn{width:100%;}
}
CSS;

$extraJs = <<<'JS'
/* ============================================================
 CONTACT PAGE FORM + HOURS HIGHLIGHT
 ============================================================ */
(function(){

 /* ---- Highlight today's hours row ---- */
 const today = new Date().getDay(); // 0=Sunday, 1=Monday...
 const rows = document.querySelectorAll('#hoursList .hours-row');
 rows.forEach(r => {
 if (parseInt(r.dataset.day, 10) === today){
 r.classList.add('today');
 const dayEl = r.querySelector('.day');
 if (dayEl){
 const tag = document.createElement('span');
 tag.className = 'today-tag';
 tag.textContent = 'TODAY';
 dayEl.appendChild(tag);
 }
 }
 });

 /* ---- Form submission: POST to api/contact-send.php ---- */
 const form = document.getElementById('contactForm');
 const success = document.getElementById('formSuccess');
 const submitBtn = document.getElementById('submitBtn');

 form.addEventListener('submit', function(e){
 e.preventDefault();

 // Basic validation
 const name = document.getElementById('fullName').value.trim();
 const phone = document.getElementById('phone').value.trim();
 const email = document.getElementById('email').value.trim();
 const subject = document.getElementById('subject').value;
 const message = document.getElementById('message').value.trim();
 const consent = document.getElementById('consent').checked;

 if (!name || !phone || !subject || !message){
 alert('Please fill in all required fields (name, phone, subject, message).');
 return;
 }
 if (!consent){
 alert('Please agree to be contacted so we can respond to your enquiry.');
 return;
 }

 // Visual feedback
 const orig = submitBtn.innerHTML;
 submitBtn.innerHTML = 'Sending...';
 submitBtn.disabled = true;
 submitBtn.style.opacity = '.65';

 fetch('api/contact-send.php', {
 method: 'POST',
 headers: {'Content-Type':'application/json'},
 body: JSON.stringify({ name, phone, email, subject, message, consent })
 })
 .then(r => r.json())
 .then(d => {
 if (d && d.ok){
 form.style.display = 'none';
 success.classList.add('active');
 window.scrollTo({ top: success.offsetTop - 120, behavior: 'smooth' });
 } else {
 alert((d && d.message) ? d.message : 'Something went wrong. Please try again or WhatsApp us.');
 }
 })
 .catch(() => {
 alert('Could not send your message. Please try again or contact us on WhatsApp.');
 })
 .finally(() => {
 submitBtn.innerHTML = orig;
 submitBtn.disabled = false;
 submitBtn.style.opacity = '';
 });
 });

})();
JS;

require __DIR__ . '/includes/header.php';
?>

<!-- ===================== PAGE HERO ===================== -->
<section class="page-hero" style="--hero-img:url('<?= e(site_image('contact-hero', 'https://images.unsplash.com/photo-1500382017468-9049fed747ef?auto=format&fit=crop&w=1920&q=80')) ?>')">
 <div class="container">
 <div class="breadcrumbs">
 <a href="index.php">Home</a>
 <span class="sep">›</span>
 <span>Contact</span>
 </div>
 <h1>Get in <span class="accent">Touch</span></h1>
 <p>We're here to help with quotes, product information or any questions. Let's build a safer Zimbabwe together one fence at a time.</p>
 </div>
 <div class="script-note">Fast Response<br>Within 24 Hours</div>
</section>

<!-- ===================== CONTACT METHOD CARDS ===================== -->
<section class="contact-cards-section">
 <div class="container">
 <div class="contact-cards-grid">

 <a href="tel:+263784689857" class="contact-card">
 <div class="ico">
 <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
 </div>
 <h3>Call Us</h3>
 <p>Mon Fri, 8am 5pm<br>Sat, 8am 12pm</p>
 <span class="link">
 +263 784 689 857
 <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M5 12h14M13 5l7 7-7 7"/></svg>
 </span>
 </a>

 <a href="mailto:sales@vuemax.co.zw" class="contact-card">
 <div class="ico">
 <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
 </div>
 <h3>Email Us</h3>
 <p>For quotes, product info<br>and general enquiries</p>
 <span class="link">
 sales@vuemax.co.zw
 <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M5 12h14M13 5l7 7-7 7"/></svg>
 </span>
 </a>

 <a href="https://wa.me/263784689857" target="_blank" rel="noopener" class="contact-card whatsapp">
 <div class="ico">
 <svg width="22" height="22" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
 </div>
 <h3>WhatsApp</h3>
 <p>Fastest response<br>Chat with our team</p>
 <span class="link">
 Message Us
 <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M5 12h14M13 5l7 7-7 7"/></svg>
 </span>
 </a>

 <a href="#map" class="contact-card">
 <div class="ico">
 <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
 </div>
 <h3>Visit Showroom</h3>
 <p>103 Willowvale Rd<br>Harare, Zimbabwe</p>
 <span class="link">
 Get Directions
 <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M5 12h14M13 5l7 7-7 7"/></svg>
 </span>
 </a>

 </div>
 </div>
</section>

<!-- ===================== MAIN LAYOUT ===================== -->
<div class="container">
 <div class="contact-layout">

 <!-- ============ FORM ============ -->
 <main>
 <div class="form-panel">
 <div class="form-head">
 <h2>Send Us a Message</h2>
 <p>Fill in your details and we'll get back to you within 24 hours. For urgent enquiries, call or WhatsApp us directly.</p>
 </div>

 <!-- Success message -->
 <div class="form-success" id="formSuccess">
 <div class="ico">
 <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg>
 </div>
 <div>
 <h4>Message Sent Successfully</h4>
 <p>Thank you for getting in touch. Our team will respond within 24 hours during business days. For urgent matters, WhatsApp us on 0784 689 857.</p>
 </div>
 </div>

 <form class="form-body" id="contactForm" novalidate>

 <div class="form-grid">
 <div class="field">
 <label>Full Name <span class="req">*</span></label>
 <div class="input-wrap">
 <input type="text" id="fullName" name="name" placeholder="e.g. John Moyo" required>
 </div>
 </div>
 <div class="field">
 <label>Phone Number <span class="req">*</span></label>
 <div class="input-wrap with-flag">
 <span class="flag">+263</span>
 <input type="tel" id="phone" name="phone" placeholder="784 000 000" required>
 </div>
 </div>
 </div>

 <div class="form-grid">
 <div class="field">
 <label>Email Address</label>
 <div class="input-wrap">
 <input type="email" id="email" name="email" placeholder="you@example.com">
 </div>
 </div>
 <div class="field">
 <label>Subject <span class="req">*</span></label>
 <div class="input-wrap">
 <select id="subject" name="subject" required>
 <option value="">Choose a topic...</option>
 <option value="quote">Request a Quote</option>
 <option value="product">Product Information</option>
 <option value="installation">Installation Enquiry</option>
 <option value="bulk">Bulk Order / Wholesale</option>
 <option value="support">Existing Order Support</option>
 <option value="other">Other</option>
 </select>
 </div>
 </div>
 </div>

 <div class="form-grid single">
 <div class="field">
 <label>Message <span class="req">*</span></label>
 <div class="input-wrap">
 <textarea id="message" name="message" placeholder="Tell us about your project or enquiry..." required></textarea>
 </div>
 <span class="hint">Include location, approximate dimensions or quantities where relevant.</span>
 </div>
 </div>

 <label class="checkbox-wrap">
 <input type="checkbox" id="consent" checked>
 <span class="chk">
 <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg>
 </span>
 I agree to be contacted by Vuemax about my enquiry.
 </label>

 <div class="submit-row">
 <div class="submit-note">
 <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
 Your details are kept private. No spam, ever.
 </div>
 <button type="submit" class="btn btn-amber" id="submitBtn">
 Send Message
 <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M5 12h14M13 5l7 7-7 7"/></svg>
 </button>
 </div>

 </form>
 </div>
 </main>

 <!-- ============ RAIL: INFO + HOURS + MAP ============ -->
 <aside class="rail">

 <!-- Contact info -->
 <div class="info-block">
 <div class="info-block-head">
 <div class="ico">
 <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>
 </div>
 <h3>Contact Information</h3>
 </div>

 <div class="info-list">

 <div class="info-item">
 <div class="ico">
 <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
 </div>
 <div>
 <div class="label">Address</div>
 <div class="value">103 Willowvale Rd, Harare, Zimbabwe</div>
 </div>
 </div>

 <div class="info-item">
 <div class="ico">
 <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
 </div>
 <div>
 <div class="label">Phone</div>
 <div class="value"><a href="tel:+263784689857">+263 784 689 857</a></div>
 <div class="sub">Also on WhatsApp</div>
 </div>
 </div>

 <div class="info-item">
 <div class="ico">
 <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
 </div>
 <div>
 <div class="label">Email</div>
 <div class="value"><a href="mailto:sales@vuemax.co.zw">sales@vuemax.co.zw</a></div>
 </div>
 </div>

 </div>
 </div>

 <!-- Business hours -->
 <div class="info-block">
 <div class="info-block-head">
 <div class="ico">
 <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
 </div>
 <h3>Business Hours</h3>
 </div>

 <ul class="hours-list" id="hoursList">
 <li class="hours-row" data-day="1"><span class="day">Monday</span><span class="time">8:00 AM 5:00 PM</span></li>
 <li class="hours-row" data-day="2"><span class="day">Tuesday</span><span class="time">8:00 AM 5:00 PM</span></li>
 <li class="hours-row" data-day="3"><span class="day">Wednesday</span><span class="time">8:00 AM 5:00 PM</span></li>
 <li class="hours-row" data-day="4"><span class="day">Thursday</span><span class="time">8:00 AM 5:00 PM</span></li>
 <li class="hours-row" data-day="5"><span class="day">Friday</span><span class="time">8:00 AM 5:00 PM</span></li>
 <li class="hours-row" data-day="6"><span class="day">Saturday</span><span class="time">8:00 AM 12:00 PM</span></li>
 <li class="hours-row" data-day="0"><span class="day">Sunday</span><span class="time">Closed</span></li>
 </ul>
 </div>

 <!-- Map -->
 <div class="map-block" id="map">
 <iframe
 class="map-embed"
 src="https://www.google.com/maps?q=103+Willowvale+Road+Harare+Zimbabwe&output=embed"
 loading="lazy"
 referrerpolicy="no-referrer-when-downgrade"
 title="Vuemax Location Map"></iframe>
 <div class="map-footer">
 <div class="addr">
 <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
 103 Willowvale Rd, Harare
 </div>
 <a class="directions" href="https://www.google.com/maps/dir/?api=1&destination=103+Willowvale+Road+Harare+Zimbabwe" target="_blank" rel="noopener">
 Open in Maps
 <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M7 17l9.2-9.2M17 17V7H7"/></svg>
 </a>
 </div>
 </div>

 </aside>
 </div>
</div>

<!-- ===================== FAQ TEASER ===================== -->
<section class="faq-teaser">
 <div class="container">
 <div class="faq-teaser-grid">
 <div>
 <h2>Frequently Asked Questions</h2>
 <p>Quick answers to common questions. Can't find what you're looking for? Send us a message above.</p>
 <a href="faq.php" class="btn btn-outline-navy">
 View All FAQs
 <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M5 12h14M13 5l7 7-7 7"/></svg>
 </a>
 </div>

 <div class="faq-mini-list">
 <a href="faq.php" class="faq-mini-item">What is the best fence for a farm?</a>
 <a href="faq.php" class="faq-mini-item">Do you deliver nationwide?</a>
 <a href="faq.php" class="faq-mini-item">Are your products SABS compliant?</a>
 <a href="faq.php" class="faq-mini-item">How accurate is the quote calculator?</a>
 <a href="faq.php" class="faq-mini-item">Do you offer bulk discounts?</a>
 <a href="faq.php" class="faq-mini-item">How long does delivery take?</a>
 </div>
 </div>
 </div>
</section>

<!-- ===================== CTA BAND ===================== -->
<section class="cta-wrap">
 <div class="cta-band">
 <h2>Prefer to Skip the Form?</h2>
 <p>Get an instant estimate using our online calculator, or chat with us on WhatsApp right now.</p>
 <div class="btns">
 <a href="calculator.php" class="btn btn-amber">
 Get Instant Quote
 <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M5 12h14M13 5l7 7-7 7"/></svg>
 </a>
 <a href="https://wa.me/263784689857" target="_blank" rel="noopener" class="btn btn-whatsapp">
 <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
 WhatsApp Us
 </a>
 </div>
 </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
