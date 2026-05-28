<?php
// Shared <head> + scripts (Bootstrap, icons, Facebook Pixel, etc.)
$page_title = $page_title ?? 'BHAVYA ENTERPRISE';
$page_description = $page_description ?? 'Discover the best deals at BHAVYA ENTERPRISE. Shop online for the latest in fashion, electronics, home goods, and more.';
?><!DOCTYPE html>
<html lang="en-IN">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title><?= h($page_title) ?></title>
  <meta name="description" content="<?= h($page_description) ?>" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" />
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@500;700&display=swap" rel="stylesheet" />
  <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet" />
  <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet" />
  <link rel="stylesheet" href="/styles/globals.css" />
  <script async defer crossorigin="anonymous" src="https://connect.facebook.net/en_US/sdk.js"></script>
  <script>
    // Pull pixel/UPI config once on page load, mirroring _app.js behaviour.
    (function () {
      try {
        fetch('/api/upichange')
          .then(function (r) { return r.json(); })
          .then(function (j) {
            try { if (j && j.upi && j.upi.upi) localStorage.setItem('upi', j.upi.upi); } catch (e) {}
            if (j && j.pixelId && j.pixelId.FacebookPixel) {
              var pixelHtml = j.pixelId.FacebookPixel;
              var scriptMatch = pixelHtml.match(/<script\b[^<]*(?:(?!<\/script>)<[^<]*)*<\/script>/gi);
              if (scriptMatch) {
                var s = document.createElement('script');
                s.text = scriptMatch.join('').replace(/<script>|<\/script>/gi, '');
                document.head.appendChild(s);
              }
            }
          })
          .catch(function () {});
      } catch (e) {}
    })();
  </script>
</head>
<body>
