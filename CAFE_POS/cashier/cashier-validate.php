<?php
$serverName = "GRIDLAPTOP\SQLEXPRESS";
$connectionOptions = [
    "Database"=>"OTG",
    "Uid" => "",
    "PWD" => ""
];

$conn = sqlsrv_connect($serverName, $connectionOptions);
if ($conn == false) {
    die(print_r(sqlsrv_errors(), true));
}

// POST
$username = $_POST['username'];
$password = $_POST['password'];

// VALIDATION
$sql_cashier = "SELECT * FROM CASHIER_DETAILS
                WHERE USERNAME = '$username' AND PASSWORD = '$password'";
$result_cashier = sqlsrv_query($conn, $sql_cashier);

if ($result_cashier === false) {
    die(print_r(sqlsrv_errors(), true));
}
$row_cashier = sqlsrv_fetch_array($result_cashier);

// LOGIN SUCCESS
if ($row_cashier) {
    header("Location: ../admin/orders/order-page.php");
    exit();
}

// LOGIN FAILED 
echo "
<script>
    alert('Invalid login credentials. Please try again.');
    window.location.href = 'cashier-login.html';
</script>
";
exit();
?>
