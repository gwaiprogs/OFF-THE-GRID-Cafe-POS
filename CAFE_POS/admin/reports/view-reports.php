<?php
// Database connection
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

// Set timezone to Philippines
date_default_timezone_set('Asia/Manila');

// Get today's date
$today = date('Y-m-d');

// ===== DAILY SALES =====
$dailySalesQuery = "SELECT 
    COUNT(DISTINCT ORDER_ID) as total_orders,
    SUM(ITEM_PRICE * QUANTITY) as total_sales,
    SUM(QUANTITY) as total_items
FROM ORDER_CART
WHERE CAST(DATE_ORDERED AS DATE) = ?";

$dailyResult = sqlsrv_query($conn, $dailySalesQuery, [$today]);
$dailyData = sqlsrv_fetch_array($dailyResult);

// ===== WEEKLY SALES =====
$weeklySalesQuery = "SELECT 
    COUNT(DISTINCT ORDER_ID) as total_orders,
    SUM(ITEM_PRICE * QUANTITY) as total_sales,
    SUM(QUANTITY) as total_items
FROM ORDER_CART
WHERE DATE_ORDERED >= DATEADD(day, -7, GETDATE())";

$weeklyResult = sqlsrv_query($conn, $weeklySalesQuery);
$weeklyData = sqlsrv_fetch_array($weeklyResult);

// ===== TOP PRODUCTS TODAY =====
$topDailyQuery = "SELECT TOP 5
    PRODUCT_NAME,
    SUM(QUANTITY) as total_sold,
    SUM(ITEM_PRICE * QUANTITY) as total_revenue
FROM ORDER_CART
WHERE CAST(DATE_ORDERED AS DATE) = ?
GROUP BY PRODUCT_NAME
ORDER BY total_sold DESC";

$topDailyResult = sqlsrv_query($conn, $topDailyQuery, [$today]);

// ===== TOP PRODUCTS THIS WEEK =====
$topWeeklyQuery = "SELECT TOP 5
    PRODUCT_NAME,
    SUM(QUANTITY) as total_sold,
    SUM(ITEM_PRICE * QUANTITY) as total_revenue
FROM ORDER_CART
WHERE DATE_ORDERED >= DATEADD(day, -7, GETDATE())
GROUP BY PRODUCT_NAME
ORDER BY total_sold DESC";

$topWeeklyResult = sqlsrv_query($conn, $topWeeklyQuery);

// ===== PAYMENT METHOD BREAKDOWN TODAY =====
$paymentDailyQuery = "SELECT 
    PAYMENT_METHOD,
    COUNT(DISTINCT ORDER_ID) as order_count,
    SUM(ITEM_PRICE * QUANTITY) as total
FROM ORDER_CART
WHERE CAST(DATE_ORDERED AS DATE) = ?
GROUP BY PAYMENT_METHOD";

$paymentDailyResult = sqlsrv_query($conn, $paymentDailyQuery, [$today]);

// ===== PAYMENT METHOD BREAKDOWN WEEKLY =====
$paymentWeeklyQuery = "SELECT 
    PAYMENT_METHOD,
    COUNT(DISTINCT ORDER_ID) as order_count,
    SUM(ITEM_PRICE * QUANTITY) as total
FROM ORDER_CART
WHERE DATE_ORDERED >= DATEADD(day, -7, GETDATE())
GROUP BY PAYMENT_METHOD";

