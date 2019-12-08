var gulp = require('gulp');
var sass = require('gulp-sass');
var sourcemaps = require('gulp-sourcemaps');
var rgbapng = require('gulp-rgbapng');
var sassImage = require('gulp-sass-image');
var cleanCss = require('gulp-clean-css');


const sassFiles = process.env.SASS || '**/*.+(sass|scss)';
var paths = {
    sass: '/etc/site-sass/sass' + sassFiles,
    css:  '/etc/site-sass/css'
};

// compile SASS to public CSS files (dev, no minify)
gulp.task('sass-dev', ['sass-image'], function (done) {
    gulp.src(paths.sass)
        .pipe(sourcemaps.init())
        .pipe(sass({errLogToConsole: true}))
        .pipe(sourcemaps.write())
        .pipe(rgbapng())
        .pipe(gulp.dest(paths.css))
        .on('end', done);
});

// compile SASS and minify to public CSS files
gulp.task('sass-dist', ['sass-image'], function (done) {
    gulp.src(paths.sass)
    // .pipe(sourcemaps.init())
        .pipe(sass({errLogToConsole: true}))
        // .pipe(sourcemaps.write())
        .pipe(rgbapng())
        .pipe(cleanCss({
            level: 2
        }))
        .pipe(gulp.dest(paths.css))
        .on('end', done);
});

// recreate sourcemap of images used in SASS, as well as image mixins
gulp.task('sass-image', function () {
    return gulp.src('./../public/img/**/*.+(jpeg|jpg|png|gif|svg)')
        .pipe(sassImage({
            targetFile: '_imagehelper-generated.scss', // default target filename is '_sass-image.scss'
            // template: 'your-sass-image-template.mustache',
            images_path: './../public/img',
            css_path: './../public/img',
            http_images_path: '/img',
            includeData: false
        }))
        .pipe(gulp.dest('.'));
});

gulp.task('default', [ 'sass-dev', 'watch' ]);

gulp.task('watch', function() {
    // Watches the sass folder for all .scss and .sass files
    // If any file changes, run the sass task
    gulp.watch(paths.sass, ['sass-dev'])
});