<?php
session_start();
$user_type = $_SESSION['user_type'];  // Assuming user_type is stored in session

// Get the current page filename
$current_page = basename($_SERVER['PHP_SELF']);
?>
<style>
    .custom-menu {
        position: fixed;
        top: 65px!important;
        left: 0;
        width: 260px;
        height: 100%;
        background-color: #2980b9;
        transition: left 0.3s ease;
        z-index: 999;
        display: block;
    }

    .custom-menu.show {
        left: -300px ;
    }

    .custom-menu .menu-header {
        padding: 15px;
        display: flex;
        justify-content: center;
        align-items: center;
    }

    .custom-menu .main-nav {
        list-style-type: none;
        padding: 0;
        margin: 0;
        overflow-y: auto;
        height: 85% !important;
    }

    .custom-menu .main-nav .link{
        padding: 10px;
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    }

    .custom-menu .main-nav .link.drop {
        border-bottom: none !important;
    }

    .custom-menu .main-nav .link a, .as-link {
        display: flex;
        align-items: center;
        color: white;
        padding: 15px;
        text-decoration: none;
        font-size: 1rem;
        transition: background-color 0.3s ease;
    }

    .custom-menu .main-nav .link i {
        margin-right: 15px;
    }

    .custom-menu .main-nav .link .link-contents a {
        margin-left: 20px;
    }

    .custom-menu .main-nav .link-contents {
        display: none; /* Hide by default */
    }

    .custom-menu .main-nav .link-contents.show {
        display: block; /* Show when toggled */
    }

    .custom-menu .main-nav {
        scrollbar-width: thin;
        scrollbar-color: #cfcfcf #ffffff;
    }

    .custom-menu .main-nav::-webkit-scrollbar {
        width: 10px;
    }

    .custom-menu .main-nav::-webkit-scrollbar-track {
        background: #ffffff;
    }

    .custom-menu .main-nav::-webkit-scrollbar-thumb {
        background-color: #cfcfcf;
        border-radius: 100px;
        border: 1px solid #ffffff;
    }

    /* Active link indicator */
    .custom-menu .main-nav .link.active > a,
    .custom-menu .main-nav .link.active > .as-link,
    .custom-menu .main-nav .link-contents .link.active > a {
        background-color: rgba(255, 255, 255, 0.2);
        border-left: 4px solid #ffffff;
    }

    /* Removed Write icon styles */
    .search-result .qr-code {
        margin-right: 15px;
    }

    .search-result .qr-image {
        width: 100px;
        height: 100px;
        object-fit: cover;

    }
</style>

