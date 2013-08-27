var Session_CCT_View = {
	data: scct_data,
	
	toggleModuleDisplay: function( slug, source ) {
		jQuery('.'+slug+'-wrapper').toggleClass('hidden');
		
		if ( source != undefined ) {
			jQuery(source).toggleClass('selected');
		}
	}
}

console.log(Session_CCT_View.data);