<?php if (session_status() == PHP_SESSION_NONE) { session_start(); } ?>
<?php
require 'connection.php';

// If already logged in, redirect based on user type
if(isset($_SESSION['username'])) {
    if(isset($_SESSION['is_admin']) && $_SESSION['is_admin'] == 1) {
        header('Location: admin_dashboard.php');
    } else {
        header('Location: products.php');
    }
    exit();
}

$error = '';

if($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = mysqli_real_escape_string($conn, trim($_POST['username']));
    $password = $_POST['password'];
    
    if(empty($username) || empty($password)) {
        $error = "Please enter username and password";
    } else {
        $hashed_password = md5(md5($password));
        
        $query = "SELECT id, name, username, is_admin FROM users WHERE username = '$username' AND password = '$hashed_password'";
        $result = mysqli_query($conn, $query);
        
        if(mysqli_num_rows($result) == 1) {
            $user = mysqli_fetch_assoc($result);
            
            // Set session variables
            $_SESSION['id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['name'] = $user['name'];
            $_SESSION['is_admin'] = $user['is_admin'];
            
            // Update last login time
            $update_query = "UPDATE users SET last_login = NOW() WHERE id = '{$user['id']}'";
            mysqli_query($conn, $update_query);
            
            // Log admin login if applicable
            if($user['is_admin'] == 1) {
                $ip_address = $_SERVER['REMOTE_ADDR'];
                $log_query = "INSERT INTO admin_logs (admin_id, action, details, ip_address) 
                              VALUES ('{$user['id']}', 'Admin Login', 'Administrator logged into system', '$ip_address')";
                mysqli_query($conn, $log_query);
            }
            
            // Redirect based on user type
            if($user['is_admin'] == 1) {
                header('Location: admin_dashboard.php');
            } else {
                // Check if there's a redirect URL stored
                if(isset($_SESSION['redirect_url'])) {
                    $redirect_url = $_SESSION['redirect_url'];
                    unset($_SESSION['redirect_url']);
                    header('Location: ' . $redirect_url);
                } else {
                    header('Location: products.php');
                }
            }
            exit();
        } else {
            $error = "Invalid username or password";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Lifestyle Store</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="css/style.css">
    <!-- jQuery -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <!-- Bootstrap JS -->
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>

    <style>
    .login-container {
        margin-top: 80px;
        margin-bottom: 50px;
    }

    .admin-login-info {
        background-color: #f8f9fa;
        border-left: 4px solid #ffc107;
        padding: 15px;
        margin-bottom: 20px;
        border-radius: 4px;
    }

    .admin-login-info h4 {
        color: #856404;
        margin-top: 0;
    }

    .login-panel {
        box-shadow: 0 2px 15px rgba(0, 0, 0, 0.1);
        border-radius: 8px;
        border: none;
    }

    .login-panel .panel-heading {
        border-radius: 8px 8px 0 0;
        padding: 20px;
    }

    .login-panel .panel-title {
        font-size: 24px;
        font-weight: 600;
    }

    .form-group label {
        font-weight: 600;
        color: #555;
    }

    .form-control {
        padding: 12px;
        border-radius: 4px;
        border: 1px solid #ddd;
    }

    .form-control:focus {
        border-color: #337ab7;
        box-shadow: 0 0 0 0.2rem rgba(51, 122, 183, 0.25);
    }

    .btn-login {
        background: linear-gradient(135deg, #337ab7 0%, #286090 100%);
        border: none;
        padding: 12px;
        font-size: 16px;
        font-weight: 600;
        border-radius: 4px;
        transition: all 0.3s ease;
    }

    .btn-login:hover {
        background: linear-gradient(135deg, #286090 0%, #204d74 100%);
        transform: translateY(-2px);
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
    }

    .login-footer {
        text-align: center;
        margin-top: 20px;
        padding-top: 20px;
        border-top: 1px solid #eee;
    }

    .login-footer a {
        color: #337ab7;
        text-decoration: none;
        font-weight: 600;
    }

    .login-footer a:hover {
        color: #23527c;
        text-decoration: underline;
    }

    .alert {
        border-radius: 4px;
        padding: 12px 15px;
    }

    @media (max-width: 768px) {
        .login-container {
            margin-top: 60px;
        }

        .col-xs-offset-3 {
            margin-left: 0;
        }

        .col-xs-6 {
            width: 100%;
        }
    }
    </style>
</head>

<body>
    <!-- Header -->
    <?php include 'header.php'; ?>

    <div class="container login-container">
        <div class="row">
            <div class="col-xs-12 col-sm-8 col-sm-offset-2 col-md-6 col-md-offset-3">
                <!-- Admin Login Information -->
                <div class="admin-login-info">
                    <h4><i class="fas fa-user-shield"></i> Administrator Access</h4>
                    <p>For administrative access, use the following credentials:</p>
                    <div class="row">
                        <div class="col-xs-6">
                            <strong>Username:</strong> <code>admin</code>
                        </div>
                        <div class="col-xs-6">
                            <strong>Password:</strong> <code>2042005</code>
                        </div>
                    </div>
                    <p class="text-muted" style="margin-top: 10px; font-size: 12px;">
                        <i class="fas fa-info-circle"></i> Regular users: Use your registered username and password
                    </p>
                </div>

                <!-- Login Panel -->
                <div class="panel panel-primary login-panel">
                    <div class="panel-heading">
                        <h3 class="panel-title text-center">
                            <i class="fas fa-sign-in-alt"></i> LOGIN TO YOUR ACCOUNT
                        </h3>
                    </div>
                    <div class="panel-body">
                        <!-- Error Message -->
                        <?php if($error): ?>
                        <div class="alert alert-danger alert-dismissible fade in">
                            <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                            <i class="fas fa-exclamation-triangle"></i> <?php echo $error; ?>
                        </div>
                        <?php endif; ?>

                        <!-- Login Form -->
                        <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                            <div class="form-group">
                                <label for="username">
                                    <i class="fas fa-user"></i> Username:
                                </label>
                                <input type="text" class="form-control" id="username" name="username" required
                                    placeholder="Enter your username"
                                    value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>">
                            </div>

                            <div class="form-group">
                                <label for="password">
                                    <i class="fas fa-lock"></i> Password:
                                </label>
                                <input type="password" class="form-control" id="password" name="password" required
                                    placeholder="Enter your password">
                            </div>

                            <div class="form-group">
                                <button type="submit" class="btn btn-primary btn-block btn-login">
                                    <i class="fas fa-sign-in-alt"></i> LOGIN
                                </button>
                            </div>
                        </form>

                        <!-- Login Footer -->
                        <div class="login-footer">
                            <p>Don't have an account?
                                <a href="signup.php">
                                    <i class="fas fa-user-plus"></i> Create New Account
                                </a>
                            </p>
                            <p>
                                <a href="index.php">
                                    <i class="fas fa-home"></i> Back to Homepage
                                </a>
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Demo Accounts Info -->
                <div class="well text-center" style="margin-top: 20px; background-color: #f8f9fa;">
                    <h5><i class="fas fa-users"></i> Demo Accounts</h5>
                    <div class="row">
                        <div class="col-xs-6">
                            <p><strong>Regular User:</strong><br>
                                <small>Username: <code>john_doe</code><br>
                                    Password: <code>password123</code></small>
                            </p>
                        </div>
                        <div class="col-xs-6">
                            <p><strong>Admin User:</strong><br>
                                <small>Username: <code>admin</code><br>
                                    Password: <code>2042005</code></small>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <?php include 'footer.php'; ?>

    <script>
    $(document).ready(function() {
        // Auto-focus on username field
        $('#username').focus();

        // Toggle password visibility
        $('#password').after(
            '<span class="input-group-btn" style="position: absolute; right: 0; top: 0; height: 100%;">' +
            '<button class="btn btn-default" type="button" id="togglePassword" style="height: 100%; border: none; background: transparent;">' +
            '<i class="fas fa-eye"></i>' +
            '</button>' +
            '</span>');

        $('#password').parent().css('position', 'relative');

        $('#togglePassword').click(function() {
            var passwordField = $('#password');
            var passwordFieldType = passwordField.attr('type');
            var icon = $(this).find('i');

            if (passwordFieldType === 'password') {
                passwordField.attr('type', 'text');
                icon.removeClass('fa-eye').addClass('fa-eye-slash');
            } else {
                passwordField.attr('type', 'password');
                icon.removeClass('fa-eye-slash').addClass('fa-eye');
            }
        });

        // Form validation
        $('form').submit(function() {
            var username = $('#username').val().trim();
            var password = $('#password').val().trim();

            if (username === '' || password === '') {
                alert('Please fill in all fields');
                return false;
            }

            return true;
        });

        // Auto-hide alerts after 5 seconds
        setTimeout(function() {
            $('.alert').fadeOut('slow');
        }, 5000);
    });
    </script>
</body>

</html>

<?php
// Close database connection
if(isset($conn)) {
    mysqli_close($conn);
}
?>