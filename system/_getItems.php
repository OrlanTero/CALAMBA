<?php

include_once "./includes/Connection.php";
$CONNECTION = new Connection();

$id = $_POST["id"];
$records = $CONNECTION->Select("equipment_details", ["equipment_id" => $id], true);

?>

<div class="cards-table-container">
    <div class="cards-header">
        <div class="buttons">
            <button class="back-btn">Back</button>
            <button id="add-item-btn">Add New Item</button>
        </div>
    </div>
    <div class="cards-content">
        <?php foreach ($records as $record): ?>
            <?php
                $equipment = $CONNECTION->Select("equipment_info", ["id" => $record["equipment_id"]],  false);
            ?>
            <div class="card" data-id="<?= $record['id'] ?>" data-type="item">
                <div class="card-top" style="background-image: url('uploads/<?php echo $equipment['picture'];?>')"></div>
                <div class="card-bot">
                    <p>Serial: <?php echo $record['serials'];?></p>
                    <p>Location: <?php echo $record['location'];?></p>
                    <small>Availability: <?php echo $record['in_used'] == "yes" ? "Not Available" : "Available";?><br></small>
                    <small>Date Received: <?php echo $record['date_rcvd'];?></small>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
    <div class="cards-footer"></div>
</div>