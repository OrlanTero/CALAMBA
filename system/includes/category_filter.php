<?php

if (!isset($_SESSION['user_id'])) {
    session_start();
}

$is_admin = $_SESSION['user_type'] == 'admin';
?>

<div class="search-engine-container" style="padding-bottom: 20px; display: flex; justify-content: flex-end;">
    <?php if ($is_admin) : ?>
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
    <?php endif; ?>

    <select id="status" name="request_status" style="margin-left: 10px;">
        <option value="">-- Select Status --</option>
        <option value="pending">Pending</option>
        <option value="accepted">Accepted</option>
        <option value="declined">Declined</option>
    </select>
</div>