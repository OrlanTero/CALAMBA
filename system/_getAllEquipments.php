<?php

include_once "./includes/Connection.php";

$CONNECTION = new Connection();

$max = 10;

$current = $_POST['start'] ?? 0;

$filter = isset($_POST['category']) ? $_POST['category'] != 'false' ? ['category' => $_POST['category']] : null : null;

if (isset($_POST['search'])  &&  $_POST['search'] && $_POST['search'] != 'false') {
    $allRecords = $CONNECTION->Search("equipment_info", $_POST['search'], ['name'], $filter);
    $records = $CONNECTION->SearchPage("equipment_info", $_POST['search'],['name'], $filter, $current, $max);

} else {
    $allRecords = $CONNECTION->Select("equipment_info", $filter, true);
    $records = $CONNECTION->SelectPage("equipment_info", $filter, true, $current, $max);


}


$all = count($allRecords) / $max;

?>

<div class="cards-table-container table-pagination-container">
    <div class="cards-header">
        <div></div>
        <div class="buttons">
            <button class="add-equipment-btn">Add New Equipment</button>
        </div>
    </div>
    <div class="cards-content c-items">
        <?php foreach ($records as $record): ?>
            <?php
                $equipments = $CONNECTION->Select("equipment_details", ["equipment_id" => $record["id"], "in_used" => "no"], true);
            ?>
            <div class="card c-item <?= count($equipments)  <= $record['alert_level'] ? 'red' : '' ?>  " data-id="<?= $record['id'] ?>" data-type="category">
                <div class="card-top" style="background-image: url('uploads/<?php echo $record['picture'];?>')"></div>
                <div class="card-bot">
                    <big><?php echo $record['name'];?></big><br>
                    <small><b><?= count($equipments) ?> Available</b> </br/></small>
                    <small><?php echo $record['description'];?></small>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
    <div class="cards-footer pagination-buttons-container">
        <div class="footer-left">
            <span class="count"><?= count($records) ?> / <?= count($allRecords) ?></span>
        </div>
        <div class="footer-right">
            <?php if (!empty($all)): ?>
                <div class="pagination-buttons">
                    <div class="page-buttons">
                        <?php for($i = 0; $i < $all; $i++): ?>
                            <div class="button page-button <?php echo $current == $i ? 'active' : '' ?>">
                                <span><?= $i + 1 ?></span>
                            </div>
                        <?php endfor?>
                    </div>
                </div>
            <?php endif ?>
        </div>
    </div>
</div>