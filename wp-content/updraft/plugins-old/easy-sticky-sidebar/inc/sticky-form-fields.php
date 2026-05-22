<?php
if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly 
}

function easy_sticky_sidebar_cta_position($stickycta, $sticky_id) {
	$positions = array(
		'right' => __('Right', 'easy-sticky-sidebar'),
		'left' => __('Left', 'easy-sticky-sidebar'),
		'top' => __('Top', 'easy-sticky-sidebar'),
		'bottom' => __('Bottom', 'easy-sticky-sidebar'),
	);

	$h_positions = array(
		'center' => __('Center', 'easy-sticky-sidebar'),
		'top' => __('Top', 'easy-sticky-sidebar'),
		'bottom' => __('Bottom', 'easy-sticky-sidebar'),
	);

	$pro_features = ['left', 'top', 'bottom'];
	$pro_h_features = ['top', 'bottom'];

	$cta_position = $stickycta->SSuprydp_cta_position;
	if ($cta_position == '' || !has_wordpress_cta_pro()) {
		$cta_position == 'right';
	} 
	
	$cta_h_position = $stickycta->horizontal_vertical_position;
	if ($cta_h_position == '' || !has_wordpress_cta_pro()) {
		$cta_h_position = 'center';
	}
	
	?>

	<div id="easy-sticky-sidebar-position">
		<?php do_action('easy_sticky_sidebar/cta_position_fields', $stickycta); ?>

		<div class="SSuprydp_field_wrap">
			<label><?php esc_html_e("Position", "easy-sticky-sidebar"); ?></label>

			<div class="group-inline-field group-inline-field-position">
				<select class="input-field" name="SSuprydp_cta_position" data-position="<?php echo esc_attr($cta_position) ?>">
					<?php
					foreach ($positions as $key => $position) {
						$disabled = in_array($key, $pro_features, true) && !has_wordpress_cta_pro();
						if ($disabled) {
							$position = sprintf('%s (%s)', $position, __('Pro Feature', 'easy-sticky-sidebar'));
						}

						printf(
							'<option value="%s"%s%s>%s</option>',
							esc_attr($key),
							selected($key, $cta_position, false),
							$disabled ? ' disabled="disabled"' : '',
							esc_html($position)
						);
					}
					?>
				</select>
			</div>
		</div>

		<div id="cta-horizontal-vertical-position" class="SSuprydp_field_wrap" data-position="<?php echo esc_attr($stickycta->horizontal_vertical_position) ?>">
			<div class="group-inline-field group-inline-field-position">
			<div class="horizontal_vertical_position-wrapper">
					<label><?php esc_html_e("Horizontal/Vertical Position", "easy-sticky-sidebar"); ?></label>
					<select class="input-field" name="horizontal_vertical_position" data-position="<?php echo esc_attr($cta_h_position) ?>">
					<?php
					foreach ($h_positions as $key => $position) {
						$disabled = in_array($key, $pro_h_features, true) && !has_wordpress_cta_pro();
						if ($disabled) {
							$position = sprintf('%s (%s)', $position, __('Pro Feature', 'easy-sticky-sidebar'));
						}

						printf(
							'<option value="%s"%s%s>%s</option>',
							esc_attr($key),
							selected($key, $cta_h_position, false),
							$disabled ? ' disabled="disabled"' : '',
							esc_html($position)
						);
					}
					?>


					</select>
				</div>
				<?php do_action('easy_sticky_sidebar_after_position2', $stickycta) ?>
			</div>
		</div>
	</div>
<?php
}
add_action('easy_sticky_sidebar_cta_position', 'easy_sticky_sidebar_cta_position', 10, 2);

add_action('easy_sticky_sidebar_form_cta_location', 'easy_sticky_sidebar_form_page_location', 10, 2);
function easy_sticky_sidebar_form_page_location($stickycta, $sticky_id) {
	$front_page = get_option('page_on_front'); ?>
	<h4 class="wordpress-cta-heading"><?php esc_html_e("Page / Post Location", "easy-sticky-sidebar"); ?></h4>
	<p class="wordpress-cta-instruction"><?php esc_html_e('Select where to display your CTA. Choose between the entire site or the home page.', 'easy-sticky-sidebar') ?></p>

	<?php
	if (!has_wordpress_cta_pro()) {
		echo '<p class="wordpress-cta-instruction text-bold">Get more options with our <a href="https://wpctapro.com/" target="_blank">pro version</a>.</p>';
	}

	// echo '<div class="SSuprydp_field_wrap">';
	// if (!$front_page) {
	// 	echo '<div style="margin-top:5px"></div>';
		
	// } else {
	// 	echo '<label>' . esc_html__("Location", "easy-sticky-sidebar") . '</label>';
	// 	echo '<select name="SSuprydp_location" class="SSuprydp_location">';
	// 	printf('<option value="" %s>%s</option>', selected('', $stickycta->SSuprydp_location, false), esc_html__('Entire Site', 'easy-sticky-sidebar'));
	// 	printf('<option value="%d" %s>%s</option>', $front_page, selected($front_page, $stickycta->SSuprydp_location, false), esc_html(get_the_title($front_page)));
	// 	echo '</select>';
	// }
	// echo '</div>';
}

