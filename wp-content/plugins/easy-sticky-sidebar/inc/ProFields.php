<?php
if (!defined('ABSPATH')) {
	exit;
}
/*
 * Pro field placeholders
 * @package sticky-sidebar/inc
 * @since 1.4.5
 */

class Easy_Sticky_Sidebar_Pro_Placeholder {
    public function __construct() {
        add_filter('easy_sticky_sidebar/pro_fields', [$this, 'register_placeholder'], 1);
    }

    public function register_placeholder($elements) {
        $elements['show_statistics'] = array('hook' => 'easy_sticky_sidebar_before_tab', 'callback' => [$this, 'show_statistics']);
        
		$elements['cta_location'] = array('hook' => 'easy_sticky_sidebar_form_cta_location', 'callback' => [$this, 'cta_location']);
        
        if (!easy_sticky_sidebar_has_pro()) {
            $elements['html_cta_disable_collapse'] = array('hook' => 'easy_sticky_sidebar_cta_scroll_options', 'callback' => [$this, 'disable_collapse']);
        }
        
        return $elements;
    }

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
				$location_types = easy_sticky_sidebar_get_location_types();

				echo '<select name="%name%[%number%][type]">';
				foreach ($location_types as $group => $locations) {
					echo '<optgroup label="' . esc_attr(easy_sticky_sidebar_location_group($group)) . '">';
					foreach ($locations as $lkey => $location) {
						$value = $group . ':' . $lkey;
						printf('<option value="%s">%s</option>', esc_attr($value), esc_html($location));
					}
					echo '</optgroup>';
				}
				echo '</select>';
				?>
				<ul class="location-field-container" id="cta-locations" data-btn-add="#btn-add-location" data-name="locations"></ul>

			</div>
			<div class="ess-placement-pro-group">
				<div class="SSuprydp_field_wrap location-field-wrapper">
				<div class="SSuprydp_field_wrap location-field-wrapper wordpress-cta-pro-element">
				<a class="button-primary button-large" id="btn-add-location" style="margin-top:20px"><?php esc_html_e('Add condition', 'easy-sticky-sidebar') ?></a>
				</div>
				</div>
			<div class="gap-10"></div>

			<div class="SSuprydp_field_wrap location-field-wrapper wordpress-cta-pro-element">
				<label>Exclude</label>
				<ul class="location-field-container" id="cta-exclude-locations" data-btn-add="#btn-add-exclude-location" data-name="exclude_locations"></ul>
				<a class="button-primary button-large" id="btn-add-exclude-location"><?php esc_html_e('Add condition', 'easy-sticky-sidebar') ?> </a>

