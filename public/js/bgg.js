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

require(['models/mathitems','views/hb'],function(Items,View){

    var m = new Items([]);
      m.url = '/public/bggimport/get';
      m.fetch({
        success:function(collection,resp){
          console.log(m);
          v.render();
        },
        reset:true
      });
    var v = new View({
       template:'fullmt-list-template',
       model:m,

    });
        
    $('#items').html(v.el);
  });