<?php

include_once "./includes/Connection.php";

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

$data = [
    "item_id" => $id,
    "quantity" => $quantity,
    "user_id" => $_SESSION['user_id'],
    "qr_key" => $key
];

$insert = json_encode($CONNECTION->Insert("material_get_requests", $data, true));

if ($insert) {
    echo $key;
}