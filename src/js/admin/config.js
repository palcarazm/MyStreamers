// CONFIGURACIÓN
/** Configurar base de datos
 *
 */
 function verifyDB() {
    $("#verifyDB form").on("submit", function (e) {
      e.preventDefault();
      callAPIverbose(
        $(this).attr("action"),
        $(this).attr("method"),
        new FormData(this),
        $(this).find("input,textarea,select").filter("[required]").length,
        function () {
          $('#verifyDB.step').removeClass('current').addClass('done');
          $('#verifyDB.step h2').addClass('text-success');
          $('#createAdmin.step ').removeClass('todo').addClass('current');
          $('#progreso').width("33%");
        }
      );
    });
  }