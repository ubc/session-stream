<?php
/*
Plugin Name: Session Stream
Plugin URI: 
Description: 
Author: Devindra Payment, CTLT
Version: 0.1
Author URI: http://ctlt.ubc.ca
*/

if ( ! defined('ABSPATH') )
	die('-1');

define( 'SESSION_CCT_DIR_PATH', plugin_dir_path( __FILE__ ) );
define( 'SESSION_CCT_BASENAME', plugin_basename( __FILE__ ) );
define( 'SESSION_CCT_DIR_URL',  plugins_url( '', SESSION_CCT_BASENAME ) );
define( 'SESSION_CCT_VERSION',  0.1 );

define( 'SESSION_CCT_SLUG', "session-cct" );

require_once( 'lib/class.session_cct.php' );
require_once( 'lib/class.session_cct_view.php' );
require_once( 'lib/class.session_cct_pulse.php' );
require_once( 'lib/class.session_cct_admin.php' );
require_once( 'lib/class.session_cct_settings.php' );

register_activation_hook( __FILE__, array( 'Session_CCT', 'install' ) );
