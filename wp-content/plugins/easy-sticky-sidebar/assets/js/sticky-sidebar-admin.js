/*
 *
 * neon maker backend end Javascript
 *
 * @since 1.0.0
 *
 */

const has_easy_sticky_sidebar_pro = function () {
    return wp.hooks.applyFilters('easy_sticky_sidebar_pro', false);
}

jQuery(document).ready(function ($) {
    $(".easy-sidebar-wrap .sticky-sidebar-name-input + i").on("click", function () {
        $(this).prev().focus();
    });
});

var SSuprydp_Admin;
(function ($) {
    var $this, $total_results, $offset, $limit, $processed;
    SSuprydp_Admin = {
        settings: {},
        initilaize: function () {
            $this = SSuprydp_Admin;
            $(document).ready(function () {
                $offset = $processed = 0;
                $limit = 10;
                $this.onInitMethods();
            });
        },
        onInitMethods: function () { },
        ProcessPageData: function (event, elem) {
            event.preventDefault();
            jQuery(".ssuprydp_load").show();
            $(".upb_error").remove();

            $(elem).find('input[type="submit"]').attr("disabled", true);
            $this.postFormData(
                ajaxurl + "?action=easy_sticky_sidebar_process_pages",
                "#SSuprydp_form",
                function (response) {
                    if (response.status == "success") {
                        jQuery(".ssuprydp_load p").text(response.message);

                        redirect = "";
                        try {
                            redirect = new URL(response.redirect);
                        } catch (_) {
                            redirect = false;
                        }

                        if (redirect !== false) {
                            window.location.replace(response.redirect);
                        }

                        setTimeout(function () {
                            jQuery(".ssuprydp_load p").text("Loading.....");
                            jQuery(".ssuprydp_load").hide();
                            $('#easy-sticky-sidebar-toast').addClass('shown')

                            setTimeout(() => {
                                $('#easy-sticky-sidebar-toast').removeClass('shown')
                            }, 1000)
                        }, 1500);

                    } else {
                        jQuery(".ssuprydp_load p").text(response.message);
                        setTimeout(function () {
                            jQuery(".ssuprydp_load p").text("Loading.....");
                            jQuery(".ssuprydp_load").hide();
                        }, 1500);
                    }
                }
            );
        },
        displayModal: function (response, sizeClass) {
            if (!sizeClass) {
                sizeClass = "modal-normal";
            }
            if (response.header) {
                $("#SSuprydp_modal .SSuprydp_modal_heading")
                    .html(response.header)
                    .show();
            } else {
                $("#SSuprydp_modal .SSuprydp_modal_heading").hide();
            }
            if (response.content) {
                $("#SSuprydp_modal_msg").show();
                $("#SSuprydp_modal_msg .SSuprydp_modal_content").html(response.content);
            } else {
                $("#SSuprydp_modal_msg").hide();
            }
            if (response.footer) {
                $("#SSuprydp_modal .SSuprydp_modal_footer")
                    .html(response.footer)
                    .show();
            } else {
                $("#SSuprydp_modal .SSuprydp_modal_footer").hide();
            }
            $("#SSuprydp_modal")
                .removeAttr("class")
                .addClass("upb_overlay " + sizeClass)
                .show();
        },
        postFormData: function (url, form, callback) {
            var formNode = $(form)[0];
            var formData = new FormData(formNode);

            // Keep one deterministic button_icon value to avoid duplicate-field drift.
            if (formNode) {
                const selectedTemplatePicker = formNode.querySelector('input[name="sidebar_template_picker"]:checked');
                if (selectedTemplatePicker && selectedTemplatePicker.value) {
                    formData.set('sidebar_template', selectedTemplatePicker.value);
                    formData.set('sidebar_template_user_selection', selectedTemplatePicker.value);
                }

                const iconValues = Array.from(formNode.querySelectorAll('input[name="button_icon"]'))
                    .map((input) => `${input.value || ''}`.trim());
                const hasButtonIconField = iconValues.length > 0;
                const normalizedIcon = iconValues.slice().reverse().find((value) => value.length > 0) || '';
                if (hasButtonIconField) {
                    formData.delete('button_icon');
                    formData.append('button_icon', normalizedIcon);
                }
            }

            $.ajax({
                url: url, // server url
                type: "POST", //POST or GET
                data: formData, // data to send in ajax format or querystring format
                datatype: "json",
                beforeSend: function (xhr) { },
                success: function (data) {
                    callback(data); // return data in callback
                },

                complete: function () { },

                error: function (xhr, status, error) { },
                cache: false,
                contentType: false,
                processData: false,
            });
        },
    };
    SSuprydp_Admin.initilaize();
})(jQuery);

jQuery(function () {
    jQuery("#SSuprydp_button_option_font").fontselect();
    jQuery("#SSuprydp_content_option_font").fontselect();
    jQuery("#SSuprydp_action_option_font").fontselect();
});

jQuery(document).on("click", ".SSuprydp_dropdowm_list a", function () {
    var getfont = jQuery(this).find("i").attr("class");
    jQuery("#SSuprydp_awesome_font").val(getfont);
    jQuery("#SSuprydp_display_font").html('<i class="' + getfont + '"></i> ' + getfont);
});


