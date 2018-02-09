
$(document).ready(function(){
// 상단고정
	var nav = $('#header');
	var login_pop = $('.login_pop');
	var findpw_pop = $('.findpw_pop');
	var join_pop = $('.join_pop');
	var review_pop = $('.review_pop');
	var qanda_pop = $('.qanda_pop');
	var body_pop = $('.all_body');
    $(window).scroll(function () {
        if ($(this).scrollTop() >100) {
            nav.addClass("float-nav");
			login_pop.addClass("float-pop ");
			findpw_pop.addClass("float-pop ");
			join_pop.addClass("float-pop ");
			review_pop.addClass("float-pop ");
			qanda_pop.addClass("float-pop ");
			body_pop.addClass("float-pop2 ");
		
        }else {
            nav.removeClass("float-nav");
			login_pop.removeClass("float-pop");
			findpw_pop.removeClass("float-pop");
			join_pop.removeClass("float-pop");
			review_pop.removeClass("float-pop");
			qanda_pop.removeClass("float-pop");
			body_pop.removeClass("float-pop2 ");
        }
    });

// 하단고정

	$('.scrollup').hide();
	$(window).scroll(function(){
   
	if ($(this).scrollTop() > 100) {
			$('.scrollup').fadeIn();
	} else {
			$('.scrollup').fadeOut();
		}
	}); 
    
	$('.scrollup').click(function(){
		$("html, body").animate({ scrollTop: 0 }, 1000);
		return false;
	});


	$(window).on("scroll", function() { 

	var scrollHeight = $(document).height(); 
	var scrollPosition = $(window).height() + $(window).scrollTop(); 
	var scrollSize = $(window).height();

		if ((scrollHeight - scrollPosition) / scrollHeight === 0) { 
			$('.scrollup').addClass("scrollup2");
			$('.lnb_nav').addClass("lnb_fixed");
		 } else {
			 $('.scrollup').removeClass("scrollup2");
			 $('.lnb_nav').removeClass("lnb_fixed");
		} 
	}); 

//
	var windowWidth = $( window ).width();
	var windowHeight = $( window ).height();
	var iheight=505;
	if(iheight <= windowHeight)
	{
		$("#lnb_nav").removeClass("lnb_fixed_top");
		$(".contents").removeClass("contents_top");
	}else{
		$("#lnb_nav").addClass("lnb_fixed_top");
		$(".contents").addClass("contents_top");
	}

	$( window ).resize(function() {

		windowWidth = $( window ).width();
		windowHeight = $( window ).height();
		if(iheight <= windowHeight)
		{
			$("#lnb_nav").removeClass("lnb_fixed_top");
			$(".contents").removeClass("contents_top");
		}else{
			$("#lnb_nav").addClass("lnb_fixed_top");
			$(".contents").addClass("contents_top");
		}
	});



//tab
	$(".tab_c").hide(); 
	$("ul.tab li:first").addClass("active").show(); 
	$(".tab_c:first").show(); 

	$("ul.tab li").click(function() {
		$("ul.tab li").removeClass("active"); 
		$(this).addClass("active");  
		$(".tab_c").hide(); 
		var activeTab = $(this).find("a").attr("href");
		$(activeTab).show(); 
		return false;
	});
//wing tab
	$(".top_con").hide(); 
	$("ul.member_tab li:first").addClass("active").show(); 
	$(".top_con:first").show(); 

	$("ul.member_tab li").click(function() {
		$("ul.member_tab li").removeClass("active"); 
		$(this).addClass("active");  
		$(".top_con").hide(); 
		var activeTab = $(this).find("a").attr("href");
		//장바구니 ajax 추가 2015 11 06 유동혁
		
		if( $(this).find("a").text() == '장바구니' && $('#tab_2 .con_size2').length > 0){
			$.post(
				'../main/ajax_basketpop.php',
				function( data ){
					if( data.length > 0 ){
						$('#tab_2 .con_size2').html( data );
					}
				}
			);
		}
		$(activeTab).show(); 
		return false;
	});

//popup
	$(".pop_info").hide(); 
	$(".btn_member_more").click(function() {
		$(".pop_info").fadeIn(); 
	});
	$(".btn_popclose").click(function() {
		$(".pop_info").hide(); 
	});
	$(".CLS_popclose").click(function() {
		$(".pop_info").hide(); 
	});

//list align
	$("ul.align_cate li").click(function() {
		$("ul.align_cate li").removeClass("align_on"); 
		$(this).addClass("align_on");  
	return false;
	});
//hover 

/*$( "ul.goods_list li " ).hover(

  function() {

    $( this ).append( $( "<span> ***</span>" ) );

  }, function() {

    $( this ).find( "span:last" ).remove();

  }

);

$( "ul.product_list li " ).hover(

  function() {

    $( this ).append( $( "<span> ***</span>" ) );

  }, function() {

    $( this ).find( "span:last" ).remove();

  }

);*/

// 마우스 우클릭 방지 2015 11 30 유동혁
	$(window.document).on("contextmenu", function(event){
		return false;
	});

});

var openPopup	="";

