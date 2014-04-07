var SCCT_Module_Toolbar = {
	
	onContentLoad: function() {
		jQuery('.tool').tooltip( {
			placement: 'right',
			container: 'body',
			delay: { show: 1500, hide: 0 },
		} );
       SCCT_Module_Toolbar.clickDetect();
	},
	
    clickDetect : function() {
    
        jQuery('.tool-pulse').click( function(e) {
            jQuery('body').toggleClass('larger-body');
            jQuery('.pulse-wrapper').toggleClass('hidden');
        });
        
        jQuery('.tool-slideshow').click( function(e) {
            jQuery('.slideshow-wrapper').toggleClass('hidden');
        });
        
        jQuery('.tool-media').click( function(e) {
            jQuery('.media-wrapper').toggleClass('hidden');
            jQuery('.timeline-wrapper').css("margin-top", "10px");
        });
        
        jQuery('.tool-reverse').toggle( function() {
                jQuery('.slideshow-wrapper').insertBefore('.media-wrapper');
                jQuery('.timeline-wrapper').insertBefore('.media-wrapper');
            } , function() {
                jQuery('.slideshow-wrapper').insertAfter('.media-wrapper');
                jQuery('.timeline-wrapper').insertAfter('.media-wrapper');
        });
        jQuery('.tool-cycle').toggle( function() {
                jQuery('.slideshow-wrapper').addClass('module-side');
                jQuery('.pulse-wrapper').addClass('pulse-wrapper-side');
            } , function() {
                jQuery('.slideshow-wrapper').removeClass('module-side');
                jQuery('.media-wrapper').addClass('module-side');
            } , function() {
                jQuery('.media-wrapper').removeClass('module-side');
                jQuery('.pulse-wrapper').removeClass('pulse-wrapper-side');
        });
    },
    
	flipMedia: function() {
		var session = jQuery('.session-cct');
		var has_media = session.hasClass('module-media');
		var has_slideshow = session.hasClass('module-slideshow');
		
		if ( has_media && has_slideshow ) {
			session.toggleClass('invert-media');
		} else if ( has_media || has_slideshow ) {
			SCCT_Module_Toolbar.toggleModuleDisplay( 'media', jQuery('.tool-media'), false );
			SCCT_Module_Toolbar.toggleModuleDisplay( 'slideshow', jQuery('.tool-slideshow'), false );
		}
	},
	
	toggleModuleDisplay: function( slug, button, preventNone ) {
    
        if( slug == 'comments') {
        }
    
		if ( preventNone == null ) {
			preventNone = true;
		}
		
		var session = jQuery('.session-cct');
		var has_media = session.hasClass('module-media');
		var has_slideshow = session.hasClass('module-slideshow');
		
		if ( preventNone && slug == 'media' && has_media && ! has_slideshow ) {
			jQuery(button).toggleClass('selected');
			jQuery('.tool-slideshow').toggleClass('selected');
			Session_CCT_View.toggleModuleDisplay('media');
			Session_CCT_View.toggleModuleDisplay('slideshow');
		} else if ( preventNone && slug == 'slideshow' && has_slideshow && ! has_media ) {
			jQuery(button).toggleClass('selected');
			jQuery('.tool-media').toggleClass('selected');
			Session_CCT_View.toggleModuleDisplay('media');
			Session_CCT_View.toggleModuleDisplay('slideshow');
		} else {
			jQuery(button).toggleClass('selected');
			Session_CCT_View.toggleModuleDisplay(slug);
		}
	},
}

document.addEventListener( "DOMContentLoaded", SCCT_Module_Toolbar.onContentLoad, false );