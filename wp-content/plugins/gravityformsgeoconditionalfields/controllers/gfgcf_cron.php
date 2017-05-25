<?php

class gfgcf_cron_controller extends gfgcf_controller_base {

	function __construct() {
		parent::__construct();

		add_action("cron_gfgcf_update_geo_file", array($this, "cron_gfgcf_update_geo_file"), 10);
	}

	/**
	 * Force will be set when the plugin is first activated.
	 *
	 * @param bool $force
	 */
	function cron_gfgcf_update_geo_file($force=false) {
		$this->library("gfgcf_update_library");

		if(!geo_fields_is_multisite() || (geo_fields_is_multisite() && get_current_blog_id() == 1)) {
			// this will cause an update to occur to the MaxMind database if necessary
			// updates will only occur on the first wednesday of the month or any day after that if the database hasn't been downloaded.
			$this->gfgcf_update_library->get_update($force);
		}

		if($force) {
			// remove the force attribute from the cron call.
			@wp_clear_scheduled_hook('cron_gfgcf_update_geo_file', array(true));
			@wp_clear_scheduled_hook('cron_gfgcf_update_geo_file');
			wp_schedule_event(time()+(60*60*24), 'daily', 'cron_gfgcf_update_geo_file');
		}
	}

}