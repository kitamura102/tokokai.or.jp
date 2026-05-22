<?php
if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly 
}

class Easy_Sticky_Sidebar_Import_Export {

	public function __construct() {
		$this->export_cta();
		$this->import_sidebars();
	}

	function export_cta() {
		$post_data = filter_input_array(INPUT_POST, FILTER_SANITIZE_SPECIAL_CHARS);

		if (!isset($post_data['_nonce_export']) || !wp_verify_nonce($post_data['_nonce_export'], '_nonce_export_cta')) {
			return;
		}


		if (!isset($post_data['cta'])) {
			return;
		}

		$items = array_map('absint', (array) $post_data['cta']);

		if (empty($items)) {
			return;
		}

		global $wpdb;
		$placeholders = implode(', ', array_fill(0, count($items), '%d'));
		$results = $wpdb->get_results(
			$wpdb->prepare("SELECT * FROM $wpdb->sticky_cta WHERE id IN ($placeholders)", $items)
		);

		array_walk($results, function (&$item) {
			$item = new Easy_Sticky_Sidebar_CTA_Data($item);
			// Convert to array and remove protected properties
			$item_array = $item->to_array();
			unset($item_array['id'], $item_array['image_attachment_id'], $item_array['locations']);
			$item = (object) $item_array;
		});

		$file_name = sprintf("%s-%s-%s", sanitize_title(get_bloginfo('name')), 'multiple-cta', time());
		if (count($results) == 1) {
			$file_name = sprintf("%s-%s-%s", sanitize_title(get_bloginfo('name')), sanitize_title($results[0]->sidebar_name), time());
		}

		header('Content-disposition: attachment; filename=' . $file_name . '.json');
		header('Content-type: application/json');
		echo wp_json_encode($results);
		exit;
	}

	public function import_sidebars() {
		$post_data = filter_input_array(INPUT_POST, FILTER_SANITIZE_SPECIAL_CHARS);

		// Only process import if the import form was submitted
		if (!isset($post_data['action'])) {
			return;
		}

		// Check if this is an import action (could be 'Import' or translated version)
		$is_import_action = false;
		if ($post_data['action'] === 'Import' || 
			$post_data['action'] === __('Import', 'easy-sticky-sidebar') ||
			strpos($post_data['action'], 'Import') !== false) {
			$is_import_action = true;
		}

		if (!$is_import_action) {
			return;
		}

		if (!isset($_FILES['cta-import']) || !isset($post_data['_nonce']) || !wp_verify_nonce($post_data['_nonce'], 'nonce_import_field')) {
			return;
		}

		$sidebars = @file_get_contents($_FILES['cta-import']['tmp_name']);
		$sidebars = json_decode($sidebars);
		if (!$sidebars || !is_array($sidebars)) {
			return;
		}

		$medias = [];

		while ($sidebar = current($sidebars)) {
			next($sidebars);


			if (!empty($sidebar->sticky_s_media) && !isset($medias[$sidebar->sticky_s_media])) {

				// Additional validation to ensure the media URL is valid
				$media_url = trim($sidebar->sticky_s_media);
				if (empty($media_url) || !filter_var($media_url, FILTER_VALIDATE_URL)) {
					continue; // Skip this item if media URL is invalid
				}

				if (!$this->is_safe_media_url($media_url)) {
					continue;
				}

				$image_content = $this->fetch_remote_media($media_url);

				if ($image_content) {
					$filename = basename($sidebar->sticky_s_media);
					$upload = wp_upload_bits($filename, null, $image_content);

					if ($upload['error'] == false) {
						$attach_id = wp_insert_attachment([
							'guid' => $upload['url'],
							'post_mime_type' => $upload['type'],
							'post_title' => sanitize_file_name($filename),
							'post_content' => '',
							'post_status' => 'inherit'
						], $upload['file']);

						$medias[$sidebar->sticky_s_media] = ['id' => $attach_id, 'guid' => $upload['url']];
						$sidebar->image_attachment_id = $attach_id;
						$sidebar->sticky_s_media = $upload['url'];

						require_once(ABSPATH . 'wp-admin/includes/image.php');
						$attach_data = wp_generate_attachment_metadata($attach_id, $upload['file']);
						wp_update_attachment_metadata($attach_id, $attach_data);
					}
				}
			} elseif (!empty($sidebar->sticky_s_media) && isset($medias[$sidebar->sticky_s_media])) {
				$sidebar->image_attachment_id = $medias[$sidebar->sticky_s_media]['id'];
				$sidebar->sticky_s_media = $medias[$sidebar->sticky_s_media]['guid'];
			}

			easy_sticky_sidebar_insert($sidebar);
		}

		$request_data = filter_var_array($_REQUEST, FILTER_SANITIZE_SPECIAL_CHARS);
		$import_count = count($sidebars);

		$redirect_url = add_query_arg(
			['settings-updated' => true, 'import-count' => $import_count],
			$request_data['_wp_http_referer']
		);
		$redirect_url = wp_sanitize_redirect($redirect_url);
		wp_safe_redirect($redirect_url);
		exit;
	}

