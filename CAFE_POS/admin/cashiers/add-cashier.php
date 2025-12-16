<?php
$serverName = "GRIDLAPTOP\SQLEXPRESS";
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
$password = $_POST['password'];

// INSERT VALUES
$sql_add = "INSERT INTO CASHIER_DETAILS (USERNAME, PASSWORD)
            VALUES('$username', '$password')";

$result_add = sqlsrv_query($conn, $sql_add);

// VERIFY RESULT
if ($result_add) {
    echo "<script>
            alert('Cashier Added Successfully!');
            window.location.href = 'manage-cashiers.php';
          </script>";
}
else {
    echo "<script>
            alert('Error adding cashier.');
            window.location.href = 'manage-cashiers.php';
          </script>";
}
?>
