<?php
require_once __DIR__ . '/includes/data.php';
header('Content-Type: text/html; charset=utf-8');

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$page = max(1, $page);
$products = bv_get_all_products();
$slice = bv_paginate($products, $page, 20);
if (empty($slice)) { exit; } // empty body signals "no more pages"

foreach ($slice as $p) {
    include __DIR__ . '/includes/product_card.php';
}
