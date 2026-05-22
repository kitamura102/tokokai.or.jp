jQuery(document).ready(function ($) {
	var CTA_Click = [];
	var CTA_Tracked = [];

	const updateButtonMetrics = function ($cta) {
		if (!$cta || !$cta.length) {
			return;
		}

		const $button = $cta.find('.sticky-sidebar-button').first();
		if (!$button.length) {
			return;
		}

		const width = Math.ceil($button.outerWidth());
		const height = Math.ceil($button.outerHeight());

		if (width) {
			$cta.css('--buttonWidth', `${width}px`);
		}
		if (height) {
			$cta.css('--buttonHeight', `${height}px`);
		}
	};

	const updateAllButtonMetrics = function () {
		$('.easy-sticky-sidebar').each(function () {
			updateButtonMetrics($(this));
		});
	};

	const updateOverlayBackgroundOffsets = function ($cta) {
		if (!$cta || !$cta.length) {
			return;
		}

		if (!$cta.hasClass('image-as-background')) {
			$cta.css('--ess-overlay-bg-offset-x', '');
			$cta.css('--ess-overlay-bg-offset-y', '');
			return;
		}

		const $panel = $cta.find('.sticky-overlay-panel').first();
		if (!$panel.length) {
			$cta.css('--ess-overlay-bg-offset-x', '');
			$cta.css('--ess-overlay-bg-offset-y', '');
			return;
		}

		const panelWidth = Math.max(0, Math.round(($panel.outerWidth() || 0) * 0.9));
		const panelHeight = Math.max(0, Math.round(($panel.outerHeight() || 0) * 0.9));
		$cta.css('--ess-overlay-bg-offset-x', `${panelWidth}px`);
		$cta.css('--ess-overlay-bg-offset-y', `${panelHeight}px`);
	};

	const updateAllOverlayBackgroundOffsets = function () {
		$('.easy-sticky-sidebar.image-as-background').each(function () {
			updateOverlayBackgroundOffsets($(this));
		});
	};

	const updateVerticalShrinkOffset = function ($cta, isShrink) {
		if (!$cta || !$cta.length || !$cta.hasClass('vertical-cta')) {
			return;
		}

		const isTop = $cta.hasClass('sticky-cta-position-top');
		const isBottom = $cta.hasClass('sticky-cta-position-bottom');
		if (!isTop && !isBottom) {
			return;
		}

		const $button = $cta.find('.sticky-sidebar-button').first();
		if (!$button.length) {
			return;
		}

		const ctaHeight = Math.ceil($cta.outerHeight());
		const buttonHeight = Math.ceil($button.outerHeight());
		const distanceRaw = getComputedStyle($cta[0]).getPropertyValue('--position_distance') || '0';
		const distance = parseFloat(distanceRaw) || 0;

		if (!isShrink) {
			$cta.css('--translateY', '0px');
			return;
		}

		const isExternalVerticalHtmlTab = $cta.hasClass('sticky-cta')
			&& ($cta.hasClass('ess-html-tab-align-left') || $cta.hasClass('ess-html-tab-align-center') || $cta.hasClass('ess-html-tab-align-right'));
		const isExternalVerticalStickyOverlayTab = $cta.hasClass('sticky-cta')
			&& $cta.hasClass('image-as-background')
			&& !$cta.hasClass('ess-overlay-full-tab-height')
			&& (isTop || isBottom);
		const isExternalVerticalTab = isExternalVerticalHtmlTab || isExternalVerticalStickyOverlayTab;
		const delta = isExternalVerticalTab
			? Math.max(0, ctaHeight - distance)
			: Math.max(0, ctaHeight - buttonHeight - distance);
		const translateY = isTop ? -delta : delta;
		$cta.css('--translateY', `${translateY}px`);
	};

	const ensureTabCtaIcon = function ($cta) {
		if (!$cta || !$cta.length || !$cta.hasClass('tab-cta')) {
			return;
		}

		let iconClass = ($cta.data('button-icon') || '').toString().trim();
		if (!iconClass) {
			return;
		}

		// Backward compatibility for values like "fa-angle-right".
		if (/^fa-[a-z0-9-]+$/i.test(iconClass)) {
			iconClass = `fa ${iconClass}`;
		}

		const $label = $cta.find('.sticky-sidebar-button > div').first();
		if (!$label.length) {
			return;
		}

		if ($label.find('i').length) {
			return;
		}

		const $icon = $('<i />').addClass(`icon ${iconClass}`);
		$label.prepend(' ').prepend($icon);
	};

	updateAllButtonMetrics();
	updateAllOverlayBackgroundOffsets();
	$('.easy-sticky-sidebar.tab-cta').each(function () {
		ensureTabCtaIcon($(this));
	});
	$(window).on('load resize', function () {
		updateAllButtonMetrics();
		updateAllOverlayBackgroundOffsets();
	});

	$('.easy-sticky-sidebar .btn-ess-close').on('click', function (e) {
		e.stopPropagation();
		$(this).closest('.easy-sticky-sidebar').fadeOut(200, function () {
			$(this).remove();
		});
	});

	var width = $(window).width();

	if (width >= 767) {
		$(window).scroll(function () {
			var scroll = $(window).scrollTop();
			if (scroll <= 120) {
				return;
			}

			jQuery('.easy-sticky-sidebar.sticky-cta:not(.scrolled)').each(function () {
				if ($(this).hasClass('shrink-disabled')) {
					return;
				}

				cta_id = parseInt($(this).data('id'));
				if (!CTA_Click.includes(cta_id)) {
					updateButtonMetrics($(this));
					$(this).addClass('shrink scrolled');
					updateVerticalShrinkOffset($(this), true);
				}
			});
		});
	}

	$('body').on('click', '.easy-sticky-sidebar .sticky-sidebar-button:not(a)', function (e) {
		e.preventDefault();

		current_cta = $(this).closest('.easy-sticky-sidebar');
		updateButtonMetrics(current_cta);

		cta_id = parseInt(current_cta.data('id'));
		if (cta_id > 0 && !CTA_Click.includes(cta_id)) {
			CTA_Click.push(cta_id);
		}

		const willShrink = !current_cta.hasClass('shrink');
		current_cta.toggleClass('shrink');
		updateVerticalShrinkOffset(current_cta, willShrink);
		updateOverlayBackgroundOffsets(current_cta);
	});

	(function trackImpressions() {
		if (typeof easy_sticky_sidebar_front === 'undefined' || !easy_sticky_sidebar_front.ajax_url) {
			return;
		}

		var ids = [];
		$('.easy-sticky-sidebar[data-id]').each(function () {
			var id = parseInt($(this).data('id'), 10);
			if (id > 0) {
				ids.push(id);
			}
		});

		if (ids.length) {
			$.post(easy_sticky_sidebar_front.ajax_url, {
				action: 'easy_sticky_sidebar_track_impressions',
				ids: ids,
				nonce: easy_sticky_sidebar_front.nonce
			});
		}
	})();

	$('body').on('click', '.easy-sticky-sidebar a', function () {
		var sticky_id = parseInt($(this).closest('[data-id]').data('id'), 10);
		if (!sticky_id || CTA_Tracked.includes(sticky_id)) {
			return;
		}

		CTA_Tracked.push(sticky_id);

		if (typeof easy_sticky_sidebar_front === 'undefined' || !easy_sticky_sidebar_front.ajax_url) {
			return;
		}

		$.post(easy_sticky_sidebar_front.ajax_url, {
			action: 'easy_sticky_sidebar_get_click',
			sticky_id: sticky_id,
			nonce: easy_sticky_sidebar_front.nonce
		});
	});
});
