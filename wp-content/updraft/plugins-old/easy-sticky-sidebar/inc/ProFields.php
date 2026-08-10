<?php
if (!defined('ABSPATH')) {
	exit;
}
/*
 * Wordpress_CTA_Pro_Content
 * @package sticky-sidebar/inc
 * @since 1.4.5
 */

class Wordpress_CTA_Pro_Placeholder {
    public function __construct() {
        add_filter('wordpress_cta_free/pro_fields', [$this, 'register_placeholder'], 1);
        add_filter('easy_sticky_sidebar_tabs', [$this, 'add_display_setting_tab'], 12);
    }

    public function add_display_setting_tab($tabs) {
        if (!isset($tabs['display-setting'])) {
            $tabs['display-setting'] = [
                'label' => __("Display Behaviour", 'easy-sticky-sidebar'),
                'callback' => [$this, 'display_setting_tab'],
                'priority' => 6
            ];
        }

        if (isset($tabs['responsive']) && (!isset($tabs['display-setting']) || $tabs['display-setting']['priority'] <= $tabs['responsive']['priority'])) {
            $tabs['responsive']['priority'] = 7;
        }

        if (isset($tabs['css']) && (!isset($tabs['display-setting']) || $tabs['display-setting']['priority'] <= $tabs['css']['priority'])) {
            $tabs['css']['priority'] = 8;
        }

        return $tabs;
    }

    public function register_placeholder($elements) {
        $elements['show_statistics'] = array('hook' => 'easy_sticky_sidebar_before_tab', 'callback' => [$this, 'show_statistics']);
        
		$elements['cta_location'] = array('hook' => 'easy_sticky_sidebar_form_cta_location', 'callback' => [$this, 'cta_location']);
        
        $elements['html_cta_disable_collapse'] = array('hook' => 'easy_sticky_sidebar_cta_scroll_options', 'callback' => [$this, 'disable_collapse']);

        $elements['cta_width'] = array('hook' => 'easy_sticky_sidebar_cta_adjustment', 'callback' => [$this, 'cta_width']);

        $elements['hide_image'] = array('hook' => 'easy_sticky_sidebar_cta_image', 'callback' => [$this, 'hide_image'], 'priority' => 3);

        $elements['letter_spacing'] = array('hook' => 'easy_sticky_sidebar_button_options', 'callback' => [$this, 'button_letter_spacing'], 'priority' => 32);
        if (!has_wordpress_cta_pro()) {
            $elements['button_padding'] = array('hook' => 'easy_sticky_sidebar_button_options', 'callback' => [$this, 'button_padding'], 'priority' => 47);
        }
        $elements['button_round'] = array('hook' => 'easy_sticky_sidebar_button_options', 'callback' => [$this, 'button_border_round'], 'priority' => 50);
        
        $elements['content_letter_spacing'] = array('hook' => 'easy_sticky_sidebar_content_option', 'callback' => [$this, 'content_letter_spacing'], 'priority' => 12);
        if (!has_wordpress_cta_pro()) {
            $elements['content_padding'] = array('hook' => 'easy_sticky_sidebar_content_option', 'callback' => [$this, 'content_padding'], 'priority' => 25);
        }
        
        $elements['line_separator_thickness'] = array('hook' => 'easy_sticky_sidebar_line_separator', 'callback' => [$this, 'line_separator_thickness'], 'priority' => 5);
        
        $elements['call_to_action_show_hide'] = array('hook' => 'easy_sticky_sidebar_call_to_action', 'callback' => [$this, 'call_to_action_show_hide'], 'priority' => 1);
        $elements['call_to_action_letter_spacing'] = array('hook' => 'easy_sticky_sidebar_call_to_action', 'callback' => [$this, 'call_to_action_letter_spacing'], 'priority' => 16);
        if (!has_wordpress_cta_pro()) {
            $elements['call_to_action_padding'] = array('hook' => 'easy_sticky_sidebar_call_to_action', 'callback' => [$this, 'call_to_action_padding'], 'priority' => 25);
        }
        $elements['call_to_action_link_or_button'] = array('hook' => 'easy_sticky_sidebar_call_to_action', 'callback' => [$this, 'call_to_action_link_or_button'], 'priority' => 21);

        $elements['show_close_button'] = array('hook' => 'easy_sticky_sidebar_close_button_options', 'callback' => [$this, 'close_button_option'], 'priority' => 5);
        $elements['enable_box_shadow'] = array('hook' => 'easy_sticky_sidebar_box_shadow_options', 'callback' => [$this, 'box_shadow_toggle'], 'priority' => 1);
		
        return $elements;
    }

