'use strict';

let gulp = require('gulp'),
    prefixer = require('gulp-autoprefixer'),
    sass = require('gulp-sass'),
    plumber = require("gulp-plumber"),
    cssmin = require("gulp-cssmin");

let path = {
    build: {
        css: '../resource/css',
    },
    src: {
        style: 'css/*.scss',
    },
    watch: {
        style: 'css/**/*.scss',
    }
};

//build tasks
gulp.task('css:build', function () {
    return gulp.src(path.src.style)
        .pipe(plumber())
        .pipe(sass({
            includePaths: ['css/'],
        }))
        .pipe(cssmin())
        .pipe(prefixer())
        .pipe(gulp.dest(path.build.css))
});

gulp.task('build',
    gulp.parallel('css:build')
);

gulp.task('watch', function() {
    gulp.watch([path.watch.style], gulp.parallel('css:build'));    
});

gulp.task('default', gulp.parallel('build', 'watch'));
