/**
 * Cargador de funciones en función de la página cargada
 */
$(function () {
  switch (currentURL()) {
    case "/config":
      togglePassword();
      $(".custom-select").select2();
      $(".custom-textarea").richTextMD();
      configDB();
      configAdmin();
      validAdminuserForm();
      configEmail();
      configSite();
      break;
    case "/login":
      togglePassword();
      login();
      break;
    case "/create-otp":
      createOTP();
      break;
    case "/invalidate-otp":
      invalidateOTP();
      break;
    case "/new-password":
      togglePassword();
      validateNewPasswordForm();
      newPassword();
      break;
    case "/admin/config/sitio":
      $(".custom-select").select2();
      $(".custom-textarea").richTextMD();
      updateSite();
      break;
    case "/admin/miperfil":
      configFileIntup();
      userForm();
      break;
    case "/admin/usuarios/editar":
      configFileIntup();
      userForm();
      break;
    case "/admin/usuarios/crear":
      configFileIntup();
      userForm();
      break;
    default:
      break;
  }
  setupAdminMenu();
});

/**
 * Configura el menu de administración
 */
function setupAdminMenu() {
  $('ul.nav-sidebar a[href="' + currentURL() + '"]')
    .addClass("active")
    .parents("ul.nav-sidebar li.nav-item")
    .each(function () {
      //$(this).addClass("active");
      if ($(this).children(".nav-treeview").length > 0) {
        $(this).addClass("menu-is-opening menu-open");
        $(this)
          .children(".nav-treeview")
          .each(function () {
            $(this).show();
          });
      }
    });
}

/**
 * Muestra y oculta la contraseña al apretar un boton
 */
function togglePassword() {
  $(".toggle-password").on("click", function () {
    if ($(this).find("i").hasClass("fa-eye")) {
      $(this).closest(".input-group").find("input").attr("type", "text");
      $(this).find("i").addClass("fa-eye-slash").removeClass("fa-eye");
    } else {
      $(this).closest(".input-group").find("input").attr("type", "password");
      $(this).find("i").addClass("fa-eye").removeClass("fa-eye-slash");
    }
  });
}

/**
 * Configura el selector de fichero
 */
function configFileIntup() {
  $('input[type="file"]').change(function (e) {
    var fileName = e.target.files[0].name;
    $(".custom-file-label").html(fileName);
  });
}

/**
 * Configura las tablas
 */
function setupTable() {
  $('table.data').DataTable({
    "paging": true,
    "lengthChange": true,
    "searching": true,
    "ordering": true,
    "info": true,
    "autoWidth": false,
    "responsive": true,
    language: {
      url: 'https://cdn.datatables.net/plug-ins/1.11.5/i18n/es-ES.json'
  }
  });
}

/**
 * Configura el text area para introducción de texto enriquecido
 */
