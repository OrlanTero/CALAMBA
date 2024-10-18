<?php

include_once "./includes/Connection.php";

$CONNECTION = new Connection();

$qr_key = $_POST['qr_key'];
$status = $_POST['status'];
$filter = [
    "borrow_status" => $status
];

$itemFilter = [];

if (isset($_POST['item_condition'])) {
    $filter['item_condition'] = $_POST['item_condition'];
    $itemFilter['item_condition'] = $_POST['item_condition'];
}

$borrowed_request = $CONNECTION->Select("borrow_requests", [
    "qr_key" => $qr_key
], false);

if ($status == "returned") {
    $itemFilter['in_used'] = "no";
    $CONNECTION->Update("equipment_details", $itemFilter, [
        "id" => $borrowed_request['item_id']
    ]);
} else {
    $itemFilter['in_used'] = "yes";

    $CONNECTION->Update("equipment_details", $itemFilter, [
        "id" => $borrowed_request['item_id']
    ]);
}

echo json_encode($CONNECTION->Update("borrow_requests", $filter, [
    "qr_key" => $qr_key
]));