require.config({
//baseUrl: "../bower_components/",
  paths: {
  	bower: '../../components',
    jquery: '../../components/jquery/dist/jquery.min',
    jqueryui: '../../components/jquery-ui/jquery-ui.min',
    underscore: '../../components/underscore/underscore-min',
    backbone: '../../components/backbone/backbone',
    handlebars: '../../components/handlebars/handlebars.min',
    plupload: '../../components/plupload/js/plupload.full.min',
    textcomplete: '../../components/jquery-textcomplete/dist/jquery.textcomplete.min',
    //models:'../../models'
  },
});

var userItems;

require(['jquery','views/mathList','models/mathItems','views/hb','MT'],function($,mathView,Items,HB,MT){
    
    userItems = new Items(games);  

    var gs = new Items();
    MT.init({
      user:gs,
      wildcards:wildcards
    });

    var Router = Backbone.Router.extend({
      routes: {
        "":             "mygames",
        "mathtrade":    "mathtrade",
        "list/:type":   "mathtrade", 
        "addgame":      "addgame",  
        "want":         "want", 
        "want/:id":         "want",  
        "edit/:id":     "addgame",
      },

      mygames: function(type) {
        
        var m = userItems;
    
        var gamesView =  HB.extend({
          onInit:function(){
            this.model.on('remove',this.render);
            this.model.on('add',this.render);
          },
          template:'games-template',
          events:{
            'click [data-toggle-mt]':'toggleMT',
          },

          //Toggles this item in the current mt
          toggleMT:function(evt){
            var id = $(evt.target).data('toggle-mt');
            var m = this.model.get(id);
            m.set('inMT',!m.get('inMT'));

            $.post('/rest/addtomt',{id:id})
          }
        });

        
        $('#main').html(new gamesView({
            model:m,
            skipchange:true

        }).el);
      },


      /**
       * Display the items currently in the mathtrade without my excluded
       */
      mathtrade: function(type) {
        require(['jquery','views/mathList','models/mathItems','views/hb'],function($,mathView,mathItems,HB){
          var m = MT.user;
          m = new mathItems([]);

          //Get the items we have not decided yet
          if (type == undefined) {
            m.url = '/rest/pendingitems/';
          }  
          //Now either the items we are interested or have excluded in case we change opinion 
          else{
            m.url = '/rest/itemstype/'+type+'/';
          }
         
          m.fetch({
            success:function(collection,resp){
            },
            reset:true
          });
        
          
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
          ],function($,wantView,mathItems,HB,Wildcard,Wildcards,Wantlist){

          var _ = require('underscore');
          
          var m = new mathItems([]);
          m.url = (id == undefined ? '/rest/itemsbyuser/' : '/rest/items/'+id+'/');

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

          var wish = new mathItems([]);
          wish.url = '/rest/useritems/';
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
            userItems : userItems,    //Reference to the userItems where the item will be added
            implements:[MC.interfaces.typeChange({silent:true})],
            nestedViews: {
                '#additional-images': new addImg({
                template:'additional-images-template',
                model: newgame,
                })
              }
          });
          $('#main').html(this.currentView.el)
        });
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
      var router = new Router();
      Backbone.history.start();
    
});