<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Lost Equipment History</title>
    <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css"
    />
    <style>
      body {
        font-family: "Arial", sans-serif;
        margin: 0;
        padding: 0;
        background: url("logo/BG.jpg") no-repeat center center fixed;
        background-size: cover;
        color: #333;
      }

      /* Navbar Styles */
      .navbar {
        position: fixed;
        top: 0;
        width: 100%;
        background-color: #2980b9;
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 15px 25px;
        z-index: 1000;
      }

      .navbar button {
        font-size: 1.2rem;
        background-color: transparent;
        color: white;
        border: none;
        cursor: pointer;
      }

      .navbar-logo {
        font-size: 1.5rem;
        font-weight: bold;
        color: white;
      }

      /* Admin Profile */
      .admin-container {
        display: flex;
        align-items: center;
      }

      .admin-container p {
        margin-right: 15px;
        color: white;
        font-weight: bold;
      }

      .profile-picture {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        object-fit: cover;
        cursor: pointer;
        transition: 0.3s ease;
        margin-right: 50px;
      }

      .profile-picture:hover {
        transform: scale(1.1);
      }

      /* Dropdown Menu */
      .dropdown-content {
        display: none;
        position: absolute;
        background-color: #f9f9f9;
        min-width: 120px;
        box-shadow: 0 8px 16px rgba(0, 0, 0, 0.3);
        z-index: 1001;
        right: 10px;
        top: 60px;
        border-radius: 5px;
      }

      .dropdown-content a {
        color: #333;
        padding: 12px 16px;
        text-decoration: none;
        display: block;
        transition: background-color 0.2s ease;
      }

      .dropdown-content a:hover {
        background-color: #e0e0e0;
      }

      .show {
        display: block;
      }

      /* Sidebar Styles */
      .menu {
        position: fixed;
        top: 85px;
        width: 260px;
        height: 100%;
        background-color: #2980b9;
        transition: left 0.3s ease;
        z-index: 999;
      }

      .menu-header {
        padding: 15px;
        font-size: 1.2rem;
        font-weight: bold;
        color: white;
        text-align: center;
      }

      .menu ul {
        list-style-type: none;
        padding: 0;
        margin: 0;
      }

      .menu li {
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
      }

      .menu a {
        display: flex;
        align-items: center;
        color: white;
        padding: 15px;
        text-decoration: none;
        font-size: 1rem;
        transition: background-color 0.3s ease;
      }

      .menu a i {
        margin-right: 15px;
      }

      .menu a:hover {
        background-color: #1a73b6;
      }

      .menu-toggle {
        font-size: 1.4rem;
        background-color: #2980b9;
        color: white;
        border: none;
        cursor: pointer;
      }

      /* Content Styles */
      .container {
        width: 80%;
        max-width: 900px;
        margin: 100px auto 40px;
        background-color: rgba(255, 255, 255, 0.9);
        border-radius: 12px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
        padding: 25px;
      }

      h1 {
        color: #2c3e50;
        font-size: 1.8rem;
        margin: 0;
      }

      table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 20px;
        font-size: 1rem;
      }

      th,
      td {
        padding: 14px 16px;
        text-align: left;
        border-bottom: 2px solid #ddd;
      }

      th {
        background-color: #3498db;
        color: white;
        font-weight: 600;
      }

      tr:nth-child(even) {
        background-color: #f9f9f9;
      }

      tr:hover {
        background-color: #f1f1f1;
      }
      /* Hide elements by default */
#menu a[id$="-admin"], 
#menu a[id$="-user"], 
#add-new-btn, 
#add-existing-btn, 
#open-scanner {
    display: none;
}
    </style>
  </head>

  <body>
    <!-- Navbar -->
    <div class="navbar">
      <button class="menu-toggle" onclick="toggleMenu()">☰</button>
      <div class="admin-container">
        <p id="user-name" class="mr-3">2023-51123</p>
        <img id="profile-picture" src="uploads/profile.jpg" alt="Profile Picture" class="profile-picture" onclick="toggleDropdown()" style="margin-top: -1px;">
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

    <!-- Sidebar Menu -->
    <?php include_once("./includes/menu.php") ?>


    <!-- Main Container -->
    <div class="container">
      <h1>Lost Equipment History</h1>
      <!-- Table -->
      <table id="borrowedItems">
        <thead>
          <tr>
            <th>Name</th>
            <th>Equipment</th>
            <th>Location</th>
            <th>DateTime Borrowed</th>
          </tr>
        </thead>
        <tbody>
          <!-- Data dynamically filled from the database -->
        </tbody>
      </table>
    </div>

    <!-- JavaScript -->
    <script>
        function adjustUIForUserType(userType) {
  // For admin users, show elements with '-admin' and hide non-admin elements
  const adminLinks = document.querySelectorAll('#menu a[id$="-admin"]');
  const nonAdminLinks = document.querySelectorAll('#menu a:not([id$="-admin"])');

  adminLinks.forEach((link) => {
    link.style.display = userType === "admin" ? "block" : "none";
  });

  nonAdminLinks.forEach((link) => {
    link.style.display = userType !== "admin" ? "block" : "none";
  });

}
      // Function to toggle dropdown visibility
      function toggleDropdown() {
        var dropdownContent = document.getElementById("dropdown-content");
        dropdownContent.classList.toggle("show");
      }

      // Function to toggle slide-in menu visibility
      function toggleMenu() {
        var menu = document.getElementById("menu");
        var menuLeft = menu.style.left === "0px" ? "-300px" : "0px";
        menu.style.left = menuLeft;
      }

      // Function to fetch borrowed items from the database
      function fetchBorrowedItems() {
        fetch("fetch_borrowed_items.php")
          .then((response) => response.json())
          .then((data) => {
            const tableBody = document.querySelector("#borrowedItems tbody");
            tableBody.innerHTML = "";

            data.forEach((item) => {
              if (item.status === "lost") {
                const row = document.createElement("tr");
                row.innerHTML = `
                                <td>${item.first_name} ${
                  item.middle_name ? item.middle_name : ""
                } ${item.last_name} ${item.suffix ? item.suffix : ""}</td>
                                <td>${item.item_to_borrow}</td>
                                <td>${item.locations}</td>
                                <td>${item.borrow_datetime}</td>
                            `;
                tableBody.appendChild(row);
              }
            });
          })
          .catch((error) => console.error("Error:", error));
      }

      // Fetch data when page loads
      window.onload = function () {
        fetchBorrowedItems();
      };
      document.addEventListener("DOMContentLoaded", function() {
      // Fetch user data and adjust UI
      fetch("fetch_user.php")
        .then((response) => response.json())
        .then((data) => {
          if (data.error) {
            console.error("Error:", data.error);
            return;
          }
          document.getElementById("user-name").textContent = data.username;
          document.getElementById("profile-picture").src =
            "uploads/" + data.profile_picture;
          adjustUIForUserType(data.user_type);
        })
        .catch((error) => console.error("Error fetching user data:", error));
    });
    </script>
  </body>
</html>
