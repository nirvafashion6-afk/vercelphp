<?php
// Renders one product card. Expects $p (associative array) in scope.
$rating_raw = $p['rating'] ?? '4.5';
$stars = round((float)$rating_raw * 2) / 2;
$full = (int)floor($stars);
$has_half = (($stars - $full) >= 0.5);
$wow_price = $p['wow_price'] ?? (int)round(((float)($p['price'] ?? 0)) * 0.95);
?>
<a href="/product?pid=<?= h($p['pid'] ?? '') ?>" class="products">
  <div class="productcard">
    <div class="imagecontainer">
      <img src="<?= h($p['image'] ?? '') ?>" class="productimage" loading="lazy" alt="<?= h($p['name'] ?? ($p['title'] ?? '')) ?>" />
    </div>
    <div class="product-info">
      <p class="product-name"><?= h($p['name'] ?? ($p['title'] ?? '')) ?></p>
      <div class="price-line">
        <span class="selling-price">&#8377;<?= h($p['price'] ?? '') ?></span>
        <del class="mrp">&#8377;<?= inr($p['mrp'] ?? 0) ?></del>
        <span class="discount"><?= h($p['discount'] ?? '') ?></span>
      </div>
      <div class="wow-offer">
        <img class="wow-badge" src="/assets/catogary/wow.webp" alt="WOW Offer" />
        <span class="wow-price">&#8377;<?= h($wow_price) ?></span>
        <span class="offer-text">with 2 offers</span>
      </div>
      <div class="rating-line">
        <div class="rating-stars">
          <?php for ($i = 1; $i <= 5; $i++):
            if ($i <= $full) echo '<i class="bi bi-star-fill"></i>';
            elseif ($has_half && $i === $full + 1) echo '<i class="bi bi-star-half"></i>';
            else echo '<i class="bi bi-star"></i>';
          endfor; ?>
        </div>
        <img class="fassured-logo-small" src="/assets/catogary/assured.png" alt="F-Assured" />
      </div>
    </div>
  </div>
</a>
