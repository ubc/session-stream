var Session_CCT_View = {
	media: null,
	
	skipTo: function( time ) {
		//Session_CCT_View.media.pause();
		Session_CCT_View.media.currentTime( time );
	},
	
	onContentLoad: function() {
		console.log("Print From Session_CCT_View.onContentLoad");
		console.log(session_data);
		console.log(pulse_data);
		
		//jQuery('#scct-slide').css('transform', 'skew(30deg,20deg)'); // Look at w3schools
		
		Session_CCT_View.media = Popcorn.smart( '#scct-media', session_data.media.url );
		Session_CCT_View.media.on( 'loadedmetadata', Session_CCT_View.loadSlides );
		Session_CCT_View.media.on( 'loadedmetadata', Session_CCT_View.loadPulses );
		Session_CCT_View.media.on( 'loadedmetadata', Session_CCT_View.loadMarkers );
		
		if ( typeof CTLT_Stream != 'undefined' ) { // Check for stream activity
            CTLT_Stream.on( 'server-push', Session_CCT_View.listen );
		}
	},
	
	loadSlides: function() {
		var time = parseInt( session_data.slides.offset );
		
		for ( var index = 0; index < session_data.slides.list.length; index++ ) {
			var slide = session_data.slides.list[index];
			var next_slide = session_data.slides.list[index+1];
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
				end = Session_CCT_View.media.duration();
			}
			
			Session_CCT_View.media.footnote( {
				start: slide.start,
				end: end,
				text: '<div class="scct-slide-content '+slide.type+'">'+content+'</div>',
				target: "scct-slide",
			} );
			
			time += duration;
		}
	},
	
	loadPulses: function() {
		for ( index in pulse_data ) {
			Session_CCT_View.addPulse( pulse_data[index], pulse_data[index].synctime );
		}
	},
	
	loadMarkers: function() {
		for ( index in session_data.bookmarks.list ) {
			var bookmark = session_data.bookmarks.list[index];
			
			Session_CCT_View.media.pulse( {
				start: bookmark.synctime,
				end: Session_CCT_View.media.duration(),
				text: '<a class="bookmark" onclick="Session_CCT_View.skipTo('+bookmark.synctime+');">'+bookmark.title+'<span class="time">'+bookmark.time+'</span></a>',
				sort: true,
				target: "pulse-list",
			} );
		}
	},
    
    listen: function( data ) {
		if ( data.type == 'pulse' ) { // We are interested
			var pulse_data = jQuery.parseJSON(data.data);
			Session_CCT_View.addPulse( pulse_data, pulse_data.synctime );
		}
    },
	
	addPulse: function( data, time ) {
		var new_pulse = Pulse_CPT_Form.single_pulse_template( data );
		var start = time;
		
		Session_CCT_View.media.pulse( {
			start: start,
			end: Session_CCT_View.media.duration(),
			text: new_pulse,
			target: "pulse-list",
		} );
	},
}

document.addEventListener( "DOMContentLoaded", Session_CCT_View.onContentLoad, false );