var gulp = require('gulp');
var mainBowerFiles = require('main-bower-files');

gulp.task('default', function() {
	//console.log(gulpBowerFiles());
	console.log(mainBowerFiles());

});
