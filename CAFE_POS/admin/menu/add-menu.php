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
$product_name = $_POST['product_name'];
$category = $_POST['category'];

$price_small = $_POST['price_small'];
$price_medium = $_POST['price_medium'];
$price_large = $_POST['price_large'];
$price_regular = $_POST['price_regular'];

// CATEGORY VALIDATION

// DRINKS: small, medium, large sizes required
if ($category == "Drinks") {

    if ($price_small === "" || $price_medium === "" || $price_large === "") {
        echo "<script>alert('Drinks must have Small, Medium and Large prices.');
        window.location.href='add-menu.html';</script>";
        exit();
    }

    // REGULAR IS FOR DESSERTS ONLY
    $price_regular = NULL;
}

// DESSERTS: regular size required
else if ($category == "Desserts") {

    if ($price_regular === "") {
        echo "<script>alert('Desserts must have a Regular price.');
        window.location.href='add-menu.html';</script>";
        exit();
    }

    // DESSERTS ONLY HAVE ONE SIZE (REGULAR)
    $price_small = NULL;
    $price_medium = NULL;
    $price_large = NULL;
}

// SPECIALS: small size required
else if ($category == "Specials") {

    if ($price_small === "") {
        echo "<script>alert('Specials must have a Small price.');
        window.location.href='add-menu.html';</script>";
        exit();
    }

    // SPECIALS ONLY HAVE ONE SIZE (SMALL)
    $price_medium = NULL;
    $price_large = NULL;
    $price_regular = NULL;
}

// CONVERT NULL FOR DATABASE INSERTION

// INSERTS PRICE FOR DRINKS AND SPECIALS ONLY, NULL FOR DESSERTS
if ($price_small === NULL) {
    $sql_small = "NULL";
} else {
    $sql_small = "'$price_small'";
}

// INSERTS PRICE FOR DRINKS ONLY, NULL FOR DESSERTS AND SPECIALS
if ($price_medium === NULL) {
    $sql_medium = "NULL";
} else {
    $sql_medium = "'$price_medium'";
}

// INSERTS PRICE FOR DRINKS ONLY, NULL FOR DESSERTS AND SPECIALS
if ($price_large === NULL) {
    $sql_large = "NULL";
} else {
    $sql_large = "'$price_large'";
}

// INSERTS PRICE FOR DESSERTS ONLY, NULL FOR DRINKS AND SPECIALS
if ($price_regular === NULL) {
    $sql_regular = "NULL";
} else {
    $sql_regular = "'$price_regular'";
}

// IMAGE UPLOAD
$destination = "../products/";
$filename = basename($_FILES['file']['name']);
$finalfilepath = $destination . $filename;

$allowtypes = array('jpg', 'png');
$filetype = pathinfo($finalfilepath, PATHINFO_EXTENSION);

if (in_array(strtolower($filetype), $allowtypes)) {
   
$finalfolder = move_uploaded_file($_FILES['file']['tmp_name'], $finalfilepath);

// INSERT VALUES
$sql_menu = "INSERT INTO MENU(PRODUCT_NAME, CATEGORY, PRICE_SMALL, PRICE_MEDIUM, PRICE_LARGE, PRICE_REGULAR, PRODUCT_IMAGE)
            VALUES('$product_name', '$category', $sql_small, $sql_medium, $sql_large, $sql_regular, '$finalfilepath')";
$result_menu = sqlsrv_query($conn, $sql_menu);

if ($result_menu == false) {
    die(print_r(sqlsrv_errors(), true));
}
}

//INCORRECT FILE TYPE
else {
 echo "<script>alert('Incorrect File Type (JPG/PNG only).');
    window.location.href='add-menu.html';</script>";
    exit();
}

// SUCCESS MESSAGE
echo "<script>alert('Menu Item Added Successfully!');
window.location.href='manage-menu.php';</script>";
exit();

?>