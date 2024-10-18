// Submit New Equipment
    function submitNewEquipment() {
      const name = document.getElementById("name").value;
      const alertLevel = document.getElementById("alert_level").value;
      const description = document.getElementById("description").value;
      const price = document.getElementById("price").value;
      const picture = document.getElementById("picture").files[0];
      const category = document.getElementById("category").value;
      const course = document.getElementById("course").value;

      console.log("Form Data:", {
        name,
        alertLevel,
        description,
        price,
        picture,
        category,
        course,
      });

      if (
        !name ||
        !alertLevel ||
        !description ||
        !price ||
        !picture ||
        !category ||
        !course
      ) {
        alert(
          "Please fill out all fields, select a category, and upload a picture."
        );
        return;
      }

      const formData = new FormData();
      formData.append("name", name);
      formData.append("alert_level", alertLevel);
      formData.append("description", description);
      formData.append("price", price);
      formData.append("picture", picture);
      formData.append("category", category);
      formData.append("course", course);

      fetch("add_equipment.php", {
          method: "POST",
          body: formData,
        })
        .then((response) => {
          if (!response.ok) {
            return response.text(); // Get response body as text
          }
          return response.json();
        })
        .then((data) => {
          if (data.success) {
            alert("New equipment added successfully");
          } else {
            alert("Error adding equipment: " + data.message);
          }
        })
        .catch((error) => {
          console.error("Fetch Error:", error);
          alert("Error submitting form: " + error.message);
        });
    }
    function submitExistingEquipment() {
      const selectedEquipment = document
        .getElementById("existing-equipment")
        .value.trim();
      const additionalQuantity = document
        .getElementById("additional-quantity")
        .value.trim();
      const location = document.getElementById("location").value.trim();
      const serials = document.getElementById("serials").value.trim();

      console.log("Selected Equipment:", selectedEquipment);
      console.log("Additional Quantity:", additionalQuantity);
      console.log("Location:", location);
      console.log("Serials:", serials);

      if (selectedEquipment === "") {
        alert("Please select an equipment.");
        return;
      }
      if (additionalQuantity === "") {
        alert("Please enter the additional quantity.");
        return;
      }
      if (location === "") {
        alert("Please enter the location.");
        return;
      }
      if (serials === "") {
        alert("Please enter the serial number.");
        return;
      }

      const formData = new FormData();
      formData.append("equipment_id", selectedEquipment);
      formData.append("additional_quantity", additionalQuantity);
      formData.append("location", location);
      formData.append("serials", serials);

      fetch("add_existing_equipment.php", {
          method: "POST",
          body: formData,
        })
        .then((response) => {
          if (!response.ok) {
            throw new Error("Network response was not ok");
          }
          return response.json();
        })
        .then((data) => {
          if (data.success) {
            alert("Existing equipment updated successfully");
          } else {
            alert(
              "Error updating equipment: " + (data.message || "Unknown error")
            );
          }
        })
        .catch((error) => {
          console.error("Fetch Error:", error.message);
          alert("Error submitting form: " + error.message);
        });
    }