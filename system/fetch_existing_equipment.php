<?php
header('Content-Type: application/json');

include "./includes/db_connection.php";

// Query to get existing equipment data
$sql = "SELECT id, name, course, category FROM equipment_info";
$result = $conn->query($sql);

if ($result === false) {
    echo json_encode(['success' => false, 'message' => 'Query error: ' . $conn->error]);
    exit();
}

if ($result->num_rows > 0) {
    $equipment = [];
    while ($row = $result->fetch_assoc()) {
        $equipment[] = $row;
    }
    echo json_encode(['success' => true, 'equipment' => $equipment]);
} else {
    echo json_encode(['success' => false, 'message' => 'No equipment found']);
}

$conn->close();
?>
