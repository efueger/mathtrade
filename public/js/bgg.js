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

require(['models/mathitems','views/hb','jquery'],function(Items,HB,$){

  var m = new Items([]);
    m.url = '/public/bggimport/get';
    
  var View = HB.extend({
    events:{
      'click [data-exclude]':'exclude',
    },
    exclude:function(evt){
      var id = $(evt.target).data('exclude');
      var m = this.model.get(id);
      this.model.remove(m);
      this.render();
    },

  });

  var v = new View({
     template:'fullmt-list-template',
     model:m,
  });    

  $(function(){
    $(document).on('click','[data-import-bgg]',function(){
        m.fetch({
          success:function(collection,resp){
            v.render();
            $('[data-add-bgg]').show();
          },
          reset:true
        });
    });

    $(document).on('click','[data-add-bgg]',function(){
       $.post('/public/bggimport/add',{data:JSON.stringify(m.toJSON())},function(){
          window.location.href='/public/home';
       });
    });
  });

        
    $('#items').html(v.el);
  });