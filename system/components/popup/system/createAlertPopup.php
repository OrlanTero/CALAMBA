<?php
global $APPLICATION;

$data = json_decode($_POST['data'], true);
$options = json_decode($_POST['options'], true);

function CreateButton($name, $label, $is_filled = false) {
    return '
        <div class="form-group filled-button '.$name.' '. ($is_filled ? 'button-fill' : '') .'">
            <div class="link">
                <span>'.$label.'</span>
            </div>
        </div>
    ';
}


?>
<div class="main-popup-container">
    <div class="popup-background"></div>
    <div class="popup-content">
        <div class="main-popup-content alert-popup">
            <div class="popup-top">
                <div class="headline">
                    <h1><?= $data['primary'] ?></h1>
                </div>
                <div class="paragraph">
                    <p><?= $data['secondary'] ?></p>
                </div>

                <div class="floating-button">
                    <div class="close-popup popup-button">
                        <img src="pictures/close.svg"/>
                    </div>
                </div>
            </div>
            <form action="" class="form-control">
                <div class="popup-bot">
                    <p class="p-message"><?= $data['message'] ?? '' ?></p>
                </div>
                <?php if($options['alert_type'] !== "alert-no-button"): ?>
                    <div class="popup-footer">
                        <div class="form-group-container submit-group">
                            <?php if ($options['alert_type'] === "alert-yes-no"): ?>
                                <?= CreateButton("no-btn", "No") ?>
                                <?= CreateButton("yes-btn", "Yes", true) ?>
                            <?php elseif ($options['alert_type'] === "alert-yes-only"): ?>
                                <?= CreateButton("yes-btn", "Yes", true) ?>
                            <?php elseif ($options['alert_type'] === "alert-close-only"): ?>
                                <?= CreateButton("close-btn", "Close", true) ?>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endif; ?>
            </form>
        </div>
    </div>
</div>