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

require(['jquery','views/mathList','models/mathItems','views/hb'],function($,mathView,Items,HB){
    
    var m = new Items(games);
    console.log(m);
    
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
});