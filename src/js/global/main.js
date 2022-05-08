/**
 * Llama a un api y muestra alertas en función del estado
 * @param {String} uri Dirección de la APi
 * @param {String} method Tipo de solicitud
 * @param {FormData} formdata Datos a enviar
 * @param {int} requiered Valores requeridos
 * @param {int} verbose Tipo de llamada 0--> No verbose, 1 --> solo validacion, 2 --> Validacion y error, 3 --> Verbose
 * @param {function} success función a ejecutar en caso de éxito
 * @param {function} error función a ejecutar en caso de error
 * @param {string} authorization cadena de autorización para incorporar al header
 * @returns
 */
function callAPI(
  uri,
  method,
  formdata,
  requiered,
  verbose = 1,
  success = null,
  error = null,
  authorization = null
) {
  let length = 0;
  var data = {};
  var waiting = false;
  var file;
  var metadata;
  var file_input;

  formdata.forEach((value, input) => {
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
    if (typeof value == "object") {
      if (value["size"] > 0) {
        file = value;
        metadata = {
          name: value["name"],
          type: value["type"],
          size: value["size"],
        };
        file_input = input;
        waiting = true;
      }
    } else {
      data[input] = value;
    }
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
  if (!waiting) {
    ajaxCall(uri, method, data, verbose, success, error, authorization);
  } else {
    readUploadedFileAsBinary(
      file,
      metadata,
      file_input,
      uri,
      method,
      data,
      verbose,
      success,
      error,
      authorization
    );
  }
}

/**
 * Lanza la llamada Ajax y muestra alertas en función del estado
 * @param {String} uri Dirección de la APi
 * @param {String} method Tipo de solicitud
 * @param {Object} data Datos a enviar
 * @param {int} verbose Tipo de llamada 0--> No verbose, 1 --> solo validacion, 2 --> Validacion y error, 3 --> Verbose
 * @param {function} success función a ejecutar en caso de éxito
 * @param {function} error función a ejecutar en caso de error
 * @param {string} authorization cadena de autorización para incorporar al header
 * @returns
 */
function ajaxCall(
  uri,
  method,
  data,
  verbose = 1,
  success = null,
  error = null,
  authorization = null
) {
  try {
    if (method.toUpperCase() == "GET") {
      $.get(uri)
        .beforeSend(function (xhr) {
          if (authorization != null) {
            xhr.setRequestHeader("Authorization", authorization);
          }
        })
        .done(function (data) {
          if (success == null) {
            if (verbose > 2) {
              swal({
                type: "success",
                title: "Éxito",
                html: data.message,
                timer: 2000,
              });
            }
          } else {
            if (verbose > 2) {
              if (success.length == 0) {
                try {
                  swal({
                    type: "success",
                    title: "Éxito",
                    html: data.message,
                    timer: 2000,
                  }).then(() => success());
                } catch (err) {
                  swal({
                    type: "success",
                    title: "Éxito",
                    html: "",
                    timer: 2000,
                  }).then(() => success());
                }
              } else {
                try {
                  swal({
                    type: "success",
                    title: "Éxito",
                    html: data.message,
                    timer: 2000,
                  }).then(() => success(data));
                } catch (err) {
                  swal({
                    type: "success",
                    title: "Éxito",
                    html: "",
                    timer: 2000,
                  }).then(() => success(""));
                }
              }
            } else {
              if (success.length == 0) {
                success();
              } else {
                try {
                  success(data);
                } catch (error) {
                  success("");
                }
              }
            }
          }
        })
        .fail(function (data) {
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
              if (error.length == 0) {
                try {
                  swal({
                    type: "error",
                    title: "Error",
                    html: data.responseJSON.message,
                  }).then(() => error());
                } catch (err) {
                  swal({
                    type: "error",
                    title: "Error",
                    html: "El motivo de error no se ha podido recuperar",
                  }).then(() => error());
                }
              } else {
                try {
                  swal({
                    type: "error",
                    title: "Error",
                    html: data.responseJSON.message,
                  }).then(() => error(data.responseJSON));
                } catch (err) {
                  swal({
                    type: "error",
                    title: "Error",
                    html: "El motivo de error no se ha podido recuperar",
                  }).then(() => error(""));
                }
              }
            } else {
              if (error.length == 0) {
                error();
              } else {
                try {
                  error(data.responseJSON);
                } catch (error) {
                  error("");
                }
              }
            }
          }
        });
    } else {
      $.ajax({
        type: method,
        data: JSON.stringify(data),
        url: uri,
        dataType: "json",
        contentType: false,
        processData: false,
        async: true,
        cache: false,
        success: function (data) {
          if (success == null) {
            if (verbose > 2) {
              swal({
                type: "success",
                title: "Éxito",
                html: data.message,
                timer: 2000,
              });
            }
          } else {
            if (verbose > 2) {
              if (success.length == 0) {
                try {
                  swal({
                    type: "success",
                    title: "Éxito",
                    html: data.message,
                    timer: 2000,
                  }).then(() => success());
                } catch (err) {
                  swal({
                    type: "success",
                    title: "Éxito",
                    html: "",
                    timer: 2000,
                  }).then(() => success());
                }
              } else {
                try {
                  swal({
                    type: "success",
                    title: "Éxito",
                    html: data.message,
                    timer: 2000,
                  }).then(() => success(data));
                } catch (err) {
                  swal({
                    type: "success",
                    title: "Éxito",
                    html: "",
                    timer: 2000,
                  }).then(() => success(""));
                }
              }
            } else {
              if (success.length == 0) {
                success();
              } else {
                try {
                  success(data);
                } catch (err) {
                  success("");
                }
              }
            }
          }
        },
        error: function (data) {
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
              if (error.length == 0) {
                try {
                  swal({
                    type: "error",
                    title: "Error",
                    html: data.responseJSON.message,
                  }).then(() => error());
                } catch (err) {
                  swal({
                    type: "error",
                    title: "Error",
                    html: "El motivo de error no se ha podido recuperar",
                  }).then(() => error());
                }
              } else {
                try {
                  swal({
                    type: "error",
                    title: "Error",
                    html: data.responseJSON.message,
                  }).then(() => error(data.responseJSON));
                } catch (err) {
                  swal({
                    type: "error",
                    title: "Error",
                    html: "El motivo de error no se ha podido recuperar",
                  }).then(() => error(""));
                }
              }
            } else {
              if (error.length == 0) {
                error();
              } else {
                try {
                  error(data.responseJSON);
                } catch (err) {
                  error("");
                }
              }
            }
          }
        },
        beforeSend: function (xhr) {
          if (authorization != null) {
            xhr.setRequestHeader("Authorization", authorization);
          }
        },
      });
    }
    return true;
  } catch (err) {
    return false;
  }
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
  return callAPI(uri, method, formdata, requiered, 3, success, error);
}

/**
 * Lee un fichero y lo devuelve en binario
 * @param {Object} file Fichero que leer
 * @param {Object} metada Datos del fichero que leer
 * @param {String} input entrada que realizar en datos
 * @param {String} uri Dirección de la APi
 * @param {String} method Tipo de solicitud
 * @param {FormData} data Datos a enviar
 * @param {int} requiered Valores requeridos
 * @param {function} success función a ejecutar en caso de éxito
 * @param {function} error función a ejecutar en caso de error
 * @returns {String} fichero en binario
 */
function readUploadedFileAsBinary(
  file,
  metadata,
  input,
  uri,
  method,
  data,
  verbose,
  success,
  error,
  authorization
) {
  var reader = new FileReader();
  reader.onload = () => {
    metadata["content"] = reader.result;
    data[input] = metadata;
    ajaxCall(uri, method, data, verbose, success, error, authorization);
  };
  reader.readAsDataURL(file);
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
