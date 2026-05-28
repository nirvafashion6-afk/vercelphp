// Shared client-side helpers. Cart + user live in localStorage; pages call
// these helpers to read/write them.

// Global image error fallback. When a product image fails to load from the
// remote source domain, rewrite the src to the equivalent local path under
// /assets/. Idempotent — only fires once per <img>, and only for URLs that
// look like product images on the original source host.
document.addEventListener('error', function (e) {
  var img = e.target;
  if (!img || img.tagName !== 'IMG' || img.dataset.fallbackTried) return;
  var src = img.src || '';
  // Strip the source-domain prefix, keep the /assets/... path
  var m = src.match(/^https?:\/\/(?:www\.)?bhavyaenterprises\.info\/(assets\/.+)$/i);
  if (m) {
    img.dataset.fallbackTried = '1';
    img.src = '/' + m[1];
    return;
  }
  // If a /assets/... local image also fails, swap to a final placeholder
  if (src.indexOf('/assets/') !== -1) {
    img.dataset.fallbackTried = '1';
    img.src = '/assets/catogary/comp.webp';
  }
}, true);  // capture phase — error events on <img> don't bubble

(function () {
  function readCart() {
    try { return JSON.parse(localStorage.getItem('cart') || '[]'); } catch (e) { return []; }
  }
  function writeCart(items) {
    localStorage.setItem('cart', JSON.stringify(items));
    updateCartBadge();
  }
  function updateCartBadge() {
    var el = document.getElementById('cart-count');
    if (!el) return;
    var n = readCart().length;
    el.textContent = String(n);
    el.style.display = n > 0 ? '' : 'none';
  }
  function addToCart(item, qty) {
    var c = readCart();
    var existing = c.find(function (i) { return String(i.pid) === String(item.pid); });
    if (existing) existing.qty += (qty || 1);
    else c.push(Object.assign({}, item, { qty: qty || 1 }));
    writeCart(c);
  }

  // Expose globals
  window.BV = { readCart: readCart, writeCart: writeCart, addToCart: addToCart, updateCartBadge: updateCartBadge };

  // ── Infinite scroll for home + category pages ────────────────────
  // Pages opt-in by giving #mainbody data-load-url="/load_products.php"
  // (or any other URL accepting ?page=N). Each call appends HTML returned
  // by the endpoint until it returns empty.
  document.addEventListener('DOMContentLoaded', function () {
    updateCartBadge();

    var grid = document.getElementById('mainbody');
    if (grid && grid.dataset.loadUrl) {
      var page = parseInt(grid.dataset.startPage || '2', 10);
      var loading = false;
      var done = false;
      var loader = document.getElementById('grid-loader');
      function onScroll() {
        if (loading || done) return;
        if ((window.innerHeight + window.scrollY) < (document.body.offsetHeight - 500)) return;
        loading = true;
        var url = grid.dataset.loadUrl + (grid.dataset.loadUrl.indexOf('?') >= 0 ? '&' : '?') + 'page=' + page;
        fetch(url).then(function (r) { return r.text(); }).then(function (html) {
          if (!html || html.trim() === '') { done = true; if (loader) loader.style.display = 'none'; return; }
          grid.insertAdjacentHTML('beforeend', html);
          page += 1;
        }).catch(function () {}).finally(function () { loading = false; });
      }
      window.addEventListener('scroll', onScroll);
    }

    // ── Auto-rotating product gallery (Bootstrap Carousel manual init) ──
    var carouselEl = document.getElementById('productCarousel');
    if (carouselEl && window.bootstrap && carouselEl.querySelectorAll('.carousel-item').length > 1) {
      try {
        var c = bootstrap.Carousel.getOrCreateInstance(carouselEl, { interval: 3000, ride: 'carousel', pause: 'hover' });
        c.cycle();
      } catch (e) {}
    }

    // ── Auto-scrolling compact carousels (Suggested / Lowest Price) ──
    document.querySelectorAll('.compact-carousel').forEach(function (carousel) {
      if (carousel.children.length === 0) return;
      var initial = carousel.children.length;
      for (var i = 0; i < initial; i++) {
        carousel.appendChild(carousel.children[i].cloneNode(true));
      }
      var frame = null;
      function step() {
        carousel.scrollLeft += 0.7;
        if (carousel.scrollLeft >= carousel.scrollWidth / 2) carousel.scrollLeft = 0;
        frame = requestAnimationFrame(step);
      }
      function start() { if (!frame) frame = requestAnimationFrame(step); }
      function stop() { if (frame) { cancelAnimationFrame(frame); frame = null; } }
      carousel.addEventListener('mouseenter', stop);
      carousel.addEventListener('mouseleave', start);
      start();
    });

    // ── Deals timer (homepage) ──────────────────────────────────────
    var t = document.getElementById('deal-timer');
    if (t) {
      var s = 5 * 60 + 38;
      setInterval(function () {
        if (s < 0) s = 5 * 60 + 38;
        var m = String(Math.floor(s / 60)).padStart(2, '0');
        var ss = String(s % 60).padStart(2, '0');
        t.textContent = m + ':' + ss;
        s -= 1;
      }, 1000);
    }
  });

  // ── Generic add-to-cart click delegation ────────────────────────
  document.addEventListener('click', function (ev) {
    var btn = ev.target.closest('[data-add-to-cart]');
    if (!btn) return;
    ev.preventDefault();
    try {
      var item = JSON.parse(btn.dataset.addToCart);
      addToCart(item, 1);
      if (btn.dataset.thenGo) location.href = btn.dataset.thenGo;
    } catch (e) {}
  });
})();
