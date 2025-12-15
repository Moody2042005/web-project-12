<?php if (session_status() == PHP_SESSION_NONE) { session_start(); } ?>
<?php
require 'connection.php';

// DEBUG: Check session
// echo "<pre>Session check: ";
// print_r($_SESSION);
// echo "</pre>";

// Check if user is logged in - ONLY redirect if not logged in
if(!isset($_SESSION['username']) || empty($_SESSION['username'])) {
    // Store current page for redirect back
    $_SESSION['redirect_url'] = $_SERVER['REQUEST_URI'];
    
    // Clear any session data that might cause issues
    if(isset($_SESSION['login_attempts'])) {
        unset($_SESSION['login_attempts']);
    }
    
    // Redirect to login
    header('Location: login.php');
    exit();
}

// If we get here, user is logged in
$category = isset($_GET['category']) ? mysqli_real_escape_string($conn, $_GET['category']) : '';

if($category && $category != 'all') {
    $query = "SELECT * FROM items WHERE category = '$category'";
    $page_title = ucfirst($category) . " - Lifestyle Store";
} else {
    $query = "SELECT * FROM items";
    $page_title = "All Products - Lifestyle Store";
}

$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html>

<head>
    <title><?php echo $page_title; ?></title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/style.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
</head>

<body>
    <?php include 'header.php'; ?>

    <div class="container products-container">
        <div class="row">
            <div class="col-xs-12">
                <h2 class="text-center">Our Products</h2>

                <div class="text-center category-filter">
                    <div class="btn-group">
                        <a href="products.php?category=all"
                            class="btn btn-default <?php echo (!$category || $category == 'all') ? 'active' : ''; ?>">All</a>
                        <a href="products.php?category=Mobile"
                            class="btn btn-default <?php echo ($category == 'Mobile') ? 'active' : ''; ?>">Mobiles</a>
                        <a href="products.php?category=Camera"
                            class="btn btn-default <?php echo ($category == 'Camera') ? 'active' : ''; ?>">Cameras</a>
                        <a href="products.php?category=Watch"
                            class="btn btn-default <?php echo ($category == 'Watch') ? 'active' : ''; ?>">Watches</a>
                        <a href="products.php?category=Clothing"
                            class="btn btn-default <?php echo ($category == 'Clothing') ? 'active' : ''; ?>">Clothing</a>
                    </div>
                </div>

                <div class="row products-grid">
                    <?php
                    if(mysqli_num_rows($result) > 0) {
                        while($row = mysqli_fetch_assoc($result)) {
                            $item_id = $row['id'];
                            $item_name = $row['name'];
                            $item_price = $row['price'];
                            $item_image = $row['image'];
                            $item_desc = $row['description'];
                            $item_category = $row['category'];
                            
require_once "check_if_added.php";
                            $is_added = check_if_added_to_cart($item_id);
                    ?>
                    <div class="col-md-3 col-sm-6">
                        <div class="thumbnail product-item">
                            <img src="images/<?php echo htmlspecialchars($item_image); ?>"
                                alt="<?php echo htmlspecialchars($item_name); ?>" class="product-image img-responsive">
                            <div class="caption">
                                <h4 class="product-title"><?php echo htmlspecialchars($item_name); ?></h4>
                                <p class="product-description">
                                    <?php echo htmlspecialchars(substr($item_desc, 0, 60)) . '...'; ?></p>
                                <p class="product-price"><strong>₹<?php echo number_format($item_price, 2); ?></strong>
                                </p>

                                <?php if($is_added == 1): ?>
                                <a href="cart.php" class="btn btn-success btn-block">
                                    <span class="glyphicon glyphicon-ok"></span> View in Cart
                                </a>
                                <?php else: ?>
                                <form method="POST" action="cart_add.php">
                                    <input type="hidden" name="item_id" value="<?php echo $item_id; ?>">
                                    <button type="submit" class="btn btn-primary btn-block">
                                        <span class="glyphicon glyphicon-shopping-cart"></span> Add to Cart
                                    </button>
                                </form>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <?php
                        }
                    } else {
                        echo '<div class="col-xs-12 text-center"><h3>No products found in this category.</h3></div>';
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>

    <?php include 'footer.php'; ?>
</body>

</html>

<?php
mysqli_close($conn);
?>