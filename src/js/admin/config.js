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
          $('#verifyDB.step .card-subtitle i').removeClass('fa-circle text-muted').addClass('fa-check-circle text-success');
          $('#createAdmin.step ').removeClass('todo').addClass('current');
          $('#progreso').width("10%");
        }
      );
    });
  }