<?php

include_once "./includes/Connection.php";
include_once "./libraries/vendor/autoload.php";
include_once "./libraries/vendor/chillerlan/php-qrcode/src/QrCode.php";

session_start();

$CONNECTION = new Connection();

$is_admin = $_SESSION['user_type'] == 'admin';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta
        name="viewport"
        content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <title>User Management</title>
    <link
        rel="stylesheet"
        href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" />


    <link rel="stylesheet" href="./style/styles.css">
    <link rel="stylesheet" href="./style/suchStyles.css">
</head>

<body>
<?php include "./includes/user_navigationbar.php"; ?>

<!-- Slide-in Menu -->

<?php include_once("./includes/menu.php") ?>

<!-- Main Content -->
<div class="main-content-container">
    <h1>User Management</h1>

    <?php

if (!isset($_SESSION['user_id'])) {
    session_start();
}

$is_admin = $_SESSION['user_type'] == 'admin';
?>

<div class="search-engine-container" style="padding-bottom: 20px; display: flex; justify-content: flex-end; align-items: center;">
    <select id="status" name="status" style="margin-left: 10px;">
        <option value="">-- Select Status --</option>
        <option value="0">Active</option>
        <option value="1">Archived</option>
    </select>    
    <select id="user_type" name="user_type" style="margin-left: 10px;">
        <option value="">-- Select User Type --</option>
        <option value="student">Student</option>
        <option value="instructor">Instructor</option>
    </select>

    <select id="course" name="course" required style="margin-left: 10px;">
        <option value="">-- Select Course --</option>
        <option value="RAC Servicing (DomRAC)">RAC Servicing (DomRAC)</option>
        <option value="Basic Shielded Metal Arc Welding">Basic Shielded Metal Arc Welding</option>
        <option value="Advanced Shielded Metal Arc Welding">Advanced Shielded Metal Arc Welding</option>
        <option value="Pc operation">Pc operation</option>
        <option value="Bread and pastry production NC II">Bread and Pastry Production NC II</option>
        <option value="Computer aid design (CAD)">Computer Aid Design (CAD)</option>
        <option value="Culinary arts">Culinary Arts</option>
        <option value="Dressmaking NC II">Dressmaking NC II</option>
        <option value="Food and beverage service NC II">Food and Beverage Service NC II</option>
        <option value="Hair care">Hair care</option>
        <option value="Junior beautician">Junior Beautician</option>
        <option value="Gas metal Arc Welding -- GMAW NC I">Gas Metal Arc Welding -- GMAW NC I</option>
        <option value="Gas metal Arc Welding -- GMAW NC II">Gas Metal Arc Welding -- GMAW NC II</option>
    </select>
</div>


    <!-- Borrowed items table -->
    <div class="main-content">
        <?php
        $types = ["student", "instructor"];
        
        include_once "_getAllUsers.php" ?>
    </div>
</div>

<?php include_once "./includes/bigpipes.php"?>

<?php include_once "./includes/user_manager_script.php" ?>

</body>
</html>
