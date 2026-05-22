<?php

if (! defined('ABSPATH')) {
    exit;
}

class Easy_Sticky_Sidebar_List  extends WP_List_Table
{

    /**
     * Constructor.
     */
    public function __construct()
    {
        add_filter('set-screen-option', [__CLASS__, 'set_screen'], 20, 3);
        parent::__construct();
    }

    /**
     * set screen option $value.
     * @since  1.0.1
     */
    public static function set_screen($status, $option, $value)
    {
        return $value;
    }

    /**
     * add options for screen setting.
     * @since  1.0.1
     */
    public function screen_option()
    {
        add_screen_option('per_page', [
            'label' => __('Sidebar Per Page', 'easy-sticky-sidebar'),
            'default' => 15,
            'option' => 'sidebar_per_page'
        ]);
    }

    /**
     * Add table nav
     * @since  1.4.0
     */
    function extra_tablenav($which)
    {
        // Always show Add New CTA button - removed restriction
        return printf(
            '<a style="margin-right: 10px" class="btn-add-new button-primary" href="%s">%s</a>',
            esc_url(admin_url('admin.php?page=add-easy-sticky-sidebar')),
            esc_html__('Add New CTA', 'easy-sticky-sidebar')
        );
    }

    /**
     * Override the parent columns method. Defines the columns to use in your listing table
     *
     * @return Array
     */
    public function get_columns()
    {

        $columns = array(
            'cb'        => '<input type="checkbox" />',
            'name'      => __('Name', 'easy-sticky-sidebar'),
           
            'impressions' => __('Impressions', 'easy-sticky-sidebar'),
            'clicks'    => __('Clicks', 'easy-sticky-sidebar'),
            'ctr'      => __('CTR%', 'easy-sticky-sidebar'),
            'status'   => __('Status', 'easy-sticky-sidebar'),
            'location'    => __('Location', 'easy-sticky-sidebar'),
            'template'  => __('Template', 'easy-sticky-sidebar'),
            'position'  => __('Position', 'easy-sticky-sidebar'),
            'action'    => __('Action', 'easy-sticky-sidebar'),
        );

        // Old Columns
        // $columns = array(
        //     'id'        => __('#ID', 'easy-sticky-sidebar'),
        //     'name'      => __('Name', 'easy-sticky-sidebar'),
        //     'display'       => __('Display', 'easy-sticky-sidebar'),
        //     'template'        => __('Template', 'easy-sticky-sidebar'),
        //     'position'        => __('Position', 'easy-sticky-sidebar'),
        //     'action'    => __('Action', 'easy-sticky-sidebar'),
        // );

        return $columns;
    }

    /**
     * Define what data to show on each column of the table
     *
     * @param  Array $item        Data
     * @param  String $column_name - Current column name
     *
     * @return Mixed
     */
    public function column_default( $sidebar, $column_name ) {

        $templates = [
            'sticky-cta' => __( 'Open Sliding CTA', 'easy-sticky-sidebar' ),
            'tab-cta'    => __( 'Tab CTA', 'easy-sticky-sidebar' )
        ];

        switch ( $column_name ) {
            case 'position':
                return ucfirst( $sidebar->SSuprydp_cta_position );
            case 'template':
                return $templates[ $sidebar->sidebar_template ];
            case 'impressions':
                return $sidebar->SSuprydp_impressions;
            case 'clicks':
                return absint($sidebar->SSuprydp_clicks);
            case 'ctr':
                return esc_html($sidebar->get_ctr());
            default:
                return print_r( $sidebar, true );
        }
    }


    /**
     * Display the status column as a dropdown (similar to the pro plugin).
     */
    function column_status($sidebar)
    {
        ob_start();
        easy_sticky_sidebar_get_status_menu($sidebar);
        return ob_get_clean();
    }


    /**
     * Get the bulk actions
     * @return array
     */
    public function get_bulk_actions()
    {
        return array(
            'delete' => __('Delete', 'easy-sticky-sidebar')
        );
    }

    /**
     * Checkbox column
     */
    function column_cb($sidebar)
    {
        return sprintf(
            '<input type="checkbox" name="sidebar[]" value="%s" />',
            esc_attr($sidebar->__get('id'))
        );
    }

    /**
     * name column 
     * @since 1.0.1
     */
    function column_name($sidebar)
    {
        return sprintf(
            '<input class="sticky-sidebar-name-input" type="text" value="%s" placeholder="%s" data-sticky="%d"><i class="dashicons dashicons-edit"></i>',
            esc_attr($sidebar->sidebar_name),
            esc_attr__('Type sidebar name here', 'easy-sticky-sidebar'),
            $sidebar->__get('id')
        );
    }

    /**
     * Display the location column
     */
    function column_location($sidebar)
    {
        // First check the main property
        $location = $sidebar->SSuprydp_location;

        // If it's a numeric value, it's likely a post/page ID
        if (is_numeric($location) && $location > 0) {
            // Special handling for home page
            if ($location == get_option('page_on_front')) {
                return __('Home Page', 'easy-sticky-sidebar');
            }

            $post = get_post($location);
            if ($post) {
                // translators: %s: Post title.
                return sprintf(esc_html__('Single: %s', 'easy-sticky-sidebar'), esc_html($post->post_title));
            }
        }

        // Rest of the function remains the same
        if (empty($location)) {
            global $wpdb;
            $location = $wpdb->get_var($wpdb->prepare(
                "SELECT option_value FROM {$wpdb->prefix}sticky_cta_options 
                WHERE sticky_cta_id = %d AND option_name = 'cta_location'",
                $sidebar->__get('id')
            ));
        }

        switch ($location) {
            case 'home':
                return __('Home Page', 'easy-sticky-sidebar');
            case 'entire_site':
                return __('Entire Site', 'easy-sticky-sidebar');
            case '':
                return __('Entire Site', 'easy-sticky-sidebar');
            default:
                // translators: %s: Location label.
                return sprintf(esc_html__('Custom Location: %s', 'easy-sticky-sidebar'), esc_html($location));
        }
    }

