"use strict"

// ��ǰ �� - �ϴ� ���� �ɼ�
function detail_bottom_toggle() {

	var $bottom = $("#goods_bottom.detailoption");
	var $option = $bottom.find("> .optionwrap");
	var $arrow = $option.find("> a.btn_arrow");
	//$("a.btn_arrow").show();
	$bottom.find("a.btn_arrow").css("display","block");
	if ($option.hasClass("on")) {
		$option.removeClass("on");
		$arrow.attr("title", "���ĺ���");
		$(".optionwrap").css("height",'');
	} else {
		$option.addClass("on");
		//$option.css("height","165");
		if($("#option2").length > 0){
			$(".optionwrap").css("height",171+(($(".opt_list").find("li").length * 70)));
		}else{
			$(".optionwrap").css("height",119+(($(".opt_list").find("li").length * 70)));
		}
		$arrow.attr("title", "�����");
	}

}