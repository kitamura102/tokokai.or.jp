<?php

namespace EasyStickySidebar;

if (!defined('ABSPATH')) {
	exit;
}

/**
 * Filter Class for templates
 * @since 1.5.0
 */
class TemplateFilters {
    /**
     * Constructor
     */
    function __construct() {
        add_action('easy_sticky_sidebar_sticky_cta_button', [$this, 'add_button_text']);
    }

    /**
     * Add button text at stickycta
     * @since 1.5.0
     */
    function add_button_text($stickycta) {
        echo esc_html($stickycta->SSuprydp_button_option_text);
    }
}