    /**
     * Template Column 
     * @since 1.3.5
     */
    function column_template($sidebar)
    {
        $templates = easy_sticky_sidebar_templates();
        return empty($templates[$sidebar->sidebar_template]) ? '' : $templates[$sidebar->sidebar_template];
    }

    /**
     * action column 
     * @since 1.0.1
     */
    // function column_action($sidebar)
    // {
    //     $permalink = add_query_arg([
    //         'id' => $sidebar->id,
    //         '_nonce' => wp_create_nonce('nonce_cta_action_' . $sidebar->id),
    //     ], admin_url('admin.php?page=easy-sticky-sidebars'));

    //     $actions[] = sprintf('<a href="%s">%s</a>', admin_url('admin.php?page=edit-easy-sticky-sidebar&id=' . $sidebar->id),  __('Edit', 'easy-sticky-sidebar'));
    //     $actions[] = sprintf('<a class="cta-delete" href="%s">%s</a>', add_query_arg('action', 'delete', $permalink), __('Delete', 'easy-sticky-sidebar'));
    //     return implode(' | ', $actions);
    // }

    function column_action($sidebar)
    {
        $permalink = add_query_arg([
            'id' => $sidebar->__get('id'),
            '_nonce' => wp_create_nonce('nonce_cta_action_' . $sidebar->__get('id')),
        ], admin_url('admin.php?page=easy-sticky-sidebars'));

        $actions = [];
        $actions[] = sprintf(
            '<a href="%s" class="button button-small">%s</a>',
            esc_url(admin_url('admin.php?page=edit-easy-sticky-sidebar&id=' . absint($sidebar->id))),
            esc_html__('Edit', 'easy-sticky-sidebar')
        );
        $actions[] = sprintf(
            '<a href="%s" class="button button-small button-link-delete cta-delete">%s</a>',
            esc_url(add_query_arg('action', 'delete', $permalink)),
            esc_html__('Delete', 'easy-sticky-sidebar')
        );

        return '<div class="action-buttons">' . implode(' ', $actions) . '</div>';
    }

    /**
     * Process bulk actions
     */
    public function process_bulk_action()
    {
        global $wpdb;

        // Security check
        if (isset($_POST['_wpnonce']) && !empty($_POST['sidebar'])) {
            $nonce  = filter_input(INPUT_POST, '_wpnonce', FILTER_SANITIZE_SPECIAL_CHARS);
            $action = 'bulk-' . $this->_args['plural'];

            if (!wp_verify_nonce($nonce, $action))
                return;

            $sidebar_ids = array_map('absint', $_POST['sidebar']);

            switch ($this->current_action()) {
                case 'delete':
                    foreach ($sidebar_ids as $id) {
                        $wpdb->delete(
                            $wpdb->prefix . 'sticky_cta',
                            ['id' => $id],
                            ['%d']
                        );
                    }
                    add_settings_error(
                        'bulk_action',
                        'bulk_action',
                        __('Selected items deleted successfully.', 'easy-sticky-sidebar'),
                        'updated'
                    );
                    break;
            }
        }
    }

    /**
     * Prepare the items for the table to process
     * @return Void
     */
    public function prepare_items()
    {
        global $wpdb;

        // Process bulk actions if any
        $this->process_bulk_action();

        $limit = 999;

        $sidebars = $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM {$wpdb->prefix}sticky_cta ORDER BY id LIMIT 0, %d",
            $limit
        ));
        $total_sidebar = count($sidebars);

        $this->items = array_map(function ($sidebar) {
            return new Easy_Sticky_Sidebar_CTA_Data($sidebar);
        }, $sidebars);

        $this->set_pagination_args(array(
            'total_items' => $total_sidebar,
            'per_page'    => $limit
        ));

        $this->_column_headers = array($this->get_columns());
    }


    /**
     * admin page for form entries
     * @since  1.0.1
     */
    public function output()
    {
        $this->prepare_items(); ?>
<div class="wrap wrap-easy-sticky-sidebar">
    <?php easy_sticky_sidebar_get_header() ?>

    <div class="easy-sticky-sidebar-container">
        <hr class="wp-header-end">
        <form method="post">
            <?php
                    wp_nonce_field('bulk-' . $this->_args['plural']);
                    $this->display();
                    ?>
        </form>

        <?php if (!easy_sticky_sidebar_has_pro()) : ?>
        <div class="wordpress-cta-advertisement">
            <span class="div-two">
                <a href="https://wpctapro.com/" target="_blank"><img
                        src="<?php echo esc_url(EASY_STICKY_SIDEBAR_PLUGIN_URL); ?>/assets/img/ads.jpeg" /></a>
            </span>
            <span class="div-two">
                <a href="https://wordpress.org/plugins/ez-countdown-timer//" target="_blank"><img
                        src="<?php echo esc_url(EASY_STICKY_SIDEBAR_PLUGIN_URL); ?>/assets/img/ezcountdowntimer.jpg" /></a>
            </span>
        </div>
        <?php endif; ?>
    </div>
</div>
<?php
    }
}
