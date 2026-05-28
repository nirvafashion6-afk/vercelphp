<?php
$bv_title = 'Order Summary | DMLINAN';
$bv_show_search = false;
include __DIR__ . '/includes/header.php';
?>

<div class="container-fluid p-3 header-container">
    <div class="row header">
        <div class="col-1">
            <div class="menu-icon" onclick="location.href='/checkout.php'" style="cursor:pointer">
                <svg width="19" height="16" viewBox="0 0 19 16" xmlns="http://www.w3.org/2000/svg">
                    <path d="M17.556 7.847H1M7.45 1L1 7.877l6.45 6.817" stroke="#000" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" fill="none"/>
                </svg>
            </div>
        </div>
        <div class="col-8"><div class="menu-logo"><h4 class="mb-0 mt-1 ms-2">Order Summary</h4></div></div>
    </div>
</div>

<div class="_1fhgRH max-height mb-70" id="order-root">
    <div class="py-4 mb-1">
        <div class="checkout-stepper">
            <div class="step done"><div class="circle">&#10003;</div><div class="lbl">Address</div></div>
            <div class="bar done"></div>
            <div class="step active"><div class="circle">2</div><div class="lbl">Order Summary</div></div>
            <div class="bar"></div>
            <div class="step"><div class="circle">3</div><div class="lbl">Place Order</div></div>
        </div>
    </div>

    <div class="px-3 py-4 mb-2 bg-white">
        <h3 style="font-size:16px;font-weight:600;margin:0">Delivered to:</h3>
        <div class="address-div mt-2" id="deliver-to"></div>
    </div>

    <div class="px-3 py-4 mb-2 bg-white">
        <ul class="list-group list-group-flush" id="order-items"></ul>
    </div>

    <div class="px-3 py-4 mb-2 bg-white" id="price-detail">
        <h3 style="font-size:16px;font-weight:600;margin:0">Price Details</h3>
        <div class="price-detail-div mt-2" id="price-rows"></div>
    </div>

    <section class="cart-reco-section" id="reco-section" style="display:none">
        <h5 id="reco-title">Recommended for You</h5>
        <p class="subtitle">From your category</p>
        <div class="cart-reco-grid" id="reco-grid"></div>
    </section>

    <div class="sefty-banner">
        <img class="sefty-img" src="/assets/images/plue-fassured.png" loading="lazy" alt="Safe and secure">
        <div dir="auto" class="sefty-txt">Safe and secure. Easy returns. 100% Authentic products.</div>
    </div>
</div>

<div class="button-container flex p-3 bg-white" id="bottom-bar">
    <div class="col-6 footer-price">
        <span class="strike mrp ms-0 mb-1" id="footer-strike"></span>
        <span class="selling_price" id="footer-selling"></span>
    </div>
    <button class="buynow-button product-page-buy col-6 btn-continue" id="place-order-btn">Place Order</button>
</div>

<!-- Inline confirmation modal (no payment integration — flow stops here per spec) -->
<div id="order-placed-modal" style="display:none;position:fixed;inset:0;z-index:1050;background:rgba(0,0,0,0.7);align-items:center;justify-content:center;padding:16px">
    <div style="background:#fff;border-radius:16px;padding:32px;max-width:380px;width:100%;text-align:center;box-shadow:0 20px 60px rgba(0,0,0,0.3)">
        <div style="width:72px;height:72px;border-radius:50%;background:#26a541;color:#fff;display:flex;align-items:center;justify-content:center;font-size:40px;margin:0 auto 16px">&#10003;</div>
        <h4 style="margin:0 0 8px;font-weight:700">Order Placed</h4>
        <p style="margin:0 0 8px;color:#666;font-size:14px">Thank you! Your order has been placed.</p>
        <p style="margin:0 0 20px;color:#888;font-size:12px">Order ID: <code id="placed-order-id"></code></p>
        <button class="common-button" style="height:46px" onclick="location.href='/'">Continue Shopping</button>
    </div>
</div>

<script src="/js/order-summary-page.js" defer></script>

<?php include __DIR__ . '/includes/footer.php'; ?>
