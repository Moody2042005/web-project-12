<?php if (session_status() == PHP_SESSION_NONE) { session_start(); } ?>
<nav class="navbar navbar-inverse navbar-fixed-top">
    <div class="container-fluid">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#mainNavbar"
                aria-expanded="false" aria-label="Toggle navigation menu">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="index.php" title="Lifestyle Store Home">
                <i class="glyphicon glyphicon-home" style="margin-right: 8px;"></i>Lifestyle Store
            </a>
        </div>

        <div class="collapse navbar-collapse" id="mainNavbar">
            <ul class="nav navbar-nav">
                <!-- Main Navigation Links -->
                <li><a href="index.php" title="Home">
                        <i class="glyphicon glyphicon-home"></i> Home
                    </a></li>

                <li><a href="products.php?category=all" title="Browse Products">
                        <i class="glyphicon glyphicon-th-large"></i> Products
                    </a></li>

                <?php if(isset($_SESSION['username'])): ?>
                <li><a href="cart.php" title="View Shopping Cart">
                        <i class="glyphicon glyphicon-shopping-cart"></i> Cart
                        <?php
                    // Show cart item count if available
                    if(isset($_SESSION['id'])) {
                        require 'connection.php';
                        $user_id = $_SESSION['id'];
                        $cart_count_query = "SELECT COUNT(*) as count FROM users_items WHERE user_id = '$user_id' AND status = 'Added to cart'";
                        $cart_count_result = mysqli_query($conn, $cart_count_query);
                        if($cart_count_result) {
                            $cart_count = mysqli_fetch_assoc($cart_count_result)['count'];
                            if($cart_count > 0) {
                                echo '<span class="badge" style="background-color: #e74c3c; margin-left: 5px;">' . $cart_count . '</span>';
                            }
                        }
                    }
                    ?>
                    </a></li>
                <?php endif; ?>
            </ul>

            <ul class="nav navbar-nav navbar-right">
                <?php if(isset($_SESSION['username'])): ?>
                <!-- Admin Panel Link (Only for Admins) -->
                <?php if(isset($_SESSION['is_admin']) && $_SESSION['is_admin'] == 1): ?>
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" title="Administrator Panel"
                        style="color: #ffd700;">
                        <i class="glyphicon glyphicon-cog"></i> Admin Panel
                        <span class="caret"></span>
                    </a>
                    <ul class="dropdown-menu">
                        <li><a href="admin_dashboard.php">
                                <i class="glyphicon glyphicon-dashboard"></i> Dashboard
                            </a></li>
                        <li><a href="admin_users.php">
                                <i class="glyphicon glyphicon-user"></i> Manage Users
                            </a></li>
                        <li><a href="admin_products.php">
                                <i class="glyphicon glyphicon-gift"></i> Manage Products
                            </a></li>
                        <li><a href="admin_orders.php">
                                <i class="glyphicon glyphicon-list-alt"></i> Manage Orders
                            </a></li>
                        <li role="separator" class="divider"></li>
                        <li><a href="index.php">
                                <i class="glyphicon glyphicon-shopping-cart"></i> Store Front
                            </a></li>
                    </ul>
                </li>
                <?php endif; ?>

                <!-- User Dropdown Menu -->
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" title="User Account Menu">
                        <i class="glyphicon glyphicon-user"></i>
                        <?php 
                            $display_name = htmlspecialchars($_SESSION['name']);
                            if(strlen($display_name) > 15) {
                                $display_name = substr($display_name, 0, 12) . '...';
                            }
                            echo $display_name; 
                            ?>
                        <span class="caret"></span>
                    </a>
                    <ul class="dropdown-menu">
                        <li class="dropdown-header">
                            <i class="glyphicon glyphicon-user"></i>
                            <?php echo htmlspecialchars($_SESSION['username']); ?>
                            <?php if(isset($_SESSION['is_admin']) && $_SESSION['is_admin'] == 1): ?>
                            <span class="label label-warning" style="margin-left: 5px;">Admin</span>
                            <?php endif; ?>
                        </li>
                        <li role="separator" class="divider"></li>
                        <li><a href="settings.php">
                                <i class="glyphicon glyphicon-cog"></i> Account Settings
                            </a></li>
                        <li><a href="cart.php">
                                <i class="glyphicon glyphicon-shopping-cart"></i> My Cart
                            </a></li>
                        <li><a href="orders.php">
                                <i class="glyphicon glyphicon-list-alt"></i> My Orders
                            </a></li>
                        <li role="separator" class="divider"></li>
                        <li><a href="logout.php">
                                <i class="glyphicon glyphicon-log-out"></i> Logout
                            </a></li>
                    </ul>
                </li>

                <?php else: ?>
                <!-- Login/Signup Links for Non-logged in Users -->
                <li><a href="signup.php" title="Create New Account">
                        <i class="glyphicon glyphicon-user"></i> Sign Up
                    </a></li>
                <li><a href="login.php" title="Login to Your Account">
                        <i class="glyphicon glyphicon-log-in"></i> Login
                    </a></li>
                <?php endif; ?>
            </ul>

            <!-- Search Form (Optional) -->
            <form class="navbar-form navbar-right" role="search" action="search.php" method="GET">
                <div class="input-group">
                    <input type="text" class="form-control" placeholder="Search products..." name="q">
                    <div class="input-group-btn">
                        <button class="btn btn-default" type="submit">
                            <i class="glyphicon glyphicon-search"></i>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</nav>

