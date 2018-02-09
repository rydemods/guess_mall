/******************************
인덱스 프리로딩, 페이지 분기, 인트로
******************************/

"use strict"

$(function() {

	var $loading = $("p.loading");
	var $data = $("[data-image]");
	
	// 프리 로딩
	var loadCount = 0;
	$data.each(function() {
		
		var $this = $(this);
		var img = new Image();

		img.addEventListener("load", event_handler);
		img.addEventListener("error", event_handler);
		img.src = $this.attr("data-image");

		function event_handler(_e) {
			
			switch(_e.type) {
				case "load":
					$this.css({ backgroundImage:"url('" + img.src + "')" })
					.removeAttr("data-image");
				break;
				case "error":
					//console.log("index_ui.js - 이미지 경로가 없거나 잘못되었습니다. imgURL : " + this.src);
				break;
			}
			img.removeEventListener("load", event_handler);
			img.removeEventListener("error", event_handler);
			img = null;
			
			loadCount++;
			$loading.children("span").text("LOADING " + Math.floor(loadCount / $data.length * 100) + "%");
			if (loadCount == $data.length) {
				TweenMax.to($loading, 1, { autoAlpha:0, delay:1, ease:Quart.easeOut });
				EventManager.load(INDEX_LOAD_COMPLETE);

				// 분기
				//window.onhashchange = hash_change;
				var hash = location.hash;
				if ($(hash).length > 0) {
					// 서브 페이지
					//intro_close();
					//Data.episodeNum = 
				} else {
					// 인트로
					//intro_open();
				}
				intro_open();
				intro_motion_play();
			}

		}

	});
	
	// 인트로
	var $logo = $("header");
	var $intro = $("article.intro");
	var $introSkip = $intro.find(".btn_skip");

	EventManager.save($intro, PAGE_RESIZE, event_handler);
	EventManager.save($intro, MAIN_EPISODE_CHANGE, event_handler);

	function event_handler(_e) {
		//console.log("intro", _e.type);
		switch(_e.type) {
			case PAGE_RESIZE:
				index_resize();
			break;
			case MAIN_EPISODE_CHANGE:
				intro_close();
			break;
		}

	}

	function index_resize() {
		
		var $content = $intro.find(".content");
		if ($(document).width() >= 1920) {
			$content.find("h2").css({ fontSize:55 })
			.children("span").css({ paddingTop:13, fontSize:30 });
			$content.find(".eng").css({ marginTop:75, marginBottom:50 })
			.children("h3").css({ fontSize:28 })
			.next("p").css({ marginTop:11, fontSize:18 });
			$content.find(".kor").css({ marginTop:50 })
			.children("h3").css({ fontSize:20 })
			.next("p").css({ marginTop:15, fontSize:14 });
		} else {
			$content.find("h2").css({ fontSize:55 - Math.round(11 * Data.resizeRatio) })
			.children("span").css({ paddingTop:13 - Math.round(2 * Data.resizeRatio), fontSize:30 - Math.round(6 * Data.resizeRatio) });
			$content.find(".eng").css({ marginTop:75 - Math.round(15 * Data.resizeRatio), marginBottom:50 - Math.round(10 * Data.resizeRatio) })
			.children("h3").css({ fontSize:28 - Math.round(6 * Data.resizeRatio) })
			.next("p").css({ marginTop:11 - Math.round(2 * Data.resizeRatio), fontSize:18 - Math.round(4 * Data.resizeRatio) });
			$content.find(".kor").css({ marginTop:50 - Math.round(10 * Data.resizeRatio) })
			.children("h3").css({ fontSize:20 - Math.round(4 * Data.resizeRatio) })
			.next("p").css({ marginTop:15 - Math.round(3 * Data.resizeRatio), fontSize:14 - Math.round(3 * Data.resizeRatio) });
		}
		$intro.find(".bg").css({ width:$intro.width() + Data.bgSpace, left:-Data.bgSpace });

	}

	function intro_open() {
		
		Data.isIntro = true;
		TweenMax.to($intro, 1, { autoAlpha:1, ease:Cubic.easeOut });

	}

	function intro_close() {
		
		TweenMax.to($intro, 1, { autoAlpha:0, ease:Cubic.easeOut, onComplete:intro_close_complete });

	}

	function intro_close_complete() {

		Data.isIntro = false;
		EventManager.load(INDEX_INTRO_CLOSE_COMPLETE);

	}

	function intro_motion_play() {

		$intro.on("mousemove", function(_e) {

			var $bg = $(this).find(".bg");
			var targetX = Data.bgSpace - Math.floor(_e.pageX * Data.bgSpace / $intro.width());
			TweenMax.to($bg, 3, { x:targetX, ease:Quad.easeOut });

		})
		.find("[data-animation]").each(function(_i) {

			TweenMax.set($(this), { autoAlpha:0, y:30 });
			TweenMax.to($(this), 1.5, { autoAlpha:1, y:0, delay:_i * 0.9 + 1.5, ease:Quad.easeOut, overwrite:1 });

		});

	}

	function intro_motion_stop() {

		$intro.off("mousemove");
		TweenMax.killTweensOf($intro.find("[data-animation]"));

	}

	$logo.on("click", function(_e) {

		_e.preventDefault();
		if (!Data.isIntro || Data.isMain) intro_motion_play();
		if (!Data.isIntro) intro_open();

		EventManager.load(INDEX_INTRO_OPEN);

	});

	$introSkip.on("click", function(_e) {

		_e.preventDefault();
		intro_motion_stop();
		EventManager.load(INDEX_INTRO_SKIP);

	});

});