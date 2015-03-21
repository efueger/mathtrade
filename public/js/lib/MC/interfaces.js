define(['lib/MC'],function(MC){

	MC.interfaces = (function(){
		var ep = {};

		/**
		 * Change a model or namespaced model when the user types on elements of the page
		 * @param  {object} trigger: the event trigger that will cause to update the model
		 */
		ep.typeChange = function(data) {
			var data = data || {};
			var ifc = {};
			//Define the event that will trigger the event
			ifc.events = {};
			ifc.events['keyup '+ (data.trigger?data.trigger:'input,textarea')]='typing';
			
			//Define the function associated with de trigger
			ifc.typing = function(evt) {
				evt.preventDefault();
				var $el = $(evt.target);

				//The field does not trigger the update
				if($el.data('skip-update'))return;
				var attr = $el.attr('name'),
				    value = $el.val();

				//If the attribute is namespaced
				if (attr.indexOf('.')!=-1) {
					var d = attr.split('.');

					var m = this.model[d[0]].find(function(e){
						return e.get('id') == d[1];
					});

					var key = d[0]+'.'+d[2];

					m.set(d[2],value,this.zones[key]?true:false);
					//Check if there is a zone defined
					if (this.zones[key]) {
						//if (!this.zones[attr].el) {
						this.zones[key].el = $(this.zones[key].selector+d[1]);
						//}

						if (!this.zones[key].hb) {
							this.zones[key].hb = EP.Handlebars.text(this.zones[key].value)
						}
						this.zones[key].el.html(this.zones[key].hb(m.toJSON()));
					}
					this.model.trigger('change',m);

				}
				else {
					if (this.model.setValue != undefined)
						this.model.setValue(attr,value,true);
					else {
						this.model.set(attr,value,data.silent?{silent:true}:{});
					}

					//If the user specifices an event we trigger that one.
					if (data.triggerevent) {
						this.model.trigger(data.triggerevent);
					} 

				}
			}

			return ifc;
		}

		/**
		 * This interface adds a Save button that triggers a save on the model when clicked. It also displays a message 
		 * and different color when it has data pending to save
		 * @param  {object} data 
		 * @return {object} the interface that will be merged into the view
		 */
		ep.saveButton = function(data) {
			var ifc = {};

			ifc.onRender = function(self) {
				var saveButton = new EP.views.savePending({model:self.model});

				//We need a save button
				$('#save-placeholder',self.$el).html(saveButton.render().el);
			}
			return ifc;
		}

		/**
		 * Enables a view to have toggable zones. This will be saved in gui_visible var in the model
		 * @param  {object} data options
		 * @return {object} the interface to be merged in the view
		 */
		ep.guiVisible = function(data) {
			var ifc = {};

			ifc.events = {'click [data-gui-visible]':'guiToggle'}
			ifc.gui_visible = {};

			ifc.guiToggle = function(evt) {
				evt.preventDefault();
				var $el = $(evt.target);
				var attr = $el.data('gui-visible');
				if (attr.indexOf('.')!=-1) {
					var d = attr.split('.');
					this.model[d[0]].guiVisible(d[1]);

					//Close main menu
					this.gui_visible.form_options = false;
				}
				//No namespace then trigger current view
				else {
					this.gui_visible[attr]= this.gui_visible[attr]?!this.gui_visible[attr]:true;
				}
				if (data.trigger != undefined)
						this.model.trigger(data.trigger);
			}
			return ifc;
		}


		/**
		 * This interface updates the model when a checkbox is clicked
		 * @param  {object} data 
		 * @return {objcet} the interface
		 */
		ep.checkbox = function(data) {
			
			var ifc = {};

			ifc.events = {'click [data-checkbox]':'checkbox'}
			/*
			 * When a checkbox is clicked toggle the value on the model
			 */
			ifc.checkbox = function(evt) {
				console.log('bing');
				var $el = $(evt.target);
				var data = $el.data('checkbox');
				var attr = data.name;

				var trigger = $el.data('trigger');


				if (attr.indexOf('.')!=-1) {
					var d = attr.split('.');
					var m = this.model[d[0]].find(function(e){
						return e.get('id') == d[1];
					});
					var val = m.get(d[2]) == '1' ? 0:1;
					m.set(d[2],val);
					this.model.trigger('change');

					if (data == 'refresh') {
						this.model.trigger('changeSidebar');
					}
				}
				else {
					var val = this.model.get(attr) == '1' ? 0:1;
					this.model.setValue(attr,val,{silent:true});
					this.model.trigger('savePending',this.model);

					if (trigger) {
						this.model.trigger(trigger);
					}
				}
			}
			return ifc;
		}

		ep.dropdown = function(data) {
			
			var ifc = {};

			ifc.events = {'change select':'dropdown'}
			/*
			 * When a checkbox is clicked toggle the value on the model
			 */
			ifc.dropdown = function(evt) {
				console.log('bingo');
				var $el = $(evt.target);
				//var data = $el.data('checkbox');
				var attr = $el.attr('name');
				var val = $el.val();


				if (attr.indexOf('.')!=-1) {
					var d = attr.split('.');
					var m = this.model[d[0]].find(function(e){
						return e.get('id') == d[1];
					});
					var val = m.get(d[2]) == '1' ? 0:1;
					m.set(d[2],val);
					this.model.trigger('change');

					if (data == 'refresh') {
						this.model.trigger('changeSidebar');
					}
				}
				else {
					var opts = {silent:true};
					var data = $el.data('dropdown');
					if (data == 'refresh') {
						opts = {};
					}
					this.model.set(attr,val,opts);
					this.model.trigger('savePending',this.model);
				}
			}
			return ifc;
		}

		ep.scrollTop = function(data) {
			
			var ifc = {};

			ifc.onRender = function() {
				console.log('scrollTop');
				$(window).scrollTop();
			}
			return ifc;
		}

		return ep;
	})();

})