<?php // 侧边栏模板：后台未配置小工具时，使用主题内置的默认模块。 ?>
<?php if ( ! dynamic_sidebar( 'First_sidebar' ) ) : ?>
	<div class="slidesBox p1 mb5 p5 slideAbout">
		<dl class="lh24 f12 cf">
			<dt class="tc l">
				<img src="<?php echo esc_url( get_template_directory_uri() . '/pic/mlogo.jpg' ); ?>" alt="荷叶荷花香" width="65" height="65">
			</dt>
			<dd> 专注网络、分享软件、记录生活、关于一切，这里会有不一样的惊喜和精彩！</dd>
		</dl>
	</div>

	<div class="mb5">
		<a class="db" title="荷花绿色便携软件" target="_blank" rel="noopener" href="https://www.hehuasoft.com"><img alt="图片" src="<?php echo esc_url( get_template_directory_uri() . '/i/portable.png' ); ?>" class="db"></a>
	</div>

	<div class="slidesBox p1 mb5 slideCat">
		<h4>文章分类</h4>
		<ul class="tc pt5 pb5 cf">
			<?php
			// 分类入口显示文章数量，排除指定分类。
			wp_list_categories(
				array(
					'sort_column'        => 'name',
					'show_count'         => 1,
					'depth'              => -1,
					'use_desc_for_title' => 0,
					'hide_empty'         => 0,
					'exclude'            => 249,
					'title_li'           => '',
				)
			);
			?>
		</ul>
		<div class="cl"></div>
	</div>

	<div class="mb5">
		<a class="db" title="荷花绿色便携软件" target="_blank" rel="noopener" href="https://www.hehuasoft.com"><img alt="绿色便携软件" src="<?php echo esc_url( get_template_directory_uri() . '/i/green.jpg' ); ?>" class="db"></a>
	</div>
<?php endif; ?>

<?php if ( ! dynamic_sidebar( 'Fourth_sidebar' ) ) : ?>
	<div class="slidesBox p1 mb5 slideArtchive">
		<h4>存档检索</h4>
		<ul class="tc pt5 pb5">
			<?php // 存档链接的月份显示由 functions.php 中的过滤器统一补零。 ?>
			<?php wp_get_archives( array( 'limit' => 10 ) ); ?>
		</ul>
		<div class="cl"></div>
	</div>
<?php endif; ?>

<div class="mb5">
	<a class="db" title="荷花绿色便携软件" target="_blank" rel="noopener" href="https://www.hehuasoft.com/crystal-diskinfo.html"><img alt="图片" src="<?php echo esc_url( get_template_directory_uri() . '/i/Shizuku.jpg' ); ?>" class="db"></a>
</div>

<?php
// 根据当前分类页或文章所属分类，决定侧边栏展示同分类最新文章。
$current_category = null;
if ( is_category() ) {
	$current_category = get_queried_object();
} elseif ( is_single() ) {
	$categories = get_the_category();
	if ( ! empty( $categories ) ) {
		$current_category = $categories[0];
	}
}
?>

<?php if ( $current_category instanceof WP_Term ) : ?>
	<div class="slidesBox p1 mb5 slideNewRadom">
		<h4><?php echo esc_html( $current_category->name ); ?>下的最新文章</h4>
		<ul>
			<?php
			// 当前分类下的最新文章，便于用户继续浏览同主题内容。
			$category_posts = new WP_Query(
				array(
					'cat'                 => $current_category->term_id,
					'posts_per_page'      => 10,
					'ignore_sticky_posts' => true,
				)
			);
			while ( $category_posts->have_posts() ) :
				$category_posts->the_post();
				?>
				<li><a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>"><?php echo esc_html( wp_html_excerpt( get_the_title(), 30, '...' ) ); ?></a></li>
			<?php endwhile; wp_reset_postdata(); ?>
		</ul>
	</div>
<?php else : ?>
	<div class="slidesBox p1 mb5 slideNewRadom">
		<h4><?php esc_html_e( '随机文章', 'qiumin' ); ?></h4>
		<ul>
			<?php
			// 没有明确分类上下文时，展示随机文章作为兜底推荐。
			$rand_posts = get_posts(
				array(
					'posts_per_page'      => 10,
					'orderby'             => 'rand',
					'ignore_sticky_posts' => true,
				)
			);
			foreach ( $rand_posts as $post ) :
				setup_postdata( $post );
				?>
				<li><a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>"><?php echo esc_html( wp_html_excerpt( get_the_title(), 30, '...' ) ); ?></a></li>
			<?php endforeach; wp_reset_postdata(); ?>
		</ul>
	</div>
