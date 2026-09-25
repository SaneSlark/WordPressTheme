<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$themename = 'Qiumin';

// 与 style.css、readme.txt 的发布版本保持一致，用于刷新样式和脚本缓存。
if ( ! defined( 'QIUMIN_THEME_VERSION' ) ) {
	define( 'QIUMIN_THEME_VERSION', '2.6.2' );
}

// 主题初始化：声明 WordPress 支持能力、菜单位置和本地化目录。
function qiumin_setup() {
	load_theme_textdomain( 'qiumin', get_template_directory() . '/languages' );

	add_theme_support( 'title-tag' );
	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'responsive-embeds' );
	add_theme_support(
		'html5',
		array(
			'search-form',
			'comment-form',
			'comment-list',
			'gallery',
			'caption',
			'style',
			'script',
		)
	);

	register_nav_menus(
		array(
			'primary' => __( '主导航', 'qiumin' ),
		)
	);
}
add_action( 'after_setup_theme', 'qiumin_setup' );

// 注册主题侧边栏；当后台没有挂载小工具时，模板会使用自带默认内容。
function qiumin_widgets_init() {
	$sidebars = array(
		'First_sidebar'  => __( '侧栏一', 'qiumin' ),
		'Third_sidebar'  => __( '侧栏三', 'qiumin' ),
		'Fourth_sidebar' => __( '侧栏四', 'qiumin' ),
	);

	foreach ( $sidebars as $id => $name ) {
		register_sidebar(
			array(
				'name'          => $name,
				'id'            => $id,
				'before_widget' => '<div id="%1$s" class="slidesBox p1 mb5 widget %2$s">',
				'after_widget'  => '</div>',
				'before_title'  => '<h4>',
				'after_title'   => '</h4>',
			)
		);
	}
}
add_action( 'widgets_init', 'qiumin_widgets_init' );

// 从归档链接中提取年月，用于把月份统一格式化为两位数。
function qiumin_get_month_label( $url ) {
	$path  = (string) wp_parse_url( $url, PHP_URL_PATH );
	$query = (string) wp_parse_url( $url, PHP_URL_QUERY );
	$year  = 0;
	$month = 0;

	if ( preg_match( '#/(\d{4})/(\d{1,2})/?$#', $path, $matches ) ) {
		$year  = (int) $matches[1];
		$month = (int) $matches[2];
	} elseif ( preg_match( '/(?:^|&)m=(\d{4})(\d{2})(?:&|$)/', $query, $matches ) ) {
		$year  = (int) $matches[1];
		$month = (int) $matches[2];
	}

	if ( $year <= 0 || $month < 1 || $month > 12 ) {
		return '';
	}

	return sprintf( '%04d年%02d月', $year, $month );
}

// 修正月度存档显示文本：例如把 2026年6月 统一为 2026年06月。
function qiumin_month_link( $link_html, $url ) {
	$label = qiumin_get_month_label( $url );

	if ( '' === $label ) {
		return $link_html;
	}

	return preg_replace_callback(
		'/(<a\b[^>]*>)(.*?)(<\/a>)/is',
		function ( $matches ) use ( $label ) {
			return $matches[1] . esc_html( $label ) . $matches[3];
		},
		$link_html,
		1
	);
}
add_filter( 'get_archives_link', 'qiumin_month_link', 10, 2 );

// 加载前台样式和脚本，版本号用于浏览器缓存刷新。
function qiumin_enqueue_assets() {
	wp_enqueue_style( 'qiumin-lib', get_template_directory_uri() . '/pic/aqy106_lib.css', array(), QIUMIN_THEME_VERSION );
	wp_enqueue_style( 'qiumin-style', get_stylesheet_uri(), array( 'qiumin-lib' ), QIUMIN_THEME_VERSION );
	wp_enqueue_script( 'qiumin-basic', get_template_directory_uri() . '/js/aqy106basic.js', array( 'jquery' ), QIUMIN_THEME_VERSION, true );
	wp_enqueue_script( 'qiumin-slider', get_template_directory_uri() . '/js/qiumin-slider.js', array( 'jquery' ), QIUMIN_THEME_VERSION, true );

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}
add_action( 'wp_enqueue_scripts', 'qiumin_enqueue_assets' );

// 菜单兜底：后台未设置主菜单时，自动输出首页、分类和页面导航。
function qiumin_nav_fallback( $args = null ) {
	$current_category = 0;

	if ( is_category() ) {
		$current_category = (int) get_queried_object_id();
	} elseif ( ! is_page() && ! is_home() ) {
		$categories = get_the_category();
		if ( ! empty( $categories ) ) {
			$current_category = (int) $categories[0]->term_id;
		}
	}

	echo '<ul id="primary-menu" class="b l">';
	echo '<li' . ( is_home() ? ' class="current"' : '' ) . '><a href="' . esc_url( home_url( '/' ) ) . '" title="' . esc_attr( get_bloginfo( 'name' ) ) . '">' . esc_html__( '首页', 'qiumin' ) . '</a></li>';
	wp_list_categories(
		array(
			'depth'            => 1,
			'title_li'         => '',
			'show_count'       => 0,
			'hide_empty'       => 0,
			'child_of'         => 0,
			'current_category' => $current_category,
		)
	);
	wp_list_pages(
		array(
			'depth'       => 1,
			'title_li'    => '',
			'sort_column' => 'menu_order',
		)
	);
	echo '</ul>';
}

// 统一处理开关型主题选项，避免保存非预期值。
function qiumin_option( $value ) {
	return '1' === (string) $value ? '1' : '0';
}

// 在后台外观菜单中增加主题设置页。
function qiumin_add_option_page() {
	add_theme_page( __( '主题设置', 'qiumin' ), __( '主题设置', 'qiumin' ), 'manage_options', basename( __FILE__ ), 'qiumin_theme_form' );
}
add_action( 'admin_menu', 'qiumin_add_option_page' );

// Add a lightweight image host under Media. Files are stored in wp-content/image.
if ( ! defined( 'QIUMIN_IMAGE_HOST_DIR' ) ) {
	define( 'QIUMIN_IMAGE_HOST_DIR', 'image' );
}

if ( ! defined( 'QIUMIN_IMAGE_HOST_ACCEPT' ) ) {
	define( 'QIUMIN_IMAGE_HOST_ACCEPT', 'image/jpeg,image/png,image/gif,image/webp,image/avif,image/bmp,image/x-icon' );
}

if ( ! defined( 'QIUMIN_IMAGE_HOST_DISPLAY_PATH' ) ) {
	define( 'QIUMIN_IMAGE_HOST_DISPLAY_PATH', 'wp-content/' . QIUMIN_IMAGE_HOST_DIR );
}

