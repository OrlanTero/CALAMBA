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
    <title>Material Requests</title>
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
    <h1>Material Requests</h1>

    <?php 
    // $NO_REQUEST_STATUS = true;
    $NO_CONDITION_USED = true;
    ?>
    <?php include_once "./includes/category_filter.php" ?>

    <!-- Borrowed items table -->
    <div class="main-content">
        <?php
        $request_status =  $_GET["status"] ?? "all";
        $is_all = true;
        $category = "material";

        include_once "_getAllGetRequests.php" ?>
    </div>
</div>

<?php include_once "./includes/bigpipes.php"?>

<?php include_once "./includes/equipment_manager_script.php" ?>

</body>
</html>
