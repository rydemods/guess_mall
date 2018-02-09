/* ==================================================
	공통함수
================================================== */

// 앵커 포커싱 이동
function focus_anchor(_target, _y) {
	
	if (arguments.length == 0) return;
	
	var $target = $(_target);
	$target.attr("tabIndex", -1).css({ outlineWidth:0 }).focus()
	.one("focusout", function() {
		$(this).css({ outlineWidth:"" }).removeAttr("tabIndex");
	});
	var scrolltop = (_y == undefined) ? $target[0].offsetTop : _y;
	$("#page").scrollTop(scrolltop);
	
}

// 앵커 스크롤 이동
function scroll_anchor(_target, _y) {
	
	if (arguments.length == 0) return;
	
	var $target = $(_target);
	var scrolltop = (_y == undefined) ? $target[0].offsetTop : _y;
	TweenMax.to($("#page"), 0.3, { scrollTop:scrolltop, ease:Sine.easeOut });;
	
}

// 팝업
function popup_open(_target) {
	
	if (arguments.length == 0) return;
	$(_target).show().closest(".popup-layer").show();
	$("#page").css({ overflow:"hidden" });
	
}

function popup_close(_target) {
	
	if (arguments.length == 0) return;
	$(_target).hide().closest(".popup-layer").hide();
	$("#page").css({ overflow:"auto" });
	
}

/* 이벤트 메니저 */
var EventManager = new function() {
		
	var objectA = [];
	this.save = function(_selector, _type, _handler, _data) {
		
		for (var i = 0; i < objectA.length; i++) if (_selector == objectA[i].selector && _type == objectA[i].type) return;
		_selector.on(_type, _data, _handler);
		objectA.push({ selector : _selector, type : _type });
		
	}
	this.load = function(_type, _paramA) {
		
		for (var i = 0; i < objectA.length; i++) if (_type == objectA[i].type) objectA[i].selector.trigger(_type, _paramA);
		
	}
	this.remove = function(_selector, _type) {
		
		_selector.off(_type);
		
	}
	
}


/* ==================================================
	페이지 스크립트
================================================== */
// 커스텀 이벤트
var CALL_RESIZE = "CALL_RESIZE";
var WINDOW_RESIZE = "WINDOW_RESIZE";
var FONT_RESIZE = "FONT_RESIZE";

$(function() {
	
	// 메인페이지
	var $main = $(".js-main").each(function() {

		var $ui = $(this);
		var $listInner = $(".js-main-list .main-list-inner");
		var $content = $(".js-main-list-content");
		
		if (!$content.attr("data-url")) return;
		
		// 페이지 로드
		var contentCount = 0;
		$content.each(function(_i){
			
			var $this = $(this);
			var url = $this.data("url");
			var index = _i;

            if ( contentCount == 0 ) {
                if ( $this.attr("data-url") != "./mainShop.php" && $this.attr("data-url") != "./mainBlank.html" ) {
                    $this.removeAttr("data-url").load(url + " #content > *", complete_handler);
                    function complete_handler() {
                        ui_init(); // 컨텐츠 스크립트를 메인에서는 로드 이후 적용
                    }
                }
            }

            contentCount++;
		});
		
		EventManager.save($ui, WINDOW_RESIZE, main_resize);
		EventManager.save($ui, FONT_RESIZE, main_resize);
		
		function main_resize() {
			
			var tempNum = $ui.triggerHandler("carousel_getPageNum");
			if (tempNum == undefined) return;
			
			$listInner.height($content.eq(tempNum).outerHeight());
			//menu_slide(); // 20160227 - 삭제
			
		}
		
		function menu_slide() {
			
			var tempNum = $ui.triggerHandler("carousel_getPageNum");
			if (tempNum == undefined) return;
			
			$ui.trigger("slidemenu_change", [tempNum, { time:0.25, ease:Sine.easeInOut }]);
			$("#page").scrollTop(0); // 20160227 - 메뉴 슬라이딩 시 최상단 이동 추가
			
		}
		
		// 움직임 연결
		$ui.carousel({ list:".js-main-list .main-list-inner", content:".js-main-list-content", page:".js-main-menu-content", startHandler:menu_slide, completeHandler:main_resize });
		$ui.slidemenu({ list:".js-main-menu", menu:".js-main-menu-content", line:".js-main-menu-line", isClick:false });
		
		//메인 메뉴 드래그 막음 2016-03-24
		$ui.find(".js-main-list .main-list-inner").off("touchstart touchend touchmove touchcancel");
		
	});
	
	// 컨텐츠 스크립트
	if (!$main[0]) ui_init();
	
});

