<?php

include "./includes/db_connection.php";

// Extract form data
$firstName = $_POST['firstName'] ?? '';
$middleName = $_POST['middleName'] ?? '';
$lastName = $_POST['lastName'] ?? '';
$suffix = $_POST['suffix'] ?? '';
$studentID = $_POST['studentID'] ?? '';
$pword = $_POST['pword'] ?? '';
$confirmPword = $_POST['confirmPword'] ?? '';
$userType = $_POST['userType'] ?? '';
$phone = $_POST['phone'] ?? '';
$course = $_POST['course'] ?? '';

// Check if passwords match
if ($pword !== $confirmPword) {
    die("Error: Passwords do not match.");
}

// Hash the password for security
$hashedPassword = password_hash($pword, PASSWORD_DEFAULT);

// Prepare and bind SQL statement
$stmt = $conn->prepare("INSERT INTO user (first_name, middle_name, last_name, suffix, student_id, pword, user_type, phone, course) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
$stmt->bind_param("sssssssss", $firstName, $middleName, $lastName, $suffix, $studentID, $hashedPassword, $userType, $phone, $course);

// Execute SQL statement
if ($stmt->execute()) {
    // Redirect to login page (index.php)
    header("Location: ../index.php");
    exit(); // Ensure no further code is executed
} else {
    echo "Error: " . $stmt->error;
}

// Close statement and connection
$stmt->close();
$conn->close();
?>
