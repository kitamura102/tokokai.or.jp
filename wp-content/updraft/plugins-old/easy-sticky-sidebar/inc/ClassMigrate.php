<?php

/**
 * Migrate Data class
 * @since 1.4.5
 */
class Wordpress_CTA_Migrate {

    /**
     * CTA Items
     * @since 1.4.5
     */
    var $items = [];

    public static function migrate() {
        new self();
    }

    /**
     * Set true if update any templates
     * @var boolean 
     */
    private $migrated = false;

    /**
     * Migrate data
     * @since 1.4.5
     * @return void
     */
    public function __construct() {
        $this->update_template();
        $this->update_image();
        $this->generate_style();
    }

    /**
     * Update Template
     * @since 1.4.5
     * @return void
     */
    public function update_template() {
        global $wpdb;

        $results = $wpdb->get_results("SELECT * FROM $wpdb->sticky_cta_options WHERE option_name = 'sidebar_template' AND option_value = 'closed-sliding-cta'");
        foreach ($results as $item) {
            $wpdb->update($wpdb->sticky_cta_options, array('option_value' => 'sticky-cta'), array('ID' => $item->ID), array('%s'), array('%d'));

            $option_id = 0;
            $get_row = $wpdb->get_row($wpdb->prepare("SELECT * FROM $wpdb->sticky_cta_options WHERE option_name = 'collapse_on_page_load' AND sticky_cta_id = %d LIMIT 1", $item->sticky_cta_id));
            if ($get_row) {
                $option_id = $get_row->ID;
            }

            $wpdb->replace($wpdb->sticky_cta_options, array('ID' => $option_id, 'sticky_cta_id' => $item->sticky_cta_id, 'option_name' => 'collapse_on_page_load', 'option_value' => 'yes'), array('%d', '%d', '%s', '%s'));

            $this->migrated = true;
        }

        $results = $wpdb->get_results("SELECT * FROM $wpdb->sticky_cta_options WHERE option_name = 'sidebar_template' AND option_value IN ('open-sliding-cta', 'closed-sliding-cta')");
        foreach ($results as $item) {
            $this->migrated = true;
            $wpdb->update($wpdb->sticky_cta_options, array('option_value' => 'sticky-cta'), array('ID' => $item->ID), array('%s'), array('%d'));
        }
    }

    /**
     * Update image
     * @since 1.5.2
     */
    public function update_image() {
        $design_template_images = get_option('wordpress_cta_design_template_images', []);
        if ( empty($design_template_images)) {
            return;
        }

        if (!is_array($design_template_images)) {
            $design_template_images = [];
        }

        foreach ($design_template_images as $template_key => $attach_id) {
            $template_key = sanitize_title($template_key);
            update_post_meta($attach_id, 'easy_sticky_sidebar_attachment', $template_key);
            unset($design_template_images[$template_key]);
        }

        update_option('wordpress_cta_design_template_images', $design_template_images);
    }

    /**
     * Generate style if any update available
     * @since 1.5.1
     */
    public function generate_style() {
        if (!$this->migrated) {
            return;
        }

        $generate = new Easy_Sticky_CTA_Generate_CSS();
        $generate->generate_style();
    }
}
