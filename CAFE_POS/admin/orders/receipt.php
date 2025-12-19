<?php
session_start();

// GET ORDER DATA FROM SESSION
$orderId = isset($_SESSION['last_order_id']) ? $_SESSION['last_order_id'] : null;
$orderNo = isset($_SESSION['last_order_no']) ? $_SESSION['last_order_no'] : null;
$cartData = isset($_SESSION['last_cart']) ? $_SESSION['last_cart'] : [];
$subtotal = isset($_SESSION['last_subtotal']) ? $_SESSION['last_subtotal'] : 0;
$paymentMethod = isset($_SESSION['last_payment_method']) ? $_SESSION['last_payment_method'] : '';
$billAmount = isset($_SESSION['last_bill_amount']) ? $_SESSION['last_bill_amount'] : 0;
$changeAmount = isset($_SESSION['last_change_amount']) ? $_SESSION['last_change_amount'] : 0;
$orderDateTime = isset($_SESSION['last_order_datetime']) ? $_SESSION['last_order_datetime'] : date('Y-m-d H:i:s'); // **NEW**

// REDIRECT IF THERE IS NO ITEMS
if (empty($cartData)) {
    header("Location: order-page.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receipt - OFF-THE-GRID</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="../../styles/receipt-style.css">
</head>
<body>

<div class="receipt-container">
    
    <!-- RECEIPT HEADER -->
    <div class="receipt-header">
        <h3>OFF-THE-GRID</h3>
        <p class="mb-0">Official Receipt</p>
        <small><?php echo date('F d, Y h:i A', strtotime($orderDateTime)); ?></small>
    </div>

    <!-- RECEIPT BODY -->
    <div class="receipt-body">
        <?php foreach ($cartData as $item): ?>
        <div class="receipt-item">
            <div>
                <strong><?php echo htmlspecialchars($item['name']); ?></strong> (<?php echo $item['size']; ?>)
                <br>
                <small><?php echo $item['qty']; ?> x ₱<?php echo number_format($item['price'], 2); ?></small>
            </div>
            <div>
                <strong>₱<?php echo number_format($item['price'] * $item['qty'], 2); ?></strong>
            </div>
        </div>
        <?php endforeach; ?>

        <div class="receipt-total">
            <div class="receipt-item">
                <span>SUBTOTAL:</span>
                <span>₱<?php echo number_format($subtotal, 2); ?></span>
            </div>
        </div>

        <div class="mt-3">
            <div class="receipt-item">
                <span>Payment Method:</span>
                <span><strong><?php echo strtoupper($paymentMethod); ?></strong></span>
            </div>
            
            <?php if ($paymentMethod === 'cash'): ?>
            <div class="receipt-item">
                <span>Bill Amount:</span>
                <span>₱<?php echo number_format($billAmount, 2); ?></span>
            </div>
            <div class="receipt-item">
                <span>Change:</span>
                <span>₱<?php echo number_format($changeAmount, 2); ?></span>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- RECEIPT FOOTER -->
    <div class="receipt-footer">
        <p class="mb-0">Thank you for your order!</p>
        <small>OFF-THE-GRID CAFE </small>
    </div>

</div>


<div class="btn-container no-print">
    <!-- GO TO NEW ORDER -->
    <form method="post" action="order-page.php" style="display: inline-block;">
        <button type="submit" class="receipt-btn">
            <i class="bi bi-plus-circle-fill"></i> New Order
        </button>
    </form>
    
    <!-- BACK TO HOME-->
    <form method="post" action="../../login.html" style="display: inline-block;">
        <button type="submit" class="receipt-btn">
            <i class="bi bi-house-door-fill"></i> Back to Home
        </button>
    </form>
</div>

</body>
</html>
