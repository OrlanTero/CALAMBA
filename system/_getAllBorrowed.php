<?php

include_once "./includes/Connection.php";
include_once "./libraries/vendor/autoload.php";
include_once "./libraries/vendor/chillerlan/php-qrcode/src/QrCode.php";

use chillerlan\QRCode\QRCode;

$QR = new QRCode();

if (!isset($_SESSION['user_id'])) {
    session_start();
}

if (!isset($borrowed_status)) {
    $borrowed_status = $_POST['status'];
}

if (isset($_POST['is_all'])) {
    $is_all = filter_var($_POST['is_all'], FILTER_VALIDATE_BOOLEAN);
}

if (!isset($course)) {
    if (isset($_POST['course']) && $_POST['course'] !== 'false' && $_POST['course'] !== '') {
        $course = $_POST['course'];
    } else {
        $course = "";
    }
}

if (!isset($category)) {
    $category = $_POST['category'] ?? null;
}

$CONNECTION = new Connection();

$max = 10;

$current = $_POST['start'] ?? 0;

$is_admin = $_SESSION['user_type'] == "admin";

$filter = [];

$myInused = false;

if (isset($_POST['borrow_status']) || isset($borrowed_status)) {
    $filter['borrow_status'] = $_POST['borrow_status'] ?? $borrowed_status;

    if ($filter['borrow_status'] == 'false' || $filter['borrow_status'] == 'all') {
        unset($filter['borrow_status']);
    }
}

if (isset($_POST['request_status'])) {
    $filter['request_status'] = $_POST['request_status'];

    if ($filter['request_status'] == 'all' || $filter['request_status'] == 'false' || $filter['request_status'] == '') {
        unset($filter['request_status']);
    }
}

if (isset($_POST['item_condition'])) {
    $filter['item_condition'] = $_POST['item_condition'];

    if ($filter['item_condition'] == 'all' || $filter['item_condition'] == '' || $filter['item_condition'] == 'false') {
        unset($filter['item_condition']);
    }
}

if (isset($_POST['in_used'])) {
    $myInused = $_POST['in_used'];

    if ($myInused== 'all' || $myInused == '' || $myInused == 'false') {
        $myInused = false;
    }
}

if (isset($request_status)) {
    $filter['request_status'] = $request_status;

    if ($filter['request_status'] == 'all') {
        unset($filter['request_status']);
    }
}

function filterCourseAndCategory($items, $course, $category, $myInused)  {
    return array_filter($items, function ($record) use ($course, $category, $myInused) {
        $CONNECTION = new Connection();
        $item = $CONNECTION->Select("equipment_details", ["id" => $record['item_id']], false);
        $equipment = $CONNECTION->Select("equipment_info", ["id" => $item['equipment_id']], false);

        $usedMatch = empty($myInused) || !$myInused || $item['in_used'] == $myInused;
        $courseMatch = empty($course) || $equipment['course'] == $course;
        $categoryMatch = empty($category) || $equipment['category'] == $category;

        return $courseMatch && $categoryMatch && $usedMatch;
    });
}

if (!$is_admin) {
    $filter['user_id'] = $_SESSION['user_id'];
}

if (empty($filter)) {
    $filter = null;
}

if (isset($_POST['from_date']) && isset($_POST['to_date'])) {
    $filterDate = [
        'from_date' => $_POST['from_date'],
        'to_date' => $_POST['to_date']
    ];
}

$returnStatus = false;


if (isset($filter) && isset($filter['borrow_status'])) {
    if ($filter['borrow_status'] == 'returned') {
        $returnStatus = true;
    }
}

