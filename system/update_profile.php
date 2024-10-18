<?php
session_start();

include "./includes/db_connection.php";

// Fetch user ID from session
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php"); // Redirect to login page if not logged in
    exit();
}

$userId = $_SESSION['user_id'];

// Handle file upload
$profilePicture = null;
if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === UPLOAD_ERR_OK) {
    $uploadDir = 'uploads/';
    $uploadFile = $uploadDir . basename($_FILES['profile_picture']['name']);

    // Ensure the uploads directory exists
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    if (move_uploaded_file($_FILES['profile_picture']['tmp_name'], $uploadFile)) {
        $profilePicture = basename($_FILES['profile_picture']['name']);
    } else {
        die("File upload failed: " . $_FILES['profile_picture']['error']);
    }
}

// Update user profile in the database
$stmt = $conn->prepare("UPDATE user SET profile_picture = ? WHERE id = ?");
$stmt->bind_param("si", $profilePicture, $userId);
$stmt->execute();

$stmt->close();
$conn->close();

header("Location: profile.php"); // Redirect back to profile page
exit();
?>
