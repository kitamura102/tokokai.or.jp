<?php
if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly 
}

if ( $ctacontent->SSuprydp_button_option_color) {
	$button_color = sanitize_hex_color($ctacontent->SSuprydp_button_option_color);
}

if ( $ctacontent->SSuprydp_button_option_backg_color) {
	$button_background_color = sanitize_hex_color($ctacontent->SSuprydp_button_option_backg_color);
}

if ( $ctacontent->SSuprydp_content_option_color) {
	$content_color = sanitize_hex_color($ctacontent->SSuprydp_content_option_color);
}
$contents_background_color = '';
if ( $ctacontent->content_background_color) {
	$contents_background_color = sanitize_hex_color($ctacontent->content_background_color);
}

if ( $ctacontent->SSuprydp_action_option_color) {
	$link_color = sanitize_hex_color($ctacontent->SSuprydp_action_option_color);
}
$links_text_background = '';
if ( $ctacontent->link_text_background) {
	$links_text_background = sanitize_hex_color($ctacontent->link_text_background);
}

if ('yes' == $ctacontent->collapse_on_page_load) {
	array_push($cta_classes, 'shrink');
}

$hide_content_text = ($ctacontent->hide_content_text ?? '') === 'yes';

$cta_link_url = '';
$tag = 'div';
if ($ctacontent->SSuprydp_action_option_url) {
	$tag = 'a';
	$cta_link_url = $ctacontent->SSuprydp_action_option_url;
}
$cta_target_blank = ($ctacontent->SSuprydp_target_blank == 'Yes');
$cta_nofollow = ($ctacontent->SSuprydp_nofollow == 'Yes');

$padding_css = "14px 24px";
$pro_enabled = function_exists('has_wordpress_cta_pro') && has_wordpress_cta_pro();
if ($pro_enabled && $ctacontent->call_to_action_padding) {
    $padding_top    = isset($ctacontent->call_to_action_padding['top']) ? intval($ctacontent->call_to_action_padding['top']) : 0;
    $padding_bottom = isset($ctacontent->call_to_action_padding['bottom']) ? intval($ctacontent->call_to_action_padding['bottom']) : 0;
    $padding_right  = isset($ctacontent->call_to_action_padding['right']) ? intval($ctacontent->call_to_action_padding['right']) : 0;
    $padding_left   = isset($ctacontent->call_to_action_padding['left']) ? intval($ctacontent->call_to_action_padding['left']) : 0;
    $padding_unit   = isset($ctacontent->call_to_action_padding['unit']) 
                        ? sanitize_text_field((string) $ctacontent->call_to_action_padding['unit']) 
                        : 'px';

    if (!($padding_top === 0 && $padding_right === 0 && $padding_bottom === 0 && $padding_left === 0)) {
        $padding_css = "{$padding_top}{$padding_unit} {$padding_right}{$padding_unit} {$padding_bottom}{$padding_unit} {$padding_left}{$padding_unit}";
    }
}


$btn_letter_spacing = '';
if($ctacontent->call_to_action_letter_spacing){
	$btn_letter_spacing = $ctacontent->call_to_action_letter_spacing ? intval($ctacontent->call_to_action_letter_spacing) : 0;
}


$horizontal_vertical_position = $ctacontent->dynamic_properties['horizontal_vertical_position'];
$position_style = '';
if($ctacontent->SSuprydp_cta_position == 'left' || $ctacontent->SSuprydp_cta_position == 'right'){
    if ($horizontal_vertical_position === 'top') {
        $position_style = 'top: 0; transform: none;';    
    } elseif ($horizontal_vertical_position === 'bottom') {
        $position_style = 'bottom: 0; transform: none; top: unset;';
    }
}
$button_alignment = $ctacontent->SSuprydp_button_option_align ?? 'left';
$justify_map = ['left' => 'flex-start', 'center' => 'center', 'right' => 'flex-end'];
$justify_value = $justify_map[$button_alignment] ?? 'flex-start';
$button_alignment_style = sprintf(
    'text-align:center; display:flex; flex-direction:column; align-items:center; justify-content:%s;',
    esc_attr($justify_value)
);

