const CONFIG_STEPS = 6;

/** Configurar base de datos
 *
 */
function configDB() {
  $("#configDatabase form").on("submit", function (e) {
    e.preventDefault();
    $(this).find('button[type="submit"]').attr("disabled", true);
    $(this).loadToggle();
    callAPIverbose(
      $(this).attr("action"),
      $(this).attr("method"),
      new FormData(this),
      $(this).find("input,textarea,select").filter("[required]").length,
      function () {
        $("#configDatabase form").loaded();
        $("#configDatabase.step").removeClass("current").addClass("done");
        $("#configDatabase.step h2").addClass("text-success");
        $("#configAdmin.step ").removeClass("todo").addClass("current");
        $("#progreso").width($("#progreso").width()+100/CONFIG_STEPS+'%');
      },
      function () {
        $("#configDatabase form").loaded();
        $("#configDatabase form").find('button[type="submit"]').attr("disabled", false);
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
    $(this).loading();
    callAPIverbose(
      $(this).attr("action"),
      $(this).attr("method"),
      new FormData(this),
      $(this).find("input,textarea,select").filter("[required]").length,
      function () {
        $("#configAdmin form").loaded();
        $("#configAdmin.step").removeClass("current").addClass("done");
        $("#configAdmin.step h2").addClass("text-success");
        $("#configEmail.step ").removeClass("todo").addClass("current");
        $("#configEmail form #adminEmail").val(
          $("#configAdmin form #email").val()
        );
        $("#progreso").width($("#progreso").width()+100/CONFIG_STEPS+'%');
      },
      function () {
        $("#configAdmin form").loaded();
        $("#configAdmin form").find('button[type="submit"]').attr("disabled", false);
      }
    );
  });
}

/** Configurar Servidor de Email
 *
 */
function configEmail() {
  $("#configEmail form").on("submit", function (e) {
    e.preventDefault();
    $(this).find('button[type="submit"]').attr("disabled", true);
    $(this).loading();
    callAPIverbose(
      $(this).attr("action"),
      $(this).attr("method"),
      new FormData(this),
      $(this).find("input,textarea,select").filter("[required]").length,
      function () {
        swal({
          type: "info",
          title: "Comprueba tu E-mail",
          html: "Hemos mandado un email de prueba al correo del administrador.<br><b>??Lo has recibido?</b>",
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
                  title: "??xito",
                  html: data.message,
                  timer: 2000,
                }).then(() => {
                  $("#configEmail form").loaded();
                  $("#configEmail.step")
                    .removeClass("current")
                    .addClass("done");
                  $("#configEmail.step h2").addClass("text-success");
                  $("#configSite.step ")
                    .removeClass("todo")
                    .addClass("current");
                  $("#progreso").width($("#progreso").width()+100/CONFIG_STEPS+'%');
                });
              },
              error: function (data) {
                swal({
                  type: "error",
                  title: "Error",
                  html: data.message,
                }).then(() => {
                  $("#configEmail form").loaded();
                  $("#configEmail form").find('button[type="submit"]').attr("disabled", false);
                });
              },
            });
          }
        });
      },
      function () {
        $("#configEmail form").loaded();
        $("#configEmail form").find('button[type="submit"]').attr("disabled", false);
      }
    );
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
            .text("La descripci??n del sitio es obligatoria")
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
    $(this).loading();
    callAPIverbose(
      $(this).attr("action"),
      $(this).attr("method"),
      new FormData(this),
      $(this).find("input,textarea,select").filter("[required]").length,
      function () {
        $("#configSite form").loaded();
        $("#configSite.step").removeClass("current").addClass("done");
        $("#configSite.step h2").addClass("text-success");
        $("#configTwitch.step ").removeClass("todo").addClass("current");
        $("#progreso").width($("#progreso").width()+100/CONFIG_STEPS+'%');
      },
      function () {
        $("#configSite form").loaded();
        $("#configSite form").find('button[type="submit"]').attr("disabled", false);
      }
    );
  });
}

