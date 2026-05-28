<?php
require_once __DIR__ . '/../../includes/helpers.php';
$page_title = 'Order Summary';
include __DIR__ . '/../../includes/layout-head.php';
?>
  <div style="height:100%">
    <div class="container-fluid p-3 header-container">
      <div class="row header">
        <div class="col-1">
          <div class="menu-icon" onclick="window.location.href='/checkout'">
            <svg width="19" height="16" viewBox="0 0 19 16" xmlns="http://www.w3.org/2000/svg">
              <path d="M17.556 7.847H1M7.45 1L1 7.877l6.45 6.817" stroke="#000" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" fill="none" />
            </svg>
          </div>
        </div>
        <div class="col-8"><div class="menu-logo"><h4 class="mb-0 mt-1 ms-2">Order Summary</h4></div></div>
      </div>

      <div class="_1fhgRH max-height mb-70">
        <div class="py-4 mb-1">
          <div class="checkout-stepper">
            <div class="step done"><div class="circle">&#10003;</div><div class="lbl">Address</div></div>
            <div class="bar done"></div>
            <div class="step active"><div class="circle">2</div><div class="lbl">Order Summary</div></div>
            <div class="bar"></div>
            <div class="step"><div class="circle">3</div><div class="lbl">Payment</div></div>
          </div>
        </div>

        <div class="px-3 py-4 mb-2 bg-white">
          <h3 style="font-size:16px;font-weight:600;margin:0">Delivered to:</h3>
          <div class="address-div mt-2">
            <h4 class="customer-name" style="font-size:14px;margin:4px 0">name: <span id="userName"></span></h4>
            <div class="mb-2 customer-address" style="font-size:13px;color:#555">address: <span id="userAddress"></span></div>
            <div class="customer-contact" style="font-size:13px;color:#555">mobile: <span id="userPhone"></span></div>
          </div>
        </div>

        <div class="px-3 py-4 mb-2 bg-white">
          <ul class="list-group list-group-flush" id="deals"></ul>
        </div>

        <div class="px-3 py-4 mb-2 bg-white" id="price-detail">
          <h3 style="font-size:16px;font-weight:600;margin:0">Price Details</h3>
          <div class="price-detail-div mt-2">
            <div class="product-price-list my-3">
              <span class="title">Price (<span id="itemCount">0</span> item<span id="itemCountS"></span>)</span>
              <span class="data mrp me-0 td-none">&#8377; <span id="mrpTotal">0</span></span>
            </div>
            <div class="product-price-list my-3">
              <span class="title">Discount</span>
              <span class="data discount-amt text-success">&#8377; <span id="discount">0</span></span>
            </div>
            <div class="product-price-list my-3">
              <span class="title">Delivery Charges</span>
              <span class="data text-success">FREE Delivery</span>
            </div>
            <div class="product-price-list my-3 pt-3 total">
              <span class="title">Total Amount</span>
              <span class="data selling_price">&#8377; <span id="sellingTotal">0</span></span>
            </div>
            <div class="product-price-list mt-3 pt-3 saved-div">
              <span class="text-success">You will save <span class="discount-amt">- &#8377; <span id="savings">0</span></span> on this order</span>
            </div>
          </div>
        </div>

        <div class="sefty-banner">
          <img class="sefty-img" src="/assets/images/plue-fassured.png" loading="lazy" alt="Safe and secure" />
          <div dir="auto" class="sefty-txt">Safe and secure payments. Easy returns. 100% Authentic products.</div>
        </div>

        <div class="button-container flex p-3 bg-white">
          <div class="col-6 footer-price">
            <span class="strike mrp ms-0 mb-1">- &#8377; <span id="discountFooter">0</span></span>
            <span class="selling_price">&#8377; <span id="sellingFooter">0</span></span>
          </div>
          <button class="buynow-button product-page-buy col-6 btn-continue" id="btnConfirm">Confirm</button>
        </div>
      </div>
    </div>
  </div>

  <script>
    (function () {
      var cart = JSON.parse(localStorage.getItem('cart') || '[]');
      var user = JSON.parse(localStorage.getItem('user') || 'null');
      if (cart.length === 0) { window.location.replace('/cart'); return; }
      if (!user) { window.location.replace('/checkout'); return; }

      function inr(n) { return (n || 0).toLocaleString('en-IN'); }

      document.getElementById('userName').textContent = user.name || '';
      var addr = user.address || [user.house, user.colonny, user.city, user.state, user.pincode].filter(Boolean).join(', ');
      document.getElementById('userAddress').textContent = addr;
      document.getElementById('userPhone').textContent = user.phone || '';

      var sellingTotal = cart.reduce(function (s, i) { return s + i.price * i.qty; }, 0);
      var mrpTotal     = cart.reduce(function (s, i) { return s + (i.mrp || i.price) * i.qty; }, 0);
      var discount     = mrpTotal - sellingTotal;
      var itemCount    = cart.reduce(function (s, i) { return s + i.qty; }, 0);

      // Render items via DOM nodes (untrusted strings from localStorage).
      var dealsEl = document.getElementById('deals');
      cart.forEach(function (it, idx) {
        var off = it.mrp ? Math.round(((it.mrp - it.price) / it.mrp) * 100) : 0;
        var li = document.createElement('li');
        li.className = 'list-group-item px-0';
        li.style.cssText = 'border-bottom:' + (idx === cart.length - 1 ? 'none' : '1px solid #f0f0f0') + ';padding-bottom:12px;margin-bottom:12px';

        var row1 = document.createElement('div');
        row1.className = 'flex recommended-product';
        var img = document.createElement('img');
        img.src = it.image || ''; img.alt = it.name || '';
        row1.appendChild(img);
        var desc = document.createElement('div');
        desc.className = 'description';
        var title = document.createElement('div');
        title.className = 'product-title mb-1';
        title.textContent = it.name || '';
        desc.appendChild(title);
        var assured = document.createElement('img');
        assured.src = '/assets/catogary/assured.png';
        assured.className = 'img-fluid';
        assured.style.width = '77px';
        assured.alt = 'F-Assured';
        desc.appendChild(assured);
        row1.appendChild(desc);
        li.appendChild(row1);

        var row2 = document.createElement('div');
        row2.className = 'flex recommended-product mt-3';
        var qty = document.createElement('div');
        qty.className = 'timer qty mx-4';
        qty.textContent = 'Qty: ' + it.qty;
        row2.appendChild(qty);
        var pdesc = document.createElement('div');
        pdesc.className = 'description';
        var priceFlex = document.createElement('div');
        priceFlex.className = 'price flex';
        if (off > 0) {
          var d = document.createElement('span');
          d.className = 'discount';
          d.textContent = off + '% Off';
          priceFlex.appendChild(d);
          priceFlex.appendChild(document.createTextNode(' '));
        }
        var sm = document.createElement('span');
        sm.className = 'strike mrp';
        sm.textContent = '₹ ' + inr(it.mrp || 0);
        priceFlex.appendChild(sm);
        priceFlex.appendChild(document.createTextNode(' '));
        var sp = document.createElement('span');
        sp.className = 'selling_price';
        sp.textContent = '₹ ' + inr(it.price * it.qty);
        priceFlex.appendChild(sp);
        pdesc.appendChild(priceFlex);
        row2.appendChild(pdesc);
        li.appendChild(row2);

        dealsEl.appendChild(li);
      });

      document.getElementById('itemCount').textContent     = itemCount;
      document.getElementById('itemCountS').textContent    = itemCount === 1 ? '' : 's';
      document.getElementById('mrpTotal').textContent      = inr(mrpTotal);
      document.getElementById('discount').textContent      = inr(discount);
      document.getElementById('sellingTotal').textContent  = inr(sellingTotal);
      document.getElementById('savings').textContent       = inr(discount);
      document.getElementById('discountFooter').textContent = inr(discount);
      document.getElementById('sellingFooter').textContent  = inr(sellingTotal);

      document.getElementById('btnConfirm').addEventListener('click', function () {
        localStorage.setItem('order', JSON.stringify({
          items: cart, total: sellingTotal, mrp: mrpTotal, address: user, ts: Date.now()
        }));
        localStorage.setItem('data', JSON.stringify({
          selling_price: sellingTotal, mrp: mrpTotal,
          name: (cart[0] || {}).name || 'Order',
          images: (cart[0] || {}).image || '',
          Title: (cart[0] || {}).name || ''
        }));
        window.location.href = '/payment?id=' + sellingTotal + '.' + mrpTotal;
      });
    })();
  </script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
