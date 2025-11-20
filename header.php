<?php
// header.php - Common header for all pages
if (!isset($pageTitle)) {
    $pageTitle = "KEC Campus Finder";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?> - KEC Campus Finder</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <header class="header">
        <div class="container">
            <div class="header-content">
                <div class="logo">
                    <a href="index.php" class="home-icon"><i class="fas fa-home"></i></a>
                    KEC Campus Finder
                </div>
                <div class="user-menu">
                    <?php if (isLoggedIn()): ?>
                    <span>Welcome, <?php echo $_SESSION['user']['name']; ?></span>
                    
                    <!-- NOTIFICATION BELL -->
                    <?php
                    if (file_exists('includes/notifications.php')) {
                        require_once 'includes/notifications.php';
                        $notificationCount = getUnreadNotificationCount($_SESSION['user']['id']);
                    } else {
                        $notificationCount = 0;
                    }
                    ?>
                    <a href="notifications.php" class="btn" style="position: relative;">
                        <i class="fas fa-bell"></i> Notifications
                        <?php if ($notificationCount > 0): ?>
                        <span style="position: absolute; top: -8px; right: -8px; background: red; color: white; border-radius: 50%; width: 20px; height: 20px; font-size: 0.8rem; display: flex; align-items: center; justify-content: center;">
                            <?php echo $notificationCount; ?>
                        </span>
                        <?php endif; ?>
                    </a>
                    <!-- END NOTIFICATION BELL -->
                    
                    <a href="dashboard.php" class="btn">Dashboard</a>
                    
                    <?php if (isAdmin()): ?>
                    <a href="admin-dashboard.php" class="btn btn-warning">Admin Panel</a>
                    <?php endif; ?>
                    <a href="logout.php" class="btn btn-danger">Logout</a>
                    <?php else: ?>
                    <a href="login.php" class="btn">Login</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </header>

    <main class="main-content">
        <div class="container">