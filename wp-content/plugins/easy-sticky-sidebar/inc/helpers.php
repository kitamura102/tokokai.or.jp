<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly 
}


/**
 * check if pro available or not
 * @since  1.3.6
 */
function easy_sticky_sidebar_has_pro() {
    return function_exists('Wordpress_CTA_Pro') && Wordpress_CTA_Pro()->is_activated;
}

/**
 * Get the current one-time product update notice ID.
 *
 * @since 2.3.0
 *
 * @return string
 */
function easy_sticky_sidebar_get_update_notice_id() {
	return 'wpcta_230_major_update';
}

/**
 * Check whether the current user dismissed the product update notice.
 *
 * @since 2.3.0
 *
 * @return bool
 */
function easy_sticky_sidebar_is_update_notice_dismissed() {
	if (!is_user_logged_in()) {
		return true;
	}

	return 'yes' === get_user_meta(
		get_current_user_id(),
		'_easy_sticky_sidebar_dismissed_' . easy_sticky_sidebar_get_update_notice_id(),
		true
	);
}

/**
 * Render the WP CTA 2.3.0 one-time update notice.
 *
 * @since 2.3.0
 *
 * @param string $context Notice context. Accepts admin or dashboard.
 * @return void
 */
function easy_sticky_sidebar_render_update_notice($context = 'admin') {
	if (!current_user_can('manage_options') || easy_sticky_sidebar_is_update_notice_dismissed()) {
		return;
	}

	$notice_id = easy_sticky_sidebar_get_update_notice_id();
	static $rendered_notices = array();
	if (isset($rendered_notices[$notice_id])) {
		return;
	}
	$rendered_notices[$notice_id] = true;

	$nonce = wp_create_nonce('easy_sticky_sidebar_dismiss_' . $notice_id);
	$dashboard_url = admin_url('admin.php?page=easy-sticky-sidebars');
	$is_dashboard = ('dashboard' === $context);
	$classes = $is_dashboard
		? 'easy-sticky-sidebar-update-notice easy-sticky-sidebar-update-notice-dashboard'
		: 'notice easy-sticky-sidebar-update-notice easy-sticky-sidebar-update-notice-admin';
	?>
	<div class="<?php echo esc_attr($classes); ?>" data-notice-id="<?php echo esc_attr($notice_id); ?>" data-nonce="<?php echo esc_attr($nonce); ?>">
		<button type="button" class="notice-dismiss easy-sticky-sidebar-update-dismiss">
			<span class="screen-reader-text"><?php esc_html_e('Dismiss this notice.', 'easy-sticky-sidebar'); ?></span>
		</button>
		<div class="easy-sticky-sidebar-update-notice__icon" aria-hidden="true">
			<span class="dashicons dashicons-megaphone"></span>
		</div>
		<div class="easy-sticky-sidebar-update-notice__content">
			<div class="easy-sticky-sidebar-update-notice__eyebrow"><?php esc_html_e('NEW FREE FEATURES AVAILABLE', 'easy-sticky-sidebar'); ?></div>
			<h2><?php esc_html_e('More free styling controls are now available in WP CTA.', 'easy-sticky-sidebar'); ?></h2>
			<p><?php esc_html_e('CTA creation limits have been removed, the new compact tab is now available for Sticky CTA and Sticky CTA Tabs, and styling controls and live preview behavior have all been improved.', 'easy-sticky-sidebar'); ?></p>
			<div class="easy-sticky-sidebar-update-notice__actions">
				<a class="button button-primary" href="<?php echo esc_url($dashboard_url); ?>"><?php esc_html_e('Open WP CTA Dashboard', 'easy-sticky-sidebar'); ?></a>
				<span><?php esc_html_e('Dismiss this message anytime.', 'easy-sticky-sidebar'); ?></span>
			</div>
		</div>
	</div>
	<?php
	static $script_printed = false;
	if ($script_printed) {
		return;
	}
	$script_printed = true;
	?>
	<script>
		(function () {
			const dismissUpdateNotice = function (notice) {
				if (!notice) {
					return;
				}

				const noticeId = notice.getAttribute('data-notice-id') || '';
				const nonce = notice.getAttribute('data-nonce') || '';
				document.querySelectorAll('.easy-sticky-sidebar-update-notice[data-notice-id="' + noticeId + '"]').forEach(function (item) {
					item.remove();
				});

				const data = new FormData();
				data.append('action', 'easy_sticky_sidebar_dismiss_new_features_notice');
				data.append('notice_id', noticeId);
				data.append('nonce', nonce);

				window.fetch('<?php echo esc_url(admin_url('admin-ajax.php')); ?>', {
					method: 'POST',
					credentials: 'same-origin',
					body: data
				});
			};

			document.addEventListener('click', function (event) {
				const dismissButton = event.target.closest('.easy-sticky-sidebar-update-notice .notice-dismiss');
				if (!dismissButton) {
					return;
				}

				event.preventDefault();
				dismissUpdateNotice(dismissButton.closest('.easy-sticky-sidebar-update-notice'));
			});
		}());
	</script>
	<?php
}

/**
 * Shared sticky CTA image overlay defaults.
 *
 * Classic image overlay and overlay mode settings are intentionally separate.
 *
 * @since 2.4.3
 *
 * @param array $defaults CTA defaults.
 * @return array
 */
function easy_sticky_sidebar_image_overlay_defaults($defaults) {
	$defaults['enable_image_overlay'] = $defaults['enable_image_overlay'] ?? 'no';
	$defaults['cta_image_overlay_color'] = $defaults['cta_image_overlay_color'] ?? '#000000';
	$defaults['cta_image_overlay_opacity'] = $defaults['cta_image_overlay_opacity'] ?? 35;

	return $defaults;
}
add_filter('easy_sticky_sidebar_cta_defaults', 'easy_sticky_sidebar_image_overlay_defaults');

/**
 * Resolve the active CTA height CSS value.
 *
 * Prefers the current CTA height control and falls back to the legacy
 * image-height value for older CTAs that still rely on it.
 *
 * @since 2.4.5
 *
 * @param Easy_Sticky_Sidebar_CTA_Data|object $stickycta CTA object.
 * @param int                                 $fallback  Fallback pixel height.
 * @param int                                 $min_px    Minimum allowed pixel value.
 * @return string
 */
function easy_sticky_sidebar_get_resolved_cta_height_css($stickycta, $fallback = 300, $min_px = 0) {
	$image_mode = strtolower((string) ($stickycta->image_placement ?? 'classic'));
	if ($image_mode === 'background') {
		$image_mode = 'overlay';
	}
	$is_classic_sticky_cta = (($stickycta->sidebar_template ?? '') === 'sticky-cta') && $image_mode !== 'overlay';
	if ($is_classic_sticky_cta && absint($fallback) === 300) {
		$fallback = 200;
	}

	$enable_cta_height = strtolower((string) ($stickycta->enable_cta_height ?? 'no'));
	$cta_height_unit = (string) ($stickycta->cta_height_unit ?? 'px');
	if (!in_array($cta_height_unit, array('px', '%'), true)) {
		$cta_height_unit = 'px';
	}

	$cta_height_value = floatval($stickycta->cta_height ?? 0);
	if ('yes' === $enable_cta_height && $cta_height_value > 0) {
		if ('px' === $cta_height_unit) {
			$cta_height_value = max($min_px, $cta_height_value);
		}

		$cta_height_value = 0.0 === fmod($cta_height_value, 1.0)
			? (string) absint($cta_height_value)
			: (string) $cta_height_value;

		return $cta_height_value . $cta_height_unit;
	}

	$legacy_height = absint($stickycta->cta_image_height ?? 0);
	if ($is_classic_sticky_cta && $legacy_height === 300) {
		$legacy_height = 200;
	}
	if ($legacy_height > 0) {
		return max($min_px, $legacy_height) . 'px';
	}

	return max($min_px, absint($fallback)) . 'px';
}

/**
 * Render close button markup for CTA templates.
 *
 * Uses the free plugin namespace so Pro can keep its own helper safely.
 *
 * @since 2.4.3
 *
 * @param Easy_Sticky_Sidebar_CTA_Data|object $stickycta CTA object.
 * @return void
 */
if (!function_exists('easy_sticky_sidebar_get_close_button')) {
	function easy_sticky_sidebar_get_close_button($stickycta) {
		$show_close_button = strtolower((string) ($stickycta->show_close_button ?? 'no'));
		if ('yes' !== $show_close_button) {
			return;
		}

		$close_button_color = sanitize_hex_color((string) ($stickycta->close_button_color ?? ''));
		$close_button_edge = ('yes' === strtolower((string) ($stickycta->close_button_edge ?? 'no'))) ? 'outside' : '';
		$close_button_position = (string) ($stickycta->close_button_position ?? 'start');

		printf(
			'<span style="background-color: %s" class="btn-ess-close icon-close %s %s"></span>',
			esc_attr($close_button_color),
			esc_attr($close_button_position),
			esc_attr($close_button_edge)
		);
	}
}

/**
 * Add classic image overlay class for frontend rendering.
 *
 * @since 2.4.3
 *
 * @param array                               $classes   CTA classes.
 * @param Easy_Sticky_Sidebar_CTA_Data|object $stickycta CTA object.
 * @return array
 */
function easy_sticky_sidebar_classic_image_overlay_class($classes, $stickycta) {
	if (($stickycta->sidebar_template ?? '') !== 'sticky-cta') {
		return $classes;
	}

	$image_mode = strtolower((string) ($stickycta->image_placement ?? 'classic'));
	if ($image_mode === 'background') {
		$image_mode = 'overlay';
	}

	if ($image_mode === 'overlay') {
		return $classes;
	}

	if ('yes' === strtolower((string) ($stickycta->enable_image_overlay ?? 'no'))) {
		$classes[] = 'has-image-ovarlay';
	}

	return $classes;
}
add_filter('easy_sticky_sidebar_class', 'easy_sticky_sidebar_classic_image_overlay_class', 10, 2);

/**
 * Generate classic image overlay CSS.
 *
 * @since 2.4.3
 *
 * @param Easy_Sticky_Sidebar_CTA_Data|object $stickycta CTA object.
 * @return void
 */
function easy_sticky_sidebar_generate_classic_image_overlay_css($stickycta) {
	if (($stickycta->sidebar_template ?? '') !== 'sticky-cta') {
		return;
	}

	$image_mode = strtolower((string) ($stickycta->image_placement ?? 'classic'));
	if ($image_mode === 'background') {
		$image_mode = 'overlay';
	}

	if ($image_mode === 'overlay' || 'yes' !== strtolower((string) ($stickycta->enable_image_overlay ?? 'no'))) {
		return;
	}

	$properties = array();
	$opacity = max(0, min(100, absint($stickycta->cta_image_overlay_opacity ?? 35)));
	$properties[] = sprintf('opacity: %s', $opacity / 100);

	$overlay_color = sanitize_hex_color((string) ($stickycta->cta_image_overlay_color ?? ''));
	if (!empty($overlay_color)) {
		$properties[] = sprintf('background-color: %s', $overlay_color);
	}

	if (empty($properties)) {
		return;
	}

	$wrapper_selector = sprintf('.easy-sticky-sidebar.easy-sticky-sidebar-%d', absint($stickycta->id));
	printf("%s .sticky-sidebar-image:after {%s}\n\n", esc_html($wrapper_selector), esc_html(implode(';', $properties)));
}
add_action('easy_sticky_sidebar_generate_css', 'easy_sticky_sidebar_generate_classic_image_overlay_css', 10);

/**
 * check if pro available or not
 * @since  1.4.5
 */
function is_easy_sticky_sidebar_screen() {
    $current_screen_id = get_current_screen()->id;
    $is_free_screen = strpos($current_screen_id, 'easy-sticky-sidebar') !== false;
    $is_pro_screen = strpos($current_screen_id, 'easy-sticky-sidebar') !== false;
    return $is_free_screen || $is_pro_screen;
}

