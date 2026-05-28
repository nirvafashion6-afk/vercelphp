<?php
require_once __DIR__ . '/../includes/data.php';
require_once __DIR__ . '/../includes/helpers.php';

$PAGE_SIZE = 20;

if (isset($_GET['anchor']) || isset($_GET['category']) || isset($_GET['exclude'])) {
    $category = $_GET['category'] ?? null;
    if (!$category && !empty($_GET['anchor'])) {
        $anchor = get_product_by_id($_GET['anchor']);
        if ($anchor) {
            $category = $anchor['category'] ?? (($anchor['categories'] ?? [null])[0] ?? null);
        }
    }
    $exclude = array_filter(explode(',', (string)($_GET['exclude'] ?? '')));
    $limit   = min((int)($_GET['limit'] ?? 12), 24);

    $products = get_recommendations([
        'excludePids' => $exclude,
        'category'    => $category,
        'limit'       => $limit,
    ]);
    json_response(['products' => $products, 'category' => $category]);
}

$page  = max(1, (int)($_GET['page'] ?? 1));
$all   = get_all_products();
$slice = paginate($all, $page, $PAGE_SIZE);

json_response([
    'products' => $slice,
    'page'     => $page,
    'total'    => count($all),
    'pageSize' => $PAGE_SIZE,
]);
