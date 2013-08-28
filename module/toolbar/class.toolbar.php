<?php
class SCCT_Module_Toolbar extends Session_CCT_Module {
	
	function __construct() {
		parent::__construct( array(
			'name'      => "Toolbar",
			'has_admin' => false,
			'icon'      => false,
			'order'     => 0,
		) );
		
    	wp_register_style( 'scct-view-toolbar', SESSION_CCT_DIR_URL.'/module/toolbar/view-toolbar.css' );
	}
	
	public function view() {
		wp_enqueue_style( 'scct-view-toolbar' );
		?>
		<ul id="scct-toolbar" class="hidden-mobile">
			<?php
				foreach ( Session_CCT_Module::get_modules() as $module ) {
					if ( ! empty( $module->atts['icon'] ) ) {
						$this->view_icon( $module );
					}
				}
			?>
			<li>
				<a class="tool" href="<?php echo home_url(); ?>">
					<?php Session_CCT_Module::module_icon( SESSION_CCT_DIR_URL.'/img/exit.svg' ); ?>
				</a>
			</li>
		</ul>
		<?php
	}
	
	private function view_icon( $module ) {
		?>
		<li>
			<a class="tool selected" onclick="Session_CCT_View.toggleModuleDisplay( '<?php echo $module->atts['slug']; ?>', this )">
				<?php $module->icon(); ?>
			</a>
		</li>
		<?php
	}
}

new SCCT_Module_Toolbar();