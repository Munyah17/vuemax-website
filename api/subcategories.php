<?php
/**
 * ============================================================
 * VUEMAX API — GET subcategories
 * ============================================================
 * File: /api/subcategories.php
 *
 * Returns active subcategories as JSON.
 *
 * GET parameters (all optional):
 *   ?category=fencing   Only subcategories of this category slug
 *   ?with_counts=1      Include product count per subcategory
 *
 * Example response:
 *
 *   GET /api/subcategories.php?category=fencing&with_counts=1
 *   {
 *     "ok": true,
 *     "count": 4,
 *     "subcategories": [
 *       {
 *         "id": 1,
 *         "slug": "diamond-mesh",
 *         "name": "Diamond Mesh",
 *         "description": "...",
 *         "sort_order": 1,
 *         "product_count": 6,
 *         "category": { "slug": "fencing", "name": "Fencing Solutions" }
 *       },
 *       ...
 *     ]
 *   }
 * ============================================================
 */

require_once __DIR__ . '/config.php';
require_db($pdo, $db_error);
require_method('GET');

/* ---------- Read query params ---------- */
$category    = isset($_GET['category'])    ? clean_str($_GET['category'], 80) : null;
$with_counts = isset($_GET['with_counts']) && $_GET['with_counts'] === '1';

/* ---------- Build query ---------- */
$sql = "
    SELECT
        sc.id,
        sc.slug,
        sc.name,
        sc.description,
        sc.sort_order,
        c.slug AS category_slug,
        c.name AS category_name
";

if ($with_counts) {
    $sql .= ",
        (
            SELECT COUNT(*)
            FROM products p
            WHERE p.subcategory_id = sc.id
              AND p.is_active = 1
        ) AS product_count
    ";
}

$sql .= "
    FROM subcategories sc
    JOIN categories c ON c.id = sc.category_id
    WHERE sc.is_active = 1
      AND c.is_active = 1
";

$params = [];
if ($category) {
    $sql .= " AND c.slug = :category ";
    $params[':category'] = $category;
}

$sql .= " ORDER BY c.sort_order ASC, sc.sort_order ASC, sc.name ASC ";

/* ---------- Execute ---------- */
try {
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $rows = $stmt->fetchAll();
} catch (PDOException $e) {
    json_error('Failed to fetch subcategories.', 500, DEBUG ? ['detail' => $e->getMessage()] : []);
}

/* ---------- Normalize output types ---------- */
$subcategories = array_map(function ($r) {
    return [
        'id'            => (int) $r['id'],
        'slug'          => $r['slug'],
        'name'          => $r['name'],
        'description'   => $r['description'],
        'sort_order'    => (int) $r['sort_order'],
        'product_count' => isset($r['product_count']) ? (int) $r['product_count'] : null,
        'category'      => [
            'slug' => $r['category_slug'],
            'name' => $r['category_name'],
        ],
    ];
}, $rows);

/* ---------- Cache hint (helps cPanel) ---------- */
header('Cache-Control: public, max-age=300'); // 5 minutes

/* ---------- Respond ---------- */
json_response([
    'ok'            => true,
    'count'         => count($subcategories),
    'subcategories' => $subcategories,
]);
