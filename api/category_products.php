<?php
require_once __DIR__ . '/includes/data.php';
$slug = isset($_GET['category']) ? $_GET['category'] : '';
$products = bv_get_products_by_category($slug);
$bv_title = ($slug ?: 'Category') . ' | DMLinan';
include __DIR__ . '/includes/header.php';
?>

<main>
    <div style="padding:12px 16px;background:#fff">
        <h2 style="font-size:18px;margin:0"><?= bv_e($slug) ?></h2>
        <p class="text-muted small mb-0"><?= count($products) ?> results</p>
    </div>

    <section class="products-section">
        <div id="mainbody" class="mainbody">
            <?php if (empty($products)): ?>
                <div style="padding:40px;text-align:center;grid-column:1 / -1;background:#fff">
                    No products in this category yet.
                </div>
            <?php else: ?>
                <?php foreach ($products as $p): include __DIR__ . '/includes/product_card.php'; endforeach; ?>
            <?php endif; ?>
        </div>
    </section>
</main>

<?php include __DIR__ . '/includes/footer.php'; ?>
