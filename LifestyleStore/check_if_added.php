<?php
// Prevent function redeclaration
if (!function_exists('check_if_added_to_cart')) {

    /**
     * Checks if a specific item is already in the user's cart (status='Added to cart').
     * @param int $item_id The ID of the item to check.
     * @param mysqli $conn The database connection object.
     * @return bool True if the item is in the cart, false otherwise.
     */
    function check_if_added_to_cart($item_id, $conn) {

        // Start session if not started
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // If user not logged in (though products.php should handle this), fail safely.
        if (!isset($_SESSION['id'])) {
            return false;
        }

        // Connection is passed via parameter, so we don't need to include connection.php here.

        $user_id = $_SESSION['id'];

        // Prepared statement query
        $query = "SELECT id 
FROM users_items 
WHERE user_id = ? 
AND item_id = ? 
AND status = 'Added to cart'";

        // Prepare the statement using the passed $conn variable
        $stmt = mysqli_prepare($conn, $query);
        
        // Handle preparation error
        if ($stmt === false) {
             // In a real application, you might log this error instead of showing it
             // die("Error preparing statement: " . mysqli_error($conn)); 
return false;
        }
        
        // Bind parameters (both user_id and item_id are integers: "ii")
        mysqli_stmt_bind_param($stmt, "ii", $user_id, $item_id);
        
        // Execute and store result
        mysqli_stmt_execute($stmt);
        mysqli_stmt_store_result($stmt);

        // Return true if at least one row is found, false otherwise
        return mysqli_stmt_num_rows($stmt) > 0;
    }
}
?>