<?php
if (!defined('ABSPATH')) {
	exit;
}

class Easy_Sticky_Sidebar_Options {

    /**
     * Holds the values to be used in the fields callbacks
     */
    private $options;

    /**
     * Export Import
     *
     * @var string
     */
    private $export_import;

    /**
     * Start up
     */
    public function __construct() {
        add_action('admin_menu', array($this, 'add_submenu_pages'));
        $this->handle_cta_action();

        add_action('admin_footer', [$this, 'pro_feature_popup']);
        add_action('admin_footer', [$this, 'load_design_template_popup']);

        add_action('init', [$this, 'handle_settings']);
    }

    function handle_settings() {
        if (!current_user_can('manage_options')) {
            return;
        }

        $post_data = filter_input_array(INPUT_POST, FILTER_SANITIZE_SPECIAL_CHARS);

        if (!isset($post_data['_wpnonce'])) {
            return;
        }

        if (!wp_verify_nonce($post_data['_wpnonce'], '_nonce_easy_sticky_sidebar_settings')) {
            return;
        }

        unset($post_data['_wpnonce'], $post_data['_wp_http_referer'], $post_data['submit']);

        $settings_data = apply_filters('easy_sticky_sidebar_settings_post_data', $post_data);
        update_option('easy_sticky_sidebar_settings', $settings_data);

        $generate = new Easy_Sticky_Sidebar_Generate_CSS();
        $generate->generate_style();
    }


    function handle_cta_action() {
        $get_data = filter_input_array(INPUT_GET, FILTER_SANITIZE_SPECIAL_CHARS);

        if (!isset($get_data['id']) || !isset($get_data['_nonce']) || !isset($get_data['action'])) {
            return;
        }

        if (!wp_verify_nonce($get_data['_nonce'], 'nonce_cta_action_' . $get_data['id'])) {
            return;
        }

        $action = $get_data['action'];

        global $wpdb;
        if ('delete' !== $action) {
            return;
        }

        if ($wpdb->delete($wpdb->sticky_cta, array('ID' => $get_data['id']), array('%d'))) {
            $wpdb->delete($wpdb->sticky_cta_options, array('sticky_cta_id' => $get_data['id']), array('%d'));
            wp_safe_redirect(admin_url('admin.php?page=easy-sticky-sidebars'));
            exit;
        }
    }

    /**
     * add submenu pages in admin menu
     */
    public function add_submenu_pages() {
        if (function_exists('easy_sticky_sidebar_has_pro') && easy_sticky_sidebar_has_pro()) {
            return;
        }

        require_once EASY_STICKY_SIDEBAR_PLUGIN_DIR . '/inc/sticky-sidebar-list.php';

        $sidebars = new Easy_Sticky_Sidebar_List();
        add_menu_page('WP CTA', 'WP CTA', 'manage_options', 'easy-sticky-sidebars', apply_filters('easy_sticky_sidebar_main_menu', [$sidebars, 'output']), 'dashicons-megaphone');

        $sidebar_list_menu = add_submenu_page('easy-sticky-sidebars', 'WP CTA Dashboard', 'WP CTA Dashboard', 'manage_options', 'easy-sticky-sidebars', apply_filters('easy_sticky_sidebar_main_menu', [$sidebars, 'output']));
        add_action("load-$sidebar_list_menu", [$sidebars, 'screen_option']);

        add_submenu_page('easy-sticky-sidebars', 'Add New', 'Add New', 'manage_options', 'add-easy-sticky-sidebar', [$this, 'add_new_cta_page']);

        $this->export_import = require_once EASY_STICKY_SIDEBAR_PLUGIN_DIR . '/inc/import-export.php';
        add_submenu_page('easy-sticky-sidebars', esc_html__('Import/Export', 'easy-sticky-sidebar'), esc_html__('Import/Export', 'easy-sticky-sidebar'), 'manage_options', 'easy-sticky-sidebar-import-export', [$this->export_import, 'output']);


        do_action('easy_sticky_sidebar_admin_submenu');

        add_submenu_page('easy-sticky-sidebars', 'CTA Settings', 'Settings', 'manage_options', 'easy-sticky-sidebar-settings', [$this, 'settings']);



        // add_submenu_page('easy-sticky-sidebars', esc_html__('How to use Wordpress CTA', 'easy-sticky-sidebar'), esc_html__('How To Use', 'easy-sticky-sidebar'), 'manage_options', 'https://wpctapro.com/help/', 499);

        add_submenu_page('easy-sticky-sidebars', esc_html__('How to use Wordpress CTA', 'easy-sticky-sidebar'), esc_html__('How To Use', 'easy-sticky-sidebar'), 'manage_options', 'how-to-use-wordpress-cta', [$this, 'how_to_use']);

        add_submenu_page('easy-sticky-sidebars', 'Edit CTA', 'Edit CTA', 'manage_options', 'edit-easy-sticky-sidebar', [$this, 'render_form_settings'], 500);
    }

