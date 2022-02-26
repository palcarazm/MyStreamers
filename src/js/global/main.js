/**
 * Llama a un api y muestra alertas en función del estado
 * @param {String} uri Dirección de la APi
 * @param {String} method Tipo de solicitud
 * @param {FormData} formdata Datos a enviar
 * @param {int} requiered Valores requeridos
 * @param {int} verbose Tipo de llamada 0--> No verbose, 1 --> solo validacion, 2 --> Validacion y error, 3 --> Verbose
 * @param {function} success función a ejecutar en caso de éxito
 * @param {function} error función a ejecutar en caso de error
 * @returns
 */
function callAPI(
  uri,
  method,
  formdata,
  requiered,
  verbose = 1,
  success = null,
  error = null
) {
  let length = 0;
  var object = {};
  formdata.forEach((value,input) => {
    length++;
    if (input == null || input == "") {
      if (verbose > 0) {
        swal({
          type: "warning",
          title: "Error",
          html: "Todos los campos son obligatorios",
          timer: 3000,
        });
      }
      return false;
    }
    object[input] = value;
  });
  if (length < requiered) {
    if (verbose > 0) {
      swal({
        type: "warning",
        title: "Error",
        html: "Todos los campos son obligatorios",
        timer: 3000,
      });
    }
    return false;
  }
  $.ajax({
    type: method,
    data: JSON.stringify(object),
    url: uri,
    dataType: "json",
    contentType: false,
    processData: false,
    async: true,
    cache: false,
    success: function (data) {
      //console.log(data);
      if (success == null) {
        if (verbose > 2) {
          swal({
            type: "success",
            title: "Éxito",
            html: data.responseJSON.message,
            timer: 2000,
          });
        }
      } else {
        if (verbose > 2) {
          swal({
            type: "success",
            title: "Éxito",
            html: data.responseJSON.message,
            timer: 2000,
          }).then(() => success());
        } else {
          success();
        }
      }
    },
    error: function (data) {
      //console.log(data);
      if (error == null) {
        if (verbose > 1) {
          swal({
            type: "error",
            title: "Error",
            html: data.responseJSON.message,
          });
        }
      } else {
        if (verbose > 1) {
          swal({
            type: "error",
            title: "Error",
            html: data.responseJSON.message,
          }).then(() => error());
        } else {
          error();
        }
      }
    },
  });
  return true;
}

/**
 * Llama a un api y muestra alertas en función del estado
 * @param {String} uri Dirección de la APi
 * @param {String} method Tipo de solicitud
 * @param {FormData} formdata Datos a enviar
 * @param {int} requiered Valores requeridos
 * @param {function} success función a ejecutar en caso de éxito
 * @param {function} error función a ejecutar en caso de error
 */
function callAPIverbose(
  uri,
  method,
  formdata,
  requiered,
  success = null,
  error = null
) {
  return callAPI( uri,
    method,
    formdata,
    requiered,
    3,
    success,
    error)
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
/**
 * Muestra un overlay de carga
 */
jQuery.fn.loading = function () {
  return this.css("position", "relative").append(
    $("<div></div>")
      .addClass("overlay loading")
      .append($("<i></i>").addClass("fas fa-spinner fa-spin-ease"))
  );
};
/**
 * Elimina el overlay de carga
 */
jQuery.fn.loaded = function () {
  return this.find(".overlay.loading").remove();
};
/**
 * Intercambia los estados de carga
 */
jQuery.fn.loadToggle = function () {
  return this.has(".overlay.loading").length > 0
    ? this.loaded()
    : this.loading();
};
