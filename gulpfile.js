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
  css: { src: "src/css/**/*.css", dest: "./dist/public/css" },
  theme: { src: "src/theme/**/*.css", dest: "./dist/public/themes/mystreamers" },
  js: { src: "src/js/**/*.js", dest: "./dist/public/js" },
};

/* COMPILACIÓN DE CSS
========================== */
function css() {
  return src(paths.css.src)
    .pipe(sourcemaps.init())
    .pipe(postcss([autoprefixer(), cssnano()]))
    .pipe(sourcemaps.write("."))
    .pipe(rename({ suffix: ".min" }))
    .pipe(dest(paths.css.dest))
    .pipe(notify({message: 'CSS Actualizado'}));
}
exports.css = css;

/* COMPILACIÓN DE THEME
========================== */
function theme() {
    return src(paths.theme.src)
      .pipe(sourcemaps.init())
      .pipe(postcss([autoprefixer(), cssnano()]))
      .pipe(sourcemaps.write("."))
      .pipe(rename({ suffix: ".min" }))
      .pipe(dest(paths.theme.dest))
      .pipe(notify({message: 'THEME Actualizado'}));
  }
  exports.theme = theme;


/* COMPILACIÓN DE JS
========================== */
function js() {
  return src(paths.js.src)
    .pipe(sourcemaps.init())
    .pipe(concat("bundle.js"))
    .pipe(terser())
    .pipe(sourcemaps.write("."))
    .pipe(rename({ suffix: ".min" }))
    .pipe(dest(paths.js.dest))
    .pipe(notify({message: 'JS Actualizado'}));
}
exports.js = js;

/* BUILD
========================== */
function watchDev() {
  watch(paths.css.src, css);
  watch(paths.js.src, js);
  watch(paths.theme.src, theme);
}
exports.default = watchDev;
exports.build = parallel(css, js, theme);
