<?php
require_once __DIR__ . '/includes/data.php';

$pid = isset($_GET['pid']) ? $_GET['pid'] : '';
$p = bv_get_product_by_id($pid);
if (!$p) {
    http_response_code(404);
    $bv_title = 'Product not found';
    include __DIR__ . '/includes/header.php';
    echo '<main style="padding:40px;text-align:center"><h3>Product not found</h3><a href="/" class="btn btn-primary mt-3">Back to Home</a></main>';
    include __DIR__ . '/includes/footer.php';
    exit;
}

$bv_title = ($p['title'] ?? $p['name'] ?? 'Product') . ' | DMLinan';
$bv_show_search = false;
$gallery = !empty($p['gallery']) ? $p['gallery'] : [$p['image'] ?? ''];
$recs = $p['recommendations'] ?? [];
$lowest = $p['lowest_price_recs'] ?? [];
$offers = !empty($p['offers']) ? $p['offers'] : [
    ['title' => 'Bank Offer', 'body' => 'Get ₹25 instant discount on first UPI txns on order of ₹250 and above'],
    ['title' => 'Bank Offer', 'body' => '5% Cashback on Axis Bank Card'],
    ['title' => 'Special Price', 'body' => 'Get extra 15% off (price inclusive of cashback/coupon)'],
];
$breakdown = !empty($p['rating_breakdown']) ? array_values(array_filter($p['rating_breakdown'], function ($r) { return ($r['stars'] ?? 0) >= 1 && ($r['stars'] ?? 0) <= 5; })) : [];
if (empty($breakdown)) {
    foreach ([5,4,3,2,1] as $s) $breakdown[] = ['stars' => $s, 'percent' => 0, 'count' => 0];
}
$breakdown = array_slice($breakdown, 0, 5);
$reviews = $p['reviews'] ?? [];
$sizes = $p['size_options'] ?? [];
$cart_item_json = htmlspecialchars(json_encode([
    'pid'   => $p['pid'],
    'name'  => $p['title'] ?? $p['name'],
    'image' => $p['image'],
    'price' => $p['price'] ?? 0,
    'mrp'   => $p['mrp'] ?? 0,
]), ENT_QUOTES, 'UTF-8');

include __DIR__ . '/includes/header.php';
?>

