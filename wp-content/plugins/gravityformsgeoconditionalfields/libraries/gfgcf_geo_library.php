<?php

class gfgcf_geo_library extends gfgcf_library_base {

	function get_db_working_directory() {

		if(($base_dir = $this->get_runtime_var("base_working_dir")) !== false) {
			return $base_dir;
		}

		if(geo_fields_is_multisite()) {
			$base_dir = WP_CONTENT_DIR . "/uploads"; // possibly will need to alter this
		} else {
			$upload_dir = wp_upload_dir();
			$base_dir = $upload_dir["basedir"];
		}
		$base_dir .= "/gfgcfgeo";

		$result = wp_mkdir_p($base_dir);
		if(empty($result)) {
			return false;
		}

		$this->set_runtime_var($base_dir, "base_working_dir");

		return $base_dir;
	}
	function get_db_local_filename() {
		return "GeoLite2-Country.mmdb";
	}

	function get_download_status() {
		global $wpdb;

		$last_downloaded = "";
		$last_error = "";

		if(geo_fields_is_multisite() && get_current_blog_id() != 1) {
			// need to load the root site information but don't want to use switch_to_blog.
			$options_table = $wpdb->base_prefix . "options";

			$sql = "Select option_name, option_value
					From {$options_table}
					Where option_name in ('gfgcf_last_downloaded', 'gfgcf_last_error')";

			$results = $wpdb->get_results($sql, ARRAY_A);
			foreach($results as $row) {
				if($row["option_name"] == "gfgcf_last_downloaded") {
					$last_downloaded = $row["option_value"];
				} else if($row["option_name"] == "gfgcf_last_error") {
					$last_error = $row["option_value"];
				}
			}
		} else {
			$last_downloaded = get_option("gfgcf_last_downloaded");
			$last_error = get_option("gfgcf_last_error");
		}

		$working_directory = $this->get_db_working_directory();
		$db_file_name = $this->get_db_local_filename();
		$db_file = $working_directory . "/" . $db_file_name;

		$output = array(
			"last_downloaded" => $last_downloaded,
			"last_error" => $last_error,
			"database_exists" => file_exists($db_file),
			"due_for_download" => false
		);

		if(!empty($output["last_downloaded"]) && is_numeric($output["last_downloaded"])) {
			$the_day = strtotime("first wednesday of " . date("Y-m"));
			if($output["last_downloaded"] < $the_day) {
				$output["due_for_download"] = true;
			}
		} else {
			$output["due_for_download"] = true;
		}

		if(!$output["database_exists"]) {
			$output["due_for_download"] = true;
		}

		return $output;
	}


	function query_database($ip) {
		$working_directory = $this->get_db_working_directory();
		$db_file_name = $this->get_db_local_filename();
		$db_file = $working_directory . "/" . $db_file_name;

		if(file_exists($db_file)) {

			// Execptions
			$this->external("GeoIp2/Exception/GeoIp2Exception");
			$this->external("GeoIp2/Exception/AddressNotFoundException");
			$this->external("GeoIp2/Exception/AuthenticationException");
			$this->external("GeoIp2/Exception/HttpException");
			$this->external("GeoIp2/Exception/InvalidRequestException");
			$this->external("GeoIp2/Exception/OutOfQueriesException");
			$this->external("MaxMind/Db/Reader/InvalidDatabaseException");

			// Models
			$this->external("GeoIp2/Model/Country");

			// Records
			$this->external("GeoIp2/Record/AbstractRecord");
			$this->external("GeoIp2/Record/AbstractPlaceRecord");
			$this->external("GeoIp2/Record/MaxMind");
			$this->external("GeoIp2/Record/Country");
			$this->external("GeoIp2/Record/Continent");
			$this->external("GeoIp2/Record/RepresentedCountry");
			$this->external("GeoIp2/Record/Traits");

			// Readers
			$this->external("MaxMind/Db/Reader/Decoder");
			$this->external("MaxMind/Db/Reader/Util");
			$this->external("MaxMind/Db/Reader/Metadata");
			$this->external("MaxMind/Db/Reader");
			$this->external("GeoIp2/ProviderInterface");
			$this->external("GeoIp2/Database/Reader");

			try {

				//$t1 = microtime(true);
				$reader = new \gfgcfGeoIp2\Database\Reader($db_file);
				$record = $reader->country($ip);
				//print (microtime(true) - $t1) . "\n";
				// 0.0032029151916504
				//exit;

				$output = array($record->country->isoCode, $record->country->name, $record->continent->code, $record->continent->name);
				return $output;
			} catch (Exception $e) {
				return array("unknown", "unknown", "unknown", "unknown");
			}
		} else {
			return array("unknown", "unknown", "unknown", "unknown");
		}
	}

}