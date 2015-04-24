define(['views/hb','../../../../../../../../../../bower_components/handlebars/handlebars','MT'],function(HB,Handlebars,MT){

	var mathList =  HB.extend({
		onInit:function(){
			//this.model.on('remove',this.render);
			this.model.on('add',this.render);
			this.model.on('reset',this.render);
		},
		template:'math-items-template',
		events:{
			'keyup [data-math-filter]':'filter',
			'click [data-exclude]':'exclude',
			'click [data-exclude-until]':'excludeUntil',
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
			$(evt.target).closest('.row').remove();

			for(var i in window.mathItems) {
				if (window.mathItems[i].item_id == id) {
					window.mathItems.splice(i,1);
					break;
				}
			}

			// if (this.model.filtered)
			// 	this.model.filtered.remove(m);
			for (var i in this.model.filtered) {
				if (this.model.filtered[i].cid == id) {
					this.model.filtered.splice(i,1);
					break;
				}
			}
			MT.exclude(m);
			if (this.model.filtered != undefined && this.model.filtered.length == 0) {
				this.model.resetFilter();
			}

			//this.nestedViews['#items'].render();
		},


		excludeUntil:function(evt) {
			var id = $(evt.target).data('exclude-until');
			var bulk = [];
			
			for (var i in this.model.models) {
				var exid = this.model.models[i].get('id');
				bulk.push(this.model.models[i])
				if (exid == id) {
					break;
				}
			}
			this.model.remove(bulk);
			$(evt.target).closest('.row').prevAll().remove();
			$(evt.target).closest('.row').remove();
			MT.excludeBulk(bulk);
			window.scrollTo(0,0);

		},


		/**
		 * Excludes a game from the list and places it on the excluded
		 * @param  {[type]} evt [description]
		 */
		want:function(evt) {
			var id = $(evt.target).data('want');
			var m = this.model.get(id);
			this.model.remove(m);
			$(evt.target).closest('.row').remove();
			if (this.model.filtered) {
				for (var i in this.model.filtered) {
					if (this.model.filtered[i].id == m.id) {
						this.model.filtered.splice(i,1);
					}
				}
				this.model.trigger('change');
			}
			MT.addwant(m);
		},

		// dataForTpl:function(){
		// 	var d = this.model.toJSON();
		// 	if (d.length > 100) {
		// 		d = d.slice(0,100);
		// 	}
		// 	return d;

		// },

		skipchange:true
	});


	return mathList;
})