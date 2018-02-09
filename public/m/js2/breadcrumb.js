"use strict"
// 브레드 크럼 (카테고리 네비게이션)
$(function() {
	var $breadcrumb = $(".breadcrumb");
	var $one = $(".breadcrumb").find(".onebox");
	var $two = $(".breadcrumb").find(".twobox");
	if ($(".breadcrumb").find(".onebox").length == 1) $(".breadcrumb").find(".onebox").closest("ul").addClass("solo");
	else if ($(".breadcrumb").find(".onebox").length == 2) $(".breadcrumb").find(".onebox").closest("ul").addClass("duo");
	$(document).on("click",".breadcrumb .onebox a", function(_e){
		_e.preventDefault();
		var isThis = ($(this).closest("li").hasClass("on")) ? true : false;
		$(".breadcrumb").find(".onebox").attr("title", "").parent().removeClass("on");
		if (!isThis) $(this).parent().attr("title", "").parent().addClass("on");
	});
});