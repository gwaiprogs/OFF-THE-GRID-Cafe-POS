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

// SET TIMEZONE
date_default_timezone_set('Asia/Manila');

// GET DATE TODAY
$today = date('Y-m-d');

// DAILY SALES
$sql_daily_sales = "SELECT 
    COUNT(DISTINCT ORDER_ID) as total_orders,
    SUM(ITEM_PRICE * QUANTITY) as total_sales,
    SUM(QUANTITY) as total_items
FROM ORDER_CART
WHERE CAST(DATE_ORDERED AS DATE) = '$today'";

$result_daily = sqlsrv_query($conn, $sql_daily_sales);
$data_daily = sqlsrv_fetch_array($result_daily);

// WEEKLY SALES
$sql_weekly_sales = "SELECT 
    COUNT(DISTINCT ORDER_ID) as total_orders,
    SUM(ITEM_PRICE * QUANTITY) as total_sales,
    SUM(QUANTITY) as total_items
FROM ORDER_CART
WHERE DATE_ORDERED >= DATEADD(day, -7, GETDATE())";

$result_weekly = sqlsrv_query($conn, $sql_weekly_sales);
$data_weekly = sqlsrv_fetch_array($result_weekly);

// TOP PRODUCTS TODAY
$sql_top_daily = "SELECT TOP 5
    PRODUCT_NAME,
    SUM(QUANTITY) as total_sold,
    SUM(ITEM_PRICE * QUANTITY) as total_revenue
FROM ORDER_CART
WHERE CAST(DATE_ORDERED AS DATE) = '$today'
GROUP BY PRODUCT_NAME
ORDER BY total_sold DESC";

$result_top_daily = sqlsrv_query($conn, $sql_top_daily);

// TOP PRODUCTS THIS WEEK
$sql_top_weekly = "SELECT TOP 5
    PRODUCT_NAME,
    SUM(QUANTITY) as total_sold,
    SUM(ITEM_PRICE * QUANTITY) as total_revenue
FROM ORDER_CART
WHERE DATE_ORDERED >= DATEADD(day, -7, GETDATE())
GROUP BY PRODUCT_NAME
ORDER BY total_sold DESC";

$result_top_weekly = sqlsrv_query($conn, $sql_top_weekly);

// PAYMENT METHOD BREAKDOWN RECORD TODAY
$sql_payment_daily = "SELECT 
    PAYMENT_METHOD,
    COUNT(DISTINCT ORDER_ID) as order_count,
    SUM(ITEM_PRICE * QUANTITY) as total
FROM ORDER_CART
WHERE CAST(DATE_ORDERED AS DATE) = '$today'
GROUP BY PAYMENT_METHOD";

$result_payment_daily = sqlsrv_query($conn, $sql_payment_daily);

// PAYMENT METHOD BREAKDOWN RECORD THIS WEEK
$sql_payment_weekly = "SELECT 
    PAYMENT_METHOD,
    COUNT(DISTINCT ORDER_ID) as order_count,
    SUM(ITEM_PRICE * QUANTITY) as total
FROM ORDER_CART
WHERE DATE_ORDERED >= DATEADD(day, -7, GETDATE())
GROUP BY PAYMENT_METHOD";

$result_payment_weekly = sqlsrv_query($conn, $sql_payment_weekly);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Sales Reports - OFF-THE-GRID</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="../../styles/view-reports-style.css">
</head>
<body>

<!-- NAVBAR -->
<nav class="navbar d-flex justify-content-between align-items-center">
    <span class="navbar-brand fw-bold">SALES REPORTS</span>
    
    <div class="d-flex gap-3">
        <form action="../admin-dashboard.html" method="get">
            <button type="submit" class="logout-btn">
                <i class="bi bi-border-width"></i> Back to Dashboard
            </button>
        </form>
        <form action="../orders/order-page.php" method="get">
            <button type="submit" class="logout-btn">
                <i class="bi bi-cart-fill"></i> Order Page
            </button>
        </form>
    </div>
</nav>

