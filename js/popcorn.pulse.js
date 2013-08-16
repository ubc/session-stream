// PLUGIN: Pulse
(function ( Popcorn ) {
  /**
   * Pulse popcorn plug-in
   *
   * Based on the built-in footnote plugin for pulse.
   * Adds text to an element on the page.
   * Options parameter will need a start, end, target and text.
   * Start is the time that you want this plug-in to execute
   * End is the time that you want this plug-in to stop executing
   * Text is the text that you want to appear in the target
   * Target is the id of the document element that the text needs to be
   * attached to, this target element must exist on the DOM
   *
   * @param {Object} options
   *
   * Example:
   *  var p = Popcorn('#video')
   *    .pulse({
   *      start: 5, // seconds
   *      end: 15, // seconds
   *      text: 'This video made exclusively for drumbeat.org',
   *      target: 'footnotediv'
   *    });
   **/

	Popcorn.plugin( "pulse", {
		manifest: {
			about: {
				name: "Popcorn Pulse Plugin",
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
				target: "footnote-container"
			}
		},
		
		_setup: function( options ) {
			var target = Popcorn.dom.find( options.target );
			
			options._container = document.createElement( "div" );
			options._container.style.display = "none";
			options._container.innerHTML  = options.text;
			
			target.appendChild( options._container );
		},
		
		/**
		 * @member footnote
		 * The start function will be executed when the currentTime
		 * of the video  reaches the start time provided by the
		 * options variable
		 */
		start: function( event, options ){
			//options._container.style.display = "inline";
			jQuery(options._container).fadeIn();
			//console.log(options);
		},
		
		/**
		 * @member footnote
		 * The end function will be executed when the currentTime
		 * of the video  reaches the end time provided by the
		 * options variable
		 */
		end: function( event, options ){
			//options._container.style.display = "none";
			jQuery(options._container).fadeOut();
		},
		
		_teardown: function( options ) {
			var target = Popcorn.dom.find( options.target );
			if ( target ) {
				target.removeChild( options._container );
			}
		}
	} );
} )( Popcorn );