jQuery(document).ready(function ($) {
    jQuery(".field-font-family").fontselect();

    $('.easy-sticky-sidebar-tab-panel > .tab-nav a').on('click', function (e) {
        e.preventDefault();
        href = $(this).attr('href');

        tab_panel = $(this).closest('.easy-sticky-sidebar-tab-panel');
        tab_navs = $(this).parent().children();

        tab_items = tab_panel.children('.easy-sticky-sidebar-tab-content').children();

        next_tab = tab_items.filter(href);

        if (!next_tab.length) {
            return;
        }

        if (typeof $.cookie === 'function') {
            $.cookie('easy-sticky-sidebar-import-export', href);
        }

        tab_navs.removeClass('active');
        $(this).addClass('active');

        tab_items.not(next_tab).hide();
        next_tab.show();
    })

    if (typeof $.cookie === 'function') {
        export_tab_item = $.cookie('easy-sticky-sidebar-import-export');
        if (export_tab_item) {
            $(`.easy-sticky-sidebar-tab-panel > .tab-nav a[href="${export_tab_item}"]`).trigger('click')
        }
    }

    $('ul.export-cta-list [data-select="all"]').on('change', function () {
        checked = $(this).is(':checked');
        $('ul.export-cta-list input[type="checkbox"]').not($(this)).prop('checked', checked)
    });

    const isStylingSectionRelevantForTemplate = function ($section, template) {
        const currentTemplate = (template || 'sticky-cta').toString();
        const sectionId = ($section.attr('id') || '').toString();
        const sectionIdLower = sectionId.toLowerCase();
        const sectionTitle = (($section.children('summary').first().text() || '').toString()).toLowerCase();
        const hiddenForTabTemplate = [
            'sticky-cta-banner-image',
            'cta-adjustment-options',
            'cta-content-options',
            'cta-line-separator-options',
            'cta-link-text-options'
        ];
        const hiddenForBannerTemplate = [
            'sticky-cta-banner-image',
            'sticky-sidebar-button-options',
            'cta-adjustment-options'
        ];
        const hiddenForHtmlTemplate = [
            'cta-link-text-options',
            'sticky-cta-banner-image'
        ];
        const hiddenForGdprTemplate = [
            'cta-link-text-options',
            'sticky-cta-banner-image'
        ];
        const visibleForGdprTemplate = [
            'cta-content-options',
            'sticky-sidebar-button-options',
            'cta-close-button-options',
            'cta-box-shadow-options',
            'cta-animation-behaviour-options'
        ];

        if (currentTemplate === 'tab-cta' && hiddenForTabTemplate.includes(sectionId)) {
            return false;
        }
        if (currentTemplate === 'banner' && hiddenForBannerTemplate.includes(sectionId)) {
            return false;
        }
        if (currentTemplate === 'html' && hiddenForHtmlTemplate.includes(sectionId)) {
            return false;
        }
        if (currentTemplate === 'gdpr' && hiddenForGdprTemplate.includes(sectionId)) {
            return false;
        }
        if (currentTemplate === 'gdpr') {
            return visibleForGdprTemplate.includes(sectionId);
        }
        if (currentTemplate === 'banner') {
            if (
                sectionIdLower.includes('sticky-sidebar-button-options') ||
                sectionIdLower.includes('sticky-cta-banner-image') ||
                sectionTitle.includes('cta tab options') ||
                sectionTitle.includes('cta image options')
            ) {
                return false;
            }
        }

        if (sectionId === 'cta-line-separator-options') {
            if (currentTemplate !== 'sticky-cta') {
                return false;
            }

            const imagePlacement = ($('[name="image_placement"]').val() || 'classic').toString().toLowerCase();
            if (imagePlacement === 'overlay') {
                return false;
            }
        }

        if ($section.hasClass('html-cta-option')) {
            return currentTemplate === 'html';
        }

        if ($section.hasClass('floating-buttons-options') || sectionId === 'section-floating-button-style-options') {
            return currentTemplate === 'floating-buttons';
        }

        if (sectionId === 'cta-animation-behaviour-options') {
            return true;
        }

        if (sectionId === 'global-style-tab') {
            return currentTemplate === 'floating-buttons';
        }

        if (currentTemplate === 'floating-buttons') {
            return false;
        }

        if ($section.hasClass('sticky-cta-option')) {
            return currentTemplate === 'sticky-cta';
        }

        return true;
    };

    const getCurrentSidebarTemplate = function () {
        const selectedTemplate = $('#ess-template-selector input[name="sidebar_template_picker"]:checked:not(:disabled)').first().val();
        return selectedTemplate || $('[name="sidebar_template"]').val() || 'sticky-cta';
    };

    const refreshStylingSectionVisibility = function () {
        const currentTemplate = getCurrentSidebarTemplate();
        const $templateField = $('[name="sidebar_template"]').first();

        if ($templateField.length && $templateField.val() !== currentTemplate) {
            $templateField.val(currentTemplate);
        }
        $('#sidebar_template_user_selection').val(currentTemplate);
        $('#SSuprydp_form').attr('data-template', currentTemplate);

        $('.wordpress-cta-styling-container').each(function () {
            const $container = $(this);
            const $panel = $container.children('.ess-settings-tabs').first();
            const $nav = $panel.children('.ess-settings-tabs-nav').first();
            const $content = $panel.children('.ess-settings-tabs-content').first();
            let firstVisibleTarget = '';

            if (!$panel.length || !$nav.length || !$content.length) {
                return;
            }

            $content.children('.ess-settings-pane').each(function () {
                const $pane = $(this);
                const paneId = ($pane.attr('id') || '').toString();
                const $section = $pane.children('.easy-sticky-sidebar-fieldset').first();
                const $button = $nav.children('.ess-settings-tab[data-target="' + paneId + '"]');
                const isVisible = isStylingSectionRelevantForTemplate($section, currentTemplate);

                $button.toggle(isVisible);
                $pane.toggle(isVisible);
                $section.find('input, select, textarea, button').prop('disabled', !isVisible);

                if (!firstVisibleTarget && isVisible) {
                    firstVisibleTarget = paneId;
                }
            });

            const $activeButton = $nav.children('.ess-settings-tab.active:visible').first();
            const activeTarget = ($activeButton.attr('data-target') || '').toString();
            if (!activeTarget || !$content.children('#' + activeTarget).is(':visible')) {
                if (firstVisibleTarget) {
                    $nav.children('.ess-settings-tab[data-target="' + firstVisibleTarget + '"]').trigger('click');
                }
            }
        });
    };
    window.easyStickySidebarRefreshStylingSections = refreshStylingSectionVisibility;

    $(document).on('click', '.ess-settings-tabs-nav .ess-settings-tab', function () {
        const $button = $(this);
        const targetId = ($button.attr('data-target') || '').toString();
        if (!targetId) {
            return;
        }

        const $nav = $button.closest('.ess-settings-tabs-nav');
        const $panel = $button.closest('.ess-settings-tabs');
        const $content = $panel.children('.ess-settings-tabs-content').first();

        $nav.children('.ess-settings-tab').removeClass('active').attr('aria-selected', 'false');
        $button.addClass('active').attr('aria-selected', 'true');
        $content.children('.ess-settings-pane').removeClass('active').hide();
        $content.children('#' + targetId).addClass('active').show();
    });

    const syncBehaviourConditionalFields = function () {
        const $behaviourPanel = $('#cta-animation-behaviour-options');
        if (!$behaviourPanel.length) {
            return;
        }

        const displayTrigger = (($behaviourPanel.find('[name="display_trigger"]').first().val() || 'immediately') + '').toLowerCase();
        $behaviourPanel.find('.ess-display-trigger-seconds').toggle(displayTrigger === 'after_seconds');
        $behaviourPanel.find('.ess-display-trigger-scroll').toggle(displayTrigger === 'after_scroll');

        const hideBehavior = (($behaviourPanel.find('[name="hide_behavior"]').first().val() || 'none') + '').toLowerCase();
        $behaviourPanel.find('.ess-hide-after-seconds').toggle(hideBehavior === 'after_seconds');

        const afterCloseBehavior = (($behaviourPanel.find('[name="after_close_behavior"]').first().val() || 'next_visit') + '').toLowerCase();
        $behaviourPanel.find('.ess-after-close-time').toggle(afterCloseBehavior === 'hide_for_time');
    };

    $('.wordpress-cta-styling-container').removeClass('ess-settings-tabs-pending').addClass('ess-settings-tabs-enabled');
    refreshStylingSectionVisibility();
    syncBehaviourConditionalFields();
    $(document).on('change', '[name="sidebar_template"], [name="sidebar_template_picker"], [name="image_placement"], [name="overlay_position"], [name="sticky_layout"]', function () {
        refreshStylingSectionVisibility();
    });
    $(document).on('change', '#cta-animation-behaviour-options [name="display_trigger"], #cta-animation-behaviour-options [name="hide_behavior"], #cta-animation-behaviour-options [name="after_close_behavior"]', function () {
        syncBehaviourConditionalFields();
    });


    $('[data-toggle="tooltip"]').tooltip()

    $('ul#adminmenu .wp-submenu a[href="https://wpctapro.com/help"]').attr("target", "_blank");

    $(".nav-tab-wrapper.sticky-sidebar-nav-tab-wrapper .nav-tab").on("click", function (e) {
        e.preventDefault();
        target = $(this).attr("href");

        tab_contents = $(".sticky-sidebar-tab-content .tab-content");

        current = tab_contents.filter(target);
        if (!current.length) {
            return;
        }

        $('#SSuprydp_form [name="cta_editor_current_tab"]').val(target.replace('#', ''));

        //window.location.hash = target;

        $(".nav-tab-wrapper.sticky-sidebar-nav-tab-wrapper .nav-tab").not($(this)).removeClass("nav-tab-active");
        $(this).addClass("nav-tab-active");

        tab_contents.not(current).hide();
        current.show();
        return false;
    });

    window.EasyStickySidebar = {
        options: {
            template: 'sticky-cta'
        },

        /** When true, form field events skip live preview (bulk design-template apply). */
        previewSuspended: false,

        /** Assigned by sticky CTA editor preview script; runs one refresh after template JSON is applied. */
        applyTemplatePreviewRefresh: null,

        set_state: function (key, value) {
            this.options[key] = value;
            wp.hooks.doAction('easy_sticky_sidebar_updated', this);
        },

        update_close_button_position: function () {
            current_template = $('[name="sidebar_template"]').val(), cta_position = $('[name="SSuprydp_cta_position"]').val();
            this.set_state('SSuprydp_cta_position', cta_position);

            positions = { start: "Top", end: "Bottom" };
            if (cta_position == "top" || cta_position == "bottom") {
                positions = { start: 'Left', end: 'Right' }
            }

            if (current_template == 'gdpr') {
                positions = { 'top-left': 'Top Left', 'top-right': 'Top Right', 'bottom-left': 'Bottom Left', 'bottom-right': 'Bottom Right' }
            } else if (current_template == 'banner') {
                positions = { left: 'Left', right: 'Right' }
            }

            positions = wp.hooks.applyFilters('easy_sticky_sidebar_close_button_positions', positions);
            let current_position = $('[name="close_button_position"]').attr('data-position');
            if (current_template == 'banner') {
                if (current_position === 'start') {
                    current_position = 'left';
                } else if (current_position === 'end') {
                    current_position = 'right';
                }
            }
            if (!Object.prototype.hasOwnProperty.call(positions, current_position)) {
                current_position = Object.keys(positions)[0] || current_position;
            }

            const options = Object.keys(positions).map((key) => {
                const selected_attr = key == current_position ? "selected" : "";
                return `<option value="${key}" ${selected_attr}>${positions[key]}</option>`;
            });

            $('[name="close_button_position"]').html(options.join(""));
            $('[name="close_button_position"]').attr('data-position', current_position);
        },

        init: function () {
            wp.hooks.doAction('easy_sticky_sidebar_init', this);
        }
    }

    $('.wp-list-table .column-action a.cta-delete').on('click', function (e) {
        response = confirm('Do you want to delete this item?');
        if (!response) {
            return e.preventDefault();
        }
    });

    let sidebar_name_timer = null;

    $(".wrap-easy-sticky-sidebar table.wp-list-table .sticky-sidebar-name-input").on("keydown", function (e) {
        if (sidebar_name_timer) {
            clearTimeout(sidebar_name_timer);
        }

        sidebar_name_timer = setTimeout(() => {
            jQuery.post(sticky_sidebar.ajax_url, {
                action: "easy_sticky_sidebar_change_name",
                sticky: $(this).data("sticky"),
                name: $(this).val(),
                nonce: sticky_sidebar.nonce,
            });
        }, 300)

        if (e.keyCode == 13) {
            $(this).blur();
            return false;
        }
    });

    $(".sticky-sidebar-colorpicker").wpColorPicker({
        change: function (event) {
            setTimeout(() => {
                $(event.target).trigger('change')
            }, 300)
        }
    });

    $(".sticky-cta-status-menu ul.statuses li").on("click", function () {
        status_item = $(this);
        menu_container = status_item.closest(".sticky-cta-status-menu");

        menu_container.find('[name="SSuprydp_development"]').val(status_item.data("status"));

        form = $('#SSuprydp_form');

        if (form.length) {
            form.attr('data-status', status_item.data("status"));
        }

        label = menu_container.children("label");
        const sticky_id = menu_container.data("id");

        if (sticky_id == 0) {
            label.html(status_item.html());
            label.attr("class", "status-" + status_item.data("status"));
            return;
        }

        const data = {
            action: "easy_sticky_sidebar_update_status",
            sticky_id,
            status: status_item.data("status"),
            nonce: sticky_sidebar.nonce,
        };

        $.post(sticky_sidebar.ajax_url, data, function (response) {
            if (response.success == false) {
                return alert(response.error);
            }

            label.html(status_item.html());
            label.attr("class", "status-" + status_item.data("status"));
        });
    });

    var current_tab = "#sticky-sidebar-template";
    var $current_tab = $('.nav-tab-wrapper.sticky-sidebar-nav-tab-wrapper .nav-tab[href="' + current_tab + '"]');
    if ($current_tab.length) {
        $current_tab.trigger("click");
    } else {
        $('.nav-tab-wrapper.sticky-sidebar-nav-tab-wrapper .nav-tab').first().trigger("click");
    }

    var CTA_Button_Options_HTML = ($('#sticky-sidebar-button-options').attr('data-tab-label') || '').toString();
    const setColorFieldValue = function ($field, value) {
        if (!$field || !$field.length) {
            return;
        }
        if ($field.hasClass('wp-color-picker')) {
            try {
                $field.wpColorPicker('color', value);
                return;
            } catch (e) {
                // fallback below
            }
        }
        $field.val(value).trigger('input').trigger('change');
    };

    // Keep template select in sync with thumbnail selection before any preview/update triggers.
    const selectedTemplateCardOnLoad = $('#ess-template-selector input[name="sidebar_template_picker"]:checked:not(:disabled)').first();
    if (selectedTemplateCardOnLoad.length) {
        $('[name="sidebar_template"]').val(selectedTemplateCardOnLoad.val());
        $('#sidebar_template_user_selection').val(selectedTemplateCardOnLoad.val());
    }


    const positon_options = $('[name="SSuprydp_cta_position"]').html();
    const setButtonOptionsHeadingByTemplate = function (template) {
        const currentTemplate = (template || $('[name="sidebar_template"]').val() || 'sticky-cta').toString();
        let heading = CTA_Button_Options_HTML;
        if (currentTemplate === 'tab-cta') {
            heading = 'Tab Button Options';
        } else if (currentTemplate === 'gdpr') {
            heading = 'Button Options';
        }

        $('#sticky-sidebar-button-options').attr('data-tab-label', heading);
        $('.ess-settings-pane').each(function () {
            const $pane = $(this);
            if (!$pane.find('#sticky-sidebar-button-options').length) {
                return;
            }

            const paneId = ($pane.attr('id') || '').toString();
            const $tab = $('.ess-settings-tab[data-target="' + paneId + '"]');
            if (!$tab.length) {
                return;
            }

            const proPillHtml = $tab.find('.ess-pro-pill').length ? ' <span class="ess-pro-pill">PRO</span>' : '';
            $tab.html(heading + proPillHtml);
        });

    };

    const toggleHtmlTemplateContentOptions = function (template) {
        const currentTemplate = (template || $('[name="sidebar_template"]').val() || 'sticky-cta').toString();
        const isHtmlTemplate = currentTemplate === 'html';
        const isGdprTemplate = currentTemplate === 'gdpr';
        const isBannerTemplate = currentTemplate === 'banner';

        // Content tab: link/button URL options are not relevant for HTML/iframe and GDPR CTA.
        const contentLinkOptions = $('#sticky-cta-content-link-options-container');
        const showContentLinkOptions = !isHtmlTemplate && !isGdprTemplate;
        contentLinkOptions.toggle(showContentLinkOptions);
        contentLinkOptions.find('input, select, textarea, button').prop('disabled', !showContentLinkOptions);

        // Hide font-size controls for HTML/iframe template.
        const htmlHiddenControls = $('.hide-on-html-template');
        htmlHiddenControls.toggle(!isHtmlTemplate);
        htmlHiddenControls.find('input, select, textarea, button').prop('disabled', isHtmlTemplate);

        // Styling tab: hide CTA Link Text section for GDPR.
        const ctaLinkTextSection = $('#cta-link-text-options');
        if (ctaLinkTextSection.length) {
            if (isGdprTemplate) {
                ctaLinkTextSection.hide();
            } else {
                ctaLinkTextSection.css('display', 'grid');
            }
            ctaLinkTextSection.find('input, select, textarea, button').prop('disabled', isGdprTemplate);
        }

        // Styling tab: hide CTA Image Options section for HTML/GDPR/Banner.
        const ctaImageSection = $('#sticky-cta-banner-image');
        if (ctaImageSection.length) {
            const showImageOptions = !isHtmlTemplate && !isGdprTemplate && !isBannerTemplate;
            ctaImageSection.toggle(showImageOptions);
            ctaImageSection.find('input, select, textarea, button').prop('disabled', !showImageOptions);
        }

        // Content settings: GDPR-only fields should not appear in Sticky CTA or other templates.
        const gdprOnlyFields = $('#cta-content-options .wordpress-cta-gdpr-only');
        if (gdprOnlyFields.length) {
            gdprOnlyFields.toggle(isGdprTemplate);
            gdprOnlyFields.find('input, select, textarea, button').prop('disabled', !isGdprTemplate);
        }

        // "Turn the text into a button" should be available only for Announcement Banner.
        const bannerOnlyFields = $('#cta-link-text-options .ess-banner-only-field');
        if (bannerOnlyFields.length) {
            bannerOnlyFields.toggle(isBannerTemplate);
            bannerOnlyFields.find('input, select, textarea, button').prop('disabled', !isBannerTemplate);
        }

        const applyBannerButtonFieldLayout = function () {
            const section = $('#cta-link-text-options');
            if (!section.length) {
                return;
            }

            const letterSpacingField = section.find('.call-to-action-letter-spacing').first();
            const fontFamilyField = section.find('.call-to-action-font').first();
            const fontSizeField = section.find('.call-to-action-font-size').first();
            const textColorField = section.find('.call-to-action-textcolor').first();
            const toggleField = section.find('.call-to-action-button.ess-banner-only-field').first();
            const bgColorField = section.find('.call-to-action-background-color').first();
            const bannerPaddingField = section.find('[name="banner_button_padding[top]"]').first().closest('.SSuprydp_field_wrap');
            const bannerRadiusField = section.find('[name="banner_button_border_radius"]').first().closest('.SSuprydp_field_wrap');
            const bannerMarginField = section.find('[name="banner_button_margin[top]"]').first().closest('.SSuprydp_field_wrap');

            if (isBannerTemplate) {
                // Keep typography grouped right after letter-spacing for banner.
                if (letterSpacingField.length && fontFamilyField.length) {
                    fontFamilyField.insertAfter(letterSpacingField);
                }
                if (fontFamilyField.length && fontSizeField.length) {
                    fontSizeField.insertAfter(fontFamilyField);
                }
                if (fontSizeField.length && textColorField.length) {
                    textColorField.insertAfter(fontSizeField);
                }
                if (textColorField.length && bannerMarginField.length) {
                    bannerMarginField.insertAfter(textColorField);
                }

                // Place banner toggle right after margin to keep 2-column flow stable.
                if (toggleField.length) {
                    if (bannerMarginField.length) {
                        toggleField.insertAfter(bannerMarginField);
                    } else {
                        toggleField.appendTo(section);
                    }
                }
            }

            const bannerButtonEnabled = section.find('[name="call_to_action_button"]').is(':checked');
            const shouldShowButtonBgField = !isBannerTemplate || bannerButtonEnabled;
            if (bgColorField.length) {
                bgColorField.toggle(shouldShowButtonBgField);
                bgColorField.find('input, select, textarea, button').prop('disabled', !shouldShowButtonBgField);
            }

            const shouldShowBannerButtonOnlyFields = isBannerTemplate && bannerButtonEnabled;
            [bannerPaddingField, bannerRadiusField].forEach(function ($field) {
                if (!$field || !$field.length) {
                    return;
                }
                $field.toggle(shouldShowBannerButtonOnlyFields);
                $field.find('input, select, textarea, button').prop('disabled', !shouldShowBannerButtonOnlyFields);
            });
        };
        applyBannerButtonFieldLayout();

        setButtonOptionsHeadingByTemplate(currentTemplate);
    };

    $(document).on('change', '#cta-link-text-options [name="call_to_action_button"]', function () {
        toggleHtmlTemplateContentOptions($('[name="sidebar_template"]').val() || 'sticky-cta');
    });

    $('[name="sidebar_template"]').on("update", function (event, form_data) {
        form_data = form_data || {
            template: $('[name="sidebar_template"]').val() || 'sticky-cta'
        };

        if (form_data.template == 'banner') {
            $('#cta-content-options').show();
            $('#cta-link-text-options').css('display', 'grid');

            current_position = $('[name="SSuprydp_cta_position"]').data('position');
            options = ['Top', 'Bottom'].map((pos) => {
                selected = current_position == pos.toLowerCase() ? 'selected' : '';
                return `<option value="${pos.toLowerCase()}" ${selected}>${pos}</option>`;
            })

            $('[name="SSuprydp_cta_position"]').html(options.join(''))
        } else {
            $('[name="SSuprydp_cta_position"]').html(positon_options)
        }
        toggleHtmlTemplateContentOptions(form_data.template);
    })


    let sidebarTemplateInitialized = false;
    $('[name="sidebar_template"]').on("change", function () {
        const isInitialTemplateSync = !sidebarTemplateInitialized;
        sidebarTemplateInitialized = true;
        const form_data = Object.assign({}, {
            template: $(this).val()
        });

        EasyStickySidebar.set_state('template', $(this).val())

        $("#SSuprydp_form").attr("data-template", form_data.template);

        setButtonOptionsHeadingByTemplate(form_data.template);

        if (
            !isInitialTemplateSync &&
            !(window.EasyStickySidebar && window.EasyStickySidebar.isApplyingPresetTemplate) &&
            typeof window.easyStickySidebarApplyEditorDefaultsForTemplate === 'function'
        ) {
            window.easyStickySidebarApplyEditorDefaultsForTemplate(form_data.template, true);
        }

        $('[name="sidebar_template"]').trigger("update", form_data);

        $('#SSuprydp_form').trigger('update', form_data);

        $('.wordpress-cta-content-container > *').each(function (index) {
            order = (index + 1) * 2;
            $(this).css({ order })
        })

        refreshStylingSectionVisibility();
    }).trigger("change");

    $('[name="horizontal_vertical_position"]').on('change', function () {
        const current_position = $(this).val();
        $(this).attr('data-position', current_position)
        $('[name="horizontal_vertical_position_value"]').val(current_position);
        $('#cta-horizontal-vertical-position').attr('data-position', current_position);
        $('#cta-horizontal-vertical-position .position2_distance-wrapper > label').attr('data-position', current_position);
    }).trigger('change')

    $('[name="SSuprydp_button_option_align"]').on('change', function () {
        $(this).attr('data-align', $(this).val())
    })

    $('#SSuprydp_form [data-relative-fields]').on('change', function () {
        const value = $(this).data('relative-fields');
        if (!value) {
            return;
        }

        const relative_fields = $(value.replace(/,\s*$/, ""));
        if (!relative_fields.length) {
            return;
        }

        if ($(this).is(':checked')) {
            return relative_fields.show();
        }

        relative_fields.hide();
    }).trigger('change');


    const syncContentImageThumbnail = function (imageUrl) {
        const resolved = `${imageUrl || ''}`.trim();
        if (!resolved.length) {
            return;
        }

        const imageField = $('#sticky_s_media');
        const imagePreview = $('#image-preview');
        if (imageField.length) {
            imageField.val(resolved);
        }
        if (imagePreview.length) {
            imagePreview.attr('src', resolved).prop('src', resolved);
        }
    };

    const DesignTemplate = {
        template: null,
        templateKey: '',

        get_selected_template: function () {
            const selectedOption = $('#cta-premade-style').find(':selected');
            const templateKey = selectedOption.attr('data-design-template-key') || this.templateKey || '';
            if (
                templateKey &&
                window.sticky_sidebar &&
                window.sticky_sidebar.design_templates &&
                window.sticky_sidebar.design_templates[templateKey]
            ) {
                return Object.assign({}, window.sticky_sidebar.design_templates[templateKey]);
            }

            const rawTemplate = selectedOption.val() || this.template || '{}';
            try {
                const parsedTemplate = JSON.parse(rawTemplate);
                return parsedTemplate && typeof parsedTemplate === 'object' ? parsedTemplate : {};
            } catch (error) {
                return {};
            }
        },

        get_values: function (style_only = false) {
            let values = this.get_selected_template();

            if (style_only === false) {
                if (values && values.sidebar_template === 'sticky-cta' && typeof values.image_placement === 'undefined') {
                    values.image_placement = 'classic';
                }
                return values;
            }

            const remove_keys = [
                'SSuprydp_action_option_text', 'SSuprydp_button_option_text', 'SSuprydp_content_option_text', 'location_type', 'sticky_s_media',
                'button2_text', 'button2_url', 'image_attachment_id', 'tab_cta_url'
            ];
            remove_keys.forEach(key => delete values[key])
            return values;
        },

        init: function () {
            const self = this;

            $(document).off('change.essDesignTemplate', '#cta-premade-style').on('change.essDesignTemplate', '#cta-premade-style', function (e) {
                if (!$(this).val()) {
                    self.template = null;
                    self.templateKey = '';
                    return;
                }
                $('#wordpress-cta-popup-load-design').trigger('open');
                self.template = $(this).val();
                self.templateKey = $(this).find(':selected').attr('data-design-template-key') || '';
            });

            $(document).off('click.essDesignTemplateCancel', '#wordpress-cta-popup-load-design .btn-cancel').on('click.essDesignTemplateCancel', '#wordpress-cta-popup-load-design .btn-cancel', function (e) {
                e.preventDefault();
                $('#wordpress-cta-popup-load-design').trigger('close');
                self.template = null;
                self.templateKey = '';
                $('#cta-premade-style').val('');
            })

            $(document).off('click.essDesignTemplateApply', '#wordpress-cta-popup-load-design .btn-wordpress-cta-primary').on('click.essDesignTemplateApply', '#wordpress-cta-popup-load-design .btn-wordpress-cta-primary', function (e) {
                e.preventDefault();

                const style_only = $(this).attr('href') === '#load-style';
                const values = self.get_values(style_only);
                const formRoot = $('#SSuprydp_form');
                const ess = window.EasyStickySidebar;
                const previewCardEl = $('.ess-live-preview-card');
                const changedInputs = [];
                const setPreviewBusy = function (busy) {
                    if (!formRoot.length || !previewCardEl.length) {
                        return;
                    }
                    if (ess) {
                        ess.previewSuspended = !!busy;
                    }
                    previewCardEl.toggleClass('is-preview-loading', !!busy).attr('aria-busy', busy ? 'true' : 'false');
                };

                const findFieldsByName = function (name) {
                    return formRoot.find('input[name], select[name], textarea[name]').filter(function () {
                        return ($(this).attr('name') || '') === name;
                    });
                };

                const resetDimensionGroup = function (groupName) {
                    const groupFields = formRoot.find('input[name], select[name], textarea[name]').filter(function () {
                        const fieldName = `${$(this).attr('name') || ''}`;
                        return fieldName.indexOf(`${groupName}[`) === 0;
                    });
                    if (!groupFields.length) {
                        return;
                    }

                    groupFields.each(function () {
                        const field = $(this);
                        const fieldName = `${field.attr('name') || ''}`.toLowerCase();
                        if (fieldName.endsWith('[unit]')) {
                            field.val('px');
                        } else {
                            field.val('');
                        }
                        changedInputs.push(field);
                    });
                };

                try {
                    if (window.EasyStickySidebar) {
                        window.EasyStickySidebar.isApplyingPresetTemplate = true;
                    }
                    setPreviewBusy(true);

                    // Presets should start from a clean spacing state so stale values don't leak.
                    resetDimensionGroup('button_padding');
                    resetDimensionGroup('content_padding');

                    // Keep content visible when loading sticky classic presets.
                    const normalizedImagePlacement = `${values.image_placement || ''}`.toLowerCase();
                    const isClassicPreset = ['', 'classic', 'block'].includes(normalizedImagePlacement);
                    if (isClassicPreset) {
                        values.hide_content_text = 'no';
                    }

                    Object.keys(values).forEach((key) => {
                        let inputs = findFieldsByName(key);
                        const checkboxInputs = inputs.filter('[type="checkbox"]');
                        if (checkboxInputs.length) {
                            inputs = checkboxInputs;
                        }

                        if (inputs.length) {
                            if (checkboxInputs.length) {
                                const checked = ['Yes', 'yes', true, '1', 1].includes(values[key]);
                                inputs.each(function () {
                                    const input = $(this);
                                    input.prop('checked', checked);
                                    changedInputs.push(input);
                                });
                            } else {
                                inputs.each(function () {
                                    const input = $(this);
                                    let nextValue = values[key];

                                    if (key === 'image_placement') {
                                        const isSelect = (input.prop('tagName') || '').toLowerCase() === 'select';
                                        const optionValues = isSelect ? input.find('option').map(function () {
                                            return `${$(this).val() || ''}`.toLowerCase();
                                        }).get() : [];
                                        const rawMode = `${values[key] || ''}`.toLowerCase();
                                        if (isSelect) {
                                            if (rawMode === 'overlay' && optionValues.includes('background')) {
                                                nextValue = 'background';
                                            } else if (rawMode === 'classic' && optionValues.includes('block')) {
                                                nextValue = 'block';
                                            }
                                        } else {
                                            if (rawMode === 'background') {
                                                nextValue = 'overlay';
                                            } else if (rawMode === 'block') {
                                                nextValue = 'classic';
                                            }
                                        }
                                    }

                                    input.val(nextValue);
                                    if (input.hasClass('wp-color-picker') && input.iris) {
                                        input.iris('color', nextValue);
                                    }

                                    const is_font = input.next('.font-select');
                                    if (is_font.length) {
                                        input.trigger('setFont', nextValue);
                                    }
                                    changedInputs.push(input);
                                });
                            }
                        }
                    });

                    const seen = new Set();
                    changedInputs.forEach(function (input) {
                        if (!input || !input.length) {
                            return;
                        }
                        const node = input.get(0);
                        if (!node || seen.has(node)) {
                            return;
                        }
                        seen.add(node);
                        input.trigger('input').trigger('change');
                    });

                    // Ensure mode-based option groups refresh immediately after preset apply.
                    findFieldsByName('image_placement').trigger('change');
                    findFieldsByName('hide_cta_image').trigger('change');
                    findFieldsByName('sidebar_template').trigger('change').trigger('update');

                    // Keep content-tab image thumbnail in sync with the loaded preset image.
                    const presetImageUrl = `${values.sticky_s_media || values.preview_image_url || ''}`.trim();
                    if (presetImageUrl.length) {
                        syncContentImageThumbnail(presetImageUrl);
                    } else {
                        syncContentImageThumbnail($('#sticky_s_media').val());
                    }
                    $('#wordpress-cta-popup-load-design').trigger('close');
                    $('#cta-premade-style').val('');
                    self.template = null;
                    self.templateKey = '';

                    const finishBulkApply = function () {
                        setPreviewBusy(false);
                        if (window.EasyStickySidebar) {
                            window.EasyStickySidebar.isApplyingPresetTemplate = false;
                        }
                        syncContentImageThumbnail($('#sticky_s_media').val());
                        if (typeof window.easyStickySidebarRefreshStylingSections === 'function') {
                            window.easyStickySidebarRefreshStylingSections();
                        }
                        if (ess && typeof ess.applyTemplatePreviewRefresh === 'function') {
                            ess.applyTemplatePreviewRefresh();
                        }
                    };

                    if (typeof window.requestAnimationFrame === 'function') {
                        window.requestAnimationFrame(function () {
                            window.requestAnimationFrame(finishBulkApply);
                        });
                    } else {
                        setTimeout(finishBulkApply, 0);
                    }
                } catch (error) {
                    setPreviewBusy(false);
                    if (window.EasyStickySidebar) {
                        window.EasyStickySidebar.isApplyingPresetTemplate = false;
                    }
                    $('#wordpress-cta-popup-load-design').trigger('close');
                    throw error;
                }
            })
        }
    }
    DesignTemplate.init();


    const Wordpress_CTA_Popup = {
        container: $('.wordpress-cta-popup'),
        heading: 'This is a pro feature',
        description: null,

        init: function () {
            const self = this;

            $(document).off('click.essCtaPopupPro', '.wordpress-cta-pro-element, .wordpress-cta-pro-feature-inline').on('click.essCtaPopupPro', '.wordpress-cta-pro-element, .wordpress-cta-pro-feature-inline', function (e) {
                e.preventDefault();
                $('#wordpress-cta-pro-feature-popup').trigger('open');
            })

            $(document).off('open.essCtaPopup', '.wordpress-cta-popup').on('open.essCtaPopup', '.wordpress-cta-popup', function (event, data) {
                const popup_text = Object.assign({ heading: self.heading, description: self.description }, data)
                const popup = $(this);
                popup.find('.pro-title').html(popup_text.heading)
                if (popup_text.description) {
                    popup.find('.pro-description').html(popup_text.description)
                }

                $('body').addClass('has-wordpress-cta-popup');
                popup.addClass('active');
            })

            $(document).off('close.essCtaPopup', '.wordpress-cta-popup').on('close.essCtaPopup', '.wordpress-cta-popup', function () {
                $('body').removeClass('has-wordpress-cta-popup')
                $(this).removeClass('active');
            })

            $(document).off('click.essCtaPopupBackdrop', '.wordpress-cta-popup').on('click.essCtaPopupBackdrop', '.wordpress-cta-popup', function (e) {
                if (this === e.target) {
                    $(this).trigger('close')
                }
            })

            $(document).off('click.essCtaPopupClose', '.wordpress-cta-popup .close').on('click.essCtaPopupClose', '.wordpress-cta-popup .close', function () {
                $(this).closest('.wordpress-cta-popup').trigger('close')
            })

            $(document).off('keydown.essCtaPopup').on('keydown.essCtaPopup', function (e) {
                if (e.keyCode === 27) { // ESC
                    $('.wordpress-cta-popup.active').trigger('close')
                }
            });
        }
    }

    Wordpress_CTA_Popup.init();


    $('ul.wordpress-cta-dimension-field input[type="number"]').on('input', function () {
        const dimension_input = $(this).closest('ul.wordpress-cta-dimension-field');
        if (!dimension_input.hasClass('linked')) {
            return;
        }

        const value = $(this).val();
        dimension_input.find('input[type="number"]').val(value);
    })

    $('ul.wordpress-cta-dimension-field li.input-link').on('click', function () {
        const dimension_input = $(this).closest('ul.wordpress-cta-dimension-field');
        if (dimension_input.hasClass('linked')) {
            dimension_input.removeClass('linked');
            return;
        }

        dimension_input.addClass('linked')

        const first_value = dimension_input.children().eq(0).children('input').val();

        dimension_input.find('input[type="number"]').val(first_value)
    })

    $('ul.wordpress-cta-dimension-field').each(function () {
        const dimension_inputs = $(this).find('input[type="number"]');
        let first_value = dimension_inputs.eq(0).val();

        const values = [];
        dimension_inputs.each(function () {
            values.push($(this).val())
        })

        const get_values = values.filter(val => val === first_value)


        if (values.length === get_values.length) {
            $(this).addClass('linked');
        }
    })

    EasyStickySidebarIconLibrary = {
        popup: $('#easy-sticky-sidebar-icon-library-popup'),
        icon_items: $('#easy-sticky-sidebar-icon-library-popup .easy-sticky-sidebar-icon-grid .icon'),
        selected: '',
        onSelect: null,

        open: function (options) {
            this.onSelect = options?.onSelect;

            let selected_icon = options?.selected;
            if (selected_icon && selected_icon.length > 0) {
                selected_icon = '.' + selected_icon.replace(' ', '.');
            }

            const selected = this.icon_items.find(selected_icon);
            if (selected.length) {
                selected.closest('.icon').addClass('selected');
            }

            $('body').addClass('has-icon-library-popup')
            this.popup.addClass('opened');
        },

        close: function () {
            this.selected = '';
            this.icon_items.removeClass('selected')
            $('body').removeClass('has-icon-library-popup');
            this.popup.removeClass('opened');
        },

        init: function () {
            const self = this;

            self.popup.find('.dialog-header .close').on('click', function (e) {
                e.preventDefault();
                self.close();
            })

            self.popup.find('.easy-sticky-sidebar-icon-grid .icon').on('click', function (e) {
                e.preventDefault();

                self.icon_items.removeClass('selected');
                $(this).addClass('selected');
                self.selected = $(this).find('span').attr('class');
            })

            self.popup.find('.dialog-footer .btn-add-icon').on('click', function (e) {
                e.preventDefault();

                if (typeof self.onSelect === 'function') {
                    self.onSelect(self.selected)
                } else {
                    console.error('Callback function is not valid')
                }

                self.close();
            })

            $(document).on('keydown', function (e) {
                if (e.keyCode === 27) { // ESC
                    self.close();
                }
            });

            self.popup.on('click', function (e) {
                if (self.popup.is(e.target)) {
                    self.close()
                }
            })

            const search_form = self.popup.find('.form-search-icons');

            search_form.on('submit', function (e) {
                return false;
            })


            let typing = null;
            search_form.on('input', 'input', function (e) {
                if (typing) {
                    clearTimeout(typing)
                }

                typing = setTimeout(() => {
                    const search_text = $(this).val();
                    const keywords = search_text.split(' ').map((keyword) => keyword.trim().toLowerCase()).filter(t => t.length > 0);

                    if (keywords.length === 0) {
                        return self.icon_items.show();
                    }

                    jQuery.expr[':'].contains = function (a, i, m) {
                        return jQuery(a).text().toLowerCase().indexOf(m[3].toLowerCase()) >= 0;
                    };

                    let icons = self.icon_items.filter(':contains(' + keywords[0] + ')')
                    keywords.forEach((keyword, i) => {
                        if (i > 0) {
                            icons = icons.add(self.icon_items.filter(':contains(' + keyword + ')'))
                        }
                    })

                    self.icon_items.not(icons).hide();
                    icons.show();
                }, 300)
            })
        }
    }

    EasyStickySidebarIconLibrary.init();

    // Button icon selector (free/pro) using shared icon library popup.
    $(document).on('click', '.icon-library-select-button .btn-select-button-icon', function (e) {
        e.preventDefault();
        const container = $(this).closest('.icon-library-select-button');
        EasyStickySidebarIconLibrary.open({
            selected: container.find('input').val() || '',
            onSelect: function (icon_class) {
                container.find('input').val(icon_class).trigger('input').trigger('change');
                container.find('.icon').attr('class', `icon ${icon_class}`);
            }
        });
    });

    // Remove selected button icon.
    $(document).on('click', '.icon-library-select-button .btn-remove-button-icon', function (e) {
        e.preventDefault();
        const container = $(this).closest('.icon-library-select-button');
        const input = container.find('input[name="button_icon"]');
        input.val('').trigger('input').trigger('change');
        const previewIcon = container.find('.icon');
        if (previewIcon.length) {
            previewIcon.attr('class', 'icon');
        }
    });


    const FloatingButton = {
        buttons: {},
        button_default_args: {},
        container: $('#floating-buttons-options'),
        button_container: $('#floating-buttons-options .floating-buttons'),

        update_buttons: function (button_id = 0, event = 'update') {
            const buttonTemplateNode = document.getElementById('tmpl-easy-sticky-sidebar-floating-single-button-style');
            if (!buttonTemplateNode || typeof wp === 'undefined' || !wp.template) {
                return;
            }

            const tabs = $('#floating-single-button-styles').find('.easy-sticky-sidebar-fieldset-floating-button');
            const current_tab = tabs.filter(`[data-id="${button_id}"]`);

            if (event === 'remove') {
                current_tab.remove();
            }

            const button_args = { ...this.button_default_args, ...(this.buttons[button_id] || {}) };
            button_args.icon = `${button_args.icon || ''}`;
            button_args.text = `${button_args.text || ''}`;


            const button_style_template = wp.template('easy-sticky-sidebar-floating-single-button-style');

            let tab_heading = '';
            if (button_args.icon.length) {
                tab_heading = `<i class="icon ${button_args.icon}"></i>`;
            }

            tab_heading += button_args.text;
            if (!tab_heading.length) {
                tab_heading = `Button ${button_id + 1}`;
            }

            button_args.button_no = button_id;
            button_args.heading = tab_heading;

            const html = button_style_template(button_args);

            if (current_tab.length) {
                current_tab.replaceWith(html)
            }

            if (event === 'add') {
                $('#floating-single-button-styles').append(html);
            }

            $('#floating-single-button-styles').find('.sticky-sidebar-colorpicker').wpColorPicker({
                change: function (event) {
                    setTimeout(() => {
                        $(event.target).trigger('change')
                    }, 300)
                }
            });
        },

        init: function () {
            const self = this;

            let button_default_args = self.container.data('button-default-args');
            if (typeof button_default_args === 'object') {
                self.button_default_args = button_default_args
            }

            const buttons = self.container.data('buttons');
            if (Array.isArray(buttons)) {
                buttons.forEach((button, key) => {
                    self.buttons[key] = button;
                })
            }

            // Ensure per-button style panels are always present.
            // If server-rendered styles are missing (or stale), build from
            // current floating buttons data immediately.
            const stylePanel = $('#floating-single-button-styles');
            const hasRenderedStylePanels = stylePanel.find('.easy-sticky-sidebar-fieldset-floating-button').length > 0;
            if (!hasRenderedStylePanels) {
                let seededFromRows = false;
                self.button_container.children('.floating-button-item').each(function () {
                    const row = $(this);
                    const buttonId = parseInt(row.attr('data-id'), 10);
                    if (Number.isNaN(buttonId)) {
                        return;
                    }
                    if (!self.buttons[buttonId]) {
                        self.buttons[buttonId] = { ...self.button_default_args };
                    }

                    self.buttons[buttonId].icon = row.find('.button-icon').val() || self.buttons[buttonId].icon || '';
                    self.buttons[buttonId].text = row.find('.button-text').val() || self.buttons[buttonId].text || '';
                    self.buttons[buttonId].url = row.find('input[name^="floating_buttons"][name$="[url]"]').val() || self.buttons[buttonId].url || '';
                    seededFromRows = true;
                });

                if (!seededFromRows && Object.keys(self.buttons).length === 0) {
                    // Last-resort fallback: one editable default style item.
                    self.buttons[0] = { ...self.button_default_args };
                }

                Object.keys(self.buttons).forEach((key) => {
                    const buttonId = parseInt(key, 10);
                    if (!Number.isNaN(buttonId)) {
                        self.update_buttons(buttonId, 'add');
                    }
                });
            }

            self.container.on('change', '[type="checkbox"][name="hide_floating_button_text"]', function () {
                if ($(this).is(':checked')) {
                    $('#SSuprydp_form').addClass('hide-floating-button-text')
                } else {
                    $('#SSuprydp_form').removeClass('hide-floating-button-text')
                }
            }).trigger('change')

            self.container.on('click', '.btn-add-button', function (e) {
                e.preventDefault();

                let next_button_no = 0;

                const buttons = self.button_container.children();
                if (buttons.length) {
                    const last_button = buttons.last();
                    next_button_no = parseInt(last_button.attr('data-id')) + 1;
                }

                if (buttons.length >= 4 && !has_easy_sticky_sidebar_pro()) {
                    $('#wordpress-cta-pro-feature-popup').trigger('open', {
                        description: 'You need pro version for adding more buttons.'
                    });

                    return;
                }

                const button_template = wp.template("easy-sticky-sidebar-floating-button");
                const button_args = { ...self.button_default_args, button_no: next_button_no };
                const button_html = button_template(button_args);
                self.button_container.append(button_html);
                self.buttons[next_button_no] = button_args;
                self.update_buttons(next_button_no, 'add');
            })

            self.container.on('click', '.btn-button-remove', function () {
                const button_container = $(this).closest('.floating-button-item')
                const button_id = button_container.attr('data-id');
                delete self.buttons[button_id];
                self.update_buttons(button_id, 'remove');
                button_container.remove();
            })

            self.button_container.on('input', '.button-text', function () {
                const button = $(this).closest('.floating-button-item');
                const button_id = button.attr('data-id');
                self.buttons[button_id]['text'] = $(this).val();
                self.update_buttons(button_id);
            })

            self.button_container.on('input', '.button-icon', function () {
                const button = $(this).closest('.floating-button-item');
                const button_id = button.attr('data-id');
                self.buttons[button_id]['icon'] = $(this).val();
                self.update_buttons(button_id);
            })

            self.button_container.on('click', '.sticky-sidebar-select-icon .btn-primary', function (e) {
                e.preventDefault();
                const container = $(this).closest('.sticky-sidebar-select-icon');

                EasyStickySidebarIconLibrary.open({
                    selected: container.find('input').val() || '',
                    onSelect: function (icon_class) {
                        container.find('input').val(icon_class).trigger('input');
                        container.find('.icon').attr('class', `icon ${icon_class}`);
                    }
                });
            })

            $('#floating-single-button-styles').on('input, change', '[data-name]', function () {
                const button_id = $(this).closest('.easy-sticky-sidebar-fieldset-floating-button').attr('data-id');
                const slug = $(this).data('name');
                if (!slug.length) {
                    return;
                }

                self.buttons[button_id][slug] = $(this).val();
            })

            wp.hooks.doAction('easy_sticky_sidebar_floating_button_init', self);
        }
    }

    FloatingButton.init();

    $('.sticky-cta-range-slider input[type="range"]').on('input', function () {
        $(this).closest('.sticky-cta-range-slider').attr('data-value', $(this).val());
    })
});




