alert("Pcx Clone Script Loaded");

var button = document.getElementsByClassName("fa-magnifying-glass")[0];

button.addEventListener("click", function () {
  var modal = new bootstrap.Modal(document.getElementById("exampleModal"));
  modal.show();
});

// var agencyButton = document.getElementById("agencyButton");

// var agencyDropdown = new bootstrap.Dropdown(
//   document.getElementById("agencyDropdown")
// );

// agencyButton.addEventListener("click", function (e) {
//   e.preventDefault(); // tránh reload trang
//   agencyDropdown.toggle(); // mở / đóng dropdown
// });
