define(['../../../../../../../../../bower_components/handlebars/handlebars'],function(Handlebars){
	Handlebars.registerHelper('count', function(array, url) {
	  return new Handlebars.SafeString(array.length);
	});
});