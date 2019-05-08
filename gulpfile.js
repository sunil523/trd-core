'use strict';

const
  gulp       = require('gulp'),
  del        = require('del'),
  concat     = require('gulp-concat'),
  csso       = require('gulp-csso'),
  imagemin   = require("gulp-imagemin"),
  newer      = require("gulp-newer"),
  rename     = require('gulp-rename'),
  sass       = require('gulp-sass'),
  sourcemaps = require('gulp-sourcemaps')
;

const
  dirs = {
    src: '.',
    dest: './assets'
  },
  cssPaths = {
    watch:  `${dirs.src}/scss/**/*.scss`,
    src:    `${dirs.src}/scss/**/*.scss`,
    dest:   `${dirs.dest}/css`
  },
  jsPaths = {
    watch: `${dirs.src}/js/**/*.js`,
    dest:  `${dirs.dest}/js`
  },
  libsPaths = {
    watch: `${dirs.src}/libs/**/*`,
    dest:  `${dirs.dest}/libs`
  },
  imgsPaths = {
    watch: `${dirs.src}/images/**/*`,
    dest:  `${dirs.dest}/images`
  }
;

function libs() {
  return gulp.src(libsPaths.watch)
    .pipe(gulp.dest(libsPaths.dest))
  ;
}

// Optimize Images
function images() {
  return gulp
    .src(imgsPaths.watch)
    .pipe(newer(imgsPaths.dest))
    .pipe(
      imagemin([
        imagemin.gifsicle({ interlaced: true }),
        imagemin.jpegtran({ progressive: true }),
        imagemin.optipng({ optimizationLevel: 5 }),
        imagemin.svgo({
          plugins: [
            {
              removeViewBox: false,
              collapseGroups: true
            }
          ]
        })
      ])
    )
    .pipe(gulp.dest(imgsPaths.dest));
}

function css() {
  return gulp.src(cssPaths.watch)
    .pipe(sass().on('error', sass.logError))
    .pipe(sourcemaps.init())
    .pipe(csso())
    .pipe(rename({ extname: '.min.css' }))
    .pipe(sourcemaps.write('.'))
    .pipe(gulp.dest(cssPaths.dest))
  ;
}

function js() {
  return gulp.src(jsPaths.watch, { sourcemaps: true })
    .pipe(concat('main.min.js'))
    .pipe(gulp.dest(jsPaths.dest, { sourcemaps: true }))
  ;
}

function cleaner() {
  return del([
    `${dirs.dest}/**`
  ])
  ;
}

// Watch files
function watchFiles() {
  gulp.watch(imgsPaths.watch, images);
  gulp.watch(cssPaths.watch, css);
  gulp.watch(jsPaths.watch, js);
}

// define complex tasks
const build = gulp.series(cleaner, gulp.parallel(libs, css, images, js));
const watch = gulp.parallel(watchFiles);
const serve = gulp.series(build, watch);

exports.build   = build;
exports.watch   = watch;
exports.serve   = serve;
exports.default = build;
