var SCCT_Module_Slideshow = {
	
	onContentLoad: function() {
		SCCT_Module_Media.media.on( 'loadedmetadata', SCCT_Module_Slideshow.loadSlides );
	},
	
	loadSlides: function() {
		var list = Session_CCT_View.data.slideshow.list;
		
		for ( var index = 0; index < list.length; index++ ) {
			var slide = list[index];
			var next_slide = list[index+1];
			var content = "";
			var duration = parseInt( slide.duration );
			
			switch ( slide.type ) {
				case "markup":
					content = slide.content;
					break;
				case "image":
					content = '<img src="'+slide.image+'" />';
					break;
			}
			
			var end;
			if ( next_slide != undefined ) {
				end = next_slide.start;
			} else {
				end = SCCT_Module_Media.media.duration();
			}
			
			SCCT_Module_Media.media.footnote( {
				start: slide.start,
				end: end,
				text: '<div class="scct-slide-content '+slide.type+'">'+content+'</div>',
				target: "scct-slide",
			} );
		}
	},
}

document.addEventListener( "DOMContentLoaded", SCCT_Module_Slideshow.onContentLoad, false );