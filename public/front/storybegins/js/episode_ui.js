/******************************
에피소드
******************************/

"use strict"

$(function() {
	
	var $ui = $("article.episode");
	var $page;
	var $scroll;

	EventManager.save($ui, CLICK_EVENT, event_handler);
	EventManager.save($ui, PAGE_RESIZE, event_handler);
	EventManager.save($ui, MAIN_EPISODE_CHANGE, event_handler);
	EventManager.save($ui, CONTROL_PAGE_CLICK, event_handler);

	function event_handler(_e) {
		
		switch(_e.type) {
			case CLICK_EVENT:
				page_change("next");
			break;
			case PAGE_RESIZE:
				episode_resize();
			break;
			case MAIN_EPISODE_CHANGE:
				if (Data.episodeNum != Data.episodeOld) episode_change();
			break;
			case CONTROL_PAGE_CLICK:
				var direction = (Data.pageNum > Data.pageOld) ? "next" : "prev";
				cube_change($page.eq(Data.pageOld), $page.eq(Data.pageNum), { direction:direction });
				page_open(Data.pageNum);
			break;
		}

	}

	// 리사이즈
	function episode_resize() {
		
		var $content = $ui.find(".content");
		if ($(document).width() >= 1920) {
			$content.children("h2").css({ fontSize:28 });
			$content.children(".eng").css({ marginBottom:52 })
			.children("p").css({ fontSize:18 })
			.parent().nextAll(".kor").css({ marginTop:59 })
			.children("p").css({ fontSize:14 });
			$content.find(".eng > h3").css({ marginBottom:27, fontSize:40 });
			$content.find(".kor > h3").css({ marginBottom:15, fontSize:22 });
			$ui.find(".first > .content > .eng").css({ marginTop:19 });
			$ui.find(".sub > .content").css({ right:120 });
			$ui.find(".next > .content > .eng").css({ marginTop:92 })
			.children("h3").css({ fontSize:33 })
			.parent().children(".button").css({ marginTop:127 });
		} else {
			$content.children("h2").css({ fontSize:28 - Math.round(5.6 * Data.resizeRatio) });
			$content.children(".eng").css({ marginBottom:52 - Math.round(21 * Data.resizeRatio) })
			.children("p").css({ fontSize:18 - Math.round(3.6 * Data.resizeRatio) })
			.parent().nextAll(".kor").css({ marginTop:59 - Math.round(23.6 * Data.resizeRatio) })
			.children("p").css({ fontSize:14 - Math.round(3 * Data.resizeRatio) });
			$content.find(".eng > h3").css({ marginBottom:27 - Math.round(5.4 * Data.resizeRatio), fontSize:40 - Math.round(8 * Data.resizeRatio) });
			$content.find(".eng > h3").css({ marginBottom:15 - Math.round(3 * Data.resizeRatio), fontSize:22 - Math.round(4 * Data.resizeRatio) });
			$ui.find(".first > .content > .eng").css({ marginTop:19 - Math.round(4 * Data.resizeRatio) });
			$ui.find(".sub > .content").css({ right:120 - Math.round(48 * Data.resizeRatio) });
			$ui.find(".next > .content > .eng").css({ marginTop:92 - Math.round(37 * Data.resizeRatio) })
			.children("h3").css({ fontSize:33 - Math.round(6.6 * Data.resizeRatio) })
			.parent().nextAll(".button").css({ marginTop:127 - Math.round(50 * Data.resizeRatio) });
		}
		$ui.find(".bg").css({ width:$ui.width() + Data.bgSpace, left:-Data.bgSpace });

	}
	
	// 에피소드
	function episode_change() {

		Data.pageNum = 0;
		Data.pageOld = 0;
		
		$ui.each(function(_i) {
			
			var $this = $(this);
			TweenMax.set($this, { autoAlpha:0, scale:1.2 });
			if (_i == Data.episodeNum) {
				$this.css({ zIndex:1 });
				TweenMax.to($this, 0.7, { autoAlpha:1, scale:1, ease:Cubic.easeOut, onComplete:episode_change_complete });

				$scroll = $this.find(".btn_scroll");
				$scroll.on("click", function() {

					page_change("next");

				});

				$page = $this.children("section");
				TweenMax.set($page, { autoAlpha:0, force3D:true });
				TweenMax.set($page.eq(Data.pageNum), { autoAlpha:1 });
				page_open(Data.pageNum);
			} else {
				$this.css({ zIndex:0 });
				$scroll.off();
			}

		});

	}

	function episode_change_complete() {
		
		$ui.eq(Data.episodeNum).css({ zIndex:0 });

	}

	// 페이지
	function page_open(_index) {
		
		$page.eq(_index).on("mousemove", function(_e) {

			var $bg = $(this).find(".bg");
			var targetX = Data.bgSpace - Math.floor(_e.pageX * Data.bgSpace / $page.width());
			TweenMax.to($bg, 3, { x:targetX, ease:Quad.easeOut });

		})
		.find("[data-animation]").each(function(_i) {

			TweenMax.set($(this), { autoAlpha:0, y:30 });
			TweenMax.to($(this), 1.2, { autoAlpha:1, y:0, delay:_i * 0.6 + 1, ease:Quad.easeOut, overwrite:1 });

		});
		if (Data.pageNum == $page.length - 1) TweenMax.to($scroll, 0.5, { autoAlpha:0, ease:Cubic.easeOut });
		else TweenMax.to($scroll, 0.5, { autoAlpha:1, ease:Cubic.easeOut });

	}

	function page_close(_index) {

		$page.eq(_index).off("mousemove");
		TweenMax.killTweensOf($page.eq(_index).find("[data-animation]"));

	}

	function page_change(_direction) {
		
		if (arguments.length < 1) {
			//console.log("episode_ui.js - page_change - 매개변수가 부족합니다.");
			return;
		}
		if (Data.isCubeChange) {
			//console.log("episode_ui.js - page_change - 화면 전환이 끝나지 않았습니다.");
			return;
		}
		
		if (_direction == "next" && Data.pageNum < $page.length - 1) {
			Data.pageOld = Data.pageNum;
			page_close(Data.pageOld);

			Data.pageNum++;
			cube_change($page.eq(Data.pageOld), $page.eq(Data.pageNum), {});
			page_open(Data.pageNum);
		}
		if (_direction == "prev" && Data.pageNum > 0) {
			Data.pageOld = Data.pageNum;
			page_close(Data.pageOld);

			Data.pageNum--;
			cube_change($page.eq(Data.pageOld), $page.eq(Data.pageNum), { direction:"prev" });
			page_open(Data.pageNum);
		}
		EventManager.load(EPISODE_PAGE_CHANGE);

	}
	
	// 마우스 휠
	$(document).on("wheel keydown", function(_e) {
		
		if (!Data.isIntro && !Data.isEpisode && !Data.isLayer) {
			if (_e.keyCode == 40 || _e.originalEvent.deltaY > 0) page_change("next");
			if (_e.keyCode == 38 || _e.originalEvent.deltaY < 0) page_change("prev");
		}

	});

});