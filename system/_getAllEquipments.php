<?php

include_once "./includes/Connection.php";
include_once "./includes/Functions.php";

$CONNECTION = new Connection();

if (!isset($_SESSION['user_id'])) {
    session_start();
}

$isAdmin = $_SESSION['user_type'] == 'admin';

$user = GetUser($_SESSION['user_id']);

$max = 10;

$current = $_POST['start'] ?? 0;

$defaultFilter = $isAdmin ?  null : ["course" => $user['course']];

$filter = isset($_POST['category']) ? $_POST['category'] != 'false' ? array_merge(['category' => $_POST['category']], $defaultFilter ?? []) : $defaultFilter : $defaultFilter;

if (isset($_POST['category']) && ($_POST['category'] == 'false' || $_POST['category'] == '')) {
    unset($filter['category']);
}

if (isset($_POST['course']) && ($_POST['course'] == 'false' || $_POST['course'] == '')) {
    unset($filter['course']);
}

if (isset($_POST['course'])  &&  $_POST['course'] && $_POST['course'] != 'false') {
    $filter['course'] = $_POST['course'];
}

// always show non deleted equipments
$filter['deleted'] = '0';

if (empty($filter)) {
    $filter = null;
}



if (isset($_POST['search'])  &&  $_POST['search'] && $_POST['search'] != 'false') {
    $allRecords = $CONNECTION->Search("equipment_info", $_POST['search'], ['name'], $filter);
    $records = $CONNECTION->SearchPage("equipment_info", $_POST['search'],['name'], $filter, $current, $max);

} else {
    $allRecords = $CONNECTION->Select("equipment_info", $filter, true);
    $records = $CONNECTION->SelectPage("equipment_info", $filter, true, $current, $max);
}



$records = array_map(function ($record) use ($CONNECTION) {
    $equipments = $CONNECTION->Select("equipment_details", ["equipment_id" => $record["id"], "in_used" => "no", "deleted" => '0'], true);

    $record['available'] = count($equipments);

    return $record;
}, $records);

$availability = $_GET['availability'] ?? null;

if ($availability) {
    $records = array_filter($records, function ($record) use ($availability) {
        if ($availability == "available") {
            return $record['available'] > 0;
        } elseif ($availability == "alert") {
            return $record['available'] <= $record['alert_level'];
        } else {
            return $record['available'] == 0;
        }
    });
}

$all = count($allRecords) / $max;
?>

<div class="cards-table-container table-pagination-container">
    <div class="cards-header">
       <?php if($isAdmin): ?>
           <div></div>
           <div class="buttons">
               <button class="add-equipment-btn">Add New Equipment</button>
           </div>
        <?php endif ?>
    </div>
    <div class="cards-content c-items">
        <?php foreach ($records as $record): ?>
            <?php
            ?>
            <div class="card c-item <?= $record['available'] <= $record['alert_level'] ? 'red' : '' ?>  " data-id="<?= $record['id'] ?>" data-type="category">
                <div class="card-top" style="background-image: url('uploads/<?php echo $record['picture'];?>')"></div>
                <div class="card-bot">
                    <big><?php echo $record['name'];?></big><br>
                    <small><b><?= $record['available'] ?> Available</b> </br/></small>
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
                            <div class="button page-button <?php echo ($current == $i * 10) ? 'active' : '' ?>">
                                <span><?= $i + 1 ?></span>
                            </div>
                        <?php endfor?>
                    </div>
                </div>
            <?php endif ?>
        </div>
    </div>
</div>