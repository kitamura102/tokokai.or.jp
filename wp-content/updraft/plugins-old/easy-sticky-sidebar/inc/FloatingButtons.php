<?php
if (!defined('ABSPATH')) {
	exit;
}

/*
 * Wordpress_CTA_Pro_Content tab option
 * @package sticky-sidebar/inc
 * @since 1.4.5
 */
class Wordpress_CTA_Free_Floating_Buttons {
    /**
     * Add floating buttons template
     * @since 1.4.5
     */
    public static function get_data($data = []) {
        return (object) wp_parse_args($data, apply_filters('easy_sticky_sidebar_floating_button_default', array(
            'icon' => '',
            'text' => '',
            'url' => '',
            'color' => '',
            'hover_color' => '',
            'background_color' => '',
            'background_hover_color' => ''
        )));
    }

    /**
     * Add floating buttons template
     * @since 1.4.5
     */
    public static function get_buttons($stickycta) {
        $floating_buttons = $stickycta->floating_buttons;
        if (!is_array($floating_buttons)) {
            $floating_buttons = [];
        }

        $floating_buttons = array_map(array(__CLASS__, 'get_data'), $floating_buttons);

        $button_style = $stickycta->floating_button_style;
        if (!is_array($button_style)) {
            $button_style = [];
        }

        array_walk($floating_buttons, function (&$item, $key) use ($button_style) {
            if (isset($button_style[$key])) {
                $item = (object) array_merge((array)$item, (array) $button_style[$key]);
            }
        });

        $new_buttons = [];
        foreach ($floating_buttons as $value) {
            $new_buttons[] = $value;
        }

        return $new_buttons;
    }

    /**
     * Construct
     * @since 1.4.5
     */
    public function __construct() {
        add_action('easy_sticky_sidebar_content_tab_options', [$this, 'floating_buttons_options']);
        add_action('easy_sticky_sidebar_styling_options', [$this, 'style_options']);

        add_action('easy_sticky_sidebar_global_styles', [$this, 'icon_width'], 5);
        add_action('easy_sticky_sidebar_global_styles', [$this, 'text_font_size'], 6);
        add_action('easy_sticky_sidebar_global_styles', [$this, 'default_color'], 10);
        add_action('easy_sticky_sidebar_global_styles', [$this, 'background_color'], 11);
        add_action('easy_sticky_sidebar_floating_buttons_style', [$this, 'buttons_style_options'], 50);

        add_action('easy_sticky_sidebar_generate_css', [$this, 'generate_css']);
    }