    /**
     * Display add banner view
     */

    public function add_new_cta_page() {
        $default_profiles = function_exists('easy_sticky_sidebar_get_editor_default_templates')
            ? easy_sticky_sidebar_get_editor_default_templates()
            : array();
        $sticky_defaults = array();
        if (!empty($default_profiles['sticky_layouts']['overlay-left']) && is_array($default_profiles['sticky_layouts']['overlay-left'])) {
            $sticky_defaults = $default_profiles['sticky_layouts']['overlay-left'];
        }

        $data = array(
            'sticky_id' => 0,
            'editor_current_tab' => 'sticky-sidebar-template',
            'stickycta' => new Easy_Sticky_Sidebar_CTA_Data($sticky_defaults)
        );

        $form_attributes = array(
            'class' => 'SSuprydp_form',
            'data-status' => 'development',
            'data-template' => 'sticky-cta',
        );

        $disable_google_font = apply_filters('easy_sticky_sidebar_disable_google_font', false);
        if ($disable_google_font) {
            $form_attributes['data-disable-google-font'] = 'yes';
        }

        $data['form_attributes'] = [];
        foreach ($form_attributes as $attribute => $value) {
            $data['form_attributes'][] = sprintf('%s="%s"', $attribute, esc_attr($value));
        }

        print easy_sticky_sidebar()->engine->get_view('add_pages', $data); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
    }

    public function how_to_use() {
        // Define the text and button
        $button_text = 'View Help Page';
        $page_url = 'https://wpctapro.com/help/';

        // Start output buffering
        ob_start();
        ?>
<div class="wrap">
    <h1><?php esc_html_e('How to use Wordpress CTA', 'easy-sticky-sidebar'); ?></h1>
    <p style="font-size: 25px;">
        <?php esc_html_e('Please click on the button below to view our help page', 'easy-sticky-sidebar'); ?></p>
    <button id="cta-button" class="button button-primary"><?php echo esc_html($button_text); ?></button>
</div>
<script type="text/javascript">
document.getElementById("cta-button").addEventListener("click", function() {
    window.open("<?php echo esc_url($page_url); ?>", "_blank");
});
</script>
<?php
        // End output buffering and output everything
        $output = ob_get_clean();
        echo $output; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
    }