    public function display_setting_tab($stickycta) { ?>
        <h4 class="wordpress-cta-heading"><?php esc_html_e("Display Setting", "easy-sticky-sidebar"); ?></h4>
        <div class="wordpress-cta-pro-features">

            <div class="SSuprydp_field_wrap">
                <h4 class="heading"><?php esc_html_e("Show CTA", "easy-sticky-sidebar"); ?></h4>
            </div>

            <div class="SSuprydp_field_wrap wordpress-cta-pro-feature-lock-inline-container">
                <label><?php esc_html_e("Trigger", "easy-sticky-sidebar"); ?></label>
                <select name="display_trigger" disabled>
                    <option><?php esc_html_e("Show immediately", "easy-sticky-sidebar"); ?></option>
                    <option><?php esc_html_e("Show after X seconds", "easy-sticky-sidebar"); ?></option>
                    <option><?php esc_html_e("Show after X% scroll", "easy-sticky-sidebar"); ?></option>
                </select>
                <?php Wordpress_CTA_Free_Utils::get_inline_lock() ?>
            </div>

            <div class="SSuprydp_field_wrap wordpress-cta-pro-feature-lock-inline-container ess-display-trigger-seconds">
                <label><?php esc_html_e("After X seconds", "easy-sticky-sidebar"); ?></label>
                <input type="number" style="width: 80px" disabled>
                <?php Wordpress_CTA_Free_Utils::get_inline_lock() ?>
            </div>

            <div class="SSuprydp_field_wrap wordpress-cta-pro-feature-lock-inline-container ess-display-trigger-scroll">
                <label><?php esc_html_e("After X% scroll", "easy-sticky-sidebar"); ?></label>
                <input type="number" style="width: 80px" disabled> %
                <?php Wordpress_CTA_Free_Utils::get_inline_lock() ?>
            </div>

            <div class="SSuprydp_field_wrap wordpress-cta-pro-feature-lock-inline-container">
                <label><?php esc_html_e("Animation", "easy-sticky-sidebar"); ?></label>
                <select name="display_animation" disabled>
                    <option><?php esc_html_e("None", "easy-sticky-sidebar"); ?></option>
                    <option><?php esc_html_e("Fade in", "easy-sticky-sidebar"); ?></option>
                    <option><?php esc_html_e("Slide up", "easy-sticky-sidebar"); ?></option>
                    <option><?php esc_html_e("Slide down", "easy-sticky-sidebar"); ?></option>
                    <option><?php esc_html_e("Slide left", "easy-sticky-sidebar"); ?></option>
                    <option><?php esc_html_e("Slide right", "easy-sticky-sidebar"); ?></option>
                </select>
                <?php Wordpress_CTA_Free_Utils::get_inline_lock() ?>
            </div>

            <div class="SSuprydp_field_wrap" style="margin-top: 14px;">
                <h4 class="heading"><?php esc_html_e("Hide CTA", "easy-sticky-sidebar"); ?></h4>
            </div>

            <div class="SSuprydp_field_wrap wordpress-cta-pro-feature-lock-inline-container">
                <label><?php esc_html_e("Auto Hide", "easy-sticky-sidebar"); ?></label>
                <select name="hide_behavior" disabled>
                    <option><?php esc_html_e("Don't auto hide", "easy-sticky-sidebar"); ?></option>
                    <option><?php esc_html_e("Hide after X seconds", "easy-sticky-sidebar"); ?></option>
                    <option><?php esc_html_e("Hide near page bottom", "easy-sticky-sidebar"); ?></option>
                </select>
                <?php Wordpress_CTA_Free_Utils::get_inline_lock() ?>
            </div>

            <div class="SSuprydp_field_wrap wordpress-cta-pro-feature-lock-inline-container ess-hide-after-seconds">
                <label><?php esc_html_e("Hide after X seconds", "easy-sticky-sidebar"); ?></label>
                <input type="number" style="width: 80px" disabled>
                <?php Wordpress_CTA_Free_Utils::get_inline_lock() ?>
            </div>

            <div class="SSuprydp_field_wrap" style="margin-top: 14px;">
                <h4 class="heading"><?php esc_html_e("Display Frequency", "easy-sticky-sidebar"); ?></h4>
            </div>

            <div class="SSuprydp_field_wrap wordpress-cta-pro-feature-lock-inline-container">
                <label><?php esc_html_e("Frequency", "easy-sticky-sidebar"); ?></label>
                <select name="display_frequency" disabled>
                    <option><?php esc_html_e("Show every time", "easy-sticky-sidebar"); ?></option>
                    <option><?php esc_html_e("Show once per visit", "easy-sticky-sidebar"); ?></option>
                    <option><?php esc_html_e("Show once every 24 hours", "easy-sticky-sidebar"); ?></option>
                    <option><?php esc_html_e("Show once every 7 days", "easy-sticky-sidebar"); ?></option>
                </select>
                <?php Wordpress_CTA_Free_Utils::get_inline_lock() ?>
            </div>

            <div class="SSuprydp_field_wrap" style="margin-top: 14px;">
                <h4 class="heading"><?php esc_html_e("After Close", "easy-sticky-sidebar"); ?></h4>
            </div>

            <div class="SSuprydp_field_wrap wordpress-cta-pro-feature-lock-inline-container">
                <label><?php esc_html_e("Behavior", "easy-sticky-sidebar"); ?></label>
                <select name="after_close_behavior" disabled>
                    <option><?php esc_html_e("Show again next visit", "easy-sticky-sidebar"); ?></option>
                    <option><?php esc_html_e("Don't show again for X time", "easy-sticky-sidebar"); ?></option>
                </select>
                <?php Wordpress_CTA_Free_Utils::get_inline_lock() ?>
            </div>

            <div class="SSuprydp_field_wrap wordpress-cta-pro-feature-lock-inline-container ess-after-close-time">
                <label><?php esc_html_e("Don't show again for", "easy-sticky-sidebar"); ?></label>
                <input type="number" style="width: 80px" disabled>
                <select name="after_close_time_unit" disabled>
                    <option><?php esc_html_e("Hours", "easy-sticky-sidebar"); ?></option>
                    <option><?php esc_html_e("Days", "easy-sticky-sidebar"); ?></option>
                </select>
                <?php Wordpress_CTA_Free_Utils::get_inline_lock() ?>
            </div>
        </div>
    <?php }

