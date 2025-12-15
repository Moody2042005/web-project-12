<?php if (session_status() == PHP_SESSION_NONE) { session_start(); } ?>
<?php
require 'connection.php';

if(!isset($_SESSION['id'])) {
    header('location: login.php');
    exit();
}

$user_id = $_SESSION['id'];
$cart_query = "SELECT i.id, i.name, i.price, i.image, ui.quantity 
               FROM users_items ui 
               JOIN items i ON ui.item_id = i.id 
               WHERE ui.user_id = '$user_id' AND ui.status = 'Added to cart'";
$cart_result = mysqli_query($conn, $cart_query);
$total = 0;
?>

<!DOCTYPE html>
<html>

<head>
    <title>My Cart | Lifestyle Store</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/style.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
</head>

<body>
    <?php include 'header.php'; ?>

    <div class="container cart-container">
        <div class="row">
            <div class="col-xs-12">
                <h2 class="text-center">My Shopping Cart</h2>

                <?php if(isset($_SESSION['message'])): ?>
                <div class="alert alert-info alert-dismissible">
                    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                    <?php 
                        echo $_SESSION['message']; 
                        unset($_SESSION['message']);
                        ?>
                </div>
                <?php endif; ?>

                <?php if(mysqli_num_rows($cart_result) == 0): ?>
                <div class="empty-cart text-center">
                    <h3>Your cart is empty!</h3>
                    <p>Add some products to your cart first.</p>
                    <a href="products.php" class="btn btn-primary btn-lg">Continue Shopping</a>
                </div>
                <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-bordered table-hover cart-table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Item</th>
                                <th>Price</th>
                                <th>Quantity</th>
                                <th>Subtotal</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                                $counter = 1;
                                while($row = mysqli_fetch_assoc($cart_result)) {
                                    $item_id = $row['id'];
                                    $item_name = $row['name'];
                                    $item_price = $row['price'];
                                    $item_image = $row['image'];
                                    $quantity = $row['quantity'];
                                    $subtotal = $item_price * $quantity;
                                    $total += $subtotal;
                                ?>
                            <tr>
                                <td><?php echo $counter++; ?></td>
                                <td>
                                    <div class="cart-item-info">
                                        <img src="images/<?php echo htmlspecialchars($item_image); ?>"
                                            alt="<?php echo htmlspecialchars($item_name); ?>"
                                            class="cart-item-image img-thumbnail">
                                        <div class="cart-item-details">
                                            <h5><?php echo htmlspecialchars($item_name); ?></h5>
                                        </div>
                                    </div>
                                </td>
                                <td>₹<?php echo number_format($item_price, 2); ?></td>
                                <td>
                                    <form method="POST" action="update_quantity.php" class="quantity-form">
                                        <input type="hidden" name="item_id" value="<?php echo $item_id; ?>">
                                        <div class="input-group">
                                            <input type="number" name="quantity" value="<?php echo $quantity; ?>"
                                                min="1" max="10" class="form-control quantity-input">
                                            <div class="input-group-btn">
                                                <button type="submit" class="btn btn-default btn-sm">Update</button>
                                            </div>
                                        </div>
                                    </form>
                                </td>
                                <td>₹<?php echo number_format($subtotal, 2); ?></td>
                                <td>
                                    <form method="POST" action="cart_remove.php" class="remove-form">
                                        <input type="hidden" name="item_id" value="<?php echo $item_id; ?>">
                                        <button type="submit" class="btn btn-danger btn-sm">
                                            <span class="glyphicon glyphicon-trash"></span> Remove
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            <?php } ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="4" class="text-right"><strong>Total Amount:</strong></td>
                                <td colspan="2">
                                    <strong>₹<?php echo number_format($total, 2); ?></strong>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <div class="cart-actions row">
                    <div class="col-xs-6">
                        <a href="products.php" class="btn btn-primary">
                            <span class="glyphicon glyphicon-arrow-left"></span> Continue Shopping
                        </a>
                    </div>
                    <div class="col-xs-6 text-right">
                        <a href="cart_clear.php" class="btn btn-warning"
                            onclick="return confirm('Clear entire cart?');">
                            <span class="glyphicon glyphicon-remove"></span> Clear Cart
                        </a>
                        <?php if($total > 0): ?>
                        <a href="checkout.php" class="btn btn-success">
                            <span class="glyphicon glyphicon-check"></span> Proceed to Checkout
                        </a>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <?php include 'footer.php'; ?>

    <script>
    $(document).ready(function() {
        $('.cart-item-image').on('error', function() {
            $(this).attr('src', 'images/cover.jpg');
        });

        $('.remove-form').on('submit', function(e) {
            return confirm('Are you sure you want to remove this item from your cart?');
        });
    });
    </script>
</body>

</html>

<?php
mysqli_close($conn);
?>