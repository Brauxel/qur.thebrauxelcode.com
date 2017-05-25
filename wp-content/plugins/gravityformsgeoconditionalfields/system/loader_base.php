<?php

if(!class_exists("gfgcf_loader_base")) {

	class gfgcf_loader_base {

		private $runtime;


		function __construct() {}

		/**
		 * getter to use loaded models that are stored in a global class
		 */
		function __get($name) {

			$GLOBALS["gfgcf"]["models"] = (empty($GLOBALS["gfgcf"]["models"]) ? array() : $GLOBALS["gfgcf"]["models"]);
			$GLOBALS["gfgcf"]["libraries"] = (empty($GLOBALS["gfgcf"]["libraries"]) ? array() : $GLOBALS["gfgcf"]["libraries"]);

			// retrieve the loaded model if found
			foreach($GLOBALS["gfgcf"]["models"] as $namespace=>$inst) {
				if($namespace == $name)
					return $GLOBALS["gfgcf"]["models"][$namespace];
			}

			// retrieve a loaded library if one is loaded
			foreach($GLOBALS["gfgcf"]["libraries"] as $namespace=>$inst) {
				if($namespace == $name)
					return $GLOBALS["gfgcf"]["libraries"][$namespace];
			}

			return null;

		}

		/**
		 * The generic classes may be used in multiple plugins, so we use reflection to get the called class to determine what plugin is loaded.
		 */
		function get_plugin_name() {
			$pathinfo = pathinfo(plugin_basename(dirname(__FILE__)));
			$plugin_name = $pathinfo["dirname"];
			return $plugin_name;
		}

		/**
		 * Load a model into memory.
		 * This will load a model into each loaded controller's isntance
		 */
		function model($model, $namespace="") {
			$plugin = $this->get_plugin_name();
			//$plugin_dir = ABSPATH . "/wp-content/plugins/" . $plugin;
			$plugin_dir = plugin_dir_path(dirname(__FILE__));

			// make sure we are referencing a php file
			$model_path = pathinfo($model);
			if(substr($model, strlen($model)-4) != ".php") $model .= ".php";
			if(empty($namespace)) $namespace = $model_path["filename"];
			$model_base = $model_path["filename"];


			// check to see if the model has already been loaded
			$GLOBALS["gfgcf"]["models"] = (empty($GLOBALS["gfgcf"]["models"]) ? array() : $GLOBALS["gfgcf"]["models"]);
			$GLOBALS["gfgcf"]["controllers"] = (empty($GLOBALS["gfgcf"]["controllers"]) ? array() : $GLOBALS["gfgcf"]["controllers"]);

			foreach($GLOBALS["gfgcf"]["models"] as $loaded_model=>$inst) {
				if($loaded_model == $namespace) return true;
			}

			if(file_exists($plugin_dir . "/models/" . $model)) {
				include_once $plugin_dir . "/models/" . $model;

				// store a new instance of the model
				$GLOBALS["gfgcf"]["models"][$namespace] = new $model_base();
				return $GLOBALS["gfgcf"]["models"][$namespace];
			} else {
				return false;
			}
		}

		/**
		 * Load a library into memory.
		 * This will load a model into each loaded controller's isntance
		 */
		function library($library, $namespace="") {
			$plugin = $this->get_plugin_name();
			//$plugin_dir = ABSPATH . "/wp-content/plugins/" . $plugin;
			$plugin_dir = plugin_dir_path(dirname(__FILE__));

			// make sure we are referencing a php file
			$library_path = pathinfo($library);
			if(substr($library, strlen($library)-4) != ".php") $library .= ".php";
			if(empty($namespace)) $namespace = $library_path["filename"];
			$library_base = $library_path["filename"];


			// check to see if the library has already been loaded
			$GLOBALS["gfgcf"]["libraries"] = (empty($GLOBALS["gfgcf"]["libraries"]) ? array() : $GLOBALS["gfgcf"]["libraries"]);

			foreach($GLOBALS["gfgcf"]["libraries"] as $loaded_library=>$inst) {
				if($loaded_library == $namespace) return true;
			}

			if(file_exists($plugin_dir . "/libraries/" . $library)) {
				include_once $plugin_dir . "/libraries/" . $library;

				// store a new instance of the model
				$GLOBALS["gfgcf"]["libraries"][$namespace] = new $library_base();
				return $GLOBALS["gfgcf"]["libraries"][$namespace];
			} else {
				return false;
			}

		}

		/**
		 * external
		 */
		function external($script) {
			$plugin = $this->get_plugin_name();
			//$plugin_dir = ABSPATH . "/wp-content/plugins/" . $plugin . "/3rdparty";
			$plugin_dir = plugin_dir_path(dirname(__FILE__)) . "/3rdparty";

			// make sure we are referencing a php file
			$script = rtrim($script, ".php");
			$script = $script . ".php";

			if(file_exists($plugin_dir . "/" . $script)) {
				require_once $plugin_dir . "/" . $script;
			} else {
				return false;
			}

		}

		function get_plugin_dir() {
			$plugin_name = $this->get_plugin_name();
			//$dir = ABSPATH . "wp-content/plugins/" . $plugin_name;
			$dir = plugin_dir_path(dirname(__FILE__));
			return $dir;
		}

		function get_plugin_url() {
			$plugin_name = $this->get_plugin_name();
			$url = plugins_url() . "/" . $plugin_name;
			return $url;
		}


		function get_runtime_var() {
			$args = func_get_args();

			$data_obj = $this->runtime;

			foreach($args as $arg) {
				if(isset($data_obj[$arg])) {
					$data_obj = $data_obj[$arg];
				} else {
					return false;
				}
			}

			return $data_obj;
		}

		function set_runtime_var() {
			$args = func_get_args();
			if(count($args) < 2) return false;

			$data = $args[0];

			array_splice($args, 0, 1);
			$path = $args;

			$data_obj = &$this->runtime;
			foreach($path as $p) {
				if(!isset($data_obj[$p]))
					$data_obj[$p] = array();

				$data_obj =& $data_obj[$p];
			}

			$data_obj = $data;

			return true;
		}

	}
}