function ui_init() {
	
	var $$header = $("#header");
	var $$page = $("#page");
	var $$content = $("#content");
	var $$footer = $("#footer");
	var $$toolbar = $("#toolbar");
	
	// 리사이즈
	$(window).on("load resize orientationchange", window_resize);
	EventManager.save($(window), CALL_RESIZE, window_resize);
	window_resize();
	
	function window_resize() {
		
		EventManager.load(WINDOW_RESIZE);
		$$page.css({ top:$$header.outerHeight() });
		var contentMinH = $$page.height() - $$footer.outerHeight();
		if (contentMinH < 0) contentMinH = 0;
		$$content.css({ minHeight:contentMinH });
		
	}
	
	// 페이지/위젯/탑/상세툴바
	$$page.each(function() {
		
		var $ui = $(this);
		var $widget = $(".js-widget");
		var $top = $(".js-btn-top");
		var $toolMenu = $$toolbar.find(".menu");// 메뉴툴바
		var $toolBuy = $$toolbar.find(".js-tool-buy");// 상세툴바

		var scrollTotal;
		var scrollTop;
		
		$$toolbar.data("isShow", true);
		TweenMax.set($top, { autoAlpha:0 });
		
		$(window).on("scroll", page_scroll);
		page_scroll(null);
		
		function page_scroll(_e) {
			
			if (_e) {
				scrollTotal = this.scrollHeight - this.clientHeight;
				scrollTop = this.scrollTop;
			}
			
			// top버튼 위치
			var toolbarH = $$toolbar.outerHeight();
			if ( $(window).scrollTop() > 100) $top.addClass("on");
			else $top.removeClass("on");
			var tempBottom = (scrollTop > scrollTotal - 35) ? toolbarH + (scrollTop - (scrollTotal - 35)) : toolbarH;
			$top.css({ bottom:tempBottom });
			
			// 위젯버튼 위치
			//$widget.css({ bottom:$$toolbar.outerHeight() + 6 });
			
			// 숨기기/보이기
			TweenMax.killDelayedCallsTo(page_scroll_end);
			TweenMax.delayedCall(0.1, page_scroll_end); //속도 조절 1 -> 0.1
			if (_e && $$toolbar.data("isShow")) {
				$$toolbar.data("isShow", false);
				if( $(".goods-detail-buy").length == 0 ) {
					TweenMax.to($toolBuy, 0.3, { autoAlpha:0, height:0, ease:Cubic.easeOut, onUpdate:page_scroll_update });
					//TweenMax.to($widget, 0.3, { autoAlpha:0, ease:Cubic.easeOut });
					//TweenMax.to($top, 0.3, { autoAlpha:0, ease:Cubic.easeOut });
				} 
			}

			// 상세 툴바
			if ($toolBuy[0]) {

				var $detailBuy = $(".goods-detail-buy");

                if ( $detailBuy.length > 0 ) {
					// 상품 상세에서는 무조건 나오게 변경
                   // var limitY = $detailBuy.position().top + $detailBuy.outerHeight();
					var targetY = $('.js-goods-detail-content').offset();
                    //if (scrollTop > limitY) { // 변경
					//if( $(window).height() > targetY.top + 25 ) { // 변경
					if( $(window).scrollTop() > targetY.top - $(window).height() ) {
                        $toolBuy.show();
                        //$toolMenu.hide();
                    } else {
                        $toolBuy.fadeOut().trigger("close");;
                        //$toolMenu.show();
                    }
					//$toolBuy.show();
                    $toolMenu.hide();
                    var childH = $$toolbar.children().filter(":visible").outerHeight();
                    $$footer.css({ paddingBottom:childH });
                    $(".js-font").css({ bottom:childH + 10 });
                }
			}
			
		}
		
		function page_scroll_end() {
			if (!$$toolbar.data("isShow")) {
				$$toolbar.data("isShow", true);
				var childH = $$toolbar.children().filter(":visible").outerHeight();
				
				if( $.type( childH ) == 'number' ){
					TweenMax.to($$toolbar, 0.4, { autoAlpha:1, height:childH, ease:Sine.easeInOut, onUpdate:page_scroll_update });
					//TweenMax.to($widget, 0.4, { autoAlpha:1, ease:Sine.easeOut });
					TweenMax.to($top, 0.4, { autoAlpha:1, ease:Sine.easeOut });
				} else {
					//TweenMax.to($$toolbar, 0.1, { autoAlpha:0, height:0, ease:Sine.easeInOut, onUpdate:page_scroll_update });
					$$toolbar.css( 'opacity', 0 ).css( 'height', 0 );
				}
			}
			
		}
		
		function page_scroll_update() {
			
			page_scroll();
			window_resize();
			
		}
		
		// 위젯 메뉴
		$widget.each(function() {
			
			// 20160227 시작 - 닫기/속도 수정
			var $ui = $(this);
			var $toggle = $ui.find(".js-widget-toggle");
			var $toggleCross = $toggle.find(".js-cross");
			var $content = $ui.find(".js-widget-content");
			var $close = $ui.next(".js-layer-dim");
			
			TweenMax.set($content, { autoAlpha:0, scale:0.3 });
			$toggle.on("click", function(_e) {
				
				$ui.toggleClass("on");
				if ($ui.hasClass("on")) {
					$$page.css({ overflow:"hidden" });
					TweenMax.to(this, 0.3, { rotation:-144, ease:Sine.easeInOut });
					TweenMax.to($toggleCross, 0.3, { marginTop:2, rotation:9, ease:Sine.easeInOut });
					TweenMax.to($content, 0.3, { autoAlpha:1, scale:1, ease:Back.easeOut });
				} else {
					widget_close();
				}
				
			});
			
			$close.on("click", function(_e) {
				
				$ui.removeClass("on");
				widget_close();
				
			});
			
			function widget_close() {
				
				$$page.css({ overflow:"auto" });
				TweenMax.to($toggle, 0.3, { rotation:0, ease:Sine.easeOut });
				TweenMax.to($toggleCross, 0.3, { marginTop:1, rotation:0, ease:Sine.easeOut });
				TweenMax.to($content, 0.3, { autoAlpha:0, scale:0.3, ease:Sine.easeOut });
				
			}
			// 20160227 끝 - 닫기/속도 수정
			
		});
		
		// 툴바 메뉴 (상세)
		$toolBuy.each(function() {
			
			var $ui = $(this);
			var $open = $toolBuy.find(".offbox .btn-shoppingbag, .offbox .btn-buy");
			var $close = $ui.find(".js-btn-close");
			var $content = $ui.find(".js-onbox");
			
			$open.on("click", function(_e) {
				
				_e.preventDefault();
				$$toolbar.addClass("on");
				$content.show();
				TweenMax.to($content, 0.5, { bottom:0, ease:Cubic.easeInOut });
				
			});
			
			$close.on("click", function(_e) {
				
				TweenMax.to($content, 0.5, { bottom:"-100%", ease:Sine.easeOut,
					onComplete:function() {
						$content.hide();
						$$toolbar.removeClass("on");
					}
				});
				
			});
			
			$ui.on({
				"close":function() {
					$close.trigger("click");
				}
			});
			
		});
		
	});
	
	// 본문 바로가기
	$(".js-skipnav").on("focusin focusout", function(_e) {
		
		var $this = $(this);
		switch(_e.type) {
			case "focusin" :
				$this.addClass("on");
			break;
			case "focusout" :
				$this.removeClass("on");
			break;
		}
		
	});
	
	// 헤더 - 배너
	$(".js-banner").each(function() {
		
		var $ui = $(this);
		var $close = $ui.find(".js-btn-close");
		
		$close.on("click", function(_e) {
			
			$ui.hide();
			page_resize();
			
		});
		
	});
	
	// 헤더 - 카테고리 메뉴
	$(".js-category").each(function() {
		
		var $ui = $(this);
		var $open = $ui.find(".js-btn-open");
		var $close = $ui.find(".js-layer-dim, .js-btn-close");// 20160227 - 버튼 수정
		var $layer = $ui.find(".js-layer");
		var $inner = $layer.find(".js-layer-inner");
		
		$open.on("click", function(_e) {
			
			$layer.addClass("on");
			$$header.css({ zIndex:1000 });
			$$page.css({ overflow:"hidden" });
			TweenMax.to($inner, 0.5, { left:0, ease:Cubic.easeInOut });
			
		});
		
		$close.on("click", function(_e) {
			
			TweenMax.to($inner, 0.2, { left:"-100%", ease:Cubic.easeOut, onComplete:close_complete });
			
		});
		
		function close_complete() {
			
			$$page.css({ overflow:"auto" });
			$$header.css({ zIndex:100 });
			$layer.removeClass("on");
			
		}
		
		$(".js-category-tab").tabcontent({ menu:".js-category-tab-menu", content:".js-category-tab-content" });
		$(".js-category-accordion").accordion({ menu:".js-category-accordion-menu", content:".js-category-accordion-content" });
		$(".js-lastdepth-accordion").accordion({ menu:".js-lastdepth-menu", content:".js-lastdepth-content" });
		$(".js-myreview-accordion").accordion({ menu:".js-review-item", content:".js-review-content" });
		
	});
	
	// 헤더 - 마이페이지 메뉴
	$(".js-mypage").each(function() {
		
		var $ui = $(this);
		var $open = $ui.find(".js-btn-open");
		var $close = $ui.find(".js-layer-dim, .js-btn-close");// 20160227 - 버튼 수정
		var $layer = $ui.find(".js-layer");
		var $inner = $layer.find(".js-layer-inner");
		
		$open.on("click", function(_e) {
			
			$layer.addClass("on");
			$$header.css({ zIndex:1000 });
			$$page.css({ overflow:"hidden" });
			TweenMax.to($inner, 0.5, { right:0, ease:Cubic.easeInOut });
			
		});
		
		$close.on("click", function(_e) {
			
			TweenMax.to($inner, 0.2, { right:"-100%", ease:Cubic.easeOut, onComplete:close_complete });
			
		});
		
		function close_complete() {
			
			$$page.css({ overflow:"auto" });
			$$header.css({ zIndex:100 });
			$layer.removeClass("on");
			
		}
		
	});
	
	// 헤더 - 검색
	$(".js-search").each(function() {
		
		var $ui = $(this);
		var $toggle = $ui.find(".js-btn-open");
		var $close = $ui.find(".js-layer-dim, .js-btn-close");
		var $layer = $ui.find(".js-layer");
		
		var isOpen = false;
		
		$toggle.on("click", function(_e) {
			
			isOpen = !isOpen;
			if (isOpen) content_open();
			else content_close();
			
		});
		
		/* 20160227 시작 - 검색 스크롤 속도 조절 */
		$close.on("click", function(_e) {
			
			isOpen = false;
			content_close();
			
		});
		
		$(".js-search-tab").tabcontent({ menu:".js-search-tab-menu", content:".js-search-tab-content" });
		
		function content_open() {
			
			$layer.stop().slideDown(300);
			$ui.addClass("on");
			$$header.css({ zIndex:1000 });
			$$page.css({ overflow:"hidden" });
			
		}
		
		function content_close() {
			
			$ui.removeClass("on");
			$layer.stop().slideUp(150, content_close_complete);
			
		}
		
		function content_close_complete() {
			
			
			$$header.css({ zIndex:100 });
			$$page.css({ overflow:"auto" });
			
		}
		/* 20160227 끝 - 검색 스크롤 속도 조절 */
		
	});

	// 푸터 - 브랜드 SNS
	var $footerBrand = $(".js-brand");
	$footerBrand.carousel({ list:".js-brand-list ul", content:".js-brand-content", arrow:".js-brand-arrow", completeHandler:footer_brand_complete });
	function footer_brand_complete() {
		
		var tempNum = $footerBrand.triggerHandler("carousel_getPageNum");
		if (tempNum == undefined) return;
		$footerBrand.find(".js-brand-sns-content").removeClass("on").eq(tempNum).addClass("on");
		
	}
	
	// 푸터 - 폰트사이즈
	$(".js-font").each(function() {
		
		var $ui = $(this);
		var $small = $ui.find(".js-btn-small");
		var $big = $ui.find(".js-btn-big");
		var $html = $("html");
		var min = $ui.data("min");
		var max = $ui.data("max");
		
		$small.on("click", function(_e) {
			
			var fontsize = parseFloat($html.css("font-size"));
			if (fontsize <= min) return;
			$html.css({ fontSize:(fontsize - 1) + "px" });
			button_check();
			
		});
		
		$big.on("click", function(_e) {
			
			var fontsize = parseFloat($html.css("font-size"));
			if (fontsize >= max) return;
			$html.css({ fontSize:(fontsize + 1) + "px" });
			button_check();
			
		});
		
		button_check();
		function button_check() {
			
			var fontsize = parseFloat($html.css("font-size"));
			$small.prop("disabled", false);
			$big.prop("disabled", false);
			if (fontsize <= min) $small.prop("disabled", true);
			if (fontsize >= max) $big.prop("disabled", true);
			
			//EventManager.load(FONT_RESIZE);
			EventManager.load(CALL_RESIZE);
			
		}
		
	});
	
	// 서브 타이틀 메뉴
	$(".js-sub-menu").accordion({ menu:".js-btn-toggle", content:".js-menu-content" });
	
	// 메인 샵 - 히어로배너
//	$(".js-shop-hero").carousel({ list:".js-carousel-list ul", isAuto:true });
	//$(".js-shop-hero").carousel({ list:".js-carousel-list ul", isAuto:false});

	
	
	//2016-04-28 슬라이더 반복 해제로 변경
	$(".js-shop-hero").each(function() {
		
		var $ui = $(this);
		var $sliderPage = $ui.find(".js-carousel-page");
		var pageNum = 0;
		
		function slider_page_change() {
			
			$sliderPage.removeClass("on").removeAttr("title")
			.eq(pageNum).addClass("on").attr("title", "선택됨");
			
		}
		
		function slider_slide_change() {
			
			pageNum = $ui.triggerHandler("slider_getPageNum");
			slider_page_change();
			
		}
		
		$ui.slider({ list:".js-carousel-list ul", content:".js-carousel-content", arrow:".js-carousel-arrow", startHandler:slider_slide_change });
		$sliderPage.on("click", function(_e) {
			
			_e.preventDefault();
			pageNum = $sliderPage.index(this);
			$ui.trigger("slider_change", pageNum);
			
		});
		
	});
	
	// 메인 샵 - MD PICK
	$(".js-shop-category").each(function() {
		
		var $ui = $(this);
		
		$ui.tabcontent();
		$ui.slidemenu({ list:".js-menu-list", menu:".js-tab-menu", line:".js-tab-line" });
		
	});

	$(".js-shop-mdpick").each(function() {
		
		/*
		var $ui = $(this);
		var $list = $ui.find(".js-carousel-list");
		var $content = $ui.find(".js-carousel-content");
		
		EventManager.save($ui, WINDOW_RESIZE, list_resize);
		EventManager.save($ui, FONT_RESIZE, list_resize);
		
		function list_resize() {
			
			$list.css({ paddingTop:$content.height() });
			
		}
		
		$ui.carousel();
		*/


		// 2016-04-28 무한 반복 제거:s
		
		var $ui = $(this);
		var $sliderPage = $ui.find(".js-carousel-page");
		var $content = $ui.find(".js-carousel-content");
		var $uiHeight = $ui.find(".js-carousel-list");
		var pageNum = 0;

		EventManager.save($ui, WINDOW_RESIZE, list_resize);
		EventManager.save($ui, FONT_RESIZE, list_resize);

		function list_resize() {
			
			$uiHeight.css({ paddingTop:$content.height() });
			
		}
		
		function slider_page_change() {
			
			$sliderPage.removeClass("on").removeAttr("title")
			.eq(pageNum).addClass("on").attr("title", "선택됨");
			
		}
		
		function sample_slide_change() {
			
			pageNum = $ui.triggerHandler("slider_getPageNum");
			slider_page_change();
			
		}
		
		$ui.slider({ list:".js-carousel-list", content:".js-carousel-content", startHandler:sample_slide_change });
		$sliderPage.on("click", function(_e) {
			
			_e.preventDefault();
			pageNum = $sliderPage.index(this);
			$ui.trigger("slider_change", pageNum);
			
		});
		// 2016-04-28 무한 반복 제거:e
		
		
		
	});
	
	// 메인 샵 - ONLY CASH
	$(".js-shop-cash").each(function() {
		
		/*
		var $ui = $(this);
		var $list = $ui.find(".js-carousel-list");
		var $content = $ui.find(".js-carousel-content");
		
		EventManager.save($ui, WINDOW_RESIZE, list_resize);
		EventManager.save($ui, FONT_RESIZE, list_resize);
		
		function list_resize() {
			
			$list.css({ paddingTop:$content.height() });
			
		}
		
		$ui.carousel();
		*/

		// 2016-04-28 무한 반복 제거:s
		
		var $ui = $(this);
		var $sliderPage = $ui.find(".js-carousel-page");
		var $content = $ui.find(".js-carousel-content");
		var $uiHeight = $ui.find(".js-carousel-list");
		var pageNum = 0;

		EventManager.save($ui, WINDOW_RESIZE, list_resize);
		EventManager.save($ui, FONT_RESIZE, list_resize);

		function list_resize() {
			
			$uiHeight.css({ paddingTop:$content.height() });
			
		}
		
		function slider_page_change() {
			
			$sliderPage.removeClass("on").removeAttr("title")
			.eq(pageNum).addClass("on").attr("title", "선택됨");
			
		}
		
		function sample_slide_change() {
			
			pageNum = $ui.triggerHandler("slider_getPageNum");
			slider_page_change();
			
		}
		
		$ui.slider({ list:".js-carousel-list", content:".js-carousel-content", startHandler:sample_slide_change });
		$sliderPage.on("click", function(_e) {
			
			_e.preventDefault();
			pageNum = $sliderPage.index(this);
			$ui.trigger("slider_change", pageNum);
			
		});
		// 2016-04-28 무한 반복 제거:e
		
	});
	
	// 메인 샵 - 배너
	//$(".js-shop-banner").carousel({ list:".js-carousel-list ul" });
	
	//2016-05-02 슬라이더 반복 해제로 변경
	$(".js-shop-banner").each(function() {
		
		var $ui = $(this);
		var $sliderPage = $ui.find(".js-carousel-page");
		var pageNum = 0;
		
		function slider_page_change() {
			
			$sliderPage.removeClass("on").removeAttr("title")
			.eq(pageNum).addClass("on").attr("title", "선택됨");
			
		}
		
		function slider_slide_change() {
			
			pageNum = $ui.triggerHandler("slider_getPageNum");
			slider_page_change();
			
		}
		
		$ui.slider({ list:".js-carousel-list ul", content:".js-carousel-content", arrow:".js-carousel-arrow", startHandler:slider_slide_change });
		$sliderPage.on("click", function(_e) {
			
			_e.preventDefault();
			pageNum = $sliderPage.index(this);
			$ui.trigger("slider_change", pageNum);
			
		});
		
	});
	
	
	// 메인 샵 - 베스트브랜드
	$(".js-shop-best").each(function() {
		/*
		var $ui = $(this);
		var $list = $ui.find(".js-carousel-list");
		var $content = $ui.find(".js-carousel-content");
		
		EventManager.save($ui, WINDOW_RESIZE, list_resize);
		EventManager.save($ui, FONT_RESIZE, list_resize);
		
		function list_resize() {
			
			$list.css({ paddingTop:$content.height() });
			
		}
		
		$ui.carousel();
		*/
		
		// 2016-05-02 무한 반복 제거:s
		
		var $ui = $(this);
		var $sliderPage = $ui.find(".js-carousel-page");
		var $content = $ui.find(".js-carousel-content");
		var $uiHeight = $ui.find(".js-carousel-list");
		var pageNum = 0;

		EventManager.save($ui, WINDOW_RESIZE, list_resize);
		EventManager.save($ui, FONT_RESIZE, list_resize);

		function list_resize() {
			
			$uiHeight.css({ paddingTop:$content.height() });
			
		}
		
		function slider_page_change() {
			
			$sliderPage.removeClass("on").removeAttr("title")
			.eq(pageNum).addClass("on").attr("title", "선택됨");
			
		}
		
		function sample_slide_change() {
			
			pageNum = $ui.triggerHandler("slider_getPageNum");
			slider_page_change();
			
		}
		
		$ui.slider({ list:".js-carousel-list", content:".js-carousel-content", startHandler:sample_slide_change });
		$sliderPage.on("click", function(_e) {
			
			_e.preventDefault();
			pageNum = $sliderPage.index(this);
			$ui.trigger("slider_change", pageNum);
			
		});
		// 2016-05-02 무한 반복 제거:e
		
	});
	
	// 메인 샵 - 스튜디오
	$(".js-shop-studio").each(function() {
		
		var $ui = $(this);
		
		$ui.slider({ list:".js-menu-list", content:".js-tab-menu" });
		$ui.tabcontent({ completeHandler:tab_change });
		$ui.slidemenu({ list:".js-menu-list", menu:".js-tab-menu", line:".js-tab-line" });
		
		function tab_change() {
			
			$ui.trigger("slider_change", [$ui.triggerHandler("tabcontent_getMenuNum") - 1, -2]); // 20160227 - 클릭 시 슬라이드 이동 추가
			EventManager.load(CALL_RESIZE);
			
		}
		
	});
	$(".js-studio-carousel").each(function() {
		/*
		var $ui = $(this);
		var $list = $ui.find(".js-carousel-list");
		var $content = $ui.find(".js-carousel-content");
		
		EventManager.save($ui, WINDOW_RESIZE, list_resize);
		EventManager.save($ui, FONT_RESIZE, list_resize);
		
		function list_resize() {
			
			$list.css({ paddingTop:$content.height() });
			
		}
		$ui.carousel();
		*/
		
		// 2016-05-02 무한 반복 제거:s
		
		var $ui = $(this);
		var $sliderPage = $ui.find(".js-carousel-page");
		var $content = $ui.find(".js-carousel-content");
		var $uiHeight = $ui.find(".js-carousel-list");
		var pageNum = 0;

		EventManager.save($ui, WINDOW_RESIZE, list_resize);
		EventManager.save($ui, FONT_RESIZE, list_resize);

		function list_resize() {
			
			$uiHeight.css({ paddingTop:$content.height() });
			
		}
		
		function slider_page_change() {
			
			$sliderPage.removeClass("on").removeAttr("title")
			.eq(pageNum).addClass("on").attr("title", "선택됨");
			
		}
		
		function sample_slide_change() {
			
			pageNum = $ui.triggerHandler("slider_getPageNum");
			slider_page_change();
			
		}
		
		$ui.slider({ list:".js-carousel-list", content:".js-carousel-content", startHandler:sample_slide_change });
		$sliderPage.on("click", function(_e) {
			
			_e.preventDefault();
			pageNum = $sliderPage.index(this);
			$ui.trigger("slider_change", pageNum);
			
		});
		// 2016-05-02 무한 반복 제거:e
		
	});
	
	// 메인 프로모션 - 히어로배너
//	$(".js-promo-hero").carousel({ list:".js-carousel-list ul", isAuto:true });
	$(".js-promo-hero").carousel({ list:".js-carousel-list ul", isAuto:false });
	
	// 메인 프로모션 - 리스트 탭 */
	$(".js-promo-list").each(function() {
		
		var $ui = $(this);
		
		$ui.tabcontent();
		$ui.slidemenu({ list:".js-menu-list", menu:".js-tab-menu", line:".js-tab-line" });
		
	});
	
	// 메인 스튜디오 - 룩북 비주얼
	$(".js-studio-lookbook-visual").each(function() {
		
		var $ui = $(this);
		
		$ui.carousel({ list:".js-carousel-list ul", isAuto:false});
		$ui.find(".js-menu-list").accordion({ menu:".js-btn-toggle", content:".js-list-content" });
		
	});
	
	// 상품 리스트 메인 - 히어로배너
	$(".js-goods-hero").carousel({ list:".js-goods-hero-list ul", content:".js-goods-hero-content", page:".js-goods-hero-page", arrow:".js-goods-hero-arrow", isAuto:true });
	
	// 상품 리스트 - 보기 방식
	var $goodsType = $(".js-goods-type");
	$goodsType.on("click", function(_e) {
		
		var $list = $(".js-goods-list");
		$goodsType.removeClass("on");
		$(this).addClass("on");
		if ($(this).data("type") == "single") $list.addClass("single");
		else $list.removeClass("single");
		
	});
	
	// 상품 상세 - 상단이미지
	$(".js-goods-detail-img").carousel({ list:".js-carousel-list ul" });
	
	// 상품 상세 - 컨텐츠탭
	$(".js-goods-detail-content").each(function() {
		
		var $ui = $(this);
		
		$ui.tabcontent();
		$ui.slidemenu({ list:".js-menu-list", menu:".js-tab-menu", line:".js-tab-line" });
		
	});

	// 라인 따라다니는 탭 기본
	$(".js-tab-component").each(function() {
		
		var $ui = $(this);
		
		$ui.tabcontent();
		$ui.slidemenu({ list:".js-menu-list", menu:".js-tab-menu", line:".js-tab-line" });
		
	});
	
	// 상품 상세 - 리뷰 리스트
	$(".js-review-accordion").accordion();

	// CS - FAQ
	$(".js-faq-accordion").accordion({ menu:".js-faq-accordion-menu", content:".js-faq-accordion-content" });
	
	// 상품 상세 - 관련상품
	$(".js-goods-detail-related").each(function() {
		
		var $ui = $(this);
		var $list = $ui.find(".js-carousel-list");
		var $content = $ui.find(".js-carousel-content");
		
		EventManager.save($ui, WINDOW_RESIZE, list_resize);
		EventManager.save($ui, FONT_RESIZE, list_resize);
		
		function list_resize() {
			
			$list.css({ paddingTop:$content.height() });
			
		}
		
		$ui.carousel();
		
	});
	
	// 상품 Q&A - 아코디언
	$(".js-goods-qna-accordion").accordion({ menu:".js-goods-qna-accordion-menu", content:".js-goods-qna-accordion-content" });
	
	// 20160227 시작 - 클릭 시 슬라이드 이동 추가/수정
	// 브랜드 메인 - 슬라이드 메뉴
	$(".js-brand-menu").each(function() {
		
		var $ui = $(this);
		
		$ui.slider({ list:".js-brand-menu-list", content:".js-brand-menu-content" });
		$ui.slidemenu({ list:".js-brand-menu-list", menu:".js-brand-menu-content", line:".js-brand-menu-line", startHandler:menu_change });
		
		function menu_change() {
			
			$ui.trigger("slider_change", $ui.triggerHandler("slidemenu_getContentNum") - 1);
			
		}
		
	});
	// 20160227 끝 - 클릭 시 슬라이드 추가/수정 
	
	// 브랜드 상품 - 브랜드 정보
	$(".js-brand-info").each(function() {
		
		var $ui = $(this);
		var $toggle = $ui.find(".js-btn-toggle");
		
		$toggle.on("click", function(_e) {
			
			$ui.toggleClass("on");
			
		});
		
	});
	
	// 브랜드 상품 - 브랜드 이미지
	$(".js-brand-visual").carousel({ list:".js-brand-visual-list ul", content:".js-brand-visual-content", arrow:".js-brand-visual-arrow", isAuto:true });
	
	// 스튜디오 프레스/스타가되고싶니 - 리스트 관련상품
	$(".js-star-related").carousel({ list:".js-star-related-list", content:".js-star-related-content", arrow:".js-star-arrow", isVertical:true });
	
}


