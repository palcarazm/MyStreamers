function login() {
  $("#login-form").on("submit", function (e) {
    e.preventDefault();
    $(this).find('button[type="submit"]').attr("disabled", true);
    $(this).loadToggle();
    if (
      !callAPI(
        $(this).attr("action"),
        $(this).attr("method"),
        new FormData(this),
        $(this).find("input,textarea,select").filter("[required]").length,
        2,
        function () {
          window.location.href = $("#login-form #destino").val();
        },
        function () {
          $("#login-form").loadToggle();
          $("#login-form")
            .find('button[type="submit"]')
            .attr("disabled", false);
        }
      )
    ) {
      $("#login-form").loadToggle();
      $("#login-form").find('button[type="submit"]').attr("disabled", false);
    }
  });
}

function createOTP() {
  $("#create-otp form").on("submit", function (e) {
    e.preventDefault();
    $(this).find('button[type="submit"]').attr("disabled", true);
    $(this).loadToggle();
    if (
      !callAPI(
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
          $("#create-otp form").loadToggle();
          $("#create-otp form")
            .find('button[type="submit"]')
            .attr("disabled", false);
        }
      )
    ) {
      $("#create-otp form").loadToggle();
      $("#create-otp form").find('button[type="submit"]').attr("disabled", false);
    }
  });
}

function invalidateOTP() {
  $("#invalidate-otp-form").on("submit", function (e) {
    e.preventDefault();
    $(this).find('button[type="submit"]').attr("disabled", true);
    $(this).loadToggle();
    if (
      !callAPI(
        $(this).attr("action"),
        $(this).attr("method"),
        new FormData(this),
        $(this).find("input,textarea,select").filter("[required]").length,
        2,
        function () {
          window.location.href = '/';
        },
        function () {
          $("#invalidate-otp-form").loadToggle();
          $("#invalidate-otp-form")
            .find('button[type="submit"]')
            .attr("disabled", false);
        }
      )
    ) {
      $("#invalidate-otp-form").loadToggle();
      $("#invalidate-otp-form").find('button[type="submit"]').attr("disabled", false);
    }
  });
}

function validateNewPasswordForm() {
  $(
    "#new-password-form #clave , #new-password-form #clave2"
  ).on("input keyup keypress blur change", function () {
    if (validatePass($("#new-password-form #clave") , $("#new-password-form #clave2"))) {
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

function newPassword() {
  $("#new-password-form").on("submit", function (e) {
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
          window.location.href = '/login';
        },
        function () {
          $("#new-password-form").loadToggle();
          $("#new-password-form")
            .find('button[type="submit"]')
            .attr("disabled", false);
        }
      )
    ) {
      $("#new-password-form").loadToggle();
      $("#new-password-form").find('button[type="submit"]').attr("disabled", false);
    }
  });
}