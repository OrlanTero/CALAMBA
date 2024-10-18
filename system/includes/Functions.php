<?php

include_once __DIR__ . "/../includes/Connection.php";
include_once __DIR__ . "/../includes/Response.php";

function IsStudentIDExist($studentID): bool
{
    $CONNECTION = new Connection();

    $result = $CONNECTION->Select("user", [
        "student_id" => $studentID
    ], true);

    return count($result) > 0;
}

function IsStudentIDValid($studentID): bool
{
    $pattern = '/^[\d-]+$/';

    return preg_match($pattern, $studentID);
}

function IsPhoneNumberValid($phone) : bool
{
    $pattern = '/^(\+?63)?09\d{9}$/';

    return preg_match($pattern, $phone);
}

function GetPhotoURLByName($name) {
    return "./avatars/" . strtoupper($name[0]) . '.jpg';
}

function GetUser($id)
{
    $CONNECTION = new Connection();

    $result = $CONNECTION->Select("user", [
        "id" => $id
    ], true);

    if (count($result) > 0) {
        $user = $result[0];
        $user['photo_url'] = GetPhotoURLByName($user['first_name']);
        return $user;
    }

    return null;
}

function GetEquipment($id) {
    $CONNECTION = new Connection();

    $result = $CONNECTION->Select("equipment_info", [
        "id" => $id
    ], true);

    return $result[0];
}

function GenerateSerialNumber($equipment_id) {
    $CONNECTION = new Connection();

    $equipment = $CONNECTION->Select("equipment_info", ["id" => $equipment_id], false);

    if (!$equipment) {
        return false;
    }

    $count = $CONNECTION->CountRow("equipment_details", ["equipment_id" => $equipment_id]);

    do {
        $count++;
        $serial = strtoupper(substr($equipment['category'], 0, 3)) . '-' . str_pad($equipment_id, 4, '0', STR_PAD_LEFT) . '-' . str_pad($count, 4, '0', STR_PAD_LEFT);
        
        $exists = $CONNECTION->Select("equipment_details", ["serials" => $serial], false);
    } while ($exists);

    return $serial;
}


function GenerateCategorySerialNumber($category) {
    $CONNECTION = new Connection();

    $count = $CONNECTION->CountRow("equipment_info", ["category" => $category]);

    do {
        $count++;
        $categoryPrefix = strtoupper(substr($category, 0, 3));
        $serial = $categoryPrefix . '-' . str_pad($count, 8, '0', STR_PAD_LEFT);
        
        $exists = $CONNECTION->Select("equipment_info", ["serials" => $serial], false);
    } while ($exists);

    return $serial;
}
