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

		const delta = Math.max(0, ctaHeight - buttonHeight - distance);
		const translateY = isTop ? -delta : delta;
		$cta.css('--translateY', `${translateY}px`);
	};

	const ensureTabCtaIcon = function ($cta) {
		if (!$cta || !$cta.length || !$cta.hasClass('tab-cta')) {
			return;
		}

		const iconClass = ($cta.data('button-icon') || '').toString().trim();
		if (!iconClass) {
			return;
		}

		const $label = $cta.find('.sticky-sidebar-button > div').first();
		if (!$label.length) {
			return;
		}

		if ($label.find('i').length) {
			return;
		}

		const $icon = $('<i />').addClass(iconClass);
		$label.prepend(' ').prepend($icon);
	};

	updateAllButtonMetrics();
	$('.easy-sticky-sidebar.tab-cta').each(function () {
		ensureTabCtaIcon($(this));
	});
	$(window).on('load resize', function () {
		updateAllButtonMetrics();
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
	});

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
			sticky_id: sticky_id
		});
	});
});