<div class="container-fluid py-4">
    
    <!-- DAILY DETAILS -->
    <div class="report-card">
        <h2 class="section-title"> <i class="bi bi-calendar-heart"></i> Today's Details (<?php echo date('F d, Y'); ?>)</h2>

        <!-- DAILY EARNINGS CARD -->
        <div class="earnings-card mb-4">
            <div class="earnings-header">
                <i class="bi bi-calendar"></i>
                <span>Today's Earnings</span>
            </div>
            
            <div class="earnings-amount">
                ₱<?php echo number_format($data_daily['total_sales'] ?: 0, 2); ?>
            </div>
            
            <div class="earnings-stats">
                <div class="stat-item">
                    <div class="stat-label">Number of Orders</div>
                    <div class="stat-value"><?php echo $data_daily['total_orders'] ?: 0; ?></div>
                    <div class="stat-sublabel">Total Orders</div>
                </div>
                <div class="stat-item">
                    <div class="stat-label">Items sold</div>
                    <div class="stat-value"><?php echo $data_daily['total_items'] ?: 0; ?></div>
                    <div class="stat-sublabel">In past 1 day</div>
                </div>
            </div>
        </div>

        <!-- PAYMENT METHODS BREAKDOWN TODAY -->
        <h5 class="mt-4 mb-3 fw-bold"> <i class="bi bi-credit-card"></i> Payment Methods</h5>
        <div class="row">
            <?php while ($row_payment = sqlsrv_fetch_array($result_payment_daily)): ?>
            <div class="col-md-4 mb-3">
                <div class="payment-box">
                    <strong><?php echo strtoupper($row_payment['PAYMENT_METHOD']); ?></strong><br>
                    <small><?php echo $row_payment['order_count']; ?> orders | ₱<?php echo number_format($row_payment['total'], 2); ?></small>
                </div>
            </div>
            <?php endwhile; ?>
        </div>

        <!-- TOP PRODUCTS TODAY -->
        <h5 class="mt-4 mb-3 fw-bold"><i class="bi bi-trophy"></i> Top Products Today</h5>
        <div class="bg-white p-3 rounded">
            <?php 
            $rank = 1;
            $has_products = false;
            while ($row_product = sqlsrv_fetch_array($result_top_daily)): 
                $has_products = true;
                $badge_class = '';
                if ($rank == 1) $badge_class = 'gold';
                elseif ($rank == 2) $badge_class = 'silver';
                elseif ($rank == 3) $badge_class = 'bronze';
            ?>
            <div class="product-item">
                <div class="d-flex align-items-center">
                    <span class="rank-badge <?php echo $badge_class; ?>"><?php echo $rank; ?></span>
                    <div>
                        <strong><?php echo $row_product['PRODUCT_NAME']; ?></strong><br>
                        <small class="text-muted"><?php echo $row_product['total_sold']; ?> sold</small>
                    </div>
                </div>
                <div class="text-end">
                    <strong>₱<?php echo number_format($row_product['total_revenue'], 2); ?></strong>
                </div>
            </div>
            <?php 
            $rank++;
            endwhile; 
            if (!$has_products) echo '<p class="text-center text-muted">No sales today yet</p>';
            ?>
        </div>
    </div>

    <!-- WEEKLY DETAILS -->
    <div class="report-card">
        <h2 class="section-title"> <i class="bi bi-calendar-heart-fill"></i> Weekly Details (Last 7 Days)</h2>

        <!-- WEEKLY EARNINGS CARD -->
        <div class="earnings-card mb-4">
            <div class="earnings-header">
                <i class="bi bi-calendar-week"></i>
                <span>Weekly Earnings</span>
            </div>
            
            <div class="earnings-amount">
                ₱<?php echo number_format($data_weekly['total_sales'] ?: 0, 2); ?>
            </div>
            
            <div class="earnings-stats">
                <div class="stat-item">
                    <div class="stat-label">Number of Orders</div>
                    <div class="stat-value"><?php echo $data_weekly['total_orders'] ?: 0; ?></div>
                    <div class="stat-sublabel">Total Orders</div>
                </div>
                <div class="stat-item">
                    <div class="stat-label">Items sold</div>
                    <div class="stat-value"><?php echo $data_weekly['total_items'] ?: 0; ?></div>
                    <div class="stat-sublabel">In past 7 days</div>
                </div>
            </div>
        </div>

        <!-- PAYMENT METHODS BREAKDOWN WEEKLY -->
        <h5 class="mt-4 mb-3 fw-bold"> <i class="bi bi-credit-card"></i>  Payment Methods</h5>
        <div class="row">
            <?php 
            $result_payment_weekly_2 = sqlsrv_query($conn, $sql_payment_weekly);
            while ($row_payment = sqlsrv_fetch_array($result_payment_weekly_2)): 
            ?>
            <div class="col-md-4 mb-3">
                <div class="payment-box">
                    <strong><?php echo strtoupper($row_payment['PAYMENT_METHOD']); ?></strong><br>
                    <small><?php echo $row_payment['order_count']; ?> orders | ₱<?php echo number_format($row_payment['total'], 2); ?></small>
                </div>
            </div>
            <?php endwhile; ?>
        </div>

        <!-- TOP PRODUCTS THIS WEEK -->
        <h5 class="mt-4 mb-3 fw-bold"><i class="bi bi-trophy"></i> Top Products This Week</h5>
        <div class="bg-white p-3 rounded">
            <?php 
            $rank = 1;
            $has_products = false;
            $result_top_weekly_2 = sqlsrv_query($conn, $sql_top_weekly);
            while ($row_product = sqlsrv_fetch_array($result_top_weekly_2)): 
                $has_products = true;
                $badge_class = '';
                if ($rank == 1) $badge_class = 'gold';
                elseif ($rank == 2) $badge_class = 'silver';
                elseif ($rank == 3) $badge_class = 'bronze';
            ?>
            <div class="product-item">
                <div class="d-flex align-items-center">
                    <span class="rank-badge <?php echo $badge_class; ?>"><?php echo $rank; ?></span>
                    <div>
                        <strong><?php echo $row_product['PRODUCT_NAME']; ?></strong><br>
                        <small class="text-muted"><?php echo $row_product['total_sold']; ?> sold</small>
                    </div>
                </div>
                <div class="text-end">
                    <strong>₱<?php echo number_format($row_product['total_revenue'], 2); ?></strong>
                </div>
            </div>
            <?php 
            $rank++;
            endwhile; 
            if (!$has_products) echo '<p class="text-center text-muted">No sales this week yet</p>';
            ?>
        </div>
    </div>

</div>

<?php sqlsrv_close($conn); ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>