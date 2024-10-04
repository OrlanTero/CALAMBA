<?php
session_start();
header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 1);

include "./includes/db_connection.php";

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'User not logged in']);
    $conn->close();
    exit();
}

$userId = $_SESSION['user_id'];

// Fetch user data
$sql = "SELECT first_name, last_name, phone, user_type, student_id FROM user WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $userId);
$stmt->execute();
$userResult = $stmt->get_result();

if ($userResult->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'User not found']);
    $stmt->close();
    $conn->close();
    exit();
}

$userData = $userResult->fetch_assoc();
$stmt->close();

// Get item ID from query string
$itemId = isset($_GET['item_id']) ? intval($_GET['item_id']) : 0;

// Validate item ID
if ($itemId <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid item ID']);
    $conn->close();
    exit();
}

// Check if the item is available for borrowing
$sql = "
    SELECT d.id, d.serials, d.location, d.date_rcvd, i.picture, i.name, i.available, i.id as equipment_info_id
    FROM equipment_details d
    JOIN equipment_info i ON d.equipment_id = i.id
    WHERE d.equipment_id = ? AND d.in_used = 'no'
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $itemId);
$stmt->execute();
$equipmentResult = $stmt->get_result();

if ($equipmentResult->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'Item not found or already in use']);
    $stmt->close();
    $conn->close();
    exit();
}

$itemData = $equipmentResult->fetch_assoc();
$stmt->close();

// Check if available quantity is greater than 0
if ($itemData['available'] <= 0) {
    echo json_encode(['success' => false, 'message' => 'No more items available for borrowing']);
    $conn->close();
    exit();
}

// Now, update the item to mark it as 'in use'
$updateSql = "UPDATE equipment_details SET in_used = 'yes' WHERE id = ?";
$updateStmt = $conn->prepare($updateSql);
$updateStmt->bind_param("i", $itemId);

if ($updateStmt->execute()) {
    // After marking the item as 'in use', reduce the available quantity by 1
    $updateAvailableSql = "UPDATE equipment_info SET available = available - 1 WHERE id = ?";
    $updateAvailableStmt = $conn->prepare($updateAvailableSql);
    $updateAvailableStmt->bind_param("i", $itemData['equipment_info_id']);
    
    if ($updateAvailableStmt->execute()) {
        // If both updates are successful, return the response with the user and item info
        $response = [
            'success' => true,
            'userInfo' => [
                'name' => $userData['first_name'] . ' ' . $userData['last_name'],
                'phone' => $userData['phone'],
                'user_type' => $userData['user_type'],
                'student_id' => $userData['student_id']
            ],
            'itemInfo' => [
                'name' => $itemData['name'],
                'serials' => $itemData['serials'],
                'location' => $itemData['location'],
                'date_rcvd' => $itemData['date_rcvd'],
                'picture' => $itemData['picture']
            ]
        ];
        
        echo json_encode($response);
    } else {
        // If the available quantity update fails, send an error message
        echo json_encode(['success' => false, 'message' => 'Failed to update available quantity']);
    }
    
    $updateAvailableStmt->close();
} else {
    // If the update fails, send an error message
    echo json_encode(['success' => false, 'message' => 'Failed to mark item as in use']);
}

$updateStmt->close();
$conn->close();
?>