// Import Gulp and its plugins
const gulp = require('gulp')
const concat = require('gulp-concat')
const sass = require('gulp-sass')(require('sass'))
const sourcemaps = require('gulp-sourcemaps')
const ftp = require('vinyl-ftp')
const rename = require('gulp-rename')
// developer initials
const dev = 'rn'

// Define file paths
const jsFiles = 'src/js/*.js'
const sassFiles = 'src/sass/**/*.scss'

// Configure FTP connection
const conn = ftp.create({
    host: 'newspark.cz',
    user: 'newsparkcz5',
    password: 'W2luV3mOMG',
    parallel: 10,
})

// Compile SCSS to CSS and create sourcemaps
gulp.task('sass', function () {
    return gulp
        .src(sassFiles)
        .pipe(sourcemaps.init())
        .pipe(sass({ outputStyle: 'compressed' }).on('error', sass.logError))
        .pipe(sourcemaps.write())
        .pipe(gulp.dest('dist/css'))
        .pipe(conn.dest(`/wp-content/themes/streamtube-child/dev/${dev}/css`))
        .on('end', function () {
            console.log(`${dev}'s CSS files uploaded to FTP`)
        })
})

// Concatenate JS files
gulp.task('concat', function () {
    return gulp
        .src(jsFiles)
        .pipe(concat('script.js'))
        .pipe(gulp.dest('dist/js'))
        .pipe(conn.dest(`/wp-content/themes/streamtube-child/dev/${dev}/js`))
        .on('end', function () {
            console.log(`${dev}'s JS files uploaded to FTP`)
        })
})

// Watch for changes
gulp.task('watch', function () {
    gulp.watch(sassFiles, gulp.series('sass')) // explicitly reference the 'sass' task
    gulp.watch(jsFiles, gulp.series('concat'))
})

// Default task
gulp.task('default', gulp.parallel('sass', 'concat', 'watch'))