function qiumin_image_host_enabled() {
	return '0' !== (string) get_option( 'qiumin_image_host_enabled', '1' );
}

function qiumin_image_menu() {
	if ( ! qiumin_image_host_enabled() ) {
		return;
	}

	add_submenu_page( 'upload.php', __( '图床上传', 'qiumin' ), __( '图床上传', 'qiumin' ), 'upload_files', 'qiumin-image-host', 'qiumin_image_page', 1 );
}
add_action( 'admin_menu', 'qiumin_image_menu' );

function qiumin_image_upload_dir( $dirs ) {
	$dirs['path']    = WP_CONTENT_DIR . '/' . QIUMIN_IMAGE_HOST_DIR;
	$dirs['url']     = content_url( QIUMIN_IMAGE_HOST_DIR );
	$dirs['subdir']  = '';
	$dirs['basedir'] = WP_CONTENT_DIR . '/' . QIUMIN_IMAGE_HOST_DIR;
	$dirs['baseurl'] = content_url( QIUMIN_IMAGE_HOST_DIR );

	return $dirs;
}

function qiumin_image_allowed_mimes() {
	static $mimes = array(
		'jpg|jpeg|jpe' => 'image/jpeg',
		'png'          => 'image/png',
		'gif'          => 'image/gif',
		'webp'         => 'image/webp',
		'avif'         => 'image/avif',
		'bmp'          => 'image/bmp',
		'ico'          => 'image/x-icon',
	);

	return $mimes;
}

function qiumin_image_normalize_files( $files ) {
	$normalized = array();

	if ( empty( $files['name'] ) ) {
		return $normalized;
	}

	if ( ! is_array( $files['name'] ) ) {
		return array( $files );
	}

	foreach ( array_keys( $files['name'] ) as $index ) {
		$normalized[] = array(
			'name'     => isset( $files['name'][ $index ] ) ? $files['name'][ $index ] : '',
			'type'     => isset( $files['type'][ $index ] ) ? $files['type'][ $index ] : '',
			'tmp_name' => isset( $files['tmp_name'][ $index ] ) ? $files['tmp_name'][ $index ] : '',
			'error'    => isset( $files['error'][ $index ] ) ? $files['error'][ $index ] : UPLOAD_ERR_NO_FILE,
			'size'     => isset( $files['size'][ $index ] ) ? $files['size'][ $index ] : 0,
		);
	}

	return $normalized;
}

function qiumin_image_upload_files( $raw_files ) {
	$upload_path = WP_CONTENT_DIR . '/' . QIUMIN_IMAGE_HOST_DIR;

	if ( empty( $raw_files ) ) {
		return array(
			array(
				'success' => false,
				'error'   => __( '请选择要上传的图片。', 'qiumin' ),
			),
		);
	}

	if ( ! wp_mkdir_p( $upload_path ) ) {
		return array(
			array(
				'success' => false,
				'error'   => sprintf( __( '无法创建 %s 目录，请检查目录权限。', 'qiumin' ), QIUMIN_IMAGE_HOST_DISPLAY_PATH ),
			),
		);
	}

	require_once ABSPATH . 'wp-admin/includes/file.php';

	$results = array();
	$files   = qiumin_image_normalize_files( $raw_files );
	$mimes   = qiumin_image_allowed_mimes();

	add_filter( 'upload_dir', 'qiumin_image_upload_dir' );

	foreach ( $files as $file ) {
		if ( UPLOAD_ERR_NO_FILE === (int) $file['error'] ) {
			continue;
		}

		$uploaded = wp_handle_upload(
			$file,
			array(
				'test_form' => false,
				'mimes'     => $mimes,
			)
		);

		if ( isset( $uploaded['error'] ) ) {
			$results[] = array(
				'success' => false,
				'name'    => isset( $file['name'] ) ? sanitize_file_name( $file['name'] ) : '',
				'error'   => $uploaded['error'],
			);
			continue;
		}

		$url       = set_url_scheme( $uploaded['url'], is_ssl() ? 'https' : 'http' );
		$filename  = basename( $uploaded['file'] );
		$alt       = preg_replace( '/\.[^.]+$/', '', $filename );
		$results[] = array(
			'success'  => true,
			'name'     => $filename,
			'url'      => esc_url_raw( $url ),
			'html'     => '<img src="' . esc_url( $url ) . '" alt="' . esc_attr( $alt ) . '" />',
			'markdown' => '![' . $alt . '](' . esc_url_raw( $url ) . ')',
		);
	}

	remove_filter( 'upload_dir', 'qiumin_image_upload_dir' );

	if ( empty( $results ) ) {
		$results[] = array(
			'success' => false,
			'error'   => __( '没有可上传的图片。', 'qiumin' ),
		);
	}

	return $results;
}

function qiumin_image_handle_uploads() {
	$image_action = isset( $_POST['image_action'] ) ? sanitize_key( wp_unslash( $_POST['image_action'] ) ) : '';

	if ( 'upload' !== $image_action ) {
		return array();
	}

	if ( ! qiumin_image_host_enabled() ) {
		wp_die( esc_html__( '图床上传功能已关闭。', 'qiumin' ) );
	}

	if ( ! current_user_can( 'upload_files' ) ) {
		wp_die( esc_html__( '你没有上传文件的权限。', 'qiumin' ) );
	}

	check_admin_referer( 'image_upload', 'image_nonce' );

	return qiumin_image_upload_files( isset( $_FILES['image_files'] ) ? $_FILES['image_files'] : array() );
}

function qiumin_image_ajax_upload() {
	if ( ! qiumin_image_host_enabled() ) {
		wp_send_json_error(
			array(
				'message' => __( '图床上传功能已关闭。', 'qiumin' ),
			)
		);
	}

	if ( ! current_user_can( 'upload_files' ) ) {
		wp_send_json_error(
			array(
				'message' => __( '你没有上传文件的权限。', 'qiumin' ),
			)
		);
	}

	check_ajax_referer( 'image_upload', 'image_nonce' );

	wp_send_json_success(
		array(
			'results' => qiumin_image_upload_files( isset( $_FILES['image_files'] ) ? $_FILES['image_files'] : array() ),
		)
	);
}
add_action( 'wp_ajax_image_upload', 'qiumin_image_ajax_upload' );

