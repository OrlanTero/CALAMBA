<?php
include_once "./includes/Connection.php";
include_once "./includes/Functions.php";

$CONNECTION = new Connection();

$data = json_decode($_POST['data'], true);

$serial = GenerateCategorySerialNumber($data['category']);

$data['serials'] = $serial;

echo json_encode($CONNECTION->Insert("equipment_info", $data,true));