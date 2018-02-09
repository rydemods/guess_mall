$(document).ready(function(){ 

	/* ==============================
		common	
	============================== */
	
	//바디 고정,해제
	function bodyFix(){
		$('html,body').css('overflow-y','hidden');
	}
	function bodyStatic(){
		$('html,body').css('overflow-y','auto');
	}

	//GNB
	var gnbC1_in = $('.gnb .category .c1');
	var gnbC1_out = $('.gnb .category li');

	gnbC1_in.hover(function(){
		$(this).next().fadeIn();
	})
	gnbC1_out.mouseleave(function(){
		$(this).children('.under-c1').fadeOut();
	})

	//스크롤시 GNB이동
	/*
	$(document).on('scroll',function(e){
		if($(document).scrollTop() > 70){			
			$('.header-wrap').addClass('fixed');
			$('.header-wrap').stop().animate({'margin-top':'-' + 80})
		} else if ($(document).scrollTop() < 69){
			$('.header-wrap').removeClass('fixed');
			$('.header-wrap').stop().css({'margin-top':0})
		}
	});
	*/

	//검색 팝업
	var hd_searchLayer = $('.header-search');
	
	$('#searchLayer-open').click(function(){
		hd_searchLayer.show();	
		$( bodyFix );
	});
	$('#searchLayer-close').click(function(){
		hd_searchLayer.hide();
		$( bodyStatic );
	});

	//이미지 로드 - masonry 와 함께 사용
	$.fn.imagesLoaded = function( callback ){
	  var elems = this.find( 'img' ),
		  elems_src = [],
		  self = this,
		  len = elems.length;

	  if ( !elems.length ) {
		callback.call( this );
		return this;
	  }

	  elems.one('load error', function() {
		if ( --len === 0 ) {
		  // Rinse and repeat.
		  len = elems.length;
		  elems.one( 'load error', function() {
			if ( --len === 0 ) {
			  callback.call( self );
			}
		  }).each(function() {
			this.src = elems_src.shift();
		  });
		}
	  }).each(function() {
		elems_src.push( this.src );
		// webkit hack from http://groups.google.com/group/jquery-dev/browse_thread/thread/eee6ab7b2da50e1f
		// data uri bypasses webkit log warning (thx doug jones)
		this.src = "data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///ywAAAAAAQABAAACAUwAOw==";
	  });

	  return this;
	};


	/* ==============================
		컴포넌트	
	============================== */
	
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

	//이미지 첨부
	var imgTarget = $('.filebox .upload-hidden');

	imgTarget.on('change', function(){
		var parent = $(this).parent();
		parent.children('.upload-display').remove();

		if(window.FileReader){
			//image 파일만
			if (!$(this)[0].files[0].type.match(/image\//)) return;
			
			var reader = new FileReader();
			reader.onload = function(e){
				var src = e.target.result;
				parent.prepend('<div class="upload-display"><div class="upload-thumb-wrap"><img src="'+src+'" class="upload-thumb"></div></div>');
				parent.children('.photoBox').addClass('after');
			}
			reader.readAsDataURL($(this)[0].files[0]);
		}

		else {
			$(this)[0].select();
			$(this)[0].blur();
			var imgSrc = document.selection.createRange().text;
			parent.prepend('<div class="upload-display"><div class="upload-thumb-wrap"><img class="upload-thumb"></div></div>');
			parent.children('.photoBox').addClass('after');

			var img = $(this).siblings('.upload-display').find('img');
			img[0].style.filter = "progid:DXImageTransform.Microsoft.AlphaImageLoader(enable='true',sizingMethod='scale',src=\""+imgSrc+"\")";
		}
	});

	//이미지 비율대로 자르기
	var squareHeight = $('.square-img').height();

	$('.square-img img').each(function(){
		if ( $(this).height() < squareHeight) {
			$(this).css({
				'height' : squareHeight,
				'max-width' : 'inherit'
			})
		}
	})

	//레이어팝업
	var layerWidth = $('.layer-dimm-wrap .layer-inner').width();
	var layerHeight = $('.layer-dimm-wrap .layer-inner').height();
	var resizeLayerPop = $('.layer-dimm-wrap .layer-inner');
	var layerPopClose = $('.layer-dimm-wrap .btn-close');
	var layerPopWrap = $('.layer-dimm-wrap');

	// 2017-07-06 추가
	var dimm_cover = $("<div class='dimmCover'></div>");
	dimm_append();
	function dimm_append () {
		layerPopWrap.prepend(dimm_cover);
	}
	
	layerPopWrap.hide();

	// 2017-07-06 추가
	$('.dimmCover').click(function(){
		layerPopWrap.hide();
		$(bodyStatic);
	})
	
	//레이어팝업 공통 닫기
	layerPopClose.click(function(){
		layerPopWrap.hide();
		$(bodyStatic);
	});

	//내용 스크롤바
	$(".js-scroll").each(function() {
		$(this).mCustomScrollbar({ theme:"dark-3" });
	});

	//상품 정렬 > 갯수
	var view_ea_btn = $('.goods-sort .view-ea button');
	
	view_ea_btn.click(function(){
		$(this).siblings().removeClass('on');
		$(this).addClass('on');
	});

	//리스트 보기방식
	var list_type = $('.goods-sort .type button');

	list_type.click(function(){
		$(this).siblings().removeClass('active');
		$(this).addClass('active');
	});
	$('#type-half').click(function(){
		$('.goods-list').removeClass('four').addClass('two');
	});
	$('#type-quarter').click(function(){
		$('.goods-list').removeClass('two').addClass('four');
	})
	
	// 탭 메뉴 - 일반형
	$("[data-ui=TabMenu]").each(function() {
		
//		var $ui = $(this);
//		var $menu = $ui.find("[data-content=menu]");
//		var $content = $ui.find("[data-content=content]");
//
//		$menu.on("click", function(_e) {
//
//			_e.preventDefault();
//			var index = $menu.index(this);
//			$menu.removeClass("active").removeAttr("title").eq(index).addClass("active").attr("title", "선택됨");
//			$content.removeClass("active").eq(index).addClass("active");
//
//		});

		var $ui = $(this);
		var $menu = $ui.find("[data-content=menu]");
		var $content = $ui.find("[data-content=content]");

		// 20170705 매장 닫기구현
		var temp_id;
		var flg = false;
		$menu.on("click", function(_e) {
			_e.preventDefault();
			var index = $menu.index(this);
			
			if(temp_id == index){
				if(flg){
					$menu.eq(index).addClass("active").attr("title", "선택됨");
					$content.eq(index).addClass("active");
					flg = false;
				} else {
					$menu.removeClass("active").removeAttr("title");
					$content.removeClass("active");
					flg = true;
				}
				
				temp_id;
			} else {
				$menu.removeClass("active").removeAttr("title").eq(index).addClass("active").attr("title", "선택됨");
				$content.removeClass("active").eq(index).addClass("active");
				flg = false;
				temp_id = index;
			}
			
		});
		
	});

	//테이블 tr 열고 닫기
	var tb_toggleMenu= $('.table-toggle .menu');
	var tb_toggleContent= $('.table-toggle .content');
	var tb_toggleTbodySubject= $('.table-toggle .tbody_subject');	// 20170404 리스트 상단 메뉴 삭제 에러 처리

	tb_toggleMenu.click(function(){
//		$(this).parents('tbody').find('tr:odd').addClass('hide');
		tb_toggleTbodySubject.find('tr:odd').addClass('hide');
		$(this).parents('tr').next().toggleClass('hide');
	})


	//기간별 조회
	var btn_dateMonth = $('.date-sort .month button');
	
	btn_dateMonth.click(function(){
		btn_dateMonth.removeClass('on');
		$(this).addClass('on');
	});

	//라이크 아이콘
	/*var like_icon = $('.icon-like');
	like_icon.click(function(){
		$(this).toggleClass('on');
	})
	*/

	/* ==============================
		레이어팝업 열기	
	============================== */
	//콤포넌트 페이지 테스트용
	$('.btn-popTest').click(function(){
		$('.popTest').show();
	});
	
	//상품 미리보기
	$('.btn-preview').click(function(){
		$('.goodsPreview').show();
		$(bodyFix);
	});

	//상세 > 리뷰 리스트
	$('#btn-reviewList').click(function(){
		$('.goodsReview-list').show();
		$(bodyFix);
	});

	//상세 & 마이페이지 > 리뷰 작성
	/*
	$('#btn-reviewWrite , .btn-reviewWrite').click(function(){
		$('.goodsReview-list').hide();
		$('.goodsReview-write').show();
		$(bodyFix);
	});*/

	//상세 > Q&A 리스트
	$('#btn-qnaList').click(function(){
		$('.goodsQna-list').show();
		$(bodyFix);
	});
	
	//상세 > Q&A 작성
	$('#btn-qnaWrite').click(function(){
		$('.goodsQna-list').hide();
		$('.goodsQna-write').show();
		$(bodyFix);
	});

	//상세 > 상품상세정보
	$('#btn-detailPop').click(function(){
		$('.goodsDetail-pop').show();
		$(bodyFix);
	});

	//상세 > 배송반품
	$('#btn-deliveryPop').click(function(){
		$('.goodsDelivery-pop').show();
		$(bodyFix);
	});

	//상세 > 당일수령
	$('#btn-shopToday').click(function(){
		$('.find-shopToday').show();
		$(bodyFix);
	});

	//상세 > 매장픽업
	$('#btn-shopPickup').click(function(){
		$('.find-shopPickup').show();
		$(bodyFix);
	});

	//주문 > 배송지목록
	$('#btn-deliveryList').click(function(){
		$('.popList.delivery').show();
		$(bodyFix);
	});

	//주문 > 쿠폰사용
/*
	주문상세페이지로 가져감
	$('.btn-couponList').click(function(){
		$('.popList.coupon').show();
		$(bodyFix);
	});
*/
	//주문 > 매장안내
	/*
	$('.btn-infoStore').click(function(){
		$('.pop-infoStore').show();
		$(bodyFix);
	});
*/
	//카탈로그 > 상세보기
	$('.open-catalogView').click(function(){
		$('.popCatalog-view').show();
		$(bodyFix);
	});

	//마이페이지 > 주문상세 > 배송지변경
	$('#delivery-change').click(function(){
		$('.popDelivery-change').show();
		$(bodyFix);
	});
	
	//마이페이지 > 주문상세 > 배송지변경 > 배송지목록
	$('.btn-deliveryList').click(function(){
		$('.popDelivery-change').hide();
		$('.popList.delivery').show();
		$(bodyFix);
	});

	//마이페이지 > 주문상세 > 교환신청
	$('.btn-deliveryExchange').click(function(){
		$('.popDelivery-return.exchange').show();
		$(bodyFix);
	});

	//마이페이지 > 주문상세 > 반품신청
	$('.btn-deliveryRefund').click(function(){
		$('.popDelivery-return.refund').show();
		$(bodyFix);
	});

	//마이페이지 > 1:1문의 작성
	$('.btn-my-qnaWrite').click(function(){
		$('.myQna-write').show();
		$(bodyFix);
	});

	//마이페이지 > 배송지 추가
	$('.btn-postList.add').click(function(){
		$('.pop-post.add').show();
		$(bodyFix);
	});

	//마이페이지 > 배송지 추가
	$('.btn-postList.modify').click(function(){
		$('.pop-post.modify').show();
		$(bodyFix);
	});

	//마이페이지 > AS접수
	$('.btn-as-order').click(function(){
		$('.pop-asReg').show();
		$(bodyFix);
	});

	//이벤트 > 포토댓글 팝업
	$('.btn-photoReply').click(function(){
		$('.pop-photoReply').show();
		$(bodyFix);
	});

	//이벤트 > 포토댓글 등록
	$('.btn-photoReg').click(function(){
		$('.pop-photoReg').show();
		$(bodyFix);
	});

	

	/* ==============================
		메인	
	============================== */

	//메인 > 비주얼
	$('#main-visual-slide').bxSlider({
		auto:true,
		pause:5000,
		slideMargin: 0
	})

	//메인 > 신상품
	$('#new-arrivals').bxSlider({
		pager:false,
		auto:true,  // 2017-06-19 자동으로 변경
		pause:5000,
		slideWidth: 300,
		minSlides: 3,
		maxSlides: 4,
		moveSlides: 4,
		slideMargin: 0
	})

	//메인 > 베스트상품
	$('#main-best').bxSlider({
		pause:5000,
		slideMargin: 0,
		auto:true, // 2017-06-19 자동으로 변경
		pagerCustom: '#best-brand-nm'
	})
	$('best-brand-nm').eq(0).addClass('active');
	
	//메인 > 베스트 상품 좌우 버튼 위치
	$('.bg-wrap .bx-prev').css({"left": ( 960 - ($(window).width() / 2 )) + 40 });
	$('.bg-wrap .bx-next').css({"right": ( 960 - ($(window).width() / 2 )) + 40 });
	function csiz(){
		$('.bg-wrap .bx-prev').css({"left": ( 960 - ($(window).width() / 2 )) + 40 });
		$('.bg-wrap .bx-next').css({"right": ( 960 - ($(window).width() / 2 )) + 40 });
	}	
	$(window).resize(function (){
		$(csiz);
	});


	/* ==============================
		상품 상세	
	============================== */
	
	//상세페이지에서만 GNB 배너 미출력
	if( window.location.href.indexOf("goods/goods_view.php") != -1 ){
		$('#header').addClass('no-banner');
		$('#contents').css('padding-top',110);
		$(document).on('scroll',function(e){
			if($(document).scrollTop() > 70){			
				$('.header-wrap').stop().animate({'margin-top':0})
			} else if ($(document).scrollTop() < 69){
				$('.header-wrap').stop().css({'margin-top':0})
			}
		});
	}

	//썸네일
	/*
	$('.thumbList-big').bxSlider({
		mode:'fade',
		controls:false,
		pagerCustom: '.thumbList-small'
	});*/

	//컬러칩 변경
	//$('.goods-colorChoice label').eq(0).addClass('active');
	$('.goods-colorChoice label').click(function(){
		$(this).siblings().removeClass('active');
		$(this).addClass('active');
	});

	//매장 찾기 팝업 리스트 토글
	$('.shopList-wrap .title').click(function(){
		$(this).parent().toggleClass('active')
	})
	
	//썸네일 상세보기
	var goodsPage_info= $('.goods-view-wrap');
	var goodsPage_thumb = $('.goodsThumb-zoom');

	/**
	 * 20170706 -> 닫기 클릭시 화면상단이동처리
	 */
	$('#thumb-zoomClose, .goodsThumb-zoom').click(function(){ // 2017-07-06 영역 추가
		goodsPage_info.addClass('hide');
		goodsPage_thumb.removeClass('hide');
		window.scrollTo(0, 0);
	});

	/**
	 * 20170706 -> 닫기 클릭시 화면상단이동처리
	 */
	$('#thumb-zoomClose').click(function(){
		goodsPage_info.removeClass('hide');
		goodsPage_thumb.addClass('hide');
		window.scrollTo(0, 0);
	});


	/* ==============================
		장바구니 주문/결제
	============================== */
	$('.opt-change .item-del').click(function(){
		$(this).parents('.th-top').find('tr.active').removeClass('active');
	})


	/* ==============================
		브랜드
	============================== */

	//브랜드 GNB 변경
	if( page_checker() != -1 ){
		$('#header').addClass('brand-header');
	}

	 function page_checker(){
		var Arr_location  = [
			"/sinwon/web/brand/brand_about.php",
			"/sinwon/web/brand/brand_store.php",
			"/sinwon/web/brand/brand_eCatalog.php",
			"/sinwon/web/brand/brand_lookbook.php",
			"/sinwon/web/brand/brand_lookbook_view.php",
			"/sinwon/web/goods/brand_goods_list.php"
		]; // check할 해당 page
		var location_this = $( location ).attr( 'pathname' );
		var check_return  = -1;
		if( $.inArray( location_this, Arr_location) != -1 ){ // 해당 page에 포함되어야 함
			if( window.location.href.indexOf( location_this ) != -1 ){ // 해당 url에 page가 존재해야함
				check_return = 1;
			}
		}
		return check_return;
	};
	/*
	//룩북 상세
	$('.lookbook-big').bxSlider({
		pagerCustom: '.lookbook-thumb'
	});
	

	//카타로그 상세 팝업 슬라이드
	$('#catalog-slide').bxSlider({
		slideWidth:600,
		infiniteLoop:false,
		hideControlOnEnd:true
	})
	*/


	/* ==============================
		아웃렛
	============================== */
	//아웃렛 헤더 변경
	if( window.location.href.indexOf("main/outlet.php") != -1 ){
		$('#header').addClass('outlet-header');
	} 

	//상단 슬라이더
	$('#outlet-top-slider').bxSlider({
		slideWidth:828,
		pause:5000,
		auto:true
	})

	//상단 슬라이더(소배너 없이 단독으로 노출되는 경우)
	$('#outlet-top-slider-full').bxSlider({
		slideWidth:1200,
		pause:5000,
		auto:true
	})

	//MD'S CHOICE
	$('#outlet-mds').bxSlider({
		slideWidth:496
	})


	/* ==============================
		스타일
	============================== */
	//인스타그램 태그
	$('.instagram-tags a').click(function(){
		$(this).siblings().removeClass('active');
		$(this).addClass('active');
	});


	/* ==============================
		프로모션
	============================== */
	//기획전 리스트 슬라이더
	$('.promotionList-slider').bxSlider({
		sliderWidth:1100,
		auto:true,
		pause:5000,
		controls:false
	});
		
	//기획전 3번 템플릿
	$('.promotionTemp3-slider').bxSlider({
		pager:false
	});


	/* ==============================
		쇼윈도
	============================== */
	var sw_contentMore= $('.showWindow-item .comment-open');

	sw_contentMore.click(function(){
		$(this).parents('.showWindow-item').toggleClass('active');
	})

});