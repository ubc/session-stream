var Session_CCT_Pulse = {
	
	init: function() {
		jQuery('<input />')
			.attr( 'type', 'hidden' )
			.attr( 'class', 'ss_synctime' )
			.attr( 'name', 'ss_synctime' )
			.appendTo( '.pulse-form' );
		
		jQuery('.pulse-form').submit( function() {
			jQuery('.ss_synctime').val( Session_CCT_View.media.roundTime() );
		} );
	}
}

Session_CCT_Pulse.init();