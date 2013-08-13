<?php

class Session_CCT_Settings {
	static $options = array();
	
	public static function init() {
		if ( ! function_exists( 'is_plugin_active' ) ):
			// Include plugins.php to check for other plugins from the frontend
			include_once( ABSPATH.'wp-admin/includes/plugin.php' );
		endif;
		
		self::$options['PULSE_CPT'] = defined( 'PULSE_CPT_VERSION' );
		
		add_action( 'admin_init', array( __CLASS__, 'load' ) );
		add_action( 'admin_menu', array( __CLASS__, 'admin_menu' ) );
	}
	
	public static function admin_menu() {
		$page = add_submenu_page( 'edit.php?post_type=session-cct', 'Settings', 'Settings', 'manage_options', 'session-cct_settings', array( __CLASS__, 'admin_page' ) );
	}
	
	public static function load() {
		//register settings
		//these need to be in admin_init otherwise Settings API doesn't work
		register_setting( 'session_options', 'session_example' );
		
		// Main settings
		add_settings_section( 'session_settings_main', 'Session CCT Settings', array( __CLASS__, 'setting_section_main' ), 'session-cct_settings' );
		add_settings_field( 'session_example', 'Example Setting', array( __CLASS__, 'setting_example' ), 'session-cct_settings', 'session_settings_main' );
		
		// Plugin integration
		add_settings_section( 'session_settings_plugins', 'Plugin Integration Status', array( __CLASS__, 'setting_section_plugins' ), 'session-cct_settings' );
		add_settings_field( 'pulse_cpt_found', 'Pulse CPT plugin', array( __CLASS__, 'setting_pulse_plugin' ), 'session-cct_settings', 'session_settings_plugins' );
	}
	
	public static function setting_section_main() {
		?>
		Main Settings
		<?php
	}
	
	public static function setting_section_plugins() {
		?>
		Integration for the Pulse CPT plugin
		<?php
	}
	
	public static function setting_example() {
		?>
		<input id="session_example" name="session_example" value="" type="text" />
		<?php
	}
	
	public static function setting_pulse_plugin() {
		?>
		<?php if ( self::$options['PULSE_CPT'] == true ): ?>
			<div style="color: green">Enabled</div>
		<?php else: ?>
			<div style="color: red">Not Found</div>
		<?php endif;
	}
	
	public static function admin_page() {
		?>
		<form id="session_options" method="post" action="options.php">
			<?php
				do_settings_sections('session-cct_settings');
				settings_fields('session_options');
			?>
			<br />
			<input type="submit" class="button-primary" value="Save Changes" />
		</form>
		<?php
	}
}

Session_CCT_Settings::init();