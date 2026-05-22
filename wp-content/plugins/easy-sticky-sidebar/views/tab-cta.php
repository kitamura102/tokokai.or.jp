<?php
if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly 
}
$button_color = (string) ($ctacontent->SSuprydp_button_option_color ?? '');
$button_color = trim($button_color) !== '' ? $button_color : '#fff';

$button_background_color = (string) ($ctacontent->SSuprydp_button_option_backg_color ?? '');
$button_background_color = trim($button_background_color) !== '' ? $button_background_color : '#218400';

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

$horizontal_vertical_position = function_exists('easy_sticky_sidebar_normalize_secondary_position')
    ? easy_sticky_sidebar_normalize_secondary_position($ctacontent->SSuprydp_cta_position ?? 'right', $ctacontent->horizontal_vertical_position ?? '', 'center')
    : strtolower((string) ($ctacontent->horizontal_vertical_position ?? 'center'));
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

$button_alignment_value = strtolower((string) ($ctacontent->button_alignment ?? ''));
if (!in_array($button_alignment_value, ['start', 'center', 'end'], true)) {
    $legacy_align = strtolower((string) ($ctacontent->SSuprydp_button_option_align ?? 'left'));
    $legacy_map = ['left' => 'start', 'center' => 'center', 'right' => 'end', 'top' => 'start', 'middle' => 'center', 'bottom' => 'end'];
    $button_alignment_value = $legacy_map[$legacy_align] ?? 'start';
}
$axis_map = ['start' => 'flex-start', 'center' => 'center', 'end' => 'flex-end'];
$axis_align = $axis_map[$button_alignment_value] ?? 'flex-start';
$is_vertical_position = in_array($ctacontent->SSuprydp_cta_position, ['top', 'bottom'], true);
$justify_value = $is_vertical_position ? 'center' : $axis_align;
$align_items_value = $is_vertical_position ? $axis_align : 'center';
$text_align_value = $button_alignment_value === 'start' ? 'left' : ($button_alignment_value === 'end' ? 'right' : 'center');
$button_alignment_style = sprintf(
    'text-align:%s; display:flex; flex-direction:column; align-items:%s; justify-content:%s;',
    esc_attr($text_align_value),
    esc_attr($align_items_value),
    esc_attr($justify_value)
);
$button_text_orientation = strtolower((string) ($ctacontent->button_text_orientation ?? ($ctacontent->overlay_tab_text_orientation ?? 'top-to-bottom')));
if (!in_array($button_text_orientation, ['top-to-bottom', 'bottom-to-top'], true)) {
    $button_text_orientation = 'top-to-bottom';
}
$is_side_tab_orientation = in_array((string) ($ctacontent->SSuprydp_cta_position ?? 'right'), ['left', 'right'], true);
$tab_cta_classes = $cta_classes;
if ($button_text_orientation === 'bottom-to-top' && $is_side_tab_orientation) {
    $tab_cta_classes[] = 'ess-tab-text-bottom-to-top';
}

$button_icon_class = function_exists('easy_sticky_sidebar_normalize_icon_class')
    ? easy_sticky_sidebar_normalize_icon_class($ctacontent->button_icon ?? '')
    : trim((string) ($ctacontent->button_icon ?? ''));
$button_icon_size = absint($ctacontent->button_icon_size ?? 16);
$button_icon_position = function_exists('easy_sticky_sidebar_get_button_icon_position')
    ? easy_sticky_sidebar_get_button_icon_position($ctacontent)
    : 'before';
?>

<div id="<?php echo esc_attr('easy-sticky-sidebar-' . $ctacontent->id); ?>" style="<?php echo esc_attr($position_style); ?>"
    class="<?php echo esc_attr(implode(' ', $tab_cta_classes)); ?>" data-id="<?php echo esc_attr($ctacontent->id); ?>"
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
        style="color: <?php echo esc_attr($button_color); ?>; background-color:<?php echo esc_attr($button_background_color); ?>; <?php echo esc_attr($position_a); ?> <?php echo esc_attr($button_alignment_style); ?>"
        href="<?php echo esc_url($cta_link_url); ?>"
        <?php echo $cta_target_blank ? ' target="_blank"' : ''; ?>
        <?php echo $cta_nofollow ? ' rel="nofollow"' : ''; ?>>
        <?php
            $button_icon = function_exists('easy_sticky_sidebar_get_button_icon_html')
                ? easy_sticky_sidebar_get_button_icon_html($ctacontent)
                : ($button_icon_class !== ''
                    ? '<i class="icon ' . esc_attr($button_icon_class) . '" style="font-size:' . esc_attr($button_icon_size > 0 ? $button_icon_size : 16) . 'px;"></i>'
                    : '');
            $button_text = trim((string) $ctacontent->SSuprydp_button_option_text);
            if ($button_text === '') {
                $button_text = 'Call Now';
            }
            $button_text_html = $button_text === '' ? '' : '<span class="ess-sticky-sidebar-button-label">' . esc_html($button_text) . '</span>';
            $button_markup = $button_icon_position === 'after'
                ? $button_text_html . $button_icon
                : $button_icon . $button_text_html;
        ?>
        <div><?php echo wp_kses($button_markup, array('i' => array('class' => array(), 'style' => array()), 'span' => array('class' => array()))); ?></div>
    </a>
    <?php 
	if (function_exists('easy_sticky_sidebar_get_close_button')) { 
		easy_sticky_sidebar_get_close_button($ctacontent); 
	} ?>
</div>
<?php
