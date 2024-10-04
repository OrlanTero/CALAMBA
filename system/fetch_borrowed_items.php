<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');

include "./includes/db_connection.php";

// Query to fetch data
$sql = "SELECT id,first_name, middle_name, last_name, suffix, roles, item_to_borrow, locations, borrow_datetime, status FROM borrowers";
$result = $conn->query($sql);

$items = array();

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $items[] = $row;
    }
}

$conn->close();

// Return JSON response
echo json_encode($items);
?>
