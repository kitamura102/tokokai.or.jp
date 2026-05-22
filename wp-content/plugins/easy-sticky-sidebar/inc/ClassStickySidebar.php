<?php
if (!defined('ABSPATH')) {
	exit;
}

/*
 * StickySidebar Main class
 * @package sticky-sidebar/inc
 * @since   1.2.0
 */

class Easy_Sticky_Sidebar
{
	/**
	 * The single instance of the class.
	 *
	 * @var StickySidebar
	 * @since 1.2.0
	 */
	protected static $_instance = null;

	/**
	 * StickySidebar core functions
	 *
	 * @var engine
	 * @since 1.2.0
	 */
	public $engine;

	/**
	 * Main StickySidebar Instance.
	 *
	 * Ensures only one instance of IsLayouts is loaded or can be loaded.
	 *
	 * @since 1.2.0
	 * @static
	 * @return StickySidebar.
	 */
	public static function instance()
	{
		if (is_null(self::$_instance)) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	/**
	 * Pro options
	 * @since 1.4.5
	 */
	var $options_pro = null;

	/**
	 * StickySidebar Constructor.
	 *
	 * @global Array StickySidebar
	 *
	 */
	function __construct()
	{
		$this->add_data_table();

		require_once EASY_STICKY_SIDEBAR_PLUGIN_DIR . '/inc/helpers.php';
		require_once EASY_STICKY_SIDEBAR_PLUGIN_DIR . '/inc/utils.php';
		require_once EASY_STICKY_SIDEBAR_PLUGIN_DIR . '/inc/EditorDefaults.php';
		require_once EASY_STICKY_SIDEBAR_PLUGIN_DIR . '/inc/template-filters.php';
		include_once EASY_STICKY_SIDEBAR_PLUGIN_DIR . '/inc/sticky-cta-data.php';
		require_once EASY_STICKY_SIDEBAR_PLUGIN_DIR . '/inc/ClassMigrate.php';
		require_once EASY_STICKY_SIDEBAR_PLUGIN_DIR . '/inc/ClassIconsLibrary.php';
		require_once EASY_STICKY_SIDEBAR_PLUGIN_DIR . '/inc/DesignTemplates.php';
		include_once EASY_STICKY_SIDEBAR_PLUGIN_DIR . '/inc/ClassStickySidebarCore.php';
		include_once EASY_STICKY_SIDEBAR_PLUGIN_DIR . '/inc/ContentTab.php';
		include_once EASY_STICKY_SIDEBAR_PLUGIN_DIR . '/inc/FloatingButtons.php';
		include_once EASY_STICKY_SIDEBAR_PLUGIN_DIR . '/inc/ClassGenerateStyle.php';

		include_once EASY_STICKY_SIDEBAR_PLUGIN_DIR . '/inc/ClassQuery.php';
		include_once EASY_STICKY_SIDEBAR_PLUGIN_DIR . '/inc/ClassActions.php';

		if (is_admin()) {
			include_once EASY_STICKY_SIDEBAR_PLUGIN_DIR . '/inc/ProFields.php';
			include_once EASY_STICKY_SIDEBAR_PLUGIN_DIR . '/inc/sticky-form-fields.php';
			include_once EASY_STICKY_SIDEBAR_PLUGIN_DIR . '/inc/ClassAdminOptions.php';
		}

		add_action('init', [$this, 'init'], 2);

		register_activation_hook(EASY_STICKY_SIDEBAR_PLUGIN_FILE, array($this, 'plugin_install'));

		add_action('admin_notices', [$this, 'wp_cta_pro_upgrade_notice']);
		add_action('admin_notices', [$this, 'new_features_notice']);
		add_action('wp_ajax_easy_sticky_sidebar_dismiss_new_features_notice', [$this, 'dismiss_new_features_notice']);
	}

	/**
	 * Add table at $wpdb object
	 * @since 1.3.6
	 */
	public function add_data_table()
	{
		global $wpdb;
		$wpdb->sticky_cta = $wpdb->prefix . 'sticky_cta';
		$wpdb->sticky_cta_options = $wpdb->prefix . 'sticky_cta_options';
	}

	/**
	 * Init
	 * @since 1.4.5
	 */
	public function init()
	{
		$this->cleanup_pro_settings_if_needed();

		$GLOBALS['CTA_Query'] = new Easy_Sticky_Sidebar_Query();

		new Easy_Sticky_Sidebar_Actions();
		new Easy_Sticky_Sidebar_Generate_CSS();
		new Easy_Sticky_Sidebar_Floating_Buttons();

		if (is_admin()) {
			new Easy_Sticky_Sidebar_Options();
			new Easy_Sticky_Sidebar_Content_Tab();
			new Easy_Sticky_Sidebar_Pro_Placeholder();
			new Easy_Sticky_Sidebar_Icons_Library();
		}

		new Easy_Sticky_Sidebar_Template_Filters();

		do_action('easy_sticky_sidebar/init');

		if (is_admin()) {
			$pro_fields = apply_filters('easy_sticky_sidebar/pro_fields', []);
			foreach ($pro_fields as $key => $action) {
				$action = wp_parse_args($action, array('hook' => '', 'callback' => null, 'priority' => 10, 'args' => 1));
				if (!is_null($action['callback'])) {
					add_action($action['hook'], $action['callback'], $action['priority'], $action['args']);
				}
			}
		}

		/* register front end scripts */
		add_action('wp_enqueue_scripts', array($this, 'enqueue_frontend_scripts'), 0);

		/* register admin scripts */
		add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_scripts'), 0);
		add_action('wp_footer', [$this, 'stick_sidebar_content']);


		$this->engine = new Easy_Sticky_Sidebar_Core();

		add_filter('plugin_row_meta', [$this, 'add_help_link_on_plugin_page'], 12, 2);

		$fontawesome_css_path = EASY_STICKY_SIDEBAR_PLUGIN_DIR . '/assets/css/fontawesome.css';
		$fontawesome_css_ver = file_exists($fontawesome_css_path) ? (string) filemtime($fontawesome_css_path) : EASY_STICKY_SIDEBAR_VERSION;
		wp_register_style('fontawesome', EASY_STICKY_SIDEBAR_PLUGIN_URL . '/assets/css/fontawesome.css', array(), $fontawesome_css_ver);

		$jquery_cookie_js_path = EASY_STICKY_SIDEBAR_PLUGIN_DIR . '/assets/js/jquery.cookie.js';
		$jquery_cookie_js_ver = file_exists($jquery_cookie_js_path) ? (string) filemtime($jquery_cookie_js_path) : EASY_STICKY_SIDEBAR_VERSION;
		wp_register_script('jquery-cookie', EASY_STICKY_SIDEBAR_PLUGIN_URL . '/assets/js/jquery.cookie.js', ['jquery'], $jquery_cookie_js_ver, true);

		Easy_Sticky_Sidebar_Migrate::migrate();

		$this->migrate_data();
	}

	public function add_help_link_on_plugin_page($links, $plugin_file_name)
	{
		if (EASY_STICKY_SIDEBAR_PLUGIN_BASENAME != $plugin_file_name) {
			return $links;
		}

		$links[] = '<a href="https://wpctapro.com/help" target="_blank">' . __('Help', 'easy-sticky-sidebar') . '</a>';
		return $links;
	}

	/**
	 * Check if user using old version of CTA pro
	 */
	public function wp_cta_pro_upgrade_notice()
	{
		if (!defined('WORDPRESS_CTA_PRO_VERSION') || version_compare(WORDPRESS_CTA_PRO_VERSION, '1.1.3', '<=') === false) {
			return;
		}
?>
<div class="notice notice-warning is-dismissible">
    <p><?php esc_html_e('Please download WP CTA Pro from our website and upgrade!', 'easy-sticky-sidebar'); ?></p>
</div>
<?php
	}

	/**
	 * Show one-time new feature notification to admins.
	 *
	 * @since 2.2.0
	 */
	public function new_features_notice()
	{
		if (function_exists('easy_sticky_sidebar_render_update_notice')) {
			easy_sticky_sidebar_render_update_notice('admin');
			return;
		}

		if (!current_user_can('manage_options')) {
			return;
		}

		$notice_id = 'wpcta_220_new_features';
		$meta_key = '_easy_sticky_sidebar_dismissed_' . $notice_id;
		if ('yes' === get_user_meta(get_current_user_id(), $meta_key, true)) {
			return;
		}

		$ajax_url = admin_url('admin-ajax.php');
		$nonce = wp_create_nonce('easy_sticky_sidebar_dismiss_wpcta_220_new_features');
		$dashboard_url = admin_url('admin.php?page=easy-sticky-sidebars');
		?>
		<div class="notice notice-info is-dismissible easy-sticky-sidebar-new-features-notice" data-notice-id="<?php echo esc_attr($notice_id); ?>" data-nonce="<?php echo esc_attr($nonce); ?>">
			<p>
				<strong><?php esc_html_e('🚀 WP CTA 2.2.2 is now live', 'easy-sticky-sidebar'); ?></strong>
			</p>
			<p>
				<?php esc_html_e('Enjoy an easier, more user-friendly dashboard with improved placement controls and a smoother, streamlined CTA-building process.', 'easy-sticky-sidebar'); ?>
			</p>
			<p>
				<a class="button button-primary" href="<?php echo esc_url($dashboard_url); ?>"><?php esc_html_e('Open WP CTA', 'easy-sticky-sidebar'); ?></a>
			</p>
		</div>
		<script>
			(function () {
				const notice = document.querySelector('.easy-sticky-sidebar-new-features-notice[data-notice-id="<?php echo esc_js($notice_id); ?>"]');
				if (!notice) {
					return;
				}

				notice.addEventListener('click', function (event) {
					if (!event.target || !event.target.classList.contains('notice-dismiss')) {
						return;
					}

					const data = new FormData();
					data.append('action', 'easy_sticky_sidebar_dismiss_new_features_notice');
					data.append('notice_id', notice.getAttribute('data-notice-id') || '');
					data.append('nonce', notice.getAttribute('data-nonce') || '');

					window.fetch('<?php echo esc_url($ajax_url); ?>', {
						method: 'POST',
						credentials: 'same-origin',
						body: data
					});
				});
			}());
		</script>
		<?php
	}

	/**
	 * Persist one-time new feature notice dismissal per user.
	 *
	 * @since 2.2.0
	 */
	public function dismiss_new_features_notice()
	{
		if (!current_user_can('manage_options')) {
			wp_send_json_error(array('message' => __('Permission denied.', 'easy-sticky-sidebar')), 403);
		}

		$notice_id = isset($_POST['notice_id']) ? sanitize_key(wp_unslash($_POST['notice_id'])) : '';
		$expected_notice_id = function_exists('easy_sticky_sidebar_get_update_notice_id')
			? easy_sticky_sidebar_get_update_notice_id()
			: 'wpcta_230_major_update';

		if ($expected_notice_id === $notice_id) {
			if (!check_ajax_referer('easy_sticky_sidebar_dismiss_' . $expected_notice_id, 'nonce', false)) {
				wp_send_json_error(array('message' => __('Security check failed.', 'easy-sticky-sidebar')), 403);
			}

			update_user_meta(get_current_user_id(), '_easy_sticky_sidebar_dismissed_' . $notice_id, 'yes');
			wp_send_json_success();
		}

		if (!check_ajax_referer('easy_sticky_sidebar_dismiss_wpcta_220_new_features', 'nonce', false)) {
			wp_send_json_error(array('message' => __('Security check failed.', 'easy-sticky-sidebar')), 403);
		}

		$notice_id = isset($_POST['notice_id']) ? sanitize_key(wp_unslash($_POST['notice_id'])) : '';
		if ('wpcta_220_new_features' !== $notice_id) {
			wp_send_json_error(array('message' => __('Invalid notice.', 'easy-sticky-sidebar')), 400);
		}

		update_user_meta(get_current_user_id(), '_easy_sticky_sidebar_dismissed_' . $notice_id, 'yes');
		wp_send_json_success();
	}

	/**
	 * sticky sidebar hook run add installation
	 **/
	public function plugin_install()
	{
		$current_version = get_option('easy_sticky_sidebar_version', EASY_STICKY_SIDEBAR_VERSION);

		global $wpdb;


		$default_attachment = get_option('easy_sticky_sidebar_default_attachment');
		$attchment_url = wp_get_attachment_image_url($default_attachment);

		if (!$attchment_url) {
			self::copy_media_files();

			$SSuprydp_dtime = gmdate("Y-m-d H:i:s");

			$currentpath = wp_get_upload_dir();

			$img_path = $currentpath['url'] . '/ss_dummy.jpg';

			$attac_lastid = wp_insert_post(array(
				'post_date' => $SSuprydp_dtime,
				'post_author' => '1',
				'post_title' => 'Sticky Sidebar',
				'post_status' => 'inherit',
				'comment_status' => 'open',
				'ping_status' => 'open',
				'post_name' => 'Sticky Sidebar',
				'post_parent' => '0',
				'guid' => $img_path,
				'post_type' => 'attachment',
				'post_mime_type' => 'image/jpeg',
			));

			update_option('easy_sticky_sidebar_default_attachment', $attac_lastid);

			$SSuprydp_wp_attached_file = gmdate('Y') . '/' . gmdate('m') . '/ss_dummy.jpg';
			update_post_meta($attac_lastid, '_wp_attached_file', $SSuprydp_wp_attached_file);

			$SSuprydp_wp_attachment_metadata = self::get_media_metadata();
			update_post_meta($attac_lastid, '_wp_attachment_metadata', $SSuprydp_wp_attachment_metadata);
		}

		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

		$table_name = $wpdb->prefix . 'sticky_cta';
		$charset_collate = $wpdb->get_charset_collate();

		if ($wpdb->get_var("show tables like '$table_name'") != $table_name) {
			$sql = "CREATE TABLE $table_name (
			  id int(11) NOT NULL AUTO_INCREMENT,
			  SSuprydp_impressions int(11) DEFAULT 0 NOT NULL,
			  SSuprydp_clicks int(11) DEFAULT 0 NOT NULL,
			  SSuprydp_development varchar(255) DEFAULT 'development' NOT NULL,
			  SSuprydp_shrink varchar(255) DEFAULT 'No' NOT NULL,
			  SSuprydp_shrink_tablet varchar(255) DEFAULT 'No' NOT NULL,
			  SSuprydp_shrink_mobile varchar(255) DEFAULT 'No' NOT NULL,
			  SSuprydp_dis_desktop varchar(255) DEFAULT 'Yes' NOT NULL,
			  SSuprydp_dis_tablet varchar(255) DEFAULT 'Yes' NOT NULL,
			  SSuprydp_dis_mobile varchar(255) DEFAULT 'No' NOT NULL,
			  SSuprydp_location varchar(255) DEFAULT '' NOT NULL,
			  SSuprydp_img_hideimg varchar(255) DEFAULT 'No' NOT NULL,
			  SSuprydp_hideimg_tablet varchar(255) DEFAULT 'No' NOT NULL,
			  SSuprydp_hideimg_mobile varchar(255) DEFAULT 'No' NOT NULL,
			  sticky_s_media varchar(255) DEFAULT '$img_path' NOT NULL,
			  image_attachment_id varchar(255) DEFAULT '$attac_lastid' NOT NULL,
			  SSuprydp_button_option_text varchar(255) DEFAULT 'Click Here' NOT NULL,
			  SSuprydp_button_option_backg_color varchar(255) DEFAULT '#4e0d61' NOT NULL,
			  SSuprydp_button_option_font varchar(255) DEFAULT 'Open Sans' NOT NULL,
			  SSuprydp_button_option_weight varchar(255) DEFAULT '400' NOT NULL,
			  SSuprydp_button_option_size varchar(255) DEFAULT '20px' NOT NULL,
			  SSuprydp_button_option_align varchar(255) DEFAULT 'top' NOT NULL,
			  SSuprydp_button_option_color varchar(255) DEFAULT '#ffffff' NOT NULL,
			  SSuprydp_content_option_text LONGTEXT NOT NULL,
			  SSuprydp_content_option_font varchar(255) DEFAULT 'Open Sans' NOT NULL,
			  SSuprydp_content_option_weight varchar(255) DEFAULT '800' NOT NULL,
			  SSuprydp_content_option_size varchar(255) DEFAULT '25px' NOT NULL,
			  SSuprydp_content_option_color varchar(255) DEFAULT '#000000' NOT NULL,
			  SSuprydp_divider_option_color varchar(255) DEFAULT '#1b7ccc' NOT NULL,
			  SSuprydp_action_option_text varchar(255) DEFAULT 'Click Here to View' NOT NULL,
			  SSuprydp_action_option_font varchar(255) DEFAULT 'Open Sans' NOT NULL,
			  SSuprydp_action_option_weight varchar(255) DEFAULT '500' NOT NULL,
			  SSuprydp_action_option_size varchar(255) DEFAULT '19px' NOT NULL,
			  SSuprydp_action_option_color varchar(255) DEFAULT '#000000' NOT NULL,
			  SSuprydp_action_option_url varchar(255) DEFAULT 'https://wpctapro.com/' NOT NULL,
			  SSuprydp_target_blank varchar(255) DEFAULT 'No' NOT NULL,
			  SSuprydp_nofollow varchar(255) DEFAULT 'No' NOT NULL,
			  SSuprydp_cta_position varchar(255) DEFAULT 'right' NOT NULL,
			  created datetime NULL,
			  PRIMARY KEY  (id)
			) $charset_collate;";

			dbDelta($sql);
		}

		$table_name = $wpdb->prefix . 'sticky_cta_options';
		maybe_create_table($table_name, "CREATE TABLE $table_name (
			`ID` INT NOT NULL AUTO_INCREMENT , 
			`sticky_cta_id` INT NOT NULL , 
			`option_name` VARCHAR(100) NOT NULL, 
			`option_value` MEDIUMTEXT, 
			PRIMARY KEY (`ID`))
		");

		update_option('easy_sticky_sidebar_version', EASY_STICKY_SIDEBAR_VERSION);

	}