/**
 * check if pro available or not
 * @since  1.3.8
 */
function easy_sticky_sidebar_get_header($args = []) {
    $args = wp_parse_args($args, apply_filters('easy_sticky_sidebar_header_args', [
        'title' => __('WP CTA', 'easy-sticky-sidebar'),
        'class' => ''
    ]));

    extract($args);

    if (!easy_sticky_sidebar_has_pro()) :
        $title = sprintf('<a target="_blank" href="https://wpctapro.com/">%s</a>', $title);
    endif; ?>
    <header class="easy-sticky-sidebar-header">
        <div class="easy-sticky-sidebar-container <?php echo esc_attr($class) ?>">
            <h3 class=""><?php echo wp_kses_post($title) ?></h3>

            <ul class="easy-sidebar-header-navs">
                <?php if (!easy_sticky_sidebar_has_pro()) : ?>
                    <li><a target="_blank" href="https://wpctapro.com/">Get more options</a></li>
                <?php endif; ?>

                <li><a target="_blank" href="https://wpctapro.com/">Website</a></li>
                <li><a target="_blank" href="https://wpctapro.com/demos/">Demos</a></li>
                <li><a target="_blank" href="https://wpctapro.com/help">Help</a></li>
            </ul>
        </div>
    </header>
<?php
}

/**
 * Insert CTA Post
 * @since  1.3.8
 */
function easy_sticky_sidebar_insert($args) {
    if (is_object($args)) {
        $args = (array) $args;
    }

    if (!is_array($args)) {
        return false;
    }

    $sticky_id = empty($args['id']) ? false : absint($args['id']);
    $is_new = ($sticky_id == false);

    $args = (array) apply_filters('easy_sticky_sidebar_args', $args, $sticky_id, $is_new);

    if (isset($args['image_attachment_id'])) {
        $args['image_attachment_id'] = Easy_Sticky_Sidebar_Utils::upload_preview_image($args['image_attachment_id']);

        $media_url = wp_get_attachment_image_url($args['image_attachment_id'], 'full');
        if ($media_url) {
            $args['sticky_s_media'] = $media_url;
        }
    }

    global $wpdb;
    $sticky_cta_columns = $wpdb->get_col("DESC {$wpdb->sticky_cta}", 0);

    $insert_format = array();

    $main_fields = $meta_fields = [];
    foreach ($args as $key => $value) {
        if (!is_string($key)) {
            continue;
        }

        if (in_array($key, $sticky_cta_columns)) {
            $main_fields[$key] = $value;
            $insert_format[$key] = '%s';
            continue;
        }

        $meta_fields[$key] = $value;
    }

    $digit_fields = array('id', 'image_attachment_id', 'SSuprydp_button_option_size', 'SSuprydp_content_option_size', 'SSuprydp_action_option_size');
    foreach ($digit_fields as $dkey) {
        if (isset($insert_format[$dkey])) {
            $insert_format[$dkey] = '%d';
        }
    }

    if ($is_new) {
        $wpdb->insert($wpdb->sticky_cta, $main_fields, $insert_format);
        $sticky_id = $wpdb->insert_id;
    } else {
        $wpdb->update($wpdb->sticky_cta, $main_fields, ['id' => $sticky_id], $insert_format, array('%d'));
    }

    if (!$sticky_id) {
        return false;
    }

    if (count($meta_fields) > 0 && $wpdb->get_var("SELECT id FROM $wpdb->sticky_cta WHERE id = $sticky_id")) {
        foreach ($meta_fields as $meta_key => $meta_value) {
            $option_ids = $wpdb->get_col(
                $wpdb->prepare(
                    "SELECT ID FROM $wpdb->sticky_cta_options WHERE option_name = %s AND sticky_cta_id = %d ORDER BY ID ASC",
                    $meta_key,
                    $sticky_id
                )
            );

            $exists = 0;
            if (!empty($option_ids)) {
                $exists = absint(end($option_ids));

                // Defensive cleanup: keep only latest row for this option key.
                // Historical saves may leave duplicate rows in sticky_cta_options.
                if (count($option_ids) > 1) {
                    $ids_to_delete = array_slice($option_ids, 0, -1);
                    foreach ($ids_to_delete as $delete_id) {
                        $wpdb->delete($wpdb->sticky_cta_options, ['ID' => absint($delete_id)], ['%d']);
                    }
                }
            }

            $data_format = array('%d', '%s', '%s');
            $data = ['sticky_cta_id' => $sticky_id, 'option_name' => $meta_key, 'option_value' => maybe_serialize($meta_value)];
            if ($exists) {
                $data['ID'] = $exists;
                array_push($data_format, '%d');
            }

            $wpdb->replace($wpdb->sticky_cta_options, $data, $data_format);
        }
    }

    do_action('easy_sticky_sidebar_after_save', $args, $sticky_id, $is_new);
    if (class_exists('Easy_Sticky_Sidebar_Generate_CSS')) {
        Easy_Sticky_Sidebar_Generate_CSS::regenerate_now();
    }

    return $sticky_id;
}

/**
 * Get Sticky CTA item
 * @since  1.3.8
 */
function get_easy_sticky_sidebar($sticky_id) {
    global $wpdb;

    $sticky_data = $wpdb->get_row($wpdb->prepare("SELECT * FROM $wpdb->sticky_cta WHERE id = %d", $sticky_id));
    if (!$sticky_data) {
        return false;
    }

    return new Easy_Sticky_Sidebar_CTA_Data($sticky_data);
}

/**
 * Returns options of a cta item
 * @since  1.3.5
 */
function easy_sticky_sidebar_templates() {
    return apply_filters('easy_sticky_sidebar_templates', [
        'sticky-cta' => __('Sticky CTA', 'easy-sticky-sidebar'),
        'tab-cta' => __('Tab CTA', 'easy-sticky-sidebar'),
        'floating-buttons' =>  __('Floating Buttons', 'easy-sticky-sidebar'),
        'banner' => __('Announcement Banner', 'easy-sticky-sidebar'),
        'html' => __('HTML / iframe CTA', 'easy-sticky-sidebar'),
        
        'gdpr' => __('GDPR / Cookies', 'easy-sticky-sidebar'),
    ]);
}

/**
 * Normalize template key across legacy/current values.
 * @since 2.3.4
 */
function easy_sticky_sidebar_normalize_template_key($template, $fallback = 'sticky-cta') {
    $raw = strtolower(trim((string) $template));
    if ($raw === '') {
        return $fallback;
    }
    $raw_hyphen = preg_replace('/\s+/', '-', $raw);
    if (!is_string($raw_hyphen) || $raw_hyphen === '') {
        $raw_hyphen = $raw;
    }

    $aliases = [
        'sticky-cta' => 'sticky-cta',
        'sticky_cta' => 'sticky-cta',
        'stickycta' => 'sticky-cta',
        'sticky' => 'sticky-cta',
        'open-sliding-cta' => 'sticky-cta',
        'closed-sliding-cta' => 'sticky-cta',

        'tab-cta' => 'tab-cta',
        'tab_cta' => 'tab-cta',
        'tabcta' => 'tab-cta',
        'tab' => 'tab-cta',

        'floating-buttons' => 'floating-buttons',
        'floating_buttons' => 'floating-buttons',
        'floatingbuttons' => 'floating-buttons',
        'floating' => 'floating-buttons',

        'banner' => 'banner',
        'announcement' => 'banner',
        'announcement-banner' => 'banner',
        'announcement_banner' => 'banner',

        'html' => 'html',
        'iframe' => 'html',
        'html-iframe' => 'html',
        'html_iframe' => 'html',

        'gdpr' => 'gdpr',
        'cookies' => 'gdpr',
        'cookie' => 'gdpr',
        'gdpr-cookies' => 'gdpr',
        'gdpr_cookies' => 'gdpr',
    ];

    $normalized = '';
    if (isset($aliases[$raw])) {
        $normalized = $aliases[$raw];
    } elseif (isset($aliases[$raw_hyphen])) {
        $normalized = $aliases[$raw_hyphen];
    } else {
        $normalized = $raw_hyphen;
    }
    $templates = array_keys((array) easy_sticky_sidebar_templates());
    if (in_array($normalized, $templates, true)) {
        return $normalized;
    }

    return $fallback;
}

/**
 * Check if CTA position uses the vertical top/bottom axis.
 *
 * @since 2.4.2
 *
 * @param string $position Primary CTA position.
 * @return bool
 */
function easy_sticky_sidebar_is_vertical_cta_position($position) {
    $position = strtolower(trim((string) $position));

    return in_array($position, ['top', 'bottom'], true);
}

/**
 * Return secondary position options for the current primary CTA position.
 *
 * @since 2.4.2
 *
 * @param string $cta_position Primary CTA position.
 * @return array
 */
function easy_sticky_sidebar_get_secondary_position_options($cta_position) {
    if (easy_sticky_sidebar_is_vertical_cta_position($cta_position)) {
        return [
            'left'   => __('Left', 'easy-sticky-sidebar'),
            'center' => __('Center', 'easy-sticky-sidebar'),
            'right'  => __('Right', 'easy-sticky-sidebar'),
        ];
    }

    return [
        'top'    => __('Top', 'easy-sticky-sidebar'),
        'center' => __('Center', 'easy-sticky-sidebar'),
        'bottom' => __('Bottom', 'easy-sticky-sidebar'),
    ];
}

/**
 * Normalize secondary CTA position against the current primary position.
 *
 * Supports legacy saved values when top/bottom CTAs still stored top/center/bottom.
 *
 * @since 2.4.2
 *
 * @param string $cta_position Primary CTA position.
 * @param string $position Secondary CTA position.
 * @param string $fallback Fallback secondary position.
 * @return string
 */
function easy_sticky_sidebar_normalize_secondary_position($cta_position, $position, $fallback = 'center') {
    $cta_position = strtolower(trim((string) $cta_position));
    $position     = strtolower(trim((string) $position));

    if (easy_sticky_sidebar_is_vertical_cta_position($cta_position)) {
        $legacy_map = [
            'top'    => 'left',
            'center' => 'center',
            'bottom' => 'right',
            'left'   => 'left',
            'right'  => 'right',
        ];
        $allowed = ['left', 'center', 'right'];
    } else {
        $legacy_map = [
            'left'   => 'top',
            'center' => 'center',
            'right'  => 'bottom',
            'top'    => 'top',
            'bottom' => 'bottom',
        ];
        $allowed = ['top', 'center', 'bottom'];
    }

    if (isset($legacy_map[$position])) {
        $position = $legacy_map[$position];
    }

    if (in_array($position, $allowed, true)) {
        return $position;
    }

    return in_array($fallback, $allowed, true) ? $fallback : 'center';
}

/**
 * Returns status menu
 * @since  1.3.8
 */
function easy_sticky_sidebar_get_status_menu($stickycta) {
    $statuses = [
        'live'          => __('Live', 'easy-sticky-sidebar'),
        'development'   => __('Development', 'easy-sticky-sidebar'),
        'off'           => __('Off', 'easy-sticky-sidebar'),
    ]; ?>

    <div class="sticky-cta-status-menu" data-id="<?php echo esc_attr($stickycta->__get('id')) ?>">
        <input type="hidden" name="SSuprydp_development" value="<?php echo esc_attr($stickycta->SSuprydp_development); ?>">
        <label class="status-<?php echo esc_attr($stickycta->SSuprydp_development); ?>"><?php echo esc_html($statuses[$stickycta->SSuprydp_development]); ?></label>
        <ul class="statuses">
            <?php foreach ($statuses as $status_key => $status_label) {
                printf('<li data-status="%s">%s</li>', esc_attr($status_key), esc_html($status_label));
            } ?>
        </ul>
    </div>
<?php
}

