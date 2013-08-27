var SCCT_Module_Media = {
	media: null,
	was_playing: null,
	
	onContentLoad: function() {
		SCCT_Module_Media.media = Popcorn.smart( '#scct-media', Session_CCT_View.data.media.url );
		SCCT_Module_Media.media.on( 'play', SCCT_Module_Media.onPlay );
	},
	
	skipTo: function( time ) {
		SCCT_Module_Media.media.currentTime( time );
	},
	
	pauseForModule: function() {
		if ( SCCT_Module_Media.was_playing == null ) {
			SCCT_Module_Media.was_playing = ! SCCT_Module_Media.media.paused();
		}
		
		if ( SCCT_Module_Media.was_playing ) {
			SCCT_Module_Media.media.pause();
		}
	},
	
	playForModule: function() {
		if ( SCCT_Module_Media.was_playing ) {
			SCCT_Module_Media.media.play();
		}
		
		SCCT_Module_Media.was_playing = null;
	},
}

document.addEventListener( "DOMContentLoaded", SCCT_Module_Media.onContentLoad, false );