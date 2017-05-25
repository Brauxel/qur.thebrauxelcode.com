<?php

class gfgcf_controller extends gfgcf_controller_base {

	public static $instance;

	function __construct() {
		self::$instance = $this;
		parent::__construct();
	}

}