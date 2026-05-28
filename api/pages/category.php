<?php
require_once __DIR__ . '/../../includes/data.php';
require_once __DIR__ . '/../../includes/helpers.php';

$category   = $_GET['category'] ?? '';
$products   = get_products_by_category($category);
$categories = get_categories();

$page_title = ($category ?: 'Category') . ' | FLIP MART';
$menu_categories = $categories;
include __DIR__ . '/../../includes/layout-head.php';
?>
  <div class="main-container">
    <?php include __DIR__ . '/../../includes/header.php'; ?>
    <main>
      <div style="padding:12px 16px;background:#fff">
        <h2 style="font-size:18px;margin:0"><?= h($category) ?></h2>
        <p class="text-muted small mb-0"><?= count($products) ?> results</p>
      </div>
      <section class="products-section">
        <div class="mainbody">
          <?php if (empty($products)): ?>
            <div style="padding:40px;text-align:center;grid-column:1 / -1;background:#fff">
              No products in this category yet.
            </div>
          <?php else: ?>
            <?php foreach ($products as $p) include __DIR__ . '/../../includes/product-card.php'; ?>
          <?php endif; ?>
        </div>
      </section>
    </main>
  </div>
  <?php include __DIR__ . '/../../includes/footer.php'; ?>
