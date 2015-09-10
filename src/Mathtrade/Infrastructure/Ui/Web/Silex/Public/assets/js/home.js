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

require(['jquery','views/mathList','models/mathItems','views/hb','MT'],function($,mathView,Items,HB,MT){
      
    var Router = Backbone.Router.extend({
      routes: {
        "":             "list",
        "addgame":      "addgame",  
        "edit/:id":     "addgame",
      },

      list: function(type) {
        var m = new Items(games);
    
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