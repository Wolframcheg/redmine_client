var gulp = require('gulp'),
    less = require('gulp-less'),
    clean = require('gulp-rimraf'),
    concatJs = require('gulp-concat'),
    minifyJs = require('gulp-uglify');


gulp.task('clean', function () {
    return gulp.src(['web/css/*', 'web/js/*', 'web/images/*', 'web/fonts/*', 'web/admin/*'])
        .pipe(clean());
});


gulp.task('front-less', function() {
    return gulp.src(['web-src/front/less/*.less'])
        .pipe(less({compress: true}))
        .pipe(gulp.dest('web/css/'));
});

gulp.task('front-js', function() {
    return gulp.src([
            'bower_components/jquery/dist/jquery.js',
            'bower_components/bootstrap/dist/js/bootstrap.js',
        ])
        .pipe(concatJs('app.js'))
        .pipe(minifyJs())
        .pipe(gulp.dest('web/js/'));
});

gulp.task('front-fonts', function () {
    return gulp.src(['bower_components/font-awesome/fonts/*',
            'bower_components/bootstrap/fonts/*'])
        .pipe(gulp.dest('web/fonts/'))
});

gulp.task('default', ['clean'], function () {
    var tasks = ['front-less', 'front-js', 'front-fonts'];
    tasks.forEach(function (val) {
        gulp.start(val);
    });
});




//gulp.task('watch', function () {
//    var less = gulp.watch('web-src/less/*.less', ['less']),
//        js = gulp.watch('web-src/js/*.js', ['pages-js']);
//});
