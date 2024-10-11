<?php

include_once "./includes/Connection.php";
include_once "./includes/Response.php";
include_once "./includes/Functions.php";


$CONNECTION = new Connection();

$data = json_decode($_POST['data'], true);

$toUC = ["first_name", "middle_name", "last_name", "suffix"];

foreach ($toUC as $field) {
    $data[$field] = ucwords(strtolower($data[$field]));
}

if (!IsPhoneNumberValid($data['phone'])) {
    echo json_encode(new Response(300, "Phone Number is not Valid", ['errors' => [[
        'input' => 'phone',
        'message' => "Phone Number is not Valid"
    ]]]));

    return;
}

if ($data['pword'] != $data['confirmPword']) {
    echo json_encode(new Response(300, "Password not Matched", ['errors' => [[
        'input' => 'confirmPword',
        'message' => "Password doesnt Matched"
    ]]]));

    return;
}


if (!IsStudentIDValid($data['student_id'])) {
    echo json_encode(new Response(300, "Student ID is not Valid", ['errors' => [[
        'input' =>'student_id',
        'message' => "Student ID is not Valid"
    ]]]));

    return;
}

if (IsStudentIDExist($data['student_id'])) {
    echo json_encode(new Response(300, "Student ID already Exists", ['errors' => [[
        'input' =>'student_id',
        'message' => "Student ID already Exists"
    ]]]));

    return;
}

unset($data['confirmPword']);

$data['pword'] = password_hash($data['pword'], PASSWORD_DEFAULT);

$insert = $CONNECTION->Insert("user", $data, true);

if ($insert) {
    echo json_encode(new Response(200, "User Created Successfully", ['id' => $insert]));
} else {
    echo json_encode(new Response(500, "Error Creating User"));
}
