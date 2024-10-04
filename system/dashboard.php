<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-image: url("logo/BG.jpg");
            background-size: cover;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            overflow-x: hidden;
        }
        .navbar {
            position: fixed;
            top: 0;
            left: 0;
            z-index: 1000;
            display: flex;
            align-items: center;
            justify-content: space-between;
            width: 100%;
            padding: 10px;
            background-color: #2980b9;
        }
        .navbar-logo {
            width: 80px;
            margin-left: 20px;
        }
        .admin-container {
            display: flex;
            align-items: center;
            margin-right: 20px;
        }
        .admin-container p {
            margin-right: 10px;
            color: white;
            font-weight: bold;
        }
        .profile-picture {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
            cursor: pointer;
        }
        .dropdown-content {
            display: none;
            position: absolute;
            background-color: #f9f9f9;
            min-width: 120px;
            box-shadow: 0 8px 16px 0 rgba(0,0,0,0.2);
            z-index: 1;
            right: 20px;
            top: 60px;
        }
        .dropdown-content a {
            color: black;
            padding: 12px 16px;
            text-decoration: none;
            display: block;
        }
        .dropdown-content a:hover {
            background-color: #f1f1f1;
        }
        .show {
            display: block;
        }
        .container-wrapper {
            display: flex;
            width: 100%;
            margin-top: 60px;
            margin-left: 350px;
        }
        .container {
            width: calc(100% - 300px);
            background-color: lightblue;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            padding: 20px;
            text-align: center;
            height: 700px;
            overflow-y: auto;
        }
        h1 {
            color: #333;
            margin-top: 0;
        }
        .dashboard-content {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            margin-top: 20px;
        }
        .dashboard-square {
            background-color: #3498db;
            color: #fff;
            border-radius: 8px;
            padding: 20px;
            text-align: center;
        }
        .dashboard-square span {
            font-weight: bold;
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
        /* Hide elements by default */
#menu a[id$="-admin"], 
#menu a[id$="-user"], 
#add-new-btn, 
#add-existing-btn, 
#open-scanner,
#alert-section,
#total-borrowed{
    display: none;
}
    </style>
</head>
<body>
<div class="navbar">
    <img src="logo/logo.jpg" alt="Logo" class="navbar-logo" onclick="toggleMenu()">
    <div class="admin-container">
        <p id="user-name"></p>
        <img id="profile-picture" src="logo/logo.jpg" alt="Profile Picture" class="profile-picture" onclick="toggleDropdown()">
        <div class="dropdown-content" id="dropdown-content">
            <a href="profile.php">Profile</a>
            <a href="../index.php">Logout</a>
        </div>
    </div>
</div>

<div class="container-wrapper">
    <div class="container" id="container">
        <h1>Dashboard</h1>
        <div class="dashboard-content">
            <!-- Dashboard content -->
            <div class="dashboard-square" id="alert-section">
                <p>Equipment Reaching Alert Level: <span id="alert-count">0</span></p>
            </div>
            <div class="dashboard-square" id="total-borrowed">
                <p>Total Equipment Borrowed: 1</p>
            </div>
        </div>
    </div>
</div>

<?php include_once("./includes/menu.php") ?>

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

   function adjustUIForUserType(userType) {
  // Select elements for admin, student/instructor
  const adminLinks = document.querySelectorAll('#menu a[id$="-admin"]');
  const studentInstructorLinks = document.querySelectorAll('#menu a[id$="-user"]');
  document.getElementById("alert-section").style.display =
    userType === "admin" ? "inline-block" : "none";

  // Fix the missing quote and show/hide total-borrowed element based on userType
  document.getElementById("total-borrowed").style.display = (userType === "student" || userType === "instructor") 
    ? "inline-block" 
    : "none";
  
  if (userType === "admin") {
    // Show admin-specific content
    adminLinks.forEach((link) => link.style.display = "block");
  } else if (userType === "student" || userType === "instructor") {
    // Show student/instructor-specific content
    studentInstructorLinks.forEach((link) => link.style.display = "block");
  }
}



    function processEquipment(data) {
        // Log the data to inspect its structure
        console.log('Data received:', data);

        // Check if the data contains an array of equipment
        if (data && data.success === true && Array.isArray(data.equipment)) {
            // Reset the alert count
            let alertCount = 0;

            // Process each item in the data
            data.equipment.forEach(item => {
                // Parse integer values
                const alertLevel = parseInt(item.alert_level, 10);
                const available = parseInt(item.available, 10);

                // Check if the equipment reaches or exceeds the alert level
                if (alertLevel >= available) {
                    // Increment the alert count
                    alertCount++;
                }
            });

            // Update the alert count display
            document.getElementById('alert-count').textContent = alertCount;
        } else {
            console.error('Unexpected data format or error:', data);
        }
    }

    function fetchEquipment() {
        fetch('get_equipment.php')
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                processEquipment(data);
            })
            .catch(error => {
                console.error('Fetch error:', error);
                // Handle error case
            });
    }

    document.addEventListener('DOMContentLoaded', function() {
        fetchEquipment(); // Fetch equipment data on page load

        // Fetch user data and adjust UI
        fetch('fetch_user.php')
            .then(response => response.json())
            .then(data => {
                if (data.error) {
                    console.error('Error:', data.error);
                    return;
                }
                document.getElementById('user-name').textContent = data.username;
                document.getElementById('profile-picture').src = 'uploads/' + data.profile_picture;
                adjustUIForUserType(data.user_type);
            })
            .catch(error => console.error('Error fetching user data:', error));
    });
</script>
</body>
</html>
