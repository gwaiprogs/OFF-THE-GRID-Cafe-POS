<?php
session_start();

// GET POST DATA
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

// CALCULATE SUBTOTAL
$subtotal = 0;
foreach ($cartData as $item) {
    $subtotal += $item['price'] * $item['qty'];
}

// SAVE TO SESSION (FOR FORM TRANSFER)
$_SESSION['cart_data'] = $cartData;
$_SESSION['subtotal'] = $subtotal;

// PAYMENT PAGE
header("Location: order-posting.php");
exit();
?>