define(['backbone','models/items','MT','handlebars'],function(Backbone,Items,MT,Handlebars) {

var games = [
	{
		id:1,
		name:"Caverna",
		description:"Dos partidas en perfecto estado",
		bgg_id:102794
	},
	{
		id:2,
		name:"Agricola",
		description:"1 g"
	}
];

var gs = new Items(games);
MT.init({
	user:gs,
	wants:wants,
	items:mathItems,
	wildcards:wildcards
});



var Router = Backbone.Router.extend({
	  routes: {
	    "":                 "list",//"home",
	    "list":          	"list", 
	    "want": 			"want", 
	    "want/:id": 		"want", 
	    "addgame":          "addgame",  
	    "edit/:id": 		"addgame"   
	  },

		home: function() {
			require(['jquery','views/gamesView','models/items'],function($,gamesView,Items) {
				
				$('#main').html('<div class="row"><div id="my-games" class="col-md-6"></div><div id="mathtrade" class="col-md-6">hola</div></div>');

		  		$('#my-games').html(new gamesView({model:gs}).el);
				//$('#mathtrade').html(new mtView({model:mt}).el);
				//this.currentView.remove();
			});	
	  	},

	  	list: function() {
	  		require(['jquery','views/mathList','models/mathItems','views/hb'],function($,mathView,Items,HB){
	  			console.log(mathItems);
	  			var m = new Items(mathItems);
	  			console.log(m);
	  			$('#main').html(new mathView({
	  				model:m,
	  				nestedViews:{
	  					'#items':new HB({
	  						model:m,
	  						template:'fullmt-list-template'
	  					})
	  				},
	  				skipchange:true

	  			}).el);
	  		});
	  	},

	  	want: function(id) {
	  		require(['jquery','views/wantList','models/mathItems','views/hb','models/wildcard','models/wildcards','models/wantList','jqueryui'
	  			],function($,wantView,Items,HB,Wildcard,Wildcards,Wantlist){

	  			var _ = require('underscore');
	  			
	  			var m = new Items([]);
	  			m.url = (id == undefined ? '/public/rest/itemsbyuser/'+hash : '/public/rest/items/'+id);

	  			m.onlyMode = (id != undefined);
	  			m.fetch({
	  				success:function(collection,resp){
	  					
	  					//collection.at(0).onlyMode = true;
	  					_.each(resp,function(i){
	  						if (!i.wantlist) i.wantlist=[];
	  						var m = collection.findWhere({item_id:i.item_id});
	  						m.wantlist = new Wantlist(i.wantlist);
	  						
	  					})

	  				},
	  				reset:true
	  			});
	  			//var m = new Items(mathItems);
	  			// m.at(0).set('user','edgard');
	  			// m.filterByUser('edgard');
	  			// if (id != undefined) {
	  			// 	m.onlyMode = true;
	  			// }

	  			//var wish = new Items(mathItems.slice(0,10));
	  			var wish = new Items([]);
	  			wish.url = '/public/rest/useritems/'+hash;
	  			wish.fetch({reset: true});
	  			m.wantlist = new Wantlist([]);

	  			m.each(function(i){

	  			});

	  			//var wildcards = new Wildcards([{name:'TEST'}]);



	  			console.log(MT.wildcards);

	  			m.wantlist.add(MT.wildcards.at(0));

	  			$('#main').html(new wantView({
	  				model:m,
	  				wish:wish,
	  				wildcards:MT.wildcards,
	  				skipchange:true

	  			}).el);
	  		});
	  	},

		addgame: function(id) {
			require(['jquery','models/item','views/addGame','views/addImg','lib/MC/interfaces'],function($,Item,AddGame,addImg,interfaces){
				if (id == undefined) {
					var newgame = new Item({});
				}
				else {
					var newgame = MT.user.get(id);
				}



				this.currentView = new AddGame({
					model : newgame,
					implements:[MC.interfaces.typeChange({silent:true})],
					nestedViews: {
				    	'#additional-images': new addImg({
				    		template:'additional-images-template',
							model: newgame,
				    	})
				    }
				});
		  		$('#main').html(this.currentView.el)
			})
	  	},

	  	edit: function(id) {
	  		var editgame = MT.user.get(id);
	  		console.log(editgame);
	  		var adg = new addgameView({
				model : editgame,
				implements:[MC.interfaces.typeChange({silent:true})]
			});
	  		$('#main').html(adg.el)
	  	}

	});
	//MC.router = new Workspace();

	return Router;
});