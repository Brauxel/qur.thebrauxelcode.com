<?php


class gfgcf_update_library extends gfgcf_library_base {

	function get_download_link() {
		return "http://geolite.maxmind.com/download/geoip/database/GeoLite2-Country.mmdb.gz";
	}

	function get_temp_gzip_name() {
		return uniqid() . ".mmdb.gz";
	}

	function can_download_file() {
		// MaxMind make the file available on the first tuesday of the month.
		// we are going to download the file on the first wednesday of the month to ensure the file is there has been updated.
		$last_downloaded = get_option("gfgcf_last_downloaded");
		$the_day = strtotime("first wednesday of " . date("Y-m"));
		if((empty($last_downloaded) || $last_downloaded < $the_day) && time() > $the_day) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Download the actual file from MaxMind.
	 */
	function download_file() {
		$this->library("gfgcf_geo_library");

		$timeout = 1800;
		$working_dir = $this->gfgcf_geo_library->get_db_working_directory();
		$temp_file_name = $this->get_temp_gzip_name();
		$temp_file = $working_dir . "/" . $temp_file_name;
		$download_url = $this->get_download_link();

		set_time_limit($timeout);
		$fid = @fopen($temp_file, 'wb');
		if(empty($fid)) {
			// there was an error opening the file for writing
			return array(false, __("Could not open temp file for writing. Ensure WordPress can write to the uploads directory.", 'gf-geo-fields'));
		}

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $download_url);
		curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
		curl_setopt($ch, CURLOPT_FILE, $fid);

		$result = curl_exec($ch);
		if(empty($result)) {
			// file could not be downloaded
			$error_message = __("Could not download file. Please ensure file exists.", 'gf-geo-fields') . " " . curl_errno($ch) . " " . curl_error($ch);
			return array(false, $error_message);
		} else {
			fclose($fid);

			return array(true, $temp_file);
		}

	}

	function get_update($force = false) {

		$this->library("gfgcf_geo_library");

		// update the main site geo database
		// the child sites always look at the main site database file.
		// switch to the root site
		// this will only occur if the user explicitly forces an update to occur (on the actual form field in the editor)
		// the cron that runs only runs on the root site so this a switch will never occur.
		if($force && geo_fields_is_multisite()) {
			$current_blog_id = get_current_blog_id();
			if($current_blog_id != 1) {
				switch_to_blog(1);
			}
		}

		if(!$force && !$this->can_download_file())
			return array(true, false);

		// download file
		$result = $this->download_file();
		if(empty($result[0])) {
			return $this->log_last_error($result); // false and error message
		}
		$temp_file = $result[1];

		// unzip file
		$temp = pathinfo($temp_file);
		$dest = $temp["dirname"] . "/" . $temp["filename"];
		$result = $this->upzip_file($temp_file, $dest);
		if(empty($result[0])) {
			// either no suitable unzip method was found or there was an error while extracting the file
			return $this->log_last_error($result); // false and error message
		}

		$working_dir = $this->gfgcf_geo_library->get_db_working_directory();
		$local_file_name = $this->gfgcf_geo_library->get_db_local_filename();
		$local_file = $working_dir . "/" . $local_file_name;

		// rename the old file
		if(file_exists($local_file) && !is_dir($local_file)) {
			rename($local_file, $local_file . ".old");
		}

		rename($dest, $local_file);

		// finally unlink the old file.
		@unlink($local_file . ".old");
		@unlink($temp_file);

		// tidy up this directory and remove any 'stuck' files
		$this->tidyup_working_directory();

		update_option("gfgcf_last_downloaded", time());
		update_option("gfgcf_last_error", "");

		// update the main site geo database
		// the child sites always look at the main site database file.
		// switch back to original blog.
		if($force && geo_fields_is_multisite()) {
			if($current_blog_id != 1) {
				restore_current_blog();
			}
		}

		return array(true, "File updated.");
	}

	function tidyup_working_directory() {
		$this->library("gfgcf_geo_library");

		$local_file = $this->gfgcf_geo_library->get_db_local_filename();
		$working_dir = $this->gfgcf_geo_library->get_db_working_directory();

		if(file_exists($working_dir) && is_dir($working_dir)) {
			$files = scandir($working_dir);
			foreach($files as $file) {
				$path = pathinfo($file);
				if($file != $local_file && (strtolower($path["extension"]) == "gz" || strtolower($path["extension"]) == "mmdb")) {
					@unlink($working_dir . "/" . $file);
				}
			}
		}
	}

