<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

$tabs = easy_sticky_sidebar_get_cta_tabs();
$templates = easy_sticky_sidebar_templates();
$current_template = (string) $stickycta->sidebar_template;
if ($current_template === '') {
    $current_template = 'sticky-cta';
}
$current_template = function_exists('easy_sticky_sidebar_normalize_template_key')
    ? easy_sticky_sidebar_normalize_template_key($current_template, 'sticky-cta')
    : $current_template;
$current_template_label = isset($templates[$current_template]) ? $templates[$current_template] : ucfirst(str_replace('-', ' ', $current_template));
$preview_image_mode = strtolower((string) ($stickycta->image_placement ?? 'classic'));
if ($preview_image_mode === 'background') {
    $preview_image_mode = 'overlay';
}
$preview_image_fallback = EASY_STICKY_SIDEBAR_PLUGIN_URL . '/assets/img/ss_dummy.jpg';
if ($current_template === 'sticky-cta' && $preview_image_mode === 'overlay') {
    $preview_image_fallback = EASY_STICKY_SIDEBAR_PLUGIN_URL . '/assets/img/overlay_dummy.webp';
}
$preview_image = !empty($stickycta->sticky_s_media) ? $stickycta->sticky_s_media : $preview_image_fallback;
if (
    $current_template === 'sticky-cta'
    && $preview_image_mode === 'overlay'
    && is_string($preview_image)
    && stripos($preview_image, 'ss_dummy.jpg') !== false
) {
    $preview_image = EASY_STICKY_SIDEBAR_PLUGIN_URL . '/assets/img/overlay_dummy.webp';
}
$is_pro_active = easy_sticky_sidebar_has_pro();
$preview_position = !empty($stickycta->SSuprydp_cta_position) ? $stickycta->SSuprydp_cta_position : 'right';
$preview_overlay_position = strtolower((string) ($stickycta->overlay_position ?? 'right'));
$preview_full_tab_height = strtolower((string) ($stickycta->overlay_full_tab_height ?? 'no')) === 'yes';
$preview_text_orientation = strtolower((string) ($stickycta->button_text_orientation ?? ($stickycta->overlay_tab_text_orientation ?? 'top-to-bottom')));

$preview_align = function_exists('easy_sticky_sidebar_normalize_secondary_position')
    ? easy_sticky_sidebar_normalize_secondary_position($preview_position, $stickycta->horizontal_vertical_position ?? '', 'center')
    : (!empty($stickycta->horizontal_vertical_position) ? $stickycta->horizontal_vertical_position : 'center');

if (!in_array($preview_position, ['left', 'right', 'top', 'bottom'], true)) {
    $preview_position = 'right';
}

$preview_classes = [
    'easy-sticky-sidebar',
    'sticky-cta',
    'ess-preview-static',
    'sticky-cta-position-' . $preview_position,
];

$show_close_button = ($stickycta->show_close_button ?? '') === 'yes';
$close_button_position = !empty($stickycta->close_button_position) ? $stickycta->close_button_position : 'start';
$close_button_edge = ($stickycta->close_button_edge ?? '') === 'yes' ? 'outside' : '';
$close_button_color = !empty($stickycta->close_button_color) ? $stickycta->close_button_color : '#ffffff';
$close_button_style = $show_close_button ? '' : 'display:none;';

if ($show_close_button) {
    $preview_classes[] = 'ess-close-button-' . $close_button_position;
}

$preview_anchor_align = $preview_align;

if ($current_template === 'sticky-cta' && $preview_image_mode === 'overlay') {
    $preview_classes[] = 'image-as-background';
    $preview_classes[] = 'overlay-pos-' . (in_array($preview_overlay_position, ['left', 'right', 'top', 'bottom'], true) ? $preview_overlay_position : 'right');

    if ($preview_full_tab_height) {
        $preview_classes[] = 'ess-overlay-full-tab-height';
    }
}

if (in_array($preview_position, ['top', 'bottom'], true)) {
    $preview_classes[] = 'vertical-cta';
    $preview_classes[] = 'vertical-cta-' . $preview_position;
}

