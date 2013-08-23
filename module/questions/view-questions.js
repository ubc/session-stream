var SCCT_Module_Questions = {
	current: null,
	
	onContentLoad: function() {
		SCCT_Module_Media.media.on( 'loadedmetadata', SCCT_Module_Questions.loadQuestions );
		Session_CCT_View.data.questions.template = doT.template( Session_CCT_View.data.questions.template );
	},
	
	loadQuestions: function() {
		var list = Session_CCT_View.data.questions.list;
		
		for ( index in list ) {
			SCCT_Module_Questions.addQuestion( list[index], list[index].synctime );
		}
	},
	
	addQuestion: function( data, start ) {
		var new_question = Session_CCT_View.data.questions.template( data );
		
		SCCT_Module_Media.media.question( {
			start: start,
			end: SCCT_Module_Media.media.duration(),
			text: new_question,
			target: "scct-questions",
		} );
	},
	
	submit: function() {
		SCCT_Module_Questions.skip();
	},
	
	skip: function() {
		SCCT_Module_Media.media.removeTrackEvent( SCCT_Module_Questions.current );
	},
}

document.addEventListener( "DOMContentLoaded", SCCT_Module_Questions.onContentLoad, false );