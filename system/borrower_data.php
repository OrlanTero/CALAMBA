<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
header('Content-Type: application/json');

// Log session variables
error_log('Session Data: ' . print_r($_SESSION, true));

include "./includes/db_connection.php";

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    error_log('User not logged in');
    echo json_encode(['success' => false, 'message' => 'User not logged in']);
    exit();
}

// Fetch user data from session
$user_name = $_SESSION['user_name'] ?? '';
$student_id = $_SESSION['student_id'] ?? '';

// Check if student_id is set
if (empty($student_id)) {
    error_log('Student ID not found in session');
    echo json_encode(['success' => false, 'message' => 'Student ID not found in session']);
    exit();
}

// Prepare SQL query to fetch borrow history for the logged-in user by student_id
$sql = "SELECT user_name, user_phone, equipment_name, serial_number, location, date_received, borrow_datetime, qr_code_image, status FROM borrow_history WHERE student_id = ?";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    error_log('SQL statement preparation failed: ' . $conn->error);
    echo json_encode(['success' => false, 'message' => 'SQL statement preparation failed.']);
    exit();
}

$stmt->bind_param("s", $student_id);
$stmt->execute();
$result = $stmt->get_result();

$borrowedItems = [];
while ($row = $result->fetch_assoc()) {
    $borrowedItems[] = $row;
}

$stmt->close();
$conn->close();

// Return the borrow history and user data as JSON
echo json_encode([
    'success' => true,
    'user' => [
        'name' => $user_name,
        'student_id' => $student_id,
    ],
    'data' => $borrowedItems
]);
?>
