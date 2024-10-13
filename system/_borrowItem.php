<?php

include_once "./includes/Connection.php";
include_once "./includes/Response.php";

session_start();
function generateRandomString($length = 10) {
    return substr(str_shuffle(str_repeat($x = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', ceil($length /strlen($x)) )), 1, $length);
}

$CONNECTION = new Connection();

$data = [
    'user_id' => $_SESSION['user_id'],
    'item_id' => $_POST['id'],
    "qr_key" => generateRandomString()
];


$item = $CONNECTION->Select("equipment_details", ["id" => $_POST['id']], false);

if ($item['in_used'] == "yes") {
    echo json_encode(new Response(400, "Item is not available"));
    exit;
}

$CONNECTION->Insert("borrow_requests", $data);

echo json_encode(new Response(200, "Item borrowed successfully", ["qr_key" => $data['qr_key']]));