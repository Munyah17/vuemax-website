<?php
require_once __DIR__ . '/includes/config.php';

$pageTitle = 'Installations Vuemax | Fencing Projects Across Zimbabwe';
$pageDesc = 'See real Vuemax fencing installations across Zimbabwe farms, residential, commercial and security projects.';
$active = 'projects';

/* ---------- Gallery: DB-driven with static fallback ----------
   proj-N image slots stay positional so the admin image manager
   still swaps card images regardless of the underlying row. */
$PROJ_CATS = ['farms' => 'Farms', 'residential' => 'Residential', 'commercial' => 'Commercial', 'security' => 'Security'];
$PROJ_BADGES = ['farms' => 'Farm', 'residential' => 'Residential', 'commercial' => 'Commercial', 'security' => 'Security'];

$projects = [
 ['slug'=>'game-reserve-masvingo','title'=>'Game Reserve Perimeter — Masvingo','cat'=>'farms','location'=>'Masvingo','year'=>2024,'chips'=>['5km perimeter','1.8m game fence','Steel posts'],'img'=>'https://images.unsplash.com/photo-1518709268805-4e9042af9f23?auto=format&fit=crop&w=800&q=80'],
 ['slug'=>'borrowdale-residential','title'=>'Residential Boundary — Borrowdale, Harare','cat'=>'residential','location'=>'Harare','year'=>2024,'chips'=>['180m perimeter','1.8m diamond mesh','Double gate'],'img'=>'https://images.unsplash.com/photo-1449844908441-8829872d2607?auto=format&fit=crop&w=800&q=80'],
 ['slug'=>'graniteside-warehouse','title'=>'Warehouse Site — Graniteside, Harare','cat'=>'commercial','location'=>'Harare','year'=>2023,'chips'=>['450m perimeter','2.1m mesh','Razor top'],'img'=>'https://images.unsplash.com/photo-1587293852726-70cdb56c2866?auto=format&fit=crop&w=800&q=80'],
 ['slug'=>'marondera-farm','title'=>'Farm Perimeter — Marondera','cat'=>'farms','location'=>'Marondera','year'=>2024,'chips'=>['1.2km perimeter','1.5m field fence','Timber posts'],'img'=>'https://images.unsplash.com/photo-1500595046743-cd271d694d30?auto=format&fit=crop&w=800&q=80'],
 ['slug'=>'harare-prison-upgrade','title'=>'Prison Facility Upgrade — Harare','cat'=>'security','location'=>'Harare','year'=>2023,'chips'=>['1.5km perimeter','Razor wire','High-security'],'img'=>'https://images.unsplash.com/photo-1595278069441-2cf29f8005a4?auto=format&fit=crop&w=800&q=80'],
 ['slug'=>'mt-pleasant-townhouses','title'=>'Townhouse Complex — Mt Pleasant, Harare','cat'=>'residential','location'=>'Harare','year'=>2024,'chips'=>['320m perimeter','1.8m mesh','Concrete posts'],'img'=>'https://images.unsplash.com/photo-1568605114967-8130f3a36994?auto=format&fit=crop&w=800&q=80'],
 ['slug'=>'chitungwiza-school','title'=>'School Perimeter — Chitungwiza','cat'=>'commercial','location'=>'Chitungwiza','year'=>2023,'chips'=>['800m perimeter','1.8m diamond mesh','Top wire'],'img'=>'https://images.unsplash.com/photo-1553413077-190dd305871c?auto=format&fit=crop&w=800&q=80'],
 ['slug'=>'chinhoyi-cattle','title'=>'Cattle Ranch — Chinhoyi','cat'=>'farms','location'=>'Chinhoyi','year'=>2024,'chips'=>['3.5km perimeter','Field fence','Steel posts'],'img'=>'https://images.unsplash.com/photo-1560493676-04071c5f467b?auto=format&fit=crop&w=800&q=80'],
 ['slug'=>'bulawayo-bank','title'=>'Bank Branch Perimeter — Bulawayo','cat'=>'security','location'=>'Bulawayo','year'=>2024,'chips'=>['120m perimeter','2.1m razor','Reinforced posts'],'img'=>'https://images.unsplash.com/photo-1580129954963-a6d2dd1d6bdd?auto=format&fit=crop&w=800&q=80'],
 ['slug'=>'victoria-falls-estate','title'=>'Estate Boundary — Victoria Falls','cat'=>'residential','location'=>'Victoria Falls','year'=>2024,'chips'=>['650m perimeter','1.8m diamond mesh','2 gates'],'img'=>'https://images.unsplash.com/photo-1518780664697-55e3ad937233?auto=format&fit=crop&w=800&q=80'],
 ['slug'=>'norton-poultry','title'=>'Poultry Operation — Norton','cat'=>'farms','location'=>'Norton','year'=>2024,'chips'=>['400m perimeter','1.2m chicken mesh','Light posts'],'img'=>'https://images.unsplash.com/photo-1500076656116-558758c991c1?auto=format&fit=crop&w=800&q=80'],
 ['slug'=>'zvishavane-mining','title'=>'Mining Camp — Zvishavane','cat'=>'commercial','location'=>'Zvishavane','year'=>2023,'chips'=>['2.5km perimeter','Game fence','Razor top'],'img'=>'https://images.unsplash.com/photo-1486406146926-c627a92ad1ab?auto=format&fit=crop&w=800&q=80'],
];

