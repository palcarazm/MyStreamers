$(function () {
  switch (currentURL()) {
    case "/config":
      togglePassword();
      verifyDB();
      configAdmin();
      validPass();
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
function checkPasswordStrength(password) {
  return new RegExp(
    "(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*[^A-Za-z0-9])(?=.{8,})"
  ).test(password);
}