if (isset($filterDate)) {
    if ($returnStatus) {
        $newFilter = [
            [
                'borrow_status' => 'returned',
            ],
            [
                'borrow_status' => 'lost',
            ],
            [
                'borrow_status' => 'damaged',
            ]
        ];

        if (isset($filter['request_status'])) {
            for($i = 0; $i < count($newFilter); $i++) {
                 $newFilter[$i]['request_status'] = $filter['request_status'];
            }
         }

         if (isset($filter['item_condition'])) {
            for($i = 0; $i < count($newFilter); $i++) {
                $newFilter[$i]['item_condition'] = $filter['item_condition'];
            }
         }

         if (isset($filter['in_used'])) {
            for($i = 0; $i < count($newFilter); $i++) {
                $newFilter[$i]['in_used'] = $filter['in_used'];
            }
         }

        $allRecords = $CONNECTION->SelectBetweenOr("borrow_requests", "date_created", $filterDate['from_date'], $filterDate['to_date'], true, false, "ORDER BY date_created DESC", $newFilter);
        $records = $CONNECTION->SelectBetweenPageOr("borrow_requests", "date_created", $filterDate['from_date'], $filterDate['to_date'], true, $current, $max, $newFilter, "ORDER BY date_created DESC");
    } else {
        $allRecords = $CONNECTION->SelectBetween("borrow_requests", "date_created", $filterDate['from_date'], $filterDate['to_date'], true, false, "ORDER BY date_created DESC", $filter);
        $records = $CONNECTION->SelectPageBetween("borrow_requests", "date_created", $filterDate['from_date'], $filterDate['to_date'], true, $current, $max, $filter, "ORDER BY date_created DESC");
    }
} else {
    if ($returnStatus) {
        $newFilter = [
            [
                'borrow_status' => 'returned',
            ],
            [
                'borrow_status' => 'lost',
            ],
            [
                'borrow_status' => 'damaged',
            ]
        ];

        if (isset($filter['request_status'])) {
           for($i = 0; $i < count($newFilter); $i++) {
                $newFilter[$i]['request_status'] = $filter['request_status'];
           }
        }

        if (isset($filter['item_condition'])) {
            for($i = 0; $i < count($newFilter); $i++) {
                $newFilter[$i]['item_condition'] = $filter['item_condition'];
            }
         }

         if (isset($filter['in_used'])) {
            for($i = 0; $i < count($newFilter); $i++) {
                $newFilter[$i]['in_used'] = $filter['in_used'];
            }
         }

        $allRecords = $CONNECTION->SelectOr("borrow_requests", $newFilter, true, "ORDER BY date_created DESC");
        $records = $CONNECTION->SelectPageOr("borrow_requests", $newFilter, true, $current, $max, "ORDER BY date_created DESC");
    } else {

        $allRecords = $CONNECTION->Select("borrow_requests", $filter, true, "=", "ORDER BY date_created DESC");
        $records = $CONNECTION->SelectPage("borrow_requests", $filter, true, $current, $max, "ORDER BY date_created DESC");
    }
}


$allRecords = filterCourseAndCategory($allRecords, $course, $category, $myInused);
$records = filterCourseAndCategory($records, $course, $category, $myInused);

$all = count($allRecords) / $max;

?>

<div class="cards-table-container table-pagination-container" data-status="<?= $borrowed_status ?>"  data-type="<?= isset($category) ? $category : $_POST['category'] ?? "equipment" ?>">
    <div class="cards-header flex">
        <div class="buttons">
            <button class="print-btn">Download</button>
        </div>
        <div class="parent">
            
        </div>
    </div>
    <div class="cards-content c-items">
        <table class="custom-table" id="print-table" data-type="<?= isset($category) ? $category : $_POST['category'] ?? "equipment" ?>" data-request-status="<?= isset($request_status) ? $request_status ?? $_POST['request_status'] : "all" ?>" data-borrow-status="<?= $borrowed_status ?>" data-is-all="<?= isset($is_all) ? $is_all : false ?>">
            <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Equipment</th>
                <th>Serial</th>
                <th>Location</th>
                <th>Date / Time</th>
                <?php if (isset($is_all)): ?>
                <th>Request Status</th>
                <?php endif;?>
                <th>Borrow Status</th>
                <th>Condition</th>
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
                        <td><?php echo $user['student_id'] ?></td>
                        <td><?php echo $user['first_name'] . " " . $user['middle_name'] . " " . $user['last_name'] ?></td>
                        <td><?php echo $equipment['name']?></td>
                        <td><?php echo $item['serials']?></td>
                        <td><?php echo $item['location']?></td>
                        <td><?php echo date('F j, Y g:i A', strtotime($record['date_created']))?></td>
                        <?php if (isset($is_all)): ?>
                            <td><?= ucwords($record['request_status'])?></td>
                        <?php endif;?>
                        <td><?= ucwords($record['borrow_status'])?></td>
                        <td><?= ucwords(str_replace('_', ' ', $record['item_condition']))?></td>
                    <td class="td-qr hide-component"><img src="<?= $QR->render($record['qr_key'])?>" style="width: 50px;height: 50px;" alt=""></td>

                    </tr>
                <?php endforeach;?>
            </tbody>
        </table>
    </div>
    <div class="cards-footer pagination-buttons-container"  data-type="equipment">
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