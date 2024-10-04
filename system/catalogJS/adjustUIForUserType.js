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