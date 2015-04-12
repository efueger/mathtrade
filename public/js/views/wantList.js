define(['views/hb','models/item'],function(HB,Wildcard){

var wantList =  HB.extend({
	onInit:function(options){
		this.wish = options.wish;
		this.wildcards = options.wildcards;
		// this.model.on('remove',this.render);
		this.model.wantlist.on('repaint',this.render);
		this.wish.on('reset',this.render);
		this.model.on('reset',this.render);
	},
	template:'want-items-template',
	events:{
		'click [data-createWildcard]':'createWildcard',
		'click [data-saveWildcard]':'saveWildcard',
		'click [data-addToWant]':'addToWant',
		'click [data-save-want]':'saveWant',
		'click [data-save-wild]':'saveWild',
		'click [data-delete-wild]':'deleteWild',
		'click [data-remove-from-want]':'removeFromWant',
		'click [data-remove-from-wild]':'removeFromWild',
		'click [data-addwildcardtowant]':'addWildcardToWant',

	},

	saveWant: function(evt) {
		var id = $(evt.target).data('save-want');
		$.post('/public/rest/wantlist/'+hash,{
			d:this.model.at(0).wantlist.serialize(),
			wid:id,
		});


	},

	saveWild: function(evt) {
		var id = $(evt.target).data('save-wild');
		var m = this.wildcards.get(id);
		if (m == undefined)m = this.wildcards.get('w'+id);
		$.post('/public/rest/wildcarditems/'+hash,{
			d:JSON.stringify(m.items.toJSON()),
			wid:m.get('id'),
		});


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

		$.post('/public/rest/wildcards/'+hash,{
			name:m.get('name')
		},function(r){
			m.set({id:r.id,wantid:r.wantid});
		},'json');

		this.render();
	},

	deleteWild:function(evt) {
		var id = $(evt.target).data('delete-wild');
		var m = this.wildcards.get(id);

		if (m == undefined) m = this.wildcards.get('w'+id);
		//Get all items in this wild and add them back
		m.items.each(function(i){
			this.wish.add(i);
		}.bind(this));
		this.wildcards.remove(m);
		//this.wish.add(m);
		this.render();
		$.ajax({
		    url: '/public/rest/wildcards/'+hash,
		    type: 'DELETE',
		    data:{id:m.get('id')}
		});
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
			        if (m == undefined)m=self.wildcards.get('w'+id);
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
					self.model.at(0).wantlist.setOrder(neworder);
					console.log(model.at(0).wantlist);
					self.render();
				}
			});
		});
	},

	removeFromWild: function(evt) {
		var d = $(evt.target).data('remove-from-wild');
        var m = this.wildcards.get(d.wid);
        if (m==undefined) m = this.wildcards.get('w'+d.wid);
        var game = m.items.get(d.id);
        m.items.remove(game);
        this.wish.add(game);
        this.render();	
	},


	addToWant:function(evt) {
		var id = $(evt.target).data('addtowant');
		var m = this.wish.get(id);
		this.wish.remove(m);
		this.model.at(0).wantlist.addToEnd(m);
		console.log(this.model.at(0).wantlist);
		this.render();
	},

	removeFromWant: function(evt) {
		var id = $(evt.target).data('remove-from-want');
		var m = this.model.at(0).wantlist.get(id);
		this.model.at(0).wantlist.remove(m);
			
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

		if (m == undefined) m = this.wildcards.get('w'+id);
		//this.wildcards.remove(m);
		this.model.at(0).wantlist.addToEnd(m);
		console.log(this.model.at(0).wantlist);
		this.render();
	},
	dataForTpl:function(){
		
		var d = {};
			if (this.model.length > 0) {
				d.user = this.model.at(0).get('username');
				d.current = this.model.at(0).toJSON();

			}
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