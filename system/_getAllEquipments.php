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

$defaultFilter = $isAdmin ? null : ["course" => $user['course']];
$filter = $defaultFilter;

$condition = null;

if (isset($_POST['category']) && $_POST['category'] !== 'false' && $_POST['category'] !== '') {
    $filter['category'] = $_POST['category'];
}

if (isset($_POST['course']) && $_POST['course'] !== 'false' && $_POST['course'] !== '') {
    $filter['course'] = $_POST['course'];
}

if (isset($_POST['item_condition']) && $_POST['item_condition'] !== 'false' && $_POST['item_condition'] !== '') {
    $condition = $_POST['item_condition'];
}

if (isset($category)) {
    $filter['category'] = $category;
}

$filter['deleted'] = '0';

if (!$isAdmin) {
    $filter['course'] = $user['course'];

}

$filter = !empty($filter) ? $filter : null;

$search = $_POST['search'] ?? false;


$equipments = $CONNECTION->Select("equipment_info", ['deleted' => '0'], true);

foreach ($equipments as $equipment) {
    if ($equipment['serials'] == '' || $equipment['serials'] == null) {
        $CONNECTION->Update("equipment_info", [
            "serials" => GenerateSerialNumber($equipment['id'])
        ], ["id" => $equipment['id']]);
    }
}


if ($search && $search !== 'false') {
    $allRecords = $CONNECTION->Search("equipment_info", $search, ['name'], $filter);
    $records = $CONNECTION->SearchPage("equipment_info", $search, ['name'], $filter, $current, $max);
} else {
    $allRecords = $CONNECTION->Select("equipment_info", $filter, true);
    $records = $CONNECTION->SelectPage("equipment_info", $filter, true, $current, $max);
}

$records = array_map(function ($record) use ($CONNECTION) {
    $equipments = $CONNECTION->Select("equipment_details", [
        "equipment_id" => $record["id"],
        "in_used" => isset($_POST['used']) && $_POST['used'] !== 'false' && !empty($_POST['used']) ? $_POST['used'] : "no",
        "deleted" => '0'
    ], true);
    $record['available'] = count($equipments);
    return $record;
}, $records);

if (isset($isAlert)) {
    $records = array_filter($records, function ($record) use ($isAlert, $CONNECTION) {
        $items = $CONNECTION->Select("equipment_details", [
            "equipment_id" => $record["id"],
            "in_used" => "no",
            "deleted" => '0'
        ], true);
    
        $items = array_filter($items, function ($item) {
            return $item['quantity'] <= $item['alert_level'];
        });
    
        return count($items) > 0;
    });
}

if (isset($_POST['used']) && $_POST['used'] !== 'false' && !empty($_POST['used'])) {
    $records = array_filter($records, function ($record) use ($CONNECTION) {
        $items = $CONNECTION->Select("equipment_details", [
            "equipment_id" => $record["id"],
            "in_used" => $_POST['used'] ?? "no",
            "deleted" => '0'
        ], true);
    
        return count($items) > 0;
    });
}

if (!is_null($condition) && $condition !== 'false' && $condition !== '') {
    $records = array_filter($records, function ($record) use ($CONNECTION, $condition) {
        $items = $CONNECTION->Select("equipment_details", [
            "equipment_id" => $record["id"],
            "item_condition" => $condition,
            "deleted" => '0'
        ], true);
    
        return count($items) > 0;
    });
}




$total_records = count($allRecords);
$all = ceil($total_records / $max);
?>

<div class="cards-table-container table-pagination-container" >
    <div class="cards-header">
       <?php if (!isset($no_add_equipment) || !$no_add_equipment): ?>
          <?php if(!isset($_POST['no_add_equipment']) || !$_POST['no_add_equipment'] || $_POST['no_add_equipment'] == 'false'): ?>
            <?php if ($isAdmin): ?>
               <div></div>
               <div class="buttons">
                   <button class="add-equipment-btn">Add New <?= isset($category) ? ucfirst($category) : ucfirst($_POST['category'] ?? 'Category') ?></button>
               </div>
            <?php endif ?>
          <?php endif ?>
       <?php endif ?>
    </div>
    <div class="cards-content c-items">
        <?php foreach ($records as $record): ?>
            <?php
                $items = $CONNECTION->Select("equipment_details", [
                    "equipment_id" => $record['id'],
                    "deleted" => '0'
                ], true);
            ?>
            <div class="card c-item" data-type="category" data-id="<?= $record['id'] ?>">
                <div class="card-top" style="background-image: url('uploads/<?= $record['picture'] ?>')">
                <?php if (!isset($no_add_equipment) || !$no_add_equipment): ?>
                    <?php if(!isset($_POST['no_add_equipment']) || !$_POST['no_add_equipment'] || $_POST['no_add_equipment'] == 'false'): ?>
                    <div class="floating-icon-button">
                            <i class="fas fa-edit"></i>
                        </div>
                    <?php endif ?>
                <?php endif ?>
                </div>
                <div class="card-bot">
                    <small><b><?= $record['available'] ?> Available</b></br></small>
                    <big><?= $record['name'] ?> (<?= count($items) ?>)</big><br>
                    <small><?= $record['description'] ?></small>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
    <div class="cards-footer pagination-buttons-container">
        <div class="footer-left">
            <span class="count"><?= $current + 1 ?>-<?= min($current + $max, $total_records) ?> of <?= $total_records ?></span>
        </div>
        <div class="footer-right">
            <div class="pagination-buttons">
                <?php if ($all > 1): ?>
                    <div class="page-buttons">
                        <?php for ($i = 0; $i < $all; $i++): ?>
                            <div class="button page-button <?= ($current == $i * $max) ? 'active' : '' ?>">
                                <span><?= $i + 1 ?></span>
                            </div>
                        <?php endfor ?>
                    </div>
                <?php endif ?>
            </div>
        </div>
    </div>
</div>
