$(window).ready(function(){
	//========== 컴포넌트 : Start ==========//
	//레이어팝업 오픈
	$('.btn-layer-open').click(function(){
		$('.layer-class').fadeIn();
	});

	//레이어팝업
	var layerWidth = $('.layer-dimm-wrap .layer-inner').width();
	var resizeLayerPop = $('.layer-dimm-wrap .layer-inner');
	var layerPopClose = $('.layer-dimm-wrap .btn-close, .layer-dimm-wrap .dimm-bg, .btn_layer_close');
	var layerPopWrap = $('.layer-dimm-wrap');
	
	layerPopWrap.hide();

	//레이어팝업 공통 닫기
	layerPopClose.click(function(){
		layerPopWrap.fadeOut();
	});

	//========== 컴포넌트 : End ==========//
	
	//========== 팝업 오픈 : Start ==========//
	$('.btn-search-pop').click(function(){
		$('.pop-detail-search').fadeIn();
		return false;
	});

	$('.btn-sns_share').click(function(){
		$('.pop-sns_share').fadeIn();
		return false;
	});

	$('.btn-brand-search').click(function(){
		$('.pop-brand-search').fadeIn();
		return false;
	});

	$('.btn-sorting-search').click(function(){
		$('.pop-sorting-search').fadeIn();
		return false;
	});

	$('.btn-agreement').click(function(){
		$('.pop-agreement').fadeIn();
		return false;
	});

	$('.btn-privacy').click(function(){
		$('.pop-privacy').fadeIn();
		return false;
	});

	$('.btn-no-mail').click(function(){
		$('.pop-no-mail').fadeIn();
		return false;
	});
	
	$('.loading-show').click(function(){
		$('.dimm-loading').fadeIn();
	});

//	$('.btn-related').click(function(){
//		$('.pop-related').fadeIn();
//		return false;
//	});

	$('.btn-store-write').click(function(){
		$('.pop-store-write').fadeIn();
		return false;
	});
	$('.btn_exchange').click(function(){
		$('.layer-dimm-wrap.layer_exchange').fadeIn();
	});

	$('.btn_refund').click(function(){
		$('.layer-dimm-wrap.layer_refund').fadeIn();
	});

	$('.btn_cash_receipt').click(function(){
		$('.layer-dimm-wrap.layer_cash_receipt').fadeIn();
	});

	$('.btn_tax_invoice').click(function(){
		$('.layer-dimm-wrap.layer_tax_invoice').fadeIn();
	});

	$('#btn_shipping_list').click(function(){
		$('.layer-dimm-wrap.layer_shipping_list').fadeIn();
	});

	/*$('.btn_coupon_use').click(function(){
		$('.layer-dimm-wrap.layer_coupon_use').fadeIn();
	});*/

	$('.term01 .term_view').click(function(){
		$('.layer-dimm-wrap.layer_term_use01').fadeIn();
	});

	$('.term02 .term_view').click(function(){
		$('.layer-dimm-wrap.layer_term_use02').fadeIn();
	});

	$('.term03 .term_view').click(function(){
		$('.layer-dimm-wrap.layer_term_use03').fadeIn();
	});

	$('.term04 .term_view').click(function(){
		$('.layer-dimm-wrap.layer_term_use04').fadeIn();
	});

	$('.term05 .term_view').click(function(){
		$('.layer-dimm-wrap.layer_term_use05').fadeIn();
	});

	$('.btn_contact_store').click(function(){
		$('.layer-dimm-wrap.layer_contact_store').fadeIn();

        // 창 뜰때 초기화하기 2016-09-03 jhjeong
        $('#storeLocWriteForm').each(function(){
             this.reset();
             $('#storeLocWriteForm').find('.upload-name').html('');
        });
	});

	$('.btn_photo_submit').click(function(){
		$('.layer-dimm-wrap.layer_photo_submit').fadeIn();
	});

	$('.btn-pop-address').click(function(){
		$('.layer-dimm-wrap.pop-address-add').fadeIn();
	});

	$('.btn_forum_submit').click(function(){
		$('.layer-dimm-wrap.layer_forum_submit').fadeIn();
	});

	$('.btn_forum_apply').click(function(){
		$('.layer-dimm-wrap.layer_forum_apply').fadeIn();
	});

	$('.btn_brand_video').click(function(){
		$('.layer-dimm-wrap.layer_brand_video').fadeIn();
	});

	//========== 팝업 오픈 : End ==========//
	
	
    //컴포넌트 > 탭메뉴
	var tabOn = $('.idx-menu');
	var tabOn_content = $('.idx-content');

	tabOn.on('click',function(e){
		var idx = tabOn.index($(this));

		tabOn.removeClass('on');
		tabOn_content.removeClass('on');

		tabOn.eq(idx).addClass('on');
		tabOn_content.eq(idx).addClass('on');
	});

	
    //메인 > 탭메뉴
    var main_tabOn = $('.main-community-tab a');
    var main_tabOn_content = $('.main-community-content');

    main_tabOn.on('click',function(e){
        var idx = main_tabOn.index($(this));

        main_tabOn.removeClass('on');
        main_tabOn_content.removeClass('on');

        main_tabOn.eq(idx).addClass('on');
        main_tabOn_content.eq(idx).addClass('on');

        //community['community'+($(this).index())].masonry();
    });


    // lnb 컨트롤
	var top;
    $('body').css('overflow','visible');
    $('body').css('position','static');

    $('.btn_lnb_open').on('click', function(){
        $('header nav').stop().animate({left: "0"}, 500);
        $('header .dimmed').stop().delay(450).animate({opacity: "1"}, 100);
        top = $(window).scrollTop();
        $('body').css('overflow','hidden');
        $('body').css('position','fixed');
        $('#contents').css({top:top*-1, position:'relative'});
    });

    $('.btn_lnb_close, .dimmed').on('click', function(){
        $('header .dimmed').stop().animate({opacity: "0"}, 100);
        $('header nav').stop().delay(100).animate({left: "-100%"}, 500);
        $('body').css('overflow','visible');
        $('body').css('position','static');
        $('#contents').css({top:0, position:'relative'});
        $(window).scrollTop(top);
    });
	
	//마이페이지 > 상품리뷰 > 작성완료
	$('.review_read .title').click(function(){
		$(this).parent('.review_read').toggleClass('on');
	});

	//파일첨부 custom
	var fileTarget = $('.upload_file .upload-hidden');

	fileTarget.on('change', function(){  // 값이 변경되면
		if(window.FileReader){  // modern browser
			var filename = $(this)[0].files[0].name;
		} else {  // old IE
			var filename = $(this).val().split('/').pop().split('\\').pop();  // 파일명만 추출
		}
		// 추출한 파일명 삽입
		$(this).siblings('.upload-name').show().val(filename);
	});

	//맨위로
	var btnTop = $('.quick_btn_wrap .top_btn');
	btnTop.click(function() {
		$('html, body').animate({scrollTop: 0}, 500);
	});

	//추천포럼 슬라이드
	$('.rcmd_forum').bxSlider({
		slideMargin: 10,
		infiniteLoop: false,
		hideControlOnEnd: true,
		pagerType: 'short'
	});

	//최근 방문 포럼 슬라이드
	$('.recent_forum').bxSlider({
		slideMargin: 10,
		infiniteLoop: false,
		hideControlOnEnd: true,
		pager: false
	});

	//포럼 카테고리 아코디언
	$('.category_forum .depth1').click(function(){
		if( $(this).next('.depth1_sub').css('display') == 'none' ){
			$('.category_forum li').removeClass('on');
			$(this).parent('li').addClass('on');
		}else{
			$(this).parent('li').removeClass('on');
		}
	});

	//상품상세 > 말풍선
	$('.help_area .btn_help').click(function(){
		$(this).next('.txt_bubble').show();
	});
	$('.help_area .btn_close').click(function(){
		$(this).parent('.txt_bubble').hide();
	});

	//상품상세 > 리뷰 이동
	$('.goods-detail-hero .hero-info-community .btn-star').click(function(){
		var pdReviewTop = $('#pdReview').offset().top - $('#header').height();
		$(window).scrollTop(pdReviewTop);
	});

	//상품상세 > 관련포스팅 이동
	$('.goods-detail-hero .hero-info-community .btn-posting').click(function(){
		var relPostingTop = $('#relPosting').offset().top - $('#header').height();
		$(window).scrollTop(relPostingTop);
	});

	//매거진 상세 > 메인 슬라이드
	$('.magazine-visual-view').bxSlider({
		infiniteLoop: false,
		hideControlOnEnd: true,
		controls:false
	});
	
	//룩북 상세 > 메인 슬라이드
	$('.lookbook-visual-view').bxSlider({
		infiniteLoop: false,
		hideControlOnEnd: true,
		controls:false
	});

	//룩북 상세 > 메인 슬라이드 
	/*$('.lookbook_slide_view').slick({
		slidesToShow: 1,
		slidesToScroll: 1,
		arrows: false,
		fade: true,
		asNavFor: '.lookbook_slide_thumb'
	});
	$('.lookbook_slide_thumb').slick({
		slidesToShow: 3,
		slidesToScroll: 3,
		asNavFor: '.lookbook_slide_view',
		dots: false,
		arrows: false,
		centerMode: false,
		focusOnSelect: true
	});*/

	//주문결제 > 결제수단선택 on/off
	$('.select_payment .list_payment li a').click(function(){
		$('.select_payment .list_payment li a').removeClass('on');
		$(this).addClass('on');
	});

	//lnb submenu open
/*	$('.lnb_menu_wrap .has_sub > a').click(function(){
	   if( $(this).next('.sublnb').css('display') == 'none' ){
		  $(this).next('.sublnb').slideDown('fast');
		  $(this).parent().addClass('on');
	   }else{
		  $(this).next('.sublnb').slideUp('fast');
		  $(this).parent().removeClass('on');
	   }
	});
*/
/*
	$('.lnb_menu_wrap .has_sub > a').click(function(){
	   if( $(this).next('.sublnb').css('display') == 'none' ){
		  $(this).parent().siblings().find('.sublnb').slideUp('fast');
		  $(this).next('.sublnb').slideDown('fast');
		  $(this).parent().siblings().removeClass('on');
		  $(this).parent().siblings().find('.has_sub').removeClass('on');
		  $(this).parent().addClass('on');
	   }else{
		  $(this).next('.sublnb').slideUp('fast');
		  $(this).parent().removeClass('on');
		  $(this).next('.sublnb').find('.sublnb').slideUp('fast');
		  $(this).next('.sublnb').find('.has_sub').removeClass('on');
	   }
	});
*/
	$('.lnb_menu_wrap .has_sub > a .btn_sub_open, .wrap_link .btn_sub_open').click(function(){
	   if( $(this).parent().next('.sublnb').css('display') == 'none' ){
		  $(this).parent().parent().siblings().find('.sublnb').slideUp('fast');
		  $(this).parent().next('.sublnb').slideDown('fast');
		  $(this).parent().parent().siblings().removeClass('on');
		  $(this).parent().parent().siblings().find('.has_sub').removeClass('on');
		  $(this).parent().parent().addClass('on');
	   }else{
		  $(this).parent().next('.sublnb').slideUp('fast');
		  $(this).parent().parent().removeClass('on');
		  $(this).parent().next('.sublnb').find('.sublnb').slideUp('fast');
		  $(this).parent().next('.sublnb').find('.has_sub').removeClass('on');
	   }
	});

    // 검색 영역 컨트롤
    var searchOpen = false;
    $('.btn_search').on('click', function(){
        if(searchOpen)
        {
            $('.search_area').css('display','none');
            $(this).removeClass('on');
            searchOpen = false;
        }else{
            $('.search_area').css('display','block');
            $(this).addClass('on');
            searchOpen = true;
        }
    });

    // 메인 > 상단 비쥬얼
    $('.main_visual').bxSlider({
        infiniteLoop: true,
        auto: true,
        speed: 500,
        hideControlOnEnd: false,
        controls:false,
		adaptiveHeight: true,
		adaptiveHeightSpeed: 300
    });

	
	// 메인 > 상품 커뮤니티 정렬
    var community = {};
    var imgLen11 = $('.main-community-content').eq(0).find('img').length;
    var count11 = 0;
    $('.main-community-content').eq(0).find('img').on('load', function(){
        count11++;
        if(imgLen11 == count11)
        {
            community.community0 = $('.main-community-content').eq(0).children('ul').masonry({
                itemSelector: '.grid-item',
                columnWidth: '.grid-sizer',
                percentPosition: false
            });

            resizeCommunity();
        }
    });

    var imgLen12 = $('.main-community-content').eq(1).find('img').length;
    var count12 = 0;
    $('.main-community-content').eq(1).find('img').on('load', function(){
        count12++;
        if(imgLen12 == count12)
        {
            community.community1 = $('.main-community-content').eq(1).children('ul').masonry({
                itemSelector: '.grid-item',
                columnWidth: '.grid-sizer',
                percentPosition: false
            });

            resizeCommunity();
        }
    });

    var imgLen13 = $('.main-community-content').eq(2).find('img').length;
    var count13 = 0;
    $('.main-community-content').eq(2).find('img').on('load', function(){
        count13++;
        if(imgLen13 == count13)
        {
            community.community2 = $('.main-community-content').eq(2).children('ul').masonry({
                itemSelector: '.grid-item',
                columnWidth: '.grid-sizer',
                percentPosition: false
            });

            resizeCommunity();
        }
    });

    function resizeCommunity()
    {
        if($(window).width()>640)
        {
            var column = Math.ceil($('.main-community-content').width()/156);
            $('.main-community-content>ul>li').css('width',(100/column)+'%');
        }else{
			$('.main-community-content>ul>li').css('width',(100/2)+'%');
        }
    }

   // 메인 > 상품 개수 노출 컨트롤
    /*var product_slide = [];
	var mdslides = Math.floor($('.product_area').width()/148);
	var mdoption = {
            infiniteLoop: false,
            hideControlOnEnd: true,
            slideWidth:148,
            slideMargin:8,
            minSlides:1,
            maxSlides:mdslides,
            controls:false
        };          

	for(var i=0;i<$('.product_area').length;i++)
	{           
	   product_slide[i] = $('.product_area').eq(i).children('ul').bxSlider(mdoption);           
	}    

	$(window).on('resize', function(){
		for(var i=0;i<$('.product_area').length;i++)
        {           
            product_slide[i].reloadSlider(mdoption);           
        }
	});*/

    var main_product_tabOn = $('.main-product-tab a');
    var main_product_tabOn_content = $('.product_area');

    main_product_tabOn.on('click',function(e){
        var idx = main_product_tabOn.index($(this));

        main_product_tabOn.removeClass('on');
        //main_product_tabOn_content.removeClass('on');

        main_product_tabOn.eq(idx).addClass('on');
       // main_product_tabOn_content.eq(idx).addClass('on');

        //$(window).trigger('resize');
    });

    // 메인 > 상품 하단 노출 정렬
    var imgLen2 = $('.main-list-item').find('img').length;
    var count2 = 0;
    $('.main-list-item').find('img').on('load', function(){
        count2++;
        if(imgLen2 == count2)
        {
            $('.main-list-item').masonry({
                itemSelector: '.grid-item',
                columnWidth: '.grid-sizer',
                percentPosition: false
            });

            //resizeProductList();
        }
    });

    function resizeProductList()
    {
        if($(window).width()>640)
        {
            var column = Math.ceil($('.main-list-item').width()/156);
            $('.main-list-item>ul>li').css('width',(100/column)+'%');
        }else{
            $('.main-list-item>ul>li').css('width',(100/2)+'%');
        }
    }

    $(window).on('resize', function(){
        // 상품 커뮤니티 정렬
        resizeCommunity();

        // 상품 개수 노출 컨트롤
        //product_slide_bind();

        // 상품 하단 노출 정렬
        //resizeProductList();
    }).trigger('resize');

	// 푸터 리뷰 리스트
	$('.best-phto-review').bxSlider({
        infiniteLoop: false,
		hideControlOnEnd: true,
		pager: false,
		slideWidth: 165,
		minSlides: 3,
		maxSlides: 20,
		moveSlides: 3,
		slideMargin: 7
    });

	
	// 아코디언 온/오프
	$(".sorting .btn-toggle").on("click", function(_e) {
		if($(this).parent().parent().parent().hasClass('inner'))
		{
			if ($(this).parent().hasClass("on"))
			{
				$(this).parent().parent().children().removeClass('on');
				$(this).parent().removeClass("on");
				$(this).attr("title", "접어놓기");
			} else{
				$(this).parent().parent().children().removeClass('on');
				$(this).parent().addClass("on")
				$(this).attr("title", "펼쳐보기");
			}
		}else{
			$(this).parent().toggleClass('on');
			if ($(this).parent().hasClass("on")) $(this).attr("title", "접어놓기");
			else $(this).attr("title", "펼쳐보기");
		}
	});

	$(".sorting .btn-toggle").prevAll('h3').on("click", function(_e) {
		if($(this).parent().parent().parent().hasClass('inner')) {
			if ($(this).parent().hasClass("on")) {
				$(this).parent().parent().children().removeClass('on');
				$(this).parent().removeClass("on")
				$(this).nextAll('.btn-toggle').attr("title", "접어놓기")
			} else {
				$(this).parent().parent().children().removeClass('on');
				$(this).parent().addClass("on")
				$(this).nextAll('.btn-toggle').attr("title", "펼쳐보기");
			}
		}else{
			$(this).parent().toggleClass('on');
			if ($(this).parent().hasClass("on")) $(this).nextAll('.btn-toggle').attr("title", "접어놓기")
			else $(this).nextAll('.btn-toggle').attr("title", "펼쳐보기");
		}
	});

	// 상품상세 > 상단 비쥬얼
    $('.detail_visual ul').bxSlider({
        infiniteLoop: true,
        hideControlOnEnd: true,
		adaptiveHeight: true,
		adaptiveHeightSpeed: 300,
		auto: true,
		speed: 500 
    });

	// 상품상세 > color 슬라이더 
	$('.hero-info-color ul').bxSlider({ 
		infiniteLoop: true, 
		hideControlOnEnd: true, 
		slideWidth: 152, 
		minSlides: 3, 
		maxSlides: 3, 
		moveSlides: 3, 
		slideMargin: 9, 
		pager:false/*, 
		onSliderLoad:function(){ 
			if($('.hero-info-color ul li').length < 3){ 
				$('.goods-detail-hero .hero-info-color').css('padding-left','9.53%'); 
				$('.goods-detail-hero .hero-info-color li').css('width','7.44%');
			} 
		} */
	}); 

	// 상품상세 > 관련상품
    $('.related_product ul').bxSlider({
        infiniteLoop: false,
        hideControlOnEnd: true,
		pager: false,
		controls:false,
		slideWidth: 230,
		minSlides: 2,
		maxSlides: 20,
		moveSlides: 2,
		slideMargin: 5
    });

	// 상품상세 > 댓글입력폼
	$('.btn_answer').on('click', function(){
		if($(this).parent().next('.answer_area').css('display') == 'none')
		{
			$(this).parent().next('.answer_area').css('display','table');
		}else{
			$(this).parent().next('.answer_area').css('display','none');
		}

	});

	// 상품상세 > 구매버튼 스크립트
	$('.buying .alone a').on('click', function(){
		$('.buying .buy_level2').addClass('on');
	});

	$('.buying .buy_level2 .top a').on('click', function(){
		$('.buying .buy_level2').removeClass('on');
	});

	// 브랜드 > 비쥬얼 배너롤링
	$('.brand_visual_wrap ul').bxSlider({
        infiniteLoop: false,
        hideControlOnEnd: true,
        controls:false,
		auto: true,
        speed: 500
		
    });

	// top 버튼 스크립트 추가
    $(window).on('scroll', function(){
        if($(window).scrollTop() > 0)
        {
            $('.quick_btn_wrap').css('display', 'block');
        }else{
            $('.quick_btn_wrap').css('display', 'none');
        }
    });

	//마이페이지 > 쿠폰적용하기 레이어
	$('.btn-apply').on('click', function(){
		if($(this).parent().parent().next().css('display')=='block')
		{
			$(this).removeClass('on');
			$(this).parent().parent().next().css('display','none');
		}else{
			$(this).addClass('on');
			$(this).parent().parent().next().css('display','block');
		}
	})

	//카트 > 옵션변경
	$('.btn-opt-change').click(function(){
		$('.opt-change-box').toggle();
	});
	$('.opt-change-hide').click(function(){
		$('.opt-change-box').hide();
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
    var scrolltop = (_y == undefined) ? $target[0].offsetTop : _y;
    $("#page").scrollTop(scrolltop);
}