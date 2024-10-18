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

export function BorrowItem(id, callback) {
    Ajax({
        url: `_borrowItem.php`,
        type: "POST",
        data: ToData({id: id}),
        success: (res) => {
             res = JSON.parse(res);

            if (res.code == 200) {  
                ShowBorrowQR(res.body.qr_key);

                callback && callback();
            } else {
                alert(res.message);
            }
        },
    });
}

export function ViewEquipment(id, callback) {
    const popup = new Popup("equipments/viewEquipment.php", {id}, {
        backgroundDismiss: false,
    });

    popup.Create().then(() => {
        popup.Show();

        const form = popup.ELEMENT.querySelector("form");
        const picture = popup.ELEMENT.querySelector(".picture");
        let inputPicture = popup.ELEMENT.querySelector("#picture");
        let changed = false;

        ListenToForm(form, async function (data) {
            if (changed) {
                await UploadImageFromFile(inputPicture.files[0], MakeID(10), "./../../uploads/").then((res) => {
                    if (res.code == 200) {
                        data.picture = res.body.path;
                    }
                })
            } else {
                delete data.picture;
            }
            
            Ajax({
                url: `_updateEquipment.php`,
                type: "POST",
                data: ToData({id:id, data: JSON.stringify(data)}),
                success: (r) => {
                    popup.Remove();

                    callback && callback();
                },
            });
        }, ['picture'])

        inputPicture.addEventListener("change", function () {
            picture.style.backgroundImage = `url('${URL.createObjectURL(inputPicture.files[0])}')`;
            changed = true;
        })
    });
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
        const rm = popup.ELEMENT.querySelector(".remove-item");

        ListenToForm(form, function (data) {
            Ajax({
                url: `_updateItem.php`,
                type: "POST",
                data: ToData({id:id, data: JSON.stringify(data)}),
                success: (r) => {
                    popup.Remove();

                    callback && callback();
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

        if (rm) {
            rm.addEventListener("click", function () {
                RemoveItem(id);

                popup.Remove();
            })
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
                        BorrowItem(id, function() {
                            pp.Remove();
                            popup.Remove();

                            callback && callback();
                        });
                    }
                })

                pp.Create().then(() => { pp.Show() })
            });
        }

    });
}

export function CreateNewItem(id, callback) {
    const popup = new Popup("equipments/addItem.php", {id}, {
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

export function CreateNewEquipment(category, callback) {
    const popup = new Popup("equipments/addEquipment.php", {category}, {
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
                            popup.Hide();
                            
                            alert("Equipment Added Successfully");

                            callback &&callback();

                            // getAllCats();
                        },
                    });
                }
            })
        })
    })
}

export function RemoveEquipment(id, callback) {
    Ajax({
        url: `_removeEquipment.php`,
        type: "POST",
        data: ToData({id}),
        success: (r) => {
            alert("Equipment Removed Successfully");

            callback && callback();

        },
    });
}

export function RemoveItem(id, callback) {
    Ajax({
        url: `_removeItem.php`,
        type: "POST",
        data: ToData({id}),
        success: (r) => {
            callback && callback();     

            alert("Item Removed Successfully");
        },
    });
}

export function ShowGettingQR(qr_key, callback) {
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

                        callback && callback();
                    },
                });
            } else if (data.borrow_status) {
                Ajax({
                    url: `_updateGetBorrowStatus.php`,
                    type: "POST",
                    data: ToData({ qr_key, status: data.borrow_status }),   
                    success: (p) => {
                        console.log(p);
                        popup.Remove();

                        callback && callback();
                    },
                })
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
            const pp = new AlertPopup({
                primary: "Get Item?",
                secondary: "Getting Item",
                message: "Are you sure to get this item?"
            }, {
                alert_type: AlertTypes.YES_NO,
            })

            pp.AddListeners({
                onYes: () => {
                    Ajax({
                        url: `_getItem.php`,
                        type: "POST",
                        data: ToData({id: id, quantity: data.quantity}),
                        success: (res) => {
                            res = JSON.parse(res);
        
                            popup.Remove();
        
                            if (res.code == 200) {
                                ShowGettingQR(res.body.qr_key);
                            } else {
                                alert(res.message);
                            }
                        },
                    });
                }
            })  

            pp.Create().then(() => {
                pp.Show()
            })
        })

    })
}

export function GetAllEquipments(start = 0, search = false, category = false, course = false, options = { isAlert: false, used: false, no_add_equipment: false, item_condition: false }) {
    const obj = {start, ...options};

    Object.assign(obj, {
        search,
        category,
        course,
    });

    return new Promise((resolve) => {
        Ajax({
            url: `_getAllEquipments.php`,
            type: "POST",
            data: ToData(obj),
            success: (popup) => {
                resolve(popup);
            },
        });
    });
}
  
export function GetItemsOf(id, start = 0, no_add_equipment = false, isAlert = false, options = { item_condition: false }) {
    const obj = {id, start, ...options};

    if (no_add_equipment) {
        obj.no_add_equipment = no_add_equipment;
    }

    if (isAlert) {
        obj.isAlert = isAlert;
    }

    return new Promise((resolve) => {
        Ajax({
            url: `_getItems.php`,
            type: "POST",
            data: ToData(obj),
            success: (popup) => {
                resolve(popup);
            },
        });
    });
}

export function RemoveEquipmentItem(id) {
    return new Promise((resolve) => {
        const pp = new AlertPopup({
            primary: "Remove this Equipment?",
            secondary: `Removing Equipment`,
            message: "Are you sure to remove this Equipment?"
        }, {
            alert_type: AlertTypes.YES_NO,
        });

        pp.AddListeners({
            onYes: () => {
                RemoveEquipment(id, function () {
                    resolve();
                });
            }
        })

        pp.Create().then(() => { pp.Show() })
    });
}

export function ViewUser(id,  callback) {
    const popup = new Popup("users/viewUser.php", {id}, {
        backgroundDismiss: false,
    });

    popup.Create().then(() => {
        popup.Show();

        const form = popup.ELEMENT.querySelector("form");

        ListenToForm(form, function (data) {
            Ajax({
                url: `_updateUser.php`,
                type: "POST",
                data: ToData({id: id, data: JSON.stringify(data)}),
                success: (r) => {
                    popup.Remove(); 
                    callback && callback();
                },
            });
        })
    });
}