<?php
session_start();

// Database connection
$serverName = "GRIDLAPTOP\SQLEXPRESS";
$connectionOptions = [
    "Database" => "OTG",
    "Uid"      => "",
    "PWD"      => ""
];

$conn = sqlsrv_connect($serverName, $connectionOptions);
if ($conn == false) {
    die("Database connection failed: " . print_r(sqlsrv_errors(), true));
}

// Get session data
$cartData = isset($_SESSION['cart_data']) ? $_SESSION['cart_data'] : null;
$subtotal = isset($_SESSION['subtotal']) ? $_SESSION['subtotal'] : 0;

// Check if cart data exists
if (empty($cartData)) {
    die("Error: No cart data found in session. Please go back and try again.");
}

// Get payment data from POST
$paymentMethod = isset($_POST['payment_method']) ? $_POST['payment_method'] : null;
$billAmount = isset($_POST['bill_amount']) && $_POST['bill_amount'] > 0 ? $_POST['bill_amount'] : $subtotal;
$changeAmount = isset($_POST['change_amount']) && $_POST['change_amount'] > 0 ? $_POST['change_amount'] : 0;

// Validate payment method
if (empty($paymentMethod)) {
    die("Error: No payment method selected. Please go back and select a payment method.");
}

// Generate ORDER_ID
$orderIdQuery = "SELECT ISNULL(MAX(ORDER_ID), 0) + 1 AS next_id FROM ORDER_CART";
$result = sqlsrv_query($conn, $orderIdQuery);
if ($result === false) {
    die("Error getting ORDER_ID: " . print_r(sqlsrv_errors(), true));
}
$row = sqlsrv_fetch_array($result);
$orderId = $row['next_id'];

// Insert each cart item with payment info
$insertSuccess = true;
$firstOrderNo = null;
$orderDateTime = null; // Will be retrieved from database

foreach ($cartData as $item) {
    $productName = $item['name'];
    $productSize = $item['size'];
    $quantity = $item['qty'];
    $itemPrice = $item['price'];

    // Get PRODUCT_ID from MENU table based on product name
    $productIdQuery = "SELECT PRODUCT_ID FROM MENU WHERE PRODUCT_NAME = ?";
    $productIdResult = sqlsrv_query($conn, $productIdQuery, [$productName]);
    
    if ($productIdResult === false) {
        die("Error getting PRODUCT_ID: " . print_r(sqlsrv_errors(), true));
    }
    
    $productRow = sqlsrv_fetch_array($productIdResult);
    $productId = $productRow['PRODUCT_ID'];

    // Insert with PRODUCT_ID
    $sql = "INSERT INTO ORDER_CART 
            (ORDER_ID, PRODUCT_ID, PRODUCT_NAME, PRODUCT_SIZE, QUANTITY, ITEM_PRICE, 
             DATE_ORDERED, PAYMENT_METHOD, TOTAL_ORDER_PAYMENT,
             BILL_AMOUNT, CHANGE_AMOUNT)
            VALUES (?, ?, ?, ?, ?, ?, GETDATE(), ?, ?, ?, ?)";

    $params = [
        $orderId,
        $productId,
        $productName,
        $productSize,
        $quantity,
        $itemPrice,
        $paymentMethod,
        $subtotal,
        $billAmount,
        $changeAmount
    ];

    $stmt = sqlsrv_query($conn, $sql, $params);

    if ($stmt === false) {
        $insertSuccess = false;
        die("Error inserting order: " . print_r(sqlsrv_errors(), true));
    }
    
    // Get first ORDER_NO and DATE_ORDERED for receipt
    if ($firstOrderNo === null) {
        $lastIdQuery = "SELECT TOP 1 ORDER_NO, DATE_ORDERED
                        FROM ORDER_CART 
                        WHERE ORDER_ID = ? 
                        ORDER BY ORDER_NO DESC";
        $lastIdResult = sqlsrv_query($conn, $lastIdQuery, [$orderId]);
        if ($lastIdResult) {
            $lastIdRow = sqlsrv_fetch_array($lastIdResult);
            $firstOrderNo = $lastIdRow['ORDER_NO'];
            
            // Get the actual date/time from database
            if ($lastIdRow['DATE_ORDERED'] instanceof DateTime) {
                $orderDateTime = $lastIdRow['DATE_ORDERED']->format('Y-m-d H:i:s');
            } else {
                $orderDateTime = date('Y-m-d H:i:s', strtotime($lastIdRow['DATE_ORDERED']));
            }
        }
    }
}

sqlsrv_close($conn);

if ($insertSuccess) {
    // Store order info for receipt
    $_SESSION['last_order_id'] = $orderId;
    $_SESSION['last_order_no'] = $firstOrderNo;
    $_SESSION['last_cart'] = $cartData;
    $_SESSION['last_subtotal'] = $subtotal;
    $_SESSION['last_payment_method'] = $paymentMethod;
    $_SESSION['last_bill_amount'] = $billAmount;
    $_SESSION['last_change_amount'] = $changeAmount;
    $_SESSION['last_order_datetime'] = $orderDateTime; // Save the actual database time
    
    // Clear cart session
    unset($_SESSION['cart_data']);
    unset($_SESSION['subtotal']);
    
    // Redirect to receipt page
    header("Location: receipt.php");
    exit();
} else {
    die("Error: Payment processing failed. Please try again.");
}
?>