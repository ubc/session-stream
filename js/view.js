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
		
		scct_question_template = doT.template( scct_question_template );
		
		Session_CCT_View.media = Popcorn.smart( '#scct-media', session_data.media.url );
		Session_CCT_View.media.on( 'loadedmetadata', Session_CCT_View.loadSlides );
		Session_CCT_View.media.on( 'loadedmetadata', Session_CCT_View.loadPulses );
		Session_CCT_View.media.on( 'loadedmetadata', Session_CCT_View.loadMarkers );
		Session_CCT_View.media.on( 'loadedmetadata', Session_CCT_View.loadQuestions );
		
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
			Session_CCT_View.addPulse( pulse_data[index], pulse_data[index].synctime, false );
		}
	},
	
	loadQuestions: function() {
		console.log("load");
		for ( index in session_data.questions.list ) {
			var question = session_data.questions.list[index];
			Session_CCT_View.addQuestion( question, question.synctime );
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
			Session_CCT_View.addPulse( pulse_data, pulse_data.synctime, true );
		}
    },
	
	addQuestion: function( data, start ) {
		var new_question = scct_question_template( data );
		
		Session_CCT_View.media.footnote( {
			start: start,
			end: Session_CCT_View.media.duration(),
			text: new_question,
			target: "scct-questions",
		} );
	},
	
	addPulse: function( data, start, sort ) {
		var new_pulse = Pulse_CPT_Form.single_pulse_template( data );
		
		Session_CCT_View.media.pulse( {
			start: start,
			end: Session_CCT_View.media.duration(),
			text: new_pulse,
			sort: sort,
			target: "pulse-list",
		} );
	},
}

document.addEventListener( "DOMContentLoaded", Session_CCT_View.onContentLoad, false );