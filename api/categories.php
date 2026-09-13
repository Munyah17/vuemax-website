<?php
/**
 * ============================================================
 * VUEMAX API GET categories
 * ============================================================
 * File: /api/categories.php
 *
 * Returns all active categories as JSON.
 *
 * GET parameters (all optional):
 * ?featured=1 Only return featured categories
 * ?with_counts=1 Include product count per category
 *
 * Example responses:
 *
 * GET /api/categories.php
 * {
 * "ok": true,
 * "count": 3,
 * "categories": [
 * {
 * "id": 1,
 * "slug": "fencing",
 * "name": "Fencing Solutions",
 * "tagline": "Fencing & wire for every need",
 * "description": "...",
 * "image": "https://...",
 * "hero_image": null,
 * "icon": null,
 * "sort_order": 1,
 * "is_featured": 1,
 * "product_count": 8
 * },
 * ...
 * ]
 * }
 * ============================================================
 */

require_once __DIR__ . '/config.php';
require_db($pdo, $db_error);
require_method('GET');

/* ---------- Read query params ---------- */
$featured = isset($_GET['featured']) && $_GET['featured'] === '1';
$with_counts = isset($_GET['with_counts']) && $_GET['with_counts'] === '1';

/* ---------- Build query ---------- */
$sql = "
 SELECT
 c.id,
 c.slug,
 c.name,
 c.tagline,
 c.description,
 c.image,
 c.hero_image,
 c.icon,
 c.sort_order,
 c.is_featured
";

if ($with_counts) {
 $sql .= ",
 (
 SELECT COUNT(*)
 FROM products p
 JOIN subcategories sc ON sc.id = p.subcategory_id
 WHERE sc.category_id = c.id
 AND p.is_active = 1
 AND sc.is_active = 1
 ) AS product_count
 ";
}

$sql .= "
 FROM categories c
 WHERE c.is_active = 1
";

if ($featured) {
 $sql .= " AND c.is_featured = 1 ";
}

$sql .= " ORDER BY c.sort_order ASC, c.name ASC ";

/* ---------- Execute ---------- */
try {
 $stmt = $pdo->query($sql);
 $rows = $stmt->fetchAll();
} catch (PDOException $e) {
 json_error('Failed to fetch categories.', 500, DEBUG ? ['detail' => $e->getMessage()] : []);
}

/* ---------- Normalize output types ---------- */
$categories = array_map(function ($r) {
 return [
 'id' => (int) $r['id'],
 'slug' => $r['slug'],
 'name' => $r['name'],
 'tagline' => $r['tagline'],
 'description' => $r['description'],
 'image' => $r['image'],
 'hero_image' => $r['hero_image'],
 'icon' => $r['icon'],
 'sort_order' => (int) $r['sort_order'],
 'is_featured' => (bool) $r['is_featured'],
 'product_count' => isset($r['product_count']) ? (int) $r['product_count'] : null,
 ];
}, $rows);

/* ---------- Cache hint (helps cPanel) ---------- */
header('Cache-Control: public, max-age=300'); // 5 minutes

/* ---------- Respond ---------- */
json_response([
 'ok' => true,
 'count' => count($categories),
 'categories' => $categories,
]);