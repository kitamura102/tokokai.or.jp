<?php 
function tpl_053_rwd_theme_options_render_page() { ?>
<div class="wrap" id="tpl_053_rwd_options">
  <?php screen_icon(); ?>
	<h2><?php printf( __( '%s Theme Options', '' ), wp_get_theme() ); ?></h2>
	<?php settings_errors(); ?>
	<form method="post" action="options.php">
	<?php
		settings_fields( 'tpl_053_rwd_options' );
		$options = tpl_053_rwd_get_theme_options();
		$default_options = tpl_053_rwd_get_default_theme_options();
	?>
  
<div class="section first">
<h3>テーマの特徴</h3>
<table>
<tr>
<th>メニュー</th>
<td>
<p>カスタムメニューに対応。</p>
<ol>
<li>Main Navigation → ヘッダーグローバルナビゲーション部分</li>
</ol>
<img src="<?php echo site_url(); ?>/wp-content/themes/<?php printf( __( '%s', '' ), wp_get_theme() ); ?>/images/englishMenu.png" class="alignLeft">

<p>ヘッダーグローバルナビゲーションは<a href="<?php echo site_url(); ?>/wp-admin/nav-menus.php" target="_blank">メニュー</a>の各項目で説明を英表記で書くと、日本語と英語でメニューが表示されます。(画像参照)　<span class="warning">こちらを設定しないとエラーが出ます</span>。</p>
<p>※「メニュー」画面の右上「表示オプション」で「説明」にチェックを入れて説明が表示されるようにして下さい。</p>
<p>※ フッターの３カラムのいずれかにメニューを表示されたい際にはウィジェットでカスタムメニューをお選びください。</p></td>
</tr>
<tr>
<th>ウィジェット</th>
<td><ol>
<li>sidebar → サイドバーに表示されます。</li>
</ol></td>
</tr>
<tr>
<th>日付</th>
<td>投稿のみに表示され、固定ページには表示されません。</td>
</tr>
<tr>
<th>ページナビゲーション</th>
<td>投稿のみに表示され、固定ページには表示されません。</td>
</tr>
<tr>
<th>コメント機能</th>
<td>コメント機能はついていません。</td>
</tr>
</table>
</div>

<div class="section first">
<h3>各設定方法</h3>
<table>
<tr>
<th>ページの設定</th>
<td><a href="<?php echo site_url(); ?>/wp-admin/options-reading.php" target="_blank">管理画面の設定</a> → 表示設定 → フロントページの表示 → 固定ページ で、フロントページに表示させたいページを選択します。
</td>
</tr>
</table>
</div>

<div class="section">
<h3>ヘッダー設定</h3>
<table>
<tr>
<th>サイト名</th>
<td>
<input type="text" name="tpl_053_rwd_theme_options[logoName]" id="logoName" value="<?php if(isset($options['logoName'])):?><?php echo esc_attr( $options['logoName'] ); ?><?php else:?>Company Name<?php endif;?>" /><br /><span>例) Company Name</span>
</td>
</tr>
<th>スローガン</th>
<td>
<input type="text" name="tpl_053_rwd_theme_options[logoSlogan]" id="logoSlogan" value="<?php if(isset($options['logoSlogan'])):?><?php echo esc_attr( $options['logoSlogan'] ); ?><?php else:?>Your Company Slogan<?php endif;?>" /><br /><span>例) Your Company Slogan</span>
</td>
</tr>
</table>
<?php submit_button(); ?>
</div>

<div class="section">
<h3>メイン画像の文章設定 <span>-メイン画像がある場合のみ表示されます-</span></h3>
<table>
<tr>
<th>スローガン　見出し</th>
<td>
<input type="text" name="tpl_053_rwd_theme_options[mainImgSlogan]" id="mainImgSlogan" value="<?php if(isset($options['mainImgSlogan'])):?><?php echo esc_attr( $options['mainImgSlogan'] ); ?><?php else:?>自然との調和を目指す企業です<?php endif;?>" /><br /><span>例) 自然との調和を目指す企業です</span>
</td>
</tr>
<th>スローガン</th>
<td>
<input type="text" name="tpl_053_rwd_theme_options[mainImgSlogan2]" id="mainImgSlogan2" value="<?php if(isset($options['mainImgSlogan2'])):?><?php echo esc_attr( $options['mainImgSlogan2'] ); ?><?php else:?>革新的な技術で世の中を動かす企業を目指します。<?php endif;?>" /><br /><span>例) 革新的な技術で世の中を動かす企業を目指します。</span>
</td>
</tr>
</table>
<?php submit_button(); ?>
</div>

<div class="section">
<h3>連絡先の設定 <span>-フッター中央sに表示されます-</span></h3>
<table>
<tr>
<th>電話番号</th>
<td>
<input type="text" name="tpl_053_rwd_theme_options[contactTel]" id="contactTel" value="<?php if(isset($options['contactTel'])):?><?php echo esc_attr( $options['contactTel'] ); ?><?php else:?>012-3456-7890<?php endif;?>" /><br /><span>例) 012-3456-7890</span>
</td>
</tr>
<tr>
<th>受付時間</th>
<td>
<input type="text" name="tpl_053_rwd_theme_options[openTime]" id="openTime" value="<?php if(isset($options['openTime'])):?><?php echo esc_attr( $options['openTime'] ); ?><?php else:?>平日 AM 10:00 〜 PM 5:00<?php endif;?>" /><br /><span>例) 平日 AM 10:00 〜 PM 5:00</span>
</td>
</tr>
</table>
<?php submit_button(); ?>
</div>

<div class="section">
<h3>メイン画像のリンク先設定 <span>-フロントページに表示されます-</span></h3>
<table>
<tr><th>画像</th>
<td>
画像は<a href="<?php echo site_url(); ?>/wp-admin/themes.php?page=custom-header" target="_blank">こちら</a>から設定してください。
</td>
</tr>
<tr>
<th>リンク先 (リンク先は空でも問題ありません)</th>
<td>
<input type="text" name="tpl_053_rwd_theme_options[mainImgLink]" id="mainImgLink" value="<?php echo esc_attr( $options['mainImgLink'] ); ?>" /><br /><span>例) http://c-tpl.com/</span>
</td>
</tr>
</table>
<?php submit_button(); ?>
</div>
</form>
</div>
<?php
}

function tpl_053_rwd_theme_options_validate( $input ) {
	$output = $defaults = tpl_053_rwd_get_default_theme_options();
	
	$output['logoName'] = $input['logoName'];
	$output['logoSlogan'] = $input['logoSlogan'];
	
	$output['contactTel'] = $input['contactTel'];
	$output['openTime'] = $input['openTime'];
	
	$output['mainImgLink'] = $input['mainImgLink'];
	$output['mainImgSlogan'] = $input['mainImgSlogan'];
	$output['mainImgSlogan2'] = $input['mainImgSlogan2'];
	
	return apply_filters( 'tpl_053_rwd_theme_options_validate', $output, $input, $defaults );
}

?>