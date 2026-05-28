<?php
// Shared helpers.

function h($v): string {
    return htmlspecialchars((string)($v ?? ''), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function inr($n): string {
    $n = (int)round((float)$n);
    return number_format($n, 0, '.', ',');
}

// Minimal HTML sanitizer — strips scripts/iframes and on*= handlers.
function sanitize_html(?string $html): string {
    if (!$html) return '';
    $html = preg_replace('/<script\b[\s\S]*?<\/script>/i', '', $html);
    $html = preg_replace('/<iframe\b[\s\S]*?<\/iframe>/i', '', $html);
    $html = preg_replace('/\son\w+\s*=\s*"[^"]*"/i', '', $html);
    $html = preg_replace('/\son\w+\s*=\s*\'[^\']*\'/i', '', $html);
    $html = preg_replace('/javascript:/i', '', $html);
    return $html;
}

function json_response($payload, int $status = 200): void {
    http_response_code($status);
    header('Content-Type: application/json; charset=utf-8');
    header('Cache-Control: no-store');
    echo json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

function read_json_body(): array {
    $raw = file_get_contents('php://input');
    if ($raw === false || $raw === '') return $_POST ?? [];
    $data = json_decode($raw, true);
    return is_array($data) ? $data : ($_POST ?? []);
}
