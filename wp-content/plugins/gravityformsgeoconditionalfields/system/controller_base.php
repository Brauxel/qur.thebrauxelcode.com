<?php

if(!class_exists("gfgcf_controller_base")) {

	class gfgcf_controller_base extends gfgcf_loader_base {

		protected $default_load_priority = 2;

		/**
		 * Constructor
		 */
		function __construct() {
			$this->init();
		}

		/**
		 * Fired after __construct()
		 */
		function init() {}

		/**
		 * Function this is called after the class has been instanced.
		 */
		function init_hooks() {
			if(is_admin()) {
				// Call for setting up admin pages in plugin
				add_action('admin_menu', array($this, 'register_pages'), $this->get_load_priority());
				add_action("admin_init", array($this, "admin_init"), $this->get_load_priority());
			}

			add_action("init", array($this, "init_action"));

			$this->init_hooks_complete();
		}

		function init_action() {}

		function init_hooks_complete() { }

		/**
		 * Add a menu page for the admin.
		 * register_pages is called from admin_menu hook. register_pages shoudl then invoke the register_page function.
		 * register_page should not be called outside the register_pages function.
		 */
		function register_page($page_title, $menu_title, $cap, $icon, $position) {
			$controller = $this->get_controller_name();
			$screen_id = add_menu_page($page_title, $menu_title, $cap, $controller, array($this, "dispatch"), $icon, $position);
			return $screen_id;
		}

		/**
		 * Add a menu page for the admin.
		 * register_pages is called from admin_menu hook. register_pages shoudl then invoke the register_page function.
		 * register_page should not be called outside the register_pages function.
		 */
		function register_sub_page($parent_slug, $page_title, $menu_title, $capability) {
			$controller = $this->get_controller_name();
			$screen_id = add_submenu_page($parent_slug, $page_title, $menu_title, $capability, $controller, array($this, "dispatch"));
			return $screen_id;
		}

		/**
		 * Returns the load priority for a class when it calls it's default hooks.
		 * Users may override this function to change the loading priority.
		 */
		function get_load_priority() {
			return $this->default_load_priority;
		}

		function get_controller_name() {
			$class_name = get_class($this);
			$class_name = str_replace("_controller", "", $class_name);
			return $class_name;
		}

		/**
		 * Called to route to a function in controller when the ?page= parameter invokes it.
		 */
		function dispatch() {
			if(is_admin()) {

				$page = $_REQUEST["page"];
				$action = $_REQUEST["action"];

				if(empty($action) && !empty($page))
					$action = $page;

				$action = (empty($action) ? "index" : $action);
				$action = ($action=="-1" ? "index" : $action);

				$callable = array($this, $action);

				$mappings = $this->get_action_mappings();
				if(!empty($mappings) && is_array($mappings)) {
					if(array_key_exists($action, $mappings)) {
						$callable = $mappings[$action];
					}
				}

				if(is_callable($callable)) {
					call_user_func($callable, $action);
				}
			}
		}

		/*************************
		 * Overridable admin hook admin_init hook
		 *************************/
		function admin_init() { }

		/*************************
		 * Overridable admin hook admin_menu hook
		 *************************/
		function register_pages() {}

	}
}