	/**
	 * Migrate data
	 * @since 1.3.1
	 */
	function migrate_data()
	{
		if (!is_admin() || get_option('easy_sticky_sidebar_migrated')) {
			return;
		}

		global $wpdb;

		$table_name = $wpdb->sticky_cta;
		if ($wpdb->get_var("show tables like '$table_name'") != $table_name) {
			$this->SSuprydp_plugin_install();
		}

		if (!get_option('SSuprydp_development')) {
			return;
		}

		$SSuprydp_location = get_option('SSuprydp_location');
		if (!$SSuprydp_location) {
			$SSuprydp_location = get_option('page_on_front');
		}

		if ($SSuprydp_location == 0) {
			$SSuprydp_location = '';
		}

		$sticky_cta = [
			'SSuprydp_development' => get_option('SSuprydp_development', 'development'),
			'sticky_s_media' => wp_get_attachment_image_url(get_option('easy_sticky_sidebar_default_attachment'), 'full'),
			'SSuprydp_button_option_text' => get_option('SSuprydp_button_option_text', 'Click Here'),
			'SSuprydp_button_option_backg_color' => get_option('SSuprydp_button_option_backg_color', '#4e0d61'),
			'SSuprydp_button_option_font' => get_option('SSuprydp_button_option_font', 'Open Sans'),
			'SSuprydp_button_option_weight' => get_option('SSuprydp_button_option_weight', '400'),
			'SSuprydp_button_option_size' => get_option('SSuprydp_button_option_size', '20px'),
			'SSuprydp_button_option_align' => get_option('SSuprydp_button_option_align', 'top'),
			'SSuprydp_button_option_color' => get_option('SSuprydp_button_option_color', '#ffffff'),
			'SSuprydp_content_option_text' => get_option('SSuprydp_content_option_text', 'This is the Content Area. Put a description here of what you want to promote.'),
			'SSuprydp_content_option_font' => get_option('SSuprydp_content_option_font', 'Open Sans'),
			'SSuprydp_content_option_weight' => get_option('SSuprydp_content_option_weight', '800'),
			'SSuprydp_content_option_size' => get_option('SSuprydp_content_option_size', '25px'),
			'SSuprydp_content_option_color' => get_option('SSuprydp_content_option_color', '#000000'),
			'SSuprydp_divider_option_color' => get_option('SSuprydp_divider_option_color', '#1b7ccc'),
			'SSuprydp_action_option_text' => get_option('SSuprydp_action_option_text', 'Click Here to View'),
			'SSuprydp_action_option_font' => get_option('SSuprydp_action_option_font', 'Open Sans'),
			'SSuprydp_action_option_weight' => get_option('SSuprydp_action_option_weight', '500'),
			'SSuprydp_action_option_size' => get_option('SSuprydp_action_option_size', '19px'),
			'SSuprydp_action_option_color' => get_option('SSuprydp_action_option_color'),
			'SSuprydp_action_option_url' => get_option('SSuprydp_action_option_url', 'https://wpctapro.com/'),
			'SSuprydp_dis_mobile' => get_option('SSuprydp_dis_mobile', 'Yes'),
			'SSuprydp_location' => $SSuprydp_location,
			'SSuprydp_target_blank' => get_option('SSuprydp_target_blank', 'No'),
			'SSuprydp_nofollow' => get_option('SSuprydp_nofollow', 'No'),
			'SSuprydp_shrink' => get_option('SSuprydp_shrink', 'No'),
			'SSuprydp_img_hideimg' => get_option('SSuprydp_img_hideimg', 'No'),
			'sidebar_name' => 'Your First CTA',
			'sidebar_template' => 'sticky-cta',
			'horizontal_vertical_position' => 'center',
			'content_background_color' => 'transparent',
			'link_text_background' => 'transparent',
			'line_separator_color' => '#1b7ccc'
		];

		easy_sticky_sidebar_insert($sticky_cta);

		update_option('easy_sticky_sidebar_migrated', true);
	}

