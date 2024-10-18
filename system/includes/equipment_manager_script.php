<script src="./scripts/jspdf.umd.min.js"></script>
<script src="./scripts/jspdf.plugin.autotable.js"></script>
<script src="./scripts/qrcode.js"></script>
<script type="module">
    import Popup from "./scripts/Popup.js";
    import { Ajax, ToData, addHtml, ListenToForm, ListenToOriginalSelect } from "./scripts/Tool.js";
    import AlertPopup, { AlertTypes } from "./scripts/AlertPopup.js";
    import { ShowGettingQR, DownloadImage } from "./scripts/Functions.js";
    import QRScanner from "./scripts/QRScanner.js";
    window.jsPDF = window.jspdf.jsPDF;

    const content = document.querySelector(".main-content");
    const scanner = new QRScanner(document.querySelector(".scan-qr"));

    let globalStatus;
    let activeCategoryID;
    let table = document.querySelector(".custom-table");
    const type = table.dataset.type;
    const course = document.querySelector("select[name=course]");
    const from_date = document.querySelector("input[name=from_date]");
    const to_date = document.querySelector("input[name=to_date]");
    const status = document.querySelector("select[name=request_status]");
    const item_condition = document.querySelector("select[name=item_condition]");
    const in_used = document.querySelector(".select-used");
    const requestStatus = table.dataset.requestStatus;
    const borrowedStatus = table.dataset.borrowStatus;
    const is_all = table.dataset.isAll;

    
    function viewItem(id) {
        const popup = new Popup("equipments/viewItem.php", { id }, { backgroundDismiss: false });

        popup.Create().then(() => {
            popup.Show();

            const form = popup.ELEMENT.querySelector("form");
            const dl = popup.ELEMENT.querySelector(".download-qr");
            const br = popup.ELEMENT.querySelector(".borrow-item");
            const qrcodeImage = popup.ELEMENT.querySelectorAll(".qr-code-container IMG")[1];

            ListenToForm(form, data => {
                Ajax({
                    url: "_updateItem.php",
                    type: "POST",
                    data: ToData({ id, data: JSON.stringify(data) }),
                    success: () => {
                        popup.Remove();
                        getItemsOf(activeCategoryID);
                    },
                });
            });

            if (dl) {
                dl.addEventListener("click", () => DownloadImage(qrcodeImage.src, `item-${id}-qr-code.png`));
            }

            if (br) {
                br.addEventListener("click", () => {
                    const pp = new AlertPopup({
                        primary: "Borrow Item?",
                        secondary: "Borrowing item",
                        message: "Are you sure to borrow this Item?"
                    }, { alert_type: AlertTypes.YES_NO });

                    pp.AddListeners({
                        onYes: () => {
                            Ajax({
                                url: "_borrowItem.php",
                                type: "POST",
                                data: ToData({ id }),
                                success: qr_key => {
                                    pp.Remove();
                                    popup.Remove();
                                    getItemsOf(activeCategoryID);
                                    showBorrowQR(qr_key);
                                },
                            });
                        }
                    });

                    pp.Create().then(() => pp.Show());
                });
            }
        });
    }

    function getTableContent(start, status, course, type, requestStatus = "", borrowedStatus = "", is_all = false, filterDate = false, options = {}) {
        Ajax({
            url: type === "material" ? "_getAllGetRequests.php" : "_getAllBorrowed.php",
            type: "POST",
            data: ToData({ 
                start, 
                status, 
                course, 
                ...(is_all && { is_all: true }),
                ...(requestStatus && { request_status: requestStatus }),
                ...(borrowedStatus && { borrow_status: borrowedStatus }),
                ...(filterDate && filterDate),
                category: type,
                ...options
            }),
            success: popup => {
                addHtml(content, popup);
                tableManager();
            },
        });
    }

    function getTable(start, type, options = {}) {
        const obj = { start, ...options, category: type, course };

        Ajax({
            url: type === "material" ? "_getAllGetRequests.php" : "_getAllBorrowed.php",
            type: "POST",
            data: ToData(obj),
            success: popup => {
                addHtml(content, popup);
                tableManager();
            },
        });
    }

    function ManageAllTablePagination() {
        const parent = document.querySelector(".table-pagination-container");
        if (!parent) return;

        const status = parent.getAttribute("data-status");
        const type = parent.getAttribute("data-type");
        const buttons = parent.querySelectorAll(".page-buttons .page-button");

        globalStatus = status;

        buttons.forEach((button, index) => {
            button.addEventListener("click", () => {
               
            });
        });
    }
    
    function filterDate() {
        return from_date.value && to_date.value ? { from_date: from_date.value, to_date: to_date.value } : null;
    }

    ListenToOriginalSelect(course, value => {
        getTableContent(0, false, value, type, status ? status.value : false, borrowedStatus, is_all, filterDate(), { item_condition: item_condition ? item_condition.value : false, in_used: in_used ? in_used.value : false });
    });

    if (status) {
        ListenToOriginalSelect(status, value => {
            getTableContent(0, value, course ? course.value : false, type, value, borrowedStatus, is_all, filterDate(), { item_condition: item_condition ? item_condition.value : false, in_used: in_used ? in_used.value : false });
        });
    }

    if (item_condition) {
        ListenToOriginalSelect(item_condition, value => {
            getTableContent(0, false, course ? course.value : false, type, status ? status.value : false, borrowedStatus, is_all, filterDate(), { item_condition: value, in_used: in_used ? in_used.value : false });
        });
    }

    if (in_used) {
        ListenToOriginalSelect(in_used, value => {
            getTableContent(0, false, course ? course.value : false, type, status ? status.value : false, borrowedStatus, is_all, filterDate(), { item_condition: item_condition ? item_condition.value : false, in_used: value });
        });
    }

    [from_date, to_date].forEach(input => {
        input.addEventListener("change", () => {
            getTableContent(0, false, course ? course.value : false, type, status ? status.value : false, borrowedStatus, is_all, filterDate(), { item_condition: item_condition ? item_condition.value : false, in_used: in_used ? in_used.value : false });
        });
    });

    function tableManager() {
        table = document.querySelector(".custom-table");
        const items = table.querySelectorAll("tbody tr");
        const printBtn = document.querySelector(".print-btn");
        const title = document.querySelector(".main-content-container h1").innerText;
     
        items.forEach(td => {
            td.addEventListener("click", () => {
                if (type == "material") {
                    ShowGettingQR(td.dataset.qr);
                } else {
                    showBorrowQR(td.getAttribute("data-qr"));
                }
            });
        });

        printBtn.addEventListener("click", () => {
            const cloneT = table.cloneNode(true);
            const tdQR = cloneT.querySelectorAll(".td-qr");

            tdQR.forEach(td => td.classList.remove("hide-component"));

            const doc = new jsPDF();
            const base64Img = 'data:image/jpeg;base64,/9j/4AAQSkZJRgABAQEAYABgAAD/2wBDAAgGBgcGBQgHBwcJCQgKDBQNDAsLDBkSEw8UHRofHh0aHBwgJC4nICIsIxwcKDcpLDAxNDQ0Hyc5PTgyPC4zNDL/2wBDAQkJCQwLDBgNDRgyIRwhMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjL/wAARCAJ2BLADASIAAhEBAxEB/8QAHwAAAQUBAQEBAQEAAAAAAAAAAAECAwQFBgcICQoL/8QAtRAAAgEDAwIEAwUFBAQAAAF9AQIDAAQRBRIhMUEGE1FhByJxFDKBkaEII0KxwRVS0fAkM2JyggkKFhcYGRolJicoKSo0NTY3ODk6Q0RFRkdISUpTVFVWV1hZWmNkZWZnaGlqc3R1dnd4eXqDhIWGh4iJipKTlJWWl5iZmqKjpKWmp6ipqrKztLW2t7i5usLDxMXGx8jJytLT1NXW19jZ2uHi4+Tl5ufo6erx8vP09fb3+Pn6/8QAHwEAAwEBAQEBAQEBAQAAAAAAAAECAwQFBgcICQoL/8QAtREAAgECBAQDBAcFBAQAAQJ3AAECAxEEBSExBhJBUQdhcRMiMoEIFEKRobHBCSMzUvAVYnLRChYkNOEl8RcYGRomJygpKjU2Nzg5OkNERUZHSElKU1RVVldYWVpjZGVmZ2hpanN0dXZ3eHl6goOEhYaHiImKkpOUlZaXmJmaoqOkpaanqKmqsrO0tba3uLm6wsPExcbHyMnK0tPU1dbX2Nna4uPk5ebn6Onq8vP09fb3+Pn6/9oADAMBAAIRAxEAPwD3+iiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKA...';

            doc.autoTable({ 
                html: cloneT, 
                didDrawPage: function(data) {
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
                        const td = data.cell.raw;
                        const img = td.querySelector("img");
                        const dim = data.cell.height - data.cell.padding('vertical');
                        doc.addImage(img.src, data.cell.x, data.cell.y, dim, dim);
                    }
                },
                margin: { top: 40 }
            });
            doc.save('table.pdf');
        });

        ManageAllTablePagination();
    }

    function showBorrowQR(qr_key) {
        const popup = new Popup("equipments/showBorrowQR.php", { qr_key }, { backgroundDismiss: false });

        popup.Create().then(() => {
            popup.Show();

            const form = popup.ELEMENT.querySelector("form.form-control");
            const downloadQrBtn = popup.ELEMENT.querySelector(".download-qr");
            const qrcodeImage = popup.ELEMENT.querySelector(".qr-code-container .image-two");
            const status = popup.ELEMENT.querySelector("select[name=borrow_status]");
            const condition = popup.ELEMENT.querySelector("select[name=item_condition]");
            const conditionContainer = popup.ELEMENT.querySelector(".condition-container");

            downloadQrBtn.addEventListener("click", () => {
                DownloadImage(qrcodeImage.src, `bqr-${(new Date()).getTime()}.png`);
            });

            ListenToOriginalSelect(status, value => {
                conditionContainer.classList.toggle("hide-component", value !== "returned");
            });

            ListenToForm(form, function (data) {
                if (data.borrow_status === 'returned' && !data.item_condition) {
                    alert("Please select the item condition");
                    return;
                }

                const url = data.request_status ? "_updateBorrowedRequest.php" : "_updateBorrowedStatus.php";
                const statusKey = data.request_status ? "request_status" : "borrow_status";
                const statusValue = data[statusKey];

                Ajax({
                    url,
                    type: "POST",
                    data: ToData({ 
                        qr_key, 
                        status: statusValue, 
                        ...(data.item_condition && { item_condition: data.item_condition }) 
                    }),
                    success: () => {
                        popup.Remove();
                        window.location.reload();
                    },
                });
            }, ['item_condition']);
        });
    }

    tableManager();
</script>
