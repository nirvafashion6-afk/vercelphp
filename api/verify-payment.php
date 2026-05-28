<?php
// POST /api/verify-payment   Body: { txnid }
//
// Simplified PHP port — the original endpoint long-polled MongoDB and the
// Paytm Order Status API. Vercel's PHP runtime doesn't ship the MongoDB
// driver, so this version returns the transient PENDING state from the
// /tmp log written by /api/initiate-payment. For real production use,
// connect this to a hosted DB (Upstash/PlanetScale/etc).

require_once __DIR__ . '/../includes/helpers.php';

if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
    json_response(['success' => false, 'error' => 'Method not allowed'], 405);
}

$body  = read_json_body();
$txnid = (string)($body['txnid'] ?? '');
if ($txnid === '') {
    json_response(['success' => false, 'error' => 'Transaction ID required'], 400);
}
if (!preg_match('/^TXN[A-Za-z0-9]+$/', $txnid)) {
    json_response(['success' => false, 'error' => 'Invalid transaction ID'], 400);
}

$file = sys_get_temp_dir() . '/bhavya-tx-' . $txnid . '.json';
if (!is_file($file)) {
    json_response(['success' => true, 'status' => 'pending']);
}

$raw = file_get_contents($file);
$tx  = $raw !== false ? json_decode($raw, true) : null;

if (!is_array($tx)) {
    json_response(['success' => true, 'status' => 'pending']);
}

$status = strtoupper((string)($tx['status'] ?? 'PENDING'));
if ($status === 'SUCCESS' || $status === 'FAILURE') {
    json_response([
        'success' => true,
        'status'  => $status === 'SUCCESS' ? 'success' : 'failure',
        'utr'     => (string)($tx['utr'] ?? ''),
        'amount'  => (string)($tx['amount'] ?? ''),
    ]);
}

json_response(['success' => true, 'status' => 'pending']);
