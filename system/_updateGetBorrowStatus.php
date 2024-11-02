<?php

include_once "./includes/Connection.php";

$CONNECTION = new Connection();

$qr_key = $_POST['qr_key'];
$status = $_POST['status'];
$condition = $_POST['condition'];

$record = $CONNECTION->Select("material_get_requests", ["qr_key" => $qr_key], false);
$item = $CONNECTION->Select("equipment_details", ["id" => $record['item_id']], false);

if ($status === 'returned') {
    $quantity = $item['quantity'];
    $newQuantity = $quantity + $record['quantity'];
    $CONNECTION->Update("equipment_details", ["quantity" => $newQuantity, "item_condition" => $condition], ['id' => $record['item_id']]);
}  

echo json_encode($CONNECTION->Update("material_get_requests", [
    "borrow_status" => $status,
    "item_condition" => $condition,
], [
    "qr_key" => $qr_key
]));