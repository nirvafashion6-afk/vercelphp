<?php
require_once __DIR__ . '/includes/data.php';
$bv_title = 'Shop Online for Fashion, Electronics & More | DMLinan';
$products = bv_get_all_products();
$categories = bv_get_categories();
$first_page = bv_paginate($products, 1, 20);
include __DIR__ . '/includes/header.php';
?>

<main>
    <section class="main-banner-container p-2">
        <div id="mainCarousel" class="carousel slide" data-bs-ride="carousel">
            <div class="carousel-inner rounded-3">
                <div class="carousel-item active"><img src="/assets/catogary/banner1.webp" class="d-block w-100" alt="Banner 1"></div>
                <div class="carousel-item"><img src="/assets/catogary/banner2.webp" class="d-block w-100" alt="Banner 2"></div>
            </div>
        </div>
    </section>

    <section class="categories-container">
        <div class="categories-grid">
            <?php foreach ($categories as $c): ?>
                <div class="category-item">
                    <a href="/category_products.php?category=<?= urlencode($c['slug']) ?>">
                        <img src="<?= bv_e($c['image']) ?>" alt="<?= bv_e($c['alt']) ?>">
                        <p class="category-label"><?= bv_e($c['label']) ?></p>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
    </section>

    <div class="deal-banner">
        <div class="deal-left">
            <div class="deal-title">Deals of the Day</div>
            <div class="deal-timer"><span id="deal-timer">05:38</span></div>
        </div>
        <div class="sale-badge">SALE IS LIVE</div>
    </div>

    <section class="products-section">
        <div id="mainbody" class="mainbody" data-load-url="/load_products.php" data-start-page="2">
            <?php foreach ($first_page as $p): include __DIR__ . '/includes/product_card.php'; endforeach; ?>
        </div>
        <div id="grid-loader" style="text-align:center;padding:20px">
            <div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div>
        </div>
    </section>
</main>

<?php include __DIR__ . '/includes/footer.php'; ?>