/**
 * Configura conexi??n con Twitch
 */
 function configTwitch() {
  $("#configTwitch form").on("submit", function (e) {
    e.preventDefault();
    $(this).find('button[type="submit"]').attr("disabled", true);
    $(this).loading();
    callAPIverbose(
      $(this).attr("action"),
      $(this).attr("method"),
      new FormData(this),
      $(this).find("input,textarea,select").filter("[required]").length,
      function () {
        $("#configTwitch form").loaded();
        $("#configTwitch.step").removeClass("current").addClass("done");
        $("#configTwitch.step h2").addClass("text-success");
        $("#configYoutube.step ").removeClass("todo").addClass("current");
        $("#progreso").width($("#progreso").width()+100/CONFIG_STEPS+'%');
      },
      function () {
        $("#configTwitch form").loaded();
        $("#configTwitch form").find('button[type="submit"]').attr("disabled", false);
      }
    );
  });
}

/**
 * Configura conexi??n con YouTube
 */
 function configYoutube() {
  $("#configYoutube form").on("submit", function (e) {
    e.preventDefault();
    $(this).find('button[type="submit"]').attr("disabled", true);
    $(this).loading();
    callAPIverbose(
      $(this).attr("action"),
      $(this).attr("method"),
      new FormData(this),
      $(this).find("input,textarea,select").filter("[required]").length,
      function () {
        $("#configYoutube form").loaded();
        $("#configYoutube.step").removeClass("current").addClass("done");
        $("#configYoutube.step h2").addClass("text-success");
        $("#finalMessage.step ").removeClass("todo").addClass("current");
        $("#progreso").width("100%");
      },
      function () {
        $("#configYoutube form").loaded();
        $("#configYoutube form").find('button[type="submit"]').attr("disabled", false);
      }
    );
  });
}

/**
 * Actualiza la configuraci??n del sitio
 */
function updateSite() {
  $("#updateSite-form").on("submit", function (e) {
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
            .text("La descripci??n del sitio es obligatoria")
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
    $(this).loading();
    callAPI(
      $(this).attr("action"),
      $(this).attr("method"),
      new FormData(this),
      $(this).find("input,textarea,select").filter("[required]").length,
      3,
      function () {
        $("#updateSite-form").loaded();
        $("#updateSite-form").find('button[type="submit"]').attr("disabled", false);
      },
      function () {
        $("#updateSite-form").find('button[type="submit"]').attr("disabled", false);
        $("#updateSite-form").loaded();
      },
      $("#token").val()
    );
  });
}

/**
 * Actualiza la configuraci??n de la conexi??n con Twitch
 */
function updateTwitch() {
  $("#updateTwitch-form").on("submit", function (e) {
    e.preventDefault();
    $(this).find('button[type="submit"]').attr("disabled", true);
    $(this).loading();
    callAPI(
      $(this).attr("action"),
      $(this).attr("method"),
      new FormData(this),
      $(this).find("input,textarea,select").filter("[required]").length,
      3,
      function () {
        $("#updateTwitch-form").loaded();
        $("#updateTwitch-form").find('button[type="submit"]').attr("disabled", false);
      },
      function () {
        $("#updateTwitch-form").find('button[type="submit"]').attr("disabled", false);
        $("#updateTwitch-form").loaded();
      }
    );
  });
}

/**
 * Actualiza la configuraci??n de la conexi??n con YouTube
 */
function updateYoutube() {
  $("#updateYoutube-form").on("submit", function (e) {
    e.preventDefault();
    $(this).find('button[type="submit"]').attr("disabled", true);
    $(this).loading();
    callAPI(
      $(this).attr("action"),
      $(this).attr("method"),
      new FormData(this),
      $(this).find("input,textarea,select").filter("[required]").length,
      3,
      function () {
        $("#updateYoutube-form").loaded();
        $("#updateYoutube-form").find('button[type="submit"]').attr("disabled", false);
      },
      function () {
        $("#updateYoutube-form").find('button[type="submit"]').attr("disabled", false);
        $("#updateYoutube-form").loaded();
      }
    );
  });
}

/**
 * Valida el formulario de configuraci??n de administrador
 */
function validAdminuserForm() {
  $(
    "#configAdmin form #pass2 , #configAdmin form #pass , #configAdmin form #email"
  ).on("input keyup keypress blur change", function () {
    const validPass = validatePass(
      $("#configAdmin form #pass"),
      $("#configAdmin form #pass2")
    );
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
 * Valida el email del Formulario de configuraci??n de afministrador
 * @returns bool Validaci??n superada (Si/No)
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
          .text("El correo electr??nico debe ser v??lido")
      );
    return false;
  }
}