/**
 * CTA button font field
 * @since 1.3.6
 */
function easy_sticky_sidebar_button_font($stickycta) { ?>
	<div class="SSuprydp_field_wrap sticky-sidebar-button_font field-google-font">
		<label><?php esc_html_e("Font Family", "easy-sticky-sidebar"); ?></label>
		<input id="SSuprydp_button_option_font" name="SSuprydp_button_option_font" type="text" value="<?php echo esc_attr($stickycta->SSuprydp_button_option_font) ?>" />
	</div>
<?php
}
add_action('easy_sticky_sidebar_button_options', 'easy_sticky_sidebar_button_font', 15);

/**
 * CTA button font size field
 * @since 1.3.6
 */
function easy_sticky_sidebar_button_font_size($stickycta) { ?>
	<div class="SSuprydp_field_wrap sticky-sidebar-button_fontsize">
		<label><?php esc_html_e("Font Size", "easy-sticky-sidebar"); ?></label>
		<?php $button_size = preg_replace('/[^0-9.]/', '', (string) $stickycta->SSuprydp_button_option_size); ?>
		<input style="width: 50px;text-align:right" type="number" min="0" name="SSuprydp_button_option_size" value="<?php echo esc_attr($button_size); ?>"> px
	</div>
<?php
}
add_action('easy_sticky_sidebar_button_options', 'easy_sticky_sidebar_button_font_size', 25);

/**
 * CTA button button color field
 * @since 1.3.6
 */
function easy_sticky_sidebar_button_color($stickycta) { ?>
	<div class="button-text-color">
		<div class="SSuprydp_field_wrap sticky-sidebar-button_text_color">
			<label><?php esc_html_e("Text Color", "easy-sticky-sidebar"); ?></label>
			<input type="text" name="SSuprydp_button_option_color" value="<?php echo esc_attr($stickycta->SSuprydp_button_option_color); ?>" class="sticky-sidebar-colorpicker" />
		</div>

		<div class="SSuprydp_field_wrap button-text-hover-color">
			<label><?php esc_html_e("Button Text Hover", "easy-sticky-sidebar"); ?></label>
			<input type="text" name="button1_text_hover" value="<?php echo esc_attr($stickycta->button1_text_hover); ?>" class="sticky-sidebar-colorpicker" />
		</div>
	</div>
<?php
}
add_action('easy_sticky_sidebar_button_options', 'easy_sticky_sidebar_button_color', 35);


/**
 * CTA button button color field
 * @since 1.3.6
 */
function easy_sticky_sidebar_button_background($stickycta) { ?>
	<div class="button-background-options">
		<div class="SSuprydp_field_wrap sticky-sidebar-button_background_color">
			<label><?php esc_html_e("Background Color", "easy-sticky-sidebar"); ?></label>
			<input type="text" name="SSuprydp_button_option_backg_color" value="<?php echo esc_attr($stickycta->SSuprydp_button_option_backg_color); ?>" class="sticky-sidebar-colorpicker" />
		</div>

		<div class="SSuprydp_field_wrap button-background-hover">
			<label><?php esc_html_e("Button Background Hover", "easy-sticky-sidebar"); ?></label>
			<input type="text" name="button1_background_hover" value="<?php echo esc_attr($stickycta->button1_background_hover); ?>" class="sticky-sidebar-colorpicker" />
		</div>
	</div>
<?php
}
add_action('easy_sticky_sidebar_button_options', 'easy_sticky_sidebar_button_background', 40);

/**
 * CTA content font
 * @since 1.4.0
 */
