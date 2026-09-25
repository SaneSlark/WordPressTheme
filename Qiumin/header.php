<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<?php if ( ! has_site_icon() ) : ?>
<link rel="shortcut icon" type="image/x-icon" href="<?php echo esc_url( get_template_directory_uri() . '/pic/favicon.ico' ); ?>">
<?php endif; ?>
<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>">
<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<div class="site-wrapper">
<header>
	<div class="head rel">
		<div class="loading abs" id="loading"></div>
		<div class="mainBox cf">
			<?php
			// 组合站点名称和副标题，用作 Logo 链接的可访问标题。
			$site_name        = get_bloginfo( 'name' );
			$site_description = get_bloginfo( 'description' );
			$logo_label       = trim( $site_name . '--' . $site_description, '-' );
			?>
			<h1 class="l logo">
				<a class="db" title="<?php echo esc_attr( $logo_label ); ?>" href="<?php echo esc_url( home_url( '/' ) ); ?>">
					<img src="<?php echo esc_url( get_template_directory_uri() . '/pic/logo.png' ); ?>" alt="<?php echo esc_attr( $logo_label ); ?>">
				</a>
			</h1>
			<!-- 顶部搜索框：提交到 WordPress 默认搜索入口。 -->
			<form id="searchform" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>" class="cf" role="search">
				<div class="r topSearch p1">
					<input type="search" value="<?php echo esc_attr( get_search_query() ); ?>" name="s" id="s" class="searchIpt l" size="30" placeholder="<?php esc_attr_e( '请输入搜索的关键字', 'qiumin' ); ?>">
					<input type="submit" name="search" id="sb" class="search l" value="<?php esc_attr_e( 'Search' ); ?>">
				</div>
			</form>
		</div>
	</div>
</header>
<nav class="siteNav" aria-label="<?php esc_attr_e( '主导航', 'qiumin' ); ?>">
	<div class="nav">
		<div class="nav" id="nav">
			<div class="mainBox cf">
				<?php
				// 主导航优先使用后台菜单；未设置时回退到分类和页面列表。
				wp_nav_menu(
					array(
						'theme_location' => 'primary',
						'container'      => false,
						'items_wrap'     => '<ul id="primary-menu" class="b l">%3$s</ul>',
						'fallback_cb'    => 'qiumin_nav_fallback',
						'depth'          => 1,
					)
				);
				?>
				<div class="navMore">
					<button class="navMoreToggle" type="button" aria-controls="nav-more-menu" aria-expanded="false"><?php esc_html_e( '更多', 'qiumin' ); ?></button>
					<ul id="nav-more-menu" class="navMoreMenu" aria-hidden="true"></ul>
				</div>
				<a class="rssIcon" href="<?php echo esc_url( get_bloginfo( 'rss2_url' ) ); ?>" target="_blank" rel="noopener" title="<?php echo esc_attr( get_bloginfo( 'name' ) . '的RSS订阅' ); ?>" aria-label="<?php echo esc_attr( get_bloginfo( 'name' ) . '的RSS订阅' ); ?>">RSS</a>
			</div>
		</div>
	</div>
</nav>
