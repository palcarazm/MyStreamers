$(function () {
  switch (currentURL()) {
    case "/config":
      togglePassword();
      verifyDB();
      configAdmin();
      validAdminuserForm();
      break;

    default:
      break;
  }
});

// Funciones
/**
 * Muestra y oculta la contraseña al apretar un boton
 */
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
/**
 * Compurueba la seguridad de la contraseña
 * @param {String} password 
 * @returns cumple las reglas de seguridad (Si/No)
 */
function checkPasswordStrength(password) {
  return new RegExp(
    "(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*[^A-Za-z0-9])(?=.{8,})"
  ).test(password);
}
/**
 * Valida que el texto inproducido es un email
 * @param {String} email 
 * @returns es un email (Si/No)
 */
function validateEmail(email) {
  const re = /^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
  return re.test(String(email).toLowerCase());
}