============================================================
VUEMAX WEBSITE — cPanel DEPLOYMENT GUIDE
============================================================
Stack: PHP 8+, MySQL (MariaDB), Apache via cPanel
Estimated time: 20–30 minutes

------------------------------------------------------------
STEP 0 — BEFORE YOU START
------------------------------------------------------------
You need:
  [ ] cPanel login credentials
  [ ] Your domain pointed at the hosting account (DNS done)
  [ ] This project folder (downloaded or git-cloned)

Files you'll deploy:
  *.php pages, includes/, assets/, api/, .htaccess
Do NOT upload:
  _archive/  (old static files — kept for reference only)
  sql/       (import it, don't serve it — .htaccess blocks it anyway,
              but cleaner to not upload)
  README.txt, AGENTS.md, .gitignore (optional, harmless)

------------------------------------------------------------
STEP 1 — CREATE THE MYSQL DATABASE
------------------------------------------------------------
1. cPanel → "MySQL® Databases" (or "MySQL Database Wizard").
2. Create a database, e.g.  youruser_vuemax
   (cPanel auto-prefixes with your account name — note the FULL name).
3. Create a database user, e.g.  youruser_vuemax_admin
   Use a strong password (cPanel generator is fine). SAVE IT.
4. Add the user to the database with ALL PRIVILEGES.

Write down:
  DB_HOST: localhost
  DB_NAME: ______________________  (e.g. youruser_vuemax)
  DB_USER: ______________________  (e.g. youruser_vuemax_admin)
  DB_PASS: ______________________

------------------------------------------------------------
STEP 2 — IMPORT THE SCHEMA
------------------------------------------------------------
1. cPanel → phpMyAdmin → select your new database (left sidebar).
2. Click the "Import" tab → Choose file → select  sql/schema.sql
   from this project.
3. Format: SQL → click "Import" / "Go".
4. Verify: you should now see tables —
   categories, subcategories, products, product_specs, projects,
   quotes, contacts, settings — with seed data inside.

------------------------------------------------------------
STEP 3 — UPLOAD THE FILES
------------------------------------------------------------
Option A — File Manager (simplest):
  1. cPanel → File Manager → public_html/
  2. Upload the project files (zip locally, upload, extract).
  3. Make sure .htaccess was uploaded (enable "Show Hidden Files").

Option B — Git (cPanel → Git Version Control):
  1. cPanel → Git Version Control → Create Repository.
  2. Clone URL: https://github.com/Munyah17/vuemax-website.git
     Repository path: a STAGING dir (not public_html directly).
  3. Pull, then use "Deploy" or copy files into public_html/
     (excluding _archive/).

Final structure under public_html/:
  public_html/
    .htaccess
    index.php, about.php, products.php, product-detail.php,
    calculator.php, estimator.php, installations.php, faq.php,
    contact.php, quote-results.php, 404.php
    api/         (all endpoints + api/config.php)
    includes/    (config.php, header.php, footer.php)
    assets/      (css/vuemax.css, js/main.js)

------------------------------------------------------------
STEP 4 — SET DATABASE CREDENTIALS (TWO FILES)
------------------------------------------------------------
Edit BOTH files in cPanel File Manager:

  api/config.php   (lines ~22–27, "EDIT THESE FIVE LINES")
    $DB_HOST = 'localhost';
    $DB_NAME = 'youruser_vuemax';
    $DB_USER = 'youruser_vuemax_admin';
    $DB_PASS = 'your-password';

  includes/config.php   (lines ~10–13)
    $DB_HOST = 'localhost';
    $DB_NAME = 'youruser_vuemax';
    $DB_USER = 'youruser_vuemax_admin';
    $DB_PASS = 'your-password';

While in api/config.php, also update:
    SITE_URL → your real domain, e.g. 'https://vuemax.co.zw'

------------------------------------------------------------
STEP 5 — PHP VERSION
------------------------------------------------------------
cPanel → "MultiPHP Manager" (or "Select PHP Version"):
  Set the domain to PHP 8.0+ (8.1/8.2/8.3 recommended).
  Required extensions (usually on by default): pdo_mysql, mbstring.

