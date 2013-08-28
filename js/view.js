var Session_CCT_View = {
	data: scct_data,
	
	toggleModuleDisplay: function( slug, source ) {
		jQuery('.'+slug+'-wrapper').toggleClass('hidden');
		jQuery('.session-cct').toggleClass('module-'+slug);
		
		if ( source != undefined ) {
			jQuery(source).toggleClass('selected');
		}
	}
}

console.log(Session_CCT_View.data);