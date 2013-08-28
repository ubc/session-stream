var SCCT_Module_Toolbar = {
	
	onContentLoad: function() {
		jQuery('.tool').tooltip( {
			placement: 'left',
			delay: { show: 1500, hide: 0 },
		} );
	},
	
	flipMedia: function() {
		jQuery('.session-cct').toggleClass('invert-media');
	},
	
	toggleModuleDisplay: function( slug, button ) {
		jQuery(button).toggleClass('selected');
		Session_CCT_View.toggleModuleDisplay(slug);
	},
}

document.addEventListener( "DOMContentLoaded", SCCT_Module_Toolbar.onContentLoad, false );