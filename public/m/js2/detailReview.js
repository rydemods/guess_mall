"use strict"

// ��ǰ �� - ��ǰ�ı�
$(function() {

	var $review = $("section.review");
	
	$review.on("click", "a.title", function(_e) {

		_e.preventDefault();
		var isThis = ($(this).hasClass("on")) ? true : false;

		$review.find("ul.review > li").removeClass("on")
		.find("> a.title").attr("title", "���ĺ���");
		
		if (!isThis) {
			$(this).attr("title", "�����")
			.parent().addClass("on");
		}

	});

});