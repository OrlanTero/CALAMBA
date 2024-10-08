<?php

include_once "./includes/Connection.php";

if (!isset($_SESSION['user_id'])) {
    session_start();
}

$isUser = $_SESSION['user_type'] == 'student';

$CONNECTION = new Connection();

$users = $CONNECTION->Select("user", ["user_type" => "student"], true);
$admins = $CONNECTION->Select("user", ["user_type" => "admin"], true);
$equipments = $CONNECTION->Select("equipment_details", null, true);
$categories = $CONNECTION->Select("equipment_info", null, true);
$borrows = $CONNECTION->Select("borrow_requests",  $isUser ? ["user_id" => $_SESSION['user_id']] : null, true);
$alertEquipments = array_filter($categories, function ($category) use ($CONNECTION) {
    $count = $CONNECTION->CountRow("equipment_details", ["equipment_id" => $category['id']]);
    return $count <= $category['alert_level'];
});
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta
            name="viewport"
            content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <title>Equipment List</title>
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

<div class="container-wrapper">
    <div class="main-content-container" id="container">
        <h1>Dashboard</h1>
        <div class="title">
            <p>System</p>
        </div>
        <div class="dashboard-content">
            <!-- Dashboard content -->
            <?php if (!$isUser): ?>
                <div class="dashboard-square">
                    <p>All Users</p>
                    <h2><?= count($users) ?></h2>
                </div>
                <div class="dashboard-square">
                    <p>All Admin</p>
                    <h2><?= count($admins) ?></h2>
                </div>
                <div class="dashboard-square">
                    <p>Equipments Alert Level</p>
                    <h2><?= count($alertEquipments) ?></h2>
                </div>
            <?php endif; ?>
            <div class="dashboard-square">
                <p>All Equipments</p>
                <h2><?= count($equipments) ?></h2>
            </div>
        </div>

        <div class="title">
            <p>Equipment Availability</p>
        </div>
        <div class="dashboard-content">
            <!-- Dashboard content -->
            <div class="dashboard-square">
                <p>Available</p>
                <h2><?= count(array_filter($equipments, function ($record) {
                        return $record["in_used"] === "no";
                    })) ?></h2>
            </div>
            <div class="dashboard-square">
                <p>Not Available </p>
                <h2><?= count(array_filter($equipments, function ($record) {
                        return $record["in_used"] === "yes";
                    })) ?></h2>
            </div>
        </div>

        <div class="title">
            <p>Equipment Manager</p>
        </div>
        <div class="dashboard-content wrap">
            <!-- Dashboard content -->
            <div class="dashboard-square">
                <p>Borrowed</p>
                <h2><?= count($borrows) ?></h2>
            </div>
            <div class="dashboard-square">
                <p>Returned </p>
                <h2><?= count(array_filter($borrows, function ($record) {
                        return $record["borrow_status"] === "returned";
                    })) ?></h2>
            </div>
            <div class="dashboard-square">
                <p>Not Returned</p>
                <h2><?= count(array_filter($borrows, function ($record) {
                        return $record["borrow_status"] === "not_returned";
                    })) ?></h2>
            </div>
            <div class="dashboard-square">
                <p>Lost</p>
                <h2><?= count(array_filter($borrows, function ($record) {
                        return $record["borrow_status"] === "lost";
                    })) ?></h2>
            </div>
            <div class="dashboard-square">
                <p>Damaged</p>
                <h2><?= count(array_filter($borrows, function ($record) {
                        return $record["borrow_status"] === "damaged";
                    })) ?></h2>
            </div>
        </div>

        <div class="title">
            <p>Borrowed Requests</p>
        </div>

        <div class="dashboard-content">
            <!-- Dashboard content -->
            <div class="dashboard-square">
                <p>Pending</p>
                <h2><?= count(array_filter($borrows, function ($record) {
                        return $record["request_status"] === "pending";
                    })) ?></h2>
            </div>
            <div class="dashboard-square">
                <p>Accepted </p>
                <h2><?= count(array_filter($borrows, function ($record) {
                        return $record["request_status"] === "accepted";
                    })) ?></h2>
            </div>
            <div class="dashboard-square">
                <p>Declined </p>
                <h2><?= count(array_filter($borrows, function ($record) {
                        return $record["request_status"] === "declined";
                    })) ?></h2>
            </div>
        </div>
    </div>
</div>

<?php include_once("./includes/menu.php") ?>




</body>
</html>
