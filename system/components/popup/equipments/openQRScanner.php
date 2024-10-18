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
                    <p>Upload / Drop QR Code</p>
                </div>

                <div class="floating-button">
                    <div class="close-popup popup-button">
                        <img src="pictures/close.svg"/>
                    </div>
                </div>
            </div>
            <form>
                <div class="popup-bot">
                    <div class="qr-code-container">
                        <div id="reader" width="400px" height="400px"></div>
                    </div>
                    <div class="qr-scanner-area" id="qr-scanner-area">
                        <input type="file" id="upload-qrcode" accept="image/*" hidden/>
                    </div>
                    <div id="drop_zone">
                        <p>Drag your QR code in this <i>drop zone</i>.</p>
                    </div>
                </div>
            </form>
            <div class="popup-footer">
                <button class="open-camera"><span>Open Camera</span></button>
            </div>
        </div>
    </div>
</div>