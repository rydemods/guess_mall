// JavaScript Document

//alert('브라우저를 최신 버전으로 업데이트 하세요');
 
$(document).ready(function(){ 
if(/msie|MSIE 6/.test(navigator.userAgent)){ $("body").addClass("ie6");} 
if(/msie|MSIE 7/.test(navigator.userAgent)){ $("body").addClass("ie7"); } 
if(/msie|MSIE 8/.test(navigator.userAgent)){ $("body").addClass("ie8"); } 
}); 

//
$(document).ready(function() { 

	$(".gnb_wrap a.all" ).click(function() {$("div.total_pannel").stop().animate({'top':'118px', 'height':'463px', 'padding':'0px 0px 50px 0px'},{complete:function(){$(".total_pannel").show('fast');}});});
	$(".total_pannel_in p.btn_close, .wrap_cont" ).click(function() {$("div.total_pannel").stop().animate({'top':'118px', 'height':'463px'},{complete:function(){$(".total_pannel").hide('fast');}});});
	/*
	$(".control_btn a.control_open" ).click(function() {$("div.open_wrap").stop().animate({'right':'+=118px', 'width':'104px','padding':'0px 0px 0px 0px'},{complete:function(){$(".open_wrap").show('slow');}});});
	$(".control_btn a.control_clos" ).click(function() {$("div.open_wrap").stop().animate({'right':'-=118px', 'width':'104px', 'padding':'0px 0px 0px 0px'},{complete:function(){$(".open_wrap").hide('slow');}});});
	*/
	
	$(".control_btn a.control_open" ).click(function() {
		$( ".skyscraper" ).animate({
			width: "104px"
		}, 500, function() {
			localStorage.setItem("rightopen","1");

		});
	});

	$(".control_btn a.control_clos" ).click(function() {
		$( ".skyscraper" ).animate({
			width: "0px"
		}, 500, function() {
			localStorage.setItem("rightopen","0");
		});
	});
	
	$(".hideAnimate").click(function(){
		alert($(".open_wrap").css('display'));
	})

	$(".btn_area a.pre" ).click(function() {$("#updownscroll").stop().animate({scrollTop:"-=80px"},{complete:function(){$("#updownscroll").show('slow');}});});
	$(".btn_area a.next" ).click(function() {$("#updownscroll").stop().animate({scrollTop:"+=80px"},{complete:function(){$("#updownscroll").show('slow');}});});
	
});

//
