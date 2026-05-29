<?php
require_once __DIR__ . '/../../includes/data.php';
require_once __DIR__ . '/../../includes/helpers.php';

$PAGE_SIZE = 20;
$products   = get_all_products();
$categories = get_categories();
$first_page = paginate($products, 1, $PAGE_SIZE);
$total      = count($products);

$page_title = 'Shop Online for Fashion, Electronics & More | FLIP MART';
$menu_categories = $categories;
include __DIR__ . '/../../includes/layout-head.php';
?>
  <div class="main-container">
    <?php include __DIR__ . '/../../includes/header.php'; ?>
    <main>
      <section class="main-banner-container p-2">
        <div id="mainCarousel" class="carousel slide" data-bs-ride="carousel">
          <div class="carousel-inner rounded-3">
            <div class="carousel-item active"><img src="/assets/catogary/banner1.webp" class="d-block w-100" alt="Banner 1" /></div>
            <div class="carousel-item"><img src="/assets/catogary/banner2.webp" class="d-block w-100" alt="Banner 2" /></div>
          </div>
        </div>
      </section>

      <section class="categories-container">
        <div class="categories-grid">
          <?php foreach ($categories as $c): ?>
            <div class="category-item">
              <a href="/category?category=<?= h(urlencode($c['slug'] ?? '')) ?>">
                <img src="<?= h($c['image'] ?? '') ?>" alt="<?= h($c['alt'] ?? '') ?>" />
                <p class="category-label"><?= h($c['label'] ?? '') ?></p>
              </a>
            </div>
          <?php endforeach; ?>
        </div>
      </section>

      <div class="deal-banner">
        <div class="deal-left">
          <div class="deal-title">Deals of the Day</div>
          <div class="deal-timer"><span id="dealTimer">05:38</span></div>
        </div>
        <div class="sale-badge">SALE IS LIVE</div>
      </div>

      <section class="products-section">
        <div class="mainbody" id="productsGrid">
          <?php foreach ($first_page as $p) {
              include __DIR__ . '/../../includes/product-card.php';
          } ?>
        </div>
        <div id="infiniteLoader" style="text-align:center;padding:20px">
          <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Loading...</span>
          </div>
        </div>
      </section>
    </main>
  </div>

  <?php include __DIR__ . '/../../includes/footer.php'; ?>

  <script>
    // Deals timer
    (function () {
      var el = document.getElementById('dealTimer');
      if (!el) return;
      var s = 5 * 60 + 38;
      setInterval(function () {
        if (s < 0) s = 5 * 60 + 38;
        var m = String(Math.floor(s / 60)).padStart(2, '0');
        var ss = String(s % 60).padStart(2, '0');
        el.textContent = m + ':' + ss;
        s--;
      }, 1000);
    })();

    // Infinite scroll
    (function () {
      var page = 2;
      var loading = false;
      var done = false;
      var grid = document.getElementById('productsGrid');
      var loader = document.getElementById('infiniteLoader');

      function loadMore() {
        if (loading || done) return;
        if ((window.innerHeight + window.scrollY) < (document.body.offsetHeight - 500)) return;
        loading = true;
        fetch('/api/products?page=' + page)
          .then(function (r) { return r.json(); })
          .then(function (data) {
            if (!data.products || data.products.length === 0) {
              done = true;
              if (loader) loader.style.display = 'none';
              return;
            }
            data.products.forEach(function (p) { grid.insertAdjacentHTML('beforeend', renderCard(p)); });
            page++;
          })
          .catch(function () {})
          .finally(function () { loading = false; });
      }

      function renderCard(p) {
        var rating = parseFloat(p.rating) || 4.5;
        var stars = Math.round(rating * 2) / 2;
        var full = Math.floor(stars);
        var half = (stars - full) >= 0.5;
        var starsHtml = '';
        for (var i = 1; i <= 5; i++) {
          if (i <= full) starsHtml += '<i class="bi bi-star-fill"></i>';
          else if (half && i === full + 1) starsHtml += '<i class="bi bi-star-half"></i>';
          else starsHtml += '<i class="bi bi-star"></i>';
        }
        var wow = p.wow_price || Math.round((p.price || 0) * 0.95);
        var name = (p.name || p.title || '').replace(/</g, '&lt;');
        var mrp = (p.mrp || 0).toLocaleString('en-IN');
        return '<a href="/product?pid=' + p.pid + '" class="products"><div class="productcard">' +
          '<div class="imagecontainer"><img src="' + (p.image || '') + '" class="productimage" loading="lazy" alt="' + name + '" /></div>' +
          '<div class="product-info"><p class="product-name">' + name + '</p>' +
          '<div class="price-line"><span class="selling-price">&#8377;' + p.price + '</span>' +
          '<del class="mrp">&#8377;' + mrp + '</del>' +
          '<span class="discount">' + (p.discount || '') + '</span></div>' +
          '<div class="wow-offer"><img class="wow-badge" src="/assets/catogary/wow.webp" alt="WOW Offer" />' +
          '<span class="wow-price">&#8377;' + wow + '</span><span class="offer-text">with 2 offers</span></div>' +
          '<div class="rating-line"><div class="rating-stars">' + starsHtml + '</div>' +
          '<img class="fassured-logo-small" src="/assets/catogary/assured.png" alt="F-Assured" /></div>' +
          '</div></div></a>';
      }

      window.addEventListener('scroll', loadMore);
    })();
  </script>
