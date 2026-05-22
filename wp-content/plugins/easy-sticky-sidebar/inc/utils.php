<?php

/**
 * Helper functions 
 * @since 1.4.5
 */
class Easy_Sticky_Sidebar_Utils {
    /**
     * Update image
     * @since 1.5.6
     * @return mixed
     */
    public static function get_settings($key = null, $default = null) {
        $settings = get_option('easy_sticky_sidebar_settings');
        $default_settings = apply_filters('easy_sticky_sidebar_settings_args', array(
            'disable_google_font' => 'no'
        ));

        $settings = wp_parse_args($settings, $default_settings);

        if (is_string($key)) {
            return isset($settings[$key]) ? $settings[$key] : $default;
        }

        return $settings;
    }

    /**
     * Update image
     * @since 1.5.2
     * @return int|false
     */
    public static function upload_preview_image($key) {
        $template_key = sanitize_title($key);

        global $wpdb;

        $attach_id = $wpdb->get_var($wpdb->prepare("SELECT post_id FROM $wpdb->postmeta WHERE meta_key = 'easy_sticky_sidebar_attachment' AND meta_value = %s", $template_key));
        if ($attach_id) {
            return $attach_id;
        }


        $get_templates = easy_sticky_sidebar_get_design_templates();
        if (!isset($get_templates[$template_key])) {
            return $key;
        }

        $template = $get_templates[$template_key];
        if (!isset($template['preview_image_path']) || !file_exists($template['preview_image_path'])) {
            return $key;
        }

        $filename = basename($template['preview_image_path']);
        $upload = wp_upload_bits($filename, null, file_get_contents($template['preview_image_path']));

        if ($upload['error']) {
            return $key;
        }

        $attach_id = wp_insert_attachment([
            'guid' => $upload['url'],
            'post_mime_type' => $upload['type'],
            'post_title' => sanitize_file_name($filename),
            'post_content' => '',
            'post_status' => 'inherit',
            'meta_input' => array(
                'easy_sticky_sidebar_attachment' => $template_key
            )
        ], $upload['file']);

        require_once(ABSPATH . 'wp-admin/includes/image.php');
        $attach_data = wp_generate_attachment_metadata($attach_id, $upload['file']);
        wp_update_attachment_metadata($attach_id, $attach_data);

        return $attach_id;
    }

    /**
     * Add design templates image
     * @since 1.4.5
     * @return void
     */
    public static function add_design_template_images($styles) {
        //DEPRECATED FUNCTION
    }

    /**
     * Check is pro tab
     * @since 1.4.5
     * @return void
     */
    public static function pro_tab_class($action) {
        if (easy_sticky_sidebar_has_pro()) {
            return '';
        }

        global $wp_filter;
        if (!isset($wp_filter[$action])) {
            return null;
        }

        $is_pro = true;

        $hooks_callbacks = $wp_filter[$action]->callbacks;

        foreach ($hooks_callbacks as $key => $callbacks) {
            foreach ($callbacks as $callback) {
                if (isset($callback['function'][0])) {
                    $object = $callback['function'][0];

                    if (!is_a($object, 'Easy_Sticky_Sidebar_Pro_Placeholder')) {
                        $is_pro = false;
                    }
                }
            }
        }

        if ($is_pro) {
            return 'wordpress-cta-pro-tab';
        }

        return null;
    }

    /**
     * Get inline popup
     * @since 1.4.5
     * @return html
     */
    public static function get_inline_lock($styles = []) {
        // Don't show lock if pro plugin is active
        $pro_active = easy_sticky_sidebar_has_pro();
        if ($pro_active) {
            return;
        }
        
        if (!is_array($styles)) {
            $styles = [];
        }

        $style = [];
        foreach ($styles as $key => $value) {
            $style[] = sprintf('%s: %s', $key, $value);
        } ?>
        <div class="wordpress-cta-pro-feature-lock-inline" style="<?php echo esc_attr(implode(';', $style)) ?>">
            <a class="button btn-wordpress-cta-primary" href="https://wpctapro.com/pricing/" target="_blank"><?php esc_html_e('Upgrade now', 'easy-sticky-sidebar') ?></a>
            <a href="https://wpctapro.com/" target="_blank"><?php esc_html_e('Learn more', 'easy-sticky-sidebar') ?></a>
        </div>
    <?php
    }

    /**
     * Get dimensions CSS output
     * @since 1.4.5
     * @return array
     */
    public static function get_dimensions_output($values, $dimension_text = '', $prefix = '') {
        $dimensions = self::get_dimensions_values($values);
        if ($dimensions->empty === true) {
            return;
        }

        $unit = $dimensions->unit;
        unset($dimensions->unit, $dimensions->empty);
        if (empty($dimensions)) {
            return;
        }

        foreach ($dimensions as $key => $value) {
            $dimension = str_replace('%', $key, $dimension_text);
            if (empty($dimension)) {
                continue;
            }

            printf("\t%s%s: %s%s;", esc_attr($prefix), esc_attr($dimension), esc_attr($value), esc_attr($unit));
        }
    }

    /**
     * Get dimensions values
     * @since 1.4.5
     * @return object
     */
    public static function get_dimensions_values($values) {
        $values = wp_parse_args($values, array('top' => '', 'right' => '', 'bottom' => '', 'left' => '', 'unit' => 'px', 'empty' => false));

        $sanitize = $values;
        unset($sanitize['unit']);

        foreach ($sanitize as $key => $value) {
            $v = trim($value);
            if (strlen($v) === 0) {
                unset($sanitize[$key]);
            }
        }

        if (empty($sanitize)) {
            $values['empty'] = true;
        }

        return (object) $values;
    }

    /**
     * Get padding field
     * @since 1.4.5
     * @return html
     */
    public static function get_dimensions_field($name, $values = []) {
        $values = self::get_dimensions_values($values);
        $names = array('top' => '', 'right' => '', 'bottom' => '', 'left' => '', 'unit' => '');

        $name = trim(esc_attr($name));
        if (!empty($name)) {
            foreach ($names as $field_key => $field_value) {
                $names[$field_key] = sprintf('%s[%s]', $name, $field_key);
            }
        } ?>
        <ul class="wordpress-cta-dimension-field">
            <li>
                <input type="number" name="<?php echo esc_attr($names['top']) ?>" value="<?php echo esc_attr($values->top) ?>" min="0">
                <span><?php esc_html_e('Top', 'easy-sticky-sidebar') ?></span>
            </li>

            <li>
                <input type="number" name="<?php echo esc_attr($names['right']) ?>" value="<?php echo esc_attr($values->right) ?>" min="0">
                <span><?php esc_html_e('Right', 'easy-sticky-sidebar') ?></span>
            </li>

            <li>
                <input type="number" name="<?php echo esc_attr($names['bottom']) ?>" value="<?php echo esc_attr($values->bottom) ?>" min="0">
                <span><?php esc_html_e('Bottom', 'easy-sticky-sidebar') ?></span>
            </li>

            <li>
                <input type="number" name="<?php echo esc_attr($names['left']) ?>" value="<?php echo esc_attr($values->left) ?>" min="0">
                <span><?php esc_html_e('Left', 'easy-sticky-sidebar') ?></span>
            </li>

            <li class="input-link dashicons dashicons-admin-links"></li>

            <li><?php easy_sticky_sidebar_get_unit_input($names['unit'], $values->unit, '', ['px']); ?></li>
        </ul>
<?php
    }
}