function easy_sticky_sidebar_content_show_hide($stickycta) { ?>
	<div class="SSuprydp_field_wrap">
		<div class="heading"><?php esc_html_e("Hide / Show Content", "easy-sticky-sidebar"); ?></div>
		<label class="SSuprydp_switch">
			<input type="hidden" name="hide_content_text" value="no">
			<input type="checkbox" name="hide_content_text" value="yes" <?php checked('yes', $stickycta->hide_content_text) ?> class="develop_check checkbox-hide-show">
		</label>
	</div>
<?php
}
add_action('easy_sticky_sidebar_content_option', 'easy_sticky_sidebar_content_show_hide', 1, 2);

/**
 * CTA content font
 * @since 1.4.0
 */
function easy_sticky_sidebar_content_font($stickycta) { ?>
	<div class="SSuprydp_field_wrap field-google-font">
		<label><?php esc_html_e("Font Family", "easy-sticky-sidebar"); ?></label>
		<input id="SSuprydp_content_option_font" name="SSuprydp_content_option_font" type="text" value="<?php echo esc_attr($stickycta->SSuprydp_content_option_font) ?>" />
	</div>
<?php
}
add_action('easy_sticky_sidebar_content_option', 'easy_sticky_sidebar_content_font', 5, 2);

/**
 * CTA content font size
 * @since 1.4.0
 */
function easy_sticky_sidebar_content_font_size($stickycta) { ?>
	<div class="SSuprydp_field_wrap">
		<label><?php esc_html_e("Font Size", "easy-sticky-sidebar"); ?></label>
		<?php $content_size = preg_replace('/[^0-9.]/', '', (string) $stickycta->SSuprydp_content_option_size); ?>
		<input style="width: 50px;text-align:right" type="number" min="0" name="SSuprydp_content_option_size" value="<?php echo esc_attr($content_size); ?>"> px
	</div>
<?php
}
add_action('easy_sticky_sidebar_content_option', 'easy_sticky_sidebar_content_font_size', 10);


/**
 * CTA content text color
 * @since 1.4.0
 */
function easy_sticky_sidebar_content_text_color($stickycta) { ?>
	<div class="SSuprydp_field_wrap">
		<label><?php esc_html_e("Text Color", "easy-sticky-sidebar"); ?></label>
		<input type="text" name="SSuprydp_content_option_color" value="<?php echo esc_attr($stickycta->SSuprydp_content_option_color); ?>" class="sticky-sidebar-colorpicker" />
	</div>
<?php
}
add_action('easy_sticky_sidebar_content_option', 'easy_sticky_sidebar_content_text_color', 15, 2);


function easy_stick_sidebar_content_background_color($stickycta) {
?>
	<div class="SSuprydp_field_wrap sticky-sidebar-content_background_color">
		<label><?php esc_html_e("Background Color", "easy-sticky-sidebar"); ?></label>
		<input type="text" name="content_background_color" value="<?php echo esc_attr($stickycta->content_background_color); ?>" class="sticky-sidebar-colorpicker" />
	</div>
<?php
}
add_action('easy_sticky_sidebar_content_option', 'easy_stick_sidebar_content_background_color', 20, 2);

add_action('easy_sticky_sidebar_line_separator', 'easy_sticky_sidebar_line_separator_show', 1, 2);
function easy_sticky_sidebar_line_separator_show($stickycta) {
?>
	<div class="SSuprydp_field_wrap">
		<div class="heading"><?php esc_html_e("Show/Hide Line Separator", "easy-sticky-sidebar"); ?></div>
		<label class="SSuprydp_switch">
			<input type="hidden" name="line_separator_show" value="no">
			<input type="checkbox" name="line_separator_show" value="yes" <?php checked('yes', $stickycta->line_separator_show) ?> class="develop_check checkbox-show-hide">
		</label>
	</div>
<?php
}

add_action('easy_sticky_sidebar_line_separator', 'easy_sticky_sidebar_line_separator_color', 10, 2);
function easy_sticky_sidebar_line_separator_color($stickycta, $sticky_id) {
?>
	<div class="SSuprydp_field_wrap">
		<label><?php esc_html_e("Line Separator Color", "easy-sticky-sidebar"); ?></label>
		<input type="text" name="line_separator_color" value="<?php echo esc_attr($stickycta->line_separator_color); ?>" class="sticky-sidebar-colorpicker" />
	</div>
<?php
}

/**
 * Add call to action text
 * @since 1.3.7
 */
