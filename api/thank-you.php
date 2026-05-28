<?php
session_start();

/* ==============================
   GET ORDER DETAILS
============================== */

$order_id = isset($_GET['order_id']) 
    ? htmlspecialchars($_GET['order_id']) 
    : 'N/A';

$payment_id = isset($_GET['payment_id']) 
    ? htmlspecialchars($_GET['payment_id']) 
    : 'N/A';

$status = isset($_GET['status']) 
    ? htmlspecialchars($_GET['status']) 
    : 'Pending';

$amount = isset($_GET['amount']) && $_GET['amount'] !== ''
    ? (float) $_GET['amount']
    : 0;

/* ==============================
   GOOGLE CONVERSION CONDITION
============================== */

$load_google_conversion = ($amount > 0 && $order_id !== 'N/A');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Confirmed - Thank You!</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        body {
            background-color: #f1f2f4;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, 'Open Sans', 'Helvetica Neue', sans-serif;
        }

        .thankyou-container {
            max-width: 650px;
            margin: 50px auto;
            background: #fff;
            border-radius: 10px;
            padding: 40px 30px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.08);
            text-align: center;
        }

        .success-icon {
            width: 100px;
            height: 100px;
            margin: 0 auto 20px;
            border-radius: 50%;
            background: #e9f7eb;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .success-icon i {
            font-size: 60px;
            color: #28a745;
        }

        .thankyou-title {
            font-size: 34px;
            font-weight: 700;
            margin-bottom: 10px;
            color: #222;
        }

        .thankyou-subtitle {
            font-size: 18px;
            color: #666;
            margin-bottom: 30px;
        }

        .order-details {
            border: 1px solid #e5e5e5;
            border-radius: 8px;
            padding: 25px;
            background: #fafafa;
            text-align: left;
            margin-bottom: 30px;
        }

        .order-details h5 {
            text-align: center;
            margin-bottom: 25px;
            font-weight: 700;
        }

        .order-details p {
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px;
            font-size: 16px;
            color: #333;
        }

        .order-details strong {
            color: #000;
        }

        .btn-continue {
            background: #fb641b;
            color: #fff;
            padding: 12px 30px;
            border-radius: 5px;
            text-decoration: none;
            font-weight: 600;
            transition: 0.3s;
            display: inline-block;
        }

        .btn-continue:hover {
            background: #e85a13;
            color: #fff;
        }

        .text-success-custom {
            color: #28a745;
            font-weight: 700;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="thankyou-container">

        <div class="success-icon">
            <i class="fas fa-check"></i>
        </div>

        <h1 class="thankyou-title">
            Thank You!
        </h1>

        <p class="thankyou-subtitle">
            Your order has been placed successfully.
        </p>

        <div class="order-details">

            <h5>Your Order Details</h5>

            <p>
                <span>Order ID:</span>
                <strong><?php echo $order_id; ?></strong>
            </p>

            <p>
                <span>Payment ID:</span>
                <strong><?php echo $payment_id; ?></strong>
            </p>

            <?php if ($amount > 0): ?>
            <p>
                <span>Amount Paid:</span>
                <strong>₹<?php echo number_format($amount, 2); ?></strong>
            </p>
            <?php endif; ?>

            <p>
                <span>Order Status:</span>
                <strong class="text-success-custom">
                    <?php echo ucfirst($status); ?>
                </strong>
            </p>

        </div>

        <p class="text-muted small mb-4">
            You will receive an order confirmation shortly.
        </p>

        <a href="index.php" class="btn-continue">
            Continue Shopping
        </a>

    </div>
</div>

<?php if ($load_google_conversion): ?>

<!-- Google tag (gtag.js) -->
<script async src="https://www.googletagmanager.com/gtag/js?id=AW-18084378359"></script>

<script>
window.dataLayer = window.dataLayer || [];

function gtag(){
    dataLayer.push(arguments);
}

gtag('js', new Date());

gtag('config', '');

/* Purchase Conversion Event */
gtag('event', 'conversion', {
    'send_to': '/',
    'value': <?php echo $amount; ?>,
    'currency': 'INR',
    'transaction_id': '<?php echo $order_id; ?>'
});
</script>

<?php endif; ?>

</body>
</html>