<main class="singleproductview" style="padding-bottom:70px">
    <div class="singlecard">
        <div id="productCarousel" class="carousel slide" data-bs-ride="carousel" data-bs-interval="3000">
            <div class="carousel-inner">
                <?php foreach ($gallery as $i => $src): ?>
                    <div class="carousel-item <?= $i === 0 ? 'active' : '' ?>">
                        <img class="d-block w-100" src="<?= bv_e($src) ?>" alt="View <?= $i + 1 ?>">
                    </div>
                <?php endforeach; ?>
            </div>
            <?php if (count($gallery) > 1): ?>
                <button class="carousel-control-prev" type="button" data-bs-target="#productCarousel" data-bs-slide="prev"><span class="carousel-control-prev-icon"></span></button>
                <button class="carousel-control-next" type="button" data-bs-target="#productCarousel" data-bs-slide="next"><span class="carousel-control-next-icon"></span></button>
                <div class="carousel-indicators">
                    <?php for ($i = 0; $i < count($gallery); $i++): ?>
                        <button type="button" data-bs-target="#productCarousel" data-bs-slide-to="<?= $i ?>" class="<?= $i === 0 ? 'active' : '' ?>" aria-label="Slide <?= $i + 1 ?>"></button>
                    <?php endfor; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="product-details-container">
        <?php if (!empty($p['urgency'])): ?><div class="urgency-banner"><?= bv_e($p['urgency']) ?></div><?php endif; ?>
        <?php if (!empty($p['stock_text'])): ?><div class="stock-alert">Only <span class="text-danger"><?= bv_e($p['stock_text']) ?></span> Left in Stock</div><?php endif; ?>
        <h1 class="product-title"><?= bv_e($p['title'] ?? $p['name']) ?></h1>
        <div class="d-flex align-items-center mt-2">
            <span class="rating-box"><?= bv_e($p['rating'] ?? '4.5') ?> <i class="material-icons" style="font-size:12px">star</i></span>
            <span class="ratings-count"><?= bv_e($p['ratings_count'] ?? '0 Ratings') ?></span>
        </div>
        <img src="/assets/images/plue-fassured.png" alt="F-Assured" class="fassured-logo">
        <div class="price-container mt-3 d-flex align-items-center">
            <span class="final-price">₹<?= bv_e($p['price']) ?></span>
            <del class="mrp">₹<?= bv_inr($p['mrp'] ?? 0) ?></del>
            <span class="discount"><?= bv_e($p['discount'] ?? '') ?></span>
        </div>

        <?php if (!empty($sizes)): ?>
            <div class="size-selector-container mt-3">
                <h6 class="fw-bold mb-2">Select Size</h6>
                <div class="d-flex flex-wrap gap-2" id="size-options">
                    <?php foreach ($sizes as $i => $sz): ?>
                        <button type="button" data-size="<?= bv_e($sz) ?>" class="btn btn-outline-secondary <?= $i === 0 ? 'selected' : '' ?>" style="min-width:44px;height:44px;border-radius:50%;padding:0;display:flex;align-items:center;justify-content:center"><?= bv_e($sz) ?></button>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <div class="offers-container">
        <h6 class="fw-bold mb-3">Available offers</h6>
        <?php foreach ($offers as $o): ?>
            <div class="offer-item">
                <i class="material-icons offer-icon">sell</i>
                <div class="offer-text">
                    <span class="offer-title"><?= bv_e($o['title']) ?></span>
                    <?= bv_e($o['body']) ?> <a href="#" class="offer-link">T&amp;C</a>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="delivery-info">
        <span class="material-icons me-3">local_shipping</span>
        <div class="delivery-text">
            <div><span class="free">FREE Delivery</span> <del class="old-fee text-muted">₹75</del></div>
            <div>Delivery by • <span class="fw-bold">In 3-5 days</span></div>
        </div>
    </div>

    <?php if (!empty($recs)): ?>
        <section class="compact-showcase-section">
            <div class="compact-section-header">
                <h5 class="fw-bold">Suggested for You</h5>
                <p class="text-muted small mb-3">Based on Your Activity</p>
            </div>
            <div class="compact-carousel-container">
                <div class="compact-carousel">
                    <?php foreach ($recs as $r): ?>
                        <div class="compact-product-card">
                            <a href="/singlepageview.php?pid=<?= bv_e($r['pid']) ?>" class="product-link">
                                <div class="compact-image-wrapper"><img src="<?= bv_e($r['image']) ?>" alt="<?= bv_e($r['name']) ?>" loading="lazy"></div>
                                <div class="compact-info-wrapper">
                                    <p class="product-name"><?= bv_e($r['name']) ?></p>
                                    <div class="price-line">
                                        <span class="fw-bold">₹<?= bv_e($r['price']) ?></span>
                                        <del class="ms-2 text-muted small">₹<?= bv_inr($r['mrp'] ?? 0) ?></del>
                                    </div>
                                </div>
                            </a>
                            <a href="/cart.php" class="compact-add-to-cart-btn"
                               data-add-to-cart='<?= htmlspecialchars(json_encode(['pid'=>$r['pid'],'name'=>$r['name'],'image'=>$r['image'],'price'=>$r['price'],'mrp'=>$r['mrp']]), ENT_QUOTES, 'UTF-8') ?>'
                               data-then-go="/cart.php">Add to cart</a>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>
    <?php endif; ?>

    <?php if (!empty($lowest)): ?>
        <section class="compact-showcase-section">
            <div class="compact-section-header">
                <h5 class="fw-bold">Lowest Price of the Year</h5>
                <p class="text-muted small mb-3">On Home Appliances</p>
            </div>
            <div class="compact-carousel-container">
                <div class="compact-carousel">
                    <?php foreach ($lowest as $r): ?>
                        <div class="compact-product-card">
                            <a href="/singlepageview.php?pid=<?= bv_e($r['pid']) ?>" class="product-link">
                                <div class="compact-image-wrapper"><img src="<?= bv_e($r['image']) ?>" alt="<?= bv_e($r['name']) ?>" loading="lazy"></div>
                                <div class="compact-info-wrapper">
                                    <p class="product-name"><?= bv_e($r['name']) ?></p>
                                    <div class="price-line">
                                        <span class="fw-bold">₹<?= bv_e($r['price']) ?></span>
                                        <del class="ms-2 text-muted small">₹<?= bv_inr($r['mrp'] ?? 0) ?></del>
                                    </div>
                                </div>
                            </a>
                            <a href="/cart.php" class="compact-add-to-cart-btn"
                               data-add-to-cart='<?= htmlspecialchars(json_encode(['pid'=>$r['pid'],'name'=>$r['name'],'image'=>$r['image'],'price'=>$r['price'],'mrp'=>$r['mrp']]), ENT_QUOTES, 'UTF-8') ?>'
                               data-then-go="/cart.php">Add to cart</a>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>
    <?php endif; ?>

    <?php if (!empty($p['description_html'])): ?>
        <div class="product-description-section">
            <h4 class="fw-bold">Product Details</h4>
            <div class="text-muted mt-2"><?= bv_sanitize_html($p['description_html']) ?></div>
        </div>
    <?php endif; ?>

    <div class="reviews-container">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h3 class="fw-bold m-0">Ratings &amp; Reviews</h3>
            <button class="btn btn-outline-secondary btn-sm">Rate Product</button>
        </div>
        <div class="rating-summary-section">
            <div class="overall-rating text-center">
                <div class="rating-value"><?= bv_e($p['rating'] ?? '4.5') ?> <i class="material-icons align-middle" style="color:#388e3c">star</i></div>
                <p class="text-muted small"><?= bv_e($p['ratings_count'] ?? '0 Ratings') ?></p>
            </div>
            <div class="rating-breakdown flex-grow-1">
                <?php foreach ($breakdown as $b): ?>
                    <div class="d-flex align-items-center small">
                        <span><?= (int)$b['stars'] ?>★</span>
                        <div class="progress mx-2 flex-grow-1" style="height:6px"><div class="progress-bar bg-success" style="width:<?= (float)($b['percent'] ?? 0) ?>%"></div></div>
                        <span class="text-muted"><?= bv_inr($b['count'] ?? 0) ?></span>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <div class="review-list">
            <?php foreach ($reviews as $r): ?>
                <div class="review-card">
                    <div class="d-flex align-items-center mb-2">
                        <span class="rating-box me-2"><?= bv_e($r['rating']) ?> <i class="material-icons" style="font-size:12px">star</i></span>
                        <h5 class="fw-bold mb-0 small"><?= bv_e($r['title']) ?></h5>
                    </div>
                    <p class="small"><?= bv_e($r['body']) ?></p>
                    <p class="text-muted small m-0"><?= bv_e($r['author']) ?> | <?= bv_e($r['date']) ?></p>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</main>

