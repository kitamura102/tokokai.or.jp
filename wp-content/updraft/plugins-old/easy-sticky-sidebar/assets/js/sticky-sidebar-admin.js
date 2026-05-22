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
    height = $(".sticky-sidebar-button").height();
    $(".sticky-sidebar-button div").css("width", height);

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
                ajaxurl + "?action=process_pages",
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
            var formData = new FormData($(form)[0]);

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

        $.cookie('easy-sticky-sidebar-import-export', href);

        tab_navs.removeClass('active');
        $(this).addClass('active');

        tab_items.not(next_tab).hide();
        next_tab.show();
    })

    export_tab_item = $.cookie('easy-sticky-sidebar-import-export');
    if (export_tab_item) {
        $(`.easy-sticky-sidebar-tab-panel > .tab-nav a[href="${export_tab_item}"]`).trigger('click')
    }

    $('ul.export-cta-list [data-select="all"]').on('change', function () {
        checked = $(this).is(':checked');
        $('ul.export-cta-list input[type="checkbox"]').not($(this)).prop('checked', checked)
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
            }

            positions = wp.hooks.applyFilters('easy_sticky_sidebar_close_button_positions', positions);
            const current_position = $('[name="close_button_position"]').attr('data-position');

            const options = Object.keys(positions).map((key) => {
                const selected_attr = key == current_position ? "selected" : "";
                return `<option value="${key}" ${selected_attr}>${positions[key]}</option>`;
            });

            $('[name="close_button_position"]').html(options.join(""));
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
                action: "change_sticky_sidebar_name",
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
            action: "update_cta_status",
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

    var CTA_Button_Options_HTML = $('#sticky-sidebar-button-options > summary h2').html();


    const positon_options = $('[name="SSuprydp_cta_position"]').html();
    $('[name="sidebar_template"]').on("update", function (event, form_data) {
        if (form_data.template == 'banner') {
            $('#cta-content-options').show();
            $('#cta-link-text-options').show();

            current_position = $('[name="SSuprydp_cta_position"]').data('position');
            options = ['Top', 'Bottom'].map((pos) => {
                selected = current_position == pos.toLowerCase() ? 'selected' : '';
                return `<option value="${pos.toLowerCase()}" ${selected}>${pos}</option>`;
            })

            $('[name="SSuprydp_cta_position"]').html(options.join(''))
        } else {
            $('[name="SSuprydp_cta_position"]').html(positon_options)
        }
    })


    $('[name="sidebar_template"]').on("change", function () {
        const form_data = Object.assign({}, {
            template: $(this).val()
        });

        EasyStickySidebar.set_state('template', $(this).val())

        $("#SSuprydp_form").attr("data-template", form_data.template);

        if (form_data.template == "tab-cta") {
            $('#sticky-sidebar-button-options > summary h2').html('Tab Button Options')
        } else {
            $('#sticky-sidebar-button-options > summary h2').html(CTA_Button_Options_HTML)
        }

        $('[name="sidebar_template"]').trigger("update", form_data);

        $('#SSuprydp_form').trigger('update', form_data);

        $('.wordpress-cta-content-container > *').each(function (index) {
            order = (index + 1) * 2;
            $(this).css({ order })
        })
    }).trigger("change");

    // const Easy_Sticky_Sidebar_Positions = {
    //     position1: 'right',
    //     position2: 'center',

    //     get_position2_positions: function () {

    //     },

    //     set_position: function (value, target_position = 'position2') {
    //         this[target_position] = value;

    //         this.update_template();
    //     },


    //     update_template: function () {
    //         const self = this;

    //         let positions = { left: 'Left', center: 'Center', right: 'Right' }
    //         if (this.position1 == "left" || this.position1 == "right") {
    //             let positions = { top: "Top", center: "Center", bottom: "Bottom" };
    //         }

    //         const options = Object.keys(positions).map((key) => {
    //             const selected_attr = key == self.position2 ? "selected" : "";
    //             return `<option value="${key}" ${selected_attr}>${positions[key]}</option>`;
    //         });

    //         $('[name="horizontal_vertical_position"]').html(options.join(""));
    //     },

    //     init: function () {

    //     }
    // }


    $('[name="SSuprydp_cta_position"]').on("change", function () {
        // cta_position = $(this).val();

        // $(this).attr('data-position', cta_position)

        // positions = { left: 'Left', center: 'Center', right: 'Right' }
        // if (cta_position == "left" || cta_position == "right") {
        //     positions = { top: "Top", center: "Center", bottom: "Bottom" };
        // }

        // const current_position = $('[name="horizontal_vertical_position"]').attr("data-position");

        // const options = Object.keys(positions).map((key) => {
        //     const selected_attr = key == current_position ? "selected" : "";
        //     return `<option value="${key}" ${selected_attr}>${positions[key]}</option>`;
        // });

        // $('[name="horizontal_vertical_position"]').html(options.join(""));

        // //text alignment option fields
        // button_text_alignment = $('[name="SSuprydp_button_option_align"]');


        // text_alignments = { left: 'Top', center: 'Center', right: 'Bottom' };
        // if (cta_position == "top" || cta_position == "bottom") {
        //     text_alignments = Object.assign(text_alignments, { left: 'Left', right: 'Right' });
        // }

        // const button_text_alignment_options = Object.keys(text_alignments).map((key) => {
        //     const selected_attr = key == button_text_alignment.attr('data-align') ? "selected" : "";
        //     return `<option value="${key}" ${selected_attr}>${text_alignments[key]}</option>`;
        // });

        // button_text_alignment.html(button_text_alignment_options.join(""));

        // $('[name="horizontal_vertical_position"]').trigger('change')

    }).trigger("change");

    $('[name="horizontal_vertical_position"]').on('change', function () {
        const current_position = $(this).val();
        $(this).attr('data-position', current_position)
        $('#cta-horizontal-vertical-position').attr('data-position', current_position);
        $('#cta-horizontal-vertical-position .position2_distance-wrapper > label').attr('data-position', current_position);
    })

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


    const DesignTemplate = {
        template: null,

        get_values: function (style_only = false) {
            let values = {}

            try {
                values = JSON.parse(this.template)
                if (typeof values !== 'object') {
                    values = {};
                }

            } catch (error) {
                return {}
            }

            if (style_only === false) {
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

            $('#cta-premade-style').on('change', function (e) {
                $('#wordpress-cta-popup-load-design').trigger('open');
                self.template = $(this).val();
            });

            $('#wordpress-cta-popup-load-design .btn-cancel').on('click', function (e) {
                e.preventDefault();
                $('#wordpress-cta-popup-load-design').trigger('close');
                self.template = {};
            })

            $('#wordpress-cta-popup-load-design .btn-wordpress-cta-primary').on('click', function (e) {
                e.preventDefault();

                const style_only = $(this).attr('href') === '#load-style';
                const values = self.get_values(style_only);

                Object.keys(values).forEach((key) => {
                    input = $(`[name="${key}"]`);
                    if ($(`[name="${key}"][type="checkbox"]`).length) {
                        input = $(`[name="${key}"][type="checkbox"]`);
                    }

                    if (input.length) {
                        type = input.attr('type');

                        if (type == 'checkbox') {
                            const checked = ['Yes', 'yes', true].includes(values[key]) ? true : false;
                            input.prop('checked', checked).trigger('change');

                        } else {
                            input.prop('value', values[key]).trigger('change').trigger('input');
                            if (input.hasClass('wp-color-picker')) {
                                input.iris('color', values[key]);
                            }

                            const is_font = input.next('.font-select');
                            if (is_font.length) {
                                input.trigger('setFont', values[key]);
                            }
                        }
                    }
                })

                $('#image-preview').prop('src', $('#sticky_s_media').val());
                $('#wordpress-cta-popup-load-design').trigger('close');
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

            $('.wordpress-cta-pro-element, .wordpress-cta-pro-feature-inline').on('click', function (e) {
                e.preventDefault();
                $('#wordpress-cta-pro-feature-popup').trigger('open');
            })

            self.container.on('open', function (event, data) {
                const popup_text = Object.assign({ heading: self.heading, description: self.description }, data)
                self.container.find('.pro-title').html(popup_text.heading)
                if (popup_text.description) {
                    self.container.find('.pro-description').html(popup_text.description)
                }

                $('body').addClass('has-wordpress-cta-popup');
                $(this).addClass('active');
            })

            self.container.on('close', function () {
                $('body').removeClass('has-wordpress-cta-popup')
                $(this).removeClass('active');
            })

            self.container.on('click', function (e) {
                if (self.container.is(e.target)) {
                    self.container.trigger('close')
                }
            })

            self.container.on('click', '.close', function () {
                $(this).closest('.wordpress-cta-popup').trigger('close')
            })

            $(document).on('keydown', function (e) {
                if (e.keyCode === 27) { // ESC
                    self.container.trigger('close')
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
                // Ensure value is inside main form for saving.
                const form = $('#SSuprydp_form');
                if (form.length) {
                    let hidden = form.find('input[name="button_icon"]');
                    if (!hidden.length) {
                        hidden = $('<input type="hidden" name="button_icon" />').appendTo(form);
                    }
                    hidden.val(icon_class);
                }
            }
        });
    });

    // Ensure button_icon is part of the main form even before any new selection.
    const syncButtonIconToForm = () => {
        const form = $('#SSuprydp_form');
        if (!form.length) {
            return;
        }
        const iconInput = form.find('.icon-library-select-button input[name="button_icon"]');
        if (!iconInput.length) {
            return;
        }
        let hidden = form.find('input[name="button_icon"].ess-button-icon-hidden');
        if (!hidden.length) {
            hidden = $('<input type="hidden" class="ess-button-icon-hidden" name="button_icon" />').appendTo(form);
        }
        hidden.val(iconInput.val() || '');
    };

    $(document).on('input change', '.icon-library-select-button input[name="button_icon"]', syncButtonIconToForm);
    syncButtonIconToForm();

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
        syncButtonIconToForm();
    });


    const FloatingButton = {
        buttons: {},
        button_default_args: {},
        container: $('#floating-buttons-options'),
        button_container: $('#floating-buttons-options .floating-buttons'),

        update_buttons: function (button_id = 0, event = 'update') {
            const tabs = $('#floating-single-button-styles').find('.easy-sticky-sidebar-fieldset-floating-button');
            const current_tab = tabs.filter(`[data-id="${button_id}"]`);

            if (event === 'remove') {
                current_tab.remove();
            }

            const button_args = { ...this.button_default_args, ...this.buttons[button_id] };


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
                const button_html = button_template({ button_no: next_button_no });
                self.button_container.append(button_html);
                self.buttons[next_button_no] = self.button_default_args;
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

    const stripText = function (value) {
        return $('<div>').html(value || '').text().trim();
    };

    const getValue = function (name, fallback = '') {
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

    const updatePreview = function () {
        const isInitialLoad = !previewInitialized;
        previewInitialized = true;

        previewCard.addClass('is-preview-loading').attr('aria-busy', 'true');
        if (previewSpinnerTimer) {
            clearTimeout(previewSpinnerTimer);
        }
        const template = getValue('sidebar_template', 'sticky-cta');
        const supportedTemplates = ['sticky-cta', 'tab-cta', 'banner', 'gdpr', 'html', 'floating-buttons'];
        const activeTemplate = supportedTemplates.includes(template) ? template : 'sticky-cta';
        const isStickyTemplate = activeTemplate === 'sticky-cta';
        const isProActive = typeof has_easy_sticky_sidebar_pro === 'function' ? has_easy_sticky_sidebar_pro() : false;
        const allowedPositions = ['left', 'right', 'top', 'bottom'];
        const allowedAlignments = ['top', 'center', 'bottom'];

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

        // Resize preview stage to fit the active template to avoid clipping.
        const activeHeight = activePane.length ? activePane.outerHeight(true) : 0;
        if (activeHeight && activeHeight > 0) {
            const nextHeight = Math.max(220, activeHeight + 32);
            previewStage.css('--ess-preview-height', `${nextHeight}px`);
        }

        $('.ess-template-label').text(form.find('[name="sidebar_template"] option:selected').text().replace(/\s+\(.*\)$/, ''));

        let ctaPosition = getValue('SSuprydp_cta_position', 'right');
        let ctaAlignment = getValue('horizontal_vertical_position', 'top');

        if (!allowedPositions.includes(ctaPosition)) {
            ctaPosition = 'right';
        }

        if (!allowedAlignments.includes(ctaAlignment)) {
            ctaAlignment = 'top';
        }

        if (!isProActive) {
            ctaPosition = 'right';
            ctaAlignment = 'top';
        }

        // Map alignment for top/bottom positions: top->left, center->center, bottom->right.
        let anchorAlign = ctaAlignment;
        if (ctaPosition === 'top' || ctaPosition === 'bottom') {
            anchorAlign = ctaAlignment === 'top' ? 'left' : (ctaAlignment === 'bottom' ? 'right' : 'center');
        }

        $('.ess-preview-anchor')
            .attr('data-position', ctaPosition)
            .attr('data-align', anchorAlign);

        const preview = $('#ess-preview-cta');
        preview
            .removeClass('sticky-cta-position-left sticky-cta-position-right sticky-cta-position-top sticky-cta-position-bottom vertical-cta vertical-cta-top vertical-cta-bottom')
            .addClass(`sticky-cta-position-${ctaPosition}`);
        resetPreviewButtonMetrics(preview);

        if (ctaPosition === 'top' || ctaPosition === 'bottom') {
            preview.addClass('vertical-cta').addClass(`vertical-cta-${ctaPosition}`);
        }
        const enableCtaWidth = getChecked('enable_cta_width') || getValue('enable_cta_width', 'no') === 'yes';
        const ctaWidthValue = parseInt(getValue('cta_width', ''), 10);
        const ctaWidthUnit = getValue('cta_width_unit', 'px') || 'px';
        const previewWidth = enableCtaWidth && !Number.isNaN(ctaWidthValue) && ctaWidthValue > 0 ? `${ctaWidthValue}${ctaWidthUnit}` : '';
        preview.css('--width', previewWidth);

        const buttonTextValue = getValue('SSuprydp_button_option_text', '');
        const buttonIconValue = getValue('button_icon', '');
        if (buttonIconValue) {
            $('#ess-preview-button-text').html(`<i class="icon ${buttonIconValue}"></i> ${buttonTextValue}`);
        } else {
            $('#ess-preview-button-text').text(buttonTextValue);
        }
        $('#ess-preview-content-text').text(stripText(getValue('SSuprydp_content_option_text', 'This is the content area for your sticky CTA.')));
        $('#ess-preview-link').text(getValue('SSuprydp_action_option_text', 'Click Here to View'));

        const image = getValue('sticky_s_media', '');
        if (image.length) {
            $('#ess-preview-image-wrap').css('background-image', `url("${image}")`).show();
        } else {
            $('#ess-preview-image-wrap').hide();
        }

        const hideImage = getChecked('hide_cta_image')
            || getValue('hide_cta_image', 'no') === 'yes'
            || getChecked('SSuprydp_img_hideimg')
            || getValue('SSuprydp_img_hideimg', 'No') === 'Yes';
        if (hideImage) {
            $('#ess-preview-image-wrap').hide();
        }

        const imageOverlayEnabled = getChecked('enable_image_overlay') || getValue('enable_image_overlay', 'no') === 'yes';
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

        $('#ess-preview-button-wrap').css('background-color', getValue('SSuprydp_button_option_backg_color', '#2466d5'));
        $('#ess-preview-button-text').css('color', getValue('SSuprydp_button_option_color', '#ffffff'));
        $('#ess-preview-content-text').css({
            'background-color': getValue('content_background_color', '#ffffff'),
            'color': getValue('SSuprydp_content_option_color', '#1a2940')
        });
        $('#ess-preview-link').css({
            'background-color': getValue('link_text_background', '#e8eef8'),
            'color': getValue('SSuprydp_action_option_color', '#1c3f87')
        });

        const showLine = getChecked('line_separator_show');
        if (showLine) {
            $('#ess-preview-divider').show().css('background-color', getValue('line_separator_color', '#c7d7ef'));
        } else {
            $('#ess-preview-divider').hide();
        }

        const buttonFontSize = parseInt(getNormalizedNumber('SSuprydp_button_option_size', '18'), 10);
        const contentFontSize = parseInt(getNormalizedNumber('SSuprydp_content_option_size', '17'), 10);
        const linkFontSize = parseInt(getNormalizedNumber('SSuprydp_action_option_size', '15'), 10);

        if (!isNaN(buttonFontSize)) {
            $('#ess-preview-button-text').css('font-size', `${buttonFontSize}px`);
        }

        if (!isNaN(contentFontSize)) {
            $('#ess-preview-content-text').css('font-size', `${contentFontSize}px`);
        }

        if (!isNaN(linkFontSize)) {
            $('#ess-preview-link').css('font-size', `${linkFontSize}px`);
        }

        // Keep preview font rendering in sync with style tab Google font values.
        applyPreviewFont('#ess-preview-button-text', getValue('SSuprydp_button_option_font', 'Open Sans'), 'Open Sans');
        applyPreviewFont('#ess-preview-content-text', getValue('SSuprydp_content_option_font', 'Open Sans'), 'Open Sans');
        applyPreviewFont('#ess-preview-link', getValue('SSuprydp_action_option_font', 'Open Sans'), 'Open Sans');

        const isVerticalPosition = ctaPosition === 'top' || ctaPosition === 'bottom';
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
        if (isVerticalPosition) {
            const textAlign = alignmentValue === 'start' ? 'left' : (alignmentValue === 'end' ? 'right' : 'center');
            $('#ess-preview-button-text').css('text-align', textAlign);
            $('#ess-preview-tab-button-text').css('text-align', textAlign);
            $('#ess-preview-html-button-text').css('text-align', textAlign);
        }

        const dividerThickness = parseInt(getValue('line_separator_thickness', ''), 10);
        $('#ess-preview-divider').css('height', Number.isNaN(dividerThickness) ? '' : `${Math.max(1, dividerThickness)}px`);

        const imageHeight = parseInt(getValue('cta_image_height', ''), 10);
        $('#ess-preview-image-wrap').css('height', Number.isNaN(imageHeight) ? '' : `${Math.max(1, imageHeight)}px`);

        const buttonLetterSpacing = parseInt(getValue('letter_spacing', ''), 10);
        $('#ess-preview-button-text').css('letter-spacing', Number.isNaN(buttonLetterSpacing) ? '' : `${buttonLetterSpacing}px`);
        $('#ess-preview-tab-button-text').css('letter-spacing', Number.isNaN(buttonLetterSpacing) ? '' : `${buttonLetterSpacing}px`);
        $('#ess-preview-html-button-text').css('letter-spacing', Number.isNaN(buttonLetterSpacing) ? '' : `${buttonLetterSpacing}px`);

        const contentLetterSpacing = parseInt(getValue('content_letter_spacing', ''), 10);
        $('#ess-preview-content-text').css('letter-spacing', Number.isNaN(contentLetterSpacing) ? '' : `${contentLetterSpacing}px`);

        const linkLetterSpacing = parseInt(getValue('call_to_action_letter_spacing', ''), 10);
        $('#ess-preview-link').css('letter-spacing', Number.isNaN(linkLetterSpacing) ? '' : `${linkLetterSpacing}px`);

        const buttonPadding = getDimensionCss('button_padding');
        $('#ess-preview-button-wrap').css('padding', buttonPadding || '');
        $('#ess-preview-tab-button').css('padding', buttonPadding || '');
        $('#ess-preview-html-button').css('padding', buttonPadding || '');

        const contentPadding = getDimensionCss('content_padding');
        $('#ess-preview-content-text').css('padding', contentPadding || '');

        const linkPadding = getDimensionCss('call_to_action_padding');
        $('#ess-preview-link').css('padding', linkPadding || '');

        const buttonRound = parseInt(getValue('button_round', ''), 10);
        if (!Number.isNaN(buttonRound)) {
            preview.css('--round', `${Math.max(0, buttonRound)}px`);
            $('#ess-preview-tab-cta').css('--round', `${Math.max(0, buttonRound)}px`);
            $('#ess-preview-html-cta').css('--round', `${Math.max(0, buttonRound)}px`);
        }

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
        const closeColor = getValue('close_button_color', '#000000') || '#000000';
        const closeButtons = activePane.length ? activePane.find('.btn-ess-close') : previewCard.find('.btn-ess-close');

        closeButtons.toggle(!!showCloseButton);
        closeButtons.css('background-color', closeColor);

        const closeClassTargets = activePane.length ? activePane.find('.easy-sticky-sidebar') : $('#ess-preview-cta, #ess-preview-tab-cta, #ess-preview-html-cta, #ess-preview-banner, #ess-preview-gdpr');
        closeClassTargets.removeClass('ess-close-button-start ess-close-button-end ess-preview-outside-close')
            .addClass(`ess-close-button-${closePosition}`);
        closeButtons.removeClass('start end outside')
            .addClass(closePosition)
            .toggleClass('outside', closeEdge === 'outside');

        closeClassTargets.each(function () {
            const $target = $(this);
            if ($target.find('.btn-ess-close.outside').length) {
                $target.addClass('ess-preview-outside-close');
            }
        });

        // Box shadow toggle (Pro)
        const shadowEnabled = getChecked('enable_box_shadow') || getValue('enable_box_shadow', 'no') !== 'no';
        const previewShadow = shadowEnabled ? '0 0 10px rgba(19, 19, 19, .2)' : '0 0 0 rgba(0, 0, 0, 0)';
        previewCard.find('.ess-preview-stage .easy-sticky-sidebar').css('--ess-preview-shadow', previewShadow);

        const tabPreview = $('#ess-preview-tab-cta');
        tabPreview
            .removeClass('sticky-cta-position-left sticky-cta-position-right sticky-cta-position-top sticky-cta-position-bottom vertical-cta vertical-cta-top vertical-cta-bottom')
            .addClass(`sticky-cta-position-${ctaPosition}`);
        resetPreviewButtonMetrics(tabPreview);

        if (ctaPosition === 'top' || ctaPosition === 'bottom') {
            tabPreview.addClass('vertical-cta').addClass(`vertical-cta-${ctaPosition}`);
        }

        const tabButtonIconValue = getValue('button_icon', '');
        if (tabButtonIconValue) {
            $('#ess-preview-tab-button-text').html(`<i class="icon ${tabButtonIconValue}"></i> ${buttonTextValue}`);
        } else {
            $('#ess-preview-tab-button-text').text(buttonTextValue);
        }
        $('#ess-preview-tab-button').css('background-color', getValue('SSuprydp_button_option_backg_color', '#2466d5'));
        $('#ess-preview-tab-button-text').css('color', getValue('SSuprydp_button_option_color', '#ffffff'));

        if (!isNaN(buttonFontSize)) {
            $('#ess-preview-tab-button-text').css('font-size', `${buttonFontSize}px`);
        }

        applyPreviewFont('#ess-preview-tab-button-text', getValue('SSuprydp_button_option_font', 'Open Sans'), 'Open Sans');

        const floatingPreview = $('#ess-preview-floating-cta');
        floatingPreview
            .removeClass('sticky-cta-position-left sticky-cta-position-right sticky-cta-position-top sticky-cta-position-bottom vertical-cta vertical-cta-top vertical-cta-bottom')
            .addClass(`sticky-cta-position-${ctaPosition}`);

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
        bannerPreview.css({
            'background-color': getValue('content_background_color', '#f8fafc'),
            'color': getValue('SSuprydp_content_option_color', '#1f2a44'),
            'display': 'block',
            'justify-content': '',
            'align-items': '',
            'gap': ''
        });

        const bannerFontSize = parseInt(getValue('SSuprydp_content_option_size', ''), 10);
        bannerText.css('font-size', Number.isNaN(bannerFontSize) ? '' : `${bannerFontSize}px`);
        bannerText.css('letter-spacing', Number.isNaN(contentLetterSpacing) ? '' : `${contentLetterSpacing}px`);
        const bannerPadding = getDimensionCss('content_padding');
        bannerPreview.css('padding', bannerPadding || '');

        bannerLink.text(getValue('SSuprydp_action_option_text', 'Learn More'));
        bannerLink.css({
            'color': getValue('SSuprydp_action_option_color', '#ffffff'),
            'font-size': Number.isNaN(linkFontSize) ? '' : `${linkFontSize}px`,
            'letter-spacing': Number.isNaN(linkLetterSpacing) ? '' : `${linkLetterSpacing}px`,
            'padding': linkPadding || '',
            'display': ''
        });

        const linkBackground = getValue('link_text_background', '#1d5fd1');
        const isBannerButton = getChecked('call_to_action_button') || getValue('call_to_action_button', 'no') === 'yes';

        bannerLink.toggleClass('btn', isBannerButton);

        bannerLink.css('background-image', 'none');
        bannerLink.css('background-color', '');
        if (isBannerButton) {
            bannerLink.css('background-color', linkBackground);
            bannerLink.css('padding', linkPadding || '');
            bannerLink.css('display', 'inline-block');
        } else {
            bannerLink.css('padding', '');
            bannerLink.css('display', 'inline');
        }

        bannerLink.toggle(!hideCallToAction);

        applyPreviewFont('#ess-preview-banner-text', getValue('SSuprydp_content_option_font', 'Open Sans'), 'Open Sans');
        applyPreviewFont('#ess-preview-banner-link', getValue('SSuprydp_action_option_font', 'Open Sans'), 'Open Sans');

        const gdprPreview = $('#ess-preview-gdpr');
        const gdprText = $('#ess-preview-gdpr-text');
        const gdprAccept = $('#ess-preview-gdpr-accept');
        const gdprDecline = $('#ess-preview-gdpr-decline');

        gdprText.text(stripText(getValue('SSuprydp_content_option_text', 'We use cookies to improve your experience.')));
        gdprPreview.css({
            'background-color': getValue('content_background_color', '#1f2937'),
            'color': getValue('SSuprydp_content_option_color', '#f8fafc')
        });

        gdprAccept.text(getValue('SSuprydp_button_option_text', 'Got it.'));
        gdprAccept.css({
            'background-color': getValue('SSuprydp_button_option_backg_color', '#1d5fd1'),
            'color': getValue('SSuprydp_button_option_color', '#ffffff'),
            'font-size': Number.isNaN(buttonFontSize) ? '' : `${buttonFontSize}px`,
            'letter-spacing': Number.isNaN(buttonLetterSpacing) ? '' : `${buttonLetterSpacing}px`,
            'border-radius': Number.isNaN(buttonRound) ? '' : `${Math.max(0, buttonRound)}px`
        });

        applyPreviewFont('#ess-preview-gdpr-accept', getValue('SSuprydp_button_option_font', 'Open Sans'), 'Open Sans');

        const button2Show = getChecked('button2_show') || getValue('button2_show', 'no') === 'yes';
        const button2Text = getValue('button2_text', '');
        gdprDecline.toggle(button2Show && !!button2Text);
        gdprDecline.text(button2Text || 'Decline');

        const button2FontSize = parseInt(getNormalizedNumber('button2_font_size', ''), 10);
        const button2LetterSpacing = parseInt(getValue('button2_letter_spacing', ''), 10);
        const button2Radius = parseInt(getValue('button2_radius', ''), 10);
        gdprDecline.css({
            'background-color': getValue('button2_background_color', '#111827'),
            'color': getValue('button2_text_color', '#ffffff'),
            'font-size': Number.isNaN(button2FontSize) ? '' : `${button2FontSize}px`,
            'letter-spacing': Number.isNaN(button2LetterSpacing) ? '' : `${button2LetterSpacing}px`,
            'border-radius': Number.isNaN(button2Radius) ? '' : `${Math.max(0, button2Radius)}px`
        });

        applyPreviewFont('#ess-preview-gdpr-decline', getValue('button2_font_family', 'Open Sans'), 'Open Sans');

        const htmlPreview = $('#ess-preview-html-cta');
        htmlPreview
            .removeClass('sticky-cta-position-left sticky-cta-position-right sticky-cta-position-top sticky-cta-position-bottom vertical-cta vertical-cta-top vertical-cta-bottom')
            .addClass(`sticky-cta-position-${ctaPosition}`);
        resetPreviewButtonMetrics(htmlPreview);

        if (ctaPosition === 'top' || ctaPosition === 'bottom') {
            htmlPreview.addClass('vertical-cta').addClass(`vertical-cta-${ctaPosition}`);
        }
        htmlPreview.css('--width', previewWidth);

        const htmlButtonText = $('#ess-preview-html-button-text');
        const htmlButton = $('#ess-preview-html-button');
        const htmlContent = $('#ess-preview-html-content-text');

        const htmlButtonLabel = getValue('SSuprydp_button_option_text', '');
        const htmlButtonIcon = getValue('button_icon', '');
        if (htmlButtonIcon) {
            htmlButtonText.html(`<i class="icon ${htmlButtonIcon}"></i> ${htmlButtonLabel}`);
        } else {
            htmlButtonText.text(htmlButtonLabel);
        }
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
        htmlContent.css({
            'background-color': getValue('content_background_color', '#ffffff'),
            'color': getValue('SSuprydp_content_option_color', '#1a2940')
        });

        if (!isNaN(buttonFontSize)) {
            htmlButtonText.css('font-size', `${buttonFontSize}px`);
        }

        if (!isNaN(contentFontSize)) {
            htmlContent.css('font-size', `${contentFontSize}px`);
        }

        applyPreviewFont('#ess-preview-html-button-text', getValue('SSuprydp_button_option_font', 'Open Sans'), 'Open Sans');
        applyPreviewFont('#ess-preview-html-content-text', getValue('SSuprydp_content_option_font', 'Open Sans'), 'Open Sans');

        const keepHtmlOpen = getValue('keep_html_cta_open', 'no') === 'yes';
        htmlPreview.toggleClass('shrink', !keepHtmlOpen);

        requestAnimationFrame(() => {
            syncPreviewButtonMetrics();
            requestAnimationFrame(syncPreviewButtonMetrics);
        });

        const finishPreviewLoading = function () {
            previewCard.removeClass('is-preview-loading').attr('aria-busy', 'false');
        };

        if (isInitialLoad) {
            const previewImageUrl = image.length ? image : '';
            let imagePending = false;

            if (previewImageUrl) {
                imagePending = true;
                const preload = new Image();
                preload.onload = preload.onerror = function () {
                    imagePending = false;
                    previewSpinnerTimer = setTimeout(finishPreviewLoading, 200);
                };
                preload.src = previewImageUrl;
            }

            if (!imagePending) {
                previewSpinnerTimer = setTimeout(finishPreviewLoading, 300);
            }
        } else {
            previewSpinnerTimer = setTimeout(finishPreviewLoading, 180);
        }
    };

    form.on('input change', 'input, textarea, select', function () {
        updatePreview();
    });

    form.on('change', '#sticky_s_media, #image_attachment_id, [name="sidebar_template"]', function () {
        updatePreview();
    });

    // Font selector dropdowns update values programmatically; ensure preview refreshes immediately.
    form.on('click', '.font-select a, .font-select li', function () {
        setTimeout(updatePreview, 0);
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
