<?php

function geo_fields_query_ip($ip) {
	gfgcf_controller::$instance->model("gfgcf_model");
	$results = gfgcf_controller::$instance->gfgcf_model->get_users_country_by_ip($ip);
	return $results;
}

/**
 * Get the current WP timezone.
 */
function geo_fields_get_tz() {
	$tz = get_option("timezone_string");
	if(!empty($tz)) {
		$tz_obj = new DateTimeZone($tz);
		return $tz_obj;
	} else {
		return false;
	}
}

/**
 * Get the current timezone offset in seconds from UTC based on WP settings.
 */
function geo_fields_get_tz_offset() {
	$wp_tz = geo_fields_get_tz();
	if(empty($wp_tz)) {
		$gmt_offset = get_option("gmt_offset");
		$offset = $gmt_offset * 60 * 60;
	} else {
		$current_tz = new DateTimeZone(date_default_timezone_get());
		$date = new DateTime("now", $current_tz);
		$offset = $wp_tz->getOffset($date);
	}
	return $offset;
}

/**
 * Convert a UTC time into local time based on WP timezone settings
 */
function geo_fields_localise_time($time_utc) {
	$offset = geo_fields_get_tz_offset();
	$time = $time_utc + $offset;
	return $time;
}


function geo_fields_is_multisite() {
	if(function_exists('is_multisite') && is_multisite()) {
		return true;
	} else {
		return false;
	}
}