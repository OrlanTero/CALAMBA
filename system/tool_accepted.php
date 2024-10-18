<?php

include_once "./includes/Connection.php";

session_start();

$CONNECTION = new Connection();

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta
        name="viewport"
        content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <title>Borrowed Tools </title>

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
    <h1>Borrowed Tools</h1>

    <?php 
      $NO_REQUEST_STATUS = true;
    ?>
    <?php include_once "./includes/category_filter.php" ?>

    <!-- Borrowed items table -->
    <div class="main-content">
        <?php
        $borrowed_status = "not_returned";
        $request_status = "accepted";
        $is_all = true;
        $category = "tools";
        include_once "_getAllBorrowed.php" ?>
    </div>
</div>

<?php include_once "./includes/bigpipes.php"?>

<?php include_once "./includes/equipment_manager_script.php" ?>

</body>
</html>