<?php

include_once "./includes/Connection.php";
include_once "./includes/Response.php";

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