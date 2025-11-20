<?php
require_once __DIR__ . '/../config/database.php';

function addItem($userId, $title, $description, $category, $location, $date, $itemType, $imagePath = null, $videoPath = null, $collectionLocation = null) {
    global $pdo;
    
    $stmt = $pdo->prepare("INSERT INTO items (user_id, title, description, category, location, date_lost_found, item_type, image_path, video_path, collection_location) 
                          VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    
    return $stmt->execute([$userId, $title, $description, $category, $location, $date, $itemType, $imagePath, $videoPath, $collectionLocation]);
}


function getItems($type = 'all', $status = 'active') {
    global $pdo;
    
    $sql = "SELECT i.*, u.name as user_name FROM items i JOIN users u ON i.user_id = u.id WHERE i.status = ?";
    $params = [$status];
    
    if ($type !== 'all') {
        $sql .= " AND i.item_type = ?";
        $params[] = $type;
    }
    
    $sql .= " ORDER BY i.created_at DESC";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getUserItems($userId, $type = 'all') {
    global $pdo;
    
    $sql = "SELECT * FROM items WHERE user_id = ?";
    $params = [$userId];
    
    if ($type !== 'all') {
        $sql .= " AND item_type = ?";
        $params[] = $type;
    }
    
    $sql .= " ORDER BY created_at DESC";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getItemById($id) {
    global $pdo;
    
    $stmt = $pdo->prepare("SELECT i.*, u.name as user_name, u.email as user_email FROM items i JOIN users u ON i.user_id = u.id WHERE i.id = ?");
    $stmt->execute([$id]);
    
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function updateItemStatus($itemId, $status, $matchedItemId = null) {
    global $pdo;
    
    if ($matchedItemId) {
        $stmt = $pdo->prepare("UPDATE items SET status = ?, matched_item_id = ? WHERE id = ?");
        return $stmt->execute([$status, $matchedItemId, $itemId]);
    } else {
        $stmt = $pdo->prepare("UPDATE items SET status = ? WHERE id = ?");
        return $stmt->execute([$status, $itemId]);
    }
}

function deleteItem($itemId) {
    global $pdo;
    
    $stmt = $pdo->prepare("DELETE FROM items WHERE id = ?");
    return $stmt->execute([$itemId]);
}
function updateItem($itemId, $title, $description, $category, $location, $date, $collectionLocation = null) {
    global $pdo;
    
    if ($collectionLocation) {
        $stmt = $pdo->prepare("UPDATE items SET title = ?, description = ?, category = ?, location = ?, date_lost_found = ?, collection_location = ? WHERE id = ?");
        return $stmt->execute([$title, $description, $category, $location, $date, $collectionLocation, $itemId]);
    } else {
        $stmt = $pdo->prepare("UPDATE items SET title = ?, description = ?, category = ?, location = ?, date_lost_found = ? WHERE id = ?");
        return $stmt->execute([$title, $description, $category, $location, $date, $itemId]);
    }
}

function markItemReturned($itemId) {
    global $pdo;
    
    $stmt = $pdo->prepare("UPDATE items SET status = 'returned', updated_at = NOW() WHERE id = ?");
    return $stmt->execute([$itemId]);
}

function checkExpiredItems() {
    global $pdo;
    
    // Set items older than 2 years as expired
    $stmt = $pdo->prepare("UPDATE items 
                          SET status = 'expired' 
                          WHERE created_at < DATE_SUB(NOW(), INTERVAL 2 YEAR) 
                          AND status = 'active'");
    return $stmt->execute();
}
?>