$display_trigger = $ctacontent->display_trigger ?? 'immediately';
$display_trigger_seconds = absint($ctacontent->display_trigger_seconds ?? 0);
$display_trigger_scroll = absint($ctacontent->display_trigger_scroll ?? 0);
$display_animation = $ctacontent->display_animation ?? 'none';
$hide_behavior = $ctacontent->hide_behavior ?? 'none';
$hide_after_seconds = absint($ctacontent->hide_after_seconds ?? 0);
$display_frequency = $ctacontent->display_frequency ?? 'every_time';
$after_close_behavior = $ctacontent->after_close_behavior ?? 'next_visit';
$after_close_time = absint($ctacontent->after_close_time ?? 0);
$after_close_time_unit = $ctacontent->after_close_time_unit ?? 'hours';
if (in_array($display_trigger, ['after_seconds', 'after_scroll'], true)) {
    $cta_classes[] = 'ess-cta-hidden';
}
if (in_array($display_frequency, ['once_per_visit', 'every_24_hours', 'every_7_days'], true)) {
    $cta_classes[] = 'ess-cta-hidden';
}
if ($display_animation && $display_animation !== 'none') {
    $cta_classes[] = 'ess-cta-hidden';
}

?>
<div id="<?php echo esc_attr('easy-sticky-sidebar-' . $ctacontent->id); ?>" style="<?php echo esc_attr($position_style); ?>"
    class="<?php echo esc_attr(implode(' ', $cta_classes)); ?>" data-id="<?php echo esc_attr($ctacontent->id); ?>"
    data-display-trigger="<?php echo esc_attr($display_trigger); ?>"
    data-display-trigger-seconds="<?php echo esc_attr($display_trigger_seconds); ?>"
    data-display-trigger-scroll="<?php echo esc_attr($display_trigger_scroll); ?>"
    data-display-animation="<?php echo esc_attr($display_animation); ?>"
    data-hide-behavior="<?php echo esc_attr($hide_behavior); ?>"
    data-hide-after-seconds="<?php echo esc_attr($hide_after_seconds); ?>"
    data-display-frequency="<?php echo esc_attr($display_frequency); ?>"
    data-after-close-behavior="<?php echo esc_attr($after_close_behavior); ?>"
    data-after-close-time="<?php echo esc_attr($after_close_time); ?>"
    data-after-close-time-unit="<?php echo esc_attr($after_close_time_unit); ?>">

    <div class="sticky-sidebar-button"
        style="background-color:<?php echo esc_attr($button_background_color); ?>; <?php echo esc_attr($button_alignment_style); ?>">
        <div style="color: <?php echo esc_attr($button_color); ?>;">
            <?php do_action('easy_sticky_sidebar_sticky_cta_button', $ctacontent); ?>
        </div>
        <?php      
		if (function_exists('wordpress_cta_pro_get_close_button')) {
			wordpress_cta_pro_get_close_button($ctacontent);
		}
		?>
    </div>

    <<?php echo esc_html($tag); ?> class="sticky-sidebar-content sticky-sidebar-container"
        <?php if ($tag === 'a') : ?>
            href="<?php echo esc_url($cta_link_url); ?>"
            <?php echo $cta_target_blank ? ' target="_blank"' : ''; ?>
            <?php echo $cta_nofollow ? ' rel="nofollow"' : ''; ?>
        <?php endif; ?>>

        <?php
		$image = $ctacontent->sticky_s_media;

		if ('yes' != $ctacontent->hide_cta_image) { ?>
        <div class="sticky-sidebar-image" style="background-image: url('<?php echo esc_url($image); ?>');"></div>
        <?php } ?>

        <?php if (!$hide_content_text) : ?>
            <div class="sticky-sidebar-text sticky-content-inner"
                style="color: <?php echo esc_attr($content_color); ?>; background-color: <?php echo esc_attr($contents_background_color); ?>;">
                <?php echo do_shortcode(wp_kses_post($ctacontent->SSuprydp_content_option_text)); ?>
            </div>
        <?php endif; ?>

        <?php 

        $url = $ctacontent->SSuprydp_action_option_url;
        $text = $ctacontent->SSuprydp_action_option_text;
        $line_background = $ctacontent->line_separator_color;
        $line_height = $ctacontent->dynamic_properties['line_separator_thickness'] ?? '';

        if (!empty($url)) :
            if (!$hide_content_text && $ctacontent->line_separator_show !== 'no') {
                echo '<hr style="background-color:' . esc_attr($line_background) . '; height:' . esc_attr($line_height) . 'px; border: none;">';
            }

            if ($ctacontent->hide_call_to_action !== 'yes') {
                $style = sprintf(
                    'color:%s; background-color:%s;%s%s',
                    esc_attr($link_color),
                    esc_attr($links_text_background),
                    $padding_css ? ' padding:' . esc_attr($padding_css) . ';' : '',
                    $btn_letter_spacing ? ' letter-spacing:' . esc_attr($btn_letter_spacing) . 'px;' : ''
                );

                printf(
                    '<div class="sticky-sidebar-call-to-action sticky-content-inner" style="%s">%s</div>',
                    esc_attr($style),
                    wp_kses_post($text)
                );
            } 
        endif;
        ?>

    </<?php echo esc_html($tag); ?>>
</div>
<?php
echo ob_get_clean(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
