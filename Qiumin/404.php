<?php
// 404 页面模板：当访问的内容不存在时，显示返回首页入口和侧边栏。
get_header();
?>

<div class="mainBox">
	<div class="wrapOut cf">
		<div class="boxLeft">
			<div class="boxLeftIn">
				<div class="rel errPage">
					<img class="errImg" src="<?php echo esc_url( get_template_directory_uri() . '/pic/404.jpg' ); ?>" alt="页面没有找到" />
					<a href="<?php echo esc_url( home_url( '/' ) ); ?>" title="点击返回首页" class="abs errTo">返回首页</a>
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