$totalProjects = count($projects);
$catCountsProj = array_count_values(array_column($projects, 'cat'));

if ($pdo) {
 try {
 $totalProjects = (int)$pdo->query('SELECT COUNT(*) FROM projects WHERE is_active = 1')->fetchColumn();
 $catCountsProj = $pdo->query('SELECT category, COUNT(*) AS n FROM projects WHERE is_active = 1 GROUP BY category')->fetchAll(PDO::FETCH_KEY_PAIR);
 $rows = $pdo->query(
 'SELECT slug, title, category, location, year, spec_chips, image
 FROM projects WHERE is_active = 1
 ORDER BY is_featured DESC, sort_order ASC, id ASC
 LIMIT 12'
 )->fetchAll();
 if ($rows) {
 $projects = array_map(function ($r) {
 return [
 'slug' => $r['slug'], 'title' => $r['title'], 'cat' => $r['category'],
 'location' => $r['location'], 'year' => $r['year'],
 'chips' => $r['spec_chips'] ? (json_decode($r['spec_chips'], true) ?: []) : [],
 'img' => $r['image'],
 ];
 }, $rows);
 }
 } catch (Throwable $e) { /* keep static fallback */ }
}
$shownProjects = count($projects);

$extraCss = <<<'CSS'
/* ---------- STATS STRIP ---------- */
.stats-strip{
 background:var(--white);border-bottom:1px solid var(--border);
}
.stats-strip .stats-grid{padding:30px 0;}
.stats-strip .stat{background:none;border:none;box-shadow:none;padding:8px 4px;}
.stats-strip .stat:hover{transform:none;box-shadow:none;}
.stats-strip .stat .lbl{
 letter-spacing:.03em;text-transform:uppercase;
}

/* ---------- GALLERY ---------- */
.gallery-section{padding:56px 0 72px;}

.filter-bar{
 display:flex;flex-direction:column;align-items:flex-start;
 gap:16px;margin-bottom:32px;
}
.filter-tabs{display:flex;gap:10px;flex-wrap:wrap;}
.filter-tab{
 display:inline-flex;align-items:center;gap:8px;
 padding:10px 18px;border-radius:var(--radius-pill);
 font-size:14px;font-weight:500;color:var(--text);
 border:1px solid var(--border);background:var(--white);
 white-space:nowrap;transition:all var(--dur) var(--ease);cursor:pointer;
}
.filter-tab:hover{border-color:var(--navy);color:var(--navy);}
.filter-tab.active{background:var(--navy);color:var(--white);border-color:var(--navy);font-weight:600;}
.filter-tab .count{
 font-size:11px;font-weight:600;padding:2px 8px;border-radius:999px;
 background:var(--bg);color:var(--muted);
}
.filter-tab.active .count{background:rgba(245,183,49,.22);color:var(--amber);}

.result-count{font-size:13.5px;color:var(--muted);}
.result-count strong{color:var(--navy);font-weight:600;}

.gallery-grid{
 display:grid;grid-template-columns:1fr;gap:18px;
}
.project-card{
 background:var(--white);border:1px solid var(--border);
 border-radius:var(--radius-card);overflow:hidden;
 transition:transform var(--dur) var(--ease), box-shadow var(--dur) var(--ease);
 box-shadow:var(--shadow-card);
 cursor:pointer;display:flex;flex-direction:column;
}
.project-card:hover{transform:translateY(-4px);box-shadow:var(--shadow-lift);}

.project-thumb{
 aspect-ratio:4/3;background:#F0EDE8 center/cover no-repeat;
 position:relative;overflow:hidden;
}
.project-thumb::after{
 content:"";position:absolute;inset:0;
 background:linear-gradient(180deg,transparent 45%,rgba(10,29,51,.75));
 opacity:0;transition:opacity var(--dur) var(--ease);
}
.project-card:hover .project-thumb::after{opacity:1;}

