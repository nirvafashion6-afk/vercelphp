<?php
// POST /api/payment-notification
//
// Webhook receiver for scraped transaction events. Verifies the shared-secret
// X-Webhook-Token (PAYMENT_NOTIFICATION_TOKEN env), validates payload, and
// updates the /tmp transaction log if it exists. See the original Next.js
// version for the full pool-merchant + Mongo flow.

require_once __DIR__ . '/../includes/helpers.php';

$method = $_SERVER['REQUEST_METHOD'] ?? '';
if ($method === 'GET') {
    json_response(['ok' => true, 'route' => 'payment-notification']);
}
if ($method !== 'POST') {
    json_response(['ok' => false, 'error' => 'Method not allowed'], 405);
}

$expected = getenv('PAYMENT_NOTIFICATION_TOKEN') ?: '';
if ($expected !== '') {
    $got = $_SERVER['HTTP_X_WEBHOOK_TOKEN'] ?? ($_GET['token'] ?? '');
    if (!hash_equals($expected, (string)$got)) {
        json_response(['ok' => false, 'error' => 'unauthorized'], 401);
    }
}

$body    = read_json_body();
$orderId = (string)($body['comment'] ?? ($body['note'] ?? ($body['orderId'] ?? ($body['txnid'] ?? ''))));
$status  = strtolower((string)($body['status'] ?? 'success'));
$utr     = (string)($body['utr'] ?? ($body['UTR'] ?? ($body['bankRef'] ?? '')));
$amount  = (string)($body['amount'] ?? ($body['txnAmount'] ?? ''));
$payerVpa  = (string)($body['payerVpa'] ?? ($body['vpa']  ?? ''));
$payerName = (string)($body['payerName'] ?? ($body['name'] ?? ''));
$paytmTxnId= (string)($body['paytmTxnId'] ?? ($body['txnId'] ?? ''));

if ($orderId === '' || !preg_match('/^TXN\d+/', $orderId)) {
    json_response(['ok' => false, 'error' => 'invalid or missing comment/orderId'], 400);
}

$is_success = in_array($status, ['success', 'successful', 'txn_success'], true);
$is_failure = in_array($status, ['failure', 'failed',     'txn_failure'], true);

if (!$is_success && !$is_failure) {
    json_response(['ok' => true, 'ignored' => true, 'reason' => 'non-terminal status']);
}

$file = sys_get_temp_dir() . '/bhavya-tx-' . $orderId . '.json';
if (!is_file($file)) {
    json_response(['ok' => true, 'matched' => 0, 'note' => 'no pending order with that comment']);
}

$raw = file_get_contents($file);
$tx  = $raw !== false ? json_decode($raw, true) : null;
if (!is_array($tx) || ($tx['status'] ?? '') !== 'PENDING') {
    json_response(['ok' => true, 'matched' => 0, 'note' => 'order not in pending state']);
}

$tx['status']      = $is_success ? 'SUCCESS' : 'FAILURE';
$tx['utr']         = $utr;
$tx['gateway']     = 'PHONEPE_VIA_PAYTM';
$tx['payerVpa']    = $payerVpa;
$tx['payerName']   = $payerName;
$tx['paytmTxnId']  = $paytmTxnId;
$tx['paytmAmount'] = $amount;
$tx['scrapedAt']   = date('c');
$tx['updatedAt']   = date('c');

file_put_contents($file, json_encode($tx, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));

json_response([
    'ok'       => true,
    'matched'  => 1,
    'modified' => 1,
    'txnid'    => $orderId,
]);
