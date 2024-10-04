<?php

include_once "./includes/Connection.php";
$CONNECTION = new Connection();



?>

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


    <link rel="stylesheet" href="./style/styles.css">
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
        <div class="main-content">
            <?php include_once "./_getAllEquipments.php" ?>
        </div>
    </div>
</div>

<?php include_once "./includes/bigpipes.php"?>

</body>

<script type="module">
    import {Ajax, ToData, addHtml, ListenToForm, UploadImageFromFile, MakeID} from "./scripts/Tool.js";
    import Popup from "./scripts/Popup.js";


    const content = document.querySelector(".main-content");

    let activeCategoryID = null;

    function createNewItem(id) {
        const popup = new Popup("equipments/addItem.phtml", {idZzz}, {
            backgroundDismiss: false,
        });

        popup.Create().then(() => {
            popup.Show();
        });
    }


    function createNewEquipment() {
        const popup = new Popup("equipments/addEquipment.phtml", null, {
            backgroundDismiss: false,
        });

        popup.Create().then(() => {
            popup.Show();
            
            const form = popup.ELEMENT.querySelector("form");
            
            ListenToForm(form, function (data) {
                console.log(data);

                new Promise((resolve) => {
                    UploadImageFromFile(data.picture, MakeID(10), "./../../uploads/").then((res) => {
                        if (res.code == 200){
                            resolve(res.body.path);
                        } else {
                            resolve(false);
                        }
                    })
                }).then((res) => {
                    if (res) {

                        data.picture = res;

                        Ajax({
                            url: `_insertEquipment.php`,
                            type: "POST",
                            data: ToData({data: JSON.stringify(data)}),
                            success: (r) => {
                                console.log(r)
                                popup.Hide()

                                getAllCats();
                            },
                        });
                    }
                })
            })
        })
    }

    function getItemsOf(id) {
        Ajax({
            url: `_getItems.php`,
            type: "POST",
            data: ToData({id}),
            success: (popup) => {

                activeCategoryID = id;

                addHtml(content, popup);

                cardManager();
            },
        });
    }
    
    function getAllCats() {
        Ajax({
            url: `_getAllEquipments.php`,
            type: "POST",
            data: null,
            success: (popup) => {
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
                   alert(1)
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
                createNewEquipment();
            })
        }

        if (addItem) {
            addItem.addEventListener("click", () => {
               createNewItem(activeCategoryID);
            })
        }
    }

    cardManager()
</script>

</html>