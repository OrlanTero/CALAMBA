<?php

include_once "./includes/Connection.php";

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

$CONNECTION->Update("equipment_details", ["in_used" => "yes"], ["id" => $_POST['id']]);

$CONNECTION->Insert("borrow_requests", $data, true);

echo $data['qr_key'];