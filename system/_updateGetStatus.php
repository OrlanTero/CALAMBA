<?php

include_once "./includes/Connection.php";

$CONNECTION = new Connection();

$qr_key = $_POST['qr_key'];
$status = $_POST['status'];

if ($status === 'accepted') {
    $record = $CONNECTION->Select("material_get_requests", ["qr_key" => $qr_key], false);
    $item = $CONNECTION->Select("equipment_details", ["id" => $record['item_id']], false);
    $quantity = $item['quantity'];
    $newQuantity = $quantity - $record['quantity'];
    $CONNECTION->Update("equipment_details", ["quantity" => $newQuantity], ['id' => $record['item_id']]);
}


echo json_encode($CONNECTION->Update("material_get_requests", [
    "status" => $status,
], [
    "qr_key" => $qr_key
]));