<?php
class SCCT_Module_Toolbar extends Session_CCT_Module {
	
	function __construct() {
		parent::__construct( array(
			'name'      => "Toolbar",
			'has_admin' => false,
			'icon'      => false,
			'order'     => 0,
		) );
		
    	wp_register_script( 'twitter-bootstrap', '//netdna.bootstrapcdn.com/bootstrap/3.0.0/js/bootstrap.min.js',  array( 'jquery' ), '3.0' );
    	wp_register_script( 'scct-view-toolbar',  SESSION_CCT_DIR_URL.'/module/toolbar/view-toolbar.js',  array( 'jquery' ), '1.0', true );
    	wp_register_style( 'scct-view-toolbar', SESSION_CCT_DIR_URL.'/module/toolbar/view-toolbar.css' );
	}
	
	public function view() {
		wp_enqueue_script( 'twitter-bootstrap' );
		wp_enqueue_script( 'scct-view-toolbar' );
		wp_enqueue_style( 'scct-view-toolbar' );
		
		$exit_url = $_SERVER['HTTP_REFERER'];
		if ( empty( $exit_url ) ) {
			$exit_url = home_url();
		}
		
		?>
		<ul id="scct-toolbar" class="hidden-mobile">
			<?php
				foreach ( Session_CCT_Module::get_modules() as $module ) {
					if ( ! empty( $module->atts['icon'] ) && $module->atts['slug'] != 'timeline' ) {
						$this->view_icon( $module );
					}
				}
			?>
            <li>
                <a class="tool selected tool-reverse" data-toggle="tooltip" title="Reverse Order">
                    <?php Session_CCT_Module::module_icon( SESSION_CCT_DIR_URL.'/img/reverse.svg' ); ?>
                </
                a>
            </li>
            <li>
                <a class="tool selected tool-cycle" data-toggle="tooltip" title="Cycle Displays">
                    <?php Session_CCT_Module::module_icon( SESSION_CCT_DIR_URL.'/img/cycle.svg' ); ?>
                </a>
            </li>
			<li>
				<a class="tool selected" data-toggle="tooltip" title="Exit" href="<?php echo $exit_url; ?>">
					<?php Session_CCT_Module::module_icon( SESSION_CCT_DIR_URL.'/img/exit.svg' ); ?>
				</a>
			</li>
		</ul>
		<?php
	}
	
	private function view_icon( $module ) {
		?>
		<li>
			<a class="tool selected tool-<?php echo $module->atts['slug']; ?>" data-toggle="tooltip" title="Hide/Show <?php echo $module->atts['name']; ?>">
				<?php $module->icon(); ?>
			</a>
		</li>
		<?php
	}
}

new SCCT_Module_Toolbar();