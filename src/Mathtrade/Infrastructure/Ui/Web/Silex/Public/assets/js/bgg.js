require.config({
	//baseUrl: "../bower_components/",
  paths: {
    jquery: '../../components/jquery/dist/jquery.min',
    jqueryui: '../../components/jquery-ui/jquery-ui.min',
    underscore: '../../components/underscore/underscore-min',
    backbone: '../../components/backbone/backbone',
    handlebars: '../../components/handlebars/handlebars.min',
    plupload: '../../components/plupload/js/plupload.full.min',
    textcomplete: '../../components/jquery-textcomplete/dist/jquery.textcomplete.min'
  },
});

require(['models/mathitems','views/hb','jquery'],function(Items,HB,$){

  var m = new Items([]);
    m.url = '/bggimport/get';
    
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
       $.post('/bggimport/add',{data:JSON.stringify(m.toJSON())},function(){
          window.location.href='/home';
       });
    });
  });

        
    $('#items').html(v.el);
  });