define(['views/hb'],function(HB){

var wantList =  HB.extend({
	onInit:function(options){
		this.wish = options.wish;
		this.wildcards = options.wildcards;
		// this.model.on('remove',this.render);
		// this.model.on('add',this.render);
	},
	template:'want-items-template',
	events:{
		'click [data-createWildcard]':'createWildcard',
		'click [data-saveWildcard]':'saveWildcard',
		'click [data-addToWant]':'addToWant',
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
		var val = $('[data-wild-id]').val();	

		var m = this.wildcards.get(id);
		m.set('name',val);
		this.render();
	},

	onRender:function(){
		var self = this;
		_.defer(function(){

			//$('#wishlist li').draggable({ 
			$('#myinterests li').draggable({ 
				revert: true, helper: "clone" 
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

			$( ".wildcard-items" ).sortable();
			$( ".want-items" ).sortable();
		});
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