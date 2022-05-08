function userList() {
  $(".data").on("click", function (e) {
    e.preventDefault();
    var target = $(e.target);
    if (target.is("i")) {
      target = target.parent();
    }
    if (target.is("a")) {
      window.location = target.attr("href");
    } else if (target.is("button")) {
      var success;
      var error;
      switch (target.attr("btn-action")) {
        case "profile-create":
          success = function () {
            target.hide();
            target.closest('td').siblings('td[col-data="profile"]').html('Si');
            target.siblings('a').show();
          };
          error = null;
          break;
        case "profile-lock":
          success = function () {
            target.hide();
            target.siblings('button[btn-action="profile-unlock"]').show();
          };
          error = null;
          break;
        case "profile-unlock":
          success = function () {
            target.hide();
            target.siblings('button[btn-action="profile-lock"]').show();
          };
          error = null;
          break;
        case "pass-reset":
          success = null;
          error = null;
          break;
        case "user-lock":
          success = function () {
            target.hide();
            target.siblings('button[btn-action="user-unlock"]').show();
          };
          error = null;
          break;
        case "user-unlock":
          success = function () {
            target.hide();
            target.siblings('button[btn-action="user-lock"]').show();
          };
          error = null;
          break;
        case "user-delete":
          success = function () {
            target.closest("tr").remove();
          };
          error = null;
          break;
        default:
          break;
      }
      switch (target.attr("btn-action-type")) {
        case "send":
          ajaxCall(
            target.attr("btn-uri"),
            target.attr("btn-method"),
            JSON.parse(target.attr("btn-data")),
            3,
            success,
            error
          );
          break;
        case "confirm":
          swal({
            type: "info",
            title: "Acción irreversible",
            html: "¿Estas seguro?",
            showCancelButton: true,
          }).then(function (confirm) {
            if (confirm.value) {
              ajaxCall(
                target.attr("btn-uri"),
                target.attr("btn-method"),
                JSON.parse(target.attr("btn-data")),
                3,
                success,
                error
              );
            }
          });
          break;
        default:
          break;
      }
    }
  });
}

function linkList() {
  $(".data").on("click", function (e) {
    e.preventDefault();
    var target = $(e.target);
    if (target.is("i")) {
      target = target.parent();
    }
    if (target.is("a")) {
      window.location = target.attr("href");
    } else if (target.is("button")) {
      var success;
      var error;
      switch (target.attr("btn-action")) {
        case "link-delete":
          success = function () {
            target.closest("tr").remove();
          };
          error = null;
          break;
        default:
          break;
      }
      switch (target.attr("btn-action-type")) {
        case "send":
          ajaxCall(
            target.attr("btn-uri"),
            target.attr("btn-method"),
            JSON.parse(target.attr("btn-data")),
            3,
            success,
            error
          );
          break;
        case "confirm":
          swal({
            type: "info",
            title: "Acción irreversible",
            html: "¿Estas seguro?",
            showCancelButton: true,
          }).then(function (confirm) {
            if (confirm.value) {
              ajaxCall(
                target.attr("btn-uri"),
                target.attr("btn-method"),
                JSON.parse(target.attr("btn-data")),
                3,
                success,
                error
              );
            }
          });
          break;
        default:
          break;
      }
    }
  });
}