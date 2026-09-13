<?php
/**
 * ============================================================
 * VUEMAX API GET installations / projects
 * ============================================================
 * File: /api/projects.php
 *
 * GET parameters (all optional):
 * ?category=farms|residential|commercial|security
 * ?featured=1 Only featured projects
 * ?search=harare Search title + location + description
 * ?sort=newest newest (default) | oldest | featured | title
 * ?page=1&per_page=12
 * ?id=5 Fetch a single project by ID
 * ?slug=game-reserve-masvingo Fetch a single project by slug
 *
 * Response shape:
 * {
 * "ok": true,
 * "count": 12,
 * "total": 12,
 * "page": 1,
 * "per_page": 12,
 * "pages": 1,
 * "filters": { "category": null, "featured": false, "search": null },
 * "categories": [ { "slug": "farms", "label": "Farms", "count": 4 }, ... ],
 * "projects": [ { ...project... }, ... ]
 * }
 * ============================================================
 */

require_once __DIR__ . '/config.php';
require_db($pdo, $db_error);
require_method('GET');

/* ---------- Read + validate query params ---------- */
$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$slug = isset($_GET['slug']) ? clean_str($_GET['slug'], 120) : '';
$category = isset($_GET['category']) ? clean_str($_GET['category'], 40) : '';
$featured = isset($_GET['featured']) && $_GET['featured'] === '1';
$search = isset($_GET['search']) ? clean_str($_GET['search'], 120) : '';
$sort = isset($_GET['sort']) ? clean_str($_GET['sort'], 20) : 'newest';
$page = max(1, (int) ($_GET['page'] ?? 1));
$per_page = min(60, max(1, (int) ($_GET['per_page'] ?? 12)));

$offset = ($page - 1) * $per_page;

/* ---------- Whitelist sort options ---------- */
$sort_map = [
 'newest' => 'p.is_featured DESC, p.year DESC, p.sort_order ASC, p.id DESC',
 'oldest' => 'p.year ASC, p.sort_order ASC, p.id ASC',
 'featured' => 'p.is_featured DESC, p.sort_order ASC, p.id DESC',
 'title' => 'p.title ASC',
];
$order_sql = $sort_map[$sort] ?? $sort_map['newest'];

/* ---------- Allowed project categories ---------- */
$allowed_categories = ['farms', 'residential', 'commercial', 'security'];
if ($category !== '' && !in_array($category, $allowed_categories, true)) {
 $category = '';
}

/* ============================================================
 SINGLE PROJECT MODE (?id= or ?slug=)
 ============================================================ */
