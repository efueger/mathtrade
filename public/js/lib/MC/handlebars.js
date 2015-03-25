define(['lib/MC','handlebars','HBhelpers'],function(MC,Handlebars){
	Handlebars.registerHelper('count', function(array, url) {
	  return new Handlebars.SafeString(array.length);
	});
	
	/**
	 * Module to work with handlebars templates, handles the compilation and caches it 
	 * so further requests can use the compiled code
	 */
	MC.Handlebars = (function(){
		var hb = {},
			templates = {},
			fast = {},
			initialized = false;


		/**
		 * If the template exists just generate the html otherwise find it compile it and do it
		 * @param  {string} id   html id of the script containing the template
		 * @param  {object} data data to apply to the template
		 * @return {string} the generated HTML
		 */
		hb.html = function(id,data) {
			return hb.get(id)(data);
		};

		/**
		 * Gets the compiled template or if it's not just compile it and save it
		 * @param  {string} id html id of the script containing the template
		 * @return {object} compiled template
		 */
		hb.get = function(id) {
			if (templates[id] == undefined) {
				var clean = MC.trim($('#'+id).html());
				clean = clean.replace(/(\r\n|\n|\r)/gm,"");
				templates[id] = Handlebars.compile(clean);
			}
			return templates[id];
		}

		hb.text = function(str) {
			if (fast[str] == undefined) {
				var clean = MC.trim(str);
				clean = clean.replace(/(\r\n|\n|\r)/gm,"");
				fast[str] = Handlebars.compile(clean);
			}
			return fast[str];
		} 

		return hb;
	})();

})