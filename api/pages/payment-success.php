<?php
require_once __DIR__ . '/../../includes/helpers.php';
$orderId = $_GET['orderId'] ?? '';
$amount  = $_GET['amount']  ?? '';
$utr     = $_GET['utr']     ?? '';
$page_title = 'Payment Successful';
include __DIR__ . '/../../includes/layout-head.php';
?>
  <div class="main-container text-center p-5">
    <div style="font-size:80px;color:#26a541">&#10003;</div>
    <h2 class="mt-3">Payment Successful</h2>
    <p class="text-muted">Order ID: <code><?= h($orderId) ?></code></p>
    <?php if ($utr): ?>
      <p class="text-muted small">UTR: <code><?= h($utr) ?></code></p>
    <?php endif; ?>
    <p class="h4">&#8377;<?= h($amount) ?></p>
    <p class="mt-4">Your order has been confirmed. We will deliver in 3-5 days.</p>
    <a href="/" class="btn btn-primary mt-4 px-5 py-2">Continue Shopping</a>
  </div>

  <script>
    (function () {
      try { localStorage.removeItem('cart'); } catch (e) {}
      var amount = <?= json_encode($amount) ?>;
      var orderId = <?= json_encode($orderId) ?>;
      var v = Number(amount) || 0;
      if (typeof window.fbq === 'function' && v > 0) {
        try {
          window.fbq('track', 'Purchase', {
            value: v,
            currency: 'INR',
            content_ids: orderId ? [String(orderId)] : undefined,
            content_type: 'product'
          });
        } catch (e) {}
      }
    })();
  </script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
