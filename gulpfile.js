var fs = require('fs'),
	gulp = require('gulp'),
	less = require('gulp-less'),
	clean = require('gulp-clean'),
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
		'build/css/bootstrap.css',
		//'vendor/bower_components/bootstrap/dist/css/bootstrap.css',
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
 * Clean the directories
 */
gulp.task('clean', function () {
	return gulp.src(['build/css/*.css', 'build/js/*.js', 'public/css/*.min.css', 'public/js/*.min.js'], {read: false})
		.pipe(clean());
});

/**
 * Compile bootstrap with less
 */
gulp.task('build-bootstrap-less', function(){
    return gulp.src('resources/less/bootstrap.less')
        .pipe(less())
        .pipe(gulp.dest('build/css/'));
});

/**
 * Handle biome
 */
gulp.task('biome_css', function () {
	return gulp.src(sources.css)
		.pipe(concatCss('app.css'))
		.pipe(gulp.dest('build/css/'));
});

gulp.task('biome_js', function() {
	return gulp.src(sources.js)
		.pipe(concat('app.js'))
		.pipe(gulp.dest('build/js/'));
});

/**
 * Vendor
 */

gulp.task('vendor_css', ['build-bootstrap-less'], function () {
	return gulp.src(sources.vendor_css)
		.pipe(concatCss('vendor.css', {rebaseUrls: false}))
		.pipe(gulp.dest('build/css/'));
});

gulp.task('vendor_js', function () {
	return gulp.src(sources.vendor_js)
		.pipe(concat('vendor.js'))
		.pipe(gulp.dest('build/js/'));
});

gulp.task('vendor_fonts', function () {
	return gulp.src(sources.vendor_fonts)
		.pipe(gulp.dest('public/fonts/'));
});

gulp.task('vendor', ['vendor_css', 'vendor_js', 'vendor_fonts']);

/**
 * Compile coffee script
 */
gulp.task('coffee', ['biome_js'], function () {
    return gulp.src(sources.coffee)
        .pipe(coffee({bare: true}))
        .pipe(concat('app.js'))
        .pipe(gulp.dest('build/js/'))
        .on('error', console.log);
});

/**
 * Handle app resources
 */
gulp.task('resources_css', ['biome_css', 'build-bootstrap-less'], function() {
	return gulp.src('resources/css/*.css')
		.pipe(concatCss('app.css'))
		.pipe(gulp.dest('build/css/'));
});
gulp.task('resources_js', ['coffee', 'biome_js'], function() {
	return gulp.src('resources/js/*.js')
		.pipe(concat('app.js'))
		.pipe(gulp.dest('build/js/'));
});
gulp.task('resources_fonts', function() {
	return gulp.src('resources/fonts/**/*')
		.pipe(gulp.dest('public/fonts/'));
});
gulp.task('resources_images', function() {
	return gulp.src('resources/images/*')
		.pipe(gulp.dest('public/images/'));
});

gulp.task('resources', ['resources_css', 'resources_js', 'resources_fonts', 'resources_images']);

/**
 * Minify CSS
 */
gulp.task('cssmin', function () {
    return gulp.src(['build/css/app.css', 'build/css/vendor.css'])
        //.pipe(require('gulp-cssmin')())
        .pipe(rename({suffix: '.min'}))
        .pipe(gulp.dest('public/css'));
});

/**
 * Uglify
 */
gulp.task('uglify', function () {
    return gulp.src(['build/js/app.js', 'build/js/vendor.js'])
        //.pipe(require('gulp-uglify')())
        .pipe(rename({suffix: '.min'}))
        .pipe(gulp.dest('public/js'));
});

/**
 * Build
 */
gulp.task('minify', ['cssmin', 'uglify']);

gulp.task('build', ['vendor', 'resources'], function() {
	return gulp.start('minify');
});

gulp.task('default', ['clean'], function() {
	return gulp.start('build');
});
