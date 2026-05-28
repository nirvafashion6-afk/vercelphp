<?php
// GET /api/upichange  →  { upi: { upi }, pixelId: <FacebookPixel doc> }
// Matches the shape of the original Next.js endpoint. Pixel HTML is stored
// as a JSON file at _data/facebook-pixel.json (created/updated by the
// /api/facebookPixel endpoint). UPI ID comes from PAYTM_UPI_ID env var.

require_once __DIR__ . '/../includes/helpers.php';

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'GET') {
    json_response(['ok' => false, 'error' => 'Method not allowed'], 405);
}

$pixel_file = __DIR__ . '/../_data/facebook-pixel.json';
$pixel = [];
if (is_file($pixel_file)) {
    $raw = file_get_contents($pixel_file);
    if ($raw !== false) {
        $decoded = json_decode($raw, true);
        if (is_array($decoded)) $pixel = $decoded;
    }
}

json_response([
    'upi'     => ['upi' => getenv('PAYTM_UPI_ID') ?: ''],
    'pixelId' => $pixel,
]);