	/**
	 * Show statistics
	 * @since 1.4.5
	 */
    public function show_statistics($stickycta) { ?>
        <h2 class="wordpress-cta-heading"><?php esc_html_e('CTA Stats', 'easy-sticky-sidebar') ?></h2>
        <div class="wordpress-cta-pro-features">
           
            <ul class="wordpress-cta-pro-stats">
                <li>
                    <i class="dashicons dashicons-info" data-toggle="tooltip" title="Impressions are the number of times your CTA is displayed, no matter if it was clicked or not."></i>
                    <h4 class="stats-label"><?php esc_html_e('Impressions', 'easy-sticky-sidebar') ?></h4>
                    <span class="result id" id="cust-img" style="filter:blur(0px)"><?php echo esc_html(absint($stickycta->SSuprydp_impressions)); ?></span>
                </li>
			 
                <li>
                    <i class="dashicons dashicons-info" data-toggle="tooltip" title="Number of times your CTA was clicked."></i>
                    <h4 class="stats-label"><?php esc_html_e('Clicks', 'easy-sticky-sidebar') ?></h4>
                    <span class="result"><?php echo esc_html(absint($stickycta->SSuprydp_clicks)); ?></span>
                </li>

                <li>
                    <i class="dashicons dashicons-info" data-toggle="tooltip" title="Clickthrough rate (CTR) is the number of clicks that your CTA receives divided by the number of times your CTA is shown (impressions)."></i>
                    <h4 class="stats-label"><?php esc_html_e('CTR', 'easy-sticky-sidebar') ?></h4>
                    <span class="result"><?php echo esc_html($stickycta->get_ctr()); ?></span>
                </li>
            </ul>
        </div>
		<?php	
    }

