import Popup from "./Popup.js";
import {Ajax, ListenToForm, MakeID, ToData, UploadImageFromFile, ListenToOriginalSelect} from "./Tool.js";
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
            Confirm("Update Equipment?", "Updating Equipment", "Are you sure to update this equipment?", async function () {
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
        const qrcodeImage = popup.ELEMENT.querySelector(".qr-code-container .image-two");
        const getItem = popup.ELEMENT.querySelector(".get-item");
        const rm = popup.ELEMENT.querySelector(".remove-item");

        ListenToForm(form, function (data) {
            Confirm("Update Item?", "Updating Item", "Are you sure to update this item?", function () {
                new Promise((resolve) => {
                    if (data.picture.name == "") {
                        resolve(false);
                    } else {
                        UploadImageFromFile(data.picture, MakeID(10), "./../../uploads/").then((res) => {
                            if (res.code == 200){
                                resolve(res.body.path);
                            } else {
                                resolve(false);
                            }
                        })
                    }
                }).then((res) => {
                    if (res) {
                        data.picture = res;
                    } else {
                        delete data.picture;
                    }
    
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
            });
        }, ['picture', 'alert_level', 'quantity'])

        if(getItem) {
            getItem.addEventListener("click", function () {
                GetMyItem(id, callback);
            })
        }

        if (dl) {
            dl.addEventListener("click", function() {
                DownloadImage(qrcodeImage.src, `item-${id}-qr-code.png`);
            });
        }

        if (rm) {
            rm.addEventListener("click", function () {
                const pp = new AlertPopup({
                    primary: "Remove Item?",
                    secondary: `Removing item`,
                    message: "Are you sure to remove this Item?"
                }, {
                    alert_type: AlertTypes.YES_NO,
                });

                pp.AddListeners({
                    onYes: () => {
                        RemoveItem(id, callback);

                        popup.Remove();
                    }
                })

                pp.Create().then(() => { pp.Show() })
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
            
            Confirm("Create Item?", "Creating Item", "Are you sure to create this item?", function () {
                new Promise((resolve) => {
                    if (data.picture.name == "") {
                        resolve(false);
                    } else {
                        UploadImageFromFile(data.picture, MakeID(10), "./../../uploads/").then((res) => {
                            if (res.code == 200){
                                resolve(res.body.path);
                            } else {
                                resolve(false);
                            }
                        })
                    }
                }).then((res) => {
                    if (res) {
                        data.picture = res;
                    } else {
                        delete data.picture;
                    }
    
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
        }, ['picture'])
    });
}

export function Confirm(primary, secondary, message, callback) {
    const pp = new AlertPopup({
        primary,
        secondary,
        message,
    }, {
        alert_type: AlertTypes.YES_NO,
    });

    pp.AddListeners({
        onYes: () => {
            callback && callback();
        }
    })

    pp.Create().then(() => { pp.Show() })
}

export function ShowBorrowQR(qr_key) {
    const popup = new Popup("equipments/showBorrowQR.php", {qr_key}, {
        backgroundDismiss: false,
    });

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
        const status = popup.ELEMENT.querySelector("select[name=borrow_status]");
        const condition = popup.ELEMENT.querySelector("select[name=item_condition]");
        const conditionContainer = popup.ELEMENT.querySelector(".condition-container");

        downloadQrBtn.addEventListener("click", function () {
            DownloadImage(qrcodeImage.src, `gqr-${(new Date()).getTime()}.png`);
        });

        ListenToOriginalSelect(status, value => {
            conditionContainer.classList.toggle("hide-component", value !== "returned");
        });

        ListenToForm(form, function (data) {
            Confirm("Update Item?", "Updating Item", "Are you sure to update this item?", function () {
                if (data.borrow_status === 'returned' && !data.item_condition) {
                    alert("Please select the item condition");
                    return;
                }
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
                        data: ToData({ qr_key, status: data.borrow_status,  condition: data.item_condition }),   
                        success: (p) => {
                            popup.Remove();
    
                            callback && callback();
                        },
                    })
                }
            });
        }, ['item_condition']);

    });
}

export  function GetMyItem(id, callback) {
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
                                ShowGettingQR(res.body.qr_key, callback);
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

export function ChangePassword(id, callback) {
    const popup = new Popup("users/changePassword.php", {id}, {
        backgroundDismiss: false,
    });

    popup.Create().then(() => {
        popup.Show();

        const form = popup.ELEMENT.querySelector("form");

        ListenToForm(form, function (data) {

            delete data.confirm_password;

            Confirm("Update Password?", "Updating Password", "Are you sure to update this password?", function () {
                Ajax({
                    url: `_updatePassword.php`,
                    type: "POST",
                    data: ToData({id: id, data: JSON.stringify(data)}),
                    success: (r) => {
                        console.log(r);
                        popup.Remove();
                        callback && callback();
                    },
                });
            });
        }, [], [{
            input: "password",
            matched: ["confirm_password"],
        }])
    })
}

export function ViewUser(id,  callback) {
    const popup = new Popup("users/viewUser.php", {id}, {
        backgroundDismiss: false,
    });

    popup.Create().then(() => {
        popup.Show();

        const form = popup.ELEMENT.querySelector("form");
        const changePasswordButton = popup.ELEMENT.querySelector(".change-password-button");

        ListenToForm(form, function (data) {
            Confirm("Update User?", "Updating User", "Are you sure to update this user?", function () {
                Ajax({
                    url: `_updateUser.php`,
                    type: "POST",
                    data: ToData({id: id, data: JSON.stringify(data)}),
                    success: (r) => {
                        popup.Remove(); 
                        callback && callback();
                    },
                });
            });
        })

        changePasswordButton.addEventListener("click", function () {
            ChangePassword(id, callback);
        })
    });
}