// jQuery(document).ready(function($) {
//     var $btn = $('#btn-add-exclude-location');
//     var isWPCTAProActive = $('body').hasClass('Wordpress_CTA_Pro');

//     if (!isWPCTAProActive) {
//         $btn.addClass('blurred');     // Plugin NOT active → blur
//     } else {
//         $btn.removeClass('blurred');  // Plugin active → remove blur
//     }
// });

jQuery(document).ready(function ($) {
    const form = $('#SSuprydp_form');
    if (!form.length) {
        return;
    }

    const tabs = $('.nav-tab-wrapper.sticky-sidebar-nav-tab-wrapper .nav-tab');
    const navWrapper = $('.nav-tab-wrapper.sticky-sidebar-nav-tab-wrapper').first();
    const progress = $('.ess-step-progress');
    const previewCard = $('.ess-live-preview-card');
    let navBgReady = false;
    let lastPreviewImageUrl = '';

    const stripText = function (value) {
        return $('<div>').html(value || '').text().trim();
    };

    const getValue = function (name, fallback = '') {
        if (name === 'image_placement') {
            const preferredInput = form.find('.cta-image-display-mode[name="image_placement"]').first();
            if (preferredInput.length) {
                return preferredInput.val() || fallback;
            }
        }

        const input = form.find(`[name="${name}"]`).first();
        if (!input.length) {
            return fallback;
        }

        if (input.attr('type') === 'checkbox') {
            return input.is(':checked') ? input.val() : '';
        }

        return input.val() || fallback;
    };

    const getChecked = function (name) {
        const input = form.find(`[name="${name}"][type="checkbox"]`).first();
        return input.length ? input.is(':checked') : false;
    };

    const syncContentImageThumbnail = function (imageUrl) {
        const resolved = `${imageUrl || ''}`.trim();
        if (!resolved.length) {
            return;
        }

        const imageField = $('#sticky_s_media');
        const imagePreview = $('#image-preview');
        if (imageField.length) {
            imageField.val(resolved);
        }
        if (imagePreview.length) {
            imagePreview.attr('src', resolved).prop('src', resolved);
        }
    };

    const getEditorDefault = function (template, fieldName, fallback = '') {
        const defaults = window.sticky_sidebar && window.sticky_sidebar.editor_defaults
            ? window.sticky_sidebar.editor_defaults
            : {};
        const templates = defaults.templates || {};
        const templateDefaults = templates[template] || {};

        if (Object.prototype.hasOwnProperty.call(templateDefaults, fieldName)) {
            return templateDefaults[fieldName];
        }

        return fallback;
    };

    const toggleStickyImageModeFieldGroups = function () {
        const mode = (getValue('image_placement', 'classic') || 'classic').toString().toLowerCase();
        const normalized = mode === 'background' ? 'overlay' : mode;
        const isOverlay = normalized === 'overlay';
        const isStickyTemplate = (getValue('sidebar_template', 'sticky-cta') || 'sticky-cta') === 'sticky-cta';
        const showClassicOnly = isStickyTemplate && !isOverlay;
        const showOverlayOnly = isStickyTemplate && isOverlay;

        form.find('.cta-image-classic-only')
            .toggle(showClassicOnly)
            .find('input, select, textarea, button')
            .prop('disabled', !showClassicOnly);

        form.find('.cta-image-overlay-only')
            .toggle(showOverlayOnly)
            .find('input, select, textarea, button')
            .prop('disabled', !showOverlayOnly);

        // Sticky classic-only controls: hide only in sticky overlay mode.
        const stickyClassicOnlyFields = form.find('.cta-sticky-classic-only');
        if (stickyClassicOnlyFields.length) {
            const showStickyClassicOnly = !isStickyTemplate || !isOverlay;
            stickyClassicOnlyFields
                .toggle(showStickyClassicOnly)
                .find('input, select, textarea, button')
                .prop('disabled', !showStickyClassicOnly);
        }

        // Hard guard: hide Content Padding in sticky overlay mode, regardless of source renderer.
        const contentPaddingWrap = form.find('[name="content_padding[top]"]').first().closest('.SSuprydp_field_wrap');
        if (contentPaddingWrap.length) {
            const shouldShowContentPadding = !isStickyTemplate || !isOverlay;
            contentPaddingWrap.toggle(shouldShowContentPadding);
            contentPaddingWrap.find('input, select, textarea, button').prop('disabled', !shouldShowContentPadding);
        }

        const overlayContentMarginWrap = form.find('[name="overlay_content_margin[top]"]').first().closest('.SSuprydp_field_wrap');
        if (overlayContentMarginWrap.length) {
            overlayContentMarginWrap
                .toggle(showOverlayOnly)
                .find('input, select, textarea, button')
                .prop('disabled', !showOverlayOnly);
        }

        const overlayButtonMarginWrap = form.find('[name="overlay_button_margin[top]"]').first().closest('.SSuprydp_field_wrap');
        if (overlayButtonMarginWrap.length) {
            overlayButtonMarginWrap
                .toggle(showOverlayOnly)
                .find('input, select, textarea, button')
                .prop('disabled', !showOverlayOnly);
        }

        const overlayPosition = (getValue('overlay_position', 'right') || 'right').toString().toLowerCase();
        const ctaPosition = (getValue('SSuprydp_cta_position', 'right') || 'right').toString().toLowerCase();
        const currentTemplate = (getValue('sidebar_template', 'sticky-cta') || 'sticky-cta').toString().toLowerCase();
        const hideContentToggleWrap = form.find('[name="hide_content_text"]').first().closest('.SSuprydp_field_wrap');
        if (hideContentToggleWrap.length) {
            const shouldShowHideContentToggle = currentTemplate !== 'gdpr';
            hideContentToggleWrap
                .toggle(shouldShowHideContentToggle)
                .find('input, select, textarea, button')
                .prop('disabled', !shouldShowHideContentToggle);
        }

        const showOverlayTextOrientationOnly = showOverlayOnly && (ctaPosition === 'left' || ctaPosition === 'right');
        const showSideTabOrientationOnly = (currentTemplate === 'tab-cta' || currentTemplate === 'html')
            ? (ctaPosition === 'left' || ctaPosition === 'right')
            : (showOverlayOnly ? showOverlayTextOrientationOnly : (isStickyTemplate && (ctaPosition === 'left' || ctaPosition === 'right')));

        const overlaySideTabOrientationWrap = form.find('[name="button_text_orientation"]').first().closest('.SSuprydp_field_wrap');
        if (overlaySideTabOrientationWrap.length) {
            overlaySideTabOrientationWrap
                .toggle(showSideTabOrientationOnly)
                .find('input, select, textarea, button')
                .prop('disabled', !showSideTabOrientationOnly);
        }

        const buttonIconValue = (getValue('button_icon', '') || '').toString().trim();
        const shouldShowButtonIconControls = (currentTemplate === 'sticky-cta' || currentTemplate === 'tab-cta' || currentTemplate === 'html') && buttonIconValue !== '';

        const buttonIconSizeWrap = form.find('[name="button_icon_size"]').first().closest('.SSuprydp_field_wrap');
        if (buttonIconSizeWrap.length) {
            buttonIconSizeWrap
                .toggle(shouldShowButtonIconControls)
                .find('input, select, textarea, button')
                .prop('disabled', !shouldShowButtonIconControls);
        }

        const buttonIconPositionWrap = form.find('[name="button_icon_position"]').first().closest('.SSuprydp_field_wrap');
        if (buttonIconPositionWrap.length) {
            buttonIconPositionWrap
                .toggle(shouldShowButtonIconControls)
                .find('input, select, textarea, button')
                .prop('disabled', !shouldShowButtonIconControls);
        }

        const buttonAlignmentWrap = form.find('[name="button_alignment"]').first().closest('.SSuprydp_field_wrap');
        if (buttonAlignmentWrap.length) {
            const shouldShowButtonAlignment = currentTemplate === 'sticky-cta' || currentTemplate === 'html';
            buttonAlignmentWrap
                .toggle(shouldShowButtonAlignment)
                .find('input, select, textarea, button')
                .prop('disabled', !shouldShowButtonAlignment);
        }

        const overlayContentCornerRadiusWrap = form.find('[name="overlay_tab_corner_radius"]').first().closest('.SSuprydp_field_wrap');
        if (overlayContentCornerRadiusWrap.length) {
            const shouldShowOverlayContentCornerRadius = showOverlayOnly || currentTemplate === 'html';
            overlayContentCornerRadiusWrap
                .toggle(shouldShowOverlayContentCornerRadius)
                .find('input, select, textarea, button')
                .prop('disabled', !shouldShowOverlayContentCornerRadius);
        }

        const fullTabHeightWrap = form.find('[name="overlay_full_tab_height"]').first().closest('.SSuprydp_field_wrap');
        if (fullTabHeightWrap.length) {
            const hasConfigurableTabSize = currentTemplate === 'sticky-cta' || currentTemplate === 'html';
            const shouldShowFullTabHeight = hasConfigurableTabSize && (currentTemplate === 'html' || showOverlayOnly);
            const fullTabLabel = (ctaPosition === 'top' || ctaPosition === 'bottom') ? 'Full Width Tab' : 'Full Height Tab';
            const fullTabDescription = (ctaPosition === 'top' || ctaPosition === 'bottom')
                ? 'When off, the tab uses only the width needed for its icon and text, then follows the Tab Alignment setting.'
                : 'When off, the tab uses only the height needed for its icon and text, then follows the Tab Alignment setting.';
            fullTabHeightWrap.find('.ess-full-tab-size-label, .heading').first().text(fullTabLabel);
            fullTabHeightWrap.find('.ess-full-tab-size-description').first().text(fullTabDescription);
            fullTabHeightWrap
                .toggle(shouldShowFullTabHeight)
                .find('input, select, textarea, button')
                .prop('disabled', !shouldShowFullTabHeight);
        }
    };

    const setStepState = function () {
        if (!tabs.length) {
            return;
        }

        const activeTab = tabs.filter('.nav-tab-active').first();
        const activeIndex = tabs.index(activeTab);
        const total = tabs.length;

        tabs.each(function (index) {
            let state = 'upcoming';
            if (index < activeIndex) {
                state = 'done';
            } else if (index === activeIndex) {
                state = 'active';
            }

            $(this)
                .attr('data-state', state)
                // CUSTOM STICKY NAV: class hook for completed tabs.
                .toggleClass('is-completed', state === 'done');
        });

        progress.text(`${activeIndex + 1} / ${total}`);
        $('.ess-step-control[data-direction="prev"]').prop('disabled', activeIndex <= 0);
        $('.ess-step-control[data-direction="next"]').prop('disabled', activeIndex >= total - 1);
        moveActiveNavBackground(true);
    };

    const ensureActiveNavBackground = function () {
        if (!navWrapper.length) {
            return null;
        }

        let bg = navWrapper.children('.ess-nav-active-bg').first();
        if (!bg.length) {
            bg = $('<span class="ess-nav-active-bg" aria-hidden="true"></span>');
            navWrapper.append(bg);
        }

        return bg;
    };

    const moveActiveNavBackground = function (animate = true) {
        const bg = ensureActiveNavBackground();
        if (!bg || !bg.length) {
            return;
        }

        const activeTab = tabs.filter('.nav-tab-active').first();
        if (!activeTab.length) {
            return;
        }

        const left = activeTab.position().left;
        const top = activeTab.position().top;
        const width = activeTab.outerWidth();
        const height = activeTab.outerHeight();

        bg
            .toggleClass('is-first', activeTab.is(':first-of-type'))
            .toggleClass('is-last', activeTab.is(':last-of-type'));

        if (!navBgReady || !animate) {
            bg.css('transition', 'none');
            bg.css({ left, top, width, height });
            // Force reflow so transition can be restored for next change.
            void bg[0].offsetHeight;
            bg.css('transition', '');
            navBgReady = true;
            return;
        }

        bg.css({ left, top, width, height });
    };

    $('.ess-step-control').on('click', function (e) {
        e.preventDefault();

        const activeIndex = tabs.index(tabs.filter('.nav-tab-active').first());
        const direction = $(this).data('direction');
        const targetIndex = direction === 'next' ? activeIndex + 1 : activeIndex - 1;

        if (targetIndex < 0 || targetIndex >= tabs.length) {
            return;
        }

        tabs.eq(targetIndex).trigger('click');
    });

    tabs.on('click', function () {
        setTimeout(setStepState, 0);
    });

    $(window).on('resize', function () {
        moveActiveNavBackground(false);
        if (!previewInitialized) {
            return;
        }
        requestAnimationFrame(function () {
            syncPreviewButtonMetrics();
            syncPreviewOverlayBackgroundOffsets($('#ess-preview-cta'));
        });
    });

    const getPreviewFont = function (rawValue, fallback) {
        const value = (rawValue || '').toString().trim();
        if (!value.length) {
            return { family: fallback || '', weight: '', style: '' };
        }

        const [familyPart, variantPart = ''] = value.split(':');
        const family = familyPart.replace(/\+/g, ' ').trim();
        const variant = variantPart.trim().toLowerCase();

        let weight = '';
        let style = '';

        const weightMatch = variant.match(/\d{3}/);
        if (weightMatch) {
            weight = weightMatch[0];
        }

        if (variant.includes('italic')) {
            style = 'italic';
        }

        return { family: family || (fallback || ''), weight, style };
    };

    const applyPreviewFont = function (selector, value, fallback) {
        const font = getPreviewFont(value, fallback);
        const cssData = {};

        if (font.family) {
            cssData['font-family'] = `'${font.family}'`;
        }
        cssData['font-weight'] = font.weight || '';
        cssData['font-style'] = font.style || '';

        $(selector).css(cssData);
    };

    const getDimensionCss = function (fieldName) {
        const top = getValue(`${fieldName}[top]`, '');
        const right = getValue(`${fieldName}[right]`, '');
        const bottom = getValue(`${fieldName}[bottom]`, '');
        const left = getValue(`${fieldName}[left]`, '');
        const unit = getValue(`${fieldName}[unit]`, 'px') || 'px';

        const values = [top, right, bottom, left];
        const hasAny = values.some((item) => `${item}`.trim().length > 0);
        if (!hasAny) {
            return '';
        }

        const normalized = values.map((item) => {
            const raw = `${item}`.trim();
            if (!raw.length) {
                return `0${unit}`;
            }

            if (/^-?\d+(\.\d+)?$/.test(raw)) {
                return `${raw}${unit}`;
            }

            return raw;
        });

        return normalized.join(' ');
    };

    // Some installs can contain duplicate hidden dimension inputs in DOM.
    // For CTA link padding, prefer visible/enabled non-empty values first.
    const getSmartFieldValue = function (name, fallback = '') {
        const fields = form.find(`[name="${name}"]`);
        if (!fields.length) {
            return fallback;
        }

        const readNonEmpty = function ($collection) {
            let selected = '';
            $collection.each(function () {
                const $field = $(this);
                const type = ($field.attr('type') || '').toLowerCase();

                if ((type === 'checkbox' || type === 'radio') && !$field.is(':checked')) {
                    return;
                }

                const value = `${$field.val() || ''}`.trim();
                if (value.length > 0) {
                    selected = value;
                    return false;
                }
            });
            return selected;
        };

        const visibleEnabled = fields.filter(':enabled').filter(':visible');
        const enabled = fields.filter(':enabled');

        return (
            readNonEmpty(visibleEnabled) ||
            readNonEmpty(enabled) ||
            readNonEmpty(fields) ||
            fallback
        );
    };

    const getDimensionCssSmart = function (fieldName) {
        const top = getSmartFieldValue(`${fieldName}[top]`, '');
        const right = getSmartFieldValue(`${fieldName}[right]`, '');
        const bottom = getSmartFieldValue(`${fieldName}[bottom]`, '');
        const left = getSmartFieldValue(`${fieldName}[left]`, '');
        const unit = getSmartFieldValue(`${fieldName}[unit]`, 'px') || 'px';

        const values = [top, right, bottom, left];
        const hasAny = values.some((item) => `${item}`.trim().length > 0);
        if (!hasAny) {
            return '';
        }

        const normalized = values.map((item) => {
            const raw = `${item}`.trim();
            if (!raw.length) {
                return `0${unit}`;
            }

            if (/^-?\d+(\.\d+)?$/.test(raw)) {
                return `${raw}${unit}`;
            }

            return raw;
        });

        return normalized.join(' ');
    };

    const collectIndexedValues = function (prefix) {
        const results = {};
        const pattern = new RegExp(`^${prefix}\\[(\\d+)\\]\\[(.+)\\]$`);

        form.find(`[name^="${prefix}["]`).each(function () {
            const name = $(this).attr('name');
            const match = name.match(pattern);
            if (!match) {
                return;
            }

            const index = match[1];
            const key = match[2];

            if (!results[index]) {
                results[index] = {};
            }

            results[index][key] = $(this).val();
        });

        return results;
    };

    const getNormalizedNumber = function (name, fallback = '') {
        const input = form.find(`[name="${name}"]`).first();
        if (!input.length) {
            return fallback;
        }

        let raw = `${input.val() || ''}`.trim();
        if (!raw.length) {
            raw = `${input.attr('value') || ''}`.trim();
        }

        const parseValue = function (value) {
            const cleaned = `${value || ''}`.trim();
            if (!cleaned.length) {
                return null;
            }
            const parsed = parseFloat(cleaned.replace(/[^\d.\-]/g, ''));
            return Number.isNaN(parsed) ? null : parsed;
        };

        let parsed = parseValue(raw);
        if (parsed === null) {
            parsed = parseValue(fallback);
        }

        if (parsed === null) {
            return '';
        }

        input.val(parsed);
        return `${parsed}`;
    };

    let previewSpinnerTimer = null;
    let previewInputDebounceTimer = null;
    let previewInitialized = false;
    const resetPreviewButtonMetrics = function ($root) {
        if (!$root || !$root.length) {
            return;
        }
        $root.css('--buttonWidth', '');
        $root.css('--buttonHeight', '');
    };
    const applyPreviewButtonMetrics = function ($root, $button) {
        if (!$root || !$root.length || !$button || !$button.length) {
            return;
        }

        const width = Math.ceil($button.outerWidth());
        const height = Math.ceil($button.outerHeight());

        if (width) {
            $root.css('--buttonWidth', `${width}px`);
        }
        if (height) {
            $root.css('--buttonHeight', `${height}px`);
        }
    };

    const syncPreviewButtonMetrics = function () {
        applyPreviewButtonMetrics($('#ess-preview-cta'), $('#ess-preview-button-wrap'));
        applyPreviewButtonMetrics($('#ess-preview-tab-cta'), $('#ess-preview-tab-button'));
        applyPreviewButtonMetrics($('#ess-preview-html-cta'), $('#ess-preview-html-button'));
    };

    const syncPreviewOverlayBackgroundOffsets = function ($preview) {
        if (!$preview || !$preview.length) {
            return;
        }

        if (!$preview.hasClass('image-as-background')) {
            $preview.css('--ess-overlay-bg-offset-x', '');
            $preview.css('--ess-overlay-bg-offset-y', '');
            return;
        }

        const panel = $preview.find('#ess-preview-overlay-panel').first();
        if (!panel.length) {
            $preview.css('--ess-overlay-bg-offset-x', '');
            $preview.css('--ess-overlay-bg-offset-y', '');
            return;
        }

        const offsetX = Math.max(0, Math.round((panel.outerWidth() || 0) * 0.9));
        const offsetY = Math.max(0, Math.round((panel.outerHeight() || 0) * 0.9));
        $preview.css('--ess-overlay-bg-offset-x', `${offsetX}px`);
        $preview.css('--ess-overlay-bg-offset-y', `${offsetY}px`);
    };

    const updatePreview = function (opts) {
        if (typeof opts !== 'object' || opts === null) {
            opts = {};
        }
        const waitForHeroImage = opts.waitForHeroImage === true;
        const isInitialLoad = !previewInitialized;
        const showSpinner = opts.showSpinner === true || isInitialLoad || waitForHeroImage;
        previewInitialized = true;
        toggleStickyImageModeFieldGroups();

        if (showSpinner) {
            previewCard.addClass('is-preview-loading').attr('aria-busy', 'true');
            if (previewSpinnerTimer) {
                clearTimeout(previewSpinnerTimer);
            }
        }
        const template = getValue('sidebar_template', 'sticky-cta');
        const supportedTemplates = ['sticky-cta', 'tab-cta', 'banner', 'gdpr', 'html', 'floating-buttons'];
        const activeTemplate = supportedTemplates.includes(template) ? template : 'sticky-cta';
        const isStickyTemplate = activeTemplate === 'sticky-cta';
        const isProActive = typeof has_easy_sticky_sidebar_pro === 'function' ? has_easy_sticky_sidebar_pro() : false;
        const allowedPositions = ['left', 'right', 'top', 'bottom'];
        const normalizeSecondaryPosition = function (position, alignment) {
            const primary = (position || 'right').toString().toLowerCase();
            const secondary = (alignment || 'center').toString().toLowerCase();
            const isVertical = primary === 'top' || primary === 'bottom';
            const legacyMap = isVertical
                ? { top: 'left', center: 'center', bottom: 'right', left: 'left', right: 'right' }
                : { left: 'top', center: 'center', right: 'bottom', top: 'top', bottom: 'bottom' };
            const allowed = isVertical ? ['left', 'center', 'right'] : ['top', 'center', 'bottom'];
            const normalized = Object.prototype.hasOwnProperty.call(legacyMap, secondary)
                ? legacyMap[secondary]
                : secondary;

            return allowed.includes(normalized) ? normalized : 'center';
        };

        previewCard.toggleClass('is-preview-disabled', !supportedTemplates.includes(template));
        previewCard.attr('data-preview-template', activeTemplate);

        const previewStage = $('.ess-preview-stage');
        const previewTemplates = $('.ess-preview-template');
        previewTemplates.hide().removeClass('is-active');
        const activePane = previewTemplates.filter(`[data-template="${activeTemplate}"]`);
        if (activePane.length) {
            activePane.show().addClass('is-active');
        } else {
            previewTemplates.filter('[data-template="sticky-cta"]').show().addClass('is-active');
        }

        const syncPreviewStageHeight = function () {
            const currentActivePane = previewTemplates.filter('.is-active:visible').first();
            const paneHeight = currentActivePane.length ? currentActivePane.outerHeight(true) : 0;
            if (paneHeight && paneHeight > 0) {
                let extraHeight = 8;
                const externalVerticalTab = currentActivePane
                    .find('#ess-preview-html-cta.vertical-cta.ess-html-tab-align-left, #ess-preview-html-cta.vertical-cta.ess-html-tab-align-center, #ess-preview-html-cta.vertical-cta.ess-html-tab-align-right, #ess-preview-cta.vertical-cta.ess-overlay-vertical-tab-preview:not(.ess-overlay-vertical-full-tab-preview)')
                    .first();
                if (externalVerticalTab.length) {
                    extraHeight += Math.ceil(externalVerticalTab.find('.sticky-sidebar-button').first().outerHeight() || 0);
                }
                const nextHeight = Math.max(120, Math.ceil(paneHeight + extraHeight));
                previewStage.css('--ess-preview-height', `${nextHeight}px`);
            }
        };

        // Initial pass; run again after style updates.
        syncPreviewStageHeight();

        $('.ess-template-label').text(form.find('[name="sidebar_template"] option:selected').text().replace(/\s+\(.*\)$/, ''));

        const imagePlacementRaw = (getValue('image_placement', 'classic') || 'classic').toString().toLowerCase();
        const imagePlacement = imagePlacementRaw === 'background' ? 'overlay' : imagePlacementRaw;
        const isOverlayMode = imagePlacement === 'overlay';
        const overlayPosition = (getValue('overlay_position', 'right') || 'right').toString().toLowerCase();

        let ctaPosition = getValue('SSuprydp_cta_position', 'right');
        let ctaAlignment = getValue('horizontal_vertical_position', 'center');

        if (!allowedPositions.includes(ctaPosition)) {
            ctaPosition = 'right';
        }

        ctaAlignment = normalizeSecondaryPosition(ctaPosition, ctaAlignment);

        if (!isProActive) {
            ctaPosition = 'right';
            ctaAlignment = 'center';
        }

        let stickyPreviewPosition = ctaPosition;
        let stickyPreviewAlignment = ctaAlignment;

        // Floating buttons use a compact preview; keep left/right previews centered in admin.
        // This avoids inheriting unrelated sticky alignment values (top/bottom) and clipping.
        if (activeTemplate === 'floating-buttons' && (ctaPosition === 'left' || ctaPosition === 'right')) {
            ctaAlignment = 'center';
        }

        const anchorPosition = activeTemplate === 'sticky-cta' ? stickyPreviewPosition : ctaPosition;
        const anchorAlign = activeTemplate === 'sticky-cta' ? stickyPreviewAlignment : ctaAlignment;

        $('.ess-preview-anchor')
            .attr('data-position', anchorPosition)
            .attr('data-align', anchorAlign);

        const preview = $('#ess-preview-cta');
        preview
            .removeClass('sticky-cta-position-left sticky-cta-position-right sticky-cta-position-top sticky-cta-position-bottom vertical-cta vertical-cta-top vertical-cta-bottom')
            .addClass(`sticky-cta-position-${stickyPreviewPosition}`);
        resetPreviewButtonMetrics(preview);

        if (stickyPreviewPosition === 'top' || stickyPreviewPosition === 'bottom') {
            preview.addClass('vertical-cta').addClass(`vertical-cta-${stickyPreviewPosition}`);
        }
        const enableCtaWidth = getChecked('enable_cta_width') || getValue('enable_cta_width', 'no') === 'yes';
        const ctaWidthValue = parseInt(getValue('cta_width', ''), 10);
        const ctaWidthUnit = getValue('cta_width_unit', 'px') || 'px';
        const previewWidth = enableCtaWidth && !Number.isNaN(ctaWidthValue) && ctaWidthValue > 0 ? `${ctaWidthValue}${ctaWidthUnit}` : '';
        preview.css('--width', previewWidth);

        const buttonTextValue = getValue('SSuprydp_button_option_text', '');
        const buttonIconValue = getValue('button_icon', '');
        const buttonIconSize = parseInt(getNormalizedNumber('button_icon_size', '16'), 10);
        const buttonIconPosition = (getValue('button_icon_position', 'before') || 'before').toString().toLowerCase();
        const overlayTabCornerRadius = parseInt(getNormalizedNumber('overlay_tab_corner_radius', '5'), 10);
        const buttonTextOrientation = (getValue('button_text_orientation', getValue('overlay_tab_text_orientation', 'top-to-bottom')) || 'top-to-bottom').toString().toLowerCase();
        const pluginBaseUrl = (
            window.sticky_sidebar &&
            typeof window.sticky_sidebar.plugin_url === 'string' &&
            window.sticky_sidebar.plugin_url.length
        ) ? window.sticky_sidebar.plugin_url.replace(/\/$/, '') : '';
        const overlayFallbackImage = pluginBaseUrl ? `${pluginBaseUrl}/assets/img/overlay_dummy.webp` : '';
        const hideImage = getChecked('hide_cta_image')
            || getValue('hide_cta_image', 'no') === 'yes'
            || getChecked('SSuprydp_img_hideimg')
            || getValue('SSuprydp_img_hideimg', 'No') === 'Yes';
        preview.toggleClass('image-as-background', isOverlayMode);
        const overlayFullTabHeight = getChecked('overlay_full_tab_height') || getValue('overlay_full_tab_height', 'no') === 'yes';
        preview.toggleClass('ess-overlay-full-tab-height', isOverlayMode && overlayFullTabHeight);
        preview.toggleClass('ess-tab-text-bottom-to-top', false);
        preview.css('--ess-overlay-tab-corner-radius', !Number.isNaN(overlayTabCornerRadius) && overlayTabCornerRadius >= 0 ? `${overlayTabCornerRadius}px` : '5px');
        const stickyButtonIconHtml = buttonIconValue ? `<i class="icon ${buttonIconValue}"></i>` : '';
        const stickyButtonLabelHtml = `<span class="ess-sticky-sidebar-button-label">${buttonTextValue}</span>`;
        $('#ess-preview-button-text').html(
            buttonIconPosition === 'after'
                ? `${stickyButtonLabelHtml}${stickyButtonIconHtml}`
                : `${stickyButtonIconHtml}${stickyButtonLabelHtml}`
        );
        $('#ess-preview-content-text').text(stripText(getValue('SSuprydp_content_option_text', 'This is the content area for your sticky CTA.')));
        $('#ess-preview-link').text(getValue('SSuprydp_action_option_text', 'Get Started'));

        let image = getValue('sticky_s_media', '');
        if (!image) {
            image = ($('#image-preview').attr('src') || '').toString();
        }
        if (isOverlayMode && image && image.toLowerCase().includes('ss_dummy.jpg')) {
            image = overlayFallbackImage || image;
        }
        if (!image) {
            image = isOverlayMode ? overlayFallbackImage : classicFallbackImage;
        }
        if (hideImage) {
            image = '';
        }
        const previewContentContainer = $('#ess-preview-cta .sticky-sidebar-container').first();
        if (image.length) {
            $('#ess-preview-image-wrap').css('background-image', `url("${image}")`).show();
            if (isOverlayMode) {
                previewContentContainer.css('background-image', `url("${image}")`);
            }
        } else {
            $('#ess-preview-image-wrap').hide();
            if (isOverlayMode) {
                previewContentContainer.css('background-image', '');
            }
        }

        if (hideImage && !isOverlayMode) {
            $('#ess-preview-image-wrap').hide();
        }

        const imageOverlayEnabled = !isOverlayMode && (getChecked('enable_image_overlay') || getValue('enable_image_overlay', 'no') === 'yes');
        const overlayColor = getValue('cta_image_overlay_color', '');
        const overlayOpacityRaw = parseInt(getValue('cta_image_overlay_opacity', ''), 10);
        const overlayOpacity = Number.isNaN(overlayOpacityRaw) ? '' : Math.max(0, Math.min(100, overlayOpacityRaw)) / 100;

        preview.toggleClass('has-image-ovarlay', !!imageOverlayEnabled);
        if (imageOverlayEnabled) {
            preview.css('--ess-image-overlay-color', overlayColor || 'rgba(0,0,0,0.35)');
            preview.css('--ess-image-overlay-opacity', overlayOpacity === '' ? '0.35' : `${overlayOpacity}`);
        } else {
            preview.css('--ess-image-overlay-color', '');
            preview.css('--ess-image-overlay-opacity', '');
        }

        const defaultTabBackground = isOverlayMode ? '#099607' : '#4e0d61';
        $('#ess-preview-button-wrap').css('background-color', getValue('SSuprydp_button_option_backg_color', defaultTabBackground));
        $('#ess-preview-button-text').css('color', getValue('SSuprydp_button_option_color', '#ffffff'));
        if (isOverlayMode) {
            const overlayPosition = (getValue('overlay_position', 'right') || 'right').toString().toLowerCase();
            const normalizedOverlayPosition = ['top', 'left', 'bottom', 'right'].includes(overlayPosition) ? overlayPosition : 'right';
            const alignmentFallback = (normalizedOverlayPosition === 'top' || normalizedOverlayPosition === 'bottom') ? 'center' : normalizedOverlayPosition;
            let overlayContentAlignment = (getValue('overlay_content_alignment', '') || '').toString().toLowerCase();
            let overlayButtonAlignment = (getValue('overlay_button_alignment', '') || '').toString().toLowerCase();
            if (!['left', 'center', 'right'].includes(overlayContentAlignment)) {
                overlayContentAlignment = alignmentFallback;
            }
            if (!['left', 'center', 'right'].includes(overlayButtonAlignment)) {
                overlayButtonAlignment = alignmentFallback;
            }
            const overlayBackdropColor = getValue('overlay_backdrop_color', '#ffffff') || '#ffffff';
            const overlayBackdropOpacityRaw = parseInt(getValue('overlay_backdrop_opacity', '70'), 10);
            const overlayBackdropOpacity = Number.isNaN(overlayBackdropOpacityRaw) ? 0.7 : Math.max(0, Math.min(100, overlayBackdropOpacityRaw)) / 100;
            const overlayWidthRaw = parseInt(getValue('overlay_width', '60'), 10);
            const overlayWidth = Number.isNaN(overlayWidthRaw) ? 60 : Math.max(20, Math.min(100, overlayWidthRaw));
            const overlayContentPaddingRaw = parseInt(getValue('overlay_content_padding', '12'), 10);
            const overlayContentPadding = Number.isNaN(overlayContentPaddingRaw) ? 12 : Math.max(0, overlayContentPaddingRaw);
            const overlayContentMarginCss = getDimensionCss('overlay_content_margin');
            const overlayButtonMarginCss = getDimensionCss('overlay_button_margin');

            preview
                .removeClass('overlay-pos-top overlay-pos-left overlay-pos-bottom overlay-pos-right')
                .addClass(`overlay-pos-${normalizedOverlayPosition}`);

            preview.css('--ess-overlay-backdrop-color', overlayBackdropColor);
            preview.css('--ess-overlay-backdrop-opacity', `${overlayBackdropOpacity}`);
            preview.css('--ess-overlay-size', `${overlayWidth}%`);
            let overlayHeightCss = '';
            const overlayHeightEnabled = getChecked('enable_cta_height') || getValue('enable_cta_height', 'no') === 'yes';
            const overlayHeightValue = parseFloat(getValue('cta_height', ''));
            const overlayHeightUnit = (getValue('cta_height_unit', 'px') || 'px').toString();
            if (overlayHeightEnabled && !Number.isNaN(overlayHeightValue) && overlayHeightValue > 0) {
                if (overlayHeightUnit === 'px') {
                    overlayHeightCss = `${Math.max(60, overlayHeightValue)}px`;
                } else {
                    overlayHeightCss = `${overlayHeightValue}${overlayHeightUnit}`;
                }
            } else {
                overlayHeightCss = '300px';
            }
            preview.css('--ess-overlay-height', overlayHeightCss);
            preview.css('--ess-overlay-content-padding', `${overlayContentPadding}px`);
            $('#ess-preview-overlay-panel').css('padding', `${overlayContentPadding}px`);

            $('#ess-preview-image-wrap').hide();

            const overlayContentColor = getValue('SSuprydp_content_option_color', '#383838');
            $('#ess-preview-content-text').css({
                'background-color': 'transparent',
                'color': overlayContentColor,
                'padding': '',
                'text-align': overlayContentAlignment,
                'margin': overlayContentMarginCss || ''
            });

            const overlayButtonPaddingCss = getDimensionCss('overlay_button_padding');
            const overlayButtonPadVRaw = parseInt(getValue('overlay_button_padding_v', '5'), 10);
            const overlayButtonPadHRaw = parseInt(getValue('overlay_button_padding_h', '20'), 10);
            const overlayButtonRadiusRaw = parseInt(getValue('overlay_button_radius', '50'), 10);
            const overlayButtonPadV = Number.isNaN(overlayButtonPadVRaw) ? 5 : Math.max(0, overlayButtonPadVRaw);
            const overlayButtonPadH = Number.isNaN(overlayButtonPadHRaw) ? 20 : Math.max(0, overlayButtonPadHRaw);
            const overlayButtonRadius = Number.isNaN(overlayButtonRadiusRaw) ? 50 : Math.max(0, overlayButtonRadiusRaw);
            const overlayButtonBg = getValue('link_text_background', '') || getValue('SSuprydp_button_option_backg_color', '#08a800');
            const overlayButtonColor = getValue('SSuprydp_action_option_color', '') || getValue('SSuprydp_button_option_color', '#ffffff');

            let alignSelf = 'flex-end';
            if (overlayButtonAlignment === 'left') {
                alignSelf = 'flex-start';
            } else if (overlayButtonAlignment === 'center') {
                alignSelf = 'center';
            }
            $('#ess-preview-link').css({
                'background-color': overlayButtonBg,
                'color': overlayButtonColor,
                'text-align': overlayButtonAlignment,
                'padding': overlayButtonPaddingCss || `${overlayButtonPadV}px ${overlayButtonPadH}px`,
                'border-radius': `${overlayButtonRadius}px`,
                'margin': overlayButtonMarginCss || '',
                'align-self': alignSelf
            });
            const previewLink = $('#ess-preview-link').get(0);
            if (previewLink) {
                previewLink.style.setProperty('padding', overlayButtonPaddingCss || `${overlayButtonPadV}px ${overlayButtonPadH}px`, 'important');
                previewLink.style.setProperty('border-radius', `${overlayButtonRadius}px`, 'important');
            }
            syncPreviewOverlayBackgroundOffsets(preview);
        } else {
            preview.removeClass('image-as-background overlay-pos-top overlay-pos-left overlay-pos-bottom overlay-pos-right');
            preview.css('--ess-overlay-backdrop-color', '');
            preview.css('--ess-overlay-backdrop-opacity', '');
            preview.css('--ess-overlay-height', '');
            preview.css('--ess-overlay-size', '');
            preview.css('--ess-overlay-content-padding', '');
            preview.css('--ess-overlay-bg-offset-x', '');
            preview.css('--ess-overlay-bg-offset-y', '');
            $('#ess-preview-overlay-panel').css('padding', '');
            previewContentContainer.css('background-image', '');

            $('#ess-preview-content-text').css({
                'background-color': getValue('content_background_color', 'rgb(37 13 97)'),
                'color': getValue('SSuprydp_content_option_color', '#ffffff'),
                'text-align': '',
                'margin': ''
            });
            $('#ess-preview-link').css({
                'background-color': getValue('link_text_background', '#11265d'),
                'color': getValue('SSuprydp_action_option_color', '#1c3f87'),
                'text-align': '',
                'border-radius': '',
                'padding': '',
                'margin': '',
                'align-self': ''
            });
        }

        const showLine = getChecked('line_separator_show');
        if (showLine && !isOverlayMode) {
            $('#ess-preview-divider').show().css('background-color', getValue('line_separator_color', '#c7d7ef'));
        } else {
            $('#ess-preview-divider').hide();
        }

        const buttonFontSize = parseInt(getNormalizedNumber('SSuprydp_button_option_size', '24'), 10);
        const contentFontDefault = isOverlayMode ? '24' : '22';
        const contentFontSize = parseInt(getNormalizedNumber('SSuprydp_content_option_size', contentFontDefault), 10);
        const linkFontDefault = isOverlayMode ? '24' : '20';
        const linkFontSize = parseInt(getNormalizedNumber('SSuprydp_action_option_size', linkFontDefault), 10);

        if (!isNaN(buttonFontSize)) {
            $('#ess-preview-button-text').css('font-size', `${buttonFontSize}px`);
        }
        $('#ess-preview-button-text .icon').css('font-size', !Number.isNaN(buttonIconSize) && buttonIconSize > 0 ? `${buttonIconSize}px` : '16px');

        if (!isNaN(contentFontSize)) {
            $('#ess-preview-content-text').css('font-size', `${contentFontSize}px`);
        }

        if (!isNaN(linkFontSize)) {
            $('#ess-preview-link').css('font-size', `${linkFontSize}px`);
        }

        // Keep preview font rendering in sync with style tab Google font values.
        applyPreviewFont('#ess-preview-button-text', getValue('SSuprydp_button_option_font', 'Archivo:700'), 'Archivo:700');
        const contentFontDefaultFamily = isOverlayMode ? 'Arial' : 'Open Sans';
        applyPreviewFont('#ess-preview-content-text', getValue('SSuprydp_content_option_font', contentFontDefaultFamily), contentFontDefaultFamily);
        const linkFontDefaultFamily = isOverlayMode ? 'Archivo:700' : 'Open Sans';
        applyPreviewFont('#ess-preview-link', getValue('SSuprydp_action_option_font', linkFontDefaultFamily), linkFontDefaultFamily);

        const isVerticalPosition = stickyPreviewPosition === 'top' || stickyPreviewPosition === 'bottom';
        const axisMap = { start: 'flex-start', center: 'center', end: 'flex-end' };
        let alignmentValue = (getValue('button_alignment', '') || '').toString().toLowerCase();
        if (!alignmentValue) {
            const legacyAlign = (getValue('SSuprydp_button_option_align', 'left') || '').toString().toLowerCase();
            const legacyMap = { left: 'start', center: 'center', right: 'end', top: 'start', middle: 'center', bottom: 'end' };
            alignmentValue = legacyMap[legacyAlign] || 'start';
        }
        const axisAlign = axisMap[alignmentValue] || 'flex-start';
        const justifyValue = isVerticalPosition ? 'center' : axisAlign;
        const alignValue = isVerticalPosition ? axisAlign : 'center';
        const htmlFullTabHeight = getChecked('overlay_full_tab_height') || getValue('overlay_full_tab_height', 'no') === 'yes';
        const tabContentJustifyValue = axisMap[alignmentValue] || 'flex-start';
        const buttonAlignStyles = {
            'text-align': 'center',
            'display': 'flex',
            'flex-direction': 'column',
            'align-items': alignValue,
            'justify-content': justifyValue
        };
        const applyAlignStyles = function ($el) {
            if (!$el || !$el.length) {
                return;
            }
            $el.css(buttonAlignStyles);
            if (isVerticalPosition) {
                $el[0].style.setProperty('align-items', alignValue, 'important');
                $el[0].style.setProperty('justify-content', justifyValue, 'important');
            }
        };
        applyAlignStyles($('#ess-preview-button-wrap'));
        applyAlignStyles($('#ess-preview-tab-button'));
        applyAlignStyles($('#ess-preview-html-button'));
        if (ctaPosition === 'left' || ctaPosition === 'right') {
            if (htmlFullTabHeight) {
                $('#ess-preview-html-cta').addClass('ess-html-full-tab-height');
            } else {
                const htmlSideAlignClass = alignmentValue === 'end'
                    ? 'ess-html-side-tab-align-bottom'
                    : (alignmentValue === 'center' ? 'ess-html-side-tab-align-center' : 'ess-html-side-tab-align-top');
                $('#ess-preview-html-cta').addClass(htmlSideAlignClass);
            }
        } else if (ctaPosition === 'top' || ctaPosition === 'bottom') {
            if (htmlFullTabHeight) {
                $('#ess-preview-html-cta').addClass('ess-html-full-tab-width');
            } else {
                const htmlVerticalAlignClass = alignmentValue === 'end'
                    ? 'ess-html-tab-align-right'
                    : (alignmentValue === 'center' ? 'ess-html-tab-align-center' : 'ess-html-tab-align-left');
                $('#ess-preview-html-cta').addClass(htmlVerticalAlignClass);
            }
        }
        if (isVerticalPosition) {
            const textAlign = alignmentValue === 'start' ? 'left' : (alignmentValue === 'end' ? 'right' : 'center');
            $('#ess-preview-button-text').css('text-align', textAlign);
            $('#ess-preview-tab-button-text').css('text-align', textAlign);
            $('#ess-preview-html-button-text').css('text-align', textAlign);
        }

        const previewButtonWrapEl = $('#ess-preview-button-wrap').get(0);
        const previewButtonTextEl = $('#ess-preview-button-text').get(0);
        const previewButtonLabelEl = previewButtonTextEl ? previewButtonTextEl.querySelector('.ess-sticky-sidebar-button-label') : null;
        const previewButtonIconEl = previewButtonTextEl ? previewButtonTextEl.querySelector('.icon') : null;
        preview.removeClass('ess-overlay-side-tab-preview ess-overlay-full-height-side-tab-preview ess-overlay-vertical-tab-preview ess-overlay-vertical-full-tab-preview ess-overlay-tab-align-left ess-overlay-tab-align-center ess-overlay-tab-align-right ess-overlay-side-tab-align-top ess-overlay-side-tab-align-center ess-overlay-side-tab-align-bottom ess-tab-text-bottom-to-top');
        if (previewButtonWrapEl) {
            [
                'text-align',
                'display',
                'flex-direction',
                'align-items',
                'justify-content',
                'align-self',
                'width',
                'height',
                'min-height'
            ].forEach(function (property) {
                previewButtonWrapEl.style.removeProperty(property);
            });
        }
        if (previewButtonTextEl) {
            [
                'display',
                'flex-direction',
                'align-items',
                'justify-content',
                'gap',
                'width',
                'min-width',
                'max-width',
                'white-space',
                'writing-mode',
                'text-orientation',
                'text-align',
                'left',
                'overflow',
                'transform',
                'transform-origin',
                'padding'
            ].forEach(function (property) {
                previewButtonTextEl.style.removeProperty(property);
            });
        }
        if (previewButtonLabelEl) {
            [
                'display',
                'width',
                'max-width',
                'white-space',
                'writing-mode',
                'line-height',
                'transform',
                'transform-origin'
            ].forEach(function (property) {
                previewButtonLabelEl.style.removeProperty(property);
            });
        }
        if (previewButtonIconEl) {
            [
                'display',
                'line-height',
                'margin',
                'margin-inline-start',
                'margin-inline-end'
            ].forEach(function (property) {
                previewButtonIconEl.style.removeProperty(property);
            });
        }

        const isOverlaySideTabPreview = isOverlayMode
            && (stickyPreviewPosition === 'left' || stickyPreviewPosition === 'right');
        const isOverlayVerticalTabPreview = isOverlayMode
            && (stickyPreviewPosition === 'top' || stickyPreviewPosition === 'bottom');
        if (isOverlaySideTabPreview) {
            const overlayTabTextAlign = stickyPreviewPosition === 'left' ? 'left' : 'right';
            const overlayTabPreviewClass = overlayFullTabHeight ? 'ess-overlay-full-height-side-tab-preview' : 'ess-overlay-side-tab-preview';
            preview.addClass(`${overlayTabPreviewClass} ess-overlay-tab-align-${overlayTabTextAlign}`);

            if (!overlayFullTabHeight) {
                const overlaySideTabAlignmentClass = alignmentValue === 'end'
                    ? 'ess-overlay-side-tab-align-bottom'
                    : (alignmentValue === 'center' ? 'ess-overlay-side-tab-align-center' : 'ess-overlay-side-tab-align-top');
                preview.addClass(overlaySideTabAlignmentClass);
            }

            if (overlayFullTabHeight && previewButtonWrapEl) {
                const previewContentHeight = Math.ceil(previewContentContainer.outerHeight() || 0);

                previewButtonWrapEl.style.setProperty('text-align', overlayTabTextAlign, 'important');
                previewButtonWrapEl.style.setProperty('display', 'flex', 'important');
                previewButtonWrapEl.style.setProperty('flex-direction', 'column', 'important');
                previewButtonWrapEl.style.setProperty('align-items', 'center', 'important');
                previewButtonWrapEl.style.setProperty('justify-content', tabContentJustifyValue, 'important');
                if (previewContentHeight > 0) {
                    previewButtonWrapEl.style.setProperty('height', `${previewContentHeight}px`, 'important');
                    previewButtonWrapEl.style.setProperty('min-height', `${previewContentHeight}px`, 'important');
                } else {
                    previewButtonWrapEl.style.setProperty('height', '100%', 'important');
                    previewButtonWrapEl.style.setProperty('min-height', '100%', 'important');
                }
            }

            if (overlayFullTabHeight && previewButtonTextEl) {
                previewButtonTextEl.style.setProperty('writing-mode', 'vertical-lr', 'important');
                previewButtonTextEl.style.setProperty('text-orientation', 'mixed', 'important');
                previewButtonTextEl.style.setProperty('transform', 'none', 'important');
                previewButtonTextEl.style.setProperty('transform-origin', 'center center', 'important');
                previewButtonTextEl.style.setProperty('left', '0', 'important');
                previewButtonTextEl.style.setProperty('width', 'auto', 'important');
                previewButtonTextEl.style.setProperty('min-width', '0', 'important');
                previewButtonTextEl.style.setProperty('max-width', 'none', 'important');
                previewButtonTextEl.style.setProperty('overflow', 'visible', 'important');
                previewButtonTextEl.style.setProperty('padding', '0', 'important');
                previewButtonTextEl.style.setProperty('line-height', '1.1', 'important');
                previewButtonTextEl.style.setProperty('text-align', overlayTabTextAlign, 'important');
            }

            if (overlayFullTabHeight && previewButtonLabelEl) {
                previewButtonLabelEl.style.setProperty('display', 'inline', 'important');
                if (buttonTextOrientation === 'bottom-to-top') {
                    previewButtonLabelEl.style.setProperty('display', 'inline-block', 'important');
                    previewButtonLabelEl.style.setProperty('transform', 'rotate(180deg)', 'important');
                    previewButtonLabelEl.style.setProperty('transform-origin', 'center center', 'important');
                }
            }

            if (overlayFullTabHeight && previewButtonIconEl) {
                previewButtonIconEl.style.setProperty('display', 'inline-block', 'important');
                previewButtonIconEl.style.setProperty('line-height', '1', 'important');
            }
        } else if (isOverlayVerticalTabPreview) {
            preview.addClass('ess-overlay-vertical-tab-preview');
            if (overlayFullTabHeight) {
                preview.addClass('ess-overlay-vertical-full-tab-preview');
            } else {
                const overlayVerticalTabAlignmentClass = alignmentValue === 'end'
                    ? 'ess-overlay-tab-align-right'
                    : (alignmentValue === 'center' ? 'ess-overlay-tab-align-center' : 'ess-overlay-tab-align-left');
                preview.addClass(overlayVerticalTabAlignmentClass);
            }

            if (previewButtonWrapEl) {
                previewButtonWrapEl.style.setProperty('display', 'flex', 'important');
                previewButtonWrapEl.style.setProperty('flex-direction', 'row', 'important');
                previewButtonWrapEl.style.setProperty('align-items', 'center', 'important');
                previewButtonWrapEl.style.setProperty('justify-content', tabContentJustifyValue, 'important');
                previewButtonWrapEl.style.removeProperty('height');
                previewButtonWrapEl.style.removeProperty('min-height');
                if (overlayFullTabHeight) {
                    previewButtonWrapEl.style.setProperty('align-self', 'stretch', 'important');
                    previewButtonWrapEl.style.setProperty('width', '100%', 'important');
                } else {
                    previewButtonWrapEl.style.setProperty('align-self', 'center', 'important');
                    previewButtonWrapEl.style.setProperty('width', 'auto', 'important');
                }
            }
            if (previewButtonTextEl) {
                previewButtonTextEl.style.setProperty('display', 'inline-flex', 'important');
                previewButtonTextEl.style.setProperty('flex-direction', 'row', 'important');
                previewButtonTextEl.style.setProperty('align-items', 'center', 'important');
                previewButtonTextEl.style.setProperty('justify-content', 'center', 'important');
                previewButtonTextEl.style.setProperty('gap', '8px', 'important');
                previewButtonTextEl.style.setProperty('writing-mode', 'horizontal-tb', 'important');
                previewButtonTextEl.style.setProperty('width', 'auto', 'important');
                previewButtonTextEl.style.setProperty('padding', '0', 'important');
                previewButtonTextEl.style.setProperty('overflow', 'visible', 'important');
            }
            if (previewButtonLabelEl) {
                previewButtonLabelEl.style.setProperty('position', 'static', 'important');
                previewButtonLabelEl.style.setProperty('display', 'inline', 'important');
                previewButtonLabelEl.style.setProperty('writing-mode', 'horizontal-tb', 'important');
                previewButtonLabelEl.style.setProperty('white-space', 'nowrap', 'important');
                previewButtonLabelEl.style.setProperty('width', 'auto', 'important');
                previewButtonLabelEl.style.removeProperty('transform');
            }
        } else {
            applyAlignStyles($('#ess-preview-button-wrap'));

            if (isVerticalPosition) {
                const textAlign = alignmentValue === 'start' ? 'left' : (alignmentValue === 'end' ? 'right' : 'center');
                $('#ess-preview-button-text').css('text-align', textAlign);
            }
        }

        const dividerThickness = parseInt(getValue('line_separator_thickness', ''), 10);
        $('#ess-preview-divider').css('height', Number.isNaN(dividerThickness) ? '' : `${Math.max(1, dividerThickness)}px`);

        const imageHeightEnabled = getChecked('enable_cta_height') || getValue('enable_cta_height', 'no') === 'yes';
        const imageHeightValue = parseFloat(getValue('cta_height', ''));
        const imageHeightUnit = (getValue('cta_height_unit', 'px') || 'px').toString();
        let imageHeightCss = '';
        if (imageHeightEnabled && !Number.isNaN(imageHeightValue) && imageHeightValue > 0) {
            if (imageHeightUnit === 'px') {
                imageHeightCss = `${Math.max(1, imageHeightValue)}px`;
            } else {
                imageHeightCss = `${imageHeightValue}${imageHeightUnit}`;
            }
        }
        $('#ess-preview-image-wrap').css('height', imageHeightCss);

        const buttonLetterSpacing = parseInt(getValue('letter_spacing', ''), 10);
        $('#ess-preview-button-text').css('letter-spacing', Number.isNaN(buttonLetterSpacing) ? '' : `${buttonLetterSpacing}px`);
        $('#ess-preview-tab-button-text').css('letter-spacing', Number.isNaN(buttonLetterSpacing) ? '' : `${buttonLetterSpacing}px`);
        $('#ess-preview-html-button-text').css('letter-spacing', Number.isNaN(buttonLetterSpacing) ? '' : `${buttonLetterSpacing}px`);

        const contentLetterSpacing = parseInt(getValue('content_letter_spacing', ''), 10);
        $('#ess-preview-content-text').css('letter-spacing', Number.isNaN(contentLetterSpacing) ? '' : `${contentLetterSpacing}px`);

        const linkLetterSpacing = parseInt(getValue('call_to_action_letter_spacing', ''), 10);
        $('#ess-preview-link').css('letter-spacing', Number.isNaN(linkLetterSpacing) ? '' : `${linkLetterSpacing}px`);

        const buttonPadding = getDimensionCss('button_padding');
        const sharedContentPadding = getDimensionCss('content_padding');
        $('#ess-preview-button-wrap').css('padding', buttonPadding || '');
        $('#ess-preview-tab-button').css('padding', buttonPadding || '');
        $('#ess-preview-html-button').css('padding', buttonPadding || '');
        $('#ess-preview-html-content-text').css('padding', sharedContentPadding || '');
        $('#ess-preview-gdpr-text').css('padding', sharedContentPadding || '');

        if (!isOverlayMode) {
            const classicDefaultContentPadding = '14px 24px 14px 24px';
            const resolvedContentPadding = sharedContentPadding || classicDefaultContentPadding;
            $('#ess-preview-content-text').css('padding', resolvedContentPadding);
            const previewContent = $('#ess-preview-content-text').get(0);
            if (previewContent && resolvedContentPadding) {
                previewContent.style.setProperty('padding', resolvedContentPadding, 'important');
            }

            const linkPadding = getDimensionCssSmart('call_to_action_padding');
            const classicDefaultLinkPadding = '14px 24px 14px 24px';
            const resolvedLinkPadding = linkPadding || classicDefaultLinkPadding;
            $('#ess-preview-link').css('padding', resolvedLinkPadding);
            const previewLink = $('#ess-preview-link').get(0);
            if (previewLink && resolvedLinkPadding) {
                previewLink.style.setProperty('padding', resolvedLinkPadding, 'important');
            }
        }

        const buttonRound = parseInt(getValue('button_round', ''), 10);
        if (!Number.isNaN(buttonRound)) {
            preview.css('--round', `${Math.max(0, buttonRound)}px`);
            $('#ess-preview-tab-cta').css('--round', `${Math.max(0, buttonRound)}px`);
            $('#ess-preview-html-cta').css('--round', `${Math.max(0, buttonRound)}px`);
        }
        $('#ess-preview-html-cta').css('--ess-overlay-tab-corner-radius', !Number.isNaN(overlayTabCornerRadius) && overlayTabCornerRadius >= 0 ? `${overlayTabCornerRadius}px` : '5px');

        const hideCallToAction = ['yes', 'Yes', '1', true].includes(getValue('hide_call_to_action', 'no')) ||
            getChecked('hide_call_to_action');
        const hideContentText = ['yes', 'Yes', '1', true].includes(getValue('hide_content_text', 'no')) ||
            getChecked('hide_content_text');

        $('#ess-preview-content-text').toggle(!hideContentText);
        $('#ess-preview-link').toggle(!hideCallToAction);
        if (hideCallToAction || hideContentText) {
            $('#ess-preview-divider').hide();
        }

        const collapseOnLoad = getChecked('collapse_on_page_load') || getValue('collapse_on_page_load', 'no') === 'yes';
        preview.toggleClass('shrink', collapseOnLoad);

        // Close button preview settings
        const showCloseButton = getChecked('show_close_button') || getValue('show_close_button', 'no') === 'yes';
        const closePosition = getValue('close_button_position', 'start') || 'start';
        const closeEdgeChecked = getChecked('close_button_edge') || getValue('close_button_edge', 'no') === 'yes';
        const closeEdge = closeEdgeChecked ? 'outside' : '';
        const closeColor = getValue('close_button_color', '#ffffff') || '#ffffff';
        const closeButtons = activePane.length ? activePane.find('.btn-ess-close') : previewCard.find('.btn-ess-close');

        closeButtons.toggle(!!showCloseButton);
        closeButtons.css('background-color', closeColor);

        const closeClassTargets = activePane.length ? activePane.find('.easy-sticky-sidebar') : $('#ess-preview-cta, #ess-preview-tab-cta, #ess-preview-html-cta, #ess-preview-banner, #ess-preview-gdpr');
        closeClassTargets.removeClass('ess-close-button-start ess-close-button-end ess-close-button-left ess-close-button-right ess-close-button-top-left ess-close-button-top-right ess-close-button-bottom-left ess-close-button-bottom-right ess-preview-outside-close')
            .addClass(`ess-close-button-${closePosition}`);
        closeButtons.removeClass('start end left right top-left top-right bottom-left bottom-right outside')
            .addClass(closePosition)
            .toggleClass('outside', closeEdge === 'outside');

        closeClassTargets.each(function () {
            const $target = $(this);
            if ($target.find('.btn-ess-close.outside').length) {
                $target.addClass('ess-preview-outside-close');
            }
        });

        // Keep preview shadow behavior in sync with frontend for free and pro.
        const shadowEnabled = getChecked('enable_box_shadow') || getValue('enable_box_shadow', 'no') === 'yes';
        const previewShadow = shadowEnabled ? '0 0 10px rgba(19, 19, 19, .2)' : '0 0 0 rgba(0, 0, 0, 0)';
        previewCard.find('.ess-preview-stage .easy-sticky-sidebar').css('--ess-preview-shadow', previewShadow);

        const tabPreview = $('#ess-preview-tab-cta');
        tabPreview
            .removeClass('sticky-cta-position-left sticky-cta-position-right sticky-cta-position-top sticky-cta-position-bottom vertical-cta vertical-cta-top vertical-cta-bottom ess-tab-text-bottom-to-top')
            .addClass(`sticky-cta-position-${ctaPosition}`);
        resetPreviewButtonMetrics(tabPreview);

        const stickySideTextOrientation = isOverlayMode
            ? (stickyPreviewPosition === 'left' || stickyPreviewPosition === 'right')
            : (ctaPosition === 'left' || ctaPosition === 'right');
        if (stickySideTextOrientation && buttonTextOrientation === 'bottom-to-top') {
            preview.addClass('ess-tab-text-bottom-to-top');
        }
        if ((ctaPosition === 'left' || ctaPosition === 'right') && buttonTextOrientation === 'bottom-to-top') {
            tabPreview.addClass('ess-tab-text-bottom-to-top');
        }

        if (ctaPosition === 'top' || ctaPosition === 'bottom') {
            tabPreview.addClass('vertical-cta').addClass(`vertical-cta-${ctaPosition}`);
        }

        const tabDefaultText = getEditorDefault('tab-cta', 'SSuprydp_button_option_text', 'Call Now');
        const tabButtonIconValue = getValue('button_icon', '');
        const tabButtonTextRaw = (buttonTextValue || '').toString().trim();
        const tabStaleDefaults = ['have questions?', 'click here'];
        const tabButtonTextValue = (
            activeTemplate === 'tab-cta' &&
            (!tabButtonTextRaw.length || tabStaleDefaults.includes(tabButtonTextRaw.toLowerCase()))
        )
            ? tabDefaultText
            : (tabButtonTextRaw.length ? buttonTextValue : tabDefaultText);
        if (activeTemplate === 'tab-cta' && tabButtonTextValue === tabDefaultText && buttonTextValue !== tabDefaultText) {
            form.find('[name="SSuprydp_button_option_text"]').val(tabDefaultText);
        }
        const tabButtonIconHtml = tabButtonIconValue ? `<i class="icon ${tabButtonIconValue}"></i>` : '';
        const tabButtonLabelHtml = `<span class="ess-sticky-sidebar-button-label">${tabButtonTextValue}</span>`;
        $('#ess-preview-tab-button-text').html(
            buttonIconPosition === 'after'
                ? `${tabButtonLabelHtml}${tabButtonIconHtml}`
                : `${tabButtonIconHtml}${tabButtonLabelHtml}`
        );
        $('#ess-preview-tab-button-text .icon').css('font-size', !Number.isNaN(buttonIconSize) && buttonIconSize > 0 ? `${buttonIconSize}px` : '16px');
        $('#ess-preview-tab-button').css('background-color', getValue('SSuprydp_button_option_backg_color', '#218400'));
        $('#ess-preview-tab-button-text').css('color', getValue('SSuprydp_button_option_color', '#ffffff'));

        if (!isNaN(buttonFontSize)) {
            $('#ess-preview-tab-button-text').css('font-size', `${buttonFontSize}px`);
        }

        applyPreviewFont('#ess-preview-tab-button-text', getValue('SSuprydp_button_option_font', 'Arial'), 'Arial');

        const floatingPreview = $('#ess-preview-floating-cta');
        floatingPreview
            .removeClass('sticky-cta-position-left sticky-cta-position-right sticky-cta-position-top sticky-cta-position-bottom vertical-cta vertical-cta-top vertical-cta-bottom')
            .addClass(`sticky-cta-position-${ctaPosition}`);
        // Keep floating preview stable inside admin canvas (no slide-out transform math).
        floatingPreview.css('top', '');
        floatingPreview.css('bottom', '');
        floatingPreview.css('--ess-preview-float-x', '0px');
        floatingPreview.css('--ess-preview-float-y', '0px');

        if (ctaPosition === 'top' || ctaPosition === 'bottom') {
            floatingPreview.addClass('vertical-cta').addClass(`vertical-cta-${ctaPosition}`);
        }

        const floatingButtons = collectIndexedValues('floating_buttons');
        const floatingStyles = collectIndexedValues('floating_button_style');
        const floatingList = $('#ess-preview-floating-list');
        const globalFloatColor = getValue('floating_button_color', '');
        const globalFloatHoverColor = getValue('floating_button_hover_color', '');
        const globalFloatBg = getValue('floating_button_background_color', '');
        const globalFloatHoverBg = getValue('floating_button_background_hover_color', '');
        const hideFloatText = getChecked('hide_floating_button_text') || getValue('hide_floating_button_text', 'no') === 'yes';

        const orderedFloatingButtons = Object.keys(floatingButtons)
            .sort((a, b) => parseInt(a, 10) - parseInt(b, 10))
            .map((key) => {
                const data = floatingButtons[key] || {};
                const style = floatingStyles[key] || {};
                return Object.assign({ id: key }, data, style);
            });

        floatingList.empty();
        floatingPreview.toggleClass('floating-button-no-text', hideFloatText);

        const floatWidth = parseInt(getNormalizedNumber('floating_button_width', ''), 10);
        if (!Number.isNaN(floatWidth) && floatWidth > 0) {
            floatingPreview.css('--button_width', `${floatWidth}px`);
        } else {
            floatingPreview.css('--button_width', '');
        }

        const floatFontSize = parseInt(getNormalizedNumber('floating_button_font_size', ''), 10);
        if (!Number.isNaN(floatFontSize) && floatFontSize > 0) {
            floatingPreview.css('font-size', `${floatFontSize}px`);
        } else {
            floatingPreview.css('font-size', '');
        }

        applyPreviewFont('#ess-preview-floating-list, #ess-preview-floating-list a', getValue('default_font_family', 'Arial'), 'Arial');

        const fallbackButtons = [
            { id: 0, text: 'Call Us', icon: 'fa-solid fa-phone' },
            { id: 1, text: 'Chat Now', icon: 'fa-solid fa-comment-dots' }
        ];

        const renderButtons = orderedFloatingButtons.length ? orderedFloatingButtons : fallbackButtons;
        renderButtons.forEach((button, index) => {
            const itemId = button.id !== undefined ? button.id : index;
            const item = $(`<li class="floating-button-${itemId}"></li>`);
            const color = button.color || globalFloatColor;
            const bg = button.background_color || globalFloatBg;
            const hoverColor = button.hover_color || globalFloatHoverColor;
            const hoverBg = button.background_hover_color || globalFloatHoverBg;

            if (color) {
                item.css('--color', color);
            }
            if (bg) {
                item.css('--background_color', bg);
            }
            if (hoverColor) {
                item.css('--hover_color', hoverColor);
            }
            if (hoverBg) {
                item.css('--background_hover_color', hoverBg);
            }

            const hasLink = !!(button.url && `${button.url}`.trim().length);
            if (hasLink) {
                item.addClass('has-link');
            }

            const nodes = [];
            if (button.icon) {
                nodes.push($(`<i class="icon ${button.icon}"></i>`)[0]);
            }

            if (!hideFloatText && button.text) {
                nodes.push(document.createTextNode(button.text));
            }

            if (hideFloatText && !button.icon) {
                nodes.push(document.createTextNode('Button'));
            }

            if (hasLink) {
                const link = $('<a></a>').attr('href', button.url || '#');
                nodes.forEach((node) => link.append(node));
                item.append(link);
            } else {
                nodes.forEach((node) => item.append(node));
            }

            floatingList.append(item);
        });

        const bannerPreview = $('#ess-preview-banner');
        const bannerText = $('#ess-preview-banner-text');
        const bannerLink = $('#ess-preview-banner-link');

        bannerText.text(stripText(getValue('SSuprydp_content_option_text', 'Your banner message goes here.')));
        // Keep banner preview defaults aligned with frontend defaults.
        const bannerBarBgDefault = '#000000';
        const bannerBarFgDefault = '#ffffff';
        const resolveBannerPreviewBackground = function (raw) {
            const trimmed = (raw || '').toString().trim();
            if (!trimmed) {
                return bannerBarBgDefault;
            }
            return trimmed;
        };
        bannerPreview.css({
            'background-color': resolveBannerPreviewBackground(getValue('content_background_color', '')),
            'color': getValue('SSuprydp_content_option_color', bannerBarFgDefault),
            'display': 'block',
            'justify-content': '',
            'align-items': '',
            'gap': ''
        });

        const bannerFontSize = parseInt(getValue('SSuprydp_content_option_size', ''), 10);
        bannerText.css('font-size', Number.isNaN(bannerFontSize) ? '' : `${bannerFontSize}px`);
        bannerText.css('letter-spacing', Number.isNaN(contentLetterSpacing) ? '' : `${contentLetterSpacing}px`);
        const bannerPadding = getDimensionCss('content_padding') || getDimensionCss('banner_content_padding');
        const bannerButtonPadding = getDimensionCss('banner_button_padding');
        const bannerButtonMargin = getDimensionCss('banner_button_margin');
        bannerPreview.css('padding', bannerPadding || '');

        bannerLink.text(getValue('SSuprydp_action_option_text', 'Learn More'));
        bannerLink.css({
            'color': getValue('SSuprydp_action_option_color', bannerBarFgDefault),
            'font-size': Number.isNaN(linkFontSize) ? '' : `${linkFontSize}px`,
            'letter-spacing': Number.isNaN(linkLetterSpacing) ? '' : `${linkLetterSpacing}px`,
            'margin': '',
            'padding': '',
            'border-radius': '',
            'display': ''
        });

        const linkBackground = getValue('link_text_background', '#1d5fd1');
        const isBannerButton = getChecked('call_to_action_button') || getValue('call_to_action_button', 'no') === 'yes';

        bannerLink.toggleClass('btn', isBannerButton);

        bannerLink.css('background-image', 'none');
        bannerLink.css('background-color', '');
        if (isBannerButton) {
            bannerLink.css('background-color', linkBackground);
            bannerLink.css('padding', bannerButtonPadding || '');
            bannerLink.css('margin', bannerButtonMargin || '');
            const bannerButtonRadius = parseInt(getValue('banner_button_border_radius', ''), 10);
            bannerLink.css('border-radius', Number.isNaN(bannerButtonRadius) ? '' : `${Math.max(0, bannerButtonRadius)}px`);
            bannerLink.css('display', 'inline-block');
        } else {
            bannerLink.css('padding', '');
            bannerLink.css('margin', bannerButtonMargin || '');
            bannerLink.css('border-radius', '');
            // Keep inline-block so link margins (including top/bottom) remain visible.
            bannerLink.css('display', 'inline-block');
        }

        bannerLink.toggle(!hideCallToAction);

        applyPreviewFont('#ess-preview-banner-text', getValue('SSuprydp_content_option_font', 'Open Sans'), 'Open Sans');
        applyPreviewFont('#ess-preview-banner-link', getValue('SSuprydp_action_option_font', 'Open Sans'), 'Open Sans');

        const gdprPreview = $('#ess-preview-gdpr');
        const gdprText = $('#ess-preview-gdpr-text');
        const gdprAccept = $('#ess-preview-gdpr-accept');
        const gdprDecline = $('#ess-preview-gdpr-decline');
        gdprPreview.css('--width', previewWidth);

        gdprText.text(stripText(getValue('SSuprydp_content_option_text', 'We use cookies to improve your experience.')));
        const gdprBoxRadius = parseInt(getValue('gdpr_box_radius', ''), 10);
        gdprPreview.css({
            'background-color': getValue('content_background_color', '#1f2937'),
            'color': getValue('SSuprydp_content_option_color', '#f8fafc'),
            'padding': sharedContentPadding || '30px 35px',
            'border-radius': Number.isNaN(gdprBoxRadius) ? '' : `${Math.max(0, gdprBoxRadius)}px`
        });
        const gdprPreviewNode = gdprPreview.get(0);
        if (gdprPreviewNode) {
            if (Number.isNaN(gdprBoxRadius)) {
                gdprPreviewNode.style.removeProperty('border-radius');
            } else {
                gdprPreviewNode.style.setProperty('border-radius', `${Math.max(0, gdprBoxRadius)}px`, 'important');
            }
            gdprPreviewNode.style.setProperty('overflow', 'hidden');
        }
        gdprText.css({
            'font-size': Number.isNaN(contentFontSize) ? '' : `${contentFontSize}px`,
            'letter-spacing': Number.isNaN(contentLetterSpacing) ? '' : `${contentLetterSpacing}px`,
            'color': getValue('SSuprydp_content_option_color', '#f8fafc')
        });
        applyPreviewFont('#ess-preview-gdpr-text', getValue('SSuprydp_content_option_font', 'Open Sans'), 'Open Sans');

        gdprAccept.text(getValue('SSuprydp_button_option_text', 'Got it.'));
        const acceptDefaultColor = getValue('SSuprydp_button_option_color', '#ffffff');
        const acceptDefaultBg = getValue('SSuprydp_button_option_backg_color', '#1d5fd1');
        const acceptHoverColor = getValue('button1_text_hover', '');
        const acceptHoverBg = getValue('button1_background_hover', '');
        gdprAccept.css({
            'background-color': acceptDefaultBg,
            'color': acceptDefaultColor,
            'font-size': Number.isNaN(buttonFontSize) ? '' : `${buttonFontSize}px`,
            'letter-spacing': Number.isNaN(buttonLetterSpacing) ? '' : `${buttonLetterSpacing}px`,
            'border-radius': Number.isNaN(buttonRound) ? '' : `${Math.max(0, buttonRound)}px`
        });
        gdprAccept.off('.essGdprHover')
            .on('mouseenter.essGdprHover', function () {
                if (acceptHoverColor) {
                    $(this).css('color', acceptHoverColor);
                }
                if (acceptHoverBg) {
                    $(this).css('background-color', acceptHoverBg);
                }
            })
            .on('mouseleave.essGdprHover', function () {
                $(this).css({
                    'color': acceptDefaultColor,
                    'background-color': acceptDefaultBg
                });
            });

        applyPreviewFont('#ess-preview-gdpr-accept', getValue('SSuprydp_button_option_font', 'Open Sans'), 'Open Sans');

        const button2Show = getChecked('button2_show') || getValue('button2_show', 'no') === 'yes';
        const button2Text = getValue('button2_text', '');
        gdprDecline.toggle(button2Show && !!button2Text);
        gdprDecline.text(button2Text || 'Decline');

        const button2FontSize = parseInt(getNormalizedNumber('button2_font_size', ''), 10);
        const button2LetterSpacing = parseInt(getValue('button2_letter_spacing', ''), 10);
        const button2Radius = parseInt(getValue('button2_radius', ''), 10);
        const declineDefaultBg = getValue('button2_background_color', '#111827');
        const declineDefaultColor = getValue('button2_text_color', '#ffffff');
        const declineHoverColor = getValue('button_decline_text_hover', '');
        const declineHoverBg = getValue('button_decline_background_hover', '');
        gdprDecline.css({
            'background-color': declineDefaultBg,
            'color': declineDefaultColor,
            'font-size': Number.isNaN(button2FontSize) ? '' : `${button2FontSize}px`,
            'letter-spacing': Number.isNaN(button2LetterSpacing) ? '' : `${button2LetterSpacing}px`,
            'border-radius': Number.isNaN(button2Radius) ? '' : `${Math.max(0, button2Radius)}px`
        });
        gdprDecline.off('.essGdprHover')
            .on('mouseenter.essGdprHover', function () {
                if (declineHoverColor) {
                    $(this).css('color', declineHoverColor);
                }
                if (declineHoverBg) {
                    $(this).css('background-color', declineHoverBg);
                }
            })
            .on('mouseleave.essGdprHover', function () {
                $(this).css({
                    'color': declineDefaultColor,
                    'background-color': declineDefaultBg
                });
            });

        applyPreviewFont('#ess-preview-gdpr-decline', getValue('button2_font_family', 'Open Sans'), 'Open Sans');

        const htmlPreview = $('#ess-preview-html-cta');
        htmlPreview
            .removeClass('sticky-cta-position-left sticky-cta-position-right sticky-cta-position-top sticky-cta-position-bottom vertical-cta vertical-cta-top vertical-cta-bottom ess-tab-text-bottom-to-top ess-html-full-tab-height ess-html-full-tab-width ess-html-side-tab-align-top ess-html-side-tab-align-center ess-html-side-tab-align-bottom ess-html-tab-align-left ess-html-tab-align-center ess-html-tab-align-right')
            .addClass(`sticky-cta-position-${ctaPosition}`);
        resetPreviewButtonMetrics(htmlPreview);

        if (ctaPosition === 'top' || ctaPosition === 'bottom') {
            htmlPreview.addClass('vertical-cta').addClass(`vertical-cta-${ctaPosition}`);
        }
        if (buttonTextOrientation === 'bottom-to-top' && (ctaPosition === 'left' || ctaPosition === 'right')) {
            htmlPreview.addClass('ess-tab-text-bottom-to-top');
        }
        if (ctaPosition === 'left' || ctaPosition === 'right') {
            if (htmlFullTabHeight) {
                htmlPreview.addClass('ess-html-full-tab-height');
            } else {
                const htmlSideAlignClass = alignmentValue === 'end'
                    ? 'ess-html-side-tab-align-bottom'
                    : (alignmentValue === 'center' ? 'ess-html-side-tab-align-center' : 'ess-html-side-tab-align-top');
                htmlPreview.addClass(htmlSideAlignClass);
            }
        } else if (ctaPosition === 'top' || ctaPosition === 'bottom') {
            if (htmlFullTabHeight) {
                htmlPreview.addClass('ess-html-full-tab-width');
            } else {
                const htmlVerticalAlignClass = alignmentValue === 'end'
                    ? 'ess-html-tab-align-right'
                    : (alignmentValue === 'center' ? 'ess-html-tab-align-center' : 'ess-html-tab-align-left');
                htmlPreview.addClass(htmlVerticalAlignClass);
            }
        }
        htmlPreview.css('--width', previewWidth);
        const htmlCornerRadius = !Number.isNaN(overlayTabCornerRadius) && overlayTabCornerRadius >= 0 ? overlayTabCornerRadius : 5;
        const htmlCornerRadiusCss = `${htmlCornerRadius}px`;
        let htmlContentRadius = '';
        if (ctaPosition === 'right') {
            htmlContentRadius = htmlFullTabHeight || alignmentValue === 'center'
                ? `${htmlCornerRadiusCss} 0 0 ${htmlCornerRadiusCss}`
                : (alignmentValue === 'end' ? `${htmlCornerRadiusCss} 0 0 0` : `0 0 0 ${htmlCornerRadiusCss}`);
        } else if (ctaPosition === 'left') {
            htmlContentRadius = htmlFullTabHeight || alignmentValue === 'center'
                ? `0 ${htmlCornerRadiusCss} ${htmlCornerRadiusCss} 0`
                : (alignmentValue === 'end' ? `0 ${htmlCornerRadiusCss} 0 0` : `0 0 ${htmlCornerRadiusCss} 0`);
        } else if (ctaPosition === 'top') {
            htmlContentRadius = `0 0 ${htmlCornerRadiusCss} ${htmlCornerRadiusCss}`;
        } else if (ctaPosition === 'bottom') {
            htmlContentRadius = `${htmlCornerRadiusCss} ${htmlCornerRadiusCss} 0 0`;
        }
        const htmlContentContainer = htmlPreview.find('.sticky-sidebar-container').first();
        if (htmlContentRadius && htmlContentContainer.length) {
            htmlContentContainer.css({
                'border-radius': htmlContentRadius,
                'overflow': 'hidden'
            });
        }

        const htmlButtonText = $('#ess-preview-html-button-text');
        const htmlButton = $('#ess-preview-html-button');
        const htmlContent = $('#ess-preview-html-content-text');

        const htmlButtonLabel = getValue('SSuprydp_button_option_text', '');
        const htmlButtonIcon = getValue('button_icon', '');
        const htmlButtonLabelHtml = htmlButtonLabel.length
            ? `<span class="ess-sticky-sidebar-button-label">${$('<div>').text(htmlButtonLabel).html()}</span>`
            : '';
        if (htmlButtonIcon) {
            const htmlButtonIconHtml = `<i class="icon ${htmlButtonIcon}"></i>`;
            const htmlButtonMarkup = buttonIconPosition === 'after'
                ? `${htmlButtonLabelHtml}${htmlButtonIconHtml}`
                : `${htmlButtonIconHtml}${htmlButtonLabelHtml}`;
            htmlButtonText.html(htmlButtonMarkup);
        } else {
            htmlButtonText.html(htmlButtonLabelHtml || $('<div>').text(htmlButtonLabel).html());
        }
        htmlButtonText.find('.icon').css('font-size', !Number.isNaN(buttonIconSize) && buttonIconSize > 0 ? `${buttonIconSize}px` : '16px');
        const rawHtmlValue = `${getValue('SSuprydp_content_option_text', '') || ''}`.trim();
        if (rawHtmlValue.length) {
            htmlContent.html(rawHtmlValue);
        } else {
            htmlContent.text('Add your HTML or iframe content here.');
        }
        htmlContent.toggle(!hideContentText);

        const htmlButtonBg = getValue('SSuprydp_button_option_backg_color', '#2466d5');

        htmlButton.css('background-image', 'none');
        htmlButton.css('background-color', htmlButtonBg);

        htmlButtonText.css('color', getValue('SSuprydp_button_option_color', '#ffffff'));
        const htmlContentColor = getValue('SSuprydp_content_option_color', '#000000');
        htmlContent.css({
            'background-color': getValue('content_background_color', '#ffffff'),
            'color': htmlContentColor
        });
        htmlContent.find('*').each(function () {
            this.style.setProperty('color', htmlContentColor, 'important');
        });

        if (!isNaN(buttonFontSize)) {
            htmlButtonText.css('font-size', `${buttonFontSize}px`);
        }

        // HTML/iframe mode keeps content sizing from user HTML/CSS, not global font-size control.

        applyPreviewFont('#ess-preview-html-button-text', getValue('SSuprydp_button_option_font', 'Open Sans'), 'Open Sans');
        applyPreviewFont('#ess-preview-html-content-text', getValue('SSuprydp_content_option_font', 'Open Sans'), 'Open Sans');

        const ctaHeightEnabled = getChecked('enable_cta_height') || getValue('enable_cta_height', 'no') === 'yes';
        const ctaHeightValue = parseFloat(getValue('cta_height', ''));
        const ctaHeightUnit = (getValue('cta_height_unit', 'px') || 'px').toString();
        const resolvedCtaHeight = (ctaHeightEnabled && !Number.isNaN(ctaHeightValue) && ctaHeightValue > 0)
            ? `${ctaHeightValue}${ctaHeightUnit}`
            : '';

        const previewHeightTargets = $('#ess-preview-cta, #ess-preview-tab-cta, #ess-preview-html-cta, #ess-preview-banner, #ess-preview-gdpr');
        previewHeightTargets.css('height', resolvedCtaHeight);

        // Keep image/background height independent from CTA wrapper height.
        const previewStickyContent = $('#ess-preview-cta .sticky-sidebar-content');
        if (resolvedCtaHeight) {
            previewStickyContent.css({
                'height': '100%',
                'min-height': '0'
            });
        } else {
            previewStickyContent.css({
                'height': '',
                'min-height': ''
            });
        }

        const collapseOnPageLoad = getValue('collapse_on_page_load', 'no') === 'yes';
        htmlPreview.toggleClass('shrink', collapseOnPageLoad);

        requestAnimationFrame(() => {
            syncPreviewButtonMetrics();
            syncPreviewOverlayBackgroundOffsets($('#ess-preview-cta'));
            syncPreviewStageHeight();
        });

        // Guard pass: on some sites another late script mutates preview styles
        // after render completion. Re-assert classic sticky link/content paddings.
        const enforceClassicStickyPreviewPadding = function () {
            if (activeTemplate !== 'sticky-cta' || isOverlayMode) {
                return;
            }

            const contentPadding = getDimensionCss('content_padding') || '14px 24px 14px 24px';
            const linkPadding = getDimensionCss('call_to_action_padding') || '14px 24px 14px 24px';
            const contentEl = $('#ess-preview-content-text').get(0);
            const linkEl = $('#ess-preview-link').get(0);

            if (contentEl) {
                contentEl.style.setProperty('padding', contentPadding, 'important');
            }
            if (linkEl) {
                linkEl.style.setProperty('padding', linkPadding, 'important');
            }
        };
        // Do not run this continuously during editing; only run during/after
        // explicit preview loading cycles where late third-party scripts can override styles.
        if (showSpinner) {
            setTimeout(enforceClassicStickyPreviewPadding, 0);
            setTimeout(enforceClassicStickyPreviewPadding, 60);
        }

        const finishPreviewLoading = function () {
            if (!showSpinner) {
                return;
            }
            enforceClassicStickyPreviewPadding();
            previewCard.removeClass('is-preview-loading').attr('aria-busy', 'false');
        };

        if (showSpinner) {
            const previewImageUrl = image.length ? image : '';
            const imageChanged = !!previewImageUrl && previewImageUrl !== lastPreviewImageUrl;
            const shouldWaitForImage = (isInitialLoad || waitForHeroImage) && imageChanged;

            if (previewImageUrl) {
                lastPreviewImageUrl = previewImageUrl;
            }

            if (shouldWaitForImage) {
                const preload = new Image();
                preload.onload = preload.onerror = function () {
                    previewSpinnerTimer = setTimeout(finishPreviewLoading, 40);
                };
                preload.src = previewImageUrl;
            } else {
                previewSpinnerTimer = setTimeout(finishPreviewLoading, 40);
            }
        }
    };

    if (window.EasyStickySidebar) {
        window.EasyStickySidebar.applyTemplatePreviewRefresh = function () {
            updatePreview({ waitForHeroImage: true });
        };
    }

    form.on('input change', 'input, textarea, select', function (e) {
        if (window.EasyStickySidebar && window.EasyStickySidebar.previewSuspended) {
            return;
        }
        const target = e.target;
        if (target && target.name === 'sidebar_name') {
            return;
        }

        // Height should update immediately while typing (no blur required).
        if (target && (target.name === 'cta_height' || target.name === 'cta_tablet_height' || target.name === 'cta_mobile_height') && e.type === 'input') {
            if (previewInputDebounceTimer) {
                clearTimeout(previewInputDebounceTimer);
            }
            updatePreview({ showSpinner: false });
            return;
        }

        // Fast path for normal editing: no spinner and debounce typing to reduce work.
        if (e.type === 'input') {
            if (previewInputDebounceTimer) {
                clearTimeout(previewInputDebounceTimer);
            }
            previewInputDebounceTimer = setTimeout(function () {
                updatePreview({ showSpinner: false });
            }, 60);
            return;
        }

        updatePreview({ showSpinner: false });
    });

    form.on('change', '#sticky_s_media, #image_attachment_id, [name="sidebar_template"]', function (e) {
        const target = e && e.target ? e.target : null;
        if (target && target.name === 'sticky_s_media') {
            syncContentImageThumbnail($(target).val());
        }
        if (window.EasyStickySidebar && window.EasyStickySidebar.previewSuspended) {
            return;
        }
        updatePreview({ showSpinner: true, waitForHeroImage: true });
    });

    // Font selector dropdowns update values programmatically; ensure preview refreshes immediately.
    form.on('click', '.font-select a, .font-select li', function () {
        setTimeout(function () {
            if (window.EasyStickySidebar && window.EasyStickySidebar.previewSuspended) {
                return;
            }
            updatePreview({ showSpinner: false });
        }, 0);
    });

    // Preview-only interaction: mimic frontend hide/show by toggling shrink.
    previewCard.on('click', '.ess-preview-template.ess-preview-sticky .sticky-sidebar-button, .ess-preview-template.ess-preview-html .sticky-sidebar-button', function (e) {
        e.preventDefault();
        e.stopPropagation();
        const preview = $(this).closest('.easy-sticky-sidebar');
        applyPreviewButtonMetrics(preview, $(this));
        preview.toggleClass('shrink');
    });

    setStepState();
    moveActiveNavBackground(false);
    updatePreview();

});

if (window.wp && window.wp.hooks && typeof window.wp.hooks.addAction === 'function') {
    window.wp.hooks.addAction('easy_sticky_sidebar_updated', 'easy-sticky-sidebar/free-tab-alignment', function (stickycta) {
        const alignmentField = jQuery('[name="button_alignment"]');
        if (!alignmentField.length) {
            return;
        }

        const template = `${stickycta?.options?.sidebar_template || ''}`.toLowerCase();
        if (!['sticky-cta', 'tab-cta', 'html'].includes(template)) {
            return;
        }

        let labels = ['Top', 'Center', 'Bottom'];
        const position = `${stickycta?.options?.SSuprydp_cta_position || ''}`.toLowerCase();
        if (position === 'top' || position === 'bottom') {
            labels = ['Left', 'Center', 'Right'];
        }

        labels.forEach(function (label, index) {
            alignmentField.find('option').eq(index).text(label);
        });
    });
}
