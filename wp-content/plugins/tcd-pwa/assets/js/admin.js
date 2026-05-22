jQuery(function ($) {
  // カラーピッカー
  $('.js-tcd-pwa-color-pucker').wpColorPicker();

  // form送信
  $('#js-tcd-pwa-page-form').on('submit', function (e) {
    // 通常のフォーム送信をキャンセル
    e.preventDefault();

    // フォームのデータを jQuery で取得
    const formData = $(this).serialize();

    // 送信ボタン
    const submitButtons = $(this).find('.js-tcd-pwa-page-form-submit');
    submitButtons.addClass('is-saving');

    // action属性からoptions.phpを取得し、POSTで送る
    $.ajax({
      url: $(this).attr('action'),
      type: 'POST',
      data: formData,
      success: function (response) {
        submitButtons.removeClass('is-saving');
        submitButtons.addClass('is-saved');
        setTimeout(() => {
          submitButtons.removeClass('is-saved');
        }, 3000);
      },
      error: function (xhr, status, error) {
        submitButtons.removeClass('is-saving');
        console.error('error:', error);
        alert('An error has occurred.');
      },
    });
  });
});