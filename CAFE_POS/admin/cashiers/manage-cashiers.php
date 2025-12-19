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

// CASHIER QUERY
$sql = "SELECT USERNAME, PASSWORD 
        FROM CASHIER_DETAILS";
$result = sqlsrv_query($conn, $sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Cashiers</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="../../styles/manage-cashiers-style.css">
</head>

<body>

<!-- NAVBAR -->
<nav class="navbar px-4 d-flex justify-content-between">
    <span class="navbar-brand">MANAGE CASHIERS</span>

    <div class="d-flex gap-3">
        <!-- BACK TO DASHBOARD -->
        <form action="../admin-dashboard.html" method="post">
            <button type="submit" class="logout-btn">
                <i class="bi bi-border-width"></i>  Back to Dashboard
            </button>
        </form>
        
        <!-- CASHIER LOGIN -->
        <form action="../../cashier/cashier-login.html" method="post">
            <button type="submit" class="logout-btn">
                <i class="bi bi-person-circle"></i>  Cashier Login Page
            </button>
        </form>
    </div>
</nav>

<!-- TABLE OF CASHIER ACCOUNTS -->
<div class="d-flex justify-content-center align-items-start mt-5">
    <div class="manage-card">

        <h3 class="text-center fw-bold mb-4">Cashier Accounts</h3>

        <table class="table table-bordered text-center align-middle">
            <thead>
                <tr>
                    <th>Username</th>
                    <th>Password</th>
                    <th>Action</th>
                </tr>
            </thead>

            <tbody>
                <?php
                while($row = sqlsrv_fetch_array($result)) {

                    $username = $row['USERNAME'];
                    $password = $row['PASSWORD'];

                    echo '
                    <tr>
                        <td>'.$username.'</td>
                        <td>'.$password.'</td>
                        <td>
                            <form action="delete-cashier.php" method="post">
                                <input type="hidden" name="username" value="'.$username.'">
                                <button class="btn btn-remove btn-sm" type="submit">Remove</button>
                            </form>
                        </td>
                    </tr>';
                }
                ?>

                <!-- ADD NEW CASHIER ROW -->
                <tr>
                    <form action="add-cashier.php" method="post">
                        <td>
                            <input type="text" name="username" class="form-control add-input" placeholder="New username" required>
                        </td>

                        <td>
                            <input type="text" name="password" class="form-control add-input" placeholder="New password" required>
                        </td>

                        <td>
                            <button type="submit" class="btn btn-manage btn-sm">Add</button>
                        </td>
                    </form>
                </tr>

            </tbody>
        </table>

    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
</body>
</html>

