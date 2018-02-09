  $(document).ready(function(){
   $("div.q_sub1,div.q_sub2,div.q_sub3,div.q_sub4,div.q_sub5,div.q_sub6,div.q_sub7,div.q_sub8,div.q_sub9").hide();

	var form_name = "ul.quick_nav li.nav_main_menu";

	$(form_name).hover(function() {
		btn_idx = $(form_name).index(this)+1;
		$("div.q_sub"+btn_idx).fadeIn();
		$(".nav"+btn_idx).addClass("nav"+btn_idx+"_on");
		$("ul.quick_nav").addClass("bg_change");
		$(form_name).find("div.wing_banner a:first").show();
	}, function() {
		$("div.q_sub"+btn_idx).fadeOut();
		$(".nav"+btn_idx).removeClass("nav"+btn_idx+"_on");
		$("ul.quick_nav").removeClass("bg_change");
		$(form_name).find("div.wing_banner a").hide();
	});

	var form_name2 = "ul.quick_nav li.nav_main_menu a.codeLink";
	$(form_name2).hover(function() {
		var cateCode	= $(this).attr("alt");
		wing_banner_change(cateCode);
	}, function() {
	});


});

var nowCode	="";

function wing_banner_change(cateCode) {
		$("ul.quick_nav li.nav_main_menu").find("div.wing_banner a").hide();
		if ($("#cate_"+cateCode).length > 0)
		{
			$("#cate_"+cateCode).show();
		}
		nowCode	= cateCode;
}
