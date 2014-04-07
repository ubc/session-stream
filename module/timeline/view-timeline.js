var SCCT_Module_Timeline = {

    volume: 1.0,
    volumeBar: jQuery("#timeline-volume-bar"),
    mediaControlPlay: jQuery('#timeline-media-control-play'),
    mediaControlPause: jQuery('#timeline-media-control-pause'),
    progressBar: jQuery("#timeline-progress-bar"),
    content: jQuery("#timeline-content"),
    volumeControl: jQuery("#timeline-volume-control"),
    volumeLabel: jQuery("#timeline-volume-label"),
    volumePadding: jQuery("#timeline-volume-padding"),
    mediaPadding: jQuery("#timeline-media-padding"),
    commentList: jQuery("#timeline-comment-list"),
    chapterList: jQuery("#timeline-chapter-list"),
    mediaWrapper: jQuery("scct-media1396480367495"),
    chapterListSlides: Session_CCT_View.data.slideshow.list,
    
	onContentLoad: function() {
		SCCT_Module_Media.media.on( 'loadedmetadata', SCCT_Module_Timeline.loadSlides );
        SCCT_Module_Media.media.on( 'loadedmetadata', SCCT_Module_Timeline.clickDetect );
	},
    
    playPause: function() {
        if( SCCT_Module_Media.media.paused() == true ) {
            SCCT_Module_Media.media.play();
            SCCT_Module_Media.media.on( 'timeupdate', SCCT_Module_Timeline.updateProgress );
            SCCT_Module_Media.media.on( 'timeupdate', SCCT_Module_Timeline.updatePlayPause );
        } else {
            SCCT_Module_Media.media.pause();
        }
    },
    
    updateProgress: function() {
        var progress = ( SCCT_Module_Media.media.currentTime() / SCCT_Module_Media.media.duration() ) * 100;
        SCCT_Module_Timeline.progressBar.css("width", progress + "%");
        if( progress == 100 ) {
            SCCT_Module_Timeline.mediaControlPlay.addClass('hidden');
            SCCT_Module_Timeline.mediaControlPause.removeClass('hidden');
        }
    },
    
    updatePlayPause: function() {
        if( SCCT_Module_Media.media.paused() == true ) {
            SCCT_Module_Timeline.mediaControlPlay.removeClass('hidden');
            SCCT_Module_Timeline.mediaControlPause.addClass('hidden');
        } else {
            SCCT_Module_Timeline.mediaControlPlay.addClass('hidden');
            SCCT_Module_Timeline.mediaControlPause.removeClass('hidden');
        }
    },
    
    updateVolumeBar: function (currentVolume) {
        SCCT_Module_Timeline.volume = currentVolume;
        var volume =  currentVolume * 100;
        SCCT_Module_Timeline.volumeBar.css("height", volume + "%");
    },
    
    muteUnmute: function() {
        var tempVolume = SCCT_Module_Timeline.volume;
        if(SCCT_Module_Media.media.volume() == 0) {
            SCCT_Module_Media.media.volume(SCCT_Module_Timeline.volume);
            SCCT_Module_Timeline.updateVolumeBar(SCCT_Module_Timeline.volume);
        } else {
            SCCT_Module_Timeline.volume = SCCT_Module_Media.media.volume();
            SCCT_Module_Media.media.volume(0);
            SCCT_Module_Timeline.updateVolumeBar(0);
            SCCT_Module_Timeline.volume = tempVolume;
        }
    },
    
    clickDetect: function(e) {
        SCCT_Module_Timeline.playPause();
        jQuery.each(SCCT_Module_Timeline.chapterListSlides, function(index, v) {
            var slide = SCCT_Module_Timeline.chapterListSlides[index];
            jQuery("#timeline-slide-" + index).on('click', function(e) {
                SCCT_Module_Media.skipTo( slide.start );
                SCCT_Module_Timeline.updateProgress();
                return false;
            });
        });
        SCCT_Module_Timeline.content.click(function(e) {
            var position = e.pageX - SCCT_Module_Timeline.content.offset().left;
            var width = SCCT_Module_Timeline.content.width();
            var percent = position / width;
            SCCT_Module_Media.skipTo( SCCT_Module_Media.media.duration() * percent );
            SCCT_Module_Timeline.updateProgress();
        });
        SCCT_Module_Timeline.volumeControl.click(function(e) {
            var position = e.pageY - SCCT_Module_Timeline.volumeControl.offset().top;
            var height = SCCT_Module_Timeline.volumeControl.height();
            var percent = 1 - position / height;
            SCCT_Module_Media.media.volume(percent);
            SCCT_Module_Timeline.updateVolumeBar(percent);
        });
        SCCT_Module_Timeline.volumeLabel.click(function(e) {
            SCCT_Module_Timeline.muteUnmute();
        });
        SCCT_Module_Timeline.volumePadding.click(function(e) {
            SCCT_Module_Media.media.volume(1);
            SCCT_Module_Timeline.updateVolumeBar(1);
        });
        SCCT_Module_Timeline.mediaPadding.click(function(e) {
            SCCT_Module_Media.skipTo( 0 );
            SCCT_Module_Timeline.updateProgress();
        });
        SCCT_Module_Timeline.mediaWrapper.click(function(e) {
            alert("hello");
            SCCT_Module_Timeline.mediaControlPlay.toggleClass('hidden');
            SCCT_Module_Timeline.mediaControlPause.toggleClass('hidden');
        });
        
    },
    
	loadSlides: function() {
        var comment_list = Session_CCT_View.data.pulse;
        var media_duration = SCCT_Module_Media.media.duration();
        var comment_array = new Array(100);
        var comment_percent;
        var comment_percent_max;
        
        SCCT_Module_Timeline.volumeBar.css("height", SCCT_Module_Timeline.volume * 100 + "%");
        
        for ( var index = 0; index < comment_array.length; index++ ) {
            comment_array[index] = 0;
        }
        
        for ( var index = 0; index < comment_list.length; index++ ) {
            comment_percent = Math.floor( SCCT_Module_Timeline.hmsToSecondsOnly( comment_list[index].date ) / media_duration * comment_array.length );
            if( comment_percent <= comment_array.length ) {
                comment_array[comment_percent]++;
            }
        }
        
        comment_percent_max = Math.max.apply(Math, comment_array);
    
        for ( var index = 0; index < comment_array.length; index++ ) {
        
            var comment_height = 16 * comment_array[index] / comment_percent_max;
        
            if( comment_height > 0 ) {
                if( comment_height < 5 ) {
                    comment_height = 5;
                }
                
                SCCT_Module_Timeline.commentList.append('<div class="timeline-comment" style="height:' + comment_height + 'px; left:' + index + '%"></div>');
            }
        }
        
		for ( var index = 0; index < SCCT_Module_Timeline.chapterListSlides.length; index++ ) {
			var slide = SCCT_Module_Timeline.chapterListSlides[index];
			var next_slide = SCCT_Module_Timeline.chapterListSlides[index+1];
			var duration = parseInt( slide.duration );
			var text_color = '#002145';

            if( index % 3 == 0 ) {
                text_color = '#004345';
            }
            if( index % 3 == 1 ) {
                text_color = '#002178';
            }
            if( index % 3 == 2 ) {
                text_color = '#222145';
            }
			
			var end;
			if ( next_slide != undefined ) {
				end = next_slide.start;
			} else {
				end = SCCT_Module_Media.media.duration();
			}
            
            var slide_duration = ((end - slide.start ) / media_duration) * 100;
            
            SCCT_Module_Timeline.chapterList.append('<div class="timeline-slide" id="timeline-slide-' + index + '" style="background:' + text_color + '; left:' + ( slide.start / media_duration ) * 100 + '%"><div class="timeline-slide-title">' + (index + 1) + '</div></div>');
        }
	},
    
    hmsToSecondsOnly : function(str) {
        var p = str.split(':'),
            s = 0, m = 1;

        while (p.length > 0) {
            s += m * parseInt(p.pop(), 10);
            m *= 60;
        }

        return s;
    }
    
}

document.addEventListener( "DOMContentLoaded", SCCT_Module_Timeline.onContentLoad, false );