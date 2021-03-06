// PLUGIN: Pulse
(function ( Popcorn ) {
  /**
   * Question popcorn plug-in
   *
   * Based on the built-in footnote plugin.
   * 
   * Example:
   *  var p = Popcorn('#video')
   *    .question({
   *      start: 5, // seconds
   *      end: 15, // seconds
   *      text: 'This video made exclusively for drumbeat.org',
   *      target: 'footnotediv'
   *    });
   **/

	Popcorn.plugin( "question", {
		manifest: {
			about: {
				name: "Popcorn Question Plugin",
				version: "1.0",
				author: "@ardnived",
				website: ""
			},
			options: {
				start: {
					elem: "input",
					type: "number",
					label: "Start"
				},
				end: {
					elem: "input",
					type: "number",
					label: "End"
				},
				text: {
					elem: "input",
					type: "text",
					label: "Text"
				},
				sort: {
					elem: "input",
					type: "checkbox",
					label: "Sort"
				},
				target: "footnote-container"
			}
		},
		
		_setup: function( options ) {
			var target = Popcorn.dom.find( options.target );
			
			options._container = document.createElement( "div" );
			options._container.dataset.start = options.start;
			options._container.style.display = "none";
			options._container.className = "dialog-wrapper";
			options._container.innerHTML = options.text;
			
			//SCCT_Module_Questions.questions[options.id] = options._id;
			
			if ( options.sort ) {
				var children = jQuery(target).children();
				children.each( function() {
					if ( this.dataset.start < options.start ) {
						target.insertBefore( options._container, this );
						return false;
					}
					return true;
				} );
			} else {
				target.insertBefore( options._container, target.firstChild );
			}
		},
		
		/**
		 * @member footnote
		 * The start function will be executed when the currentTime
		 * of the video  reaches the start time provided by the
		 * options variable
		 */
		start: function( event, options ) {
			jQuery(options._container).fadeIn();
			jQuery(options._container).addClass('visible');
			SCCT_Module_Questions.questions[options.id] = options._id;
			
			SCCT_Module_Media.pauseForModule();
		},
		
		/**
		 * @member footnote
		 * The end function will be executed when the currentTime
		 * of the video  reaches the end time provided by the
		 * options variable
		 */
		end: function( event, options ) {
			jQuery(options._container).fadeOut();
			jQuery(options._container).removeClass('visible');
			
			if ( jQuery('.dialog-wrapper.visible').length < 1 ) {
				SCCT_Module_Media.playForModule();
			}
		},
		
		_teardown: function( options ) {
			var target = Popcorn.dom.find( options.target );
			if ( target ) {
				target.removeChild( options._container );
			}
		}
	} );
} )( Popcorn );
