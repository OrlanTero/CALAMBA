<?php

include_once "./includes/Connection.php";

$CONNECTION = new Connection();

$qr_key = $_POST['qr_key'];
$status = $_POST['status'];


if ($status == "approved") {
    $CONNECTION->Update("equipment_details", [
        "in_used" => "yes",
    ], [
        "qr_key" => $qr_key
    ]);
} else {
    $CONNECTION->Update("equipment_details", [
        "in_used" => "no",
    ], [
        "qr_key" => $qr_key
    ]);
}

echo json_encode($CONNECTION->Update("borrow_requests", [
    "request_status" => $status,
], [
    "qr_key" => $qr_key
]));