.project-cat{
 position:absolute;top:14px;left:14px;z-index:2;
 background:rgba(255,255,255,.95);color:var(--navy);
 font-size:10.5px;font-weight:700;letter-spacing:.06em;
 text-transform:uppercase;padding:5px 10px;border-radius:6px;
}

.view-overlay{
 position:absolute;inset:0;z-index:2;
 display:flex;align-items:center;justify-content:center;
 opacity:0;transition:opacity var(--dur) var(--ease);
}
.project-card:hover .view-overlay{opacity:1;}
.view-overlay .btn-view{
 display:inline-flex;align-items:center;gap:8px;
 padding:11px 20px;border-radius:var(--radius-pill);
 background:var(--amber);color:var(--navy);
 font-size:13.5px;font-weight:700;letter-spacing:.02em;
 transform:translateY(8px);transition:transform var(--dur) var(--ease);
 box-shadow:0 8px 20px rgba(0,0,0,.25);
}
.project-card:hover .view-overlay .btn-view{transform:translateY(0);}

.project-body{padding:20px;flex:1;display:flex;flex-direction:column;}
.project-body h3{
 font-size:16px;font-weight:600;color:var(--navy);
 margin-bottom:6px;line-height:1.35;
}
.project-meta{
 display:flex;gap:14px;flex-wrap:wrap;
 font-size:12.5px;color:var(--muted);
 margin-bottom:14px;
}
.project-meta span{display:inline-flex;align-items:center;gap:5px;}
.project-meta svg{color:var(--amber);flex-shrink:0;}
.project-spec{display:flex;flex-wrap:wrap;gap:6px;margin-bottom:14px;}
.spec-chip{
 font-size:11px;font-weight:600;color:var(--navy);
 background:var(--bg);border:1px solid var(--border);
 padding:4px 9px;border-radius:6px;
 letter-spacing:.02em;
}
.project-body .footer-link{
 margin-top:auto;padding-top:14px;
 border-top:1px solid var(--border);
 font-size:13px;font-weight:600;color:var(--navy);
 display:inline-flex;align-items:center;gap:6px;
}
.project-body .footer-link svg{transition:transform var(--dur) var(--ease);}
.project-card:hover .footer-link svg{transform:translateX(4px);}

.gallery-empty{
 display:none;text-align:center;padding:80px 24px;
}
.gallery-empty.active{display:block;}
.gallery-empty .icon{
 width:64px;height:64px;border-radius:50%;
 background:var(--bg);color:var(--muted);
 display:flex;align-items:center;justify-content:center;
 margin:0 auto 18px;
}
.gallery-empty h3{font-size:16px;font-weight:600;color:var(--navy);margin-bottom:6px;}
.gallery-empty p{font-size:13.5px;color:var(--muted);}

.load-more-wrap{text-align:center;margin-top:44px;}

/* ---------- BEFORE / AFTER ---------- */
.ba-section{
 background:linear-gradient(150deg,var(--navy) 0%,var(--navy-deep) 100%);
 color:var(--white);
 padding:72px 0;position:relative;overflow:hidden;
}
.ba-grid{
 display:grid;grid-template-columns:1fr;gap:36px;
 align-items:center;
}
.ba-content .eyebrow{
 font-size:12px;letter-spacing:.16em;text-transform:uppercase;
 color:var(--amber);font-weight:700;margin-bottom:12px;
 display:inline-block;
}
.ba-content h2{
 font-family:'Playfair Display',serif;
 font-size:clamp(24px,2.8vw,36px);font-weight:700;
 line-height:1.2;margin-bottom:16px;letter-spacing:-.01em;
}
.ba-content h2 .accent{color:var(--amber);}
.ba-content p{
 color:rgba(255,255,255,.82);font-size:15.5px;
 line-height:1.7;margin-bottom:24px;max-width:480px;
}
.ba-content .ba-btn-row{display:flex;flex-direction:column;gap:10px;}

.ba-grid{max-width:640px;margin:0 auto;text-align:center;}
.ba-content p{margin-left:auto;margin-right:auto;}
.ba-content .ba-btn-row{justify-content:center;}

/* ---------- RESPONSIVE ---------- */
@media (min-width:640px){
 .filter-bar{flex-direction:row;justify-content:space-between;align-items:center;}
 .gallery-grid{grid-template-columns:repeat(2,1fr);gap:20px;}
 .ba-content .ba-btn-row{flex-direction:row;flex-wrap:wrap;}
}
@media (min-width:1024px){
 .gallery-grid{grid-template-columns:repeat(3,1fr);}
}
CSS;