------------------------------------------------------------
STEP 6 — SSL / HTTPS
------------------------------------------------------------
1. cPanel → "SSL/TLS Status" → run AutoSSL (free) for the domain.
2. Wait for the certificate to issue (usually minutes).
3. .htaccess already forces HTTPS automatically on non-localhost.
4. AFTER HTTPS works, optionally enable HSTS:
   In .htaccess, uncomment the line:
     Header always set Strict-Transport-Security "max-age=31512000; includeSubDomains" env=HTTPS

------------------------------------------------------------
STEP 7 — TURN OFF DEBUG WHEN LIVE
------------------------------------------------------------
In api/config.php change:
    define('DEBUG', true);   →   define('DEBUG', false);
(Debug mode leaks SQL error details in API responses.)

------------------------------------------------------------
STEP 8 — TEST CHECKLIST
------------------------------------------------------------
Browse to your domain and verify:
  [ ] Homepage loads, header/footer/nav look correct
  [ ] Mobile menu (hamburger) opens and closes
  [ ] /products, /about, /contact etc. load via clean URLs
      (no .php needed — also confirm .php URLs work)
  [ ] Homepage "Popular Right Now" shows products FROM THE DB
      (if DB connected, API-driven cards replace the fallbacks)
  [ ] Calculator produces a BOQ and "Generate Quote" redirects
      to quote-results.php showing your quote
  [ ] Contact form submits (check the `contacts` table in
      phpMyAdmin, or your email if mail() is configured)
  [ ] A bogus URL like /nonexistent shows the styled 404 page
  [ ] https:// works and http:// redirects to https://

API smoke test (in browser):
  https://yourdomain/api/categories.php   → should return JSON
  If it returns {"ok":false,"error":"Database unavailable..."}
  → your credentials in api/config.php are wrong or the user
    isn't added to the DB (Step 1.4).

------------------------------------------------------------
STEP 9 — EMAIL (OPTIONAL, RECOMMENDED)
------------------------------------------------------------
api/contact-send.php uses PHP mail() by default, which is
unreliable on many hosts. For reliable delivery:
  1. Create a mailbox: cPanel → Email Accounts → sales@yourdomain
  2. Ask your host for SMTP settings, then either:
     - Install PHPMailer and update contact-send.php to use SMTP, or
     - Keep mail() but ensure the From address is a mailbox that
       EXISTS on the same domain (many hosts require this).

------------------------------------------------------------
COMMON PROBLEMS
------------------------------------------------------------
"500 Internal Server Error" on every page
  → .htaccess conflict. Temporarily rename it to .htaccess.off;
    if pages load, your host disabled a module. Comment out the
    mod_headers / mod_deflate / mod_expires sections one by one.

Blank page
  → Wrong PHP version (needs 8.0+) or a PHP error. Check
    cPanel → Errors, or set display_errors on temporarily.

API returns "Database unavailable"
  → Credentials in api/config.php don't match Step 1 values,
    or user wasn't added to the database with privileges.

Styles look broken / unstyled
  → assets/css/vuemax.css didn't upload, or was uploaded to the
    wrong folder. Path must be exactly  public_html/assets/css/

Quotes don't save / contact form fails
  → Check browser console (F12) for the fetch error, then check
    the API endpoint directly in the browser.

Changes not visible after editing files
  → .htaccess sets no-cache for HTML but 1-week cache for CSS/JS.
    Hard-refresh (Ctrl+F5), or bump the URL: vuemax.css?v=2

------------------------------------------------------------
KEEPING THE SITE UPDATED
------------------------------------------------------------
- Edit locally → commit → push to GitHub → re-upload changed
  files via File Manager/FTP (or git pull on the server).
- NEVER commit real DB passwords to GitHub. The committed
  config files contain placeholders only — the live credentials
  live only on the server.

============================================================
DONE — your site should be live. 
Questions: sales@vuemax.co.zw
============================================================
