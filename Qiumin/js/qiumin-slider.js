(function($) {
	'use strict';

	// 首页轮播脚本：支持左右箭头、圆点切换、悬停暂停和自动播放。
	$(function() {
		$('.qiumin-slider').each(function() {
			var $slider = $(this);
			var $track = $slider.find('.qiumin-slider__track');
			var $slides = $slider.find('.qiumin-slider__slide');
			var $dots = $slider.find('.qiumin-slider__dot');
			var total = $slides.length;
			var current = 0;
			var timer = null;
			var interval = parseInt($slider.data('interval'), 10) || 5000;

			if (total <= 1) {
				$slider.find('.qiumin-slider__arrow, .qiumin-slider__dots').hide();
				return;
			}

			function goTo(index) {
				// 索引越界时首尾相接，保证轮播可以循环。
				if (index < 0) {
					index = total - 1;
				}
				if (index >= total) {
					index = 0;
				}

				current = index;
				$track.css('transform', 'translateX(' + (-current * 100) + '%)');
				$slides.removeClass('is-active').eq(current).addClass('is-active');
				$dots.removeClass('is-active').eq(current).addClass('is-active');
			}

			function start() {
				// 每次重新启动前先清理旧定时器，避免重复播放。
				stop();
				timer = window.setInterval(function() {
					goTo(current + 1);
				}, interval);
			}

			function stop() {
				if (timer) {
					window.clearInterval(timer);
					timer = null;
				}
			}

			$slider.find('.qiumin-slider__arrow--prev').on('click', function(e) {
				e.preventDefault();
				goTo(current - 1);
				start();
			});

			$slider.find('.qiumin-slider__arrow--next').on('click', function(e) {
				e.preventDefault();
				goTo(current + 1);
				start();
			});

			$dots.on('click', function(e) {
				e.preventDefault();
				goTo(parseInt($(this).data('index'), 10) || 0);
				start();
			});

			$slider.on('mouseenter focusin', stop);
			// 鼠标移出或焦点离开后恢复自动轮播。
			$slider.on('mouseleave focusout', start);

			start();
		});
	});
})(jQuery);
