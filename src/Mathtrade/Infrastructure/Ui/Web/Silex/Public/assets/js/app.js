require.config({
  paths: {
    jquery: '../../components/jquery/dist/jquery.min',
    jqueryui: '../../components/jquery-ui/jquery-ui.min',
    underscore: '../../components/underscore/underscore-min',
    backbone: '../../components/backbone/backbone',
    handlebars: '../../components/handlebars/handlebars.min',
    plupload: '../../components/plupload/js/plupload.full.min',
    textcomplete: '../../components/jquery-textcomplete/dist/jquery.textcomplete.min'
  }
});

require(['jquery','underscore','backbone','router'],function($,_,Backbone,Router){

	var router = new Router();
  Backbone.history.start();
});
