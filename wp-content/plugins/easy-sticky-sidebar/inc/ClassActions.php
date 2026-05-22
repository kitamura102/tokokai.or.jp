<?php
if (!defined('ABSPATH')) {
	exit;
}

/*
 * StickySidebar Actions
 * @package wp-dynamic-shortcodes/inc
 * @since   1.2.0
 */

class Easy_Sticky_Sidebar_Actions {

	/**
	 * StickySidebar Constructor.
	 */
	function __construct() {
		$public_ajax_actions = array(
			'easy_sticky_sidebar_get_click',
			'easy_sticky_sidebar_track_impressions',
		);

		foreach ($this->get_ajax_actions() as $key => $action) {
			add_action("wp_ajax_{$action['name']}", [$this, $action['callback']]);

			if (in_array($action['name'], $public_ajax_actions, true)) {
				add_action("wp_ajax_nopriv_{$action['name']}", [$this, $action['callback']]);
			}
		}

		// Fixed: Removed wp_ajax_nopriv_ hooks for security - only authenticated users can access these functions
		add_action('wp_ajax_easy_sticky_sidebar_update_status', [$this, 'update_cta_status']);
		add_action('wp_ajax_easy_sticky_sidebar_change_name', [$this, 'change_sticky_sidebar_name']);
		
		// Removed tracking functionality - keeping analytics as pro features

		add_action('easy_sticky_sidebar_after_save', [$this, 'redirect_after_creating_new_sidebar'], 10, 3);
	}

	public function update_cta_status() {
		// Security: Check if user has proper capabilities
		if (!current_user_can('manage_options')) {
			wp_send_json(['success' => false, 'error' => esc_html__('Insufficient permissions.', 'easy-sticky-sidebar')]);
		}

		// Security: Verify nonce to prevent CSRF attacks
		if (!check_ajax_referer('easy_sticky_sidebar_nonce', 'nonce', false)) {
			wp_send_json(['success' => false, 'error' => esc_html__('Security check failed.', 'easy-sticky-sidebar')]);
		}

		global $wpdb;

		$post_data = filter_input_array(INPUT_POST, FILTER_SANITIZE_SPECIAL_CHARS);

		$sticky_id = isset($post_data['sticky_id']) ? absint($post_data['sticky_id']) : 0;
		$status = isset($post_data['status']) ? sanitize_text_field($post_data['status']) : '';

		$allowed_statuses = ['live', 'development', 'off'];
		if (!in_array($status, $allowed_statuses, true)) {
			wp_send_json(['success' => false, 'error' => esc_html__('Invalid status value.', 'easy-sticky-sidebar')]);
		}

		$sticky = $wpdb->get_row($wpdb->prepare("SELECT * FROM $wpdb->sticky_cta WHERE id = %d", $sticky_id));
		if (!$sticky) {
			wp_send_json(['success' => false, 'error' => esc_html__('Sticky item not exists.', 'easy-sticky-sidebar')]);
		}

		$wpdb->update(
			$wpdb->sticky_cta,
			array('SSuprydp_development' => $status),
			array('id' => $sticky_id),
			array('%s'),
			array('%d')
		);

		wp_send_json(['success' => true]);
	}

	public function change_sticky_sidebar_name() {
		// Security: Check if user has proper capabilities
		if (!current_user_can('manage_options')) {
			wp_send_json(['success' => false, 'error' => esc_html__('Insufficient permissions.', 'easy-sticky-sidebar')]);
		}

		// Security: Verify nonce to prevent CSRF attacks
		if (!check_ajax_referer('easy_sticky_sidebar_nonce', 'nonce', false)) {
			wp_send_json(['success' => false, 'error' => esc_html__('Security check failed.', 'easy-sticky-sidebar')]);
		}

		global $wpdb;

		$post_data = filter_input_array(INPUT_POST, FILTER_SANITIZE_SPECIAL_CHARS);

		$sticky_id = $post_data['sticky'];

		$exists = $wpdb->get_var($wpdb->prepare("SELECT ID FROM $wpdb->sticky_cta_options WHERE option_name = 'sidebar_name' AND sticky_cta_id = %d", $sticky_id));

		$data_format = array('%d', '%s', '%s');

		$data = ['sticky_cta_id' => $sticky_id, 'option_name' => 'sidebar_name', 'option_value' => $post_data['name']];
		if ($exists) {
			$data['ID'] = $exists;
			array_push($data_format, '%d');
		}

		$wpdb->replace($wpdb->sticky_cta_options, $data, $data_format);
		wp_send_json(['success' => true]);
	}

