<?php 

include_once "./../../../libraries/vendor/autoload.php";
include_once "./../../../includes/Connection.php";
include_once "./../../../libraries/vendor/chillerlan/php-qrcode/src/QrCode.php";

session_start();

use chillerlan\QRCode\Data\QRMatrix;
use chillerlan\QRCode\Output\QRGdImagePNG;
use chillerlan\QRCode\Output\QROutputInterface;
use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;

$CONNECTION = new Connection();

$data = json_decode($_POST['data'], true);

$id = $data['id'];

$record = $CONNECTION->Select("equipment_details", ["id" => $id], false);
$equipment = $CONNECTION->Select("equipment_info", ["id" => $record['equipment_id']], false);

$QR = new QrCode();

$isUser = $_SESSION['user_type'] == "student";

?>

<div class="main-popup-container">
    <div class="popup-background"></div>
    <div class="popup-content">
        <div class="main-popup-content">
            <div class="popup-top">
                <div class="headline">
                    <h1>Get Item</h1>
                </div>
                <div class="paragraph">
                    <p>Input Information</p>
                </div>

                <div class="floating-button">
                    <div class="close-popup popup-button">
                        <img src="pictures/close.svg"/>
                    </div>
                </div>
            </div>
            <form class="">
                <div class="popup-bot">
                    <div class="qr-code-container">
                        <?php
                        $out = $QR->render($record['qr_key']);
                        echo '<img src="'. $out .'" alt="QR Code" />';
                        ?>
                    </div>
                    <div class="form-group">
                        <label for="location">Equipment Name</label>
                        <input type="text" value="<?= $equipment['name'] ?>" readonly />
                    </div>
                    <div class="form-group">
                        <label for="location">Location</label>
                        <input type="text" id="location" placeholder="Enter location" value="<?php echo $record['location'] ?>" <?= $isUser ? "readonly" : ""?> />
                    </div>
                    <div class="form-group">
                        <label for="serials">Serial Number</label>
                        <input type="text" id="serials"  placeholder="Enter serial number"  value="<?php echo $record['serials'] ?>"  <?= $isUser ? "readonly" : ""?> />
                    </div>

                    <div class="material-content">
                        <div class="form-group">
                            <label for="price">Quantity</label>
                            <input type="number" id="quantity" placeholder="Enter Quantity"  name="quantity" value="<?= $record['quantity'] ?>" required/>
                        </div>
                    </div>
                </div>
                <div class="popup-footer">
                    <button type="submit" class="get-item">Get Item</button>
                </div>
            </form>
        </div>
    </div>
</div>