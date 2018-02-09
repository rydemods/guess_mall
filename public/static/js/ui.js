$(document).ready(function(){

    // 상단으로 이동
	$('.move-top').click(function(){
		$("html, body").animate({ scrollTop: 0 }, 500);
		return false;
	});

	// 메인 > 히어로배너
	var mainHero = $(".main-hero-slide").bxSlider({ mode:"fade", controls:false, pager:false, onSlideBefore:hero_before });
	function hero_before() {
		
		// 유투브 정지
		youtubeAllStop();
		
	}

	// GNB 상단 상품배너 해상도 수정
	$(window).on('resize', function(){
        var itemW = 263;
        var cw = ($(window).width()/100)*45;
        var cols = (Math.floor(cw/itemW) < 4) ? Math.floor(cw/itemW) : 3 ;
        var iw = 100/cols-4.16;

        for(var i=0;i<$('.gnb .nav .sub .sub_inner').length;i++)
        {
            if(i==0 || i==1 || i==2)
            {
                $('.gnb .nav .sub .sub_inner').eq(i).children('ul').children('li').css('display', 'none');
                for(var j=0;j<cols;j++)
                {
                    $('.gnb .nav .sub .sub_inner').eq(i).children('ul').children('li').eq(j).css({
                        display:'block',
                        width:iw+'%'
                    });
                }
            }
        }
    });
	
	$('.gnb .nav').on('mouseover', function(){
        $(window).trigger('resize');
    });

	// GNB 검색폼 노출 영역
/*	$('.gnb .nav > ul > li').on('mouseover', function(){
		//mouseover();
	}).on('mouseout',function(){
		//mouseout();
	});

	$('.gnb .util li:nth-child(2) a').on('mouseover', function(){
		mouseover();
	});

	$('.gnb .util li .gnb-search').on('mouseover', function(){
		mouseover();
	});

	$('.gnb .util li .gnb-search').on('mouseout', function(){
		mouseout();
	});

	$('.gnb .util li .gnb-search').children('input[type="text"]').on('blur', function(){
		$(this).parent().css('display','none');
	});

	function mouseover()
	{
		$('.gnb').find('.gnb-search').css('display','block');
	}

	function mouseout()
	{
		if($('.gnb').find('input[type="text"]:focus')[0] == undefined)
		{
			$('.gnb').find('.gnb-search').css('display','none');
		}else{
			$('.gnb').find('.gnb-search').css('display','block');
		}
	} */

   // 메인 > 히어로배너
	var mainHero = $(".main-hero-slide").bxSlider({ 
		mode:"fade", 
		auto: true,
		speed: 500,        // 이동 속도를 설정
		controls:true
	});
	
	// 메인 > 커뮤니티
	$(".main-community-content").each(function() {
		
		var $ui = $(this);
		var $list = $ui.children("ul");
		var $li = $ui.find("li");
		var $arrow = $ui.find(".btn-arrow-prev, .btn-arrow-next");
		var scrollTotal; // 총 가로 스크롤 길이
		var columnNum; // 화면에 보여지는 세로라인 수
		var liWidth = $li.width() + parseFloat($li.css("margin-left"));
		var isSlide = false;
		
		$ui.on({
			"COMMUNITY_RESIZE":function(_e) {
				community_resize();
			},
			"COMMUNITY_RESET":function(_e) {
				$li = $ui.find("li");
				community_resize();
			}
		});
		community_resize();
		
		$arrow.on("click", function(_e) {
			
			_e.preventDefault();
			if (isSlide) return;
			isSlide = true;
			
			var posLeft;
			if ($(this).hasClass("btn-arrow-prev")) {
				posLeft = $list.position().left + liWidth * columnNum;
				if (posLeft > 0) posLeft = 0;
			} else {
				posLeft = $list.position().left - liWidth * columnNum;
				if (posLeft < -scrollTotal) posLeft = -scrollTotal;
			}
			TweenMax.to($list, 0.5, { left:posLeft, ease:Cubic.easeInOut,
				onComplete:function() {
					isSlide = false;
				}
			});
			
		});
		
		/*
		function community_resize() {
			
			$li.each(function(_i) {
				//console.log(_i)
				var $this = $(this);
				var $prev = $li.eq(_i - 1);
				var prevTotal = (_i == 0) ? 0 : $prev.outerHeight() + $prev.data("pos").top;
				var posTop;
				var posLeft;
				
				if (_i == 0 || $ui.outerHeight() - prevTotal - $this.outerHeight() < 0) {
					posTop = 0;
					posLeft = (_i == 0) ? 0 : $prev.data("pos").left + liWidth;
				} else {
					posTop = prevTotal;
					posLeft = $prev.data("pos").left;
				};
				$this.data("pos", { top:posTop, left:posLeft }).css({ position:"absolute" });
				TweenMax.to($this, 0.5, { top:posTop, left:posLeft, ease:Cubic.easeInOut });
				
			});
			columnNum = Math.floor($list.width() / liWidth);
			scrollTotal = ($li.eq(-1).data("pos").left + liWidth) - $list.width() - parseFloat($li.css("margin-left"));
			if (scrollTotal > 0 && $list.position().left < -scrollTotal) TweenMax.to($list, 0.5, { left:-scrollTotal, ease:Cubic.easeInOut });
		}
		*/

		function community_resize() {
			var tempTotal = $li.length;
			var count = 0;			resize_sort($li.eq(count), count);
			
			function resize_sort(li, _i) {
				if (count < tempTotal) {
					var $this = $(li);
					var $img = $this.find("img");
					var $prev = $li.eq(_i - 1);
					
					if ($img.height() > 0) set_position();
					else {
						$this.css({ visibility:"hidden" });
						$img.on("load", set_position);
					}
					
					function set_position() {
						
						var prevTotal = (_i == 0) ? 0 : $prev.outerHeight() + $prev.data("pos").top;
						var posTop;
						var posLeft;
						
						if (_i == 0 || $ui.outerHeight() - prevTotal - $this.outerHeight() < 0) {
							posTop = 0;
							posLeft = (_i == 0) ? 0 : $prev.data("pos").left + liWidth;
						} else {
							posTop = prevTotal;
							posLeft = $prev.data("pos").left;
						};
						$this.data("pos", { top:posTop, left:posLeft }).css({ position:"absolute" }).css({ visibility:"visible" });
						TweenMax.to($this, 0.5, { top:posTop, left:posLeft, ease:Cubic.easeInOut });
						
						count++;
						resize_sort($li.eq(count), count);
						
					}
				} else {
					columnNum = Math.floor($list.width() / liWidth);
					scrollTotal = ($li.eq(-1).data("pos").left + liWidth) - $list.width() - parseFloat($li.css("margin-left"));
					if (scrollTotal > 0 && $list.position().left < -scrollTotal) TweenMax.to($list, 0.5, { left:-scrollTotal, ease:Cubic.easeInOut });
				}
				
			}
			
		}
		
	});
	
	// 메인 > 추천상품(베스트, MD, 신상)
    /*var recSlide = [];
	$(".main-list-rec").each(function() {
		
		var $ui = $(this);
		var $tab = $ui.find(".rec-tab");
		var $list = $ui.find(".rec-content > ul");

        for(var i=0;i<$list.length;i++)
        {
            slide_bind($list.eq(i), i);
        }
		
		$ui.on("REC_RESIZE", function(_e) {
            for(i=0;i<$list.length;i++)
            {
                slide_bind($list.eq(i), i);
            }
			$tab.css("top", function() {
				var top = 104 + ($(window).height() - 650) * 0.1;
				if (top < 104) top = 104;
				return top;
			});
			$ui.find(".rec-content").css("margin-bottom", function() {
				var bottom = 22 + ($(window).height() - 650) * 0.2466666;
				if (bottom < 22) bottom = 22;
				return bottom;
			}).find(".bx-pager").css("margin-bottom", function() {
				var bottom = 5 + ($(window).height() - 650) * 0.1;
				if (bottom < 5) bottom = 5;
				return -bottom;
			});
		});

		function slide_bind($tg, idx) {
			var slides = Math.round(4 + ($(window).width() - 1024) * 0.002232);
			var margin = ($ui.width() - 80 - (slides * 382)) / (slides - 1);
			var option = { slideWidth:382, maxSlides:slides, slideMargin:margin, controls:false };
			if (recSlide[idx] == undefined) {
                recSlide[idx] = $tg.bxSlider(option);
            } else {
                recSlide[idx].reloadSlider(option);
            }
		}
	});
	
	// 메인 > 리스트
	$(".main-list-item").each(function() {
		
		var $ui = $(this);
		var $list = $ui.children("ul");
		var $arrow = $ui.find(".btn-arrow-prev, .btn-arrow-next");
		var listSlide;
		
		slide_bind();
		
		$ui.on("LIST_ITEM_RESIZE", function(_e) {
			
			slide_bind();
			$ui.css("margin-top", function() {
				
				var margin = Math.floor(10 + ($(window).height() - 650) * 0.1);
				if (margin < 10) margin = 10;
				return margin;
				
			});
			
		});
		
		$arrow.on("click", function(_e) {
			
			_e.preventDefault();
			if ($(this).hasClass("btn-arrow-prev")) listSlide.goToPrevSlide();
			else listSlide.goToNextSlide();
			
		});
		
		function slide_bind() {
			
			var slides = Math.round(4 + ($(window).width() - 1024) * 0.003348);
			var margin = ($ui.width() - (slides * 382)) / (slides - 1);
			var option = { slideWidth:382, maxSlides:slides, slideMargin:margin, pager:false, controls:false };
			if (listSlide == undefined) listSlide = $list.bxSlider(option);
			else listSlide.reloadSlider(option);
			
		}
		
	});*/

	
	// 메인 > 추천상품(베스트, MD, 신상)
	$(".main-list-rec").each(function() {
	
		var $ui = $(".main-list-rec");
		var $tab = $ui.find(".rec-tab");
		var $list = $ui.find(".rec-content > .mdpick-list");
		var recSlide;

		//slide_bind();
		
		$ui.on("REC_RESIZE", function(_e) {		
			$tab.css("top", function() {				
				var top = 50 + ($(window).height() - 650) * 0.1;
				if (top < 50) top = 50;
				return top;
			});
			$ui.find(".rec-content").css("margin-bottom", function() {
				var bottom = 46 + ($(window).height() - 650) * 0.2466666;
				if (bottom < 46) bottom = 46;
				return bottom;				
			}).find(".bx-pager").css("margin-bottom", function() {
				var bottom = 20 + ($(window).height() - 650) * 0.1;
				if (bottom < 20) bottom = 20;
				return -bottom;
			});
			//console.log('ddd')
			slide_bind();			
		});
		
		function slide_bind() {			
			 // 160922 수정
			 var contentW = $list.find("li").width();
			 var slides = Math.floor($ui.width() / contentW);// 80은 .main-list-rec 영역의 좌/우 여백을 더한 값
			 //var margin = (($ui.width() - 80) - (contentW * slides)) / (slides - 1); 
			 //var margin = (($ui.width() - 40) - (contentW * slides)) / (slides - 1); // 수치 변경함 2016-10-05
			 var option = { slideWidth:contentW, maxSlides:slides, slideMargin:0, controls:false};
			 if (recSlide == undefined)recSlide = $list.bxSlider(option);
			 else recSlide.reloadSlider(option);
		 }
	});
	
	// 메인 > 리스트
	$(".main-list-item").each(function() {
		
		var $ui = $(this);
		var $list = $ui.children("ul");
		var $arrow = $ui.find(".btn-arrow-prev, .btn-arrow-next");
		var listSlide;
		
		slide_bind();
		
		$ui.on("LIST_ITEM_RESIZE", function(_e) {
			
			slide_bind();
			$ui.css("margin-top", function() {
				
				var margin = Math.floor(10 + ($(window).height() - 650) * 0.1);
				if (margin < 10) margin = 10;
				return margin;
				
			});
			
		});
		
		$arrow.on("click", function(_e) {
			
			_e.preventDefault();
			if ($(this).hasClass("btn-arrow-prev")) listSlide.goToPrevSlide();
			else listSlide.goToNextSlide();
			
		});
		
		function slide_bind() {
			
			// 160922 수정
			var contentW = $list.find("li").width();
			var slides = Math.floor(($ui.width() - 154) / contentW);
			var margin = (($ui.width() - 154) - (contentW * slides)) / (slides - 1);
			var option = { slideWidth:contentW, maxSlides:slides, slideMargin:margin, pager:false, controls:false };
			if (listSlide == undefined) listSlide = $list.bxSlider(option);
			else listSlide.reloadSlider(option);
			
		}
		
	});	
	
	// 메인 리사이징, 휠/스크롤이벤트
	if ($(".main")[0]) {
		// 리사이징
		/*$(window).on("load resize", function(_e) {
			
			mainHero.reloadSlider(); // 메인 히어로 배너
			$(".main-community-content").trigger("COMMUNITY_RESIZE"); // 메인 커뮤니티
			$(".main-list-rec").trigger("REC_RESIZE"); // 메인 추천상품
			$(".main-list-item").trigger("LIST_ITEM_RESIZE"); // 메인 상품리스트
			
			// 메인 큰 상품
			var tempTop = Math.floor(32 + ($(window).height() - 650) * 0.216666);
			var tempLeft = Math.floor(40 + ($(window).width() - 1024) * 0.139508);
			$(".main-item-content").css({ top:tempTop, left:tempLeft });
			
			timeline_reload(); // 메인 섹션
			
		});*/
		 $(window).on("load resize", function(_e) {

			if(mainHero[0])mainHero.reloadSlider(); // 메인 히어로 배너
			$(".main-community-content").trigger("COMMUNITY_RESIZE"); // 메인 커뮤니티
			//$(".main-list-rec").trigger("REC_RESIZE"); // 메인 추천상품
			$(".main-list-item").trigger("LIST_ITEM_RESIZE"); // 메인 상품리스트

			// 메인 큰 상품
			var tempTop = Math.floor(32 + ($(window).height() - 650) * 0.216666);
			var tempLeft = Math.floor(40 + ($(window).width() - 1024) * 0.139508);
			$(".main-item-content").css({ top:tempTop, left:tempLeft });

			timeline_reload(); // 메인 섹션

		});
		
		// 섹션 이동
		var $section = $(".main-section");
		var sectionTotal = 0; // 섹션 총 높이
		var scrollTimeline = new TimelineMax();
		var isScroll = false;
		
		$(document).on("mousewheel DOMMouseScroll", function(_e) {
			
			_e.preventDefault();
			var delta = (_e.type == "mousewheel") ? _e.originalEvent.wheelDelta : -_e.originalEvent.detail;
			var direction = (delta < 0) ? "next" : "prev";
			section_scroll(direction);
			
		});
		
		$(document).on("keydown", function(_e) {
			
			if (_e.keyCode != 33 && _e.keyCode != 38 && _e.keyCode != 34 && _e.keyCode != 40) return;
			_e.preventDefault();
			switch(_e.keyCode) {
				case 33:
				case 38:
					section_scroll("prev");
				break;
				case 34:
				case 40:
					section_scroll("next");
				break;
			}
			
		});
		
		function section_scroll(_direction) {
			
			if (isScroll) return;
			isScroll = true;
			
			var time;
			if (_direction == "next") time = scrollTimeline.getLabelTime(scrollTimeline.getLabelAfter());
			else time = scrollTimeline.getLabelTime(scrollTimeline.getLabelBefore());
			
			if (time < 0) {
				isScroll = false;
				return;
			}
			
			var scroll = ($(document).height() - $(window).height()) * time / scrollTimeline.totalDuration();
			TweenMax.to("html,body", 0.7, { scrollTop:scroll, ease:Cubic.easeInOut,
				onComplete:function() {
					isScroll = false;
				}
			});
			
			// 유투브 정지
			youtubeAllStop();
			
		}
		
		$(window).on("scroll", function(_e) {
			
			var time = $(window).scrollTop() * scrollTimeline.totalDuration() / sectionTotal;
			scrollTimeline.seek(time);
			
		});
		timeline_reload();
		
		function timeline_reload() {
			
			scrollTimeline.pause().clear()
			.set(".main-footer", { y:"100%" });
			
			// 스크롤 타임라인
			$section.each(function(_i) {
				
				if (_i == 0) scrollTimeline.set(".main-inner", { top:0 });
				else scrollTimeline.to(".main-inner", 1, { top:-$(window).height() * _i, ease:Linear.easeNone });
				
				scrollTimeline.addLabel("seek" + _i);
				
			});
			scrollTimeline.to(".main-footer", 1, { y:"0%", ease:Linear.easeNone })
			.addLabel("seek" + scrollTimeline.getLabelsArray().length);
			scrollTimeline.pause();
			
			sectionTotal = $(window).height() * (scrollTimeline.getLabelsArray().length - 1);
			var time = $(window).scrollTop() * scrollTimeline.totalDuration() / sectionTotal;
			scrollTimeline.seek(time);
			
			var scroll = ($(document).height() - $(window).height()) * Math.round(time) / scrollTimeline.totalDuration();
			TweenMax.to("html,body", 0.5, { scrollTop:scroll, ease:Cubic.easeInOut,
				onComplete:function() {
					isScroll = false;
				}
			});
			
		}
	}

	// 상품리스트 > 사이드바 온/오프
	$(".goods-list-sidebar .btn-toggle").on("click", function(_e) {
		
		$(this).parent().toggleClass("on");
		if ($(this).parent().hasClass("on")) $(this).attr("title", "접어놓기");
		else $(this).attr("title", "펼쳐보기");
		
	});

	// 상품리스트 > 컬러썸네일
	color_slider_control();
	
	function color_slider_control() {
		
		$(".comp-goods .color-thumb").each(function(i) {
			
			var $ui = $(this);
			var $list = $ui.find("ul");
			var listTotal = $list.children().length;
			var viewNum = 3;
			var isControl = (listTotal > viewNum) ? true : false;
			
			if (!$list.closest(".bx-viewport")[0]) $list.bxSlider({ slideWidth:62, maxSlides:viewNum, pager:false, controls:isControl });
			
		});
		
	}

	// 상품상세 > color 슬라이더
	 $('.hero-info-color ul').bxSlider({
      infiniteLoop: false,
      hideControlOnEnd: false,
      slideWidth: 100,
      minSlides: 1,
      maxSlides: 5,
      moveSlides: 3,
      slideMargin: 8,
      pager:false,
      onSliderLoad:function(){
            if($('.hero-info-color ul li').length > 3){
                $('.goods-detail-hero .hero-info-color').css('padding-left','15px');
            }
        }
    });
	
	// 상품상세 > 히어로
	$(".goods-detail-hero ul.image-list").bxSlider({
        infiniteLoop: true,
		auto: true,
		speed: 300, 
        hideControlOnEnd: true
	});
	
	// 상품상세 > 포스팅
	$(".goods-detail-posting ul").each(function() {
		
		var $ui = $(this);
		$ui.masonry({ columnWidth:283, gutter:25, itemSelector:".goods-detail-posting li" });
		$ui.imagesLoaded().progress(function() {
			
			$ui.masonry("layout");
			
		});
		
	});
	
	// 상품상세 > 리뷰
	//$(".goods-detail-review table.board").accordion({ menu:".btn-toggle", isParentOn:true });
	
	// 상품상세 > Q&A
	$(".goods-detail-qa table.board").accordion({ menu:".btn-toggle", isParentOn:true });
	
	// 푸터 리뷰 리스트
	$(".footer-review").each(function() {
		
		var $ui = $(this);
		var $list = $ui.children("ul");
		var $arrow = $ui.find(".btn-arrow-prev, .btn-arrow-next");
		var listSlide;
		var listTotal = $list.children().length;
		
		slide_bind();
		
		$(window).on("load resize", function(_e) {
			
			slide_bind();
			
		});
		
		$arrow.on("click", function(_e) {
			
			_e.preventDefault();
			if ($(this).hasClass("btn-arrow-prev")) listSlide.goToPrevSlide();
			else listSlide.goToNextSlide();
			
		});
		
		function slide_bind() {
			
			var slides = Math.round(6 + ($(window).width() - 1024) * 0.0066964);
			var margin = ($ui.width() - (slides * 115)) / (slides - 1);
			var option = { slideWidth:115, maxSlides:slides, slideMargin:margin, pager:false, controls:false };
			if (listSlide == undefined) listSlide = $list.bxSlider(option);
			else listSlide.reloadSlider(option);
			
			if (slides == listTotal) $arrow.hide();
			else $arrow.show();
			
		}
		
	});

	
	// 핫포스팅/핫리뷰/핫트 탭 설정
	var currentTab1 = $('.main-community-tab .on');
	var tabContents1 = $('.main-community-content');
	$('.main-community-tab>li').on('click', function(){
		if(currentTab1[0])
		{
			currentTab1.removeClass('on');
            tabContents1.eq(currentTab1.index()).removeClass('on');
		}
		$(this).addClass('on');
        tabContents1.eq($(this).index()).addClass('on');
        $(".main-community-content").trigger("COMMUNITY_RESET");
		currentTab1 = $(this);
	});

    // 베스트셀러/엠디픽/뉴어레이
    var currentTab2 = $('.rec-tab .on');
    var tabContents2 = $('.rec-content');
    $('.rec-tab>li').on('click', function(){
        if(currentTab2[0])
        {
            currentTab2.removeClass('on');
            //tabContents2.eq(currentTab2.index()).removeClass('on');
        }
        $(this).addClass('on');
        //tabContents2.eq($(this).index()).addClass('on');
        //$('rec-content').trigger('resize');
        currentTab2 = $(this);
    });

	 // 브랜드 > 상단 비쥬얼
    $('.brand_visual ul').bxSlider({
        infiniteLoop: true,
        hideControlOnEnd: true,
		auto: true,
		speed: 500
    });

	 // 프로모션 > 이벤트 상단 비쥬얼
    $('.visual_img ul').bxSlider({
        infiniteLoop: true,
        hideControlOnEnd: true,
        controls:false
    });
	
	// 포럼 > 컨텐츠 슬라이더
    $('.recommend-rolling > ul').bxSlider({
        infiniteLoop: true,
        hideControlOnEnd: true,
		slideWidth: 918,
		pagerType: 'short'
	});

	// 매거진 > 비쥬얼 슬라이더
    $('.magazine-visual > ul').bxSlider({
        infiniteLoop: true,
		auto: true,
		speed: 500, 
        hideControlOnEnd: true
	});

	// 매거진 > 상세 비쥬얼 슬라이더
    $('.magazine-detail > ul').bxSlider({
        infiniteLoop: true,
		hideControlOnEnd: true,
		pager:false,
		controls:false
	});

	

/*	
	//룩북 상세 > 썸네일 슬라이더
	$('.lookbook-slider-thumb .thumb-img').bxSlider({
		infiniteLoop: false,
		hideControlOnEnd: true,
		slideWidth: 132,
		slideMargin: 8,
		minSlides: 8,
		maxSlides: 8,
		moveSlides:8,
		pager: false
	});

	//룩북 상세 > 배너이미지 슬라이더
	$('#lookbookSliderBanner').bxSlider({
		infiniteLoop: false,
		pagerCustom: '#bxPager',
	});
*/
	
	//컴포넌트 > 기간 설정 버튼
	var btn_dateMonth = $('.date-sort .month button');

	btn_dateMonth.click(function(){
		btn_dateMonth.removeClass('on');
		$(this).addClass('on');
	});


	//컴포넌트 > 하위 TR 열고 닫기
	var nextTr_open = $('.tr-open');
	var nextTr_close = $('.tr-close');

	nextTr_open.click(function(){
		$(this).parents('tr').next().toggle();
	});

	nextTr_close.click(function(){
		$(this).parents('tr').attr('css', {display:'table-row'}).hide();
	});


	//마이페이지 LNB
	var myLnb_C1 = $('.s_menu > li > a');
	
	myLnb_C1.on('click', function(){
			myLnb_C1.removeClass('on');
			$(this).addClass('on');
		});

	//마이페이지 > 탭메뉴 공통
	var myTab_menu = $('.my-tab-menu li');
	var myTab_content = $('.tab-menu-content');

	myTab_menu.on('click',function(e){
		var idx = myTab_menu.index($(this));
		
		myTab_menu.removeClass('on');
		myTab_content.removeClass('on');

		myTab_menu.eq(idx).addClass('on');
		myTab_content.eq(idx).addClass('on');
	});

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


	// 마이페이지 좋아요 시작
	/*if($('.mypage-community-content')[0])
	{
		var listLen = 0;

		for(var i=0;i<$('.mypage-community-content>ul>li>a>figure>img').length;i++)
		{
			$('.mypage-community-content>ul>li>a>figure>img').eq(i).attr("src", $('.mypage-community-content>ul>li>a>figure>img').eq(i).attr("src"));
		}

		$('.mypage-community-content>ul>li>a>figure>img').on('load', function(){
			listLen++;
			if(listLen == $('.mypage-community-content>ul>li').length)
			{
				$('.mypage-community-content>ul').masonry({
					itemSelector: '.mypage-community-content>ul>li'
				});
			}
		});
	}*/

	// 비대칭 리스트 시작
	if($('.asymmetry_list')[0])
	{
		var listLen = 0;

		for(var i=0;i<$('.asymmetry_list>ul>li>figure>a>img').length;i++)
		{
			$('.asymmetry_list>ul>li>figure>a>img').eq(i).attr("src", $('.asymmetry_list>ul>li>figure>a>img').eq(i).attr("src"));
		}

		$('.asymmetry_list>ul>li>figure>a>img').on('load', function(){
			listLen++;
			if(listLen == $('.asymmetry_list>ul>li').length)
			{
				$('.asymmetry_list>ul').masonry({
					itemSelector: '.asymmetry_list>ul>li'
				});
			}
		});
	}
	//스토어 스토리 > 탭메뉴
    var stTab_menu = $('.store_tab ul li');
    var stTab_content = $('.store-menu-content');

    stTab_menu.on('click',function(e){
        var idx = stTab_menu.index($(this));

        stTab_menu.removeClass('on');
        stTab_content.removeClass('on');

        stTab_menu.eq(idx).addClass('on');
        stTab_content.eq(idx).addClass('on');

        $('.asymmetry_list>ul').masonry({
            itemSelector: '.asymmetry_list>ul>li'
        });
    });

	//리플 리스트
   $('.reply-list .answer-reply').click(function(){
      $(this).toggleClass('on');
      $(this).next().toggle();
   })
   
   $('.reply_comment .answer .buttonset').on('click', function(){
      if($(this).parent().next().find('.review_comment_form').css('display') == 'none')
      {
         $(this).parent().next().find('.review_comment_form').css('display', 'block');
      }else{
         $(this).parent().next().find('.review_comment_form').css('display', 'none');
      }
   });
   
   $('.review_comment_form').find('.cancel').on('click', function(){
      $(this).parent().parent().css('display','none');
   });

	// 첨부파일 등록 시 사용
	$('.add-photo input[type=file]').on('change', function(){
		readURL(this);
	});
	function browser() {
		var s = navigator.userAgent.toLowerCase();
		var match = /(webkit)[ \/](\w.]+)/.exec(s) ||
				/(opera)(?:.*version)?[ \/](\w.]+)/.exec(s) ||
				/(msie) ([\w.]+)/.exec(s) ||
				!/compatible/.test(s) && /(mozilla)(?:.*? rv:([\w.]+))?/.exec(s) || [];
		return { name: match[1] || "", version: match[2] || "0" };
	}
	function readURL(input) {
		var tg = input;
		if (input.files && input.files[0]) {
			var reader = new FileReader();
			reader.onload = function (e) {
				var img = '<p style="background:url('+ e.target.result +') center no-repeat; background-size:contain"></p>';
				$(tg).parent().prepend(img);

				var btn = $(tg).parent().prepend('<button type="button">삭제</button>');
				$(btn).on('click',function(){
					$(this).find('p').remove();
					$(this).find('button').remove();
					if (parseInt(browser().version) > 0) {
						// ie 일때 input[type=file] init.
						$(this).find('input[type=file]').replaceWith( $(this).find('input[type=file]').clone(true) );
					} else {
						// other browser 일때 input[type=file] init.
						$(this).find('input[type=file]').val("");
					}
				});
			}
			reader.readAsDataURL(input.files[0]);
		}
	}

	// 팝업 > 증명서 발급 > 현금영수증 발급
	$('.receipt_box p input[type=radio]').on('change', function(){
		//console.log($('.receipt_box p input[type=radio]:checked').index())
		$('.receipt_box p.mobile,.receipt_box p.license').css('display','none');
		if($('.receipt_box p input[type=radio]:checked').index()==0)
		{
			$('.receipt_box p.mobile').css('display','block');
		}else{
			$('.receipt_box p.license').css('display','block');
		}
	});

	$('.receipt_box p input[type=radio]').trigger('change');
	
	//레이어팝업
	var layerWidth = $('.layer-dimm-wrap .layer-inner').width();
	var resizeLayerPop = $('.layer-dimm-wrap .layer-inner');
	var layerPopClose = $('.layer-dimm-wrap .btn-close');
	var layerPopWrap = $('.layer-dimm-wrap');
	
	layerPopWrap.hide();
	//resizeLayerPop.css({"margin-left":"-" + ((layerWidth / 2) + 15) + "px"});

	//레이어팝업 공통 닫기
	layerPopClose.click(function(){
		layerPopWrap.fadeOut();
	});


	// 레이어팝업 오픈 모음 - 아래 해당부분에는 레이어팝업 오픈만 사용합니다.
	$('.btn-agree-detail1').click(function(){
		$('.pop-agree-detail1').fadeIn();
		return false;
	});

	$('.btn-take-back').click(function(){
		$('.pop-take-back').fadeIn();
		return false;
	});

	$('.btn-pop-address').click(function(){
		$('.pop-address-add').fadeIn();
		return false;
	});
    /* 2016-08-19 jhjeong
	$('.btn-qna-detail').click(function(){
		$('.pop-qna-detail').fadeIn();
		return false;
	});

	$('.btn-review-detail').click(function(){
		$('.pop-review-detail').fadeIn();
		return false;
	});
    */
	
	$('.btn-file-view').click(function(){
		$('.pop-file-view').fadeIn();
		return false;
	});

	$('.btn-cash-receipt').click(function(){
		$('.pop-cash-receipt').fadeIn();
		return false;
	});
	
	$('.btn-tax-invoice').click(function(){
		$('.pop-tax-invoice').fadeIn();
		return false;
	});

	$('.btn-exchange').click(function(){
		$('.pop-exchange').fadeIn();
		return false;
	});
	
	$('.btn-address-list').click(function(){
		$('.pop-address-list').fadeIn();
		return false;
	});

	$('.btn-coupon-list').click(function(){
		$('.pop-coupon-list').fadeIn();
		return false;
	});

	$('.btn-standing-point').click(function(){
		$('.pop-standing-point').fadeIn();

        // 창 뜰때 초기화하기 2016-08-23 jhjeong
        $('#storeLocWriteForm').each(function(){
             this.reset();
             $('#storeLocWriteForm').find('.txt-box').html('');
        });
		//return false;
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
	
	$('.btn-photo-write').click(function(){
		$('.pop-photo-write').fadeIn();
		return false;
	});

	$('.btn_stock_detail').click(function(){
		$('.pop_stock_detail').fadeIn();
		return false;
	});

	$('.loading-show').click(function(){
		$('.dimm-loading').fadeIn();
	});


	/*$('.btn-view-detail').click(function(){
		$('.pop-view-detail').fadeIn();
		return false;
	});*/
	

 });

 /* ==================================================
	커스텀 플러그인
================================================== */
(function($) {
	
	// 아코디언 컨텐츠
	$.fn.accordion = function(_option) {

		return this.each(function() {

			var option = {
				menu:".js-accordion-menu",
				content:".js-accordion-content",
				isParentOn:false, // content 대신 부모 요소에 on클래스 추가
				completeHandler:null
			}

			var $ui = $(this);
			if ($ui.data("accordion")) return;
			$ui.data("accordion", true);

			if (typeof _option === "object") $.extend(option, _option);

			var $menu = $ui.find(option.menu);
			var menuNum = $menu.index($menu.filter(".on"));
			if (menuNum == -1) menuNum = undefined;

			function menu_change() {

				$ui.find(option.menu).removeClass("on").attr("title", "펼쳐보기").eq(menuNum).addClass("on").attr("title", "접어놓기");
				if (!option.isParentOn) $ui.find(option.content).removeClass("on").eq(menuNum).addClass("on");
				else $ui.find(option.menu).parent().removeClass("on").eq(menuNum).addClass("on");
				
				if (option.completeHandler != null) option.completeHandler();

			}

			$ui.on("click", option.menu, function(_e) {

				_e.preventDefault();
				menuNum = ($(this).hasClass("on")) ? undefined : $ui.find(option.menu).index(this);
				menu_change();

			});
			menu_change();
			
			// 이벤트 트리거
			$ui.on({
				"accordion_getMenuNum":function(_e, _data) {
					_e.stopImmediatePropagation();
					return menuNum;
				}
			});

		});

	}
	
})(jQuery);
	
// 인스타그램 상세 이미지 리사이징
function popupImgResize()
{
	var nw = $('.img-area img')[0].naturalWidth;
	var nh = $('.img-area img')[0].naturalHeight;
  if(nw >= nh)
	{
		$('.img-area').css({lineHeight:$('.img-area').height()+'px'});
		$('.img-area img').css({width:'100%', height:'auto', position:'relative', top:'50%', transform:'translateY(-50%)'});
	}else{
		$('.img-area').css({textAlign:'center'});
		$('.img-area img').css({width:'auto', height:'100%'});
	}
}
