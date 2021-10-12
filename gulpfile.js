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
  globalcss: { src: "src/css/global/*.css", dest: "./dist/public/css" },
  admincss: { src: "src/css/admin/*.css", dest: "./dist/public/css" },
  theme: { src: "src/theme/*.css", dest: "./dist/public/themes/mystreamers" },
  globaljs: { src: "src/js/global/*.js", dest: "./dist/public/js" },
  adminjs: { src: "src/js/admin/*.js", dest: "./dist/public/js" },
};

/* COMPILACIÓN DE CSS
========================== */
function css(path,name) {
  return src(path.src)
    .pipe(sourcemaps.init())
    .pipe(concat(name+".css"))
    .pipe(postcss([autoprefixer(), cssnano()]))
    .pipe(rename({ suffix: ".min" }))
    .pipe(sourcemaps.write("."))
    .pipe(dest(path.dest))
    //.pipe(notify({message: 'CSS Actualizado'}))
    ;
}
function globalcss() {
  return css(paths.globalcss,"main");
}
function admincss() {
  return css(paths.admincss,"admin");
}
exports.globalcss = globalcss;
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
function js(path,name) {
  return src(path.src)
    .pipe(sourcemaps.init())
    .pipe(concat(name+".js"))
    .pipe(terser())
    .pipe(rename({ suffix: ".min" }))
    .pipe(sourcemaps.write("."))
    .pipe(dest(path.dest))
    //.pipe(notify({message: 'JS Actualizado'}))
    ;
}
function globaljs() {
  return js(paths.globaljs,"main");
}
function adminjs() {
  return js(paths.adminjs,"admin");
}
exports.globaljs = globaljs;
exports.adminjs = adminjs;

/* BUILD
========================== */
function watchDev() {
  watch(paths.globalcss.src, globalcss);
  watch(paths.globaljs.src, globaljs);
  watch(paths.admincss.src, admincss);
  watch(paths.adminjs.src, adminjs);
  watch(paths.theme.src, theme);
}
exports.default = watchDev;
exports.build = parallel(globalcss, globaljs, admincss, adminjs, theme);
