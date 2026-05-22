<?php
/**
 * マニュアルのサブメニューを追加
 */


function add_menu_submenu_page(){
    add_dashboard_page(__( 'TCD Manual', TCD_TEXTDOMAIN ) ,__( 'TCD Manual', TCD_TEXTDOMAIN ), 'edit_theme_options', 'theme_manual', 'menu_add_theme_manual');
}
 
function menu_add_theme_manual(){
	?>
	<div class="wrap">
		<h2><?php _e( 'TCD Manual', TCD_TEXTDOMAIN ); ?></h2>
		<p><a href="<?php echo TCD_MANUAL_URL ?>" class="button-primary" rel="noopener" target="_blank"><?php _e( 'Manual Site', TCD_TEXTDOMAIN ); ?></a></p>
		<p><?php printf( __( 'The password for viewing the manual is on <a href=%s rel="noopener" target="_blank">My Page</a>.', TCD_TEXTDOMAIN ),'"https://tcd.style/order-history"'); ?></p>
		<h2><?php _e( 'Related Links', TCD_TEXTDOMAIN ); ?></h2>
		<ul>
			<li>・<a href="https://tcd-theme.com/introduction" rel="noopener" target="_blank"><?php _e( 'The complete collection of WordPress usage', TCD_TEXTDOMAIN ); ?></a></li>
			<li>・<a href="https://tcdmuseum.com/" rel="noopener" target="_blank"><?php _e( 'TCD MUSEUM', TCD_TEXTDOMAIN ); ?></a></li>
			<li>・<a href="https://tcd-manual.net/" rel="noopener" target="_blank"><?php _e( 'TCD LABO', TCD_TEXTDOMAIN ); ?></a></li>
		</ul>
	</div>
	<?php
}

add_action('admin_menu', 'add_menu_submenu_page');


/**
 * マニュアルのダッシュボード追加
 */

function theme_manual_dashboard_widgets(){
	wp_add_dashboard_widget('theme_manual_widget', __( 'TCD Manual', TCD_TEXTDOMAIN ), 'theme_manual_dashboard_manual');
}

function theme_manual_dashboard_manual(){
    // This tells the function to cache the remote call for 21600 seconds (6 hours)
	$xml = get_tcd_update_notifier_xml();

	// Get theme data from style.css (current version is what we want)
	$theme_data = function_exists( 'wp_get_theme' ) ?  wp_get_theme( get_template() ) : get_theme_data( TEMPLATEPATH . '/style.css' );
    
	?>
	<div class="wrap">
		<p><a href="<?php echo TCD_MANUAL_URL ?>" class="button-primary" rel="noopener" target="_blank"><?php _e( 'Manual Site', TCD_TEXTDOMAIN ); ?></a></p>
		<p><?php printf( __( 'The password for viewing the manual is on <a href=%s rel="noopener" target="_blank">My Page</a>.', TCD_TEXTDOMAIN ),'"https://tcd.style/"'); ?></p>
        <strong><?php _e( 'Related Links', TCD_TEXTDOMAIN ); ?></strong>
        <ul>
            <li>・<a href="https://tcd-theme.com/introduction" rel="noopener" target="_blank"><?php _e( 'The complete collection of WordPress usage', TCD_TEXTDOMAIN ); ?></a></li>
            <li>・<a href="https://tcdmuseum.com/" rel="noopener" target="_blank"><?php _e( 'TCD MUSEUM', TCD_TEXTDOMAIN ); ?></a></li>
            <li>・<a href="https://tcd-manual.net/" rel="noopener" target="_blank"><?php _e( 'TCD LABO', TCD_TEXTDOMAIN ); ?></a></li>
        </ul>
        <hr>
        <p>
        <?php if ( $xml && version_compare( $theme_data['Version'], $xml->latest ) == -1 ) { ?>
            <strong><?php printf( __( 'The latest version of %s is released.', TCD_TEXTDOMAIN ), esc_html( $theme_data['Name'] ) ); ?></strong><br>
            <?php printf( __( 'Current version is %s. You can update to the latest version, %s.', TCD_TEXTDOMAIN ), esc_html( $theme_data['Version'] ), esc_html( $xml->latest ) ); ?><br>
            <a href="?page=design-plus-updates"><?php _e( 'Theme Update', TCD_TEXTDOMAIN ) ?></a>
        <?php }else{ ?>
            <?php printf( __( 'The current version of %s is %s. This is the latest version.', TCD_TEXTDOMAIN ), esc_html( $theme_data['Name'] ),esc_html( $theme_data['Version'] )); ?>
        <?php } ?>
        </p>
		<?php 	if ( ! $xml || empty( $xml->latest ) ) { ?>
			<!--テーマの最新情報の取得ができませんでした。時間をおいてから再度お試しください。-->
			<?php _e( 'We were unable to get the latest information on the theme. Please wait some time and try again.',TCD_TEXTDOMAIN);?>
		<?php } ?>
	</div>
	<?php
}

if( current_user_can( 'administrator' )){
  add_action('wp_dashboard_setup', 'theme_manual_dashboard_widgets');
}

?>