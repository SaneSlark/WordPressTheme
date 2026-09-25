<?php
// 搜索结果模板：展示当前关键词命中的文章列表。
get_header();
?>
<div class="mainBox">
	<div class="wrapOut cf">
		<div class="boxLeft">
			<div class="boxLeftIn ovh">
				<div class="searchPage">
					<h2 class="searchPageTit">搜索结果</h2>
					<div class="searchList pt10 pr10 pl10">
						<ul id="post_list" class="">
							<?php if (have_posts()) : while ( have_posts() ) : the_post(); ?>
							<li <?php post_class(); ?>>
								<h3 class="f16 searchListTit"><a class="tdn" href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
								<p class="g9 f12"><?php the_time( 'Y-m-d' ); ?> | <?php esc_html_e( 'Author:' ); ?><?php the_author(); ?> | <?php esc_html_e( '分类目录：', 'qiumin' ); ?><?php the_category( '、' ); ?> | <?php esc_html_e( '阅读次数：', 'qiumin' ); ?><?php echo esc_html( qiumin_get_post_views( get_the_ID() ) ); ?> | <?php comments_popup_link( '0 条评论', '1 条评论', '% 条评论', '', '评论已关闭' ); ?></p>
								<p class="g9 f12 meta"><?php esc_html_e( 'Tags:' ); ?><?php the_tags( ' ', '、' ); ?></p>
							</li>
							<?php endwhile; ?>
							<?php else : ?>
							<li class="tc">
								<h3 class="f16 searchListTit">非常抱歉，没有搜索到关键字为 <span class="hightline"><?php echo esc_html( get_search_query() ); ?></span> 的文章！</h3>
								<p class="g6 mt20"><strong>请尝试重新填入搜索关键字进行搜索或移步其他分类下进行查看！</strong></p>
								<p class="mt20"><img class="searchNoneImg" src="<?php echo esc_url( get_template_directory_uri() . '/pic/404Search.jpg' ); ?>" alt="没有搜索到结果" /></p>
							</li>
							<?php endif; ?>
						</ul> 
					</div>
					<div class="countPage">
						<?php qiumin_pages(); ?>
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
