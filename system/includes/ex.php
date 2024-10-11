<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta
        name="viewport"
        content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <title>Equipment List</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.qrcode/1.0/jquery.qrcode.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/qrcodejs@1.0.0/qrcode.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/jsqr@1.4.0/dist/jsQR.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/html5-qrcode/minified/html5-qrcode.min.js"></script>
    <script src="https://unpkg.com/html5-qrcode/minified/html5-qrcode.min.js"></script>
    <link
        rel="stylesheet"
        href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" />

    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: white;
        }

        .container {
            width: 80%;
            max-width: 800px;
            margin: 80px auto 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            padding: 20px;
            background-color: #e8e4c9;
        }

        h1 {
            color: #333;
            margin-top: 0;
            text-align: center;
        }

        .item {
            width: 200px;
            border: 1px solid #ccc;
            border-radius: 5px;
            padding: 10px;
            margin: 10px;
            display: inline-block;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .item:hover {
            border-color: #999;
            box-shadow: 0 0 5px rgba(0, 0, 0, 0.1);
        }

        .item img {
            max-width: 100px;
            /* Changed max-width */
            max-height: 100px;
            /* Changed max-height */
            display: block;
            margin: auto;
            transition: all 0.3s ease;
        }

        .item p {
            text-align: center;
            margin-top: 5px;
            color: #666;
            font-size: 16px;
        }

        .item-description {
            display: none;
            background-color: #f9f9f9;
            padding: 10px;
            border-radius: 5px;
            margin-top: 5px;
        }

        .item-description p {
            color: #333;
            font-size: 14px;
            line-height: 1.6;
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

        /* Fixed position for the profile container */
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
            margin-top: -10px;
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
            display: block;
            padding: 15px 20px;
            color: white;
            text-decoration: none;
            transition: background-color 0.3s;
        }

        .menu a:hover {
            background-color: #2471a3;
        }

        /* Add the slide-in menu button styles */
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

        .container {
            width: 80%;
            max-width: 900px;
            margin: 100px auto 40px;
            background-color: rgba(255, 255, 255, 0.9);
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
            padding: 25px;
        }

        .buttons {
            display: flex;
            justify-content: flex-start;
            margin-bottom: 20px;
        }

        .buttons button {
            padding: 10px 20px;
            margin-right: 10px;
            background-color: #2980b9;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .buttons button:hover {
            background-color: #2471a3;
        }

        .form-container {
            display: none;
        }

        .form-container.active {
            display: block;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
        }

        .form-group input,
        .form-group textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        .submit-button {
            padding: 10px 20px;
            background-color: #2980b9;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .submit-button:hover {
            background-color: #2471a3;
        }

        /* Alert level styles */
        .green {
            background-color: lightgreen;
        }

        .yellow {
            background-color: yellow;
        }

        .red {
            background-color: lightcoral;
        }

        .equipment-list {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            /* Adjust the space between items */
            justify-content: center;
        }

        .item {
            width: 200px;
            border: 1px solid #ccc;
            border-radius: 5px;
            padding: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            background-color: #fff;
            /* Default background color */
            text-align: center;
            transition: background-color 0.3s;
        }

        .item img {
            max-width: 100%;
            height: auto;
            display: block;
            margin: 0 auto;
        }

        .item p {
            margin: 10px 0;
        }

        .borrow-button {
            background-color: #2980b9;
            color: white;
            border: none;
            padding: 10px;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .borrow-button:hover {
            background-color: #2471a3;
        }

        .catalog-list {
            margin-top: 20px;
        }

        .catalog-item {
            padding: 10px;
            border: 1px solid #ccc;
            margin-bottom: 10px;
        }

        /* General Button Styles */
        button {
            font-family: Arial, sans-serif;
            font-size: 16px;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s, color 0.3s;
        }

        /* Back Button Specific Styles */
        #back-button {
            background-color: #007bff;
            /* Bootstrap Primary Color (Blue) */
            color: #fff;
            /* White Text */
            margin: 10px 0;
            /* Margin for spacing */
            display: inline-block;
            /* Ensure it fits well in the container */
        }

        /* Hover State */
        #back-button:hover {
            background-color: #0056b3;
            /* Darker Blue for Hover */
            color: #e6e6e6;
            /* Slightly lighter text color on hover */
        }

        /* Focus State */
        #back-button:focus {
            outline: none;
            /* Remove default outline */
            box-shadow: 0 0 0 3px rgba(38, 143, 255, 0.5);
            /* Add custom focus outline */
        }

        #scanner-modal {
            display: none;
            /* Hidden by default */
            position: fixed;
            top: 10%;
            left: 10%;
            width: 80%;
            height: 80%;
            background: white;
            z-index: 1000;
            /* Ensure it is above other content */
        }

        #close-scanner {
            position: absolute;
            top: 10px;
            right: 10px;
            cursor: pointer;
            /* Ensure cursor indicates clickable area */
        }

        #download-btn {
            position: fixed;
            left: 50%;
            transform: translateX(-50%);
            bottom: 20px;
            background-color: #2980b9;
            color: white;
            padding: 10px 20px;
            border-radius: 5px;
            text-decoration: none;
            display: none;
            /* Initially hidden */
        }

        #download-btn:hover {
            background-color: #2471a3;
        }

        #qr-code-content {
            margin: 20px auto;
            /* Center the QR code container */
            text-align: center;
            /* Center-align the contents */
        }

        .modal {
            display: flex;
            /* Use flexbox to center items */
            justify-content: center;
            align-items: center;
            position: fixed;
            /* Fixed position for modal */
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            /* Semi-transparent background */
            z-index: 1000;
            /* Ensure it appears above other content */
        }

        .modal-content {
            background-color: white;
            /* White background for content */
            padding: 20px;
            /* Add some padding */
            border-radius: 8px;
            /* Rounded corners */
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
            /* Shadow for depth */
            text-align: center;
            /* Center the content */
            height: 30%;
            width: 30%;
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

<!-- Slide-in Menu -->

<?php include_once("./includes/menu.php") ?>


<div class="container">
    <h1>List of Equipment</h1>

    <!-- QR Code Container -->
    <div id="qr-modal" class="modal" style="display: none">
        <div class="modal-content">
            <div id="qr-code-content"></div>
            <a id="download-btn" href="#" download="qrcode.png" style="display: none; margin-top: 10px">Download QR Code</a>
            <p>Download & proceed to admin</p>
            <button id="close-qr-modal">Close</button>
        </div>
    </div>

    <!-- Scanner Modal -->
    <div id="scanner-modal" style="display: none">
        <button id="close-scanner">Close</button>
        <div id="scanner"></div>
        <input type="file" id="upload-qrcode" accept="image/*" style="display: block; margin: 10px auto" />
        <div id="qr-code-result"></div>
    </div>

    <button id="open-scanner" style="position: fixed; bottom: 20px; right: 20px">Scan QR Code</button>

    <div class="buttons">
        <button id="add-new-btn">Add New Equipment</button>
        <button id="add-existing-btn">Add Existing Equipment</button>
    </div>

    <!-- Add New Equipment Form -->
    <div class="form-container" id="new-equipment-form">
        <h2>Add New Equipment</h2>
        <div class="form-group">
            <label for="name">Equipment Name</label>
            <input type="text" id="name" placeholder="Enter equipment name" />
        </div>
        <div class="form-group">
            <label for="alert_level">Alert Level</label>
            <input type="number" id="alert_level" placeholder="Enter alert level" />
        </div>
        <div class="form-group">
            <label for="description">Description</label>
            <textarea id="description" placeholder="Enter equipment description"></textarea>
        </div>
        <div class="form-group">
            <label for="picture">Picture</label>
            <input type="file" id="picture" />
        </div>
        <div class="form-group">
            <label for="price">Price</label>
            <input type="number" id="price" step="0.01" placeholder="Enter price" />
        </div>
        <label for="category">Category:</label>
        <select id="category" required>
            <option value="">-- Select Category --</option>
            <option value="equipment">Equipment</option>
            <option value="tools">Tools</option>
            <option value="material">Material</option>
        </select>

        <label for="course">Course:</label>
        <select id="course" required>
            <option value="">-- Select Course --</option>
            <option value="RAC Servicing (DomRAC)">RAC Servicing (DomRAC)</option>
            <option value="Basic Shielded Metal Arc Welding">Basic Shielded Metal Arc Welding</option>
            <option value="Advanced Shielded Metal Arc Welding">Advanced Shielded Metal Arc Welding</option>
            <option value="Pc operation">Pc operation</option>
            <option value="Bread and pastry production NC II">Bread and pastry production NC II</option>
            <option value="Computer aid design (CAD)">Computer aid design (CAD)</option>
            <option value="Culinary arts">Culinary arts</option>
            <option value="Dressmaking NC II">Dressmaking NC II</option>
            <option value="Food and beverage service NC II">Food and beverage service NC II</option>
            <option value="Hair care">Hair care</option>
            <option value="Junior beautician">Junior beautician</option>
            <option value="Gas metal Arc Welding -- GMAW NC I">Gas metal Arc Welding -- GMAW NC I</option>
            <option value="Gas metal Arc Welding -- GMAW NC II">Gas metal Arc Welding -- GMAW NC II</option>
        </select>

        <button class="submit-button" onclick="submitNewEquipment()">Submit</button>
    </div>

    <input type="text" id="search-input" placeholder="Search items..." />
    <select id="category-filter">
        <option value="equipment">equipment</option>
        <option value="material">material</option>
        <option value="tools">tools</option>
    </select>

    <!-- Equipment List Here -->
    <div id="equipment-list" class="equipment-list">
        <!-- Equipment items will be dynamically inserted here -->
    </div>

    <!-- Container for available items -->
    <div id="available-items" style="display: none">
        <button id="back-button">Back</button>
        <!-- Available items will be displayed here -->
    </div>

    <div id="confirm-modal" class="modal" style="display: none">
        <div class="modal-content">
            <p>Are you sure you want to borrow this item?</p>
            <button id="confirm-yes">Yes</button>
            <button id="confirm-no">No</button>
        </div>
    </div>

    <!-- Add Existing Equipment Form -->
    <div class="form-container" id="existing-equipment-form">
        <h2>Add Existing Equipment</h2>
        <div class="form-group">
            <label for="existing-equipment">Select Existing Equipment</label>
            <select id="existing-equipment">
                <option value="">-- Select Equipment --</option>
                <!-- Options will be added dynamically here -->
            </select>
        </div>
        <div class="form-group">
            <label for="additional-quantity">Add Quantity</label>
            <input type="number" id="additional-quantity" placeholder="Enter quantity to add" />
        </div>
        <div class="form-group">
            <label for="location">Location</label>
            <input type="text" id="location" placeholder="Enter location" />
        </div>
        <div class="form-group">
            <label for="serials">Serial Number</label>
            <input type="text" id="serials" placeholder="Enter serial number" />
        </div>
        <button class="submit-button" onclick="submitExistingEquipment()">Submit</button>
    </div>
</div>


<script src="https://unpkg.com/@zxing/library@latest"></script>
<script>
    function adjustUIForUserType(userType) {
        // Select elements for admin, student/instructor, and default content
        const adminLinks = document.querySelectorAll('#menu a[id$="-admin"]');
        const studentInstructorLinks = document.querySelectorAll('#menu a[id$="-user"]');

        // Default behavior: hide everything initially (CSS handles this part now)
        // Show or hide Add Equipment buttons based on userType
        document.getElementById("add-new-btn").style.display =
            userType === "admin" ? "inline-block" : "none";
        document.getElementById("add-existing-btn").style.display =
            userType === "admin" ? "inline-block" : "none";
        document.getElementById("open-scanner").style.display =
            userType === "admin" ? "inline-block" : "none";
        // Show content based on user type
        if (userType === "admin") {
            adminLinks.forEach((link) => link.style.display = "block"); // Show admin-specific content


        } else if (userType === "student" || userType === "instructor") {
            studentInstructorLinks.forEach((link) => link.style.display = "block"); // Show student/instructor content
        }
    }




    // Toggle between showing and hiding forms
    document
        .getElementById("add-new-btn")
        .addEventListener("click", function() {
            toggleForm("new-equipment-form", "existing-equipment-form");
        });

    document
        .getElementById("add-existing-btn")
        .addEventListener("click", function() {
            toggleForm("existing-equipment-form", "new-equipment-form");
            fetchExistingEquipment(); // Populate existing equipment dropdown
        });

    function toggleForm(showFormId, hideFormId) {
        const showForm = document.getElementById(showFormId);
        const hideForm = document.getElementById(hideFormId);

        if (showForm.classList.contains("active")) {
            showForm.classList.remove("active"); // Hide the form if it is currently shown
        } else {
            showForm.classList.add("active"); // Show the form
        }

        hideForm.classList.remove("active"); // Always hide the other form
    }

    // Submit New Equipment
    function submitNewEquipment() {
        const name = document.getElementById("name").value;
        const alertLevel = document.getElementById("alert_level").value;
        const description = document.getElementById("description").value;
        const price = document.getElementById("price").value;
        const picture = document.getElementById("picture").files[0];
        const category = document.getElementById("category").value;
        const course = document.getElementById("course").value;

        console.log("Form Data:", {
            name,
            alertLevel,
            description,
            price,
            picture,
            category,
            course,
        });

        if (
            !name ||
            !alertLevel ||
            !description ||
            !price ||
            !picture ||
            !category ||
            !course
        ) {
            alert(
                "Please fill out all fields, select a category, and upload a picture."
            );
            return;
        }

        const formData = new FormData();
        formData.append("name", name);
        formData.append("alert_level", alertLevel);
        formData.append("description", description);
        formData.append("price", price);
        formData.append("picture", picture);
        formData.append("category", category);
        formData.append("course", course);

        fetch("add_equipment.php", {
            method: "POST",
            body: formData,
        })
            .then((response) => {
                if (!response.ok) {
                    return response.text(); // Get response body as text
                }
                return response.json();
            })
            .then((data) => {
                if (data.success) {
                    alert("New equipment added successfully");
                } else {
                    alert("Error adding equipment: " + data.message);
                }
            })
            .catch((error) => {
                console.error("Fetch Error:", error);
                alert("Error submitting form: " + error.message);
            });
    }

    // Fetch Existing Equipment
    function fetchExistingEquipment() {
        fetch("fetch_existing_equipment.php")
            .then((response) => response.json())
            .then((data) => {
                if (!data.success) {
                    throw new Error(
                        "Error fetching existing equipment: " + data.message
                    );
                }

                const dropdown = document.getElementById("existing-equipment");
                dropdown.innerHTML =
                    '<option value="">-- Select Equipment --</option>'; // Reset dropdown

                if (Array.isArray(data.equipment)) {
                    data.equipment.forEach((equipment) => {
                        const option = document.createElement("option");
                        option.value = equipment.id;
                        option.textContent = equipment.name;
                        dropdown.appendChild(option);
                    });
                } else {
                    console.error(
                        "Data format error: Expected an array for equipment"
                    );
                }
            })
            .catch((error) =>
                console.error("Error fetching existing equipment:", error)
            );
    }

    function submitExistingEquipment() {
        const selectedEquipment = document
            .getElementById("existing-equipment")
            .value.trim();
        const additionalQuantity = document
            .getElementById("additional-quantity")
            .value.trim();
        const location = document.getElementById("location").value.trim();
        const serials = document.getElementById("serials").value.trim();

        console.log("Selected Equipment:", selectedEquipment);
        console.log("Additional Quantity:", additionalQuantity);
        console.log("Location:", location);
        console.log("Serials:", serials);

        if (selectedEquipment === "") {
            alert("Please select an equipment.");
            return;
        }
        if (additionalQuantity === "") {
            alert("Please enter the additional quantity.");
            return;
        }
        if (location === "") {
            alert("Please enter the location.");
            return;
        }
        if (serials === "") {
            alert("Please enter the serial number.");
            return;
        }

        const formData = new FormData();
        formData.append("equipment_id", selectedEquipment);
        formData.append("additional_quantity", additionalQuantity);
        formData.append("location", location);
        formData.append("serials", serials);

        fetch("add_existing_equipment.php", {
            method: "POST",
            body: formData,
        })
            .then((response) => {
                if (!response.ok) {
                    throw new Error("Network response was not ok");
                }
                return response.json();
            })
            .then((data) => {
                if (data.success) {
                    alert("Existing equipment updated successfully");
                } else {
                    alert(
                        "Error updating equipment: " + (data.message || "Unknown error")
                    );
                }
            })
            .catch((error) => {
                console.error("Fetch Error:", error.message);
                alert("Error submitting form: " + error.message);
            });
    }
    // Fetch Equipment
    document.addEventListener("DOMContentLoaded", () => {
        const equipmentListDiv = document.getElementById("equipment-list");
        const availableItemsDiv = document.getElementById("available-items");
        const backButton = document.getElementById("back-button");

        function filterItems() {
            const searchInput = document
                .getElementById("search-input")
                .value.toLowerCase();
            const categoryFilter =
                document.getElementById("category-filter").value;
            const items = document.querySelectorAll(".item");

            items.forEach((item) => {
                const itemName = item
                    .querySelector("strong")
                    .textContent.toLowerCase();
                const itemCategory = item.getAttribute("data-category");

                const matchesSearch = itemName.includes(searchInput);
                const matchesCategory = !categoryFilter || itemCategory === categoryFilter;

                if (matchesSearch && matchesCategory) {
                    item.style.display = ""; // Show item
                } else {
                    item.style.display = "none"; // Hide item
                }
            });
        }

        // Attach event listeners
        document
            .getElementById("search-input")
            .addEventListener("input", filterItems);
        document
            .getElementById("category-filter")
            .addEventListener("change", filterItems);

        function fetchEquipment() {
            getUserCourse().then((userCourse) => {
                fetch("get_equipment.php")
                    .then((response) => response.json())
                    .then((data) => {
                        if (!data.success) {
                            throw new Error("Error fetching equipment: " + data.message);
                        }

                        equipmentListDiv.innerHTML = ""; // Clear previous equipment items
                        availableItemsDiv.style.display = "none"; // Hide available items section
                        equipmentListDiv.style.display = "block"; // Show equipment list

                        if (Array.isArray(data.equipment)) {
                            data.equipment.forEach((item) => {
                                // Check if course exists
                                if (item.course) {
                                    // Normalize the comparison
                                    const itemCourse = item.course.trim().toLowerCase();
                                    const normalizedUserCourse = userCourse
                                        .trim()
                                        .toLowerCase();

                                    // Check if the item's course matches the user's course
                                    if (itemCourse === normalizedUserCourse) {
                                        const itemDiv = document.createElement("div");
                                        itemDiv.classList.add("item");
                                        itemDiv.setAttribute(
                                            "data-category",
                                            item.category ?
                                                item.category.trim().toLowerCase() :
                                                ""
                                        ); // Set category

                                        const alertLevel = parseInt(item.alert_level, 10);
                                        const available = parseInt(item.available, 10);

                                        let backgroundColor = "#fff"; // Default background color
                                        if (alertLevel >= available) {
                                            backgroundColor = "#ffcccc"; // Light red color
                                        }

                                        itemDiv.style.backgroundColor = backgroundColor;

                                        itemDiv.innerHTML = `
                                    <img src="uploads/${item.picture}" alt="${item.name}">
                                    <p><strong>${item.name}</strong></p>
                                    <p>Available: ${item.available}</p>
                                    <p>Description: ${item.description}</p>
                                    <button class="borrow-button" data-equipment-id="${item.id}">Show Details</button>
                                `;
                                        equipmentListDiv.appendChild(itemDiv);
                                    }
                                } else {
                                    console.warn(
                                        `Item with ID ${item.id} does not have a course defined.`
                                    );
                                }
                            });

                            document
                                .querySelectorAll(".borrow-button")
                                .forEach((button) => {
                                    button.addEventListener("click", function() {
                                        const equipmentId =
                                            this.getAttribute("data-equipment-id");
                                        fetchAvailableItems(equipmentId);
                                    });
                                });
                        } else {
                            console.error(
                                "Data format error: Expected an array for equipment"
                            );
                            alert("Unexpected data format received from the server.");
                        }
                    })
                    .catch((error) => {
                        console.error("Fetch error:", error);
                        alert(
                            "An error occurred while fetching equipment. Check the console for details."
                        );
                    });
            });
        }

        function filterItems() {
            const searchInput = document
                .getElementById("search-input")
                .value.toLowerCase();
            const categoryFilter = document
                .getElementById("category-filter")
                .value.toLowerCase();
            const items = document.querySelectorAll(".item");

            items.forEach((item) => {
                const itemName = item
                    .querySelector("strong")
                    .textContent.toLowerCase();
                const itemCategory = item.getAttribute("data-category"); // Get the category

                // Allow all categories to show if "all" is selected
                const matchesCategory = itemCategory === categoryFilter;

                const matchesSearch = itemName.includes(searchInput);

                if (matchesSearch && matchesCategory) {
                    item.style.display = ""; // Show item
                } else {
                    item.style.display = "none"; // Hide item
                }
            });
        }

        function getUserCourse() {
            return fetch("get_course.php")
                .then((response) => {
                    if (!response.ok) {
                        throw new Error("Network response was not ok");
                    }
                    return response.json();
                })
                .then((data) => {
                    if (data.error) {
                        console.error("Error fetching user course:", data.error);
                        return null;
                    }
                    return data.course; // Return the user's course
                })
                .catch((error) => {
                    console.error("Fetch error:", error);
                    return null;
                });
        }

        // Fetch available items for a particular equipment
        function fetchAvailableItems(equipmentId) {
            fetch(`get_available_items.php?equipment_id=${equipmentId}`)
                .then((response) => response.json())
                .then((data) => {
                    if (!data.success) {
                        throw new Error("Error fetching available items: " + data.message);
                    }

                    availableItemsDiv.innerHTML = '<button id="back-button">Back</button>';
                    availableItemsDiv.style.display = "block";
                    equipmentListDiv.style.display = "none";

                    data.items.forEach((item) => {
                        if (item.in_used === "no") { // Ensure it checks if 'in_used' is 'no'
                            const itemDiv = document.createElement("div");
                            itemDiv.classList.add("item");

                            itemDiv.innerHTML = `
              <img src="uploads/${item.picture}" alt="Item Picture" style="max-width: 100px; height: auto;">
              <p><strong>${item.name}</strong></p>
              <p>Serial: ${item.serials}</p>
              <p>Location: ${item.location}</p>
              <p>Date Received: ${item.date_rcvd}</p>
              <button class="borrow-button" data-item-id="${item.equipment_id}">Borrow</button>
            `;
                            availableItemsDiv.appendChild(itemDiv);
                        }
                    });

                    document.querySelectorAll(".borrow-button").forEach((button) => {
                        button.addEventListener("click", handleBorrowClick);
                    });
                })
                .catch((error) => {
                    console.error("Fetch error:", error);
                    alert("An error occurred while fetching available items. Check the console for details.");
                });
        }


        document.addEventListener("click", (event) => {
            if (event.target && event.target.id === "back-button") {
                fetchEquipment(); // Refresh and show the equipment list
            }
        });

        fetchEquipment();
    });

    function generateQRCode(userInfo, itemInfo) {
        return new Promise((resolve, reject) => {
            const qrData = JSON.stringify({
                name: userInfo,
                item: itemInfo
            });

            const qrCodeUrl = `https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=${encodeURIComponent(qrData)}`;

            const qrCodeContainer = document.getElementById('qr-code-content');
            qrCodeContainer.innerHTML = ''; // Clear previous QR codes

            const qrImage = document.createElement('img');
            qrImage.src = qrCodeUrl;
            qrImage.alt = 'QR Code';
            qrCodeContainer.appendChild(qrImage);

            qrImage.onload = () => {
                // Make the download button visible
                const downloadBtn = document.getElementById('download-btn');
                downloadBtn.style.display = 'inline-block';
                downloadBtn.href = qrCodeUrl;
                downloadBtn.download = 'qrcode.png'; // Set download filename

                resolve(qrCodeUrl); // Resolve the promise with the URL
            };

            qrImage.onerror = () => {
                reject(new Error("Failed to load QR code image"));
            };

            // Show the QR modal
            document.getElementById('qr-modal').style.display = 'flex';
        });
    }

    // Close modal functionality
    document
        .getElementById("close-qr-modal")
        .addEventListener("click", function() {
            document.getElementById("qr-modal").style.display = "none";
        });

    // Global variable to store the current item ID
    let currentItemId;

    function handleBorrowClick(event) {
        currentItemId = event.target.getAttribute("data-item-id"); // Store the current item ID
        document.getElementById("confirm-modal").style.display = "flex"; // Show confirmation modal
    }

    // Event listeners for confirmation buttons
    document.getElementById("confirm-yes").addEventListener("click", () => {
        document.getElementById("confirm-modal").style.display = "none"; // Hide confirmation modal
        borrowItem(currentItemId); // Call borrowItem with the current item ID
    });

    document.getElementById("confirm-no").addEventListener("click", () => {
        document.getElementById("confirm-modal").style.display = "none"; // Hide confirmation modal
    });

    function borrowItem(itemId) {
        console.log("Borrowing Item ID:", itemId);

        fetch(`borrow_equipment.php?item_id=${itemId}`)
            .then((response) => response.json())
            .then((data) => {
                if (!data.success) {
                    throw new Error("Error borrowing item: " + data.message);
                }

                const userInfo = {
                    name: data.userInfo.name,
                    phone: data.userInfo.phone,
                    user_type: data.userInfo.user_type,
                    student_id: data.userInfo.student_id,
                };

                const itemInfo = {
                    name: data.itemInfo.name,
                    serials: data.itemInfo.serials,
                    location: data.itemInfo.location,
                    date_rcvd: data.itemInfo.date_rcvd,
                };

                // Generate QR code and wait for the URL
                return generateQRCode(userInfo, itemInfo).then((qrCodeUrl) => {
                    const historyPayload = {
                        user_name: userInfo.name,
                        user_phone: userInfo.phone,
                        user_type: userInfo.user_type,
                        student_id: userInfo.student_id,
                        equipment_name: itemInfo.name,
                        serial_number: itemInfo.serials,
                        location: itemInfo.location,
                        date_received: itemInfo.date_rcvd,
                        qr_code_image: qrCodeUrl,
                    };

                    return fetch("insert_borrow_history.php", {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json",
                        },
                        body: JSON.stringify(historyPayload),
                    });
                });
            })
            .then((historyResponse) => historyResponse.json())
            .then((historyData) => {
                if (historyData.success) {
                    alert(
                        "Item borrowed successfully! QR code generated and stored in history."
                    );
                } else {
                    alert("Failed to store in history: " + historyData.error);
                }
            })
            .catch((error) => {
                console.error("Borrow error:", error);
                alert("An error occurred while borrowing the item: " + error);
            });
    }




    // DOMContentLoaded Event Listener
    document.addEventListener("DOMContentLoaded", () => {
        // Event listeners for borrow button clicks
        document.querySelectorAll(".borrow-button").forEach((button) => {
            button.addEventListener("click", handleBorrowClick);
        });

        // Open scanner modal
        document
            .getElementById("open-scanner")
            .addEventListener("click", function() {
                document.getElementById("scanner-modal").style.display = "block";
                startScanner();
            });

        // Close scanner modal functionality
        document
            .getElementById("close-scanner")
            .addEventListener("click", function() {
                document.getElementById("scanner-modal").style.display = "none";
                stopScanner();
            });

        // File upload event listener for QR code
        document
            .getElementById("upload-qrcode")
            .addEventListener("change", function(event) {
                const file = event.target.files[0];
                if (!file) {
                    alert("No file selected.");
                    return;
                }

                const reader = new FileReader();
                reader.onload = function(e) {
                    const image = new Image();
                    image.src = e.target.result;
                    image.onload = function() {
                        const canvas = document.createElement("canvas");
                        const context = canvas.getContext("2d");
                        canvas.width = image.width;
                        canvas.height = image.height;
                        context.drawImage(image, 0, 0);
                        const imageData = context.getImageData(0, 0, canvas.width, canvas.height);
                        const qr = jsQR(imageData.data, canvas.width, canvas.height);
                        if (qr) {
                            const qrData = JSON.parse(qr.data); // Assuming the QR code contains JSON data
                            handleQrCodeData(qrData); // Function to handle the QR data
                        } else {
                            alert("No QR code found.");
                        }
                    };
                };
                reader.readAsDataURL(file);
            });
    });

    function startScanner() {
        scanner = new Html5Qrcode("scanner");
        const config = { fps: 10, qrbox: { width: 250, height: 250 } };
        scanner.start(
            { facingMode: "environment" },
            config,
            qrCodeSuccess
        ).catch(err => {
            console.error("Error starting scanner:", err);
        });
    }

    function stopScanner() {
        if (scanner) {
            scanner.stop().catch(err => {
                console.error("Error stopping scanner:", err);
            });
        } else {
            console.warn("Scanner was never started or has already been stopped.");
        }
    }

    // Function to handle successful QR code scanning
    function qrCodeSuccess(qrCodeMessage) {
        const qrData = JSON.parse(qrCodeMessage); // Assuming QR data is in JSON format
        handleQrCodeData(qrData); // Call the function to handle the QR data
    }

    // Function to handle the QR code data
    function handleQrCodeData(qrData) {
        // Example of how to handle the data:
        const payload = {
            user_phone: qrData.user_phone,
            user_type: qrData.user_type,
            student_id: qrData.student_id,
            equipment_name: qrData.equipment_name,
            serial_number: qrData.serial_number,
            location: qrData.location,
            date_received: qrData.date_received,
            qr_code_image: qrData.qr_code_image // Optional, if QR code image is included in the data
        };

        // Send the payload to the PHP script
        sendQrDataToServer(payload);
    }

    // Function to send QR code data to the PHP server
    function sendQrDataToServer(payload) {
        fetch('insert_borrow_history.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(payload)
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Borrow history successfully recorded!');
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error sending QR data:', error);
            });
    }

    function borrowItem(itemId) {
        console.log("Borrowing Item ID:", itemId);

        fetch(`borrow_equipment.php?item_id=${itemId}`)
            .then((response) => response.json())
            .then((data) => {
                if (!data.success) {
                    throw new Error("Error borrowing item: " + data.message);
                }

                const userInfo = {
                    name: data.userInfo.name,
                    phone: data.userInfo.phone,
                    user_type: data.userInfo.user_type,
                    student_id: data.userInfo.student_id,
                };

                const itemInfo = {
                    name: data.itemInfo.name,
                    serials: data.itemInfo.serials,
                    location: data.itemInfo.location,
                    date_rcvd: data.itemInfo.date_rcvd,
                };

                console.log("User Info:", userInfo);
                console.log("Item Info:", itemInfo);

                // Ensure generateQRCode returns a valid Promise
                return generateQRCode(userInfo, itemInfo).then((qrCodeUrl) => {
                    console.log("Generated QR Code URL:", qrCodeUrl); // Log the QR code URL

                    const historyPayload = {
                        user_name: userInfo.name,
                        user_phone: userInfo.phone,
                        user_type: userInfo.user_type,
                        student_id: userInfo.student_id,
                        equipment_name: itemInfo.name,
                        serial_number: itemInfo.serials,
                        location: itemInfo.location,
                        date_received: itemInfo.date_rcvd,
                        qr_code_image: qrCodeUrl
                    };

                    console.log("History Payload:", historyPayload); // Log this payload

                });
            })
            .catch((error) => {
                console.error("Borrow error:", error);
                alert(
                    "An error occurred while borrowing the item: " + error.message
                );
            });
    }

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