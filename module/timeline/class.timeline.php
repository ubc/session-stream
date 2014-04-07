<?php
class SCCT_Module_Timeline extends Session_CCT_Module {
	
	function __construct() {
		parent::__construct( array(
			'name'     => "Timeline",
			'slug'     => "timeline",
			'priority' => "high",
			'order'    => 2,
		) );
		
    	wp_register_style(  'scct-view-timeline', SESSION_CCT_DIR_URL.'/module/timeline/view-timeline.css' );
        wp_register_script( 'scct-view-timeline', SESSION_CCT_DIR_URL.'/module/timeline/view-timeline.js', array( 'jquery', 'backbone', 'scct-view' ), '1.0', true );
    
    }
	
	public function load_view() {
		add_filter( 'scct_localize_view', array( $this, 'localize_view' ) );
		
		wp_enqueue_style(  'scct-view-timeline' );
		wp_enqueue_script( 'scct-view-timeline' );
	}
	
	public function view() {
		?>
        <a onclick="SCCT_Module_Timeline.playPause()"><div id="timeline-media-control">
            <img src=" <?php echo SESSION_CCT_DIR_URL.'/img/play.svg'; ?>" id="timeline-media-control-play">
            <img src=" <?php echo SESSION_CCT_DIR_URL.'/img/pause.svg'; ?>" id="timeline-media-control-pause" class="hidden">
        </div></a>
        <div id="timeline-volume-wrapper">
            <div id="timeline-volume-padding"></div>
            <div id="timeline-volume-control">
                <div id="timeline-volume-bar"></div>
            </div>
            <div id="timeline-volume-label">
                <img src=" <?php echo SESSION_CCT_DIR_URL.'/img/volume.svg'; ?>" id="timeline-media-volume">
            </div>
        </div>
        <div id="timeline-media-padding">
        </div>
        <div id="timeline-content-wrapper">
            <div id="timeline-content">
                <div id="timeline-progress-wrapper">
                    <div id="timeline-progress-bar">
                    </div>
                </div>
                <div id="timeline-chapter-list"></div>
                <div id="timeline-line"></div>
                <div id="timeline-comment-list"></div>
            </div>
        </div>
		<?php
	}
	
	function localize_view( $data ) {
		$timeline = $this->data();
		
		if ( ! empty( $timeline['list'] ) ) {
			foreach ( $timeline['list'] as $index => $slide ) {
				error_log('test');
				if ( ! empty( $slide['content'] ) ) {
					error_log("do shortcode on ".$slide['content']);
					$slide['content'] = do_shortcode( $slide['content'] );
				} else {
					error_log("empty ".$slide['content']);
				}
				
				$slide['start'] = Session_CCT_View::string_to_seconds( $slide['start'] );
				$timeline['list'][$index] = $slide;
			}
		}
		
		$data['timeline'] = $timeline;
		return $data;
	}
	
}

new SCCT_Module_timeline();