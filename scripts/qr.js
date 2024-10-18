function generateQRCode(userInfo, itemInfo) {
      return new Promise((resolve, reject) => {
        const qrData = {
          name: userInfo,
          item: itemInfo,
        };

        const qrCodeContainer = document.getElementById("qr-code-content");
        qrCodeContainer.innerHTML = ""; // Clear previous QR codes

        const qr = new QRCode(qrCodeContainer, {
          text: JSON.stringify(qrData),
          width: 200,
          height: 200,
          colorDark: "#000000",
          colorLight: "#ffffff",
          correctLevel: QRCode.CorrectLevel.H,
        });

        // Make the download button visible
        const downloadBtn = document.getElementById("download-btn");
        downloadBtn.style.display = "inline-block";

        qrCodeContainer.querySelector("canvas").toBlob(function(blob) {
          if (blob) {
            const url = URL.createObjectURL(blob);
            downloadBtn.href = url;
            resolve(url); // Resolve the promise with the URL
          } else {
            reject(new Error("Failed to create QR code blob"));
          }
        }, "image/png");

        // Show the QR modal
        document.getElementById("qr-modal").style.display = "flex";
      });

      // Make the download button visible
      const downloadBtn = document.getElementById("download-btn");
      downloadBtn.style.display = "inline-block";

      // Update the download button's href
      qrCodeContainer.querySelector("canvas").toBlob(function(blob) {
        const url = URL.createObjectURL(blob);
        downloadBtn.href = url;
      }, "image/png");

      // Show the QR modal
      document.getElementById("qr-modal").style.display = "flex";
    }

    // Close modal functionality
    document
      .getElementById("close-qr-modal")
      .addEventListener("click", function() {
        document.getElementById("qr-modal").style.display = "none";
      });

    // Global variable to store the current item ID
    let currentItemId;

    function handleBorrowClick(event) {
      currentItemId = event.target.getAttribute("data-item-id"); // Store the current item ID
      document.getElementById("confirm-modal").style.display = "flex"; // Show confirmation modal
    }

    // Event listeners for confirmation buttons
    document.getElementById("confirm-yes").addEventListener("click", () => {
      document.getElementById("confirm-modal").style.display = "none"; // Hide confirmation modal
      borrowItem(currentItemId); // Call borrowItem with the current item ID
    });

    document.getElementById("confirm-no").addEventListener("click", () => {
      document.getElementById("confirm-modal").style.display = "none"; // Hide confirmation modal
    });

    function borrowItem(itemId) {
      console.log("Borrowing Item ID:", itemId);

      fetch(`borrow_equipment.php?item_id=${itemId}`)
        .then((response) => response.json())
        .then((data) => {
          if (!data.success) {
            throw new Error("Error borrowing item: " + data.message);
          }

          const userInfo = {
            name: data.userInfo.name,
            phone: data.userInfo.phone,
            user_type: data.userInfo.user_type,
            student_id: data.userInfo.student_id,
          };

          const itemInfo = {
            name: data.itemInfo.name,
            serials: data.itemInfo.serials,
            location: data.itemInfo.location,
            date_rcvd: data.itemInfo.date_rcvd,
          };

          // Generate QR code and wait for the URL
          return generateQRCode(userInfo, itemInfo).then((qrCodeUrl) => {
            const historyPayload = {
              user_name: userInfo.name,
              user_phone: userInfo.phone,
              user_type: userInfo.user_type,
              student_id: userInfo.student_id,
              equipment_name: itemInfo.name,
              serial_number: itemInfo.serials,
              location: itemInfo.location,
              date_received: itemInfo.date_rcvd,
              qr_code_image: qrCodeUrl,
            };

            return fetch("insert_borrow_history.php", {
              method: "POST",
              headers: {
                "Content-Type": "application/json",
              },
              body: JSON.stringify(historyPayload),
            });
          });
        })
        .then((historyResponse) => historyResponse.json())
        .then((historyData) => {
          if (historyData.success) {
            alert(
              "Item borrowed successfully! QR code generated and stored in history."
            );
          } else {
            alert("Failed to store in history: " + historyData.error);
          }
        })
        .catch((error) => {
          console.error("Borrow error:", error);
          alert("An error occurred while borrowing the item: " + error);
        });
    }

    // DOMContentLoaded Event Listener
    document.addEventListener("DOMContentLoaded", () => {
      // Event listeners for borrow button clicks
      document.querySelectorAll(".borrow-button").forEach((button) => {
        button.addEventListener("click", handleBorrowClick);
      });

      // Open scanner modal
      document
        .getElementById("open-scanner")
        .addEventListener("click", function() {
          document.getElementById("scanner-modal").style.display = "block";
        });

      // Close scanner modal functionality
      document
        .getElementById("close-scanner")
        .addEventListener("click", function() {
          document.getElementById("scanner-modal").style.display = "none";
          stopScanner();
        });

      // File upload event listener for QR code
      document
        .getElementById("upload-qrcode")
        .addEventListener("change", function(event) {
          const file = event.target.files[0];
          if (!file) {
            alert("No file selected.");
            return;
          }

          const reader = new FileReader();
          reader.onload = function(e) {
            const image = new Image();
            image.src = e.target.result;
            image.onload = function() {
              const canvas = document.createElement("canvas");
              const context = canvas.getContext("2d");
              canvas.width = image.width;
              canvas.height = image.height;
              context.drawImage(image, 0, 0);
              const imageData = context.getImageData(
                0,
                0,
                canvas.width,
                canvas.height
              );
              const qr = jsQR(imageData.data, canvas.width, canvas.height);
              if (qr) {
                document.getElementById("qr-code-result").innerText = qr.data;
              } else {
                alert("No QR code found.");
              }
            };
          };
          reader.readAsDataURL(file);
        });
    });

    function startScanner() {
      if (!html5QrCode) {
        html5QrCode = new Html5Qrcode("scanner");
      }

      html5QrCode
        .start({
            facingMode: "environment",
          }, {
            fps: 10,
            qrCodeSuccessCallback: function(decodedText, decodedResult) {
              document.getElementById("qr-code-result").innerText =
                decodedText;
              // Optionally stop the scanner after a successful scan
              html5QrCode.stop().then(() => {
                html5QrCode = null; // Clear the instance after stopping
              });

              // Optionally send the scanned data to the server
              fetch("fetch_qr_code_data.php", {
                  method: "POST",
                  headers: {
                    "Content-Type": "application/json",
                  },
                  body: JSON.stringify({
                    qrCodeData: decodedText,
                  }),
                })
                .then((response) => response.json())
                .then((data) => {
                  if (data.success) {
                    alert("QR Code data processed successfully.");
                  } else {
                    alert("Error processing QR Code data: " + data.message);
                  }
                })
                .catch((error) => console.error("Error:", error));
            },
          },
          function(errorMessage) {
            console.log(errorMessage);
          }
        )
        .catch((err) => {
          console.error(err);
        });
    }

    function stopScanner() {
      if (html5QrCode) {
        html5QrCode
          .stop()
          .then(() => {
            html5QrCode = null; // Clear the instance after stopping
          })
          .catch((err) => {
            console.error("Error stopping the scanner:", err);
          });
      }
    }

    function borrowItem(itemId) {
      console.log("Borrowing Item ID:", itemId);

      fetch(`borrow_equipment.php?item_id=${itemId}`)
        .then((response) => response.json())
        .then((data) => {
          if (!data.success) {
            throw new Error("Error borrowing item: " + data.message);
          }

          const userInfo = {
            name: data.userInfo.name,
            phone: data.userInfo.phone,
            user_type: data.userInfo.user_type,
            student_id: data.userInfo.student_id,
          };

          const itemInfo = {
            name: data.itemInfo.name,
            serials: data.itemInfo.serials,
            location: data.itemInfo.location,
            date_rcvd: data.itemInfo.date_rcvd,
          };

          console.log("User Info:", userInfo);
          console.log("Item Info:", itemInfo);

          // Ensure generateQRCode returns a valid Promise
          return generateQRCode(userInfo, itemInfo).then((qrCodeUrl) => {
            console.log("Generated QR Code URL:", qrCodeUrl); // Log the QR code URL

            const historyPayload = {
              user_name: userInfo.name,
              user_phone: userInfo.phone,
              user_type: userInfo.user_type,
              student_id: userInfo.student_id,
              equipment_name: itemInfo.name,
              serial_number: itemInfo.serials,
              location: itemInfo.location,
              date_received: itemInfo.date_rcvd,
            };

            console.log("History Payload:", historyPayload); // Log this payload

            return fetch("insert_borrow_history.php", {
              method: "POST",
              headers: {
                "Content-Type": "application/json",
              },
              body: JSON.stringify(historyPayload),
            });
          });
        })
        .then((historyResponse) => historyResponse.json())
        .then((historyData) => {
          if (historyData.success) {
            alert(
              "Item borrowed successfully! QR code generated and stored in history."
            );
          } else {
            alert("Failed to store in history: " + historyData.error);
          }
        })
        .catch((error) => {
          console.error("Borrow error:", error);
          alert(
            "An error occurred while borrowing the item: " + error.message
          );
        });
    }