	/*
     * AJAX action definitions
     *
     * @return Array
     */

	private function get_ajax_actions() {
		return [
			['name' => 'easy_sticky_sidebar_process_pages', 'callback' => 'process_pages'],
			['name' => 'easy_sticky_sidebar_ajax_check', 'callback' => 'ajax_check'],
			['name' => 'easy_sticky_sidebar_validate_data', 'callback' => 'validate_data'],
			['name' => 'easy_sticky_sidebar_get_click', 'callback' => 'track_click'],
			['name' => 'easy_sticky_sidebar_track_impressions', 'callback' => 'track_impressions'],
		];
	}

	/**
	 * Track CTA click on frontend.
	 * Clicks/CTR are now available in free plugin stats.
	 *
	 * @return void
	 */
	public function track_click() {
		if (!check_ajax_referer('easy_sticky_sidebar_front_nonce', 'nonce', false)) {
			wp_send_json_success();
		}

		global $wpdb;

		$sticky_id = isset($_POST['sticky_id']) ? absint($_POST['sticky_id']) : 0;
		if ($sticky_id <= 0) {
			wp_send_json_success();
		}

		$current_user = wp_get_current_user();
		$has_admin_role = array_intersect(['administrator', 'editor'], (array) $current_user->roles);
		if (!empty($has_admin_role)) {
			wp_send_json_success();
		}

		$sticky = $wpdb->get_row($wpdb->prepare("SELECT id FROM $wpdb->sticky_cta WHERE id = %d", $sticky_id));
		if (!$sticky) {
			wp_send_json_success();
		}

		$wpdb->query($wpdb->prepare("UPDATE $wpdb->sticky_cta SET SSuprydp_clicks = SSuprydp_clicks + 1 WHERE id = %d", $sticky_id));
		wp_send_json_success();
	}

	/**
	 * Track CTA impressions via AJAX (non-blocking).
	 *
	 * @return void
	 */
	public function track_impressions() {
		if (!check_ajax_referer('easy_sticky_sidebar_front_nonce', 'nonce', false)) {
			wp_send_json_success();
		}

		$current_user = wp_get_current_user();
		$has_admin_role = array_intersect(['administrator', 'editor'], (array) $current_user->roles);
		if (!empty($has_admin_role)) {
			wp_send_json_success();
		}

		$ids = isset($_POST['ids']) && is_array($_POST['ids']) ? array_map('absint', $_POST['ids']) : [];
		$ids = array_filter($ids);
		if (empty($ids)) {
			wp_send_json_success();
		}

		global $wpdb;
		foreach ($ids as $sticky_id) {
			$wpdb->query($wpdb->prepare(
				"UPDATE $wpdb->sticky_cta SET SSuprydp_impressions = SSuprydp_impressions + 1 WHERE id = %d",
				$sticky_id
			));
		}

		wp_send_json_success();
	}

	function content_filter($tags, $context) {
		$tags['iframe'] = array(
			'src'               => true,
			'height'            => true,
			'width'             => true,
			'allow'             => true,
			'frameborder'       => true,
			'allowfullscreen'   => true,
		);

		return $tags;
	}

