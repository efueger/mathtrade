define(['views/hb','handlebars'],function(HB,Handlebars){

var mathList =  HB.extend({
	onInit:function(){
		this.model.on('remove',this.render);
		this.model.on('add',this.render);
	},
	template:'math-items-template',
	events:{
		'keyup [data-math-filter]':'filter',
		'click [data-exclude]':'exclude',
		'click [data-want]':'want',

	},
	filter:function(evt) {
		var val = $(evt.target).val();
		this.model.setFilter(val);
		this.model.trigger('change');
	},

	/**
	 * Excludes a game from the list and places it on the excluded
	 * @param  {[type]} evt [description]
	 */
	exclude:function(evt) {
		var id = $(evt.target).data('exclude');
		var m = this.model.get(id);
		this.model.remove(m);
		for (var i in this.model.filtered) {
			if (this.model.filtered[i].cid == id) {
				this.model.filtered.splice(i,1);
				break;
			}
		}
		MT.exclude(m);
		if (this.model.filtered.length == 0) {
			this.model.resetFilter();
		}

		this.nestedViews['#items'].render();
	},


	/**
	 * Excludes a game from the list and places it on the excluded
	 * @param  {[type]} evt [description]
	 */
	want:function(evt) {
		console.log('want');
		var id = $(evt.target).data('want');
		var m = this.model.get(id);
		this.model.remove(m);
		MT.want(m);
	},

	skipchange:true
});


	return mathList;
})