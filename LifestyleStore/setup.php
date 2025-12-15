<?php if (session_status() == PHP_SESSION_NONE) { session_start(); } ?>
<?php
require 'connection.php';

if(!isset($_SESSION['id'])) {
    header('location: login.php');
    exit();
}

$user_id = $_SESSION['id'];
$success = '';
$error = '';

$query = "SELECT * FROM users WHERE id = '$user_id'";
$result = mysqli_query($conn, $query);
$user = mysqli_fetch_assoc($result);

if($_SERVER["REQUEST_METHOD"] == "POST") {
    if(isset($_POST['update_profile'])) {
        $name = mysqli_real_escape_string($conn, trim($_POST['name']));
        $contact = mysqli_real_escape_string($conn, trim($_POST['contact']));
        $city = mysqli_real_escape_string($conn, trim($_POST['city']));
        $address = mysqli_real_escape_string($conn, trim($_POST['address']));
        
        if(empty($name) || empty($contact) || empty($city) || empty($address)) {
            $error = "All fields are required";
        } elseif(!preg_match('/^[0-9]{10}$/', $contact)) {
            $error = "Contact number must be 10 digits";
        } else {
            $update_query = "UPDATE users SET name = '$name', contact = '$contact', 
                             city = '$city', address = '$address' WHERE id = '$user_id'";
            
            if(mysqli_query($conn, $update_query)) {
                $_SESSION['name'] = $name;
                $success = "Profile updated successfully!";
                $result = mysqli_query($conn, $query);
                $user = mysqli_fetch_assoc($result);
            } else {
                $error = "Error updating profile.";
            }
        }
    }
    
    if(isset($_POST['change_password'])) {
        $current_password = $_POST['current_password'];
        $new_password = $_POST['new_password'];
        $confirm_password = $_POST['confirm_password'];
        
        $hashed_current = md5(md5($current_password));
        if($hashed_current != $user['password']) {
            $error = "Current password is incorrect";
        } elseif(strlen($new_password) < 6) {
            $error = "New password must be at least 6 characters";
        } elseif($new_password != $confirm_password) {
            $error = "New passwords do not match";
        } else {
            $hashed_new = md5(md5($new_password));
            $pass_query = "UPDATE users SET password = '$hashed_new' WHERE id = '$user_id'";
            
            if(mysqli_query($conn, $pass_query)) {
                $success = "Password changed successfully!";
            } else {
                $error = "Error changing password.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Settings | Lifestyle Store</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>

<body>
    <?php include 'header.php'; ?>

    <div class="container settings-container">
        <div class="row">
            <div class="col-xs-8 col-xs-offset-2">
                <div class="panel panel-primary">
                    <div class="panel-heading">
                        <h3 class="panel-title">Settings</h3>
                    </div>
                    <div class="panel-body">
                        <?php if($success): ?>
                        <div class="alert alert-success"><?php echo $success; ?></div>
                        <?php endif; ?>

                        <?php if($error): ?>
                        <div class="alert alert-danger"><?php echo $error; ?></div>
                        <?php endif; ?>

                        <form method="POST" action="">
                            <h4>Update Profile Information</h4>

                            <div class="form-group">
                                <label>Name:</label>
                                <input type="text" class="form-control" name="name" required
                                    value="<?php echo htmlspecialchars($user['name']); ?>">
                            </div>

                            <div class="form-group">
                                <label>Username:</label>
                                <input type="text" class="form-control"
                                    value="<?php echo htmlspecialchars($user['username']); ?>" disabled>
                                <small class="text-muted">Username cannot be changed</small>
                            </div>

                            <div class="form-group">
                                <label>Contact:</label>
                                <input type="text" class="form-control" name="contact" required
                                    value="<?php echo htmlspecialchars($user['contact']); ?>" pattern="[0-9]{10}"
                                    title="10 digit number">
                            </div>

                            <div class="form-group">
                                <label>City:</label>
                                <input type="text" class="form-control" name="city" required
                                    value="<?php echo htmlspecialchars($user['city']); ?>">
                            </div>

                            <div class="form-group">
                                <label>Address:</label>
                                <textarea class="form-control" name="address" rows="3"
                                    required><?php echo htmlspecialchars($user['address']); ?></textarea>
                            </div>

                            <button type="submit" name="update_profile" value="1" class="btn btn-primary">Update
                                Profile</button>
                        </form>

                        <hr>

                        <form method="POST" action="">
                            <h4>Change Password</h4>

                            <div class="form-group">
                                <label>Current Password:</label>
                                <input type="password" class="form-control" name="current_password" required>
                            </div>

                            <div class="form-group">
                                <label>New Password:</label>
                                <input type="password" class="form-control" name="new_password" required>
                                <small class="text-muted">Minimum 6 characters</small>
                            </div>

                            <div class="form-group">
                                <label>Confirm New Password:</label>
                                <input type="password" class="form-control" name="confirm_password" required>
                            </div>

                            <button type="submit" name="change_password" value="1" class="btn btn-warning">Change
                                Password</button>
                        </form>
                    </div>
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