<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Borrow History</title>
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
            max-width: 1000px;
            margin: 80px auto 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            padding: 20px;
            padding-left: 20px;
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
            cursor: pointer; 
        }
        tr:nth-child(even) {
            background-color: #f2f2f2;
        }
        .navbar {
            position: fixed;
            top: 0;
            left: 0;
            z-index: 1000;
            width: 100%;
            padding: 10px;
            background-color: #2980b9;
            display: flex;
            justify-content: space-between;
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
            background-color: #2980b9;
            color: white;
            border: none;
            padding: 10px;
            cursor: pointer;
            z-index: 1001;
        }
        .modal {
    display: none; /* Hidden by default */
    position: fixed; /* Stay in place */
    z-index: 1000; /* Sit on top */
    left: 0;
    top: 0;
    width: 100%; /* Full width */
    height: 100%; /* Full height */
    overflow: auto; /* Enable scroll if needed */
    background-color: rgba(0, 0, 0, 0.4); /* Black w/ opacity */
}

.modal-content {
    background-color: #fefefe;
    margin: 15% auto; /* 15% from the top and centered */
    padding: 20px;
    border: 1px solid #888;
    width: 30%; 
    display:float;
}

.close {
    color: #aaa;
    float: right;
    font-size: 28px;
    font-weight: bold;
}

.close:hover,
.close:focus {
    color: black;
    text-decoration: none;
    cursor: pointer;
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
    <?php include "./includes/user_navigationbar.php"; ?>

    <div class="menu" id="menu">
        <a href="dashboard.php" id="dashboard">Dashboard</a>
        <a href="catalog.php">List of Equipment</a>
        <a href="borrow.php"id="for-admin">Borrowed Equipment History</a>
        <a href="returned.php"id="for-admin">Returned Equipment History</a>
        <a href="not_returned.php"id="for-admin">Not Returned Equipment History</a>
        <a href="lost.php"id="for-admin">Lost Equipment History</a>
        <a href="damaged.php"id="for-admin">Damaged Equipment History</a>
        <a href="borrow_history.php">Borrow History</a>
    </div>

    <div class="container">
        <h1>Borrow History</h1>

        <table id="borrowedItems">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Phone</th>
                    <th>Equipment</th>
                    <th>Serial Number</th>
                    <th>Location</th>
                    <th>Date Received</th>
                    <th>Date Borrowed</th>
                    <th>Status</th>
                    <th>QR Code</th>
                </tr>
            </thead>
            <tbody>
                <!-- Data will be filled dynamically from the database -->
            </tbody>
        </table>
    </div>
        <!-- Modal Structure -->
<div id="qrModal" class="modal">
    <div class="modal-content">
        <span class="close" id="modalClose">&times;</span>
        <h2>QR Code</h2>
        <img id="modalImage" src="" alt="QR Code" style="width: 300px; height: 300px;"/>
        <a id="downloadLink" href="#" download style="display: inline-block; margin-top: 10px;">Download QR Code</a>
    </div>
</div>

    <script>
        function adjustUIForUserType(userType) {
            const historyLinks = document.querySelectorAll('#menu a[id$="-admin"]');
            historyLinks.forEach(link => link.style.display = userType === 'admin' ? 'block' : 'none');
        }

        function toggleDropdown() {
            var dropdownContent = document.getElementById("dropdown-content");
            dropdownContent.classList.toggle("show");
        }

        function toggleMenu() {
            var menu = document.getElementById("menu");
            var menuLeft = menu.style.left === "0px" ? "-300px" : "0px";
            menu.style.left = menuLeft;
        }

          function fetchBorrowedItems() {
    fetch('borrower_data.php')
        .then(response => response.json())
        .then(data => {
            if (!data.success) {
                console.error('Error fetching data:', data.message);
                return;
            }

            const tableBody = document.querySelector("#borrowedItems tbody");
            tableBody.innerHTML = '';

            data.data.forEach(item => {
                const row = document.createElement("tr");
                row.innerHTML = `
                    <td>${item.user_name}</td>
                    <td>${item.user_phone}</td>
                    <td>${item.equipment_name}</td>
                    <td>${item.serial_number}</td>
                    <td>${item.location}</td>
                    <td>${item.date_received}</td>
                    <td>${item.borrow_datetime}</td>
                    <td>${item.status}</td>
                    <td>
                        <img src="${item.qr_code_image}" alt="QR Code" style="width: 100px; height: 100px;" class="qr-code" data-url="${item.qr_code_image}"/>
                    </td>
                `;
                tableBody.appendChild(row);
            });

            // Add click event to QR Code images
            document.querySelectorAll('.qr-code').forEach(img => {
                img.addEventListener('click', function() {
                    const qrImageUrl = this.dataset.url;
                    document.getElementById('modalImage').src = qrImageUrl;
                    document.getElementById('downloadLink').href = qrImageUrl;
                    document.getElementById('qrModal').style.display = 'block'; // Show the modal
                });
            });
        })
        .catch(error => console.error('Error:', error));
}

// Close the modal
document.getElementById('modalClose').onclick = function() {
    document.getElementById('qrModal').style.display = 'none'; // Hide the modal
}

// Close the modal when clicking outside of it
window.onclick = function(event) {
    const modal = document.getElementById('qrModal');
    if (event.target === modal) {
        modal.style.display = 'none'; // Hide the modal
    }
}

window.onload = function() {
    fetchBorrowedItems(); // Fetch the borrowed items on page load
};


    
        document.addEventListener('DOMContentLoaded', function() {
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
