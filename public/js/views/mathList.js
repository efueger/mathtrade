define(['views/hb'],function(HB){


var mathList =  HB.extend({
	onInit:function(){
		this.filtered = this.collection;
		this.model.on('remove',this.render);
		this.model.on('add',this.render);
	},
	template:'math-items-template',
	events:{
		'keyup [data-math-filter]':'filter'
	},
	filter:function(evt) {
		var val = $(evt.target).val();
		this.filter = val;
		this.model.trigger('change');
	},


	dataForTpl:function(){
		var r = new RegExp(this.filter,'g');
		this.filtered = this.model.filter(function(m){
			return r.test(m.name);
		});
		console.log(this.filtered);
		return this.model.toJSON();
	},
	skipchange:true

});


	return mathList;
})