var SCCT_Module_Toolbar = {
	
	onContentLoad: function() {
		jQuery('.tool').tooltip( {
			placement: 'left',
			container: 'body',
			delay: { show: 1500, hide: 0 },
		} );
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