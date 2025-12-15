<?php if (session_status() == PHP_SESSION_NONE) { session_start(); } ?>
<?php
require 'connection.php';

// Check if user is admin
if(!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1) {
    header('Location: login.php');
    exit();
}

// Get statistics
$stats = array();

// Total users
$result = mysqli_query($conn, "SELECT COUNT(*) as total FROM users WHERE is_admin = 0");
$stats['total_users'] = mysqli_fetch_assoc($result)['total'];

// Total products
$result = mysqli_query($conn, "SELECT COUNT(*) as total FROM items");
$stats['total_products'] = mysqli_fetch_assoc($result)['total'];

// Total orders
$result = mysqli_query($conn, "SELECT COUNT(*) as total FROM users_items WHERE status != 'Added to cart'");
$stats['total_orders'] = mysqli_fetch_assoc($result)['total'];

// Total revenue
$result = mysqli_query($conn, "SELECT SUM(i.price * ui.quantity) as total FROM users_items ui JOIN items i ON ui.item_id = i.id WHERE ui.status = 'Confirmed'");
$row = mysqli_fetch_assoc($result);
$stats['total_revenue'] = $row['total'] ? $row['total'] : 0;

// Recent orders
$recent_orders = mysqli_query($conn, "
    SELECT ui.*, u.username, i.name as product_name, i.price 
    FROM users_items ui 
    JOIN users u ON ui.user_id = u.id 
    JOIN items i ON ui.item_id = i.id 
    WHERE ui.status != 'Added to cart' 
    ORDER BY ui.added_at DESC 
    LIMIT 10
");

// Recent users
$recent_users = mysqli_query($conn, "SELECT * FROM users WHERE is_admin = 0 ORDER BY created_at DESC LIMIT 10");
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard | Lifestyle Store</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
    .admin-container {
        margin-top: 80px;
        margin-bottom: 50px;
    }

    .admin-sidebar {
        background-color: #2c3e50;
        color: white;
        min-height: calc(100vh - 130px);
        padding: 0;
    }

    .admin-sidebar .nav-pills>li>a {
        color: #ecf0f1;
        border-radius: 0;
        padding: 15px 20px;
        border-left: 4px solid transparent;
    }

    .admin-sidebar .nav-pills>li>a:hover {
        background-color: #34495e;
        color: white;
    }

    .admin-sidebar .nav-pills>li.active>a {
        background-color: #34495e;
        color: #3498db;
        border-left: 4px solid #3498db;
    }

    .admin-header {
        background-color: #3498db;
        color: white;
        padding: 20px;
        margin-bottom: 20px;
        border-radius: 5px;
    }

    .stat-card {
        background: white;
        border-radius: 8px;
        padding: 20px;
        margin-bottom: 20px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        transition: transform 0.3s ease;
    }

    .stat-card:hover {
        transform: translateY(-5px);
    }

    .stat-icon {
        font-size: 40px;
        float: right;
        opacity: 0.3;
    }

    .stat-value {
        font-size: 28px;
        font-weight: bold;
        color: #2c3e50;
    }

    .stat-label {
        color: #7f8c8d;
        font-size: 14px;
        text-transform: uppercase;
    }

    .table-responsive {
        background: white;
        border-radius: 8px;
        padding: 20px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    }

    .badge-pending {
        background-color: #f39c12;
    }

    .badge-confirmed {
        background-color: #3498db;
    }

    .badge-shipped {
        background-color: #9b59b6;
    }

    .badge-delivered {
        background-color: #27ae60;
    }

    .badge-cancelled {
        background-color: #e74c3c;
    }
    </style>
</head>

<body>
    <!-- Admin Header -->
    <nav class="navbar navbar-inverse navbar-fixed-top">
        <div class="container-fluid">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#adminNavbar"
                    aria-expanded="false" aria-label="Toggle navigation menu">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="admin_dashboard.php">
                    <i class="fas fa-crown"></i> Admin Panel
                </a>
            </div>
            <div class="collapse navbar-collapse" id="adminNavbar">
                <ul class="nav navbar-nav">
                    <li class="active"><a href="admin_dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
                    </li>
                    <li><a href="admin_users.php"><i class="fas fa-users"></i> Users</a></li>
                    <li><a href="admin_products.php"><i class="fas fa-box"></i> Products</a></li>
                    <li><a href="admin_orders.php"><i class="fas fa-shopping-cart"></i> Orders</a></li>
                    <li><a href="admin_reports.php"><i class="fas fa-chart-bar"></i> Reports</a></li>
                </ul>
                <ul class="nav navbar-nav navbar-right">
                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                            <i class="fas fa-user-shield"></i> <?php echo $_SESSION['name']; ?> <span
                                class="caret"></span>
                        </a>
                        <ul class="dropdown-menu">
                            <li><a href="admin_profile.php"><i class="fas fa-user-cog"></i> Profile</a></li>
                            <li><a href="admin_settings.php"><i class="fas fa-cog"></i> Settings</a></li>
                            <li role="separator" class="divider"></li>
                            <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
                        </ul>
                    </li>
                    <li><a href="index.php" title="Go to Store Front">
                            <i class="fas fa-store"></i> Store
                        </a></li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container-fluid admin-container">
        <div class="row">
            <!-- Sidebar removed for simplicity, using top navbar instead -->

            <!-- Main Content -->
            <div class="col-xs-12">
                <div class="admin-header">
                    <h1><i class="fas fa-tachometer-alt"></i> Admin Dashboard</h1>
                    <p>Welcome back, <?php echo $_SESSION['name']; ?>! Here's what's happening with your store.</p>
                </div>

                <!-- Statistics Cards -->
                <div class="row">
                    <div class="col-md-3 col-sm-6">
                        <div class="stat-card">
                            <div class="stat-icon text-primary">
                                <i class="fas fa-users"></i>
                            </div>
                            <div class="stat-value"><?php echo $stats['total_users']; ?></div>
                            <div class="stat-label">Total Users</div>
                        </div>
                    </div>

                    <div class="col-md-3 col-sm-6">
                        <div class="stat-card">
                            <div class="stat-icon text-success">
                                <i class="fas fa-box"></i>
                            </div>
                            <div class="stat-value"><?php echo $stats['total_products']; ?></div>
                            <div class="stat-label">Total Products</div>
                        </div>
                    </div>

                    <div class="col-md-3 col-sm-6">
                        <div class="stat-card">
                            <div class="stat-icon text-warning">
                                <i class="fas fa-shopping-cart"></i>
                            </div>
                            <div class="stat-value"><?php echo $stats['total_orders']; ?></div>
                            <div class="stat-label">Total Orders</div>
                        </div>
                    </div>

                    <div class="col-md-3 col-sm-6">
                        <div class="stat-card">
                            <div class="stat-icon text-danger">
                                <i class="fas fa-rupee-sign"></i>
                            </div>
                            <div class="stat-value">₹<?php echo number_format($stats['total_revenue'], 2); ?></div>
                            <div class="stat-label">Total Revenue</div>
                        </div>
                    </div>
                </div>

                <!-- Recent Orders -->
                <div class="row">
                    <div class="col-md-8">
                        <div class="table-responsive">
                            <h3><i class="fas fa-clock"></i> Recent Orders</h3>
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Order ID</th>
                                        <th>Customer</th>
                                        <th>Product</th>
                                        <th>Quantity</th>
                                        <th>Amount</th>
                                        <th>Status</th>
                                        <th>Date</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while($order = mysqli_fetch_assoc($recent_orders)): ?>
                                    <tr>
                                        <td>#<?php echo $order['id']; ?></td>
                                        <td><?php echo htmlspecialchars($order['username']); ?></td>
                                        <td><?php echo htmlspecialchars($order['product_name']); ?></td>
                                        <td><?php echo $order['quantity']; ?></td>
                                        <td>₹<?php echo number_format($order['price'] * $order['quantity'], 2); ?></td>
                                        <td>
                                            <?php 
                                            $status_class = 'badge-'.strtolower($order['status']);
                                            ?>
                                            <span class="badge <?php echo $status_class; ?>">
                                                <?php echo $order['status']; ?>
                                            </span>
                                        </td>
                                        <td><?php echo date('d M Y', strtotime($order['added_at'])); ?></td>
                                        <td>
                                            <a href="admin_order_view.php?id=<?php echo $order['id']; ?>"
                                                class="btn btn-xs btn-info">
                                                <i class="fas fa-eye"></i> View
                                            </a>
                                        </td>
                                    </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Recent Users -->
                    <div class="col-md-4">
                        <div class="table-responsive">
                            <h3><i class="fas fa-user-plus"></i> Recent Users</h3>
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Username</th>
                                        <th>Name</th>
                                        <th>Joined</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while($user = mysqli_fetch_assoc($recent_users)): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($user['username']); ?></td>
                                        <td><?php echo htmlspecialchars($user['name']); ?></td>
                                        <td><?php echo date('d M', strtotime($user['created_at'])); ?></td>
                                    </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>

                        <!-- Quick Actions -->
                        <div class="table-responsive" style="margin-top: 20px;">
                            <h3><i class="fas fa-bolt"></i> Quick Actions</h3>
                            <div class="list-group">
                                <a href="admin_add_product.php" class="list-group-item">
                                    <i class="fas fa-plus-circle"></i> Add New Product
                                </a>
                                <a href="admin_users.php" class="list-group-item">
                                    <i class="fas fa-user-edit"></i> Manage Users
                                </a>
                                <a href="admin_orders.php" class="list-group-item">
                                    <i class="fas fa-clipboard-list"></i> View All Orders
                                </a>
                                <a href="admin_reports.php" class="list-group-item">
                                    <i class="fas fa-chart-line"></i> Generate Reports
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
    $(document).ready(function() {
        // Auto-refresh dashboard every 60 seconds
        setInterval(function() {
            $.ajax({
                url: 'admin_stats.php',
                success: function(data) {
                    // Update stats if needed
                }
            });
        }, 60000);
    });
    </script>
</body>

</html>

<?php mysqli_close($conn); ?>