	/**
	 * register and enque front end styles and scripts.
	 *
	 * @since 1.2.0
	 */
	public function enqueue_frontend_scripts()
	{
		$frontend_css_path = EASY_STICKY_SIDEBAR_PLUGIN_DIR . '/assets/css/sticky-sidebar.css';
		$frontend_css_ver = file_exists($frontend_css_path) ? (string) filemtime($frontend_css_path) : EASY_STICKY_SIDEBAR_VERSION;
		wp_enqueue_style('SSuprydp_style', EASY_STICKY_SIDEBAR_PLUGIN_URL . '/assets/css/sticky-sidebar.css', array('fontawesome'), $frontend_css_ver);

		$upload_dir = wp_upload_dir();
		$generated_css = $upload_dir['basedir'] . '/sticky-sidebar-generated.css';
		if (file_exists($generated_css)) {
			wp_enqueue_style('sticky-sidebar-generated', $upload_dir['baseurl'] . '/sticky-sidebar-generated.css', [], filemtime($generated_css));
		}

		$frontend_js_path = EASY_STICKY_SIDEBAR_PLUGIN_DIR . '/assets/js/sticky-sidebar.js';
		$frontend_js_ver = file_exists($frontend_js_path) ? (string) filemtime($frontend_js_path) : EASY_STICKY_SIDEBAR_VERSION;
		wp_enqueue_script('SSuprydp_script', EASY_STICKY_SIDEBAR_PLUGIN_URL . "/assets/js/sticky-sidebar.js", array('jquery'), $frontend_js_ver, true);
		wp_localize_script('SSuprydp_script', 'easy_sticky_sidebar_front', [
			'ajax_url' => admin_url('admin-ajax.php'),
			'nonce'    => wp_create_nonce('easy_sticky_sidebar_front_nonce'),
		]);
	}

