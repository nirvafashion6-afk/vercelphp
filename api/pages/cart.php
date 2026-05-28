<?php
require_once __DIR__ . '/../../includes/helpers.php';
$page_title = 'My Cart';
include __DIR__ . '/../../includes/layout-head.php';
?>
  <header class="page-header">
    <div class="header-content d-flex align-items-center" style="gap:16px">
      <a href="/" class="back-arrow"><i class="bi bi-arrow-left"></i></a>
      <img src="/assets/catogary/logo.png" alt="Logo" style="height:32px" />
      <h4 style="font-size:18px;font-weight:500;margin:0;color:#212121">My Cart</h4>
    </div>
  </header>

  <div class="cart-tabs">
    <a class="nav-link active" href="#">Cart (<span id="cartCount">0</span>)</a>
  </div>

  <div id="emptyCart" class="empty-cart-container" style="display:none">
    <img src="/assets/catogary/comp.webp" alt="Empty Cart" />
    <h4>Your cart is empty!</h4>
    <a href="/" class="shop-now-btn">Shop now</a>
  </div>

  <div id="filledCart" style="display:none">
    <div class="cart-container" id="cartList"></div>
    <div class="savings-banner">You will save &#8377;<span id="savings">0</span> on this order</div>
    <div class="price-details-card">
      <h6 class="fw-bold mb-3">Price Details</h6>
      <div class="price-details-row"><span>Price (<span id="itemCount">0</span> items)</span><span>&#8377;<span id="mrpTotal">0</span></span></div>
      <div class="price-details-row"><span>Discount</span><span class="text-success">- &#8377;<span id="discount">0</span></span></div>
      <div class="price-details-row"><span>Delivery Charges</span><span class="text-success">FREE</span></div>
      <div class="price-details-row total-amount-row"><span>Total Amount</span><span>&#8377;<span id="totalAmount">0</span></span></div>
    </div>
    <section id="recoSection" class="cart-reco-section" style="display:none">
      <h5>More in <span id="recoCategory">Electronics</span></h5>
      <p class="subtitle">You might also like</p>
      <div class="cart-reco-grid" id="recoGrid"></div>
    </section>
    <div class="page-footer">
      <div>
        <div class="footer-price">&#8377;<span id="footerTotal">0</span></div>
        <div class="footer-price-info">View price details</div>
      </div>
      <button class="place-order-btn" onclick="window.location.href='/checkout'">Place Order</button>
    </div>
  </div>

  <script>
    (function () {
      var items = JSON.parse(localStorage.getItem('cart') || '[]');
      var emptyEl = document.getElementById('emptyCart');
      var filledEl = document.getElementById('filledCart');
      var listEl = document.getElementById('cartList');

      function inr(n) { return (n || 0).toLocaleString('en-IN'); }
      function save() { localStorage.setItem('cart', JSON.stringify(items)); }

      // Build a cart item via DOM nodes (no innerHTML on untrusted strings).
      function makeItemCard(i, idx) {
        var wrap = document.createElement('div');
        wrap.className = 'cart-item-card-inner';

        var row = document.createElement('div');
        row.className = 'cart-item-row';
        var img = document.createElement('img');
        img.className = 'cart-item-image';
        img.src = i.image || '';
        img.alt = i.name || '';
        row.appendChild(img);

        var meta = document.createElement('div');
        meta.style.flex = '1';

        var name = document.createElement('p');
        name.className = 'product-name';
        name.style.cssText = 'font-size:14px;line-height:1.4;margin-bottom:8px';
        name.textContent = i.name || '';
        meta.appendChild(name);

        var priceLine = document.createElement('div');
        priceLine.className = 'price-line';
        var sp = document.createElement('span');
        sp.className = 'selling-price';
        sp.textContent = '₹' + i.price;
        priceLine.appendChild(sp);
        var del = document.createElement('del');
        del.className = 'mrp';
        del.textContent = '₹' + inr(i.mrp || 0);
        priceLine.appendChild(del);
        meta.appendChild(priceLine);

        var qtyRow = document.createElement('div');
        qtyRow.className = 'd-flex align-items-center mt-2';
        qtyRow.style.gap = '12px';
        var lbl = document.createElement('span');
        lbl.className = 'text-muted small';
        lbl.textContent = 'Qty:';
        qtyRow.appendChild(lbl);
        var sel = document.createElement('select');
        sel.className = 'qty-select';
        [1,2,3,4,5].forEach(function (n) {
          var opt = document.createElement('option');
          opt.value = n; opt.textContent = n;
          if (n === i.qty) opt.selected = true;
          sel.appendChild(opt);
        });
        sel.addEventListener('change', function () {
          items[idx].qty = parseInt(sel.value, 10);
          save(); render();
        });
        qtyRow.appendChild(sel);
        meta.appendChild(qtyRow);

        row.appendChild(meta);
        wrap.appendChild(row);

        var actions = document.createElement('div');
        actions.className = 'action-buttons';
        var rm = document.createElement('button');
        rm.textContent = 'REMOVE';
        rm.addEventListener('click', function () {
          items.splice(idx, 1); save(); render();
        });
        var vw = document.createElement('button');
        vw.textContent = 'VIEW';
        vw.addEventListener('click', function () {
          window.location.href = '/product?pid=' + encodeURIComponent(i.pid);
        });
        actions.appendChild(rm); actions.appendChild(vw);
        wrap.appendChild(actions);
        return wrap;
      }

      function makeRecoCard(r) {
        var a = document.createElement('a');
        a.className = 'cart-reco-card';
        a.href = '/product?pid=' + encodeURIComponent(r.pid);
        var img = document.createElement('img');
        img.src = r.image || ''; img.alt = r.name || ''; img.loading = 'lazy';
        a.appendChild(img);
        var body = document.createElement('div');
        body.className = 'body';
        var name = document.createElement('p');
        name.className = 'name'; name.textContent = r.name || '';
        body.appendChild(name);
        var prow = document.createElement('div');
        prow.className = 'price-row';
        var p = document.createElement('span');
        p.className = 'price'; p.textContent = '₹' + r.price;
        prow.appendChild(p);
        var m = document.createElement('span');
        m.className = 'mrp'; m.textContent = '₹' + inr(r.mrp || 0);
        prow.appendChild(m);
        body.appendChild(prow);
        a.appendChild(body);
        return a;
      }

      function render() {
        document.getElementById('cartCount').textContent = items.length;
        if (items.length === 0) {
          emptyEl.style.display = '';
          filledEl.style.display = 'none';
          return;
        }
        emptyEl.style.display = 'none';
        filledEl.style.display = '';

        listEl.textContent = '';
        items.forEach(function (i, idx) { listEl.appendChild(makeItemCard(i, idx)); });

        var subtotal = items.reduce(function (s, i) { return s + i.price * i.qty; }, 0);
        var mrpTotal = items.reduce(function (s, i) { return s + (i.mrp || i.price) * i.qty; }, 0);
        var savings  = mrpTotal - subtotal;
        document.getElementById('savings').textContent     = inr(savings);
        document.getElementById('itemCount').textContent   = items.length;
        document.getElementById('mrpTotal').textContent    = inr(mrpTotal);
        document.getElementById('discount').textContent    = inr(savings);
        document.getElementById('totalAmount').textContent = inr(subtotal);
        document.getElementById('footerTotal').textContent = inr(subtotal);

        if (items.length > 0) loadRecos();
      }

      function loadRecos() {
        var exclude = items.map(function (c) { return c.pid; }).join(',');
        var anchor = items[0].pid;
        fetch('/api/products?anchor=' + anchor + '&exclude=' + exclude + '&limit=12')
          .then(function (r) { return r.json(); })
          .then(function (j) {
            if (!j.products || j.products.length === 0) return;
            if (j.category) document.getElementById('recoCategory').textContent = j.category;
            var grid = document.getElementById('recoGrid');
            grid.textContent = '';
            j.products.forEach(function (r) { grid.appendChild(makeRecoCard(r)); });
            document.getElementById('recoSection').style.display = '';
          })
          .catch(function () {});
      }

      render();
    })();
  </script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
