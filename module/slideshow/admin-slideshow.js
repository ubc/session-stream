var SCCT_Module_Slideshow = {
	frame_output: null,
	
	onReady: function() {
		jQuery('.upload-image-button').on( 'click', SCCT_Module_Slideshow.onImageSelect );
		jQuery('.scct-slide-type').on( 'change', SCCT_Module_Slideshow.changeType );
		jQuery('.scct-slide-type').change();
	},
	
	changeType: function() {
		element = jQuery(this);
		var slide = element.closest('.scct-slide');
		
		slide.removeClass('show-markup');
		slide.removeClass('show-image');
		slide.addClass('show-'+element.val());
	},
	
	onImageSelect: function( event ) {
		event.preventDefault();
		
		SCCT_Module_Slideshow.frame_output = jQuery(this).siblings('.upload-image');
		
		// If the media frame doesn't exist, create it.
		if ( ! wp.media.frames.ssct_frame ) {
			// Create the media frame.
			var frame = wp.media( {
				title: "Choose an Image",
				button: { text: "Choose" },
				library: { type : 'image' },
				multiple: false, // Set to true to allow multiple files to be selected
			} );
			
			// When an image is selected, run a callback.
			frame.on( 'select', function() {
				// We set multiple to false so only get one image from the uploader
				attachment = frame.state().get('selection').first().toJSON();
				
				// Do something with attachment.id and/or attachment.url here
				SCCT_Module_Slideshow.frame_output.val(attachment.url);
			} );
			
			wp.media.frames.ssct_frame = frame
		}
		
		// Finally, open the modal
		wp.media.frames.ssct_frame.open();
	},
}

jQuery(document).ready( SCCT_Module_Slideshow.onReady );