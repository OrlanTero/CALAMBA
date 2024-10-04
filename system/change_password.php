<?php
session_start();

include "./includes/db_connection.php";

// Fetch user ID from session
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php"); // Redirect to login page if not logged in
    exit();
}

$userId = $_SESSION['user_id'];

// Extract form data
$currentPassword = $_POST['current_password'] ?? '';
$newPassword = $_POST['new_password'] ?? '';
$confirmNewPassword = $_POST['confirm_new_password'] ?? '';

// Validate new passwords
if ($newPassword !== $confirmNewPassword) {
    die("Error: New passwords do not match.");
}

// Fetch the current hashed password from the database
$sql = "SELECT pword FROM user WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$user) {
    die("Error: User not found.");
}

$hashedPassword = $user['pword'];

// Verify current password
if (!password_verify($currentPassword, $hashedPassword)) {
    die("Error: Current password is incorrect.");
}

// Hash the new password
$hashedNewPassword = password_hash($newPassword, PASSWORD_DEFAULT);

// Update the password in the database
$updateSql = "UPDATE user SET pword = ? WHERE id = ?";
$stmt = $conn->prepare($updateSql);
$stmt->bind_param("si", $hashedNewPassword, $userId);

if ($stmt->execute()) {
    echo "Password updated successfully.";
    header("Location: profile.php"); // Redirect back to profile page
    exit();
} else {
    echo "Error updating password: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