function qiumin_image_copy_field( $label, $value ) {
	$field_id = wp_unique_id( 'qiumin-image-host-field-' );
	?>
	<label class="qiumin-image-host__field" for="<?php echo esc_attr( $field_id ); ?>">
		<span><?php echo esc_html( $label ); ?></span>
		<input id="<?php echo esc_attr( $field_id ); ?>" type="text" class="regular-text code" readonly value="<?php echo esc_attr( $value ); ?>">
		<button type="button" class="button qiumin-image-host-copy" data-copy-target="<?php echo esc_attr( $field_id ); ?>"><?php esc_html_e( '复制', 'qiumin' ); ?></button>
	</label>
	<?php
}

function qiumin_image_page() {
	if ( ! qiumin_image_host_enabled() ) {
		wp_die( esc_html__( '图床上传功能已关闭。', 'qiumin' ) );
	}

	$results   = qiumin_image_handle_uploads();
	$files_url = content_url( QIUMIN_IMAGE_HOST_DIR );
	?>
	<div class="wrap qiumin-image-host">
		<h1><?php esc_html_e( '图床上传', 'qiumin' ); ?></h1>
		<p><?php echo esc_html( sprintf( __( '图片会保存到 %s，生成的链接可直接插入文章。', 'qiumin' ), QIUMIN_IMAGE_HOST_DISPLAY_PATH ) ); ?></p>

		<form method="post" enctype="multipart/form-data">
			<?php wp_nonce_field( 'image_upload', 'image_nonce' ); ?>
			<input type="hidden" name="image_action" value="upload">
			<table class="form-table" role="presentation">
				<tr>
					<th scope="row"><label for="image_files"><?php esc_html_e( '选择图片', 'qiumin' ); ?></label></th>
					<td>
						<input id="image_files" name="image_files[]" type="file" accept="<?php echo esc_attr( QIUMIN_IMAGE_HOST_ACCEPT ); ?>" multiple required>
						<p class="description"><?php esc_html_e( '支持 JPG、PNG、GIF、WebP、AVIF、BMP、ICO，可一次选择多张。', 'qiumin' ); ?></p>
						<p class="description"><?php echo esc_html( sprintf( __( '当前目录链接前缀：%s', 'qiumin' ), $files_url ) ); ?></p>
					</td>
				</tr>
			</table>
			<?php submit_button( __( '上传并生成链接', 'qiumin' ) ); ?>
		</form>

		<?php if ( ! empty( $results ) ) : ?>
			<h2><?php esc_html_e( '上传结果', 'qiumin' ); ?></h2>
			<div class="qiumin-image-host__results">
				<?php foreach ( $results as $result ) : ?>
					<?php if ( empty( $result['success'] ) ) : ?>
						<div class="notice notice-error inline"><p><?php echo esc_html( $result['error'] ); ?></p></div>
						<?php continue; ?>
					<?php endif; ?>
					<div class="qiumin-image-host__item">
						<div class="qiumin-image-host__preview">
							<img src="<?php echo esc_url( $result['url'] ); ?>" alt="">
						</div>
						<div class="qiumin-image-host__links">
							<strong><?php echo esc_html( $result['name'] ); ?></strong>
							<?php
							qiumin_image_copy_field( __( '图片 URL', 'qiumin' ), $result['url'] );
							qiumin_image_copy_field( __( 'HTML', 'qiumin' ), $result['html'] );
							qiumin_image_copy_field( __( 'Markdown', 'qiumin' ), $result['markdown'] );
							?>
						</div>
					</div>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>
	</div>
	<style>
		.qiumin-image-host__item{display:flex;gap:16px;align-items:flex-start;max-width:960px;margin:14px 0;padding:14px;border:1px solid #dcdcde;background:#fff}
		.qiumin-image-host__preview{width:132px;min-width:132px;text-align:center}
		.qiumin-image-host__preview img{max-width:132px;max-height:132px;width:auto;height:auto}
		.qiumin-image-host__links{flex:1;min-width:0}
		.qiumin-image-host__field{display:flex;gap:8px;align-items:center;margin-top:10px}
		.qiumin-image-host__field span{width:90px;min-width:90px}
		.qiumin-image-host__field input{flex:1;max-width:none}
		@media (max-width: 782px){
			.qiumin-image-host__item,.qiumin-image-host__field{display:block}
			.qiumin-image-host__preview{width:auto;min-width:0;margin-bottom:12px;text-align:left}
			.qiumin-image-host__field span{display:block;width:auto;margin-bottom:4px}
			.qiumin-image-host__field .button{margin-top:6px}
		}
	</style>
	<script>
		document.addEventListener('click', function(event) {
			var button = event.target.closest('.qiumin-image-host-copy');
			if (!button) {
				return;
			}

			var field = document.getElementById(button.getAttribute('data-copy-target'));
			if (!field) {
				return;
			}

			field.select();
			field.setSelectionRange(0, field.value.length);

			if (navigator.clipboard && navigator.clipboard.writeText) {
				navigator.clipboard.writeText(field.value);
			} else {
				document.execCommand('copy');
			}

			button.textContent = '<?php echo esc_js( __( '已复制', 'qiumin' ) ); ?>';
			window.setTimeout(function() {
				button.textContent = '<?php echo esc_js( __( '复制', 'qiumin' ) ); ?>';
			}, 1200);
		});
	</script>
	<?php
}

function qiumin_image_media_button() {
	if ( ! qiumin_image_host_enabled() || ! current_user_can( 'upload_files' ) ) {
		return;
	}

	echo '<a href="#TB_inline?width=760&height=640&inlineId=image-editor-modal" class="button thickbox" id="image-editor-modal-open">' . esc_html__( '图床上传', 'qiumin' ) . '</a>';
}
add_action( 'media_buttons', 'qiumin_image_media_button', 20 );

function qiumin_image_editor_assets( $hook_suffix ) {
	if ( ! in_array( $hook_suffix, array( 'post.php', 'post-new.php' ), true ) || ! qiumin_image_host_enabled() || ! current_user_can( 'upload_files' ) ) {
		return;
	}

	add_thickbox();

	$button_css = '.wp-media-buttons #image-editor-modal-open{display:inline-flex;align-items:center;justify-content:center;box-sizing:border-box;line-height:normal;vertical-align:top}';
	wp_add_inline_style( 'common', $button_css );
}
add_action( 'admin_enqueue_scripts', 'qiumin_image_editor_assets' );

function qiumin_image_editor_modal() {
	if ( ! qiumin_image_host_enabled() || ! current_user_can( 'upload_files' ) ) {
		return;
	}
	?>
	<div id="image-editor-modal" style="display:none;">
		<div class="image-editor-modal">
			<form id="image-editor-upload-form" class="image-editor-upload-form" enctype="multipart/form-data">
				<?php wp_nonce_field( 'image_upload', 'image_nonce' ); ?>
				<div class="uploader-inline image-editor-uploader">
					<div class="uploader-inline-content">
						<div class="upload-ui">
							<h2 class="upload-instructions drop-instructions"><?php esc_html_e( '拖文件到这里', 'qiumin' ); ?></h2>
							<p class="upload-instructions drop-instructions"><?php esc_html_e( '或', 'qiumin' ); ?></p>
							<button type="button" class="browser button button-hero" id="image-editor-upload-select"><?php esc_html_e( '选择文件', 'qiumin' ); ?></button>
							<input id="image-editor-upload-input" class="image-editor-upload-input" type="file" name="image_files[]" accept="<?php echo esc_attr( QIUMIN_IMAGE_HOST_ACCEPT ); ?>" multiple>
						</div>
						<p class="max-upload-size"><?php echo esc_html( sprintf( __( '图片会保存到 %s。', 'qiumin' ), QIUMIN_IMAGE_HOST_DISPLAY_PATH ) ); ?></p>
					</div>
				</div>
				<div class="image-editor-upload-status">
					<span class="spinner"></span>
					<div id="image-editor-upload-message" class="image-editor-upload-message"></div>
				</div>
				<p class="submit"><input type="submit" name="save" id="button-primary" class="button-primary" value="<?php esc_attr_e( 'Save Changes' ); ?>" /></p>
			</form>
			<div id="image-editor-upload-results" class="image-editor-upload-results"></div>
		</div>
	</div>
	<style>
		.image-editor-modal{padding:0 4px 4px;box-sizing:border-box;max-width:100%}
		.image-editor-modal *{box-sizing:border-box}
		.image-editor-upload-form{margin:0;max-width:100%}
		.image-editor-uploader{position:relative;width:100%;max-width:100%;margin:0;background:#fff;border:4px dashed #c3c4c7;min-height:300px}
		.image-editor-uploader.is-dragover{border-color:#2271b1}
		.image-editor-uploader .uploader-inline-content{position:absolute;top:50%;left:0;right:0;max-width:100%;transform:translateY(-50%);padding:0 20px;text-align:center}
		.image-editor-uploader .upload-ui{margin:0}
		.image-editor-uploader .upload-instructions{max-width:100%;overflow-wrap:anywhere}
		.image-editor-uploader .button-hero{max-width:100%;white-space:normal}
		.image-editor-uploader .max-upload-size{max-width:100%;overflow-wrap:anywhere}
		.image-editor-upload-input{position:absolute;width:1px;height:1px;overflow:hidden;clip:rect(0 0 0 0);clip-path:inset(50%);white-space:nowrap}
		.image-editor-upload-status{min-height:24px;margin:12px 0;display:flex;align-items:center;gap:8px}
		.image-editor-modal .spinner{float:none;margin:0}
		.image-editor-upload-message{display:block}
		.image-editor-upload-item{display:grid;grid-template-columns:96px minmax(0,1fr);gap:12px;align-items:start;margin:12px 0;padding:12px;border:1px solid #dcdcde;background:#fff}
		.image-editor-upload-item img{display:block;max-width:96px;max-height:96px;width:auto;height:auto}
		.image-editor-upload-item input{width:100%;margin:8px 0}
		.image-editor-upload-item .button{margin-right:6px}
		body.image-editor-modal-open #TB_ajaxContent{box-sizing:border-box;max-width:100%;overflow-x:hidden}
		@media (max-width: 600px){
			body.image-editor-modal-open #TB_window{left:12px!important;right:12px!important;width:auto!important;max-width:none!important;margin-left:0!important}
			body.image-editor-modal-open #TB_ajaxContent{width:100%!important;height:auto!important;max-height:calc(100vh - 72px);padding:12px;overflow-y:auto}
			.image-editor-modal{padding:0}
			.image-editor-uploader{min-height:220px;border-width:3px}
			.image-editor-uploader .uploader-inline-content{padding:0 12px}
			.image-editor-upload-item{display:block}
			.image-editor-upload-item img{margin-bottom:10px}
		}
	</style>
	<script>
		jQuery(function($) {
			var $form = $('#image-editor-upload-form');
			var $message = $('#image-editor-upload-message');
			var $results = $('#image-editor-upload-results');
			var $uploader = $('.image-editor-uploader');
			var $fileInput = $('#image-editor-upload-input');
			var $selectButton = $('#image-editor-upload-select');

			$('#image-editor-modal-open').on('click', function() {
				$('body').addClass('image-editor-modal-open');
			});

			if (typeof window.tb_remove === 'function' && !window.imageEditorTbRemoveWrapped) {
				var originalTbRemove = window.tb_remove;
				window.tb_remove = function() {
					$('body').removeClass('image-editor-modal-open');
					return originalTbRemove.apply(this, arguments);
				};
				window.imageEditorTbRemoveWrapped = true;
			}

			function insertImage(html, url) {
				if (window.send_to_editor) {
					window.send_to_editor(html);
					tb_remove();
					return;
				}

				if (window.wp && wp.blocks && wp.data) {
					wp.data.dispatch('core/block-editor').insertBlocks(
						wp.blocks.createBlock('core/image', { url: url })
					);
					tb_remove();
				}
			}

			function renderResult(result) {
				if (!result.success) {
					return '<div class="notice notice-error inline"><p>' + $('<div>').text(result.error || '').html() + '</p></div>';
				}

				var url = $('<div>').text(result.url).html();
				var name = $('<div>').text(result.name).html();
				var encodedHtml = encodeURIComponent(result.html || '');

				return '<div class="image-editor-upload-item">' +
					'<div><img src="' + url + '" alt=""></div>' +
					'<div>' +
					'<strong>' + name + '</strong>' +
					'<input type="text" class="regular-text code" readonly value="' + url + '">' +
					'<button type="button" class="button button-primary image-editor-insert" data-url="' + url + '" data-html="' + encodedHtml + '"><?php echo esc_js( __( '插入文章', 'qiumin' ) ); ?></button>' +
					'<button type="button" class="button image-editor-copy" data-copy="' + url + '"><?php echo esc_js( __( '复制链接', 'qiumin' ) ); ?></button>' +
					'</div>' +
					'</div>';
			}

			$form.on('submit', function(event) {
				event.preventDefault();

				var formData = new FormData(this);
				formData.append('action', 'image_upload');

				$form.find('.spinner').addClass('is-active');
				$message.empty();

				$.ajax({
					url: ajaxurl,
					type: 'POST',
					data: formData,
					processData: false,
					contentType: false
				}).done(function(response) {
					if (!response || !response.success) {
						$message.html('<div class="notice notice-error inline"><p><?php echo esc_js( __( '上传失败，请重试。', 'qiumin' ) ); ?></p></div>');
						return;
					}

					var html = '';
					$.each(response.data.results || [], function(index, result) {
						html += renderResult(result);
					});

					$results.html(html);
					$form[0].reset();
				}).fail(function() {
					$message.html('<div class="notice notice-error inline"><p><?php echo esc_js( __( '上传失败，请重试。', 'qiumin' ) ); ?></p></div>');
				}).always(function() {
					$form.find('.spinner').removeClass('is-active');
				});
			});

			$selectButton.on('click', function() {
				$fileInput.trigger('click');
			});

			$fileInput.on('change', function() {
				$form.trigger('submit');
			});

			$uploader.on('dragenter dragover', function(event) {
				event.preventDefault();
				event.stopPropagation();
				$uploader.addClass('is-dragover');
			});

			$uploader.on('dragleave dragend drop', function(event) {
				event.preventDefault();
				event.stopPropagation();
				$uploader.removeClass('is-dragover');
			});

			$uploader.on('drop', function(event) {
				var files = event.originalEvent.dataTransfer ? event.originalEvent.dataTransfer.files : null;
				if (!files || !files.length) {
					return;
				}

				try {
					$fileInput[0].files = files;
					$form.trigger('submit');
				} catch (error) {
					$message.text('<?php echo esc_js( __( '请点击选择文件上传。', 'qiumin' ) ); ?>');
				}
			});

			$(document).on('click', '.image-editor-insert', function() {
				insertImage(decodeURIComponent($(this).attr('data-html') || ''), $(this).attr('data-url'));
			});

			$(document).on('click', '.image-editor-copy', function() {
				var button = this;
				var text = $(button).attr('data-copy');
				if (navigator.clipboard && navigator.clipboard.writeText) {
					navigator.clipboard.writeText(text);
				}
				$(button).text('<?php echo esc_js( __( '已复制', 'qiumin' ) ); ?>');
				window.setTimeout(function() {
					$(button).text('<?php echo esc_js( __( '复制链接', 'qiumin' ) ); ?>');
				}, 1200);
			});
		});
	</script>
	<?php
}
add_action( 'admin_footer-post.php', 'qiumin_image_editor_modal' );
add_action( 'admin_footer-post-new.php', 'qiumin_image_editor_modal' );

// 注册主题设置项，并指定每项的类型、清洗函数和默认值。
function qiumin_register_settings() {
	register_setting(
		'theme-settings',
		'web_tip',
		array(
			'type'              => 'string',
			'sanitize_callback' => 'wp_kses_post',
			'default'           => '',
		)
	);

	register_setting(
		'theme-settings',
		'web_if_tip',
		array(
			'type'              => 'string',
			'sanitize_callback' => 'qiumin_option',
			'default'           => '0',
		)
	);

	register_setting(
		'theme-settings',
		'qiumin_slider_enabled',
		array(
			'type'              => 'string',
			'sanitize_callback' => 'qiumin_option',
			'default'           => '1',
		)
	);

	register_setting(
		'theme-settings',
		'qiumin_image_host_enabled',
		array(
			'type'              => 'string',
			'sanitize_callback' => 'qiumin_option',
			'default'           => '1',
		)
	);

	register_setting(
		'theme-settings',
		'qiumin_slider_category',
		array(
			'type'              => 'integer',
			'sanitize_callback' => 'absint',
			'default'           => 0,
		)
	);

	register_setting(
		'theme-settings',
		'qiumin_slider_count',
		array(
			'type'              => 'integer',
			'sanitize_callback' => 'absint',
			'default'           => 5,
		)
	);

	register_setting(
		'theme-settings',
		'qiumin_slider_interval',
		array(
			'type'              => 'integer',
			'sanitize_callback' => 'absint',
			'default'           => 5,
		)
	);

	register_setting(
		'theme-settings',
		'qiumin_slider_height',
		array(
			'type'              => 'integer',
			'sanitize_callback' => 'absint',
			'default'           => 360,
		)
	);
}
add_action( 'admin_init', 'qiumin_register_settings' );

// 后台主题设置表单：配置公告、首页幻灯片、图床上传和显示参数。
function qiumin_theme_form() {
	?>
	<div class="setingBox">
		<h2 class="pt10 pb10 pl20"><?php esc_html_e( '主题设置', 'qiumin' ); ?></h2>
		<form method="post" action="options.php">
			<?php settings_fields( 'theme-settings' ); ?>
			<div class="setingBoxIn">
				<div class="setingBoxTit">
					<select name="web_if_tip" class="status">
						<option value="1" <?php selected( get_option( 'web_if_tip' ), '1' ); ?>><?php esc_html_e( '显示', 'qiumin' ); ?></option>
						<option value="0" <?php selected( get_option( 'web_if_tip' ), '0' ); ?>><?php esc_html_e( '不显示', 'qiumin' ); ?></option>
					</select>
					<?php esc_html_e( '网站公告设置', 'qiumin' ); ?>
				</div>
				<p class="notices"><strong>*</strong><?php esc_html_e( '每条公告请放在 <li></li> 之间，可设置多条公告前台滚动，支持常用 HTML 代码。', 'qiumin' ); ?></p>
				<div class="setingBoxInside">
					<div class="text"><textarea name="web_tip"><?php echo esc_textarea( get_option( 'web_tip', '' ) ); ?></textarea></div>
				</div>
				<div class="setingBoxTit"><?php esc_html_e( '首页幻灯片设置', 'qiumin' ); ?></div>
				<div class="setingBoxInside">
					<p>
						<label>
							<?php esc_html_e( '显示幻灯片：', 'qiumin' ); ?>
							<select name="qiumin_slider_enabled">
								<option value="1" <?php selected( get_option( 'qiumin_slider_enabled', '1' ), '1' ); ?>><?php esc_html_e( '显示', 'qiumin' ); ?></option>
								<option value="0" <?php selected( get_option( 'qiumin_slider_enabled', '1' ), '0' ); ?>><?php esc_html_e( '隐藏', 'qiumin' ); ?></option>
							</select>
						</label>
					</p>
					<p>
						<?php esc_html_e( '文章分类：', 'qiumin' ); ?>
						<?php
						wp_dropdown_categories(
							array(
								'name'              => 'qiumin_slider_category',
								'selected'          => (int) get_option( 'qiumin_slider_category', 0 ),
								'show_option_none'  => esc_html__( '全部文章', 'qiumin' ),
								'option_none_value' => 0,
								'hide_empty'        => 0,
							)
						);
						?>
					</p>
					<p>
						<label>
							<?php esc_html_e( '显示数量：', 'qiumin' ); ?>
							<select name="qiumin_slider_count">
								<?php for ( $i = 3; $i <= 8; $i++ ) : ?>
									<option value="<?php echo esc_attr( $i ); ?>" <?php selected( (int) get_option( 'qiumin_slider_count', 5 ), $i ); ?>><?php echo esc_html( $i ); ?></option>
								<?php endfor; ?>
							</select>
						</label>
					</p>
					<p>
						<label>
							<?php esc_html_e( '切换间隔：', 'qiumin' ); ?>
							<select name="qiumin_slider_interval">
								<?php foreach ( array( 3, 5, 8, 10 ) as $seconds ) : ?>
									<option value="<?php echo esc_attr( $seconds ); ?>" <?php selected( (int) get_option( 'qiumin_slider_interval', 5 ), $seconds ); ?>><?php echo esc_html( $seconds ); ?>s</option>
								<?php endforeach; ?>
							</select>
						</label>
					</p>
					<p>
						<label>
							<?php esc_html_e( '幻灯片高度：', 'qiumin' ); ?>
							<select name="qiumin_slider_height">
								<?php foreach ( array( 300, 360, 420 ) as $height ) : ?>
									<option value="<?php echo esc_attr( $height ); ?>" <?php selected( (int) get_option( 'qiumin_slider_height', 360 ), $height ); ?>><?php echo esc_html( $height ); ?>px</option>
								<?php endforeach; ?>
							</select>
						</label>
					</p>
				</div>
				<div class="setingBoxTit"><?php esc_html_e( '图床上传设置', 'qiumin' ); ?></div>
				<div class="setingBoxInside">
					<p>
						<label>
							<?php esc_html_e( '图床上传：', 'qiumin' ); ?>
							<select name="qiumin_image_host_enabled">
								<option value="1" <?php selected( get_option( 'qiumin_image_host_enabled', '1' ), '1' ); ?>><?php esc_html_e( '开启', 'qiumin' ); ?></option>
								<option value="0" <?php selected( get_option( 'qiumin_image_host_enabled', '1' ), '0' ); ?>><?php esc_html_e( '关闭', 'qiumin' ); ?></option>
							</select>
						</label>
					</p>
				</div>
			</div>
			<p class="submit"><input type="submit" name="save" id="button-primary" class="button-primary" value="<?php esc_attr_e( 'Save Changes' ); ?>" /></p>
		</form>
	</div>
	<style type="text/css">
		.setingBox{min-width:255px; width:50%; float:left; margin-left:20px; margin-bottom:20px;}
		.setingBoxIn{background-color:#F5F5F5; background-image:linear-gradient(#F9F9F9, #F5F5F5); border:#DFDFDF 1px solid; border-radius:3px; box-shadow:0 1px 0 #FFFFFF inset;}
		.setingBoxIn .setingBoxTit{border-bottom-color:#DFDFDF; box-shadow:0 1px 0 #FFFFFF; text-shadow:0 1px 0 #FFFFFF; background-color:#F1F1F1; background-image:linear-gradient(#F9F9F9, #ECECEC); border-bottom:#DFDFDF 1px solid; font-size:15px; font-weight:normal; line-height:24px; margin:0; padding:4px 5px;}
		.setingBoxIn p.notices{color:#999; font-size:12px; line-height:20px; padding-left:15px; margin:5px 0;}
		.setingBoxIn p.notices strong{color:#F00;}
		.status{float:right;}
		.setingBoxInside{padding:0 15px 15px 15px;}
		.setingBoxInside .text textarea{width:100%; height:150px;}
		.setingBox p.submit{text-align:right; padding:0;}
	</style>
	<?php
}

// 将 Gravatar 域名替换为国内可访问的镜像域名。
function qiumin_filter_avatar( $avatar ) {
	$new_gravatar_server = 'cravatar.cn';

	$sources = array(
		'www.gravatar.com/avatar/',
		'0.gravatar.com/avatar/',
		'1.gravatar.com/avatar/',
		'2.gravatar.com/avatar/',
		'secure.gravatar.com/avatar/',
		'cn.gravatar.com/avatar/',
	);

	return str_replace( $sources, $new_gravatar_server . '/avatar/', $avatar );
}
add_filter( 'get_avatar', 'qiumin_filter_avatar' );

// 文章首行缩进，仅作用于正文页的主循环内容。
function qiumin_text_indent( $text ) {
	if ( ! is_singular() || ! in_the_loop() || ! is_main_query() ) {
		return $text;
	}

	return preg_replace_callback(
		'/<p\b([^>]*)>/i',
		function ( $matches ) {
			$attrs = $matches[1];
			if ( preg_match( '/\sstyle=(["\'])(.*?)\1/i', $attrs ) ) {
				$attrs = preg_replace( '/\sstyle=(["\'])(.*?)\1/i', ' style=$1text-indent:1em;$2$1', $attrs, 1 );
			} else {
				$attrs .= ' style="text-indent:1em;"';
			}

			return '<p' . $attrs . '>';
		},
		$text
	);
}
add_filter( 'the_content', 'qiumin_text_indent', 12 );

// 隐藏评论表单中的 Cookie 同意复选框，保持旧版主题表单布局。
function qiumin_comment_cookies_consent( $fields ) {
	unset( $fields['cookies'] );
	return $fields;
}
add_filter( 'comment_form_default_fields', 'qiumin_comment_cookies_consent' );

// 禁用 Open Sans，减少外部字体加载。
function qiumin_remove_open_sans() {
	wp_deregister_style( 'open-sans' );
}
add_action( 'wp_enqueue_scripts', 'qiumin_remove_open_sans', 20 );
add_action( 'admin_enqueue_scripts', 'qiumin_remove_open_sans', 20 );

// 列表页只显示摘要，详情页保留完整正文。
function qiumin_trim_archive_content( $content ) {
	if ( is_singular() || ! in_the_loop() || ! is_main_query() ) {
		return $content;
	}

	return wp_trim_words( wp_strip_all_tags( $content ), 180, '' );
}
add_filter( 'the_content', 'qiumin_trim_archive_content', 20 );

// 文章缩略图获取顺序：自定义字段、特色图像、正文首图、默认图。
function qiumin_get_image_url( $post = null ) {
	$post = get_post( $post );

	if ( ! $post ) {
		return get_template_directory_uri() . '/pic/mogo.png';
	}

	$custom_thumb = get_post_meta( $post->ID, 'thumb', true );
	if ( $custom_thumb ) {
		return esc_url_raw( $custom_thumb );
	}

	if ( has_post_thumbnail( $post ) ) {
		$thumbnail_src = wp_get_attachment_image_url( get_post_thumbnail_id( $post ), 'medium' );
		if ( $thumbnail_src ) {
			return $thumbnail_src;
		}
	}

	if ( preg_match( '/<img[^>]+src=[\'"]([^\'"]+)[\'"]/i', $post->post_content, $matches ) ) {
		return esc_url_raw( $matches[1] );
	}

	return get_template_directory_uri() . '/pic/mogo.png';
}

// 兼容旧模板函数名：输出列表缩略图地址。
function qiumin_thumb_image() {
	return qiumin_get_image_url();
}

if ( ! function_exists( 'thumb_image' ) ) {
	function thumb_image() {
		return qiumin_thumb_image();
	}
}

// 兼容旧模板函数名：输出文章推荐缩略图地址。
function qiumin_post_thumbnail_src() {
	return qiumin_get_image_url();
}

if ( ! function_exists( 'post_thumbnail_src' ) ) {
	function post_thumbnail_src() {
		return qiumin_post_thumbnail_src();
	}
}

// 首页幻灯片渲染：按后台设置读取文章并生成轮播结构。
function qiumin_render_slider() {
	if ( '0' === (string) get_option( 'qiumin_slider_enabled', '1' ) ) {
		return;
	}

	$category = (int) get_option( 'qiumin_slider_category', 0 );
	$count    = min( 8, max( 3, (int) get_option( 'qiumin_slider_count', 5 ) ) );
	$interval = max( 3, (int) get_option( 'qiumin_slider_interval', 5 ) ) * 1000;
	$height   = min( 420, max( 260, (int) get_option( 'qiumin_slider_height', 360 ) ) );

	$args = array(
		'posts_per_page'      => $count,
		'post_status'         => 'publish',
		'ignore_sticky_posts' => true,
	);

	if ( $category > 0 ) {
		$args['cat'] = $category;
	}

	$slides = new WP_Query( $args );
	if ( ! $slides->have_posts() ) {
		wp_reset_postdata();
		return;
	}

	$slide_items = array();
	while ( $slides->have_posts() ) {
		$slides->the_post();
		$slide_items[] = array(
			'id'        => get_the_ID(),
			'title'     => get_the_title(),
			'excerpt'   => wp_trim_words( wp_strip_all_tags( get_the_excerpt() ), 34, '...' ),
			'permalink' => get_permalink(),
			'thumbnail' => qiumin_get_image_url( get_the_ID() ),
			'date'      => get_the_date( 'Y-m-d' ),
		);
	}
	wp_reset_postdata();

	if ( empty( $slide_items ) ) {
		return;
	}

	$slide_id = 'qiumin-slider-' . wp_rand( 1000, 9999 );
	?>
	<div class="qiumin-slider-wrapper" style="--slider-height: <?php echo esc_attr( $height ); ?>px;">
		<div id="<?php echo esc_attr( $slide_id ); ?>" class="qiumin-slider" data-interval="<?php echo esc_attr( $interval ); ?>" aria-label="<?php esc_attr_e( '首页幻灯片', 'qiumin' ); ?>">
			<div class="qiumin-slider__track">
				<?php foreach ( $slide_items as $index => $slide ) : ?>
					<div class="qiumin-slider__slide<?php echo 0 === $index ? ' is-active' : ''; ?>" data-index="<?php echo esc_attr( $index ); ?>">
						<a href="<?php echo esc_url( $slide['permalink'] ); ?>" class="qiumin-slider__link">
							<img class="qiumin-slider__image" src="<?php echo esc_url( $slide['thumbnail'] ); ?>" alt="<?php echo esc_attr( $slide['title'] ); ?>" loading="<?php echo 0 === $index ? 'eager' : 'lazy'; ?>" decoding="async">
							<span class="qiumin-slider__shade"></span>
							<span class="qiumin-slider__content">
								<span class="qiumin-slider__meta">
									<?php
									$cats = get_the_category( $slide['id'] );
									if ( ! empty( $cats ) ) {
										echo '<span class="qiumin-slider__category">' . esc_html( $cats[0]->name ) . '</span>';
									}
									?>
									<span><?php echo esc_html( $slide['date'] ); ?></span>
								</span>
								<span class="qiumin-slider__title"><?php echo esc_html( $slide['title'] ); ?></span>
								<span class="qiumin-slider__excerpt"><?php echo esc_html( $slide['excerpt'] ); ?></span>
							</span>
						</a>
					</div>
				<?php endforeach; ?>
			</div>
			<button class="qiumin-slider__arrow qiumin-slider__arrow--prev" type="button" aria-label="<?php esc_attr_e( '上一张', 'qiumin' ); ?>">&#8249;</button>
			<button class="qiumin-slider__arrow qiumin-slider__arrow--next" type="button" aria-label="<?php esc_attr_e( '下一张', 'qiumin' ); ?>">&#8250;</button>
			<div class="qiumin-slider__dots">
				<?php foreach ( $slide_items as $index => $slide ) : ?>
					<button class="qiumin-slider__dot<?php echo 0 === $index ? ' is-active' : ''; ?>" type="button" data-index="<?php echo esc_attr( $index ); ?>" aria-label="<?php echo esc_attr( sprintf( __( '切换到第 %d 张', 'qiumin' ), $index + 1 ) ); ?>"></button>
				<?php endforeach; ?>
			</div>
		</div>
	</div>
	<?php
}

// 获取文章阅读量，未记录时返回 0。
function qiumin_get_post_views( $postID ) {
	$count = get_post_meta( (int) $postID, 'views', true );
	return ( '' === $count || ! $count ) ? '0' : (string) absint( $count );
}

if ( ! function_exists( 'getPostViews' ) ) {
	function getPostViews( $postID ) {
		return qiumin_get_post_views( $postID );
	}
}

// 递增文章阅读量，由详情页和页面模板按 Cookie 控制调用频率。
function qiumin_set_post_views( $postID ) {
	$postID = (int) $postID;
	$count  = (int) get_post_meta( $postID, 'views', true );

	update_post_meta( $postID, 'views', $count + 1 );
}

if ( ! function_exists( 'setPostViews' ) ) {
	function setPostViews( $postID ) {
		qiumin_set_post_views( $postID );
	}
}

function qiumin_record_post_view( $post_id ) {
	$post_id = (int) $post_id;
	if ( $post_id <= 0 ) {
		return;
	}

	$cookie_name = 'views' . $post_id . COOKIEHASH;
	$viewed      = isset( $_COOKIE[ $cookie_name ] ) ? sanitize_text_field( wp_unslash( $_COOKIE[ $cookie_name ] ) ) : '';
	if ( '1' === $viewed ) {
		return;
	}

	qiumin_set_post_views( $post_id );

	$cookie_path   = defined( 'COOKIEPATH' ) && COOKIEPATH ? COOKIEPATH : '/';
	$cookie_domain = defined( 'COOKIE_DOMAIN' ) && COOKIE_DOMAIN ? COOKIE_DOMAIN : '';

	setcookie( $cookie_name, '1', time() + YEAR_IN_SECONDS, $cookie_path, $cookie_domain, is_ssl(), true );
}

// 输出分页导航，兼容文章列表、归档和搜索结果。
function qiumin_pages( $query_string = '' ) {
	global $wp_query;

	$total_pages = isset( $wp_query->max_num_pages ) ? (int) $wp_query->max_num_pages : 1;
	if ( $total_pages <= 1 ) {
		return;
	}

	$current_page = max( 1, (int) get_query_var( 'paged' ), (int) get_query_var( 'page' ) );

	echo '<nav class="pagination" aria-label="' . esc_attr__( '分页导航', 'qiumin' ) . '">';
	echo wp_kses_post(
		paginate_links(
			array(
				'base'      => str_replace( 999999999, '%#%', esc_url( get_pagenum_link( 999999999 ) ) ),
				'format'    => '?paged=%#%',
				'current'   => $current_page,
				'total'     => $total_pages,
				'mid_size'  => 2,
				'end_size'  => 1,
				'prev_text' => esc_html__( '上一页', 'qiumin' ),
				'next_text' => esc_html__( '下一页', 'qiumin' ),
			)
		)
	);
	echo '</nav>';
}

if ( ! function_exists( 'pages' ) ) {
	function pages( $query_string = '' ) {
		qiumin_pages( $query_string );
	}
}

// 获取并缓存 Bing 每日图，用于登录页背景。
function qiumin_bing_login_image() {
	$cached = get_transient( 'qiumin_bing_login_image' );
	if ( $cached ) {
		return $cached;
	}

	$response = wp_remote_get(
		'https://cn.bing.com/HPImageArchive.aspx?format=js&idx=0&n=1',
		array( 'timeout' => 3 )
	);

	if ( is_wp_error( $response ) ) {
		return '';
	}

	$data = json_decode( wp_remote_retrieve_body( $response ), true );
	if ( empty( $data['images'][0]['url'] ) ) {
		return '';
	}

	$image_url = esc_url_raw( 'https://cn.bing.com' . $data['images'][0]['url'] );
	set_transient( 'qiumin_bing_login_image', $image_url, DAY_IN_SECONDS );

	return $image_url;
}

// 登录页背景样式注入。
function qiumin_login_head() {
	$imgurl = qiumin_bing_login_image();
	if ( ! $imgurl ) {
		return;
	}
	?>
	<style type="text/css">
		body {
			background: url('<?php echo esc_url( $imgurl ); ?>') no-repeat center center fixed;
			background-size: cover;
		}
	</style>
	<?php
}
add_action( 'login_head', 'qiumin_login_head' );

// 标签云链接随机着色，让侧边栏标签更醒目。
function qiumin_color_cloud( $text ) {
	return preg_replace_callback( '|<a (.+?)>|i', 'qiumin_color_cloud_callback', $text );
}

// 为单个标签云链接补充或合并 color 样式。
function qiumin_color_cloud_callback( $matches ) {
	$text  = $matches[1];
	$color = sprintf( '%06x', wp_rand( 0, 0xFFFFFF ) );

	if ( preg_match( '/style=(\'|")(.*?)(\'|")/i', $text ) ) {
		$text = preg_replace( '/style=(\'|")(.*?)(\'|")/i', 'style="color:#' . $color . ';$2"', $text );
	} else {
		$text .= ' style="color:#' . $color . ';"';
	}

	return '<a ' . $text . '>';
}
add_filter( 'wp_tag_cloud', 'qiumin_color_cloud', 1 );

// 启用旧版链接管理器，供首页友情链接继续使用。
add_filter( 'pre_option_link_manager_enabled', '__return_true' );

// 关闭 WordPress 内置站点地图，避免和自定义地图入口冲突。
add_filter( 'wp_sitemaps_enabled', '__return_false' );

// HTTPS 页面输出前开启缓冲，用于把站内 http 资源替换为协议相对地址。
function qiumin_ssl_buffer_start( $name = null, $args = array() ) {
	if ( ! is_ssl() ) {
		return;
	}

	ob_start( 'qiumin_ssl_buffer_replace' );
}
add_action( 'get_header', 'qiumin_ssl_buffer_start' );

// 替换站点和上传目录的 http 协议，减少混合内容警告。
function qiumin_ssl_buffer_replace( $content ) {
	$siteurl    = get_option( 'siteurl' );
	$upload_dir = wp_upload_dir();

	$content = str_replace( 'http:' . strstr( $siteurl, '//' ), strstr( $siteurl, '//' ), $content );
	$content = str_replace( 'http:' . strstr( $upload_dir['baseurl'], '//' ), strstr( $upload_dir['baseurl'], '//' ), $content );

	return $content;
}

// 在单篇文章和 Feed 末尾追加版权声明。
function qiumin_append_copyright( $content ) {
	if ( is_single() || is_feed() ) {
		$site_url = home_url( '/' );
		$content .= '<p class="commentsTit p5 mb10">© 版权声明</p>';
		$content .= '<p>1、本站软件内容来源于互联网络，仅供大家学习与研究之用，请勿用于商业用途，谢谢合作。</p>';
		$content .= '<p>2、本站软件完美兼容 PortableApps 软件平台，可自动识别软件目录，安装、卸载、升级方便。</p>';
		$content .= '<p>3、除非注明，本站文章均为 <a title="' . esc_attr( $site_url ) . '" href="' . esc_url( $site_url ) . '" target="_blank" rel="noopener">' . esc_html( get_bloginfo( 'name' ) ) . '</a> 原创文章，转载请以链接的形式标明本文链接。</p>';
		$content .= '<p>4、本文链接：<a title="' . esc_attr( get_the_title() ) . '" href="' . esc_url( get_permalink() ) . '" target="_blank" rel="noopener">' . esc_html( get_permalink() ) . '</a></p>';
	}

	return $content;
}
add_filter( 'the_content', 'qiumin_append_copyright' );
