# Vuemax Website

Static-first marketing site for Vuemax (fencing, steel & hardware, Zimbabwe).
Stack: PHP 8+ (shared includes), MySQL, Apache/cPanel.

## Structure

```
index.php, about.php, products.php, product-detail.php,
calculator.php, estimator.php, installations.php, faq.php,
contact.php, quote-results.php, 404.php   — pages (all use shared partials)
includes/config.php    — DB (PDO) + e()/usd() helpers
includes/header.php    — <head>, topbar, header, mobile drawer
includes/footer.php    — footer + shared <script> tag
assets/css/vuemax.css  — design system (single source of truth)
assets/js/main.js      — drawer, header state, scroll-reveal (.reveal)
api/*.php              — JSON endpoints (categories, subcategories, products,
                         product, estimate, quote-save, contact-send, projects)
sql/schema.sql         — full schema + seed data
_archive/              — original static .html files (pre-conversion)
```

## Page template

```php
<?php
require_once __DIR__ . '/includes/config.php';
$pageTitle = '...';
$pageDesc  = '...';
$active    = 'home';   // home|fencing|steel|hardware|projects|about|contact|faq|''
$extraCss  = <<<'CSS' ...page-only CSS... CSS;
$extraJs   = <<<'JS' ...page-only JS... JS;
require __DIR__ . '/includes/header.php';
?>
...body...
<?php require __DIR__ . '/includes/footer.php'; ?>
```

## Conventions

- Shared classes live in `assets/css/vuemax.css` — do NOT redefine
  `.btn`, `.section`, `.page-hero`, `.product-card`, `.cta-band`, footer, etc.
  in page `$extraCss` except as scoped overrides.
- Mobile-first CSS: base styles = phones, `@media (min-width:…)` scales up.
- Add `class="reveal"` (+ `reveal-d1/d2/d3`) to elements for scroll-in animation.
- All internal links use relative `*.php` paths (`.htaccess` also maps clean
  URLs → `.php`). API calls use relative `api/*.php`.
- Quotes flow: calculator/estimator write `vuemax_quote` to sessionStorage →
  `quote-results.php` renders it. Phase 2: also POST to `api/quote-save.php`.

## Deploy (cPanel)

1. Upload everything except `_archive/` to `public_html/`.
2. Import `sql/schema.sql` via phpMyAdmin.
3. Set credentials in `api/config.php` (5 lines at top) and
   `includes/config.php`.
4. Set `DEBUG = false` in `api/config.php` when live.
