<?php
header('Content-Type: application/json');

include "./includes/db_connection.php";

// Check if the form is submitted via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = isset($_POST['name']) ? $conn->real_escape_string($_POST['name']) : '';
    $alert_level = isset($_POST['alert_level']) ? (int)$_POST['alert_level'] : 0;
    $description = isset($_POST['description']) ? $conn->real_escape_string($_POST['description']) : '';
    $price = isset($_POST['price']) ? (float)$_POST['price'] : 0.0;
    $category = isset($_POST['category']) ? $conn->real_escape_string($_POST['category']) : '';
    $course = isset($_POST['course']) ? $conn->real_escape_string($_POST['course']) : '';

    // Handle file upload
    if (isset($_FILES['picture']) && $_FILES['picture']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['picture']['tmp_name'];
        $fileName = $_FILES['picture']['name'];
        $fileSize = $_FILES['picture']['size'];
        $fileType = $_FILES['picture']['type'];
        $fileNameCmps = explode(".", $fileName);
        $fileExtension = strtolower(end($fileNameCmps));
        
        // Define allowed file extensions and size
        $allowedExts = ['jpg', 'jpeg', 'png'];
        $maxFileSize = 5 * 1024 * 1024; // 5 MB

        if (in_array($fileExtension, $allowedExts) && $fileSize <= $maxFileSize) {
            // Directory where the file will be uploaded
            $uploadDir = 'uploads/';
            $newFileName = md5(time() . $fileName) . '.' . $fileExtension;
            $uploadFileDir = $uploadDir . $newFileName;

            if (move_uploaded_file($fileTmpPath, $uploadFileDir)) {
                // Prepare SQL statement
                $stmt = $conn->prepare("INSERT INTO equipment_info (name, alert_level, description, price, picture, category, course) VALUES (?, ?, ?, ?, ?, ?, ?)");
                $stmt->bind_param("sisssss", $name, $alert_level, $description, $price, $newFileName, $category, $course);

                if ($stmt->execute()) {
                    echo json_encode(['success' => true, 'message' => 'Equipment added successfully']);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Failed to add equipment: ' . $stmt->error]);
                }

                $stmt->close();
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to move uploaded file']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Invalid file type or size']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'No file uploaded or upload error']);
    }

    $conn->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
?>
