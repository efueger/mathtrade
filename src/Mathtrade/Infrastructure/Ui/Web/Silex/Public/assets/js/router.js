define(['backbone','models/items','MT','handlebars'],function(Backbone,Items,MT,Handlebars) {

var gs = new Items(mathItems);
MT.init({
	user:gs,
	wants:wants,
	items:mathItems,
	wildcards:wildcards
});

var Router = Backbone.Router.extend({
		routes: {
			"":                 "list",
			"list":          	"list",
			"list/:type": 		"list", 
			"want": 			"want", 
			"want/:id": 		"want", 
			"addgame":          "addgame",  
			"edit/:id": 		"addgame",
			"results":  		"results" 
		},

		home: function() {
			require(['jquery','views/gamesView','models/items'],function($,gamesView,Items) {
				$('#main').html('<div class="row"><div id="my-games" class="col-md-6"></div><div id="mathtrade" class="col-md-6">hola</div></div>');
		  		$('#my-games').html(new gamesView({model:gs}).el);
			});	
	  	},

	  	list: function(type) {
	  		require(['jquery','views/mathList','models/mathItems','views/hb'],function($,mathView,mathItems,HB){
	  			var m = MT.user;
	  			//if (type != undefined) {
		  			m = new mathItems([]);
		  			m.url = '/rest/itemstype/'+type+'/'+hash;
		  			m.fetch({
		  				success:function(collection,resp){
		  				},
		  				reset:true
		  			});
		  		//}	
	  			
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
	  		require(['jquery','views/wantlist','models/mathItems','views/hb','models/wildcard','models/wildcards','models/wantlist','jqueryui'
	  			],function($,wantView,Items,HB,Wildcard,Wildcards,Wantlist){

	  			var _ = require('underscore');
	  			
	  			var m = new Items([]);
	  			m.url = (id == undefined ? '/rest/itemsbyuser/'+hash : '/rest/items/'+id+'/'+hash);

	  			m.onlyMode = (id != undefined);
	  			m.fetch({
	  				success:function(collection,resp){
	  					_.each(resp,function(i){
	  						if (!i.wantlist) i.wantlist=[];
	  						var m = collection.findWhere({item_id:i.item_id});
	  						m.wantlist = new Wantlist(i.wantlist);
	  					})
	  				},
	  				reset:true
	  			});

	  			var wish = new Items([]);
	  			wish.url = '/rest/useritems/'+hash;
	  			wish.fetch({reset: true});
	  			m.wantlist = new Wantlist([]);
	  			m.wantlist.add(MT.wildcards.at(0));

	  			$('#main').html(new wantView({
	  				model:m,
	  				wish:wish,
	  				wildcards:MT.wildcards,
	  				skipchange:true

	  			}).el);
	  		});
	  	},


	  	results: function() {
	  		require(['jquery','views/resultlist','models/mathItems','views/hb',
	  			],function($,resultList,Items,HB){

	  			var _ = require('underscore');
	  			
	  			var m = new Items([]);
	  			m.url = '/rest/results/'+hash;

	  			m.fetch({
	  				success:function(collection,resp){
	  				},
	  				reset:true
	  			});
	  		
	  			

	  			$('#main').html(new resultList({
	  				model:m,
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
	return Router;
});