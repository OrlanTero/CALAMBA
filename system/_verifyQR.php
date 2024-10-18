<?php

include_once "./includes/Connection.php";


$CONNECTION = new Connection();


$key = $_POST['key'];

// find in equipments

$equipments = $CONNECTION->Select("equipment_details", ["qr_key" => $key], false);
$borrowed = $CONNECTION->Select("borrow_requests", ["qr_key" => $key], false);
$getRequests = $CONNECTION->Select("material_get_requests", ["qr_key" => $key], false);

if ($equipments) {
    $record['type'] = 'E';

    $data = [
        "type" => 'E',
        "id" => $equipments['id']
    ];

    echo json_encode($data);
} elseif ($borrowed) {

// find in borrow requests
    $borrowed['type'] = 'B';

    $data = [
        "type" => 'B',
        "id" => $borrowed['request_id']
    ];

    echo json_encode($data);
} else if ($getRequests) {

    // find in get requests
    $getRequests['type'] = 'G';

    $data = [
        "type" => 'G',
        "id" => $getRequests['request_id']
    ];

    echo json_encode($data);
} else {
    echo json_encode(["type" => 'N']);
}
