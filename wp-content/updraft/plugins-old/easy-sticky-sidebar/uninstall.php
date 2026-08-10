<?php
/**
 * Uninstall WP CTA Plugin
 *
 * @package easy-sticky-sidebar
 */

// If uninstall not called from WordPress, exit
if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}

// Delete plugin options
delete_option('easy_sticky_sidebar_version');
delete_option('easy_sticky_sidebar_default_attachment');
delete_option('SSuprydp_action_option_url');

// Get global wpdb
global $wpdb;

// Drop custom tables
$wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}sticky_cta");
$wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}sticky_cta_options");

// Clear any cached data that has been removed
wp_cache_flush(); 