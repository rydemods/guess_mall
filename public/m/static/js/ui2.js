$(document).ready(function(){
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
	$('.lnb_menu_wrap .has_sub > a').click(function(){
	   if( $(this).next().find('.sublnb').css('display') == 'none' ){
		  $(this).next().find('.sublnb').slideDown('fast');
		  $(this).parent().addClass('on');
	   }else{
		  $(this).next().find('.sublnb').slideUp('fast');
		  $(this).parent().removeClass('on');
	   }
	});

});