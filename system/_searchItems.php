<?php

include_once "./libraries/vendor/autoload.php";
include_once "./libraries/vendor/chillerlan/php-qrcode/src/QrCode.php";
include_once "./includes/Connection.php";


use chillerlan\QRCode\Output\QRGdImagePNG;
use chillerlan\QRCode\QRCode;

if (!isset($_SESSION['user_id'])) {
    session_start();
}
$CONNECTION = new Connection();

$user_id = $_SESSION['user_id'];
$user = $CONNECTION->Select("user", ["id" => $user_id], false);

$user_type = $user['user_type']; // student, instructor, admin
$course = $user['course'];

$QR = new QrCode();

$search = $_POST['search'];

$query = "SELECT ed.*, ec.category, ec.name, ec.picture, ec.course
          FROM equipment_details ed
          LEFT JOIN equipment_info ec ON ed.equipment_id = ec.id
          WHERE (ec.name LIKE :search 
             OR ec.category LIKE :search 
             OR ed.serials LIKE :search 
             OR ed.location LIKE :search)
          AND ed.deleted = '0'";

// Add course restriction for students and instructors
if ($user_type == 'student' || $user_type == 'instructor') {
    $query .= " AND ec.course = :course";
}

$stmt = $CONNECTION->CONNECTION->prepare($query);
$stmt->bindValue(':search', '%' . $search . '%', PDO::PARAM_STR);

if ($user_type == 'student' || $user_type == 'instructor') {
    $stmt->bindValue(':course', $course, PDO::PARAM_STR);
}

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
