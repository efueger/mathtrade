define(['../../../components/backbone/backbone','lib/MC/handlebars'],function(Backbone,Handlebars){

	//Base Handlebars View that has all boilerplate already done for us
	var HB = Backbone.View.extend({
		initialize : function(options){
			model = options.model;
		    _.bindAll(this, 'render');

			//Safe extending of events
	    	this.events = this.events || {};
	    	this.renderQ = this.renderQ || [];
		    
		    //Implementations
		    if (options.implements) {
		    	//Integrate everything in the implementation
		    	_.each(options.implements,function(i){
		    		_.each(i,function(m,k){
		    			if (k=='events') {
		    				_.extend(this.events,m);
		    			}
		    			else if (k=='onRender') {
		    				this.renderQ.push(m);
		    			}
		    			else {
		    				this[k] = m;
		    			}
		    		}.bind(this));
		    	}.bind(this));		
		    }

		    //The view can have nested views
		    if (options.nestedViews) {
		    	this.nestedViews = options.nestedViews;
		    }

		    if (options.onInit) {
		    	this.onInit = options.onInit(options);
		    }

		    //if (options.onRender) this.onRender = options.onRender;

		    //Any template to override?
			if (options.template) this.template = options.template;
		    //Bind basic change on the model?
		    if (!this.skipchange) this.model.on('change',this.render);
		    
		    if (this.onInit) this.onInit(options);

		    this.render();
		},

		onRender: function() {
			
		},


		render: function() {
			//If ther is a function to prepare the data use it otherwis its the model to json.
			var data = this.dataForTpl?this.dataForTpl():this.model.toJSON();
			//Render the template
			this.$el.html(MC.Handlebars.html(this.template,data));

			//If the view has nested views then render them
			
			//Render nested views
			if (this.nestedViews != undefined) {
				_.each(this.nestedViews,function(v,i){
					$(i,this.$el).html(v.render().el);
				}.bind(this));
			}
			
			if (this.onRender != undefined) this.onRender();
			//This is the queue of the onRender of all the implementations
			if (this.renderQ) {
				_.each(this.renderQ,function(f){
					f(this);
				}.bind(this));
			}

			return this;
		}
	});

	return HB;

});
