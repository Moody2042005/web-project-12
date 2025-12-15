<?php if (session_status() == PHP_SESSION_NONE) { session_start(); } ?>
<?php
require 'connection.php';

// Check admin
if(!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1) {
    header('Location: login.php');
    exit();
}

// Handle product actions
if(isset($_POST['add_product'])) {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $price = floatval($_POST['price']);
    $category = mysqli_real_escape_string($conn, $_POST['category']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $stock = intval($_POST['stock']);
    $image = 'default.jpg'; // Default image
    
    $query = "INSERT INTO items (name, price, image, description, category, stock) 
VALUES ('$name', '$price', '$image', '$description', '$category', '$stock')";
    
    if(mysqli_query($conn, $query)) {
        // Log action
        $log_query = "INSERT INTO admin_logs (admin_id, action, details) 
VALUES ('{$_SESSION['id']}', 'Add Product', 'Added product: $name')";
        mysqli_query($conn, $log_query);
        
        $success = "Product added successfully!";
    } else {
        $error = "Error adding product.";
    }
}

// Get all products
$products = mysqli_query($conn, "SELECT * FROM items ORDER BY created_at DESC");
?>

<!DOCTYPE html>
<html>

<head>
    <title>Manage Products | Admin Panel</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
</head>

<body>
    <?php 
    $admin_header = file_get_contents('admin_dashboard.php');
    preg_match('/<nav class="navbar navbar-inverse navbar-fixed-top">.*?<\/nav>/s', $admin_header, $matches);
    echo $matches[0] ?? ''; 
    ?>

    <div class="container" style="margin-top: 80px; margin-bottom: 50px;">
        <div class="row">
            <div class="col-md-4">
                <div class="panel panel-primary">
                    <div class="panel-heading">
                        <h3 class="panel-title"><i class="fas fa-plus-circle"></i> Add New Product</h3>
                    </div>
                    <div class="panel-body">
                        <?php if(isset($success)): ?>
                        <div class="alert alert-success"><?php echo $success; ?></div>
                        <?php endif; ?>
                        <?php if(isset($error)): ?>
                        <div class="alert alert-danger"><?php echo $error; ?></div>
                        <?php endif; ?>

                        <form method="POST" action="">
                            <div class="form-group">
                                <label>Product Name:</label>
                                <input type="text" class="form-control" name="name" required>
                            </div>

                            <div class="form-group">
                                <label>Price (₹):</label>
                                <input type="number" class="form-control" name="price" step="0.01" required>
                            </div>

                            <div class="form-group">
                                <label>Category:</label>
                                <select class="form-control" name="category" required>
                                    <option value="">Select Category</option>
                                    <option value="Mobile">Mobile Phone</option>
                                    <option value="Camera">Camera</option>
                                    <option value="Watch">Watch</option>
                                    <option value="Clothing">Clothing</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label>Stock Quantity:</label>
                                <input type="number" class="form-control" name="stock" value="10" required>
                            </div>

                            <div class="form-group">
                                <label>Description:</label>
                                <textarea class="form-control" name="description" rows="3" required></textarea>
                            </div>

                            <button type="submit" name="add_product" class="btn btn-primary btn-block">
                                <i class="fas fa-save"></i> Add Product
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-md-8">
                <h2><i class="fas fa-boxes"></i> All Products</h2>

                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Image</th>
                                <th>Name</th>
                                <th>Price</th>
                                <th>Category</th>
                                <th>Stock</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($product = mysqli_fetch_assoc($products)): ?>
                            <tr>
                                <td><?php echo $product['id']; ?></td>
                                <td>
                                    <img src="images/<?php echo $product['image']; ?>"
                                        alt="<?php echo htmlspecialchars($product['name']); ?>"
                                        style="width: 50px; height: 50px; object-fit: cover;">
                                </td>
                                <td><?php echo htmlspecialchars($product['name']); ?></td>
                                <td>₹<?php echo number_format($product['price'], 2); ?></td>
                                <td>
                                    <span class="label label-info"><?php echo $product['category']; ?></span>
                                </td>
                                <td>
                                    <?php if($product['stock'] > 0): ?>
                                    <span class="label label-success"><?php echo $product['stock']; ?> in stock</span>
                                    <?php else: ?>
                                    <span class="label label-danger">Out of stock</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <a href="admin_edit_product.php?id=<?php echo $product['id']; ?>"
                                        class="btn btn-xs btn-warning">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="admin_delete_product.php?id=<?php echo $product['id']; ?>"
                                        class="btn btn-xs btn-danger" onclick="return confirm('Delete this product?')">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</body>

</html>