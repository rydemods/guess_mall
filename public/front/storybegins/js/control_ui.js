/******************************
하단 컨트롤 메뉴, 오디오 제어
******************************/

"use strict"

$(function() {
	
	var $ui = $("nav.control");
	var $episodeOpen = $ui.find("button.btn_episode");
	var $episodeClose = $ui.find("button.btn_close");
	var $main = $ui.find("div.main");
	var $mainTitle = $main.find("p");
	var $sub = $ui.find("div.sub");
	var $subTitle = $sub.find("p");
	var $episodeMenu = $main.find("ol.episode > li");

	var isChange = false;

	EventManager.save($ui, PAGE_RESIZE, event_handler);
	EventManager.save($ui, CUBE_CHANGE_COMPLETE, event_handler);
	EventManager.save($ui, INDEX_LOAD_COMPLETE, event_handler);
	EventManager.save($ui, INDEX_INTRO_OPEN, event_handler);
	EventManager.save($ui, INDEX_INTRO_CLOSE_COMPLETE, event_handler);
	EventManager.save($ui, MAIN_EPISODE_CHANGE, event_handler);
	EventManager.save($ui, EPISODE_PAGE_CHANGE, event_handler);

	function event_handler(_e) {
		//console.log("control", _e.type);
		switch(_e.type) {
			case PAGE_RESIZE:
				control_resize();
			break;
			case CUBE_CHANGE_COMPLETE:
				if (isChange) {
					//console.log("컨트롤 큐브");
					isChange = false;
					episode_toggle();
				}
			break;
			case INDEX_LOAD_COMPLETE:
				TweenMax.to($ui, 1, { bottom:0, ease:Quart.easeInOut });
			break;
			case INDEX_INTRO_OPEN:
				$episodeClose.hide()
				.next(".content").css({ marginLeft:0 });
				if (!Data.isEpisode) episode_open();
			break;
			case INDEX_INTRO_CLOSE_COMPLETE:
				Data.isEpisode = false;
				$episodeClose.show()
				.next(".content").css({ marginLeft:$episodeClose.outerWidth() });
			break;
			case MAIN_EPISODE_CHANGE:
				episode_change_menu();
			break;
			case EPISODE_PAGE_CHANGE:
				page_change_menu();
			break;
		}

	}

	// 리사이즈
	function control_resize() {
		
		var $box = $ui.find(".box");
		var $menuList = $ui.find("ul.menu");
		var $menu = $ui.find(".btn_menu");
		var $episodeMenuA = $episodeMenu.children("a");
		var $pageList = $ui.find("ol.page");
		var $pageLine = $pageList.find(".line");

		if ($(document).width() >= 1920) {
			$menu.width(73);
			$episodeOpen.width(110);
			$mainTitle.css({ paddingLeft:38, fontSize:20 })
			.children("span").css({ fontSize:14 });
			$subTitle.css({ paddingLeft:38, fontSize:20 })
			.children("span").css({ fontSize:14 });
			$episodeMenuA.css({ marginLeft:20 });
			$pageList.css({ paddingLeft:28 });
			$pageLine.width(40);
		} else {
			$menu.width(73 - Math.round(30 * Data.resizeRatio));
			$episodeOpen.width(110 - Math.round(50 * Data.resizeRatio));
			$mainTitle.css({ paddingLeft:38 - Math.round(20 * Data.resizeRatio), fontSize:20 - Math.round(4 * Data.resizeRatio) })
			.children("span").css({ fontSize:14 - Math.round(3 * Data.resizeRatio) });
			$subTitle.css({ paddingLeft:38 - Math.round(20 * Data.resizeRatio), fontSize:20 - Math.round(4 * Data.resizeRatio) })
			.children("span").css({ fontSize:14 - Math.round(3 * Data.resizeRatio) });
			$episodeMenuA.css({ marginLeft:20 - Math.round(14 * Data.resizeRatio) });
			$pageList.css({ paddingLeft:28 - Math.round(18 * Data.resizeRatio) });
			$pageLine.width(40 - Math.round(34 * Data.resizeRatio));
		}
		$box.css({ marginRight:$menuList.width() });
		$episodeClose.width($episodeOpen.outerWidth());
		if (!Data.isIntro) $main.find(".content").css({ marginLeft:$episodeOpen.outerWidth() });
		$sub.find(".content").css({ marginLeft:$episodeOpen.outerWidth() });

	}

	// 챕터 보기/숨기기
	$episodeOpen.on("click", function(_e) {
		
		episode_open();
		EventManager.load(CONTROL_EPISODE_OPEN);

	});

	$episodeClose.on("click", function(_e) {
		
		episode_close();
		EventManager.load(CONTROL_EPISODE_CLOSE);

	}).hide()
	.next(".content").css({ marginLeft:0 });

	function episode_open() {
		
		isChange = true;
		Data.isEpisode = true;
		cube_change($sub, $main, { time:0.7, direction:"prev", rotation:90 });
		TweenMax.delayedCall(0.4, episode_change_index);

	}

	function episode_close() {
		
		isChange = true;
		Data.isEpisode = false;
		cube_change($main, $sub, { time:0.7, rotation:90 });
		TweenMax.delayedCall(0.4, episode_change_index);

	}

	function episode_change_index() {

		if (Data.isEpisode) $main.css({ zIndex:1 });
		else $main.css({ zIndex:0 });

	}

	function episode_toggle() {
		
		if (Data.isEpisode) {
			TweenMax.set($main, { autoAlpha:1 });
			TweenMax.set($sub, { autoAlpha:0 });
		} else {
			TweenMax.set($main, { autoAlpha:0 });
			TweenMax.set($sub, { autoAlpha:1 });
		}

	}

	// 챕터 메뉴
	$episodeMenu.on("mouseenter mouseleave click", function(_e) {
		
		_e.preventDefault();
		var index = $episodeMenu.index(this);
		switch(_e.type) {
			case "mouseenter":
				if (!Data.isIntro) EventManager.load(CONTROL_EPISODE_OVER, { index:index });
			break;
			case "mouseleave":
				if (!Data.isIntro) EventManager.load(CONTROL_EPISODE_OUT, { index:index });
			break;
			case "click":
				EventManager.load(CONTROL_EPISODE_CLICK, { index:index });
			break;
		}

	});

	function episode_change_menu() {
		
		$episodeMenu.removeClass("on").eq(Data.episodeNum).addClass("on");
		episode_close();

	}

	// 페이지 메뉴
	$sub.on("click", "ol.page > li", function(_e) {
		
		_e.preventDefault();
		var temp = $sub.find("ol.page > li").index(this);
		if (!Data.isCubeChange && temp != Data.pageNum) {
			Data.pageOld = Data.pageNum;
			Data.pageNum = temp;
			page_change_menu();
			EventManager.load(CONTROL_PAGE_CLICK);
		}

	});

	function page_change_menu() {

		$sub.find("ol.page > li").each(function(_i) {

			if (_i <= Data.pageNum) $(this).addClass("on");
			else $(this).removeClass("on");

		});

	}

	// 오디오 제어
	var $audio = $("audio");
	var $mute = $(".btn_mute");

	$audio.prop({ autoplay:true, loop:true });
	$audio.prop({ src:"mp3/Lake_Louise.mp3" });

	$mute.on("click", function(_e) {

		$audio.prop({ muted:!$audio.prop("muted") });
		if ($audio.prop("muted")) $(this).addClass("on");
		else $(this).removeClass("on");

	});

});