<?php
if (!defined('ABSPATH')) {
	exit;
}

/**
 * WP Sticky CTA Data
 * @package sticky-sidebar
 * @since   1.3.6
 */
class Easy_Sticky_Sidebar_CTA_Data {
    private $sticky_data = null;
    
    // Main class properties
    protected $id = 0;
    protected $SSuprydp_impressions = "0";
    protected $SSuprydp_clicks = "0";
    protected $SSuprydp_development = "development";
    protected $SSuprydp_shrink = "No";
    protected $SSuprydp_shrink_tablet = "No";
    protected $SSuprydp_shrink_mobile = "No";
    protected $SSuprydp_dis_desktop = "Yes";
    protected $SSuprydp_dis_tablet = "Yes";
    protected $SSuprydp_dis_mobile = "Yes";
    protected $SSuprydp_location = "home";
    protected $SSuprydp_location_type = "Pages";
    protected $SSuprydp_img_hideimg = "No";
    protected $SSuprydp_hideimg_tablet = "No";
    protected $SSuprydp_hideimg_mobile = "No";
    protected $sticky_s_media = "";
    protected $image_attachment_id = "0";
    protected $SSuprydp_button_option_text = "Click Here";
    protected $SSuprydp_button_option_backg_color = "#4e0d61";
    protected $SSuprydp_button_option_font = "Archivo:700";
    protected $SSuprydp_button_option_weight = "400";
    protected $SSuprydp_button_option_size = "24";
    protected $SSuprydp_button_option_align = "left";
    protected $SSuprydp_button_option_color = "#fff";
    protected $SSuprydp_content_option_text = "";
    protected $SSuprydp_content_option_font = "Open Sans";
    protected $SSuprydp_content_option_weight = "800";
    protected $SSuprydp_content_option_size = "22";
    protected $SSuprydp_content_option_color = "#383838";
    protected $SSuprydp_divider_option_color = "#1b7ccc";
    protected $SSuprydp_action_option_text = "Click Here to View";
    protected $SSuprydp_action_option_font = "Open Sans";
    protected $SSuprydp_action_option_weight = "500";
    protected $SSuprydp_action_option_size = "20";
    protected $SSuprydp_action_option_color = "#fff";
    protected $SSuprydp_action_option_url = "https://wpctapro.com/";
    protected $SSuprydp_target_blank = "No";
    protected $SSuprydp_nofollow = "No";
    protected $SSuprydp_cta_position = "center";
    protected $sidebar_template = "sticky-cta";
    protected $line_separator_show = "yes";
    protected $line_separator_color = "#fff";
    protected $collapse_on_page_load = "no";
    
    // Properties for locations
    protected $locations = [];
    protected $exclude_locations = [];
    
    // Property to store post-related data
    protected $post_data = [];
    
    // Store all dynamic properties in this array
    protected $dynamic_properties = [];
    // Track which options are explicitly saved in sticky_cta_options.
    protected $saved_option_keys = [];

    function __construct($sticky_data = []) {
        $this->sticky_data = (object) wp_parse_args($sticky_data, apply_filters( 'easy_sticky_sidebar_cta_defaults', array(
            'id' => 0,
            "SSuprydp_impressions"=>"0",
            "SSuprydp_clicks"=>"0",
            "SSuprydp_development"=>"development",
            "SSuprydp_shrink"=>"No",
            "SSuprydp_shrink_tablet"=>"No",
            "SSuprydp_shrink_mobile"=>"No",
            "SSuprydp_dis_desktop"=>"Yes",
            "SSuprydp_dis_tablet"=>"Yes",
            "SSuprydp_dis_mobile"=>"Yes",
            "SSuprydp_location"=>"home",
            "SSuprydp_location_type"=>"Pages",
            "SSuprydp_img_hideimg"=>"No",
            "SSuprydp_hideimg_tablet"=>"No",
            "SSuprydp_hideimg_mobile"=>"No",
            "sticky_s_media"=>"",
            "image_attachment_id"=>"0",
            "SSuprydp_button_option_text"=>"Click Here",
            "SSuprydp_button_option_backg_color"=>"#4e0d61",
            "SSuprydp_button_option_font"=>"Archivo:700",
            "SSuprydp_button_option_weight"=>"400",
            "SSuprydp_button_option_size"=>"24",
            "button_round"=>"5",
            "SSuprydp_button_option_align"=>"left",
            "SSuprydp_button_option_color"=>"#fff",
            "SSuprydp_content_option_text"=>"This is the Content Area. Put a description here of what you want to promote.",
            "SSuprydp_content_option_font"=>"Open Sans",
            "SSuprydp_content_option_weight"=>"800",
            "SSuprydp_content_option_size"=>"22",
            "SSuprydp_content_option_color"=>"#383838",
            "SSuprydp_divider_option_color"=>"#1b7ccc",
            "SSuprydp_action_option_text"=>"Click Here to View",
            "SSuprydp_action_option_font"=>"Open Sans",
            "SSuprydp_action_option_weight"=>"500",
            "SSuprydp_action_option_size"=>"20",
            "SSuprydp_action_option_color"=>"#fff",
            "SSuprydp_action_option_url"=> "https://wpctapro.com/",
            "SSuprydp_target_blank"=>"No",
            "SSuprydp_nofollow"=>"No",
            "SSuprydp_cta_position"=>"center",
            'sidebar_template' => 'sticky-cta',
            'line_separator_show' => 'yes',
            'line_separator_color' => '#fff',
            'collapse_on_page_load' => 'no',
            'hide_content_text' => 'no'
        )));

        $this->get_options();

        foreach ($this->sticky_data as $key => $value) {
            // Set value to existing property or store in dynamic_properties
            if (property_exists($this, $key)) {
                $this->$key = $value;
            } else {
                // Store all other properties in dynamic_properties array
                $this->dynamic_properties[$key] = $value;
            }
        }

        $this->apply_template_aware_defaults();

        $this->SSuprydp_content_option_text = stripslashes($this->SSuprydp_content_option_text);

        unset($this->sticky_data);
    }
    public function to_array_without_id() {
        $data = $this->to_array();

        // Remove identifiers/internal trackers that should never be persisted as CTA options.
        unset($data['id']);
        unset($data['sticky_data']);
        unset($data['dynamic_properties']);
        unset($data['saved_option_keys']);
        unset($data['post_data']);

        return $data;
    }
    
