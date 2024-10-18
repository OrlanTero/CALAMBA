<?php
header('Content-Type: application/json');

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include "./includes/db_connection.php";

// Fetch QR code approvals
$sql = "SELECT id, borrower_id, qr_code_data, date_scanned, status FROM qr_code_approvals WHERE status = 'pending'";
$result = $conn->query($sql);

$items = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $items[] = $row;
    }
}

echo json_encode(['success' => true, 'items' => $items]);

$conn->close();
?>
