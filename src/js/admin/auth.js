/** Authentificación de usuarios
 *
 */
function login() {
  $("#login-form").on("submit", function (e) {
    e.preventDefault();
    $(this).find('button[type="submit"]').attr("disabled", true);
    $(this).loading();
    callAPI(
      $(this).attr("action"),
      $(this).attr("method"),
      new FormData(this),
      $(this).find("input,textarea,select").filter("[required]").length,
      2,
      function () {
        window.location.href = $("#login-form #destino").val();
      },
      function () {
        $("#login-form").loaded();
        $("#login-form").find('button[type="submit"]').attr("disabled", false);
      }
    );
  });
}

/** Creación de OTP
 *
 */
function createOTP() {
  $("#create-otp form").on("submit", function (e) {
    e.preventDefault();
    $(this).find('button[type="submit"]').attr("disabled", true);
    $(this).loading();
    callAPI(
      $(this).attr("action"),
      $(this).attr("method"),
      new FormData(this),
      $(this).find("input,textarea,select").filter("[required]").length,
      2,
      function () {
        $("#create-otp").addClass("d-none");
        $("#finalMessage").removeClass("d-none");
      },
      function () {
        $("#create-otp form").loaded();
        $("#create-otp form")
          .find('button[type="submit"]')
          .attr("disabled", false);
      }
    );
  });
}

/** Invalidation de OTP
 *
 */
function invalidateOTP() {
  $("#invalidate-otp-form").on("submit", function (e) {
    e.preventDefault();
    $(this).find('button[type="submit"]').attr("disabled", true);
    $(this).loading();
    callAPI(
      $(this).attr("action"),
      $(this).attr("method"),
      new FormData(this),
      $(this).find("input,textarea,select").filter("[required]").length,
      2,
      function () {
        window.location.href = "/";
      },
      function () {
        $("#invalidate-otp-form").loaded();
        $("#invalidate-otp-form")
          .find('button[type="submit"]')
          .attr("disabled", false);
      }
    );
  });
}

/** Validation del formulario de restablecimeinto de contraseña
 *
 */
function validateNewPasswordForm() {
  $("#new-password-form #clave , #new-password-form #clave2").on(
    "input keyup keypress blur change",
    function () {
      if (
        validatePass(
          $("#new-password-form #clave"),
          $("#new-password-form #clave2")
        )
      ) {
        $(this)
          .closest("form")
          .find('button[type="submit"]')
          .removeAttr("disabled");
      } else {
        $(this)
          .closest("form")
          .find('button[type="submit"]')
          .attr("disabled", "disabled");
      }
    }
  );
}

/** Restablecimiento de contraseña
 *
 */
function newPassword() {
  $("#new-password-form").on("submit", function (e) {
    e.preventDefault();
    $(this).find('button[type="submit"]').attr("disabled", true);
    $(this).loading();
    callAPIverbose(
      $(this).attr("action"),
      $(this).attr("method"),
      new FormData(this),
      $(this).find("input,textarea,select").filter("[required]").length,
      function () {
        window.location.href = "/login";
      },
      function () {
        $("#new-password-form").loaded();
        $("#new-password-form")
          .find('button[type="submit"]')
          .attr("disabled", false);
      }
    );
  });
}
