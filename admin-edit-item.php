<?php
require_once 'includes/auth.php';
require_once 'includes/items.php';

if (!isLoggedIn() || !isAdmin()) {
    header('Location: login.php');
    exit;
}

$error = '';
$success = '';

if (!isset($_GET['id'])) {
    header('Location: admin-dashboard.php');
    exit;
}

$itemId = $_GET['id'];
$item = getItemById($itemId);

if (!$item) {
    header('Location: admin-dashboard.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $category = $_POST['category'];
    $location = $_POST['location'];
    $date = $_POST['date'];
    $collectionLocation = $_POST['collection_location'] ?? null;
    
    if (updateItem($itemId, $title, $description, $category, $location, $date, $collectionLocation)) {
        $success = "Item updated successfully!";
        // Refresh item data
        $item = getItemById($itemId);
    } else {
        $error = "Error updating item. Please try again.";
    }
}

$pageTitle = "Edit Item";
?>
<?php include 'includes/header.php'; ?>

<h1>Edit Item</h1>

<?php if ($error): ?>
<div class="alert alert-error"><?php echo $error; ?></div>
<?php endif; ?>

<?php if ($success): ?>
<div class="alert alert-success"><?php echo $success; ?></div>
<?php endif; ?>

<div class="form-container">
    <form method="POST" action="">
        <div class="form-group">
            <label for="title" class="required">Item Title</label>
            <input type="text" id="title" name="title" value="<?php echo htmlspecialchars($item['title']); ?>" required>
        </div>
        
        <div class="form-group">
            <label for="description" class="required">Description</label>
            <textarea id="description" name="description" required><?php echo htmlspecialchars($item['description']); ?></textarea>
        </div>
        
        <div class="form-group">
            <label for="category">Category</label>
            <select id="category" name="category">
                <option value="">Select a category</option>
                <option value="electronics" <?php echo $item['category'] === 'electronics' ? 'selected' : ''; ?>>Electronics</option>
                <option value="books" <?php echo $item['category'] === 'books' ? 'selected' : ''; ?>>Books & Notes</option>
                <option value="money" <?php echo $item['category'] === 'money' ? 'selected' : ''; ?>>Money</option>
                <option value="accessories" <?php echo $item['category'] === 'accessories' ? 'selected' : ''; ?>>Accessories</option>
                <option value="id" <?php echo $item['category'] === 'id' ? 'selected' : ''; ?>>ID Cards</option>
                <option value="other" <?php echo $item['category'] === 'other' ? 'selected' : ''; ?>>Other</option>
            </select>
        </div>
        
        <div class="form-group">
            <label for="location" class="required">Location</label>
            <input type="text" id="location" name="location" value="<?php echo htmlspecialchars($item['location']); ?>" required>
        </div>
        
        <div class="form-group">
            <label for="date" class="required">Date</label>
            <input type="date" id="date" name="date" value="<?php echo $item['date_lost_found']; ?>" required>
        </div>
        
        <?php if ($item['item_type'] === 'found'): ?>
        <div class="form-group">
            <label for="collection_location">Collection Location</label>
            <input type="text" id="collection_location" name="collection_location" value="<?php echo htmlspecialchars($item['collection_location'] ?? ''); ?>" placeholder="Where can the owner collect the item?">
        </div>
        <?php endif; ?>
        
        <div class="form-group">
            <label>Current Status: <span class="status-badge status-<?php echo $item['status']; ?>"><?php echo ucfirst($item['status']); ?></span></label>
        </div>
        
        <button type="submit" class="btn">Update Item</button>
        <a href="admin-dashboard.php" class="btn btn-outline">MATCH ITEM</a>
    </form>
</div>
                                                                    
<?php include 'includes/footer.php'; ?>