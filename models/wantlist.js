define(['backbone','models/item','underscore','models/wildcard'],function(Backbone,Item,_,Wilcard){
	return Backbone.Collection.extend({
		model:Item,
		comparator:'pos',
		initialize:function(d){
			console.log(d,'init');
		},
		addToEnd: function(m) {
			m.set('pos',this.models.length-1,{silent:true});
			this.add(m);
		},

		setOrder:function(order) {
			var o = 0;
			_.each(order,function(i){
				var p = i.split('_');
				var m =  this.get(p[1]);
				if (m == undefined) m = this.get('w'+p[1]);
				m.set('pos',o,{silent:true});
				o++;
			}.bind(this));
			this.sort();
			this.trigger('repaint');
		},
		/**
		 * Format the want list in order to send it to the server	
		 * @return string of the [{id:1,t:"i"}]
		 */
		serialize:function() {
			var d = [];
			this.each(function(i){
				var o = {
					id:i.get('id') || i.cid,
					t: i instanceof Wilcard ? 2:1
				};
				if (i.get('type')==2) {
					o.t = 2;
					o.id = i.get('target_id');
				}

				d.push(o);
			});
			return JSON.stringify(d);
		}
	});
});