import Popup from "./Popup.js";
import {Ajax, ToData} from "./Tool.js";
import {ShowBorrowQR, ViewItem, ShowGettingQR} from "./Functions.js";

export default class QRScanner {
    constructor(btn) {
        this.scanner = null;
        this.scannerBtn = btn;
        this.initEventListeners();
    }

    initEventListeners() {
        this.scannerBtn.addEventListener("click", () => this.openQRScanner());
    }

    openCameraQR() {
        const popup = new Popup("equipments/openCameraQR.php", null, {
            backgroundDismiss: false,
        });

        popup.Create().then(() => {
            popup.Show();

            this.startScanner((qr) => {
                popup.Remove();
                this.handleQRCode(qr);
                this.stopScanner();
            });
        });
    }

    openQRScanner() {
        const popup = new Popup("equipments/openQRScanner.php", null, {
            backgroundDismiss: false,
        });

        popup.Create().then(() => {
            popup.Show();

            const drop_zone = popup.ELEMENT.querySelector("#drop_zone");
            const input = popup.ELEMENT.querySelector("input#upload-qrcode");
            const openCamera = popup.ELEMENT.querySelector(".open-camera");

            openCamera.addEventListener("click", () => {
                this.openCameraQR();
                popup.Remove();
            });

            drop_zone.addEventListener("drop", (ev) => this.dropHandler(ev));
            drop_zone.addEventListener("dragover", (ev) => this.dragOverHandler(ev));
            drop_zone.addEventListener("click", () => input.click());
            input.addEventListener("change", (e) => this.handleQR(e.target.files[0]));
        });
    }

    dragOverHandler(ev) {
        ev.preventDefault();
    }

    dropHandler(ev) {
        ev.preventDefault();
        const item = ev.dataTransfer.items ? ev.dataTransfer.items[0] : ev.dataTransfer.files[0];
        if (item.kind === "file") {
            this.handleQR(item.getAsFile());
        }
    }

    handleQRCode(qrcode) {
        Ajax({
            url: `_verifyQR.php`,
            type: "POST",
            data: ToData({ key: qrcode }),
            success: (res) => {
                res = JSON.parse(res);
                if (res.type == 'E') {
                    ViewItem(res.id);
                } else if (res.type == 'B') {
                    ShowBorrowQR(qrcode, () => window.location.reload());
                } else if (res.type == 'G') {
                    ShowGettingQR(qrcode, () => window.location.reload());
                } else {
                    alert("Invalid QR Code");
                }
            },
        });
    }

    handleQR(file) {
        const html5QrCode = new Html5Qrcode("reader");
        html5QrCode.scanFile(file, true)
            .then((decoded) => this.handleQRCode(decoded));
    }

    startScanner(callback) {
        this.scanner = new Html5Qrcode("scanner");
        const config = { fps: 10, qrbox: { width: 400, height: 400 } };
        this.scanner.start(
            { facingMode: "environment" },
            config,
            callback
        ).catch(err => console.error("Error starting scanner:", err));
    }

    stopScanner() {
        if (this.scanner) {
            this.scanner.stop().catch(err => console.error("Error stopping scanner:", err));
        } else {
            console.warn("Scanner was never started or has already been stopped.");
        }
    }
}
