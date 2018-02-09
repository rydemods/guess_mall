/******************************
메인 챕터 리스트
******************************/

"use strict"

$(function() {

	var $ui = $("article.main");
	var $list = $ui.find("li");

	var listWidth = 0;
	var sensorX = 0; // 좌우 마우스 이동 시작 위치

	EventManager.save($ui, PAGE_RESIZE, event_handler);
	EventManager.save($ui, INDEX_INTRO_OPEN, event_handler);
	EventManager.save($ui, INDEX_INTRO_SKIP, event_handler);
	EventManager.save($ui, CONTROL_EPISODE_OPEN, event_handler);
	EventManager.save($ui, CONTROL_EPISODE_CLOSE, event_handler);
	EventManager.save($ui, CONTROL_EPISODE_OVER, event_handler);
	EventManager.save($ui, CONTROL_EPISODE_OUT, event_handler);
	EventManager.save($ui, CONTROL_EPISODE_CLICK, event_handler);

	function event_handler(_e, _data) {
		//console.log("main", _e.type);
		switch(_e.type) {
			case PAGE_RESIZE:
				main_resize();
			break;
			case INDEX_INTRO_OPEN:
			case CONTROL_EPISODE_CLOSE:
				if (Data.isMain) close();
			break;
			case INDEX_INTRO_SKIP:
			case CONTROL_EPISODE_OPEN:
				if (!Data.isMain) open();
			break;
			case CONTROL_EPISODE_OVER:
				var moveX = ($(window).width() - listWidth) / ($list.length - 1) * _data.index;
				TweenMax.to($ui, 1, { x:moveX, ease:Quad.easeOut });
				list_motion_over($list.eq(_data.index));
			break;
			case CONTROL_EPISODE_OUT:
				list_motion_out($list.eq(_data.index));
			break;
			case CONTROL_EPISODE_CLICK:
				list_change(_data.index);
			break;
		}

	}

	function main_resize() {
		
		if ($(document).width() >= 1920) {
			$list.width(520)
			.find("p").css({ width:260, height:260 })
			.children("span.episode").css({ paddingTop:47, width:80, fontSize:18 })
			.next("strong").css({ paddingTop:31, fontSize:32 })
			.next("span.kor").css({ paddingTop:13, fontSize:17 })
			.next("span.prepare").css({ paddingTop:22, fontSize:15 });
		} else {
			$list.width(520 - Math.round(160 * Data.resizeRatio))
			.find("p").css({ width:260 - Math.round(85 * Data.resizeRatio), height:260 - Math.round(85 * Data.resizeRatio) })
			.children("span.episode").css({ paddingTop:47 - Math.round(17 * Data.resizeRatio), width:80 - Math.round(24 * Data.resizeRatio), fontSize:18 - Math.round(7 * Data.resizeRatio) })
			.next("strong").css({ paddingTop:31 - Math.round(10 * Data.resizeRatio), fontSize:32 - Math.round(10 * Data.resizeRatio) })
			.next("span.kor").css({ paddingTop:13 - Math.round(3 * Data.resizeRatio), fontSize:17 - Math.round(6 * Data.resizeRatio) })
			.next("span.prepare").css({ paddingTop:22 - Math.round(8 * Data.resizeRatio), fontSize:15 - Math.round(5 * Data.resizeRatio) });
		}
		$list.each(function(_i) {
					
			$(this).css({ left:$(this).width() * _i });
			TweenMax.set($(this).find("p"), { background:"rgba(0, 0, 0, 0.5)" });

		});
		listWidth = $list.width() * $list.length;
		sensorX = $list.width() / 5;

		var moveTotal = $ui.width() - listWidth;
		if ($ui.data().x < moveTotal) {
			$ui.data().x = moveTotal;
			TweenMax.set($ui, { x:moveTotal });
		}

	}
	
	function open() {
		
		Data.isMain = true;

		TweenMax.to($ui, 1, { autoAlpha:1, x:0, ease:Cubic.easeOut });
		$list.each(function() {

			TweenMax.set($(this), { rotationY:60 });
			TweenMax.to($(this), 1, { rotationY:0, ease:Cubic.easeOut });

		});
		$ui.on("mousemove", mouse_move_handler);

	}

	function close() {
		
		Data.isMain = false;

		TweenMax.to($ui, 0.7, { autoAlpha:0, ease:Cubic.easeOut });
		$ui.off("mousemove", mouse_move_handler);

	}

	function mouse_move_handler(_e) {
		
		var moveTotal = $(this).width() - listWidth;
		var moveX = (_e.pageX - sensorX) * moveTotal / ($(this).width() - sensorX * 2);
		if (moveX > 0) moveX = 0;
		if (moveX < moveTotal) moveX = moveTotal;
		$ui.data().x = moveX;
		TweenMax.to($ui, 1, { x:moveX, ease:Quad.easeOut });

	}
	
	$list.on("mouseenter mouseleave click", function(_e) {
		
		_e.preventDefault();
		switch(_e.type) {
			case "mouseenter":
				list_motion_over($(this));
			break;
			case "mouseleave":
				list_motion_out($(this));
			break;
			case "click":
				list_change($list.index(this));
			break;
		}

	});

	function list_motion_over(_$selector) {

		TweenMax.to(_$selector.find("p"), 0.5, { background:"rgba(0, 0, 0, 0)", ease:Cubic.easeOut });
		TweenMax.to(_$selector.find(".dim"), 0.5, { autoAlpha:0.6, ease:Cubic.easeOut });
		TweenMax.to(_$selector.find(".border"), 0.5, { autoAlpha:0.4, ease:Cubic.easeOut });

	}

	function list_motion_out(_$selector) {

		TweenMax.to(_$selector.find("p"), 0.5, { background:"rgba(0, 0, 0, 0.5)", ease:Cubic.easeOut });
		TweenMax.to(_$selector.find(".dim"), 0.5, { autoAlpha:0, ease:Cubic.easeOut });
		TweenMax.to(_$selector.find(".border"), 0.5, { autoAlpha:0, ease:Cubic.easeOut });

	}

	function list_change(_index) {
		
		if ($list.eq(_index).find(".prepare").length > 0) {
			alert("준비 중 입니다.");
		} else {
			Data.episodeOld = Data.episodeNum;
			Data.episodeNum = _index;
			/* 페이지 url 변경
			history.pushState(null, "title", "url");
			window.onpopstate=function(e){
				alert(e.state);
			}
			*/
			close();
			EventManager.load(MAIN_EPISODE_CHANGE);
		}

	}

});