<?php

include_once "./libraries/vendor/autoload.php";
include_once "./libraries/vendor/chillerlan/php-qrcode/src/QrCode.php";
include_once "./includes/Connection.php";


use chillerlan\QRCode\Output\QRGdImagePNG;
use chillerlan\QRCode\QRCode;


$QR = new QrCode();


$CONNECTION = new Connection();

$search = $_POST['search'];

$query = "SELECT ed.*, ec.category, ec.name, ec.picture
          FROM equipment_details ed
          LEFT JOIN equipment_info ec ON ed.equipment_id = ec.id
          WHERE (ec.name LIKE :search 
             OR ec.category LIKE :search 
             OR ed.serials LIKE :search 
             OR ed.location LIKE :search)
          AND ed.deleted = '0'";

$stmt = $CONNECTION->CONNECTION->prepare($query);
$stmt->bindValue(':search', '%' . $search . '%', PDO::PARAM_STR);
$stmt->execute();

$results = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<?php foreach($results as $result): ?>
    <div class="search-result" data-id="<?php echo $result['id']; ?>" data-qr="<?php echo $result['qr_key'] ?? $result['serials']; ?>">
        <div class="qr-code">
            <?php
            $qrData = $result['qr_key'] ?? $result['serials'];
            $qrImage = $QR->render($qrData);
            echo '<img src="' . $qrImage . '" alt="QR Code" class="qr-image">';
            ?>
        </div>
        <?php if (!empty($result['picture'])): ?>
            <img src="uploads/<?php echo $result['picture']; ?>" alt="<?php echo $result['name']; ?>" class="result-image">
        <?php endif; ?>
        <div class="result-details">
            <h3><?php echo $result['name']; ?></h3>
            <p class="category"><?php echo $result['category']; ?></p>
            <p class="serials"><?php echo $result['serials']; ?></p>
            <p class="location"><?php echo $result['location']; ?></p>
        </div>
    </div>
<?php endforeach; ?>
