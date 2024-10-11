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
        <h1>List of Equipment</h1>
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
    import Popup from "./scripts/Popup.js";
    import {Ajax, ToData} from "./scripts/Tool.js";
    import {ShowBorrowQR, ViewItem} from "./scripts/Functions.js";

    let scanner;

    const scannerBtn = document.querySelector(".scan-qr");

    function openCameraQR() {
        const popup = new Popup("equipments/openCameraQR.php", null, {
            backgroundDismiss: false,
        });

        popup.Create().then(() => {
            popup.Show();

            startScanner(function (qr) {
                popup.Remove();

                handleQRCode(qr);

                stopScanner();
            });
        });
    }

    scannerBtn.addEventListener("click", function() {
        const popup = new Popup("equipments/openQRScanner.php", null, {
            backgroundDismiss: false,
        });

        popup.Create().then(() => {
            popup.Show();

            const drop_zone = popup.ELEMENT.querySelector("#drop_zone");
            const input = popup.ELEMENT.querySelector("input#upload-qrcode");

            const openCamera = popup.ELEMENT.querySelector(".open-camera");

            openCamera.addEventListener("click", function() {
                openCameraQR();
                popup.Remove();
            })

            drop_zone.addEventListener("drop", dropHandler)
            drop_zone.addEventListener("dragover", dragOverHandler)

            drop_zone.addEventListener("click", function () {
                input.click();
            })

            input.addEventListener("change", function(e) {
                const file = e.target.files[0];

                handleQR(file)
            })

            function dragOverHandler(ev) {

                // Prevent default behavior (Prevent file from being opened)
                ev.preventDefault();
            }

            function dropHandler(ev) {
                

                ev.preventDefault();

                if (ev.dataTransfer.items) {

                    const item = ev.dataTransfer.items[0];

                    if (item.kind === "file") {
                        const file = item.getAsFile();

                        handleQR(file);
                    }
                } else {
                    // Use DataTransfer interface to access the file(s)
                    const item = ev.dataTransfer.items[0];

                    if (item.kind === "file") {
                        const file = item.getAsFile();

                        handleQR(file);
                    }
                }


            }
        });
    })

    function handleQRCode(qrcode) {
        Ajax({
            url: `_verifyQR.php`,
            type: "POST",
            data: ToData({ key: qrcode }),
            success: (res) => {
                res = JSON.parse(res);

                if (res.type == 'E') {
                    ViewItem(res.id);
                } else if (res.type == 'B') {
                    ShowBorrowQR(qrcode);
                } else {
                    alert("Invalid QR Code");
                }
            },
        });
    }

    function handleQR(file) {
        const html5QrCode = new Html5Qrcode("reader");

        html5QrCode
            .scanFile(file, true)
            .then((decoded) => {
                handleQRCode(decoded)
            })
    }

    function startScanner(callback) {
        scanner = new Html5Qrcode("scanner");
        const config = { fps: 10, qrbox: { width: 400, height: 400 } };
        scanner.start(
            { facingMode: "environment" },
            config,
            callback
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


</script>

<script type="module">
    import {Ajax, ToData, addHtml} from "./scripts/Tool.js";
    import {CreateNewEquipment, CreateNewItem, ViewItem} from "./scripts/Functions.js";

    const content = document.querySelector(".main-content");

    let activeCategoryID = null;

    let offset = 0;
    let max = 10;
    let view = 100;

    let scanner;

    const searchEngine = document.querySelector(".search-engine input");
    const selectCategory = document.querySelector(".select-category");
    const selectCourse = document.querySelector(".select-course");

    if (searchEngine) {
        searchEngine.addEventListener("input", function () {
            getAllCats(0, searchEngine.value, selectCategory.value)
        })
    }

    if (selectCategory) {
        selectCategory.addEventListener("change", function () {
            getAllCats(0, searchEngine.value, selectCategory.value)
        })
    }

    if (selectCourse) {
        selectCourse.addEventListener("change", function () {
            getAllCats(0, searchEngine.value, selectCategory.value, selectCourse.value)
        })
    }

    function ManageAllTablePagination() {
        const parent = document.querySelector(".table-pagination-container");

        if (!parent ) return

        const buttons = parent.querySelectorAll(".page-buttons .page-button");

        let off = 0;

        for (const button of buttons) {
            let oo = off;
            button.addEventListener("click", function() {
                if (view == 100) {
                    getAllCats(oo);
                } else {
                    getItemsOf(activeCategoryID, oo);
                }
            })

            off++;
        }
    }


    function getItemsOf(id, start = 0) {
        Ajax({
            url: `_getItems.php`,
            type: "POST",
            data: ToData({id, start}),
            success: (popup) => {

                activeCategoryID = id;

                view = 200;

                addHtml(content, popup);

                cardManager();
            },
        });
    }
    
    function getAllCats(start = 0, search = false, category = false, course = false) {
        Ajax({
            url: `_getAllEquipments.php`,
            type: "POST",
            data: ToData({ start: start, search, category, course}),
            success: (popup) => {

                view = 100;

                addHtml(content, popup);

                cardManager();
            },
        });
    }

    function cardManager() {
        const cards = document.querySelectorAll(".cards-table-container .card");

        const back = document.querySelector(".back-btn");
        const addEq = document.querySelector(".add-equipment-btn");
        const addItem = document.querySelector(".add-item-btn");

        for (const card of cards) {
            const id = card.getAttribute("data-id");
            const type = card.getAttribute("data-type");

            card.addEventListener("click", function () {
                if (type === "category") {
                    getItemsOf(id);
                } else {
                    ViewItem(id, function () {
                        getItemsOf(activeCategoryID);
                    });
                }
            })
        }


        if (back) {
            back.addEventListener("click", () => {
                getAllCats();
            })
        }

        if (addEq) {
            addEq.addEventListener("click", () => {
                CreateNewEquipment();
            })
        }

        if (addItem) {
            addItem.addEventListener("click", () => {
               CreateNewItem(activeCategoryID, function () {
                   getItemsOf(activeCategoryID);
               });
            })
        }

        ManageAllTablePagination()
    }

    cardManager()
</script>

</html>