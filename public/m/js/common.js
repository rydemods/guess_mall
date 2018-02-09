"use strict"

$(function() {
	
	// 본문 바로가기
	$(".btn_skip").on("focusin focusout", function(_e) {

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

	// 내용 상/하단 여백 설정 (헤더/푸터 공백)
	$("#footer").css({ marginBottom:$("#bottom").height() });

	// 카테고리
	var $cateOpen = $("#header .catewrap > a.btn_open");
	var $cate = $("#category");
	var $cateCover = $cate.find("> .cover");
	var $cateBox = $cate.find("> .catebox");
	var $cateMenuOne = $cateBox.find("> nav.menu > ul > li");

	var cateTimer;

	$cateOpen.on("click", function(_e) {

		_e.preventDefault();
		$(window).scrollTop(0);
		$("body").css({ position:"fixed", width:"100%", height:"100%" });

		$cate.show().attr("tabIndex", 0).focus()
		.on("focusin focusout", function(_e) {
			
			switch(_e.type) {
				case "focusin" :
					clearTimeout(cateTimer);
				break;
				case "focusout" :
					cateTimer = setTimeout(function() {
						$cate.focus()
					}, 100);
				break;
			}

		});

		TweenMax.to($cateCover, 0.3, { autoAlpha:0.7, ease:Cubic.easeOut });
		TweenMax.to($cateBox, 0.4, { right:0, ease:Cubic.easeInOut, overwrite:1 });
		
	});

	$cate.find(".cover, a.btn_close").on("click", function(_e) {

		_e.preventDefault();
		cate_close();

	});
	
	$cateMenuOne.each(function() {
		
		var $a = $(this).children("a");
		if ($(this).find("ul").length == 0) {
			if (!$(this).hasClass("link")) $(this).addClass("link");
			$a.removeAttr("title");
		} else {
			$a.on("click", function(_e) {

				_e.preventDefault();
				var isThis = ($(this).parent().hasClass("on")) ? true : false;

				$cateMenuOne.not(".link").removeClass("on")
				.children("a").attr("title", "하위 메뉴 펼쳐보기")
				.next("ul").slideUp("fast");
				
				if (!isThis) {
					$(this).attr("title", "하위 메뉴 숨기기")
					.next("ul").slideDown("fast")
					.parent().addClass("on");
				}

			});
		}

	});

	function cate_close() {
		
		$("body").css({ position:"static", width:"auto", height:"auto" });
		$cate.removeAttr("tabIndex").off("focusin focusout");
		$cateOpen.focus();

		TweenMax.to($cateCover, 0.2, { autoAlpha:0, ease:Cubic.easeOut });
		TweenMax.to($cateBox, 0.3, { right:"-100%", ease:Cubic.easeOut, onComplete:cate_close_complete });

	}

	function cate_close_complete() {

		$cate.hide();

	}

});

// 바로가기 앵커
function focus_anchor(_target, _y) {
	
	var $target = $(_target);
	if ($target.length > 0) {
		$target.attr("tabIndex", 0).focus().on("focusout", function(_e) {

			$target.removeAttr("tabIndex").off("focusout");

		});
		var scrollY = (_y == undefined) ? $target.offset().top - $(window).height() * 0.3 : _y;
		$(window).scrollTop(scrollY);
	}

}

// 상품 보기 정렬
function product_view(_this, _type) {
	
	var $this = $(_this);
	$this.parent().children("a").removeClass("on").removeAttr("title");
	$this.addClass("on").attr("title", "선택됨");
	
	var $product = $(".productwrap");
	if (_type == "thumb") $product.addClass("thumb");
	else $product.removeClass("thumb");

}

// 레이어 팝업
function layer_open(_this, _layer) {

	var $button = $(_this);
	var $layer = $(_layer);
	var timer;
	
	$(window).scrollTop(0);
	$("body").css({ position:"fixed", width:"100%", height:"100%" });

	$layer.show().attr("tabIndex", 0).focus().on("focusin focusout", function(_e) {
		
		clearTimeout(timer);
		switch(_e.type) {
			case "focusout":
				timer = setTimeout(function() {
			
					$layer.focus();

				}, 1);
			break;
		}

	});

	$layer.find(".btn_hide").on("click", function(_e) {

		_e.preventDefault();
		
		$("body").css({ position:"static", width:"auto", height:"auto" });
		$layer.removeAttr("tabIndex").off("focusin focusout").hide();
		$button.focus();

	});

}

//반품,교환 단계 열고닫기
$(function(){
	$('section.my_step a.detail_btn').click(function(){
	$('section.my_step div.reques_info_detail').show();
	});
	$('section.my_step div.reques_info_detail a.detail_close').click(function(){
	$('section.my_step div.reques_info_detail').hide();
	});
});

function jsSetComa(str_result){
 var reg = /(^[+-]?\d+)(\d{3})/;   // 정규식
 str_result += '';  // 숫자를 문자열로 변환
 while (reg.test(str_result)){
  str_result = str_result.replace(reg, '$1' + ',' + '$2');
 }
 
 return str_result;
}

function trim(str){
	str = str.replace(/(^\s*)|(\s*$)/gi, "");	
	return str;
}

// 카테고리 선택시 해당 카테고리 상품리스트로 가는 스크립트. (2015.12.09 - 김재수)
function goCateProduct(form, c_code, n_code) {
	if (c_code != 'none') {
		location.href = "productlist.php?code="+c_code;
	} else {
		form.value	= n_code;
	}
}