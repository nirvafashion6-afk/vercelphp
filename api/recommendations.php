<?php
require_once __DIR__ . '/includes/data.php';
header('Content-Type: application/json; charset=utf-8');

// Query params:
//   anchor=<pid>            → resolve category from the anchor product
//   category=<slug>         → explicit category override
//   exclude=<pid,pid,...>   → comma-separated pids to skip
//   limit=<N>               → up to 24

$category = $_GET['category'] ?? null;
if (!$category && !empty($_GET['anchor'])) {
    $anchor = bv_get_product_by_id($_GET['anchor']);
    if ($anchor) $category = $anchor['category'] ?? ($anchor['categories'][0] ?? null);
}
$exclude = isset($_GET['exclude']) ? array_filter(explode(',', $_GET['exclude'])) : [];
$limit = isset($_GET['limit']) ? min(24, max(1, (int)$_GET['limit'])) : 12;

$products = bv_recommendations($exclude, $category, $limit);
echo json_encode(['products' => $products, 'category' => $category]);
