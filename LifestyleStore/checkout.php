<?php if (session_status() == PHP_SESSION_NONE) { session_start(); } ?>
<?php
require 'connection.php';

if(!isset($_SESSION['id'])) {
    header('location: login.php');
    exit();
}

$user_id = $_SESSION['id'];
$cart_query = "SELECT i.id, i.name, i.price, ui.quantity 
FROM users_items ui 
JOIN items i ON ui.item_id = i.id 
WHERE ui.user_id = '$user_id' AND ui.status = 'Added to cart'";
$cart_result = mysqli_query($conn, $cart_query);

$total = 0;
$item_count = mysqli_num_rows($cart_result);

if($item_count == 0) {
    header('location: cart.php');
    exit();
}

if($_SERVER["REQUEST_METHOD"] == "POST") {
    $update_query = "UPDATE users_items SET status = 'Confirmed' 
WHERE user_id = '$user_id' AND status = 'Added to cart'";
    
    if(mysqli_query($conn, $update_query)) {
        $order_id = mysqli_insert_id($conn);
        $_SESSION['order_id'] = $order_id;
        $_SESSION['order_total'] = $total;
        header('location: success.php');
        exit();
    } else {
        $error = "Error processing order.";
    }
}

$cart_items = array();
while($row = mysqli_fetch_assoc($cart_result)) {
    $row['subtotal'] = $row['price'] * $row['quantity'];
    $total += $row['subtotal'];
    $cart_items[] = $row;
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Checkout | Lifestyle Store</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>

<body>
    <?php include 'header.php'; ?>

    <div class="container checkout-container">
        <div class="row">
            <div class="col-md-8">
                <div class="panel panel-primary">
                    <div class="panel-heading">
                        <h3 class="panel-title">Order Summary</h3>
                    </div>
                    <div class="panel-body">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Item</th>
                                    <th>Price</th>
                                    <th>Quantity</th>
                                    <th>Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($cart_items as $item): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($item['name']); ?></td>
                                    <td>₹<?php echo number_format($item['price'], 2); ?></td>
                                    <td><?php echo $item['quantity']; ?></td>
                                    <td>₹<?php echo number_format($item['subtotal'], 2); ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>

                        <div class="text-right">
                            <h4>Total: ₹<?php echo number_format($total, 2); ?></h4>
                        </div>
                    </div>
                </div>

                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title">Shipping Information</h3>
                    </div>
                    <div class="panel-body">
                        <?php
                        $user_query = "SELECT name, contact, city, address FROM users WHERE id = '$user_id'";
                        $user_result = mysqli_query($conn, $user_query);
                        $user = mysqli_fetch_assoc($user_result);
                        ?>

                        <p><strong>Name:</strong> <?php echo htmlspecialchars($user['name']); ?></p>
                        <p><strong>Contact:</strong> <?php echo htmlspecialchars($user['contact']); ?></p>
                        <p><strong>City:</strong> <?php echo htmlspecialchars($user['city']); ?></p>
                        <p><strong>Address:</strong> <?php echo htmlspecialchars($user['address']); ?></p>

                        <a href="settings.php" class="btn btn-default btn-sm">Update Information</a>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="panel panel-success">
                    <div class="panel-heading">
                        <h3 class="panel-title">Confirm Order</h3>
                    </div>
                    <div class="panel-body">
                        <?php if(isset($error)): ?>
                        <div class="alert alert-danger"><?php echo $error; ?></div>
                        <?php endif; ?>

                        <form method="POST" action="">
                            <div class="form-group">
                                <label>Payment Method:</label>
                                <select class="form-control" name="payment_method" required>
                                    <option value="">Select payment method</option>
                                    <option value="cod">Cash on Delivery</option>
                                    <option value="card">Credit/Debit Card</option>
                                    <option value="netbanking">Net Banking</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label>Delivery Date:</label>
                                <input type="date" class="form-control" name="delivery_date" required
                                    value="<?php echo date('Y-m-d', strtotime('+3 days')); ?>">
                            </div>

                            <hr>

                            <h4>Order Total: ₹<?php echo number_format($total, 2); ?></h4>

                            <button type="submit" class="btn btn-success btn-lg btn-block">
                                <span class="glyphicon glyphicon-check"></span> Confirm & Place Order
                            </button>
                        </form>

                        <a href="cart.php" class="btn btn-default btn-block" style="margin-top: 10px;">
                            <span class="glyphicon glyphicon-arrow-left"></span> Back to Cart
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include 'footer.php'; ?>
</body>

</html>