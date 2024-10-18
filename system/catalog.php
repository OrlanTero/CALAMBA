<?php

include_once "./includes/Connection.php";
include_once "./includes/Functions.php";

if (!isset($_SESSION['user_id'])) {
    session_start();
}

$CONNECTION = new Connection();

$isAdmin = $_SESSION['user_type'] == 'admin';
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

    <script src='./scripts/download.js'></script>

</head>

<body>
<?php include "./includes/user_navigationbar.php"; ?>

<!-- Slide-in Menu -->

<?php include_once("./includes/menu.php") ?>

<div class="main-content-container">
    <div class="heading">
        <h1>Inventory</h1>
    </div>

    <div class="body">
        <div class="search-engine-container" style="padding-bottom: 20px">
            <div class="search-engine">
                <input type="search" class="search-engine" name="search" placeholder="Search Items">
            </div>
            <select class="select-category" required>
                <option value="">-- Select Category --</option>
                <option value="equipment">Equipment</option>
                <option value="tools">Tools</option>
                <option value="material">Material</option>
            </select>
            <?php if ($isAdmin) : ?>
                <select class="select-course" required style="margin-left: 50px">
                <option value="">-- Select Course --</option>
                <option value="RAC Servicing (DomRAC)">RAC Servicing (DomRAC)</option>
                <option value="Basic Shielded Metal Arc Welding">Basic Shielded Metal Arc Welding</option>
                <option value="Advanced Shielded Metal Arc Welding">Advanced Shielded Metal Arc Welding</option>
                <option value="Pc operation">Pc Operation</option>
                <option value="Bread and pastry production NC II">Bread and Pastry Production NC II</option>
                <option value="Computer aid design (CAD)">Computer Aid Design (CAD)</option>
                <option value="Culinary arts">Culinary Arts</option>
                <option value="Dressmaking NC II">Dressmaking NC II</option>
                <option value="Food and beverage service NC II">Food and Beverage Service NC II</option>
                <option value="Hair care">Hair Care</option>
                <option value="Junior beautician">Junior Beautician</option>
                <option value="Gas metal Arc Welding -- GMAW NC I">Gas Metal Arc Welding -- GMAW NC I</option>
                <option value="Gas metal Arc Welding -- GMAW NC II">Gas Metal Arc Welding -- GMAW NC II</option>
            </select>
            <?php endif; ?>
        </div>
        <div class="main-content">
            <?php include_once "./_getAllEquipments.php" ?>
        </div>
    </div>
</div>

<?php include_once "./includes/bigpipes.php"?>


</body>
<script src="./scripts/qrcode.js"></script>

<script type="module">
    import {Ajax, ToData, addHtml} from "./scripts/Tool.js";
    import {CreateNewEquipment, CreateNewItem, ViewItem,ViewEquipment, RemoveEquipment, GetItemsOf, GetAllEquipments} from "./scripts/Functions.js";
    import AlertPopup, {AlertTypes} from "./scripts/AlertPopup.js";
    import QRScanner from "./scripts/QRScanner.js";

    const content = document.querySelector(".main-content");
    const qrScanner = new QRScanner(document.querySelector(".scan-qr"));

    // Initialize the QRScanner

    let activeCategoryID = null;

    let offset = 0;
    let max = 10;
    let view = 100;

    let scanner;

    const searchEngine = document.querySelector(".search-engine input");
    const selectCourse = document.querySelector(".select-course");
    const selectCategory = document.querySelector(".select-category");


    [searchEngine, selectCourse, selectCategory].forEach(el => {
        if (el) {
            el.addEventListener("input", function () {
                getAllCats(0, searchEngine.value ? searchEngine.value : false, selectCategory ? selectCategory.value : false, selectCourse ? selectCourse.value : false);
            })
        }
    });
    
    function ManageAllTablePagination() {
        const parent = document.querySelector(".table-pagination-container");

        if (!parent ) return

        const buttons = parent.querySelectorAll(".page-buttons .page-button");

        buttons.forEach((button, index) => {
            const offset = index * 10;
            
            button.addEventListener("click", function() {
                if (view == 100) {
                    getAllCats(offset);
                } else {
                    getItemsOf(activeCategoryID, offset);
                }
            });
        });
    }


    function getItemsOf(id, start = 0) {
        GetItemsOf(id, start, false).then(popup => {
            activeCategoryID = id;
            view = 200;
            addHtml(content, popup);
            cardManager();
        });
    }
    
    function getAllCats(start = 0, search = false, category = false, course = false) {
        GetAllEquipments(start, search, category, course).then(popup => {
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

        for (const card of cards) {
            const id = card.getAttribute("data-id");
            const type = card.getAttribute("data-type");
            const floatingIconButton = card.querySelector(".floating-icon-button");

            // Changed from .card-body to .card-bot
            const bot = card.querySelector(".card-bot");

            if (floatingIconButton) {
                floatingIconButton.addEventListener("click", function () {
                    ViewEquipment(id, function () {
                        getAllCats(0, searchEngine.value ? searchEngine.value : false, category, selectCourse.value ? selectCourse.value : false);
                    });
                });
            }

            bot.addEventListener("click", function () {
                if (type === "category") {
                    getItemsOf(id);
                } else {
                    ViewItem(id, function () {
                        getItemsOf(activeCategoryID);
                    });
                }
            });
        }



        if (back) {
            back.addEventListener("click", () => {
                getAllCats(0, searchEngine.value ? searchEngine.value : false, selectCategory ? selectCategory.value : false, selectCourse.value ? selectCourse.value : false);
            })
        }

        if (addEq) {
            addEq.addEventListener("click", () => {
                CreateNewEquipment(selectCategory ? selectCategory.value : false, function () {
                    getAllCats(0);
                });
            })
        }

        if (addItem) {
            addItem.addEventListener("click", () => {
               CreateNewItem(activeCategoryID, function () {
                   getItemsOf(activeCategoryID);
               });
            })
        }

        if (removeItem) {
            removeItem.addEventListener("click", () => {
                RemoveEquipmentItem(activeCategoryID).then(() => {
                    getItemsOf(activeCategoryID);
                });
            })
        }

        ManageAllTablePagination()
    }

    cardManager()
</script>


</html>