function easy_sticky_sidebar_call_top_action_font($stickycta) { ?>
	<div class="SSuprydp_field_wrap call-to-action-font field-google-font">
		<label><?php esc_html_e("Font Family", "easy-sticky-sidebar"); ?></label>
		<input id="SSuprydp_action_option_font" name="SSuprydp_action_option_font" type="text" value="<?php echo esc_attr($stickycta->SSuprydp_action_option_font); ?>" />
	</div>
<?php
}
add_action('easy_sticky_sidebar_call_to_action', 'easy_sticky_sidebar_call_top_action_font', 10);

/**
 * Add call to action font weight
 * @since 1.3.7
 */
function easy_sticky_sidebar_call_top_action_font_size($stickycta) { ?>
	<div class="SSuprydp_field_wrap call-to-action-font-size">
		<label><?php esc_html_e("Font Size", "easy-sticky-sidebar"); ?></label>
		<?php $action_size = preg_replace('/[^0-9.]/', '', (string) $stickycta->SSuprydp_action_option_size); ?>
		<input style="width: 50px;text-align:right" type="number" min="0" name="SSuprydp_action_option_size" value="<?php echo esc_attr($action_size); ?>"> px
	</div>
<?php
}
add_action('easy_sticky_sidebar_call_to_action', 'easy_sticky_sidebar_call_top_action_font_size', 15);

/**
 * Add call to action text color
 * @since 1.3.7
 */
function easy_sticky_sidebar_call_top_action_textcolor($stickycta) { ?>
	<div class="SSuprydp_field_wrap call-to-action-textcolor">
		<label><?php esc_html_e("Text Color", "easy-sticky-sidebar"); ?></label>
		<input type="text" name="SSuprydp_action_option_color" value="<?php echo esc_attr($stickycta->SSuprydp_action_option_color); ?>" class="sticky-sidebar-colorpicker" />
	</div>
<?php
}
add_action('easy_sticky_sidebar_call_to_action', 'easy_sticky_sidebar_call_top_action_textcolor', 20);

/**
 * Add call to action text background color
 * @since 1.3.7
 */
function easy_stick_sidebar_link_text_background($stickycta) { ?>
	<div class="SSuprydp_field_wrap call-to-action-background-color">
		<label><?php esc_html_e("Background Color", "easy-sticky-sidebar"); ?></label>
		<input type="text" name="link_text_background" value="<?php echo esc_attr($stickycta->link_text_background); ?>" class="sticky-sidebar-colorpicker" />
	</div>
<?php
}
add_action('easy_sticky_sidebar_call_to_action', 'easy_stick_sidebar_link_text_background', 25);

/**
 * Design Template option
 * @since 1.4.5
 */
function wordpress_cta_free_design_template_option($stickycta) {
	$design_templates = wordpress_cta_get_design_templates(); ?>
	<div class="gap-5"></div>
	<p class="wordpress-cta-instruction"><?php esc_html_e('Choose a design template.', 'easy-sticky-sidebar'); ?></p>
	<div class="SSuprydp_field_wrap">
		<select id="cta-premade-style" class="SSuprydp_input">
			<option value=""><?php esc_html_e('Select style', 'easy-sticky-sidebar'); ?></option>
			<?php foreach ($design_templates as $key => $template) {
				$name = empty($template['name']) ? $key : $template['name'];
				$disabled = isset($template['is_pro']) && !has_wordpress_cta_pro();
				if ($disabled) {
					$name = sprintf('%s (%s)', $name, __('Pro Feature', 'easy-sticky-sidebar'));
				}

				printf(
					'<option value="%s"%s>%s</option>',
					esc_attr(wp_json_encode($template)),
					$disabled ? ' disabled="disabled"' : '',
					esc_html($name)
				);
			} ?>
		</select>
	</div>
<?php
}
add_action('easy_sticky_sidebar_design_template', 'wordpress_cta_free_design_template_option', 1);


/**
 * Design Template option
 * @since 1.4.5
 */
function wordpress_cta_free_cta_collapse($stickycta) { ?>
	<div class="SSuprydp_field_wrap">
		<h4 class="heading"><?php esc_html_e("Collapse CTA On Page Load", "easy-sticky-sidebar"); ?></h4>
		<label class="SSuprydp_switch">
			<input type="hidden" name="collapse_on_page_load" value="no">
			<input type="checkbox" name="collapse_on_page_load" value="yes" <?php checked('yes', $stickycta->collapse_on_page_load) ?>>
		</label>
	</div>

<?php
}
add_action('easy_sticky_sidebar_page_load_options', 'wordpress_cta_free_cta_collapse');

