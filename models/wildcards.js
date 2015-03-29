define(['backbone','models/wildcard','underscore'],function(Backbone,Wildcard,_){
	return Backbone.Collection.extend({
		model:Wildcard,
		make: function() {
			var i  = new Wildcard({});
			this.add(i);
			console.log('Wildcard',i);
			return i;
		},
	});
});