<?php endif; ?>

	<div class="mb5">
		<a class="db" title="荷花飘香" target="_blank" rel="noopener" href="https://www.hehua.us"><img alt="荷花飘香" src="<?php echo esc_url( get_template_directory_uri() . '/i/hehua.jpg' ); ?>" class="db"></a>
	</div>

<div class="slidesBox p1 mb5 slideReplay slideNewRadom">
	<h4>热门文章</h4>
	<div class="slideReplayList ovh mb10" id="slideReplayList">
		<ul>
			<?php
			// 热门文章按评论数排序，排除受密码保护和未发布内容。
			$popular_posts = new WP_Query(
				array(
					'post_password'       => '',
					'post_status'         => 'publish',
					'ignore_sticky_posts' => true,
					'orderby'             => 'comment_count',
					'posts_per_page'      => 10,
				)
			);
			while ( $popular_posts->have_posts() ) :
				$popular_posts->the_post();
				?>
				<li><a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>"><?php the_title(); ?>...获得<?php comments_number( '0', '1', '%' ); ?>条评论...</a></li>
			<?php endwhile; wp_reset_postdata(); ?>
		</ul>
	</div>
</div>

<div class="mb5">
	<a class="db" title="荷花绿色便携软件" target="_blank" rel="noopener" href="https://www.hehuasoft.com"><img alt="图片" src="<?php echo esc_url( get_template_directory_uri() . '/i/mieus.jpg' ); ?>" class="db"></a>
</div>

<?php if ( ! dynamic_sidebar( 'Third_sidebar' ) ) : ?>
	<div class="slidesBox p1 slidecloud mb5">
		<h4>标签云图</h4>
		<div class="tagcloud-sphere" id="tagcloudSphere">
			<?php wp_tag_cloud( array( 'smallest' => 8, 'largest' => 22 ) ); ?>
		</div>
	</div>
<?php endif; ?>

<div class="mb5">
	<a class="db" title="荷花绿色便携软件" target="_blank" rel="noopener" href="https://www.hehuasoft.com"><img alt="图片" src="<?php echo esc_url( get_template_directory_uri() . '/i/wpidc.jpg' ); ?>" class="db"></a>
</div>

<?php
global $wpdb;
// 站点统计集中计算，模板中只负责安全输出。
$count_posts      = wp_count_posts();
$count_pages      = wp_count_posts( 'page' );
$total_comments   = get_comment_count();
$category_count   = wp_count_terms( array( 'taxonomy' => 'category' ) );
$tag_count        = wp_count_terms( array( 'taxonomy' => 'post_tag' ) );
$link_count       = $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->links} WHERE link_visible = 'Y'" );
$last_modified    = get_lastpostmodified( 'blog' );
$last_update_text = $last_modified ? date_i18n( 'Y年n月j日', strtotime( $last_modified ) ) : '';
?>
<div class="slidesBox p1 slidecloud mb5 slideNewRadom">
	<h4>站点统计</h4>
	<ul>
		<li>&nbsp;软件数量：<?php echo esc_html( $count_posts->publish ); ?></li>
		<li>&nbsp;评论数量：<?php echo esc_html( $total_comments['approved'] ); ?></li>
		<li>&nbsp;分类数量：<?php echo esc_html( is_wp_error( $category_count ) ? 0 : $category_count ); ?></li>
		<li>&nbsp;页面数量：<?php echo esc_html( $count_pages->publish ); ?></li>
		<li>&nbsp;链接数量：<?php echo esc_html( $link_count ); ?></li>
		<li>&nbsp;标签数量：<?php echo esc_html( is_wp_error( $tag_count ) ? 0 : $tag_count ); ?></li>
		<li>&nbsp;迄今运行：<?php echo esc_html( floor( ( time() - strtotime( '2012-08-08' ) ) / DAY_IN_SECONDS ) ); ?>天</li>
		<li>&nbsp;站点成立：2012年8月8日</li>
		<li>&nbsp;最后更新：<?php echo esc_html( $last_update_text ); ?></li>
	</ul>
</div>

<div class="mb5">
	<a class="db" title="荷花绿色便携软件" target="_blank" rel="noopener" href="https://www.hehuasoft.com"><img alt="图片" src="<?php echo esc_url( get_template_directory_uri() . '/i/mango.jpg' ); ?>" class="db"></a>
</div>

<?php if ( is_home() || is_front_page() ) : ?>
	<?php
	// 友情链接放在首页侧边栏最后，沿用 WordPress 链接管理器输出。
	wp_list_bookmarks(
		array(
			'categorize'      => false,
			'title_li'        => '友情链接',
			'title_before'    => '<h4>',
			'title_after'     => '</h4>',
			'category_before' => '<div id="%id" class="slidesBox p1 mb5 slideLink">',
			'category_after'  => '</div>',
		)
	);
	?>
<?php endif; ?>