	public function enqueue_admin_scripts()
	{
		$global_css_path = EASY_STICKY_SIDEBAR_PLUGIN_DIR . '/assets/css/easy-sidebar-global.css';
		$global_css_ver = file_exists($global_css_path) ? (string) filemtime($global_css_path) : EASY_STICKY_SIDEBAR_VERSION;
		wp_enqueue_style('easy-sidebar-global', EASY_STICKY_SIDEBAR_PLUGIN_URL . '/assets/css/easy-sidebar-global.css', [], $global_css_ver);
		if (!is_easy_sticky_sidebar_screen()) {
			return;
		}

		$preview_css_path = EASY_STICKY_SIDEBAR_PLUGIN_DIR . '/assets/css/sticky-sidebar.css';
		$preview_css_ver = file_exists($preview_css_path) ? (string) filemtime($preview_css_path) : EASY_STICKY_SIDEBAR_VERSION;
		wp_enqueue_style('easy-sticky-sidebar-preview', EASY_STICKY_SIDEBAR_PLUGIN_URL . '/assets/css/sticky-sidebar.css', ['fontawesome'], $preview_css_ver);

		wp_deregister_script('gform_tooltip_init');

		$select2_css_path = EASY_STICKY_SIDEBAR_PLUGIN_DIR . '/assets/css/select2.min.css';
		$select2_css_ver = file_exists($select2_css_path) ? (string) filemtime($select2_css_path) : EASY_STICKY_SIDEBAR_VERSION;
		$select2_js_path = EASY_STICKY_SIDEBAR_PLUGIN_DIR . '/assets/js/select2.min.js';
		$select2_js_ver = file_exists($select2_js_path) ? (string) filemtime($select2_js_path) : EASY_STICKY_SIDEBAR_VERSION;
		wp_register_style('select2', EASY_STICKY_SIDEBAR_PLUGIN_URL . '/assets/css/select2.min.css', [], $select2_css_ver);
		wp_register_script('select2', EASY_STICKY_SIDEBAR_PLUGIN_URL . '/assets/js/select2.min.js', ['jquery'], $select2_js_ver, true);

		wp_enqueue_style('wp-color-picker');

		wp_enqueue_style(
			'easy-sticky-sidebar-admin',
			EASY_STICKY_SIDEBAR_PLUGIN_URL . '/assets/css/admin.css',
			array(),
			file_exists(EASY_STICKY_SIDEBAR_PLUGIN_DIR . '/assets/css/admin.css') ? (string) filemtime(EASY_STICKY_SIDEBAR_PLUGIN_DIR . '/assets/css/admin.css') : EASY_STICKY_SIDEBAR_VERSION
		);

		$fontselect_js_path = EASY_STICKY_SIDEBAR_PLUGIN_DIR . '/assets/js/jquery.fontselect.js';
		$fontselect_js_ver = file_exists($fontselect_js_path) ? (string) filemtime($fontselect_js_path) : EASY_STICKY_SIDEBAR_VERSION;
		wp_enqueue_script('jquery-fontselect-js', EASY_STICKY_SIDEBAR_PLUGIN_URL . '/assets/js/jquery.fontselect.js', [], $fontselect_js_ver);

		$admin_js_path = EASY_STICKY_SIDEBAR_PLUGIN_DIR . '/assets/js/sticky-sidebar-admin.js';
		$admin_js_ver = file_exists($admin_js_path) ? (string) filemtime($admin_js_path) : EASY_STICKY_SIDEBAR_VERSION;
		wp_enqueue_script('SSuprydp_admin_script', EASY_STICKY_SIDEBAR_PLUGIN_URL . '/assets/js/sticky-sidebar-admin.js', ['wp-color-picker', 'jquery-cookie'], $admin_js_ver, true);
		wp_localize_script('SSuprydp_admin_script', 'sticky_sidebar', [
			'ajax_url' => admin_url('admin-ajax.php'),
			'nonce' => wp_create_nonce('easy_sticky_sidebar_nonce'),
			'plugin_url' => EASY_STICKY_SIDEBAR_PLUGIN_URL,
			'editor_defaults' => function_exists('easy_sticky_sidebar_get_editor_default_templates') ? easy_sticky_sidebar_get_editor_default_templates() : [],
			'design_templates' => function_exists('easy_sticky_sidebar_get_design_templates') ? easy_sticky_sidebar_get_design_templates() : [],
		]);

		$popper_js_path = EASY_STICKY_SIDEBAR_PLUGIN_DIR . '/assets/js/popper.min.js';
		$popper_js_ver = file_exists($popper_js_path) ? (string) filemtime($popper_js_path) : EASY_STICKY_SIDEBAR_VERSION;
		wp_enqueue_script('SSuprydp_popper', EASY_STICKY_SIDEBAR_PLUGIN_URL . '/assets/js/popper.min.js', [], $popper_js_ver);

		$bootstrap_js_path = EASY_STICKY_SIDEBAR_PLUGIN_DIR . '/assets/js/bootstrap.min.js';
		$bootstrap_js_ver = file_exists($bootstrap_js_path) ? (string) filemtime($bootstrap_js_path) : EASY_STICKY_SIDEBAR_VERSION;
		wp_enqueue_script('SSuprydp_bootstrap', EASY_STICKY_SIDEBAR_PLUGIN_URL . '/assets/js/bootstrap.min.js', [], $bootstrap_js_ver);

		$fontselect_css_path = EASY_STICKY_SIDEBAR_PLUGIN_DIR . '/assets/css/fontselect-default.css';
		$fontselect_css_ver = file_exists($fontselect_css_path) ? (string) filemtime($fontselect_css_path) : EASY_STICKY_SIDEBAR_VERSION;
		wp_enqueue_style('fontselect-default', EASY_STICKY_SIDEBAR_PLUGIN_URL . '/assets/css/fontselect-default.css', [], $fontselect_css_ver);

		$admin_base_css_path = EASY_STICKY_SIDEBAR_PLUGIN_DIR . '/assets/css/sticky-sidebar-admin-base.css';
		$admin_base_css_ver = file_exists($admin_base_css_path) ? (string) filemtime($admin_base_css_path) : EASY_STICKY_SIDEBAR_VERSION;
		wp_enqueue_style('SSuprydp_admin_style', EASY_STICKY_SIDEBAR_PLUGIN_URL . '/assets/css/sticky-sidebar-admin-base.css', ['fontawesome'], $admin_base_css_ver);

		$current_page = isset($_GET['page']) ? sanitize_text_field(wp_unslash($_GET['page'])) : '';
		if (in_array($current_page, ['add-easy-sticky-sidebar', 'edit-easy-sticky-sidebar'], true)) {
			$admin_builder_css_path = EASY_STICKY_SIDEBAR_PLUGIN_DIR . '/assets/css/sticky-sidebar-admin-builder.css';
			$admin_builder_css_ver = file_exists($admin_builder_css_path) ? (string) filemtime($admin_builder_css_path) : EASY_STICKY_SIDEBAR_VERSION;
			wp_enqueue_style('SSuprydp_admin_style_builder', EASY_STICKY_SIDEBAR_PLUGIN_URL . '/assets/css/sticky-sidebar-admin-builder.css', ['SSuprydp_admin_style'], $admin_builder_css_ver);
		}

		//wp_enqueue_style('SSuprydp_bootstrap', EASY_STICKY_SIDEBAR_PLUGIN_URL . '/assets/css/bootstrap.min.css', [], EASY_STICKY_SIDEBAR_VERSION);

		if (!did_action('wp_enqueue_media')) {
			wp_enqueue_media();
		}
	}

