<?php if (session_status() == PHP_SESSION_NONE) { session_start(); } ?>
<?php
require 'connection.php';

if(isset($_SESSION['username'])) {
    header('location: products.php');
    exit();
}

$error = '';
$success = '';

if($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = mysqli_real_escape_string($conn, trim($_POST['name']));
    $username = mysqli_real_escape_string($conn, trim($_POST['username']));
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $contact = mysqli_real_escape_string($conn, trim($_POST['contact']));
    $city = mysqli_real_escape_string($conn, trim($_POST['city']));
    $address = mysqli_real_escape_string($conn, trim($_POST['address']));
    
    if(empty($name) || empty($username) || empty($password) || empty($contact) || empty($city) || empty($address)) {
        $error = "All fields are required";
    }
    elseif(strlen($username) < 4) {
        $error = "Username must be at least 4 characters";
    }
    elseif(strlen($password) < 6) {
        $error = "Password must be at least 6 characters";
    }
    elseif($password !== $confirm_password) {
        $error = "Passwords do not match";
    }
    elseif(!preg_match('/^[0-9]{10}$/', $contact)) {
        $error = "Contact number must be 10 digits";
    }
    else {
        $check_query = "SELECT id FROM users WHERE username = '$username'";
        $check_result = mysqli_query($conn, $check_query);
        
        if(mysqli_num_rows($check_result) > 0) {
            $error = "Username already exists";
        } else {
            $hashed_password = md5(md5($password));
            
            $insert_query = "INSERT INTO users (name, username, password, contact, city, address) 
                            VALUES ('$name', '$username', '$hashed_password', '$contact', '$city', '$address')";
            
            if(mysqli_query($conn, $insert_query)) {
                $success = "Registration successful! You can now login.";
                $_POST = array();
            } else {
                $error = "Registration failed. Please try again.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Sign Up | Lifestyle Store</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/style.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
</head>

<body>
    <?php include 'header.php'; ?>

    <div class="container signup-container">
        <div class="row">
            <div class="col-xs-6 col-xs-offset-3">
                <div class="panel panel-primary">
                    <div class="panel-heading">
                        <h3 class="panel-title">SIGN UP</h3>
                    </div>
                    <div class="panel-body">
                        <?php if($error): ?>
                        <div class="alert alert-danger"><?php echo $error; ?></div>
                        <?php endif; ?>

                        <?php if($success): ?>
                        <div class="alert alert-success"><?php echo $success; ?></div>
                        <?php endif; ?>

                        <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                            <div class="form-group">
                                <label>Name:</label>
                                <input type="text" class="form-control" name="name" required
                                    value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''; ?>">
                            </div>

                            <div class="form-group">
                                <label>Username:</label>
                                <input type="text" class="form-control" name="username" required
                                    value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>">
                                <small class="text-muted">Minimum 4 characters</small>
                            </div>

                            <div class="form-group">
                                <label>Password:</label>
                                <input type="password" class="form-control" name="password" required>
                                <small class="text-muted">Minimum 6 characters</small>
                            </div>

                            <div class="form-group">
                                <label>Confirm Password:</label>
                                <input type="password" class="form-control" name="confirm_password" required>
                            </div>

                            <div class="form-group">
                                <label>Contact:</label>
                                <input type="text" class="form-control" name="contact" required
                                    value="<?php echo isset($_POST['contact']) ? htmlspecialchars($_POST['contact']) : ''; ?>"
                                    pattern="[0-9]{10}" title="10 digit number">
                            </div>

                            <div class="form-group">
                                <label>City:</label>
                                <input type="text" class="form-control" name="city" required
                                    value="<?php echo isset($_POST['city']) ? htmlspecialchars($_POST['city']) : ''; ?>">
                            </div>

                            <div class="form-group">
                                <label>Address:</label>
                                <textarea class="form-control" name="address" rows="3"
                                    required><?php echo isset($_POST['address']) ? htmlspecialchars($_POST['address']) : ''; ?></textarea>
                            </div>

                            <button type="submit" class="btn btn-primary btn-block">Sign Up</button>
                        </form>

                        <div class="text-center" style="margin-top: 15px;">
                            <p>Already have an account? <a href="login.php">Login here</a></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include 'footer.php'; ?>
</body>

</html>