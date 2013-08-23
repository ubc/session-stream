var SCCT_Module_Media = {
	media: null,
	
	onContentLoad: function() {
		SCCT_Module_Media.media = Popcorn.smart( '#scct-media', Session_CCT_View.data.media.url );
	},
	
	skipTo: function( time ) {
		//SCCT_Module_Media.media.pause();
		SCCT_Module_Media.media.currentTime( time );
	},
}

document.addEventListener( "DOMContentLoaded", SCCT_Module_Media.onContentLoad, false );