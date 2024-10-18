<?php
session_start();

include "./includes/db_connection.php";

// Fetch user ID from session
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'User not logged in']);
    exit();
}

$userId = $_SESSION['user_id'];

// Fetch user data
$sql = "SELECT course FROM user WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();
$userData = $result->fetch_assoc();

$stmt->close();
$conn->close();

// Return JSON response
if ($userData) {
    echo json_encode(['course' => $userData['course']]);
} else {
    echo json_encode(['error' => 'User not found']);
}
?>