	/**
	 * Add page location
	 * @since 1.4.5
	 */
	function cta_location() {
		

		?>
		<div class="wordpress-cta-pro-features">
		
			<div class="SSuprydp_field_wrap location-field-wrapper">
				<label>Include</label>
			
				<?php 
				$location_types = wordpress_cta_get_location_types();

				echo '<select name="%name%[%number%][type]">';
				foreach ($location_types as $group => $locations) {
					echo '<optgroup label="' . esc_attr(wordpress_cta_location_group($group)) . '">';
					foreach ($locations as $lkey => $location) {
						$value = $group . ':' . $lkey;
						printf('<option value="%s">%s</option>', esc_attr($value), esc_html($location));
					}
					echo '</optgroup>';
				}
				echo '</select>';
				?>
				<ul class="location-field-container" id="cta-locations" data-btn-add="#btn-add-location" data-name="locations"></ul>

				<div class="SSuprydp_field_wrap location-field-wrapper wordpress-cta-pro-element">
				<a class="button-primary button-large" id="btn-add-location" style="margin-top:20px"><?php esc_html_e('Add condition', 'easy-sticky-sidebar') ?></a>

				<?php Wordpress_CTA_Free_Utils::get_inline_lock() ?>
				</div>
			</div>
			<script>
    (function($) {
        $(document).ready(function() {
            // Process all location select dropdowns
            $('select[name*="[type]"]').each(function() {
                var $select = $(this);
                
                $select.find('option').each(function() {
                    var $option = $(this);
                    var optionText = $option.text();
                    
                    // Disable if option contains "(pro feature)"
                    if (optionText.includes('(pro feature)')) {
                        $option.prop('disabled', true)
                               .addClass('location-blur');
                    } else {
                        $option.prop('disabled', false)
                               .removeClass('location-blur')
                               .css('color', '#000');
                    }
                });
                
                // Ensure the select itself is enabled
                $select.prop('disabled', false);
            });
        });
    })(jQuery);
    </script>  
			<div class="gap-10"></div>

			<div class="SSuprydp_field_wrap location-field-wrapper wordpress-cta-pro-element">
				<label>Exclude</label>
				<ul class="location-field-container" id="cta-exclude-locations" data-btn-add="#btn-add-exclude-location" data-name="exclude_locations"></ul>
				<a class="button-primary button-large" id="btn-add-exclude-location"><?php esc_html_e('Add condition', 'easy-sticky-sidebar') ?> </a>

				<?php Wordpress_CTA_Free_Utils::get_inline_lock() ?>
			</div>
		</div>
		<?php
	}

    /**
     * Add option for prevent collapse on scroll
     * @since 1.4.5
     */
    function disable_collapse($stickycta) { ?>
        <div class="SSuprydp_field_wrap keep_html_cta_open-option wordpress-cta-pro-element bo">
		
		<label class="SSuprydp_switch">
			<h4 class="heading h"><?php esc_html_e('Disable Collapse (Keep CTA open after scroll)', 'easy-sticky-sidebar'); ?></h4>
           <input type="checkbox"> </label>

			<?php Wordpress_CTA_Free_Utils::get_inline_lock() ?>
		
		<!-- end wrap -->
        <?php
    }

