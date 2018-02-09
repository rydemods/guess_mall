"use strict"

// 상품 상세 - 상세 내용 탭
$(function() {
	
	var $tab = $(".detailtab");
	var $tabList = $tab.find("> ul");
	var $tagLi = $tabList.find("> li");
	var $content = $("section.section");
	var isFixed = false;
	
	$(window).on("load scroll resize orientationchange", tab_resize);
	tab_resize();
	
	function tab_resize() {
		
		var headerH = $("#header").height();
		if ($(window).scrollTop() < $tab.offset().top - headerH) {
			if (isFixed) {
				isFixed = false;
				$tabList.css({ position:"relative", top:0 });
			}
		} else {
			if (!isFixed) {
				isFixed = true;
				$tabList.css({ position:"fixed", top:headerH });
			}
		}

	}

	$tagLi.on("click", function(_e) {
		
		_e.preventDefault();
		$tagLi.removeClass("on").removeAttr("title");
		$(this).addClass("on").attr("title", "선택됨");
		$content.hide().eq($tagLi.index(this)).show();

	});

});