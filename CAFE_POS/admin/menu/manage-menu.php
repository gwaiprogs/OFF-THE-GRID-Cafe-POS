<?php
$serverName = "GRIDLAPTOP\SQLEXPRESS";
$connectionOptions = [
    "Database" => "OTG",
    "Uid" => "",
    "PWD" => ""
];

$conn = sqlsrv_connect($serverName, $connectionOptions);
if($conn == false) {
    die(print_r(sqlsrv_errors(), true));
}

$sql = "SELECT * FROM MENU ORDER BY PRODUCT_ID";
$result = sqlsrv_query($conn, $sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Menu</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="../../styles/manage-menu-style.css">
</head>

<body>

<!-- NAVBAR -->
<nav class="navbar px-4 d-flex justify-content-between align-items-center">
       <span class="navbar-brand fw-bold">MANAGE MENU</span>
    
    <div class="d-flex gap-3">
        <!-- Back to Dashboard Button with Icon -->
        <form action="../admin-dashboard.html" method="post">
            <button type="submit" class="logout-btn">
                <i class="bi bi-border-width"></i> Back to Dashboard
            </button>
        </form>
        
        <!-- Order Page Button with Icon -->
        <form action="../orders/order-page.php" method="post">
            <button type="submit" class="logout-btn">
                <i class="bi bi-cart-fill"></i> Order Page
            </button>
        </form>
    </div>
</nav>

<!-- MAIN CARD -->
<div class="d-flex justify-content-center align-items-start mt-5 w-100">
    <div class="manage-card">

        <h3 class="text-center fw-bold mb-4">Menu Items</h3>

        <!-- LEFT CONTROL SECTION -->
        <div class="mb-3">

            <!-- ADD NEW PRODUCT BUTTON -->
            <form action="add-menu.html" method="post" class="mb-2">
                <button class="btn btn-manage btn-sm">Add New Product</button>
            </form>

            <!-- FILTER DROPDOWN -->
            <select id="categoryFilter" class="form-select w-auto">
                <option value="All">All</option>
                <option value="Drinks">Drinks</option>
                <option value="Desserts">Desserts</option>
                <option value="Specials">Specials</option>
            </select>
        </div>

        <table class="table table-bordered text-center align-middle mt-3">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Product</th>
                    <th>Category</th>
                    <th>Small</th>
                    <th>Medium</th>
                    <th>Large</th>
                    <th>Regular</th>
                    <th>Image</th>
                    <th>Action</th>
                </tr>
            </thead>

            <tbody>
                <?php
                    while($row = sqlsrv_fetch_array($result)) {

                        $id = $row['PRODUCT_ID'];
                        $name = $row['PRODUCT_NAME'];
                        $category = $row['CATEGORY'];
                        $small = $row['PRICE_SMALL'];
                        $medium = $row['PRICE_MEDIUM'];
                        $large = $row['PRICE_LARGE'];
                        $regular = $row['PRICE_REGULAR'];
                        $image = $row['PRODUCT_IMAGE'];
                        

                        echo "
                        <tr>
                            <td>$id</td>
                            <td>$name</td>
                            <td>$category</td>
                            <td>$small</td>
                            <td>$medium</td>
                            <td>$large</td>
                            <td>$regular</td>

                            <td>
                                <img src='$image' class='menu-img'>
                            </td>

                            <td>
                                <form action='edit-menu.php' method='post' class='mb-1'>
                                    <input type='hidden' name='prod_id' value='$id'>
                                    <button class='btn-edit action-btn' type='submit'>Edit</button>
                                </form>

                                <form action='delete-menu.php' method='post'>
                                    <input type='hidden' name='prod_id' value='$id'>
                                    <button class='btn-delete action-btn' onclick=\"return confirm('Delete this item?')\" type='submit'>Delete</button>
                                </form>
                            </td>
                        </tr>";
                    }
                ?>
            </tbody>

        </table>

    </div>
</div>

<!-- JAVASCRIPT FILTER -->
<script>
document.getElementById("categoryFilter").addEventListener("change", function() {
    let filter = this.value.toLowerCase();
    let rows = document.querySelectorAll("tbody tr");

    rows.forEach(row => {
        let category = row.children[2].textContent.trim().toLowerCase(); 

        if (filter === "all" || filter === category) {
            row.style.display = "";
        } else {
            row.style.display = "none";
        }
    });
});
</script>

</body>
</html>
