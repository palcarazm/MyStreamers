$(function () {
  switch (currentURL()) {
    case "/config":
      togglePassword();
      $('.custom-select').select2();
      $('.custom-textarea').richTextMD();
      verifyDB();
      configAdmin();
      validAdminuserForm();
      configSite();
      break;

    default:
      break;
  }
});

// Funciones
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
    fileHTML: '',
    imageHTML: '',
  
    // translations
    translations: {
        'title': 'Título',
        'white': 'Blanco',
        'black': 'Negro',
        'brown': 'Marrón',
        'beige': 'Beige',
        'darkBlue': 'Azul oscuro',
        'blue': 'Azul',
        'lightBlue': 'Azul claro',
        'darkRed': 'Rojo oscuro',
        'red': 'Rojo',
        'darkGreen': 'Verde oscuro',
        'green': 'Verde',
        'purple': 'Violeta',
        'darkTurquois': 'Turquesa oscuro',
        'turquois': 'turquesa',
        'darkOrange': 'Naranja oscuro',
        'orange': 'Naranja',
        'yellow': 'Amarillo',
        'imageURL': 'Dirección de la imagen',
        'fileURL': 'Dirección del archivo',
        'linkText': 'Texto del enlace',
        'url': 'URL',
        'size': 'Tamaño',
        'responsive': 'Responsive',
        'text': 'Texto',
        'openIn': 'Abrir en',
        'sameTab': 'Misma pestaña',
        'newTab': 'Nueva pestaña',
        'align': 'Alineación',
        'left': 'Izquierda',
        'center': 'Centrado',
        'right': 'Derecha',
        'rows': 'Filas',
        'columns': 'Columnas',
        'add': 'Añadir',
        'pleaseEnterURL': 'Porfavor indica una URL',
        'videoURLnotSupported': 'URL del video no soportada',
        'pleaseSelectImage': 'Porfavor indica una URL',
        'pleaseSelectFile': 'Porfavor selecciona un archivo',
        'bold': 'Negrita',
        'italic': 'Cursiva',
        'underline': 'Subrayado',
        'alignLeft': 'Alineación izquierda',
        'alignCenter': 'Alineación centrada',
        'alignRight': 'Alineación derecha',
        'addOrderedList': 'Añade una lista numerada',
        'addUnorderedList': 'Añade una lista',
        'addHeading': 'Añade un titulo',
        'addFont': 'Añade una fuente',
        'addFontColor': 'Añade un color',
        'addFontSize' : 'Añade un tamaño de letra',
        'addImage': 'Añade una imagen',
        'addVideo': 'Añade un video',
        'addFile': 'Añade un archivo',
        'addURL': 'Añade un enlace',
        'addTable': 'Añade una tabla',
        'removeStyles': 'Elimina los estilos',
        'code': 'Muestra el código HTML',
        'undo': 'Deshacer',
        'redo': 'Rehacer',
        'close': 'Cerrar'
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
    useTabForNext: false
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
  const re = /^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
  return re.test(String(email).toLowerCase());
}