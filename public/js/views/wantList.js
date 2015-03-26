define(['views/hb'],function(HB){

var wantList =  HB.extend({
	onInit:function(options){
		this.wish = options.wish;
		// this.model.on('remove',this.render);
		// this.model.on('add',this.render);
	},
	template:'want-items-template',
	events:{
		'keyup [data-math-filter]':'filter',
		'click [data-exclude]':'exclude',
		'click [data-addToWant]':'addToWant',

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
	addToWant:function(evt) {
		var id = $(evt.target).data('addtowant');
		var m = this.wish.get(id);
		this.wish.remove(m);
		this.model.wantlist.add(m);
		this.render();
	},
	dataForTpl:function(){
		console.log(this);
		var d = {};
			d.items = this.model.toJSON();
			d.onlyMode = this.model.onlyMode;
			d.wish = this.wish.toJSON();
			d.want = this.model.wantlist.toJSON();
		return d;
	},
	//skipchange:true
});


	return wantList;
})