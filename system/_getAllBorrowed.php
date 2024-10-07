<?php

include_once "./includes/Connection.php";

if (!isset($_SESSION['user_id'])) {
    session_start();
}

if (!isset($borrowed_status)) {
    $borrowed_status = $_POST['status'];
}

$CONNECTION = new Connection();

$max = 10;

$current = $_POST['start'] ?? 0;

$is_user = $_SESSION['user_type'] == "student";

$filter = $borrowed_status == "all" ? [] : ["borrow_status" => $borrowed_status, "request_status" => "accepted"];

if (isset($request_status)) {
    unset($filter['request_status']);
}

if ($is_user) {
    $filter['user_id'] = $_SESSION['user_id'];
} else {
    $filter = $borrowed_status == "all" ? ["request_status" => "accepted"] : $filter;
}

$allRecords = $CONNECTION->Select("borrow_requests", $filter, true);
$records = $CONNECTION->SelectPage("borrow_requests", $filter, true, $current, $max);

$all = count($allRecords) / $max;

?>

<div class="cards-table-container table-pagination-container" data-status="<?= $borrowed_status ?>">
    <div class="cards-header">
        <div class="buttons">
            <button class="print-btn">Download</button>
        </div>
    </div>
    <div class="cards-content c-items">
        <table class="custom-table" id="print-table">
            <thead>
            <tr>
                <th>Name</th>
                <th>Equipment</th>
                <th>Serial</th>
                <th>Location</th>
                <th>Date Time Borrowed</th>
                <?php if (isset($request_status)): ?>
                <th>Request Status</th>
                <?php endif;?>
            </tr>
            </thead>
            <tbody>
                <?php foreach ($records as $record): ?>
                    <?php
                    $item = $CONNECTION->Select("equipment_details", ["id" => $record['item_id']], false);
                    $equipment = $CONNECTION->Select("equipment_info", ["id" => $item['equipment_id']], false);
                    $user = $CONNECTION->Select("user", ["id" => $record['user_id']], false);
                    ?>
                    <tr data-qr="<?= $record['qr_key'] ?>">
                        <td><?php echo $user['first_name'] . " " . $user['middle_name'] . " " . $user['last_name'] ?></td>
                        <td><?php echo $equipment['name']?></td>
                        <td><?php echo $item['serials']?></td>
                        <td><?php echo $item['location']?></td>
                        <td><?php echo $record['date_created']?></td>
                        <?php if (isset($request_status)): ?>
                            <td><?= $record['request_status'] == 'accepted' ? "Accepted" :"Declined" ?></td>
                        <?php endif;?>
                    </tr>
                <?php endforeach;?>
            </tbody>
        </table>
    </div>
    <div class="cards-footer pagination-buttons-container">
        <div class="footer-left">
            <span class="count"><?= count($records) ?> / <?= count($allRecords) ?></span>
        </div>
        <div class="footer-right">
            <div class="pagination-buttons">
                <?php if (!empty($all)): ?>
                    <div class="page-buttons">
                        <?php for($i = 0; $i < $all; $i++): ?>
                            <div class="button page-button <?php echo $current == $i ? 'active' : '' ?>">
                                <span><?= $i + 1 ?></span>
                            </div>
                        <?php endfor?>
                    </div>
                <?php endif ?>
            </div>
        </div>
    </div>
</div>