<style>
/* Custom Styles for Header */
.navbar-inverse {
    background-color: #2c3e50;
    border-color: #2c3e50;
}

.navbar-inverse .navbar-brand {
    color: #ecf0f1;
    font-weight: 600;
}

.navbar-inverse .navbar-brand:hover {
    color: #ffffff;
}

.navbar-inverse .navbar-nav>li>a {
    color: #bdc3c7;
}

.navbar-inverse .navbar-nav>li>a:hover {
    color: #ffffff;
    background-color: transparent;
}

.navbar-inverse .navbar-nav>.active>a,
.navbar-inverse .navbar-nav>.active>a:hover,
.navbar-inverse .navbar-nav>.active>a:focus {
    color: #ffffff;
    background-color: #1a252f;
}

/* Dropdown Styles */
.dropdown-menu {
    background-color: #2c3e50;
    border: 1px solid #34495e;
}

.dropdown-menu>li>a {
    color: #bdc3c7;
    padding: 8px 20px;
}

.dropdown-menu>li>a:hover {
    color: #ffffff;
    background-color: #34495e;
}

.dropdown-menu>.divider {
    background-color: #34495e;
}

.dropdown-header {
    color: #7f8c8d;
    font-size: 12px;
    padding: 8px 20px;
}

/* Admin Panel Golden Color */
.navbar-inverse .navbar-nav>li>a[title="Administrator Panel"] {
    color: #ffd700;
    font-weight: bold;
}

.navbar-inverse .navbar-nav>li>a[title="Administrator Panel"]:hover {
    color: #ffed4e;
}

/* Search Form */
.navbar-form .form-control {
    background-color: #34495e;
    border-color: #2c3e50;
    color: #ecf0f1;
}

.navbar-form .form-control:focus {
    background-color: #3d566e;
    border-color: #3498db;
    color: #ffffff;
    box-shadow: none;
}

.navbar-form .btn-default {
    background-color: #3498db;
    border-color: #2980b9;
    color: white;
}

.navbar-form .btn-default:hover {
    background-color: #2980b9;
    border-color: #2472a4;
}

/* Responsive Styles */
@media (max-width: 767px) {
    .navbar-form {
        margin: 8px 15px;
        width: calc(100% - 30px);
    }

    .navbar-form .input-group {
        width: 100%;
    }

    .dropdown-menu {
        background-color: #34495e;
    }
}
</style>

<script>
$(document).ready(function() {
    // Highlight current page in navbar
    var currentPage = window.location.pathname.split('/').pop();
    $('.navbar-nav a[href="' + currentPage + '"]').parent().addClass('active');

    // For pages with query strings
    if (currentPage.includes('?')) {
        var basePage = currentPage.split('?')[0];
        $('.navbar-nav a[href="' + basePage + '"]').parent().addClass('active');
    }

    // Handle dropdown hover on desktop
    if ($(window).width() > 767) {
        $('.dropdown').hover(function() {
            $(this).addClass('open');
        }, function() {
            $(this).removeClass('open');
        });
    }

    // Close dropdowns when clicking elsewhere
    $(document).click(function(e) {
        if (!$(e.target).closest('.dropdown').length) {
            $('.dropdown').removeClass('open');
        }
    });
});
</script>