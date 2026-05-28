<?php
require_once __DIR__ . '/data.php';
$bv_categories = bv_get_categories();
$bv_title = $bv_title ?? 'DMLinan';
$bv_show_search = $bv_show_search ?? true;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= bv_e($bv_title) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="stylesheet" href="/css/styles.css">
    <!-- main.js defines window.BV synchronously and must load before any
         page-specific scripts (cart-page.js, order-summary-page.js, etc.). -->
    <script src="/js/main.js" defer></script>
</head>
<body>
<div class="main-container">
<header class="page-header">
    <?php include __DIR__ . '/sidemenu.php'; ?>
    <div class="top-bar">
        <div class="d-flex align-items-center">
            <button class="btn p-0 me-2" type="button" data-bs-toggle="offcanvas" data-bs-target="#sideMenu" aria-label="Open Menu">
                <i class="bi bi-list" style="color:#212121;font-size:24px"></i>
            </button>
            <div class="logo-container">
                <a href="/"><img src="/assets/catogary/svg-image-1.svg" alt="Logo" class="logo-img" onerror="this.src='/assets/catogary/logo.png'"></a>
            </div>
        </div>
        <div class="cart-link">
            <a href="/cart.php">
                <svg class="cart-icon" xmlns="http://www.w3.org/2000/svg" height="24" viewBox="0 0 24 24" width="24" fill="#212121">
                    <path d="M0 0h24v24H0V0z" fill="none" />
                    <path d="M7 18c-1.1 0-1.99.9-1.99 2S5.9 22 7 22s2-.9 2-2-.9-2-2-2zm10 0c-1.1 0-1.99.9-1.99 2s.89 2 1.99 2 2-.9 2-2-.9-2-2-2zm-1.45-5c.75 0 1.41-.41 1.75-1.03l3.58-6.49c.37-.66-.11-1.48-.87-1.48H5.21l-.94-2H1v2h2l3.6 7.59-1.35 2.44C4.52 15.37 5.24 17 6.5 17h12v-2H6.5c-.25 0-.42-.21-.38-.45l.93-1.68h7.45z" />
                </svg>
                <span class="badge bg-danger" id="cart-count" style="display:none">0</span>
            </a>
        </div>
    </div>
    <?php if ($bv_show_search): ?>
    <div class="location-and-search">
        <div class="search-bar">
            <svg class="search-icon" width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                <path d="M15.5 14h-.79l-.28-.27A6.471 6.471 0 0 0 16 9.5 6.5 6.5 0 1 0 9.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z" />
            </svg>
            <input type="text" placeholder="Search for Products" />
        </div>
    </div>
    <?php endif; ?>
</header>
