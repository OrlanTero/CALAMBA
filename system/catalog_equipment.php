<?php
include_once "./includes/Connection.php";
include_once "./includes/Functions.php";

if (!isset($_SESSION['user_id'])) {
    session_start();
}

$CONNECTION = new Connection();
$isAdmin = $_SESSION['user_type'] == 'admin';

// Set the category for this file
$category = 'equipment';  // Hardcoded category: equipment

$no_add_equipment = true;

// Get the list of available courses (you can define them here or fetch them from the database)
$courses = [
    "RAC Servicing (DomRAC)",
    "Basic Shielded Metal Arc Welding",
    "Advanced Shielded Metal Arc Welding",
    "Pc Operation",
    "Bread and Pastry Production NC II",
    "Computer Aid Design (CAD)",
    "Culinary Arts",
    "Dressmaking NC II",
    "Food and Beverage Service NC II",
    "Hair Care",
    "Junior Beautician",
    "Gas Metal Arc Welding -- GMAW NC I",
    "Gas Metal Arc Welding -- GMAW NC II"
];

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <title>Equipment List</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" />
    <link rel="stylesheet" href="./style/styles.css">
    <link rel="stylesheet" href="./style/suchStyles.css">
    <script src='./scripts/download.js'></script>
</head>

<body>
<?php include "./includes/user_navigationbar.php"; ?>
<?php include_once("./includes/menu.php") ?>

<div class="main-content-container">
    <div class="heading">
        <h1>Equipment Inventory</h1>
    </div>

    <div class="body">
        <div class="search-engine-container" style="padding-bottom: 20px">
            <div class="search-engine">
                <input type="search" class="search-engine" name="search" placeholder="Search Equipment">
            </div>
            <?php if ($isAdmin) : ?>
                <select class="select-course" id="courseSelect" required style="margin-left: 50px">
                    <option value="">-- Select Course --</option>
                    <?php foreach ($courses as $course): ?>
                        <option value="<?= $course ?>"><?= $course ?></option>
                    <?php endforeach; ?>
                </select>
            <?php endif; ?>

            <select class="select-used" id="usedSelect" required style="margin-left: 50px">
                    <option value="">-- Select Used --</option>
                    <option value="yes">Using</option>
                    <option value="no">Not Using</option>
                </select>
                <select id="item_condition" name="item_condition" style="margin-left: 20px">
                    <option value="">-- Select Status --</option>
                    <option value="good_condition">Good Condition</option>
                    <option value="bad_condition">Bad Condition</option>
                    <!-- <option value="obsolete">Obsolete</option>
                    <option value="damaged">Damaged</option>
                    <option value="lost">Lost</option> -->
                </select>
        </div>
        <div class="main-content">
            <?php
                $category = "equipment";
                include_once "./_getAllEquipments.php";  // The $category variable will be passed here
            ?>
        </div>
    </div>
</div>

<?php include_once "./includes/bigpipes.php"?>

</body>
<script src="./scripts/qrcode.js"></script>

<script type="module">
    import {Ajax, ToData, addHtml} from "./scripts/Tool.js";
    import {CreateNewEquipment, CreateNewItem, ViewItem, RemoveEquipment, GetItemsOf, GetAllEquipments} from "./scripts/Functions.js";
    import AlertPopup, {AlertTypes} from "./scripts/AlertPopup.js";
    import QRScanner from "./scripts/QRScanner.js";

    const content = document.querySelector(".main-content");
    const qrScanner = new QRScanner(document.querySelector(".scan-qr"));

    let activeCategoryID = null;
    let offset = 0;
    let max = 10;
    let view = 100;

    const searchEngine = document.querySelector(".search-engine input");
    const selectCourse = document.querySelector(".select-course");
    const selectUsed = document.querySelector(".select-used");
    const itemCondition = document.querySelector("#item_condition");
    const category = "equipment";

    [searchEngine, selectCourse].forEach(el => {
        if (el) {
            el.addEventListener("input", function () {
                getAllCats(0, searchEngine.value || false, category, selectCourse?.value || false, selectUsed?.value || false, itemCondition?.value || false);
            });
        }
    });

    [selectUsed, itemCondition].forEach(el => {
        if (el) {
            el.addEventListener("input", function () {
            if (view === 100) {
                getAllCats(0, searchEngine.value || false, category, selectCourse?.value || false, selectUsed?.value || false, itemCondition?.value || false);
            } else {
                getItemsOf(activeCategoryID, 0, false, false, { item_condition: itemCondition?.value || false });
            }
        });
        }
    });

    function ManageAllTablePagination() {
        const parent = document.querySelector(".table-pagination-container");
        if (!parent) return;

        const buttons = parent.querySelectorAll(".page-buttons .page-button");
        buttons.forEach((button, index) => {
            const offset = index * 10;
            button.addEventListener("click", function() {
                view === 100 ? getAllCats(offset, searchEngine.value || false, category, selectCourse.value || false, selectUsed?.value || false, itemCondition?.value || false) : getItemsOf(activeCategoryID, offset, false, false, { item_condition: itemCondition?.value || false });
            });
        });
    }

    function getItemsOf(id, start = 0) {
        GetItemsOf(id, start, true, false, { item_condition: itemCondition?.value || false, in_used: selectUsed?.value || false }).then(popup => {
            activeCategoryID = id;
            view = 200;
            addHtml(content, popup);
            cardManager();
        });
    }
    
    function getAllCats(start = 0, search = false, category = false, course = false, used = false, itemCondition = false) {
        GetAllEquipments(start, search, category, course, { no_add_equipment: true, used, item_condition: itemCondition }).then(popup => {
            view = 100;
            addHtml(content, popup);
            cardManager();
        });
    }

    function cardManager() {
        const cards = document.querySelectorAll(".cards-table-container .card");
        const back = document.querySelector(".back-btn");
        const addEq = document.querySelector(".add-equipment-btn");
        const addItem = document.querySelector(".add-item-btn");
        const removeItem = document.querySelector(".remove-item-btn");

        cards.forEach(card => {
            const id = card.getAttribute("data-id");
            const type = card.getAttribute("data-type");
            const floatingIconButton = card.querySelector(".floating-icon-button");
            const bot = card.querySelector(".card-bot");

            if (floatingIconButton) {
                floatingIconButton.addEventListener("click", function () {
                    ViewEquipment(id, () => {
                        getAllCats(0, searchEngine.value || false, category, selectCourse.value || false);
                    });
                });
            }

            bot.addEventListener("click", function () {
                type === "category" ? getItemsOf(id) : ViewItem(id, () => getItemsOf(activeCategoryID));
            });
        });

        if (back) {
            back.addEventListener("click", () => {
                getAllCats(0, searchEngine.value || false, category, selectCourse?.value || false, selectUsed?.value || false, itemCondition?.value || false);
            });
        }

        if (addEq) {
            addEq.addEventListener("click", () => {
                CreateNewEquipment(category, () => getAllCats(0));
            });
        }

        if (addItem) {
            addItem.addEventListener("click", () => {
               CreateNewItem(activeCategoryID, () => getItemsOf(activeCategoryID));
            });
        }

        if (removeItem) {
            removeItem.addEventListener("click", () => {
                RemoveEquipmentItem(activeCategoryID).then(() => getItemsOf(activeCategoryID));
            });
        }

        ManageAllTablePagination();
    }

    cardManager();
</script>
</html>
