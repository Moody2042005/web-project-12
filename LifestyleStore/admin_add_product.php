<?php if (session_status() == PHP_SESSION_NONE) { session_start(); } ?>
<?php
require 'connection.php';

// Check if user is admin
if(!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1) {
    header('Location: login.php');
    exit();
}

$success = '';
$error = '';
$product_added = false;

// Handle form submission
if($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_product'])) {
    $name = mysqli_real_escape_string($conn, trim($_POST['name']));
    $price = floatval($_POST['price']);
    $category = mysqli_real_escape_string($conn, trim($_POST['category']));
    $description = mysqli_real_escape_string($conn, trim($_POST['description']));
    $stock = intval($_POST['stock']);
    $image_name = mysqli_real_escape_string($conn, trim($_POST['image_name']));
    
    // Validation
    if(empty($name) || empty($price) || empty($category) || empty($description)) {
        $error = "All fields are required except image name";
    } elseif($price <= 0) {
        $error = "Price must be greater than 0";
    } elseif($stock < 0) {
        $error = "Stock cannot be negative";
    } else {
        // Default image if not provided
        if(empty($image_name)) {
            $image_name = 'default.jpg';
        }
        
        // Check if image exists in img folder
        $image_path = "img/" . $image_name;
        if(!file_exists($image_path) && $image_name != 'default.jpg') {
            $error = "Warning: Image '$image_name' not found in img folder. Using default image.";
            $image_name = 'default.jpg';
        }
        
        // Insert product
        $query = "INSERT INTO items (name, price, image, description, category, stock) 
                  VALUES ('$name', '$price', '$image_name', '$description', '$category', '$stock')";
        
        if(mysqli_query($conn, $query)) {
            $product_id = mysqli_insert_id($conn);
            
            // Log admin action
            $log_query = "INSERT INTO admin_logs (admin_id, action, details) 
                          VALUES ('{$_SESSION['id']}', 'Add Product', 'Added new product: $name (ID: $product_id)')";
            mysqli_query($conn, $log_query);
            
            $success = "Product added successfully! Product ID: $product_id";
            $product_added = true;
            
            // Clear form if needed
            if(!isset($_POST['add_another'])) {
                $_POST = array();
            }
        } else {
            $error = "Error adding product: " . mysqli_error($conn);
        }
    }
}

