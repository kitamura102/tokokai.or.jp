<?php
if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly 
}
$btn_color = $ctacontent->SSuprydp_button_option_color;
if($btn_color){
$button_color = $btn_color ;
}

$btn_backcolor = $ctacontent->SSuprydp_button_option_backg_color;
if($btn_backcolor){
$button_background_color = $btn_backcolor ;
}

$cta_link_url = $ctacontent->tab_cta_url;
$cta_target_blank = ($ctacontent->tab_cta_target_blank == 'yes');
$cta_nofollow = ($ctacontent->tab_cta_nofollow == 'yes');

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

$horizontal_vertical_position = $ctacontent->dynamic_properties['horizontal_vertical_position'];
$position_style = '';
$position_a = '';
    if($ctacontent->SSuprydp_cta_position == 'left' || $ctacontent->SSuprydp_cta_position == 'right'){
        if ($horizontal_vertical_position === 'top') {
            $position_style = 'top: 0; transform: none;';    
        } elseif ($horizontal_vertical_position === 'bottom') {
            $position_style = 'bottom: 0; transform: none; top:100%';
            $position_a = 'position: absolute; bottom: 0; top:unset';

        }
    }

$button_icon_class = trim((string) ($ctacontent->button_icon ?? ''));
?>

<div id="<?php echo esc_attr('easy-sticky-sidebar-' . $ctacontent->id); ?>" style="<?php echo esc_attr($position_style); ?>"
    class="<?php echo esc_attr(implode(' ', $cta_classes)); ?>" data-id="<?php echo esc_attr($ctacontent->id); ?>"
    data-button-icon="<?php echo esc_attr($button_icon_class); ?>"
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
    <?php 
 
?>

    <a class="sticky-sidebar-button"
        style="color: <?php echo esc_attr($button_color); ?>; background-color:<?php echo esc_attr($button_background_color); ?>; <?php echo esc_attr($position_a); ?>"
        href="<?php echo esc_url($cta_link_url); ?>"
        <?php echo $cta_target_blank ? ' target="_blank"' : ''; ?>
        <?php echo $cta_nofollow ? ' rel="nofollow"' : ''; ?>>
        <?php
            $button_icon = $button_icon_class !== ''
                ? '<i class="' . esc_attr($button_icon_class) . '"></i> '
                : '';
            $button_text = trim((string) $ctacontent->SSuprydp_button_option_text);
            $button_text_html = $button_text === '' ? '' : esc_html($button_text);
        ?>
        <div><?php echo wp_kses($button_icon . $button_text_html, array('i' => array('class' => array()))); ?></div>
    </a>
    <?php 
	if (function_exists('wordpress_cta_pro_get_close_button')) { 
		wordpress_cta_pro_get_close_button($ctacontent); 
	} ?>
</div>
<?php
