define(['models/items'],function(Items){

	/**
	 * Module to work with handlebars templates, handles the compilation and caches it 
	 * so further requests can use the compiled code
	 */
	MT = (function(){
		var mt = {},
			excluded = new Items({}),
			interested = new Items({});

		mt.init = function(data){
			mt.user = data.user;
			
		}
		/**
		 * Excludes a game and adds it to the excluded game list
		 * @param  {[type]} item [description]
		 */
		mt.exclude = function(item){
			excluded.add(item);
		}

		/**
		 * Excludes a game and adds it to the excluded game list
		 * @param  {[type]} item [description]
		 */
		mt.want = function(item){
			interested.add(item);

			$.post('/public/rest/useritems/1',{id:1,type:1},function(resp){
				console.log(resp);
			});

		}




		return mt;
	})();


	return MT;

})