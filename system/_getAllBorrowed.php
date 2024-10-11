<?php

include_once "./includes/Connection.php";

if (!isset($_SESSION['user_id'])) {
    session_start();
}

if (!isset($borrowed_status)) {
    $borrowed_status = $_POST['status'];
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

$filter = $borrowed_status == "all" ? [] : ["borrow_status" => $borrowed_status, "request_status" => "accepted"];

if (isset($request_status)) {
    unset($filter['request_status']);
}

if ($is_user) {
    $filter['user_id'] = $_SESSION['user_id'];
} else {
    $filter = $borrowed_status == "all" ? ["request_status" => "accepted"] : $filter;
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

if (isset($filter['borrow_status']) && $filter['borrow_status'] == 'false') {
    unset($filter['borrow_status']);
}

$allRecords = filterCourse($CONNECTION->Select("borrow_requests", $filter, true), $course);
$records = filterCourse($CONNECTION->SelectPage("borrow_requests", $filter, true, $current, $max), $course);


$all = count($allRecords) / $max;

?>

<div class="cards-table-container table-pagination-container" data-status="<?= $borrowed_status ?>"  data-type="equipment">
    <div class="cards-header flex">
        <div class="buttons">
            <button class="print-btn">Download</button>
        </div>
        <div class="parent">
            <div class="search-engine-container" style="padding-bottom: 20px">

                <select id="course" name="course" required >
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
        <table class="custom-table" id="print-table" data-type="equipment">
            <thead>
            <tr>
                <th>Name</th>
                <th>Equipment</th>
                <th>Serial</th>
                <th>Location</th>
                <th>Date Time Borrowed</th>
                <?php if (isset($request_status)): ?>
                <th>Request Status</th>
                <?php endif;?>
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
                        <td><?php echo $record['date_created']?></td>
                        <?php if (isset($request_status)): ?>
                            <td><?= $record['request_status'] == 'accepted' ? "Accepted" :"Declined" ?></td>
                        <?php endif;?>
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