<?php

include_once "./includes/Connection.php";
include_once "./libraries/vendor/autoload.php";
include_once "./libraries/vendor/chillerlan/php-qrcode/src/QrCode.php";

use chillerlan\QRCode\Output\QRGdImagePNG;
use chillerlan\QRCode\QRCode;

$QR = new QrCode();

if (!isset($_SESSION['user_id'])) {
    session_start();
}

if (!isset($request_status)) {
    $request_status = $_POST['status'];
}

if (!isset($course)) {
    if (isset($_POST['course'])) {
        $course = $_POST['course'];
    } else {
        $course = "";
    }
}

$CONNECTION = new Connection();

$max = 10;

$current = $_POST['start'] ?? 0;

$is_user = $_SESSION['user_type'] == "student";

$filter = $request_status == "all" ? null : [ "status" => "accepted"];

if (isset($request_status)) {
    unset($filter['request_status']);
}

if ($is_user) {
    $filter['user_id'] = $_SESSION['user_id'];
}

function filterCourse($items, $course)  {
    if (empty($course)) {
        return $items;
    }

    return array_filter($items, function ($record) use ($course)  {
        $CONNECTION = new Connection();
        $item = $CONNECTION->Select("equipment_details", ["id" => $record['item_id']], false);
        $equipment = $CONNECTION->Select("equipment_info", ["id" => $item['equipment_id']], false);

        return $equipment['course'] == $course;
    });
}


$allRecords = filterCourse($CONNECTION->Select("material_get_requests", $filter, true), $course);
$records = filterCourse($CONNECTION->SelectPage("material_get_requests", $filter, true, $current, $max), $course);

$all = count($allRecords) / $max;

?>

<div class="cards-table-container table-pagination-container" data-status="<?= $request_status ?>"  data-type="material">
    <div class="cards-header flex">
        <div class="buttons">
            <button class="print-btn">Download</button>
        </div>
        <div class="parent">
            <div class="search-engine-container" style="padding-bottom: 20px">

                <select id="course" name="course" required>
                    <option value="">-- Select Course --</option>
                    <option value="RAC Servicing (DomRAC)">RAC Servicing (DomRAC)</option>
                    <option value="Basic Shielded Metal Arc Welding">Basic Shielded Metal Arc Welding</option>
                    <option value="Advanced Shielded Metal Arc Welding">Advanced Shielded Metal Arc Welding</option>
                    <option value="Pc operation">Pc operation</option>
                    <option value="Bread and pastry production NC II">Bread and pastry production NC II</option>
                    <option value="Computer aid design (CAD)">Computer aid design (CAD)</option>
                    <option value="Culinary arts">Culinary arts</option>
                    <option value="Dressmaking NC II">Dressmaking NC II</option>
                    <option value="Food and beverage service NC II">Food and beverage service NC II</option>
                    <option value="Hair care">Hair care</option>
                    <option value="Junior beautician">Junior beautician</option>
                    <option value="Gas metal Arc Welding -- GMAW NC I">Gas metal Arc Welding -- GMAW NC I</option>
                    <option value="Gas metal Arc Welding -- GMAW NC II">Gas metal Arc Welding -- GMAW NC II</option>
                </select>
            </div>
        </div>
    </div>
    <div class="cards-content c-items">
        <table class="custom-table" id="print-table"  data-type="material">
            <thead>
            <tr>
                <th>Name</th>
                <th>Material</th>
                <th>Serial</th>
                <th>Location</th>
                <th>Quantity</th>
                <th>Date Time Request</th>
                <?php if (isset($request_status)): ?>
                    <th>Request Status</th>
                <?php endif;?>
                <th class="td-qr hide-component">QR</th>
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
                    <td><?php echo $record['quantity']?></td>
                    <td><?php echo $record['date_created']?></td>
                    <?php if (isset($request_status)): ?>
                        <td><?= ucwords($record['status'])  ?></td>
                    <?php endif;?>
                    <td class="td-qr hide-component"><img src="<?= $QR->render($record['qr_key'])?>" style="width: 50px;height: 50px;" alt=""></td>
                    
                </tr>
            <?php endforeach;?>
            </tbody>
        </table>
    </div>
    <div class="cards-footer pagination-buttons-container" data-type="material">
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