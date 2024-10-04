<?php

include_once "./includes/Connection.php";
$CONNECTION = new Connection();

$records = $CONNECTION->Select("equipment_info", null, true);

if (isset($_POST['data'])) {
    $data = json_decode($_POST['data'], true);
}
?>


<div class="cards-table-container">
    <div class="cards-header">
        <div class="buttons">
            <button class="add-equipment-btn">Add New Equipment</button>
        </div>
    </div>
    <div class="cards-content">
        <?php foreach ($records as $record): ?>

            <?php
            $equipments = $CONNECTION->Select("equipment_details", ["equipment_id" => $record["id"], "in_used" => "no"], true);
            ?>
            <div class="card" data-id="<?= $record['id'] ?>" data-type="category">
                <div class="card-top" style="background-image: url('uploads/<?php echo $record['picture'];?>')"></div>
                <div class="card-bot">
                    <p><?php echo $record['name'];?></p>
                    <small><b><?= count($equipments) ?> Available</b> </br/></small>
                    <small><?php echo $record['description'];?></small>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
    <div class="cards-footer"></div>
</div>