define(['backbone'],function(Backbone){

	var m = Backbone.Model.extend({
		initialize:function(data) {
			this.packItems = [];

			//If we are passed an array then we are trying to initialize a group
			if (data instanceof Array) {
				for (var i in data) {
					this.packItems.push(new m(data[i]));
				}
				this.setupPack();
			}
			else {
				this.setMedia();
			}
			if (!this.attributes['target_id'])
			this.attributes['target_id'] = this.attributes['id']
			
			if (this.attributes.type == 2) this.attributes.wantid = '%'+this.attributes.name;
			else 
				this.attributes['wantid'] = this.attributes['id'];


			if (this.attributes['inMT'] != undefined) {
				console.log(this.attributes);
				this.attributes['inMT'] = this.attributes['inMT']=='0'?0:1;
			}
		},

		setupPack:function() {
			this.attributes = {};
			this.attributes['name'] = 'Pack ';
			_.each(this.packItems,function(i){
				this.attributes['name'] += '+'+i.get('name');
			}.bind(this));
			this.attributes['is_pack'] = true;
		},


		setMedia:function(){
			if (this.attributes['img']) {
				var id = /pic_([0-9]+)/i.exec(this.get('img'));
				if (id) {
					this.attributes['full_img'] = 'http://muevecubos.com'+MC.path(id[1],this.get('img'));
				}
				else {
					this.attributes['full_img'] = 'uploads/'+this.get('img');
				}
			}
		},

		toJSON : function(options) {
			var d = _.clone(this.attributes);
			if (d.id == undefined)d.id = this.cid;
			if (d.is_pack) {
				d.packItems = [];
				_.each(this.packItems,function(m){
					d.packItems.push(m.toJSON());
				});
			}
			d.wantname = d.id;

			if (this.wantlist) {
				d.wantlist = this.wantlist.toJSON();
			}
			return d;
		}

	});

	return m;

});