require.config({
	//baseUrl: "../bower_components/",
  paths: {
  	bower: '../../bower_components',
    jquery: '../../bower_components/jquery/dist/jquery.min',
    underscore: '../../bower_components/underscore/underscore-min',
    backbone: '../../bower_components/backbone/backbone',
    handlebars: '../../bower_components/handlebars/handlebars.min',
    plupload: '../../bower_components/plupload/js/plupload.full.min',
    textcomplete: '../../bower_components/jquery-textcomplete/dist/jquery.textcomplete.min',
    models:'../../models'
  },
  /*
  shim: {
        'backbone': {
            deps: ['underscore', 'jquery'],
            exports: 'Backbone'
        },
        'underscore': {
            exports: '_'
        }
    }*/

});

require(['jquery','underscore','backbone','router'],function($,_,Backbone,Router){


	var router = new Router();
  Backbone.history.start();
});


// <script type="text/javascript" src="../bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
