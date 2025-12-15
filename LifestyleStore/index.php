<?php if (session_status() == PHP_SESSION_NONE) { session_start(); } ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lifestyle Store | Home</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/style.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
</head>

<body>
    <?php include 'header.php'; ?>

    <div class="banner-image">
        <div class="banner-content">
            <h1>We Sell Lifestyle</h1>
            <p>Flat 40% OFF on premium brands</p>
            <?php if(isset($_SESSION['username'])): ?>
            <a href="products.php" class="btn btn-danger btn-lg">Shop Now</a>
            <?php else: ?>
            <a href="login.php" class="btn btn-danger btn-lg">Login to Shop</a>
            <?php endif; ?>
        </div>
    </div>

    <div class="container main-container">
        <div class="row text-center">
            <div class="col-md-4 col-sm-6">
                <a href="products.php?category=Watch">
                    <div class="thumbnail">
                        <img src="images/watch.jpg" alt="Watches" class="img-responsive category-image">
                        <div class="caption">
                            <h3>Watches</h3>
                            <p>Original watches from the best brands.</p>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-md-4 col-sm-6">
                <a href="products.php?category=Camera">
                    <div class="thumbnail">
                        <img src="images/camera.jpg" alt="Cameras" class="img-responsive category-image">
                        <div class="caption">
                            <h3>Cameras</h3>
                            <p>Choose among the best available in the world.</p>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-md-4 col-sm-6">
                <a href="products.php?category=Clothing">
                    <div class="thumbnail">
                        <img src="images/shirt.jpg" alt="Clothing" class="img-responsive category-image">
                        <div class="caption">
                            <h3>Clothing</h3>
                            <p>Our exquisite collection of shirts.</p>
                        </div>
                    </div>
                </a>
            </div>
        </div>
    </div>

    <?php include 'footer.php'; ?>
</body>

</html>