// 하단 메뉴툴바,위젯 숨기기/보이기
var didScroll;
var lastScrollTop = 0;
var delta = 5;
var toolbarHeight = $('#toolbar ul.menu').outerHeight();

$(window).scroll(function(event){
    didScroll = true;
});

setInterval(function() {
    if (didScroll) {
        hasScrolled();
        didScroll = false;
    }
}, 0);

function hasScrolled() {
    var st = $(this).scrollTop();
    
    if(Math.abs(lastScrollTop - st) <= delta)
        return;
    
    if (st > lastScrollTop && st > toolbarHeight){ //스크롤 내릴때 툴바,위젯 사라지기

		$('#toolbar ul.menu').stop().transition({y:46,delay:0},200,'linear');
		$('.js-widget').stop().transition({y:95,delay:0},200,'linear');
		$('.js-btn-top').stop().transition({y:84,delay:0},200,'linear');

        /* $('#toolbar ul.menu').removeClass('toolbar_down').addClass('toolbar_up');
        $('.js-widget').removeClass('toolbar_down').addClass('toolbar_up'); */

    } else {
        if(st + $(window).height() < $(document).height()) { //스크롤 올릴때 툴바,위젯 나타나기

			$('#toolbar ul.menu').stop().transition({y:0,delay:0},200,'linear');
			$('.js-widget').stop().transition({y:0,delay:0},200,'linear');
			$('.js-btn-top').stop().transition({y:0,delay:0},200,'linear');

            /* $('#toolbar ul.menu').removeClass('toolbar_up').addClass('toolbar_down');
            $('.js-widget').removeClass('toolbar_up').addClass('toolbar_down'); */

        }
    }
    
    lastScrollTop = st;
}


