$( window ).load( function(){
    //브랜드 > 룩북 > 리스트 비대칭
	$( '.grid_col2' ).masonry({
        itemSelector: '.grid_item',
		gutter: 5,
		percentPosition: true
    });
	
	//쇼윈도 > 리스트 비대칭
	var $gridSwd = $( '.swd_grid' ).masonry({
        itemSelector: '.swd_item',
		percentPosition: true
    });
	$gridSwd.on('click','.btn_readmore',function(){
		$gridSwd.masonry('layout');
		return false;
	});
});


$(document).ready(function(){ 
	/* ==============================
		common	
	============================== */
	//resize
	window_resize();

	function window_resize() {
		$("#page").css({ paddingTop:$("#header").outerHeight() });
		var pageMinH = $("body").height() - $("#header").outerHeight();
		$("#page").css({ minHeight:pageMinH });
		var contentMinH = pageMinH - $("#footer").outerHeight();
		$("#content").css({ minHeight:contentMinH });
	}

	//앱 설치 유도 배너 닫기
	$('.topbanner').each(function(){
		var ui = $(this);
		var btnCls = $(this).find('.btn_close');
		btnCls.click(function(){
			ui.hide();
			window_resize();
		});
	});

	//body stop/static
	function bodyStop(){
		var sct = - $(window).scrollTop();
		$('body').addClass('lyr_open');
		$('#page').css('top',sct);
	}
	function bodyStatic(){
		var osc = - $('#page').position().top;
		$('body').removeClass('lyr_open');
		$(window).scrollTop(osc);
	};

	//lnb open/close
	$('.btn-lnb-open').click(function(){
		$('.lnb-layer').addClass('on');
		$('.lnb-layer-inner').stop().animate({left:0},300);
		bodyStop();
	})
	$('.btn-lnb-close, .lnb-layer-dim').click(function(){
		$('.lnb-layer-inner').stop().animate({left:'-100%'},200,function(){
			$('.lnb-layer').removeClass('on');
		});
		bodyStatic();
	});

	//rnb open/close
	$('.btn-rnb-open').click(function(){
		$('.rnb-layer').addClass('on');
		$('.rnb-layer-inner').stop().animate({right:0},300);
		bodyStop();
	})
	$('.btn-lnb-close, .rnb-layer-dim').click(function(){
		$('.rnb-layer-inner').stop().animate({right:'-100%'},200,function(){
			$('.rnb-layer').removeClass('on');
		});
		bodyStatic();
	});

	//lnb category submenu open
	$('.main_category a').click(function(){
		var subMenu = $(this).next('.sub_category');
		if( subMenu.css('display') == 'block' ){
			subMenu.hide();
		}else{
			$(this).parent('li').siblings().find('.sub_category').hide();
			subMenu.show();
		}
	});

	//header > search popup
	$('#btn_search').click(function(){
		$('.pop_search').show();
		bodyStop();
	});
	$('.pop_search .close_search').click(function(){
		$('.pop_search').hide();
		bodyStatic();
	});

	// prev, top 버튼 
    $(window).on('scroll', function(){
        if($(window).scrollTop() >= 80)
        {
            $('.quick_btn_wrap').css('display', 'block');
        }else{
            $('.quick_btn_wrap').css('display', 'none');
        }
    });

	var btnTop = $('.quick_btn_wrap .top_btn');
	btnTop.click(function() {
		$('html, body').animate({scrollTop: 0}, 500);
	});

	if( window.location.href.indexOf("productdetail") > -1 ){ 
		$('.quick_btn_wrap a.prev_btn, .quick_btn_wrap a.top_btn').css('bottom','45px');
	}; 


	/* ==============================
		component	
	============================== */
	// 탭 메뉴 - 일반형
	$("[data-ui=TabMenu]").each(function() {
		var $ui = $(this);
		var $menu = $ui.find("[data-content=menu]");
		var $content = $ui.find("[data-content=content]");
		$menu.on("click", function(_e) {
			//_e.preventDefault();
			var index = $menu.index(this);
			$menu.removeClass("active").removeAttr("title").eq(index).addClass("active").attr("title", "선택됨");
			$content.removeClass("active").eq(index).addClass("active");
		});
	});

	//파일 첨부
	var fileTarget = $('.filebox .upload-hidden');
	fileTarget.on('change', function(){  // 값이 변경되면
		if(window.FileReader){  // modern browser
		  var filename = $(this)[0].files[0].name;
		} 
		else {  // old IE
		  var filename = $(this).val().split('/').pop().split('\\').pop();  // 파일명만 추출
		}
		// 추출한 파일명 삽입
		$(this).siblings('.upload-nm').val(filename);
	});

	//이미지 업로드 
	 $('.add-image').on('change', function( e ) {        
		ext = $(this).val().split('.').pop().toLowerCase();
		if($.inArray(ext, ['gif', 'png', 'jpg', 'jpeg']) == -1) {
			resetFormElement($(this));
			window.alert('이미지 파일이 아닙니다! (gif, png, jpg, jpeg 만 업로드 가능)');
		} else {
			blobURL = window.URL.createObjectURL(e.target.files[0]);
			$(this).parents("li").find('.image_preview img').attr('src', blobURL);
			$(this).parents("li").find('.vi-image').val('');
			$(this).parents("li").find('.image_preview').show();
		}
	});

	$('.image_preview a').bind('click', function() {
		//if( confirm('삭제 하시겠습니까?') ){
			resetFormElement($(this).parents("li").find('.add-image'));
			$(this).parents("li").find('.vi-image').val('');
			$(this).parent().hide();
		//}
		return false;
	});

    function resetFormElement(e) {
        e.wrap('<form>').closest('form').get(0).reset(); 
        e.unwrap();
    }

	//좋아요 버튼 on off
	/*
	$('.btn_like').click(function(){
		if( $(this).hasClass('on') ){
			$(this).removeClass('on');
			$(this).attr('title','선택 안됨');
		}else{
			$(this).addClass('on');
			$(this).attr('title','선택됨');
		}
	});
	*/

	
	//레이어팝업 모음
	$('.pop_layer .btn_close').click(function(){ //공통 닫기
		$('.pop_layer').hide();
		bodyStatic();
	});

	function popClose(){
		$('.pop_layer').hide();
		bodyStatic();
		//$(this).parents('.pop_layer').find('form')[0].reset();
	};

	$('.btn_qna_list').click(function(){
		$('.layer_qna_list').show();
		bodyStop();
	});
	/*
	$('.btn_review_list').click(function(){
		$('.layer_review_list').show();
		bodyStop();
	});
	*/
	$('.btn_qna_write').click(function(){
		$('.layer_qna_write').show();
		bodyStop();
	});
	$('.btn_review_write').click(function(){
		//$('.layer_review_write').show();
		//bodyStop();
	});
	/*
	$('.btn_select_store01').click(function(){
		$('.layer_select_store01').show();
		bodyStop();
	});
	$('.btn_select_store02').click(function(){
		$('.layer_select_store02').show();
		bodyStop();
	});
	*/
	$('.btn_goods_detail').click(function(){
		$('.layer_goods_detail').show();
		bodyStop();
	});
	$('.btn_goods_delivery').click(function(){
		$('.layer_goods_delivery').show();
		bodyStop();
	});
	/*
	$('.btn_use_coupon').click(function(){
		$('.layer_use_coupon').show();
		bodyStop();
	});*/
	$('.btn_deli_site').click(function(){
		$('.layer_deli_site').show();
		bodyStop();
	});
	$('.btn_store_info').click(function(){
		$('.layer_store_info').show();
		bodyStop();
	});
	$('.btn_exchange').click(function(){
		$('.layer_exchange').show();
		bodyStop();
	});
	$('.btn_refund').click(function(){
		$('.layer_refund').show();
		bodyStop();
	});
	$('.btn_change_addr').click(function(){
		$('.layer_change_addr').show();
		bodyStop();
	});
	$('.btn_inquiry_write').click(function(){
		$('.layer_inquiry_write').show();
		bodyStop();
	});
	$('.btn_add_deli').click(function(){
		$('.layer_add_deli').show();
		bodyStop();
	});
	$('.btn_photo_submit').click(function(){
		$('.layer_photo_submit').show();
		bodyStop();
	});
	$('.btn_as_write').click(function(){
		$('.layer_as_write').show();
		bodyStop();
	});

	/* ==============================
		content	
	============================== */
	//메인 > 메인 비주얼 슬라이드 
	var mainSlider = $('.main_visual .slide');
	mainSlider.bxSlider({
		controls: false,
		auto: true,
		pause: 5000,
		onSlideAfter: function(){
            mainSlider.startAuto()
        }
	});

	//메인 > NEW ARRIVALS 슬라이드 
	$('.new_arrivals .goodslist').bxSlider({
		controls: false,
		slideWidth: 320,
		minSlides: 2,
		maxSlides: 2,
		moveSlides: 2,
		auto: true,
		pause: 5000
	});

	//메인 > BEST SELLER 슬라이드
	$('.best_slider').carousel({
		num: 3,
		maxWidth: 135,
		maxHeight: 200,
		distance: 80,
		autoPlay: true,
		scale: 0.9,
		animationTime: 500,
		showTime: 5000
	});
	
	//메인 > 하단 슬라이드 
	$('.btm_banner .slide').bxSlider({
		controls: false
	});
	
	//리스트 > breadcrumb 드롭다운
	$('.breadcrumb').each(function(){
		var bread = $(this);
		var list_d2 = $(this).find('.depth2 > li');
		var a_d2 = list_d2.children('a');
		var cls = $(this).find('.dimm_bg');
		a_d2.click(function(){
			var prtLi = $(this).parent('li');
			var nxtD3 = $(this).next('.depth3');
			if( nxtD3.css('display') == 'none' ){
				list_d2.removeClass('on');
				prtLi.addClass('on');
				bread.addClass('on');
				$('html,body').css({'position':'fixed','overflow':'hidden'});
			}else{
				prtLi.removeClass('on');
				bread.removeClass('on');
				$('html,body').css({'position':'static','overflow':'visible'});
			}
		});
		cls.click(function(){
			list_d2.removeClass('on');
			bread.removeClass('on');
			$('html,body').css({'position':'static','overflow':'visible'});
		});
	});

	//리스트 > 필터 레이어 팝업
	$('.btn_filter').click(function(){
		$('.filter_pop').show();
		$('html,body').css({'position':'fixed','overflow':'hidden'});
	});
	$('.filter_pop .btn_close').click(function(){
		$('.filter_pop').hide();
		$('html,body').css({'position':'static','overflow':'visible'});
	});

	$('.filter_menu .filter_name').click(function(){ //필터 메뉴 오픈
		var filterMenu = $(this).parent('li');
		if( $(this).next('.filter_con').css('display') == 'none' ){
			//filterMenu.siblings('li').removeClass('on');
			filterMenu.addClass('on');
		}else{
			filterMenu.removeClass('on');
		}
	});

	//상세 > 상세이미지 슬라이드 
	$('.detail_img .slide').bxSlider({
		controls: false
	});

	//상세 > 하단고정 버튼 옵션 on off

	$('.goods_order .btn_addcart').on('click', function() { //장바구니 클릭시

		var goodsOrder = $(this).parents('.goods_order');
		if ( goodsOrder.hasClass('on') ){
			//alert('장바구니에 추가되었습니다. 장바구니로 이동하시겠습니까1?');
			/* 장바구니에 추가시 '장바구니에 추가되었습니다. 장바구니로 이동하시겠습니까?'
			   사이즈 미선택시 '사이즈를 선택하세요.' */
			 addCart();
		}else{

			goodsOrder.addClass('on');
			bodyStop();
		}
	});
	$('.goods_order .btn_ordernow').on('click', function() { //바로구매 클릭시
		var goodsOrder = $(this).parents('.goods_order');
		if ( goodsOrder.hasClass('on') ){
			//var url = ''; //주문페이지로 이동(사이즈 미선택시 '사이즈를 선택하세요.' alert)/
			//$(location).attr('href',url);
			directOrder();
		}else{
			goodsOrder.addClass('on');
			bodyStop();
		}
	});
	
	$('.goods_order .staff_buy').on('click', function() { //임직원 클릭시
		var goodsOrder = $(this).parents('.goods_order');
		if ( goodsOrder.hasClass('on') ){
			//var url = ''; //주문페이지로 이동(사이즈 미선택시 '사이즈를 선택하세요.' alert)/
			//$(location).attr('href',url);
			directOrder('staff');
		}else{
			goodsOrder.addClass('on');
			bodyStop();
		}
	});
	
	
	
	$('.goods_order .bg, .select_shipping > .btn_close').click(function(){ //옵션 선택 영역 닫기
		$(this).parents('.goods_order').removeClass('on');
		bodyStatic();
	});

	//상세 > 말풍선형 팝업 공통
	$('.wrap_bubble').each(function(){
		var wrapBubble = $(this);
		var btnBubble = $(this).find('.btn_bubble');
		var popBubble = $(this).find('.pop_bubble');
		btnBubble.children('button').click(function(){
			$('.wrap_bubble').removeClass('on');
			$('.pop_bubble').hide();
			wrapBubble.addClass('on');
			popBubble.show();
		});
		popBubble.find('.btn_pop_close').click(function(){
			wrapBubble.removeClass('on');
			popBubble.hide();
		});
	});

	//상세 > Q&A리스트 팝업 > 리스트 토글
	/*
	$('.list_board > li').each(function(){
		var subject = $(this).find('.subject');
		var titleArea = $(this).find('.title_area');
		var conArea = $(this).find('.con_area');
		var otherCon = $(this).siblings().find('.con_area');
		subject.click(function(){
			if( conArea.css('display') == 'none' ){
				otherCon.hide();
				conArea.show();
			}else{
				conArea.hide();
			}
		});
	});*/

	//상세 > 매장선택 팝업 > 지도 토글
	$('.list_store > li').each(function(){
		var btnMap = $(this).find('.btn_map');
		var mapArea = $(this).find('.map_area');
		btnMap.click(function(){
			if( mapArea.css('display') == 'none' ){
				btnMap.addClass('on');
				btnMap.text('지도닫기');
				mapArea.show();
			}else{
				btnMap.removeClass('on');
				btnMap.text('지도보기');
				mapArea.hide();
			}
		});
	});
	
	//상세 > 하단 여백 추가
	if( window.location.href.indexOf("goodsdetail") > -1 ){ 
		$('#footer').css('padding-bottom','40px'); 
	}; 

	//장바구니 > 옵션/수량 변경
	/*
	$('.cart_goods li').each(function(){
		var openOpt = $(this).find('.btn_open_opt');
		var optBox = $(this).find('.optbox');
		var closeOpt = $(this).find('.btn_close_opt');
		var optForm = optBox.find('form');
		openOpt.click(function(){
			optBox.show();
		});
		closeOpt.click(function(){
			optForm[0].reset();
			optBox.hide();
		});
	});*/
	
	//장바구니 > 전체선택/해제(선택,해제 버튼이 따로 있는 경우)
	/*
	$(function(){
		$('#allCheck').click(function(){
			$('input[type=checkbox]').prop('checked',true);
		});
		$('#allCheckF').click(function(){
			$('input[type=checkbox]').prop('checked',false);
		});
	});
	*/
	//회원가입 > 약관동의 > 전체선택/해제
	$('.join_form').each(function() {
		var agreeAll = $(this).find('#checkAll');
		var agree = $(this).find('.check_def').not(agreeAll);
		
		agreeAll.on('change', function(_e) {
			if ($(this).is(':checked')) agree.prop('checked', true);
			else agree.prop('checked', false);
		});
		
		agree.on('change', function(_e) {
			var count = agree.filter(':checked').length;
			if (agree.length == count) agreeAll.prop('checked', true);
			else agreeAll.prop('checked', false);
		});
	});

	//브랜드 > 룩북 상세 > 좋아요 버튼
	/*
	$('.photo_type_view .btns').each(function(){
		var btnLike = $(this).find('.icon_like');
		btnLike.click(function(){
			if( btnLike.hasClass('on') ){
				btnLike.removeClass('on');
				btnLike.attr('title','선택 안됨');
			}else{
				btnLike.addClass('on');
				btnLike.attr('title','선택됨');
			}
		});
	});*/

	//마이페이지 > 쿠폰 > 적용대상 토글
	$('.mypage_coupon .list_coupon li').each(function(){
		var cList = $(this);
		var btnTarget = $(this).find('.target');
		var targetMore = $(this).find('.target_more');
		btnTarget.click(function(){
			if( targetMore.css('display') == 'none' ){
				cList.addClass('on');
			}else{
				cList.removeClass('on');
			}
		});
	});

	//마이페이지 > 완료리뷰, 상품문의 1:1문의
	$('.accordion_list > li').each(function(){
		var acdnList = $(this);
		var acdnBtn = $(this).find('.accordion_btn');
		var acdnCon = $(this).find('.accordion_con');
		acdnBtn.click(function(){
			if( acdnCon.css('display') == 'none' ){
				acdnList.siblings('li').removeClass('on');
				acdnList.addClass('on');
			}else{
				acdnList.removeClass('on');
			}
		});
	});

	//마이페이지 > 1:1문의 > 파일첨부
	var fileTarget = $('.input_file .upload_hidden'); 
	fileTarget.on('change', function(){ // 값이 변경되면 
		if(window.FileReader){ // modern browser 
			var filename = $(this)[0].files[0].name; 
		} else { // old IE 
			var filename = $(this).val().split('/').pop().split('\\').pop(); // 파일명만 추출 
		} 
		// 추출한 파일명 삽입 
		$(this).siblings('.upload_name').val(filename); 
	}); 

	//프로모션 > 이벤트, 기획전 슬라이드
	$('.promotion .topbanner .slide').bxSlider({
		controls: false
	});

	//프로모션 > 기획전 > 상세 슬라이드
	$('.prgoods_detail.slider').bxSlider({
		pager: false,
		infiniteLoop: true,
		responsive: true
	});
	
	//쇼윈도 > 상품설명 더보기 토글
	$('.list_showindow .goods_area').each(function(){
		var wrapGoods = $(this);
		var btnMore = $(this).find('.btn_readmore');
		btnMore.click(function(){
			if( wrapGoods.hasClass('on') ){
				wrapGoods.removeClass('on');
				btnMore.text('더보기');
			}else{
				$('.list_showindow .goods_area').removeClass('on');
				wrapGoods.addClass('on');
				btnMore.text('닫기');
			}
		});
	});

	//고객센터 > FAQ
	$('.accordion_tbl').each(function(){
		var acdnTbl = $(this);
		var acdnTblBtn = $(this).find('.accordion_btn');
		var acdnTblCon = $(this).find('.accordion_con');
		acdnTblBtn.click(function(){
			var nextCon = $(this).parents('tr').next('.accordion_con');
			if( nextCon.css('display') == 'none' ){
				acdnTblCon.hide();
				nextCon.show();
			}else{
				nextCon.hide();
			}
		});
	});


	

});