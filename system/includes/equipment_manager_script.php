

<!--<div class="scanner-container">-->
<!--    <button class="scan-qr">Scan QR Code</button>-->
<!--</div>-->


<script src="./scripts/jspdf.umd.min.js"></script>
<script src="./scripts/jspdf.plugin.autotable.js"></script>
<script src="./scripts/qrcode.js"></script>

<script type="module">
    import Popup from "./scripts/Popup.js";
    import {Ajax, ToData, addHtml, ListenToForm,HideShowComponent,ListenToOriginalSelect, RemoveAllListenerOf} from "./scripts/Tool.js";
    import AlertPopup from "./scripts/AlertPopup.js";
    import {ShowGettingQR} from "./scripts/Functions.js";

    window.jsPDF = window.jspdf.jsPDF;

    const content = document.querySelector(".main-content");

    let globalStatus;

    function viewItem(id) {
        const popup = new Popup("equipments/viewItem.php", {id}, {
            backgroundDismiss: false,
        });

        popup.Create().then(() => {
            popup.Show();

            const form = popup.ELEMENT.querySelector("form");
            const dl = popup.ELEMENT.querySelector(".download-qr");
            const br = popup.ELEMENT.querySelector(".borrow-item");
            const qrcodeImage = popup.ELEMENT.querySelectorAll(".qr-code-container IMG")[1];

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

    let scanner;

    const scannerBtn = document.querySelector(".scan-qr");

    console.log(scannerBtn)

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
                } else if (res.type == 'G') {
                    ShowGettingQR(qrcode);
                }  else {
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

    function getTableContent(start, status, course, type, requestStatus = "", borrowedStatus = "", is_all = false) {
        Ajax({
            url: type !== "material" ? `_getAllBorrowed.php` : "_getAllGetRequests.php",
            type: "POST",
            data: ToData({ start: start, status: status, course, ...(is_all ? { is_all: true } : {}), ...(requestStatus ? { request_status: requestStatus } : {}), ...(borrowedStatus ? { borrow_status: borrowedStatus } : {})}),
            success: (popup) => {
                addHtml(content, popup);

                tableManager();
            },
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

    function ManageAllTablePagination() {
        const parent = document.querySelector(".table-pagination-container");

        if (!parent ) return

        const status = parent.getAttribute("data-status");
        const type = parent.getAttribute("data-type");
        const buttons = parent.querySelectorAll(".page-buttons .page-button");

        globalStatus = status;

        let off = 0;

        for (const button of buttons) {
            let oo = off;

            button.addEventListener("click", function() {
                getTableContent(oo, status, "", type, requestStatus, borrowedStatus, is_all);
            })

            off += 10;
        }
    }

    let table = document.querySelector(".custom-table");
    const type = table.dataset.type;
    const course = document.querySelector("select[name=course]");
    const status = document.querySelector("select[name=request_status]");
    const requestStatus = table.dataset.requestStatus;
    const borrowedStatus = table.dataset.borrowStatus;
    const is_all = table.dataset.isAll;

    ListenToOriginalSelect(course, function (value) {
        getTableContent(0, false, value, type, status.value, borrowedStatus, is_all);
    })

    ListenToOriginalSelect(status, function (value) {
        getTableContent(0, value, course ? course.value : false, type, value, borrowedStatus, is_all);
    })

    function tableManager() {
        table = document.querySelector(".custom-table");
        const items = table.querySelectorAll("tbody tr");
        const printBtn = document.querySelector(".print-btn");
        const title = document.querySelector(".main-content-container h1").innerText;
        // RemoveAllListenerOf(course);

     
        for (const td of items) {
            td.addEventListener("click", function () {
                if (type == "material") {
                    ShowGettingQR(td.dataset.qr);
                } else {
                    showBorrowQR(td.getAttribute("data-qr"));
                }
            })
        }

        printBtn.addEventListener("click", function () {
            const cloneT = table.cloneNode(true);
            const tdQR = cloneT.querySelectorAll(".td-qr");

            tdQR.forEach((td) => td.classList.remove("hide-component"));

            var doc = new jsPDF();

            const base64Img = 'data:image/jpeg;base64,/9j/4AAQSkZJRgABAQEAYABgAAD/2wBDAAgGBgcGBQgHBwcJCQgKDBQNDAsLDBkSEw8UHRofHh0aHBwgJC4nICIsIxwcKDcpLDAxNDQ0Hyc5PTgyPC4zNDL/2wBDAQkJCQwLDBgNDRgyIRwhMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjL/wAARCAJ2BLADASIAAhEBAxEB/8QAHwAAAQUBAQEBAQEAAAAAAAAAAAECAwQFBgcICQoL/8QAtRAAAgEDAwIEAwUFBAQAAAF9AQIDAAQRBRIhMUEGE1FhByJxFDKBkaEII0KxwRVS0fAkM2JyggkKFhcYGRolJicoKSo0NTY3ODk6Q0RFRkdISUpTVFVWV1hZWmNkZWZnaGlqc3R1dnd4eXqDhIWGh4iJipKTlJWWl5iZmqKjpKWmp6ipqrKztLW2t7i5usLDxMXGx8jJytLT1NXW19jZ2uHi4+Tl5ufo6erx8vP09fb3+Pn6/8QAHwEAAwEBAQEBAQEBAQAAAAAAAAECAwQFBgcICQoL/8QAtREAAgECBAQDBAcFBAQAAQJ3AAECAxEEBSExBhJBUQdhcRMiMoEIFEKRobHBCSMzUvAVYnLRChYkNOEl8RcYGRomJygpKjU2Nzg5OkNERUZHSElKU1RVVldYWVpjZGVmZ2hpanN0dXZ3eHl6goOEhYaHiImKkpOUlZaXmJmaoqOkpaanqKmqsrO0tba3uLm6wsPExcbHyMnK0tPU1dbX2Nna4uPk5ebn6Onq8vP09fb3+Pn6/9oADAMBAAIRAxEAPwD3+iiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKa8ixoXdgqgZJNADqKzZNe0+Nc+eH9kGTWffeKoLWJ5ViZkUZyx25rSNKcnZIxlXpxV2zoqKo6XqUGq6dBfWzBoplDL7Vd61mapqSuhaK5PX9RkW+Mcd0YUiAyAcZNYX/CUxRnH26Rsdw1ddPBznHmRx1cdTpy5Wek0V4k/i+a58daLYxzMd94qk7uo9K9sBrKtRdJ2bNqFdVk2kLRXF6xqrwatP/pjxLEcFQ2ABXMXXxUsbK4Aa/d/TCgj8eK0jhJyVzOWMpqTj2PW6K8qtvi5YTYzcxj6piugtPHtteKTB5MnsHOfyxS+qVOgfXKfU7Wisey8RWF5geb5Uh6LJxk+x6GtisJQlF2kjpjOM1eLCiiipKCiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAorH1PW4bBSAnmyDsG4H1PavNvEfxTsLNthuzK6k4itzhf++q2hQlJX2Rz1MRGLstWevNNGmNzgZ9TTgwIyCCK+fJfibJEouJtEuo4H5EsyNgj2Yjmnn4oWJQyQNPE/8ACFYjH0I4rb6orfEY/XJLeDPoHNYniqZoNAnlWNpNhViAccBgTn2rlfhr8QX8XrPaT2swmt13fahHiJxkDBPZuRx6V22sWjX2jXlqhw8sLICfUiueK5aiudEm503ZdDyq58RGOBXt0ADjNYlx4huNQ3RGXYD/AHRVe8GNJ3/3RTdM07+0PBU2o2+77XpdyRMgPWF8c/XIP4Zr6OdSnSadtz5ilGddNN7HZ/C3XfKvbzQrhzliZ4Nx6f3gP516r2r5wS8k0nUbPV7c4e3kDnkjcB1U+xr6BS/iuNJF7Ed0Lweap9QVyK8fH0uSrddT28urqVHle6PMvGeqrD9uuAc+Y+xfw4ri7SKV9NtLyQkrdByg9lIH9ab8R714LO3gTh2ZiR7122p6FLpekeFrdipFvaGElem44J/rXoUavs6kaXQ8+pQU6Uqj3PP9JGPiv4d/6+1/ka+m6+arL/ksGhD/AKfV/ka+la8zHfxmergP4KPJPGUmP7VlHUE4NZPwS0e01W31y8vrSC4BmSEedGHAwM9/rVzxs+yy1M+rkVe+AiMvhTVJCPlk1Biv4IorfFSaoxSObCRUq0mzrr34c+Eb1CH0CyQn+KGPyz+a4rltV+CmllPN8PX1zplwoyqs5kjJ988j8K9TIpMV50ak4u6Z6bpwkrNHhS/2toV+2i64AzrzFODkOv8AeFdj4E8WNNff2BePmRYy1u7Hkgfwk9z6ewrT8f6PHfWNtcgqskMmCSOSp7V47LfS6P8AEHSpLd9ri7ijB9mYK36NXq3jWwrlLdHjKMqGMUY7H0pRRXPa1r5tpTa2pUTKcOzfw/T1ry4QlN2iexUqRpx5pHQ0V4lrXxMsNJdoze3F1Kv3gj4UH35x+lZcfxfyQWgvIoez7jiuh4W2jlqcyxd9os+gaK8t8P8AxEivQrQXq3CnrHLnd+Bz1/Ou803XrPUcKrGOXujkA/h61nUw84a20NaeJpz02ZrUUZorA6AooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigArK1vUhZWxVHAmYcf7I9a1a8v8b6w/2bUXU8KrIhz2row1L2k7HLi63s4epxd3c6n498Uf8I7oUhgtUJa7uSP4e5J7+w/pXqnhn4ceH/DVsixWcd1c4/eXNwgZn/PpXL/Aq0ij8N6ldsg+1S3pSSTHJVUUgfmzH8a9Yor1ZSlYeHpRUFLuV5bO3mhEMsMTxD/lm6Ar+VcHdfBrwrdeJG1V7ciB1+exXiJn/vcc/h6816JRXPdnRyoqWdha6dbpb2dvHBCowEjUKP0qyelLn3poYN0IP0NAWPEfEVn9l1TVLR0wDIzonYKSSMfhip/g/LBJqGvaRMoZbmBHIPdRlWH/AI+K1PH1p9m8TJNyVuYRnPdhx/ICuU+HU76b8U4bcjC3cMsJPqMeZ/7IK9uv+9wcZLoeBhU6WLlAXVdHbStTu9KnGRC2Yyf4oz0Nei+GtQjsfCIsZnLyw5Vc8ZBJI/nVP4kaQRLaaxEvKsIpTj7oPQk/Xj8a5NNa2QlcZx157VaSxNCLb2M3P6riJLucx4nCa38Q9L0wgvE1xFGyqMkqWAb9M17Z40gVbGwVBtVZSAAOnymvGfh3HNq/xeguANy2yyyt7DaV/mwr2zxq6rp9u7MFAm6k/wCya45Tviorsd3LbCSPFLVMfGDQPe9H8jX0j2r5vsnR/jBoO1wcXq9D/smvpE9Kxx38ZnTgf4KPEPH0mNLv39ZTXW/BaDyvhtaSEYM08zn3/eEf0rlPGMaX2nXEQYBjLkZruPhklrYeB7GxSdS0ZkLBm5BLs3T8a3xkJeyizlwVSLqyVztc0uaSmSTRwrukYKPevMPWuYXi8sdKRFOGZwR+FeARRnX/AItabaROB/pis2f+mfzn/wBBr034i+MbewspCJV3FCsK55JP8Vc18EfDM19qlz4svUBjQNFbbxyXP3mH0AAz/tNXf/DoWfU89RVTEc/RHtmo3i2Vq0pIB5C59cV4X8SvEs1rGbO3lAecfO3fFeseMJ9ltaxk4DOWP0A/+vXhc1qviH4tabYON9sbhQw/2VJJ/UVVCKjQdRbk15e0rqn0R6X8M/hrp2maPb6tq9pHc6tcr5p85ciFW6KAeM4OSTzk9sV6M2mWDx+W1lblP7piXH8qtUtefKTbuz0oxSVkedeKPhNpGqu99pAGmaj97dCMRyH0ZR0+o/KuHtbi/CS2Go5ivrR8FwcEEd1PfsRXvteN/Fiw1S11hNX03TbiWFrcfaJII8gEHHzfhiu7B4lQlyz2POx2Gc4qUNztfA3ik6/p7W9yV/tC1wso/vDs34/zrrxXzZ8Jtdvm+JttayfILuKVZVPBICFx+qivpLFc1dRVR8ux2Yfn9mufcWiiisTYKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAo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BSc+tfW3h7RrLQtHhs7GNVjCglh1Y46mvmMXXk5tH1WDoxjFFywsbfTbOK0tY1jgjG1VA6VZpKWuA7wooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiis3VdQFpFsBIkcHGB0pxi5OyJnJQXMyvrWqCGNraFv3rDDEfwiuKu0hhgZjVq6uAgLu3PUk1yep6q1xIVU8CvbwmGcTwMbilMzJW3SsRTKKK9tKyseK3d3G0UUUxBRRRQAtIVDjaehpainnS3j3OwUe9TKSitRpNuyM66t3tJ1nh4KnII7V7J8O/GH9vWH2K7lDX0CjnP319a8B1TxF52YYAfds1D4c1PVLTWreexLmdXBUL1NeBjnSktD6LA+0gkpH19RUNpI8trFJIu12QFl9Djmpq8g9kKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKwPFsmpR6M7aahd/wCIL1x7U0ruwpOyuZ2rao97cNEhxBG+Bj+IisO+vEgif5gGArzPUvG+o2E7oVKY7AVTtvF51CT/AElzz6ivZw9OnFLU8HFSrSu7HRXdw9xcMxOQTUPamq6uoZTkGnV7atyqx4jd3cTvRR3oq0SFFFFAC0rKGXB6UlLmpauUc9qGhKH+0J1Bzx2r1X4Z+MjOBo2oS/Mo/cOx/wDHTXFkBlKnoayLiGayuUmg4wc15uMwkZQbjuelgsZOE0m9D6dBpa43wL4vi1/T1tpmxfQqAwY8uP71dlXzsouLsz6aElJXQUUUVJQUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAU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D+JOh+tdFp/xa05blLLUTvmY4DwjOT7rXkYnAzoq+6Pbw2YQrO1rM9MopiOHUMucEZ5GKfXCegUtU0221Wye1uow8TjkGvnb4g+ALjQLozwKXtWJKOBX0sap6jplrqtm9rdxCSNhjB7VUZcruROCmrM+SNK1mW0nCSk7enNdpb3CXEQdCCDUvxD+G0mhlr203PbEk567a5Pw/flD5LnnpXt4HFtu0jw8fg18UTrad2pikEZFOr2bnhjT1ooPWgUxC0ZopKAHVk6jAYT5y9Aa1qhuYhLCyHuKzqQ5oNGlKTjNNHd/CrxSbiNtFuGyyjfExPbuK9Tr5i0Ka40jXYLiFirI3XHavoCHxRYy2KzhyZCvMYHOfSvlsRQcJaI+rwuIUoas3ajluIoF3SyIg9WbFcvdeJp5lZLeLyh2duSR9K5fxBqMsdk000rv/vHNTTw05l1cXCCPUkdZFDIwIPcU6vPPhj4kTUrKexeTMkTbkyf4fSvQ6xnFwk4s3pzU4qSCiiipLCiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigBkkgjQsxwoGc15zPO097LcsfvsWx2Fdd4nujb6YI1OGlbb+lcNcSrBaMT1PSvSwFN6vvoeTmVW1kumpm3OrhZ2UjI7jPWpopobgqEcAnsa5qVy8hJ60scrRnKkg17nsF0PB9s29Tv47iRLQqZiRjbt3ZGKoX98ILRiD8xGBXL/ANp3GPvmq8l1LMfnYmsoYTllc1nim1YZIxkkLseTSUuaK7jiG0UUUwCiiikxjXdY0LscADNcJruqG9uCqMdorX8R6qEX7PEeeQSKz/B/he98Va3FaQLhG5ZyOAPWvIx2JSdj2cvw13zs6z4T+BRr+qrqN7GTY253HI4duy19IRxrGioihVUYAHQVnaPpNn4f0mGytlCQxDBY9WPqalfVbVN21w+Ou2vB1lse7G0FqXj0qCe7ht13SPj0Hc/SuX1XxVbRAh7hUA/hU5z+NchfeMp5Ny2c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AFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRVDU9Vg0uJGl5ZzhVzyaaTbshSkoq7LF1cx2kJllYBQM1yWr6oNRYwqxW2U9P7xqrfX0l/N5kjq2OAB2FZtxcJBGXc8dhXoYfCy5ryR5WJxsWrIr6qYo7Q4PXNcgTkk1o6lqTXble1Zte9RpuMdT5+tJSloNoooroMRaKKKYCUuaQUtFgFqzbzKDtk5BqtQamSKTL81srrvhOGHPFa3h3xXdaJOsUjGS1Y4ZCenuK5+G4eI8HIqZoVnG9CAT1Fc9ahGtHlkbUa86UuaLPdLS8gvbaOe3cOjqGGKsivEdG16+0G5RlYtDn50bkMK9U0TxHZa1EDC2yTaCY26ivnMTg6lF90fT4bHU667M2qKKK5DtCiiigAooooAKKKKAILu0gvbZ7a5hSaFxh0cZBFYp8DeGD/zBbX/vmuhooA57/hBvDA/5gtr/AN8ml/4Qfwx/0BbX/vk/410FFAHPHwN4YJz/AGLa/kf8a27a1gs7dILaFIYkGFRBgCpqKACiiigAooooAKKKKACiiigAqlfanBYgCQkk9gKzdS8QJGGitCGccF+y1zM9yZZC5JYn+Jjk100cO5vU5K+JVNaFi+v5buUySMSv8K9hWVdXcdum5j83aqt9qiQFkVsv/Kudubt7hyxJx6V7eGwqXQ8DEYtye4+9vnuJCdx29hVEUpOaQV6EUoqyPPk23dhmiiitLCCkoooELRRRQACsTVNEF3J5q4yK3BViCYR4yKyqwU48rNKVSVOXNE3PAXi640WJbHU5t9mqgLheVNevW9zDdQrNBKskbDIZTkV4VPBA0RkUqo9BSaN8QG8M3uwlpbU/6yMHr9B614mOwUYR54Hu4HMJSfJNHvlFZ2i6zZa9p0d/YTCSB+/cHuDWjXjntJiUtFJQMWiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACuS8d6Pd3+ki40/m5twSEAyXHoK62iqhJwlzImcVKLiz5dXxlqNpeG3uSQRwQe1bS6rJqEGd+R161vfFr4ePN5uv6av+1cIBkj3+leV6HqbW8pikPHTHvXtYPGKTszwsbg+WN4nbDpRTEkDoGXoRmn17K1VzwxtGKWirEFFFFABRRRQAUGiigBakilaI5Wo6KTWg0zWjkiu0AkABqZLSW1cS20jKw5G04rGDFTkHFXLfUZIiAxyK55wdmbQqWdztNI8aXNmyQ6iDJF3kP3h/jXcWOq2eoIHt5kfPYHkV5KssN0oBxn3qWBJLOZZrd2Rx3U4ryq+XxavHRnrYbMZxdp6o9iorhtK8ZSwqsWop5gHHmjr+NdbaanaXyBreZWz2715M6U4OzR7NKvCorxZcopM0tZmwUUUUAFFFFABRRRQAUUUUAFFFFABRRTXcIuSQB6k0AOorIutftIFIjzM46heB+dYlzrlzO2RJ5QHZf8AGtqdCU3oYTxEIdTpLzVLazQl5ASP4V5NcvqWrzXjkYCw/wB0Hr9azncFi3OT1NZt1qcEGec4967qOCV9TzcRjnrYuzzqiFmOF71gX2sbT5cPAqhd6jJdZG7iqDZJyTzXsUsMopM8eriHMeSWYszZJ70zNFFdaVjlbuFJS0U7CCiiigYUUUUCCiiigBRThTBWRrWsJZxFI2/eVlVmoK7NaVKVSXLEq+INdMETQQnDdyD1rA0TSr7xLq8Nnaq0skh544A9T6CqUMN1q98kECPLLI2FVeSTX038NPBC+E9CV7lEOoz/ADSN12Z/hr57F4lybsfSYPDKCVzd8JeGoPCugw6dCdzD5pX/ALzHqa3hSUorzT0gpKU0ZoGFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFADJI0ljKOoZWGCCOCK+cPil4DPhjUl1DTx/oM54UD/VnuK+kqpatpdrrOnTWN5EskMqlSCOnvVQm4O6InBSVmfLOhauzEQyHP1rqVwwyK5nxr4RufCOvMgDGAnckh4yv+NWtE1ZZ4wjvkivocDi1NcsmfN4/CSi+aJvYoxQDkZFUb/U4bOMh3w3pXpOagry0PNhCU3aJdxRiuGufE8xkzDnH161a07xS/miO4bg9M1y/XqXNyo65YCqo3R1+KMVHBcRXMQeNwSe1S12J3VzjkuV2YlFFFBIUUUUwFopuKKVgJFdkOVJBq/bam6YD8is6gVMoKW5cZWOkiuoZh1ANXIif4GI91NciHI5BIq1BqU8PRs1zzw6ZtCu47Hd2uv6hasCZjIvcPzmtdPGcYX95bHjqQ4Feewaxlv3gyD29Kr6z4itrKweRc7iCBzXBUwNPdo9Gjj6iVk7nrOn+KdK1GUQpcqkx6RyHBP0razXy14RlufEfxA02Euwj84O+Ou0da+pcYrxq0IwnaJ7eHnKcLyFooorI3CiiigAoopKAFzWdc6zaW2QXLsG2lUGcGrF9G01lPEn3mjYA+9eVwa9Gsr27nayMVPNdOHoe1djkxOI9irndS+JXziK1wP9tqxZ9QuZ8+bKzD0zxWLJq0C/ec1m3OukArCpx65r06WBS6HlVswv1N+W52IWJwo6msi61qOI/JljWFPezTn5nOKrE13wwsVujzqmKlLYu3GpzTk84qkzs5yzEmkzS11xgkrI53NvcSikoqiQoopDQIWkopaTYCUUVUvtThsFy7/N6UnJR3LjFydkW6K4+48XOZDsjGPXFXNM8SCZhHKQtc0cXTcrXOmWCqxV7HS0U1HDgMpyDUV7dR2sQkbr2roc4pX6HLGLbsivqepRWFuxY/ORwK4C6uZb66yMks2AKsarqDajdnZnbnAHrXqnwk+HDzzReINVhxCnzW8Z/jbkZI9BivBx2KcrpH0OBwihq9zo/hN8PZNCiGtanGBdyp+5iYcxg/xex/oa9WFFLXkt3PYSS2CilxRikMKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAwfFfhez8UaTJa3CDzACY5Mcqa+W9U0q+8LaxLaXKFGjfFfYVcL8RfA0PinTjcQRr/aMS4Vu7j0rSlNwldGNampwszw6PxEiWGTy/SuW1C/kvpW+ai/065sb1rORWDq2MEV6Z8M/hi2qypqeqIVtEO4KeDJ7fTiu+tjZzhqefQwcYyuiP4cfC2TXYxqGqK8VoP9WCCC3vj0qn8QvhjdeH5mvLBGlsc53qMlfrX0nBBHbQJDEgSNBtUAdBSTW8VzA8M0ayRONrKwyCK8+M2nc9L2asfHWm6tPZSCNzwPWu0sdTgu4lIcbq6jx/8JI44n1DQ4yVyWkiHUD29a8dzc6TdbX3AqcHtXqYXGcu7PJxeBUtUekgg9KK5vTPEMcmFlNdEjrIgZTkGvchWjNXR4VWlKm7MKBS0laGQtFFFMYgpaKSgQuaM0lFIY8VyPi26wqxp6nNdYzbVJrzzXZ/tGosM5CnGK4cdPlhod2X0+apd9D0z4C6S0urXmquvyW8flofdute/wCa4D4QaO+meBYJJVxLdMZSPboK7+vmJu7ufVQVoi0UUVJYUUUUAFFFFABXz/4ps5dJ8U3MbphGkLIfUGvoCvK/ixp217K/UDkFG49Oa7MDU5KqOHMKXPRfkcczGRVYEUnaorQ74fpUnrX1EdT5NrWw2iiirEwooopiCiiigAoNGKUClcBKKZNKkK7nOBXK6v4jOWitmOKwrV4Ul7xvSw86srRNPVdbitEIjbL1xV1dTahcHkkk9KdBa3mrXSxxK8jMeMc17l8PfhHb20Eeo67FvlbBW3bt/vV4WJxkpu19D38LglBX6nI+B/hNe64our9fs1tjKMw5b6CsXx54EvvB+pBwTJbOCySqOCB/KvqhI1jUIihVAwAO1U9W0ex1uxaz1CBZoG7Ecg+orgVVp3R6HsVy2Pk/TfEbW6rHLkgVW1fWJL5yFJC12nxF+GU/h1zeWCtJYE5z1K/Wue8FeDbzxXq626ArAh3SSdlFd31qXs9WcawcYzvY2Phr8PZvFF6t5dKU0+Ihi5H3j6Cvpi3t4rW3jggjEcUahVVRwBVTRdHtNB0qDT7KMJDEuAPU+taOK8+UnJ3Z3wjYKKKKksKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACkNLRQByms/D/AELXdXTUruBvPX72xtof610tvBFawJDCgSNBhVA6CpcUuKACiiigBMV5144+GFr4g3XdgRBc8kp/C5r0akxQJq58keIPB+p+H5sT28ic8NjANU7HXLi0YI+cCvrLWNEsdbs2tryFXBHysRyp9q8O8YfCK8sTLc6avmwDkbfvfjXXQxModTjr4WM+hkWerQXajBw3fNaAIIyK83mjvdLmKsHRge4xXUaDqkl3hG6jrmvcw+O9paLPCxWC9l7y2OhopdtJivQR51wooopiCiiigCvfTLBau57dq88hifUNYWJDlpHAH4muu8U3f2ew2f36X4SaKuseNbVpBmOD96wwD0Bx+uK8TMqvRHuZZSduZ9T6a021FlplrbL0iiVOfYVaoNFeEfQJWQtFFFABRRRQAUUUUAFct8QLD7d4UuGVcvARKPw6/oTXU1Be2qXtlNbSfclQofxqoS5ZKRM480Wj520+UZ2HqOKtMMMRVS4tzYa3dW4P+rlKH8KuycgGvrqE+empI+MxEHCo0yLvRRSVsjFi0UUhqhC0AUVHNKsKF3OAKlvQaV3YlyAKzb7WrezUlm+Ydq5/VfEjF3ihG2sFUudRnCRhpJD0ArzK+OUdInp4fL3J3mXdU1+W8YqjsFq34Y8I6p4m1AQW0DsP42K8KPU12ng74QalqMqXOpp9ktmAzu+8w+n+Ne9aNolhoVhHaWECxRoMZ7t9TXjVsQ5u7Pdo4aNNWSOe8HfD3S/C1tE/lrPegfNMw6ewrsxSCiuVu51JWDFFFLQMhubaK7geCdFeNxhlI61naJ4b0zw9FLHp9uIxI25j1J9s1r0UAJSijFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRR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7m5l2IxOan0zwxrWuShbOzllJ77Tj869ieYNbHjUsus/eJL3xPcSkiNiBWV5t1eSfKXdjXruifAa6mCTaperbccxp85/pXqehfD/AMPaAi/Z7GOWYDmWYbifw6fpXnVcVKbvc9KlhIxWx87+Hvht4h8RNmK1KwnBLygqv516loHwPtLV1l1W780jqkRI/WvWwm0AAAAdAOKcDXNKrJnXGkkZel+G9J0ZAtjZQx9t23J/OtUClorMtIKKKKBhRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAUNQ0fT9UiaO9tIZVII+ZecGuUsvhV4dsdVW+WORyh3LGxG0H1rusUmKabQmrhRRRSAWiiigYUUUUAFFFFABRRRQAUUUUAFV72zg1C0ltblA8Mq7XU9xViigDhNO+EvhjTr77Utu8x7LKcgV2lva29rGI7eGOJBwAi4qfFFArBRRRQMKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooA/9k=';

            doc.autoTable({ html: cloneT, didDrawPage: function(data) {
                    doc.setFontSize(20);
                    doc.setTextColor(40);

                    if (base64Img) {
                        doc.addImage(base64Img, 'JPEG', data.settings.margin.left - 10, 10, 30, 15);
                    }

                    doc.text(title, data.settings.margin.left, 35);
                    doc.text("Calamba Man Power Development Center", data.settings.margin.left + 15, 20);


                },
                didDrawCell: function(data) {
                    if (data.column.index === 7 && data.cell.section === 'body') {
                        var td = data.cell.raw;
                        var img = td.querySelector("img");
                        var dim = data.cell.height - data.cell.padding('vertical');
                        console.log(data.cell)
                        doc.addImage(img.src, data.cell.x,  data.cell.y, dim, dim);
                    }
                },
                margin: {
                    top: 40
                }
            })
            doc.save('table.pdf');
        })

        ManageAllTablePagination();
    }

    function showBorrowQR(qr_key) {
        const popup = new Popup("equipments/showBorrowQR.php", {qr_key}, {
            backgroundDismiss: false,
        });

        popup.Create().then(() => {
            popup.Show();

            const form = popup.ELEMENT.querySelector("form.form-control");
            const downloadQrBtn = popup.ELEMENT.querySelector(".download-qr");

            const qrcodeImage = popup.ELEMENT.querySelector(".qr-code-container .image-two");

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

                            window.location.reload();
                            // getTableContent(0, globalStatus);
                        },
                    });


                } else if (data.borrow_status) {
                    Ajax({
                        url: `_updateBorrowedStatus.php`,
                        type: "POST",
                        data: ToData({ qr_key, status: data.borrow_status }),
                        success: (p) => {
                            popup.Remove();

                            window.location.reload();
                        },
                    });

                    // getTableContent(0, globalStatus);
                }
            })
        });
    }

    tableManager();
</script>
