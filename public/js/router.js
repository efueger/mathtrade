define(['backbone','models/items','MT'],function(Backbone,Items,MT) {

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
	user:gs
});

var Router = Backbone.Router.extend({
	  routes: {
	    "":                 "home",    // #help
	    "addgame":          "addgame",  // #search/kiwis
	    "edit/:id": 		"addgame"   // #search/kiwis/p7
	  },

		home: function() {
			require(['jquery','views/gamesView','models/items'],function($,gamesView,Items) {
				
				$('#main').html('<div class="row"><div id="my-games" class="col-md-6"></div><div id="mathtrade" class="col-md-6">hola</div></div>');

		  		$('#my-games').html(new gamesView({model:gs}).el);
				//$('#mathtrade').html(new mtView({model:mt}).el);
				//this.currentView.remove();
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