$extraJs = 'const PROJ_BADGES = ' . json_encode($PROJ_BADGES) . ";\n" . <<<'JS'
/* ============================================================
 INSTALLATIONS FILTER + UI
 ============================================================ */
var curFilter = 'all';
var lmPage = 1;
var LM_PER_PAGE = 12;

function escHtml(s){
 return String(s == null ? '' : s).replace(/[&<>"']/g, function(c){
 return {'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c];
 });
}

function projectCardHtml(p){
 var chips = (p.spec_chips || []).map(function(c){
 return '<span class="spec-chip">' + escHtml(c) + '</span>';
 }).join('');
 var img = p.image || 'assets/img/products/diamond-mesh.jpg';
 return '<article class="project-card" data-category="' + escHtml(p.category) + '">'
 + '<div class="project-thumb" style="background-image:url(\'' + escHtml(img) + '\')">'
 + '<span class="project-cat">' + escHtml(PROJ_BADGES[p.category] || p.category) + '</span>'
 + '<div class="view-overlay"><span class="btn-view">View Project'
 + ' <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M5 12h14M13 5l7 7-7 7"/></svg>'
 + '</span></div></div>'
 + '<div class="project-body">'
 + '<h3>' + escHtml(p.title) + '</h3>'
 + '<div class="project-meta">'
 + '<span><svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg> ' + escHtml(p.location || '') + '</span>'
 + '<span><svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg> ' + escHtml(p.year || '') + '</span>'
 + '</div>'
 + '<div class="project-spec">' + chips + '</div>'
 + '<span class="footer-link">View Project'
 + ' <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M5 12h14M13 5l7 7-7 7"/></svg>'
 + '</span>'
 + '</div></article>';
}

function setFilter(filter){
 curFilter = filter;
 document.querySelectorAll('.filter-tab').forEach(t => t.classList.toggle('active', t.dataset.filter === filter));

 let visible = 0;
 document.querySelectorAll('#galleryGrid .project-card').forEach(card => {
 const show = (filter === 'all') || (card.dataset.category === filter);
 card.style.display = show ? '' : 'none';
 if (show) visible++;
 });

 document.getElementById('visibleCount').textContent = visible;
 document.getElementById('galleryEmpty').classList.toggle('active', visible === 0);
}

document.querySelectorAll('.filter-tab').forEach(t =>
 t.addEventListener('click', () => setFilter(t.dataset.filter)));

const params = new URLSearchParams(window.location.search);
if (params.get('filter')) setFilter(params.get('filter'));

/* ---- Load more (api/projects.php pages beyond the first 12) ---- */
function loadMore(){
 const btn = document.getElementById('loadMoreBtn');
 const wrap = document.getElementById('loadMoreWrap');
 if (!btn) return;
 btn.disabled = true;
 lmPage++;
 fetch('api/projects.php?page=' + lmPage + '&per_page=' + LM_PER_PAGE)
 .then(r => r.json())
 .then(d => {
 if (!d || !d.ok || !Array.isArray(d.projects)) throw new Error('bad response');
 const grid = document.getElementById('galleryGrid');
 d.projects.forEach(p => grid.insertAdjacentHTML('beforeend', projectCardHtml(p)));
 setFilter(curFilter);
 if (!d.projects.length || lmPage * LM_PER_PAGE >= d.total) wrap.style.display = 'none';
 btn.disabled = false;
 btn.innerHTML = 'Load More Projects';
 })
 .catch(() => {
 btn.disabled = false;
 btn.innerHTML = 'Load More Projects';
 });
}
JS;

require __DIR__ . '/includes/header.php';
?>

<!-- ===================== PAGE HERO ===================== -->
<section class="page-hero" style="--hero-img:url('<?= e(site_image('inst-hero', 'https://images.unsplash.com/photo-1500382017468-9049fed747ef?auto=format&fit=crop&w=1920&q=80')) ?>')">
 <div class="container">
 <div class="breadcrumbs">
 <a href="index.php">Home</a>
 <span class="sep">›</span>
 <span>Installations</span>
 </div>
 <h1>Our <span class="accent">Installations</span></h1>
 <p>Real projects. Real results. Across Zimbabwe from residential boundaries to farm perimeters, commercial sites and security installations.</p>
 </div>
 <div class="script-note">Building<br>a Safer<br>Zimbabwe</div>
</section>

<!-- ===================== STATS ===================== -->
<section class="stats-strip">
 <div class="container">
 <div class="stats-grid">
 <div class="stat">
 <div class="num">500<span class="plus">+</span></div>
 <div class="lbl">Projects Completed</div>
 </div>
 <div class="stat">
 <div class="num">15<span class="plus">+</span></div>
 <div class="lbl">Years Experience</div>
 </div>
 <div class="stat">
 <div class="num">10</div>
 <div class="lbl">Provinces Covered</div>
 </div>
 <div class="stat">
 <div class="num">98<span class="plus">%</span></div>
 <div class="lbl">Customer Satisfaction</div>
 </div>
 </div>
 </div>
</section>

<!-- ===================== GALLERY ===================== -->
<section class="gallery-section">
 <div class="container">

 <div class="filter-bar">
 <div class="filter-tabs" id="filterTabs">
 <button class="filter-tab active" data-filter="all">
 All Projects <span class="count"><?= $totalProjects ?></span>
 </button>
 <?php foreach ($PROJ_CATS as $fslug => $flabel): if (empty($catCountsProj[$fslug])) continue; ?>
 <button class="filter-tab" data-filter="<?= e($fslug) ?>">
 <?= e($flabel) ?> <span class="count"><?= (int)$catCountsProj[$fslug] ?></span>
 </button>
 <?php endforeach; ?>
 </div>

 <div class="result-count">
 Showing <strong id="visibleCount"><?= $shownProjects ?></strong> of <strong><?= $totalProjects ?></strong> projects
 </div>
 </div>

 <div class="gallery-grid" id="galleryGrid">
 <?php foreach ($projects as $i => $pr): ?>
 <article class="project-card reveal<?= ['', ' reveal-d1', ' reveal-d2'][$i % 3] ?>" data-category="<?= e($pr['cat']) ?>">
 <div class="project-thumb" style="background-image:url('<?= e(site_image('proj-' . ($i + 1), $pr['img'] ?: 'https://images.unsplash.com/photo-1518709268805-4e9042af9f23?auto=format&fit=crop&w=800&q=80')) ?>')">
 <span class="project-cat"><?= e($PROJ_BADGES[$pr['cat']] ?? ucfirst($pr['cat'])) ?></span>
 <div class="view-overlay">
 <span class="btn-view">
 View Project
 <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M5 12h14M13 5l7 7-7 7"/></svg>
 </span>
 </div>
 </div>
 <div class="project-body">
 <h3><?= e($pr['title']) ?></h3>
 <div class="project-meta">
 <span>
 <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
 <?= e($pr['location']) ?>
 </span>
 <span>
 <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
 <?= e($pr['year']) ?>
 </span>
 </div>
 <div class="project-spec">
 <?php foreach ($pr['chips'] as $chip): ?><span class="spec-chip"><?= e($chip) ?></span><?php endforeach; ?>
 </div>
 <span class="footer-link">
 View Project
 <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M5 12h14M13 5l7 7-7 7"/></svg>
 </span>
 </div>
 </article>
 <?php endforeach; ?>
 </div>
 <!-- Empty state -->
 <div class="gallery-empty" id="galleryEmpty">
 <div class="icon">
 <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><path d="m21 15-5-5L5 21"/></svg>
 </div>
 <h3>No projects in this category yet</h3>
 <p>Check back soon we're always adding new installations.</p>
 </div>

 <!-- Load more (only when the DB has projects beyond the first page) -->
 <?php if ($totalProjects > $shownProjects): ?>
 <div class="load-more-wrap" id="loadMoreWrap">
 <button class="btn btn-outline-navy" id="loadMoreBtn" onclick="loadMore()">
 Load More Projects
 <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M19 12H5M12 5l-7 7 7 7"/><line x1="19" y1="12" x2="19" y2="12"/></svg>
 </button>
 </div>
 <?php endif; ?>

 </div>
</section>

<!-- ===================== BEFORE / AFTER TEASER ===================== -->
<section class="ba-section">
 <div class="container">
 <div class="ba-grid">
 <div class="ba-content reveal">
 <span class="eyebrow">Before &amp; After</span>
 <h2>See the <span class="accent">Transformation.</span></h2>
 <p>Every project tells a story. From overgrown, insecure boundaries to clean, protected perimeters see how a Vuemax installation changes a property.</p>
 <div class="ba-btn-row">
 <a href="contact.php" class="btn btn-amber">
 Start Your Project
 <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M5 12h14M13 5l7 7-7 7"/></svg>
 </a>
 <a href="calculator.php" class="btn btn-ghost">
 Try the Calculator
 </a>
 </div>
 </div>

 </div>
 </div>
</section>

<!-- ===================== CTA BAND ===================== -->
<section class="section">
 <div class="container">
 <div class="cta-band reveal">
 <h2>Ready to Be Our Next Project?</h2>
 <p>Whether it's a home boundary or a large commercial perimeter, we'll design and install the right fencing for your site.</p>
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
