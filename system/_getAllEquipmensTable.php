<?php

include_once "./includes/Connection.php";
include_once "./libraries/vendor/autoload.php";
include_once "./libraries/vendor/chillerlan/php-qrcode/src/QRCode.php";

use chillerlan\QRCode\QRCode;

$QR = new QRCode();

if (!isset($_SESSION['user_id'])) {
    session_start();
}

$CONNECTION = new Connection();

$max = 10;
$current = $_POST['start'] ?? 0;

$is_admin = $_SESSION['user_type'] == "admin";

$filter = ['deleted' => '0'];

if (!$is_admin) {
    $user = $CONNECTION->Select("user", ["id" => $_SESSION['user_id']], false);
    $filter['course'] = $user['course'];
}

// Add category filter for equipment
$category = $category ?? $_POST['category'] ?? '';

// Add filters based on POST data
if (isset($_POST['in_used']) && $_POST['in_used'] !== '') {
    $filter['in_used'] = $_POST['in_used'];
}

if (isset($_POST['item_condition']) && $_POST['item_condition'] !== '') {
    $filter['item_condition'] = $_POST['item_condition'];
}

if (isset($_POST['course']) && $_POST['course'] !== '') {
    $course = $_POST['course'];
}

$search = $_POST['search'] ?? false;

if ($search && $search !== 'false') {
    $allRecords = $CONNECTION->Search("equipment_details", $search, ['serials'], $filter);
    $records = $CONNECTION->SearchPage("equipment_details", $search, ['serials'], $filter, $current, $max);
} else {
    $newFilter = [
        ['equipment_details.deleted', '=', '0']
    ];

    if (isset($course)) {
        $newFilter[] = ["equipment_info.course", "=", $course];
    }

    if ($category !== '') {
        $newFilter[] = ["equipment_info.category", "=", $category];
    }

    if (isset($_POST['in_used']) && $_POST['in_used'] !== '' && $_POST['in_used'] !== 'false') {
        $newFilter[] = ["equipment_details.in_used", "=", $_POST['in_used']];
    }

    if (isset($_POST['item_condition']) && $_POST['item_condition'] !== '' && $_POST['item_condition'] !== 'false') {
        $newFilter[] = ["equipment_details.item_condition", "=", $_POST['item_condition']];
    }

    $allRecords = $CONNECTION->SelectMultiConditionInnerJoin(
        "equipment_details",
        "INNER JOIN equipment_info ON equipment_details.equipment_id = equipment_info.id",
        $newFilter,
        true,
        false
    );

    $records = array_slice($allRecords, $current, $max);


}

$total_records = count($allRecords);
$all = ceil($total_records / $max);



?>

<div class="cards-table-container table-pagination-container">
    <div class="cards-header flex">
        <div class="buttons">
            <button class="print-btn">Download</button>
        </div>
    </div>
    <div class="cards-content c-items">
        <table class="custom-table" id="print-table" data-category="<?php echo htmlspecialchars($category) ?>">
            <thead>
            <tr>
                <th>Serial</th>
                <th><?= ucwords(str_replace('_', ' ', $category)) ?> Name</th>
                <th>Course</th>
                <th>Location</th>
                <th>Condition</th>
                <th>Status</th>
                <th>QR Code</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($records as $record): ?>
                <?php
                $equipment = $CONNECTION->Select("equipment_info", ["id" => $record['equipment_id']], false);
                ?>
                <tr data-id="<?php echo htmlspecialchars($record['id']) ?>">
                    <td><?php echo htmlspecialchars($record['serials']) ?></td>
                    <td><?php echo htmlspecialchars($equipment['name']) ?></td>
                    <td><?php echo htmlspecialchars($equipment['course']) ?></td>
                    <td><?php echo htmlspecialchars($record['location']) ?></td>
                    <td><?= ucwords(str_replace('_', ' ', htmlspecialchars($record['item_condition']))) ?></td>
                    <td><?php echo $record['in_used'] == 'yes' ? 'In Use' : 'Available' ?></td>
                    <td> 
                        <?php if (!empty($record['serials'])): ?>
                            <img src="<?php echo $QR->render($record['serials']); ?>" alt="QR Code" style="width: 50px; height: 50px;">
                        <?php endif ?>
                    </td>
                </tr>
            <?php endforeach;?>
            </tbody>
        </table>
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
