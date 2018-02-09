"use strict"

// 메인 페이지 로드, 화면전환
$(function() {

	var $menu = $("nav.maintop > ul > li");
	//var $menu = $("#mainTop > ul > li");
	var $container = $(".container");
	var $list = $container.children("ul");
	var $content = $list.children("li");

	var loadCount = 0;
	var contentTotal = $content.length;
	var pageNum = 0;
	var viewNum = 1;
	var scrollTop;
	var isScroll = false;
	var isChange = false;

	/*$menu.each(function(){//메뉴 크기 재정렬
		$(this).width($(window).width() / $menu.length);
	});*/
	
	content_load(loadCount);

	// 페이지 로드
	function content_load(_index) {
		
		$.ajax({
			type:"get",
			url:$content.eq(_index).attr("data-url"),
			dataType:"html",
			success:function(_data) {

				var $cont = $content.eq(_index);
				var $data = $(_data).find(".loadwrap");
				var $obj = $data.find("img, iframe");

				var count = 0;
				$obj.on("load", function() {

					count++;
					if (count == $obj.length) {
						$obj.off("load");
						$content.eq(_index).removeAttr("data-url");
						content_resize();
						if (loadCount < contentTotal) content_load(loadCount);
					}

				});
				$data.appendTo($cont).removeClass("loadwrap");
				
			}
		});
		loadCount++;

	}

	$(window).on("scroll resize orientationchange", content_resize);
	
	function content_resize() {
		
		content_sort();

	}
	
	// 메뉴
	$menu.on("click", function(_e) {
		/*console.log("1");*/
		_e.preventDefault();
		if (!isChange && contentNum() != $menu.index(this)) {
			var oldNum = contentNum();
			pageNum = $menu.index(this);
			var direction = (pageNum > oldNum) ? "next" : "prev";
			content_change({ oldNum:oldNum, direction:direction });
		}
	});

	function menu_change() {
	/*console.log("2");*/
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
		//console.log($(".containerB").height());
		$(".containerB").height($(".containerB").height * 0.50);	//높이 수정해줘야 함

	}
	
	// 현재 번호
	function contentNum(_num) {
		
		var tenuNum = (_num == undefined) ? pageNum : _num;
		var returnNum = (tenuNum < 0) ? contentTotal + (tenuNum + 1) % contentTotal - 1 : tenuNum % contentTotal;
		return returnNum;

	}

});