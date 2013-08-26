var SCCT_Module_Questions = {
	questions: {},
	
	onContentLoad: function() {
		SCCT_Module_Media.media.on( 'loadedmetadata', SCCT_Module_Questions.loadQuestions );
		Session_CCT_View.data.questions.template = doT.template( Session_CCT_View.data.questions.template );
	},
	
	loadQuestions: function() {
		var list = Session_CCT_View.data.questions.list;
		
		for ( index in list ) {
			SCCT_Module_Questions.addQuestion( list[index].index, list[index], list[index].synctime );
		}
	},
	
	addQuestion: function( id, data, start ) {
		if ( Session_CCT_View.data.questions.meta.random ) {
			data.answers = SCCT_Module_Questions.shuffle( data.answers );
		}
		
		var new_question = Session_CCT_View.data.questions.template( data );
		
		SCCT_Module_Media.media.question( {
			id: id,
			start: start,
			end: SCCT_Module_Media.media.duration(),
			text: new_question,
			target: "scct-questions",
		} );
	},
	
	submit: function( element ) {
		var id = jQuery(element).closest('.question-dialog').data('id');
		
		jQuery.post( Session_CCT_View.data.ajaxurl, {
			action: 'scct_answer',
			session_id: Session_CCT_View.data.session_id,
			question: id,
			answer: jQuery(".question-"+id+" input:radio[name='answer']:checked").val(),
		}, function( response ) {
			console.log(response);
			if ( response != 1 && Session_CCT_View.data.questions.meta.mode == 'correct' ) {
				jQuery(".question-"+id+" .error").show();
			} else {
				SCCT_Module_Questions.skip( element );
			}
		} );
	},
	
	skip: function( element ) {
		var id = jQuery(element).closest('.question-dialog').data('id');
		console.log(SCCT_Module_Questions.questions+", "+id);
		console.log(SCCT_Module_Questions.questions[id]);
		SCCT_Module_Media.media.removeTrackEvent( SCCT_Module_Questions.questions[id] );
	},
	
	shuffle: function( array ) {
		for ( var j, x, i = array.length; i; j = parseInt(Math.random() * i), x = array[--i], array[i] = array[j], array[j] = x );
		return array;
	},
}

document.addEventListener( "DOMContentLoaded", SCCT_Module_Questions.onContentLoad, false );