jQuery.fn.richTextMD = function () {
  return this.richText({
    // text formatting
    bold: true,
    italic: true,
    underline: true,

    // text alignment
    leftAlign: true,
    centerAlign: true,
    rightAlign: true,
    justify: true,

    // lists
    ol: true,
    ul: true,

    // title
    heading: true,

    // fonts
    fonts: false,
    fontColor: false,
    fontSize: false,

    // uploads
    imageUpload: false,
    fileUpload: false,

    // media
    videoEmbed: false,

    // link
    urls: true,

    // tables
    table: false,

    // code
    removeStyles: true,
    code: true,

    // colors
    colors: [],

    // dropdowns
    fileHTML: "",
    imageHTML: "",

    // translations
    translations: {
      title: "Título",
      white: "Blanco",
      black: "Negro",
      brown: "Marrón",
      beige: "Beige",
      darkBlue: "Azul oscuro",
      blue: "Azul",
      lightBlue: "Azul claro",
      darkRed: "Rojo oscuro",
      red: "Rojo",
      darkGreen: "Verde oscuro",
      green: "Verde",
      purple: "Violeta",
      darkTurquois: "Turquesa oscuro",
      turquois: "turquesa",
      darkOrange: "Naranja oscuro",
      orange: "Naranja",
      yellow: "Amarillo",
      imageURL: "Dirección de la imagen",
      fileURL: "Dirección del archivo",
      linkText: "Texto del enlace",
      url: "URL",
      size: "Tamaño",
      responsive: "Responsive",
      text: "Texto",
      openIn: "Abrir en",
      sameTab: "Misma pestaña",
      newTab: "Nueva pestaña",
      align: "Alineación",
      left: "Izquierda",
      center: "Centrado",
      right: "Derecha",
      rows: "Filas",
      columns: "Columnas",
      add: "Añadir",
      pleaseEnterURL: "Porfavor indica una URL",
      videoURLnotSupported: "URL del video no soportada",
      pleaseSelectImage: "Porfavor indica una URL",
      pleaseSelectFile: "Porfavor selecciona un archivo",
      bold: "Negrita",
      italic: "Cursiva",
      underline: "Subrayado",
      alignLeft: "Alineación izquierda",
      alignCenter: "Alineación centrada",
      alignRight: "Alineación derecha",
      addOrderedList: "Añade una lista numerada",
      addUnorderedList: "Añade una lista",
      addHeading: "Añade un titulo",
      addFont: "Añade una fuente",
      addFontColor: "Añade un color",
      addFontSize: "Añade un tamaño de letra",
      addImage: "Añade una imagen",
      addVideo: "Añade un video",
      addFile: "Añade un archivo",
      addURL: "Añade un enlace",
      addTable: "Añade una tabla",
      removeStyles: "Elimina los estilos",
      code: "Muestra el código HTML",
      undo: "Deshacer",
      redo: "Rehacer",
      close: "Cerrar",
    },

    // privacy
    youtubeCookies: false,

    // developer settings
    useSingleQuotes: false,
    height: 0,
    heightPercentage: 0,
    id: "",
    class: "",
    useParagraph: true,
    maxlength: 0,
    callback: undefined,
    useTabForNext: false,
  });
};

/**
 * Compurueba la seguridad de la contraseña
 * @param {String} password
 * @returns cumple las reglas de seguridad (Si/No)
 */
function checkPasswordStrength(password) {
  return new RegExp(
    "(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*[^A-Za-z0-9])(?=.{8,})"
  ).test(password);
}

/**
 * Valida que el texto inproducido es un email
 * @param {String} email
 * @returns es un email (Si/No)
 */
function validateEmail(email) {
  const re =
    /^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
  return re.test(String(email).toLowerCase());
}

/**
 * Valida las contraseñas del Formulario de establecimiento, restablecimeinto o modificación
 * @returns bool Validación superada (Si/No)
 */
function validatePass(password, passwordRepeat) {
  password.parent().find(".invalid-feedback").remove();
  passwordRepeat.parent().find(".invalid-feedback").remove();
  if (passwordRepeat.val() != password.val()) {
    password
      .parent()
      .append(
        $("<div></div>")
          .addClass("invalid-feedback")
          .text("Las contraseñas no coinciden")
      );
    passwordRepeat
      .parent()
      .append(
        $("<div></div>")
          .addClass("invalid-feedback")
          .text("Las contraseñas no coinciden")
      );
    passwordRepeat.addClass("is-invalid").removeClass("is-valid");
    password.addClass("is-invalid").removeClass("is-valid");
    return false;
  } else if (!checkPasswordStrength(String(password.val()).trim())) {
    password.addClass("is-invalid").removeClass("is-valid");
    password
      .parent()
      .append(
        $("<div></div>")
          .addClass("invalid-feedback")
          .text(
            "La contraseña debe contener al menos una minúscula, una mayúscula, un número, un carácter especial y ser de almenos 8 carácteres"
          )
      );
    return false;
  } else {
    passwordRepeat.removeClass("is-invalid").addClass("is-valid");
    password.removeClass("is-invalid").addClass("is-valid");
    return true;
  }
}
