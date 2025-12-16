<?php
session_start();

// Get POST data
if (!isset($_POST['cart_data'])) {
    die("Error: No cart data received!");
}

$cartData = json_decode($_POST['cart_data'], true);

if (json_last_error() !== JSON_ERROR_NONE) {
    die("JSON Error: " . json_last_error_msg());
}

if (empty($cartData)) {
    die("Error: Cart is empty!");
}

// Calculate subtotal
$subtotal = 0;
foreach ($cartData as $item) {
    $subtotal += $item['price'] * $item['qty'];
}

// Store everything in SESSION (don't save to database yet!)
$_SESSION['cart_data'] = $cartData;
$_SESSION['subtotal'] = $subtotal;

// Redirect to payment page
header("Location: order-posting.php");
exit();
?>