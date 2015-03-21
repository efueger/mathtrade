define(['views/hb','lib/MC/upload'],function(HB,MC){


	var addimg = HB.extend({
		onInit:function(){
			this.listenTo(this.model,'img_added',this.render);
		},
		onRender:function(){
			//_.defer(function(){
				MC.upload.init('[data-add-upload]',{
					uploaded:function(up, file, info) {
		                // Called when file has finished uploading
		                var obj = JSON.parse(info.response);
		                console.log(info);

		                if (!model.get('additional_images')) {
		                	model.set('additional_images',[],{silent:true});
		                }

		                if ($.isNumeric(info.originalId)) {
		                	var m = model.get('additional_images')[info.originalId];
		                	m.img = obj.file;
		                	m.full_img ='/public/uploads/'+obj.file;
		                }
		                else {
			               	model.get('additional_images').push({
			               		img:obj.file,
			               		full_img:'public/uploads/'+obj.file
			               	});
		                }
		               	model.trigger('img_added');
		  			}
				});
			//})
		}
	});


	return addimg;
});