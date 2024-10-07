<?php
session_start();

include "./includes/db_connection.php";
include "./includes/sessions.php";
include_once "./includes/Connection.php";

// Fetch user data
$sql = "SELECT student_id, user_type, profile_picture, phone, course, first_name,last_name, course FROM user WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();
$userData = $result->fetch_assoc();

$stmt->close();
$conn->close();

// Determine if the user is an admin
$isAdmin = $userData['user_type'] === 'admin';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta
            name="viewport"
            content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <title>Equipment List</title>
    <link
            rel="stylesheet"
            href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" />

    <link rel="stylesheet" href="./style/styles.css">
    <link rel="stylesheet" href="./style/suchStyles.css">
</head>

<body>
<?php include "./includes/user_navigationbar.php"; ?>

<!-- Slide-in Menu -->

<?php include_once("./includes/menu.php") ?>

<div class="main-content-container text-center" style="margin-top: 100px;">
    <h1 class="text-start">Profile</h1>
    <form action="update_profile.php" method="post" enctype="multipart/form-data">
        <div class="row mb-3">
            <div class="col-12 col-md-4 text-center">
                <img src="uploads/<?php echo htmlspecialchars($userData['profile_picture']); ?>" alt="Profile Picture" class="img-fluid preview-image mb-3">
                <input type="file" name="profile_picture" class="form-control">
            </div>
            <div class="col-12 col-md-8">
                <div class="form-group row mb-3">
                    <label for="student_id" class="col-12 col-md-4 col-form-label">Student ID:</label>
                    <div class="col-12 col-md-8">
                        <input type="text" id="student_id" name="student_id" value="<?php echo htmlspecialchars($userData['student_id']); ?>" class="form-control" readonly>
                    </div>
                </div>

                <div class="form-group row mb-3">
                    <label for="student_name" class="col-12 col-md-4 col-form-label">Student Full Name:</label>
                    <div class="col-12 col-md-8">
                        <input type="text" id="student_name" name="student_name" value="<?php echo htmlspecialchars($userData['first_name'] . ' ' . $userData['last_name']); ?>" class="form-control" readonly>
                    </div>
                </div>

                <div class="form-group row mb-3">
                    <label for="student_phone" class="col-12 col-md-4 col-form-label">Student Phone Number:</label>
                    <div class="col-12 col-md-8">
                        <input type="text" id="student_phone" name="student_phone" value="<?php echo htmlspecialchars($userData['phone']); ?>" class="form-control" readonly>
                    </div>
                </div>

                <div class="form-group row mb-3">
                    <label for="user_type" class="col-12 col-md-4 col-form-label">User Type:</label>
                    <div class="col-12 col-md-8">
                        <input type="text" class="form-control" id="user_type" name="user_type" value="<?php echo htmlspecialchars($userData['user_type']); ?>" class="form-control" readonly>
                    </div>
                </div>

                <div class="form-group row mb-3">
                    <label for="course" class="col-12 col-md-4 col-form-label">Course:</label>
                    <div class="col-12 col-md-8">
                        <input type="text" id="course" name="course" value="<?php echo htmlspecialchars($userData['course']); ?>" class="form-control" readonly>
                    </div>
                </div>

                <div class="form-group row mb-3">
                    <div class="col-12 col-md-8 offset-md-4">
                        <button type="submit" class="btn btn-primary w-100">Update Profile</button>
                    </div>
                </div>
            </div>
        </div>
    </form>

</div>
<div class="container text-center">
    <h2 class="mb-4 text-start">Change Password</h2>
    <form action="change_password.php" method="post" class="p-4">
        <div class="form-group text-start mb-3">
            <label for="current_password" class="form-label">Current Password:</label>
            <input type="password" id="current_password" name="current_password" class="form-control" required>
        </div>
        <div class="form-group text-start mb-3">
            <label for="new_password" class="form-label">New Password:</label>
            <input type="password" id="new_password" name="new_password" class="form-control" required>
        </div>
        <div class="form-group text-start mb-4">
            <label for="confirm_new_password" class="form-label">Confirm New Password:</label>
            <input type="password" id="confirm_new_password" name="confirm_new_password" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary">Change Password</button>
    </form>
</div>

<style>
    .form-label {
        font-weight: bold;
    }

    .button:hover {
        background-color: #0056b3;
    }
</style>
</body>

</html>