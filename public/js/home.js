require.config({
	//baseUrl: "../bower_components/",
  paths: {
  	bower: '../../bower_components',
    jquery: '../../bower_components/jquery/dist/jquery.min',
    jqueryui: '../../bower_components/jquery-ui/jquery-ui.min',
    underscore: '../../bower_components/underscore/underscore-min',
    backbone: '../../bower_components/backbone/backbone',
    handlebars: '../../bower_components/handlebars/handlebars.min',
    plupload: '../../bower_components/plupload/js/plupload.full.min',
    textcomplete: '../../bower_components/jquery-textcomplete/dist/jquery.textcomplete.min',
    models:'../../models'
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
        'click [data-exclude]':'exclude',
        'click [data-want]':'want',
        'click [data-toggle-mt]':'toggleMt',
      },

      toggleMt:function(evt){
        var id = $(evt.target).data('toggle-mt');
        var m = this.model.get(id);
      }

    });

    
    $('#main').html(new gamesView({
        model:m,
        skipchange:true

    }).el);
});