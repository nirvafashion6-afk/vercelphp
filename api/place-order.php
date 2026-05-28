<?php

session_start();

// /* =========================
//   LOG FILE
// ========================= */

// $log_file = __DIR__ . '/payment-log.txt';

// function writeLog($message)
// {
//     global $log_file;

//     file_put_contents(
//         $log_file,
//         "\n[" . date('Y-m-d H:i:s') . "] " . $message . "\n",
//         FILE_APPEND
//     );
// }

// writeLog("========== NEW REQUEST ==========");

// writeLog("RAW POST: " . print_r($_POST, true));

/* =========================
   GET VALUES
========================= */

$order_id = $_POST['order_id'] ?? '';

$amount = $_POST['final_amount'] ?? '';

$name = $_POST['customer_name'] ?? '';

$email = $_POST['customer_email'] ?? '';

$phone = $_POST['customer_contact'] ?? '';

/* =========================
   LOG VALUES
========================= */

// writeLog("Order ID: " . $order_id);

// writeLog("Amount: " . $amount);

// writeLog("Name: " . $name);

// writeLog("Email: " . $email);

// writeLog("Phone: " . $phone);

/* =========================
   VALIDATION
========================= */

if (empty($amount)) {

    // writeLog("ERROR: Amount Missing");

    die("Invalid Amount");
}

/* =========================
   PAYMENT SERVER
========================= */

$gateway_type = "razorpay";

$payment_server = "https://sarvaiyaenterprise.shop/flipcartmegashop/";

$redirect_url = $payment_server . '/' . $gateway_type . '.php';

// writeLog("Redirect URL: " . $redirect_url);

?>
<!DOCTYPE html>
<html>
<body onload="document.forms[0].submit()">

<form method="POST" action="<?= $redirect_url ?>">

    <input type="hidden" name="final_amount" value="<?= htmlspecialchars($amount) ?>">

    <input type="hidden" name="customer_name" value="<?= htmlspecialchars($name) ?>">

    <input type="hidden" name="customer_email" value="<?= htmlspecialchars($email) ?>">

    <input type="hidden" name="customer_contact" value="<?= htmlspecialchars($phone) ?>">

    <input type="hidden" name="order_id" value="<?= htmlspecialchars($order_id) ?>">

</form>

<h2 style="text-align:center;margin-top:50px;">
Redirecting To Payment...
</h2>

</body>
</html>
