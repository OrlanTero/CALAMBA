<?php

include_once "./includes/Connection.php";

$CONNECTION = new Connection();

$qr_key = $_POST['qr_key'];
$status = $_POST['status'];

echo json_encode($CONNECTION->Update("borrow_requests", [
    "request_status" => $status,
], [
    "qr_key" => $qr_key
]));