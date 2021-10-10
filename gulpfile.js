const { src, dest, watch, parallel } = require("gulp");
const autoprefixer = require("autoprefixer");
const postcss = require("gulp-postcss");
const sourcemaps = require("gulp-sourcemaps");
const cssnano = require("cssnano");
const concat = require("gulp-concat");
const terser = require("gulp-terser-js");
const rename = require("gulp-rename");
const notify = require("gulp-notify");

const paths = {
  admincss: { src: "src/css/**/admin/*.css", dest: "./dist/public/css" },
  theme: { src: "src/theme/**/*.css", dest: "./dist/public/themes/mystreamers" },
  adminjs: { src: "src/js/**/admin/*.js", dest: "./dist/public/js" },
};

/* COMPILACIÓN DE CSS
========================== */
function admincss() {
  return src(paths.admincss.src)
    .pipe(sourcemaps.init())
    .pipe(concat("admin-style.css"))
    .pipe(postcss([autoprefixer(), cssnano()]))
    .pipe(rename({ suffix: ".min" }))
    .pipe(sourcemaps.write("."))
    .pipe(dest(paths.admincss.dest))
    //.pipe(notify({message: 'CSS Actualizado'}))
    ;
}
exports.admincss = admincss;

/* COMPILACIÓN DE THEME
========================== */
function theme() {
    return src(paths.theme.src)
      .pipe(sourcemaps.init())
      .pipe(postcss([autoprefixer(), cssnano()]))
      .pipe(rename({ suffix: ".min" }))
      .pipe(sourcemaps.write("."))
      .pipe(dest(paths.theme.dest))
      //.pipe(notify({message: 'THEME Actualizado'}))
      ;
  }
  exports.theme = theme;


/* COMPILACIÓN DE JS
========================== */
function adminjs() {
  return src(paths.adminjs.src)
    .pipe(sourcemaps.init())
    .pipe(concat("admin.js"))
    .pipe(terser())
    .pipe(rename({ suffix: ".min" }))
    .pipe(sourcemaps.write("."))
    .pipe(dest(paths.adminjs.dest))
    //.pipe(notify({message: 'JS Actualizado'}))
    ;
}
exports.adminjs = adminjs;

/* BUILD
========================== */
function watchDev() {
  watch(paths.admincss.src, admincss);
  watch(paths.adminjs.src, adminjs);
  watch(paths.theme.src, theme);
}
exports.default = watchDev;
exports.build = parallel(admincss, adminjs, theme);
