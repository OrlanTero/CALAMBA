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
$sql = "SELECT student_id, user_type, profile_picture FROM user WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();
$userData = $result->fetch_assoc();

// Store student_id in session
$_SESSION['student_id'] = $userData['student_id'];

$stmt->close();
$conn->close();

// Return user data as JSON
echo json_encode([
    'username' => $userData['student_id'],
    'user_type' => $userData['user_type'],
    'profile_picture' => $userData['profile_picture']
]);
?>
