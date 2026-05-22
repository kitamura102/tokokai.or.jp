<?php
if (!defined('ABSPATH')) {
	exit;
}

/**
 * Filter Class for templates
 * @since 1.5.0
 */
class Easy_Sticky_Sidebar_Template_Filters {
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
        $button_text = trim((string) ($stickycta->SSuprydp_button_option_text ?? ''));
        $label_html = sprintf(
            '<span class="ess-sticky-sidebar-button-label">%s</span>',
            esc_html($button_text)
        );
        $icon_html = function_exists('easy_sticky_sidebar_get_button_icon_html')
            ? easy_sticky_sidebar_get_button_icon_html($stickycta)
            : '';
        $icon_position = function_exists('easy_sticky_sidebar_get_button_icon_position')
            ? easy_sticky_sidebar_get_button_icon_position($stickycta)
            : 'before';
        $button_html = ($icon_position === 'after')
            ? $label_html . $icon_html
            : $icon_html . $label_html;

        echo wp_kses(
            $button_html,
            array(
                'span' => array(
                    'class' => array(),
                ),
                'i' => array(
                    'class' => array(),
                    'style' => array(),
                ),
            )
        );
    }
}