	/**
	 *
	 * @global type $wpdb
	 * @return JSON
	 */
	public function process_pages() {
		if (!isset($_POST)) {
			wp_send_json(['status' => 'failed', 'message' => 'Data missing']);
		}

		$check_security = check_ajax_referer('_nonce_easy_sticky_sidebar', '_wpnonce', false);
		if (false === $check_security) {
			wp_send_json(['status' => 'failed', 'message' => 'Security failed']);
		}

		if (!current_user_can('manage_options')) {
			wp_send_json(['status' => 'failed', 'message' => 'You are not able to update CTA.']);
		}

		$postdata = filter_input_array(INPUT_POST, FILTER_SANITIZE_SPECIAL_CHARS);
		$sticky_id = isset($postdata['sticky_id']) ? absint($postdata['sticky_id']) : 0;

		// Keep template persistence deterministic for thumbnail-based picker UI.
		// Use sidebar_template when posted; otherwise fallback to sidebar_template_picker.
		$posted_template = '';
		// Priority matters:
		// 1) sidebar_template_picker (actual selected thumbnail radio)
		// 2) sidebar_template (hidden/native select mirror)
		// 3) sidebar_template_user_selection (legacy hidden mirror)
		if (isset($_POST['sidebar_template_picker'])) {
			$posted_template = sanitize_text_field(wp_unslash($_POST['sidebar_template_picker']));
		} elseif (isset($_POST['sidebar_template'])) {
			$posted_template = sanitize_text_field(wp_unslash($_POST['sidebar_template']));
		} elseif (isset($_POST['sidebar_template_user_selection'])) {
			$posted_template = sanitize_text_field(wp_unslash($_POST['sidebar_template_user_selection']));
		}
		$available_templates = function_exists('easy_sticky_sidebar_templates') ? array_keys((array) easy_sticky_sidebar_templates()) : [];
		if ($posted_template !== '') {
			$posted_template = function_exists('easy_sticky_sidebar_normalize_template_key')
				? easy_sticky_sidebar_normalize_template_key($posted_template, '')
				: $posted_template;
			if ($posted_template !== '' && (empty($available_templates) || in_array($posted_template, $available_templates, true))) {
				$postdata['sidebar_template'] = $posted_template;
			}
		}
		// Never let an update silently reset template to default because of missing/malformed template payload.
		if (empty($postdata['sidebar_template']) && $sticky_id > 0) {
			global $wpdb;
			$existing_template = $wpdb->get_var(
				$wpdb->prepare(
					"SELECT option_value FROM {$wpdb->prefix}sticky_cta_options WHERE sticky_cta_id = %d AND option_name = %s ORDER BY ID DESC LIMIT 1",
					$sticky_id,
					'sidebar_template'
				)
			);
			$existing_template = maybe_unserialize($existing_template);
			if (function_exists('easy_sticky_sidebar_normalize_template_key')) {
				$existing_template = easy_sticky_sidebar_normalize_template_key($existing_template, '');
			}
			if (is_string($existing_template) && $existing_template !== '') {
				if (empty($available_templates) || in_array($existing_template, $available_templates, true)) {
					$postdata['sidebar_template'] = $existing_template;
				}
			}
		}

		// Ensure button_icon is captured (free feature now).
		if (isset($_POST['button_icon'])) {
			$button_icon = sanitize_text_field(wp_unslash($_POST['button_icon']));
			if (function_exists('easy_sticky_sidebar_normalize_icon_class')) {
				$button_icon = easy_sticky_sidebar_normalize_icon_class($button_icon);
			}
			$postdata['button_icon'] = $button_icon;
		}

		// Ensure sticky overlay fields are saved reliably (admin + frontend parity).
		if (isset($_POST['image_placement'])) {
			$postdata['image_placement'] = sanitize_text_field(wp_unslash($_POST['image_placement']));
		}
		if (isset($_POST['overlay_position'])) {
			$postdata['overlay_position'] = sanitize_text_field(wp_unslash($_POST['overlay_position']));
		}
		if (isset($_POST['overlay_content_alignment'])) {
			$overlay_content_alignment = sanitize_text_field(wp_unslash($_POST['overlay_content_alignment']));
			$postdata['overlay_content_alignment'] = in_array($overlay_content_alignment, ['left', 'center', 'right'], true) ? $overlay_content_alignment : 'center';
		}
		if (isset($_POST['overlay_button_alignment'])) {
			$overlay_button_alignment = sanitize_text_field(wp_unslash($_POST['overlay_button_alignment']));
			$postdata['overlay_button_alignment'] = in_array($overlay_button_alignment, ['left', 'center', 'right'], true) ? $overlay_button_alignment : 'center';
		}
		if (isset($_POST['button_alignment'])) {
			$button_alignment = sanitize_text_field(wp_unslash($_POST['button_alignment']));
			$postdata['button_alignment'] = in_array($button_alignment, ['start', 'center', 'end'], true) ? $button_alignment : 'start';
		}
		if (isset($_POST['button_text_orientation'])) {
			$button_text_orientation = sanitize_text_field(wp_unslash($_POST['button_text_orientation']));
			$postdata['button_text_orientation'] = in_array($button_text_orientation, ['top-to-bottom', 'bottom-to-top'], true) ? $button_text_orientation : 'top-to-bottom';
		}
		if (isset($_POST['button_icon_position'])) {
			$button_icon_position = sanitize_text_field(wp_unslash($_POST['button_icon_position']));
			$postdata['button_icon_position'] = in_array($button_icon_position, ['before', 'after'], true) ? $button_icon_position : 'before';
		}
		if (isset($_POST['overlay_tab_text_orientation'])) {
			$overlay_tab_text_orientation = sanitize_text_field(wp_unslash($_POST['overlay_tab_text_orientation']));
			$postdata['overlay_tab_text_orientation'] = in_array($overlay_tab_text_orientation, ['top-to-bottom', 'bottom-to-top'], true) ? $overlay_tab_text_orientation : 'top-to-bottom';
		}
		if (isset($_POST['overlay_backdrop_color'])) {
			$postdata['overlay_backdrop_color'] = sanitize_hex_color(wp_unslash($_POST['overlay_backdrop_color'])) ?: '#000000';
		}
		if (isset($_POST['overlay_backdrop_opacity'])) {
			$postdata['overlay_backdrop_opacity'] = max(0, min(100, absint(wp_unslash($_POST['overlay_backdrop_opacity']))));
		}
		if (isset($_POST['overlay_content_padding'])) {
			$postdata['overlay_content_padding'] = max(0, absint(wp_unslash($_POST['overlay_content_padding'])));
		}
		$dimension_fields = array(
			'button_padding',
			'content_padding',
			'call_to_action_padding',
			'overlay_button_padding',
			'overlay_button_margin',
			'overlay_content_margin',
		);
		foreach ($dimension_fields as $dimension_field) {
			if (isset($_POST[ $dimension_field ]) && is_array($_POST[ $dimension_field ])) {
				$postdata[ $dimension_field ] = $this->sanitize_dimension_input(wp_unslash($_POST[ $dimension_field ]));
			}
		}

		$numeric_fields = array(
			'cta_width',
			'cta_tablet_width',
			'cta_mobile_width',
			'cta_height',
			'cta_tablet_height',
			'cta_mobile_height',
			'cta_image_height',
			'cta_image_overlay_opacity',
			'button_round',
			'button_icon_size',
			'overlay_tab_corner_radius',
			'letter_spacing',
			'line_separator_thickness',
			'content_letter_spacing',
			'call_to_action_letter_spacing',
		);
		foreach ($numeric_fields as $numeric_field) {
			if (isset($_POST[ $numeric_field ])) {
				$postdata[ $numeric_field ] = max(0, absint(wp_unslash($_POST[ $numeric_field ])));
			}
		}

		$unit_fields = array(
			'cta_width_unit',
			'cta_tablet_width_unit',
			'cta_mobile_width_unit',
			'cta_height_unit',
			'cta_tablet_height_unit',
			'cta_mobile_height_unit',
		);
		foreach ($unit_fields as $unit_field) {
			if (isset($_POST[ $unit_field ])) {
				$unit = sanitize_text_field(wp_unslash($_POST[ $unit_field ]));
				$postdata[ $unit_field ] = in_array($unit, array('px', '%'), true) ? $unit : 'px';
			}
		}

		$yes_no_fields = array(
			'enable_cta_width',
			'enable_cta_height',
			'enable_image_overlay',
			'overlay_full_tab_height',
			'hide_cta_image',
			'hide_call_to_action',
			'call_to_action_button',
			'show_close_button',
			'close_button_edge',
			'enable_box_shadow',
		);
		foreach ($yes_no_fields as $yes_no_field) {
			if (isset($_POST[ $yes_no_field ])) {
				$postdata[ $yes_no_field ] = $this->sanitize_yes_no_input(wp_unslash($_POST[ $yes_no_field ]));
			}
		}

		if (isset($_POST['close_button_color'])) {
			$postdata['close_button_color'] = sanitize_hex_color(wp_unslash($_POST['close_button_color'])) ?: '';
		}
		if (isset($_POST['cta_image_overlay_color'])) {
			$postdata['cta_image_overlay_color'] = sanitize_hex_color(wp_unslash($_POST['cta_image_overlay_color'])) ?: '#000000';
		}
		if (isset($_POST['close_button_position'])) {
			$postdata['close_button_position'] = sanitize_text_field(wp_unslash($_POST['close_button_position']));
		}
		$horizontal_vertical_position = '';
		if (isset($_POST['horizontal_vertical_position'])) {
			$horizontal_vertical_position = sanitize_text_field(wp_unslash($_POST['horizontal_vertical_position']));
		} elseif (isset($_POST['horizontal_vertical_position_value'])) {
			$horizontal_vertical_position = sanitize_text_field(wp_unslash($_POST['horizontal_vertical_position_value']));
		}
		if ($horizontal_vertical_position !== '') {
			$cta_position = '';
			if (isset($_POST['SSuprydp_cta_position'])) {
				$cta_position = sanitize_text_field(wp_unslash($_POST['SSuprydp_cta_position']));
			}
			$postdata['horizontal_vertical_position'] = function_exists('easy_sticky_sidebar_normalize_secondary_position')
				? easy_sticky_sidebar_normalize_secondary_position($cta_position, $horizontal_vertical_position, 'center')
				: (in_array($horizontal_vertical_position, ['top', 'center', 'bottom'], true) ? $horizontal_vertical_position : 'center');
		}

		add_filter('wp_kses_allowed_html', [$this, 'content_filter'], 10, 2);
		$postdata['SSuprydp_content_option_text'] = wp_kses_stripslashes(wp_kses_post($_POST['SSuprydp_content_option_text'], wp_kses_allowed_html()));
		remove_filter('wp_kses_allowed_html', [$this, 'content_filter'], 2);

		$postdata['SSuprydp_content_option_text'] = apply_filters('easy_sticky_sidebar/cta_content', $postdata['SSuprydp_content_option_text'], $postdata);

		$switch_fields = apply_filters('easy_sticky_sidebar/switch_inputs', ['SSuprydp_target_blank', 'SSuprydp_nofollow', 'SSuprydp_shrink', 'SSuprydp_shrink_tablet', 'SSuprydp_shrink_mobile', 'SSuprydp_dis_desktop', 'SSuprydp_dis_tablet', 'SSuprydp_dis_mobile', 'SSuprydp_img_hideimg', 'SSuprydp_hideimg_tablet', 'SSuprydp_hideimg_mobile']);

		while ($switch = current($switch_fields)) {
			next($switch_fields);
			if (!isset($postdata[$switch])) {
				$postdata[$switch] = 'No';
			}
		}

		if (is_array($postdata)) {
			$postdata['created'] = get_the_date();

			// Normalize tab CTA defaults before persistence when users have not
			// provided their own custom tab copy/styles yet.
			if (($postdata['sidebar_template'] ?? '') === 'tab-cta') {
				$this->apply_tab_cta_defaults($postdata);
			}

			$sticky_id = $postdata['sticky_id'];
			unset($postdata['sticky_id']);

			$postdata['id'] = $sticky_id;
			$saved_id = easy_sticky_sidebar_insert($postdata);

			// Hard guarantee: persist CTA template explicitly in options table.
			// This prevents template reset on reload when mixed field sources exist.
			if (!empty($postdata['sidebar_template'])) {
				$persist_id = absint($saved_id ? $saved_id : $sticky_id);
				if ($persist_id > 0) {
					global $wpdb;
					// Remove any stale duplicates first.
					$wpdb->query(
						$wpdb->prepare(
							"DELETE FROM {$wpdb->prefix}sticky_cta_options WHERE sticky_cta_id = %d AND option_name = %s",
							$persist_id,
							'sidebar_template'
						)
					);

					$template_row = array(
						'sticky_cta_id' => $persist_id,
						'option_name' => 'sidebar_template',
						'option_value' => maybe_serialize((string) $postdata['sidebar_template']),
					);
					$template_format = array('%d', '%s', '%s');
					$wpdb->insert($wpdb->sticky_cta_options, $template_row, $template_format);
				}
			}

			wp_send_json(['status' => 'success', 'message' => 'Saved']);
		}

		wp_send_json(['status' => 'failed', 'message' => 'Data missing']);
	}

