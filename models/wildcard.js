define(['backbone'],function(Backbone){
	return Backbone.Model.extend({
		initialize:function(options){
			//var items = 
			if(this.attributes.id) this.id = 'w'+this.attributes.id;
			this.items = new Backbone.Collection(this.attributes.items || []);
		},


		toJSON: function(){
		    // call the "super" method - 
		    // the original, being overriden
		    var json = Backbone.Model.prototype.toJSON.call(this);
		    if (!json.id) json.id = this.cid;
		    json.items = this.items.toJSON();

		    json.wantname = '%'+json.name;
		    // manipulate the json here
		    return json;
		}

		
		
	});
});