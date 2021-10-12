// CONFIGURACIÓN
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
          $('#verifyDB.step').removeClass('current').addClass('done');
          $('#verifyDB.step h2').addClass('text-success');
          $('#createAdmin.step ').removeClass('todo').addClass('current');
          $('#progreso').width("33%");
        },
        function (){
          $("#verifyDB form").loadToggle();
          $('#verifyDB form').find('button[type="submit"]').attr("disabled", false);
        }
      );
    });
  }