"use strict"

// 메인 및 서브 롤링
$(function() {
	//console.log($(".mainpage").find(".rollingwrap").attr("class"));
	var $event = $("#"+$(".rollingwrap").attr('id'));
	var $arrow = $event.find("> span > a.arrow");
	var $menu = $event.find("> nav li");
	var $container = $event.find(".containerB");
	var $list = $container.find("> ul");
	var $content = $list.find("> li");
	var contentTotal = $content.length;
	var pageNum = 0;
	var viewNum = 1;
	var scrollTop;
	var timer;
	var isScroll = false;
	var isChange = false;

	$(window).on("load scroll resize orientationchange", content_resize);
	$content.on("load", content_resize);
	
	function content_resize() {
		
		content_sort();

	}
	content_resize();

	// 화살표
	$arrow.on("click", function(_e) {
		
		_e.preventDefault();
		if (!isChange) {
			var oldNum = contentNum();
			if ($(this).hasClass("next")) pageNum = (pageNum == contentTotal - 1) ? 0 : pageNum + 1;
			else pageNum = (pageNum == 0) ? contentTotal - 1 : pageNum - 1;
			var direction = ($(this).hasClass("next")) ? "next" : "prev";
			content_change({ oldNum:oldNum, direction:direction });
		}

	});
	
	// 메뉴
	$menu.on("click", function(_e) {
		
		_e.preventDefault();
		if (!isChange && contentNum() != $menu.index(this)) {
			var oldNum = contentNum();
			pageNum = $menu.index(this);
			var direction = (pageNum > oldNum) ? "next" : "prev";
			content_change({ oldNum:oldNum, direction:direction });
		}

	});

	function menu_change() {

		$menu.removeClass("on").removeAttr("title")
			.eq(contentNum()).addClass("on").attr("title", "선택됨");

	}

	// 드래그
	$container.on("touchstart touchend touchmove", function(_e) {
		
		switch(_e.type) {
			case "touchstart":
				scrollTop = $(window).scrollTop();
			break;
			case "touchend":
				setTimeout(function() {

					hammer.get("pan").set({ enable:true });

				}, 1);
			break;
			case "touchmove":
				if ($(window).scrollTop() != scrollTop) hammer.get("pan").set({ enable:false });
			break;
		}

	});
	
	var hammer = new Hammer($container[0], { preventDefault:true, cssProps:{ tapHighlightColor:"rgba(51, 181, 229, 0.4)" } });
	hammer.get("pan").set({ threshold:30, direction:Hammer.DIRECTION_HORIZONTAL });
	hammer.on("panend panmove", function(_e) {

		switch(_e.type) {
			case "panend":
				if (Math.abs(_e.deltaX) > $content.width() * 0.2) {
					pageNum = (_e.deltaX < 0) ? pageNum + 1 : pageNum - 1;
					content_drag({ ease:Cubic.easeOut });
				} else {
					content_drag();
				}
			break;
			case "panmove":
				var $temp;
				if (_e.deltaX < 0) {
					$temp = $content.eq(contentNum(pageNum + viewNum));
					TweenMax.to($temp, 0, { x:$content.width() * pageNum + $content.width() * viewNum });
				} else {
					$temp = $content.eq(contentNum(pageNum - 1));
					TweenMax.to($temp, 0, { x:$content.width() * (pageNum - 1) });
				}
				TweenMax.to($list, 0, { x:$list.data("x") + _e.deltaX });
			break;
		}

	});

	// 포커스
	$content.on("focusin", function() {
		
		pageNum = $content.index(this);
		content_sort();

	});

	// 드래그 내용 변경 { time:이동 시간, ease:이동 효과 }
	function content_drag(_obj) {
		
		isChange = true;
		var option = $.extend({ time:0.3, ease:Cubic.easeInOut }, _obj);

		$list.data("x", -$content.width() * pageNum);
		TweenMax.to($list, option.time, { x:$list.data("x"), ease:option.ease, onStart:menu_change, onComplete:content_tween_complete });

	}

	// 내용 변경 { oldNum:이전 번호, direction:이동 방향, time:이동 시간, ease:이동 효과 }
	function content_change(_obj) {
		
		isChange = true;
		var option = $.extend({ oldNum:null, direction:"next", time:0.4, ease:Cubic.easeInOut }, _obj);
		var target = (option.direction == "next") ? -$content.width() : $content.width();
		
		TweenMax.to($list, 0, { x:0 });
		if (option.oldNum != null) {
			var $old = $content.eq(option.oldNum);
			TweenMax.to($old, 0, { x:0 });
			TweenMax.to($old, option.time, { x:target, ease:option.ease });
		}
		var $current = $content.eq(contentNum());
		TweenMax.to($current, 0, { x:-target });
		TweenMax.to($current, option.time, { x:0, ease:option.ease, onStart:menu_change, onComplete:content_tween_complete });

	}

	function content_tween_complete() {
		
		content_sort();
		isChange = false;

	}
	
	// 내용 재정렬
	function content_sort() {
		
		pageNum = contentNum();
		$content.each(function(i) {
			
			TweenMax.set($(this), { x:$(this).width() * i, force3D:true });

		});
		$list.data("x", -$content.width() * pageNum);
		TweenMax.set($list, { x:$list.data("x"), force3D:true });

		$container.height($content.eq(contentNum()).outerHeight());

	}
	
	// 현재 번호
	function contentNum(_num) {
		
		var tenuNum = (_num == undefined) ? pageNum : _num;
		var returnNum = (tenuNum < 0) ? contentTotal + (tenuNum + 1) % contentTotal - 1 : tenuNum % contentTotal;
		return returnNum;

	}
	
	// 자동롤링
	$event.on("focusin focusout touchstart touchend", function(_e) {
		
		timer_stop();
		switch(_e.type) {
			case "focusout":
			case "touchend":
				timer_start();
			break;
		}

	});
	timer_start();

	function timer_start() {

		timer = setInterval(function() {
			
			var oldNum = contentNum();
			pageNum = (pageNum == contentTotal - 1) ? 0 : pageNum + 1;
			content_change({ oldNum:oldNum, direction:"next" });

		}, 4000);

	}

	function timer_stop() {

		clearInterval(timer);

	}

});