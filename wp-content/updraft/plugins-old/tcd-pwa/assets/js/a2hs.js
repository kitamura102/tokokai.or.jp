// 名前空間を作成
const TCDPWA = {};

// PWAのインストール基準を満たしているか
let deferredPrompt = null;

// クッキーのセット
TCDPWA.setCookie = (name, value, days) => {
  document.cookie = `${name}=${value}; max-age=${days * 24 * 60 * 60}`;
}

// standaloneモードかどうか
// NOTE: standaloneモードの場合はインストールボタンを非表示
TCDPWA.isStandAlone = window.matchMedia('(display-mode: standalone)').matches;

// a2hsインストールボタン
const footerA2hs = document.getElementById('js-tcd-footer-a2hs');
if (footerA2hs && !TCDPWA.isStandAlone) {

  // beforeinstallprompt イベントをキャッチ
  window.addEventListener('beforeinstallprompt', (e) => {

    // デフォルトのインストールプロンプトを抑制
    e.preventDefault();
    // イベントオブジェクトを保持
    deferredPrompt = e;
    // インストール可能環境
    footerA2hs.classList.remove('is-uninstallable');
    footerA2hs.classList.add('is-installable');
  });

  // スクロールしたら表示
  window.addEventListener('scroll', () => {
    // ページトップからのスクロール量を取得
    const scrollY = window.scrollY || document.documentElement.scrollTop;

    if (scrollY > 0) {
      footerA2hs.classList.add('is-active');
    } else {
      footerA2hs.classList.remove('is-active');
    }
  });

  // 閉じるボタンクリックでフッターバーを非表示
  document.getElementById('js-tcd-footer-a2hs-close')?.addEventListener('click', (e) => {

    // 非表示にする
    footerA2hs.remove();

    // 1日後に期限切れ
    TCDPWA.setCookie('tcd_footer_a2hs_close', 1, 1);
  });

  // インストールボタンクリックでプロンプトを表示
  document.getElementById('js-tcd-footer-a2hs-install')?.addEventListener('click', async () => {

    // インストール可能じゃなければ
    if (!deferredPrompt) {

      // iOSリンクがあれば繊維させる
      const footerA2hsLink = footerA2hs.querySelector('[data-url]')?.getAttribute('data-url');
      if (footerA2hsLink) {
        window.location.href = footerA2hsLink;
      }
      return;
    }

    // プロンプトを表示
    deferredPrompt.prompt();

    // ユーザーがインストールを「許可」または「キャンセル」した結果を取得
    deferredPrompt.userChoice.then((choiceResult) => {
      if (choiceResult.outcome === 'accepted') {

        // インストールが行われた場合、ボタンを削除
        footerA2hs.remove();
        // 7日間表示しない
        TCDPWA.setCookie('tcd_footer_a2hs_close', 1, 7);
      } else {

        // キャンセルされた場合も削除
        footerA2hs.remove();
        // 7日間表示しない
        TCDPWA.setCookie('tcd_footer_a2hs_close', 1, 7);
      }
      // 以降、この deferredPrompt は再利用できないため null に
      deferredPrompt = null;
    });
  });
}
