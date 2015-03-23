define(['backbone'],function(Backbone){

return Backbone.Model.extend({
		initialize:function(options) {
			this.setMedia();
			
		},
		setMedia:function(){
			console.log('setting media');
			if (this.attributes['img']) {
				var id = /pic_([0-9]+)/i.exec(this.get('img'));
				if (id) {
					this.attributes['full_img'] = 'http://muevecubos.com'+MC.path(id[1],this.get('img'));
				}
				else {
					this.attributes['full_img'] = 'uploads/'+this.get('img');
				}
			}
		}
	});

});