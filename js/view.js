var Session_CCT_View = {
	data: scct_data,
	
	toggleModuleDisplay: function( slug ) {
		jQuery('.'+slug+'-wrapper').toggleClass('hidden');
		jQuery('.session-cct').toggleClass('module-'+slug);
	}
}

console.log(Session_CCT_View.data);