	public function stick_sidebar_content()
	{
		global $CTA_Query;

		$dataview = array();

		foreach ($CTA_Query->cta as $sticky_data) {
			$dataview['ctacontent'] = $sticky_data;
			$SSuprydp_development = $sticky_data->SSuprydp_development;


			$dataview['cta_classes'] = ['easy-sticky-sidebar', 'easy-sticky-sidebar-' . $sticky_data->__get('id'), $sticky_data->sidebar_template];
			$dataview['cta_classes'][] = 'easy-sticky-sidebar-' . $sticky_data->sidebar_template;

			if ($sticky_data->sidebar_template === 'sticky-cta') {
				$dataview['cta_classes'][] = 'sticky-cta';
			}

			if ($sticky_data->SSuprydp_dis_desktop == 'Yes') {
				$dataview['cta_classes'][] = 'sticky-sidebar-desktop';
			}

			if ($sticky_data->SSuprydp_dis_tablet == 'Yes') {
				$dataview['cta_classes'][] = 'sticky-sidebar-tablet';
			}

			if ($sticky_data->SSuprydp_dis_mobile == 'Yes') {
				$dataview['cta_classes'][] = 'sticky-sidebar-mobile';
			}

			$dataview['cta_classes'][] = 'sticky-cta-position-' . $sticky_data->SSuprydp_cta_position;

			if ($sticky_data->SSuprydp_cta_position == 'top' || $sticky_data->SSuprydp_cta_position == 'bottom') {
				$dataview['cta_classes'][] = 'vertical-cta';
				$dataview['cta_classes'][] = 'vertical-cta-' . $sticky_data->SSuprydp_cta_position;
			}


			if (!empty($sticky_data->horizontal_vertical_position)) {
				$secondary_position = function_exists('easy_sticky_sidebar_normalize_secondary_position')
					? easy_sticky_sidebar_normalize_secondary_position($sticky_data->SSuprydp_cta_position, $sticky_data->horizontal_vertical_position, 'center')
					: $sticky_data->horizontal_vertical_position;
				$dataview['cta_classes'][] = 'sticky-cta-' . $secondary_position;
			}

			if ($sticky_data->show_close_button == 'yes') {
				$dataview['cta_classes'][] = 'ess-close-button-' . $sticky_data->close_button_position;
			}

			$shadow_enabled = isset($sticky_data->enable_box_shadow) ? $sticky_data->enable_box_shadow : 'no';
			if ($shadow_enabled === 'yes') {
				$dataview['cta_classes'][] = 'ess-shadow-enabled';
			}

			$dataview['cta_classes'] = array_unique($dataview['cta_classes']);

			$dataview['cta_classes'] = apply_filters('easy_sticky_sidebar_class', $dataview['cta_classes'], $sticky_data);

			$template = $sticky_data->sidebar_template;

			$view_template = EASY_STICKY_SIDEBAR_PLUGIN_DIR . "/views/{$template}.php";
			if (!file_exists($view_template)) {
				$template = 'default';
			}

			if ($SSuprydp_development == 'live' || ($SSuprydp_development == 'development' && current_user_can('manage_options'))) {
				print easy_sticky_sidebar()->engine->get_view($template, $dataview); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			}
		}
	}

