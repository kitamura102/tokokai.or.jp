<?php
if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly 
}

$ess_get_css_color = static function ($raw, $fallback = '') {
	$value = trim(html_entity_decode((string) $raw, ENT_QUOTES, 'UTF-8'));
	if ($value === '') {
		return $fallback;
	}

	$hex = sanitize_hex_color($value);
	if (!empty($hex)) {
		return $hex;
	}

	$is_rgb = preg_match('/^rgba?\(\s*[0-9.,%\s]+\)$/i', $value);
	$is_hsl = preg_match('/^hsla?\(\s*[0-9.,%\s]+\)$/i', $value);
	if ($is_rgb || $is_hsl) {
		return $value;
	}

	return $fallback;
};

$button_color = '';
if ( $ctacontent->SSuprydp_button_option_color) {
	$button_color = sanitize_hex_color($ctacontent->SSuprydp_button_option_color);
}

$button_background_color = '';
if ( $ctacontent->SSuprydp_button_option_backg_color) {
	$button_background_color = sanitize_hex_color($ctacontent->SSuprydp_button_option_backg_color);
}

$content_color = '';
if ( $ctacontent->SSuprydp_content_option_color) {
	$content_color = $ess_get_css_color($ctacontent->SSuprydp_content_option_color, '');
}
$contents_background_color = '';
if ( $ctacontent->content_background_color) {
	$contents_background_color = sanitize_hex_color($ctacontent->content_background_color);
}

$link_color = '';
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
$image_mode = strtolower((string) ($ctacontent->image_placement ?? 'classic'));
if ($image_mode === 'background') {
    $image_mode = 'overlay';
}
$is_overlay_mode = ($image_mode === 'overlay');
$hide_image = (($ctacontent->hide_cta_image ?? '') === 'yes');
$resolved_image = !empty($ctacontent->sticky_s_media) ? (string) $ctacontent->sticky_s_media : '';
if ($resolved_image === '' && absint($ctacontent->image_attachment_id ?? 0) > 0) {
    $resolved_image = (string) wp_get_attachment_image_url(absint($ctacontent->image_attachment_id), 'full');
}
if (!$hide_image && ($resolved_image === '' || ($is_overlay_mode && stripos($resolved_image, 'ss_dummy.jpg') !== false))) {
    $resolved_image = $is_overlay_mode
        ? EASY_STICKY_SIDEBAR_PLUGIN_URL . '/assets/img/overlay_dummy.webp'
        : EASY_STICKY_SIDEBAR_PLUGIN_URL . '/assets/img/ss_dummy.jpg';
}
if ($hide_image) {
    $resolved_image = '';
}
if ($is_overlay_mode) {
    $overlay_position_class = strtolower((string) ($ctacontent->overlay_position ?? 'right'));
    if (!in_array($overlay_position_class, ['top', 'left', 'bottom', 'right'], true)) {
        $overlay_position_class = 'right';
    }
    $cta_classes[] = 'image-as-background';
    $cta_classes[] = 'overlay-pos-' . $overlay_position_class;
    if ('yes' === strtolower((string) ($ctacontent->overlay_full_tab_height ?? 'no'))) {
        $cta_classes[] = 'ess-overlay-full-tab-height';
    }
}
$overlay_tab_corner_radius = max(0, absint($ctacontent->overlay_tab_corner_radius ?? 5));

$cta_link_url = '';
$tag = 'div';
if ($ctacontent->SSuprydp_action_option_url) {
	$tag = 'a';
	$cta_link_url = $ctacontent->SSuprydp_action_option_url;
}
$cta_target_blank = ($ctacontent->SSuprydp_target_blank == 'Yes');
$cta_nofollow = ($ctacontent->SSuprydp_nofollow == 'Yes');