    /**
	 * Add CTA width field
	 * @since 1.4.5
	 */
    public function cta_width($stickycta) {?>
	
		<div class="wordpress-cta-pro-features bli">
			
            <div class="SSuprydp_field_wrap wordpress-cta-pro-element " style="margin-top:20px">
			
				<h4 class="heading h"><?php esc_html_e('Enable CTA Width', 'easy-sticky-sidebar') ?></h4>
				<label class="SSuprydp_switch has-label" style="margin-bottom: 0">      
                    <input type="checkbox">
					</label>
					<?php Wordpress_CTA_Free_Utils::get_inline_lock() ?>
               


			
			
            </div>

            <div id="ess-cta-width">	
                <div class="SSuprydp_field_wrap wordpress-cta-pro-element">
                    <label class="h"><?php esc_html_e('CTA Width', 'easy-sticky-sidebar') ?>
					<br>
                    <input style="width: 50px;text-align:right" type="number">
                    <?php easy_sticky_sidebar_get_unit_input(''); ?>
					</label>

					<?php Wordpress_CTA_Free_Utils::get_inline_lock() ?>
                </div>

                <div class="SSuprydp_field_wrap wordpress-cta-pro-element">
                    <label class="h"><?php esc_html_e('CTA Tablet Width', 'easy-sticky-sidebar') ?>
					<br>
                    <input style="width: 50px;text-align:right" type="number">
                    <?php easy_sticky_sidebar_get_unit_input(''); ?>
					</label>
					<?php Wordpress_CTA_Free_Utils::get_inline_lock() ?>
					
                </div>

                <div class="SSuprydp_field_wrap wordpress-cta-pro-element">
				<label class="h"><?php esc_html_e('CTA Mobile Width', 'easy-sticky-sidebar') ?>
				<br>
                    <input style="width: 50px;text-align:right" type="number">
                   
					</label>
					<?php Wordpress_CTA_Free_Utils::get_inline_lock() ?>
                </div>
				
            </div>
        </div>
		<?php	
    }

    /**
	 * CTA field for Hide or Show image
	 * @since 1.4.5
	 */
	function hide_image() { ?>
		<div class="wordpress-cta-pro-features">
			
			<div class="SSuprydp_field_wrap wordpress-cta-pro-element">
			<label class="h"><?php esc_html_e('Hide / Show Image', 'easy-sticky-sidebar') ?></label>
				<label class="SSuprydp_switch has-label h">
				<input type="checkbox" class="checkbox-hide-show"> 
					
				</label>
				
				<?php Wordpress_CTA_Free_Utils::get_inline_lock() ?>
			</div>
			<?php

			?>
			<div class="SSuprydp_field_wrap wordpress-cta-pro-element">
				<label class="h"><?php esc_html_e('Image Height', 'easy-sticky-sidebar') ?></label>
				<input style="width: 50px;text-align:right" type="number"> px
				<?php Wordpress_CTA_Free_Utils::get_inline_lock() ?>
			</div>
		</div>
		<?php
	}

    /**
	 * CTA button letter spacing
	 * @since 1.4.5
	 */
	function button_letter_spacing() { ?>
		<div class="SSuprydp_field_wrap sticky-sidebar-button_letter_spacing">
			<label><?php esc_html_e("Letter Spacing", "easy-sticky-sidebar"); ?></label>
			<div class="wordpress-cta-pro-feature-lock-inline-container">
				<input type="number" style="width: 50px"> px
				<?php Wordpress_CTA_Free_Utils::get_inline_lock() ?>
			</div>
		</div>
		<?php
	}

