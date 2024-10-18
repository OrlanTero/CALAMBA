<?php

include_once "./includes/Connection.php";


function generateRandomString($length = 10) {
    return substr(str_shuffle(str_repeat($x = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', ceil($length /strlen($x)) )), 1, $length);
}

$CONNECTION = new Connection();

$data = json_decode($_POST['data'], true);

$data["qr_key"] = generateRandomString();

echo json_encode($CONNECTION->Insert("equipment_details", $data, true));