<?php

include_once "./includes/Connection.php";
include_once "./includes/Response.php";


if(!isset($_SESSION['user_id'])) {
    session_start();
}

function generateRandomString($length = 10) {
    return substr(str_shuffle(str_repeat($x = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', ceil($length /strlen($x)) )), 1, $length);
}

$CONNECTION = new Connection();

$quantity = $_POST['quantity'];
$id = $_POST['id'];
$key = generateRandomString();


$item = $CONNECTION->Select("equipment_details", ["id" => $id], false);

if ($quantity > $item['quantity']) {
    echo json_encode(new Response(400, "Quantity is not available"));
    exit;
}

$data = [
    "item_id" => $id,
    "quantity" => $quantity,
    "user_id" => $_SESSION['user_id'],
    "qr_key" => $key
];

$insert = json_encode($CONNECTION->Insert("material_get_requests", $data, true));

if ($insert) {
    echo json_encode(new Response(200, "Item requested successfully", ["qr_key" => $key]));
} else {
    echo json_encode(new Response(400, "Failed to request item"));
}
