<?php
require_once 'includes/auth.php';
require_once 'includes/items.php';

if (!isLoggedIn()) {
    header('Location: login.php');
    exit;
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $category = $_POST['category'];
    $location = $_POST['location'];
    $date = $_POST['date'];
    
    // Handle file upload
    $imagePath = null;
    if (!empty($_FILES['image']['name'])) {
        $targetDir = "uploads/";
        if (!file_exists($targetDir)) {
            mkdir($targetDir, 0777, true);
        }
        
        $fileName = time() . '_' . basename($_FILES['image']['name']);
        $targetFile = $targetDir . $fileName;
        
        if (move_uploaded_file($_FILES['image']['tmp_name'], $targetFile)) {
            $imagePath = $targetFile;
        }
    }
    
    // Handle video upload
    $videoPath = null;
    if (!empty($_FILES['video']['name'])) {
        $targetDir = "uploads/";
        $fileName = time() . '_' . basename($_FILES['video']['name']);
        $targetFile = $targetDir . $fileName;
        
        if (move_uploaded_file($_FILES['video']['tmp_name'], $targetFile)) {
            $videoPath = $targetFile;
        }
    }
    
    if (addItem($_SESSION['user']['id'], $title, $description, $category, $location, $date, 'lost', $imagePath, $videoPath)) {
        $success = "Lost item reported successfully!";
    } else {
        $error = "Error reporting lost item. Please try again.";
    }
}

$pageTitle = "Report Lost Item";
?>
<?php include 'includes/header.php'; ?>

<h1>Report Lost Item</h1>

<?php if ($error): ?>
<div class="alert alert-error"><?php echo $error; ?></div>
<?php endif; ?>

<?php if ($success): ?>
<div class="alert alert-success"><?php echo $success; ?></div>
<?php endif; ?>

<div class="form-container">
    <form method="POST" action="" enctype="multipart/form-data">
        <div class="form-group">
            <label for="title" class="required">Item Title</label>
            <input type="text" id="title" name="title" placeholder="e.g., Black Water Bottle, Calculus Textbook" required>
        </div>
        
        <div class="form-group">
            <label for="description" class="required">Description</label>
            <textarea id="description" name="description" placeholder="Describe your item in detail (color, size, brand, distinguishing features)" required></textarea>
        </div>
        
        <div class="form-group">
            <label for="category">Category</label>
            <select id="category" name="category">
                <option value="">Select a category</option>
                <option value="electronics">Electronics</option>
                <option value="books">Books & Notes</option>
                <option value="clothing">Money</option>
                <option value="accessories">Accessories</option>
                <option value="id">ID Cards</option>
                <option value="other">Other</option>
            </select>
        </div>
        
        <div class="form-group">
            <label for="location" class="required">Where did you lose it?</label>
            <input type="text" id="location" name="location" placeholder="e.g., Library, Cafeteria, Block A" required>
        </div>
        
        <div class="form-group">
            <label for="date" class="required">When did you lose it?</label>
            <input type="date" id="date" name="date" required>
        </div>
        
        <div class="form-group">
            <label for="image">Upload Image (Optional but highly recommended)</label>
            <input type="file" id="image" name="image" accept="image/*">
            <p class="file-note">Adding a photo significantly increases the chances of finding your item</p>
        </div>
        
        <div class="form-group">
            <label for="video">Upload Video (Optional)</label>
            <input type="file" id="video" name="video" accept="video/*">
            <p class="file-note">Video can provide additional details about the item</p>
        </div>
        
        <button type="submit" class="btn">Report Lost Item</button>
    </form>
</div>

<style>
    .form-container {
        background: var(--white);
        padding: 2rem;
        border-radius: 8px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        margin-top: 20px;
    }
    
    .form-group {
        margin-bottom: 20px;
    }
    
    .form-group label {
        display: block;
        margin-bottom: 8px;
        font-weight: 500;
    }
    
    .form-group input, .form-group textarea, .form-group select {
        width: 100%;
        padding: 12px 15px;
        border: 1px solid #ddd;
        border-radius: 4px;
        font-size: 1rem;
    }
    
    .form-group textarea {
        min-height: 100px;
    }
    
    .form-group input:focus, .form-group textarea:focus, .form-group select:focus {
        border-color: var(--primary-blue);
        outline: none;
    }
    
    .required::after {
        content: " *";
        color: var(--danger);
    }
    
    .file-note {
        font-size: 0.9rem;
        color: #666;
        font-style: italic;
        margin-top: 5px;
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
</style>

<?php include 'includes/footer.php'; ?>