			</div>
			<?php Easy_Sticky_Sidebar_Utils::get_inline_lock(); ?>
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
		</div>
		<?php
	}

    /**
     * Add option for prevent collapse on scroll
     * @since 1.4.5
     */
    function disable_collapse($stickycta) { ?>
        <div class="SSuprydp_field_wrap wordpress-cta-pro-element bo">
            <label class="SSuprydp_switch">
                <h4 class="heading h"><?php esc_html_e('Disable Collapse (Keep CTA open after scroll)', 'easy-sticky-sidebar'); ?></h4>
                <input type="checkbox">
            </label>
            <?php Easy_Sticky_Sidebar_Utils::get_inline_lock() ?>
        </div>
        <?php
    }

    /**
	 * Add CTA width field
	 * @since 1.4.5
	 */
    public function cta_width($stickycta) {?>
		<div class="SSuprydp_field_wrap wordpress-cta-pro-element">
			<h4 class="heading h"><?php esc_html_e('Enable CTA Width', 'easy-sticky-sidebar') ?></h4>
			<label class="SSuprydp_switch has-label" style="margin-bottom: 0">
                <input type="checkbox">
			</label>
			<?php Easy_Sticky_Sidebar_Utils::get_inline_lock() ?>
        </div>

        <div class="SSuprydp_field_wrap wordpress-cta-pro-element">
            <label class="h"><?php esc_html_e('CTA Width', 'easy-sticky-sidebar') ?></label>
            <input style="width: 50px;text-align:right" type="number">
            <?php easy_sticky_sidebar_get_unit_input(''); ?>
			<?php Easy_Sticky_Sidebar_Utils::get_inline_lock() ?>
        </div>

        <div class="SSuprydp_field_wrap wordpress-cta-pro-element">
            <label class="h"><?php esc_html_e('CTA Tablet Width', 'easy-sticky-sidebar') ?></label>
            <input style="width: 50px;text-align:right" type="number">
            <?php easy_sticky_sidebar_get_unit_input(''); ?>
			<?php Easy_Sticky_Sidebar_Utils::get_inline_lock() ?>
        </div>

        <div class="SSuprydp_field_wrap wordpress-cta-pro-element">
			<label class="h"><?php esc_html_e('CTA Mobile Width', 'easy-sticky-sidebar') ?></label>
            <input style="width: 50px;text-align:right" type="number">
            <?php easy_sticky_sidebar_get_unit_input(''); ?>
			<?php Easy_Sticky_Sidebar_Utils::get_inline_lock() ?>
        </div>
		<?php	
    }

    /**
	 * Add CTA height field (locked in free).
	 * @since 2.4.2
	 */
    public function cta_height($stickycta) {?>
		<div class="SSuprydp_field_wrap wordpress-cta-pro-element">
			<h4 class="heading h"><?php esc_html_e('Enable CTA Height', 'easy-sticky-sidebar') ?></h4>
			<label class="SSuprydp_switch has-label" style="margin-bottom: 0">
                <input type="checkbox">
			</label>
			<?php Easy_Sticky_Sidebar_Utils::get_inline_lock() ?>
        </div>

        <div class="SSuprydp_field_wrap wordpress-cta-pro-element">
            <label class="h"><?php esc_html_e('CTA Height', 'easy-sticky-sidebar') ?></label>
            <input style="width: 50px;text-align:right" type="number">
            <?php easy_sticky_sidebar_get_unit_input(''); ?>
			<?php Easy_Sticky_Sidebar_Utils::get_inline_lock() ?>
        </div>

        <div class="SSuprydp_field_wrap wordpress-cta-pro-element">
            <label class="h"><?php esc_html_e('CTA Tablet Height', 'easy-sticky-sidebar') ?></label>
            <input style="width: 50px;text-align:right" type="number">
            <?php easy_sticky_sidebar_get_unit_input(''); ?>
			<?php Easy_Sticky_Sidebar_Utils::get_inline_lock() ?>
        </div>

        <div class="SSuprydp_field_wrap wordpress-cta-pro-element">
			<label class="h"><?php esc_html_e('CTA Mobile Height', 'easy-sticky-sidebar') ?></label>
            <input style="width: 50px;text-align:right" type="number">
            <?php easy_sticky_sidebar_get_unit_input(''); ?>
			<?php Easy_Sticky_Sidebar_Utils::get_inline_lock() ?>
        </div>
		<?php
    }

    /**
	 * CTA field for Hide or Show image
	 * @since 1.4.5
	 */
	function hide_image() { ?>
			<div class="ess-image-settings-pro-group">
			<div class="SSuprydp_field_wrap wordpress-cta-pro-element">
			<label class="h"><?php esc_html_e('Hide / Show Image', 'easy-sticky-sidebar') ?></label>
				<label class="SSuprydp_switch has-label h">
				<input type="checkbox" class="checkbox-hide-show"> 
					
				</label>
			</div>

			<div class="SSuprydp_field_wrap wordpress-cta-pro-element">
				<label class="h"><?php esc_html_e('Image Height', 'easy-sticky-sidebar') ?></label>
				<input style="width: 50px;text-align:right" type="number"> px
			</div>
			<?php Easy_Sticky_Sidebar_Utils::get_inline_lock(); ?>
			</div>
		<?php
	}

    /**
	 * CTA button letter spacing
	 * @since 1.4.5
	 */
	function button_letter_spacing() { ?>
		<div class="ess-tab-settings-pro-group">
		<div class="SSuprydp_field_wrap sticky-sidebar-button_letter_spacing wordpress-cta-pro-element">
			<label><?php esc_html_e("Letter Spacing", "easy-sticky-sidebar"); ?></label>
			<input type="number" style="width: 50px"> px
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
        </div>
        <?php Easy_Sticky_Sidebar_Utils::get_inline_lock(); ?>
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
            <?php Easy_Sticky_Sidebar_Utils::get_dimensions_field(''); ?>
        </div>
    <?php
    }

	/**
	 * CTA content padding
	 * @since 1.4.5
	 */
	function content_padding($stickycta) {?>
		<?php
		$wrapper_classes = array('SSuprydp_field_wrap', 'wordpress-cta-pro-element');
		if (($stickycta->sidebar_template ?? '') === 'sticky-cta') {
			$wrapper_classes[] = 'cta-image-classic-only';
		}
		?>
		<div class="<?php echo esc_attr(implode(' ', $wrapper_classes)); ?>">
			<label><?php esc_html_e("Padding", "easy-sticky-sidebar"); ?></label>
			<?php Easy_Sticky_Sidebar_Utils::get_dimensions_field(''); ?>
		</div>
		<?php Easy_Sticky_Sidebar_Utils::get_inline_lock(); ?>
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
		<div class="ess-content-settings-pro-group">
		<div class="SSuprydp_field_wrap wordpress-cta-pro-element">
			<label><?php esc_html_e("Letter Spacing", "easy-sticky-sidebar"); ?></label>
			<input type="number" style="width: 50px" disabled> px
		</div>
		<?php
	}

    /**
	 * CTA call to action letter spacing
	 * @since 1.4.5
	 */
	function line_separator_thickness() {?>
		<div class="ess-line-separator-pro-group">
		<div class="SSuprydp_field_wrap wordpress-cta-pro-element">
			<label><?php esc_html_e("Line Thickness", "easy-sticky-sidebar"); ?></label>
			<input type="number" style="width: 50px"> px
		</div>
		<?php Easy_Sticky_Sidebar_Utils::get_inline_lock(); ?>
		</div>
		<?php
	}

    /**
	 * CTA call to action show/hide field
	 * @since 1.4.5
	 */
	function call_to_action_show_hide() { ?>
		<div class="ess-button-settings-pro-group">
		<div class="SSuprydp_field_wrap wordpress-cta-pro-element">
			<h3 class="heading" style="margin-top:0; margin-bottom: 5px"><?php esc_html_e('Display Link Text', 'easy-sticky-sidebar') ?></h3>
			<label class="SSuprydp_switch">
				<input type="checkbox" class="checkbox-hide-show"> 
			</label>
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
			<?php Easy_Sticky_Sidebar_Utils::get_dimensions_field(''); ?>
		</div>
		<?php
	}

	/**
	 * Call to action paddding
	 * @since 1.4.5
	 */
	function call_to_action_link_or_button($stickycta) { ?>
		<div class="SSuprydp_field_wrap call-to-action-button wordpress-cta-pro-element">
			<label class="SSuprydp_switch has-label">
				<input type="checkbox"><?php esc_html_e('Button', 'easy-sticky-sidebar') ?>
			</label>
		</div>
		<?php Easy_Sticky_Sidebar_Utils::get_inline_lock(); ?>
		</div>
		<?php
	}

    /**
	 * CTA call to action letter spacing
	 * @since 1.4.5
	 */
	function call_to_action_letter_spacing() {?>
		<div class="SSuprydp_field_wrap call-to-action-letter-spacing wordpress-cta-pro-element">
			<label><?php esc_html_e("Letter Spacing", "easy-sticky-sidebar"); ?></label>
			<input type="number" style="width: 50px"> px
		</div>
		<?php
	}

    /**
	 * CTA close button option - show/hide
	 * @since 1.4.5
	 */
	function close_button_option() { ?>
			<div class="SSuprydp_field_wrap wordpress-cta-pro-element">
			<label class="h"><?php esc_html_e('Show/Hide Close Button', 'easy-sticky-sidebar') ?></label>
				<label class="SSuprydp_switch has-label h">
					<input type="checkbox">
				</label>
				<?php Easy_Sticky_Sidebar_Utils::get_inline_lock() ?>
			</div>
		
			<div class="SSuprydp_field_wrap wordpress-cta-pro-element">
				<label class="h"><?php esc_html_e("Color", "easy-sticky-sidebar"); ?></label>
				<input type="text" class="sticky-sidebar-colorpicker" />
				<?php Easy_Sticky_Sidebar_Utils::get_inline_lock() ?>
			</div>
		
			<div class="SSuprydp_field_wrap wordpress-cta-pro-element">
				<label class="h"><?php esc_html_e("Position", "easy-sticky-sidebar"); ?></label>
				<select>
					<option value="start">Top / Left</option>
					<option value="end">Bottom / Right</option>
				</select>
				<?php Easy_Sticky_Sidebar_Utils::get_inline_lock() ?>
			</div>
		
			<div class="SSuprydp_field_wrap close-button-edge wordpress-cta-pro-element bir-main">
			<label class="h"><?php esc_html_e('Inside/Outside', 'easy-sticky-sidebar') ?></label>
				<label class="SSuprydp_switch has-label h">
					<input type="checkbox" class="checkbox-switch checkbox-inside-outside">
				</label>
					
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
			<?php Easy_Sticky_Sidebar_Utils::get_inline_lock() ?>
		</div>
	<?php
	}
}

