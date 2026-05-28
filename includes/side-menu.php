<?php
// Off-canvas sidebar menu.
$menu_categories = $menu_categories ?? (function_exists('get_categories') ? get_categories() : []);
?>
<div class="offcanvas offcanvas-start" tabindex="-1" id="sideMenu">
  <div class="offcanvas-header" style="background-color:#1F74BA;color:white">
    <h5 class="offcanvas-title">Menu</h5>
    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="Close"></button>
  </div>
  <div class="offcanvas-body p-0">
    <nav class="nav flex-column">
      <a class="nav-link" href="/" style="color:#212121"><i class="bi bi-house-door-fill"></i> Home</a>
      <div class="nav-item dropdown">
        <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" data-bs-toggle="collapse" data-bs-target="#categoryCollapse" style="color:#212121">
          <i class="bi bi-grid-fill"></i> Shop by Category
        </a>
        <div class="collapse" id="categoryCollapse">
          <ul class="list-unstyled m-0 p-0">
            <?php foreach ($menu_categories as $c): ?>
              <li>
                <a class="dropdown-item ps-5" href="/category?category=<?= h(urlencode($c['slug'] ?? '')) ?>" style="color:#212121">
                  <?= h($c['label'] ?? '') ?>
                </a>
              </li>
            <?php endforeach; ?>
          </ul>
        </div>
      </div>
      <a class="nav-link" href="/about-us" style="color:#212121"><i class="bi bi-info-circle-fill"></i> About Us</a>
      <a class="nav-link" href="/contact-us" style="color:#212121"><i class="bi bi-telephone-fill"></i> Contact Us</a>
      <hr class="my-2" />
      <a class="nav-link" href="/shipping-policy" style="color:#212121"><i class="bi bi-truck"></i> Shipping Policy</a>
      <a class="nav-link" href="/return-policy" style="color:#212121"><i class="bi bi-box-arrow-left"></i> Return Policy</a>
    </nav>
  </div>
</div>