/* ==================================================
	커스텀 플러그인
================================================== */

(function($) {
	
	// 컨텐츠 롤링
	$.fn.carousel = function(_option) {
		
		return this.each(function() {
			
			var option = {
				list:".js-carousel-list",
				content:".js-carousel-content",
				page:".js-carousel-page",
				arrow:".js-carousel-arrow",
				isAuto:false,// 자동롤링
				isVertical:false,// 세로방향
				autoDuration:4,
				startHandler:null,
				completeHandler:null
			}
			
			var $ui = $(this);
			if ($ui.data("carousel")) return;
			$ui.data("carousel", true);
			
			if (typeof _option === "object") $.extend(option, _option);
			
			var $list = $ui.find(option.list);
			var $content = $list.find(option.content);
			var $page = $ui.find(option.page);
            var $arrow = $ui.find(option.arrow);
			
			var pageTotal = $content.length;
			var pageNum = 0;
			var isChange = false;
			var touchStartX = null;
			var touchStartY = null;
			var dragDirection = null;
			var timer;
			var timerDuration = option.autoDuration;

			if (pageTotal == 1) {
				$arrow.hide();
				return;
			}
			
			$(window).on("load resize orientationchange", content_sort);
			content_sort();
			
			// 페이지
			$page.on("click", function(_e) {
				
				_e.preventDefault();
				if (!isChange && contentNum() != $page.index(this)) {
					var oldNum = contentNum();
					pageNum = $page.index(this);
					var direction = (pageNum > oldNum) ? "next" : "prev";
					content_change({ oldNum:oldNum, direction:direction });
				}
				
			});
			
			menu_change();
			function menu_change() {
				
				$page.removeClass("on").removeAttr("title")
					.eq(contentNum()).addClass("on").attr("title", "선택됨");
				
				if (option.startHandler != null) option.startHandler();
				
			}
			
			// 화살표
			if ( $ui.attr('class') !== "js-main" ) { 
                $arrow.on("click", function(_e) {

                    _e.preventDefault();
                    if (!isChange) {
                        var oldNum = contentNum();
                        var direction = $(this).data("direction");

                        if (direction == "prev") pageNum = (pageNum == 0) ? pageTotal - 1 : pageNum - 1;
                        else pageNum = (pageNum == pageTotal - 1) ? 0 : pageNum + 1;
                        content_change({ oldNum:oldNum, direction:direction });

                        // ===================================================================================================
                        // 예외처리
                        // 메인 > STUDIO에서 룩북 롤링 오른쪽, 왼쪽 화살표를 클릭한 경우 하단 상품 리스트 변경
                        // ===================================================================================================
                        if ( $(this).attr("id") == "lookbook_left_arrow" || $(this).attr("id") == "lookbook_right_arrow" ) {
                            setTimeout( find_lookbook, 100 );
                        }
                    }
                    
                });
            }
			
			// 드래그
			$list.on("touchstart touchend touchmove touchcancel", function(_e) {
				
				_e.stopPropagation();
				
				var touchObj = _e.originalEvent.changedTouches[0];
				var moveX = touchObj.screenX - touchStartX;
				var moveY = touchObj.screenY - touchStartY;
				
				switch(_e.type) {
					case "touchstart":
						touchStartX = touchObj.screenX;
						touchStartY = touchObj.screenY;
						if (option.isAuto) timer_stop();
					break;
					case "touchend":
						if (!option.isVertical) {
							if (dragDirection == "horizontal") {
								if (Math.abs(moveX) > $content.width() * 0.2) {
									pageNum = (moveX < 0) ? pageNum + 1 : pageNum - 1;
									content_drag({ ease:Cubic.easeOut });
								} else content_drag();

                                // ===================================================================================================
                                // 예외처리
                                // 메인 > STUDIO에서 룩북 좌우로 이동할 경우 하단 상품 리스트 변경
                                // ===================================================================================================
                                if ( $(this).attr("id") == "lookbook_ul" ) {
                                    setTimeout( find_lookbook, 100 );
                                }
							}
						} else {
							if (dragDirection == "vertical") {
								if (Math.abs(moveY) > $content.height() * 0.2) {
									pageNum = (moveY < 0) ? pageNum + 1 : pageNum - 1;
									content_drag({ ease:Cubic.easeOut });
								} else content_drag();
							}
						}
						dragDirection = null;
					break;
					case "touchmove":
						if (dragDirection == null) dragDirection = (Math.abs(moveY) > Math.abs(moveX)) ? "vertical" : "horizontal";
						if (!option.isVertical) {
							if (dragDirection == "horizontal") {
								_e.preventDefault();
								$content.css({ visibility:"inherit" });
								var $temp;
								if (moveX < 0) {
									$temp = $content.eq(contentNum(pageNum + 1));
									TweenMax.set($temp, { x:$content.width() * pageNum + $content.width() * 1 });
								} else {
									$temp = $content.eq(contentNum(pageNum - 1));
									TweenMax.set($temp, { x:$content.width() * (pageNum - 1) });
								}
								TweenMax.set($list, { x:$list.data("x") + moveX });
							}
						} else {
							if (dragDirection == "vertical") {
								_e.preventDefault();
								$content.css({ visibility:"inherit" });
								var $temp;
								if (moveY < 0) {
									$temp = $content.eq(contentNum(pageNum + 1));
									TweenMax.set($temp, { y:$content.height() * pageNum + $content.height() * 1 });
								} else {
									$temp = $content.eq(contentNum(pageNum - 1));
									TweenMax.set($temp, { y:$content.height() * (pageNum - 1) });
								}
								TweenMax.set($list, { y:$list.data("y") + moveY });
							}
						}
					break;
					case "touchcancel":
						dragDirection = null;
					break;
				}
				
			});
			
			// 드래그 내용 변경 { time:이동 시간, ease:이동 효과 }
			function content_drag(_obj) {
				
				isChange = true;
				var tweenOption = $.extend({ time:0.3, ease:Cubic.easeInOut }, _obj);
				
				if (!option.isVertical) {
					$list.data("x", -$content.width() * pageNum);
					TweenMax.to($list, tweenOption.time, { x:$list.data("x"), ease:tweenOption.ease, onStart:menu_change, onComplete:content_tween_complete });
				} else {
					$list.data("y", -$content.height() * pageNum);
					TweenMax.to($list, tweenOption.time, { y:$list.data("y"), ease:tweenOption.ease, onStart:menu_change, onComplete:content_tween_complete });
				}
				
			}
			
			// 내용 변경 { oldNum:이전 번호, direction:이동 방향, time:이동 시간, ease:이동 효과 }
			function content_change(_obj) {
				
				isChange = true;
				var tweenOption = $.extend({ oldNum:null, direction:"next", time:0.4, ease:Cubic.easeInOut }, _obj);
				
				if (!option.isVertical) {
					var target = (tweenOption.direction == "next") ? -$content.width() : $content.width();
					$content.css({ visibility:"inherit" });
					
					TweenMax.set($list, { x:0 });
					if (tweenOption.oldNum != null) {
						var $old = $content.eq(tweenOption.oldNum);
						TweenMax.set($old, { x:0 });
						TweenMax.to($old, tweenOption.time, { x:target, ease:tweenOption.ease });
					}
					var $current = $content.eq(contentNum());
					TweenMax.set($current, { x:-target });
					TweenMax.to($current, tweenOption.time, { x:0, ease:tweenOption.ease, onStart:menu_change, onComplete:content_tween_complete });
				} else {
					var target = (tweenOption.direction == "next") ? -$content.height() : $content.height();
					$content.css({ visibility:"inherit" });
					
					TweenMax.set($list, { y:0 });
					if (tweenOption.oldNum != null) {
						var $old = $content.eq(tweenOption.oldNum);
						TweenMax.set($old, { y:0 });
						TweenMax.to($old, tweenOption.time, { y:target, ease:tweenOption.ease });
					}
					var $current = $content.eq(contentNum());
					TweenMax.set($current, { y:-target });
					TweenMax.to($current, tweenOption.time, { y:0, ease:tweenOption.ease, onStart:menu_change, onComplete:content_tween_complete });
				}
				
			}
			
			function content_tween_complete() {
				
				content_sort();
				isChange = false;
				if (option.isAuto) timer_reset();
				
			}
			
			// 내용 재정렬
			function content_sort() {
				
				pageNum = contentNum();
				
				if (!option.isVertical) {
					$content.css({ visibility:"inherit" }).each(function(i) {
						
						TweenMax.set($(this), { x:$(this).width() * i, force3D:true });
						if (i != contentNum()) $(this).css({ visibility:"hidden" });
						
					});
					$list.data("x", -$content.width() * pageNum);
					TweenMax.set($list, { x:$list.data("x"), force3D:true });
				} else {
//					console.log(contentNum());
					$content.css({ visibility:"inherit" }).each(function(i) {
						
						TweenMax.set($(this), { y:$(this).height() * i, force3D:true });
						if (i != contentNum()) $(this).css({ visibility:"hidden" });
						
					});
					$list.data("y", -$content.height() * pageNum);
					TweenMax.set($list, { y:$list.data("y"), force3D:true });
				}
				
				if (option.completeHandler != null) option.completeHandler();
				
			}
			
			// 현재 번호
			function contentNum(_num) {
				
				var tempNum = (_num == undefined) ? pageNum : _num;
				var returnNum = (tempNum < 0) ? pageTotal + (tempNum + 1) % pageTotal - 1 : tempNum % pageTotal;
				return returnNum;
				
			}
			
			// 자동롤링
			if (option.isAuto) {
				$ui.on("focusin focusout touchstart touchend touchcancel", function(_e) {
					
					timer_stop();
					switch(_e.type) {
						case "focusout":
						case "touchend":
						case "touchcancel":
							timer_reset();
						break;
					}
					
				});
				timer_start();
			}
			
			function timer_start() {
				
				timer = setInterval(function() {
					
					var oldNum = contentNum();
					pageNum = (pageNum == pageTotal - 1) ? 0 : pageNum + 1;
					content_change({ oldNum:oldNum, direction:"next" });
					
				}, timerDuration * 1000);
				
			}
			
			function timer_stop() {
				
				clearInterval(timer);
				
			}
			
			function timer_reset() {
				
				timer_stop();
				timer_start();
				
			}
			
			// trigger
			$ui.on({
				"carousel_getPageNum":function(_e, _data) {
					return contentNum();
				}
			});
			
		});
		
	}
	
	// 컨텐츠 슬라이더
	$.fn.slider = function(_option) {
		
		return this.each(function() {
			
			var option = {
				list:".js-slider-list",
				content:".js-slider-content",
				arrow:".js-slider-arrow",
				startHandler:null
			}
			
			var $ui = $(this);
			if ($ui.data("slider")) return;
			$ui.data("slider", true);
			
			if (typeof _option === "object") $.extend(option, _option);
			
			var $list = $ui.find(option.list);
			var $content = $list.find(option.content);
			var $arrow = $ui.find(option.arrow);
			
			var pageTotal;
			var pageNum;
			var pageXA = [];
			var isChange = false;
			var touchStartX = null;
			var touchStartY = null;
			var dragDirection = null;
			
			$(window).on("load resize orientationchange", content_sort);
			content_sort();
			
			// 화살표
			$arrow.on("click", function(_e) {
				
				_e.preventDefault();
				if (!isChange) {
					var direction = $(this).data("direction");
					if (direction == "prev") pageNum = (pageNum == 0) ? 0 : pageNum - 1;
					else pageNum = (pageNum == pageTotal) ? pageTotal : pageNum + 1;
					content_change({ time:0.4 });
				}
				
			});
			
			// 포커스
			var isContentDown = false;
			$content.on("mousedown mouseup focusin", function(_e) {
				
				switch(_e.type) {
					case "mousedown":
						isContentDown = true;
					break;
					case "mouseup":
						isContentDown = false;
					break;
					case "focusin":
						if (!isContentDown) {
							pageNum = $content.index(this);
							if (pageNum > pageTotal) pageNum = pageTotal;
							content_change({ time:0 });
						}
					break;
				}
				
			});
			
			// 드래그
			$list.on("touchstart touchend touchmove touchcancel", function(_e) {
				
				_e.stopPropagation();
				
				var touchObj = _e.originalEvent.changedTouches[0];
				var moveX = touchObj.screenX - touchStartX;
				var moveY = touchObj.screenY - touchStartY;
				
				switch(_e.type) {
					case "touchstart":
						touchStartX = touchObj.screenX;
						touchStartY = touchObj.screenY;
					break;
					case "touchend":
						if (dragDirection == "horizontal") {
							if (Math.abs(moveX) > $content.width() * 0.2) {
								var tempNum = getClosestObject(pageXA, -($list.data("x") + moveX)).index;
								if (tempNum == pageNum) {
									pageNum = (moveX < 0) ? pageNum + 1 : pageNum - 1;
									if (pageNum < 0) pageNum = 0;
									if (pageNum > pageTotal) pageNum = pageTotal;
								} else pageNum = tempNum;
								content_change({ ease:Cubic.easeOut });
							} else content_change();
						}
						dragDirection = null;
					break;
					case "touchmove":
						if (dragDirection == null) dragDirection = (Math.abs(moveY) > Math.abs(moveX)) ? "vertical" : "horizontal";
						if (dragDirection == "horizontal") {
							_e.preventDefault();
							TweenMax.set($list, { x:$list.data("x") + moveX });
						}
					break;
					case "touchcancel":
						dragDirection = null;
					break;
				}
				
			});
			
			// 내용 변경 { time:이동 시간, ease:이동 효과 }
			function content_change(_obj) {
				
				isChange = true;
				var tween = $.extend({ time:0.3, ease:Cubic.easeInOut }, _obj);
				
				$list.data("x", -pageXA[pageNum]);
				TweenMax.to($list, tween.time, { x:$list.data("x"), ease:tween.ease, onComplete:content_tween_complete });

				if (option.startHandler != null) option.startHandler();
				
			}
			
			function content_tween_complete() {
				
				isChange = false;
				
			}
			
			// 내용 재정렬
			function content_sort() {
				
				var listW = 0;
				var tempXA = [];
				$content.each(function(i) {
					
					tempXA[i] = (i == 0) ? 0 : tempXA[i - 1] + $content.eq(i - 1).outerWidth();
					listW += $(this).outerWidth();
					TweenMax.set($(this), { x:tempXA[i], force3D:true });
					
				});
				
				var max = listW - $list.width();
				var closest = null;
				pageXA = [];
				$.each(tempXA, function(i){
					if (closest == null || Math.abs(this - max) < Math.abs(closest - max)) {
						closest = this;
						pageXA[i] = closest;
					}
				});
				if (pageXA.length > 1) {
					if (max > closest && Math.abs(max - closest) > 2) pageXA.push(max);
					else pageXA[pageXA.length - 1] = max;
				}
				pageTotal = pageXA.length - 1;
				
				pageNum = $content.index($content.filter(".on"));
				if (pageNum == -1) pageNum = 0;
				
				if (pageNum > pageTotal) pageNum = pageTotal;
				$list.data("x", -pageXA[pageNum]);
				TweenMax.set($list, { x:$list.data("x"), force3D:true });

				if (option.startHandler != null) option.startHandler();
				
			}
			
			function getClosestObject(_array, _num) {
				
				var closest = null;
				var index;
				$.each(_array, function(_i) {
					
					if (closest == null || Math.abs(this - _num) < Math.abs(closest - _num)) {
						closest = this;
						index = _i;
					}
					
				});
				return { closest:closest, index:index }
				
			}

			// 20160227 시작 - 이벤트 추가
			// trigger
			$ui.on({
				"slider_change":function(_e, _data, _count) {
					pageNum = _data;
					var count = (_count == undefined) ? 0 : _count;
					if (pageNum < 0) pageNum = 0;
					if (pageNum > pageTotal + count) pageNum = pageTotal;
					content_change({ time:0.4 });
				},
				"slider_getPageNum":function(_e, _data) {
					return pageNum;
				}
			});
			// 20160227 끝 - 이벤트 추가
			
		});
		
	}
	
	// 탭 컨텐츠
	$.fn.tabcontent = function(_option) {
		
		return this.each(function() {
			
			var option = {
				menu:".js-tab-menu",
				content:".js-tab-content",
				completeHandler:null
			}
			
			var $ui = $(this);
			if ($ui.data("tabcontent")) return;
			$ui.data("tabcontent", true);
			
			if (typeof _option === "object") $.extend(option, _option);
			
			var $menu = $ui.find(option.menu);
			var $content = $ui.find(option.content);
			var menuNum = $menu.index($menu.filter(".on"));
			
			// 20160227 시작 - 이벤트 추가
			// trigger
			$ui.on({
				"tabcontent_getMenuNum":function(_e, _data) {
					return menuNum;
				}
			});
			// 20160227 끝 - 이벤트 추가
			
			$menu.on("click", function(_e) {
				
				menuNum = $menu.index(this);
				menu_change();
				
			});
			
			menu_change();
			function menu_change() {
				
				$menu.removeClass("on").removeAttr("title").eq(menuNum).addClass("on").attr("title", "선택됨");
				$content.removeClass("on").eq(menuNum).addClass("on");
				
				if (option.completeHandler != null) option.completeHandler();
				
			}
			
		});
		
	}
	
	// 아코디언 컨텐츠
	$.fn.accordion = function(_option) {
		
		return this.each(function() {
			
			var option = {
				menu:".js-accordion-menu",
				content:".js-accordion-content",
			}
			
			var $ui = $(this);
			if ($ui.data("accordion")) return;
			$ui.data("accordion", true);
			
			if (typeof _option === "object") $.extend(option, _option);
			
			var $menu = $ui.find(option.menu);
			var $content = $ui.find(option.content);
			var menuNum = $menu.index($menu.filter(".on"));
			if (menuNum == -1) menuNum = undefined;
			
			$menu.on("click", function(_e) {
				
				menuNum = ($(this).hasClass("on")) ? undefined : $menu.index(this);
				menu_change();
				
			});
			
			menu_change();
			function menu_change() {
				
				$menu.removeClass("on").attr("title", "펼쳐보기").eq(menuNum).addClass("on").attr("title", "접어놓기");
				$content.removeClass("on").eq(menuNum).addClass("on");;
				
			}
			
		});
		
	}
	
	// 슬라이드 메뉴
	$.fn.slidemenu = function(_option) {
		
		return this.each(function() {
			
			var option = {
				list:".js-menu-list",
				menu:".js-menu-content",
				line:".js-menu-line",
				isClick:true,
				startHandler:null // 20160227 - 이벤트추가
			}
			
			var $ui = $(this);
			if ($ui.data("slidemenu")) return;
			$ui.data("slidemenu", true);
			
			if (typeof _option === "object") $.extend(option, _option);
			
			var $list = $ui.find(option.list);
			var $menu = $ui.find(option.menu);
			var $line = $ui.find(option.line);
			
			var contentNum = $menu.index($menu.filter(".on"));
			if (contentNum == -1) contentNum = 0;
			
			// 20160227 시작 - 이벤트 추가/수정
			// trigger
			$ui.on({
				"slidemenu_change":function(_e, _data, _tweenOption) {
					contentNum = _data;
					content_change(_tweenOption);
				},
				"slidemenu_getContentNum":function(_e, _data) {
					return contentNum;
				}
			});
			// 20160227 끝 - 이벤트 추가/수정
			
			if (option.isClick) {
				$menu.on("click", function(_e) {
					
					_e.preventDefault();
					contentNum = $menu.index(this);
					content_change();
					
				});
			}
			
			$(window).on("load resize orientationchange", content_change);
			content_change();
			
			function content_change(_option) {
				
				var tweenOption = $.extend({ time:0.3, ease:Cubic.easeInOut }, _option);
				var $span = $menu.eq(contentNum).find("span");
				var listX = ($list.offset()) ? $list.offset().left : $ui.offset().left;

                try { 
    				TweenMax.to($line, tweenOption.time, { width:$span.outerWidth(), left:$span.offset().left - listX, ease:tweenOption.ease });
                } catch(e) {}
				
				if (option.startHandler != null) option.startHandler(); // 20160227 - 이벤트 추가
				
			}
			
		});
		
	}
	
})(jQuery);




