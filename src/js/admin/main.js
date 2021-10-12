$(function () {
  switch (currentURL()) {
    case "/config":
      togglePassword();
      verifyDB();
      break;

    default:
      break;
  }
});

// Funciones
function togglePassword() {
  $(".toggle-password").on("click", function () {
    if ($(this).find("i").hasClass("fa-eye")) {
      $(this).closest(".input-group").find("input").attr("type", "text");
      $(this).find("i").addClass("fa-eye-slash").removeClass("fa-eye");
    } else {
      $(this).closest(".input-group").find("input").attr("type", "password");
      $(this).find("i").addClass("fa-eye").removeClass("fa-eye-slash");
    }
  });
}