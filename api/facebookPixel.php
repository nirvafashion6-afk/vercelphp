<?php
// GET   /api/facebookPixel              -> returns the pixel doc
// POST  /api/facebookPixel  { pixelId } -> upsert
// PUT   /api/facebookPixel  { pixelId } -> same as POST
//
// Storage: JSON file at _data/facebook-pixel.json (writable on Vercel /tmp
// fallback when the bundled file is read-only). For long-term persistence,
// commit the file to the repo or front this endpoint with a real datastore.

require_once __DIR__ . '/../includes/helpers.php';

$pixel_file = __DIR__ . '/../_data/facebook-pixel.json';
$tmp_file   = sys_get_temp_dir() . '/bhavya-facebook-pixel.json';
$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

function load_pixel(string $primary, string $fallback): array {
    foreach ([$primary, $fallback] as $f) {
        if (is_file($f)) {
            $raw = file_get_contents($f);
            if ($raw !== false) {
                $d = json_decode($raw, true);
                if (is_array($d)) return $d;
            }
        }
    }
    return [];
}

function save_pixel(string $primary, string $fallback, array $doc): string {
    $json = json_encode($doc, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    if (@file_put_contents($primary, $json) !== false) return $primary;
    file_put_contents($fallback, $json);
    return $fallback;
}

if ($method === 'GET') {
    json_response(load_pixel($pixel_file, $tmp_file));
}

if ($method === 'POST' || $method === 'PUT') {
    $body = read_json_body();
    $pixel_html = (string)($body['pixelId'] ?? '');
    if ($pixel_html === '') {
        json_response(['status' => 0, 'message' => 'pixelId (HTML blob) required'], 400);
    }
    $existing = load_pixel($pixel_file, $tmp_file);
    $doc = ['FacebookPixel' => $pixel_html, '_id' => $existing['_id'] ?? uniqid('px_', true)];
    save_pixel($pixel_file, $tmp_file, $doc);
    json_response([
        'status' => 1,
        ($existing ? 'updatedPixelData' : 'savedPixelData') => $doc,
    ], $existing ? 200 : 201);
}

json_response(['status' => 0, 'message' => 'Method Not Allowed'], 405);
