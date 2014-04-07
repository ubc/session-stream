var Session_CCT_View = {
	data: scct_data,
	
	onContentLoad: function() {
		jQuery('.toggle-collapse').on( 'click', Session_CCT_View.toggleCollapse );
		console.log(Session_CCT_View.data);
	},
	
	toggleModuleDisplay: function( slug ) {
		jQuery('.'+slug+'-wrapper').toggleClass('hidden');
		jQuery('.session-cct').toggleClass('module-'+slug);
	},
	
	toggleCollapse: function( event ) {
		jQuery(this).closest('.scct-wrapper').toggleClass('collapsed');
	},
}

document.addEventListener( "DOMContentLoaded", Session_CCT_View.onContentLoad, false );