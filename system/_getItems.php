<?php

include_once "./includes/Connection.php";

$CONNECTION = new Connection();


if (!isset($_SESSION['user_id'])) {
    session_start();
}

$isAdmin = $_SESSION['user_type'] == 'admin';

$id = $_POST["id"];

$max = 10;

$current = $_POST['start'] ?? 0;

$allRecords = $CONNECTION->Select("equipment_details", ["equipment_id" => $id, "deleted" => '0'], true);
$records = $CONNECTION->SelectPage("equipment_details", ["equipment_id" => $id, "deleted" => '0'], true, $current, $max);

$all = count($allRecords) / $max;

?>

<div class="cards-table-container">
    <div class="cards-header">
        <?php if($isAdmin): ?>

        <div class="buttons">
            <button class="back-btn">Back</button>
            <button class="remove-item-btn">Remove this Equipment</button>
            <button class="add-item-btn">Add New Item</button>
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
            ?>
            <div class="card" data-id="<?= $record['id'] ?>" data-type="item">
                <div class="card-top" style="background-image: url('uploads/<?php echo $equipment['picture'];?>')"></div>
                <div class="card-bot">
                    <big>Serial: <?php echo $record['serials'];?></big><br>
                    <big>Location: <?php echo $record['location'];?></big><br>
                    <?php if ($equipment['category'] == "material"): ?>
                        <small>Quantity Available: <?php echo $record['quantity'] ?><br></small>
                    <?php else: ?>
                        <small>Availability: <?php echo $record['in_used'] == "yes" ? "Not Available" : "Available";?><br></small>
                    <?php endif ?>
                    <small><?php echo date('F j, Y g:i A', strtotime($record['date_rcvd'])); ?></small>
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