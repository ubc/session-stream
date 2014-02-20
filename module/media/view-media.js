var SCCT_Media = {
	media: null,
	was_playing: null,
	duration: null,
	playing: false,
	
	onContentLoad: function() {
		
		SCCT_Media.media = Popcorn.smart( '#scct-media', Session_CCT_View.data.media.url );
		SCCT_Media.media.autoplay( false );
		
		
		SCCT_Media.media.on('loadeddata', function(){
			SCCT_Media.duration = SCCT_Media.media.duration()
			SCCT_Timeline.set( 'duration' ,  SCCT_Media.duration );
		});
		// this is here just in case
		SCCT_Media.media.on("play", function() {
    		SCCT_Media.playing = true;
    		console.log("Playing!");
    		SCCT_Play_Media_Button.html('<span class="genericon genericon-pause"></span>');
    		SCCT_Media.media.on( "timeupdate", SCCT_Media.update_time );

   		});
   		SCCT_Media.media.on("pause", function() {
    		SCCT_Media.playing = false;
    		console.log("paused");
    		SCCT_Play_Media_Button.html('<span class="genericon genericon-play"></span>');
    		SCCT_Media.media.off("timeupdate");

   		});

   		jQuery("#fullscreen").toggle( function() {
   			var element = jQuery('body').get(0);
			if(element.requestFullscreen) {
				element.requestFullscreen();
			} else if(element.mozRequestFullScreen) {
				element.mozRequestFullScreen();
			} else if(element.webkitRequestFullscreen) {
				element.webkitRequestFullscreen();
			} else if(element.msRequestFullscreen) {
				element.msRequestFullscreen();
			}
   		}, function() {
			if(document.exitFullscreen) {
				document.exitFullscreen();
			} else if(document.mozCancelFullScreen) {
				document.mozCancelFullScreen();
			} else if(document.webkitExitFullscreen) {
				document.webkitExitFullscreen();
			}
   		})
	},
	launchFullscreen: function( element ) {
	  
	},
	
	skipTo: function( time ) {
		
		SCCT_Media.media.currentTime( time );
		SCCT_Media.media.pause( time );
		var px = SCCT_Timeline.time_to_pixel( time );
		SCCT_Timeline.set( 'freeze_left', px + SCCT_Timeline.margin );
		SCCT_Timeline.set( 'freeze_width', px + SCCT_Timeline.timeline_time_width );
		SCCT_Timeline.set( 'freeze_time',  time  );
		SCCT_Timeline.set( 'time',  time );

	},
	update_time: function(){
		
    		var time = this.currentTime();
   			var px = SCCT_Timeline.time_to_pixel( time );
			SCCT_Timeline.set( 'freeze_left', px + SCCT_Timeline.margin );
			SCCT_Timeline.set( 'freeze_width', px + SCCT_Timeline.timeline_time_width );
			SCCT_Timeline.set( 'freeze_time',  time );
			SCCT_Timeline.set( 'time',  time );
	},
	pauseForModule: function() {
		if ( SCCT_Media.was_playing == null ) {
			SCCT_Media.was_playing = ! SCCT_Media.media.paused();
		}
		
		if ( SCCT_Media.was_playing ) {
			SCCT_Media.media.pause();
		}
	},
	
	playForModule: function() {
		if ( SCCT_Media.was_playing ) {
			SCCT_Media.media.play();
		}
		
		SCCT_Media.was_playing = null;
	}
}

document.addEventListener( "DOMContentLoaded", SCCT_Media.onContentLoad, false );

