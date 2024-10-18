<?php

include_once "./includes/Connection.php";
include_once "./libraries/vendor/autoload.php";
include_once "./libraries/vendor/chillerlan/php-qrcode/src/QrCode.php";

use chillerlan\QRCode\Output\QRGdImagePNG;
use chillerlan\QRCode\QRCode;

$QR = new QrCode();

$filter = [];


if (!isset($_SESSION['user_id'])) {
    session_start();
}

if (isset($_POST['course'])) {
    if (!empty($_POST['course'])) {
        $filter['course'] = $_POST['course'];
    }
}

if (isset($_POST['user_type'])) {
    if (!empty($_POST['user_type'])) {
        $filter['user_type'] = $_POST['user_type'];
    }
} 

if (isset($_POST['is_all'])) {
    $is_all = filter_var($_POST['is_all'], FILTER_VALIDATE_BOOLEAN);
}

$CONNECTION = new Connection();

$max = 10;

$current = $_POST['start'] ?? 0;

$is_admin = $_SESSION['user_type'] == "admin";


if (isset($_POST['status'])) {
    if (!empty($_POST['status'])) {
        $filter['archived'] = $_POST['status'];
    }
}

if (empty($filter)) {
    $filter = null;
}

$allRecords = $CONNECTION->Select("user", $filter, true);
$records = $CONNECTION->SelectPage("user", $filter, true, $current, $max);

$all = count($allRecords) / $max;

?>

<div class="cards-table-container table-pagination-container"  data-is-all="<?= isset($is_all) ? $is_all : false ?>">
    <div class="cards-header flex">
        <div class="buttons">
            <button class="print-btn">Download</button>
        </div>
        <div class="parent">
            
        </div>
    </div>
    <div class="cards-content c-items">
        <table class="custom-table" id="print-table"  data-type="material"  data-is-all="<?= isset($is_all) ? $is_all : false ?>">
            <thead>
            <tr>
                <th>ID</th>
                <th>First Name</th>
                <th>Middle Name</th>
                <th>Last Name</th>
                <th>Suffix</th>
                <th>Phone Number</th>
                <th>Course</th>
                <th>User Type</th>
                <th>Status</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($records as $record): ?>
                <tr data-id="<?= $record['id'] ?>">
                    <td><?= $record['student_id'] ?></td>
                    <td><?= $record['first_name'] ?></td>
                    <td><?= $record['middle_name'] ?></td>
                    <td><?= $record['last_name'] ?></td>
                    <td><?= $record['suffix'] ?></td>
                    <td><?= $record['phone'] ?></td>
                    <td><?= $record['course'] ?></td>
                    <td><?= $record['user_type'] ?></td>
                    <td><?= $record['archived'] == 0 ? "Active" : "Archived" ?></td>
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
                            <div class="button page-button <?php echo ($current == $i * 10) ? 'active' : '' ?>">
                                <span><?= $i + 1 ?></span>
                            </div>
                        <?php endfor?>
                    </div>
                <?php endif ?>
            </div>
        </div>
    </div>
</div>