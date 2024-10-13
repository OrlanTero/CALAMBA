<?php

include_once "./includes/Connection.php";

if (!isset($_SESSION['user_id'])) {
    session_start();
}

$isUser = $_SESSION['user_type'] == 'student' || $_SESSION['user_type'] == 'instructor';

$CONNECTION = new Connection();

$users = $CONNECTION->Select("user", ["user_type" => "student"], true);
$admins = $CONNECTION->Select("user", ["user_type" => "admin"], true);
$equipments = $CONNECTION->Select("equipment_details", ['deleted' => '0'], true);
$categories = $CONNECTION->Select("equipment_info", ['deleted' => '0'], true);
$borrows = $CONNECTION->Select("borrow_requests",  $isUser ? ["user_id" => $_SESSION['user_id']] : null, true);
$materialRequests = $CONNECTION->Select("material_get_requests",  $isUser ? ["user_id" => $_SESSION['user_id']] : null, true);

$alertEquipments = array_filter($categories, function ($category) use ($CONNECTION, $equipments) {
    $count = $CONNECTION->CountRow("equipment_details", ["equipment_id" => $category['id'], "deleted" => '0']);

    if ($category['category'] == 'material') {
        $count = count(array_filter($equipments, function ($equipment) use ($category) {
            return $equipment['equipment_id'] == $category['id'] && $equipment['quantity'] > 0;
        }));
    }

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
           
            <a href="catalog.php?availability=alert" class="dashboard-square">
                <p>Equipments Alert Level</p>
                <h2><?= count($alertEquipments) ?></h2>
            </a>
            <?php endif; ?>
            <a href="catalog.php" class="dashboard-square">
                <p>All Equipments</p>
                <h2><?= count($equipments) ?></h2>
            </a>
        </div>

        <div class="title">
            <p>Item Availability</p>
        </div>
        <div class="dashboard-content">
            <!-- Dashboard content -->
            <a href="catalog.php?availability=available" class="dashboard-square">
                <p>Available</p>
                <h2><?= count(array_filter($equipments, function ($record) {
                        return $record["in_used"] === "no";
                    })) ?></h2>
            </a>
            <a href="catalog.php?availability=not_available" class="dashboard-square">
                <p>Not Available </p>
                <h2><?= count(array_filter($equipments, function ($record) {
                        return $record["in_used"] === "yes";
                    })) ?></h2>
            </a>
        </div>

        <div class="title">
            <p>Equipment Manager</p>
        </div>
        <div class="dashboard-content wrap">
            <!-- Dashboard content -->
            <a href="borrow.php" class="dashboard-square">
                <p>Borrowed</p>
                <h2><?= count($borrows) ?></h2>
                </a>
            <a href="returned.php" class="dashboard-square">
                <p>Returned </p>
                <h2><?= count(array_filter($borrows, function ($record) {
                        return $record["borrow_status"] === "returned";
                    })) ?></h2>
            </a>
            <a href="not_returned.php" class="dashboard-square">
                <p>Not Returned</p>
                <h2><?= count(array_filter($borrows, function ($record) {
                        return $record["borrow_status"] === "not_returned";
                    })) ?></h2>
            </a>
            <a href="lost.php" class="dashboard-square">
                <p>Lost</p>
                <h2><?= count(array_filter($borrows, function ($record) {
                        return $record["borrow_status"] === "lost";
                    })) ?></h2>
            </a>
            <a href="damaged.php" class="dashboard-square">
                <p>Damaged</p>
                <h2><?= count(array_filter($borrows, function ($record) {
                        return $record["borrow_status"] === "damaged";
                    })) ?></h2>
            </a>
        </div>

        <div class="title">
            <p>Borrowed Requests</p>
        </div>

        <div class="dashboard-content">
            <!-- Dashboard content -->
            <a href="requests.php?status=pending" class="dashboard-square">
                <p>Pending</p>
                <h2><?= count(array_filter($borrows, function ($record) {
                        return $record["request_status"] === "pending";
                    })) ?></h2>
            </a>
            <a href="requests.php?status=accepted" class="dashboard-square">
                <p>Accepted </p>
                <h2><?= count(array_filter($borrows, function ($record) {
                        return $record["request_status"] === "accepted";
                    })) ?></h2>
            </a>
            <a href="requests.php?status=declined" class="dashboard-square">
                <p>Declined </p>
                <h2><?= count(array_filter($borrows, function ($record) {
                        return $record["request_status"] === "declined";
                    })) ?></h2>
            </a>
        </div>

        <div class="title">
            <p>Material Requests</p>
        </div>

        <div class="dashboard-content">
            <!-- Dashboard content -->
            <a href="material_requests.php?status=pending" class="dashboard-square">
                <p>Pending</p>
                <h2><?= count(array_filter($materialRequests, function ($record) {
                        return $record["status"] === "pending";
                    })) ?></h2>
            </a>
            <a href="material_requests.php?status=accepted" class="dashboard-square">
                <p>Accepted </p>
                    <h2><?= count(array_filter($materialRequests, function ($record) {
                            return $record["status"] === "accepted";
                    })) ?></h2>
            </a>
            <a href="material_requests.php?status=declined" class="dashboard-square">
                <p>Declined </p>
                <h2><?= count(array_filter($materialRequests, function ($record) {
                        return $record["status"] === "declined";
                    })) ?></h2>
            </a>
        </div>
    </div>
</div>

<?php include_once("./includes/menu.php") ?>




</body>
</html>
