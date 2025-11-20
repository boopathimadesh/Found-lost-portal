<?php
require_once 'includes/auth.php';
require_once 'includes/items.php';

if (!isLoggedIn() || !isAdmin()) {
    header('Location: login.php');
    exit;
}

// Handle admin actions
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['match_items'])) {
        $lostItemId = $_POST['lost_item_id'];
        $foundItemId = $_POST['found_item_id'];
        
        if (updateItemStatus($lostItemId, 'matched', $foundItemId) && 
    updateItemStatus($foundItemId, 'matched', $lostItemId)) {
    $success = "Items matched successfully!";
    
    // Send notification to lost item owner
    require_once 'includes/notifications.php';
    sendMatchNotification($lostItemId, $foundItemId);
} else {
    $error = "Error matching items. Please try again.";
}
    }
}

// Get all items
$lostItems = getItems('lost');
$foundItems = getItems('found');
$allItems = array_merge(getItems('all', 'active'), getItems('all', 'matched'), getItems('all', 'returned'));

$pageTitle = "Admin Dashboard";
?>
<?php include 'includes/header.php'; ?>

<h1>Admin Dashboard</h1>

<?php if ($error): ?>
<div class="alert alert-error"><?php echo $error; ?></div>
<?php endif; ?>

<?php if ($success): ?>
<div class="alert alert-success"><?php echo $success; ?></div>
<?php endif; ?>

