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
$id          = $_POST['id'];
$productName = $_POST['product_name'];
$category    = $_POST['category'];
$small       = $_POST['small'];
$medium      = $_POST['medium'];
$large       = $_POST['large'];
$regular     = $_POST['regular'];
$oldImage    = $_POST['old_image'];

// CHECK IF THERE IS ALREADY AN IMAGE
$imagePath = $oldImage;

if (!empty($_FILES['file']['name'])) {

    // IMAGE UPLOAD
    $destination = "../products/";
    $filename = basename($_FILES['file']['name']);
    $finalfilepath = $destination . $filename;

    $allowtypes = array('jpg', 'png');
    $filetype = pathinfo($finalfilepath, PATHINFO_EXTENSION);

    if (in_array(strtolower($filetype), $allowtypes)) {
       
        $finalfolder = move_uploaded_file($_FILES['file']['tmp_name'], $finalfilepath);

        if ($finalfolder) {
            $imagePath = $finalfilepath;
        } else {
            echo "<script>alert('Failed to upload image.');
            window.location.href='edit-menu.php?id=$id';</script>";
            exit();
        }

    } else {
        // INCORRECT FILE TYPE
        echo "<script>alert('Incorrect File Type (JPG/PNG only).');
        window.location.href='edit-menu.php?id=$id';</script>";
        exit();
    }
}

// EMPTY VALUES WILL BE NULL IN SQL
if ($small === "" || $small === null)      
    $small = "NULL";
if ($medium === "" || $medium === null)    
    $medium = "NULL";
if ($large === "" || $large === null)      
    $large = "NULL";
if ($regular === "" || $regular === null)  
    $regular = "NULL";

// UPDATE QUERY
$sql_update = "
UPDATE MENU SET
    PRODUCT_NAME  = '$productName',
    CATEGORY      = '$category',
    PRICE_SMALL   = $small,
    PRICE_MEDIUM  = $medium,
    PRICE_LARGE   = $large,
    PRICE_REGULAR = $regular,
    PRODUCT_IMAGE = '$imagePath'
WHERE PRODUCT_ID = '$id'
";

$result_update = sqlsrv_query($conn, $sql_update);

if ($result_update == false) {
    die(print_r(sqlsrv_errors(), true));
}

// SUCCESS MESSAGE
echo "<script>alert('Product Updated Successfully!');
window.location.href='manage-menu.php';</script>";
exit();
?>