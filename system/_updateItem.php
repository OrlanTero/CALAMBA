<?php

include_once "./includes/Connection.php";

$CONNECTION = new Connection();

$data = json_decode($_POST['data'], true);

echo json_encode($CONNECTION->Update("equipment_details", $data, ["id" => $_POST['id']]));