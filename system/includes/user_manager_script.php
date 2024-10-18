<script src="./scripts/jspdf.umd.min.js"></script>
<script src="./scripts/jspdf.plugin.autotable.js"></script>
<script src="./scripts/qrcode.js"></script>

<script type="module">
    import Popup from "./scripts/Popup.js";
    import { Ajax, ToData, addHtml, ListenToForm, ListenToOriginalSelect } from "./scripts/Tool.js";
    import AlertPopup from "./scripts/AlertPopup.js";
    import { ShowGettingQR, DownloadImage, ViewUser } from "./scripts/Functions.js";
    import QRScanner from "./scripts/QRScanner.js";


    const content = document.querySelector(".main-content");
    const scanner = new QRScanner(document.querySelector(".scan-qr"));
    const course = document.querySelector("select[name=course]");
    const user_type = document.querySelector("select[name=user_type]");
    const status = document.querySelector("select[name=status]");
    let globalStatus;
    let table = document.querySelector(".custom-table");
    const is_all = table.dataset.isAll;

    window.jsPDF = window.jspdf.jsPDF;

    function getTableContent(start, user_type, course, status,  is_all = false) {
        Ajax({
            url: `_getAllUsers.php`,
            type: "POST",
            data: ToData({ 
                start, 
                user_type, 
                course,
                status,
                is_all
            }),
            success: (popup) => {
                addHtml(content, popup);
                tableManager();
            },
        });
    }

    function ManageAllTablePagination() {
        const parent = document.querySelector(".table-pagination-container");

        if (!parent) return;

        const buttons = parent.querySelectorAll(".page-buttons .page-button");

        globalStatus = status;

        buttons.forEach((button, index) => {
            button.addEventListener("click", () => {
                getTableContent(index * 10, user_type.value, course.value, status.value, is_all);
            });
        });
    }

    ListenToOriginalSelect(course, (value) => {
        getTableContent(0, user_type.value, value, status.value, is_all);
    });

    ListenToOriginalSelect(user_type, (value) => {
        getTableContent(0, value, course.value, status.value, is_all);
    });

    ListenToOriginalSelect(status, (value) => {
        getTableContent(0, user_type.value, course.value, value, is_all);
    });

    function tableManager() {
        table = document.querySelector(".custom-table");
        const items = table.querySelectorAll("tbody tr");
        const printBtn = document.querySelector(".print-btn");
        const title = document.querySelector(".main-content-container h1").innerText;
     
        items.forEach(td => {
            td.addEventListener("click", () => {
                ViewUser(td.dataset.id, () => {
                    getTableContent(0, user_type.value, course.value, status.value, is_all);
                });
            });
        });

        printBtn.addEventListener("click", () => {
            const cloneT = table.cloneNode(true);
            const tdQR = cloneT.querySelectorAll(".td-qr");

            tdQR.forEach((td) => td.classList.remove("hide-component"));

            var doc = new jsPDF();

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
                        var td = data.cell.raw;
                        var img = td.querySelector("img");
                        var dim = data.cell.height - data.cell.padding('vertical');
                        doc.addImage(img.src, data.cell.x, data.cell.y, dim, dim);
                    }
                },
                margin: {
                    top: 40
                }
            });
            doc.save('table.pdf');
        });

        ManageAllTablePagination();
    }

    tableManager();
</script>