<div class="admin-panel">
    <div class="admin-section">
        <h2 class="section-title">All Items (<?php echo count($allItems); ?>)</h2>
        <div class="item-list">
            <?php if (empty($allItems)): ?>
            <p>No items found.</p>
            <?php else: ?>
            <?php foreach ($allItems as $item): ?>
            <div class="item-list-item">
                <h4><?php echo htmlspecialchars($item['title']); ?></h4>
                <p><strong>Type:</strong> <?php echo ucfirst($item['item_type']); ?></p>
                <p><strong>Status:</strong> <span class="status-badge status-<?php echo $item['status']; ?>"><?php echo ucfirst($item['status']); ?></span></p>
                <p><strong>Reported by:</strong> <?php echo htmlspecialchars($item['user_name']); ?></p>
                <div class="item-actions">
                    <a href="admin-edit-item.php?id=<?php echo $item['id']; ?>" class="btn btn-warning btn-small">Edit</a>
                    <form method="POST" action="admin-process.php" style="display: inline;">
                        <input type="hidden" name="item_id" value="<?php echo $item['id']; ?>">
                        <input type="hidden" name="action" value="delete">
                        <button type="submit" class="btn btn-danger btn-small" onclick="return confirm('Are you sure you want to delete this item?')">Delete</button>
                    </form>
                    <?php if ($item['status'] === 'matched' && $item['item_type'] === 'found'): ?>
                    <form method="POST" action="admin-process.php" style="display: inline;">
                        <input type="hidden" name="item_id" value="<?php echo $item['id']; ?>">
                        <input type="hidden" name="action" value="mark_returned">
                        <button type="submit" class="btn btn-success btn-small">Mark Returned</button>
                    </form>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
    
    <div class="admin-section">
        <h2 class="section-title">Match Items</h2>
        <form method="POST">
            <div class="form-group">
                <label for="lost_item_id">Lost Item</label>
                <select id="lost_item_id" name="lost_item_id" required>
                    <option value="">Select a lost item</option>
                    <?php foreach ($lostItems as $item): ?>
                    <?php if ($item['status'] === 'active'): ?>
                    <option value="<?php echo $item['id']; ?>">
                        <?php echo htmlspecialchars($item['title']); ?> (<?php echo htmlspecialchars($item['user_name']); ?>)
                    </option>
                    <?php endif; ?>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="form-group">
                <label for="found_item_id">Found Item</label>
                <select id="found_item_id" name="found_item_id" required>
                    <option value="">Select a found item</option>
                    <?php foreach ($foundItems as $item): ?>
                    <?php if ($item['status'] === 'active'): ?>
                    <option value="<?php echo $item['id']; ?>">
                        <?php echo htmlspecialchars($item['title']); ?> (<?php echo htmlspecialchars($item['user_name']); ?>)
                    </option>
                    <?php endif; ?>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <button type="submit" name="match_items" class="btn btn-success">Match Items</button>
        </form>
        
        <h3 class="section-title" style="margin-top: 30px;">Matched Items</h3>
        <div class="item-list">
            <?php
            $matchedItems = array_filter($allItems, function($item) {
                return $item['status'] === 'matched';
            });
            ?>
            
            <?php if (empty($matchedItems)): ?>
            <p>No matched items yet.</p>
            <?php else: ?>
            <?php foreach ($matchedItems as $item): ?>
            <div class="item-list-item">
                <h4><?php echo htmlspecialchars($item['title']); ?></h4>
                <p><strong>Type:</strong> <?php echo ucfirst($item['item_type']); ?></p>
                <p><strong>Matched with ID:</strong> <?php echo $item['matched_item_id']; ?></p>
                <p><strong>Reported by:</strong> <?php echo htmlspecialchars($item['user_name']); ?></p>
                <?php if ($item['item_type'] === 'found'): ?>
                <form method="POST" action="admin-process.php">
                    <input type="hidden" name="item_id" value="<?php echo $item['id']; ?>">
                    <input type="hidden" name="action" value="mark_returned">
                    <button type="submit" class="btn btn-success btn-small">Mark Returned</button>
                </form>
                <?php endif; ?>
            </div>
            <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - KEC Campus Finder</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-blue: #00008B;
            --white: #FFFFFF;
            --black: #000000;
            --light-gray: #f5f5f5;
            --success: #28a745;
            --danger: #dc3545;
            --warning: #ffc107;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        body {
            background-color: #f8f9fa;
        }
        
        .header {
            background: var(--primary-blue);
            color: var(--white);
            padding: 1rem 0;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        
        .container {
            width: 100%;
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 15px;
        }
        
        .header-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .logo {
            font-size: 1.5rem;
            font-weight: bold;
        }
        
        .user-menu {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        .btn {
            padding: 10px 20px;
            background: var(--primary-blue);
            color: var(--white);
            border: none;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
        }
        
        .btn-danger {
            background: var(--danger);
        }
        
        .btn-success {
            background: var(--success);
        }
        
        .main-content {
            padding: 2rem 0;
        }
        
        .admin-panel {
            display: flex;
            gap: 20px;
            flex-wrap: wrap;
        }
        
        .items-section {
            flex: 1;
            min-width: 300px;
        }
        
        .matching-section {
            flex: 1;
            min-width: 300px;
        }
        
        .item-list {
            max-height: 400px;
            overflow-y: auto;
            border: 1px solid #ddd;
            border-radius: 4px;
            padding: 10px;
            background: var(--white);
        }
        
        .item-card {
            padding: 10px;
            border-bottom: 1px solid #eee;
        }
        
        .item-card:last-child {
            border-bottom: none;
        }
        
        .alert {
            padding: 10px 15px;
            border-radius: 4px;
            margin-bottom: 20px;
        }
        
        .alert-error {
            background: #ffebee;
            color: var(--danger);
            border: 1px solid #f5c6cb;
        }
        
        .alert-success {
            background: #e8f5e9;
            color: var(--success);
            border: 1px solid #c3e6cb;
        }
        
        .section-title {
            margin-bottom: 15px;
            color: var(--primary-blue);
        }
        
        .form-group {
            margin-bottom: 15px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: 500;
        }
        
        .form-group select, .form-group input {
            width: 100%;
            padding: 8px 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
    </style>
</head>
<body>
    <header class="header">
        <div class="container">
            <div class="header-content">
                <div class="logo">KEC Campus Finder - Admin Panel</div>
                <div class="user-menu">
                    <span>Welcome, <?php echo $_SESSION['user']['name']; ?></span>
                    <a href="dashboard.php" class="btn">User Dashboard</a>
                    <a href="?logout=1" class="btn btn-danger">Logout</a>
                </div>
            </div>
        </div>
    </header>

    <main class="main-content">
        <div class="container">
            <h1>Admin Dashboard</h1>
            
            <?php if ($error): ?>
            <div class="alert alert-error"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <?php if ($success): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
            <?php endif; ?>
            
            <div class="admin-panel">
                <div class="items-section">
                    <h2 class="section-title">All Items (<?php echo count($allItems); ?>)</h2>
                    <div class="item-list">
                        <?php if (empty($allItems)): ?>
                        <p>No items found.</p>
                        <?php else: ?>
                        <?php foreach ($allItems as $item): ?>
                        <div class="item-card">
                            <h4><?php echo htmlspecialchars($item['title']); ?></h4>
                            <p><strong>Type:</strong> <?php echo ucfirst($item['item_type']); ?></p>
                            <p><strong>Status:</strong> <?php echo ucfirst($item['status']); ?></p>
                            <p><strong>Reported by:</strong> <?php echo htmlspecialchars($item['user_name']); ?></p>
                            <form method="POST" style="margin-top: 10px;">
                                <input type="hidden" name="item_id" value="<?php echo $item['id']; ?>">
                                <button type="submit" name="delete_item" class="btn btn-danger">Delete</button>
                            </form>
                        </div>
                        <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
                
                <div class="matching-section">
                    <h2 class="section-title">Match Items</h2>
                    <form method="POST">
                        <div class="form-group">
                            <label for="lost_item_id">Lost Item</label>
                            <select id="lost_item_id" name="lost_item_id" required>
                                <option value="">Select a lost item</option>
                                <?php foreach ($lostItems as $item): ?>
                                <?php if ($item['status'] === 'active'): ?>
                                <option value="<?php echo $item['id']; ?>">
                                    <?php echo htmlspecialchars($item['title']); ?> (<?php echo htmlspecialchars($item['user_name']); ?>)
                                </option>
                                <?php endif; ?>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="found_item_id">Found Item</label>
                            <select id="found_item_id" name="found_item_id" required>
                                <option value="">Select a found item</option>
                                <?php foreach ($foundItems as $item): ?>
                                <?php if ($item['status'] === 'active'): ?>
                                <option value="<?php echo $item['id']; ?>">
                                    <?php echo htmlspecialchars($item['title']); ?> (<?php echo htmlspecialchars($item['user_name']); ?>)
                                </option>
                                <?php endif; ?>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <button type="submit" name="match_items" class="btn btn-success">Match Items</button>
                    </form>
                    
                    <h3 class="section-title" style="margin-top: 30px;">Matched Items</h3>
                    <div class="item-list">
                        <?php
                        $matchedItems = array_filter($allItems, function($item) {
                            return $item['status'] === 'matched';
                        });
                        ?>
                        
                        <?php if (empty($matchedItems)): ?>
                        <p>No matched items yet.</p>
                        <?php else: ?>
                        <?php foreach ($matchedItems as $item): ?>
                        <div class="item-card">
                            <h4><?php echo htmlspecialchars($item['title']); ?></h4>
                            <p><strong>Type:</strong> <?php echo ucfirst($item['item_type']); ?></p>
                            <p><strong>Matched with ID:</strong> <?php echo $item['matched_item_id']; ?></p>
                            <p><strong>Reported by:</strong> <?php echo htmlspecialchars($item['user_name']); ?></p>
                        </div>
                        <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </main>
</body>
</html>