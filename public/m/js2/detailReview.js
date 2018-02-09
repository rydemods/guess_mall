"use strict"

// 상품 상세 - 상품후기
$(function() {

	var $review = $("section.review");
	
	$review.on("click", "a.title", function(_e) {

		_e.preventDefault();
		var isThis = ($(this).hasClass("on")) ? true : false;

		$review.find("ul.review > li").removeClass("on")
		.find("> a.title").attr("title", "펼쳐보기");
		
		if (!isThis) {
			$(this).attr("title", "숨기기")
			.parent().addClass("on");
		}

	});

});