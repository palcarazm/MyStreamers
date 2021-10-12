/** Configurar base de datos
 *
 */
function verifyDB() {
  $("#verifyDB form").on("submit", function (e) {
    e.preventDefault();
    $(this).find('button[type="submit"]').attr("disabled", true);
    $(this).loadToggle();
    callAPIverbose(
      $(this).attr("action"),
      $(this).attr("method"),
      new FormData(this),
      $(this).find("input,textarea,select").filter("[required]").length,
      function () {
        $("#verifyDB form").loadToggle();
        $("#verifyDB.step").removeClass("current").addClass("done");
        $("#verifyDB.step h2").addClass("text-success");
        $("#configAdmin.step ").removeClass("todo").addClass("current");
        $("#progreso").width("33%");
      },
      function () {
        $("#verifyDB form").loadToggle();
        $("#verifyDB form")
          .find('button[type="submit"]')
          .attr("disabled", false);
      }
    );
  });
}
/** Configurar administrador
 *
 */
function configAdmin() {
  $("#configAdmin form").on("submit", function (e) {
    e.preventDefault();
    $(this).find('button[type="submit"]').attr("disabled", true);
    $(this).loadToggle();
    callAPIverbose(
      $(this).attr("action"),
      $(this).attr("method"),
      new FormData(this),
      $(this).find("input,textarea,select").filter("[required]").length,
      function () {
        $("#configAdmin form").loadToggle();
        $("#configAdmin.step").removeClass("current").addClass("done");
        $("#configAdmin.step h2").addClass("text-success");
        $("#siteConfig.step ").removeClass("todo").addClass("current");
        $("#progreso").width("67%");
      },
      function () {
        $("#configAdmin form").loadToggle();
        $("#configAdmin form")
          .find('button[type="submit"]')
          .attr("disabled", false);
      }
    );
  });
}

/**
 * Valida contraseñas coincidentes
 */
function validPass() {
  $("#configAdmin form #pass2 , #configAdmin form #pass").on(
    "input keyup keypress blur change",
    function () {
      const password = $("#configAdmin form #pass");
      const passwordRepeat = $("#configAdmin form #pass2");
      password.parent().find(".invalid-feedback").remove();
      passwordRepeat.parent().find(".invalid-feedback").remove();
      if (passwordRepeat.val() != password.val()) {
        password
          .parent()
          .append(
            $("<div></div>")
              .addClass("invalid-feedback")
              .text("Las contraseñas no coinciden")
          );
        passwordRepeat
          .parent()
          .append(
            $("<div></div>")
              .addClass("invalid-feedback")
              .text("Las contraseñas no coinciden")
          );
        passwordRepeat.addClass("is-invalid").removeClass("is-valid");
        password.addClass("is-invalid").removeClass("is-valid");
        $(this)
          .closest("form")
          .find('button[type="submit"]')
          .attr("disabled", "disabled");
      } else if (!checkPasswordStrength(trim($(this).val()))) {
        $(this).addClass("is-invalid").removeClass("is-valid");
        $(this)
          .parent()
          .append(
            $("<div></div>")
              .addClass("invalid-feedback")
              .text(
                "La contraseña debe contener al menos una minúscula, una mayúscula, un número, un carácter especial y ser de almenos 8 carácteres"
              )
          );
        $(this)
          .closest("form")
          .find('button[type="submit"]')
          .attr("disabled", "disabled");
      } else {
        passwordRepeat.removeClass("is-invalid").addClass("is-valid");
        password.removeClass("is-invalid").addClass("is-valid");
        $(this)
          .closest("form")
          .find('button[type="submit"]')
          .removeAttr("disabled");
      }
    }
  );
}
