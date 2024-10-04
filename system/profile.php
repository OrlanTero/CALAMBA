<?php
session_start();

include "./includes/db_connection.php";
include "./includes/sessions.php";

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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <!-- Bootstrap CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">

    <!-- Optional: Bootstrap Icons (if you want to use icons) -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">

    <!-- jQuery (Optional, if you're using Bootstrap's JavaScript components that require jQuery) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- Bootstrap Bundle (includes Popper) -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }

        .container {
            width: 60%;
            margin: 0 auto;
            padding: 20px;
            background-color: #fff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            margin-top: 50px;
        }

        .profile-picture {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid #ddd;
        }

        input[type="file"] {
            margin-top: 10px;
        }

        .button {
            background-color: #2980b9;
            color: #fff;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
        }

        .button:hover {
            background-color: #3498db;
        }

        .form-group {
            margin-bottom: 15px;

        }

        .navbar {
            position: fixed;
            top: 0;
            left: 0;
            z-index: 1000;
            width: 100%;
            padding: 10px;
            background-color: #2980b9;
            /* Navbar background color */
            display: flex;
            justify-content: space-between;
            /* Align items to both ends */
            align-items: center;
            padding-bottom: 50px;
        }

        .admin-container {
            position: absolute;
            right: 20px;
            top: 10px;
            display: flex;
            align-items: center;
            padding-right: 20px;
            /* Add padding to the right */
        }

        .admin-container p {
            margin-right: 10px;
            margin-bottom: 10px;
            color: white;
            font-weight: bold;
        }

        .profile-picture {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
            cursor: pointer;
            margin-left: 10px;
            /* Adds some space between the name and picture */
        }

        .profile-picture {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
            cursor: pointer;
            margin-left: 10px;
            /* Adds some space between the name and picture */
        }

        .preview-image {
            max-width: 100%;
            /* Allow the image to scale down */
            height: auto;
            /* Maintain aspect ratio */
            border-radius: 5px;
            /* Circular shape */
            object-fit: cover;
            /* Ensures the image covers the element without distortion */
            cursor: pointer;
        }

        .dropdown-content {
            display: none;
            position: absolute;
            background-color: #ffffff;
            /* Changed to white for better contrast */
            min-width: 160px;
            /* Increased width */
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            border-radius: 8px;
            /* Rounded corners */
            z-index: 1;
            right: 0;
            top: 50px;
            transition: all 0.3s ease;
            /* Smooth transition */
            padding: 10px 0;
            /* Added padding for better spacing */
        }

        .dropdown-content a {
            color: #333;
            /* Darker color for better readability */
            padding: 12px 20px;
            /* More padding for better click area */
            text-decoration: none;
            display: flex;
            /* Use flexbox for icon and text alignment */
            align-items: center;
            /* Center align icon and text */
        }

        .dropdown-content a i {
            margin-right: 10px;
            /* Space between icon and text */
        }

        .dropdown-content a:hover {
            background-color: #e0e0e0;
            /* Subtle hover effect */
            color: #007bff;
            /* Change text color on hover */
            transition: background-color 0.2s ease;
            /* Smooth transition */
        }

        .dropdown-content a.active {
            background-color: #007bff;
            /* Highlight active item */
            color: white;
            /* Change text color for active item */
        }

        .show {
            display: block;
        }

        .menu {
            position: fixed;
            top: 60px;
            left: 0;
            width: 300px;
            height: 100%;
            background-color: #2980b9;
            transition: left 0.3s ease;
            z-index: 1000;
        }

        .menu a {
            display: block;
            color: white;
            padding: 20px;
            text-decoration: none;
            border-bottom: 1px solid #555;
            transition: background-color 0.3s ease;
        }

        .menu a:hover {
            background-color: #2471a3;
        }

        .menu-toggle {
            position: fixed;
            top: 10px;
            left: 10px;
            background-color: #2980b9; /* Original background color */
            color: white; /* Original text color */
            border: none;
            padding: 10px;
            cursor: pointer;
            z-index: 1001;
            transition: background-color 0.3s ease, color 0.3s ease; /* Smooth transition */
        }

        .menu-toggle:hover {
            background-color: #3498db; /* Change background color on hover */
            color: #ecf0f1; /* Change text color on hover */
        }
        
        .centered-container {
            display: flex;
            align-items: center; /* Vertically center */
            justify-content: center; /* Optionally horizontally center */
        }

        #user-name {
            margin: 0; /* Remove default margin */
        }
    </style>
</head>
<div class="navbar">
    <button class="menu-toggle" onclick="toggleMenu()">
        <i class="fas fa-bars"></i> Menu
    </button>
    <div class="admin-container">
        <div class="centered-container">
            <p id="user-name"><?php echo htmlspecialchars($userData['first_name']); ?></p>
        </div>
        <img src="uploads/<?php echo htmlspecialchars($userData['profile_picture']); ?>" alt="Profile Picture" class="profile-picture" onclick="toggleDropdown()">
        <div class="dropdown-content" id="dropdown-content">
            <a href="profile.php" class="dropdown-item">
                <i class="fas fa-user"></i> Profile
            </a>
            <a href="../index.php" class="dropdown-item">
                <i class="fas fa-sign-out-alt"></i> Logout
            </a>
        </div>
    </div>
</div>

<!-- Slide-in Menu -->
<div class="menu" id="menu">
    <a href="dashboard.html">Dashboard</a>
    <a href="inventory.html" id="inventory-link">Add Equipment/Item</a>
    <a href="catalog.html">List of Equipment</a>
    <a href="borrow.html" id="borrow-link">Borrowed Equipment History</a>
    <a href="returned.html" id="returned-link">Returned Equipment History</a>
    <a href="not_returned.html" id="not-returned-link">Not Returned Equipment History</a>
    <a href="lost.html" id="lost-link">Lost Equipment History</a>
    <a href="damaged.html" id="damaged-link">Damaged Equipment History</a>
</div>
<div class="container text-center" style="margin-top: 100px;">
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
    <script>
        function toggleDropdown() {
            var dropdownContent = document.getElementById("dropdown-content");
            dropdownContent.classList.toggle("show");
        }

        function toggleMenu() {
            var menu = document.getElementById("menu");
            var menuLeft = menu.style.left === "0px" ? "-300px" : "0px";
            menu.style.left = menuLeft;
        }

        function adjustUIForUserType(isAdmin) {
            const historyLinks = document.querySelectorAll('#menu a[id$="-link"]');
            historyLinks.forEach(link => link.style.display = isAdmin ? 'block' : 'none');
            const inventoryLink = document.getElementById('inventory-link');
            if (isAdmin) {
                inventoryLink.style.display = 'block';
            } else {
                inventoryLink.style.display = 'none';
            }
        }

        // Initialize UI based on user type
        document.addEventListener('DOMContentLoaded', function() {
            adjustUIForUserType(<?php echo json_encode($isAdmin); ?>);
        });
    </script>
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