/**
 * Unit input
 * @since  1.4.0
 */
function easy_sticky_sidebar_get_unit_input($name, $value = 'px', $class = '', $allowed_units = ['px', '%']) {
    if (empty($value)) {
        $value = 'px';
    }

    if (!is_array($allowed_units) || empty($allowed_units)) {
        $allowed_units = ['px', '%'];
    }

    $allowed_units = array_values(array_unique(array_filter(array_map('strval', $allowed_units))));
    if (!in_array($value, $allowed_units, true)) {
        $value = in_array('px', $allowed_units, true) ? 'px' : $allowed_units[0];
    } ?>
    <div class="wpcta-unit-input <?php echo esc_attr($class) ?>">
        <?php foreach ($allowed_units as $unit) : ?>
            <label><input type="radio" name="<?php echo esc_attr($name) ?>" value="<?php echo esc_attr($unit) ?>" <?php checked($unit, $value) ?>><span><?php echo esc_html($unit) ?></span></label>
        <?php endforeach; ?>
    </div>
<?php
}

/**
 * return unique cta name
 * @since  1.3.8
 */

function easy_sticky_sidebar_get_unique_name($name, $number = false) {
    global $wpdb;
    if (empty($name)) {
        return $name;
    }

    $next_name = $name . ' - Copy';
    if ($number) {
        $next_name = $next_name . ' ' . $number;
    }

    $has_name = $wpdb->get_row($wpdb->prepare("SELECT * FROM $wpdb->sticky_cta_options WHERE option_name = 'sidebar_name' AND option_value = %s LIMIT 0, 1", $next_name));

    if (!$has_name) {
        return $next_name;
    }

    if (absint($number) == 0) {
        $number = 2;
    } else {
        $number++;
    }

    return easy_sticky_sidebar_get_unique_name($name, $number);
}

/**
 * Get easy sticky sidebar tabs
 * @since  1.4.0
 */
function easy_sticky_sidebar_get_cta_tabs() {
    $sidebar_tabs = apply_filters('easy_sticky_sidebar_tabs', array(
        'template' => [
            'label' => __("Layout", 'easy-sticky-sidebar'),
            'callback' => 'easy_sticky_sidebar_template_tab',
            'priority' => 1
        ],
        'placement' => [
            'label' => __("Placement", 'easy-sticky-sidebar'),
            'callback' => 'easy_sticky_sidebar_placement_tab',
            'priority' => 4
        ],
        'content' => [
            'label' => __("Content", 'easy-sticky-sidebar'),
            'callback' => 'easy_sticky_sidebar_content_tab_callback',
            'priority' => 2
        ],
        'content-styling' => [
            'label' => __("Styling", 'easy-sticky-sidebar'),
            'callback' => 'easy_sticky_sidebar_styling_tab',
            'priority' => 3
        ],
        'responsive' => [
            'label' => __("Responsive", 'easy-sticky-sidebar'),
            'callback' => 'easy_sticky_sidebar_responsive_tab',
            'priority' => 7
        ],
        'css' => [
            'label' => __("CSS", 'easy-sticky-sidebar'),
            'callback' => 'easy_sticky_sidebar_css_tab',
            'priority' => 8
        ]
    ));

    if (!is_array($sidebar_tabs) || empty($sidebar_tabs)) {
        return [];
    }

    foreach ($sidebar_tabs as $key => $tab) {
        if (empty($tab['callback'])) {
            unset($sidebar_tabs[$key]);
            continue;
        }

        if (is_string($tab['callback']) && !function_exists($tab['callback'])) {
            unset($sidebar_tabs[$key]);
            continue;
        }

        if (is_array($tab['callback']) && !method_exists($tab['callback'][0], $tab['callback'][1])) {
            unset($sidebar_tabs[$key]);
        }
    }

    array_multisort(array_column($sidebar_tabs, 'priority'), SORT_ASC, $sidebar_tabs);

    return $sidebar_tabs;
}

/**
 * Easy sticky sidebar template tab
 * @since  1.4.0
 */
