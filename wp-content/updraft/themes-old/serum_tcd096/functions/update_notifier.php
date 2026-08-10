<?php
/**
 * Provides a notification everytime the theme is updated.
 *
 * @package TCD
 */

/**
 * Admin menu.
 *
 * @return void
 */
function tcd_update_notifier_admin_menu() {
	// Get xml.
	$xml = get_tcd_update_notifier_xml();

	// Get theme data.
	$theme_data = wp_get_theme( get_template() );

	// Has newer version.
	if ( $xml && version_compare( $theme_data['Version'], $xml->latest, '<' ) ) {
		$tcd_memu_title = __( 'Theme Update', TCD_TEXTDOMAIN ) . '<span class="update-plugins count-1"><span class="update-count">1</span></span>';
	}else{
		$tcd_memu_title = __( 'Theme Update', TCD_TEXTDOMAIN ) . '';
	}
		add_dashboard_page(
			$theme_data['Name'] . ' ' . __('Theme Update Information', TCD_TEXTDOMAIN ),
			$tcd_memu_title,
			'administrator',
			'design-plus-updates',
			'tcd_update_notifier_content'
		);
}
add_action( 'admin_menu', 'tcd_update_notifier_admin_menu' );

/**
 * Render the content.
 *
 * @return void
 */
function tcd_update_notifier_content() {
	// Get xml.
	$xml = get_tcd_update_notifier_xml();
	if ( ! $xml || empty( $xml->latest ) ) { ?>
			<p style="font-size:14px;">
			<!--テーマの最新情報の取得ができませんでした。時間をおいてから再度お試しください。-->
			<?php _e( 'We were unable to get the latest information on the theme. Please wait some time and try again.',TCD_TEXTDOMAIN);?>
			</p>
		<?php
		return;
	}

	// Get theme data.
	$theme_data = wp_get_theme( get_template() );
	?>
	<style>
	.update-nag {
		display: none;
	}
	.tcd-update-info {
		background: #fff;
		border: 1px solid #ccc;
		border-radius: 5px;
		float: left;
		width: 400px;
		margin: 0 20px 20px 0;
	}
	.tcd-update-info h3 {
		background: #f2f2f2;
		background: linear-gradient(to bottom, #fff, #eee);
		border-bottom: 1px solid #ccc;
		border-radius: 5px 5px 0 0;
		font-size: 14px;
		margin: 0 0 15px 0;
		padding: 15px 15px 12px;
	}
	.tcd-update-info dl {
		font-size: 12px;
		margin: 0 15px 5px 15px;
	}
	.tcd-update-info dt {
		font-weight: 700;
		margin: 0 0 2px 0;
	}
	.tcd-update-info dd {
		margin: 0 0 15px 0;
	}
	.tcd-update-theme-thumbnail {
		border:1px solid #ccc;
		display: block;
		height: auto;
		max-width: 100%;
		width: 600px;
	}
	</style>
	<div class="wrap">
		<div id="icon-tools" class="icon32"></div>
		<h2>
			<?php echo esc_html( $theme_data['Name'] ); ?>
			<?php _e( 'Theme Update Information', TCD_TEXTDOMAIN ); ?>
		</h2>
		<?php 	if ( $xml && version_compare( $theme_data['Version'], $xml->latest, '<' ) ) { ?>
		<h3> 
				<strong>
					<?php
						printf(
							/* translators: %s: Theme name. */
							esc_html__( 'The latest version of %s is released.', TCD_TEXTDOMAIN ),
							esc_html( $theme_data['Name'] )
						);
					?>
				</strong>
		</h3>
			<p style="font-size:14px;">
			<?php
					printf(
						esc_html__( 'Current version is %s. You can update to the latest version, %s.', TCD_TEXTDOMAIN ),
						esc_html( $theme_data['Version'] ),
						esc_html( $xml->latest )
					);
				?>	
			</p>
			<?php }else{ ?>
			<p style="font-size:14px;">
				<?php printf( __( 'The current version of %s is %s. This is the latest version.', TCD_TEXTDOMAIN ), esc_html( $theme_data['Name'] ),esc_html( $theme_data['Version'] )); ?>
			</p>
			<?php } ?>
		<div class="tcd-update-instructions wp-clearfix">
			<p style="font-size:14px;">
				<strong>
					<!--最新版のテーマは<a href="https://tcd.style/order-history" rel="noopener" target="_blank">マイページ</a> からダウンロードできます。-->
					<?php
						_e( 
							'The latest version of the theme can be downloaded from <a href="https://tcd.style/order-history" rel="noopener" target="_blank">Mypage</a>.',
							TCD_TEXTDOMAIN
						);
					?>
				</strong>
					<!--テーマアップデートの方法はこちらをご確認ください。-->
					<?php
						_e(
							'Click <a href="https://tcd-theme.com/2017/01/theme_update.html" rel="noopener" target="_blank">here</a> to find out how to update the theme.',
							TCD_TEXTDOMAIN
						);
					?>
			</p>
		</div>
		<div style="font-weight: bold;font-size:15px;display: block;padding: 20px;border: 1px solid #ccc;margin-bottom: 1.5em;background-color: #fff;">
			<!--最新版のテーマへアップデートする前に、必ずご利用中のテーマファイルのバックアップをしてください。-->
			<?php _e( 'Please be sure to back up your theme files before updating to the latest version.',TCD_TEXTDOMAIN); ?>
			
		</div>
		<div class="tcd-update-instructions wp-clearfix">
			<div class="tcd-update-info">

				<h3><!--更新履歴--><?php _e( 'Changelog', TCD_TEXTDOMAIN ); ?></h3>
				<?php echo $xml->changelog; ?>
			</div>
			<img class="tcd-update-theme-thumbnail" src="<?php echo esc_url( (string) $theme_data->get_screenshot() ); ?>" alt="">
		</div>
	</div>
	<?php
}


