<?php

include_once "./includes/Connection.php";

$CONNECTION = new Connection();

$filter = [];

if (!isset($_SESSION['user_id'])) {
    session_start();
}

if (isset($_POST['no_add_equipment'])) {
    $no_add_equipment = $_POST['no_add_equipment'];
}

if (isset($_POST['item_condition'])) {
    $item_condition = $_POST['item_condition'];
}

if (!isset($_POST['in_used'])) {
}

else if (isset($_POST['in_used']) || $_POST['in_used'] === 'false' || $_POST['in_used'] === '') {
    unset($_POST['in_used']);
}


$filter = [
    "equipment_id" => $_POST["id"],
    "deleted" => '0',
    'in_used' => $_POST['in_used'] ?? 'no'
];

if (isset($item_condition) && $item_condition !== 'false' && $item_condition !== '') {
    $filter['item_condition'] = $item_condition;
}


$isAdmin = $_SESSION['user_type'] == 'admin';

$id = $_POST["id"];

$max = 10;

$current = $_POST['start'] ?? 0;

$allRecords = $CONNECTION->Select("equipment_details", $filter, true);
$records = $CONNECTION->SelectPage("equipment_details", $filter, true, $current, $max);

if (isset($_POST['isAlert'])) {
    $allRecords = array_filter($allRecords, function ($record) {
        return $record['quantity'] <= $record['alert_level'];
    });

    $records = array_filter($records, function ($record) {
        return $record['quantity'] <= $record['alert_level'];
    });
}


$all = count($allRecords) / $max;

?>

<div class="cards-table-container">
    <div class="cards-header">

        <?php if($isAdmin): ?>

        <div class="buttons">
            <button class="back-btn">Back</button>
            <?php if (!isset($no_add_equipment)): ?>
            <button class="remove-item-btn">Remove this Equipment</button>
            <button class="add-item-btn">Add New Item</button>
            <?php endif ?>
        </div>

        <?php else: ?>
            <div class="buttons">
                <button class="back-btn">Back</button>
            </div>
        <?php endif ?>

    </div>
    <div class="cards-content">
        <?php foreach ($records as $record): ?>
            <?php
                $equipment = $CONNECTION->Select("equipment_info", ["id" => $record["equipment_id"]],  false);

                $isOnAlert = is_null($record['alert_level']) ? false : $record['quantity'] <= $record['alert_level'];
                
            ?>
            <div class="card <?php echo $isOnAlert ? 'red' : '' ?>" data-id="<?= $record['id'] ?>" data-type="item">
                <div class="card-top" style="background-image: url('uploads/<?php echo $equipment['picture'];?>')"></div>
                <div class="card-bot">
                    <big>Serial: <?php echo $record['serials'];?></big>
                    <big style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 100%;"><?php echo $equipment['name'];?></big>
                    <?php if ($equipment['category'] == "material"): ?>
                        <small>Quantity Available: <?php echo $record['quantity'] ?></small>
                    <?php else: ?>
                        <!-- <small>Availability: <?php echo $record['in_used'] == "yes" ? "Not Available" : "Available";?><br></small> -->
                    <?php endif ?>
                    <small style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 100%;"><?php echo date('F j, Y g:i A', strtotime($record['date_rcvd'])); ?></small>
                    <small><?= $record['location'] ?></small>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
    <div class="cards-footer pagination-buttons-container">
        <div class="footer-left">
            <span class="count"><?= count($records) ?> / <?= count($allRecords) ?></span>
        </div>
        <div class="footer-right">
            <?php if(!empty($all)): ?>
                <div class="pagination-buttons">
                    <div class="button-left page-button">
                        <span>First</span>
                    </div>
                    <div class="page-buttons">
                        <?php for($i = 0; $i < $all; $i++): ?>
                            <div class="button page-button <?php echo $current == $i ? 'active' : '' ?>">
                                <span><?= $i + 1 ?></span>
                            </div>
                        <?php endfor?>
                    </div>
                    <div class="button-right page-button">
                        <span>Last</span>
                    </div>
                </div>
            <?php endif ?>
        </div>
    </div>
</div>