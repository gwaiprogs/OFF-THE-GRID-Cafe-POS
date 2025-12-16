<?php
$serverName = "GRIDLAPTOP\\SQLEXPRESS";
$connectionOptions = [
    "Database" => "OTG",
    "Uid" => "",
    "PWD" => ""
];

$conn = sqlsrv_connect($serverName, $connectionOptions);
if ($conn == false) {
    die(print_r(sqlsrv_errors(), true));
}

// POST
$username = $_POST['username'];

// DELETE QUERY
$sql_delete = "DELETE FROM CASHIER_DETAILS WHERE UPPER(USERNAME) = UPPER('$username')";
$result_delete = sqlsrv_query($conn, $sql_delete);

// DELETE SUCCESS
if ($result_delete) {
    echo "<script>
            alert('Cashier Removed Successfully!');
            window.location.href = 'manage-cashiers.php';
          </script>";
} 

// DELETE FAILED
else {
    echo "<script>
            alert('Warning: Error removing cashier!');
            window.location.href = 'manage-cashiers.php';
          </script>";
}
?>
