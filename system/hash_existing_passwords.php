<?php
include "./includes/db_connection.php";

// Fetch all users with plain text passwords
$sql = "SELECT id, pword FROM user";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $id = $row['id'];
        $plainPassword = $row['pword'];
        $hashedPassword = password_hash($plainPassword, PASSWORD_DEFAULT);

        // Update password to hashed version
        $updateSql = "UPDATE user SET pword = ? WHERE id = ?";
        $stmt = $conn->prepare($updateSql);
        $stmt->bind_param("si", $hashedPassword, $id);

        if ($stmt->execute()) {
            echo "Updated user ID $id successfully.<br>";
        } else {
            echo "Error updating user ID $id: " . $stmt->error . "<br>";
        }

        $stmt->close();
    }
} else {
    echo "No users found.";
}

$conn->close();
?>