	/**
	 * Sanitize dimension control values.
	 *
	 * @since 2.4.3
	 *
	 * @param array $values Raw values.
	 * @return array
	 */
	private function sanitize_dimension_input($values) {
		if (!is_array($values)) {
			return array();
		}

		return array(
			'top'    => isset($values['top']) && '' !== trim((string) $values['top']) ? max(0, absint($values['top'])) : '',
			'right'  => isset($values['right']) && '' !== trim((string) $values['right']) ? max(0, absint($values['right'])) : '',
			'bottom' => isset($values['bottom']) && '' !== trim((string) $values['bottom']) ? max(0, absint($values['bottom'])) : '',
			'left'   => isset($values['left']) && '' !== trim((string) $values['left']) ? max(0, absint($values['left'])) : '',
			'unit'   => isset($values['unit']) && in_array($values['unit'], array('px', '%'), true) ? $values['unit'] : 'px',
		);
	}

	/**
	 * Sanitize shared yes/no values.
	 *
	 * @since 2.4.3
	 *
	 * @param string $value Raw value.
	 * @return string
	 */
	private function sanitize_yes_no_input($value) {
		return 'yes' === strtolower((string) $value) ? 'yes' : 'no';
	}

	/**
	 * Apply Tab CTA defaults when user has not saved custom tab styling/text yet.
	 *
	 * @param array<string,mixed> $postdata
	 * @return void
	 */
	private function apply_tab_cta_defaults(array &$postdata) {
		$text = isset($postdata['SSuprydp_button_option_text']) ? trim((string) $postdata['SSuprydp_button_option_text']) : '';
		$text_lc = strtolower($text);
		if ($text === '' || in_array($text_lc, ['click here', 'tab cta', 'have questions?'], true)) {
			$postdata['SSuprydp_button_option_text'] = 'Call Now';
		}

		$font = isset($postdata['SSuprydp_button_option_font']) ? trim((string) $postdata['SSuprydp_button_option_font']) : '';
		$font_lc = strtolower(str_replace('+', ' ', $font));
		if (
			$font === '' ||
			strpos($font_lc, 'open sans') !== false ||
			strpos($font_lc, 'archivo') !== false
		) {
			$postdata['SSuprydp_button_option_font'] = 'Arial';
		}

		$size_raw = isset($postdata['SSuprydp_button_option_size']) ? (string) $postdata['SSuprydp_button_option_size'] : '';
		$size_val = absint(preg_replace('/[^0-9.]/', '', $size_raw));
		if ($size_val <= 0 || in_array($size_val, [20, 24], true)) {
			$postdata['SSuprydp_button_option_size'] = '24';
		}

		$text_color = isset($postdata['SSuprydp_button_option_color']) ? strtolower(trim((string) $postdata['SSuprydp_button_option_color'])) : '';
		if ($text_color === '' || in_array($text_color, ['#fff', '#ffffff'], true)) {
			$postdata['SSuprydp_button_option_color'] = '#fff';
		}

		$background_color = isset($postdata['SSuprydp_button_option_backg_color']) ? strtolower(trim((string) $postdata['SSuprydp_button_option_backg_color'])) : '';
		if ($background_color === '' || in_array($background_color, ['#4e0d61', '#2466d5'], true)) {
			$postdata['SSuprydp_button_option_backg_color'] = '#218400';
		}
	}