    /**
	 * CTA button letter spacing
	 * @since 1.4.5
	 */
    function button_border_round() {?>
        <div class="SSuprydp_field_wrap sticky-sidebar-button_radius wordpress-cta-pro-element">
            <label><?php esc_html_e("Button Corners (border radius)", "easy-sticky-sidebar"); ?></label>
            <input type="number" style="width: 50px"> px
            <?php Wordpress_CTA_Free_Utils::get_inline_lock() ?>
        </div>
        <?php
    }

    /**
     * CTA button padding (pro)
     * @since 1.4.5
     */
    function button_padding($stickycta) { ?>
        <div class="SSuprydp_field_wrap wordpress-cta-pro-element">
            <label><?php esc_html_e("Padding", "easy-sticky-sidebar"); ?></label>
            <?php Wordpress_CTA_Free_Utils::get_dimensions_field(''); ?>
            <?php Wordpress_CTA_Free_Utils::get_inline_lock(['top' => '-2px', 'bottom' => 'auto']) ?>
        </div>
    <?php
    }

	/**
	 * CTA content padding
	 * @since 1.4.5
	 */
	function content_padding($stickycta) {?>
		<div class="SSuprydp_field_wrap wordpress-cta-pro-element">
			<label><?php esc_html_e("Padding", "easy-sticky-sidebar"); ?></label>
			<?php Wordpress_CTA_Free_Utils::get_dimensions_field(''); ?>
			<?php Wordpress_CTA_Free_Utils::get_inline_lock(['top' => '-2px', 'bottom' => 'auto']) ?>
		</div>

<script>
	jQuery(document).ready(function($) {
      function updateInputStates() {
        $('.wordpress-cta-pro-feature-lock-inline-container').each(function() {
            // Check if lock element exists in this container
            const hasLock = $(this).find('.wordpress-cta-pro-feature-lock-inline').length > 0;
            
            // Disable all inputs/selects/textarea inside locked containers
            $(this).find('input, select, textarea').prop('disabled', hasLock);
        });
    }

    // Run on page load
    updateInputStates();
    
    // Run when AJAX completes (for dynamic content)
    $(document).ajaxComplete(updateInputStates);
    
    // Add click handler for lock icons
    $(document).on('click', '.wordpress-cta-pro-feature-lock-inline', function(e) {
        // Open the upgrade popup first, then allow users to click links inside the popup.
        e.preventDefault();
        $('#wordpress-cta-pro-feature-popup').trigger('open');
    });
});
	</script>
		
		<?php
	}

    /**
	 * CTA content letter spacing
	 * @since 1.4.5
	 */
	function content_letter_spacing() {?>
		<div class="SSuprydp_field_wrap">
			<label><?php esc_html_e("Letter Spacing", "easy-sticky-sidebar"); ?></label>
			<div class="wordpress-cta-pro-feature-lock-inline-container">
				<input type="number" style="width: 50px" disabled> px
				<?php Wordpress_CTA_Free_Utils::get_inline_lock() ?>
			</div>
		</div>
		<?php
	}

    /**
	 * CTA call to action letter spacing
	 * @since 1.4.5
	 */
	function line_separator_thickness() {?>
		<div class="SSuprydp_field_wrap">
			<label><?php esc_html_e("Line Thickness", "easy-sticky-sidebar"); ?></label>
			<div class="wordpress-cta-pro-feature-lock-inline-container">
				<input type="number" style="width: 50px"> px
				<?php Wordpress_CTA_Free_Utils::get_inline_lock() ?>
			</div>
		</div>
		<?php
	}

    /**
	 * CTA call to action show/hide field
	 * @since 1.4.5
	 */
	function call_to_action_show_hide() { ?>
		<div class="SSuprydp_field_wrap wordpress-cta-pro-feature-lock-inline-container">
			<h3 class="heading" style="margin-top:0; margin-bottom: 5px"><?php esc_html_e('Display Link Text', 'easy-sticky-sidebar') ?></h3>
			<label class="SSuprydp_switch">
				<input type="checkbox" class="checkbox-hide-show"> 
			</label>
			<?php Wordpress_CTA_Free_Utils::get_inline_lock() ?>
		</div>
		<?php
	}

