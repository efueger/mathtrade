define(['models/items','models/wildcards'],function(Items,Wildcards){

	/**
	 * MathTrade module handles all the information that a user needs encapsulating them and
	 * making them available for all the parts of the site
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
			$.post('/rest/useritems/',{id:item.get('id'),type:2},function(resp){
			});
		}

		mt.excludeBulk = function(bulk){
			excluded.add(bulk);
			//var ids = JSON.stringify(_.map(bulk,function(i){return i.get('item_id')}));
			var ids = JSON.stringify(_.map(bulk,function(i){return i.id}));

			$.post('/rest/useritems/',{bulk:ids,type:2},function(resp){
			});
		}

		/**
		 * Adds a game to the list of interested
		 * @param  {[type]} item [description]
		 */
		mt.addwant = function(item){
			interested.add(item);

			$.post('/rest/useritems/',{id:item.get('id'),type:1},function(resp){
				console.log(resp);
			});

		}




		return mt;
	})();


	return MT;

})