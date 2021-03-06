var SCCT_Module_Pulse = {
	
	init: function() {
		jQuery('<input />')
			.attr( 'type', 'hidden' )
			.attr( 'class', 'ss_synctime' )
			.attr( 'name', 'ss_synctime' )
			.appendTo( '.pulse-form' );
		
		jQuery('.pulse-form textarea').keyup( function() {
			SCCT_Module_Media.pauseForModule();
		} );
		
		jQuery('.pulse-form').submit( function() {
			jQuery('.ss_synctime').val( SCCT_Module_Media.media.roundTime() );
			SCCT_Module_Media.playForModule();
		} );
	},
	
	onContentLoad: function() {
		if ( typeof CTLT_Stream != 'undefined' ) { // Check for stream activity
            CTLT_Stream.on( 'server-push', SCCT_Module_Pulse.listen );
		}
		
		SCCT_Module_Media.media.on( 'loadedmetadata', SCCT_Module_Pulse.loadPulses );
		SCCT_Module_Media.media.on( 'loadedmetadata', SCCT_Module_Pulse.loadMarkers );
	},
    
    listen: function( data ) {
		if ( data.type == 'pulse' ) { // We are interested
			var pulse_data = jQuery.parseJSON(data.data);
			SCCT_Module_Pulse.addPulse( pulse_data, pulse_data.synctime, true );
		}
    },
	
	loadMarkers: function() {
		
	},
	
	loadPulses: function() {
		var list = Session_CCT_View.data.pulse;
		
		for ( index in list ) {
			SCCT_Module_Pulse.addPulse( list[index], list[index].synctime, false );
		}
	},
	
	addPulse: function( data, start, sort ) {
		var new_pulse = Pulse_CPT_Form.single_pulse_template( data );
		
		SCCT_Module_Media.media.pulse( {
			start: start,
			end: SCCT_Module_Media.media.duration(),
			text: new_pulse,
			sort: sort,
			target: "pulse-list",
		} );
	},
}

SCCT_Module_Pulse.init();
document.addEventListener( "DOMContentLoaded", SCCT_Module_Pulse.onContentLoad, false );