<?php

include "./includes/db_connection.php";

// Extract form data
$firstName = $_POST['firstName'] ?? '';
$middleName = $_POST['middleName'] ?? '';
$lastName = $_POST['lastName'] ?? '';
$suffix = $_POST['suffix'] ?? '';
$role = $_POST['roles'] ?? '';
$borrowedItem = $_POST['borrowedItem'] ?? '';
$location = $_POST['location'] ?? '';

// Prepare and bind SQL statement
$stmt = $conn->prepare("INSERT INTO borrowers (first_name, middle_name, last_name, suffix, roles, item_to_borrow, locations, borrow_datetime) VALUES (?, ?, ?, ?, ?, ?, ?,NOW())");
$stmt->bind_param("sssssss", $firstName, $middleName, $lastName, $suffix, $role, $borrowedItem, $location);

// Execute SQL statement
if ($stmt->execute()) {
    echo "New record created successfully";
} else {
    echo "Error: " . $stmt->error;
}

// Close statement and connection
$stmt->close();
$conn->close();
?>