// 2016-02-22 내부 작업 시작
$(document).ready(function() {
    $(".tabs-menu a").click(function(event) {
        event.preventDefault();
        $(this).parent().addClass("on");
        $(this).parent().siblings().removeClass("on");
        var tab = $(this).attr("href");
        $(".tab-content").not(tab).css("display", "none");
        $(tab).fadeIn();
    });
});

$(function(){
	// category box 클릭 ON
	$('.category-box li a').click(function(){
		// 카테고리 명
		var category_name = $(this).parent().parent().attr('name');
		
		if( category_name == 'delivery-category' ) { // 배송목록
			var delivery_category = $('ul[name="delivery-category"] li a');
			$(delivery_category).removeClass('on');
			$(this).addClass('on');
		} else if( category_name == 'payment-category' ) { // 결제방법
			var delivery_category = $('ul[name="payment-category"] li a');
			if( $(this).attr('href') == undefined ) return;
			$(delivery_category).removeClass('on');
			$(this).addClass('on');
		} else {
			$('.category-box li a').removeClass('on');
			$(this).addClass('on');
		}
	})

	//주문서 작성 결제 항목 토글
	$('.order-section dt button').click(function(){
		$(this).parent().toggleClass('close');
		$(this).parent().next().toggleClass('close');
	})
	
})

// ====================================================
// STUDIO 탭 룩북
// ====================================================

$(window).load(function() {
    changeLookbookItem = function() { setTimeout( find_lookbook, 100 ); };

    setTimeout( function() {
        try {
            $('#lookbook-visual').flexslider({
                animation: "slide",
                controlNav: false,
                animationLoop: false,
                slideshow: false,
                itemWidth: 110,
                itemMargin: 0,
                asNavFor: '#lookbook-thumb'
            });
            
            $('#lookbook-thumb').flexslider({
                animation: "slide",
                controlNav: false,
                animationLoop: false,
                slideshow: false,
                sync: "#lookbook-visual",
                before : changeLookbookItem,
            });
        } catch(e) {}
    }, 300);
});

