 CodeMirror.defineMode("mustache", function(config, parserConfig) {
  var mustacheOverlay = {
    token: function(stream, state) {
      var ch;
      if (stream.match("{{")) {
        while ((ch = stream.next()) != null)
          if (ch == "}" && stream.next() == "}") break;
        stream.eat("}");
        return "mustache";
      }
      while (stream.next() != null && !stream.match("{{", false)) {}
      return null;
    }
  };
  return CodeMirror.overlayMode(CodeMirror.getMode(config, parserConfig.backdrop || "text/html"), mustacheOverlay);
});


jQuery(function(){
	
	var wck_stp_textareas = ["wck_stp_template_all", "wck_stp_template_single", "wck_stp_singlepost_template"]
	var length = wck_stp_textareas.length
	element = null
	var cmeditors = new Array();;
	
	for ( var i=0; i < length; i++ ){
		element = wck_stp_textareas[i]
		
		if ( jQuery( "#" + element ).length > 0 ){			
			var editor = CodeMirror.fromTextArea(document.getElementById( element ), {
				mode: "mustache",
				lineNumbers: true,
				extraKeys: {
					"F11": function(cm) {
					cm.setOption("fullScreen", !cm.getOption("fullScreen"));
					},
					"Esc": function(cm) {
					if (cm.getOption("fullScreen")) cm.setOption("fullScreen", false);
					}		
				}
			});
			cmeditors.push( editor );
		}
	}

			
	jQuery("body").bind("ajaxComplete", function(e, xhr, settings){
		//var editor_all_ajax = CodeMirror.fromTextArea(document.getElementById("all-posts-archive"), {mode: "mustache"});
		//var editor_single_ajax = CodeMirror.fromTextArea(document.getElementById("single-posts-archive"), {mode: "mustache"});
	});	
	
	
	/* Callback implementetion to refresh codemirror instance when the metabox is loaded hidden and we show it
	   the postboxes.pbshow is a callback implemented in wordpress that we can define to run a piece of code on showing the inside of a postbox
	*/
	postboxes.pbshow = function(box){
		jQuery('.CodeMirror').each(function(i, el){
			el.CodeMirror.refresh();
		});		
	}
})


