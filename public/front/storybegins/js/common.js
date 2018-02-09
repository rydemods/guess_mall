/******************************
공통 이벤트, 변수, 함수 제어
******************************/

"use strict"

/* 전역 변수 */
var Data = {
	isCubeChange : false, // 큐브 모션 진행 여부
	isIntro : true, // 인트로 노출 여부
	isMain : false, // 메인 노출 여부
	isEpisode : true, // 에피소드 메뉴 노출 여부
	isLayer : false, // 레이어 노출 여부
	episodeOld : null,
	episodeNum : null,
	pageOld : null,
	pageNum : 0,
	bgSpace : 50, // 배경 이미지 이동 거리
	resizeRatio : 1 // 리사이징 요소 비율
}

/* 커스텀 이벤트 */
const CLICK_EVENT = "CLICK_EVENT"; // 이벤트 클릭
const PAGE_RESIZE = "PAGE_RESIZE"; // 브라우저 리사이징
const CUBE_CHANGE_COMPLETE = "CUBE_CHANGE_COMPLETE"; // 큐브 화면전환 완료
const INDEX_LOAD_COMPLETE = "INDEX_LOAD_COMPLETE"; // 이미지 로드 완료
const INDEX_INTRO_OPEN = "INDEX_INTRO_OPEN"; // 인트로 보기
const INDEX_INTRO_CLOSE_COMPLETE = "INDEX_INTRO_CLOSE_COMPLETE"; // 인트로 숨기기 완료
const INDEX_INTRO_SKIP = "INDEX_INTRO_SKIP"; // 인트로 스킵 클릭
const CONTROL_EPISODE_OPEN = "CONTROL_EPISODE_OPEN"; // 에피소드 메뉴 보기
const CONTROL_EPISODE_CLOSE = "CONTROL_EPISODE_CLOSE"; // 에피소드 메뉴 숨기기
const CONTROL_EPISODE_OVER = "CONTROL_EPISODE_OVER"; // 에피소드 메뉴 마우스오버
const CONTROL_EPISODE_OUT = "CONTROL_EPISODE_OUT"; // 에피소드 메뉴 마우스아웃
const CONTROL_EPISODE_CLICK = "CONTROL_EPISODE_CLICK"; // 에피소드 메뉴 클릭
const CONTROL_PAGE_CLICK = "CONTROL_PAGE_CLICK"; // 페이지 메뉴 클릭
const MAIN_EPISODE_CHANGE = "MAIN_EPISODE_CHANGE"; // 에피소드 메뉴 변경
const EPISODE_PAGE_CHANGE = "EPISODE_PAGE_CHANGE"; // 페이지 변경

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

/* 페이지 리사이징 */
$(window).on("resize", function() {
	
	var maxW = 1920 - 1024;
	var currentW = 1920 - $(document).width();
	Data.resizeRatio = currentW / maxW;
	EventManager.load(PAGE_RESIZE);

});

$(window).one("load", function() {
	
	var maxW = 1920 - 1024;
	var currentW = 1920 - $(document).width();
	Data.resizeRatio = currentW / maxW;
	EventManager.load(PAGE_RESIZE);

});

/* 큐브 화면 전환 */
function cube_change(_$prev, _$current, _obj) {
	
	if (arguments.length < 3) {
		//console.log("common.js - cube_change - 매개변수가 부족합니다.");
		return;
	}
	if (Data.isCubeChange) {
		//console.log("common.js - cube_change - 화면 전환이 끝나지 않았습니다.");
		return;
	}
	Data.isCubeChange = true;

	var obj = { time:1, direction:"next", rotation:70 } // 기본 값
	$.extend(obj, _obj);

	if (obj.direction == "next") {
		TweenMax.set(_$prev, { top:0, rotationX:0, transformOrigin:"50% 100%", force3D:true });
		TweenMax.to(_$prev, obj.time, { top:"-100%", rotationX:obj.rotation, ease:Quart.easeInOut });
		TweenMax.set(_$current, { autoAlpha:1, top:"100%", rotationX:-obj.rotation, transformOrigin:"50% 0", force3D:true });
		TweenMax.to(_$current, obj.time, { top:0, rotationX:0, ease:Quart.easeInOut, onComplete:cube_change_complete });
	}
	if (obj.direction == "prev") {
		TweenMax.set(_$prev, { top:0, rotationX:0, transformOrigin:"50% 0", force3D:true });
		TweenMax.to(_$prev, obj.time, { top:"100%", rotationX:-obj.rotation, ease:Quart.easeInOut });
		TweenMax.set(_$current, { autoAlpha:1, top:"-100%", rotationX:obj.rotation, transformOrigin:"50% 100%", force3D:true });
		TweenMax.to(_$current, obj.time, { top:0, rotationX:0, ease:Quart.easeInOut, onComplete:cube_change_complete, onCompleteParams:[_$current] });
	}

}

function cube_change_complete() {
	
	Data.isCubeChange = false;
	EventManager.load(CUBE_CHANGE_COMPLETE);

}

// 레이어 팝업
function layer_open(_selector, _opacity) {
	
	Data.isLayer = true;

	var $layer = $(_selector);
	var $dim = $layer.find(".dim");
	var $box = $layer.find(".box");
	
	$layer.show();
	TweenMax.set($dim, { autoAlpha:0 });
	TweenMax.to($dim, 0.7, { autoAlpha:_opacity, ease:Cubic.easeInOut });
	TweenMax.set($box, { autoAlpha:0, top:"100%" });
	TweenMax.to($box, 0.7, { autoAlpha:1, top:"50%", ease:Back.easeOut });

}

function layer_close(_selector) {
	
	Data.isLayer = false;

	var $layer = $(_selector);
	var $dim = $layer.find(".dim");
	var $box = $layer.find(".box");

	TweenMax.to($dim, 0.3, { autoAlpha:0, ease:Cubic.easeOut });
	TweenMax.to($box, 0.3, { autoAlpha:0, top:"65%", ease:Quart.easeOut, onComplete:layer_close_complete });

	function layer_close_complete() {

		$layer.hide();

	}

}