<?php
require_once __DIR__ . '/../../includes/data.php';
require_once __DIR__ . '/../../includes/helpers.php';

$pid = $_GET['pid'] ?? '';
$p   = get_product_by_id($pid);
if (!$p) {
    http_response_code(404);
    echo '<h1 style="padding:40px;font-family:sans-serif">404 — Product not found</h1>';
    exit;
}

$gallery        = (!empty($p['gallery']) && is_array($p['gallery'])) ? $p['gallery'] : [$p['image'] ?? ''];
$size_options   = $p['size_options'] ?? [];
$selected_size  = $size_options[0] ?? '';
$offers         = (!empty($p['offers']) && is_array($p['offers'])) ? $p['offers'] : [
    ['title' => 'Bank Offer', 'body' => 'Get ₹25 instant discount on first UPI txns on order of ₹250 and above'],
    ['title' => 'Bank Offer', 'body' => '5% Cashback on Axis Bank Card'],
    ['title' => 'Special Price', 'body' => 'Get extra 15% off (price inclusive of cashback/coupon)'],
];
$breakdown_raw = $p['rating_breakdown'] ?? [];
$breakdown = [];
if (!empty($breakdown_raw)) {
    foreach ($breakdown_raw as $b) {
        if (($b['stars'] ?? 0) >= 1 && ($b['stars'] ?? 0) <= 5) $breakdown[] = $b;
    }
}
if (empty($breakdown)) {
    foreach ([5,4,3,2,1] as $s) $breakdown[] = ['stars' => $s, 'percent' => 0, 'count' => 0];
}
$breakdown = array_slice($breakdown, 0, 5);

$reviews      = $p['reviews'] ?? [];
$lowest_recs  = $p['lowest_price_recs'] ?? [];
$recs         = $p['recommendations'] ?? [];
$clean_html   = sanitize_html($p['description_html'] ?? '');
$title_text   = $p['title'] ?? ($p['name'] ?? '');
$page_title   = mb_substr($title_text, 0, 70, 'UTF-8');

