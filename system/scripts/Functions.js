import Popup from "./Popup.js";
import {Ajax, ListenToForm, MakeID, ToData, UploadImageFromFile} from "./Tool.js";
import AlertPopup, {AlertTypes} from "./AlertPopup.js";

export function DownloadImage(src, filename) {
    const a = document.createElement('a');
    a.href = src;
    a.download = filename;
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
}
export function ViewItem(id, callback) {
    const popup = new Popup("equipments/viewItem.php", {id}, {
        backgroundDismiss: false,
    });

    popup.Create().then(() => {
        popup.Show();

        const form = popup.ELEMENT.querySelector("form");
        const dl = popup.ELEMENT.querySelector(".download-qr");
        const br = popup.ELEMENT.querySelector(".borrow-item");
        const qrcodeImage = popup.ELEMENT.querySelector(".qr-code-container IMG");
        const getItem = popup.ELEMENT.querySelector(".get-item");

        ListenToForm(form, function (data) {
            Ajax({
                url: `_updateItem.php`,
                type: "POST",
                data: ToData({id:id, data: JSON.stringify(data)}),
                success: (r) => {
                    popup.Remove();

                    // getItemsOf(activeCategoryID);
                },
            });
        })

        if(getItem) {
            getItem.addEventListener("click", function () {
                GetMyItem(id);
            })
        }

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

                                callback(activeCategoryID)

                                ShowBorrowQR(qr_key);
                            },
                        });
                    }
                })

                pp.Create().then(() => { pp.Show() })
            });
        }

    });
}

export function CreateNewItem(id, callback) {
    const popup = new Popup("equipments/addItem.phtml", {id}, {
        backgroundDismiss: false,
    });

    popup.Create().then(() => {
        popup.Show();

        const form = popup.ELEMENT.querySelector("form");

        ListenToForm(form, function (data) {
            data.equipment_id = id;
            Ajax({
                url: `_insertItem.php`,
                type: "POST",
                data: ToData({data: JSON.stringify(data)}),
                success: (r) => {
                    popup.Remove();

                    callback()
                },
            });
        })
    });
}

export function ShowBorrowQR(qr_key) {
    const popup = new Popup("equipments/showBorrowQR.php", {qr_key}, {
        backgroundDismiss: false,
    });

    popup.Create().then(() => {
        popup.Show();

        const downloadQrBtn = popup.ELEMENT.querySelector(".download-qr");

        const qrcodeImage = popup.ELEMENT.querySelector(".qr-code-container .image-two");

        downloadQrBtn.addEventListener("click", function () {
            DownloadImage(qrcodeImage.src, `bqr-${(new Date()).getTime()}.png`);
        })
    });
}

export function CreateNewEquipment() {
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


export function ShowGettingQR(qr_key) {
    const popup = new Popup("equipments/showGettingQR.php", {qr_key}, {
        backgroundDismiss: false,
    });

    popup.Create().then(() => {
        popup.Show();
        const form = popup.ELEMENT.querySelector("form");

        const downloadQrBtn = popup.ELEMENT.querySelector(".download-qr");

        const qrcodeImage = popup.ELEMENT.querySelector(".qr-code-container .image-two");

        downloadQrBtn.addEventListener("click", function () {
            DownloadImage(qrcodeImage.src, `gqr-${(new Date()).getTime()}.png`);
        })

        ListenToForm(form, function (data) {
            if (data.status) {
                Ajax({
                    url: `_updateGetStatus.php`,
                    type: "POST",
                    data: ToData({ qr_key, status: data.status }),
                    success: (p) => {
                        popup.Remove();

                    },
                });
            }
        })

    });
}

export  function GetMyItem(id) {
    const popup = new Popup("equipments/getItem.php", {id}, {
        backgroundDismiss: false,
    });

    popup.Create().then(() => {
        popup.Show();

        const form = popup.ELEMENT.querySelector("form");

        ListenToForm(form, function (data) {
            Ajax({
                url: `_getItem.php`,
                type: "POST",
                data: ToData({id: id, quantity: data.quantity}),
                success: (qr_key) => {
                    popup.Remove();

                    ShowGettingQR(qr_key);
                },
            });
        })

    })
}