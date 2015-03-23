define(['backbone','models/item','underscore'],function(Backbone,Item,_){
	return Backbone.Collection.extend({
		// initialize:function(options){
		// 	_.defer(function(){
		// 		this.filtered = _.clone(this.models);
		// 		console.log(this.models);
		// 	}.bind(this))
			
		// },
		model:Item,

		setFilter:function(value) {
			var r = new RegExp(value,'gi');
			this.filtered = this.filter(function(m){
				return r.test(m.get('name'));
			});
			console.log(this.filtered);
		},
		
		/**
		 * Override default to display filtered items instead
		 * @return {[type]} [description]
		 */
		toJSON: function(options) {
		   	if (this.filtered == undefined) return this.map(function(model){ return model.toJSON(options); });
      		return _.map(this.filtered,function(model){ return model.toJSON(options); });
   		},
		
	});
});