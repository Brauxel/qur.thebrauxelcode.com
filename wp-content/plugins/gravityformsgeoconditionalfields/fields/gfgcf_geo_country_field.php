<?php

class GF_Geo_Country_Field extends GF_Field {

	public $type = 'geo_country_field';

	public function get_form_editor_field_title() {
		return esc_attr__('Geo Country', 'gf-geo-fields');
	}

	public function get_form_editor_button() {
		return array(
			'group' => 'extra_conditionals',
			'text'  => $this->get_form_editor_field_title()
		);
	}

	public function add_button( $field_groups ) {
		$field_groups = $this->maybe_add_field_group( $field_groups );
		return parent::add_button( $field_groups );
	}

	/**
	 * Adds the custom field group if it doesn't already exist.
	 * @param array $field_groups The field groups containing the individual field buttons.
	 * @return array
	 */
	public function maybe_add_field_group( $field_groups ) {
		foreach ( $field_groups as $field_group ) {
			if ( $field_group['name'] == 'extra_conditionals' ) {

				return $field_groups;
			}
		}

		$field_groups[] = array(
			'name'   => 'extra_conditionals',
			'label'  => __('Extra Conditionals', 'gf-geo-fields'),
			'fields' => array()
		);

		return $field_groups;
	}

	function get_form_editor_field_settings() {
		return array(
			'label_setting',
			'no_additional_settings_required_geo_field',
			'geo_field_download_status',
			'geo_field_max_mind',
		);
	}

	// admin
	public function get_form_editor_inline_script_on_page_render() {
		ob_start();
		?>
		<script type="text/javascript">
			function SetDefaultValues_geo_country_field(field) {
				field.label = '<?php print $this->get_form_editor_field_title(); ?>';
			}
		</script>
		<?php
		$content = ob_get_clean();

		$content = str_replace('<script type="text/javascript">', '', $content);
		$content = str_replace('</script>', '', $content);

		return $content;
	}

	public function is_conditional_logic_supported() {
		return true;
	}

	public function get_field_input( $form, $value = '', $entry = null ) {
		$form_id = $form['id'];
		$is_entry_detail = $this->is_entry_detail();
		$is_form_editor  = $this->is_form_editor();
		$is_entry_detail_edit = $this->is_entry_detail_edit();

		$placeholder_attribute = $this->get_field_placeholder_attribute();
		$disabled_text = $this->is_form_editor() ? 'disabled="disabled"' : '';
		$logic_event = $this->get_conditional_logic_event( 'change' );
		$attributes = $disabled_text . " " . $logic_event . " " . $placeholder_attribute;

		ob_start();
		?>
		<div class='ginput_container'>
			<input type="<?php print ($is_entry_detail_edit ? 'text' : 'hidden'); ?>" <?php print $attributes; ?> name="input_<?php print $this->id; ?>" id="input_<?php print $form_id; ?>_<?php print $this->id; ?>" value="<?php print $value; ?>" />
			<?php

			if(($is_form_editor || $is_entry_detail) && !$is_entry_detail_edit) {
				print '<b>' . $value . '</b>';
			}
			?>
		</div>
		<?php
		$input = ob_get_clean();
		$input = preg_replace('/\\n/', '', $input);

		return $input;
	}

	public function get_value_save_entry($value, $form, $input_name, $lead_id, $lead) {
		$value = $this->sanitize_entry_value($value, $form['id']);
		return $value;
	}

	public function get_value_merge_tag($value, $input_id, $entry, $form, $modifier, $raw_value, $url_encode, $esc_html, $format, $nl2br) {
		$output = $this->get_display_value($value);
		return $output;
	}

	public function get_value_entry_detail($value, $currency = '', $use_text = false, $format = 'html', $media = 'screen' ) {
		$output = $this->get_display_value($value);
		return $output;
	}

	public function get_value_entry_list($value, $entry, $field_id, $columns, $form) {
		$output = $this->get_display_value($value);
		return $output;
	}

	public function get_display_value($value) {

		gfgcf_controller::$instance->model('gfgcf_model');
		$countries = gfgcf_controller::$instance->gfgcf_model->get_countries();

		if(array_key_exists($value, $countries))
			$value = $countries[$value];

		return $value;
	}


}
GF_Fields::register(new GF_Geo_Country_Field);

class GF_Geo_Country_Field_Extensions {

	public $type = 'geo_country_field';

	public function __construct() {
		add_filter('gform_pre_render', array($this, 'gform_pre_render'));
		add_filter('gform_form_notification_page', array($this, 'gform_pre_render'));
		add_action("gform_pre_process", array($this, "gform_pre_process"));
		add_filter('gform_admin_pre_render', array($this, 'gform_admin_pre_render'));
		add_filter('gform_field_css_class', array($this, 'gform_field_css_class'), 10, 3);


		/**
		 * When a field is added to a form an ajax request is sent which is caught by this action.
		 * This will populate the initial options for the field.
		 */
		add_action("wp_ajax_gfgcf_load_options", array($this, "ajax_field_added_load_options"));

		// add custom merge tags for use
		add_filter("gform_custom_merge_tags", array($this, "gform_custom_merge_tags"), 12, 4);

		// repalce custom merge tags
		add_filter("gform_replace_merge_tags", array($this, "gform_replace_merge_tags"), 12, 7);
	}

