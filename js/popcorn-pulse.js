
(function (Popcorn) {  
  Popcorn.plugin( "pulse" , {
    _setup : function( options ) {
		// setup code, fire on initialization
		// options refers to the options passed into the plugin on init
		// this refers to the popcorn object
    },
    start: function( event, options ){
		// fire on options.start
		// event refers to the event object
		// options refers to the options passed into the plugin on init
		// this refers to the popcorn object
		console.log(" --- Add --- ");
		console.log(event);
		console.log(options);
		console.log(" ------ ");
    },
    end: function( event, options ){
		// fire on options.end
		// event refers to the event object
		// options refers to the options passed into the plugin on init
		// this refers to the popcorn object
		console.log(" --- Remove --- ");
		console.log(event);
		console.log(options);
		console.log(" ------ ");
    }
  });
})(Popcorn);