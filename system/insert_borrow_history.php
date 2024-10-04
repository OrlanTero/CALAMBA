<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
header('Content-Type: application/json');

// Log session variables
error_log('Session: ' . print_r($_SESSION, true));

// Read the JSON input
$input = json_decode(file_get_contents('php://input'), true);
error_log('Input Payload: ' . print_r($input, true)); // Log the input payload

include "./includes/db_connection.php";

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'User not logged in']);
    $conn->close();
    exit();
}

// Prepare data for insertion
$historyPayload = [
    'user_name' => $input['user_name'] ?? '',
    'user_phone' => $input['user_phone'] ?? '',
    'user_type' => $input['user_type'] ?? '',
    'student_id' => $input['student_id'] ?? '',
    'equipment_name' => $input['equipment_name'] ?? '',
    'serial_number' => $input['serial_number'] ?? '',
    'location' => $input['location'] ?? '',
    'date_received' => $input['date_received'] ?? '',
    'qr_code_image' => $input['qr_code_image'] ?? '', // Added QR code image field
    'borrow_datetime' => date('Y-m-d H:i:s'),
    'status' => 'pending'
];

// Validate required fields
$requiredFields = ['user_name', 'user_phone', 'user_type', 'student_id', 'equipment_name', 'serial_number', 'location', 'date_received', 'qr_code_image'];
foreach ($requiredFields as $field) {
    if (empty($historyPayload[$field])) {
        echo json_encode(['success' => false, 'message' => "Missing required field: $field"]);
        $conn->close();
        exit();
    }
}

// Log the payload for debugging
error_log('History Payload: ' . print_r($historyPayload, true));

// Prepare and bind SQL statement
$sql = "INSERT INTO borrow_history (user_name, user_phone, user_type, student_id, equipment_name, serial_number, location, date_received, qr_code_image, borrow_datetime, status) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    echo json_encode(['success' => false, 'error' => 'Statement preparation failed: ' . $conn->error]);
    $conn->close();
    exit();
}

$stmt->bind_param("sssssssssss", 
    $historyPayload['user_name'], 
    $historyPayload['user_phone'], 
    $historyPayload['user_type'], 
    $historyPayload['student_id'], 
    $historyPayload['equipment_name'], 
    $historyPayload['serial_number'], 
    $historyPayload['location'], 
    $historyPayload['date_received'], 
    $historyPayload['qr_code_image'], // Bind QR code image
    $historyPayload['borrow_datetime'],
    $historyPayload['status']
);

// Execute the statement and handle the response
if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    error_log('Insert failed: ' . $stmt->error); // Log the exact error
    echo json_encode(['success' => false, 'error' => 'Insert failed: ' . $stmt->error]); // Return the error message
}

$stmt->close();
$conn->close();
?>