	/**
	 * Discover the correct method to extract the downloaded file from the gzip
	 */
	function upzip_file($src, $dest) {
		$errors = "";

		$methods = array(
			"gzip" => array(
				"tests" => array(array($this, "check_gzip_functions")),
				"function" => array($this, "gzfilemethod"),
			),
			"command_line" => array(
				"tests" => array(array($this, "check_can_system")),
				"function" => array($this, "gunzipmethod"),
			)
		);

		foreach($methods as $method=>$obj) {
			$found_method = true;
			foreach($obj["tests"] as $test) {
				if(is_callable($test[0])) {
					// call without args
					if(!call_user_func($test[0])) {
						$found_method = false;
						break;
					}
				}
			}

			if(!empty($found_method)) {
				// try this method.
				$result = call_user_func($obj["function"], $src, $dest);
				if($result[0] === true) {
					// the file was extracted successfully
					return array(true, "Extracted");
				} else {
					$errors .= $result[1];
				}
			}
		}

		// ran out of methods, can't extract the file
		return array(false, __("Could not find a suitable method to extract gz file.", 'gf-geo-fields') . "\n" . $errors);
	}

	function gzfilemethod($src, $dest) {
		$buffer_size = 4096; // read 4kb at a time

		try {
			$gz_file = gzopen($src, "rb");
			$file = fopen($dest, "wb");

			while(!gzeof($gz_file)) {
				fwrite($file, gzread($gz_file, $buffer_size));
			}

			fclose($file);
			gzclose($gz_file);

		} catch (Exception $e) {
			return array(false, __("Could not extract files.", 'gf-geo-fields') . $e->toString());
		}

		return array(true, __("Unzipped file ok.", 'gf-geo-fields') . "\n");
	}

	function gunzipmethod($src, $dest) {
		try {
			$errors = array(
				"No such file or directory",
				"can't stat",
				"error",
				"warning",
				"invalid"
			);
			$errors = array_merge($errors, array(
				__("No such file or directory", 'gf-geo-fields'),
				__("can't stat", 'gf-geo-fields'),
				__("error", 'gf-geo-fields'),
				__("warning", 'gf-geo-fields'),
				__("invalid", 'gf-geo-fields')
			));
			$output = "";
			$execute = "gunzip --force " . escapeshellarg($src);
			$result = system($execute, $output);
			foreach($errors as $error) {
				if(strpos($result, $error) !== false) {
					return array(false, $result . " " . $output);
				} else if(strpos($output, $error) !== false) {
					return array(false, $result . " " . $output);
				}
			}

			// rename the file to the tmp name
			$path = pathinfo($src);
			$temp = $path["dirname"] . "/" . $path["filename"];
			rename($temp, $dest);
		} catch(Exception $e) {
			return array(false, $e->toString());
		}

		return array(true, __("Unzipped file ok.", 'gf-geo-fields') . "\n");
	}

	function check_gzip_functions() {
		if(!function_exists("gzfile") || !function_exists("gzread") || !function_exists("gzclose")) {
			return array(false, __("GZip Lib not installed.", 'gf-geo-fields') . "\n");
		}

		return array(true, "");
	}

	function check_can_system() {

		if(!function_exists("system")) {
			return array(false, __("System function does not exist.", 'gf-geo-fields') . "\n");
		}

		if(ini_get('safe_mode'))
			return array(false, __("Safe mode is enabled.", 'gf-geo-fields') . "\n");

		$disabled = ini_get('disable_functions');
		if(!empty($disabled)) {
			$disabled = explode(',', $disabled);
			$disabled = array_map('trim', $disabled);
			$result = !in_array("system", $disabled);
			return array($result, (!$result ? __("System is a disabled function.", 'gf-geo-fields') . "\n" : __("Unzipped file ok.", 'gf-geo-fields') . "\n"));
		}

		return array(true, "");
	}

	function log_last_error($result) {
		if(empty($result[0])) {
			// an error occurred.
			// store the last error to occur
			update_option("gfgcf_last_error", $result[1]);
		}
		return $result;
	}

}