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
    <title>Damaged Equipment History</title>
    <!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.qrcode/1.0/jquery.qrcode.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/qrcodejs@1.0.0/qrcode.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/jsqr@1.4.0/dist/jsQR.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/html5-qrcode/minified/html5-qrcode.min.js"></script>
    <script src="https://unpkg.com/html5-qrcode/minified/html5-qrcode.min.js"></script> -->
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
    <h1>Damaged Equipment History</h1>

    <?php include_once "./includes/category_filter.php" ?>

    <!-- Borrowed items table -->
    <div class="main-content">
        <?php
        $borrowed_status = "damaged";
        $request_status = "accepted";
        include_once "_getAllBorrowed.php" ?>
    </div>
</div>

<?php include_once "./includes/bigpipes.php"?>

<?php include_once "./includes/equipment_manager_script.php" ?>
</body>
</html>
