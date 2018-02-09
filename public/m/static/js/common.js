"use strict"

$(function(e) {

	// 컨텐츠 높이 리셋
	$(window).on("load resize orientationchange", content_resize);
	function content_resize() {
		
		var $content = $("[data-ui=ContentResize]");
		$content.css({ minHeight:$(window).height() - $content.offset().top - parseInt($content.css("paddingTop")) - parseInt($content.css("paddingBottom")) - $("[data-element=footer]").outerHeight() });

	}
	
	// 본문 바로가기
	$("[data-ui=SkipNav]").on("focusin focusout", function(_e) {

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

	// 상단 바로가기
	$(window).on("scroll", function() {
		
		var $top = $("[data-ui=topButton]");
		if ($(this).scrollTop() > $(this).height() * 0.7) {
			if (!$top.is(":visible")) $top.show();
		} else {
			if ($top.is(":visible")) $top.hide();
		}

	});

	// 푸터 더보기
	$("[data-element=footerToggle]").on("click", function(_e) {
		
		var $content = $("[data-element=footerContent]");
		$content.toggleClass("on");
		
		if ($content.hasClass("on")) $(this).addClass("on").text("숨기기");
		else $(this).removeClass("on").text("더보기");

		content_resize();

	});

	// input:text placeholder
	$("[data-ui=Placeholder]").each(function() {
		
		var $this = $(this);
		$this.on("focusin focusout change", function(_e) {

			switch(_e.type) {
				case "focusin":
					$this.addClass("on");
				break;
				case "focusout":
				case "change":
					placeholder_check();
				break;
			}

		});
		placeholder_check();

		function placeholder_check() {
			
			var temp = $.trim($this.val());
			$this.val(temp);
			if (temp.length > 0) $this.addClass("on");
			else $this.removeClass("on");

		}

	});

	// flexible textarea (자동 높이, 기본문장)
	$(document).on("keydown keyup focusin focusout", "[data-ui=FlexibleText]", function(_e) {

		var $ui = $(this);
		if ($ui.data().defaultTxt == undefined) $ui.data().defaultTxt = $ui.val();

		switch(_e.type) {
			case "keydown":
			case "keyup":
				text_resize();
			break;
			case "focusin":
				if ($ui.val() == $ui.data().defaultTxt) $ui.val("");
			break;
			case "focusout":
				var temp = $.trim($ui.val());
				if (temp.length == 0) $ui.val($ui.data().defaultTxt);
				else $ui.val(temp);
				text_resize();
			break;
		}

		text_resize();
		function text_resize() {

			$ui.height(1);
			$ui.height($ui[0].scrollHeight - 20);

		}

	});
	
	// input:file fake
	$(document).on("click change", "[data-ui=FakeFile]", function(_e) {

		var $ui = $(this);
		var $path = $ui.find("[data-element=path]");
		var $button = $ui.find("[data-element=button]");
		var $delete = $ui.find("[data-element=delete]");
		
		switch(_e.type) {
			case "click":
				if ($(_e.target).attr("data-element") == "path") $button.trigger("click");
				if ($(_e.target).parent().attr("data-element") == "delete") {
					$button.val("");
					$path.val($button.val());
					$delete.hide();
				}
			break;
			case "change":
				if ($(_e.target).attr("data-element") == "button") {
					var tempA = $button.val().split("\\");
					$path.val(tempA[tempA.length - 1]);
					$delete.show();
				}
			break;
		}

	});

	// 버튼 클릭 포커싱 삭제
	$("button").on("mouseup", function() {

		//$(this).blur();

	});

	// 수량 변경
	$(document).on("click", "[data-ui=QuantityChange] > [data-element=add], [data-ui=QuantityChange] > [data-element=subtract]", function(_e) {

		var $text = $(this).prevAll("[data-element=text]");
		var value = parseInt($text.val());
		if ($(_e.currentTarget).attr("data-element") == "add" && value < 99) value++;
		if ($(_e.currentTarget).attr("data-element") == "subtract" && value > 1) value--;
		$text.val(value).attr("value", value);

	});

//	$(document).on("keypress keyup focusout", "[data-ui=QuantityChange] > [data-element=text]", function(_e) {
	$(document).on("keyup focusout", "[data-ui=QuantityChange] > [data-element=text]", function(_e) {
		
		var $this = $(this);
		var value = parseInt($this.val());
		switch(_e.type) {
//			case "keypress":
			case "keyup":
				if (value > 99) value = 99;
			break;
			case "focusout":
				if (isNaN(value)) value = 1;
			break;
		}
		$this.val(value).attr("value", value);

	});

	// 드롭다운 메뉴 - 공통 헤딩, SNS공유, 콤보박스 메뉴
	$("[data-ui=DropdownMenu]").each(function() {

		var $ui = $(this);
		var $button = $(this).find("[data-element=button]");
		var $content = $(this).find("[data-element=content]");

		$button.on("click", function(_e) {

			_e.preventDefault();
			if ($ui.hasClass("on")) menu_close();
			else {
				$ui.addClass("on");
				$(this).attr("title", function(_i, _value) {
					
					return _value.replace("펼쳐보기", "숨기기");

				});
			}

		});
		$content.on("click", menu_close);

		$(window).on("mousedown touchstart", function(_e) {
			
			if ($(_e.target).closest("[data-ui=DropdownMenu]")[0] != $ui[0] && $ui.hasClass("on")) menu_close();

		});

		function menu_close() {
			
			$ui.removeClass("on");
			$button.attr("title", function(_i, _value) {
				
				return _value.replace("숨기기", "펼쳐보기");

			});

		}

	});

	// select 직접입력 선택시 input 활성화 - 회원가입(아이디), 주문/취소/반품/교환 신청, 주문/결제, 회원탈퇴
	$("[data-ui=SelectDirectInput]").each(function() {

		var $ui = $(this);
		var $select = $ui.find("[data-element=select]");
		var $input = $ui.find("[data-element=input]");
		
		$select.on("change", function(_e) {

			if ($(this).val() == "0") $input.prop("disabled", false).show();
			else $input.prop("disabled", true).val("").hide();

		}).change();

	});

	// 레이어 - 이벤트 > 퍼펙트하우스, 마이페이지 > 쿠폰내역, 마이페이지 > 포인트내역
	$("[data-ui=LayerSet]").each(function() {

		var $ui = $(this);
		var $open = $ui.find("[data-element=open]");
		var $close = $ui.find("[data-element=close]");
		var $content = $ui.find("[data-element=content]");

		var timer;

		$content.attr("tabIndex", -1).css({ outline:0 })
		.on("focusin focusout", function(_e) {
			
			clearTimeout(timer);
			if (_e.type == "focusout") {
				timer = setTimeout(function() {
					if ($content.is(":visible")) $close.focus();
				}, 1);
			}

		});

		$open.on("click", function(_e) {
		
			_e.preventDefault();
			$content.addClass("on").focus();
			$(window).on("scroll", close);

		});

		$close.on("click", function(_e) {

			_e.preventDefault();
			close();
			$open.focus();

		});

		function close() {

			$content.removeClass("on");
			$(window).off("scroll", close);

		}

	});

});

// 앵커 포커싱 이동
function focus_anchor(_target, _y) {
	
	if (arguments.length == 0) return;
	
	var $target = $(_target);
	$target.attr("tabIndex", -1).css({ outlineWidth:0 }).focus()
	.one("focusout", function() {
		$(this).css({ outlineWidth:"" }).removeAttr("tabIndex");
	});
	var scrolltop = (_y == undefined) ? $target.offset().top - 60 : _y;
	$(window).scrollTop(scrolltop);

}

// 레이어 배너
function layer_banner_open(_id) {
	
	if (_id == "undefined") return;

	var $layerbanner = $("#" + _id);
	var $content = $layerbanner.find(".layer_banner");
	var $img = $layerbanner.find("img.banner");

	$layerbanner.addClass("on")
	.attr("tabIndex", -1)
	.data({ resize:layer_resize, timer:null })
	.on("focusin focusout", function(_e) {

		clearTimeout($(this).data().timer);
		if (_e.type == "focusout") {
			$(this).data().timer = setTimeout(function() {
				if ($layerbanner.is(":visible")) $layerbanner.focus();
			}, 1);
		}

	});

	$(window).on("touchmove", function(_e) {

		_e.preventDefault();

	});

	$(window).on("load resize orientationchange", layer_resize);
	layer_resize();
	
	function layer_resize() {
		
		if ($content.offset().left < $content.offset().top) $img.css({ width:$(window).width() * 0.7, height:"auto" });
		else $img.css({ width:"auto", height:$(window).height() * 0.7 });

	}
	
}

function layer_banner_close(_id, _isCookie) {
	
	if (_id == "undefined") return;

	var $layerbanner = $("#" + _id);

	$(window).off("touchmove");
	$(window).off("load resize orientationchange", $layerbanner.data().resize);
	
	clearTimeout($layerbanner.data().timer);
	$layerbanner.removeClass("on").off("focusin focusout");
	$layerbanner = null;

	if (_isCookie) setCookie(_id, "hide");

}

// 쿠키
function setCookie(_name, _value, _day) {
	
	// _day 값이 있으면 지정한 일(day) 수 기준, 없으면 24시(자정) 기준으로 쿠키 저장
	var date = new Date();
	var addTime = (_day == undefined) ? (24 * 60 * 60) - (date.getHours() * 60 * 60 + date.getMinutes() * 60 + date.getSeconds()) : _day * 24 * 60 * 60;
	date.setTime(date.getTime() + (addTime * 1000));
	document.cookie = _name + "=" + escape(_value) + "; expires=" + date.toUTCString() + "; path=/;";

}

function getCookie(_name) {

	var name = _name + "=";
	var cookieArray = document.cookie.split(";");
	for (var i = 0; i < cookieArray.length; i++) {

		var cookie = cookieArray[i];
		while (cookie.charAt(0)==" ") cookie = cookie.substring(1, cookie.length);
		if (cookie.indexOf(name) == 0) return cookie.substring(name.length, cookie.length);

	}
	return null;

}

function removeCookie(_name) {

	setCookie(_name, null, -1);

}

// visible, hidden
$.fn.visible = function() {

	return this.css("visibility", "visible");

};

$.fn.hidden = function() {

	return this.css("visibility", "hidden");

};

$.fn.toggleVisible = function() {

	return this.css("visibility", function(_index, _value) {
		return (_value == "visible") ? "hidden" : "visible";
	});

};