$padding_css = "14px 24px";
$content_padding_css = "14px 24px";
if ($ctacontent->call_to_action_padding) {
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


$horizontal_vertical_position = function_exists('easy_sticky_sidebar_normalize_secondary_position')
    ? easy_sticky_sidebar_normalize_secondary_position($ctacontent->SSuprydp_cta_position ?? 'right', $ctacontent->horizontal_vertical_position ?? '', 'center')
    : strtolower((string) ($ctacontent->horizontal_vertical_position ?? 'center'));

if ($ctacontent->content_padding) {
    $content_padding_top    = isset($ctacontent->content_padding['top']) ? intval($ctacontent->content_padding['top']) : 0;
    $content_padding_bottom = isset($ctacontent->content_padding['bottom']) ? intval($ctacontent->content_padding['bottom']) : 0;
    $content_padding_right  = isset($ctacontent->content_padding['right']) ? intval($ctacontent->content_padding['right']) : 0;
    $content_padding_left   = isset($ctacontent->content_padding['left']) ? intval($ctacontent->content_padding['left']) : 0;
    $content_padding_unit   = isset($ctacontent->content_padding['unit'])
                        ? sanitize_text_field((string) $ctacontent->content_padding['unit'])
                        : 'px';

    if (!($content_padding_top === 0 && $content_padding_right === 0 && $content_padding_bottom === 0 && $content_padding_left === 0)) {
        $content_padding_css = "{$content_padding_top}{$content_padding_unit} {$content_padding_right}{$content_padding_unit} {$content_padding_bottom}{$content_padding_unit} {$content_padding_left}{$content_padding_unit}";
    }
}
$position_style = '';
if($ctacontent->SSuprydp_cta_position == 'left' || $ctacontent->SSuprydp_cta_position == 'right'){
    if ($horizontal_vertical_position === 'top') {
        $position_style = 'top: 0; transform: none;';    
    } elseif ($horizontal_vertical_position === 'bottom') {
        $position_style = 'bottom: 0; transform: none; top: unset;';
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
$is_vertical_position = in_array((string) $ctacontent->SSuprydp_cta_position, ['top', 'bottom'], true);
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
if ($button_text_orientation === 'bottom-to-top') {
    $is_side_text_orientation = $is_overlay_mode
        ? in_array((string) ($ctacontent->SSuprydp_cta_position ?? 'right'), ['left', 'right'], true)
        : in_array((string) ($ctacontent->SSuprydp_cta_position ?? 'right'), ['left', 'right'], true);
    if ($is_side_text_orientation) {
        $cta_classes[] = 'ess-tab-text-bottom-to-top';
    }
}
if (
    $is_overlay_mode
    && !in_array('ess-overlay-full-tab-height', $cta_classes, true)
    && in_array((string) ($ctacontent->SSuprydp_cta_position ?? 'right'), ['left', 'right'], true)
) {
    $overlay_side_tab_alignment = $button_alignment_value === 'end'
        ? 'bottom'
        : ($button_alignment_value === 'center' ? 'center' : 'top');
    $cta_classes[] = 'ess-overlay-side-tab-align-' . $overlay_side_tab_alignment;
}
if (
    $is_overlay_mode
    && !in_array('ess-overlay-full-tab-height', $cta_classes, true)
    && in_array((string) ($ctacontent->SSuprydp_cta_position ?? 'right'), ['top', 'bottom'], true)
) {
    $overlay_vertical_tab_alignment = $button_alignment_value === 'end'
        ? 'right'
        : ($button_alignment_value === 'center' ? 'center' : 'left');
    $cta_classes[] = 'ess-overlay-tab-align-' . $overlay_vertical_tab_alignment;
}

$overlay_content_style = '';
$overlay_button_style = '';
$overlay_container_style = '';
$overlay_wrapper_vars = '';
if ($is_overlay_mode) {
    $overlay_position_for_alignment = strtolower((string) ($ctacontent->overlay_position ?? 'right'));
    if (!in_array($overlay_position_for_alignment, ['top', 'left', 'bottom', 'right'], true)) {
        $overlay_position_for_alignment = 'right';
    }
    $overlay_alignment_fallback = in_array($overlay_position_for_alignment, ['top', 'bottom'], true)
        ? 'center'
        : $overlay_position_for_alignment;

    $overlay_content_alignment = strtolower((string) ($ctacontent->overlay_content_alignment ?? ''));
    if (!in_array($overlay_content_alignment, ['left', 'center', 'right'], true)) {
        $overlay_content_alignment = $overlay_alignment_fallback;
    }
    $overlay_button_alignment = strtolower((string) ($ctacontent->overlay_button_alignment ?? ''));
    if (!in_array($overlay_button_alignment, ['left', 'center', 'right'], true)) {
        $overlay_button_alignment = $overlay_alignment_fallback;
    }

    $overlay_content_color = $ess_get_css_color($ctacontent->SSuprydp_content_option_color ?? '', '#383838');
    $overlay_content_padding = max(0, absint($ctacontent->overlay_content_padding ?? 12));
    $overlay_content_margin_values = class_exists('Easy_Sticky_Sidebar_Utils')
        ? Easy_Sticky_Sidebar_Utils::get_dimensions_values((array) ($ctacontent->overlay_content_margin ?? array()))
        : (object) array('top' => 0, 'right' => 0, 'bottom' => 0, 'left' => 0, 'unit' => 'px');
    $overlay_content_margin_top = max(0, absint($overlay_content_margin_values->top ?? 0));
    $overlay_content_margin_right = max(0, absint($overlay_content_margin_values->right ?? 0));
    $overlay_content_margin_bottom = max(0, absint($overlay_content_margin_values->bottom ?? 0));
    $overlay_content_margin_left = max(0, absint($overlay_content_margin_values->left ?? 0));
    $overlay_content_margin_unit = in_array((string) ($overlay_content_margin_values->unit ?? 'px'), ['px'], true) ? 'px' : 'px';
    $overlay_content_style = sprintf(
        'color:%s; background-color:transparent; text-align:%s; margin:%d%s %d%s %d%s %d%s;',
        esc_attr($overlay_content_color),
        esc_attr($overlay_content_alignment),
        $overlay_content_margin_top,
        esc_attr($overlay_content_margin_unit),
        $overlay_content_margin_right,
        esc_attr($overlay_content_margin_unit),
        $overlay_content_margin_bottom,
        esc_attr($overlay_content_margin_unit),
        $overlay_content_margin_left,
        esc_attr($overlay_content_margin_unit)
    );

    $overlay_button_color = (string) ($ctacontent->SSuprydp_action_option_color ?? '');
    if ($overlay_button_color === '') {
        $overlay_button_color = (string) ($ctacontent->SSuprydp_button_option_color ?? '#ffffff');
    }
    if ($overlay_button_color === '') {
        $overlay_button_color = '#ffffff';
    }
    $overlay_button_bg = (string) ($ctacontent->link_text_background ?? '');
    if ($overlay_button_bg === '') {
        $overlay_button_bg = (string) ($ctacontent->SSuprydp_button_option_backg_color ?? '#0e2163');
    }
    if ($overlay_button_bg === '') {
        $overlay_button_bg = '#0e2163';
    }
    $overlay_button_padding = $ctacontent->overlay_button_padding ?? array();
    if (!is_array($overlay_button_padding) || empty($overlay_button_padding)) {
        $overlay_button_padding = array(
            'top'    => max(0, absint($ctacontent->overlay_button_padding_v ?? 5)),
            'right'  => max(0, absint($ctacontent->overlay_button_padding_h ?? 20)),
            'bottom' => max(0, absint($ctacontent->overlay_button_padding_v ?? 5)),
            'left'   => max(0, absint($ctacontent->overlay_button_padding_h ?? 20)),
            'unit'   => 'px',
        );
    } elseif (class_exists('Easy_Sticky_Sidebar_Utils')) {
        $normalized_overlay_button_padding = Easy_Sticky_Sidebar_Utils::get_dimensions_values((array) $overlay_button_padding);
        if (!empty($normalized_overlay_button_padding->empty)) {
            $overlay_button_padding = array(
                'top'    => max(0, absint($ctacontent->overlay_button_padding_v ?? 5)),
                'right'  => max(0, absint($ctacontent->overlay_button_padding_h ?? 20)),
                'bottom' => max(0, absint($ctacontent->overlay_button_padding_v ?? 5)),
                'left'   => max(0, absint($ctacontent->overlay_button_padding_h ?? 20)),
                'unit'   => 'px',
            );
        }
    }
    $overlay_button_padding_values = Easy_Sticky_Sidebar_Utils::get_dimensions_values((array) $overlay_button_padding);
    $overlay_button_padding_v = max(0, absint($overlay_button_padding_values->top ?? 5));
    $overlay_button_padding_h = max(0, absint($overlay_button_padding_values->right ?? 20));
    $overlay_button_margin_values = Easy_Sticky_Sidebar_Utils::get_dimensions_values((array) ($ctacontent->overlay_button_margin ?? array()));
    $overlay_button_margin_top = max(0, absint($overlay_button_margin_values->top ?? 0));
    $overlay_button_margin_right = max(0, absint($overlay_button_margin_values->right ?? 0));
    $overlay_button_margin_bottom = max(0, absint($overlay_button_margin_values->bottom ?? 0));
    $overlay_button_margin_left = max(0, absint($overlay_button_margin_values->left ?? 0));
    $overlay_button_margin_unit = in_array((string) ($overlay_button_margin_values->unit ?? 'px'), ['px'], true) ? 'px' : 'px';
    $overlay_button_radius = max(0, absint($ctacontent->overlay_button_radius ?? 50));
    $overlay_backdrop_color = (string) ($ctacontent->overlay_backdrop_color ?? '');
    if ($overlay_backdrop_color === '') {
        $overlay_backdrop_color = '#000000';
    }
    $overlay_backdrop_opacity = max(0, min(100, absint($ctacontent->overlay_backdrop_opacity ?? 55)));
    $overlay_height = function_exists('easy_sticky_sidebar_get_resolved_cta_height_css')
        ? easy_sticky_sidebar_get_resolved_cta_height_css($ctacontent, 300, 60)
        : '300px';
    $overlay_wrapper_vars = sprintf(
        '--ess-overlay-backdrop-color:%s; --ess-overlay-backdrop-opacity:%s; --ess-overlay-height:%s; --ess-overlay-content-padding:%dpx;',
        esc_attr($overlay_backdrop_color),
        esc_attr(round($overlay_backdrop_opacity / 100, 2)),
        esc_attr($overlay_height),
        $overlay_content_padding
    );
    $overlay_button_align_self = 'flex-end';
    if ($overlay_button_alignment === 'left') {
        $overlay_button_align_self = 'flex-start';
    } elseif ($overlay_button_alignment === 'center') {
        $overlay_button_align_self = 'center';
    }

    $overlay_button_style = sprintf(
        'color:%s; background-color:%s; text-align:%s; padding:%dpx %dpx !important; border-radius:%dpx !important; margin:%d%s %d%s %d%s %d%s; align-self:%s;%s',
        esc_attr($overlay_button_color),
        esc_attr($overlay_button_bg),
        esc_attr($overlay_button_alignment),
        $overlay_button_padding_v,
        $overlay_button_padding_h,
        $overlay_button_radius,
        $overlay_button_margin_top,
        esc_attr($overlay_button_margin_unit),
        $overlay_button_margin_right,
        esc_attr($overlay_button_margin_unit),
        $overlay_button_margin_bottom,
        esc_attr($overlay_button_margin_unit),
        $overlay_button_margin_left,
        esc_attr($overlay_button_margin_unit),
        esc_attr($overlay_button_align_self),
        $btn_letter_spacing ? ' letter-spacing:' . esc_attr($btn_letter_spacing) . 'px;' : ''
    );
    if (!empty($resolved_image)) {
        $overlay_container_style = sprintf('background-image:url(%s);', esc_url($resolved_image));
    }
}

$wrapper_style = trim($position_style . ' ' . $overlay_wrapper_vars . ' --ess-overlay-tab-corner-radius:' . $overlay_tab_corner_radius . 'px;');

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
<div id="<?php echo esc_attr('easy-sticky-sidebar-' . $ctacontent->id); ?>" style="<?php echo esc_attr($wrapper_style); ?>"
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
		if (function_exists('easy_sticky_sidebar_get_close_button')) {
			easy_sticky_sidebar_get_close_button($ctacontent);
		}
		?>
    </div>

    <<?php echo esc_html($tag); ?> class="sticky-sidebar-content sticky-sidebar-container"
        style="<?php echo esc_attr($overlay_container_style); ?>"
        <?php if ($tag === 'a') : ?>
            href="<?php echo esc_url($cta_link_url); ?>"
            <?php echo $cta_target_blank ? ' target="_blank"' : ''; ?>
            <?php echo $cta_nofollow ? ' rel="nofollow"' : ''; ?>
        <?php endif; ?>>

        <?php
		$image = $resolved_image;

		if (!$hide_image) { ?>
        <?php if (!$is_overlay_mode) : ?>
        <div class="sticky-sidebar-image" style="background-image: url('<?php echo esc_url($image); ?>');"></div>
        <?php endif; ?>
        <?php } ?>

        <?php if ($is_overlay_mode) : ?>
        <div class="sticky-overlay-panel">
        <?php endif; ?>

        <?php if (!$hide_content_text) : ?>
            <?php
            $classic_content_color = $content_color;
            if ($classic_content_color === '') {
                $classic_content_color = '#ffffff';
            }
            $classic_content_style = sprintf(
                'color:%s; background-color:%s; padding:%s;',
                esc_attr($classic_content_color),
                esc_attr($contents_background_color),
                esc_attr($content_padding_css)
            );
            ?>
            <div class="sticky-sidebar-text sticky-content-inner"
                style="<?php echo esc_attr($is_overlay_mode ? $overlay_content_style : $classic_content_style); ?>">
                <?php echo do_shortcode(wp_kses_post($ctacontent->SSuprydp_content_option_text)); ?>
            </div>
        <?php endif; ?>

        <?php 

        $url = $ctacontent->SSuprydp_action_option_url;
        $text = $ctacontent->SSuprydp_action_option_text;
        $line_background = $ctacontent->line_separator_color;
        $line_height = $ctacontent->line_separator_thickness ?? '';

        if (!empty($url)) :
            if (!$hide_content_text && !$is_overlay_mode && $ctacontent->line_separator_show !== 'no') {
                echo '<hr style="background-color:' . esc_attr($line_background) . '; height:' . esc_attr($line_height) . 'px; border: none;">';
            }

            if ($ctacontent->hide_call_to_action !== 'yes') {
                if ($is_overlay_mode) {
                    $style = $overlay_button_style;
                } else {
                    $style = sprintf(
                        'color:%s; background-color:%s;%s%s',
                        esc_attr($link_color),
                        esc_attr($links_text_background),
                        $padding_css ? ' padding:' . esc_attr($padding_css) . ';' : '',
                        $btn_letter_spacing ? ' letter-spacing:' . esc_attr($btn_letter_spacing) . 'px;' : ''
                    );
                }

                printf(
                    '<div class="sticky-sidebar-call-to-action sticky-content-inner" style="%s">%s</div>',
                    esc_attr($style),
                    wp_kses_post($text)
                );
            } 
        endif;
        ?>

        <?php if ($is_overlay_mode) : ?>
        </div>
        <?php endif; ?>

    </<?php echo esc_html($tag); ?>>
</div>
