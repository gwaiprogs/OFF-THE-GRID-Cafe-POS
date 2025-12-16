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
$product_name  = $_POST['name'];
$category      = "Desserts";
$price_regular = $_POST['price_regular'];

// IMAGE UPLOAD

$destination = "../products/";
$filename = basename($_FILES['file']['name']);
$finalfilepath = $destination . $filename;

$allowtypes = array('jpg', 'png');
$filetype = pathinfo($finalfilepath, PATHINFO_EXTENSION);

if(in_array(strtolower($filetype), $allowtypes)) {

$finalfolder = move_uploaded_file($_FILES['file']['tmp_name'], $finalfilepath);

// INSERT VALUES        
$sql_dessert = "INSERT INTO MENU(PRODUCT_NAME, CATEGORY, PRICE_REGULAR, PRODUCT_IMAGE)
                VALUES('$product_name', '$category','$price_regular','$finalfilepath')";
$result_dessert = sqlsrv_query($conn, $sql_dessert);

if ($result_dessert == false) {
    die(print_r(sqlsrv_errors(), true));
}

// INCORRECT FILE TYPE
} else {
    echo "<script>alert('Incorrect File Type (JPG/PNG only).');
    window.location.href='add-dessert.html';</script>";
    exit();
}

// SUCCESS MESSAGE
echo "<script>alert('Dessert Added Successfully!');
window.location.href='manage-menu.php';</script>";
exit();

?>