function easy_sticky_sidebar_template_tab($stickycta) {
    $pro_templates = array('html', 'banner', 'gdpr', 'floating-buttons');
    $templates = easy_sticky_sidebar_templates();
    $current_template = (string) $stickycta->sidebar_template;
    if ($current_template === '') {
        $current_template = 'sticky-cta';
    }
    $current_template = easy_sticky_sidebar_normalize_template_key($current_template, 'sticky-cta');
    $has_pro = easy_sticky_sidebar_has_pro();
    $thumbnail_map = array(
        'sticky-cta' => 'sticky-cta.png',
        'tab-cta' => 'tab-cta.png',
        'floating-buttons' => 'floating-icons-cta.png',
        'banner' => 'announcement-banner-cta.png',
        'html' => 'html-cta.png',
        'gdpr' => 'gdpr-cta.png',
    );
    $sticky_layout = 'classic';
    $image_mode = strtolower((string) ($stickycta->image_placement ?? 'classic'));
    if ($image_mode === 'background') {
        $image_mode = 'overlay';
    }
    $overlay_position = strtolower((string) ($stickycta->overlay_position ?? 'right'));
    if (!in_array($overlay_position, array('left', 'right', 'top', 'bottom'), true)) {
        $overlay_position = 'right';
    }
    if ($image_mode === 'overlay') {
        $sticky_layout = 'overlay-' . $overlay_position;
    }
    ?>
    <h4 class="wordpress-cta-heading"><?php esc_html_e("Layout Options", "easy-sticky-sidebar") ?></h4>
    <p class="wordpress-cta-instruction"><?php esc_html_e('Select a layout for this CTA. Click on the button below to view our demos.', 'easy-sticky-sidebar') ?></p>
    <?php
    if (!$has_pro) {
        echo '<p class="wordpress-cta-instruction text-bold">Get more options with our <a href="https://wpctapro.com/" target="_blank">pro version</a>.</p>';
    }
    ?>

    <div class="SSuprydp_field_wrap">
        <label><?php esc_html_e("Layout", "easy-sticky-sidebar"); ?></label>
        <div class="ess-template-selector" id="ess-template-selector">
            <?php foreach ($templates as $template => $name) :
                $is_pro_template = in_array($template, $pro_templates, true);
                $is_locked = $is_pro_template && !$has_pro;
                $is_checked = ($template === $current_template);
                $thumb_file = isset($thumbnail_map[$template]) ? $thumbnail_map[$template] : '';
                $thumb_url = $thumb_file ? EASY_STICKY_SIDEBAR_PLUGIN_URL . '/assets/img/thumbnails/' . $thumb_file : '';
                ?>
                <label class="ess-template-card<?php echo $is_locked ? ' is-locked' : ''; ?>" data-template="<?php echo esc_attr($template); ?>">
                    <input
                        type="radio"
                        name="sidebar_template_picker"
                        class="ess-template-card-input"
                        value="<?php echo esc_attr($template); ?>"
                        <?php checked($is_checked); ?>
                        <?php disabled($is_locked); ?>
                    >
                    <?php if ($is_locked) : ?>
                        <span class="ess-template-card-lock dashicons dashicons-lock" aria-hidden="true"></span>
                    <?php endif; ?>
                    <span class="ess-template-card-media">
                        <?php if (!empty($thumb_url)) : ?>
                            <img src="<?php echo esc_url($thumb_url); ?>" alt="<?php echo esc_attr($name); ?>">
                        <?php endif; ?>
                    </span>
                    <span class="ess-template-card-title"><?php echo esc_html($name); ?></span>
                    <?php if ($is_pro_template) : ?>
                        <span class="ess-template-card-badge"><?php esc_html_e('Pro Feature', 'easy-sticky-sidebar'); ?></span>
                    <?php endif; ?>
                </label>
            <?php endforeach; ?>
        </div>

        <select name="sidebar_template" class="SSuprydp_input ess-template-native-select" id="sidebar_template" aria-hidden="true" tabindex="-1">
            <?php
            foreach ($templates as $template => $name) {
                $is_pro_locked = in_array($template, $pro_templates, true) && !$has_pro;
                if ($is_pro_locked) {
                    $name = sprintf('%s (%s)', $name, __('Pro Feature', 'easy-sticky-sidebar'));
                }
                ?>
                <option
                    value="<?php echo esc_attr($template); ?>"
                    <?php selected($template, $current_template); ?>
                    <?php disabled($is_pro_locked); ?>
                >
                    <?php echo esc_html($name); ?>
                </option>
                <?php
            }
            ?>
        </select>
        <input type="hidden" name="sidebar_template_user_selection" id="sidebar_template_user_selection" value="<?php echo esc_attr($current_template); ?>">

        <div class="ess-template-demos">
            <a class="button btn-wordpress-cta-primary" href="https://wpctapro.com/demos/" target="_blank"><?php esc_html_e('View Demos', 'easy-sticky-sidebar') ?></a>
        </div>
    </div>

    <div id="ess-sticky-layout-section" class="ess-sticky-layout-section">
        <h4 class="wordpress-cta-heading"><?php esc_html_e("Template Selection", "easy-sticky-sidebar"); ?></h4>
        <p class="wordpress-cta-instruction"><?php esc_html_e('Choose one sticky CTA layout style.', 'easy-sticky-sidebar'); ?></p>
        <div class="ess-sticky-layout-selector" id="ess-sticky-layout-selector">
            <?php
            $sticky_layouts = array(
                'overlay-left' => __('Overlay Left', 'easy-sticky-sidebar'),
                'overlay-bottom' => __('Overlay Bottom', 'easy-sticky-sidebar'),
                'overlay-right' => __('Overlay Right', 'easy-sticky-sidebar'),
                'overlay-top' => __('Overlay Top', 'easy-sticky-sidebar'),
                'classic' => __('Classic', 'easy-sticky-sidebar'),
            );
            foreach ($sticky_layouts as $layout_key => $layout_label) :
                $is_selected = ($layout_key === $sticky_layout);
            ?>
                <label class="ess-sticky-layout-card<?php echo $is_selected ? ' is-selected' : ''; ?>" data-layout="<?php echo esc_attr($layout_key); ?>">
                    <input
                        type="radio"
                        name="sticky_layout_picker"
                        class="ess-sticky-layout-input"
                        value="<?php echo esc_attr($layout_key); ?>"
                        <?php checked($is_selected); ?>
                    >
                    <span class="ess-sticky-layout-media">
                        <span class="ess-sticky-layout-thumb ess-layout-<?php echo esc_attr($layout_key); ?>"></span>
                    </span>
                    <span class="ess-sticky-layout-title"><?php echo esc_html($layout_label); ?></span>
                </label>
            <?php endforeach; ?>
        </div>
    </div>

    <div id="design_template_section" class="ess-design-template-section">
        <div class="gap-5"></div>
        <h4 class="wordpress-cta-heading"><?php esc_html_e("Design Template", "easy-sticky-sidebar"); ?></h4>
        <div class="SSuprydp_field_wrap">
            <?php
            if (has_action('easy_sticky_sidebar_design_template')) :
                do_action('easy_sticky_sidebar_design_template', $stickycta);
            endif;
            ?>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const sidebarTemplate = document.getElementById("sidebar_template");
            const sidebarTemplateUserSelection = document.getElementById("sidebar_template_user_selection");
            const designTemplateSection = document.getElementById("design_template_section");
            const templateSelector = document.getElementById("ess-template-selector");
            const stickyLayoutSection = document.getElementById("ess-sticky-layout-section");
            const stickyLayoutSelector = document.getElementById("ess-sticky-layout-selector");
            const form = document.getElementById("SSuprydp_form");
            if (!sidebarTemplate || !templateSelector || !form) {
                return;
            }

            const radios = templateSelector.querySelectorAll('input[name="sidebar_template_picker"]');
            const stickyLayoutRadios = stickyLayoutSelector ? stickyLayoutSelector.querySelectorAll('input[name="sticky_layout_picker"]') : [];
            const imagePlacementFields = Array.from(form.querySelectorAll('[name="image_placement"]'));
            const imagePlacementField = form.querySelector('.cta-image-display-mode[name="image_placement"]') || imagePlacementFields[0] || null;
            const overlayPositionField = form.querySelector('[name="overlay_position"]');

            const normalizeImageMode = function (rawValue) {
                const mode = (rawValue || 'classic').toString().toLowerCase();
                return mode === 'background' ? 'overlay' : mode;
            };

            const normalizeOverlayPosition = function (rawValue) {
                const position = (rawValue || 'right').toString().toLowerCase();
                return ['left', 'right', 'top', 'bottom'].includes(position) ? position : 'right';
            };

            const getCheckedTemplateCard = function () {
                return templateSelector.querySelector('input[name="sidebar_template_picker"]:checked:not(:disabled)');
            };

            // Use server-rendered checked thumbnail as source of truth on page load.
            // Prevent any stale hidden-select value from forcing sticky-cta during preview init.
            const initialCheckedTemplate = getCheckedTemplateCard();
            if (initialCheckedTemplate && initialCheckedTemplate.value) {
                sidebarTemplate.value = initialCheckedTemplate.value;
                if (sidebarTemplateUserSelection) {
                    sidebarTemplateUserSelection.value = initialCheckedTemplate.value;
                }
            }

            const getCurrentStickyLayout = function () {
                const mode = normalizeImageMode(imagePlacementField ? imagePlacementField.value : 'classic');
                if (mode === 'overlay') {
                    return 'overlay-' + normalizeOverlayPosition(overlayPositionField ? overlayPositionField.value : 'right');
                }
                return 'classic';
            };

            const triggerFieldChange = function (field) {
                if (!field) {
                    return;
                }
                if (window.jQuery) {
                    window.jQuery(field).trigger('change');
                } else {
                    field.dispatchEvent(new Event("change", { bubbles: true }));
                }
            };

            const setImagePlacementMode = function (mode) {
                if (!imagePlacementFields.length) {
                    return;
                }
                const normalizedMode = normalizeImageMode(mode);
                imagePlacementFields.forEach(function (field) {
                    const isSelect = field.tagName && field.tagName.toLowerCase() === 'select';
                    const optionValues = isSelect ? Array.from(field.options).map(function (opt) {
                        return (opt.value || '').toString().toLowerCase();
                    }) : [];

                    if (normalizedMode === 'overlay') {
                        if (optionValues.includes('overlay')) {
                            field.value = 'overlay';
                        } else if (optionValues.includes('background')) {
                            field.value = 'background';
                        } else {
                            field.value = 'overlay';
                        }
                        return;
                    }

                    if (optionValues.includes('classic')) {
                        field.value = 'classic';
                    } else if (optionValues.includes('block')) {
                        field.value = 'block';
                    } else {
                        field.value = 'classic';
                    }
                });
            };

            const applyStickyLayout = function (layoutValue, silent) {
                if (!imagePlacementField || !overlayPositionField) {
                    return;
                }
                const layout = (layoutValue || 'classic').toString();
                if (layout === 'classic') {
                    setImagePlacementMode('classic');
                    if (!silent) {
                        triggerFieldChange(imagePlacementField);
                    }
                    return;
                }
                const overlayPosition = layout.replace('overlay-', '');
                setImagePlacementMode('overlay');
                overlayPositionField.value = normalizeOverlayPosition(overlayPosition);
                if (!silent) {
                    triggerFieldChange(imagePlacementField);
                    triggerFieldChange(overlayPositionField);
                }
            };

            const setFormFieldValue = function (name, value, options) {
                const opts = options || {};
                const silent = opts.silent === true;
                if (!window.jQuery) {
                    const field = form.querySelector('[name="' + name + '"]');
                    if (!field) {
                        return;
                    }
                    field.value = value;
                    if (!silent) {
                        field.dispatchEvent(new Event("change", { bubbles: true }));
                    }
                    return;
                }

                const $form = window.jQuery(form);
                const $fields = $form.find('[name="' + name + '"]');
                if (!$fields.length) {
                    return;
                }

                const $radio = $fields.filter('[type="radio"]');
                if ($radio.length) {
                    const $target = $radio.filter('[value="' + value + '"]');
                    if ($target.length) {
                        $target.prop('checked', true);
                        if (!silent) {
                            $target.trigger('change');
                        }
                    }
                    return;
                }

                const $checkbox = $fields.filter('[type="checkbox"]');
                if ($checkbox.length) {
                    const checked = value === true || value === 'yes' || value === '1';
                    $checkbox.prop('checked', checked);
                    if (!silent) {
                        $checkbox.trigger('change');
                    }
                    return;
                }

                $fields.val(value);
                if ($fields.hasClass('wp-color-picker') && typeof $fields.wpColorPicker === 'function') {
                    try {
                        $fields.wpColorPicker('color', value);
                    } catch (e) {
                        // Fallback to plain value assignment above.
                    }
                }
                if (!silent) {
                    $fields.trigger('input').trigger('change');
                }
            };
            const editorDefaultProfiles = <?php echo wp_json_encode(function_exists('easy_sticky_sidebar_get_editor_default_templates') ? easy_sticky_sidebar_get_editor_default_templates() : array()); ?>;

            const showPreviewSpinner = function () {
                const previewCard = document.querySelector('.ess-live-preview-card');
                if (!previewCard) {
                    return;
                }
                previewCard.classList.add('is-preview-loading');
                previewCard.setAttribute('aria-busy', 'true');
            };

            const runSinglePreviewRefresh = function () {
                if (window.EasyStickySidebar && typeof window.EasyStickySidebar.applyTemplatePreviewRefresh === 'function') {
                    window.EasyStickySidebar.applyTemplatePreviewRefresh();
                    return;
                }
                if (window.jQuery && imagePlacementField) {
                    window.jQuery(imagePlacementField).trigger('change');
                    return;
                }
                if (imagePlacementField) {
                    imagePlacementField.dispatchEvent(new Event("change", { bubbles: true }));
                }
            };

            const getTemplateDefaults = function (templateKey) {
                const allTemplates = (editorDefaultProfiles && editorDefaultProfiles.templates) ? editorDefaultProfiles.templates : {};
                return allTemplates[templateKey] ? Object.assign({}, allTemplates[templateKey]) : {};
            };

            const getStickyLayoutDefaults = function (layoutKey) {
                const layouts = (editorDefaultProfiles && editorDefaultProfiles.sticky_layouts) ? editorDefaultProfiles.sticky_layouts : {};
                const nextLayout = (layoutKey || 'classic').toString();
                let defaults = {};

                if (nextLayout !== 'classic' && layouts['overlay-left']) {
                    defaults = Object.assign({}, layouts['overlay-left']);
                }
                if (layouts[nextLayout]) {
                    defaults = Object.assign(defaults, layouts[nextLayout]);
                }
                return defaults;
            };

            const getSwitchBaseDefaults = function () {
                return {
                    sidebar_template: 'sticky-cta',
                    SSuprydp_cta_position: 'right',
                    horizontal_vertical_position: 'center',
                    button_alignment: 'start',
                    button_icon_position: 'before',
                    button_text_orientation: 'top-to-bottom',
                    image_placement: 'classic',
                    overlay_position: 'right',
                    overlay_full_tab_height: 'yes',
                    overlay_tab_text_orientation: 'top-to-bottom',
                    hide_cta_image: 'no',
                    hide_content_text: 'no',
                    hide_call_to_action: 'no',
                    show_close_button: 'no',
                    close_button_position: 'end',
                    close_button_edge: 'no',
                    collapse_on_page_load: 'no',
                    html_cta_disable_collapse: 'no',
                    enable_cta_width: 'no',
                    cta_width: '500',
                    cta_width_unit: 'px',
                    enable_cta_height: 'no',
                    cta_height: '300',
                    cta_height_unit: 'px'
                };
            };

            const clearDimensionGroup = function (groupName) {
                if (!window.jQuery) {
                    return;
                }
                const $form = window.jQuery(form);
                const $groupFields = $form.find('input[name], select[name], textarea[name]').filter(function () {
                    const fieldName = `${window.jQuery(this).attr('name') || ''}`;
                    return fieldName.indexOf(`${groupName}[`) === 0;
                });
                if (!$groupFields.length) {
                    return;
                }
                $groupFields.each(function () {
                    const $field = window.jQuery(this);
                    const fieldName = `${$field.attr('name') || ''}`.toLowerCase();
                    if (fieldName.endsWith('[unit]')) {
                        $field.val('px');
                    } else {
                        $field.val('');
                    }
                });
            };

            const applyDefaultsMap = function (defaults, options) {
                const opts = options || {};
                const silent = opts.silent === true;
                if (!defaults || typeof defaults !== 'object') {
                    return;
                }

                Object.keys(defaults).forEach(function (fieldName) {
                    setFormFieldValue(fieldName, defaults[fieldName], { silent: silent });
                });
            };

            const applyEditorContentDefaults = function (defaults) {
                if (!defaults || typeof defaults !== 'object') {
                    return;
                }

                ['SSuprydp_button_option_text', 'SSuprydp_content_option_text', 'SSuprydp_action_option_text'].forEach(function (fieldName) {
                    if (Object.prototype.hasOwnProperty.call(defaults, fieldName)) {
                        setFormFieldValue(fieldName, defaults[fieldName], { silent: false });
                    }
                });
            };

            let resetScenarioVersion = 0;
            const runResetScenario = function (scenario) {
                const scenarioData = scenario || {};
                const template = (scenarioData.template || sidebarTemplate.value || 'sticky-cta').toString();
                const stickyLayout = (scenarioData.stickyLayout || getCurrentStickyLayout()).toString();
                const currentResetVersion = ++resetScenarioVersion;

                showPreviewSpinner();
                window.requestAnimationFrame(function () {
                    if (currentResetVersion !== resetScenarioVersion) {
                        return;
                    }
                    if (window.EasyStickySidebar && window.EasyStickySidebar.isApplyingPresetTemplate) {
                        return;
                    }
                    try {
                        window.easyStickySidebarApplyingDefaults = true;
                        if (window.EasyStickySidebar) {
                            window.EasyStickySidebar.previewSuspended = true;
                        }

                        // Always reset key/shared fields first so saved values
                        // from previous template cannot leak into the next one.
                        applyDefaultsMap(getSwitchBaseDefaults(), { silent: true });
                        [
                            'button_padding',
                            'content_padding',
                            'call_to_action_padding',
                            'overlay_button_padding',
                            'overlay_button_margin',
                            'overlay_content_margin',
                            'banner_content_padding',
                            'banner_button_padding',
                            'banner_button_margin'
                        ].forEach(clearDimensionGroup);

                        if (template === 'sticky-cta') {
                            applyStickyLayout(stickyLayout, true);
                            const stickyDefaults = getStickyLayoutDefaults(stickyLayout);
                            applyDefaultsMap(stickyDefaults, { silent: true });
                        } else {
                            const templateDefaults = getTemplateDefaults(template);
                            templateDefaults.sidebar_template = template;
                            applyDefaultsMap(templateDefaults, { silent: true });
                        }
                    } catch (error) {
                        if (window.console && typeof window.console.error === "function") {
                            window.console.error('CTA defaults reset failed:', error);
                        }
                    } finally {
                        if (window.jQuery) {
                            if (imagePlacementField) {
                                window.jQuery(imagePlacementField).trigger('change');
                            }
                            if (overlayPositionField) {
                                window.jQuery(overlayPositionField).trigger('change');
                            }
                        }
                        const resolvedDefaults = template === 'sticky-cta'
                            ? getStickyLayoutDefaults(stickyLayout)
                            : getTemplateDefaults(template);

                        if (window.EasyStickySidebar) {
                            window.EasyStickySidebar.previewSuspended = false;
                        }
                        window.easyStickySidebarApplyingDefaults = false;
                        syncStickyLayoutCards();
                        toggleTemplateSections();
                        applyEditorContentDefaults(resolvedDefaults);
                        if (typeof window.easyStickySidebarRefreshStylingSections === "function") {
                            window.easyStickySidebarRefreshStylingSections();
                        }
                        runSinglePreviewRefresh();
                    }
                });
            };

            window.easyStickySidebarApplyEditorDefaultsForTemplate = function (templateKey, forceApply) {
                const force = forceApply === true;
                if (!force && window.EasyStickySidebar && window.EasyStickySidebar.isApplyingPresetTemplate) {
                    return false;
                }
                const nextTemplate = (templateKey || sidebarTemplate.value || 'sticky-cta').toString();
                const stickyLayout = nextTemplate === 'sticky-cta' ? 'overlay-left' : getCurrentStickyLayout();
                runResetScenario({ template: nextTemplate, stickyLayout: stickyLayout });
                return true;
            };

            window.easyStickySidebarApplyEditorDefaultsForStickyLayout = function (layoutKey, forceApply) {
                const force = forceApply === true;
                if (!force && window.EasyStickySidebar && window.EasyStickySidebar.isApplyingPresetTemplate) {
                    return false;
                }
                const nextLayout = (layoutKey || getCurrentStickyLayout()).toString();
                runResetScenario({ template: 'sticky-cta', stickyLayout: nextLayout });
                return true;
            };

            function syncCardsFromSelect() {
                const currentValue = sidebarTemplate.value;
                radios.forEach(function (radio) {
                    radio.checked = (radio.value === currentValue);
                    if (radio.closest('.ess-template-card')) {
                        radio.closest('.ess-template-card').classList.toggle('is-selected', radio.checked);
                    }
                });
                if (sidebarTemplateUserSelection) {
                    sidebarTemplateUserSelection.value = currentValue;
                }
                syncStickyLayoutCards();
                toggleTemplateSections();
            }

            function syncSelectFromCards() {
                const checkedCard = getCheckedTemplateCard();
                if (!checkedCard || checkedCard.disabled) {
                    return;
                }
                sidebarTemplate.value = checkedCard.value;
                if (sidebarTemplateUserSelection) {
                    sidebarTemplateUserSelection.value = checkedCard.value;
                }
            }

            function syncStickyLayoutCards() {
                const currentLayout = getCurrentStickyLayout();
                stickyLayoutRadios.forEach(function (radio) {
                    radio.checked = (radio.value === currentLayout);
                    if (radio.closest('.ess-sticky-layout-card')) {
                        radio.closest('.ess-sticky-layout-card').classList.toggle('is-selected', radio.checked);
                    }
                });
            }

            function toggleTemplateSections() {
                const isStickyTemplate = sidebarTemplate.value === 'sticky-cta';
                if (stickyLayoutSection) {
                    stickyLayoutSection.style.display = isStickyTemplate ? "block" : "none";
                }
                if (!designTemplateSection) {
                    return;
                }
                const showPresets = isStickyTemplate && getCurrentStickyLayout() === 'classic';
                designTemplateSection.style.display = showPresets ? "block" : "none";
            }

            radios.forEach(function (radio) {
                radio.addEventListener("change", function () {
                    if (!radio.checked || radio.disabled) {
                        return;
                    }

                    sidebarTemplate.value = radio.value;
                    if (window.jQuery) {
                        window.jQuery(sidebarTemplate).trigger("change").trigger("update");
                    } else {
                        sidebarTemplate.dispatchEvent(new Event("change", { bubbles: true }));
                    }
                    if (sidebarTemplateUserSelection) {
                        sidebarTemplateUserSelection.value = radio.value;
                    }
                    toggleTemplateSections();
                    if (radio.value === "sticky-cta" && !window.easyStickySidebarApplyingDefaults) {
                        window.easyStickySidebarApplyEditorDefaultsForStickyLayout('overlay-left');
                    }
                    if (typeof window.easyStickySidebarRefreshStylingSections === "function") {
                        window.easyStickySidebarRefreshStylingSections();
                    }
                });
            });

            form.addEventListener("submit", function () {
                syncSelectFromCards();
            });

            stickyLayoutRadios.forEach(function (radio) {
                radio.addEventListener("change", function () {
                    if (!radio.checked) {
                        return;
                    }
                    if (!window.easyStickySidebarApplyingDefaults) {
                        window.easyStickySidebarApplyEditorDefaultsForStickyLayout(radio.value);
                    }
                    if (typeof window.easyStickySidebarRefreshStylingSections === "function") {
                        window.easyStickySidebarRefreshStylingSections();
                    }
                });
            });


            if (window.jQuery) {
                window.jQuery(sidebarTemplate).on("change update", syncCardsFromSelect);
                if (imagePlacementField) {
                    window.jQuery(imagePlacementField).on("change", function () {
                        syncStickyLayoutCards();
                        toggleTemplateSections();
                    });
                }
                if (overlayPositionField) {
                    window.jQuery(overlayPositionField).on("change", function () {
                        syncStickyLayoutCards();
                        toggleTemplateSections();
                    });
                }
            } else {
                sidebarTemplate.addEventListener("change", syncCardsFromSelect);
                if (imagePlacementField) {
                    imagePlacementField.addEventListener("change", function () {
                        syncStickyLayoutCards();
                        toggleTemplateSections();
                    });
                }
                if (overlayPositionField) {
                    overlayPositionField.addEventListener("change", function () {
                        syncStickyLayoutCards();
                        toggleTemplateSections();
                    });
                }
            }

            syncCardsFromSelect();
            syncSelectFromCards();
        });
    </script>
    <?php
    do_action('easy_sticky_sidebar_form_after_template', $stickycta, $stickycta->__get('id'));
}

