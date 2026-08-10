<?php
if (!defined('ABSPATH')) {
	exit;
}

/*
 * StickySidebar Actions
 * @package wp-dynamic-shortcodes/inc
 * @since   1.2.0
 */

class SSuprydpproActions {

	/**
	 * StickySidebar Constructor.
	 */
	function __construct() {
		$public_ajax_actions = array(
			'easy_sticky_sidebar_get_click',
		);

		foreach ($this->AjaxActions() as $key => $action) {
			add_action("wp_ajax_{$action['name']}", [$this, $action['callback']]);

			if (in_array($action['name'], $public_ajax_actions, true)) {
				add_action("wp_ajax_nopriv_{$action['name']}", [$this, $action['callback']]);
			}
		}

		// Fixed: Removed wp_ajax_nopriv_ hooks for security - only authenticated users can access these functions
		add_action('wp_ajax_update_cta_status', [$this, 'update_cta_status']);
		add_action('wp_ajax_change_sticky_sidebar_name', [$this, 'change_sticky_sidebar_name']);
		
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

		$sticky_id = $post_data['sticky_id'];

		$sticky = $wpdb->get_row($wpdb->prepare("SELECT * FROM $wpdb->sticky_cta WHERE id = %d", $sticky_id));
		if (!$sticky) {
			wp_send_json(['success' => false, 'error' => esc_html__('Sticky item not exists.', 'easy-sticky-sidebar')]);
		}

		$wpdb->update(
			$wpdb->sticky_cta,
			array('SSuprydp_development' => $post_data['status']),
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
     * SSuprydpStickySidebar ajax handlers
     *
     * @return Array
     */

	private function AjaxActions() {
		return [
			['name' => 'process_pages', 'callback' => 'processPages'],
			['name' => 'ajax_check', 'callback' => 'ajaxCheck'],
			['name' => 'validate_data', 'callback' => 'validateData'],
			['name' => 'easy_sticky_sidebar_get_click', 'callback' => 'easyStickySidebarGetClick'],
		];
	}

	/**
	 * Track CTA click on frontend.
	 * Clicks/CTR are now available in free plugin stats.
	 *
	 * @return void
	 */
	public function easyStickySidebarGetClick() {
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
	public function processPages() {
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

		// Ensure button_icon is captured (free feature now).
		if (isset($_POST['button_icon'])) {
			$postdata['button_icon'] = sanitize_text_field(wp_unslash($_POST['button_icon']));
		}

		if (!has_wordpress_cta_pro() && $sticky_id === 0) {
			global $wpdb;
			$cta_count = (int) $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}sticky_cta");
			if ($cta_count >= 3) {
				wp_send_json([
					'status' => 'failed',
					'message' => __('Only 3 CTAs are allowed in free version. Please upgrade to Pro to build more CTAs.', 'easy-sticky-sidebar')
				]);
			}
		}

		add_filter('wp_kses_allowed_html', [$this, 'content_filter'], 10, 2);
		$postdata['SSuprydp_content_option_text'] = wp_kses_stripslashes(wp_kses_post($_POST['SSuprydp_content_option_text'], wp_kses_allowed_html()));
		remove_filter('wp_kses_allowed_html', [$this, 'content_filter'], 2);

		$postdata['SSuprydp_content_option_text'] = apply_filters('wordpress_cta_free/cta_content', $postdata['SSuprydp_content_option_text'], $postdata);

		$switch_fields = apply_filters('wordpress_cta_free/swtich_inputs', ['SSuprydp_target_blank', 'SSuprydp_nofollow', 'SSuprydp_shrink', 'SSuprydp_shrink_tablet', 'SSuprydp_shrink_mobile', 'SSuprydp_dis_desktop', 'SSuprydp_dis_tablet', 'SSuprydp_dis_mobile', 'SSuprydp_img_hideimg', 'SSuprydp_hideimg_tablet', 'SSuprydp_hideimg_mobile']);

		while ($switch = current($switch_fields)) {
			next($switch_fields);
			if (!isset($postdata[$switch])) {
				$postdata[$switch] = 'No';
			}
		}

		if (is_array($postdata)) {
			$postdata['created'] = get_the_date();

			$sticky_id = $postdata['sticky_id'];
			unset($postdata['sticky_id']);

			$postdata['id'] = $sticky_id;
			easy_sticky_sidebar_insert($postdata);

			wp_send_json(['status' => 'success', 'message' => 'Saved']);
		}

		wp_send_json(['status' => 'failed', 'message' => 'Data missing']);
	}

	public function redirect_after_creating_new_sidebar($postdata, $sticky_id, $new) {
		if ($new && wp_doing_ajax()) {
			wp_send_json(['status' => 'success', 'message' => 'Saved', 'redirect' => add_query_arg(['page' => 'edit-easy-sticky-sidebar', 'id' => $sticky_id], admin_url('admin.php'))]);
		}
	}

	/**
	 * actions init ajaxCheck
	 */
	public function ajaxCheck() {
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
	public function validateData() {

		$postdata = filter_input_array(INPUT_POST, FILTER_SANITIZE_SPECIAL_CHARS);

		$return = ['errors' => null, 'where' => null];
		$button_text = SSuprydpStickySidebar()->engine->getValue('SSuprydp_button_option_text', $postdata, false);

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
