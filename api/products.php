<?php
/**
 * ============================================================
 * VUEMAX API — GET products (list, filter, search, sort, paginate)
 * ============================================================
 * File: /api/products.php
 *
 * GET parameters (all optional):
 *   ?category=fencing           Filter by category slug
 *   ?subcategory=diamond-mesh   Filter by subcategory slug
 *   ?featured=1                 Only featured products
 *   ?search=mesh                Search name + short_desc + long_desc
 *   ?sort=popular               popular (default) | price_asc | price_desc | newest | name
 *   ?page=1                     Page number (default 1)
 *   ?per_page=12                Items per page (default 12, max 60)
 *   ?include_specs=1            Include product_specs rows per product
 *   ?include_features=1         Include product_features rows per product
 *
 * Example:
 *   GET /api/products.php?category=fencing&sort=price_asc&page=1&per_page=9
 *
 * Response:
 * {
 *   "ok": true,
 *   "count": 9,               // items in this page
 *   "total": 8,               // total matching (before pagination)
 *   "page": 1,
 *   "per_page": 9,
 *   "pages": 1,
 *   "products": [ { ...product... }, ... ]
 * }
 * ============================================================
 */

require_once __DIR__ . '/config.php';
require_db($pdo, $db_error);
require_method('GET');

/* ---------- Read + validate query params ---------- */
$category     = isset($_GET['category'])    ? clean_str($_GET['category'], 80)   : null;
$subcategory  = isset($_GET['subcategory']) ? clean_str($_GET['subcategory'], 80): null;
$featured     = isset($_GET['featured'])    && $_GET['featured'] === '1';
$search       = isset($_GET['search'])      ? clean_str($_GET['search'], 120)    : null;
$sort         = isset($_GET['sort'])        ? clean_str($_GET['sort'], 20)       : 'popular';
$page         = max(1, (int) ($_GET['page'] ?? 1));
$per_page     = min(60, max(1, (int) ($_GET['per_page'] ?? 12)));
$with_specs   = isset($_GET['include_specs'])    && $_GET['include_specs']    === '1';
$with_feats   = isset($_GET['include_features']) && $_GET['include_features'] === '1';

$offset = ($page - 1) * $per_page;

/* ---------- Whitelist sort options (prevents SQL injection) ---------- */
$sort_map = [
    'popular'    => 'p.is_featured DESC, p.reviews_count DESC, p.sort_order ASC',
    'price_asc'  => 'p.price_usd ASC, p.sort_order ASC',
    'price_desc' => 'p.price_usd DESC, p.sort_order ASC',
    'newest'     => 'p.created_at DESC, p.id DESC',
    'name'       => 'p.name ASC',
];
$order_sql = $sort_map[$sort] ?? $sort_map['popular'];

/* ---------- Build WHERE clause ---------- */
$where  = ["p.is_active = 1", "sc.is_active = 1", "c.is_active = 1"];
$params = [];

if ($category) {
    $where[] = "c.slug = :category";
    $params[':category'] = $category;
}
if ($subcategory) {
    $where[] = "sc.slug = :subcategory";
    $params[':subcategory'] = $subcategory;
}
if ($featured) {
    $where[] = "p.is_featured = 1";
}
if ($search) {
    // Search name + short_desc + long_desc
    $where[] = "(p.name LIKE :search OR p.short_desc LIKE :search OR p.long_desc LIKE :search)";
    $params[':search'] = '%' . $search . '%';
}

$where_sql = implode(' AND ', $where);

/* ---------- Count total matching ---------- */
try {
    $count_sql = "
        SELECT COUNT(*) AS total
        FROM products p
        JOIN subcategories sc ON sc.id = p.subcategory_id
        JOIN categories    c  ON c.id  = sc.category_id
        WHERE {$where_sql}
    ";
    $stmt = $pdo->prepare($count_sql);
    $stmt->execute($params);
    $total = (int) $stmt->fetchColumn();
} catch (PDOException $e) {
    json_error('Failed to count products.', 500, DEBUG ? ['detail' => $e->getMessage()] : []);
}

