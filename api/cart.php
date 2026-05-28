<?php
require_once __DIR__ . '/includes/data.php';
$bv_title = 'My Cart | DMLinan';
$bv_show_search = false;
include __DIR__ . '/includes/header.php';
?>

<div class="cart-tabs">
    <a class="nav-link active" href="#">Cart (<span id="cart-tab-count">0</span>)</a>
</div>

<div id="cart-root">
    <div class="empty-cart-container" id="empty-state">
        <img src="/assets/catogary/comp.webp" alt="Empty Cart">
        <h4>Your cart is empty!</h4>
        <a href="/" class="shop-now-btn">Shop now</a>
    </div>

    <div id="cart-filled" style="display:none">
        <div class="cart-container" id="cart-items"></div>
        <div class="savings-banner" id="savings-banner"></div>
        <div class="price-details-card" id="price-details"></div>
        <section class="cart-reco-section" id="reco-section" style="display:none">
            <h5 id="reco-title">More for You</h5>
            <p class="subtitle">You might also like</p>
            <div class="cart-reco-grid" id="reco-grid"></div>
        </section>
    </div>
</div>

<div class="page-footer" id="cart-footer" style="display:none">
    <div>
        <div class="footer-price" id="footer-total">&#8377;0</div>
        <div class="footer-price-info">View price details</div>
    </div>
    <button class="place-order-btn" onclick="location.href='/checkout.php'">Place Order</button>
</div>

<script src="/js/cart-page.js" defer></script>

<?php include __DIR__ . '/includes/footer.php'; ?>
