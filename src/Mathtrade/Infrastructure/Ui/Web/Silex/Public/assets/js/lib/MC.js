define(function(require){

	/**
	 * Module to work with handlebars templates, handles the compilation and caches it 
	 * so further requests can use the compiled code
	 */
	MC = (function(){
		var mc = {};

		/**
		 * Trim spaces at the beggining and end of lines
		 * @param  {string} str [description]
		 * @return {string}     Trimmed string
		 */
		mc.trim = function(str) {
			if(str == undefined) str = '';
			
			var	str = str.replace(/^\s\s*/gm, ''),
				ws = /\s/gm,
				i = str.length;
			while (ws.test(str.charAt(--i)));
			return str.slice(0, i + 1);
		}

		mc.path = function(id,file) {
		    var fold = id % 64;

		    if (fold < 0) {
		        fold = 0xFFFFFFFF + fold + 1;
		    }
		    fold = parseInt(fold, 10).toString(16);
		    if(fold==0)fold='00';
		    return '/pic/'+fold+'/'+file;
		}

		return mc;
	})();


	return MC;

})