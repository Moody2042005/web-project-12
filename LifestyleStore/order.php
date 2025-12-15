<?php
session_start();

// User must be logged in
if (!isset($_SESSION['id'])) {
    header("Location: login.php");
    exit;
}

require_once "connection.php";

$user_id = $_SESSION['id'];

// 1️⃣ Get cart items
$cart_query = "
    SELECT ui.item_id, i.price 
    FROM users_items ui
    JOIN items i ON ui.item_id = i.id
    WHERE ui.user_id = ? AND ui.status = 'Added to cart'
";

$stmt = mysqli_prepare($conn, $cart_query);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result) == 0) {
    header("Location: cart.php");
    exit;
}

// 2️⃣ Calculate total price
$total_price = 0;
$items = [];

while ($row = mysqli_fetch_assoc($result)) {
    $total_price += $row['price'];
    $items[] = $row['item_id'];
}

// 3️⃣ Insert into orders table
$order_query = "INSERT INTO orders (user_id, total_price, status) VALUES (?, ?, 'Confirmed')";
$stmt = mysqli_prepare($conn, $order_query);
mysqli_stmt_bind_param($stmt, "id", $user_id, $total_price);
mysqli_stmt_execute($stmt);

$order_id = mysqli_insert_id($conn);

// 4️⃣ Insert order items
foreach ($items as $item_id) {
    $item_query = "INSERT INTO order_items (order_id, item_id, price)
                   SELECT ?, id, price FROM items WHERE id = ?";
    $stmt = mysqli_prepare($conn, $item_query);
    mysqli_stmt_bind_param($stmt, "ii", $order_id, $item_id);
    mysqli_stmt_execute($stmt);
}

// 5️⃣ Update cart items to Ordered
$update_query = "UPDATE users_items SET status = 'Ordered' WHERE user_id = ?";
$stmt = mysqli_prepare($conn, $update_query);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);

?>

<!DOCTYPE html>
<html>

<head>
    <title>Order Placed</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
</head>

<body>

    <div class="container" style="margin-top:100px;">
        <div class="jumbotron text-center">
            <h2>🎉 Order Confirmed!</h2>
            <p>Thank you for shopping with <strong>Lifestyle Store</strong>.</p>
            <p><strong>Order ID:</strong> <?php echo $order_id; ?></p>
            <p><strong>Total Amount:</strong> ₹<?php echo number_format($total_price, 2); ?></p>

            <a href="products.php" class="btn btn-primary">Continue Shopping</a>
            <a href="logout.php" class="btn btn-default">Logout</a>
        </div>
    </div>

</body>

</html>