    /**
     * handle data for getting item
     * @package sticky-sidebar
     * @since   1.3.6
     */
    public function __get($key) {
        if (property_exists($this, $key)) {
            return $this->$key;
        }
        
        // Check for dynamic properties
        if (isset($this->dynamic_properties[$key])) {
            return $this->dynamic_properties[$key];
        }
        
        // Check in post_data for post-related information
        if (isset($this->post_data[$key])) {
            return $this->post_data[$key];
        }

        return null;
    }
    
    /**
     * Magic method to set dynamic properties
     * @package sticky-sidebar
     * @since   1.3.6
     */
    public function __set($key, $value) {
        if (property_exists($this, $key)) {
            $this->$key = $value;
        } else {
            // Store all other properties in dynamic_properties array
            $this->dynamic_properties[$key] = $value;
        }
    }

    /**
     * Support isset()/empty() on magic properties.
     * Without this, empty($obj->prop) may incorrectly evaluate true
     * and force fallbacks (e.g. sidebar_template -> sticky-cta).
     *
     * @param string $key Property key.
     * @return bool
     */
    public function __isset($key) {
        if (property_exists($this, $key)) {
            return isset($this->$key);
        }

        if (isset($this->dynamic_properties[$key])) {
            return isset($this->dynamic_properties[$key]);
        }

        if (isset($this->post_data[$key])) {
            return isset($this->post_data[$key]);
        }

        return false;
    }