	/**
	 * Clear pro-only settings when pro is disabled.
	 * Ensures free users don't retain pro config effects.
	 */
	private function cleanup_pro_settings_if_needed()
	{
		$pro_active = function_exists('easy_sticky_sidebar_has_pro') && easy_sticky_sidebar_has_pro();
		$last_status = get_option('easy_sticky_sidebar_last_pro_status', '0');

		if ($pro_active) {
			if ($last_status !== '1') {
				update_option('easy_sticky_sidebar_last_pro_status', '1');
			}
			return;
		}

		if ($last_status !== '1') {
			return;
		}

		global $wpdb;
		$pro_keys = [
			'display_trigger',
			'display_trigger_seconds',
			'display_trigger_scroll',
			'display_animation',
			'hide_behavior',
			'hide_after_seconds',
			'display_frequency',
			'after_close_behavior',
			'after_close_time',
			'after_close_time_unit',
			'close_button_background',
		];

		$placeholders = implode(',', array_fill(0, count($pro_keys), '%s'));
		$wpdb->query($wpdb->prepare(
			"DELETE FROM {$wpdb->sticky_cta_options} WHERE option_name IN ($placeholders)",
			$pro_keys
		));

		update_option('easy_sticky_sidebar_last_pro_status', '0');

		if (class_exists('Easy_Sticky_Sidebar_Generate_CSS')) {
			Easy_Sticky_Sidebar_Generate_CSS::regenerate_now();
		}
	}

