<?php

include_once "./includes/Connection.php";


$CONNECTION = new Connection();

$id = $_POST['id'];

$data["deleted"] = 1;

echo json_encode($CONNECTION->Update("equipment_info", $data, ["id" => $id], true));