    /**
     * get cta options
     * @package sticky-sidebar
     * @since   1.3.6
     */
    private function get_options() {
        if (absint($this->sticky_data->id) == 0) {
            return;
        }
    
        global $wpdb;
    
        $options = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT * FROM {$wpdb->prefix}sticky_cta_options WHERE sticky_cta_id = %d ORDER BY ID ASC",
                $this->sticky_data->id
            )
        );
    
        // Skip setting properties that are protected
        $protected_props = ['id'];
    
        foreach ($options as $option) {
            $option_name = $option->option_name;
            $this->saved_option_keys[$option_name] = true;
    
            // Skip protected properties
            if (in_array($option_name, $protected_props, true)) {
                continue;
            }
    
            // Only assign if the name is safe and doesn't start with a null character
            if (strpos($option_name, "\0") === false) {
                $this->sticky_data->{$option_name} = maybe_unserialize($option->option_value);
            }
        }
    }

    /**
     * Apply template-specific defaults only when value is not explicitly saved.
     * This keeps user-saved data intact while improving first-load defaults.
     *
     * @return void
     */
    private function apply_template_aware_defaults() {
        $template = strtolower(trim((string) ($this->sidebar_template ?? '')));
        if ($template !== 'tab-cta') {
            return;
        }

        if (is_object($this->saved_option_keys)) {
            $this->saved_option_keys = (array) $this->saved_option_keys;
        } elseif (!is_array($this->saved_option_keys)) {
            $this->saved_option_keys = [];
        }

        // CTA Tab Text
        if (empty($this->saved_option_keys['SSuprydp_button_option_text'])) {
            $current_text = trim((string) ($this->SSuprydp_button_option_text ?? ''));
            if ($current_text === '' || in_array(strtolower($current_text), array('click here', 'have questions?'), true)) {
                $this->SSuprydp_button_option_text = 'Call Now';
            }
        }

        // Font family
        if (empty($this->saved_option_keys['SSuprydp_button_option_font'])) {
            $current_font = trim((string) ($this->SSuprydp_button_option_font ?? ''));
            if (
                $current_font === ''
                || stripos($current_font, 'open sans') !== false
                || stripos($current_font, 'archivo') !== false
            ) {
                $this->SSuprydp_button_option_font = 'Arial';
            }
        }

        // Font size
        if (empty($this->saved_option_keys['SSuprydp_button_option_size'])) {
            $size = absint(preg_replace('/[^0-9.]/', '', (string) ($this->SSuprydp_button_option_size ?? '')));
            if ($size <= 0 || in_array($size, [20, 24], true)) {
                $this->SSuprydp_button_option_size = '24';
            }
        }

        // Text color
        if (empty($this->saved_option_keys['SSuprydp_button_option_color'])) {
            $color = strtolower(trim((string) ($this->SSuprydp_button_option_color ?? '')));
            if ($color === '' || in_array($color, ['#fff', '#ffffff'], true)) {
                $this->SSuprydp_button_option_color = '#fff';
            }
        }

        // Background color
        if (empty($this->saved_option_keys['SSuprydp_button_option_backg_color'])) {
            $bg = strtolower(trim((string) ($this->SSuprydp_button_option_backg_color ?? '')));
            if ($bg === '' || in_array($bg, ['#4e0d61', '#2466d5'], true)) {
                $this->SSuprydp_button_option_backg_color = '#218400';
            }
        }
    }
    public function reset_id() {
        $this->id = 0;
    }
    

     /**
     * Get calculated CTR
     * @since 1.0.1
     */
    function get_ctr() {
        if (absint($this->SSuprydp_impressions) > 0 && absint($this->SSuprydp_clicks) > 0) {
            return number_format((100 * $this->SSuprydp_clicks) / $this->SSuprydp_impressions, 2) . '%';
        }

        return "0%";
    }

    /**
     * get locations
     * @package sticky-sidebar
     * @since   1.4.0
     */
    public function get_locations() {
        $get_location_types = wordpress_cta_pro_get_location_types();
        
        $locations = (array) ($this->locations ?: []);

        $processed_locations = array_map(function($item) use($get_location_types) {
            if (is_object($item)) {
                $item = (array) $item;
            }

            if (!is_array($item) || !isset($item['type'])) {
                return false;
            }

            $type = array_filter(explode(':', $item['type']));
            $item['group'] = isset($type[0]) ? $type[0] : false;
            $item['object'] = isset($type[1]) ? $type[1] : false;

            $item['label'] = @$get_location_types[$item['group']][$item['object']];

            if (!isset($item['values']) || !is_array($item['values'])) {
                $item['values'] = [];
            }

            return $item;
        }, $locations);
        
        $this->locations = $processed_locations;
        return $this->locations;
    }

    /**
     * get exclude locations
     * @package sticky-sidebar
     * @since   1.4.0
     */
    public function get_exclude_locations() {
        $get_location_types = wordpress_cta_pro_get_location_types();
        
        $exclude_locations = (array) ($this->exclude_locations ?: []);

        $processed_exclude_locations = array_map(function($item) use($get_location_types) {
            if (!isset($item['type'])) {
                return $item;
            }

            $type = array_filter(explode(':', $item['type']));
            $item['group'] = isset($type[0]) ? $type[0] : false;
            $item['object'] = isset($type[1]) ? $type[1] : false;

            $item['label'] = @$get_location_types[$item['group']][$item['object']];

            if (!isset($item['values']) || !is_array($item['values'])) {
                $item['values'] = [];
            }

            return $item;
        }, $exclude_locations);
        
        $this->exclude_locations = $processed_exclude_locations;
        return $this->exclude_locations;
    }

    /**
     * Converts an object to array.
     * @since 1.3.6
     * @return array Object as array.
     */
    public function to_array() {
        $vars = get_object_vars($this);
        
        // Add dynamic properties to the returned array
        if (!empty($this->dynamic_properties)) {
            $vars = array_merge($vars, $this->dynamic_properties);
        }
        
        // Add post data to the returned array
        if (!empty($this->post_data)) {
            $vars = array_merge($vars, $this->post_data);
        }
        
        // Remove internal tracking properties from the returned array
        unset($vars['dynamic_properties']);
        unset($vars['sticky_data']);
        
        return $vars;
    }
}
