<?php

include_once "./includes/Connection.php";

$CONNECTION = new Connection();

$data = json_decode($_POST['data'], true);

$data['pword'] = password_hash($data['password'], PASSWORD_DEFAULT);

unset($data['password']);

echo json_encode($CONNECTION->Update("user", $data, ["id" => $_POST['id']]));