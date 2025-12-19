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
$product_id = $_POST['prod_id'];

// DELETE QUERY
$sql_delete = "DELETE FROM MENU 
               WHERE PRODUCT_ID = '$product_id'";
$result_delete = sqlsrv_query($conn, $sql_delete);

// DELETE FAILED
if ($result_delete == false) {
    "<script>
        alert('Warning: Error removing Menu Item!');
        window.location.href='manage-menu.php';
      </script>";
}

// DELETE SUCCESS
echo "<script>
        alert('Menu Item Deleted Successfully!');
        window.location.href='manage-menu.php';
      </script>";
exit();

?>
