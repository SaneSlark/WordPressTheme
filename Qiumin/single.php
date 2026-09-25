<?php
// 文章详情模板：进入单篇文章时记录阅读量，并展示正文、相关推荐和评论。
$post_id = get_queried_object_id();
if ( $post_id ) {
	qiumin_record_post_view( $post_id );
}

get_header();
?>

<div class="mainBox">
	<div class="wrapOut cf">
		<div class="boxLeft">
			<div class="boxLeftIn ovh">
				<div class="singleArticle">
					<?php // 单篇文章主循环：通常只会输出当前一篇文章。 ?>
					<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
						<h3 class="title pt10 pb10 pl5 pr20"><?php the_title(); ?></h3>
						<p class="h24 lh24 pt5 pb5 g9 ml10 articleInfo">
							<span class="mr10"><?php esc_html_e( 'Author:' ); ?><?php the_author(); ?></span>|
							<span class="ml10 mr10"><?php esc_html_e( '分类目录：', 'qiumin' ); ?><?php the_category( '、' ); ?></span>|
							<span class="ml10 mr10">发布时间：<?php the_time( 'Y-m-d' ); ?></span>|
							<span class="ml10 mr10">阅读次数：<?php echo esc_html( qiumin_get_post_views( get_the_ID() ) ); ?></span>|
							<span class="ml10 mr10"><?php comments_popup_link( '0 条评论', '1 条评论', '% 条评论', '', '评论已关闭' ); ?></span>|
							<?php edit_post_link( '编辑', ' &nbsp;', '' ); ?>
						</p>
						<p class="h24 lh24 pt5 pb5 g9 ml10 articleInfo mb10">文档标签：<?php the_tags( '', ', ', '' ); ?></p>
						<div class="content pt10 pr20 pl10 ovh">
							<?php the_content(); ?>
						</div>

						<div class="p10">
							<h4 class="commentsTit p5 mb10">
								<?php
								if ( get_next_post( true ) ) {
									next_post_link( '上一篇文章: %link', '%title', true );
								} else {
									echo esc_html__( '上一篇文章: 这已是最新的文章', 'qiumin' );
								}
								?>
								<br>
								<?php
								if ( get_previous_post( true ) ) {
									previous_post_link( '下一篇文章: %link', '%title', true );
								} else {
									echo esc_html__( '下一篇文章: 这已是最后的文章', 'qiumin' );
								}
								?>
							</h4>
						</div>

						<div class="singleArticle pt10 pb10 pl5 pr10">
							<h3 class="title pt10 pb10 pl5 pr10">相关推荐</h3>
							<ul class="related_img">
								<?php
								// 相关推荐优先按标签匹配，不足时再用同分类文章补齐。
								$post_num      = 5;
								$related_count = 0;
								$exclude_ids   = array( get_the_ID() );
								$posttags      = get_the_tags();

								if ( $posttags ) {
									$tag_ids       = wp_list_pluck( $posttags, 'term_id' );
									$related_posts = new WP_Query(
										array(
											'post_status'         => 'publish',
											'tag__in'             => $tag_ids,
											'post__not_in'        => $exclude_ids,
											'ignore_sticky_posts' => true,
											'orderby'             => 'comment_count',
											'posts_per_page'      => $post_num,
										)
									);

									while ( $related_posts->have_posts() ) {
										$related_posts->the_post();
										$exclude_ids[] = get_the_ID();
										$related_count++;
										?>
										<li class="related_box">
											<div class="r_pic">
												<a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>" target="_blank" rel="noopener">
													<img src="<?php echo esc_url( qiumin_post_thumbnail_src() ); ?>" alt="<?php the_title_attribute(); ?>" class="thumbnail" />
												</a>
											</div>
											<div class="r_title"><a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>" target="_blank" rel="bookmark noopener"><?php the_title(); ?></a></div>
										</li>
										<?php
									}
									wp_reset_postdata();
								}

								if ( $related_count < $post_num ) {
									$cat_ids = wp_list_pluck( get_the_category(), 'term_id' );
									if ( $cat_ids ) {
										$related_posts = new WP_Query(
											array(
												'category__in'        => $cat_ids,
												'post__not_in'        => $exclude_ids,
												'ignore_sticky_posts' => true,
												'orderby'             => 'comment_count',
												'posts_per_page'      => $post_num - $related_count,
											)
										);

										while ( $related_posts->have_posts() ) {
											$related_posts->the_post();
											$related_count++;
											?>
											<li class="related_box">
												<div class="r_pic">
													<a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>" target="_blank" rel="noopener">
														<img src="<?php echo esc_url( qiumin_post_thumbnail_src() ); ?>" alt="<?php the_title_attribute(); ?>" class="thumbnail" />
													</a>
												</div>
												<div class="r_title"><a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>" target="_blank" rel="bookmark noopener"><?php the_title(); ?></a></div>
											</li>
											<?php
										}
										wp_reset_postdata();
									}
								}

								if ( 0 === $related_count ) {
									echo '<div class="r_title">没有相关文章!</div>';
								}
								?>
							</ul>
						</div>

						<div class="p10"><?php comments_template(); ?></div>
					<?php endwhile; else : ?>
						<h3 class="title pt10 pb10 g3">非常抱歉，该文章没有找到！</h3>
						<p class="g6">非常抱歉，该文章可能已经被博主删除或移到别的地方去了，你或者可以继续查看博主的其他文章！</p>
					<?php endif; ?>
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
