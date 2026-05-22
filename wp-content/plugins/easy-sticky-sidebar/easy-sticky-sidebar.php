<?php

/**
 * Plugin Name: WP CTA - Call Now Button, Sticky Button & Call to Action Builder
 * Description: WordPress Call To Action builder that creates sticky buttons, call now buttons and CTAs to boost clicks, increase sales and generate leads.
 * Version: 2.3.0
 * Author: WP CTA PRO
 * Text Domain: easy-sticky-sidebar
 * Author URI: https://wpctapro.com/
 * Requires at least: 5.0
 * Tested up to: 6.9
 * Requires PHP: 7.4
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * @package easy-sticky-sidebar
 */
if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly.
}

define('EASY_STICKY_SIDEBAR_VERSION', '2.3.0');
define('EASY_STICKY_SIDEBAR_PLUGIN_DIR', untrailingslashit(plugin_dir_path(__FILE__)));
define('EASY_STICKY_SIDEBAR_PLUGIN_URL', untrailingslashit(plugin_dir_url(__FILE__)));
define('EASY_STICKY_SIDEBAR_PLUGIN_FILE', __FILE__);
define('EASY_STICKY_SIDEBAR_PLUGIN_BASENAME', plugin_basename(__FILE__));

if (!class_exists('Easy_Sticky_Sidebar')) {
	include_once EASY_STICKY_SIDEBAR_PLUGIN_DIR . '/inc/ClassStickySidebar.php';
}

/**
 * Main instance of Easy_Sticky_Sidebar.
 *
 * @since  1.2.0
 * @return Easy_Sticky_Sidebar
 */
function easy_sticky_sidebar()
{
	return Easy_Sticky_Sidebar::instance();
}

$GLOBALS['easy_sticky_sidebar'] = easy_sticky_sidebar();


