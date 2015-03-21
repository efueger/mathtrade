define(['views/hb'],function(HB){


var gamesView =  HB.extend({
	onInit:function(){
		this.model.on('remove',this.render);
		this.model.on('add',this.render);
	},
	template:'item-list-template',
	events: {
		'click [data-add]':'add',
		'click [data-delete]':'delete',
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