'use strict';

var gulp = require('gulp');
var gutil = require('gulp-util');
var streamqueue = require('streamqueue');
var runSequence = require('run-sequence');
var del = require('del');

// Load plugins
var $ = require('gulp-load-plugins')();

var assets = require('./assets.json');

var jsFilter = $.filter('**/*.js');
var cssFilter = $.filter('**/*.css');

gulp.task('compass', function() {
  return gulp.src('assets/styles/**/*.scss')
    .pipe($.changed('tmp/assets/styles'))
    .pipe($.rubySass({
      style: 'expanded',
      loadPath: ['assets/vendor', 'assets/vendor/bootstrap-sass-official/assets/stylesheets']
    }))
    .pipe($.autoprefixer('last 1 version'))
    .pipe(gulp.dest('tmp/assets/styles'));
});

gulp.task('clean', function(cb) {
  return del(['tmp/**/*', 'web/assets/**/*'], cb);
});

gulp.task('fonts', function() {
  var stream = streamqueue({ objectMode: true });

  stream.queue(gulp.src('assets/vendor/bootstrap-sass-official/assets/fonts/bootstrap/**'));
  stream.queue(gulp.src('assets/fonts/**'));

  return stream.done()
        .pipe(gulp.dest('web/assets/fonts'));
});

gulp.task('icomoon', function() {
  return gulp.src('assets/styles/icomoon/**')
    .pipe($.changed('assets/styles/icomoon/**'))
    .pipe(gulp.dest('web/assets/css/icomoon'));
});

gulp.task('styles', ['compass'], function() {
  var stream = streamqueue({ objectMode: true });

  // concat styles files
  Object.keys(assets.styles).forEach(function(key) {
    stream.queue(gulp.src(assets.styles[key]).pipe($.concat(key)));
  });

  return stream.done()
    .pipe(gulp.dest('web/assets'))
});

gulp.task('scripts', function() {
  var stream = streamqueue({ objectMode: true });

  // concat scripts files
  Object.keys(assets.scripts).forEach(function(key) {
    stream.queue(gulp.src(assets.scripts[key]).pipe($.concat(key)));
  });

  return stream.done()
    .pipe(gulp.dest('web/assets'))
});

gulp.task('optimize', ['scripts', 'styles', 'fonts', 'icomoon'], function() {
  return gulp.src('web/assets/**/*')
    .pipe($.changed('web/assets/**/*'))
    .pipe(jsFilter)
    .pipe($.uglify())
    .pipe(jsFilter.restore())
    .pipe(cssFilter)
    .pipe($.csso("--restructure-off"))
    .pipe(cssFilter.restore())
    .pipe(gulp.dest('web/assets'))
});

gulp.task('watch', function () {
    gulp.watch('assets/styles/**/*.scss', ['styles']);
    gulp.watch('assets/scripts/**/*.js', ['scripts']);
});

gulp.task('build', function(callback) {
  runSequence('clean', 'optimize', function() {
    callback();
  });
});

gulp.task('package', ['build'], function() {
  return gulp.src('web/assets/**/*')
    .pipe($.tar('assets.tar'))
    .pipe($.gzip())
    .pipe(gulp.dest('tmp'));
});