$paymentWeeklyResult = sqlsrv_query($conn, $paymentWeeklyQuery);

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
    
    <!-- ========== DAILY DETAILS ========== -->
    <div class="report-card">
        <h2 class="section-title"> <i class="bi bi-calendar-heart"></i> Today's Details (<?php echo date('F d, Y'); ?>)</h2>

        <!-- Today's Earnings Card -->
        <div class="earnings-card mb-4">
            <div class="earnings-header">
                <i class="bi bi-calendar"></i>
                <span>Today's Earnings</span>
            </div>
            
            <div class="earnings-amount">
                ₱<?php echo number_format($dailyData['total_sales'] ?: 0, 2); ?>
            </div>
            
            <div class="earnings-stats">
                <div class="stat-item">
                    <div class="stat-label">Number of Orders</div>
                    <div class="stat-value"><?php echo $dailyData['total_orders'] ?: 0; ?></div>
                    <div class="stat-sublabel">Total Orders</div>
                </div>
                <div class="stat-item">
                    <div class="stat-label">Items sold</div>
                    <div class="stat-value"><?php echo $dailyData['total_items'] ?: 0; ?></div>
                    <div class="stat-sublabel">In past 1 day</div>
                </div>
            </div>
        </div>

        <!-- Payment Methods Today -->
        <h5 class="mt-4 mb-3 fw-bold"> <i class="bi bi-credit-card"></i> Payment Methods</h5>
        <div class="row">
            <?php while ($payment = sqlsrv_fetch_array($paymentDailyResult)): ?>
            <div class="col-md-4 mb-3">
                <div class="payment-box">
                    <strong><?php echo strtoupper($payment['PAYMENT_METHOD']); ?></strong><br>
                    <small><?php echo $payment['order_count']; ?> orders | ₱<?php echo number_format($payment['total'], 2); ?></small>
                </div>
            </div>
            <?php endwhile; ?>
        </div>

        <!-- Top Products Today -->
        <h5 class="mt-4 mb-3 fw-bold"><i class="bi bi-trophy"></i> Top Products Today</h5>
        <div class="bg-white p-3 rounded">
            <?php 
            $rank = 1;
            $hasProducts = false;
            while ($product = sqlsrv_fetch_array($topDailyResult)): 
                $hasProducts = true;
                $badgeClass = '';
                if ($rank == 1) $badgeClass = 'gold';
                elseif ($rank == 2) $badgeClass = 'silver';
                elseif ($rank == 3) $badgeClass = 'bronze';
            ?>
            <div class="product-item">
                <div class="d-flex align-items-center">
                    <span class="rank-badge <?php echo $badgeClass; ?>"><?php echo $rank; ?></span>
                    <div>
                        <strong><?php echo $product['PRODUCT_NAME']; ?></strong><br>
                        <small class="text-muted"><?php echo $product['total_sold']; ?> sold</small>
                    </div>
                </div>
                <div class="text-end">
                    <strong>₱<?php echo number_format($product['total_revenue'], 2); ?></strong>
                </div>
            </div>
            <?php 
            $rank++;
            endwhile; 
            if (!$hasProducts) echo '<p class="text-center text-muted">No sales today yet</p>';
            ?>
        </div>
    </div>

    <!-- ========== WEEKLY DETAILS ========== -->
    <div class="report-card">
        <h2 class="section-title"> <i class="bi bi-calendar-heart-fill"></i> Weekly Details (Last 7 Days)</h2>

        <!-- Weekly Earnings Card -->
        <div class="earnings-card mb-4">
            <div class="earnings-header">
                <i class="bi bi-calendar-week"></i>
                <span>Weekly Earnings</span>
            </div>
            
            <div class="earnings-amount">
                ₱<?php echo number_format($weeklyData['total_sales'] ?: 0, 2); ?>
            </div>
            
            <div class="earnings-stats">
                <div class="stat-item">
                    <div class="stat-label">Number of Orders</div>
                    <div class="stat-value"><?php echo $weeklyData['total_orders'] ?: 0; ?></div>
                    <div class="stat-sublabel">Total Orders</div>
                </div>
                <div class="stat-item">
                    <div class="stat-label">Items sold</div>
                    <div class="stat-value"><?php echo $weeklyData['total_items'] ?: 0; ?></div>
                    <div class="stat-sublabel">In past 7 days</div>
                </div>
            </div>
        </div>

        <!-- Payment Methods Weekly -->
        <h5 class="mt-4 mb-3 fw-bold"> <i class="bi bi-credit-card"></i>  Payment Methods</h5>
        <div class="row">
            <?php 
            // Reset the result pointer for weekly payments
            $paymentWeeklyResult2 = sqlsrv_query($conn, $paymentWeeklyQuery);
            while ($payment = sqlsrv_fetch_array($paymentWeeklyResult2)): 
            ?>
            <div class="col-md-4 mb-3">
                <div class="payment-box">
                    <strong><?php echo strtoupper($payment['PAYMENT_METHOD']); ?></strong><br>
                    <small><?php echo $payment['order_count']; ?> orders | ₱<?php echo number_format($payment['total'], 2); ?></small>
                </div>
            </div>
            <?php endwhile; ?>
        </div>

        <!-- Top Products Weekly -->
        <h5 class="mt-4 mb-3 fw-bold"><i class="bi bi-trophy"></i> Top Products This Week</h5>
        <div class="bg-white p-3 rounded">
            <?php 
            $rank = 1;
            $hasProducts = false;
            // Reset the result pointer for weekly products
            $topWeeklyResult2 = sqlsrv_query($conn, $topWeeklyQuery);
            while ($product = sqlsrv_fetch_array($topWeeklyResult2)): 
                $hasProducts = true;
                $badgeClass = '';
                if ($rank == 1) $badgeClass = 'gold';
                elseif ($rank == 2) $badgeClass = 'silver';
                elseif ($rank == 3) $badgeClass = 'bronze';
            ?>
            <div class="product-item">
                <div class="d-flex align-items-center">
                    <span class="rank-badge <?php echo $badgeClass; ?>"><?php echo $rank; ?></span>
                    <div>
                        <strong><?php echo $product['PRODUCT_NAME']; ?></strong><br>
                        <small class="text-muted"><?php echo $product['total_sold']; ?> sold</small>
                    </div>
                </div>
                <div class="text-end">
                    <strong>₱<?php echo number_format($product['total_revenue'], 2); ?></strong>
                </div>
            </div>
            <?php 
            $rank++;
            endwhile; 
            if (!$hasProducts) echo '<p class="text-center text-muted">No sales this week yet</p>';
            ?>
        </div>
    </div>

</div>

<?php sqlsrv_close($conn); ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>