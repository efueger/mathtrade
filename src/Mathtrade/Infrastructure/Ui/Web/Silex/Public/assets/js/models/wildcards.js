define(['backbone','models/wildcard','underscore'],function(Backbone,Wildcard,_){
	return Backbone.Collection.extend({
		model:Wildcard,
		comparator:'pos',
		make: function() {
			var i  = new Wildcard({});
			this.add(i);
			console.log('Wildcard',i);
			return i;
		},

		setOrder:function(order) {
			var o = 0;
			_.each(order,function(i){
				var p = i.split('_');
				var m =  this.get(p[1]);
				if (m == undefined) m = this.get('w'+p[1]);
				if (m != undefined) {
				var wi = m.items.get(p[2]);
					wi.set('pos',o,{silent:true});
					o++;
				}
			}.bind(this));
			this.sort();
		},
	});
});