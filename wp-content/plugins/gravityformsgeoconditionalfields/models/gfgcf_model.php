<?php

class gfgcf_model extends gfgcf_model_base {

	function get_users_country_by_ip($ip) {
		$this->library("gfgcf_geo_library");

		if(($results = $this->get_runtime_var("ip_query", $ip)) !== false) {
			return $results;
		}

		// $results = array(cc code, country name, continent code, continent code)
		$results = $this->gfgcf_geo_library->query_database($ip);
		$results = $this->localise_results($results);

		$this->set_runtime_var($results, "ip_query", $ip);

		return $results;
	}

	function localise_results($results) {
		$countries = $this->get_countries();
		$continents = $this->get_continents();

		if(array_key_exists($results[0], $countries)) {
			$results[1] = $countries[$results[0]];
		}
		if(array_key_exists($results[2], $continents)) {
			$results[3] = $continents[$results[2]];
		}

		return $results;
	}

	function get_continents() {
		$continents = array(
			"unknown" => __("Unknown", 'gf-geo-fields'),
			"EU" => __("Europe", 'gf-geo-fields'),
			"OC" => __("Oceania", 'gf-geo-fields'),
			"AS" => __("Asia", 'gf-geo-fields'),
			"NA" => __("North America", 'gf-geo-fields'),
			"SA" => __("South America", 'gf-geo-fields'),
			"AF" => __("Africa", 'gf-geo-fields'),
			"AN" => __("Antarctica", 'gf-geo-fields')
		);

		return $continents;
	}

