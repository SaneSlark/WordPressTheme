<?php
// 归档模板：按日期、标签等归档维度展示文章列表。
get_header();
?>

<div class="mainBox">
	<div class="wrapOut cf">
		<div class="boxLeft">
			<div class="boxLeftIn ovh">
				<div class="archiveList">
					<?php // 根据当前归档类型输出对应标题，保持页面语义清晰。 ?>
					<?php if ( is_day() ) : ?>
						<h2 class="pt5 pb5 mb10 archiveTit archiveTitDaily"><?php printf( esc_html__( '日度 文档归类: %s', 'qiumin' ), '<span>' . esc_html( get_the_date() ) . '</span>' ); ?></h2>
					<?php elseif ( is_month() ) : ?>
						<h2 class="pt5 pb5 mb10 archiveTit archiveTitMonthly"><?php printf( esc_html__( '月度 文档归类: %s', 'qiumin' ), '<span>' . esc_html( get_the_date( 'F Y' ) ) . '</span>' ); ?></h2>
					<?php elseif ( is_year() ) : ?>
						<h2 class="pt5 pb5 mb10 archiveTit archiveTitYearly"><?php printf( esc_html__( '年度 文档归类: %s', 'qiumin' ), '<span>' . esc_html( get_the_date( 'Y' ) ) . '</span>' ); ?></h2>
					<?php elseif ( is_tag() ) : ?>
						<h2 class="pt5 pb5 mb10 archiveTit archiveTitTag"><?php printf( esc_html__( 'Tag 文档归类: %s', 'qiumin' ), '<span>' . esc_html( single_tag_title( '', false ) ) . '</span>' ); ?></h2>
					<?php else : ?>
						<h2 class="pt5 pb5 mb10 archiveTit"><?php esc_html_e( '文档归类', 'qiumin' ); ?></h2>
					<?php endif; ?>
					<div class="archive_list pt10 mr10 ml10 ovh">
						<ul id="post_list" class="cf">
							<?php // 主循环：输出归档页中的每一篇文章。 ?>
							<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
							<li class="mb20">
								<h3 class="title pt10 pb10 pl5"><a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>"><?php the_title(); ?></a></h3>
								<p class="lh20 pt5 pb5 g9 articleInfo"><?php the_time( 'Y-m-d' ); ?> | <?php esc_html_e( 'Author:' ); ?><?php the_author(); ?> | <?php esc_html_e( '分类目录：', 'qiumin' ); ?><?php the_category( '、' ); ?> | <?php esc_html_e( '阅读次数：', 'qiumin' ); ?><?php echo esc_html( qiumin_get_post_views( get_the_ID() ) ); ?> | <?php comments_popup_link( '0 条评论', '1 条评论', '% 条评论', '', '评论已关闭' ); ?></p>
 							<div class="thumbnail_box"> 
							<img class="alignnone" src="<?php echo esc_url( qiumin_thumb_image() ); ?>" height="150" width="150" alt="<?php the_title_attribute(); ?>"/>
                            </div>
								
                             <?php the_content(); ?>......

							<p class="lh24 lh24 pt5 pb5 g9 ml10 articleInfo meta"><?php _e("Tags:"); ?><?php the_tags(__(' '), '、'); ?></p>
							</li>
							<?php endwhile; ?>
							<?php else : ?>
							<h3 class="pt10 pb10 g3">非常抱歉，该分类下没有文章！</h3>
							<p class="g6">非常抱歉，该分类下没有文章！请移步其他分类下进行查看，谢谢！</p>
							<?php endif; ?>
						</ul>
					</div>
					<div class="countPage">
						<?php // 列表分页：复用 functions.php 中的分页函数。 ?>
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