	private function is_safe_media_url($media_url) {
		$validated_url = wp_http_validate_url($media_url);
		if (empty($validated_url)) {
			return false;
		}

		$parsed = wp_parse_url($validated_url);
		if (empty($parsed['host'])) {
			return false;
		}

		$host = $parsed['host'];
		$resolved = gethostbyname($host);
		if ($resolved && $this->is_private_ip($resolved)) {
			return false;
		}

		return true;
	}

	private function is_private_ip($ip) {
		if (!filter_var($ip, FILTER_VALIDATE_IP)) {
			return true;
		}

		if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
			return !filter_var(
				$ip,
				FILTER_VALIDATE_IP,
				FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE
			);
		}

		return !filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE);
	}

	private function fetch_remote_media($media_url) {
		$response = wp_remote_get($media_url, [
			'timeout' => 10,
			'redirection' => 3,
		]);

		if (is_wp_error($response)) {
			return false;
		}

		$code = wp_remote_retrieve_response_code($response);
		if ($code < 200 || $code >= 300) {
			return false;
		}

		return wp_remote_retrieve_body($response);
	}

	public function output() {
		global $wpdb;

		$sidebars = $wpdb->get_results("SELECT * FROM $wpdb->sticky_cta");
		array_walk($sidebars, function (&$item) {
			$item = new Easy_Sticky_Sidebar_CTA_Data($item);
		}); ?>
		<div class="wrap wrap-easy-sticky-sidebar">
			<?php easy_sticky_sidebar_get_header(['class' => 'medium']) ?>

			<div class="easy-sticky-sidebar-container medium">
				<hr class="wp-header-end">
				<div class="easy-sticky-sidebar-tab-panel">
					<nav class="tab-nav">
						<a class="active" href="#tab-content-export"><?php esc_html_e('Export', 'easy-sticky-sidebar'); ?></a>
						<a href="#tab-content-import"><?php esc_html_e('Import', 'easy-sticky-sidebar'); ?></a>
					</nav>

					<div class="easy-sticky-sidebar-tab-content">
						<div id="tab-content-export">
							<header><?php esc_html_e('Export CTA', 'easy-sticky-sidebar'); ?></header>

							<form method="post">
								<?php wp_nonce_field('_nonce_export_cta', '_nonce_export') ?>

								<table class="form-table form-table-export">
									<tbody>
										<tr valign="top">
											<th scope="row"><?php esc_html_e('Select Items', 'easy-sticky-sidebar'); ?></th>
											<td>
												<ul class="export-cta-list">
													<li><label><input type="checkbox" data-select="all"> <?php esc_html_e('Select All', 'easy-sticky-sidebar'); ?></label></li>

													<?php foreach ($sidebars as $sidebar) {
														printf('<li><label><input type="checkbox" name="cta[]" value="%d" /> %s</label></li>', absint($sidebar->__get('id')), esc_attr($sidebar->__get('sidebar_name')));
													} ?>
												</ul>
											</td>
										</tr>
									</tbody>
								</table>

								<?php submit_button(__('Export', 'easy-sticky-sidebar'), 'primary', 'action', false); ?>
							</form>
						</div>

						<div id="tab-content-import">
							<header><?php esc_html_e('Import CTA', 'easy-sticky-sidebar'); ?></header>
							<?php
							if (isset($_GET['settings-updated'])) {
								$import_count = isset($_GET['import-count']) ? intval($_GET['import-count']) : 0;
								if ($import_count > 0) {
									// translators: %d: Number of CTAs imported.
									$message = sprintf(_n('%d CTA has been successfully imported.', '%d CTAs have been successfully imported.', $import_count, 'easy-sticky-sidebar'), $import_count);
									echo '<div class="notice notice-success is-dismissible"><p><strong>' . esc_html__('Success!', 'easy-sticky-sidebar') . '</strong> ' . esc_html($message) . '</p></div>';
								} else {
									echo '<div class="notice notice-success is-dismissible"><p><strong>' . esc_html__('Success!', 'easy-sticky-sidebar') . '</strong> ' . esc_html__('CTA data has been successfully imported.', 'easy-sticky-sidebar') . '</p></div>';
								}
							}
							?>

							<form method="post" enctype="multipart/form-data">
								<?php wp_nonce_field('nonce_import_field', '_nonce') ?>
								<p><input name="cta-import" type="file"></p>
								<?php submit_button(__('Import', 'easy-sticky-sidebar'), 'primary', 'action', false); ?>
							</form>
						</div>
					</div>
				</div>
			</div>
		</div>
<?php
	}
}

return new Easy_Sticky_Sidebar_Import_Export();
