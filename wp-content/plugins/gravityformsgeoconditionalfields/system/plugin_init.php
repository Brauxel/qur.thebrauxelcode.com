<?php


if(!function_exists("gfgcf_load_init")) {
	function gfgcf_load_init($deprecated) {
		//$plugins_dir = ABSPATH . "wp-content/plugins/";
		$plugin_dir = plugin_dir_path(dirname(__FILE__));
		$basename = basename($plugin_dir);

		$GLOBALS["gfgcf"] = (!empty($GLOBALS["gfgcf"]) ? $GLOBALS["gfgcf"] : array());
		$GLOBALS["gfgcf"][$basename] = array();

		$init_file = $plugin_dir . "/init.php";
		if(file_exists($init_file)) {
			include $init_file;
		}

		// Dispatch plugin
		gfgcf_load_runtime($deprecated);
	}
}

if(!function_exists("gfgcf_load_runtime")) {

	function gfgcf_load_runtime($deprecated) {
		//$plugins_dir = ABSPATH . "wp-content/plugins/";
		$plugin_dir = plugin_dir_path(dirname(__FILE__));

		// Load generic classes into memory
		$system_dir = $plugin_dir . "/system";

		if(file_exists($system_dir . "/loader_base.php")) include $system_dir . "/loader_base.php";
		if(file_exists($system_dir . "/controller_base.php")) include $system_dir . "/controller_base.php";
		if(file_exists($system_dir . "/model_base.php")) include $system_dir . "/model_base.php";
		if(file_exists($system_dir . "/library_base.php")) include $system_dir . "/library_base.php";

		// Load controllers for this runtime
		gfgcf_load_controllers($deprecated);
	}
}

if(!function_exists("gfgcf_load_controllers")) {
	/**
	 * Get a list of the available controllers to load for this plugin.
	 */
	function gfgcf_load_controllers($deprecated) {
		//$plugins_dir = ABSPATH . "wp-content/plugins/";
		$plugin_dir = plugin_dir_path(dirname(__FILE__));

		$controller_dir = $plugin_dir . "/controllers";

		$GLOBALS["gfgcf"] = array();
		$GLOBALS["gfgcf"]["controllers"] = array();

		// Load applicable controllers
		$target_dir = $controller_dir;

		if(file_exists($target_dir)) {
			$files = gfgcf_get_files_in_folder($target_dir);

			foreach($files as $file) {
				$path_info = pathinfo($file);
				if(strtolower($path_info["extension"]) == "php") {
					include $file;
					$controller = $path_info["filename"];
					$controller_class = $controller . "_controller";
					$GLOBALS["gfgcf"]["controllers"][$controller] = new $controller_class();
				}
			}
		}

		// initialise based on priority
		uasort($GLOBALS["gfgcf"]["controllers"], "gfgcf_controller_priority");

		// Load all the standard hooks needed for a controller.
		foreach($GLOBALS["gfgcf"]["controllers"] as $controller=>$inst) {
			$GLOBALS["gfgcf"]["controllers"][$controller]->init_hooks();
		}

	}

	function gfgcf_get_files_in_folder($dir) {
		$file_list = array();

		$files = scandir($dir);
		foreach($files as $file) {
			if(!in_array($file, array(".",".."))) {
				if(is_dir($dir . "/" . $file)) {
					$file_list = array_merge($file_list, gfgcf_get_files_in_folder($dir . "/" . $file));
				} else {
					$file_list[] = $dir . "/" . $file;
				}
			}
		}

		return $file_list;

	}

	function gfgcf_controller_priority($a, $b) {
		if ($a->get_load_priority() == $b->get_load_priority()) {
			return 0;
		}
		return ($a->get_load_priority() < $b->get_load_priority()) ? -1 : 1;
	}
}