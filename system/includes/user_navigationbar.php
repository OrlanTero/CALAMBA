
<?php

if (!isset($CONNECTION)) {
    $CONNECTION = new Connection();
}

if (!isset($_SESSION['user_id'])) {
    session_start();
}

$user = $CONNECTION->Select("user", ["id" => $_SESSION['user_id']], false);
?>

<head>
    <!-- Optional: Bootstrap Icons (if you want to use icons) -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <!-- jQuery (Optional, if you're using Bootstrap's JavaScript components that require jQuery) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<style>
    .dropdown-content {
        display: none;
        position: absolute;
        background-color: #ffffff;
        min-width: 160px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        border-radius: 8px;
        z-index: 1;
        right: 0;
        top: 50px;
        transition: all 0.3s ease;
        padding: 10px 0;
    }

    .dropdown-content.show {
        display: block;
        /* Show dropdown when class 'show' is added */
    }

    .dropdown-content a {
        color: #333;
        padding: 12px 20px;
        text-decoration: none;
        display: flex;
        align-items: center;
    }

    .dropdown-content a i {
        margin-right: 10px;
    }

    .dropdown-content a:hover {
        background-color: #e0e0e0;
        color: #007bff;
        transition: background-color 0.2s ease;
    }

    .dropdown-content a.active {
        background-color: #007bff;
        color: white;
    }

    /* .navbar {
        position: relative;
        /* Set to relative for dropdown positioning */
    } */

    .profile-picture {
        width: 40px;
        height: 40px;
        border-radius: 50%;
    }

    .admin-container {
        display: flex;
        align-items: center;
        /* Vertically center items */
    }

    #user-name {
        margin: 0;
        /* Remove default margin */
        line-height: 40px;
        /* Match the height of the profile picture */
    }
</style>
<div class="navbar">
    <button
        class="menu-toggle btn btn-outline-secondary"
        onclick="toggleMenu()">
        <img
            src="logo/logo.jpg"
            alt="Profile Picture"
            class="profile-picture" />
    </button>
    <div class="admin-container">
        <span>
            <p id="user-name" class="mr-3"><?= $user['last_name'] . ', '  . $user['first_name']?></p>
        </span>
        <img
            id="profile-picture"
            src="./uploads/<?= $user['profile_picture'] ?>"
            alt="Profile Picture"
            class="profile-picture"
            onclick="toggleDropdown()"
            style="margin-top: -1px;" />
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
<script>

    // Toggle Dropdown Menu
    function toggleDropdown() {
        document.getElementById("dropdown-content").classList.toggle("show");
    }

    // Close dropdown if clicked outside
    window.onclick = function(event) {
        if (!event.target.matches('.profile-picture')) {
            const dropdowns = document.getElementsByClassName("dropdown-content");
            for (let i = 0; i < dropdowns.length; i++) {
                const openDropdown = dropdowns[i];
                if (openDropdown.classList.contains('show')) {
                    openDropdown.classList.remove('show');
                }
            }
        }
    }

    // Toggle Slide-in Menu
    function toggleMenu() {
        const menu = document.querySelector(".custom-menu");

        if (!menu.classList.contains("hide") && !menu.classList.contains("show")) {
            menu.classList.add("hide");
        } else if (menu.classList.contains("hide")) {
            menu.classList.add("show");
            menu.classList.remove("hide");
        } else {
            menu.classList.add("hide");
            menu.classList.remove("show");
        }
    }

    function checkResizeMenu() {
        const menu = document.querySelector(".custom-menu");

        if (window.innerWidth <= 930) {
            menu.classList.add("hide");
            menu.classList.remove("show");
        }
    }

    window.onresize = function () {
        checkResizeMenu();
    }

    document.addEventListener("DOMContentLoaded", function () {
        checkResizeMenu();

    })
</script>