include __DIR__ . '/../../includes/layout-head.php';
?>
  <div class="main-container singleproductview">
    <header class="page-header">
      <a href="#" class="back-arrow" onclick="event.preventDefault();history.back()">
        <i class="material-icons">arrow_back</i>
      </a>
      <img src="/assets/catogary/logo.png" alt="Logo" style="width:40px;height:40px" />
      <div class="header-cart">
        <a href="/cart"><i class="material-icons">shopping_cart</i></a>
      </div>
    </header>

    <main style="padding-bottom:70px">
      <div class="singlecard">
        <div id="productCarousel" class="carousel slide" data-bs-ride="carousel" data-bs-interval="3000">
          <div class="carousel-inner">
            <?php foreach ($gallery as $i => $src): ?>
              <div class="carousel-item<?= $i === 0 ? ' active' : '' ?>">
                <img class="d-block w-100" src="<?= h($src) ?>" alt="View <?= $i + 1 ?>" />
              </div>
            <?php endforeach; ?>
          </div>
          <?php if (count($gallery) > 1): ?>
            <button class="carousel-control-prev" type="button" data-bs-target="#productCarousel" data-bs-slide="prev"><span class="carousel-control-prev-icon"></span></button>
            <button class="carousel-control-next" type="button" data-bs-target="#productCarousel" data-bs-slide="next"><span class="carousel-control-next-icon"></span></button>
            <div class="carousel-indicators">
              <?php foreach ($gallery as $i => $_): ?>
                <button type="button" data-bs-target="#productCarousel" data-bs-slide-to="<?= $i ?>" class="<?= $i === 0 ? 'active' : '' ?>" aria-label="Slide <?= $i + 1 ?>"></button>
              <?php endforeach; ?>
            </div>
          <?php endif; ?>
        </div>
      </div>

      <div class="product-details-container">
        <?php if (!empty($p['urgency'])): ?>
          <div class="urgency-banner"><?= h($p['urgency']) ?></div>
        <?php endif; ?>
        <?php if (!empty($p['stock_text'])): ?>
          <div class="stock-alert">Only <span class="text-danger"><?= h($p['stock_text']) ?></span> Left in Stock</div>
        <?php endif; ?>
        <h1 class="product-title"><?= h($title_text) ?></h1>
        <div class="d-flex align-items-center mt-2">
          <span class="rating-box"><?= h($p['rating'] ?? '4.5') ?> <i class="material-icons" style="font-size:12px">star</i></span>
          <span class="ratings-count"><?= h($p['ratings_count'] ?? '0 Ratings') ?></span>
        </div>
        <img src="/assets/images/plue-fassured.png" alt="F-Assured" class="fassured-logo" />
        <div class="price-container mt-3 d-flex align-items-center">
          <span class="final-price">&#8377;<?= h($p['price'] ?? '') ?></span>
          <del class="mrp">&#8377;<?= inr($p['mrp'] ?? 0) ?></del>
          <span class="discount"><?= h($p['discount'] ?? '') ?></span>
        </div>

        <?php if (!empty($size_options)): ?>
          <div class="size-selector-container mt-3">
            <h6 class="fw-bold mb-2">Select Size</h6>
            <div class="d-flex flex-wrap gap-2" id="sizeButtons">
              <?php foreach ($size_options as $sz): ?>
                <button type="button" data-size="<?= h($sz) ?>"
                  class="btn <?= $sz === $selected_size ? 'btn-warning' : 'btn-outline-secondary' ?>"
                  style="min-width:44px;height:44px;border-radius:50%;padding:0;display:flex;align-items:center;justify-content:center">
                  <?= h($sz) ?>
                </button>
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
              <span class="offer-title"><?= h($o['title'] ?? '') ?></span>
              <?= h($o['body'] ?? '') ?> <a href="#" class="offer-link">T&amp;C</a>
            </div>
          </div>
        <?php endforeach; ?>
      </div>

      <div class="delivery-info">
        <span class="material-icons me-3">local_shipping</span>
        <div class="delivery-text">
          <div><span class="free">FREE Delivery</span> <del class="old-fee text-muted">&#8377;75</del></div>
          <div>Delivery by &bull; <span class="fw-bold">In 3-5 days</span></div>
        </div>
      </div>

      <?php if (!empty($recs)): ?>
        <section class="compact-showcase-section">
          <div class="compact-section-header">
            <h5 class="fw-bold">Suggested for You</h5>
            <p class="text-muted small mb-3">Based on Your Activity</p>
          </div>
          <div class="compact-carousel-container">
            <div class="compact-carousel" id="recRail">
              <?php foreach ($recs as $r): ?>
                <div class="compact-product-card">
                  <a href="/product?pid=<?= h($r['pid']) ?>" class="product-link">
                    <div class="compact-image-wrapper"><img src="<?= h($r['image'] ?? '') ?>" alt="<?= h($r['name'] ?? '') ?>" loading="lazy" /></div>
                    <div class="compact-info-wrapper">
                      <p class="product-name"><?= h($r['name'] ?? '') ?></p>
                      <div class="price-line">
                        <span class="fw-bold">&#8377;<?= h($r['price'] ?? '') ?></span>
                        <del class="ms-2 text-muted small">&#8377;<?= inr($r['mrp'] ?? 0) ?></del>
                      </div>
                    </div>
                  </a>
                  <a href="#" class="compact-add-to-cart-btn"
                     data-add='<?= h(json_encode([
                        'pid' => $r['pid'] ?? null,
                        'name' => $r['name'] ?? '',
                        'image' => $r['image'] ?? '',
                        'price' => $r['price'] ?? 0,
                        'mrp' => $r['mrp'] ?? 0,
                     ], JSON_UNESCAPED_UNICODE)) ?>'>Add to cart</a>
                </div>
              <?php endforeach; ?>
            </div>
          </div>
        </section>
      <?php endif; ?>

      <?php if (!empty($lowest_recs)): ?>
        <section class="compact-showcase-section">
          <div class="compact-section-header">
            <h5 class="fw-bold">Lowest Price of the Year</h5>
            <p class="text-muted small mb-3">On Home Appliances</p>
          </div>
          <div class="compact-carousel-container">
            <div class="compact-carousel" id="lowestRail">
              <?php foreach ($lowest_recs as $r): ?>
                <div class="compact-product-card">
                  <a href="/product?pid=<?= h($r['pid']) ?>" class="product-link">
                    <div class="compact-image-wrapper"><img src="<?= h($r['image'] ?? '') ?>" alt="<?= h($r['name'] ?? '') ?>" loading="lazy" /></div>
                    <div class="compact-info-wrapper">
                      <p class="product-name"><?= h($r['name'] ?? '') ?></p>
                      <div class="price-line">
                        <span class="fw-bold">&#8377;<?= h($r['price'] ?? '') ?></span>
                        <del class="ms-2 text-muted small">&#8377;<?= inr($r['mrp'] ?? 0) ?></del>
                      </div>
                    </div>
                  </a>
                  <a href="#" class="compact-add-to-cart-btn"
                     data-add='<?= h(json_encode([
                        'pid' => $r['pid'] ?? null,
                        'name' => $r['name'] ?? '',
                        'image' => $r['image'] ?? '',
                        'price' => $r['price'] ?? 0,
                        'mrp' => $r['mrp'] ?? 0,
                     ], JSON_UNESCAPED_UNICODE)) ?>'>Add to cart</a>
                </div>
              <?php endforeach; ?>
            </div>
          </div>
        </section>
      <?php endif; ?>

      <?php if (!empty($clean_html)): ?>
        <div class="product-description-section">
          <h4 class="fw-bold">Product Details</h4>
          <div class="text-muted mt-2"><?= $clean_html ?></div>
        </div>
      <?php endif; ?>

      <div class="reviews-container">
        <div class="d-flex justify-content-between align-items-center mb-3">
          <h3 class="fw-bold m-0">Ratings &amp; Reviews</h3>
          <button class="btn btn-outline-secondary btn-sm">Rate Product</button>
        </div>
        <div class="rating-summary-section">
          <div class="overall-rating text-center">
            <div class="rating-value"><?= h($p['rating'] ?? '4.5') ?> <i class="material-icons align-middle" style="color:#388e3c">star</i></div>
            <p class="text-muted small"><?= h($p['ratings_count'] ?? '0 Ratings') ?></p>
          </div>
          <div class="rating-breakdown flex-grow-1">
            <?php foreach ($breakdown as $b): ?>
              <div class="d-flex align-items-center small">
                <span><?= h($b['stars']) ?>&#9733;</span>
                <div class="progress mx-2 flex-grow-1" style="height:6px">
                  <div class="progress-bar bg-success" style="width:<?= h($b['percent'] ?? 0) ?>%"></div>
                </div>
                <span class="text-muted"><?= inr($b['count'] ?? 0) ?></span>
              </div>
            <?php endforeach; ?>
          </div>
        </div>
        <div class="review-list">
          <?php foreach ($reviews as $r): ?>
            <div class="review-card">
              <div class="d-flex align-items-center mb-2">
                <span class="rating-box me-2"><?= h($r['rating'] ?? '') ?> <i class="material-icons" style="font-size:12px">star</i></span>
                <h5 class="fw-bold mb-0 small"><?= h($r['title'] ?? '') ?></h5>
              </div>
              <p class="small"><?= h($r['body'] ?? '') ?></p>
              <p class="text-muted small m-0"><?= h($r['author'] ?? '') ?> | <?= h($r['date'] ?? '') ?></p>
            </div>
          <?php endforeach; ?>
        </div>
      </div>
    </main>

    <div class="footerbuttonbuy d-flex">
      <button class="btn1 btncart w-50" id="btnAddCart">ADD TO CART</button>
      <button class="btn1 btnbuy w-50" id="btnBuyNow">BUY NOW</button>
    </div>
  </div>

  <script>
    // Product state — exported via JSON so client-side cart logic can use it.
    var PRODUCT = <?= json_encode([
        'pid'   => $p['pid'] ?? null,
        'name'  => $title_text,
        'image' => $p['image'] ?? '',
        'price' => $p['price'] ?? 0,
        'mrp'   => $p['mrp'] ?? 0,
    ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>;
    var SELECTED_SIZE = <?= json_encode($selected_size) ?>;

    (function () {
      var btns = document.querySelectorAll('#sizeButtons button');
      btns.forEach(function (b) {
        b.addEventListener('click', function () {
          SELECTED_SIZE = b.dataset.size;
          btns.forEach(function (x) {
            x.classList.remove('btn-warning'); x.classList.add('btn-outline-secondary');
          });
          b.classList.add('btn-warning'); b.classList.remove('btn-outline-secondary');
        });
      });

      function addToCart(qty) {
        qty = qty || 1;
        var cart = JSON.parse(localStorage.getItem('cart') || '[]');
        var existing = cart.find(function (i) { return i.pid === PRODUCT.pid && i.size === SELECTED_SIZE; });
        if (existing) existing.qty += qty;
        else cart.push({
          pid: PRODUCT.pid, qty: qty, name: PRODUCT.name, image: PRODUCT.image,
          price: PRODUCT.price, mrp: PRODUCT.mrp, size: SELECTED_SIZE
        });
        localStorage.setItem('cart', JSON.stringify(cart));
      }

      var addBtn = document.getElementById('btnAddCart');
      if (addBtn) addBtn.addEventListener('click', function () { addToCart(1); window.location.href = '/cart'; });

      var buyBtn = document.getElementById('btnBuyNow');
      if (buyBtn) buyBtn.addEventListener('click', function () { addToCart(1); window.location.href = '/cart'; });

      // Rec-card "Add to cart" buttons
      document.querySelectorAll('.compact-add-to-cart-btn').forEach(function (a) {
        a.addEventListener('click', function (e) {
          e.preventDefault();
          try {
            var item = JSON.parse(a.dataset.add || '{}');
            if (!item.pid) return;
            var cart = JSON.parse(localStorage.getItem('cart') || '[]');
            item.qty = 1;
            cart.push(item);
            localStorage.setItem('cart', JSON.stringify(cart));
            window.location.href = '/cart';
          } catch (err) {}
        });
      });

      // Auto-scroll the two compact rails
      [document.getElementById('recRail'), document.getElementById('lowestRail')].forEach(function (rail) {
        if (!rail || rail.children.length === 0) return;
        var initial = rail.children.length;
        for (var i = 0; i < initial; i++) rail.appendChild(rail.children[i].cloneNode(true));
        var frame = null;
        function step() {
          rail.scrollLeft += 0.7;
          if (rail.scrollLeft >= rail.scrollWidth / 2) rail.scrollLeft = 0;
          frame = requestAnimationFrame(step);
        }
        function start() { if (!frame) frame = requestAnimationFrame(step); }
        function stop() { if (frame) { cancelAnimationFrame(frame); frame = null; } }
        rail.addEventListener('mouseenter', stop);
        rail.addEventListener('mouseleave', start);
        start();
      });
    })();
  </script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
