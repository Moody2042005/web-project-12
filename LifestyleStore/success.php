<?php if (session_status() == PHP_SESSION_NONE) { session_start(); } ?>
<?php
if(!isset($_SESSION['id']) || !isset($_SESSION['order_id'])) {
    header('location: index.php');
    exit();
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Order Successful | Lifestyle Store</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/style.css">
    <meta http-equiv="refresh" content="5;url=index.php">
</head>

<body>
    <?php include 'header.php'; ?>

    <div class="container success-container text-center">
        <div class="alert alert-success">
            <h1><span class="glyphicon glyphicon-ok-circle"></span></h1>
            <h2>Order Placed Successfully!</h2>
            <p>Your order has been confirmed. Thank you for shopping with us!</p>
            <p><strong>Order ID:</strong> #<?php echo $_SESSION['order_id']; ?></p>
            <p><strong>Total Amount:</strong>
                ₹<?php echo isset($_SESSION['order_total']) ? number_format($_SESSION['order_total'], 2) : '0.00'; ?>
            </p>
            <p>You will receive your order in 3-5 business days.</p>
            <hr>
            <p>Redirecting to homepage in 5 seconds...</p>
            <a href="index.php" class="btn btn-primary">Go to Homepage Now</a>
        </div>
    </div>

    <?php
    unset($_SESSION['order_id']);
    unset($_SESSION['order_total']);
    ?>

    <?php include 'footer.php'; ?>
</body>

</html>