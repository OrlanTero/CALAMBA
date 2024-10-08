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
        </div>
        <div class="main-content">
            <?php include_once "./_getAllEquipments.php" ?>
        </div>
    </div>
</div>

<?php include_once "./includes/bigpipes.php"?>


<div class="scanner-container">
    <button class="scan-qr">Scan QR Code</button>
</div>

</body>
<script src="./scripts/qrcode.js"></script>

<script type="module">
    import Popup from "./scripts/Popup.js";
    import {Ajax, ToData, ListenToForm} from "./scripts/Tool.js";

    let scanner;

    const scannerContainer = document.querySelector(".scanner-container");
    const scannerBtn = scannerContainer.querySelector(".scan-qr");

    function openCameraQR() {
        const popup = new Popup("equipments/openCameraQR.php", null, {
            backgroundDismiss: false,
        });

        popup.Create().then(() => {
            popup.Show();

            startScanner(function (qr) {
                handleQRCode(qr);

                popup.Remove();

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
                    viewItem(res.id);
                } else if (res.type == 'B') {
                    showBorrowQR(qrcode);
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

    function showBorrowQR(qr_key) {
        const popup = new Popup("equipments/showBorrowQR.php", {qr_key}, {
            backgroundDismiss: false,
        });

        popup.Create().then(() => {
            popup.Show();

            const form = popup.ELEMENT.querySelector("form.form-control");
            const downloadQrBtn = popup.ELEMENT.querySelector(".download-qr");

            const qrcodeImage = popup.ELEMENT.querySelector(".qr-code-container IMG");

            downloadQrBtn.addEventListener("click", function () {
                DownloadImage(qrcodeImage.src, `bqr-${(new Date()).getTime()}.png`);
            })

            ListenToForm(form, function (data) {
                if (data.request_status) {
                    Ajax({
                        url: `_updateBorrowedRequest.php`,
                        type: "POST",
                        data: ToData({ qr_key, status: data.request_status }),
                        success: (p) => {
                            popup.Remove();

                            getTableContent(0, globalStatus);
                        },
                    });


                } else if (data.borrow_status) {
                    Ajax({
                        url: `_updateBorrowedStatus.php`,
                        type: "POST",
                        data: ToData({ qr_key, status: data.borrow_status }),
                        success: (p) => {
                            popup.Remove();
                        },
                    });

                    getTableContent(0, globalStatus);
                }
            })
        });
    }

    function DownloadImage(src, filename) {
        const a = document.createElement('a');
        a.href = src;
        a.download = filename;
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
    }

    function viewItem(id) {
        const popup = new Popup("equipments/viewItem.php", {id}, {
            backgroundDismiss: false,
        });

        popup.Create().then(() => {
            popup.Show();

            const form = popup.ELEMENT.querySelector("form");
            const dl = popup.ELEMENT.querySelector(".download-qr");
            const br = popup.ELEMENT.querySelector(".borrow-item");
            const qrcodeImage = popup.ELEMENT.querySelector(".qr-code-container IMG");

            ListenToForm(form, function (data) {
                Ajax({
                    url: `_updateItem.php`,
                    type: "POST",
                    data: ToData({id:id, data: JSON.stringify(data)}),
                    success: (r) => {
                        popup.Remove();

                        getItemsOf(activeCategoryID);
                    },
                });
            })

            if (dl) {
                dl.addEventListener("click", function() {
                    DownloadImage(qrcodeImage.src, `item-${id}-qr-code.png`);
                });
            }

            if (br) {
                br.addEventListener("click", function() {
                    const pp = new AlertPopup({
                        primary: "Borrow Item?",
                        secondary: `Borrowing item`,
                        message: "Are you sure to borrow this Item?"
                    }, {
                        alert_type: AlertTypes.YES_NO,
                    });

                    pp.AddListeners({
                        onYes: () => {
                            Ajax({
                                url: `_borrowItem.php`,
                                type: "POST",
                                data: ToData({id: id}),
                                success: (qr_key) => {
                                    pp.Remove();
                                    popup.Remove();

                                    getItemsOf(activeCategoryID);

                                    showBorrowQR(qr_key);
                                },
                            });
                        }
                    })

                    pp.Create().then(() => { pp.Show() })
                });
            }

        });
    }
</script>

<script type="module">
    import {Ajax, ToData, addHtml, ListenToForm, UploadImageFromFile, MakeID} from "./scripts/Tool.js";
    import Popup from "./scripts/Popup.js";
    import AlertPopup, {AlertTypes} from "./scripts/AlertPopup.js";

    const content = document.querySelector(".main-content");

    let activeCategoryID = null;

    let offset = 0;
    let max = 10;
    let view = 100;

    let scanner;

    const searchEngine = document.querySelector(".search-engine input");
    const selectCategory = document.querySelector(".select-category");

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


    function createNewItem(id) {
        const popup = new Popup("equipments/addItem.phtml", {id}, {
            backgroundDismiss: false,
        });

        popup.Create().then(() => {
            popup.Show();

            const form = popup.ELEMENT.querySelector("form");

            ListenToForm(form, function (data) {
                data.equipment_id = activeCategoryID;
                Ajax({
                    url: `_insertItem.php`,
                    type: "POST",
                    data: ToData({data: JSON.stringify(data)}),
                    success: (r) => {
                        popup.Remove();

                        getItemsOf(activeCategoryID);
                    },
                });
            })
        });
    }

    function showBorrowQR(qr_key) {
        const popup = new Popup("equipments/showBorrowQR.php", {qr_key}, {
            backgroundDismiss: false,
        });

        popup.Create().then(() => {
            popup.Show();

            const downloadQrBtn = popup.ELEMENT.querySelector(".download-qr");

            const qrcodeImage = popup.ELEMENT.querySelector(".qr-code-container IMG");

            downloadQrBtn.addEventListener("click", function () {
                DownloadImage(qrcodeImage.src, `bqr-${(new Date()).getTime()}.png`);
            })

        });
    }




    function viewItem(id) {
        const popup = new Popup("equipments/viewItem.php", {id}, {
            backgroundDismiss: false,
        });

        popup.Create().then(() => {
            popup.Show();

            const form = popup.ELEMENT.querySelector("form");
            const dl = popup.ELEMENT.querySelector(".download-qr");
            const br = popup.ELEMENT.querySelector(".borrow-item");
            const qrcodeImage = popup.ELEMENT.querySelector(".qr-code-container IMG");

            ListenToForm(form, function (data) {
                Ajax({
                    url: `_updateItem.php`,
                    type: "POST",
                    data: ToData({id:id, data: JSON.stringify(data)}),
                    success: (r) => {
                        popup.Remove();

                        getItemsOf(activeCategoryID);
                    },
                });
            })

            if (dl) {
                dl.addEventListener("click", function() {
                    DownloadImage(qrcodeImage.src, `item-${id}-qr-code.png`);
                });
            }

            if (br) {
                br.addEventListener("click", function() {
                    const pp = new AlertPopup({
                        primary: "Borrow Item?",
                        secondary: `Borrowing item`,
                        message: "Are you sure to borrow this Item?"
                    }, {
                        alert_type: AlertTypes.YES_NO,
                    });

                    pp.AddListeners({
                        onYes: () => {
                            Ajax({
                                url: `_borrowItem.php`,
                                type: "POST",
                                data: ToData({id: id}),
                                success: (qr_key) => {
                                    pp.Remove();
                                    popup.Remove();

                                    getItemsOf(activeCategoryID);

                                    showBorrowQR(qr_key);
                                },
                            });
                        }
                    })

                    pp.Create().then(() => { pp.Show() })
                });
            }

        });
    }

    function DownloadImage(src, filename) {
        const a = document.createElement('a');
        a.href = src;
        a.download = filename;
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
    }


    function createNewEquipment() {
        const popup = new Popup("equipments/addEquipment.phtml", null, {
            backgroundDismiss: false,
        });

        popup.Create().then(() => {
            popup.Show();
            
            const form = popup.ELEMENT.querySelector("form");
            
            ListenToForm(form, function (data) {
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
    
    function getAllCats(start = 0, search = false, category = false) {
        Ajax({
            url: `_getAllEquipments.php`,
            type: "POST",
            data: ToData({ start: start, search, category }),
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
                    viewItem(id);
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

        ManageAllTablePagination()
    }

    cardManager()
</script>

</html>