/* ---------- Fetch page of products ---------- */
try {
    $sql = "
        SELECT
            p.id,
            p.slug,
            p.name,
            p.short_desc,
            p.long_desc,
            p.unit,
            p.price_usd,
            p.price_zwl,
            p.image,
            p.gallery,
            p.badge,
            p.rating,
            p.reviews_count,
            p.is_featured,
            p.sort_order,
            p.created_at,
            c.id   AS category_id,
            c.slug AS category_slug,
            c.name AS category_name,
            sc.id   AS subcategory_id,
            sc.slug AS subcategory_slug,
            sc.name AS subcategory_name
        FROM products p
        JOIN subcategories sc ON sc.id = p.subcategory_id
        JOIN categories    c  ON c.id  = sc.category_id
        WHERE {$where_sql}
        ORDER BY {$order_sql}
        LIMIT :limit OFFSET :offset
    ";
    $stmt = $pdo->prepare($sql);
    foreach ($params as $k => $v) {
        $stmt->bindValue($k, $v);
    }
    $stmt->bindValue(':limit',  $per_page, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset,   PDO::PARAM_INT);
    $stmt->execute();
    $rows = $stmt->fetchAll();
} catch (PDOException $e) {
    json_error('Failed to fetch products.', 500, DEBUG ? ['detail' => $e->getMessage()] : []);
}

/* ---------- Normalize rows ---------- */
$products = array_map(function ($r) {
    return [
        'id'                => (int)    $r['id'],
        'slug'              =>          $r['slug'],
        'name'              =>          $r['name'],
        'short_desc'        =>          $r['short_desc'],
        'long_desc'         =>          $r['long_desc'],
        'unit'              =>          $r['unit'],
        'price_usd'         => $r['price_usd'] !== null ? (float) $r['price_usd'] : null,
        'price_zwl'         => $r['price_zwl'] !== null ? (float) $r['price_zwl'] : null,
        'image'             =>          $r['image'],
        'gallery'           => $r['gallery'] ? json_decode($r['gallery'], true) : [],
        'badge'             =>          $r['badge'],
        'rating'            => $r['rating'] !== null ? (float) $r['rating'] : null,
        'reviews_count'     => (int)    $r['reviews_count'],
        'is_featured'       => (bool)   $r['is_featured'],
        'sort_order'        => (int)    $r['sort_order'],
        'created_at'        =>          $r['created_at'],
        'category'          => [
            'id'   => (int) $r['category_id'],
            'slug' =>       $r['category_slug'],
            'name' =>       $r['category_name'],
        ],
        'subcategory'       => [
            'id'   => (int) $r['subcategory_id'],
            'slug' =>       $r['subcategory_slug'],
            'name' =>       $r['subcategory_name'],
        ],
    ];
}, $rows);

/* ---------- Optionally attach specs + features ---------- */
if ($with_specs || $with_feats) {
    $ids = array_column($products, 'id');
    if ($ids) {
        $placeholders = implode(',', array_fill(0, count($ids), '?'));

        // Specs
        if ($with_specs) {
            try {
                $stmt = $pdo->prepare("
                    SELECT product_id, label, value
                    FROM product_specs
                    WHERE product_id IN ($placeholders)
                    ORDER BY product_id, sort_order ASC
                ");
                $stmt->execute($ids);
                $by_product = [];
                foreach ($stmt->fetchAll() as $row) {
                    $by_product[(int) $row['product_id']][] = [
                        'label' => $row['label'],
                        'value' => $row['value'],
                    ];
                }
                foreach ($products as &$p) {
                    $p['specs'] = $by_product[$p['id']] ?? [];
                }
                unset($p);
            } catch (PDOException $e) { /* silent */ }
        }

        // Features
        if ($with_feats) {
            try {
                $stmt = $pdo->prepare("
                    SELECT product_id, text
                    FROM product_features
                    WHERE product_id IN ($placeholders)
                    ORDER BY product_id, sort_order ASC
                ");
                $stmt->execute($ids);
                $by_product = [];
                foreach ($stmt->fetchAll() as $row) {
                    $by_product[(int) $row['product_id']][] = $row['text'];
                }
                foreach ($products as &$p) {
                    $p['features'] = $by_product[$p['id']] ?? [];
                }
                unset($p);
            } catch (PDOException $e) { /* silent */ }
        }
    }
}

/* ---------- Pagination math ---------- */
$pages = $per_page > 0 ? (int) ceil($total / $per_page) : 0;

/* ---------- Cache hint ---------- */
header('Cache-Control: public, max-age=180'); // 3 minutes

/* ---------- Respond ---------- */
json_response([
    'ok'       => true,
    'count'    => count($products),
    'total'    => $total,
    'page'     => $page,
    'per_page' => $per_page,
    'pages'    => $pages,
    'sort'     => $sort,
    'filters'  => [
        'category'    => $category,
        'subcategory' => $subcategory,
        'featured'    => $featured,
        'search'      => $search,
    ],
    'products' => $products,
]);