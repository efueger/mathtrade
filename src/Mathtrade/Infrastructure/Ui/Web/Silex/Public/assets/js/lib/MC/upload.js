define(['lib/MC','plupload'],function(MC,pu){

	MC.upload = (function(){
		mc = {};

		mc.initialize=function(id,data) {
			var uploader = new plupload.Uploader({
			    runtimes : 'html5,flash,html4',
			    originalId: data.original,
			    browse_button : id, // you can pass in id...
			    url : "/",
			    flash_swf_url : '../bower_components/plupload/js/Moxie.swf',
			    init: {
			        PostInit: function() {
			            
			        },
			        FilesAdded: function(up, files) {
			           up.start();
			        },
			        FileUploaded: function(up,file,info){
			        	info.originalId = up.settings.originalId;
			        	data.uploaded(up,file,info);
			        },
			        UploadProgress: function(up, file) {
			        //     document.getElementById(file.id).getElementsByTagName('b')[0].innerHTML = '<span>' + file.percent + "%</span>";
			         },
			        Error: function(up, err) {
			            document.getElementById('console').innerHTML += "\nError #" + err.code + ": " + err.message;
			        }
			    }
			});
			uploader.init();
			return uploader;
		}

		mc.single = function(id,data) {
			return this.initialize(id,data);
		}

		mc.init = function(selector,data) {
			$(selector).each(function(i,v){
				data.original = $(v).data('add-upload');
				console.log($(v),$(v).attr('id'));
				mc.initialize($(v).attr('id'),data);	
			});
		}


		return mc;
	})();

	return MC;

})