	function gform_admin_pre_render($form) {
		return $this->gform_pre_render($form);
	}

	function gform_pre_render($form) {

		foreach($form["fields"] as $index=>$field) {
			if($field["type"] == $this->type) {
				gfgcf_controller::$instance->model('gfgcf_model');

				$countries = gfgcf_controller::$instance->gfgcf_model->get_formatted_countries();
				$form["fields"][$index]["choices"] = $countries;

				$ip = $_SERVER["REMOTE_ADDR"];
				$results = gfgcf_controller::$instance->gfgcf_model->get_users_country_by_ip($ip);
				$form["fields"][$index]["defaultValue"] = $results[0];
			}
		}

		return $form;
	}

	function gform_pre_process($form) {
		foreach($form["fields"] as $index=>$field) {
			if($field["type"] == $this->type) {
				gfgcf_controller::$instance->model("gfgcf_model");

				$countries = gfgcf_controller::$instance->gfgcf_model->get_formatted_countries();
				$form["fields"][$index]["choices"] = $countries;

				$ip = $_SERVER["REMOTE_ADDR"];
				$results = gfgcf_controller::$instance->gfgcf_model->get_users_country_by_ip($ip);
				$_POST["input_" . $field["id"]] = $results[0];
			}
		}

		return $form;
	}

	function gform_field_css_class($classes, $field, $form){

		if( $field->type == $this->type ){
			$classes .= ' gf_hidden gform_geo_country_field';
		}

		return $classes;
	}

	/**
	 * Populate the dropdown field with custom merge tags.
	 * This is a deprecated method however still required for some aspects of content template population.
	 *
	 * @deprecated
	 */
	function gform_custom_merge_tags($merge_tags, $form_id, $fields, $element_id) {
		$merge_locations = array("field_post_content_template", "field_customfield_content_template");

		if(in_array($element_id, $merge_locations)) {
			$merge_tags[] = array('tag' => '{geo_country}', 'label' => __("Geo Country Code", "gf-geo-fields"));
			$merge_tags[] = array('tag' => '{geo_country_display}', 'label' => __("Geo Country Display", "gf-geo-fields"));
		}

		return $merge_tags;
	}

	/**
	 * Replace merge tags with actual values.
	 */
	function gform_replace_merge_tags($text, $form, $entry, $url_encode, $esc_html, $nl2br, $format) {
		$text = $this->replace_vars($text, $entry);
		return $text;
	}

	/**
	 * This is used to replace custom merge tags in GF logic as well as replace the defaultValue of a custom tag.
	 * This will allow custom tags to be used in conjunction with Hidden form fields to extend functionality of allowing routing/integration into feed logic.
	 *
	 * @param $text
	 * @param bool $entry
	 * @return mixed
	 */
	function replace_vars($text, $entry=false) {

		if(is_array($text)) {
			foreach($text as $key=>$value) {
				$text[$key] = $this->replace_vars($value, $entry);
			}
		} else {
			$pattern1 = '/\{geo_country\}/i';
			$pattern2 = '/\{geo_country_display\}/i';
			$match1 = preg_match($pattern1, $text);
			$match2 = preg_match($pattern2, $text);

			if(!empty($match1) || !empty($match2)) {

				if(!empty($entry)) {
					$ip = $entry["ip"];
				} else {
					// setting the defaultValue
					$ip = $_SERVER["REMOTE_ADDR"];
				}

				if(!empty($ip)) {
					gfgcf_controller::$instance->model('gfgcf_model');
					$results = gfgcf_controller::$instance->gfgcf_model->get_users_country_by_ip($ip);
					$country_code = $results[0];
					$country_name = $results[1];
				} else {
					$country_code = "";
					$country_name = "";
				}
				$text = preg_replace($pattern1, $country_code, $text);
				$text = preg_replace($pattern2, $country_name, $text);
			}
		}

		return $text;
	}

	function ajax_field_added_load_options() {
		if(wp_verify_nonce($_POST["nonce"], 'gfgcf-load-field-options')) {
			$type = $_POST["type"];

			if($type == $this->type) {
				gfgcf_controller::$instance->model('gfgcf_model');

				$options = gfgcf_controller::$instance->gfgcf_model->get_formatted_countries();

				$output = array(
					"nonce" => wp_create_nonce('gfgcf-load-field-options'),
					"options" => $options
				);

				header("Content-Type: application/json");
				print json_encode($output);
				exit;
			}
		} else {
			print "no";
			exit;
		}
	}

}
new GF_Geo_Country_Field_Extensions();