/**
 * Merged placement tab — page position + page/post location.
 * @since  2.2.0
 */
function easy_sticky_sidebar_placement_tab($stickycta) {
    easy_sticky_sidebar_position_tab($stickycta);

    if (has_action('easy_sticky_sidebar_cta_position') && has_action('easy_sticky_sidebar_form_cta_location')) {
        echo '<hr class="ess-section-divider" />';
    }

    easy_sticky_sidebar_location_tab($stickycta);
}

/**
 * Page position section.
 * @since  1.4.0
 */
function easy_sticky_sidebar_position_tab($stickycta) {
    if (has_action('easy_sticky_sidebar_cta_position')) :
        echo '<h4 class="wordpress-cta-heading">' . esc_html__("Page Position", "easy-sticky-sidebar") . '</h4>';
        echo '<p class="wordpress-cta-instruction">' . esc_html__('Select where on the page you would like to display your CTA.', 'easy-sticky-sidebar') . '</p>';

        if (!easy_sticky_sidebar_has_pro()) {
            echo '<p class="wordpress-cta-instruction text-bold">Get more options with our <a href="https://wpctapro.com/" target="_blank">pro version</a>.</p>';
        }
        echo '<div class="ess-settings-grid">';
        do_action('easy_sticky_sidebar_cta_position', $stickycta, $stickycta->__get('id'));
        echo '</div>';
    endif;
}

/**
 * Page/post location section.
 * @since  1.4.0
 */
function easy_sticky_sidebar_location_tab($stickycta) {
    if (has_action('easy_sticky_sidebar_form_cta_location')) :
        echo '<div class="ess-settings-grid">';
        do_action('easy_sticky_sidebar_form_cta_location', $stickycta, $stickycta->__get('id'));
        echo '</div>';
    endif;
}


/**
 * Easy sticky sidebar location tab
 * @since  1.4.0
 */
function easy_sticky_sidebar_responsive_tab($stickycta) { ?>
<!-- 
<div class="s_set"><input type="submit" onclick="return SSuprydp_Admin.ProcessPageData(event, this);"
class="button_save" value="Save Setting"></div> -->
    <h4 class="wordpress-cta-heading"><?php esc_html_e("Responsive Setting", "easy-sticky-sidebar"); ?></h4>
    <p class="wordpress-cta-instruction">Show and hide the cta on different devices.</p>

    <div class="ess-settings-grid ess-responsive-grid">
        <div class="SSuprydp_yes_btn SSuprydp_sliderview">
            <label class="SSuprydp_switch">
                <input type="checkbox" name="SSuprydp_dis_desktop" value="Yes" <?php checked('Yes', $stickycta->SSuprydp_dis_desktop) ?> class="develop_check checkbox-show-hide">
            </label>
            <span class="field_title"><?php esc_html_e("Desktop", "easy-sticky-sidebar"); ?></span>
        </div>

        <div class="SSuprydp_yes_btn SSuprydp_sliderview">
            <label class="SSuprydp_switch">
                <input type="checkbox" name="SSuprydp_dis_tablet" value="Yes" <?php checked('Yes', $stickycta->SSuprydp_dis_tablet) ?> class="develop_check checkbox-show-hide">
            </label>
            <span class="field_title"><?php esc_html_e("Tablet", "easy-sticky-sidebar"); ?></span>
        </div>

        <div class="SSuprydp_yes_btn SSuprydp_sliderview">
            <label class="SSuprydp_switch">
                <input type="checkbox" name="SSuprydp_dis_mobile" value="Yes" <?php checked('Yes', $stickycta->SSuprydp_dis_mobile) ?> class="develop_check checkbox-show-hide">
            </label>
            <span class="field_title"><?php esc_html_e("Mobile", "easy-sticky-sidebar"); ?></span>
        </div>
    </div>
<?php
}

/**
 * Easy sticky sidebar css tab
 * @since  1.4.5
 */
function easy_sticky_sidebar_css_tab($stickycta) { ?>


    <h4 class="wordpress-cta-heading"><?php esc_html_e("Custom CSS", "easy-sticky-sidebar"); ?></h4>

    <p>Example: a { font-size: 16px; }</p>
   
    <div class="SSuprydp_field_wrap wordpress-cta-pro-features ess-settings-span-2">
        <?php easy_sticky_sidebar_pro_get_block(); ?>
        <textarea style="width: 100%" cols="30" rows="10"></textarea>
    </div>
<?php
}

/**
 * Collect styling sections for server-rendered tabs.
 * @since 2.4.2
 */
function easy_sticky_sidebar_get_styling_sections($stickycta) {
    global $wp_filter;

    $sections = [];
    $hook_name = 'easy_sticky_sidebar_styling_options';
    if (empty($wp_filter[$hook_name]) || !($wp_filter[$hook_name] instanceof WP_Hook)) {
        return $sections;
    }

    foreach ($wp_filter[$hook_name]->callbacks as $priority => $callbacks) {
        foreach ($callbacks as $callback_data) {
            if (empty($callback_data['function']) || !is_callable($callback_data['function'])) {
                continue;
            }

            ob_start();
            call_user_func($callback_data['function'], $stickycta);
            $html = trim((string) ob_get_clean());
            if ($html === '') {
                continue;
            }

            if (!preg_match('/<(details|div)\b([^>]*)>/is', $html, $wrapper_match)) {
                continue;
            }

            $wrapper_attrs = $wrapper_match[2];
            $section_id = '';
            $section_class = '';
            if (preg_match('/\bid=(["\'])([^"\']+)\1/i', $wrapper_attrs, $id_match)) {
                $section_id = $id_match[2];
            }
            if (preg_match('/\bclass=(["\'])([^"\']+)\1/i', $wrapper_attrs, $class_match)) {
                $section_class = $class_match[2];
            }

            if ($section_id === '') {
                $section_id = 'ess-styling-section-' . count($sections);
            }

            $label = '';
            if (preg_match('/\bdata-tab-label=(["\'])([^"\']+)\1/i', $wrapper_attrs, $data_label_match)) {
                $label = wp_strip_all_tags($data_label_match[2]);
            } elseif (preg_match('/<h2\b[^>]*class=(["\'])heading\1[^>]*>(.*?)<\/h2>/is', $html, $heading_h2_match)) {
                $label = wp_strip_all_tags($heading_h2_match[2]);
            } elseif (preg_match('/<div\b[^>]*class=(["\'])heading\1[^>]*>(.*?)<\/div>/is', $html, $heading_div_match)) {
                $label = wp_strip_all_tags($heading_div_match[2]);
            } elseif (preg_match('/<h4\b[^>]*class=(["\'])wordpress-cta-heading\1[^>]*>(.*?)<\/h4>/is', $html, $heading_h4_match)) {
                $label = wp_strip_all_tags($heading_h4_match[2]);
            }
            $label = trim(preg_replace('/\s+/', ' ', (string) $label));
            if ($label === '') {
                $label = __('Section', 'easy-sticky-sidebar');
            }

            $sections[] = [
                'id' => $section_id,
                'label' => $label,
                'class' => trim($section_class),
                'html' => $html,
                'priority' => (int) $priority,
            ];
        }
    }

    return $sections;
}

