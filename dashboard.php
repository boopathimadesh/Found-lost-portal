<?php
require_once 'includes/auth.php';
require_once 'includes/items.php';

if (!isLoggedIn()) {
    header('Location: login.php');
    exit;
}

// Check for expired items
checkExpiredItems();

// Get filter parameters
$type = isset($_GET['type']) ? $_GET['type'] : 'all';
$statusFilter = isset($_GET['status']) ? $_GET['status'] : 'all';

// Get items based on filters
if ($statusFilter === 'all') {
    $items = getItems($type, 'active');
    $matchedItems = getItems($type, 'matched');
    $returnedItems = getItems($type, 'returned');
    $items = array_merge($items, $matchedItems, $returnedItems);
} else {
    $items = getItems($type, $statusFilter);
}

$pageTitle = "Dashboard";
?>
<?php include 'includes/header.php'; ?>

<h1>Lost & Found Items</h1>

<div class="filters">
    <a href="?type=all&status=<?php echo $statusFilter; ?>" class="filter-btn <?php echo $type === 'all' ? 'active' : ''; ?>">All Items</a>
    <a href="?type=lost&status=<?php echo $statusFilter; ?>" class="filter-btn <?php echo $type === 'lost' ? 'active' : ''; ?>">Lost Items</a>
    <a href="?type=found&status=<?php echo $statusFilter; ?>" class="filter-btn <?php echo $type === 'found' ? 'active' : ''; ?>">Found Items</a>
    
    <select id="status-filter" class="filter-btn" onchange="window.location.href='?type=<?php echo $type; ?>&status='+this.value">
        <option value="all" <?php echo $statusFilter === 'all' ? 'selected' : ''; ?>>All Status</option>
        <option value="active" <?php echo $statusFilter === 'active' ? 'selected' : ''; ?>>Active</option>
        <option value="matched" <?php echo $statusFilter === 'matched' ? 'selected' : ''; ?>>Matched</option>
        <option value="returned" <?php echo $statusFilter === 'returned' ? 'selected' : ''; ?>>Returned</option>
    </select>
    
    <a href="report-lost.php" class="btn">Report Lost Item</a>
    <a href="report-found.php" class="btn btn-success">Report Found Item</a>
</div>

<!-- ADD NOTIFICATION ALERT HERE -->
<?php
// Show recent unread notifications
if (file_exists('includes/notifications.php')) {
    require_once 'includes/notifications.php';
    $recentNotifications = getUserNotifications($_SESSION['user']['id'], true);
    if (!empty($recentNotifications) && count($recentNotifications) > 0): 
?>
    <div class="alert alert-success" style="background: #e3f2fd; border-color: #90caf9; color: #1976d2; margin-bottom: 20px;">
        <h4><i class="fas fa-bell"></i> You have <?php echo count($recentNotifications); ?> unread notification(s)</h4>
        <p><a href="notifications.php" class="btn btn-small">View Notifications</a></p>
    </div>
<?php 
    endif;
}
?>
<!-- END NOTIFICATION ALERT -->

<div class="items-grid" id="items-container">
    <?php if (empty($items)): ?>
    <div class="no-items">
        <i class="fas fa-search" style="font-size: 3rem; color: #ccc; margin-bottom: 15px;"></i>
        <p>No items found. Be the first to report!</p>
    </div>
    <?php else: ?>
    <?php foreach ($items as $item): ?>
    <div class="item-card status-<?php echo $item['status']; ?>">
        <div class="item-image">
            <?php if (!empty($item['image_path'])): ?>
            <img src="<?php echo $item['image_path']; ?>" alt="<?php echo $item['title']; ?>">
            <?php else: ?>
            <i class="fas fa-<?php 
                echo $item['category'] === 'electronics' ? 'laptop' : 
                     ($item['category'] === 'books' ? 'book' : 
                     ($item['category'] === 'clothing' ? 'tshirt' : 
                     ($item['category'] === 'accessories' ? 'key' : 
                     ($item['category'] === 'id' ? 'id-card' : 'box')))); 
            ?>" style="font-size: 3rem; color: #ccc;"></i>
            <?php endif; ?>
        </div>
        <div class="item-details">
            <div class="item-badges">
                <span class="item-type type-<?php echo $item['item_type']; ?>">
                    <?php echo strtoupper($item['item_type']); ?>
                </span>
                
                <span class="status-badge status-<?php echo $item['status']; ?>">
                    <?php echo strtoupper($item['status']); ?>
                </span>
            </div>
            
            <h3 class="item-title"><?php echo htmlspecialchars($item['title']); ?></h3>
            <p class="item-description"><?php echo htmlspecialchars($item['description']); ?></p>
            
            <div class="item-meta">
                <span><i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($item['location']); ?></span>
                <span><i class="fas fa-calendar"></i> <?php echo date('M j, Y', strtotime($item['date_lost_found'])); ?></span>
            </div>
            
            <div class="item-meta">
                <span><i class="fas fa-user"></i> <?php echo htmlspecialchars($item['user_name']); ?></span>
            </div>
            
            <?php if (!empty($item['collection_location'])): ?>
            <div class="item-meta">
                <span><i class="fas fa-hand-holding-heart"></i> Collect at: <?php echo htmlspecialchars($item['collection_location']); ?></span>
            </div>
            <?php endif; ?>
            
            <?php if ($item['status'] === 'returned'): ?>
            <div class="returned-info">
                <span><i class="fas fa-check-circle"></i> Successfully returned to owner</span>
            </div>
            <?php endif; ?>
            
            <?php if (isAdmin()): ?>
            <div class="item-actions">
                <?php if ($item['status'] === 'active' || $item['status'] === 'matched'): ?>
                <a href="admin-edit-item.php?id=<?php echo $item['id']; ?>" class="btn btn-warning btn-small">Edit</a>
                <?php endif; ?>
                
                <?php if ($item['status'] === 'matched' && $item['item_type'] === 'found'): ?>
                <form method="POST" action="admin-process.php" style="display: inline;">
                    <input type="hidden" name="item_id" value="<?php echo $item['id']; ?>">
                    <input type="hidden" name="action" value="mark_returned">
                    <button type="submit" class="btn btn-success btn-small">Mark Returned</button>
                </form>
                <?php endif; ?>
                
                <form method="POST" action="admin-process.php" style="display: inline;">
                    <input type="hidden" name="item_id" value="<?php echo $item['id']; ?>">
                    <input type="hidden" name="action" value="delete">
                    <button type="submit" class="btn btn-danger btn-small" onclick="return confirm('Are you sure you want to delete this item?')">Delete</button>
                </form>
            </div>
            <?php endif; ?>
        </div>
    </div>
    <?php endforeach; ?>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>