    /**
     * Generate Style
     * @since 1.4.5
     */
    public function generate_css($stickycta) {
        if (empty($stickycta) || $stickycta->sidebar_template !== 'floating-buttons') {
            return;
        }
        $wrapper = sprintf('.easy-sticky-sidebar.easy-sticky-sidebar-%d', $stickycta->__get('id'));


        $font_size = absint($stickycta->floating_button_font_size);
        $button_width = absint($stickycta->floating_button_width);

        ob_start();

        if ($button_width == 0 && $font_size > 0) {
            $icon_size = $font_size + 40;
        }

        if ($button_width > 0) {
            printf("\t--button_width: %dpx;\n", esc_attr($button_width));
        }

        if ($font_size > 0) {
            printf("\tfont-size: %dpx;\n", esc_attr($font_size));
        }

        if ($stickycta->enable_cta_width == 'yes' && absint($stickycta->cta_width) > 0) {
            $unit = empty($stickycta->cta_width_unit) ? 'px' : $stickycta->cta_width_unit;
            printf("\twidth: %d%s;\n", absint($stickycta->cta_width), esc_attr($unit));
        }

        Easy_Sticky_CTA_Generate_CSS::get_font_style($stickycta->default_font_family);


        $style = ob_get_clean();

        if (!empty($style)) {
            printf('%s {%s}', esc_html($wrapper), esc_html($style));
        }

        echo '@media screen and (min-width: 768px) and (max-width: 1024px){';
        printf("%s {\n", esc_html($wrapper));
        if ($stickycta->enable_cta_width == 'yes' && absint($stickycta->cta_tablet_width) > 0) {
            $unit = empty($stickycta->cta_tablet_width_unit) ? 'px' : $stickycta->cta_tablet_width_unit;
            printf("\twidth: %d%s;\n", absint($stickycta->cta_tablet_width), esc_attr($unit));
        }
        echo "}\n\n";
        echo '}';

        echo '@media screen and (max-width: 767px){';
        printf("%s {\n", esc_html($wrapper));
        if ($stickycta->enable_cta_width == 'yes' && absint($stickycta->cta_mobile_width) > 0) {
            $unit = empty($stickycta->cta_mobile_width_unit) ? 'px' : $stickycta->cta_mobile_width_unit;
            printf("\twidth: %d%s;\n", absint($stickycta->cta_mobile_width), esc_attr($unit));
        }

        echo "}\n\n";

        echo '}';

        ob_start();
        if ($stickycta->floating_button_color) {
            printf("\t--color:%s;", esc_html($stickycta->floating_button_color));
        }

        if ($stickycta->floating_button_hover_color) {
            printf("\t--hover_color:%s;", esc_html($stickycta->floating_button_hover_color));
        }

        if ($stickycta->floating_button_background_color) {
            printf("\t--background_color:%s;", esc_html($stickycta->floating_button_background_color));
        }

        if ($stickycta->floating_button_background_hover_color) {
            printf("\t--background_hover_color:%s;", esc_html($stickycta->floating_button_background_hover_color));
        }

        $button_style = ob_get_clean();

        if (!empty($button_style)) {
            printf("%s li {%s}\n", esc_html($wrapper), esc_html($button_style));
        }

        $floating_buttons = self::get_buttons($stickycta);
        foreach ($floating_buttons as $key => $button) {
            ob_start();
            if ($button->color) {
                printf("\t--color:%s;", esc_html($button->color));
            }

            if ($button->hover_color) {
                printf("\t--hover_color:%s;", esc_html($button->hover_color));
            }

            if ($button->background_color) {
                printf("\t--background_color:%s;", esc_html($button->background_color));
            }

            if ($button->background_hover_color) {
                printf("\t--background_hover_color:%s;", esc_html($button->background_hover_color));
            }

            $button_style = ob_get_clean();

            if (!empty($button_style)) {
                printf('%s li.floating-button-%d {%s}', esc_html($wrapper), absint($key), esc_html($button_style));
            }
        }
    }

    /**
     * Floating buttons styling options
     * @since 1.4.5
     */
    public function style_options($stickycta) {
        if (has_action('easy_sticky_sidebar_floating_buttons_style')) : ?>
            <details class="easy-sticky-sidebar-fieldset floating-buttons-options <?php echo esc_attr(Wordpress_CTA_Free_Utils::pro_tab_class('easy_sticky_sidebar_floating_buttons_style')); ?>" id="section-floating-button-style-options">
            <summary class="heading"><?php esc_html_e("Floating Button Options", "easy-sticky-sidebar"); ?></summary>
            <div class="gap-5"></div>
            <p class="wordpress-cta-instruction"><?php esc_html_e('If you edit an individual button, that style will override the global style for only that button.', 'easy-sticky-sidebar'); ?></p>
                <?php do_action('easy_sticky_sidebar_floating_buttons_style', $stickycta); ?>
            </details>
        <?php endif;
    }

    /**
     * Add buttons
     * @since 1.4.5
     */
    public function floating_buttons_options($stickycta) {
        $floating_buttons = self::get_buttons($stickycta); ?>
        <div id="floating-buttons-options" data-button-default-args='<?php echo esc_attr(wp_json_encode(self::get_data())); ?>' data-buttons='<?php echo esc_attr(wp_json_encode(self::get_buttons($stickycta))); ?>'>
            <div class="heading"><?php esc_html_e('Floating Buttons', 'easy-sticky-sidebar'); ?></div>

            <div class="SSuprydp_field_wrap">
                <h4 class="heading"><?php esc_html_e('Hide Text', 'easy-sticky-sidebar'); ?></h4>
                <label class="SSuprydp_switch">
                    <input type="hidden" name="hide_floating_button_text" value="no">
                    <input type="checkbox" name="hide_floating_button_text" value="yes" <?php checked('yes', $stickycta->hide_floating_button_text) ?> class="checkbox-hide-show">
                </label>
            </div>

            <div class="floating-buttons">
                <?php
                foreach ($floating_buttons as $key => $button) {
                    $this->get_button($button, $key, $stickycta);
                }
                ?>
            </div>

            <a class="btn-add-button button btn-primary" href="#"><?php esc_html_e('Add Button', 'easy-sticky-sidebar'); ?></a>
        </div>

        <script id="tmpl-easy-sticky-sidebar-floating-button" type="text/html">
            <?php
            $allowed_html = array(
                'div' => array(
                    'class' => array(),
                    'data-id' => array(),
                ),
                'label' => array(
                    'class' => array(),
                ),
                'input' => array(
                    'class' => array(),
                    'type' => array(),
                    'name' => array(),
                    'value' => array(),
                    'placeholder' => array(),
                ),
                'a' => array(
                    'href' => array(),
                    'class' => array(),
                ),
                'i' => array(
                    'class' => array(),
                ),
                'span' => array(
                    'class' => array(),
                ),
            );
            echo wp_kses($this->single_button_html($stickycta), $allowed_html);
            ?>
        </script>
    <?php
    }