	/**
	 * Call to action paddding
	 * @since 1.4.5
	 */
	function call_to_action_padding($stickycta) { ?>
		<div class="SSuprydp_field_wrap wordpress-cta-pro-element">
			<label><?php esc_html_e("Padding", "easy-sticky-sidebar"); ?></label>
			<?php Wordpress_CTA_Free_Utils::get_dimensions_field(''); ?>
			<?php Wordpress_CTA_Free_Utils::get_inline_lock(['top' => '-2px', 'bottom' => 'auto']) ?>
		</div>
		<?php
	}

	/**
	 * Call to action paddding
	 * @since 1.4.5
	 */
	function call_to_action_link_or_button($stickycta) { ?>
		<div class="SSuprydp_field_wrap call-to-action-button wordpress-cta-pro-feature-lock-inline-container">
			<label class="SSuprydp_switch has-label">
				<input type="checkbox"><?php esc_html_e('Button', 'easy-sticky-sidebar') ?>
			</label>
			<?php Wordpress_CTA_Free_Utils::get_inline_lock() ?>
		</div>
		<?php
	}

    /**
	 * CTA call to action letter spacing
	 * @since 1.4.5
	 */
	function call_to_action_letter_spacing() {?>
		<div class="SSuprydp_field_wrap call-to-action-letter-spacing">
			<label><?php esc_html_e("Letter Spacing", "easy-sticky-sidebar"); ?></label>
			<div class="wordpress-cta-pro-feature-lock-inline-container">
				<input type="number" style="width: 50px"> px
				<?php Wordpress_CTA_Free_Utils::get_inline_lock() ?>
			</div>
		</div>
		<?php
	}

    /**
	 * CTA close button option - show/hide
	 * @since 1.4.5
	 */
	function close_button_option() { ?>
		<div class="wordpress-cta-pro-features">
			
			<div class="SSuprydp_field_wrap wordpress-cta-pro-element">
			<label class="h"><?php esc_html_e('Show/Hide Close Button', 'easy-sticky-sidebar') ?></label>
				<label class="SSuprydp_switch has-label h">
					<input type="checkbox">
				</label>
				<?php Wordpress_CTA_Free_Utils::get_inline_lock() ?>
			</div>
		
			<div class="SSuprydp_field_wrap wordpress-cta-pro-element">
				<label class="h"><?php esc_html_e("Color", "easy-sticky-sidebar"); ?></label>
				<input type="text" class="sticky-sidebar-colorpicker" />
				<?php Wordpress_CTA_Free_Utils::get_inline_lock() ?>
			</div>
		
			<div class="SSuprydp_field_wrap wordpress-cta-pro-element">
				<label class="h"><?php esc_html_e("Position", "easy-sticky-sidebar"); ?></label>
				<select>
					<option value="start">Top / Left</option>
					<option value="end">Bottom / Right</option>
				</select>
				<?php Wordpress_CTA_Free_Utils::get_inline_lock() ?>
			</div>
		
			<div class="SSuprydp_field_wrap close-button-edge wordpress-cta-pro-element bir-main">
			<label class="h"><?php esc_html_e('Inside/Outside', 'easy-sticky-sidebar') ?></label>
				<label class="SSuprydp_switch has-label h">
					<input type="checkbox" class="checkbox-switch checkbox-inside-outside">
				</label>
					
			</div>
		</div>
		<?php
	}

	/**
	 * CTA box shadow toggle (Pro)
	 * @since 1.4.5
	 */
	function box_shadow_toggle() { ?>
		<div class="SSuprydp_field_wrap wordpress-cta-pro-element">
			<label class="h"><?php esc_html_e('Enable Box Shadow', 'easy-sticky-sidebar') ?></label>
			<label class="SSuprydp_switch has-label h">
				<input type="checkbox">
			</label>
			<?php Wordpress_CTA_Free_Utils::get_inline_lock() ?>
		</div>
	<?php
	}
}

