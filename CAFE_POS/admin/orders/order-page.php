<?php
$serverName = "GRIDLAPTOP\SQLEXPRESS";
$connectionOptions = [
    "Database" => "OTG",
    "Uid"      => "",
    "PWD"      => ""
];

$conn = sqlsrv_connect($serverName, $connectionOptions);
if ($conn == false) {
    die(print_r(sqlsrv_errors(), true));
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <title>Drink Ordering System</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="../../styles/order-page-style.css">
</head>

<body class="bg-light">

<!-- NAVBAR -->
<nav class="navbar shadow-sm px-4 d-flex justify-content-between">
    <span class="navbar-brand fw-bold fs-4">OFF-THE-GRID MENU</span>

    <!-- BACK TO HOME -->
    <form method="post" action="../../login.html" class="m-0">
        <button type="submit" class="btn btn-outline-light">
            Back to Home
        </button>
    </form>
</nav>

<div class="container-fluid py-3">

    <div class="row">


        <!-- PRODUCTS -->
        <div class="col-lg-8">

            <!-- CATEGORY BUTTONS -->
            <div class="d-flex justify-content-between mb-3 align-items-center">
                <div class="d-flex gap-3">
                    <button class="category-btn active">Drinks</button>
                    <button class="category-btn">Desserts</button>
                    <button class="category-btn">Specials</button>
                    <input type="text" placeholder ="Search" name="search"> 
                </div>
            </div>

            
            <!-- DRINKS -->
            <div id="section-drinks" class="category-section">
                <h4 class="fw-semibold mb-3">Drinks</h4>
                <div class="row g-4">

                <?php
                $sql = "SELECT PRODUCT_NAME, PRICE_SMALL, PRICE_MEDIUM, PRICE_LARGE, PRODUCT_IMAGE
                        FROM MENU
                        WHERE CATEGORY = 'Drinks'
                        ORDER BY PRODUCT_NAME";

                $result = sqlsrv_query($conn, $sql);

                if ($result === false) {
                    die(print_r(sqlsrv_errors(), true));
                }

                while ($fetch = sqlsrv_fetch_array($result)) {

                    $name   = $fetch['PRODUCT_NAME'];
                    $small  = $fetch['PRICE_SMALL'];
                    $medium = $fetch['PRICE_MEDIUM'];
                    $large  = $fetch['PRICE_LARGE'];
                    $image  = $fetch['PRODUCT_IMAGE'];

                    $priceJson = '{"S":'.$small.',"M":'.$medium.',"L":'.$large.'}';
                ?>

                    <div class="col-md-4">
                        <div class="drink-card"
                             data-name="<?php echo $name; ?>"
                             data-price='<?php echo $priceJson; ?>'>
                            <img src="<?php echo $image; ?>" class="drink-img">
                            <h6 class="mt-3"><?php echo $name; ?></h6>

                            <p class="price">
                                Php <?php echo $small; ?> / Php <?php echo $medium; ?> / Php <?php echo $large; ?>
                            </p>

                            <div class="size-options mt-2">
                                <button class="size-btn" data-size="S">S</button>
                                <button class="size-btn" data-size="M">M</button>
                                <button class="size-btn" data-size="L">L</button>
                            </div>
                        </div>
                    </div>

                <?php } ?>
                </div>
            </div>

            <!-- DESSERTS -->
        
            <div id="section-desserts" class="category-section d-none">
                <h4 class="fw-semibold mb-3">Desserts</h4>
                <div class="row g-4">

                <?php
                $sql = "SELECT PRODUCT_NAME, PRICE_REGULAR, PRODUCT_IMAGE
                        FROM MENU
                        WHERE CATEGORY = 'Desserts'
                        ORDER BY PRODUCT_NAME";

                $result = sqlsrv_query($conn, $sql);

                if ($result === false) {
                    die(print_r(sqlsrv_errors(), true));
                }

                while ($fetch = sqlsrv_fetch_array($result)) {

                    $name  = $fetch['PRODUCT_NAME'];
                    $reg   = $fetch['PRICE_REGULAR'];
                    $image = $fetch['PRODUCT_IMAGE'];

                    $priceJson = '{"R":'.$reg.'}';
                ?>

                    <div class="col-md-4">
                        <div class="drink-card"
                             data-name="<?php echo $name; ?>"
                             data-price='<?php echo $priceJson; ?>'>
                            <img src="<?php echo $image; ?>" class="drink-img">
                            <h6 class="mt-3"><?php echo $name; ?></h6>
                            <p class="price">Php <?php echo $reg; ?></p>

                            <div class="size-options mt-2">
                                <button class="size-btn" data-size="R">R</button>
                            </div>
                        </div>
                    </div>

                <?php } ?>
                </div>
            </div>

            <!-- SPECIALS -->
            <div id="section-specials" class="category-section d-none">
                <h4 class="fw-semibold mb-3">Specials</h4>
                <div class="row g-4">

                <?php
                $sql = "SELECT PRODUCT_NAME, PRICE_SMALL, PRODUCT_IMAGE
                        FROM MENU
                        WHERE CATEGORY = 'Specials'
                        ORDER BY PRODUCT_NAME";

                $result = sqlsrv_query($conn, $sql);

                if ($result === false) {
                    die(print_r(sqlsrv_errors(), true));
                }

                while ($fetch = sqlsrv_fetch_array($result)) {

                    $name  = $fetch['PRODUCT_NAME'];
                    $small = $fetch['PRICE_SMALL'];
                    $image = $fetch['PRODUCT_IMAGE'];

                    $priceJson = '{"S":'.$small.'}';
                ?>

                    <div class="col-md-4">
                        <div class="drink-card"
                             data-name="<?php echo $name; ?>"
                             data-price='<?php echo $priceJson; ?>'>
                            <img src="<?php echo $image; ?>" class="drink-img">
                            <h6 class="mt-3"><?php echo $name; ?></h6>
                            <p class="price">Php <?php echo $small; ?></p>

                            <div class="size-options mt-2">
                                <button class="size-btn" data-size="S">S</button>
                            </div>
                        </div>
                    </div>

                <?php } ?>
                </div>
            </div>

        </div>

       
        <!-- ORDER SUMMARY -->
        <div class="col-lg-4">

            <div class="order-box shadow-sm p-3">

                <h5 class="fw-bold mb-3">Current Order</h5>

                <div id="order-items"></div>

                <div class="border-top pt-3 mt-3">

                    <div class="d-flex justify-content-between mb-2">
                        <span>Items (<span id="item-count">0</span>)</span>
                        <span id="item-total">Php 0.00</span>
                    </div>

                    <div class="d-flex justify-content-between fw-bold fs-5">
                        <span>Total</span>
                        <span id="grand-total">Php 0.00</span>
                    </div>

                    <form action="process-order.php" method="post" id="checkout-form">
                        <input type="hidden" name="cart_data" id="cart-data-input">
                        <input type="hidden" name="remarks" id="remarks-input">
                        <button class="btn btn-dark w-100 mt-3 fw-bold" type="submit">Proceed to Payment</button>
                    </form>

                </div>

            </div>

        </div>

    </div>
</div>


<script>
    // FOR CATEGORY CHOOSING 
document.querySelectorAll(".category-btn").forEach((btn, index) => {
    btn.addEventListener("click", function () {

        document.querySelectorAll(".category-btn")
            .forEach(b => b.classList.remove("active"));

        this.classList.add("active");

        const names = ["drinks", "desserts", "specials"];

        document.querySelectorAll(".category-section")
            .forEach(sec => sec.classList.add("d-none"));

        document.getElementById("section-" + names[index]).classList.remove("d-none");
    });
});

</script>

<script src="order.js"></script>

</body>
</html>
