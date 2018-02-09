"use strict"

$(function() {
	
	// ���� �ٷΰ���
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

	// ���� ��/�ϴ� ���� ���� (���/Ǫ�� ����)
	$(".mainpage.rolling").css({ paddingTop:"84px"});
	$(".subpage").css({ paddingTop:"84px"});
	$(".subpage.goodsList").css({ paddingTop:"119px"});
	//$("#content").css({ paddingTop:$("#header").height() });
	$("#footer").css({ marginBottom:$("#bottom").height() });

	// ī�װ�
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
				.children("a").attr("title", "���� �޴� ���ĺ���")
				.next("ul").slideUp("fast");
				
				if (!isThis) {
					$(this).attr("title", "���� �޴� �����")
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

// �ٷΰ��� ��Ŀ
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

// ��ǰ ���� ����
function product_view(_this, _type) {
	
	var $this = $(_this);
	$this.parent().children("a").removeClass("on").removeAttr("title");
	$this.addClass("on").attr("title", "���õ�");
	
	var $product = $(".productwrap");
	if (_type == "thumb") $product.addClass("thumb");
	else $product.removeClass("thumb");

}

// ���̾� �˾�
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

//��ǰ,��ȯ �ܰ� ����ݱ�
$(function(){
	$('section.my_step a.detail_btn').click(function(){
	$('section.my_step div.reques_info_detail').show();
	});
	$('section.my_step div.reques_info_detail a.detail_close').click(function(){
	$('section.my_step div.reques_info_detail').hide();
	});
});

function jsSetComa(str_result){
 var reg = /(^[+-]?\d+)(\d{3})/;   // ���Խ�
 str_result += '';  // ���ڸ� ���ڿ��� ��ȯ
 while (reg.test(str_result)){
  str_result = str_result.replace(reg, '$1' + ',' + '$2');
 }
 
 return str_result;
}
