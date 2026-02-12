<?php
session_start();
if (isset($_SESSION['user_id'])) {
    $user_name = $_SESSION['user_name'] ?? 'User';
    session_destroy();
    
    // Redirect with success message
    header("Location: index.php?logout=success&name=" . urlencode($user_name));
    exit();
} else {
    header("Location: index.php");
    exit();
}
?>