/**
 * Easy sticky sidebar content tab
 * @since  1.4.0
 */
function easy_sticky_sidebar_styling_tab($stickycta) {

    
    echo '<h4 class="wordpress-cta-heading">' . esc_html__("Content / Styling", "easy-sticky-sidebar") . '</h4>';
    echo '<p class="wordpress-cta-instruction">' . esc_html__('Add your content and edit the styles of your CTA.', 'easy-sticky-sidebar') . '</p>';


    
    if (!easy_sticky_sidebar_has_pro()) {
        echo '<p class="wordpress-cta-instruction text-bold">Get more options with our <a href="https://wpctapro.com/" target="_blank">pro version</a>.</p>';
    }


 
   
    $sections = easy_sticky_sidebar_get_styling_sections($stickycta);

    echo '<div class="wordpress-cta-styling-container ess-settings-tabs-pending">';
    echo '<div class="ess-settings-tabs">';
    echo '<div class="ess-settings-tabs-nav" role="tablist">';
    foreach ($sections as $index => $section) {
        $button_classes = 'ess-settings-tab';
        $pane_id = $section['id'] . '-pane';
        if ($index === 0) {
            $button_classes .= ' active';
        }
        printf(
            '<button type="button" class="%1$s" role="tab" aria-selected="%2$s" data-target="%3$s">%4$s%5$s</button>',
            esc_attr($button_classes),
            $index === 0 ? 'true' : 'false',
            esc_attr($pane_id),
            esc_html($section['label']),
            strpos(' ' . $section['class'] . ' ', ' wordpress-cta-pro-tab ') !== false ? ' <span class="ess-pro-pill">PRO</span>' : ''
        );
    }
    echo '</div>';

    echo '<div class="ess-settings-tabs-content">';
    foreach ($sections as $index => $section) {
        $pane_classes = 'ess-settings-pane';
        $pane_id = $section['id'] . '-pane';
        if ($index === 0) {
            $pane_classes .= ' active';
        }
        printf(
            '<div id="%1$s" class="%2$s" role="tabpanel">%3$s</div>',
            esc_attr($pane_id),
            esc_attr($pane_classes),
            // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Server-rendered pane HTML is collected from trusted callbacks.
            $section['html']
        );
    }
    echo '</div>';
    echo '</div>';
    echo '</div>';
}

/**
 * Add section for cta Design templates
 * @since  1.4.5
 */
function easy_sticky_sidebar_design_template_callback($stickycta) {
    return;
}
add_action('easy_sticky_sidebar_styling_options', 'easy_sticky_sidebar_design_template_callback', 1);

/**
 * Easy sticky sidebar CTA Adjustment
 * @since  1.4.0
 */
function easy_sticky_sidebar_cta_adjustment($stickycta) {
    if (has_action('easy_sticky_sidebar_cta_adjustment')) :
        $fieldset_classes = array(
            'easy-sticky-sidebar-fieldset',
            'ess-settings-panel',
            Easy_Sticky_Sidebar_Utils::pro_tab_class('easy_sticky_sidebar_cta_adjustment'),
        );
        ?>
        <div class="<?php echo esc_attr(trim(implode(' ', array_filter($fieldset_classes)))); ?>" id="cta-adjustment-options" data-tab-label="<?php esc_attr_e("Width & Height", "easy-sticky-sidebar"); ?>">
            <?php do_action('easy_sticky_sidebar_cta_adjustment', $stickycta, $stickycta->__get('id')); ?>
            <?php
            if (has_action('easy_sticky_sidebar_cta_height')) {
                do_action('easy_sticky_sidebar_cta_height', $stickycta, $stickycta->__get('id'));
            }
            ?>
        </div>
    <?php endif;
}
add_action('easy_sticky_sidebar_styling_options', 'easy_sticky_sidebar_cta_adjustment', 5);

/**
 * Merged section for animation/behavior settings.
 * @since 2.4.1
 */
function easy_sticky_sidebar_animation_behaviour_options_callback($stickycta) {
    $fieldset_classes = array('easy-sticky-sidebar-fieldset');
    if (!easy_sticky_sidebar_has_pro()) {
        $fieldset_classes[] = 'wordpress-cta-pro-tab';
        $fieldset_classes[] = 'ess-section-pro-cover';
    }
    $fieldset_classes[] = 'ess-settings-panel';
    $current_template = easy_sticky_sidebar_normalize_template_key((string) ($stickycta->sidebar_template ?? 'sticky-cta'), 'sticky-cta');
    $is_floating_buttons = ($current_template === 'floating-buttons');
    ?>
    <div class="<?php echo esc_attr(implode(' ', array_unique($fieldset_classes))); ?>" id="cta-animation-behaviour-options" data-tab-label="<?php esc_attr_e("Animations & Behaviour", "easy-sticky-sidebar"); ?>">
        <h4 class="wordpress-cta-heading ess-settings-span-2"><?php esc_html_e("CTA/Display Behaviour", "easy-sticky-sidebar"); ?></h4>
        <p class="wordpress-cta-instruction ess-settings-span-2"><?php esc_html_e("Control when and how your CTA appears to visitors. Configure triggers, timing, animation, and display frequency for optimal engagement.", "easy-sticky-sidebar"); ?></p>
        <?php
        $is_pro_active = function_exists('easy_sticky_sidebar_has_pro') && easy_sticky_sidebar_has_pro();
        $lock_class = $is_pro_active ? '' : ' wordpress-cta-pro-feature-lock-inline-container wordpress-cta-pro-element';
        ?>
        <?php if (!$is_floating_buttons) : ?>
            <section class="ess-behaviour-card">
                <h5 class="ess-behaviour-card-title"><?php esc_html_e("Scroll Setting", "easy-sticky-sidebar"); ?></h5>
                <div class="SSuprydp_field_wrap<?php echo esc_attr($lock_class); ?>">
                    <h5 class="ess-behaviour-card-title"><?php esc_html_e("Disable Collapse (Keep CTA open after scroll)", "easy-sticky-sidebar"); ?></h5>
                    <label class="SSuprydp_switch">
                        <input type="hidden" name="sticky_cta_disable_collapse" value="no">
                        <input type="checkbox" name="sticky_cta_disable_collapse" value="yes" <?php checked('yes', strtolower((string) ($stickycta->sticky_cta_disable_collapse ?? 'no'))); ?><?php disabled(!$is_pro_active); ?>>
                    </label>
                    <?php if (!$is_pro_active) { Easy_Sticky_Sidebar_Utils::get_inline_lock(); } ?>
                </div>
            </section>

            <section class="ess-behaviour-card">
                <h5 class="ess-behaviour-card-title"><?php esc_html_e("Page Load Setting", "easy-sticky-sidebar"); ?></h5>
                <div class="SSuprydp_field_wrap<?php echo esc_attr($lock_class); ?>">
                    <h5 class="ess-behaviour-card-title"><?php esc_html_e("Collapse CTA On Page Load", "easy-sticky-sidebar"); ?></h5>
                    <label class="SSuprydp_switch">
                        <input type="hidden" name="collapse_on_page_load" value="no">
                        <input type="checkbox" name="collapse_on_page_load" value="yes" <?php checked('yes', strtolower((string) ($stickycta->collapse_on_page_load ?? 'no'))); ?><?php disabled(!$is_pro_active); ?>>
                    </label>
                    <?php if (!$is_pro_active) { Easy_Sticky_Sidebar_Utils::get_inline_lock(); } ?>
                </div>
            </section>
        <?php endif; ?>

        <section class="ess-behaviour-card">
            <h5 class="ess-behaviour-card-title"><?php esc_html_e("Show CTA", "easy-sticky-sidebar"); ?></h5>
            <div class="SSuprydp_field_wrap<?php echo esc_attr($lock_class); ?>">
                <label><?php esc_html_e("Trigger", "easy-sticky-sidebar"); ?></label>
                <select name="display_trigger"<?php disabled(!$is_pro_active); ?>>
                    <option value="immediately" <?php selected('immediately', $stickycta->display_trigger ?? 'immediately'); ?>><?php esc_html_e("Show immediately", "easy-sticky-sidebar"); ?></option>
                    <option value="after_seconds" <?php selected('after_seconds', $stickycta->display_trigger ?? ''); ?>><?php esc_html_e("Show after X seconds", "easy-sticky-sidebar"); ?></option>
                    <option value="after_scroll" <?php selected('after_scroll', $stickycta->display_trigger ?? ''); ?>><?php esc_html_e("Show after X% scroll", "easy-sticky-sidebar"); ?></option>
                </select>
                <?php if (!$is_pro_active) { Easy_Sticky_Sidebar_Utils::get_inline_lock(); } ?>
            </div>
            <div class="SSuprydp_field_wrap ess-display-trigger-seconds<?php echo esc_attr($lock_class); ?>">
                <label><?php esc_html_e("After X seconds", "easy-sticky-sidebar"); ?></label>
                <input type="number" min="0" style="width:80px" name="display_trigger_seconds" value="<?php echo esc_attr($stickycta->display_trigger_seconds ?? 0); ?>"<?php disabled(!$is_pro_active); ?>>
                <?php if (!$is_pro_active) { Easy_Sticky_Sidebar_Utils::get_inline_lock(); } ?>
            </div>
            <div class="SSuprydp_field_wrap ess-display-trigger-scroll<?php echo esc_attr($lock_class); ?>">
                <label><?php esc_html_e("After X% scroll", "easy-sticky-sidebar"); ?></label>
                <input type="number" min="0" max="100" style="width:80px" name="display_trigger_scroll" value="<?php echo esc_attr($stickycta->display_trigger_scroll ?? 0); ?>"<?php disabled(!$is_pro_active); ?>> %
                <?php if (!$is_pro_active) { Easy_Sticky_Sidebar_Utils::get_inline_lock(); } ?>
            </div>
            <div class="SSuprydp_field_wrap<?php echo esc_attr($lock_class); ?>">
                <label><?php esc_html_e("Animation", "easy-sticky-sidebar"); ?></label>
                <select name="display_animation"<?php disabled(!$is_pro_active); ?>>
                    <option value="none" <?php selected('none', $stickycta->display_animation ?? 'none'); ?>><?php esc_html_e("None", "easy-sticky-sidebar"); ?></option>
                    <option value="fade" <?php selected('fade', $stickycta->display_animation ?? ''); ?>><?php esc_html_e("Fade in", "easy-sticky-sidebar"); ?></option>
                    <option value="slide_up" <?php selected('slide_up', $stickycta->display_animation ?? ''); ?>><?php esc_html_e("Slide up", "easy-sticky-sidebar"); ?></option>
                    <option value="slide_down" <?php selected('slide_down', $stickycta->display_animation ?? ''); ?>><?php esc_html_e("Slide down", "easy-sticky-sidebar"); ?></option>
                    <option value="slide_left" <?php selected('slide_left', $stickycta->display_animation ?? ''); ?>><?php esc_html_e("Slide left", "easy-sticky-sidebar"); ?></option>
                    <option value="slide_right" <?php selected('slide_right', $stickycta->display_animation ?? ''); ?>><?php esc_html_e("Slide right", "easy-sticky-sidebar"); ?></option>
                </select>
                <?php if (!$is_pro_active) { Easy_Sticky_Sidebar_Utils::get_inline_lock(); } ?>
            </div>
        </section>

        <section class="ess-behaviour-card">
            <h5 class="ess-behaviour-card-title"><?php esc_html_e("Hide CTA", "easy-sticky-sidebar"); ?></h5>
            <div class="SSuprydp_field_wrap<?php echo esc_attr($lock_class); ?>">
                <label><?php esc_html_e("Auto Hide", "easy-sticky-sidebar"); ?></label>
                <select name="hide_behavior"<?php disabled(!$is_pro_active); ?>>
                    <option value="none" <?php selected('none', $stickycta->hide_behavior ?? 'none'); ?>><?php esc_html_e("Don't auto hide", "easy-sticky-sidebar"); ?></option>
                    <option value="after_seconds" <?php selected('after_seconds', $stickycta->hide_behavior ?? ''); ?>><?php esc_html_e("Hide after X seconds", "easy-sticky-sidebar"); ?></option>
                    <option value="near_bottom" <?php selected('near_bottom', $stickycta->hide_behavior ?? ''); ?>><?php esc_html_e("Hide near page bottom", "easy-sticky-sidebar"); ?></option>
                </select>
                <?php if (!$is_pro_active) { Easy_Sticky_Sidebar_Utils::get_inline_lock(); } ?>
            </div>
            <div class="SSuprydp_field_wrap ess-hide-after-seconds<?php echo esc_attr($lock_class); ?>">
                <label><?php esc_html_e("Hide after X seconds", "easy-sticky-sidebar"); ?></label>
                <input type="number" min="0" style="width:80px" name="hide_after_seconds" value="<?php echo esc_attr($stickycta->hide_after_seconds ?? 0); ?>"<?php disabled(!$is_pro_active); ?>>
                <?php if (!$is_pro_active) { Easy_Sticky_Sidebar_Utils::get_inline_lock(); } ?>
            </div>
        </section>

        <section class="ess-behaviour-card">
            <h5 class="ess-behaviour-card-title"><?php esc_html_e("Display Frequency", "easy-sticky-sidebar"); ?></h5>
            <div class="SSuprydp_field_wrap<?php echo esc_attr($lock_class); ?>">
                <label><?php esc_html_e("Frequency", "easy-sticky-sidebar"); ?></label>
                <select name="display_frequency"<?php disabled(!$is_pro_active); ?>>
                    <option value="every_time" <?php selected('every_time', $stickycta->display_frequency ?? 'every_time'); ?>><?php esc_html_e("Show every time", "easy-sticky-sidebar"); ?></option>
                    <option value="once_per_visit" <?php selected('once_per_visit', $stickycta->display_frequency ?? ''); ?>><?php esc_html_e("Show once per visit", "easy-sticky-sidebar"); ?></option>
                    <option value="every_24_hours" <?php selected('every_24_hours', $stickycta->display_frequency ?? ''); ?>><?php esc_html_e("Show once every 24 hours", "easy-sticky-sidebar"); ?></option>
                    <option value="every_7_days" <?php selected('every_7_days', $stickycta->display_frequency ?? ''); ?>><?php esc_html_e("Show once every 7 days", "easy-sticky-sidebar"); ?></option>
                </select>
                <?php if (!$is_pro_active) { Easy_Sticky_Sidebar_Utils::get_inline_lock(); } ?>
            </div>
        </section>

        <section class="ess-behaviour-card">
            <h5 class="ess-behaviour-card-title"><?php esc_html_e("After Close", "easy-sticky-sidebar"); ?></h5>
            <div class="SSuprydp_field_wrap<?php echo esc_attr($lock_class); ?>">
                <label><?php esc_html_e("Behavior", "easy-sticky-sidebar"); ?></label>
                <select name="after_close_behavior"<?php disabled(!$is_pro_active); ?>>
                    <option value="next_visit" <?php selected('next_visit', $stickycta->after_close_behavior ?? 'next_visit'); ?>><?php esc_html_e("Show again next visit", "easy-sticky-sidebar"); ?></option>
                    <option value="hide_for_time" <?php selected('hide_for_time', $stickycta->after_close_behavior ?? ''); ?>><?php esc_html_e("Don't show again for X time", "easy-sticky-sidebar"); ?></option>
                </select>
                <?php if (!$is_pro_active) { Easy_Sticky_Sidebar_Utils::get_inline_lock(); } ?>
            </div>
            <div class="SSuprydp_field_wrap ess-after-close-time<?php echo esc_attr($lock_class); ?>">
                <label><?php esc_html_e("Don't show again for", "easy-sticky-sidebar"); ?></label>
                <input type="number" min="0" style="width:80px" name="after_close_time" value="<?php echo esc_attr($stickycta->after_close_time ?? 0); ?>"<?php disabled(!$is_pro_active); ?>>
                <select name="after_close_time_unit"<?php disabled(!$is_pro_active); ?>>
                    <option value="hours" <?php selected('hours', $stickycta->after_close_time_unit ?? 'hours'); ?>><?php esc_html_e("Hours", "easy-sticky-sidebar"); ?></option>
                    <option value="days" <?php selected('days', $stickycta->after_close_time_unit ?? ''); ?>><?php esc_html_e("Days", "easy-sticky-sidebar"); ?></option>
                </select>
                <?php if (!$is_pro_active) { Easy_Sticky_Sidebar_Utils::get_inline_lock(); } ?>
            </div>
        </section>
        <?php
        if (!$is_pro_active) {
            Easy_Sticky_Sidebar_Utils::get_inline_lock();
        }
        ?>
    </div>
    <?php
}
add_action('easy_sticky_sidebar_styling_options', 'easy_sticky_sidebar_animation_behaviour_options_callback', 13);

