<?php

include_once "./includes/Connection.php";

$CONNECTION = new Connection();

$qr_key = $_POST['qr_key'];
$status = $_POST['status'];

$borrowed_request = $CONNECTION->Select("borrow_requests", [
    "qr_key" => $qr_key
], false);

if ($status == "returned") {
    $CONNECTION->Update("equipment_details", [
        "in_used" => "no",
    ], [
        "id" => $borrowed_request['item_id']
    ]);
} else {
    $CONNECTION->Update("equipment_details", [
        "in_used" => "yes",
    ], [
        "id" => $borrowed_request['item_id']
    ]);
}

echo json_encode($CONNECTION->Update("borrow_requests", [
    "borrow_status" => $status,
], [
    "qr_key" => $qr_key
]));