/**
 *
 */
function userForm() {
  $("#user-form").on("submit", function (e) {
    e.preventDefault();
    $(this).find('button[type="submit"]').attr("disabled", true);
    $(this).loading();
    if (
      !callAPIverbose(
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
      )
    ) {
      $("#user-form").loaded();
      $("#user-form").find('button[type="submit"]').attr("disabled", false);
    }
  });
}
