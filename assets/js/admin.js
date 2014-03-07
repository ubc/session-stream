var Session_CCT_Admin = {
	data: session_stream_data,
	
	onReady: function() {
		
	},
	
	addSection: function( element, type ) {
		jQuery(element).siblings('.scct-section-list').append(Session_CCT_Admin.data.template[type]);
	},
	
	removeSection: function( element ) {
		jQuery(element).closest('.scct-admin-section').remove();
	},
	
	move: function( element, up ) {
		element = jQuery(element).closest('.scct-admin-section');
		
		if ( up ) {
			element.insertBefore(element.prev('.scct-admin-section'));
		} else {
			element.insertAfter(element.next('.scct-admin-section'));
		}
	},
}

jQuery(document).ready( Session_CCT_Admin.onReady );