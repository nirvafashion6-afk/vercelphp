<?php
// Expects $p to be in scope with at least: pid, image, name|title, price, mrp,
// discount, wow_price, rating.
$name = $p['name'] ?? $p['title'] ?? '';
$rating = (float)($p['rating'] ?? 4.5);
$full = (int)floor($rating);
$half = ($rating - $full) >= 0.5;
?>
<a href="/singlepageview.php?pid=<?= bv_e($p['pid']) ?>" class="products">
    <div class="productcard">
        <div class="imagecontainer">
            <img src="<?= bv_e($p['image']) ?>" class="productimage" loading="lazy" alt="<?= bv_e($name) ?>">
        </div>
        <div class="product-info">
            <p class="product-name"><?= bv_e($name) ?></p>
            <div class="price-line">
                <span class="selling-price">₹<?= bv_e($p['price']) ?></span>
                <del class="mrp">₹<?= bv_inr($p['mrp'] ?? 0) ?></del>
                <span class="discount"><?= bv_e($p['discount'] ?? '') ?></span>
            </div>
            <div class="wow-offer">
                <img class="wow-badge" src="/assets/catogary/wow.webp" alt="WOW Offer">
                <span class="wow-price">₹<?= bv_e($p['wow_price'] ?? (int)round(($p['price'] ?? 0) * 0.95)) ?></span>
                <span class="offer-text">with 2 offers</span>
            </div>
            <div class="rating-line">
                <div class="rating-stars">
                    <?php for ($i = 1; $i <= 5; $i++):
                        if ($i <= $full) echo '<i class="bi bi-star-fill"></i>';
                        elseif ($i === $full + 1 && $half) echo '<i class="bi bi-star-half"></i>';
                        else echo '<i class="bi bi-star"></i>';
                    endfor; ?>
                </div>
                <img class="fassured-logo-small" src="/assets/catogary/assured.png" alt="F-Assured">
            </div>
        </div>
    </div>
</a>
