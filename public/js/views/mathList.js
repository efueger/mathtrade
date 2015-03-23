define(['views/hb'],function(HB){


var mathList =  HB.extend({
	onInit:function(){
		this.model.on('remove',this.render);
		this.model.on('add',this.render);
	},
	template:'math-items-template',
	events:{
		'keyup [data-math-filter]':'filter'
	},
	filter:function(evt) {
		var val = $(evt.target).val();
		this.model.setFilter(val);
		this.model.trigger('change');
	},
	skipchange:true
});


	return mathList;
})