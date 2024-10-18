<?php 

include_once "./../../../libraries/vendor/autoload.php";
include_once "./../../../includes/Connection.php";
include_once "./../../../libraries/vendor/chillerlan/php-qrcode/src/QrCode.php";

session_start();

use chillerlan\QRCode\QRCode;

$CONNECTION = new Connection();

?>

<div class="main-popup-container">
    <div class="popup-background"></div>
    <div class="popup-content">
        <div class="main-popup-content">
            <div class="popup-top">
                <div class="headline">
                    <h1>Scan QR</h1>
                </div>
                <div class="paragraph">
                    <p>Please scan your QR using Webcam / Camera</p>
                </div>

                <div class="floating-button">
                    <div class="close-popup popup-button">
                        <img src="pictures/close.svg"/>
                    </div>
                </div>
            </div>
            <form>
                <div class="popup-bot">
                    <div id="scanner"></div>
                </div>
            </form>
            <div class="popup-footer">
            </div>
        </div>
    </div>
</div>