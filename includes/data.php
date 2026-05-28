<?php
// Data layer — reads JSON files from /_data, mirrors src/lib/data.js.

if (!defined('DATA_DIR')) {
    define('DATA_DIR', __DIR__ . '/../_data');
}

function bhavya_read_json(string $file, $fallback = []) {
    $path = DATA_DIR . '/' . $file;
    if (!is_file($path)) return $fallback;
    $raw = file_get_contents($path);
    if ($raw === false) return $fallback;
    $data = json_decode($raw, true);
    return is_array($data) ? $data : $fallback;
}

function get_all_products(): array {
    static $cache = null;
    if ($cache === null) $cache = bhavya_read_json('products.json', []);
    return $cache;
}

function get_categories(): array {
    static $cache = null;
    if ($cache === null) $cache = bhavya_read_json('categories.json', []);
    return $cache;
}

function get_product_by_id($pid) {
    foreach (get_all_products() as $p) {
        if ((string)($p['pid'] ?? '') === (string)$pid) return $p;
    }
    return null;
}

function get_products_by_category(string $category): array {
    $c = strtolower(trim($category));
    if ($c === '') return [];
    $out = [];
    foreach (get_all_products() as $p) {
        $cats = array_map('strtolower', array_map('strval', $p['categories'] ?? []));
        if (in_array($c, $cats, true)) { $out[] = $p; continue; }
        if (strtolower((string)($p['category'] ?? '')) === $c) { $out[] = $p; continue; }
        if (empty($p['categories']) && stripos((string)($p['title'] ?? $p['name'] ?? ''), $c) !== false) {
            $out[] = $p;
        }
    }
    return $out;
}

function paginate(array $arr, int $page = 1, int $pageSize = 20): array {
    $page = max(1, $page);
    return array_slice($arr, ($page - 1) * $pageSize, $pageSize);
}

function get_recommendations(array $opts = []): array {
    $exclude = array_map('strval', $opts['excludePids'] ?? []);
    $category = $opts['category'] ?? null;
    $limit = (int)($opts['limit'] ?? 12);

    $all = get_all_products();
    $excludeSet = array_flip($exclude);
    $pool = [];

    if ($category) {
        foreach ($all as $p) {
            $cats = $p['categories'] ?? [];
            if ((in_array($category, $cats, true) || ($p['category'] ?? null) === $category)
                && !isset($excludeSet[(string)$p['pid']])) {
                $pool[] = $p;
            }
        }
    }
    if (count($pool) < $limit) {
        $alreadyIds = [];
        foreach ($pool as $p) $alreadyIds[(string)$p['pid']] = true;
        foreach ($all as $p) {
            $sid = (string)$p['pid'];
            if (!isset($excludeSet[$sid]) && !isset($alreadyIds[$sid])) {
                $pool[] = $p;
            }
        }
    }

    $pool = array_slice($pool, 0, $limit);
    $out = [];
    foreach ($pool as $p) {
        $out[] = [
            'pid'      => $p['pid'] ?? null,
            'name'     => $p['title'] ?? ($p['name'] ?? ''),
            'image'    => $p['image'] ?? '',
            'price'    => $p['price'] ?? 0,
            'mrp'      => $p['mrp'] ?? 0,
            'discount' => $p['discount'] ?? '',
            'category' => $p['category'] ?? (($p['categories'] ?? [null])[0] ?? null),
        ];
    }
    return $out;
}
