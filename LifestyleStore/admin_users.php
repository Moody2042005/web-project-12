<?php if (session_status() == PHP_SESSION_NONE) { session_start(); } ?>
<?php
require 'connection.php';

// Check admin
if(!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1) {
    header('Location: login.php');
    exit();
}

// Handle user actions
if(isset($_GET['action'])) {
    $action = $_GET['action'];
    $user_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
    
    if($action == 'delete' && $user_id > 0) {
        // Don't delete admin or current user
        if($user_id != $_SESSION['id']) {
            $delete_query = "DELETE FROM users WHERE id = '$user_id' AND is_admin = 0";
            mysqli_query($conn, $delete_query);
            
            // Log action
            $log_query = "INSERT INTO admin_logs (admin_id, action, details) 
VALUES ('{$_SESSION['id']}', 'Delete User', 'Deleted user ID: $user_id')";
            mysqli_query($conn, $log_query);
        }
    }
}

// Get all users
$users = mysqli_query($conn, "SELECT * FROM users ORDER BY created_at DESC");
?>

<!DOCTYPE html>
<html>

<head>
    <title>Manage Users | Admin Panel</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
</head>

<body>
    <?php 
    // Include admin header from dashboard
    $admin_header = file_get_contents('admin_dashboard.php');
    preg_match('/<nav class="navbar navbar-inverse navbar-fixed-top">.*?<\/nav>/s', $admin_header, $matches);
    echo $matches[0] ?? ''; 
    ?>

    <div class="container" style="margin-top: 80px; margin-bottom: 50px;">
        <div class="row">
            <div class="col-xs-12">
                <h2><i class="fas fa-users"></i> User Management</h2>

                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Username</th>
                                <th>Name</th>
                                <th>Email/Contact</th>
                                <th>City</th>
                                <th>Status</th>
                                <th>Joined</th>
                                <th>Last Login</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($user = mysqli_fetch_assoc($users)): ?>
                            <tr>
                                <td><?php echo $user['id']; ?></td>
                                <td>
                                    <?php echo htmlspecialchars($user['username']); ?>
                                    <?php if($user['is_admin'] == 1): ?>
                                    <span class="label label-primary">Admin</span>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo htmlspecialchars($user['name']); ?></td>
                                <td><?php echo htmlspecialchars($user['contact']); ?></td>
                                <td><?php echo htmlspecialchars($user['city']); ?></td>
                                <td>
                                    <?php if($user['is_admin'] == 1): ?>
                                    <span class="label label-success">Active</span>
                                    <?php else: ?>
                                    <span class="label label-info">User</span>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo date('d M Y', strtotime($user['created_at'])); ?></td>
                                <td>
                                    <?php echo $user['last_login'] ? date('d M Y H:i', strtotime($user['last_login'])) : 'Never'; ?>
                                </td>
                                <td>
                                    <a href="admin_view_user.php?id=<?php echo $user['id']; ?>"
                                        class="btn btn-xs btn-info">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <?php if($user['is_admin'] == 0 && $user['id'] != $_SESSION['id']): ?>
                                    <a href="admin_users.php?action=delete&id=<?php echo $user['id']; ?>"
                                        class="btn btn-xs btn-danger" onclick="return confirm('Delete this user?')">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>

                <div class="well">
                    <h4>Total Users: <?php echo mysqli_num_rows($users); ?></h4>
                    <p>Admins: <?php 
                        $admin_count = mysqli_query($conn, "SELECT COUNT(*) as count FROM users WHERE is_admin = 1");
                        echo mysqli_fetch_assoc($admin_count)['count'];
                    ?></p>
                </div>
            </div>
        </div>
    </div>
</body>

</html>