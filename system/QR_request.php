<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QR Code Approval</title>
    <style>
        
        /* Hide elements by default */
#menu a[id$="-admin"], 
#menu a[id$="-user"], 
#add-new-btn, 
#add-existing-btn, 
#open-scanner {
    display: none;
}
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background: url('logo/BG.jpg') no-repeat center center fixed;
            background-size: cover;
        }

        .container {
            width: 80%;
            max-width: 800px;
            margin: 80px auto 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            padding: 20px;
        }

        h1 {
            color: #333;
            margin-top: 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th, td {
            border: 1px solid #ddd;
            padding: 12px;
            text-align: left;
        }

        th {
            background-color: #2980b9;
            color: #fff;
        }

        tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        .status-dropdown {
            width: 100%;
            padding: 5px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        .navbar {
            position: fixed;
            top: 0;
            left: 0;
            z-index: 1000;
            width: 100%;
            padding: 10px;
            background-color: #2980b9; /* Navbar background color */
            display: flex;
            justify-content: space-between; /* Align items to both ends */
            align-items: center;
            padding-bottom: 50px;
        }

        .admin-container {
            position: absolute;
            right: 20px;
            top: 10px;
            display: flex;
            align-items: center;
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
            right: 0;
            top: 50px;
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

        .menu {
            position: fixed;
            top: 60px; /* Adjusted top position to go below navbar */
            left: 0;
            width: 300px;
            height: 100%;
            background-color: #2980b9; /* Matched the color with the theme */
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
            background-color: #2471a3; /* Button hover background color */
        }

        .menu-toggle {
            position: fixed;
            top: 10px;
            left: 10px;
            background-color: #2980b9;
            color: white;
            border: none;
            padding: 10px;
            cursor: pointer;
            z-index: 1001;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <div class="navbar">
        <button class="menu-toggle" onclick="toggleMenu()">☰ Menu</button>
        
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

    

<!-- Slide-in Menu -->
<div class="menu" id="menu">
  <div class="menu-header">Navigation</div>
  <ul>
    <li><a href="dashboard.php" id="dashboard-admin"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
    <li><a href="catalog.php" id="list-admin"><i class="fas fa-list"></i> List of Equipment</a></li>
    <li><a href="borrow.php" id="borrow-admin"><i class="fas fa-history"></i> Borrowed Equipment History</a></li>
    <li><a href="returned.php" id="returned-admin"><i class="fas fa-undo"></i> Returned Equipment History</a></li>
    <li><a href="not_returned.php" id="not-returned-admin"><i class="fas fa-times"></i> Not Returned Equipment History</a></li>
    <li><a href="lost.php" id="lost-admin"><i class="fas fa-exclamation-circle"></i> Lost Equipment History</a></li>
    <li><a href="damaged.php" id="damaged-admin"><i class="fas fa-tools"></i> Damaged Equipment History</a></li>
    <li><a href="QR_request.php" id="borrower-admin"><i class="fas fa-paper-plane"></i> Request Borrowers</a></li>
    <li><a href="borrow_history.php" id="borrower-user"><i class="fas fa-history"></i> Borrow History</a></li>
  </ul>
</div>

    <!-- Main Content -->
    <div class="container">
        <h1>QR Code Approval</h1>

        <!-- QR code approvals table -->
        <table id="qrCodeApprovals">
            <thead>
                <tr>
                    <th>Borrower Name</th>
                    <th>QR Code Data</th>
                    <th>Date Scanned</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <!-- Data will be filled dynamically from the database -->
            </tbody>
        </table>
    </div>

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

        // Function to toggle dropdown menu visibility
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

        // Function to fetch QR code approvals and populate the table
        function fetchQRCodeApprovals() {
            fetch('fetch_qr_code_data.php')
            .then(response => response.json())
            .then(data => {
                const tableBody = document.querySelector("#qrCodeApprovals tbody");
                tableBody.innerHTML = '';

                data.items.forEach(item => {
                    const row = document.createElement("tr");
                    row.innerHTML = `
                        <td>${item.first_name} ${item.middle_name ? item.middle_name : ''} ${item.last_name} ${item.suffix ? item.suffix : ''}</td>
                        <td>${item.qr_code_data}</td>
                        <td>${item.date_scanned}</td>
                        <td>${item.status}</td>
                        <td>
                            <button onclick="updateStatus(${item.id}, 'approved')">Approve</button>
                            <button onclick="updateStatus(${item.id}, 'declined')">Decline</button>
                        </td>
                    `;
                    tableBody.appendChild(row);
                });
            })
            .catch(error => console.error('Error:', error));
        }

        // Function to update the status of a QR code
        function updateStatus(id, status) {
            fetch(status === 'approved' ? 'approve_qr_code.php' : 'decline_qr_code.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ id: id, status: status })
            })
            .then(response => response.json())
            .then(result => {
                if (result.success) {
                    fetchQRCodeApprovals(); // Refresh the table to show updated status
                } else {
                    console.error('Error:', result.error);
                }
            })
            .catch(error => console.error('Error:', error));
        }

        // Initialize data when the page loads
        window.onload = function() {
            fetchQRCodeApprovals();
        };
        
         // Initialize UI
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
