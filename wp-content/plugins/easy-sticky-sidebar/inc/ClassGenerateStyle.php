<?php
if (!defined('ABSPATH')) {
	exit;
}

/**
 * Easy_Sticky_Sidebar_Generate_CSS
 * @package sticky-sidebar
 * @since   1.3.6
 */
class Easy_Sticky_Sidebar_Generate_CSS {

    // Declare the property to avoid dynamic property creation deprecated warning
    protected $item;
    protected static $has_generated = false;

    function __construct($register_hooks = true) {
        if ($register_hooks) {
            $this->generate_css_file();
            add_action('easy_sticky_sidebar_after_save', [$this, 'generate_style'], 2);
        }
    }

    public function generate_css_file() {
        $upload_dir = wp_get_upload_dir();
        $css_file = $upload_dir['basedir'] . '/sticky-sidebar-generated.css';
        $has_pro = function_exists('easy_sticky_sidebar_has_pro') && easy_sticky_sidebar_has_pro();
        $current_status = $has_pro ? '1' : '0';
        $last_status = get_option('easy_sticky_sidebar_last_pro_status', '');

        if (!file_exists($css_file) || $last_status !== $current_status) {
            $this->generate_style();
            update_option('easy_sticky_sidebar_last_pro_status', $current_status);
        }
    }

    public function generate_style() {
        self::$has_generated = true;
        global $wpdb;

        $results = $wpdb->get_results("SELECT * FROM $wpdb->sticky_cta WHERE SSuprydp_development != 'off' ORDER BY id");

        ob_start();
        foreach ($results as $item) {
            $this->item = new Easy_Sticky_Sidebar_CTA_Data($item);
            $this->generate_wrapper_style($this->item);
            $this->template_style();
            do_action('easy_sticky_sidebar_generate_css', $this->item, $this);
        }

        do_action('easy_sticky_sidebar_after_generate_css');

        $styles = ob_get_clean();

        global $wp_filesystem;
        if (!function_exists('WP_Filesystem')) {
            require_once ABSPATH . 'wp-admin/includes/file.php';
        }
        WP_Filesystem();

        if ($wp_filesystem) {
            $wp_filesystem->put_contents(wp_upload_dir()['basedir'] . '/sticky-sidebar-generated.css', $styles, FS_CHMOD_FILE);
        }
    }

    public static function regenerate_now() {
        if (self::$has_generated) {
            return;
        }

        $generator = new self(false);
        $generator->generate_style();
    }

    public static function get_font_style($font) {
        $disable_google_font = apply_filters('easy_sticky_sidebar_disable_google_font', false);
        if ($disable_google_font) {
            return;
        }
    
        @list($font_family, $font_style) = explode(':', str_replace('+', ' ', $font));
        
        if ($font_family) {
            printf("\tfont-family: '%s';\n", esc_html($font_family));
        }
    
        if (absint($font_style) > 0) {
            printf("\tfont-weight: %s;\n", absint($font_style));
        }
    
        if (is_string($font_style) && strpos($font_style, 'italic') !== false) {
            print("\tfont-style: italic;\n");
        }
    }
    
    public function generate_wrapper_style($sticky_cta) {
        $disable_position = apply_filters('easy_sticky_sidebar/disable_position_css', ['banner']);
        if (in_array($sticky_cta->sidebar_template, $disable_position)) {
            return;
        }

        $disable_position2 = apply_filters('easy_sticky_sidebar/disable_position2_css', []);

        $wrapper_selector = sprintf("#easy-sticky-sidebar-%d", absint($sticky_cta->__get('id')));

        $styles = '';
        if (!in_array($sticky_cta->sidebar_template, $disable_position2)) {
            $unit = empty($sticky_cta->position2_distance_unit) ? 'px' : $sticky_cta->position2_distance_unit;

            if (($position2_distance = intval($sticky_cta->position2_distance)) && $sticky_cta->horizontal_vertical_position !== 'center') {
                $styles .= sprintf("\t--position2_distance: %d%s;\n", $position2_distance, $unit);
            }
        }

        if (!empty($styles)) {
            printf("%s {%s}\n\n", esc_html($wrapper_selector), esc_html($styles));
        }
    }

    public function generate_button_style() {
        $sticky_cta = $this->item;

        if (!empty($this->item->SSuprydp_button_option_color)) {
            printf("\tcolor: %s;\n", esc_html($this->item->SSuprydp_button_option_color));
        }

        self::get_font_style($this->item->SSuprydp_button_option_font);

        $font_size = absint($this->item->SSuprydp_button_option_size);
        if ($font_size > 0) {
            printf("\tfont-size: %dpx;\n", absint($font_size));
        }

        printf("\ttext-align: %s;\n", esc_html($this->item->SSuprydp_button_option_align));

        if (!empty($this->item->SSuprydp_button_option_backg_color)) {
            printf("\tbackground-color: %s;\n", esc_html($this->item->SSuprydp_button_option_backg_color));
        }

        Easy_Sticky_Sidebar_Utils::get_dimensions_output($sticky_cta->button_padding, 'padding-%');

        do_action('easy_sticky_sidebar_generate_button_style', $this->item);
    }

    public function sidebar_image_style() {
        if (!empty($this->item->sticky_s_media)) {
            printf("\tbackground-image: url(%s);\n", esc_url($this->item->sticky_s_media));
        }

        $resolved_cta_height = function_exists('easy_sticky_sidebar_get_resolved_cta_height_css')
            ? easy_sticky_sidebar_get_resolved_cta_height_css($this->item, 300, 1)
            : '';
        if ($resolved_cta_height !== '') {
            printf("\theight: %s;\n", esc_attr($resolved_cta_height));
        }

        do_action('easy_sticky_sidebar_generate_image_style', $this->item);
    }