	public function redirect_after_creating_new_sidebar($postdata, $sticky_id, $new) {
		if ($new && wp_doing_ajax()) {
			wp_send_json(['status' => 'success', 'message' => 'Saved', 'redirect' => add_query_arg(['page' => 'edit-easy-sticky-sidebar', 'id' => $sticky_id], admin_url('admin.php'))]);
		}
	}

	/**
	 * actions init ajaxCheck
	 */
	public function ajax_check() {
		global $wpdb;

		if (!check_ajax_referer('_nonce_easy_sticky_sidebar', '_wpnonce', false)) {
			wp_send_json_error(['message' => esc_html__('Security check failed.', 'easy-sticky-sidebar')], 403);
		}

		if (!current_user_can('manage_options')) {
			wp_send_json_error(['message' => esc_html__('Insufficient permissions.', 'easy-sticky-sidebar')], 403);
		}

		$sticky_id = isset($_POST['sticky_id']) ? absint($_POST['sticky_id']) : 0;
		$field = isset($_POST['fildname']) ? sanitize_key(wp_unslash($_POST['fildname'])) : '';
		$value_raw = isset($_POST['fieldval']) ? wp_unslash($_POST['fieldval']) : '';

		if ($sticky_id <= 0 || $field === '') {
			wp_send_json_error(['message' => esc_html__('Invalid request.', 'easy-sticky-sidebar')], 400);
		}

		$allowed_columns = array(
			'SSuprydp_development',
			'SSuprydp_shrink',
			'SSuprydp_shrink_tablet',
			'SSuprydp_shrink_mobile',
			'SSuprydp_dis_desktop',
			'SSuprydp_dis_tablet',
			'SSuprydp_dis_mobile',
			'SSuprydp_img_hideimg',
			'SSuprydp_hideimg_tablet',
			'SSuprydp_hideimg_mobile',
			'SSuprydp_target_blank',
			'SSuprydp_nofollow',
		);

		$allowed_columns = apply_filters('easy_sticky_sidebar_ajax_check_allowed_columns', $allowed_columns);

		if (!in_array($field, $allowed_columns, true)) {
			wp_send_json_error(['message' => esc_html__('Invalid field.', 'easy-sticky-sidebar')], 400);
		}

		$value = sanitize_text_field($value_raw);

		$updated = $wpdb->update(
			$wpdb->sticky_cta,
			array($field => $value),
			array('id' => $sticky_id),
			array('%s'),
			array('%d')
		);

		if (false === $updated) {
			wp_send_json_error(['message' => esc_html__('Update failed.', 'easy-sticky-sidebar')], 500);
		}

		wp_send_json_success(['message' => esc_html__('Updated.', 'easy-sticky-sidebar')]);
	}

	/**
	 * validate data
	 * @param array $postdata
	 * @return array
	 */
	public function validate_data() {

		$postdata = filter_input_array(INPUT_POST, FILTER_SANITIZE_SPECIAL_CHARS);

		$return = ['errors' => null, 'where' => null];
		$button_text = easy_sticky_sidebar()->engine->getValue('SSuprydp_button_option_text', $postdata, false);

		if (!$button_text) {
			$return['page_name'] = __("Please enter button text", "easy-sticky-sidebar");
			wp_send_json(['status' => 'failed', 'errors' => $return]);
		} else {
			$response['status'] = 'success';
			$response['content'] = 'Fields are added successfully.';
			wp_send_json($response);
		}
	}
	
	// Removed tracking methods - keeping analytics as pro features
}
