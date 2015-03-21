define(function(require){

	/**
	 * Module to work with handlebars templates, handles the compilation and caches it 
	 * so further requests can use the compiled code
	 */
	MT = (function(){
		var mt = {};

		mt.init = function(data){
			mt.user = data.user;
		}

		return mt;
	})();


	return MT;

})