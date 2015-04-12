define(['models/items','models/wildcards'],function(Items,Wildcards){

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
			mt.want = new Items(data.want);
			mt.wildcards = new Wildcards(data.wildcards);

			
		}
		/**
		 * Excludes a game and adds it to the excluded game list
		 * @param  {[type]} item [description]
		 */
		mt.exclude = function(item){
			excluded.add(item);
			$.post('/public/rest/useritems/'+hash,{id:item.get('item_id'),type:2},function(resp){
			});
		}

		/**
		 * Excludes a game and adds it to the excluded game list
		 * @param  {[type]} item [description]
		 */
		mt.addwant = function(item){
			interested.add(item);

			$.post('/public/rest/useritems/'+hash,{id:item.get('item_id'),type:1},function(resp){
				console.log(resp);
			});

		}




		return mt;
	})();


	return MT;

})