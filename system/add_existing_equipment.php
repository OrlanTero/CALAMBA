<?php
session_start();

include "./includes/db_connection.php";

// Get POST data
$equipmentId = isset($_POST['equipment_id']) ? trim($_POST['equipment_id']) : '';
$additionalQuantity = isset($_POST['additional_quantity']) ? trim($_POST['additional_quantity']) : 0;
$location = isset($_POST['location']) ? trim($_POST['location']) : '';
$serials = isset($_POST['serials']) ? trim($_POST['serials']) : '';

// Automatically set date_rcvd to current date and time
$dateRcvd = date('Y-m-d H:i:s');

// Validate inputs
if (empty($equipmentId) || !is_numeric($equipmentId) || $equipmentId <= 0 ||
    empty($additionalQuantity) || !is_numeric($additionalQuantity) || $additionalQuantity <= 0 ||
    empty($serials)) {
    echo json_encode(['success' => false, 'message' => 'Invalid input. Please check the values.']);
    exit();
}

// Prepare and execute query to get the existing quantity and available status
$sql = "SELECT total_quantity, available FROM equipment_info WHERE id = ?";
$stmt = $conn->prepare($sql);
if (!$stmt) {
    echo json_encode(['success' => false, 'message' => 'Failed to prepare statement: ' . $conn->error]);
    exit();
}

$stmt->bind_param("i", $equipmentId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $equipment = $result->fetch_assoc();
    $newQuantity = $equipment['total_quantity'] + $additionalQuantity;
    $newAvailable = $equipment['available'] + $additionalQuantity;

    // Update total_quantity and available in the database
    $updateStmt = $conn->prepare("UPDATE equipment_info SET total_quantity = ?, available = ? WHERE id = ?");
    if (!$updateStmt) {
        echo json_encode(['success' => false, 'message' => 'Failed to prepare update statement: ' . $conn->error]);
        exit();
    }

    $updateStmt->bind_param("iii", $newQuantity, $newAvailable, $equipmentId);
    $updateStmt->execute();

    if ($updateStmt->affected_rows > 0) {
        // Insert new record for the additional equipment with the serial number and location
        $insertStmt = $conn->prepare("INSERT INTO equipment_details (equipment_id, serials, location, date_rcvd) VALUES (?, ?, ?, ?)");
        if (!$insertStmt) {
            echo json_encode(['success' => false, 'message' => 'Failed to prepare insert statement: ' . $conn->error]);
            exit();
        }

        $insertStmt->bind_param("isss", $equipmentId, $serials, $location, $dateRcvd);
        $insertStmt->execute();

        if ($insertStmt->affected_rows > 0) {
            echo json_encode(['success' => true, 'message' => 'Equipment quantity and details updated successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error inserting equipment details.']);
        }

        $insertStmt->close();
    } else {
        echo json_encode(['success' => false, 'message' => 'Error updating quantity.']);
    }

    $updateStmt->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Equipment not found.']);
}

$stmt->close();
$conn->close();
?>