// modal login
function pushLogin(){
		if (openPopup) {
			$("."+openPopup).css("display","none");
			$("."+openPopup+" > iframe").attr("src", "../blank.php");
		}
		var src	= $(".login_pop > iframe").attr("alt");
		$(".login_pop > iframe").attr("src", src);
		var $width=parseInt($(".login_pop").css("width"));
	//	var $height=parseInt($(".login_pop").css("height"));
		var left=($(window).width()-$width)/2;
		var sctop=$(window).scrollTop()*2;
	//	var top=($(window).height()-$height+sctop)/2;
		var height=document.getElementsByTagName("body")[0].scrollHeight;
		$(".login_pop").css("left",left);
	//	$(".login_pop").css("top",top);
		$(".join_pop").css("display","none");
		$(".login_pop").css("display","block");
		$(".login_pop").css("z-index","500");
		$(".all_body").css("display","block");
		$(".all_body").css("width",$(window).width());
		$(".all_body").css("height",height);
		openPopup	="login_pop";

	}
	function loginClose(popup,backDiv){
		$("."+backDiv).css("display","none");
		$("."+popup).css("display","none");
		$("."+popup+" > iframe").attr("src", "../blank.php");
		openPopup	="";
	}

// modal join

function pushJoin(){
		if (openPopup) {
			$("."+openPopup).css("display","none");
			$("."+openPopup+" > iframe").attr("src", "../blank.php");
		}
		var src	= $(".join_pop > iframe").attr("alt");
		$(".join_pop > iframe").attr("src", src);
		var $width=parseInt($(".join_pop").css("width"));
//		var $height=parseInt($(".join_pop").css("height"));
		var left=($(window).width()-$width)/2;
		var sctop=$(window).scrollTop()*2;
//		var top=($(window).height()-$height+sctop)/2;
		var height=document.getElementsByTagName("body")[0].scrollHeight;
		$(".join_pop").css("left",left);
//		$(".join_pop").css("top",top);
		$(".join_pop").css("display","block");
		$(".join_pop").css("z-index","500");
		$(".all_body").css("display","block");
		$(".all_body").css("width",$(window).width());
		$(".all_body").css("height",height);
		openPopup	="join_pop";

	}
	function joinClose(popup,backDiv){
		$("."+backDiv).css("display","none");
		$("."+popup).css("display","none");
		$("."+popup+" > iframe").attr("src", "../blank.php");
		openPopup	="";
	}

// find pw
function pushFind(){
		if (openPopup) {
			$("."+openPopup).css("display","none");
			$("."+openPopup+" > iframe").attr("src", "../blank.php");
		}
		var src	= $(".findpw_pop > iframe").attr("alt");
		$(".findpw_pop > iframe").attr("src", src);
		var $width=parseInt($(".findpw_pop").css("width"));
//		var $height=parseInt($(".findpw_pop").css("height"));
		var left=($(window).width()-$width)/2;
		var sctop=$(window).scrollTop()*2;
//		var top=($(window).height()-$height+sctop)/2;
		var height=document.getElementsByTagName("body")[0].scrollHeight;
		$(".findpw_pop").css("left",left);
//		$(".findpw_pop").css("top",top);
		$(".login_pop").css("display","none");
		$(".findpw_pop").css("display","block");
		$(".findpw_pop").css("z-index","500");
		$(".all_body").css("display","block");
		$(".all_body").css("width",$(window).width());
		$(".all_body").css("height",height);
		openPopup	="findpw_pop";

	}
	function findClose(popup,backDiv){
		$("."+backDiv).css("display","none");
		$("."+popup).css("display","none");
		$("."+popup+" > iframe").attr("src", "../blank.php");
		openPopup	="";
	}
	
// qna
function pushQanda(){
		var $width=parseInt($(".qanda_pop").css("width"));
		var left=($(window).width()-$width)/2;
		var sctop=$(window).scrollTop()*2;
		var height=document.getElementsByTagName("body")[0].scrollHeight;
		$(".qanda_pop").css("left",left);
		$(".qanda_pop").css("display","block");
		$(".qanda_pop").css("z-index","500");
		$(".all_body").css("display","block");
		$(".all_body").css("width",$(window).width());
		$(".all_body").css("height",height);

	}
	function qandaClose(popup,backDiv){
		$("."+backDiv).css("display","none");
		$("."+popup).css("display","none");
		$("."+popup).find("#qna_mobile").val('');
		$("."+popup).find("#qna_title").val('');
		$("."+popup).find("#qna_content").val('');
		$("."+popup).find("#secret").attr("checked",false); 
	}
	
// review
function pushReview(){
		var $width=parseInt($(".review_pop").css("width"));
		var left=($(window).width()-$width)/2;
		var sctop=$(window).scrollTop()*2;
		var height=document.getElementsByTagName("body")[0].scrollHeight;
		$(".review_pop").css("left",left);
		$(".review_pop").css("display","block");
		$(".review_pop").css("z-index","500");
		$(".all_body").css("display","block");
		$(".all_body").css("width",$(window).width());
		$(".all_body").css("height",height);

	}
	function reviewClose(popup,backDiv){
		$("."+backDiv).css("display","none");
		$("."+popup).css("display","none");
		$("."+popup).find("#rmarks option:eq(0)").attr('selected','selected'); 
		$("."+popup).find("#rsubject").val('');
		$("."+popup).find("#rcontent").val('');	
		$('#review_num').val('');
		$('#review_id').val('');
	}

// review modify
	function reviewModify_pop(){
		
		var $width=parseInt($(".revie_modify_pop").css("width"));
		var left=($(window).width()-$width)/2;
		var sctop=$(window).scrollTop()*2;
		var top = ( ( ( $(window).height() - $(".revie_modify_pop").outerHeight() ) / 2 ) + $(window).scrollTop() ) + 'px';
		var height=document.getElementsByTagName("body")[0].scrollHeight;
		$(".revie_modify_pop").css("left",left);
		$(".revie_modify_pop").css("top",top);
		$(".revie_modify_pop").css("display","block");
		$(".revie_modify_pop").css("z-index","500");
		$(".all_body").css("display","block");
		$(".all_body").css("width",$(window).width());
		$(".all_body").css("height",height);
	}