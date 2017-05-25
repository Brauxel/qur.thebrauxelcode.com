<?php

class GF_Geo_Common_Field_Extensions {

	public function __construct() {
		add_action('gform_field_standard_settings', array($this, 'field_standard_settings'), 10, 2);

		add_action('gform_editor_js', array($this, 'gform_editor_js'));

		add_action('admin_enqueue_scripts', array($this, 'admin_enqueue_scripts'));

		/**
		 * When the user triggers an forced GEO database update, this ajax command is fired off.
		 * This will download a database right on the spot and spit back the results immediately.
		 */
		add_action("wp_ajax_gfgcf_trigger_geo_update", array($this, "ajax_gfgcf_trigger_geo_update"));
	}

	/**
	 * Make sure our checkbox scripts runs in admin
	 */
	function admin_enqueue_scripts() {
		wp_register_script('gfgcf_conditional_admin_js', plugins_url('gravityformsgeoconditionalfields/js/conditionals_admin.js'), array(), false, true);
		wp_localize_script('gfgcf_conditional_admin_js', 'gfgcf_localisations', array(
			'country_code' => __("Geo Country Code", "gf-geo-fields"),
			'country_display' => __("Geo Country Display", "gf-geo-fields"),
			'continent_code' => __("Geo Continent Code", "gf-geo-fields"),
			'continent_display' => __("Geo Country Display", "gf-geo-fields"),
		));

		wp_enqueue_script('gfgcf_conditional_admin_js');
	}

	/**
	 * Admin settings for configuring the field.
	 *
	 * @param $position
	 * @param $form_id
	 */
	function field_standard_settings($position, $form_id) {

		gfgcf_controller::$instance->library("gfgcf_geo_library");
		$download_status = gfgcf_controller::$instance->gfgcf_geo_library->get_download_status();


		// Create settings on position 50 (right after Field Label)
		if($position == 25) {
			?>
			<li class="geo_field_download_status field_label field_setting">
				<?php print $this->get_download_status($download_status); ?>
			</li>
			<li class="no_additional_settings_required_geo_field field_label field_setting">
				<strong><?php _e("You can access the conditional logic in each of your fields under 'Enable Conditional Logic'.", 'gf-geo-fields'); ?></strong>
			</li>
			<li class="geo_field_max_mind field_label field_setting">
				<p style="font-size:0.7em;line-height:1;"><?php print __('This product includes GeoLite2 data created by MaxMind, available from <a href="http://www.maxmind.com" target="_blank">http://www.maxmind.com</a>. Accuracy of this plugin is subject to the accuracy of the GeoLite2 database provided by MaxMind.', 'gf-geo-fields'); ?></p>
			</li>
			<?php
		}
	}

	/**
	 * Load options for when the field is first added to the form.
	 */
	function gform_editor_js() {
		?>
		<script type="text/javascript">
			var gfgcf_nonce = '<?php print wp_create_nonce('gfgcf-load-field-options'); ?>';
			var gfgcf_geo_nonce = '<?php print wp_create_nonce('gfgcf-geo-nonce'); ?>';

			jQuery(document).bind("gform_field_added", function(event, gfform, field){

				var fields = ["geo_country_field", "geo_continent_field"];
				if(jQuery.inArray(field.type, fields) != -1) {
					var data = {
						action: 'gfgcf_load_options',
						nonce: gfgcf_nonce,
						type: field.type
					};

					jQuery.post(ajaxurl, data, function(response) {
						gfgcf_nonce = response.nonce;

						for(var i in form.fields) {
							if(form.fields[i].id == field.id) {
								form.fields[i].choices = response.options;
							}
						}
					});
				}
			});

			jQuery(".geo_field_download_status").on('click', 'a.update-geo-database', function(e) {

				var data = {
					action: 'gfgcf_trigger_geo_update',
					nonce: gfgcf_geo_nonce
				};

				// load wait
				var $target = jQuery(e.target);

				// check if already being actioned.
				if($target.data("disabled"))
					return false;

				$target.closest("li.geo_field_download_status").find(".wait").show();

				// disable link
				$target.data("disabled", true);

				jQuery.post(ajaxurl, data, function(response) {
					gfgcf_geo_nonce = response.nonce;
					jQuery("li.geo_field_download_status").html(response.html);
				});

				return false;
			});
		</script>
		<?php
	}

	/**
	 * When the user triggers an forced GEO database update, this ajax command is fired off.
	 * This will download a database right on the spot and spit back the results immediately.
	 */
	function ajax_gfgcf_trigger_geo_update() {

		$nonce = $_REQUEST["nonce"];
		if(!wp_verify_nonce($nonce, "gfgcf-geo-nonce"))
			exit;

		gfgcf_controller::$instance->library("gfgcf_update_library");
		gfgcf_controller::$instance->library("gfgcf_geo_library");

		// this will cause an update to occur to the MaxMind database if necessary
		// updates will only occur on the first wednesday of the month or any day after that if the database hasn't been downloaded.
		$response = gfgcf_controller::$instance->gfgcf_update_library->get_update(true);

		$download_status = gfgcf_controller::$instance->gfgcf_geo_library->get_download_status();
		$download_status_html = $this->get_download_status($download_status);

		$output = array(
			"nonce" => wp_create_nonce('gfgcf-geo-nonce'),
			"html" => $download_status_html
		);

		header("Content-Type: application/json");
		print json_encode($output);
		exit;
	}

	function get_download_status($download_status) {
		$local_time = geo_fields_localise_time($download_status["last_downloaded"]);

		ob_start();
		if(empty($download_status["database_exists"]) || empty($download_status["last_downloaded"])) {
			?>
			<p><?php print __("This field will not function as the GEO database has not yet been downloaded.", 'gf-geo-fields') . (current_user_can("manage_options") ? " " . __("Please trigger an update below.", 'gf-geo-fields') : ""); ?></p>
			<?php
		} else if($download_status["due_for_download"]) {
			?>
			<p><?php print __("The GEO database may report inaccurate results. There is a pending update which has not been downloaded.", 'gf-geo-fields'); ?></p>
			<?php
		} else if(!empty($download_status["last_error"])) {
			?>
			<p><?php print __("While trying to download the GEO database, the following error occurred:", 'gf-geo-fields') . " " . $download_status["last_error"]; ?></p>
			<?php
		}

		if(current_user_can("manage_options")) {
			?>
			<p><a href="#" class="update-geo-database"><?php print __("Trigger update of GEO Database.", 'gf-geo-fields'); ?> <?php print (!empty($download_status["last_downloaded"]) && is_numeric($download_status["last_downloaded"]) ? "(" . __("Last Updated:", 'gf-geo-fields') . " " . date(get_option("date_format") . " " . get_option("time_format"), $local_time) . ")" : ""); ?></a></p>
			<div class="wait" style="width:16px;height:16px;display:none;background-image:url(<?php print plugins_url("gravityformsgeoconditionalfields/images/wait.gif"); ?>);"></div>
			<?php
		}
		$content = ob_get_clean();
		return $content;
	}

}

new GF_Geo_Common_Field_Extensions();