    /**
     * Get button
     * @since 1.4.5
     */
    public function get_button($button, $key, $stickycta) {
        $button_data = self::get_data($button);
        $button_data->button_no = $key;
        $button_html = $this->single_button_html($stickycta);
        foreach ($button_data as $key => $value) {
            $button_html = preg_replace(sprintf('/{{data.%s}}/', $key), esc_attr($value), $button_html);
        }

        $allow_html = array_merge(wp_kses_allowed_html( 'post' ), array(
            'input' => array(
                'class' => array(),
                'type' => array(),
                'name' => array(),
                'value' => array(),
                'placeholder' => array(),
            )
        ));

        echo wp_kses($button_html, $allow_html);
    }

    /**
     * Button Template
     * @since 1.4.5
     */
    public function single_button_html($stickycta) {
        ob_start() ?>
        <div class="floating-button-item" data-id="{{data.button_no}}">
            <div class="button-field-item">
                <label><?php esc_html_e('Icon', 'easy-sticky-sidebar') ?></label>
                <div class="sticky-sidebar-select-icon">
                    <input class="button-icon" type="hidden" name="floating_buttons[{{data.button_no}}][icon]" value="{{data.icon}}">
                    <a href="#" class="button btn-primary"><?php esc_html_e('Select Icon', 'easy-sticky-sidebar') ?></a>
                    <i class="icon {{data.icon}}"></i>
                </div>
            </div>

            <div class="button-field-item button-field-item-text">
                <label><?php esc_html_e('Text', 'easy-sticky-sidebar') ?></label>
                <input class="button-text" type="text" name="floating_buttons[{{data.button_no}}][text]" value="{{data.text}}" placeholder="<?php esc_attr_e('Enter button text here', 'easy-sticky-sidebar'); ?>">
            </div>

            <div class="button-field-item">
                <label><?php esc_html_e('URL', 'easy-sticky-sidebar') ?></label>
                <input type="text" name="floating_buttons[{{data.button_no}}][url]" value="{{data.url}}" placeholder="<?php esc_attr_e('Enter button url here', 'easy-sticky-sidebar'); ?>">
            </div>

            <?php do_action('easy_sticky_sidebar_floating_single_button', $stickycta); ?>

            <div class="actions">
                <span class="btn-button-remove dashicons dashicons-remove"></span>
            </div>
        </div>
    <?php return ob_get_clean();
    }

    /**
     * Icon width
     * @since 1.4.5
     */
    public function icon_width($stickycta) { ?>
        <div class="SSuprydp_field_wrap">
            <label><?php esc_html_e('Button Width', 'easy-sticky-sidebar') ?></label>
            <input name="floating_button_width" style="width: 50px;text-align:right" type="number" value="<?php echo esc_attr($stickycta->floating_button_width) ?>"> px
        </div>
    <?php
    }

    /**
     * Icon width
     * @since 1.4.5
     */
    public function text_font_size($stickycta) { ?>
        <div class="SSuprydp_field_wrap">
            <label><?php esc_html_e("Icon / Font Size", "easy-sticky-sidebar"); ?></label>
            <input name="floating_button_font_size" style="width: 50px;text-align:right" type="number" min="0" value="<?php echo esc_attr($stickycta->floating_button_font_size) ?>"> px
        </div>
    <?php
    }

    /**
     * Default color of floating buttons
     * @since 1.4.7
     */
    public function default_color($stickycta) { ?>
        <div class="easy-sticky-sidebar-group-fields">
            <div class="SSuprydp_field_wrap">
                <label><?php esc_html_e("Color", "easy-sticky-sidebar"); ?></label>
                <input class="sticky-sidebar-colorpicker" type="text" name="floating_button_color" value="<?php echo esc_attr($stickycta->floating_button_color) ?>" />
            </div>

            <div class="SSuprydp_field_wrap">
                <label><?php esc_html_e("Hover Color", "easy-sticky-sidebar"); ?></label>
                <input class="sticky-sidebar-colorpicker" type="text" name="floating_button_hover_color" value="<?php echo esc_attr($stickycta->floating_button_hover_color) ?>" />
            </div>
        </div>
    <?php
    }

