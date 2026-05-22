<?php
if (!defined('ABSPATH')) {
	exit;
}

/**
 * Easy_Sticky_Sidebar_Query
 * @package sticky-sidebar
 * @since   1.3.7
 */

class Easy_Sticky_Sidebar_Query {

	/**
	 * Fonts
	 * @since 1.3.7
	 */
	var $fonts = [];

	/**
	 * Fonts
	 * @since 1.3.7
	 */
	var $cta = [];

	/**
	 * Easy_Sticky_Sidebar_Query Constructor
	 * @since 1.3.7
	 */
	function __construct() {
		if (is_admin()) {
			return;
		}

		add_action('wp', [$this, 'get_cta']);
		add_action('wp_enqueue_scripts', array($this, 'easy_sticky_sidebar_google_fonts'));
	}

	public function easy_sticky_sidebar_google_fonts() {
		global $CTA_Query;
		if (!is_array($CTA_Query->fonts)) {
			return;
		}

		if (!empty($CTA_Query->fonts)) {
			wp_enqueue_style('easy-sticky-sidebar-font', 'https://fonts.googleapis.com/css?family=' . implode('|', $CTA_Query->fonts));
		}
	}

	/**
	 * Get CTA Items
	 * @since 1.3.7
	 */
	public function get_cta() {
		global $wpdb;

		$current_id = get_the_ID();
		$is_front = is_front_page() || is_home();

		if ($is_front) {
			$results = $wpdb->get_results($wpdb->prepare(
				"SELECT * FROM $wpdb->sticky_cta WHERE SSuprydp_location = %d OR SSuprydp_location = '' OR SSuprydp_location = 'home' OR SSuprydp_location IN ('entire_site','all') ORDER BY id ASC LIMIT 0, 3",
				$current_id
			));
		} else {
			$results = $wpdb->get_results($wpdb->prepare(
				"SELECT * FROM $wpdb->sticky_cta WHERE SSuprydp_location = %d OR SSuprydp_location IN ('entire_site','all') ORDER BY id ASC LIMIT 0, 3",
				$current_id
			));
		}
		$results = apply_filters('easy_sticky_sidebar_query', $results);

		//field keys for fonts
		$font_fields = apply_filters('easy_sticky_sidebar_font_fields', ['default_font_family', 'SSuprydp_button_option_font', 'SSuprydp_content_option_font', 'SSuprydp_action_option_font']);

		$fonts = [];
		foreach ($results as &$item) {
			if (!is_a($item, 'WP_Sticky_CTA_Data')) {
				$item = new WP_Sticky_CTA_Data($item);
			}

			foreach ((array) $font_fields as $field_key) {
				array_push($fonts, $item->$field_key);
			}
		}

		$this->cta = $results;
		$this->fonts = array_unique(array_filter($fonts));

		$disable_google_font = apply_filters('easy_sticky_sidebar_disable_google_font', false);
		if ($disable_google_font) {
			$this->fonts = [];
		}

		$GLOBALS['CTA_Query'] = $this;
	}
}