if ($id > 0 || $slug !== '') {
 try {
 if ($id > 0) {
 $stmt = $pdo->prepare("
 SELECT * FROM projects
 WHERE id = :k AND is_active = 1
 LIMIT 1
 ");
 $stmt->execute([':k' => $id]);
 } else {
 $stmt = $pdo->prepare("
 SELECT * FROM projects
 WHERE slug = :k AND is_active = 1
 LIMIT 1
 ");
 $stmt->execute([':k' => $slug]);
 }
 $row = $stmt->fetch();
 } catch (PDOException $e) {
 json_error('Failed to fetch project.', 500, DEBUG ? ['detail' => $e->getMessage()] : []);
 }

 if (!$row) {
 json_error('Project not found.', 404);
 }

 /* Attach related projects from same category */
 $related = [];
 try {
 $stmt = $pdo->prepare("
 SELECT id, slug, title, category, location, year, perimeter, fence_type, image, is_featured
 FROM projects
 WHERE category = :cat
 AND id <> :id
 AND is_active = 1
 ORDER BY is_featured DESC, sort_order ASC
 LIMIT 4
 ");
 $stmt->execute([':cat' => $row['category'], ':id' => (int) $row['id']]);
 $related = array_map('normalize_project_card', $stmt->fetchAll());
 } catch (PDOException $e) { /* silent */ }

 $project = normalize_project_full($row);
 $project['related'] = $related;

 header('Cache-Control: public, max-age=300');
 json_response([
 'ok' => true,
 'project' => $project,
 ]);
}

/* ============================================================
 LIST MODE
 ============================================================ */

/* ---------- Build WHERE ---------- */
$where = ["p.is_active = 1"];
$params = [];

if ($category !== '') {
 $where[] = "p.category = :category";
 $params[':category'] = $category;
}
if ($featured) {
 $where[] = "p.is_featured = 1";
}
if ($search !== '') {
 $where[] = "(p.title LIKE :search OR p.location LIKE :search OR p.description LIKE :search OR p.fence_type LIKE :search)";
 $params[':search'] = '%' . $search . '%';
}

$where_sql = implode(' AND ', $where);

/* ---------- Count total ---------- */
try {
 $stmt = $pdo->prepare("SELECT COUNT(*) FROM projects p WHERE {$where_sql}");
 $stmt->execute($params);
 $total = (int) $stmt->fetchColumn();
} catch (PDOException $e) {
 json_error('Failed to count projects.', 500, DEBUG ? ['detail' => $e->getMessage()] : []);
}

/* ---------- Fetch page ---------- */
try {
 $sql = "
 SELECT p.*
 FROM projects p
 WHERE {$where_sql}
 ORDER BY {$order_sql}
 LIMIT :limit OFFSET :offset
 ";
 $stmt = $pdo->prepare($sql);
 foreach ($params as $k => $v) {
 $stmt->bindValue($k, $v);
 }
 $stmt->bindValue(':limit', $per_page, PDO::PARAM_INT);
 $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
 $stmt->execute();
 $rows = $stmt->fetchAll();
} catch (PDOException $e) {
 json_error('Failed to fetch projects.', 500, DEBUG ? ['detail' => $e->getMessage()] : []);
}

/* ---------- Category counts (always across full active set, not filtered) ---------- */
try {
 $stmt = $pdo->query("
 SELECT category, COUNT(*) AS n
 FROM projects
 WHERE is_active = 1
 GROUP BY category
 ");
 $raw = $stmt->fetchAll();
} catch (PDOException $e) {
 $raw = [];
}

$counts = ['farms' => 0, 'residential' => 0, 'commercial' => 0, 'security' => 0];
$total_active = 0;
foreach ($raw as $r) {
 $cat = $r['category'];
 $n = (int) $r['n'];
 if (isset($counts[$cat])) $counts[$cat] = $n;
 $total_active += $n;
}

$category_meta = [
 ['slug' => 'all', 'label' => 'All Projects', 'count' => $total_active],
 ['slug' => 'farms', 'label' => 'Farms', 'count' => $counts['farms']],
 ['slug' => 'residential', 'label' => 'Residential', 'count' => $counts['residential']],
 ['slug' => 'commercial', 'label' => 'Commercial', 'count' => $counts['commercial']],
 ['slug' => 'security', 'label' => 'Security', 'count' => $counts['security']],
];

/* ---------- Normalize ---------- */
$projects = array_map('normalize_project_card', $rows);

$pages = $per_page > 0 ? (int) ceil($total / $per_page) : 0;

header('Cache-Control: public, max-age=180');

json_response([
 'ok' => true,
 'count' => count($projects),
 'total' => $total,
 'page' => $page,
 'per_page' => $per_page,
 'pages' => $pages,
 'sort' => $sort,
 'filters' => [
 'category' => $category ?: null,
 'featured' => $featured,
 'search' => $search ?: null,
 ],
 'categories' => $category_meta,
 'projects' => $projects,
]);

/* ============================================================
 NORMALIZATION HELPERS
 ============================================================ */

/**
 * Compact shape for cards in the gallery grid.
 */
function normalize_project_card($r) {
 return [
 'id' => (int) $r['id'],
 'slug' => $r['slug'],
 'title' => $r['title'],
 'category' => $r['category'],
 'location' => $r['location'],
 'year' => $r['year'] !== null ? (int) $r['year'] : null,
 'perimeter' => $r['perimeter'],
 'fence_type' => $r['fence_type'],
 'spec_chips' => $r['spec_chips'] ? json_decode($r['spec_chips'], true) : [],
 'image' => $r['image'],
 'is_featured' => (bool) $r['is_featured'],
 ];
}

/**
 * Full shape for a single project detail.
 */
function normalize_project_full($r) {
 return [
 'id' => (int) $r['id'],
 'slug' => $r['slug'],
 'title' => $r['title'],
 'category' => $r['category'],
 'location' => $r['location'],
 'year' => $r['year'] !== null ? (int) $r['year'] : null,
 'perimeter' => $r['perimeter'],
 'fence_type' => $r['fence_type'],
 'spec_chips' => $r['spec_chips'] ? json_decode($r['spec_chips'], true) : [],
 'image' => $r['image'],
 'gallery' => $r['gallery'] ? json_decode($r['gallery'], true) : [],
 'description' => $r['description'],
 'is_featured' => (bool) $r['is_featured'],
 'created_at' => $r['created_at'],
 ];
}