    public function content_style() {
        if (!empty($this->item->SSuprydp_content_option_color)) {
            printf("\tcolor: %s;\n", esc_attr($this->item->SSuprydp_content_option_color));
        }

        self::get_font_style($this->item->SSuprydp_content_option_font);

        $font_size = absint($this->item->SSuprydp_content_option_size);
        if ($font_size > 0) {
            printf("\tfont-size: %dpx;\n", absint($font_size));
        }

        if (!empty($this->item->content_background_color)) {
            printf("background-color: %s;\n", esc_attr($this->item->content_background_color));
        }

        $image_mode = strtolower((string) ($this->item->image_placement ?? 'classic'));
        if ($image_mode === 'background') {
            $image_mode = 'overlay';
        }
        $is_sticky_cta = $this->item->sidebar_template === 'sticky-cta';
        $is_sticky_overlay = $is_sticky_cta && $image_mode === 'overlay';
        $is_sticky_classic = $is_sticky_cta && !$is_sticky_overlay;

        // Sticky CTA content padding is handled by runtime/view styles for both
        // classic and overlay image modes.
        if (!$is_sticky_classic && !$is_sticky_overlay) {
            Easy_Sticky_Sidebar_Utils::get_dimensions_output($this->item->content_padding, 'padding-%');
        }

        do_action('easy_sticky_sidebar_generate_content_style', $this->item);
    }

    public function call_to_action_style() {
        if (!empty($this->item->SSuprydp_action_option_color)) {
            printf("\tcolor: %s;\n", esc_attr($this->item->SSuprydp_action_option_color));
        }

        self::get_font_style($this->item->SSuprydp_action_option_font);

        $font_size = absint($this->item->SSuprydp_action_option_size);
        if ($font_size > 0) {
            printf("\tfont-size: %dpx;\n", absint($font_size));
        }

        if (!empty($this->item->link_text_background)) {
            printf("background-color: %s;\n", esc_attr($this->item->link_text_background));
        }

        $image_mode = strtolower((string) ($this->item->image_placement ?? 'classic'));
        if ($image_mode === 'background') {
            $image_mode = 'overlay';
        }
        $is_sticky_classic = $this->item->sidebar_template === 'sticky-cta' && $image_mode !== 'overlay';

        // Classic sticky CTA link/button padding is handled by runtime/view styles.
        // Do not emit generated CSS padding for this mode.
        if (!$is_sticky_classic) {
            Easy_Sticky_Sidebar_Utils::get_dimensions_output($this->item->call_to_action_padding, 'padding-%');
        }

        do_action('easy_sticky_sidebar_generate_call_to_action_style', $this->item);
    }

    function template_style() {
        if (!in_array($this->item->sidebar_template, ['sticky-cta', 'tab-cta', 'html', 'gdpr'])) {
            return;
        }

        $sticky_class = sprintf("#easy-sticky-sidebar-%d.easy-sticky-sidebar", absint($this->item->__get('id')));

        printf("%s {\n", esc_html($sticky_class));
        if ($this->item->enable_cta_width == 'yes' && absint($this->item->cta_width) > 0) {
            $unit = empty($this->item->cta_width_unit) ? 'px' : $this->item->cta_width_unit;
            printf("\t--width: %d%s;\n", absint($this->item->cta_width), esc_attr($unit));
        }

        do_action('easy_sticky_sidebar_wrapper_style', $this->item);
        echo "}\n\n";

        echo '@media screen and (min-width: 768px) and (max-width: 1024px){';
        printf("%s {\n", esc_html($sticky_class));
        if ($this->item->enable_cta_width == 'yes' && absint($this->item->cta_tablet_width) > 0) {
            $unit = empty($this->item->cta_tablet_width_unit) ? 'px' : $this->item->cta_tablet_width_unit;
            printf("\t--width: %d%s;\n", absint($this->item->cta_tablet_width), esc_attr($unit));
        }

        do_action('easy_sticky_sidebar_wrapper_style_tablet', $this->item);
        echo "}\n\n";

        echo '}';

        echo '@media screen and (max-width: 767px){';
        printf("%s {\n", esc_html($sticky_class));
        if ($this->item->enable_cta_width == 'yes' && absint($this->item->cta_mobile_width) > 0) {
            $unit = empty($this->item->cta_mobile_width_unit) ? 'px' : $this->item->cta_mobile_width_unit;
            printf("\t--width: %d%s;\n", absint($this->item->cta_mobile_width), esc_attr($unit));
        }

        do_action('easy_sticky_sidebar_wrapper_style_mobile', $this->item);

        echo "}\n\n";

        echo '}';

        printf("%s .sticky-sidebar-button {\n", esc_html($sticky_class));
        $this->generate_button_style();
        echo "}\n\n";

        printf("%s .sticky-sidebar-image {\n", esc_html($sticky_class));
        $this->sidebar_image_style();
        echo "}\n\n";

        printf("%s .sticky-sidebar-content {\n", esc_html($sticky_class));
        $this->content_style();
        echo "}\n\n";

        if ($this->item->sidebar_template === 'html' && !empty($this->item->SSuprydp_content_option_color)) {
            printf("%s .sticky-sidebar-text, %s .sticky-sidebar-text * {\n", esc_html($sticky_class), esc_html($sticky_class));
            printf("\tcolor: %s !important;\n", esc_attr($this->item->SSuprydp_content_option_color));
            echo "}\n\n";
        }

        printf("%s .call-to-action {\n", esc_html($sticky_class));
        $this->call_to_action_style();
        echo "}\n\n";
    }
}
