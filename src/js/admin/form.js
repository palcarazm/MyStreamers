/**
 * AJAX Formulario de Usuario
 */
function userForm() {
  $("#user-form").on("submit", function (e) {
    e.preventDefault();
    $(this).find('button[type="submit"]').attr("disabled", true);
    $(this).loading();
    callAPIverbose(
      $(this).attr("action"),
      $(this).attr("method"),
      new FormData(this),
      $(this).find("input,textarea,select").filter("[required]").length,
      function () {
        $("#user-form").loaded();
        $("#user-form").find('button[type="submit"]').attr("disabled", false);
        if ($("#user-form").attr("form-success")) {
          if ($("#user-form").attr("form-success") == "redirect") {
            window.location.href = $("#user-form").attr("destino");
          }
          if ($("#user-form").attr("form-success") == "reset") {
            $("#user-form").trigger("reset");
          }
        }
      },
      function () {
        $("#user-form").loaded();
        $("#user-form").find('button[type="submit"]').attr("disabled", false);
      }
    );
  });
}

/**
 * AJAX Formulario de tipos de enlaces
 */
 function linkForm() {
  $("#link-form").on("submit", function (e) {
    e.preventDefault();
    $(this).find('button[type="submit"]').attr("disabled", true);
    $(this).loading();
    callAPIverbose(
      $(this).attr("action"),
      $(this).attr("method"),
      new FormData(this),
      $(this).find("input,textarea,select").filter("[required]").length,
      function () {
        $("#link-form").loaded();
        $("#link-form").find('button[type="submit"]').attr("disabled", false);
        if ($("#link-form").attr("form-success")) {
          if ($("#link-form").attr("form-success") == "redirect") {
            window.location.href = $("#link-form").attr("destino");
          }
          if ($("#link-form").attr("form-success") == "reset") {
            $("#link-form").trigger("reset");
          }
        }
      },
      function () {
        $("#link-form").loaded();
        $("#link-form").find('button[type="submit"]').attr("disabled", false);
      }
    );
  });
}

/**
 * AJAX Formulario de enlaces públicos
 */
function profileLinksForm() {
  $("#profile-links-form").on("submit", function (e) {
    e.preventDefault();
    $(this).find('button[type="submit"]').attr("disabled", true);
    $(this).loading();
    var formdata = new Object();
    formdata["enlaces"] = [];
    var enlace;
    new FormData(this).forEach((value, input) => {
      if (value != "") {
        enlace = new Object();
        enlace["id"] = input;
        enlace["enlace"] = value;
        formdata.enlaces.push(enlace);
      }
    });

    ajaxCall(
      $(this).attr("action"),
      $(this).attr("method"),
      formdata,
      3,
      function (data) {
        $("#profile-links-form").loaded();
        $("#profile-links-form")
          .find('button[type="submit"]')
          .attr("disabled", false);
          const form =  $("#profile-links-form");
          data.content.tipo_en_error.forEach(tipo => {
            form.find('input[name="'+tipo+'"').addClass('is-invalid')
        });
          data.content.enlace_en_error.forEach(enlace => {
            form.find('input[value="'+enlace+'"').addClass('is-invalid')
        });
      },
      function () {
        $("#profile-links-form").loaded();
        $("#profile-links-form")
          .find('button[type="submit"]')
          .attr("disabled", false);
      }
    );
  });
}

/**
 * AJAX Formulario de Usuario
 */
 function profileStreamsForm() {
  $("#profile-streams-form").on("submit", function (e) {
    e.preventDefault();
    $(this).find('button[type="submit"]').attr("disabled", true);
    $(this).loading();
    callAPIverbose(
      $(this).attr("action"),
      $(this).attr("method"),
      new FormData(this),
      $(this).find("input,textarea,select").filter("[required]").length,
      function () {
        $("#profile-streams-form").loaded();
        $("#profile-streams-form").find('button[type="submit"]').attr("disabled", false);
      },
      function () {
        $("#profile-streams-form").loaded();
        $("#profile-streams-form").find('button[type="submit"]').attr("disabled", false);
      }
    );
  });
}