    /**
     * Default background color of floating buttons
     * @since 1.4.7
     */
    public function background_color($stickycta) { ?>
        <div class="easy-sticky-sidebar-group-fields">
            <div class="SSuprydp_field_wrap">
                <label><?php esc_html_e("Background Color", "easy-sticky-sidebar"); ?></label>
                <input class="sticky-sidebar-colorpicker" type="text" name="floating_button_background_color" value="<?php echo esc_attr($stickycta->floating_button_background_color) ?>" />
            </div>

            <div class="SSuprydp_field_wrap">
                <label><?php esc_html_e("Background Hover Color", "easy-sticky-sidebar"); ?></label>
                <input class="sticky-sidebar-colorpicker" type="text" name="floating_button_background_hover_color" value="<?php echo esc_attr($stickycta->floating_button_background_hover_color) ?>" />
            </div>
        </div>
    <?php
    }

    /**
     * Single button style options
     * @since 1.4.5
     */
    public function single_buttons_style_template() {  ?>
        <details class="easy-sticky-sidebar-fieldset easy-sticky-sidebar-fieldset-floating-button" data-id="{{data.button_no}}">
            <summary class="heading">{{{data.heading}}}</summary>

            <div class="easy-sticky-sidebar-group-fields">
                <div class="SSuprydp_field_wrap">
                    <label><?php esc_html_e("Color", "easy-sticky-sidebar"); ?></label>
                    <input class="sticky-sidebar-colorpicker" type="text" name="floating_button_style[{{data.button_no}}][color]" value="{{data.color}}" data-name="color" />
                </div>

                <div class="SSuprydp_field_wrap">
                    <label><?php esc_html_e("Hover Color", "easy-sticky-sidebar"); ?></label>
                    <input type="text" name="floating_button_style[{{data.button_no}}][hover_color]" value="{{data.hover_color}}" class="sticky-sidebar-colorpicker" data-name="hover_color" />
                </div>
            </div>

            <div class="easy-sticky-sidebar-group-fields">
                <div class="SSuprydp_field_wrap">
                    <label><?php esc_html_e("Background Color", "easy-sticky-sidebar"); ?></label>
                    <input class="sticky-sidebar-colorpicker" type="text" name="floating_button_style[{{data.button_no}}][background_color]" value="{{data.background_color}}" data-name="background_color" />
                </div>

                <div class="SSuprydp_field_wrap">
                    <label><?php esc_html_e("Background Hover Color", "easy-sticky-sidebar"); ?></label>
                    <input type="text" name="floating_button_style[{{data.button_no}}][background_hover_color]" value="{{data.background_hover_color}}" class="sticky-sidebar-colorpicker" data-name="background_hover_color" />
                </div>
            </div>

            <?php do_action('easy_sticky_sidebar/floating_single_button_style_options'); ?>
        </details>
    <?php
    }

    /**
     * Single button style options
     * @since 1.4.5
     */
    public function buttons_style_options($stickycta) {
        $floating_buttons = self::get_buttons($stickycta); ?>
        <div id="floating-single-button-styles">
            <?php foreach ($floating_buttons as $key => $button) :
                $button_heading = '';
                if ($button->icon) {
                    $button_heading = sprintf('<i class="icon %s"></i>', esc_attr($button->icon));
                }

                $button_heading .= esc_html((string) $button->text);
                if (trim(wp_strip_all_tags($button_heading)) === '') {
                    // translators: %d: Button number.
                    $button_heading = sprintf(__("Button %d", "easy-sticky-sidebar"), $key + 1);
                }

                ob_start();
                $this->single_buttons_style_template();
                $single_button_html = ob_get_clean();

                $data = array_merge((array) $button, array('heading' => $button_heading, 'button_no' => absint($key)));
                foreach ($data as $data_key => $value) {
                    $replacement = ($data_key === 'heading') ? wp_kses_post($value) : esc_attr($value);
                    $single_button_html = preg_replace(sprintf('/({{data.%s}}|{{{data.%s}}})/', $data_key, $data_key), $replacement, $single_button_html);
                }

                echo wp_kses_post($single_button_html);
            endforeach; ?>
        </div>

        <script id="tmpl-easy-sticky-sidebar-floating-single-button-style" type="text/html">
            <?php $this->single_buttons_style_template(); ?>
        </script>
<?php
    }
}