<div class="custom-menu">
    <div class="menu-header">Navigation</div>
    <div class="main-nav">
        <div class="link <?php echo ($current_page == 'dashboard.php') ? 'active' : ''; ?>">
            <a href="dashboard.php"><i class="fas fa-tachometer-alt"></i> <span>Dashboard</span></a>
        </div>
        <?php if ($user_type == 'admin'): ?>
            <div class="link <?php echo ($current_page == 'catalog.php') ? 'active' : ''; ?>">
            <a href="catalog.php"><i class="fas fa-boxes"></i> <span>Inventory</span></a>
        </div>
        <div class="link <?php echo ($current_page == 'user_management.php') ? 'active' : ''; ?>">
            <a href="user_management.php"><i class="fas fa-users"></i> <span>User Management</span></a>
        </div>
            <?php endif; ?>
        <div class="link drop <?php echo (in_array($current_page, ['catalog_equipment.php', 'requests.php', 'equipment_accepted.php', 'equipment_returned.php'])) ? 'active' : ''; ?>">
            <div class="as-link" role="button">
                <i class="fas fa-tools"></i><span>Equipment</span>
            </div>
            <div class="link-contents">
                <div class="link <?php echo ($current_page == 'catalog_equipment.php') ? 'active' : ''; ?>"><a href="catalog_equipment.php"><i class="fas fa-box"></i>Equipment List</a></div>
                <?php if ($user_type == 'admin'): ?>
                    <div class="link <?php echo ($current_page == 'all_equipments.php') ? 'active' : ''; ?>"><a href="all_equipments.php"><i class="fas fa-file"></i>All Equipments</a></div>
                <?php endif; ?> 
                <div class="link <?php echo ($current_page == 'requests.php') ? 'active' : ''; ?>"><a href="requests.php"><i class="fas fa-history"></i>Borrowing Requests</a></div>
                <div class="link <?php echo ($current_page == 'equipment_accepted.php') ? 'active' : ''; ?>"><a href="equipment_accepted.php"><i class="fas fa-check"></i>Borrowed Equipments</a></div>
                <div class="link <?php echo ($current_page == 'equipment_returned.php') ? 'active' : ''; ?>"><a href="equipment_returned.php"><i class="fas fa-file"></i>Returned Equipments</a></div>
            </div>
        </div>
        <div class="link drop <?php echo (in_array($current_page, ['catalog_tools.php', 'tool_requests.php', 'tool_accepted.php', 'tool_returned.php'])) ? 'active' : ''; ?>">
            <div class="as-link" role="button">
                <i class="fas fa-wrench"></i><span>Tools</span>
            </div>
            <div class="link-contents">
                <div class="link <?php echo ($current_page == 'catalog_tools.php') ? 'active' : ''; ?>"><a href="catalog_tools.php"><i class="fas fa-box"></i>Tool List</a></div>
                <?php if ($user_type == 'admin'): ?>
                
                <div class="link <?php echo ($current_page == 'all_tools.php') ? 'active' : ''; ?>"><a href="all_tools.php"><i class="fas fa-file"></i>All Tools</a></div>
                <?php endif; ?>
                <div class="link <?php echo ($current_page == 'tool_requests.php') ? 'active' : ''; ?>"><a href="tool_requests.php"><i class="fas fa-history"></i>Borrowing Requests</a></div>
                <div class="link <?php echo ($current_page == 'tool_accepted.php') ? 'active' : ''; ?>"><a href="tool_accepted.php"><i class="fas fa-check"></i>Borrowed Tools</a></div>
                <div class="link <?php echo ($current_page == 'tool_returned.php') ? 'active' : ''; ?>"><a href="tool_returned.php"><i class="fas fa-file"></i>Returned Tools</a></div>
            </div>
        </div>
        <?php if ($user_type != 'student'): ?>
        <div class="link drop <?php echo (in_array($current_page, ['catalog_consumables.php', 'material_requests.php', 'history.php'])) ? 'active' : ''; ?>">
            <div class="as-link" role="button">
                <i class="fas fa-box-open"></i><span>Consumable Items</span>
            </div>
            <div class="link-contents">
                <div class="link <?php echo ($current_page == 'catalog_consumables.php') ? 'active' : ''; ?>"><a href="catalog_consumables.php"><i class="fas fa-box"></i>Consumable Items List</a></div>
                <div class="link <?php echo ($current_page == 'all_materials.php') ? 'active' : ''; ?>"><a href="all_materials.php"><i class="fas fa-file"></i>All Consumables</a></div>
                <div class="link <?php echo ($current_page == 'material_requests.php') ? 'active' : ''; ?>"><a href="material_requests.php"><i class="fas fa-history"></i>Requests</a></div>
                <div class="link <?php echo ($current_page == 'history.php') ? 'active' : ''; ?>"><a href="history.php"><i class="fas fa-file"></i>History</a></div>

                <?php if ($user_type == 'admin'): ?>
                    <div class="link <?php echo ($current_page == 'alert_materials.php') ? 'active' : ''; ?>"><a href="alert_materials.php"><i class="fas fa-exclamation-triangle"></i>Alert Materials</a></div>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>
        <div class="link drop">
            <div class="as-link scan-qr" role="button">
                <i class="fas fa-qrcode"></i><span>Scan QR Code</span>
            </div>
        </div>
        <div class="link drop">
            <div class="as-link search-item" role="button">
                <i class="fas fa-search"></i><span>Search Items</span>
            </div>
        </div>
    </div>
</div>
<script type="module">
    import { Popup } from './scripts/Popup.js';
    import { Ajax } from './scripts/Tool.js';
    import { ViewItem } from './scripts/Functions.js';
// Toggle dropdowns on click
document.querySelectorAll('.link.drop .as-link').forEach(function(link) {
    link.addEventListener('click', function() {
        var content = this.nextElementSibling;
        content.classList.toggle('show');
    });
});

const searchItem = document.querySelector('.search-item');
const scanQr = document.querySelector('.scan-qr');

function manageAll() {
    const searchResults = document.querySelectorAll('.search-result');

    searchResults.forEach(result => {
        result.addEventListener('click', () => {
            const id = result.getAttribute('data-id');
            const qr = result.getAttribute('data-qr');

            ViewItem(id);

        });
    });
}

searchItem.addEventListener('click', () => {
    const popup = new Popup("equipments/searchItems.php", null, {
        backgroundDismiss: false,
    });

    popup.Create().then(() => {
        popup.Show();

        const form = popup.ELEMENT.querySelector("form");
        const searchInput = form.querySelector("#search-input");

        searchInput.addEventListener("input", function(e) {
            const searchTerm = e.target.value;
            const searchResults = document.querySelector('.search-results');

            Ajax({
                url: "_searchItems.php",
                type: "POST",
                data: { search: searchTerm },
                success: function(response) {
                    if (response.trim() === '') {
                        searchResults.innerHTML = '<div class="no-results">No results found</div>';
                    } else {
                        searchResults.innerHTML = response;
                        manageAll();
                    }
                }
            });
        });
    });
});

</script>