<div class="footerbuttonbuy d-flex">
    <button class="btn1 btncart w-50" id="pdp-add-to-cart">ADD TO CART</button>
    <button class="btn1 btnbuy w-50" id="pdp-buy-now">BUY NOW</button>
</div>

<script>
(function () {
    var item = <?= json_encode([
        'pid'   => $p['pid'],
        'name'  => $p['title'] ?? $p['name'],
        'image' => $p['image'],
        'price' => $p['price'] ?? 0,
        'mrp'   => $p['mrp'] ?? 0,
    ]) ?>;
    var selectedSize = '';
    document.querySelectorAll('#size-options button').forEach(function (b) {
        b.addEventListener('click', function () {
            document.querySelectorAll('#size-options button').forEach(function (x) { x.classList.remove('btn-warning'); x.classList.add('btn-outline-secondary'); });
            b.classList.remove('btn-outline-secondary');
            b.classList.add('btn-warning');
            selectedSize = b.dataset.size;
        });
    });
    function add() { window.BV.addToCart(Object.assign({}, item, { size: selectedSize }), 1); }
    document.getElementById('pdp-add-to-cart').addEventListener('click', function () { add(); location.href = '/cart.php'; });
    document.getElementById('pdp-buy-now').addEventListener('click', function () { add(); location.href = '/cart.php'; });
})();
</script>

<?php include __DIR__ . '/includes/footer.php'; ?>