// Get available images from img folder
$available_images = array();
if(is_dir('img')) {
    $files = scandir('img');
    foreach($files as $file) {
        if($file != '.' && $file != '..' && !is_dir('img/' . $file)) {
            $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
            if(in_array($ext, ['jpg', 'jpeg', 'png', 'gif'])) {
                $available_images[] = $file;
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Product | Admin Panel</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
    .admin-container {
        margin-top: 80px;
        margin-bottom: 50px;
    }

    .preview-box {
        border: 2px dashed #ddd;
        padding: 20px;
        text-align: center;
        min-height: 200px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 20px;
        border-radius: 8px;
    }

    .preview-image {
        max-width: 100%;
        max-height: 180px;
        object-fit: contain;
    }

    .image-selector {
        max-height: 200px;
        overflow-y: auto;
        border: 1px solid #ddd;
        border-radius: 5px;
        padding: 10px;
    }

    .image-option {
        padding: 5px;
        margin: 2px;
        border: 1px solid #eee;
        border-radius: 3px;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .image-option:hover {
        background-color: #f5f5f5;
    }

    .image-option.selected {
        background-color: #337ab7;
        color: white;
        border-color: #2e6da4;
    }

    .stats-box {
        background: #f8f9fa;
        border-radius: 8px;
        padding: 20px;
        margin-bottom: 20px;
    }
    </style>
</head>

<body>
    <!-- Admin Navigation -->
    <nav class="navbar navbar-inverse navbar-fixed-top">
        <div class="container-fluid">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#adminNavbar">
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
                    <li><a href="admin_dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                    <li><a href="admin_products.php"><i class="fas fa-box"></i> Products</a></li>
                    <li class="active"><a href="admin_add_product.php"><i class="fas fa-plus-circle"></i> Add
                            Product</a></li>
                    <li><a href="admin_orders.php"><i class="fas fa-shopping-cart"></i> Orders</a></li>
                    <li><a href="admin_reports.php"><i class="fas fa-chart-bar"></i> Reports</a></li>
                </ul>
                <ul class="nav navbar-nav navbar-right">
                    <li><a href="index.php"><i class="fas fa-store"></i> Store Front</a></li>
                    <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container admin-container">
        <div class="row">
            <div class="col-md-8">
                <div class="panel panel-primary">
                    <div class="panel-heading">
                        <h3 class="panel-title"><i class="fas fa-plus-circle"></i> Add New Product</h3>
                    </div>
                    <div class="panel-body">
                        <?php if($success): ?>
                        <div class="alert alert-success">
                            <i class="fas fa-check-circle"></i> <?php echo $success; ?>
                            <?php if($product_added): ?>
                            <br>
                            <a href="admin_add_product.php" class="btn btn-success btn-sm">
                                <i class="fas fa-plus"></i> Add Another Product
                            </a>
                            <a href="admin_products.php" class="btn btn-primary btn-sm">
                                <i class="fas fa-list"></i> View All Products
                            </a>
                            <?php endif; ?>
                        </div>
                        <?php endif; ?>

                        <?php if($error): ?>
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-triangle"></i> <?php echo $error; ?>
                        </div>
                        <?php endif; ?>

                        <form method="POST" action="" id="productForm">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Product Name *</label>
                                        <input type="text" class="form-control" name="name" required
                                            value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''; ?>"
                                            placeholder="Enter product name">
                                    </div>

                                    <div class="form-group">
                                        <label>Price (₹) *</label>
                                        <input type="number" class="form-control" name="price" required step="0.01"
                                            min="0" value="<?php echo isset($_POST['price']) ? $_POST['price'] : ''; ?>"
                                            placeholder="Enter price">
                                    </div>

                                    <div class="form-group">
                                        <label>Category *</label>
                                        <select class="form-control" name="category" required>
                                            <option value="">Select Category</option>
                                            <option value="Watch"
                                                <?php echo (isset($_POST['category']) && $_POST['category'] == 'Watch') ? 'selected' : ''; ?>>
                                                Watch</option>
                                            <option value="Camera"
                                                <?php echo (isset($_POST['category']) && $_POST['category'] == 'Camera') ? 'selected' : ''; ?>>
                                                Camera</option>
                                            <option value="Mobile"
                                                <?php echo (isset($_POST['category']) && $_POST['category'] == 'Mobile') ? 'selected' : ''; ?>>
                                                Mobile Phone</option>
                                            <option value="Clothing"
                                                <?php echo (isset($_POST['category']) && $_POST['category'] == 'Clothing') ? 'selected' : ''; ?>>
                                                Clothing</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Stock Quantity</label>
                                        <input type="number" class="form-control" name="stock" min="0" value="10"
                                            value="<?php echo isset($_POST['stock']) ? $_POST['stock'] : '10'; ?>">
                                    </div>

                                    <div class="form-group">
                                        <label>Image File Name</label>
                                        <input type="text" class="form-control" name="image_name" id="imageInput"
                                            value="<?php echo isset($_POST['image_name']) ? htmlspecialchars($_POST['image_name']) : ''; ?>"
                                            placeholder="e.g., watch.jpg">
                                        <small class="text-muted">Leave empty for default image. Must be in img/
                                            folder.</small>
                                    </div>

                                    <!-- Image Preview -->
                                    <div class="preview-box">
                                        <div id="imagePreview">
                                            <p class="text-muted">Image preview will appear here</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label>Description *</label>
                                <textarea class="form-control" name="description" rows="4" required
                                    placeholder="Enter product description"><?php echo isset($_POST['description']) ? htmlspecialchars($_POST['description']) : ''; ?></textarea>
                            </div>

                            <div class="form-group">
                                <button type="submit" name="add_product" class="btn btn-primary btn-lg">
                                    <i class="fas fa-save"></i> Add Product
                                </button>
                                <button type="submit" name="add_product" value="add_another"
                                    class="btn btn-success btn-lg">
                                    <i class="fas fa-plus-circle"></i> Add & Create Another
                                </button>
                                <a href="admin_products.php" class="btn btn-default btn-lg">
                                    <i class="fas fa-times"></i> Cancel
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <!-- Available Images -->
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title"><i class="fas fa-images"></i> Available Images</h3>
                    </div>
                    <div class="panel-body">
                        <div class="image-selector">
                            <?php if(count($available_images) > 0): ?>
                            <?php foreach($available_images as $image): ?>
                            <div class="image-option" data-image="<?php echo $image; ?>">
                                <img src="img/<?php echo $image; ?>" alt="<?php echo $image; ?>"
                                    style="width: 50px; height: 50px; object-fit: cover; margin-right: 10px;">
                                <?php echo $image; ?>
                            </div>
                            <?php endforeach; ?>
                            <?php else: ?>
                            <p class="text-muted">No images found in img folder</p>
                            <?php endif; ?>
                        </div>
                        <small class="text-muted">Click an image to select it</small>
                    </div>
                </div>

                <!-- Quick Stats -->
                <div class="stats-box">
                    <h4><i class="fas fa-chart-line"></i> Quick Stats</h4>
                    <?php
                    $total_products = mysqli_query($conn, "SELECT COUNT(*) as total FROM items");
                    $total = mysqli_fetch_assoc($total_products)['total'];
                    
                    $low_stock = mysqli_query($conn, "SELECT COUNT(*) as total FROM items WHERE stock < 5");
                    $low = mysqli_fetch_assoc($low_stock)['total'];
                    ?>
                    <p>Total Products: <strong><?php echo $total; ?></strong></p>
                    <p>Low Stock (< 5): <strong class="<?php echo $low > 0 ? 'text-danger' : 'text-success'; ?>">
                            <?php echo $low; ?></strong></p>
                    <p>Last Added:
                        <?php
                        $last_product = mysqli_query($conn, "SELECT name FROM items ORDER BY id DESC LIMIT 1");
                        if($row = mysqli_fetch_assoc($last_product)) {
                            echo htmlspecialchars($row['name']);
                        } else {
                            echo "None";
                        }
                        ?>
                    </p>
                </div>

                <!-- Quick Tips -->
                <div class="panel panel-info">
                    <div class="panel-heading">
                        <h3 class="panel-title"><i class="fas fa-lightbulb"></i> Tips</h3>
                    </div>
                    <div class="panel-body">
                        <ul style="padding-left: 20px;">
                            <li>Ensure image exists in <code>img/</code> folder</li>
                            <li>Use descriptive product names</li>
                            <li>Set appropriate stock levels</li>
                            <li>Add detailed descriptions</li>
                            <li>Use correct category for better organization</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
    $(document).ready(function() {
        // Image selection
        $('.image-option').click(function() {
            $('.image-option').removeClass('selected');
            $(this).addClass('selected');
            var imageName = $(this).data('image');
            $('#imageInput').val(imageName);
            updatePreview(imageName);
        });

        // Update preview when typing
        $('#imageInput').on('input', function() {
            var imageName = $(this).val();
            updatePreview(imageName);
        });

        // Update preview function
        function updatePreview(imageName) {
            var preview = $('#imagePreview');
            if (imageName) {
                preview.html('<img src="img/' + imageName +
                    '" class="preview-image" alt="Preview" onerror="this.style.display=\'none\'; preview.innerHTML=\'<p class=\'text-danger\'>Image not found</p>\';">'
                );
            } else {
                preview.html('<p class="text-muted">No image selected</p>');
            }
        }

        // Form validation
        $('#productForm').submit(function() {
            var price = $('input[name="price"]').val();
            if (price <= 0) {
                alert('Price must be greater than 0');
                return false;
            }
            return true;
        });

        // Auto-update preview on page load
        var initialImage = $('#imageInput').val();
        if (initialImage) {
            updatePreview(initialImage);
        }
    });
    </script>
</body>

</html>