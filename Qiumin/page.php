<?php
/*
Template Name: page
*/
?>
<?php
// 页面模板：浏览页面时记录一次阅读量，同一浏览器通过 Cookie 防止重复计数。
$post_id = get_queried_object_id();
if ( $post_id ) {
	qiumin_record_post_view( $post_id );
}

get_header();
?>

<div class="mainBox">
	<div class="wrapOut cf">
		<div class="boxLeft">
			<div class="boxLeftIn">
				<div class="pageArticle">
					<!-- 页面正文 -->
					<?php // 页面主循环：输出当前页面标题、元信息、正文和评论。 ?>
					<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
					<!-- 页面标题 -->
					<h3 class="title pt10 pb10 pl5 pr10 "><?php the_title(); ?></h3>		
					<!-- 页面元信息 -->
					<p class="h24 lh24 pt5 pb5 g9 ml10 articleInfo">
						<span class="mr20">发布时间：<?php the_time('Y-m-d') ?></span>|
						<span class="ml10 mr10">阅读次数：<?php echo esc_html( qiumin_get_post_views( get_the_ID() ) ); ?></span>|
						<span class="mr20"><?php comments_popup_link('0 条评论', '1 条评论', '% 条评论', '', '评论已关闭'); ?></span>
						<?php edit_post_link('编辑', ' &nbsp;', ''); ?>
					</p>
					<div class="p10 pr20 pageContent">
						<!-- 页面内容 -->
						<?php the_content(); ?>						
						<?php endwhile; ?>
						<?php else : ?>
						<h3 class="pt10 pb10 g3">非常抱歉，该分类下没有文章！</h3>
						<p class="g6">非常抱歉，该分类下没有文章！请移步其他分类下进行查看，谢谢！</p>
						<?php endif; ?>
					</div>
					<div class="p10"><?php comments_template(); ?></div>
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
