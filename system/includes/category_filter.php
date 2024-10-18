<?php

if (!isset($_SESSION['user_id'])) {
    session_start();
}

$is_admin = $_SESSION['user_type'] == 'admin';
?>

<div class="search-engine-container" style="padding-bottom: 20px; display: flex; justify-content: flex-end; align-items: center;">
    <?php if ($is_admin) : ?>
    
    <select id="course" name="course" required>
        <option value="">-- Select Course --</option>
        <option value="RAC Servicing (DomRAC)">RAC Servicing (DomRAC)</option>
        <option value="Basic Shielded Metal Arc Welding">Basic Shielded Metal Arc Welding</option>
        <option value="Advanced Shielded Metal Arc Welding">Advanced Shielded Metal Arc Welding</option>
        <option value="Pc operation">Pc operation</option>
        <option value="Bread and pastry production NC II">Bread and Pastry Production NC II</option>
        <option value="Computer aid design (CAD)">Computer Aid Design (CAD)</option>
        <option value="Culinary arts">Culinary Arts</option>
        <option value="Dressmaking NC II">Dressmaking NC II</option>
        <option value="Food and beverage service NC II">Food and Beverage Service NC II</option>
        <option value="Hair care">Hair care</option>
        <option value="Junior beautician">Junior Beautician</option>
        <option value="Gas metal Arc Welding -- GMAW NC I">Gas Metal Arc Welding -- GMAW NC I</option>
        <option value="Gas metal Arc Welding -- GMAW NC II">Gas Metal Arc Welding -- GMAW NC II</option>
    </select>
    <?php endif; ?>

    <?php if (!isset($NO_REQUEST_STATUS)): ?>
    <select id="status" name="request_status" style="margin-left: 10px;">
        <option value="">-- Select Status --</option>
        <option value="pending">Pending</option>
        <option value="accepted">Accepted</option>
    </select>
    <?php endif; ?>
    <?php if (!isset($NO_CONDITION_USED)): ?>
        <select id="item_condition" name="item_condition" style="margin-left: 20px">
        <option value="">-- Select Condition --</option>
        <option value="good_condition">Good Condition</option>
        <option value="bad_condition">Bad Condition</option>
        <option value="obsolete">Obsolete</option>
        <option value="damaged">Damaged</option>
        <option value="lost">Lost</option>
    </select>
    <select class="select-used" id="usedSelect" required style="margin-left: 20px">
        <option value="">-- Select Used --</option>
        <option value="yes">Using</option>
        <option value="no">Not Using</option>
    </select>
        <?php endif ?>
    <?php if (!isset($NO_DATE_FILTER)): ?>
    <div style="margin-left: 10px;">
        <label for="from_date">From:</label>
        <input type="date" id="from_date" name="from_date">
    </div>

    <div style="margin-left: 10px;">
        <label for="to_date">To:</label>
        <input type="date" id="to_date" name="to_date">
    </div>
    <?php endif; ?>
</div>