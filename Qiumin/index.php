<?php
// 首页模板：展示公告、幻灯片、文章列表和侧边栏链接。
get_header();
?>

<div class="mainBox">
	<div class="wrapOut  cf">
		<?php // 站点公告由后台主题设置控制，关闭时不输出内容。 ?>
		<?php if ( get_option( 'web_if_tip' ) == '1' ) { ?>	
			<div class="notices p2">
				<div class="noticesIn ovh" id="notices">
					<ul>
						<?php echo wp_kses_post( get_option( 'web_tip', '' ) ); ?>
					</ul>
				</div>
			</div>
		<?php } ?>
		
 <!-- 幻灯片 -->
<?php if ( function_exists( 'qiumin_render_slider' ) ) { qiumin_render_slider(); } ?>
		
		<div class="boxLeft">
			<div class="boxLeftIn">
				<div class="indexArticleList">
					<div class="archive_list pt10 mr10 ml10 ovh">
						<ul class="cf">
							<!-- 首页文章列表 -->
							<?php // 首页主循环：按 WordPress 查询结果输出文章摘要列表。 ?>
							<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
							<li class="mb20">

							<!-- 文章标题 -->
							<h3 class="title pt10 pb10 pl5 pr10 ">
							<a class="tdn" href="<?php the_permalink(); ?>" rel="bookmark" title="<?php the_title_attribute(); ?>"><?php the_title(); ?>
							<?php
							$diff = ( current_time( 'timestamp' ) - get_the_time( 'U' ) ) / HOUR_IN_SECONDS;
							if ( $diff < 24 ) {
								echo '<img src="' . esc_url( get_template_directory_uri() . '/pic/new.gif' ) . '" alt="24小时内最新">';
							}
							?></a>
							</h3>
				
							<!-- 文章元信息 -->
							<p class="h24 lh24 pt5 pb5 g9 articleInfo">
								<span class="mr10"><?php _e("Author:"); ?><?php the_author(); ?></span>|
								<span class="ml10 mr10"><?php _e("分类目录："); ?><?php the_category('、') ?></span>|
								<span class="ml10 mr10">发布时间：<?php the_time('Y-m-d') ?></span>|
								<span class="ml10 mr10">阅读次数：<?php echo esc_html( qiumin_get_post_views( get_the_ID() ) ); ?></span>|
								<span class="ml10 mr10"><?php comments_popup_link('0 条评论', '1 条评论', '% 条评论', '', '评论已关闭'); ?></span>|
								<?php edit_post_link('编辑', ' &nbsp;', ''); ?>
							</p>
								
							<!-- 文章缩略图 -->
							<div class="thumbnail_box">
								<img class="alignnone" src="<?php echo esc_url( qiumin_thumb_image() ); ?>" height="150" width="150" alt="<?php the_title_attribute(); ?>"/>
							</div>
								
							<!-- 文章摘要 -->
							<?php the_content(); ?>......
							<p class="h24 lh24 pt5 pb5 g9 ml10 articleInfo meta"><?php _e("Tags:"); ?><?php the_tags(__(' '), '、'); ?></p>
							</li>
							<?php endwhile; ?>
							<?php else : ?>
							<h3 class="pt10 pb10 g3">非常抱歉，该分类下没有文章！</h3>
							<p class="g6">非常抱歉，该分类下没有文章！请移步其他分类下进行查看，谢谢！</p>
							<?php endif; ?>
						</ul>	
					</div>
					<div class="countPage">
						<?php qiumin_pages(); //列表分页 ?>
					</div>
				</div>
			</div>		
		</div>
		<div class="Boxslide">
			<div class="slideIn">
				<?php get_sidebar(); ?>
			</div>
		</div>
	</div>
</div>

<?php get_footer(); ?>
