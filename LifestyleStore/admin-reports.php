<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit;
}

require_once "connection.php";

// Total sales & orders
$totalSalesStmt = $conn->query("SELECT COUNT(*) AS total_orders, SUM(total_price) AS total_revenue FROM orders");
$summary = $totalSalesStmt->fetch_assoc();

// Product sales
$productStmt = $conn->query("
    SELECT p.id, p.name, COUNT(oi.quantity) AS sold_qty, SUM(oi.quantity * oi.price) AS revenue
    FROM order_items oi
    JOIN products p ON oi.product_id = p.id
    GROUP BY p.id
    ORDER BY revenue DESC
");

?>
<!DOCTYPE html>
<html>

<head>
    <title>Admin - Sales Reports</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>

<body>

    <div class="container mt-4">
        <h1>Sales Reports</h1>

        <div class="row mb-4">
            <div class="col-md-4">
                <div class="card text-bg-success p-3">
                    <h5>Total Orders</h5>
                    <p class="fs-4"><?php echo $summary['total_orders'] ?: 0; ?></p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card text-bg-info p-3">
                    <h5>Total Revenue</h5>
                    <p class="fs-4"><?php echo number_format($summary['total_revenue'], 2); ?> EGP</p>
                </div>
            </div>
        </div>

        <h3>Revenue by Product</h3>
        <table class="table table-bordered">
            <thead class="table-dark">
                <tr>
                    <th>Product</th>
                    <th>Quantity Sold</th>
                    <th>Revenue</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($prod = $productStmt->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($prod['name']); ?></td>
                    <td><?php echo intval($prod['sold_qty']); ?></td>
                    <td><?php echo number_format($prod['revenue'], 2); ?> EGP</td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

</body>

</html>