define(['views/hb','lib/MC','MT','textcomplete'],function(HB,MC,MT){


	var addgame =  HB.extend({
		onInit:function(){
		},
		events: {
			'submit':'addgame'
		},
		addgame:function(evt){
			evt.preventDefault();
			console.log('adding',this.model.toJSON());
			if(!this.model.attributes['id']){
				this.model.attributes['id']=this.model.cid;
				//MT.user.add(this.model);
				
				$.post('/rest/items/',this.model.toJSON());

			}
			else {
				//We just modified the game
			}
			//window.location.href = '#';

		},
		onRender:function(){
			var model = this.model;
			_.defer(function(){

				$('#bgg_id').textcomplete([{
			        match: /((.|\s){2,})$/,
			        search: function (term, callback) {
			        	$.ajax({
							url: 'http://muevecubos.com/api/game',
							data:{id:term},
							dataType: "jsonp",
							jsonp : "jsonp",
							success:function(result){
								callback(result);
							}
						});
			        },
			        replace: function (data) {
			        	//Additionally set the name	
			        	model.set({name:data.name,bgg_id:data.bgg_id,img:data.img},{silent:true});
			        	$('#img').html('<img src="http://muevecubos.com'+MC.path(data.id,data.img)+'" />')
			        	$('#name').val(data.name);
			        	model.setMedia();
			        	model.trigger('img_added');
			        

			            return data.bgg_id;
			        },
			        template: function(obj) {
			            return obj.name;
			        },
			        index:1

			    },
			    {
			        match: /\@img$/,
			        search: function (term, callback) {
			            callback([]);
			            $('#file').click();
			            var el = $('#type');
			            console.log(el);
			            el.val(el.val().replace(/\@img$/, ''));
			        },
			        replace: function (data) {
			            return '';
			        },
			    }

			    ]).on({
			        'textComplete:select': function (e, value, strategy) {
			           _.defer(function(){
			           	$('#description').focus();
			           })
			        }
			    });

		  		this.uploader = MC.upload.single('img',{
		  			uploaded:function(up, file, info) {
		                // Called when file has finished uploading
		                var obj = JSON.parse(info.response);
		                $('#img').html('<img src="/uploads/'+obj.file+'" />');
		                model.set('img',obj.file,{silent:true});
		                model.setMedia();
		                model.trigger('img_added');
		  			}
	            });

			}.bind(this));

		},
		template:'addgame-template'
	});


	return addgame;
});