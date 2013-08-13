var Session_CCT_View = {
	media: null,
	
	skipTo: function( time ) {
		//Session_CCT_View.media.pause();
		Session_CCT_View.media.currentTime( time );
	},
	
	onContentLoad: function() {
		console.log(session_data);
		
		//jQuery('#scct-slide').css('transform', 'skew(30deg,20deg)'); // Look at w3schools
		
		Session_CCT_View.media = Popcorn.smart( '#scct-media', session_data.media.url );
		
		var time = parseInt( session_data.slides.offset );
		
		for ( index in session_data.slides.list ) {
			var slide = session_data.slides.list[index];
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
			
			Session_CCT_View.media.footnote( {
				start: time,
				end: time + duration,
				text: slide.content,
				target: "scct-slide"
			} );
			
			time += duration;
		}
		
		/*
		media.pulse( {
			start: 4,
			text: "Pulse",
			target: "scct-pulse-list"
		} );
		*/
	}
}

document.addEventListener( "DOMContentLoaded", Session_CCT_View.onContentLoad, false );