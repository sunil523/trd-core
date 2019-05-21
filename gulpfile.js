'use strict';

const
  gulp       = require('gulp'),
  del        = require('del'),
  browserify = require('browserify'),
  babelify   = require('babelify'),
  csso       = require('gulp-csso'),
  imagemin   = require("gulp-imagemin"),
  newer      = require("gulp-newer"),
  rename     = require('gulp-rename'),
  sass       = require('gulp-sass'),
  sourcemaps = require('gulp-sourcemaps'),
  uglify     = require('gulp-uglify'),
  source      = require('vinyl-source-stream'),
  buffer      = require('vinyl-buffer'),
  livereload  = require('gulp-livereload')
;

const
  dirs = {
    src: './src',
    dest: './assets'
  },
  cssPaths = {
    watch:  `${dirs.src}/scss/**/*.scss`,
    src:    `${dirs.src}/scss/**/*.scss`,
    dest:   `${dirs.dest}/css`
  },
  jsPaths = {
    watch: `${dirs.src}/js/**/*.js`,
    src:   `${dirs.src}/js/trd-core.js`,
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
  return browserify({entries: jsPaths.src, debug: true})
    .transform(babelify, {"presets": ["@babel/preset-env"]})
    .bundle()
    .pipe(source('trd-core.js'))
    .pipe(buffer())
    .pipe(sourcemaps.init())
    .pipe(uglify())
    .pipe(rename('trd-core.min.js'))
    .pipe(sourcemaps.write('.'))
    .pipe(gulp.dest(jsPaths.dest))
    .pipe(livereload())
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
  livereload.listen();
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
