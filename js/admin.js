var Session_CCT_Admin = {
	frame_output: null,
	
	onReady: function() {
		jQuery('.upload-image-button').on( 'click', Session_CCT_Admin.onImageSelect );
		jQuery('.scct-slide-type').change();
	},
	
	onImageSelect: function( event ) {
		event.preventDefault();
		
		Session_CCT_Admin.frame_output = jQuery(this).siblings('.upload-image');
		
		// If the media frame doesn't exist, create it.
		if ( ! wp.media.frames.ssct_frame ) {
			// Create the media frame.
			var frame = wp.media( {
				title: "Choose an Image",
				button: { text: "Choose" },
				library: { type : 'image' },
				multiple: false  // Set to true to allow multiple files to be selected
			} );
			
			// When an image is selected, run a callback.
			frame.on( 'select', function() {
				// We set multiple to false so only get one image from the uploader
				attachment = frame.state().get('selection').first().toJSON();
				
				// Do something with attachment.id and/or attachment.url here
				Session_CCT_Admin.frame_output.val(attachment.url);
			} );
			
			wp.media.frames.ssct_frame = frame
		}
		
		// Finally, open the modal
		wp.media.frames.ssct_frame.open();
	},
	
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

jQuery(document).ready( Session_CCT_Admin.onReady );