var SCCT_Timeline = {

	timeline_shell: null,
	time: null,	
	duration: null,
	timeline_time_el: null,
	timeline_fill: null,
	timeline_el: null,
	margin: -60,
	freeze_time: 0,
	freeze_left:60,
	freeze_width:60,
	timeline_time_width: 0,

	get: function( name ){
		return this[name];

	},
	set: function( name, value){
		// emit update event
		this[name] = value;

		if( typeof SCCT_Timeline['update_'+name] != 'undefined' ){
			SCCT_Timeline['update_'+name]( value );
		}
		
		
	},

	update_duration: function( time ){
		// do this when we know what time the whole thing is...
		SCCT_Timeline.init();
		jQuery("#total-time").text( SCCT_Timeline.seconds_to_time( time ) );
	},
	update_time: function ( time ){
		SCCT_Timeline.timeline_time_el.text( SCCT_Timeline.seconds_to_time( time ) );
	},
	update_freeze_left: function( px ){
		SCCT_Timeline.timeline_fill.width( px+'px');
	},
	update_freeze_width: function( px ){
		SCCT_Timeline.timeline_time_el.css('left', px +'px');
	},
	update_freeze_time: function( time ){
		
	},
	init: function( event ){
		// console.log
		jQuery("#timeline").slideDown();
		SCCT_Timeline.timeline_time_el = jQuery("#timeline-time");
		SCCT_Timeline.timeline_el 	  = jQuery("#timeline-shell"); 
		SCCT_Timeline.timeline_width   = SCCT_Timeline.timeline_el.width();
		SCCT_Timeline.timeline_fill	  = jQuery("#timeline-fill");
		SCCT_Timeline.timeline_shell = jQuery("#timeline");
		SCCT_Play_Media_Button = jQuery("#play-media");
		SCCT_Timeline.comments_on_timeline();
		SCCT_Timeline.slides_on_timeline();
		SCCT_Play_Media_Button.click( function(){

			if( SCCT_Media.playing ){
				SCCT_Media.media.pause(SCCT_Timeline.time);
				
			} else {
				SCCT_Media.media.play(SCCT_Timeline.time);
				
			}

		});

		SCCT_Timeline.timeline_el.hover( 
			function( event ) {
			
				SCCT_Timeline.timeline_time_el.css('left', event.clientX + SCCT_Timeline.timeline_time_width );
				// SCCT_Timeline.timeline_fill.width( (event.clientX + SCCT_Timeline.margin )+'px');
				var time = SCCT_Timeline.pixel_to_time( event.clientX );  
			
				SCCT_Timeline.set( 'time',  time );

				// if the event is playing
				// take over the time line but continou playing
				if( SCCT_Media.playing )
					SCCT_Media.media.off( "timeupdate" );


		}, function( event ) {

			// SCCT_Timeline.timeline_fill.width( SCCT_Timeline.freeze_left );
			SCCT_Timeline.timeline_time_el.css('left',SCCT_Timeline.freeze_width ).text( SCCT_Timeline.seconds_to_time( SCCT_Timeline.freeze_time ) );
			
			if( SCCT_Media.playing )
				SCCT_Media.media.on( "timeupdate", SCCT_Media.update_time );

		}).mousemove(function( event ) {

			var time = SCCT_Timeline.pixel_to_time( event.clientX );

			// SCCT_Timeline.timeline_fill.width( (event.clientX + SCCT_Timeline.margin )+'px');
			SCCT_Timeline.set( 'time',  time );

			SCCT_Timeline.timeline_time_el.css('left', event.clientX + SCCT_Timeline.timeline_time_width );

		}).click( function( event ){
			// pause the thing first
			SCCT_Media.skipTo( SCCT_Timeline.time );

		});

	},
	time_to_seconds: function( time ) {
		
		var split_time = time.split(":");
		
		var num_times = split_time.length; 
		var seconds = 0;
		var count = 0;
		var exponent = num_times;
		
		while( exponent ){
			
			exponent--;
			seconds += parseInt( split_time[ count ]*Math.pow( 60, exponent ) );
			count++;
			
		}
		return seconds;
		
	},

	seconds_to_time: function( sec ) {
		var totalSec = parseInt( sec );
		var hours 	= parseInt( totalSec / 3600 ) % 24;
		var minutes = parseInt( totalSec / 60 ) % 60;
		var seconds = totalSec % 60;
		
		total_hours = '';
		if( hours )
			total_hours = (hours < 10 ? "0" + hours : hours) + ":";
		return total_hours + (minutes < 10 ? "0" + minutes : minutes) + ":" + (seconds  < 10 ? "0" + seconds : seconds);
		
	},

	comments_on_timeline: function( ){

		var comments = jQuery(".comment-shell").each(function(num, el){
			var elm =  jQuery(el);
			var time = elm.data( 'time' );
			
			var seconds =  SCCT_Timeline.time_to_seconds( time );
			
			elm.css('left', SCCT_Timeline.time_to_pixel( seconds )+"px" ).show();
			

		} );

	},
	slides_on_timeline: function(){

		var list = Session_CCT_View.data.slideshow.list;
		
		var size_of_list = list.length;
		var count = 0;
		var slides = '';
		while( count < size_of_list ){
			var item = list[count];
			count++;
			var left = SCCT_Timeline.time_to_pixel( item.start )+"px";
			slides += "<div class='slide-shell' style='left:"+left+"' ><div class='slide-icon' data-time='"+item.start+"' ><span class='genericon genericon-picture'></span></div></div>";
		}

		SCCT_Timeline.timeline_shell.append( slides );

		jQuery('.slide-icon').click( function() {
				var time = jQuery(this).data( "time" );
				
				SCCT_Timeline.set( 'time',  time );
				SCCT_Media.skipTo( time );
		} );

	}, 
	time_to_pixel: function( time ) {
		// convert time to to seconds
		// 
		var px = parseInt( ( time / SCCT_Timeline.duration ) * SCCT_Timeline.timeline_width  ) - SCCT_Timeline.margin;
		return px;
	},
	pixel_to_time: function( px ) { 
		// algebra rocks!
		var time = parseInt( ( ( px + SCCT_Timeline.margin ) / SCCT_Timeline.timeline_width ) * SCCT_Timeline.duration ); 
		return time;
	},
}