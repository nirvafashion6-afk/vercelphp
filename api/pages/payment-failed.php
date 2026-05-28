<?php
require_once __DIR__ . '/../../includes/helpers.php';
$orderId = $_GET['orderId'] ?? '';
$page_title = 'Payment Failed';
include __DIR__ . '/../../includes/layout-head.php';
?>
  <div class="main-container text-center p-5">
    <div style="font-size:80px;color:#dc3545">&#10007;</div>
    <h2 class="mt-3">Payment Failed</h2>
    <p class="text-muted">Order ID: <code><?= h($orderId) ?></code></p>
    <p class="mt-4">Your payment did not go through. Please try again.</p>
    <a href="/checkout" class="btn btn-primary mt-4 px-5 py-2">Retry Payment</a>
    <a href="/" class="btn btn-link mt-2 d-block">Back to Home</a>
  </div>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
