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