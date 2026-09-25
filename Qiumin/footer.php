<!-- 页脚模板：展示版权、站点链接、地图入口，并在结尾调用 wp_footer。 -->
<footer>
	<div class="footer pt10 pb10">
		<div class="mainBox footercenter">
			<br>
			<img src="<?php echo esc_url( get_template_directory_uri() . '/pic/slogo.png' ); ?>" alt="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>" width="30" height="30">

			<a href="<?php echo esc_url( home_url( '/' ) ); ?>" title="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>" class="mr20"><?php echo esc_html( get_bloginfo( 'name' ) ); ?></a>

			Copyright © <?php echo esc_html( date_i18n( 'Y' ) ); ?> HehuaSoft.com All Rights Reserved<a href="<?php echo esc_url( wp_login_url() ); ?>" title="login" class="mr20">.</a>
			<br><br>
			Powered by <a href="https://wordpress.org" target="_blank" rel="noopener" title="WordPress" class="mr20">WordPress</a>

			引用本站文章 · <a href="https://creativecommons.org/licenses/by/4.0/deed.zh" target="_blank" rel="noopener" title="本站文章和资源来自互联网或者站长的原创，按照 署名 4.0 国际 (CC BY 4.0) 协议发布和共享，转载或引用本站文章应遵循相同协议。" class="mr20">遵循相关协议</a>

			<a href="<?php echo esc_url( home_url( '/sitemap.xml' ) ); ?>" target="_blank" rel="noopener" title="谷歌引擎" class="mr20">网站索引</a>

			<a href="<?php echo esc_url( home_url( '/sitemap.html' ) ); ?>" target="_blank" rel="noopener" title="百度引擎" class="mr20">网站地图</a>
			<br><br>
			<img src="<?php echo esc_url( get_template_directory_uri() . '/pic/windows.png' ); ?>" alt="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>" class="alignright">
		</div>
	</div>
</footer>
</div><!-- .site-wrapper -->

<?php // WordPress 插件和主题脚本通常会通过 wp_footer 输出到这里。 ?>
<?php wp_footer(); ?>

</body>
</html>
