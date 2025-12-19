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
$id = $_POST['prod_id'];

// DATA QUERY
$sql_data = "SELECT * 
        FROM MENU 
        WHERE PRODUCT_ID = '$id'";
$result_data = sqlsrv_query($conn, $sql_data);
$row_data = sqlsrv_fetch_array($result_data);

$productName = $row_data['PRODUCT_NAME'];
$category    = $row_data['CATEGORY'];
$small       = $row_data['PRICE_SMALL'];
$medium      = $row_data['PRICE_MEDIUM'];
$large       = $row_data['PRICE_LARGE'];
$regular     = $row_data['PRICE_REGULAR'];
$image       = $row_data['PRODUCT_IMAGE'];

// CREATION OF VARIABLES FOR DISABLING
$disableSmall   = "";
$disableMedium  = "";
$disableLarge   = "";
$disableRegular = "";

// DRINKS (REGULAR PRICE IS DISABLED)
if ($category == "Drinks") {
    $disableRegular = "disabled";
}

// DESSERTS (SMALL, MEDIUM, LARGE PRICE IS DISABLED)
else if ($category == "Desserts") {
    $disableSmall  = "disabled";
    $disableMedium = "disabled";
    $disableLarge  = "disabled";
}

// SPECIALS (MEDIUM, LARGE, REGULAR PRICE IS DISABLED)
else if ($category == "Specials") {
    $disableMedium  = "disabled";
    $disableLarge   = "disabled";
    $disableRegular = "disabled";
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Product</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="../../styles/manage-menu-style.css">
</head>

<body>

<!-- NAVBAR -->
<nav class="navbar px-4 d-flex justify-content-between">
    <span class="navbar-brand">MANAGE MENU</span>

    <form action="manage-menu.php" method="post">
        <button class="logout-btn btn"> <i class="bi bi-clipboard2"></i> Back to Menu</button>
    </form>
</nav>

<!-- MAIN CARD -->
<div class="d-flex justify-content-center align-items-start mt-5 w-100">
    <div class="manage-card add-width">

        <h3 class="text-center fw-bold mb-4">Edit Product</h3>

        <form action="update-menu.php" method="post" enctype="multipart/form-data">

            <!-- HIDDEN FIELDS FOR UPDATE -->
            <input type="hidden" name="id" value="<?php echo $id; ?>">
            <input type="hidden" name="old_image" value="<?php echo $image; ?>">

            <!-- PRODUCT NAME -->
            <div class="mb-3">
                <label class="label-text">Product Name</label>
                <input 
                    type="text" 
                    name="product_name" 
                    class="form-control add-input"
                    value="<?php echo $productName; ?>"
                    required
                >
            </div>

            <!-- CATEGORY (READ-ONLY AS ADDING PRODUCTS ALREADY DEFINE THE CATEGORY EXPLICITLY) -->
            <div class="mb-3">
                <label class="label-text">Category</label>
                <input 
                    type="text" 
                    name="category"
                    class="form-control add-input"
                    value="<?php echo $category; ?>"
                    readonly
                >
            </div>

            <!-- SMALL PRICE -->
            <div class="mb-3">
                <label class="label-text">Small Price</label>
                <input 
                    type="number" 
                    name="small"
                    class="form-control add-input"
                    value="<?php echo $small; ?>"
                    <?php echo $disableSmall; ?>
                >
            </div>

            <!-- MEDIUM PRICE -->
            <div class="mb-3">
                <label class="label-text">Medium Price</label>
                <input 
                    type="number" 
                    name="medium"
                    class="form-control add-input"
                    value="<?php echo $medium; ?>"
                    <?php echo $disableMedium; ?>
                >
            </div>

            <!-- LARGE PRICE -->
            <div class="mb-3">
                <label class="label-text">Large Price</label>
                <input 
                    type="number" 
                    name="large"
                    class="form-control add-input"
                    value="<?php echo $large; ?>"
                    <?php echo $disableLarge; ?>
                >
            </div>

            <!-- REGULAR PRICE -->
            <div class="mb-3">
                <label class="label-text">Regular Price</label>
                <input 
                    type="number" 
                    name="regular"
                    class="form-control add-input"
                    value="<?php echo $regular; ?>"
                    <?php echo $disableRegular; ?>
                >
            </div>

            <!-- CURRENT IMAGE -->
            <div class="mb-3">
                <label class="label-text">Current Image</label><br>
                <img src="<?php echo $image; ?>" class="menu-img mb-2">
            </div>

            <!-- REPLACE IMAGE -->
            <div class="mb-4">
                <label class="label-text">Replace Image (Optional)</label>
                <input type="file" name="file" class="form-control add-input" accept=".jpg, .png">
            </div>

            <button type="submit" class="btn btn-submit w-100">Update Product</button>

        </form>

    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
</body>
</html>
