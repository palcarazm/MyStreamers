/**
 * Cargador de funciones en función de la página cargada
 */
$(function () {
  switch (currentURL()) {
    case "/":
      setOnline(".offline", ".online", "#participantes", "#badge-directo");
      break;
    case "/participantes":
      setOnline("#archivo-participante", "#archivo-participante");
      break;
    default:
      break;
  }
});

/**
 * Establece los jugadores online
 */
function setOnline(
  offlineSelector,
  onlineSelector,
  containerSelector = null,
  bagdeSelector = null
) {
  if (containerSelector != null) {
    $(containerSelector).loading();
  }
  const offlineContainer = $(offlineSelector);
  ajaxCall(
    "/api/streams/v1/status",
    "GET",
    "{}",
    0,
    function (bodyout) {
      let usersID = Array(Object(bodyout.content).online);
      if (usersID.length > 0) {
        if (bagdeSelector != null) {
          $(bagdeSelector).html(usersID.length);
        }
        if (offlineSelector != onlineSelector) {
          $(onlineSelector).html("");
        }
        usersID.forEach((userId) => {
          offlineContainer
            .children('DIV.card-user[data-id="' + userId + '"]')
            .addClass("online")
            .detach()
            .prependTo(onlineSelector);
        });
      }
      if (containerSelector != null) {
        $(containerSelector).loaded();
      }
    },
    function () {
      if (containerSelector != null) {
        $(containerSelector).loaded();
      }
    }
  );
}
