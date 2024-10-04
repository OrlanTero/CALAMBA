import {
    Ajax,
    CreateElement,
    DisableScroll,
    EnableScroll,
    ExecuteFn,
    HideShowComponent,
    ToData,
} from "./Tool.js";

export class Popup {
    constructor(CONTENT_URL, DATA = {}, OPTIONS) {
        this.CONTENT_URL = CONTENT_URL;
        this.DATA = DATA;
        this.OPTIONS = this.InitOptions(OPTIONS);
        this.PARENT = this.FindMyParent();
        this.ELEMENT = null;
        this.LISTENERS = {};
    }

    AddListeners(listeners) {
        this.LISTENERS = {
            ...listeners,
            ...this.LISTENERS
        }
    }

    InitOptions(option = {}) {
        return {
            fromURL: option.fromURL === undefined ? true : option.fromURL,
            backgroundDismiss: option.backgroundDismiss ?? false,
            removeOnDismiss: option.removeOnDismiss ?? false,
        };
    }

    FindMyParent() {
        const parentID = "big-pipes-container";
        const parent = document.querySelector("#" + parentID);

        const newParent = CreateElement({
            el: "section",
            id: parentID,
        });

        if (!parent) {
            document.querySelector("#system-popup").appendChild(newParent);
        }

        return parent ?? newParent;
    }

    Create() {
        const pop = this;

        return new Promise((resolve) => {
            if (pop.OPTIONS.fromURL === true) {
                Ajax({
                    url: `components/popup/${pop.CONTENT_URL}`,
                    type: "POST",
                    data: ToData({data: JSON.stringify(pop.DATA)}),
                    success: (popup) => {
                        pop.Place(popup);
                        resolve(pop);
                    },
                });
            } else {
                Ajax({
                    url: "components/popup/dialog",
                    type: "POST",
                    data: ToData({
                        data: JSON.stringify(this.CONTENT_URL),
                        options: JSON.stringify(pop.DATA),
                    }),
                    success: (popup) => {
                        ExecuteFn(pop.LISTENERS, "onCreate")
                        pop.Place(popup);
                        resolve(pop);
                    },
                });
            }
        });
    }


    Place(popup, node = false) {
        if (popup && this.PARENT) {
            this.ELEMENT = node
                ? this.CreateDefaultElement(popup)
                : CreateElement({
                    el: "SPAN",
                    html: popup,
                });

            this.PARENT.appendChild(this.ELEMENT);

            this.Listeners();

            this.Show();

            ExecuteFn(this.LISTENERS, "onPlace");
        }
    }

    Listeners() {
        const obj = this;
        if (this.PARENT && this.ELEMENT) {
            const background = this.ELEMENT.querySelector(".popup-background");
            const closeBtn = this.ELEMENT.querySelector(".close-popup");

            background.addEventListener("click", () => {
                if (this.OPTIONS.backgroundDismiss) {
                    if (this.OPTIONS.removeOnDismiss) {
                        this.Remove();
                    } else {
                        this.Hide();
                    }
                } else {
                    this.Shake();
                }
            });

            if (closeBtn) {
                closeBtn.addEventListener("click", () => {
                    this.Remove();
                });
            }

           document.addEventListener("keydown", function(e){
                if(e.key === "Escape"){
                    obj.Remove();
                }
            })
        }
    }

    Show() {
        if (!this.ELEMENT) return false;

        DisableScroll();
        HideShowComponent(this.ELEMENT, true);
        ExecuteFn(this.LISTENERS, "onShow");

        return this.ELEMENT;
    }

    Hide() {
        if (!this.ELEMENT) return false;

        EnableScroll();
        HideShowComponent(this.ELEMENT, false);
        ExecuteFn(this.LISTENERS, "onHide");
    }

    Shake() {
        const element = this.ELEMENT.querySelector(".popup-content");
        const child = element.querySelector("div");

        child.classList.add("apply-shake");

        setTimeout(function () {
            child.classList.remove("apply-shake");
        }, 1100);
    }

    Remove() {
        if (!this.ELEMENT) return false;

        EnableScroll()

        this.ELEMENT.remove();

        ExecuteFn(this.LISTENERS, "onRemove");
    }
}

export default Popup;
