var fs = require('fs'),
	gulp = require('gulp'),
	//postcss = require('gulp-postcss'),
	rename = require('gulp-rename'),
    concatCss = require('gulp-concat-css'),
    concat = require('gulp-concat'),
    coffee = require('gulp-coffee');

var sources = {
	coffee: [
		'vendor/mermetbt/biome/src/resources/js/app.coffee'
	],
	css: [
		'vendor/mermetbt/biome/src/resources/css/app.css'
	],
	js: [
		//'vendor/mermetbt/biome/src/resources/js/app.js'
	],
	vendor_css: [
		'vendor/bower_components/font-awesome/css/font-awesome.css',
		'vendor/bower_components/bootstrap/dist/css/bootstrap.css',
		//'vendor/bower_components/bootstrap/dist/css/bootstrap-theme.css',
		'vendor/bower_components/datatables-plugins/integration/bootstrap/3/dataTables.bootstrap.css',
		'vendor/bower_components/datatables-responsive-helper/files/1.10/css/datatables.responsive.css',
		//'vendor/bower_components/datatables/media/css/jquery.dataTables.css',
		//'vendor/bower_components/datatables/media/css/dataTables.bootstrap.css',
		'vendor/bower_components/select2-ng/dist/css/select2.min.css'
	],
	vendor_js: [
		'vendor/bower_components/jquery/dist/jquery.js',
		'vendor/bower_components/bootstrap/dist/js/bootstrap.js',
		'vendor/bower_components/datatables/media/js/jquery.dataTables.js',
		'vendor/bower_components/datatables/media/js/dataTables.bootstrap.js',
		//'vendor/bower_components/datatables-plugins/integration/bootstrap/3/dataTables.bootstrap.min.js',
		'vendor/bower_components/datatables-plugins/integration/bootstrap/3/dataTables.bootstrap.min.js',
		'vendor/bower_components/autosize/dist/autosize.min.js',
		'vendor/bower_components/select2-ng/dist/js/select2.full.min.js'
	],
	vendor_fonts: [
		'vendor/bower_components/bootstrap/dist/fonts/*',
		'vendor/bower_components/font-awesome/fonts/*'
	]
}

gulp.task('check', function () {
	var found = true;
	for(var i in sources.coffee)
	{
		if(!fs.existsSync(sources.coffee[i]))
		{
			console.error(sources.coffee[i] + ' not found!');
			found = false;
		}
	}
	for(var i in sources.css)
	{
		if(!fs.existsSync(sources.css[i]))
		{
			console.error(sources.css[i] + ' not found!');
			found = false;
		}
	}
	for(var i in sources.js)
	{
		if(!fs.existsSync(sources.js[i]))
		{
			console.error(sources.js[i] + ' not found!');
			found = false;
		}
	}
	for(var i in sources.vendor_js)
	{
		if(!fs.existsSync(sources.vendor_js[i]))
		{
			console.error(sources.vendor_js[i] + ' not found!');
			found = false;
		}
	}
	for(var i in sources.vendor_css)
	{
		if(!fs.existsSync(sources.vendor_css[i]))
		{
			console.error(sources.vendor_css[i] + ' not found!');
			found = false;
		}
	}
	return found;
});

/**
 * Handle biome
 */
gulp.task('biome', function () {
	gulp.src(sources.css)
		.pipe(concatCss('app.css'))
		.pipe(gulp.dest('build/css/'));

	gulp.src(sources.js)
		.pipe(concat('app.js'))
		.pipe(gulp.dest('build/js/'));
});

/**
 * Handle app resources
 */
gulp.task('resources', function () {
	gulp.src('resources/css/*.css')
		.pipe(concatCss('app.css'))
		.pipe(gulp.dest('build/css/'));

	gulp.src('resources/js/*.js')
		.pipe(concat('app.js'))
		.pipe(gulp.dest('build/js/'));

	gulp.src('resources/fonts/**/*')
		.pipe(gulp.dest('public/fonts/'));

	gulp.src('resources/images/*')
		.pipe(gulp.dest('public/images/'));
});

/**
 * Vendor
 */
gulp.task('vendor', function () {
	gulp.src(sources.vendor_css)
		.pipe(concatCss('vendor.css', {rebaseUrls: false}))
		.pipe(gulp.dest('build/css/'));

	gulp.src(sources.vendor_js)
		.pipe(concat('vendor.js'))
		.pipe(gulp.dest('build/js/'));

	gulp.src(sources.vendor_fonts)
		.pipe(gulp.dest('public/fonts/'));
});

/**
 * Compile coffee script
 */
gulp.task('coffee', function () {
    gulp.src(sources.coffee)
        .pipe(coffee({bare: true}))
        .pipe(concat('app.js'))
        .pipe(gulp.dest('build/js/'))
        .on('error', console.log);
});

/**
 * Minify CSS
 */
gulp.task('cssmin', function () {
    gulp.src('build/css/*.css')
        .pipe(require('gulp-cssmin')())
        .pipe(rename({suffix: '.min'}))
        .pipe(gulp.dest('public/css'));
});

/**
 * Uglify
 */
gulp.task('uglify', function () {
    gulp.src('build/js/*.js')
        .pipe(require('gulp-uglify')())
        .pipe(rename({suffix: '.min'}))
        .pipe(gulp.dest('public/js'));
});

/**
 * Build
 */
gulp.task('build', ['check', 'vendor', 'biome', 'resources', 'coffee']);
gulp.task('minify', ['cssmin', 'uglify']);

gulp.task('default', ['build', 'minify']);
