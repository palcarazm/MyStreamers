// CONSTANTES

// MAIN
/** Funcion principal
 *
 */
$(function () {
  switch (currentURL()) {
    case "/config":
      verifyDB();
      break;

    default:
      break;
  }
});

// GLOBALES
/**
 * Llama a un api desde un formulario
 * @param {String} uri Dirección de la APi
 * @param {String} method Tipo de solicitud
 * @param {FormData} formdata Datos a enviar
 * @param {int} requiered Valores requeridos
 * @param {function} callback función a ejecutar en caso de éxito
 */
function callAPI(uri, method, formdata, requiered, callback) {
  let length = 0;
  formdata.forEach((input) => {
    length++;
    if (input == null || input == "") {
      swal({
        type: "warning",
        title: "Error",
        html: "Todos los campos son obligatorios",
        timer: 3000,
      });
      return;
    }
  });
  if (length < requiered) {
    swal({
      type: "warning",
      title: "Error",
      html: "Todos los campos son obligatorios",
      timer: 3000,
    });
    return;
  }
  $.ajax({
    type: method,
    data: formdata,
    url: uri,
    dataType: "json",
    contentType: false,
    processData: false,
    async: true,
    cache: false,
    success: function (data) {
      //console.log(data);
      if (data.status == "200") {
        swal({
          type: "success",
          title: "Información actualizada",
          html: data.message,
          timer: 2000,
        }).then(()=>callback());
      } else {
        swal({
          type: "error",
          title: "Error",
          html: data.message,
        });
      }
    },
  });
}
/** Devuelve la URL de la página actual
 *
 * @returns URL actual
 */
function currentURL() {
  return document.location.href
    .replace(document.location.origin, "")
    .replace(".php", "")
    .split("?")[0]
    .split("#")[0];
}
