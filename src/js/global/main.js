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
        if (success == null) {
          swal({
            type: "success",
            title: "Éxito",
            html: data.message,
            timer: 2000,
          });
        } else {
          swal({
            type: "success",
            title: "Éxito",
            html: data.message,
            timer: 2000,
          }).then(() => success());
        }
      },
      error: function (data) {
        //console.log(data);
        if (error == null) {
          swal({
            type: "error",
            title: "Error",
            html: data.message,
          });
        } else {
          swal({
            type: "error",
            title: "Error",
            html: data.message,
          }).then(() => error());
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
  