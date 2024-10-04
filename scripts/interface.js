function adjustUIForUserType(userType) {
      const historyLinks = document.querySelectorAll('#menu a[id$="-admin"]');
      historyLinks.forEach(
        (link) =>
        (link.style.display = userType === "admin" ? "block" : "none")
      );

      // Show or hide Add Equipment buttons based on userType
      document.getElementById("add-new-btn").style.display =
        userType === "admin" ? "inline-block" : "none";
      document.getElementById("add-existing-btn").style.display =
        userType === "admin" ? "inline-block" : "none";
      document.getElementById("open-scanner").style.display =
        userType === "admin" ? "inline-block" : "none";
    }

    // Toggle between showing and hiding forms
    document
      .getElementById("add-new-btn")
      .addEventListener("click", function() {
        toggleForm("new-equipment-form", "existing-equipment-form");
      });

    document
      .getElementById("add-existing-btn")
      .addEventListener("click", function() {
        toggleForm("existing-equipment-form", "new-equipment-form");
        fetchExistingEquipment(); // Populate existing equipment dropdown
      });

    function toggleForm(showFormId, hideFormId) {
      const showForm = document.getElementById(showFormId);
      const hideForm = document.getElementById(hideFormId);

      if (showForm.classList.contains("active")) {
        showForm.classList.remove("active"); // Hide the form if it is currently shown
      } else {
        showForm.classList.add("active"); // Show the form
      }

      hideForm.classList.remove("active"); // Always hide the other form
    }
// Initialize UI
document.addEventListener("DOMContentLoaded", function() {
  // Fetch user data and adjust UI
  fetch("fetch_user.php")
    .then((response) => response.json())
    .then((data) => {
      if (data.error) {
        console.error("Error:", data.error);
        return;
      }
      document.getElementById("user-name").textContent = data.username;
      document.getElementById("profile-picture").src =
        "uploads/" + data.profile_picture;
      adjustUIForUserType(data.user_type);
    })
    .catch((error) => console.error("Error fetching user data:", error));
});