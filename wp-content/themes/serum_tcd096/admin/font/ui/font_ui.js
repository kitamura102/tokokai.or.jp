jQuery(function ($) {

	/**
	 * ラジオボタン
	 *
	 * NOTE: data-checked属性の追加
	 */
	 const updateRadioStatus = function () {
		if ($(this).prop('checked')) {
			const index = $(this).parent().index();
			$(this).closest('.js-tcd-ui-radio').attr('data-checked', index);
		}
	};

	// ユーザー操作による changeイベント
	$(document).on('change', '.js-tcd-ui-radio input[type=radio]', updateRadioStatus);

	// レガシーウィジェットが追加、更新された時の対策
	$(document).on('widget-added widget-updated', function (e, widget) {
		widget.find('.js-tcd-ui-radio input[type=radio]').each(function () {
			updateRadioStatus.call(this);
		});
	});

	// ページ読み込み時の初期化
	$('.js-tcd-ui-radio input[type=radio]').each(function () {
		updateRadioStatus.call(this);
	});

	/**
	 * セレクトボックス
	 *
	 * NOTE: data-selected属性を追加して、CSSでフィールドを切り替える
	 */
	const updateSelectStatus = function () {
		const index = $(this).prop('selectedIndex');
		$(this).attr('data-selected', index);
	};

	// ユーザー操作による changeイベント
	$(document).on('change', '.js-tcd-ui-select', updateSelectStatus);

	// レガシーウィジェットが追加、更新された時の対策
	$(document).on('widget-added widget-updated', function (e, widget) {
		widget.find('.js-tcd-ui-select').each(function () {
			updateSelectStatus.call(this);
		});
	});

	// ページ読み込み時の初期化
	$('.js-tcd-ui-select').each(function () {
		updateSelectStatus.call(this);
	});
});