    /**
     * add bulk pages
     */
    public function render_form_settings() {
        global $wpdb;

        $sticky_id = isset($_GET['id']) ? absint($_GET['id']) : 0;

        $record = $wpdb->get_row($wpdb->prepare("SELECT * FROM $wpdb->sticky_cta WHERE id = %d ORDER BY id ASC", $sticky_id));
        if (!$record) {
            return include_once EASY_STICKY_SIDEBAR_PLUGIN_DIR . '/views/sidebar-404.php';
        }

        $stickycta = new Easy_Sticky_Sidebar_CTA_Data($record);

        // Ensure editor template selection reflects latest persisted option value.
        $saved_template = $wpdb->get_var(
            $wpdb->prepare(
                "SELECT option_value FROM {$wpdb->prefix}sticky_cta_options WHERE sticky_cta_id = %d AND option_name = %s ORDER BY ID DESC LIMIT 1",
                $sticky_id,
                'sidebar_template'
            )
        );
        $saved_template = maybe_unserialize($saved_template);
        if (function_exists('easy_sticky_sidebar_normalize_template_key')) {
            $saved_template = easy_sticky_sidebar_normalize_template_key($saved_template, '');
        }
        if (is_string($saved_template) && $saved_template !== '') {
            $available_templates = function_exists('easy_sticky_sidebar_templates') ? array_keys((array) easy_sticky_sidebar_templates()) : [];
            if (empty($available_templates) || in_array($saved_template, $available_templates, true)) {
                $stickycta->sidebar_template = $saved_template;
            }
        }

        $data['stickycta'] = $stickycta;
        $data['sticky_id'] = $stickycta->__get('id') ? $stickycta->__get('id') : 0;

        $data['editor_current_tab'] = 'sticky-sidebar-template';
        if ($stickycta->cta_editor_current_tab && WP_DEBUG) {
            $data['editor_current_tab'] = $stickycta->cta_editor_current_tab;
        }

        $form_attributes = array(
            'class' => 'SSuprydp_form',
            'data-status' => esc_attr($stickycta->SSuprydp_development),
            'data-template' => esc_attr($stickycta->sidebar_template),
        );

        $disable_google_font = apply_filters('easy_sticky_sidebar_disable_google_font', false);
        if ($disable_google_font) {
            $form_attributes['data-disable-google-font'] = 'yes';
        }

        if ($stickycta->hide_floating_button_text == 'yes') {
            $form_attributes['class'] .= ' hide-floating-button-text';
        }

        $data['form_attributes'] = [];
        foreach ($form_attributes as $attribute => $value) {
            $data['form_attributes'][] = sprintf('%s="%s"', $attribute, esc_attr($value));
        }

        print easy_sticky_sidebar()->engine->get_view('add_pages', $data); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
    }

    /**
     * Sanitize each setting field as needed
     *
     * @param array $input Contains all settings fields as array keys
     */
    public function sanitize($input) {
        return $input;
    }

    /**
     * Pro feature popup
     * @since 1.4.5
     */
    public function pro_feature_popup() {
        $is_plugin_page = strpos(get_current_screen()->id, 'easy-sticky-sidebar');
        if ($is_plugin_page === false) {
            return;
        } ?>
<div id="wordpress-cta-pro-feature-popup" class="wordpress-cta-popup">
    <div class="popup-content">
        <?php easy_sticky_sidebar_pro_get_block(); ?>
        <span class="close"></span>
    </div>
</div>
<?php
    }

    /**
     * Pro feature popup
     * @since 1.0.4
     */
    public function load_design_template_popup() {
        $is_plugin_page = strpos(get_current_screen()->id, 'easy-sticky-sidebar');
        if ($is_plugin_page === false) {
            return;
        } ?>
<div id="wordpress-cta-popup-load-design" class="wordpress-cta-popup">
    <div class="popup-content">
        <?php esc_html_e('Do you want to replace this style?', 'easy-sticky-sidebar'); ?>

        <footer>
            <a class="button btn-wordpress-cta-primary"
                href="#load-style"><?php esc_html_e('Load Styles', 'easy-sticky-sidebar'); ?></a>
            <a class="button btn-wordpress-cta-primary"
                href="#load-style-content"><?php esc_html_e('Load Styles and Content', 'easy-sticky-sidebar'); ?></a>
            <a class="button btn-cancel" href="#"><?php esc_html_e('Cancel', 'easy-sticky-sidebar'); ?></a>
        </footer>
        <span class="close"></span>
    </div>
</div>
<?php
    }

    /**
     * Settings
     * @since 1.5.6
     */
    public function settings() {
        $settings = Easy_Sticky_Sidebar_Utils::get_settings();
        if (!empty($_POST)) {
            $settings = filter_input_array(INPUT_POST, FILTER_SANITIZE_SPECIAL_CHARS);
        }

        $post_data = wp_parse_args($settings, array('disable_google_font' => 'no')); ?>

<div class="wrap wrap-easy-sticky-sidebar">
    <?php easy_sticky_sidebar_get_header();  ?>
    <hr class="wp-header-end">

    <div class="easy-sticky-sidebar-container">
        <div id="SSuprydp_builder_form">
            <div class="SSuprydp_col_2 SSuprydp-form-col">
                <form id="SSuprydp_form" method="post">
                    <?php wp_nonce_field('_nonce_easy_sticky_sidebar_settings'); ?>
                    <?php do_action('easy_sticky_sidebar_settings', $post_data) ?>
                    <?php submit_button() ?>
                </form>
            </div>
        </div>
    </div>
</div>

<?php
    }
}
