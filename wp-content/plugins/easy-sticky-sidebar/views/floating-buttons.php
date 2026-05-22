<?php
if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly 
}

$cta_classes[] = 'easy-sticky-sidebar ess-floating-buttons';


$floating_buttons = Easy_Sticky_Sidebar_Floating_Buttons::get_buttons($ctacontent);

if (sizeof($floating_buttons) === 0) {
	return;
}     	   
  
$has_text_items = array_filter($floating_buttons, function ($button) {
	$text = trim($button->text);
	return strlen($text) > 0;
}); 

$hide_text = $ctacontent->hide_floating_button_text === 'yes' || sizeof($has_text_items) === 0;

if ($hide_text) {
	array_push($cta_classes, 'floating-button-no-text');
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
		$position_style = 'bottom: 0; transform: none; top: auto; --button_width: auto; ';
		$position_a = 'position: absolute; bottom: 0;';

	}
}

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

    <ul class="floating-buttons-container" style="">
        <?php foreach ($floating_buttons as $key => $button) :
			$has_link = !empty($button->url);
			$class = $has_link ? 'has-link' : '';
			printf('<li class="floating-button-%d %s">', absint($key), esc_attr($class));

			ob_start();
			if ($button->icon) {
				printf('<i class="icon %s"></i>', esc_attr($button->icon));
			}

			if ($button->text && $hide_text === false) {
				echo esc_html($button->text);
			}

			$html = ob_get_clean();

			if ($has_link) {
				$html = sprintf('<a href="%s">%s</a>', esc_url($button->url), $html);
			}

			echo wp_kses_post($html);

			echo '</li>';
		endforeach; ?>
    </ul>

</div>
