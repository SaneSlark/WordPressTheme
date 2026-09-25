(function($) {
	'use strict';

	$(function() {
		$('a').aqy106Preview();
		aqy106AutoScroll('#notices', 300, 4000);
		aqy106AutoScroll('#slideReplayList', 1000, 5000);
		initLoadingBar();
		initSearchGuard();
		initResponsiveNav();
		initCommentGuard();
		initBackToTop();
		initTagCloudSphere();
	});

	function initLoadingBar() {
		var $loading = $('#loading');

		if (!$loading.length) {
			return;
		}

		$loading.stop(true).animate({ width: '100%' }, function() {
			window.setTimeout(function() {
				$loading.hide();
			}, 1000);
		});
	}

	function initSearchGuard() {
		var $searchButton = $('#sb');
		var $searchInput = $('#s');

		if (!$searchButton.length || !$searchInput.length) {
			return;
		}

		$searchButton.on('click', function() {
			if ($.trim($searchInput.val()) === '') {
				alert('你是不是忘了输入要搜索的内容呢？');
				$searchInput.focus();
				return false;
			}
		});
	}

	function initResponsiveNav() {
		var $siteNav = $('.siteNav');
		var $menu = $('#primary-menu');
		var $more = $siteNav.find('.navMore');
		var $toggle = $siteNav.find('.navMoreToggle');
		var $moreMenu = $('#nav-more-menu');
		var $rss = $siteNav.find('.rssIcon');
		var $navBox = $siteNav.find('.mainBox').first();
		var resizeTimer = null;
		var $items = $menu.children('li');

		if (!$siteNav.length || !$toggle.length || !$menu.length || !$moreMenu.length) {
			return;
		}

		function setOpen(isOpen) {
			$siteNav.toggleClass('is-more-open', isOpen);
			$toggle.attr('aria-expanded', isOpen ? 'true' : 'false');
			$moreMenu.attr('aria-hidden', isOpen ? 'false' : 'true');
		}

		function closeMore() {
			setOpen(false);
		}

		function getVisibleWidth() {
			var width = 0;

			$menu.children('li').each(function() {
				width += $(this).outerWidth(true);
			});

			if ($siteNav.hasClass('has-overflow')) {
				width += $more.outerWidth(true);
			}

			if ($rss.length) {
				width += $rss.outerWidth(true);
			}

			return Math.ceil(width);
		}

		function resetMenu() {
			$siteNav.removeClass('has-overflow');
			closeMore();
			$moreMenu.empty();
			$menu.append($items);
		}

		function moveLastItemToMore() {
			var $item = $menu.children('li:last');

			if (!$item.length || $menu.children('li').length <= 1) {
				return false;
			}

			$moreMenu.prepend($item);
			return true;
		}

		function updateMode() {
			var availableWidth = $navBox.width();
			var safetyGap = 2;

			resetMenu();

			if (getVisibleWidth() <= availableWidth) {
				return;
			}

			$siteNav.addClass('has-overflow');

			while (getVisibleWidth() > availableWidth - safetyGap) {
				if (!moveLastItemToMore()) {
					break;
				}
			}
		}

		$toggle.on('click', function() {
			setOpen($toggle.attr('aria-expanded') !== 'true');
		});

		$menu.on('click', 'a', function() {
			closeMore();
		});

		$moreMenu.on('click', 'a', function() {
			closeMore();
		});

		$(document).on('click', function(event) {
			if (!$(event.target).closest('.navMore').length) {
				closeMore();
			}
		});

		$(document).on('keydown', function(event) {
			if (event.key === 'Escape') {
				closeMore();
			}
		});

		$(window).on('resize orientationchange load', function() {
			window.clearTimeout(resizeTimer);
			resizeTimer = window.setTimeout(updateMode, 120);
		});

		updateMode();
	}

	function initCommentGuard() {
		var $form = $('#commentform');
		var $comment = $('#comment');

		if (!$form.length || !$comment.length) {
			return;
		}

		$form.on('submit', function() {
			if ($.trim($comment.val()) === '') {
				alert('请输入您的评论文本。');
				$comment.focus();
				return false;
			}
		});
	}

	function initBackToTop() {
		var $fixed = $('.bottomFiexed');
		var $scrollBody = $('html,body');
		var hoverTimer = null;
		var hideTimer = null;

		if (!$fixed.length) {
			$fixed = $('<div class="bottomFiexed"><a href="#" class="backToTop" title="点击返回顶部">点击返回顶部</a><a href="#" class="toBottom" title="点击到达底部">点击到达底部</a></div>').appendTo($('body'));
		}

		function showFixed() {
			$fixed.addClass('is-visible');
			if (hideTimer) {
				window.clearTimeout(hideTimer);
				hideTimer = null;
			}
		}

		function hideFixed(delay) {
			if (hideTimer) {
				window.clearTimeout(hideTimer);
			}
			hideTimer = window.setTimeout(function() {
				$fixed.removeClass('is-visible');
			}, delay || 900);
		}

		function stopHoverScroll() {
			if (hoverTimer) {
				window.clearTimeout(hoverTimer);
				hoverTimer = null;
			}
			hideFixed(900);
		}

		function hoverScroll(step) {
			var $window = $(window);
			showFixed();
			$window.scrollTop($window.scrollTop() + step);
			hoverTimer = window.setTimeout(function() {
				hoverScroll(step);
			}, 50);
		}

		$fixed.find('.backToTop')
			.on('mouseenter', function() {
				showFixed();
				hoverScroll(-1);
			})
			.on('mouseleave', stopHoverScroll)
			.on('click', function(event) {
				event.preventDefault();
				stopHoverScroll();
				showFixed();
				$scrollBody.stop(true).animate({ scrollTop: 0 }, 800, function() {
					hideFixed(500);
				});
			});

		$fixed.find('.toBottom')
			.on('mouseenter', function() {
				showFixed();
				hoverScroll(1);
			})
			.on('mouseleave', stopHoverScroll)
			.on('click', function(event) {
				event.preventDefault();
				stopHoverScroll();
				showFixed();
				$scrollBody.stop(true).animate({ scrollTop: $(document).height() }, 800, function() {
					hideFixed(500);
				});
			});

		$(window).on('scroll', function() {
			showFixed();
			hideFixed(900);
		});
	}

	function initTagCloudSphere() {
		var $sphere = $('#tagcloudSphere');

		if (!$sphere.length) {
			return;
		}

		var $links = $sphere.find('a');

		if ($links.length < 2) {
			return;
		}

		var $surface = $('<div class="tagcloud-surface"></div>');
		$surface.append($links);
		$sphere.append($surface).addClass('is-sphere');

		var count = $links.length;
		var radius = 90;
		var goldenAngle = Math.PI * (3 - Math.sqrt(5));
		var positions = [];

		$links.each(function(index) {
			var y = 1 - (index / (count - 1)) * 2;
			var radiusAtY = Math.sqrt(1 - y * y);
			var theta = goldenAngle * index;

			var x = Math.cos(theta) * radiusAtY * radius;
			var z = Math.sin(theta) * radiusAtY * radius;
			y = y * radius;

			positions.push({ x: x, y: y, z: z });

			$(this).css({
				'--tx': x.toFixed(2) + 'px',
				'--ty': y.toFixed(2) + 'px',
				'--tz': z.toFixed(2) + 'px',
				'opacity': (0.45 + 0.55 * ((z + radius) / (2 * radius))).toFixed(2)
			});
		});

		var rotation = Math.random() * 360;
		var speed = (Math.random() * 0.3 + 0.2) * (Math.random() > 0.5 ? 1 : -1);
		var isPaused = false;

		$sphere.on('mouseenter', function() {
			isPaused = true;
		});

		$sphere.on('mouseleave', function() {
			isPaused = false;
		});

		function animate() {
			if (!isPaused) {
				rotation += speed;
			}

			var rad = rotation * Math.PI / 180;
			var cosR = Math.cos(rad);
			var sinR = Math.sin(rad);

			$links.each(function(index) {
				var pos = positions[index];
				var newY = pos.y * cosR - pos.z * sinR;
				var newZ = pos.y * sinR + pos.z * cosR;
				var opacity = 0.45 + 0.55 * ((newZ + radius) / (2 * radius));

				$(this).css({
					'transform': 'translate3d(' + pos.x.toFixed(2) + 'px, ' + newY.toFixed(2) + 'px, ' + newZ.toFixed(2) + 'px) translate(-50%, -50%)',
					'opacity': opacity.toFixed(2)
				});
			});

			window.requestAnimationFrame(animate);
		}

		animate();
	}

	function aqy106AutoScroll(selector, speed, interval) {
		var $container = $(selector);
		var $ul = $container.children('ul');
		var timer = null;

		if (!$container.length || !$ul.length || $ul.children('li').length < 2) {
			return;
		}

		function stop() {
			if (timer) {
				window.clearInterval(timer);
				timer = null;
			}
		}

		function move() {
			var $last = $ul.children('li:last');
			var itemHeight = $last.outerHeight(true);

			if (!itemHeight) {
				return;
			}

			$ul.stop(true, true).animate({ marginTop: itemHeight }, speed, function() {
				$last.prependTo($ul).hide().fadeIn(speed);
				$ul.css({ marginTop: 0 });
			});
		}

		function start() {
			stop();
			timer = window.setInterval(move, interval);
		}

		start();
		$container.on('mouseenter', stop).on('mouseleave', start);
	}

	function positionPreview($preview, event, xOffset, yOffset) {
		var windowWidth = $(window).width();

		$preview.css('top', (event.pageY - xOffset) + 'px');

		if (event.pageX < windowWidth / 2) {
			$preview.css({
				left: event.pageX + yOffset + 'px',
				right: 'auto',
				boxShadow: '4px 4px 6px rgba(33,33,33,.7)'
			});
		} else {
			$preview.css({
				right: windowWidth - event.pageX + yOffset + 'px',
				left: 'auto',
				boxShadow: '-4px 4px 6px rgba(33,33,33,.7)'
			});
		}
	}

	$.fn.aqy106Preview = function() {
		var xOffset = 10;
		var yOffset = 20;
		var imageHrefPattern = /\.(png|gif|jpe?g|bmp|webp)(\?.*)?$/i;

		return this.each(function() {
			var $link = $(this);
			var $image = $link.children('img').first();
			var originalTitle = $link.attr('title') || '';
			var originalImageTitle = $image.attr('title') || '';

			$link.on('mouseenter', function(event) {
				var href = $link.attr('href') || '';
				var imageAlt = $image.attr('alt') || '';
				var linkText = $link.text().replace(/\s+/g, ' ').trim();
				var previewText = originalTitle || linkText || imageAlt;
				var isImageLink = imageHrefPattern.test(href);
				var $preview = $('<div id="preview"><div></div></div>');
				var $inner = $preview.children('div');

				if (!isImageLink && !previewText) {
					return;
				}

				if (isImageLink) {
					$('<img>', { src: href, alt: imageAlt || previewText }).appendTo($inner);
				}
				$('<p>').text(isImageLink ? imageAlt || previewText : previewText).appendTo($inner);

				$link.attr('title', '');
				$image.attr('title', '');

				$preview.css({
					position: 'absolute',
					padding: '4px',
					border: '1px solid #f3f3f3',
					backgroundColor: '#eee',
					zIndex: 100000
				});
				$inner.css({
					padding: '5px',
					backgroundColor: 'white',
					border: '1px solid #ccc'
				});
				$inner.children('p').css({
					textAlign: 'center',
					fontSize: '12px',
					padding: '4px 0 4px'
				});

				$preview.appendTo($('body'));
				positionPreview($preview, event, xOffset, yOffset);
				$preview.fadeIn('fast');
			}).on('mouseleave', function() {
				$('#preview').remove();

				if (originalTitle) {
					$link.attr('title', originalTitle);
				} else {
					$link.removeAttr('title');
				}

				if ($image.length) {
					if (originalImageTitle) {
						$image.attr('title', originalImageTitle);
					} else {
						$image.removeAttr('title');
					}
				}
			}).on('mousemove', function(event) {
				var $preview = $('#preview');

				if ($preview.length) {
					positionPreview($preview, event, xOffset, yOffset);
				}
			});
		});
	};
})(jQuery);
