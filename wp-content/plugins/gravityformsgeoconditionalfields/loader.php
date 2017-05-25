<?php
/*
Plugin Name: Gravity Forms Geo Conditional Fields
Description: A plugin that will add geographical conditional logic to your Gravity Forms experience.
Version: 2.0.0
Author: Nathan Franklin
Author URI: http://www.nathanfranklin.com.au/
Network: true
Text Domain: gf-geo-fields
Domain Path: /lang/
*/

// General setup... getting plugin ready to load.
defined( 'ABSPATH' ) OR exit;

load_textdomain('gf-geo-fields', dirname(__FILE__) . '/lang/gf-geo-fields-' . get_locale() . '.mo');

// include system file for loading plugin.
include  dirname(__FILE__) . '/system/plugin_init.php';

// Load moduled functions.
if(file_exists(dirname(__FILE__) . "/functions/") && is_dir(dirname(__FILE__) . "/functions/")) {
	$function_files = scandir(dirname(__FILE__) . "/functions/");
	foreach($function_files as $function_file) {
		if(substr($function_file, -4) == ".php") {
			require_once dirname(__FILE__) . "/functions/" . $function_file;
		}
	}
}

add_action( 'gform_loaded', array( 'GF_GeoConditional_Bootstrap', 'load' ), 5 );
class GF_GeoConditional_Bootstrap {
	public static function load() {
		require_once 'fields/gfgcf_common_field.php';
		require_once 'fields/gfgcf_geo_country_field.php';
		require_once 'fields/gfgcf_geo_continent_field.php';
	}
}

gfgcf_load_init(basename(dirname(__FILE__)));

register_activation_hook( __FILE__, 'gfgcf_activate');
register_deactivation_hook( __FILE__, 'gfgcf_deactivate');
register_uninstall_hook( __FILE__, 'gfgcf_uninstall' );

function gfgcf_activate() {
	wp_schedule_event(time(), 'daily', 'cron_gfgcf_update_geo_file', array(true));
}

function gfgcf_deactivate() {
	@wp_clear_scheduled_hook('cron_gfgcf_update_geo_file', array(true));
	@wp_clear_scheduled_hook('cron_gfgcf_update_geo_file');
}

function gfgcf_uninstall() {
	@wp_clear_scheduled_hook('cron_gfgcf_update_geo_file', array(true));
	@wp_clear_scheduled_hook('cron_gfgcf_update_geo_file');
}

