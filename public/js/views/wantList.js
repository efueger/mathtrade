define(['views/hb'],function(HB){

var wantList =  HB.extend({
	onInit:function(options){
		this.wish = options.wish;
		this.wildcards = options.wildcards;
		// this.model.on('remove',this.render);
		this.model.wantlist.on('repaint',this.render);
	},
	template:'want-items-template',
	events:{
		'click [data-createWildcard]':'createWildcard',
		'click [data-saveWildcard]':'saveWildcard',
		'click [data-addToWant]':'addToWant',
		'click [data-delete-wild]':'deleteWild',
		'click [data-remove-from-want]':'removeFromWant',
		'click [data-remove-from-wild]':'removeFromWild',
		'click [data-addwildcardtowant]':'addWildcardToWant',

	},
	filter:function(evt) {
		var val = $(evt.target).val();
		this.model.setFilter(val);
		this.model.trigger('change');
	},

	
	createWildcard:function(evt) {
		this.wildcards.make();
		this.render();
	},

	/**
	 * Saves a wilcard to the database
	 * @param  {[type]} evt [description]
	 * @return {[type]}     [description]
	 */
	saveWildcard:function(evt) {
		var id = $(evt.target).data('savewildcard');
		var val = $('[data-wild-input-id]').val();	

		var m = this.wildcards.get(id);
		m.set('name',val);
		this.render();
	},

	deleteWild:function(evt) {
		var id = $(evt.target).data('delete-wild');
		var m = this.wildcards.get(id);
		//Get all items in this wild and add them back
		m.items.each(function(i){
			this.wish.add(i);
		}.bind(this));
		this.wildcards.remove(m);
		//this.wish.add(m);
		this.render();
	}, 

	onRender:function(){
		var self = this;
		_.defer(function(){

			//$('#wishlist li').draggable({ 
			$('#myinterests li').draggable({ 
				revert: true, helper: "clone",
				handle:'.handle',
				start: function() {
			       $('.droptarget').show();
			    },
			    stop: function(){
			    	$('.droptarget').hide();
			    }

			});

			$( ".wilcard-drop" ).droppable({
			    drop: function( event, ui ) {
			        console.log('droped',event,ui);
			        //When dropped we need to add it to the wilcard and remove it from the wish
			        var id = $(event.target).data('wild-id');
			        var m = self.wildcards.get(id);
			        var gid = ui.draggable.data('id');
			        var game = self.wish.get(gid);
			        m.items.add(game);
			        self.wish.remove(game);
			        self.render();
			    }
			});

			$( ".wildcard-items" ).sortable({
				handle:'.handle'
			});
			$( ".want-items" ).sortable({
				stop:function(evt,ui){
					var neworder = $(this).sortable('toArray');
					self.model.wantlist.setOrder(neworder);
				}
			});
		});
	},

	removeFromWild: function(evt) {
		var d = $(evt.target).data('remove-from-wild');
        var m = this.wildcards.get(d.wid);
        var game = m.items.get(d.id);
        m.items.remove(game);
        this.wish.add(game);
        this.render();	
	},


	addToWant:function(evt) {
		var id = $(evt.target).data('addtowant');
		var m = this.wish.get(id);
		this.wish.remove(m);
		this.model.wantlist.addToEnd(m);
		this.render();
	},

	removeFromWant: function(evt) {
		var id = $(evt.target).data('remove-from-want');
		var m = this.model.wantlist.get(id);
		this.model.wantlist.remove(m);
			
		//If it's a wildcard then move it to the wildcards 
		if (m.items != undefined) {
			this.wildcards.add(m);
		}
		else {
			this.wish.add(m);
		}

		this.render();
	},

	addWildcardToWant:function(evt) {
		var id = $(evt.target).data('addwildcardtowant');
		var m = this.wildcards.get(id);
		this.wildcards.remove(m);
		this.model.wantlist.add(m);
		this.render();
	},
	dataForTpl:function(){
		console.log(this);
		var d = {};
			d.user = this.model.at(0).get('user');
			d.current = this.model.at(0).toJSON();
			d.items = this.model.toJSON();
			d.onlyMode = this.model.onlyMode;
			d.wish = this.wish.toJSON();
			d.want = this.model.wantlist.toJSON();
			d.wildcards = this.wildcards.toJSON();
		return d;
	},
	//skipchange:true
});


	return wantList;
})