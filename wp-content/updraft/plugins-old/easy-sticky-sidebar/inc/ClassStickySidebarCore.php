<?php
if (!defined('ABSPATH')) {
	exit;
}

/*
 * StickySidebar Core funtions
 * @package wp-dynamic-shortcodes/inc
 * @since   1.2.0
 */

class SSuprydpStickySidebarCore {
    /**
     * class constructor 
     */
    function __construct() {}

    /**
     * get the content from view file
     * @param String $viewname view file name
     * @param Array $data Data to send into view file
     * @throws ApiException on a non 2xx response
     * @return HTML
     */
    public function getView($viewname, array $data = []) {
        if (!empty($data)) {
            foreach ($data as $key => $value) {
                $$key = $value;
            }
        }

		$viewpath = apply_filters( 'easty_sticky_sidebar_view_template', get_stylesheet_directory() . "/sticky-sidebar/{$viewname}.php", $data);
        if (!file_exists($viewpath)) {
            $viewpath = EASY_STICKY_SIDEBAR_PLUGIN_DIR . "/views/{$viewname}.php";
        }

        ob_start();
        require($viewpath);
        return ob_get_clean();
    }
}
