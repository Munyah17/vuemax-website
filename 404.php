<?php
require_once __DIR__ . '/includes/config.php';

$pageTitle = 'Page Not Found — Vuemax';
$pageDesc  = 'The page you are looking for could not be found.';
$active    = '';

$extraCss = <<<'CSS'
.not-found{
  min-height:60vh;
  display:flex;align-items:center;justify-content:center;
  text-align:center;padding:80px 0;
}
.not-found .code{
  font-family:'Playfair Display',serif;
  font-size:clamp(72px,18vw,140px);font-weight:700;
  color:var(--navy);line-height:1;
}
.not-found .code span{color:var(--amber);}
.not-found h1{
  font-family:'Playfair Display',serif;
  font-size:clamp(22px,5vw,32px);color:var(--navy);
  margin:10px 0 12px;
}
.not-found p{color:var(--muted);font-size:15px;max-width:420px;margin:0 auto 26px;}
.not-found .btns{display:flex;flex-direction:column;gap:10px;align-items:center;}
@media (min-width:640px){
  .not-found .btns{flex-direction:row;justify-content:center;}
}
CSS;

require __DIR__ . '/includes/header.php';
?>

<section class="not-found">
  <div class="container">
    <div class="code">4<span>0</span>4</div>
    <h1>Page Not Found</h1>
    <p>The page you're looking for doesn't exist or may have been moved. Let's get you back on track.</p>
    <div class="btns">
      <a href="index.php" class="btn btn-amber">
        Back to Home
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M5 12h14M13 5l7 7-7 7"/></svg>
      </a>
      <a href="products.php" class="btn btn-outline-navy">Browse Products</a>
    </div>
  </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
