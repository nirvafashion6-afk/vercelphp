<?php
// POST /api/initiate-payment
// Body: { amount, productinfo?, firstname?, lastname?, email?, phone? }
//
// Simplified PHP port: builds the UPI URLs and returns them. Pool-merchant
// and Mongo persistence from the original Next.js version are NOT included
// (Vercel's PHP runtime doesn't ship with the MongoDB extension). Records
// can optionally be written to a JSON log file in /tmp for inspection.

require_once __DIR__ . '/../includes/helpers.php';

if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
    json_response(['success' => false, 'error' => 'Method not allowed'], 405);
}

$body = read_json_body();
$amount = $body['amount'] ?? null;
if ($amount === null || $amount === '') {
    json_response(['success' => false, 'error' => 'amount is required'], 400);
}
$num_amount = (float)$amount;
if (!is_finite($num_amount) || $num_amount <= 0) {
    json_response(['success' => false, 'error' => 'Invalid amount'], 400);
}

$upi_id        = getenv('PAYTM_UPI_ID') ?: '';
$merchant_name = getenv('PAYTM_MERCHANT_NAME') ?: 'Store';
$mid           = getenv('PAYTM_MID') ?: '';

if ($upi_id === '') {
    json_response([
        'success' => false,
        'error'   => 'No UPI ID available — set PAYTM_UPI_ID env var',
    ], 500);
}

$txnid = 'TXN' . round(microtime(true) * 1000) . random_int(100, 999);

$productinfo = (string)($body['productinfo'] ?? 'Order');
$amount_fmt  = number_format($num_amount, 2, '.', '');
$tn          = rawurlencode($productinfo);
$pa          = rawurlencode($upi_id);
$pn          = rawurlencode($merchant_name);
$tr          = rawurlencode($txnid);

$upi_url       = "upi://pay?pa={$pa}&pn={$pn}&am={$amount_fmt}&cu=INR&tn={$tn}&tr={$tr}";
$paytm_intent  = "paytmmp://pay?pa={$pa}&pn={$pn}&am={$amount_fmt}&cu=INR&tn={$tn}&tr={$tr}";
$phonepe_intent= "phonepe://pay?pa={$pa}&pn={$pn}&am={$amount_fmt}&cu=INR&tn={$tn}&tr={$tr}";

// Best-effort log to /tmp (Vercel functions have a writable /tmp).
$log = [
    'txnid'        => $txnid,
    'amount'       => $amount_fmt,
    'status'       => 'PENDING',
    'productinfo'  => $productinfo,
    'firstname'    => (string)($body['firstname'] ?? ''),
    'lastname'     => (string)($body['lastname']  ?? ''),
    'email'        => (string)($body['email']     ?? ''),
    'phone'        => (string)($body['phone']     ?? ''),
    'merchantName' => $merchant_name,
    'upiId'        => $upi_id,
    'mid'          => $mid,
    'source'       => 'env',
    'createdAt'    => date('c'),
];
@file_put_contents(
    sys_get_temp_dir() . '/bhavya-tx-' . $txnid . '.json',
    json_encode($log, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)
);

json_response([
    'success'       => true,
    'txnid'         => $txnid,
    'amount'        => $num_amount,
    'upiUrl'        => $upi_url,
    'paytmIntent'   => $paytm_intent,
    'phonepeIntent' => $phonepe_intent,
    'merchantName'  => $merchant_name,
    'upiId'         => $upi_id,
    'source'        => 'env',
]);
