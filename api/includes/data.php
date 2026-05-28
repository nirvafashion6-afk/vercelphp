<?php
// Data layer — reads from _data/*.json. Mirrors the lib/data.js semantics
// from the Next.js rebuild. JSON is loaded once per request and cached
// in a static so subsequent calls within the same request are free.

function bv_data_dir() {
    return __DIR__ . '/../_data';
}

function bv_get_all_products() {
    static $cache = null;
    if ($cache === null) {
        $path = bv_data_dir() . '/products.json';
        $cache = file_exists($path) ? json_decode(file_get_contents($path), true) : [];
        if (!is_array($cache)) $cache = [];
    }
    return $cache;
}

function bv_get_categories() {
    static $cache = null;
    if ($cache === null) {
        $path = bv_data_dir() . '/categories.json';
        $cache = file_exists($path) ? json_decode(file_get_contents($path), true) : [];
        if (!is_array($cache)) $cache = [];
    }
    return $cache;
}

function bv_get_product_by_id($pid) {
    foreach (bv_get_all_products() as $p) {
        if ((string)($p['pid'] ?? '') === (string)$pid) return $p;
    }
    return null;
}

function bv_get_products_by_category($slug) {
    $needle = strtolower((string)$slug);
    if ($needle === '') return [];
    $out = [];
    foreach (bv_get_all_products() as $p) {
        $cats = array_map('strtolower', $p['categories'] ?? []);
        if (in_array($needle, $cats, true)) { $out[] = $p; continue; }
        if (strtolower((string)($p['category'] ?? '')) === $needle) { $out[] = $p; continue; }
        if (empty($p['categories']) && strpos(strtolower((string)($p['title'] ?? $p['name'] ?? '')), $needle) !== false) $out[] = $p;
    }
    return $out;
}

function bv_paginate($arr, $page = 1, $size = 20) {
    $page = max(1, (int)$page);
    return array_slice($arr, ($page - 1) * $size, $size);
}

function bv_recommendations($exclude_pids = [], $category = null, $limit = 12) {
    $all = bv_get_all_products();
    $exclude = array_map('strval', $exclude_pids);
    $pool = [];
    if ($category) {
        foreach ($all as $p) {
            $cats = $p['categories'] ?? [];
            if ((in_array($category, $cats, true) || ($p['category'] ?? null) === $category)
                && !in_array((string)$p['pid'], $exclude, true)) {
                $pool[] = $p;
            }
        }
    }
    if (count($pool) < $limit) {
        foreach ($all as $p) {
            if (count($pool) >= $limit) break;
            if (in_array((string)$p['pid'], $exclude, true)) continue;
            $already = false;
            foreach ($pool as $x) if ($x['pid'] === $p['pid']) { $already = true; break; }
            if (!$already) $pool[] = $p;
        }
    }
    $pool = array_slice($pool, 0, $limit);
    return array_map(function ($p) {
        return [
            'pid'      => $p['pid'],
            'name'     => $p['title'] ?? $p['name'] ?? '',
            'image'    => $p['image'] ?? null,
            'price'    => $p['price'] ?? 0,
            'mrp'      => $p['mrp'] ?? 0,
            'discount' => $p['discount'] ?? '',
            'category' => $p['category'] ?? ($p['categories'][0] ?? null),
        ];
    }, $pool);
}

// Light HTML sanitizer for the rich description block — strips <script>,
// <iframe>, on*= handlers, and javascript: URLs. Matches the Next.js port.
function bv_sanitize_html($html) {
    if (!$html) return '';
    $html = preg_replace('#<script[\s\S]*?</script>#i', '', $html);
    $html = preg_replace('#<iframe[\s\S]*?</iframe>#i', '', $html);
    $html = preg_replace('#\son\w+\s*=\s*"[^"]*"#i', '', $html);
    $html = preg_replace("#\son\w+\s*=\s*'[^']*'#i", '', $html);
    $html = preg_replace('#javascript:#i', '', $html);
    return $html;
}

// Decode existing HTML entities (the scraped JSON has `&amp;` etc. baked in
// from the source HTML) before re-encoding for safe display. Otherwise
// `&amp;` would render as the literal text "&amp;" in the browser.
function bv_e($s) {
    $decoded = html_entity_decode((string)$s, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    return htmlspecialchars($decoded, ENT_QUOTES, 'UTF-8');
}
function bv_inr($n) { return number_format((float)$n, 0, '.', ','); }
