// PLUGIN: Pulse
(function ( Popcorn ) {
  /**
   * Pulse popcorn plug-in
   *
   * Based on the built-in footnote plugin.
   *
   * Example:
   *  var p = Popcorn('#video')
   *    .pulse({
   *      start: 5, // seconds
   *      end: 15, // seconds
   *      text: 'This video made exclusively for drumbeat.org',
   *      sort: true,
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
			options._container.innerHTML = options.text;
			
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
				target.appendChild( options._container );
			}
			
			/** For reversed display order.
			if ( options.sort ) {
				var children = jQuery(target).children();
				var success = false;
				children.each( function() {
					if ( this.dataset.start >= options.start ) {
						target.insertBefore( options._container, this );
						success = true;
					}
					return ! success;
				} );
				
				if ( ! success ) {
					target.appendChild( options._container );
				}
			} else {
				target.insertBefore( options._container, target.firstChild );
			}
			*/
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