/**
 * Easy sticky sidebar CTA Image
 * @since  1.4.0
 */
function easy_sticky_sidebar_cta_image($stickycta) {
    if (has_action('easy_sticky_sidebar_cta_image')) :
        $fieldset_classes = array('easy-sticky-sidebar-fieldset', 'ess-settings-panel');
        $auto_pro_class = Easy_Sticky_Sidebar_Utils::pro_tab_class('easy_sticky_sidebar_cta_image');
        if (!empty($auto_pro_class)) {
            $fieldset_classes[] = $auto_pro_class;
        }
        ?>
        <div class="<?php echo esc_attr(implode(' ', array_unique($fieldset_classes))); ?>" id="sticky-cta-banner-image" data-tab-label="<?php esc_attr_e("Image Settings", "easy-sticky-sidebar"); ?>">
            <div class="ess-settings-grid">
                <?php do_action('easy_sticky_sidebar_cta_image', $stickycta, $stickycta->__get('id')); ?>
            </div>
        </div>
    <?php endif;
}
add_action('easy_sticky_sidebar_styling_options', 'easy_sticky_sidebar_cta_image', 6);

/**
 * Easy sticky sidebar CTA Tab options
 * @since  1.4.0
 */
function easy_sticky_sidebar_button_options($stickycta) {
    if (has_action('easy_sticky_sidebar_button_options')) : ?>
        <div class="easy-sticky-sidebar-fieldset ess-settings-panel <?php echo esc_attr(Easy_Sticky_Sidebar_Utils::pro_tab_class('easy_sticky_sidebar_button_options')); ?>" id="sticky-sidebar-button-options" data-tab-label="<?php esc_attr_e("Tab Settings", "easy-sticky-sidebar"); ?>">

            <?php do_action('easy_sticky_sidebar_button_options', $stickycta, $stickycta->__get('id')); ?>

            <?php if (has_action('easy_sticky_sidebar_button2_options')) : ?>
                <div id="sticky-sidebar-button2-options" class="wordpress-cta-gdpr-options">
                    <div class="ess-settings-grid">
                        <h3 class="ess-settings-span-2">Decline Button Options</h3>
                        <?php do_action('easy_sticky_sidebar_button2_options', $stickycta, $stickycta->__get('id')); ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    <?php endif;
}
add_action('easy_sticky_sidebar_styling_options', 'easy_sticky_sidebar_button_options', 7);

/**
 * Easy sticky sidebar CTA content options
 * @since  1.4.0
 */
function easy_sticky_sidebar_line_separator($stickycta) {
    if (has_action('easy_sticky_sidebar_line_separator')) : ?>
        <div class="easy-sticky-sidebar-fieldset ess-settings-panel <?php echo esc_attr(Easy_Sticky_Sidebar_Utils::pro_tab_class('easy_sticky_sidebar_line_separator')); ?>" id="cta-line-separator-options" data-tab-label="<?php esc_attr_e("Line Separator Settings", "easy-sticky-sidebar"); ?>">
            <?php do_action('easy_sticky_sidebar_line_separator', $stickycta, $stickycta->__get('id')) ?>
        </div>
    <?php endif;
}
add_action('easy_sticky_sidebar_styling_options', 'easy_sticky_sidebar_line_separator', 9);


/**
 * Easy sticky sidebar CTA call to action
 * @since  1.4.0
 */
function easy_sticky_sidebar_call_to_action($stickycta) {
    if (has_action('easy_sticky_sidebar_call_to_action')) : ?>
        <div class="easy-sticky-sidebar-fieldset ess-settings-panel <?php echo esc_attr(Easy_Sticky_Sidebar_Utils::pro_tab_class('easy_sticky_sidebar_call_to_action')); ?>" id="cta-link-text-options" data-tab-label="<?php esc_attr_e("Button Settings", "easy-sticky-sidebar"); ?>">
            <?php do_action('easy_sticky_sidebar_call_to_action', $stickycta, $stickycta->__get('id')) ?>
        </div>
    <?php endif;
}
add_action('easy_sticky_sidebar_styling_options', 'easy_sticky_sidebar_call_to_action', 10);

/**
 * Easy sticky sidebar CTA content options
 * @since  1.4.0
 */
function easy_sticky_sidebar_close_button_options($stickycta) {
    if (has_action('easy_sticky_sidebar_close_button_options')) :
        $fieldset_classes = array(
            'easy-sticky-sidebar-fieldset',
            'ess-settings-panel',
            Easy_Sticky_Sidebar_Utils::pro_tab_class('easy_sticky_sidebar_close_button_options'),
        );
        ?>
        <div class="<?php echo esc_attr(trim(implode(' ', array_filter($fieldset_classes)))); ?>" id="cta-close-button-options" data-tab-label="<?php esc_attr_e("Close Button Settings", "easy-sticky-sidebar"); ?>">
            <?php do_action('easy_sticky_sidebar_close_button_options', $stickycta, $stickycta->__get('id')) ?>
        </div>
    <?php endif;
}
add_action('easy_sticky_sidebar_styling_options', 'easy_sticky_sidebar_close_button_options', 11);

function easy_sticky_sidebar_box_shadow_options($stickycta) {
    if (has_action('easy_sticky_sidebar_box_shadow_options')) :
        $fieldset_classes = array(
            'easy-sticky-sidebar-fieldset',
            'ess-settings-panel',
            Easy_Sticky_Sidebar_Utils::pro_tab_class('easy_sticky_sidebar_box_shadow_options'),
        );
        ?>
        <div class="<?php echo esc_attr(trim(implode(' ', array_filter($fieldset_classes)))); ?>" id="cta-box-shadow-options" data-tab-label="<?php esc_attr_e("Box Shadow Setting", "easy-sticky-sidebar"); ?>">
            <?php do_action('easy_sticky_sidebar_box_shadow_options', $stickycta, $stickycta->__get('id')) ?>
        </div>
    <?php endif;
}
add_action('easy_sticky_sidebar_styling_options', 'easy_sticky_sidebar_box_shadow_options', 12);

