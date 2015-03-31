define(['backbone','models/item','underscore'],function(Backbone,Item,_){
	return Backbone.Collection.extend({
		model:Item,
		comparator:'pos',
		addToEnd: function(m) {
			m.set('pos',this.models.length-1,{silent:true});
			this.add(m);
		},

		setOrder:function(order) {
			var o = 0;
			_.each(order,function(i){
				var p = i.split('_');
				this.get(p[1]).set('pos',o,{silent:true});
				o++;
			}.bind(this));
			this.sort();
			this.trigger('repaint');
		}
	});
});