var SCCT_Module_Questions = {
	questions: {},
	
	onContentLoad: function() {
		SCCT_Media.media.on( 'loadedmetadata', SCCT_Module_Questions.loadQuestions );
		Session_CCT_View.data.questions.template = doT.template( Session_CCT_View.data.questions.template );
		
		jQuery('.questions-wrapper').on( 'change.answer', ".unanswered input:radio[name='answer']:checked", SCCT_Module_Questions.onChange );
	},
	
	loadQuestions: function() {
		var list = Session_CCT_View.data.questions.list;
		
		for ( index in list ) {
			SCCT_Module_Questions.addQuestion( list[index] );
		}
	},
	
	addQuestion: function( data ) {
		if ( Session_CCT_View.data.questions.meta.random ) {
			data.answers = SCCT_Module_Questions.shuffle( data.answers );
		}
		
		var new_question = Session_CCT_View.data.questions.template( data );
		
		SCCT_Media.media.question( {
			id: data.index,
			start: data.synctime,
			end: SCCT_Media.media.duration(),
			text: new_question,
			target: "scct-questions",
		} );
	},
	
	onChange: function() {
		var element = jQuery(this);
		var dialog = element.closest('.question-dialog');
		var id = dialog.data('id');
		var answer = element.closest('.answer');
		var value = element.val();
		
		answer.siblings('.selected').removeClass('selected');
		answer.addClass('selected');
		
		jQuery.post( Session_CCT_View.data.ajaxurl, {
			action: 'scct_answer',
			session_id: Session_CCT_View.data.session_id,
			question: id,
			answer: value,
		}, function( response ) {
			if ( response == 1 ) {
				answer.addClass('correct');
				dialog.removeClass('unanswered');
			} else {
				answer.addClass('wrong');
			}
			
			dialog.find('.explanation').hide();
			dialog.find('.explanation-'+value).fadeIn();
			
			if ( Session_CCT_View.data.questions.meta.mode == 'any' || response == 1 ) {
				dialog.find('.cancel').hide();
				dialog.find('.skip').hide();
				dialog.find('.submit').fadeIn();
			}
		} );
	},
	
	cancel: function( element ) {
		var start = jQuery(element).closest('.dialog-wrapper').data('start');
		SCCT_Media.skipTo( start - 1 );
	},
	
	skip: function( element ) {
		var id = jQuery(element).closest('.question-dialog').data('id');
		SCCT_Media.media.removeTrackEvent( SCCT_Module_Questions.questions[id] );
	},
	
	shuffle: function( array ) {
		for ( var j, x, i = array.length; i; j = parseInt(Math.random() * i), x = array[--i], array[i] = array[j], array[j] = x );
		return array;
	},
}

document.addEventListener( "DOMContentLoaded", SCCT_Module_Questions.onContentLoad, false );