<?php 

include_once "./../../../libraries/vendor/autoload.php";
include_once "./../../../includes/Connection.php";
include_once "./../../../libraries/vendor/chillerlan/php-qrcode/src/QrCode.php";

session_start();

use chillerlan\QRCode\QRCode;

$CONNECTION = new Connection();

$data = json_decode($_POST['data'], true);

$qr_key = $data['qr_key'];

$QR = new QrCode();

$isUser = $_SESSION['user_type'] == "student" || $_SESSION['user_type'] == "instructor";

$record = $CONNECTION->Select("borrow_requests", ["qr_key" => $qr_key], false);

$item = $CONNECTION->Select("equipment_details", ['id' => $record['item_id']], false);

$equipment = $CONNECTION->Select("equipment_info", ['id' => $item['equipment_id']], false);
?>

<div class="main-popup-container">
    <div class="popup-background"></div>
    <div class="popup-content">
        <div class="main-popup-content">
            <div class="popup-top">
                <div class="headline">
                    <h1>Borrow QR</h1>
                </div>
                <div class="paragraph">
                    <p>Take a Picture/Download QR</p>
                </div>

                <div class="floating-button">
                    <div class="close-popup popup-button">
                        <img src="pictures/close.svg"/>
                    </div>
                </div>
            </div>
            <form class="form-control">
                <div class="popup-bot">
                    <div class="qr-code-container">
                        <div class="two-image">
                            <img src="uploads/<?php echo $equipment['picture'];?>" class="image-one" alt="QR Code" />
                            <img src="<?= $QR->render($qr_key) ?>" class="image-two" alt="QR Code" />
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="location">Equipment Name</label>
                        <input type="text" value="<?= $equipment['name'] ?>" readonly />
                    </div>
                    <div class="form-group">
                        <label for="location">Location</label>
                        <input type="text" id="location" name="location" placeholder="Enter location" value="<?php echo $item['location'] ?>" readonly />
                    </div>
                    <div class="form-group">
                        <label for="serials">Serial Number</label>
                        <input type="text" id="serials" name="serials" placeholder="Enter serial number"  value="<?php echo $item['serials'] ?>"  readonly />
                    </div>
                    <?php if ($record['request_status'] != 'accepted'): ?>

                        <?php if(!$isUser): ?>
                            <div class="form-group-container flex">
                                <center>
                                    <?php if ($record['request_status'] != 'accepted'): ?>
                                        <label for="serials">Request Status: </label>

                                        <select id="status" required name="request_status">
                                            <option value="" <?= $record['request_status'] == "" ? "selected" : "" ?>>-- Select Status --</option>
                                            <option value="accepted" <?= $record['request_status'] == "accepted" ? "selected" : "" ?>>Accepted</option>
                                            <option value="declined" <?= $record['request_status'] == "not_returned" ? "selected" : "" ?>>Declined</option>
                                        </select>
                                    <?php else: ?>
                                        <label for="serials">Borrow Status: </label>

                                        <select id="status" required name="borrow_status">
                                            <option value="" <?= $record['borrow_status'] == "" ? "selected" : "" ?>>-- Select Status --</option>
                                            <option value="returned" <?= $record['borrow_status'] == "returned" ? "selected" : "" ?>>Returned</option>
                                            <option value="not_returned" <?= $record['borrow_status'] == "not_returned" ? "selected" : "" ?>>Not Returned</option>
                                            <option value="lost" <?= $record['borrow_status'] == "lost" ? "selected" : "" ?>>Lost</option>
                                            <option value="damaged" <?= $record['borrow_status'] == "damaged" ? "selected" : "" ?>>Damaged</option>
                                            </select>
                                    <?php endif ?>
                                </center>
                            </div>
                        <?php endif ?>
                    <?php endif ?>

                </div>
                <div class="popup-footer">
                    <button type="button" class="download-qr" >Download QR</button>
                    <?php if (!$isUser): ?>
                        <button type="submit" >Save</button>
                    <?php endif ?>
                </div>
            </form>
        </div>
    </div>
</div>