	/**
	 * Increment impressions when CTA is rendered on frontend.
	 * Clicks/CTR remain unchanged (pro features).
	 *
	 * @param Easy_Sticky_Sidebar_CTA_Data|object $sticky_data
	 * @return void
	 */
	private function track_impression($sticky_data)
	{
		if (is_admin()) {
			return;
		}

		$sticky_id = 0;
		if (is_object($sticky_data) && method_exists($sticky_data, '__get')) {
			$sticky_id = absint($sticky_data->__get('id'));
		} elseif (is_object($sticky_data) && isset($sticky_data->id)) {
			$sticky_id = absint($sticky_data->id);
		}

		if ($sticky_id <= 0) {
			return;
		}

		global $wpdb;
		$wpdb->query($wpdb->prepare("UPDATE $wpdb->sticky_cta SET SSuprydp_impressions = SSuprydp_impressions + 1 WHERE id = %d", $sticky_id));
	}

	public function get_media_metadata()
	{
		$ar = array();
		$ar['width'] = 1344;
		$ar['height'] = 751;
		$ar['file'] = '2020/05/ss_dummy.jpg';
		$ar['sizes'] = array(
			"medium" => array(
				"file" => 'ss_dummy-300x167.jpg',
				"width" => 300,
				"height" => 167,
				"mime-type" => 'image/jpeg'
			),
			"large" => array(
				"file" => 'ss_dummy-1024x572.jpg',
				"width" => 1024,
				"height" => 572,
				"mime-type" => 'image/jpeg'
			),

			"thumbnail" => array(
				"file" => 'ss_dummy-150x83.jpg',
				"width" => 150,
				"height" => 83,
				"mime-type" => 'image/jpeg'
			),

			"medium_large" => array(
				"file" => 'ss_dummy-768x429.jpg',
				"width" => 768,
				"height" => 429,
				"mime-type" => 'image/jpeg'
			),
			"1536x1536" => array(
				"file" => 'ss_dummy-1536x858.jpg',
				"width" => 1536,
				"height" => 858,
				"mime-type" => 'image/jpeg'
			),
			"post-thumbnail" => array(
				"file" => 'ss_dummy-1200x670.jpg',
				"width" => 1200,
				"height" => 670,
				"mime-type" => 'image/jpeg'
			)
		);
		$ar['image_meta'] = array(
			"aperture" => 0,
			"credit" => '',
			"camera" => '',
			"caption" => '',
			"created_timestamp" => 0,
			"copyright" => '',
			"focal_length" => 0,
			"iso" => 0,
			"shutter_speed" => 0,
			"title" => '',
			"keywords" => array(),
		);

		return $ar;
	}

	public function copy_media_files()
	{
		$currentpath = wp_get_upload_dir();

		if (!$currentpath['path']) {
			return;
		}

		global $wp_filesystem;
		if (!function_exists('WP_Filesystem')) {
			require_once ABSPATH . 'wp-admin/includes/file.php';
		}
		WP_Filesystem();

		if (!$wp_filesystem) {
			return;
		}

		$files = [
			'ss_dummy.jpg',
			'ss_dummy-300x167.jpg',
			'ss_dummy-1024x572.jpg',
			'ss_dummy-150x83.jpg',
			'ss_dummy-768x429.jpg',
			'ss_dummy-1536x858.jpg',
			'ss_dummy-1200x670.jpg',
		];

		foreach ($files as $file) {
			$source = EASY_STICKY_SIDEBAR_PLUGIN_DIR . '/assets/img/' . $file;
			$destination = $currentpath['path'] . '/' . $file;
			if ($wp_filesystem->exists($source)) {
				$wp_filesystem->copy($source, $destination, true);
			}
		}
	}
}
