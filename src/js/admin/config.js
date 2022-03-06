/** Configurar base de datos
 *
 */
function configDB() {
  $("#configDatabase form").on("submit", function (e) {
    e.preventDefault();
    $(this).find('button[type="submit"]').attr("disabled", true);
    $(this).loadToggle();
    if (
      !callAPIverbose(
        $(this).attr("action"),
        $(this).attr("method"),
        new FormData(this),
        $(this).find("input,textarea,select").filter("[required]").length,
        function () {
          $("#configDatabase form").loadToggle();
          $("#configDatabase.step").removeClass("current").addClass("done");
          $("#configDatabase.step h2").addClass("text-success");
          $("#configAdmin.step ").removeClass("todo").addClass("current");
          $("#progreso").width("25%");
        },
        function () {
          $("#configDatabase form").loadToggle();
          $("#configDatabase form")
            .find('button[type="submit"]')
            .attr("disabled", false);
        }
      )
    ) {
      $("#configDatabase form").loadToggle();
      $("#configDatabase form")
        .find('button[type="submit"]')
        .attr("disabled", false);
    }
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
    if (
      !callAPIverbose(
        $(this).attr("action"),
        $(this).attr("method"),
        new FormData(this),
        $(this).find("input,textarea,select").filter("[required]").length,
        function () {
          $("#configAdmin form").loadToggle();
          $("#configAdmin.step").removeClass("current").addClass("done");
          $("#configAdmin.step h2").addClass("text-success");
          $("#configEmail.step ").removeClass("todo").addClass("current");
          $("#configEmail form #adminEmail").val(
            $("#configAdmin form #email").val()
          );
          $("#progreso").width("50%");
        },
        function () {
          $("#configAdmin form").loadToggle();
          $("#configAdmin form")
            .find('button[type="submit"]')
            .attr("disabled", false);
        }
      )
    ) {
      $("#configAdmin form").loadToggle();
      $("#configAdmin form")
        .find('button[type="submit"]')
        .attr("disabled", false);
    }
  });
}

/** Configurar Servidor de Email
 *
 */
function configEmail() {
  $("#configEmail form").on("submit", function (e) {
    e.preventDefault();
    $(this).find('button[type="submit"]').attr("disabled", true);
    $(this).loadToggle();
    if (
      !callAPIverbose(
        $(this).attr("action"),
        $(this).attr("method"),
        new FormData(this),
        $(this).find("input,textarea,select").filter("[required]").length,
        function () {
          swal({
            type: "info",
            title: "Comprueba tu E-mail",
            html: "Hemos mandado un email de prueba al correo del administrador.<br><b>¿Lo has recibido?</b>",
            showCancelButton: true,
          }).then(function (isConfirm) {
            if (isConfirm) {
              $.ajax({
                type: "put",
                data: new FormData(),
                url: "/api/config/v1/email?confirm=true",
                dataType: "json",
                contentType: false,
                processData: false,
                async: true,
                cache: false,
                success: function (data) {
                  swal({
                    type: "success",
                    title: "Éxito",
                    html: data.message,
                    timer: 2000,
                  }).then(() => {
                    $("#configEmail form").loadToggle();
                    $("#configEmail.step")
                      .removeClass("current")
                      .addClass("done");
                    $("#configEmail.step h2").addClass("text-success");
                    $("#configSite.step ")
                      .removeClass("todo")
                      .addClass("current");
                    $("#progreso").width("75%");
                  });
                },
                error: function (data) {
                  swal({
                    type: "error",
                    title: "Error",
                    html: data.message,
                  }).then(() => {
                    $("#configEmail form").loadToggle();
                    $("#configEmail form")
                      .find('button[type="submit"]')
                      .attr("disabled", false);
                  });
                },
              });
            }
          });
        },
        function () {
          $("#configEmail form").loadToggle();
          $("#configEmail form")
            .find('button[type="submit"]')
            .attr("disabled", false);
        }
      )
    ) {
      $("#configEmail form").loadToggle();
      $("#configEmail form")
        .find('button[type="submit"]')
        .attr("disabled", false);
    }
  });
}

/**
 * Configura el sitio
 */
function configSite() {
  $("#configSite form").on("submit", function (e) {
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
    if (
      callAPIverbose(
        $(this).attr("action"),
        $(this).attr("method"),
        new FormData(this),
        $(this).find("input,textarea,select").filter("[required]").length,
        function () {
          $("#configSite form").loadToggle();
          $("#configSite.step").removeClass("current").addClass("done");
          $("#configSite.step h2").addClass("text-success");
          $("#configSite.step ").removeClass("todo").addClass("current");
          $("#finalMessage.step ")
                      .removeClass("todo")
                      .addClass("current");
          $("#progreso").width("100%");
        },
        function () {
          $("#configSite form").loadToggle();
          $("#configSite form")
            .find('button[type="submit"]')
            .attr("disabled", false);
        }
      )
    ) {
      $("#configSite form").loadToggle();
      $("#configSite form")
        .find('button[type="submit"]')
        .attr("disabled", false);
    }
  });
}

/**
 * Valida el formulario de configuración de administrador
 */
function validAdminuserForm() {
  $(
    "#configAdmin form #pass2 , #configAdmin form #pass , #configAdmin form #email"
  ).on("input keyup keypress blur change", function () {
    const validPass = validatePass($("#configAdmin form #pass") , $("#configAdmin form #pass2"));
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
