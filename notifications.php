<?php
require_once 'includes/auth.php';
require_once 'includes/notifications.php';

if (!isLoggedIn()) {
    header('Location: login.php');
    exit;
}

$userId = $_SESSION['user']['id'];
$notifications = getUserNotifications($userId);

// Mark all as read if requested
if (isset($_GET['mark_all_read'])) {
    markAllNotificationsAsRead($userId);
    header('Location: notifications.php');
    exit;
}

// Mark single as read if requested
if (isset($_GET['mark_read'])) {
    markNotificationAsRead($_GET['mark_read']);
    header('Location: notifications.php');
    exit;
}

$pageTitle = "Notifications";
?>
<?php include 'includes/header.php'; ?>

<h1>My Notifications</h1>

<div class="filters">
    <a href="?mark_all_read=1" class="btn btn-success">Mark All as Read</a>
    <a href="dashboard.php" class="btn">Back to Dashboard</a>
</div>

<div class="notifications-list">
    <?php if (empty($notifications)): ?>
        <div class="no-items">
            <i class="fas fa-bell-slash" style="font-size: 3rem; color: #ccc; margin-bottom: 15px;"></i>
            <p>No notifications yet.</p>
        </div>
    <?php else: ?>
        <?php foreach ($notifications as $notification): ?>
            <div class="item-card <?php echo $notification['is_read'] ? 'status-returned' : 'status-active'; ?>">
                <div class="item-details">
                    <div class="item-badges">
                        <span class="status-badge <?php echo $notification['is_read'] ? 'status-returned' : 'status-active'; ?>">
                            <?php echo $notification['is_read'] ? 'READ' : 'NEW'; ?>
                        </span>
                        <span class="item-meta">
                            <i class="fas fa-clock"></i> 
                            <?php echo date('M j, Y g:i A', strtotime($notification['created_at'])); ?>
                        </span>
                    </div>
                    
                    <p class="item-description"><?php echo htmlspecialchars($notification['message']); ?></p>
                    
                    <?php if (!$notification['is_read']): ?>
                        <div class="item-actions">
                            <a href="?mark_read=<?php echo $notification['id']; ?>" class="btn btn-success btn-small">
                                Mark as Read
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>