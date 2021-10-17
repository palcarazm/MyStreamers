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
 * Valida el formulario de configuración de administrador
 */
function validAdminuserForm() {
  $(
    "#configAdmin form #pass2 , #configAdmin form #pass , #configAdmin form #email"
  ).on("input keyup keypress blur change", function () {
    const validPass = validPassAdminuserForm();
    const validEmail = validEmailAdminuserForm();
    if (validPass && validEmail) {
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
  });
}

/**
 * Valida las contraseñas del Formulario de configuración de administrador
 * @returns bool Validación superada (Si/No)
 */
function validPassAdminuserForm() {
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
    return false;
  } else if (!checkPasswordStrength(String(password.val()).trim())) {
    password.addClass("is-invalid").removeClass("is-valid");
    password
      .parent()
      .append(
        $("<div></div>")
          .addClass("invalid-feedback")
          .text(
            "La contraseña debe contener al menos una minúscula, una mayúscula, un número, un carácter especial y ser de almenos 8 carácteres"
          )
      );
    return false;
  } else {
    passwordRepeat.removeClass("is-invalid").addClass("is-valid");
    password.removeClass("is-invalid").addClass("is-valid");
    return true;
  }
}

/**
 * Valida el email del Formulario de configuración de afministrador
 * @returns bool Validación superada (Si/No)
 */
function validEmailAdminuserForm() {
  const email = $("#configAdmin form #email");
  email.parent().find(".invalid-feedback").remove();
  if (validateEmail(String(email.val()).trim())) {
    email.removeClass("is-invalid").addClass("is-valid");
    return true;
  } else {
    email.addClass("is-invalid").removeClass("is-valid");
    email
      .parent()
      .append(
        $("<div></div>")
          .addClass("invalid-feedback")
          .text("El correo electrónico debe ser válido")
      );
    return false;
  }
}

/**
 * Configura el sitio
 */
function configSite() {
  $("#siteConfig form").on("submit", function (e) {
    e.preventDefault();
    if (
      $(this)
        .find("#descripcion")
        .val()
        .replace(/<[^>]+>/g, "") == ""
    ) {
      $(this)
        .find("#descripcion")
        .closest(".form-group")
        .append(
          $("<div></div>")
            .addClass("invalid-feedback d-block")
            .text("La descripción del sitio es obligatoria")
        );
      return;
    } else {
      $(this)
        .find("#descripcion")
        .closest(".form-group")
        .find(".invalid-feedback")
        .remove();
    }
    $(this).find('button[type="submit"]').attr("disabled", true);
    $(this).loadToggle();
    callAPIverbose(
      $(this).attr("action"),
      $(this).attr("method"),
      new FormData(this),
      $(this).find("input,textarea,select").filter("[required]").length,
      function () {
        $("#siteConfig form").loadToggle();
        $("#siteConfig.step").removeClass("current").addClass("done");
        $("#siteConfig.step h2").addClass("text-success");
        $("#siteConfig.step ").removeClass("todo").addClass("current");
        $("#progreso").width("67%");
      },
      function () {
        $("#siteConfig form").loadToggle();
        $("#siteConfig form")
          .find('button[type="submit"]')
          .attr("disabled", false);
      }
    );
  });
}
