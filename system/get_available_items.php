<?php
header('Content-Type: application/json');

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include "./includes/db_connection.php";

// Get equipment_id from the query string
$equipment_id = isset($_GET['equipment_id']) ? intval($_GET['equipment_id']) : 0;

// Validate equipment_id
if ($equipment_id <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid equipment ID']);
    $conn->close();
    exit();
}

// Prepare the SQL statement to fetch items that are NOT in use (in_used = 'no')
$sql = "
    SELECT d.id, d.serials, d.location, d.date_rcvd, i.picture, d.equipment_id, d.in_used, i.name, i.course, i.category
    FROM equipment_details d
    JOIN equipment_info i ON d.equipment_id = i.id
    WHERE d.equipment_id = ? AND d.in_used = 'no'
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $equipment_id); // Bind the equipment_id parameter

if ($stmt->execute()) {
    $result = $stmt->get_result();
    $items = [];
    while ($row = $result->fetch_assoc()) {
        // Add only items that are not in use to the items array
        $items[] = $row;
    }
    // Return the available items as a JSON response
    echo json_encode(['success' => true, 'items' => $items]);
} else {
    // If there's an error in the query execution
    echo json_encode(['success' => false, 'message' => 'Query error: ' . $stmt->error]);
}

$stmt->close();
$conn->close();
?>
