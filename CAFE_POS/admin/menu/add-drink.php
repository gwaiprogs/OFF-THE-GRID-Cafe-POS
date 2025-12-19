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
$category      = "Drinks";
$price_small   = $_POST['price_small'];
$price_medium  = $_POST['price_medium'];
$price_large   = $_POST['price_large'];

// IMAGE UPLOAD

$destination = "../products/";
$filename = basename($_FILES['file']['name']);
$finalfilepath = $destination . $filename;

$allowtypes = array('jpg', 'png');
$filetype = pathinfo($finalfilepath, PATHINFO_EXTENSION);

if (in_array(strtolower($filetype), $allowtypes)) {
   
$finalfolder = move_uploaded_file($_FILES['file']['tmp_name'], $finalfilepath);

// INSERT VALUES
$sql_drink = "INSERT INTO MENU(PRODUCT_NAME, CATEGORY, PRICE_SMALL, PRICE_MEDIUM, PRICE_LARGE, PRODUCT_IMAGE)
            VALUES('$product_name', '$category','$price_small', '$price_medium', '$price_large','$finalfilepath')";
$result_drink = sqlsrv_query($conn, $sql_drink);

if ($result_drink == false) {
    die(print_r(sqlsrv_errors(), true));
}
}

//  INCORRECT FILE TYPE
else {
    echo "<script>alert('Incorrect File Type (JPG/PNG only).');
    window.location.href='add-drink.html';</script>";
    exit();
}

// SUCCESS MESSAGE
echo "<script>alert('Drink Added Successfully!');
window.location.href='manage-menu.php';</script>";
exit();

?>
