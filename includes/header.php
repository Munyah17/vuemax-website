<?php
/**
 * Vuemax shared page header
 * Set before including:
 * $pageTitle (string) <title> text
 * $pageDesc (string) meta description
 * $active (string) nav key: home|fencing|steel|hardware|projects|about|contact|faq
 * $extraCss (string) optional page-specific CSS (printed inside <style>)
 * $extraHead (string) optional extra <head> markup
 */
$pageTitle = isset($pageTitle) ? $pageTitle : 'Vuemax Fencing, Steel & Hardware Solutions | Zimbabwe';
$pageDesc = isset($pageDesc) ? $pageDesc : 'Vuemax supplies quality fencing, steel and hardware products across Zimbabwe.';
$active = isset($active) ? $active : '';

function navClass($key, $active) { return $key === $active ? ' class="active"' : ''; }
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="theme-color" content="#0E2745">
<title><?= e($pageTitle) ?></title>
<meta name="description" content="<?= e($pageDesc) ?>">

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Playfair+Display:wght@600;700&family=Caveat:wght@600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="assets/css/vuemax.css">
<?php if (!empty($extraCss)): ?>
<style>
<?= $extraCss ?>
</style>
<?php endif; ?>
<?= isset($extraHead) ? $extraHead : '' ?>
</head>
<body>

<!-- ===================== TOP BAR ===================== -->
<div class="topbar">
 <div class="container topbar-inner">
 <span class="topbar-item">
 <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
 103 Willowvale Rd, Harare
 </span>
 <span class="topbar-item">
 <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
 0784 689 857
 </span>
 <span class="topbar-tagline">
 Stronger Boundaries <span class="dash"> </span> Brighter Possibilities
 </span>
 </div>
</div>

<!-- ===================== HEADER ===================== -->
<header class="site-header">
 <div class="container header-inner">
 <a href="index.php" class="logo">
 <?= logo_mark_html() ?>
 <div class="logo-text">
 <strong>VUEMAX</strong>
 <small>Fencing · Steel · Hardware</small>
 </div>
 </a>

 <nav class="nav-desktop">
 <a href="index.php"<?= navClass('home', $active) ?>>Home</a>
 <a href="products.php?category=fencing"<?= navClass('fencing', $active) ?>>Fencing</a>
 <a href="products.php?category=steel"<?= navClass('steel', $active) ?>>Steel</a>
 <a href="products.php?category=hardware"<?= navClass('hardware', $active) ?>>Hardware</a>
 <a href="installations.php"<?= navClass('projects', $active) ?>>Projects</a>
 <a href="about.php"<?= navClass('about', $active) ?>>About</a>
 <a href="contact.php"<?= navClass('contact', $active) ?>>Contact</a>
 </nav>

 <div class="header-actions">
 <a href="calculator.php" class="btn btn-amber btn-pill btn-sm">
 <span class="header-cta-text">Get Quote</span>
 <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M5 12h14M13 5l7 7-7 7"/></svg>
 </a>
 <button class="hamburger" id="hamburger" aria-label="Open menu" aria-expanded="false">
 <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><line x1="4" y1="7" x2="20" y2="7"/><line x1="4" y1="12" x2="20" y2="12"/><line x1="4" y1="17" x2="20" y2="17"/></svg>
 </button>
 </div>
 </div>
</header>

<!-- ===================== MOBILE DRAWER ===================== -->
<div class="drawer-backdrop" id="drawerBackdrop"></div>
<aside class="drawer" id="drawer" aria-hidden="true">
 <div class="drawer-head">
 <a href="index.php" class="logo">
 <?= logo_mark_html() ?>
 <div class="logo-text">
 <strong>VUEMAX</strong>
 <small>Fencing · Steel · Hardware</small>
 </div>
 </a>
 <button class="drawer-close" id="drawerClose" aria-label="Close menu">
 <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><line x1="6" y1="6" x2="18" y2="18"/><line x1="6" y1="18" x2="18" y2="6"/></svg>
 </button>
 </div>

 <nav class="drawer-nav">
 <a href="index.php"<?= navClass('home', $active) ?>>Home</a>
 <a href="products.php?category=fencing"<?= navClass('fencing', $active) ?>>Fencing</a>
 <a href="products.php?category=steel"<?= navClass('steel', $active) ?>>Steel</a>
 <a href="products.php?category=hardware"<?= navClass('hardware', $active) ?>>General Hardware</a>
 <a href="installations.php"<?= navClass('projects', $active) ?>>Projects</a>
 <a href="about.php"<?= navClass('about', $active) ?>>About</a>
 <a href="contact.php"<?= navClass('contact', $active) ?>>Contact</a>
 </nav>

 <div class="drawer-cta">
 <a href="calculator.php" class="btn btn-amber btn-block">
 Get Instant Quote
 <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M5 12h14M13 5l7 7-7 7"/></svg>
 </a>
 <a href="estimator.php" class="btn btn-outline-navy btn-block">
 AI Estimator
 </a>
 </div>

 <div class="drawer-contact">
 <strong style="color:var(--navy);font-weight:600;">Contact</strong><br>
 <a href="tel:+263784689857">0784 689 857</a><br>
 <a href="mailto:sales@vuemax.co.zw">sales@vuemax.co.zw</a>
 </div>
</aside>
