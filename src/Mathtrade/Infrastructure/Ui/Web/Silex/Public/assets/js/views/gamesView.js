define(['views/hb','MT'],function(HB,MT){


var gamesView =  HB.extend({
	onInit:function(){
		this.model.on('remove',this.render);
		this.model.on('add',this.render);
	},
	template:'item-list-template',
	events:{
		'click [data-exclude]':'exclude',
		'click [data-want]':'want',
	},

	add:function(evt){
		var id = $(evt.target).data('add');
		var m = this.model.get(id);
		this.model.remove(m);
		mt.add(m);
	},

	delete:function(evt) {
		var id = $(evt.target).data('delete');
		var m = this.model.get(id);
		this.model.remove(m);
	},
});


	return gamesView;
})