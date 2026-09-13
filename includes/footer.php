<?php
/**
 * Vuemax — shared page footer
 * Optional: $extraJs (string) — page-specific JS printed inside <script> before </body>
 */
?>
<!-- ===================== FOOTER ===================== -->
<footer class="site-footer">
  <div class="container">
    <div class="footer-grid">
      <div class="footer-brand">
        <a href="index.php" class="logo">
          <div class="logo-mark">V</div>
          <div class="logo-text">
            <strong>VUEMAX</strong>
            <small>Fencing · Steel · Hardware</small>
          </div>
        </a>
        <p>Zimbabwe's trusted supplier of quality fencing, steel and hardware products. Nationwide delivery, expert support, quality you can trust.</p>
      </div>

      <div class="footer-col">
        <h4>Shop</h4>
        <ul>
          <li><a href="products.php?category=fencing">Fencing</a></li>
          <li><a href="products.php?category=steel">Steel</a></li>
          <li><a href="products.php?category=hardware">General Hardware</a></li>
          <li><a href="products.php">All Products</a></li>
        </ul>
      </div>

      <div class="footer-col">
        <h4>Company</h4>
        <ul>
          <li><a href="about.php">About Us</a></li>
          <li><a href="installations.php">Projects</a></li>
          <li><a href="faq.php">FAQ</a></li>
          <li><a href="contact.php">Contact</a></li>
        </ul>
      </div>

      <div class="footer-col">
        <h4>Get in Touch</h4>
        <ul class="footer-contact">
          <li>
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
            <span>103 Willowvale Rd, Harare, Zimbabwe</span>
          </li>
          <li>
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
            <a href="tel:+263784689857">0784 689 857</a>
          </li>
          <li>
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
            <a href="mailto:sales@vuemax.co.zw">sales@vuemax.co.zw</a>
          </li>
        </ul>
      </div>
    </div>

    <div class="footer-bottom">
      <div>&copy; <span id="footerYear">2026</span> Vuemax Investments. All rights reserved.</div>
      <div class="footer-socials">
        <a href="#" aria-label="Facebook">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M22 12a10 10 0 1 0-11.56 9.88v-6.99H7.9V12h2.54V9.8c0-2.5 1.5-3.89 3.77-3.89 1.09 0 2.24.2 2.24.2v2.46h-1.26c-1.24 0-1.63.77-1.63 1.56V12h2.78l-.45 2.89h-2.33v6.99A10 10 0 0 0 22 12z"/></svg>
        </a>
        <a href="#" aria-label="WhatsApp">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M20.52 3.48A11.94 11.94 0 0 0 12.04 0C5.5 0 .2 5.3.2 11.84c0 2.09.55 4.13 1.6 5.93L0 24l6.4-1.68a11.83 11.83 0 0 0 5.64 1.44h.01c6.53 0 11.84-5.3 11.84-11.84 0-3.16-1.23-6.13-3.37-8.44z"/></svg>
        </a>
        <a href="#" aria-label="LinkedIn">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M20.45 20.45h-3.55v-5.57c0-1.33-.02-3.04-1.85-3.04-1.85 0-2.14 1.45-2.14 2.94v5.67H9.36V9h3.41v1.56h.05c.48-.9 1.63-1.85 3.36-1.85 3.6 0 4.27 2.37 4.27 5.45v6.29zM5.34 7.43a2.06 2.06 0 1 1 0-4.12 2.06 2.06 0 0 1 0 4.12zM7.12 20.45H3.56V9h3.56v11.45z"/></svg>
        </a>
      </div>
    </div>
  </div>
</footer>

<script src="assets/js/main.js" defer></script>
<?php if (!empty($extraJs)): ?>
<script>
<?= $extraJs ?>
</script>
<?php endif; ?>
</body>
</html>
