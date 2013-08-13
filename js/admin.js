var Session_CCT_Admin = {
	
	addSlide: function( element ) {
		jQuery(element).siblings('.scct-slide-list').append(scct_slide_html);
	},
	
	addBookmark: function( element ) {
		jQuery(element).siblings('.scct-bookmark-list').append(scct_bookmark_html);
	},
	
	removeSection: function( element ) {
		jQuery(element).closest('.scct-admin-section').remove();
	},
	
	changeType: function( element ) {
		element = jQuery(element);
		var slide = element.closest('.scct-slide');
		
		slide.removeClass('show-markup');
		slide.removeClass('show-image');
		slide.addClass('show-'+element.val());
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