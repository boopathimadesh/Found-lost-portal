<?php
require_once 'includes/auth.php';
require_once 'includes/items.php';

if (!isLoggedIn() || !isAdmin()) {
    header('Location: login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $itemId = $_POST['item_id'];
    $action = $_POST['action'];
    
    if ($action === 'delete') {
        if (deleteItem($itemId)) {
            $_SESSION['success'] = "Item deleted successfully!";
        } else {
            $_SESSION['error'] = "Error deleting item. Please try again.";
        }
    } elseif ($action === 'mark_returned') {
    if (markItemReturned($itemId)) {
        $_SESSION['success'] = "Item marked as returned successfully!";
        
        // Send notification
        require_once 'includes/notifications.php';
        sendReturnedNotification($itemId);
    } else {
        $_SESSION['error'] = "Error marking item as returned. Please try again.";
    }
}
    header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? 'admin-dashboard.php'));
    exit;
}
?>