	function get_countries() {

		$countries = array(
			"unknown" => __("Unknown", 'gf-geo-fields'),
			"AF" => __("Afghanistan", 'gf-geo-fields'),
			"AL" => __("Albania", 'gf-geo-fields'),
			"DZ" => __("Algeria", 'gf-geo-fields'),
			"AS" => __("American Samoa", 'gf-geo-fields'),
			"AD" => __("Andorra", 'gf-geo-fields'),
			"AO" => __("Angola", 'gf-geo-fields'),
			"AI" => __("Anguilla", 'gf-geo-fields'),
			"AQ" => __("Antarctica", 'gf-geo-fields'),
			"AG" => __("Antigua and Barbuda", 'gf-geo-fields'),
			"AR" => __("Argentina", 'gf-geo-fields'),
			"AM" => __("Armenia", 'gf-geo-fields'),
			"AW" => __("Aruba", 'gf-geo-fields'),
			"AU" => __("Australia", 'gf-geo-fields'),
			"AT" => __("Austria", 'gf-geo-fields'),
			"AZ" => __("Azerbaijan", 'gf-geo-fields'),
			"BS" => __("Bahamas", 'gf-geo-fields'),
			"BH" => __("Bahrain", 'gf-geo-fields'),
			"BD" => __("Bangladesh", 'gf-geo-fields'),
			"BB" => __("Barbados", 'gf-geo-fields'),
			"BY" => __("Belarus", 'gf-geo-fields'),
			"BE" => __("Belgium", 'gf-geo-fields'),
			"BZ" => __("Belize", 'gf-geo-fields'),
			"BJ" => __("Benin", 'gf-geo-fields'),
			"BM" => __("Bermuda", 'gf-geo-fields'),
			"BT" => __("Bhutan", 'gf-geo-fields'),
			"BO" => __("Bolivia", 'gf-geo-fields'),
			"BA" => __("Bosnia and Herzegovina", 'gf-geo-fields'),
			"BW" => __("Botswana", 'gf-geo-fields'),
			"BV" => __("Bouvet Island", 'gf-geo-fields'),
			"BR" => __("Brazil", 'gf-geo-fields'),
			"BQ" => __("British Antarctic Territory", 'gf-geo-fields'),
			"IO" => __("British Indian Ocean Territory", 'gf-geo-fields'),
			"VG" => __("British Virgin Islands", 'gf-geo-fields'),
			"BN" => __("Brunei", 'gf-geo-fields'),
			"BG" => __("Bulgaria", 'gf-geo-fields'),
			"BF" => __("Burkina Faso", 'gf-geo-fields'),
			"BI" => __("Burundi", 'gf-geo-fields'),
			"KH" => __("Cambodia", 'gf-geo-fields'),
			"CM" => __("Cameroon", 'gf-geo-fields'),
			"CA" => __("Canada", 'gf-geo-fields'),
			"CT" => __("Canton and Enderbury Islands", 'gf-geo-fields'),
			"CV" => __("Cape Verde", 'gf-geo-fields'),
			"KY" => __("Cayman Islands", 'gf-geo-fields'),
			"CF" => __("Central African Republic", 'gf-geo-fields'),
			"TD" => __("Chad", 'gf-geo-fields'),
			"CL" => __("Chile", 'gf-geo-fields'),
			"CN" => __("China", 'gf-geo-fields'),
			"CX" => __("Christmas Island", 'gf-geo-fields'),
			"CC" => __("Cocos [Keeling] Islands", 'gf-geo-fields'),
			"CO" => __("Colombia", 'gf-geo-fields'),
			"KM" => __("Comoros", 'gf-geo-fields'),
			"CG" => __("Congo - Brazzaville", 'gf-geo-fields'),
			"CD" => __("Congo - Kinshasa", 'gf-geo-fields'),
			"CK" => __("Cook Islands", 'gf-geo-fields'),
			"CR" => __("Costa Rica", 'gf-geo-fields'),
			"HR" => __("Croatia", 'gf-geo-fields'),
			"CU" => __("Cuba", 'gf-geo-fields'),
			"CY" => __("Cyprus", 'gf-geo-fields'),
			"CZ" => __("Czech Republic", 'gf-geo-fields'),
			"CI" => __("Côte d’Ivoire", 'gf-geo-fields'),
			"DK" => __("Denmark", 'gf-geo-fields'),
			"DJ" => __("Djibouti", 'gf-geo-fields'),
			"DM" => __("Dominica", 'gf-geo-fields'),
			"DO" => __("Dominican Republic", 'gf-geo-fields'),
			"NQ" => __("Dronning Maud Land", 'gf-geo-fields'),
			"DD" => __("East Germany", 'gf-geo-fields'),
			"EC" => __("Ecuador", 'gf-geo-fields'),
			"EG" => __("Egypt", 'gf-geo-fields'),
			"SV" => __("El Salvador", 'gf-geo-fields'),
			"GQ" => __("Equatorial Guinea", 'gf-geo-fields'),
			"ER" => __("Eritrea", 'gf-geo-fields'),
			"EE" => __("Estonia", 'gf-geo-fields'),
			"ET" => __("Ethiopia", 'gf-geo-fields'),
			"FK" => __("Falkland Islands", 'gf-geo-fields'),
			"FO" => __("Faroe Islands", 'gf-geo-fields'),
			"FJ" => __("Fiji", 'gf-geo-fields'),
			"FI" => __("Finland", 'gf-geo-fields'),
			"FR" => __("France", 'gf-geo-fields'),
			"GF" => __("French Guiana", 'gf-geo-fields'),
			"PF" => __("French Polynesia", 'gf-geo-fields'),
			"TF" => __("French Southern Territories", 'gf-geo-fields'),
			"FQ" => __("French Southern and Antarctic Territories", 'gf-geo-fields'),
			"GA" => __("Gabon", 'gf-geo-fields'),
			"GM" => __("Gambia", 'gf-geo-fields'),
			"GE" => __("Georgia", 'gf-geo-fields'),
			"DE" => __("Germany", 'gf-geo-fields'),
			"GH" => __("Ghana", 'gf-geo-fields'),
			"GI" => __("Gibraltar", 'gf-geo-fields'),
			"GR" => __("Greece", 'gf-geo-fields'),
			"GL" => __("Greenland", 'gf-geo-fields'),
			"GD" => __("Grenada", 'gf-geo-fields'),
			"GP" => __("Guadeloupe", 'gf-geo-fields'),
			"GU" => __("Guam", 'gf-geo-fields'),
			"GT" => __("Guatemala", 'gf-geo-fields'),
			"GG" => __("Guernsey", 'gf-geo-fields'),
			"GN" => __("Guinea", 'gf-geo-fields'),
			"GW" => __("Guinea-Bissau", 'gf-geo-fields'),
			"GY" => __("Guyana", 'gf-geo-fields'),
			"HT" => __("Haiti", 'gf-geo-fields'),
			"HM" => __("Heard Island and McDonald Islands", 'gf-geo-fields'),
			"HN" => __("Honduras", 'gf-geo-fields'),
			"HK" => __("Hong Kong SAR China", 'gf-geo-fields'),
			"HU" => __("Hungary", 'gf-geo-fields'),
			"IS" => __("Iceland", 'gf-geo-fields'),
			"IN" => __("India", 'gf-geo-fields'),
			"ID" => __("Indonesia", 'gf-geo-fields'),
			"IR" => __("Iran", 'gf-geo-fields'),
			"IQ" => __("Iraq", 'gf-geo-fields'),
			"IE" => __("Ireland", 'gf-geo-fields'),
			"IM" => __("Isle of Man", 'gf-geo-fields'),
			"IL" => __("Israel", 'gf-geo-fields'),
			"IT" => __("Italy", 'gf-geo-fields'),
			"JM" => __("Jamaica", 'gf-geo-fields'),
			"JP" => __("Japan", 'gf-geo-fields'),
			"JE" => __("Jersey", 'gf-geo-fields'),
			"JT" => __("Johnston Island", 'gf-geo-fields'),
			"JO" => __("Jordan", 'gf-geo-fields'),
			"KZ" => __("Kazakhstan", 'gf-geo-fields'),
			"KE" => __("Kenya", 'gf-geo-fields'),
			"KI" => __("Kiribati", 'gf-geo-fields'),
			"KW" => __("Kuwait", 'gf-geo-fields'),
			"KG" => __("Kyrgyzstan", 'gf-geo-fields'),
			"LA" => __("Laos", 'gf-geo-fields'),
			"LV" => __("Latvia", 'gf-geo-fields'),
			"LB" => __("Lebanon", 'gf-geo-fields'),
			"LS" => __("Lesotho", 'gf-geo-fields'),
			"LR" => __("Liberia", 'gf-geo-fields'),
			"LY" => __("Libya", 'gf-geo-fields'),
			"LI" => __("Liechtenstein", 'gf-geo-fields'),
			"LT" => __("Lithuania", 'gf-geo-fields'),
			"LU" => __("Luxembourg", 'gf-geo-fields'),
			"MO" => __("Macau SAR China", 'gf-geo-fields'),
			"MK" => __("Macedonia", 'gf-geo-fields'),
			"MG" => __("Madagascar", 'gf-geo-fields'),
			"MW" => __("Malawi", 'gf-geo-fields'),
			"MY" => __("Malaysia", 'gf-geo-fields'),
			"MV" => __("Maldives", 'gf-geo-fields'),
			"ML" => __("Mali", 'gf-geo-fields'),
			"MT" => __("Malta", 'gf-geo-fields'),
			"MH" => __("Marshall Islands", 'gf-geo-fields'),
			"MQ" => __("Martinique", 'gf-geo-fields'),
			"MR" => __("Mauritania", 'gf-geo-fields'),
			"MU" => __("Mauritius", 'gf-geo-fields'),
			"YT" => __("Mayotte", 'gf-geo-fields'),
			"FX" => __("Metropolitan France", 'gf-geo-fields'),
			"MX" => __("Mexico", 'gf-geo-fields'),
			"FM" => __("Micronesia", 'gf-geo-fields'),
			"MI" => __("Midway Islands", 'gf-geo-fields'),
			"MD" => __("Moldova", 'gf-geo-fields'),
			"MC" => __("Monaco", 'gf-geo-fields'),
			"MN" => __("Mongolia", 'gf-geo-fields'),
			"ME" => __("Montenegro", 'gf-geo-fields'),
			"MS" => __("Montserrat", 'gf-geo-fields'),
			"MA" => __("Morocco", 'gf-geo-fields'),
			"MZ" => __("Mozambique", 'gf-geo-fields'),
			"MM" => __("Myanmar [Burma]", 'gf-geo-fields'),
			"NA" => __("Namibia", 'gf-geo-fields'),
			"NR" => __("Nauru", 'gf-geo-fields'),
			"NP" => __("Nepal", 'gf-geo-fields'),
			"NL" => __("Netherlands", 'gf-geo-fields'),
			"AN" => __("Netherlands Antilles", 'gf-geo-fields'),
			"NT" => __("Neutral Zone", 'gf-geo-fields'),
			"NC" => __("New Caledonia", 'gf-geo-fields'),
			"NZ" => __("New Zealand", 'gf-geo-fields'),
			"NI" => __("Nicaragua", 'gf-geo-fields'),
			"NE" => __("Niger", 'gf-geo-fields'),
			"NG" => __("Nigeria", 'gf-geo-fields'),
			"NU" => __("Niue", 'gf-geo-fields'),
			"NF" => __("Norfolk Island", 'gf-geo-fields'),
			"KP" => __("North Korea", 'gf-geo-fields'),
			"VD" => __("North Vietnam", 'gf-geo-fields'),
			"MP" => __("Northern Mariana Islands", 'gf-geo-fields'),
			"NO" => __("Norway", 'gf-geo-fields'),
			"OM" => __("Oman", 'gf-geo-fields'),
			"PC" => __("Pacific Islands Trust Territory", 'gf-geo-fields'),
			"PK" => __("Pakistan", 'gf-geo-fields'),
			"PW" => __("Palau", 'gf-geo-fields'),
			"PS" => __("Palestinian Territories", 'gf-geo-fields'),
			"PA" => __("Panama", 'gf-geo-fields'),
			"PZ" => __("Panama Canal Zone", 'gf-geo-fields'),
			"PG" => __("Papua New Guinea", 'gf-geo-fields'),
			"PY" => __("Paraguay", 'gf-geo-fields'),
			"YD" => __("People's Democratic Republic of Yemen", 'gf-geo-fields'),
			"PE" => __("Peru", 'gf-geo-fields'),
			"PH" => __("Philippines", 'gf-geo-fields'),
			"PN" => __("Pitcairn Islands", 'gf-geo-fields'),
			"PL" => __("Poland", 'gf-geo-fields'),
			"PT" => __("Portugal", 'gf-geo-fields'),
			"PR" => __("Puerto Rico", 'gf-geo-fields'),
			"QA" => __("Qatar", 'gf-geo-fields'),
			"RO" => __("Romania", 'gf-geo-fields'),
			"RU" => __("Russia", 'gf-geo-fields'),
			"RW" => __("Rwanda", 'gf-geo-fields'),
			"RE" => __("Réunion", 'gf-geo-fields'),
			"BL" => __("Saint Barthélemy", 'gf-geo-fields'),
			"SH" => __("Saint Helena", 'gf-geo-fields'),
			"KN" => __("Saint Kitts and Nevis", 'gf-geo-fields'),
			"LC" => __("Saint Lucia", 'gf-geo-fields'),
			"MF" => __("Saint Martin", 'gf-geo-fields'),
			"PM" => __("Saint Pierre and Miquelon", 'gf-geo-fields'),
			"VC" => __("Saint Vincent and the Grenadines", 'gf-geo-fields'),
			"WS" => __("Samoa", 'gf-geo-fields'),
			"SM" => __("San Marino", 'gf-geo-fields'),
			"SA" => __("Saudi Arabia", 'gf-geo-fields'),
			"SN" => __("Senegal", 'gf-geo-fields'),
			"RS" => __("Serbia", 'gf-geo-fields'),
			"CS" => __("Serbia and Montenegro", 'gf-geo-fields'),
			"SC" => __("Seychelles", 'gf-geo-fields'),
			"SL" => __("Sierra Leone", 'gf-geo-fields'),
			"SG" => __("Singapore", 'gf-geo-fields'),
			"SK" => __("Slovakia", 'gf-geo-fields'),
			"SI" => __("Slovenia", 'gf-geo-fields'),
			"SB" => __("Solomon Islands", 'gf-geo-fields'),
			"SO" => __("Somalia", 'gf-geo-fields'),
			"ZA" => __("South Africa", 'gf-geo-fields'),
			"GS" => __("South Georgia and the South Sandwich Islands", 'gf-geo-fields'),
			"KR" => __("South Korea", 'gf-geo-fields'),
			"ES" => __("Spain", 'gf-geo-fields'),
			"LK" => __("Sri Lanka", 'gf-geo-fields'),
			"SD" => __("Sudan", 'gf-geo-fields'),
			"SR" => __("Suriname", 'gf-geo-fields'),
			"SJ" => __("Svalbard and Jan Mayen", 'gf-geo-fields'),
			"SZ" => __("Swaziland", 'gf-geo-fields'),
			"SE" => __("Sweden", 'gf-geo-fields'),
			"CH" => __("Switzerland", 'gf-geo-fields'),
			"SY" => __("Syria", 'gf-geo-fields'),
			"ST" => __("São Tomé and Príncipe", 'gf-geo-fields'),
			"TW" => __("Taiwan", 'gf-geo-fields'),
			"TJ" => __("Tajikistan", 'gf-geo-fields'),
			"TZ" => __("Tanzania", 'gf-geo-fields'),
			"TH" => __("Thailand", 'gf-geo-fields'),
			"TL" => __("Timor-Leste", 'gf-geo-fields'),
			"TG" => __("Togo", 'gf-geo-fields'),
			"TK" => __("Tokelau", 'gf-geo-fields'),
			"TO" => __("Tonga", 'gf-geo-fields'),
			"TT" => __("Trinidad and Tobago", 'gf-geo-fields'),
			"TN" => __("Tunisia", 'gf-geo-fields'),
			"TR" => __("Turkey", 'gf-geo-fields'),
			"TM" => __("Turkmenistan", 'gf-geo-fields'),
			"TC" => __("Turks and Caicos Islands", 'gf-geo-fields'),
			"TV" => __("Tuvalu", 'gf-geo-fields'),
			"UM" => __("U.S. Minor Outlying Islands", 'gf-geo-fields'),
			"PU" => __("U.S. Miscellaneous Pacific Islands", 'gf-geo-fields'),
			"VI" => __("U.S. Virgin Islands", 'gf-geo-fields'),
			"UG" => __("Uganda", 'gf-geo-fields'),
			"UA" => __("Ukraine", 'gf-geo-fields'),
			"SU" => __("Union of Soviet Socialist Republics", 'gf-geo-fields'),
			"AE" => __("United Arab Emirates", 'gf-geo-fields'),
			"GB" => __("United Kingdom", 'gf-geo-fields'),
			"US" => __("United States", 'gf-geo-fields'),
			"ZZ" => __("Unknown or Invalid Region", 'gf-geo-fields'),
			"UY" => __("Uruguay", 'gf-geo-fields'),
			"UZ" => __("Uzbekistan", 'gf-geo-fields'),
			"VU" => __("Vanuatu", 'gf-geo-fields'),
			"VA" => __("Vatican City", 'gf-geo-fields'),
			"VE" => __("Venezuela", 'gf-geo-fields'),
			"VN" => __("Vietnam", 'gf-geo-fields'),
			"WK" => __("Wake Island", 'gf-geo-fields'),
			"WF" => __("Wallis and Futuna", 'gf-geo-fields'),
			"EH" => __("Western Sahara", 'gf-geo-fields'),
			"YE" => __("Yemen", 'gf-geo-fields'),
			"ZM" => __("Zambia", 'gf-geo-fields'),
			"ZW" => __("Zimbabwe", 'gf-geo-fields'),
			"AX" => __("Åland Islands", 'gf-geo-fields'),
		);

		return $countries;
	}

	function get_formatted_countries() {
		$countries = $this->get_countries();

		$output = array();
		foreach($countries as $cc=>$c) {
			$output[] = array(
				"value" => $cc,
				"text" => $c
			);
		}

		return $output;
	}

	function get_formatted_continents() {
		$countries = $this->get_continents();

		$output = array();
		foreach($countries as $cc=>$c) {
			$output[] = array(
				"value" => $cc,
				"text" => $c
			);
		}

		return $output;
	}



}