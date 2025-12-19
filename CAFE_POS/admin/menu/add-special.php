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

// POST VALUES
$product_name = $_POST['name'];
$category     = "Specials";
$price_small  = $_POST['price_small'];

// IMAGE UPLOAD
$destination = "../products/";
$filename = basename($_FILES['file']['name']);
$finalfilepath = $destination . $filename;

$allowtypes = array('jpg', 'png');
$filetype = pathinfo($finalfilepath, PATHINFO_EXTENSION);

if (in_array(strtolower($filetype), $allowtypes)) {

$finalfolder = move_uploaded_file($_FILES['file']['tmp_name'], $finalfilepath);

// INSERT VALUES
$sql_special = "INSERT INTO MENU(PRODUCT_NAME, CATEGORY, PRICE_SMALL,PRODUCT_IMAGE) 
                VALUES('$product_name', '$category','$price_small','$finalfilepath')";
$result_special = sqlsrv_query($conn, $sql_special);

if ($result_special == false) {
    die(print_r(sqlsrv_errors(), true));
}

// INCORRECT FILE TYPE
} else {
    echo "<script>alert('Incorrect File Type (JPG/PNG only).');
    window.location.href='add-special.html';</script>";
    exit();
}

// SUCCESS MESSAGE
echo "<script>alert('Special Added Successfully!');
window.location.href='manage-menu.php';</script>";
exit();

?>
