<?php // 评论模板：负责评论列表、评论分页、表情和评论表单。 ?>
<?php if ( post_password_required() ) : ?>
<?php esc_html_e( 'Enter your password to view comments.' ); ?>
<?php return; endif; ?>
<div id="comments" class="comments">	
	<?php if ( have_comments() ) : ?>
		<h3 class="commentsTit p5 mb10"><?php comments_number( esc_html__( 'No Comments', 'qiumin' ), esc_html__( '1 Comment', 'qiumin' ), esc_html__( '% Comments', 'qiumin' ) ); ?></h3>
		<ol class="comment_list">
			<?php // WordPress 内置评论列表输出，头像尺寸在这里统一控制。 ?>
			<?php wp_list_comments( array( 'avatar_size' => 48, 'type' => 'comment' ) ); ?>				   
		</ol>
		<?php if ( get_comment_pages_count() > 1 && get_option( 'page_comments' ) ) : ?>
			<div class="navigation">	
				<span class="alignleft"><?php previous_comments_link( esc_html__( '&laquo; Older Comments', 'qiumin' ) ); ?></span>
				<span class="alignright"><?php next_comments_link( esc_html__( 'Newer Comments &raquo;', 'qiumin' ) ); ?></span>
    		</div>
		<?php endif; ?>
	<?php elseif ( ! comments_open() && ! is_page() && post_type_supports( get_post_type(), 'comments' ) ) : ?>
		<p><?php esc_html_e( 'Comments are closed.' ); ?></p>
	<?php endif; ?>

	<!--for comments img-->
	<?php // 引入表情面板，并把表情 HTML 拼到评论框下方。 ?>
	<?php include get_template_directory() . '/smiley.php'; ?>
	<?php
	comment_form(
		array(
			'comment_field' => '<p class="comment-form-comment"><textarea aria-required="true" rows="8" cols="45" name="comment" id="comment" onkeydown="if(event.ctrlKey){if(event.keyCode==13){document.getElementById(\'submit\').click();return false}};"></textarea></p><p class="smilelink">' . $smilies . '</p>',
		)
	);
	?>
</div>
