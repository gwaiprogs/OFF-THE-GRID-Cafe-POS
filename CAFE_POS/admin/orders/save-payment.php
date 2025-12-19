<?php
session_start();

$serverName = "GRIDLAPTOP\SQLEXPRESS";
$connectionOptions = [
    "Database" => "OTG",
    "Uid"      => "",
    "PWD"      => ""
];

$conn = sqlsrv_connect($serverName, $connectionOptions);
if ($conn == false) {
    die(print_r(sqlsrv_errors(), true));
}

// GET SESSION DATA
$cart_data = isset($_SESSION['cart_data']) ? $_SESSION['cart_data'] : null;
$subtotal = isset($_SESSION['subtotal']) ? $_SESSION['subtotal'] : 0;

// CHECK FOR CART DATA
if (empty($cart_data)) {
    die(print_r(sqlsrv_errors(), true));
}

// GET DATA FROM POST
$payment_method = isset($_POST['payment_method']) ? $_POST['payment_method'] : null;
$bill_amount = isset($_POST['bill_amount']) && $_POST['bill_amount'] > 0 ? $_POST['bill_amount'] : $subtotal;
$change_amount = isset($_POST['change_amount']) && $_POST['change_amount'] > 0 ? $_POST['change_amount'] : 0;

// VALIDATE PAYMENT METHOD
if (empty($payment_method)) {
    die(print_r(sqlsrv_errors(), true));
}

// GENERATE ORDER_ID (BECAUSE 1 ORDER MAY HAVE MULTIPLE PRODUCTS)
$sql_order_id = "SELECT ISNULL(MAX(ORDER_ID), 0) + 1 AS next_id FROM ORDER_CART";
$result_order_id = sqlsrv_query($conn, $sql_order_id);
if ($result_order_id === false) {
    die(print_r(sqlsrv_errors(), true));
}
$row_order_id = sqlsrv_fetch_array($result_order_id);
$order_id = $row_order_id['next_id'];

// INSERT EACH ITEM INTO ORDER_CART
$insert_success = true;
$first_order_no = null;
$order_date_time = null; 

foreach ($cart_data as $item) {
    $product_name = $item['name'];
    $product_size = $item['size'];
    $quantity = $item['qty'];
    $item_price = $item['price'];

    // GET PRODUCT_ID FROM MENU TABLE
    $sql_product_id = "SELECT PRODUCT_ID FROM MENU WHERE PRODUCT_NAME = '$product_name'";
    $result_product_id = sqlsrv_query($conn, $sql_product_id);
    
    if ($result_product_id === false) {
        die(print_r(sqlsrv_errors(), true));
    }
    
    $row_product_id = sqlsrv_fetch_array($result_product_id);
    $product_id = $row_product_id['PRODUCT_ID'];

    // INSERT WITH PRODUCT_ID
    $sql_insert = "INSERT INTO ORDER_CART 
            (ORDER_ID, PRODUCT_ID, PRODUCT_NAME, PRODUCT_SIZE, QUANTITY, ITEM_PRICE, 
             DATE_ORDERED, PAYMENT_METHOD, TOTAL_ORDER_PAYMENT, BILL_AMOUNT, CHANGE_AMOUNT)
            VALUES ($order_id, $product_id, '$product_name', '$product_size', $quantity, $item_price, 
                    GETDATE(), '$payment_method', $subtotal, $bill_amount, $change_amount)";

    $result_insert = sqlsrv_query($conn, $sql_insert);

    if ($result_insert === false) {
        $insert_success = false;
        die(print_r(sqlsrv_errors(), true));
    }
    
    // GET FIRST ORDER_NO AND DATE_ORDERED FOR RECEIPT
    if ($first_order_no === null) {
        $sql_last_order = "SELECT TOP 1 ORDER_NO, DATE_ORDERED
                        FROM ORDER_CART 
                        WHERE ORDER_ID = $order_id 
                        ORDER BY ORDER_NO DESC";
        $result_last_order = sqlsrv_query($conn, $sql_last_order);
        if ($result_last_order) {
            $row_last_order = sqlsrv_fetch_array($result_last_order);
            $first_order_no = $row_last_order['ORDER_NO'];
            
            // GET THE ACTUAL DATE/TIME FROM DATABASE
            if ($row_last_order['DATE_ORDERED'] instanceof DateTime) {
                $order_date_time = $row_last_order['DATE_ORDERED']->format('Y-m-d H:i:s');
            } else {
                $order_date_time = date('Y-m-d H:i:s', strtotime($row_last_order['DATE_ORDERED']));
            }
        }
    }
}

sqlsrv_close($conn);

if ($insert_success) {
    // STORE ORDER INFO FOR RECEIPT
    $_SESSION['last_order_id'] = $order_id;
    $_SESSION['last_order_no'] = $first_order_no;
    $_SESSION['last_cart'] = $cart_data;
    $_SESSION['last_subtotal'] = $subtotal;
    $_SESSION['last_payment_method'] = $payment_method;
    $_SESSION['last_bill_amount'] = $bill_amount;
    $_SESSION['last_change_amount'] = $change_amount;
    $_SESSION['last_order_datetime'] = $order_date_time;
    
    // CLEAR CART SESSION
    unset($_SESSION['cart_data']);
    unset($_SESSION['subtotal']);
    
    // REDIRECT TO RECEIPT PAGE
    header("Location: receipt.php");
    exit();
} else {
    die(print_r(sqlsrv_errors(), true));
}
?>