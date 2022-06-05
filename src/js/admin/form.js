/**
 * AJAX Formulario de Usuario
 */
function userForm() {
  $("#user-form").on("submit", function (e) {
    e.preventDefault();
    $(this).find('button[type="submit"]').attr("disabled", true);
    $(this).find(".card").loading();
    callAPIverbose(
      $(this).attr("action"),
      $(this).attr("method"),
      new FormData(this),
      $(this).find("input,textarea,select").filter("[required]").length,
      function () {
        $("#user-form .card").loaded();
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
        $("#user-form .card").loaded();
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
    $(this).find(".card").loading();
    callAPIverbose(
      $(this).attr("action"),
      $(this).attr("method"),
      new FormData(this),
      $(this).find("input,textarea,select").filter("[required]").length,
      function () {
        $("#link-form .card").loaded();
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
        $("#link-form .card").loaded();
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
    $(this).find(".card").loading();
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
        $("#profile-links-form .card").loaded();
        $("#profile-links-form")
          .find('button[type="submit"]')
          .attr("disabled", false);
        const form = $("#profile-links-form");
        data.content.tipo_en_error.forEach((tipo) => {
          form.find('input[name="' + tipo + '"').addClass("is-invalid");
        });
        data.content.enlace_en_error.forEach((enlace) => {
          form.find('input[value="' + enlace + '"').addClass("is-invalid");
        });
      },
      function () {
        $("#profile-links-form .card").loaded();
        $("#profile-links-form")
          .find('button[type="submit"]')
          .attr("disabled", false);
      }
    );
  });
}

/**
 * AJAX Formulario de Streams
 */
function profileStreamsForm() {
  $("#profile-streams-form").on("submit", function (e) {
    e.preventDefault();
    $(this).find('button[type="submit"]').attr("disabled", true);
    $(this).find(".card").loading();
    callAPIverbose(
      $(this).attr("action"),
      $(this).attr("method"),
      new FormData(this),
      $(this).find("input,textarea,select").filter("[required]").length,
      function () {
        $("#profile-streams-form .card").loaded();
        $("#profile-streams-form")
          .find('button[type="submit"]')
          .attr("disabled", false);
      },
      function () {
        $("#profile-streams-form .card").loaded();
        $("#profile-streams-form")
          .find('button[type="submit"]')
          .attr("disabled", false);
      }
    );
  });
}

/**
 * AJAX Formulario de Canales YouTube
 */
function profileChannelsForm() {
  $('#profile-channels-form button[btn-action="add"]').on(
    "click",
    function (e) {
      let channelNum =
        $('#profile-channels-form input[name="channel"]').length + 1;
      var label = $("<label></label>")
        .addClass("col-auto col-form-label")
        .html("ID Canal")
        .attr("for", "canal-" + channelNum);
      var input = $("<div></div>")
        .addClass("col")
        .html(
          '<input type="text" class="form-control" id="canal-' +
            channelNum +
            '" name="channel" placeholder="ID de canal de YouTube" minlength="24" maxlength="24"></input>'
        );
      var form_group = $("<div></div>")
        .addClass("form-group row")
        .append(label)
        .append(input);
      $("#profile-channels-form .card-body").append(form_group);
    }
  );
  $("#profile-channels-form").on("submit", function (e) {
    e.preventDefault();
    $(this).find('button[type="submit"]').attr("disabled", true);
    $(this).find(".card").loading();
    var formdata = new Object();
    formdata["channels"] = [];
    new FormData(this).forEach((value, input) => {
      if (value != "") {
        formdata.channels.push(value);
      }
    });
    ajaxCall(
      $(this).attr("action"),
      $(this).attr("method"),
      formdata,
      3,
      function (data) {
        $("#profile-channels-form .card").loaded();
        $("#profile-channels-form")
          .find('button[type="submit"]')
          .attr("disabled", false);
        if (data.status != 200) {
          var errorChannels = Object(data.content).errorChannels;
          $('#profile-channels-form input[name="channel"]').each(function () {
            if (errorChannels.includes($(this).val())) {
              $(this).addClass("is-invalid");
            } else {
              $(this).addClass("is-valid");
            }
          });
        }
      },
      function () {
        $("#profile-channels-form .card").loaded();
        $("#profile-channels-form")
          .find('button[type="submit"]')
          .attr("disabled", false);
      }
    );
  });
}
