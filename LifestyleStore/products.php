<?php 
// Ensure session is started once
if (session_status() == PHP_SESSION_NONE) { 
    session_start(); 
} 
?>
<?php
// This line correctly defines the connection variable $conn
require 'connection.php'; 

// Include the function definition here (needed before the function is called)
require_once "check_if_added.php"; 

// SIMPLE check - if not logged in, redirect to login
if(!isset($_SESSION['id'])) {
    header('Location: login.php');
    exit();
}

// Get category
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
                        <a href="products.php?category=Watch"
                            class="btn btn-default <?php echo ($category == 'Watch') ? 'active' : ''; ?>">Watches</a>
                        <a href="products.php?category=Camera"
                            class="btn btn-default <?php echo ($category == 'Camera') ? 'active' : ''; ?>">Cameras</a>
                        <a href="products.php?category=Clothing"
                            class="btn btn-default <?php echo ($category == 'Clothing') ? 'active' : ''; ?>">Clothing</a>
                    </div>
                </div>

                <div class="row products-grid">
                    <?php if(mysqli_num_rows($result) > 0): ?>
                    <?php while($row = mysqli_fetch_assoc($result)): 
                        
                        // --- THE FIX: Pass the $conn variable to the function ---
                        $is_added = check_if_added_to_cart($row['id'], $conn); 
                    ?>
                    <div class="col-md-3 col-sm-6">
                        <div class="thumbnail product-item">
                            <img src="images/<?php echo htmlspecialchars($row['image']); ?>"
                                alt="<?php echo htmlspecialchars($row['name']); ?>"
                                class="product-image img-responsive">
                            <div class="caption">
                                <h4 class="product-title"><?php echo htmlspecialchars($row['name']); ?></h4>
                                <p class="product-description">
                                    <?php echo htmlspecialchars(substr($row['description'], 0, 60)) . '...'; ?></p>
                                <p class="product-price">
                                    <strong>₹<?php echo number_format($row['price'], 2); ?></strong>
                                </p>

                                <?php if($is_added): ?>
                                <a href="cart.php" class="btn btn-success btn-block">
                                    <span class="glyphicon glyphicon-ok"></span> View in Cart
                                </a>
                                <?php else: ?>
                                <form method="POST" action="cart_add.php">
                                    <input type="hidden" name="item_id" value="<?php echo $row['id']; ?>">
                                    <button type="submit" class="btn btn-primary btn-block">
                                        <span class="glyphicon glyphicon-shopping-cart"></span> Add to Cart
                                    </button>
                                </form>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <?php endwhile; ?>
                    <?php else: ?>
                    <div class="col-xs-12 text-center">
                        <h3>No products found in this category.</h3>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <?php include 'footer.php'; ?>
</body>

</html>

<?php mysqli_close($conn); ?>