// Fetch Existing Equipment
    function fetchExistingEquipment() {
      fetch("fetch_existing_equipment.php")
        .then((response) => response.json())
        .then((data) => {
          if (!data.success) {
            throw new Error(
              "Error fetching existing equipment: " + data.message
            );
          }

          const dropdown = document.getElementById("existing-equipment");
          dropdown.innerHTML =
            '<option value="">-- Select Equipment --</option>'; // Reset dropdown

          if (Array.isArray(data.equipment)) {
            data.equipment.forEach((equipment) => {
              const option = document.createElement("option");
              option.value = equipment.id;
              option.textContent = equipment.name;
              dropdown.appendChild(option);
            });
          } else {
            console.error(
              "Data format error: Expected an array for equipment"
            );
          }
        })
        .catch((error) =>
          console.error("Error fetching existing equipment:", error)
        );
    }

    // Fetch Equipment
    document.addEventListener("DOMContentLoaded", () => {
      const equipmentListDiv = document.getElementById("equipment-list");
      const availableItemsDiv = document.getElementById("available-items");
      const backButton = document.getElementById("back-button");

      function filterItems() {
        const searchInput = document
          .getElementById("search-input")
          .value.toLowerCase();
        const categoryFilter =
          document.getElementById("category-filter").value;
        const items = document.querySelectorAll(".item");

        items.forEach((item) => {
          const itemName = item
            .querySelector("strong")
            .textContent.toLowerCase();
          const itemCategory = item.getAttribute("data-category");

          const matchesSearch = itemName.includes(searchInput);
          const matchesCategory = !categoryFilter || itemCategory === categoryFilter;

          if (matchesSearch && matchesCategory) {
            item.style.display = ""; // Show item
          } else {
            item.style.display = "none"; // Hide item
          }
        });
      }

      // Attach event listeners
      document
        .getElementById("search-input")
        .addEventListener("input", filterItems);
      document
        .getElementById("category-filter")
        .addEventListener("change", filterItems);

      function fetchEquipment() {
        getUserCourse().then((userCourse) => {
          fetch("get_equipment.php")
            .then((response) => response.json())
            .then((data) => {
              if (!data.success) {
                throw new Error("Error fetching equipment: " + data.message);
              }

              equipmentListDiv.innerHTML = ""; // Clear previous equipment items
              availableItemsDiv.style.display = "none"; // Hide available items section
              equipmentListDiv.style.display = "block"; // Show equipment list

              if (Array.isArray(data.equipment)) {
                data.equipment.forEach((item) => {
                  // Check if course exists
                  if (item.course) {
                    // Normalize the comparison
                    const itemCourse = item.course.trim().toLowerCase();
                    const normalizedUserCourse = userCourse
                      .trim()
                      .toLowerCase();

                    // Check if the item's course matches the user's course
                    if (itemCourse === normalizedUserCourse) {
                      const itemDiv = document.createElement("div");
                      itemDiv.classList.add("item");
                      itemDiv.setAttribute(
                        "data-category",
                        item.category ?
                        item.category.trim().toLowerCase() :
                        ""
                      ); // Set category

                      const alertLevel = parseInt(item.alert_level, 10);
                      const available = parseInt(item.available, 10);

                      let backgroundColor = "#fff"; // Default background color
                      if (alertLevel >= available) {
                        backgroundColor = "#ffcccc"; // Light red color
                      }

                      itemDiv.style.backgroundColor = backgroundColor;

                      itemDiv.innerHTML = `
                                    <img src="uploads/${item.picture}" alt="${item.name}">
                                    <p><strong>${item.name}</strong></p>
                                    <p>Available: ${item.available}</p>
                                    <p>Description: ${item.description}</p>
                                    <button class="borrow-button" data-equipment-id="${item.id}">Show Details</button>
                                `;
                      equipmentListDiv.appendChild(itemDiv);
                    }
                  } else {
                    console.warn(
                      `Item with ID ${item.id} does not have a course defined.`
                    );
                  }
                });

                document
                  .querySelectorAll(".borrow-button")
                  .forEach((button) => {
                    button.addEventListener("click", function() {
                      const equipmentId =
                        this.getAttribute("data-equipment-id");
                      fetchAvailableItems(equipmentId);
                    });
                  });
              } else {
                console.error(
                  "Data format error: Expected an array for equipment"
                );
                alert("Unexpected data format received from the server.");
              }
            })
            .catch((error) => {
              console.error("Fetch error:", error);
              alert(
                "An error occurred while fetching equipment. Check the console for details."
              );
            });
        });
      }

      function filterItems() {
        const searchInput = document
          .getElementById("search-input")
          .value.toLowerCase();
        const categoryFilter = document
          .getElementById("category-filter")
          .value.toLowerCase();
        const items = document.querySelectorAll(".item");

        items.forEach((item) => {
          const itemName = item
            .querySelector("strong")
            .textContent.toLowerCase();
          const itemCategory = item.getAttribute("data-category"); // Get the category

          // Allow all categories to show if "all" is selected
          const matchesCategory = itemCategory === categoryFilter;

          const matchesSearch = itemName.includes(searchInput);

          if (matchesSearch && matchesCategory) {
            item.style.display = ""; // Show item
          } else {
            item.style.display = "none"; // Hide item
          }
        });
      }

      function getUserCourse() {
        return fetch("get_course.php")
          .then((response) => {
            if (!response.ok) {
              throw new Error("Network response was not ok");
            }
            return response.json();
          })
          .then((data) => {
            if (data.error) {
              console.error("Error fetching user course:", data.error);
              return null;
            }
            return data.course; // Return the user's course
          })
          .catch((error) => {
            console.error("Fetch error:", error);
            return null;
          });
      }

      function fetchAvailableItems(equipmentId) {
        fetch(`get_available_items.php?equipment_id=${equipmentId}`)
          .then((response) => response.json())
          .then((data) => {
            if (!data.success) {
              throw new Error(
                "Error fetching available items: " + data.message
              );
            }

            availableItemsDiv.innerHTML =
              '<button id="back-button">Back</button>'; // Add back button dynamically
            availableItemsDiv.style.display = "block"; // Show available items section
            equipmentListDiv.style.display = "none"; // Hide equipment list

            data.items.forEach((item) => {
              const itemDiv = document.createElement("div");
              itemDiv.classList.add("item");

              itemDiv.innerHTML = `
                        <img src="uploads/${item.picture}" alt="Item Picture" style="max-width: 100px; height: auto;">
                        <p><strong>${item.name}</strong></p>
                        <p>Serial: ${item.serials}</p>
                        <p>Location: ${item.location}</p>
                        <p>Date Received: ${item.date_rcvd}</p>
                        <button class="borrow-button" data-item-id="${item.id}">Borrow</button>
                    `;
              availableItemsDiv.appendChild(itemDiv);
            });

            // Bind the handleBorrowClick function to the newly created borrow buttons
            document.querySelectorAll(".borrow-button").forEach((button) => {
              button.addEventListener("click", handleBorrowClick);
            });
          })
          .catch((error) => {
            console.error("Fetch error:", error);
            alert(
              "An error occurred while fetching available items. Check the console for details."
            );
          });
      }

      document.addEventListener("click", (event) => {
        if (event.target && event.target.id === "back-button") {
          fetchEquipment(); // Refresh and show the equipment list
        }
      });

      fetchEquipment();
    });