if ($preview_text_orientation === 'bottom-to-top' && in_array($preview_position, ['left', 'right'], true)) {
    $preview_classes[] = 'ess-tab-text-bottom-to-top';
}

ob_start();
do_action('easy_sticky_sidebar_before_tab', $stickycta);
$before_tab_content = trim(ob_get_clean());
?>

<div class="wrap wrap-easy-sticky-sidebar ess-dashboard-redesign">
    <?php easy_sticky_sidebar_get_header(); ?>
    <hr class="wp-header-end">

    <div class="easy-sticky-sidebar-container">
        <div id="SSuprydp_builder_form">
            <div class="SSuprydp_col_2 SSuprydp-form-col">
                <form id="SSuprydp_form" method="post"
                    action="<?php echo esc_url(add_query_arg('action', 'process_pages', admin_url('admin-ajax.php'))); ?>"
                    <?php echo wp_kses_post(implode(' ', $form_attributes)); ?>>
                    <input type="hidden" id="ajaxaction"
                        value="<?php echo esc_url(add_query_arg('action', 'ajax_check', admin_url('admin-ajax.php'))); ?>" />
                    <?php wp_nonce_field('_nonce_easy_sticky_sidebar'); ?>
                    <input type="hidden" name="sticky_id" value="<?php echo esc_attr($sticky_id); ?>" />
                    <input type="hidden" name="cta_editor_current_tab" value="<?php echo esc_attr($editor_current_tab); ?>">
                    <div class="SSuprydp_page_fields ess-page-fields">
                        <div class="ssuprydp_load" style="display:none;">
                            <p>Loading.....</p>
                        </div>

                        <div class="ess-editor-grid">
                            <div class="ess-main-column">
                                <section class="SSuprydp_field_wrap cta-name-field ess-card">
                                    <label class="heading"><?php esc_html_e("CTA Name", "easy-sticky-sidebar"); ?></label>
                                    <input type="text" name="sidebar_name" class="SSuprydp_input"
                                        value="<?php echo esc_attr($stickycta->sidebar_name); ?>" placeholder="Enter CTA name here">
                                </section>

                                <div class="status-notice status-notice-off">
                                    <p><?php esc_html_e('Your CTA live status is set to Off and will not show on the front end.', 'easy-sticky-sidebar'); ?></p>
                                </div>

                                <div class="status-notice status-notice-development">
                                    <p><?php esc_html_e('Your CTA live status is set to Development and will show on the front end only for users logged in as admin.', 'easy-sticky-sidebar'); ?></p>
                                </div>

                                <section class="ess-live-preview-card ess-card is-preview-loading" aria-busy="true">
                                    <header class="ess-section-head">
                                        <h2><?php esc_html_e('Live Preview', 'easy-sticky-sidebar'); ?></h2>
                                    </header>

                                    <div class="ess-preview-canvas">
                                        <div class="ess-preview-spinner" aria-hidden="true">
                                            <span class="ess-spinner"></span>
                                        </div>
                                        <div class="ess-preview-stage">
                                            <div class="ess-preview-anchor" data-position="<?php echo esc_attr($preview_position); ?>" data-align="<?php echo esc_attr($preview_anchor_align); ?>">
                                                <div class="ess-preview-template ess-preview-sticky<?php echo $current_template === 'sticky-cta' ? ' is-active' : ''; ?>" data-template="sticky-cta">
                                                    <div class="<?php echo esc_attr(implode(' ', $preview_classes)); ?>" id="ess-preview-cta">
                                                        <div class="sticky-sidebar-button" id="ess-preview-button-wrap">
                                                            <div id="ess-preview-button-text">
                                                            <?php echo esc_html($stickycta->SSuprydp_button_option_text ? $stickycta->SSuprydp_button_option_text : __('Have Questions?', 'easy-sticky-sidebar')); ?>
                                                            </div>
                                                        </div>

                                                    <div class="sticky-sidebar-content sticky-sidebar-container">
                                                        <div class="sticky-sidebar-image" id="ess-preview-image-wrap"
                                                            style="background-image: url('<?php echo esc_url($preview_image); ?>');"></div>

                                                        <div class="sticky-overlay-panel" id="ess-preview-overlay-panel">
                                                        <div class="sticky-sidebar-text sticky-content-inner" id="ess-preview-content-text">
                                                            <?php echo esc_html(wp_strip_all_tags((string) $stickycta->SSuprydp_content_option_text)); ?>
                                                        </div>

                                                        <hr id="ess-preview-divider">

                                                        <div class="sticky-sidebar-call-to-action sticky-content-inner" id="ess-preview-link">
                                                            <?php echo esc_html($stickycta->SSuprydp_action_option_text ? $stickycta->SSuprydp_action_option_text : __('Get Started', 'easy-sticky-sidebar')); ?>
                                                        </div>
                                                        </div>
                                                    </div>
                                                    <span style="background-color: <?php echo esc_attr($close_button_color); ?>; <?php echo esc_attr($close_button_style); ?>" class="btn-ess-close icon-close <?php echo esc_attr($close_button_position); ?> <?php echo esc_attr($close_button_edge); ?>"></span>
                                                    </div>
                                                </div>

                                                <div class="ess-preview-template ess-preview-tab<?php echo $current_template === 'tab-cta' ? ' is-active' : ''; ?>" data-template="tab-cta">
                                                    <div class="easy-sticky-sidebar ess-preview-static ess-preview-tab-cta sticky-cta-position-<?php echo esc_attr($preview_position); ?><?php echo $show_close_button ? ' ess-close-button-' . esc_attr($close_button_position) : ''; ?>" id="ess-preview-tab-cta">
                                                        <a class="sticky-sidebar-button" id="ess-preview-tab-button" href="#" aria-label="<?php esc_attr_e('Preview tab CTA', 'easy-sticky-sidebar'); ?>">
                                                                <div id="ess-preview-tab-button-text">
                                                                <?php echo esc_html($stickycta->SSuprydp_button_option_text ? $stickycta->SSuprydp_button_option_text : __('Call Now', 'easy-sticky-sidebar')); ?>
                                                            </div>
                                                        </a>
                                                        <span style="background-color: <?php echo esc_attr($close_button_color); ?>; <?php echo esc_attr($close_button_style); ?>" class="btn-ess-close icon-close <?php echo esc_attr($close_button_position); ?> <?php echo esc_attr($close_button_edge); ?>"></span>
                                                    </div>
                                                </div>

                                                <div class="ess-preview-template ess-preview-banner<?php echo $current_template === 'banner' ? ' is-active' : ''; ?>" data-template="banner">
                                                    <div class="easy-sticky-sidebar wordpress-cta-pro-banner ess-preview-banner" id="ess-preview-banner">
                                                        <div class="ess-preview-banner-text" id="ess-preview-banner-text">
                                                            <?php echo esc_html(wp_strip_all_tags((string) $stickycta->SSuprydp_content_option_text)); ?>
                                                        </div>
                                                        <a class="ess-preview-banner-link btn-banner" id="ess-preview-banner-link" href="#">
                                                            <?php echo esc_html($stickycta->SSuprydp_action_option_text ? $stickycta->SSuprydp_action_option_text : __('Learn More', 'easy-sticky-sidebar')); ?>
                                                        </a>
                                                        <span style="background-color: <?php echo esc_attr($close_button_color); ?>; <?php echo esc_attr($close_button_style); ?>" class="btn-ess-close icon-close <?php echo esc_attr($close_button_position); ?> <?php echo esc_attr($close_button_edge); ?>"></span>
                                                    </div>
                                                </div>

                                                <div class="ess-preview-template ess-preview-gdpr<?php echo $current_template === 'gdpr' ? ' is-active' : ''; ?>" data-template="gdpr">
                                                    <div class="easy-sticky-sidebar wordpress-cta-pro-gdpr ess-preview-gdpr" id="ess-preview-gdpr">
                                                        <div class="gdpr-content ess-preview-gdpr-text" id="ess-preview-gdpr-text">
                                                            <?php echo esc_html(wp_strip_all_tags((string) $stickycta->SSuprydp_content_option_text)); ?>
                                                        </div>
                                                        <div class="gdpr-footer ess-preview-gdpr-actions">
                                                            <a class="ess-preview-gdpr-button btn-gdpr-close" id="ess-preview-gdpr-accept" href="#">
                                                                <?php echo esc_html($stickycta->SSuprydp_button_option_text ? $stickycta->SSuprydp_button_option_text : __('Got it.', 'easy-sticky-sidebar')); ?>
                                                            </a>
                                                            <a class="ess-preview-gdpr-button ess-preview-gdpr-decline btn-gdpr-decline" id="ess-preview-gdpr-decline" href="#">
                                                                <?php echo esc_html($stickycta->button2_text ? $stickycta->button2_text : __('Decline', 'easy-sticky-sidebar')); ?>
                                                            </a>
                                                        </div>
                                                        <span style="background-color: <?php echo esc_attr($close_button_color); ?>; <?php echo esc_attr($close_button_style); ?>" class="btn-ess-close icon-close <?php echo esc_attr($close_button_position); ?> <?php echo esc_attr($close_button_edge); ?>"></span>
                                                    </div>
                                                </div>

                                                <div class="ess-preview-template ess-preview-html<?php echo $current_template === 'html' ? ' is-active' : ''; ?>" data-template="html">
                                                    <div class="easy-sticky-sidebar sticky-cta ess-preview-static ess-preview-html-cta sticky-cta-position-<?php echo esc_attr($preview_position); ?><?php echo $show_close_button ? ' ess-close-button-' . esc_attr($close_button_position) : ''; ?>" id="ess-preview-html-cta">
                                                        <div class="sticky-sidebar-button" id="ess-preview-html-button">
                                                            <div id="ess-preview-html-button-text">
                                                                <?php echo esc_html($stickycta->SSuprydp_button_option_text ? $stickycta->SSuprydp_button_option_text : __('Click Here', 'easy-sticky-sidebar')); ?>
                                                            </div>
                                                        </div>
                                                        <div class="sticky-sidebar-content sticky-sidebar-container">
                                                            <div class="sticky-sidebar-text sticky-content-inner" id="ess-preview-html-content-text">
                                                                <?php echo esc_html(wp_strip_all_tags((string) $stickycta->SSuprydp_content_option_text)); ?>
                                                            </div>
                                                        </div>
                                                        <span style="background-color: <?php echo esc_attr($close_button_color); ?>; <?php echo esc_attr($close_button_style); ?>" class="btn-ess-close icon-close <?php echo esc_attr($close_button_position); ?> <?php echo esc_attr($close_button_edge); ?>"></span>
                                                    </div>
                                                </div>

                                                <div class="ess-preview-template ess-preview-floating<?php echo $current_template === 'floating-buttons' ? ' is-active' : ''; ?>" data-template="floating-buttons">
                                                    <?php
                                                    $preview_buttons = Easy_Sticky_Sidebar_Floating_Buttons::get_buttons($stickycta);
                                                    if (empty($preview_buttons)) {
                                                        $preview_buttons = [
                                                            (object) ['icon' => 'fa-solid fa-phone', 'text' => __('Call Us', 'easy-sticky-sidebar'), 'url' => ''],
                                                            (object) ['icon' => 'fa-solid fa-comment-dots', 'text' => __('Chat Now', 'easy-sticky-sidebar'), 'url' => ''],
                                                        ];
                                                    }

                                                    $preview_has_text = array_filter($preview_buttons, function ($button) {
                                                        return !empty(trim((string) ($button->text ?? '')));
                                                    });
                                                    $preview_hide_text = ($stickycta->hide_floating_button_text ?? '') === 'yes' || empty($preview_has_text);
                                                    $preview_floating_classes = [
                                                        'easy-sticky-sidebar',
                                                        'ess-preview-static',
                                                        'ess-preview-floating-buttons',
                                                        'ess-floating-buttons',
                                                        'sticky-cta-position-' . $preview_position
                                                    ];
                                                    if ($preview_hide_text) {
                                                        $preview_floating_classes[] = 'floating-button-no-text';
                                                    }
                                                    ?>
                                                    <div class="<?php echo esc_attr(implode(' ', $preview_floating_classes)); ?>" id="ess-preview-floating-cta">
                                                        <ul class="floating-buttons-container ess-preview-floating-list" id="ess-preview-floating-list">
                                                        <?php
                                                        foreach ($preview_buttons as $key => $button) :
                                                            $button_icon = !empty($button->icon) ? sprintf('<i class="icon %s"></i>', esc_attr($button->icon)) : '';
                                                            $button_text = (!$preview_hide_text && isset($button->text)) ? esc_html($button->text) : '';
                                                            $button_html = trim($button_icon . $button_text);
                                                            if ($button_html === '') {
                                                                $button_html = esc_html__('Button', 'easy-sticky-sidebar');
                                                            }
                                                            $has_link = !empty($button->url);
                                                            $button_class = $has_link ? 'has-link' : '';
                                                        ?>
                                                            <li class="floating-button-<?php echo esc_attr($key); ?> <?php echo esc_attr($button_class); ?>">
                                                                <?php
                                                                if ($has_link) {
                                                                    printf('<a href="%s">%s</a>', esc_url($button->url), wp_kses_post($button_html));
                                                                } else {
                                                                    echo wp_kses_post($button_html);
                                                                }
                                                                ?>
                                                            </li>
                                                        <?php endforeach; ?>
                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </section>

                                <section class="ess-card ess-steps-card">
                                    <nav class="nav-tab-wrapper sticky-sidebar-nav-tab-wrapper">
                                        <?php
                                        $tab_number = 1;
                                        foreach ($tabs as $key => $tab) {
                                            $tab_active = ('sticky-sidebar-' . $key === $editor_current_tab) ? 'nav-tab-active' : '';
                                            $tab_icon = !empty($tab['icon']) ? sprintf('<i class="%s"></i>', esc_attr($tab['icon'])) : '';
                                            printf(
                                                '<a href="#sticky-sidebar-%1$s" class="nav-tab nav-tab-%1$s %2$s" data-step="%3$d"><span class="ess-step-label">%4$s%5$s</span><span class="cta-chevron"></span></a>', // CUSTOM STICKY NAV: chevron element
                                                esc_attr($key),
                                                esc_attr($tab_active),
                                                absint($tab_number),
                                                wp_kses($tab_icon, array('i' => array('class' => array()))),
                                                esc_html($tab['label'])
                                            );
                                            $tab_number++;
                                        }
                                        ?>
                                    </nav>
                                </section>

                                <div class="sticky-sidebar-tab-content">
                                    <?php
                                    foreach ($tabs as $key => $tab) {
                                        $tab_display = ('sticky-sidebar-' . $key === $editor_current_tab) ? 'display:block' : '';
                                        printf('<div id="sticky-sidebar-%s" class="tab-content" style="%s">', esc_attr($key), esc_attr($tab_display));
                                        call_user_func_array($tab['callback'], [$stickycta]);
                                        echo '</div>';
                                    }
                                    ?>
                                </div>
                            </div>

                            <aside class="ess-side-column">
                                <div class="ess-card ess-save-card">
                                    <h2 class="wordpress-cta-heading"><?php esc_html_e('Publish', 'easy-sticky-sidebar'); ?><span class="status"></span></h2>
                                    <div class="ess-publish-status">
                                        <?php easy_sticky_sidebar_get_status_menu($stickycta); ?>
                                    </div>
                                    <div class="SSuprydp_btn_save">   
                                        <input type="submit"
                                            onclick="return SSuprydp_Admin.ProcessPageData(event, this);"
                                            class="button_save"
                                            value="<?php esc_attr_e('Save', 'easy-sticky-sidebar'); ?>"
                                            >
                                    </div>
                                    <p class="wordpress-cta-instruction ess-publish-help">
                                        <?php
                                        echo wp_kses_post(
                                            __(
                                                '<strong>Change the status of your CTA.</strong><br><strong>Live:</strong> This will show to everyone.<br><strong>Development:</strong> This will only show to admins who are logged in.<br><strong>Off:</strong> Will not show to anyone.',
                                                'easy-sticky-sidebar'
                                            )
                                        );
                                        ?>
                                    </p>
                                </div>

                                <?php
                                $has_masked_hook_stats = !empty($before_tab_content) && strpos($before_tab_content, 'wordpress-cta-pro-feature-lock-inline') !== false;
                                if (!empty($before_tab_content) && !$has_masked_hook_stats) :
                                ?>
                                    <div class="ess-card ess-stat-card ess-hooked-stats">
                                        <?php echo $before_tab_content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                                    </div>
                                <?php else : ?>
                                    <div class="ess-card ess-stat-card">
                                        <h3><?php esc_html_e('CTA Stats', 'easy-sticky-sidebar'); ?></h3>
                                        <ul>
                                            <li><span><?php esc_html_e('Impressions', 'easy-sticky-sidebar'); ?></span><strong><?php echo esc_html(absint($stickycta->SSuprydp_impressions)); ?></strong></li>
                                            <li><span><?php esc_html_e('Clicks', 'easy-sticky-sidebar'); ?></span><strong><?php echo esc_html(absint($stickycta->SSuprydp_clicks)); ?></strong></li>
                                            <li><span><?php esc_html_e('CTR', 'easy-sticky-sidebar'); ?></span><strong><?php echo esc_html($stickycta->get_ctr()); ?></strong></li>
                                            <li><span><?php esc_html_e('Template', 'easy-sticky-sidebar'); ?></span><strong class="ess-template-label"><?php echo esc_html($current_template_label); ?></strong></li>
                                        </ul>
                                    </div>
                                <?php endif; ?>
                            </aside>
                        </div>

                        <div class="SSuprydp_field_wrap" id="SSuprydp_modal_msg" style="display:none;">
                            <div class="SSuprydp_modal_content"></div>
                        </div>
                    </div>
                </form>
            </div>

            <?php if (!easy_sticky_sidebar_has_pro()) : ?>
                <div class="wordpress-cta-advertisement">
                    <span class="div-two">
                        <a href="https://wpctapro.com/" target="_blank"><img src="<?php echo esc_url(EASY_STICKY_SIDEBAR_PLUGIN_URL . '/assets/img/ads.jpeg'); ?>" alt="WP CTA Pro"></a>
                    </span>
                    <span class="div-two">
                        <a href="https://wordpress.org/plugins/ez-countdown-timer/" target="_blank"><img src="<?php echo esc_url(EASY_STICKY_SIDEBAR_PLUGIN_URL . '/assets/img/ezcountdowntimer.jpg'); ?>" alt="Alpha Link SEO"></a>
                    </span>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<div id="easy-sticky-sidebar-toast"><?php esc_html_e('Clear Your Cache', 'easy-sticky-sidebar'); ?></div>

<script type='text/javascript'>
jQuery(document).ready(function($) {
    var file_frame;

    jQuery('#upload_image_button').on('click', function(event) {
        event.preventDefault();

        if (file_frame) {
            file_frame.open();
            return;
        }

        file_frame = wp.media({
            title: 'Select a image to upload',
            button: {
                text: 'Use this image'
            },
            multiple: false
        });

        file_frame.on('select', function() {
            var attachment = file_frame.state().get('selection').first().toJSON();
            $('#image-preview').attr('src', attachment.url).css('width', 'auto');
            $('#image_attachment_id').val(attachment.id);
            $('#sticky_s_media').val(attachment.url).trigger('change');
        });

        file_frame.open();
    });
});
</script>
