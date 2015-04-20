define(['views/hb','models/item','jquery'],function(HB,Wildcard,$){

var wantList =  HB.extend({
	onInit:function(options){
		this.model.on('reset',this.render);
	},
	template:'results-template',
	events:{

	},



	onRender:function(){
		var self = this;
		$('[data-reveal]').click(function(evt){
			$(evt.target).fadeOut();
			$(evt.target).parents('[data-reveal]').fadeOut();
		});

	},


	dataForTpl:function(){
		
		var d = {};
			if (this.model.length > 0) {
				d.user = this.model.at(0).get('username');
				d.current = this.model.at(0).toJSON();
			}
			d.items = this.model.toJSON();
			
		return d;
	},
});


	return wantList;
})