/**
 * Get pro featured block
 * @since  1.4.5
 */
function easy_sticky_sidebar_pro_get_block($title = '', $description = null) {
    // Don't show block if pro plugin is active
    if (easy_sticky_sidebar_has_pro()) {
        return;
    }
    
    if (empty($title)) {
        $title = __('This is a pro feature', 'easy-sticky-sidebar');
    }

    if (empty($description)) {
        //$description = __('Unlock to create unlimited and customizable recurring campaigns.', 'easy-sticky-sidebar');
    } ?>

    <div class="wordpress-cta-pro-block">
        <span class="dashicons dashicons-lock"></span>
        <h4 class="pro-title"><?php echo esc_html($title) ?></h4>

        <div class="pro-description"><?php echo esc_html($description) ?></div>

        <footer>
            <a class="button btn-wordpress-cta-primary" href="https://wpctapro.com/pricing/" target="_blank"><?php esc_html_e('Upgrade now', 'easy-sticky-sidebar') ?></a>
            <a href="https://wpctapro.com/" target="_blank"><?php esc_html_e('Learn more', 'easy-sticky-sidebar') ?></a>
        </footer>
    </div>
    <?php
}

function easy_sticky_sidebar_location_group($key = '') {
    $location_groups = [
        'general' => __('General', 'easy-sticky-sidebar'),
        'post' => __('Single Posts, Pages or CPT (pro Feature)', 'easy-sticky-sidebar'),
        'post_taxonomy' => __('Posts, Pages or CPT With(pro Feature)', 'easy-sticky-sidebar'),
        'archive' => __('Archive Pages With (pro Feature)', 'easy-sticky-sidebar'),
    ];

    return isset($location_groups[$key]) ? $location_groups[$key] : '';
}
function easy_sticky_sidebar_get_location_types() {
    $location_types = [];

    // General group - only Home Page should be enabled
    $location_types['general'] = [
        'all'        => __('Home / Front Page', 'easy-sticky-sidebar'),
        'singular'   => __('All Singular (pro feature)', 'easy-sticky-sidebar'),
        'archive'    => __('All Archives (pro feature)', 'easy-sticky-sidebar'),
        'search'     => __('Search Results (pro feature)', 'easy-sticky-sidebar'),
        '404'        => __('404 Page (pro feature)', 'easy-sticky-sidebar')
    ];

    // Other groups - all options disabled
    $post_types = get_post_types(['public' => true], 'objects');
    foreach ($post_types as $pkey => $post_type) {
        $location_types['post'][$pkey] = $post_type->label . ' (pro feature)';
    }

    $taxonomies = get_taxonomies(['public' => true], 'objects');
    unset($taxonomies['post_format']);
    foreach ($taxonomies as $key => $taxonomy) {
        $location_types['post_taxonomy'][$key] = $taxonomy->label . ' (pro feature)';
    }

    foreach ($taxonomies as $taxonomy_slug => $taxonomy) {
        $location_types['archive'][$taxonomy_slug] = 'Archive ' . $taxonomy->label . ' (pro feature)';
    }

    return $location_types;
}


 








/**
 * Easy sticky sidebar content tab
 * @since  1.4.5
 */
function easy_sticky_sidebar_content_tab_callback($stickycta) {

  
    echo '<h4 class="wordpress-cta-heading">' . esc_html__("Content", "easy-sticky-sidebar") . '</h4>';
    echo '<p class="wordpress-cta-instruction">' . esc_html__('Please enter your text / content', 'easy-sticky-sidebar') . '</p>';

    echo '<div class="gap-10"></div>';
    echo '<div class="wordpress-cta-content-container ess-settings-grid">';
    do_action('easy_sticky_sidebar_content_tab_options', $stickycta);
    echo '</div>';
}

/**
 * CTA image container
 * @since 1.4.5
 */
function easy_sticky_sidebar_image_container($stickycta) {
    if (!has_action('easy_sticky_sidebar_content_image')) {
        return;
    } ?>
    <div id="sticky_cta_banner_img" class="SSuprydp_field_wrap banner_img ">
        <?php do_action('easy_sticky_sidebar_content_image', $stickycta) ?>
    </div>
<?php
}
add_action('easy_sticky_sidebar_content_tab_options', 'easy_sticky_sidebar_image_container', 5);

/**
 * CTA button text container
 * @since 1.4.5
 */
function easy_sticky_sidebar_button_text_container($stickycta) {
    if (!has_action('easy_sticky_sidebar_content_button')) {
        return;
    } ?>
    <div id="sticky-cta-content-button-container">
        <?php do_action('easy_sticky_sidebar_content_button', $stickycta) ?>
        <?php if (has_action('easy_sticky_sidebar_content_button2_options')) : ?>
            <div class="button2-content-option wordpress-cta-gdpr-options">
                <hr>
                <h3>Decline Button Options</h3>
                <?php do_action('easy_sticky_sidebar_content_button2_options', $stickycta); ?>
            </div>
        <?php endif; ?>
    </div>
<?php
}
add_action('easy_sticky_sidebar_content_tab_options', 'easy_sticky_sidebar_button_text_container', 10);



/**
 * CTA content text container
 * @since 1.4.5
 */
function easy_sticky_sidebar_content_text_container($stickycta) {
    if (!has_action('easy_sticky_sidebar_content_text')) {
        return;
    } ?>
    <div id="sticky-cta-content-text-container">
        <?php do_action('easy_sticky_sidebar_content_text', $stickycta) ?>
    </div>
<?php
}
add_action('easy_sticky_sidebar_content_tab_options', 'easy_sticky_sidebar_content_text_container', 15);

/**
 * CTA button text container
 * @since 1.4.5
 */
function easy_sticky_sidebar_content_link_option_callback($stickycta) {
    if (!has_action('easy_sticky_sidebar_content_link_options')) {
        return;
    } ?>
    <div id="sticky-cta-content-link-options-container">
        <?php do_action('easy_sticky_sidebar_content_link_options', $stickycta) ?>
    </div>
    <?php
}
add_action('easy_sticky_sidebar_content_tab_options', 'easy_sticky_sidebar_content_link_option_callback', 20);


/**
 * Easy sticky sidebar CTA content options
 * @since  1.4.5
 */
function easy_sticky_sidebar_content_styling_option_callback($stickycta) {
    if (has_action('easy_sticky_sidebar_content_option')) : ?>
        <div class="easy-sticky-sidebar-fieldset ess-settings-panel <?php echo esc_attr(Easy_Sticky_Sidebar_Utils::pro_tab_class('easy_sticky_sidebar_page_load_options')); ?>" id="cta-content-options" data-tab-label="<?php esc_attr_e("Content Settings", "easy-sticky-sidebar"); ?>">
            <?php do_action('easy_sticky_sidebar_content_option', $stickycta); ?>
        </div>
    <?php endif;
}
add_action('easy_sticky_sidebar_styling_options', 'easy_sticky_sidebar_content_styling_option_callback', 8);

/**
 * global style
 * @since  1.4.7
 */
function easy_sticky_sidebar_global_style_callback($stickycta) {
    if (has_action('easy_sticky_sidebar_global_styles')) : ?>
        <div id="global-style-tab" class="easy-sticky-sidebar-fieldset ess-settings-panel ess-template-floating-only <?php echo esc_attr(Easy_Sticky_Sidebar_Utils::pro_tab_class('easy_sticky_sidebar_global_styles')); ?>" data-tab-label="<?php esc_attr_e("Global Style", "easy-sticky-sidebar"); ?>">
            <div class="ess-settings-grid">
                <p class="wordpress-cta-instruction ess-settings-span-2">Set the styles for all the buttons here. If you edit an individual button, that style will override the global style for only that button.</p>
                <?php do_action('easy_sticky_sidebar_global_styles', $stickycta); ?>
            </div>
        </div>
    <?php endif;
}
add_action('easy_sticky_sidebar_styling_options', 'easy_sticky_sidebar_global_style_callback', 1);

function easy_sticky_sidebar_settings_disable_google_font($post_data) { ?>
    <div class="SSuprydp_field_wrap">
        <div class="heading">Disable Google Fonts</div>
        <p class="wordpress-cta-instruction">Disable Google fonts to be GDPR compliant. Please note that you need will to call your own local fonts using css.</p>

        <div class="gap-10"></div>

        <div class="wordpress-cta-pro-feature-lock-inline-container transparent">
            <label class="SSuprydp_switch">
                <input type="checkbox" disabled>
            </label>

            <?php Easy_Sticky_Sidebar_Utils::get_inline_lock(array('left' => '16px', 'top' => '-13px')) ?>
        </div>
    </div>

<?php
}
add_action('easy_sticky_sidebar_settings', 'easy_sticky_sidebar_settings_disable_google_font');

// Button icon (now free)
if (!function_exists('easy_sticky_sidebar_normalize_icon_class')) {
    /**
     * Normalize saved icon class so frontend/admin preview render consistently.
     *
     * @since 1.7.2
     */
    function easy_sticky_sidebar_normalize_icon_class($icon_class) {
        $icon_class = trim((string) $icon_class);
        if ($icon_class === '') {
            return '';
        }

        $parts = preg_split('/\s+/', $icon_class);
        if (!is_array($parts)) {
            return '';
        }

        $parts = array_values(array_unique(array_filter(array_map(static function ($part) {
            $part = strtolower(trim((string) $part));
            if ($part === '') {
                return '';
            }

            return preg_match('/^[a-z0-9-]+$/', $part) ? $part : '';
        }, $parts))));

        if (empty($parts)) {
            return '';
        }

        $has_base = (bool) array_intersect($parts, ['fa', 'fas', 'far', 'fal', 'fab', 'fat', 'fa-solid', 'fa-regular', 'fa-light', 'fa-brands', 'fa-thin', 'fa-duotone']);
        $has_icon_name = false;
        foreach ($parts as $part) {
            if (strpos($part, 'fa-') === 0 && !in_array($part, ['fa-solid', 'fa-regular', 'fa-light', 'fa-brands', 'fa-thin', 'fa-duotone'], true)) {
                $has_icon_name = true;
                break;
            }
        }

        // Backward compatibility for values like "fa-angle-right".
        if ($has_icon_name && !$has_base) {
            array_unshift($parts, 'fa');
        }

        return implode(' ', $parts);
    }
}

if (!function_exists('easy_sticky_sidebar_add_button_icon')) {
    function easy_sticky_sidebar_get_button_icon_position($stickycta) {
        $position = strtolower((string) ($stickycta->button_icon_position ?? 'before'));
        return in_array($position, array('before', 'after'), true) ? $position : 'before';
    }
}

if (!function_exists('easy_sticky_sidebar_get_button_icon_html')) {
    function easy_sticky_sidebar_get_button_icon_html($stickycta) {
        $icon_class = easy_sticky_sidebar_normalize_icon_class($stickycta->button_icon ?? '');
        $icon_size  = absint($stickycta->button_icon_size ?? 16);
        if (!empty($icon_class)) {
            return sprintf(
                '<i class="icon %s" style="font-size:%dpx;"></i>',
                esc_attr($icon_class),
                $icon_size > 0 ? $icon_size : 16
            );
        }
        return '';
    }
}

if (!function_exists('easy_sticky_sidebar_add_button_icon')) {
    function easy_sticky_sidebar_add_button_icon($stickycta) {
        echo wp_kses(
            easy_sticky_sidebar_get_button_icon_html($stickycta),
            array(
                'i' => array(
                    'class' => array(),
                    'style' => array(),
                ),
            )
        );
    }
}


