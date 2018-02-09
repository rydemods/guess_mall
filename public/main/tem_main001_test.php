<?php
/*********************************************************************
// 파 일 명		: tem_main001.php
// 설     명	: 메인 템플릿
// 상세설명	    : 메인 템플릿
// 작 성 자		: 2015.11.02 - 김재수
// 수 정 자		: 2016.07.28 - 김재수
//
//
*********************************************************************/
?>

<?include ($Dir.MainDir.$_data->menu_type.".php");?>
<?
$instaimgpath = $Dir.DataDir."shopimages/instagram/";
$productimgpath = $Dir.DataDir."shopimages/product/";
//메인 상단 배너(이미지)
$imagepath = $Dir.DataDir."shopimages/mainbanner/";
$banner_img_sql = "SELECT * FROM tblmainbannerimg WHERE banner_hidden = 1 AND banner_no = 113 AND banner_type = 0";
$banner_img_sql .= " ORDER BY banner_date DESC limit 3";
$baner_img_result = pmysql_query($banner_img_sql);
while ( $row = pmysql_fetch_array($baner_img_result) ) {
	$arrMainImgBanner[] = $row;
}
//메인 상단 배너(동영상)
$clip_sql = "SELECT * FROM tblmainbannerimg WHERE banner_hidden = 1 AND banner_no = 113 AND banner_type = 1";
$clip_result = pmysql_query($clip_sql);
if($row=pmysql_fetch_object($clip_result)) {
	$mainClipBanner = $row;
}

//메인 중단 배너
$banner_img2_sql = "SELECT * FROM tblmainbannerimg WHERE banner_hidden = 1 AND banner_no = 114 AND banner_type = 0";
$banner_img2_sql .= " ORDER BY banner_date DESC  limit 1";
$baner_img_result = pmysql_query($banner_img2_sql);
if($row=pmysql_fetch_object($baner_img_result)) {
	$mainImgBanner2 = $row;
}
preg_match_all('/src=\"(.[^"]+)"/i', $mainImgBanner2->banner_img, $src);

//베스트 포토 리뷰
$sql = "SELECT b.minimage, a.id,a.name,a.reserve,a.display,a.subject,a.content,a.date,a.productcode,
			b.productname,b.tinyimage,b.selfcode,b.assembleuse, a.upfile, a.best_type, a.marks, a.type, a.num
			FROM tblproductreview a, tblproduct b, (SELECT c_productcode,c_category FROM tblproductlink WHERE c_maincate = 1 ) c
			WHERE a.productcode = b.productcode AND a.productcode = c.c_productcode AND a.type = '1' ORDER BY a.best_type desc, a.date DESC";
$result = pmysql_query($sql);
while ( $row = pmysql_fetch_array($result) ) {
	$photoReviewList[] = $row;
}
?>

<!-- 유투브 컨트롤 추가 -->
    <script>

        var tag = document.createElement("script");
        tag.src = "https://www.youtube.com/iframe_api";
        var firstScriptTag = document.getElementsByTagName("script")[0];
        firstScriptTag.parentNode.insertBefore(tag, firstScriptTag);

        var playerA = [];
        var playerIndex = 0;

        function onYouTubePlayerAPIReady() {

            $(".youtube-toggle").each(function(i) {

                var $frame = $(this).prev().data("index", i);
                var id = $frame.attr("id");
                playerA[i] = new YT.Player(id, {
                    events: {
                        "onReady": onPlayerReady
                    }
                });

            });

        }

        function onPlayerReady(obj){

            var $frame = $(obj.target.a);
            var player = playerA[$frame.data("index")];

            $frame.next().off("click").on("click", function(_e) {

                playerIndex = $frame.data("index");
                player.playVideo();
                $(this).hide();

                player.removeEventListener("onStateChange", onPlayerStateChange);
                player.addEventListener("onStateChange", onPlayerStateChange);

            });

            $(player).on("mousewheel DOMMouseScroll", function(_e) {

    			_e.preventDefault();
    			var delta = (_e.type == "mousewheel") ? _e.originalEvent.wheelDelta : -_e.originalEvent.detail;
    			var direction = (delta < 0) ? "next" : "prev";
    			console.log(direction)

    		});

        }

        function onPlayerStateChange(event) {

            if (event.data == YT.PlayerState.PLAYING) {
                $(".youtube-toggle").hide();
            } else {
                $(".youtube-toggle").show();
            }

        }

        function youtubeAllStop() {

            for (var i = 0; i < playerA.length; i++) {
                playerA[i].stopVideo();
            }

        }
    </script>

<!-- main #container -->
<main id="contents" class="main">

<div class="main-inner">
		<!-- 메인 - 히어로 -->
		<div class="main-section main-hero">
			 <!--
				(D) 이미지는 background-image:url()로 연결합니다.
				유투브 iframe 위에서 마우스 휠 이벤트가 동작하지 않아 위에 div를 하나 덮어주고 해당 div 클릭으로 유투브 영상이 플레이 되도록 외부 스크립트를 추가 했습니다.
				유투브 iframe에 id를 각각 다르게 넣어주고, src 마지막에 enablejsapi=1를 추가해줘야 스크립트가 적용됩니다.
			-->
			<?if(count($arrMainImgBanner) > 0){?>
				<ul class="main-hero-slide">
				<?foreach( $arrMainImgBanner as $key=>$val ){ ?>
					<li style="background-image:url('<?=$imagepath.$val['banner_img'] ?>');">
						<a href="<?=banner_link ?>"></a>
						<div class="info">
	<!-- 						<p><strong>NIKE AIR<br>PRESTO QS</strong><br>CARGO KHAKI</p> -->
	<!-- 						<a href="">BUY NOW</a> -->
						</div>
					</li>
				<?} ?>
				</ul>
			<?} ?>
			<?if(count($mainClipBanner) > 0){ ?>
			<ul>
				<li><?=$mainClipBanner->banner_img ?></li>
			</ul>
			<?} ?>
		</div>
		<!-- // 메인 - 히어로 -->

		<!-- 메인 - 커뮤니티 -->
		<div class="main-section main-community">
			<!-- (D) 선택된 탭메뉴 li에 class="on" title="선택됨"을 추가합니다. -->
			<ul class="main-community-tab">
				<li class="on" title="선택됨"><a href="javascript:postingList();">핫포스팅</a></li>
				<li><a href="javascript:reviewList();">핫리뷰</a></li>
				<li><a href="javascript:likeList();">핫트</a></li>
			</ul>
			<!-- 핫포스팅 영역-->
			<div class="main-community-content on">
				<!--
					(D) 탭 선택 시
					1. ajax로 아래 내용을 다시 로드할 경우,
					ul요소는 그대로 두고 li요소만 교체한 후,
					$(".main-community-content").trigger("COMMUNITY_RESET");를 호출하면 리스트 정렬을 초기화합니다.

					2. 컨텐츠를 페이지 접속시 3개 탭 내용을 전부 불러오고 display:none/block 으로 제어할 경우,
					.main-community-content 요소를 3개로 복제하여 사용하고,
					탭 변경할 때 정렬이 원활하지 않으면
					탭 선택할 때 $(".main-community-content").trigger("COMMUNITY_RESET");를 함께 호출해주세요.]

					좋아요를 선택하면 버튼에 class="on" title="선택됨"을 추가합니다.
				-->
				<ul class="comp-posting hot-posting">
					<li></li>
				</ul>
				<a class="btn-arrow-prev" href="javascript:void(0);"><img src="../static/img/btn/btn_arrow_prev.png" alt="이전"></a>
				<a class="btn-arrow-next" href="javascript:void(0);"><img src="../static/img/btn/btn_arrow_next.png" alt="다음"></a>
			</div>
			<!-- // 핫포스팅 영역-->

			<!-- 핫리뷰 영역-->
			<div class="main-community-content">
				<ul class="comp-posting hot-review">
					<li></li>
				</ul>
				<a class="btn-arrow-prev" href="javascript:void(0);"><img src="../static/img/btn/btn_arrow_prev.png" alt="이전"></a>
				<a class="btn-arrow-next" href="javascript:void(0);"><img src="../static/img/btn/btn_arrow_next.png" alt="다음"></a>
			</div>
			<!-- // 핫리뷰 영역-->

			<!-- 핫트 영역-->
			<div class="main-community-content">
				<ul class="comp-posting hot-like">
					<li></li>
				</ul>
				<a class="btn-arrow-prev" href="javascript:void(0);"><img src="../static/img/btn/btn_arrow_prev.png" alt="이전"></a>
				<a class="btn-arrow-next" href="javascript:void(0);"><img src="../static/img/btn/btn_arrow_next.png" alt="다음"></a>
			</div>
			<!-- // 핫트 영역-->
		</div>
		<!-- // 메인 - 커뮤니티 -->

		<!-- 메인 - 상품 -->
		<div class="main-section main-item">
			<!-- (D) 이미지는 background-image:url()로 연결합니다. -->
			<a href="" style="background-image:url(<?=$src[1][0] ?>);">
				<!--
				<div class="main-item-content">
					<strong class="brand">NIKE</strong>
					<p class="title">울프 그레이 에어 풋스케이프 마지스타 스니커즈</p>
					<ul>
						<li><img src="../static/img/test/@test_main_item_thumb1.jpg" alt=""></li>
						<li><img src="../static/img/test/@test_main_item_thumb2.jpg" alt=""></li>
						<li><img src="../static/img/test/@test_main_item_thumb3.jpg" alt=""></li>
						<li><img src="../static/img/test/@test_main_item_thumb4.jpg" alt=""></li>
					</ul>
					<p class="desc">
						혁신적인 스니커 아이콘. 플라이니트로 돌아오다..수많은 프로토타입을 거쳐 마침내 탄생한 Air Max 1.
						토우 및 설포에는 플라이니트의 짜임을 성기게 하여 통기성을 극대화하였으며, 힐 부분에는 촘촘한 짜임으로 지지력을 더했습니다.
						중창 바로 위에 본딩된 소재를 적용하여 오리지널 디자인을 재현했으며,
						가볍고 유연한 울트라 툴링으로 쿠셔닝 기능이 향상되어 하루 종일 편안한 착화감을 선사합니다.
						남성용 &#38; 여성용 사이즈로 출시되는 이번 제품은 7월 28일부터 Nike.com에서 만나보실 수 있습니다.
					</p>
				</div>
				-->
			</a>
		</div>
		<!-- // 메인 - 상품 -->

		<!-- 메인 - 리스트 -->
		<div class="main-section main-list">
			<div class="main-list-rec">
				<!-- (D) 선택된 탭메뉴 li에 class="on" title="선택됨"을 추가합니다. -->
				<ul class="rec-tab">
					<li><a href="javascript:mainItemList('best');">Best Sellers</a></li>
					<li class="on" title="선택됨"><a href="javascript:mainItemList('md');">MD's Pick</a></li>
					<li><a href="javascript:mainItemList('new');">New Arrivals</a></li>
				</ul>
				<!-- Best Sellers
				 <div class="rec-content">
					<ul class="rec-list best-list">

					</ul>
				</div>
				 // Best Sellers -->

				<!-- MD's Pick -->
				<div class="rec-content on">
					<ul class="rec-list mdpick-list">
					</ul>
				</div>
				<!-- // MD's Pick -->

				<!-- New Arrivals
				<div class="rec-content">
					<ul class="rec-list new-list">

					</ul>
				</div>
				 // New Arrivals -->
			</div>
			<div class="main-list-item">
				<!--
					(D) 별점은 .comp-star > strong에 width:n%로 넣어줍니다.
					좋아요를 선택하면 버튼에 class="on" title="선택됨"을 추가합니다.
				-->
				<ul class="comp-goods item-list bottom-item-list">


				</ul>
				<a class="btn-arrow-prev" href="javascript:void(0);"><img src="../static/img/btn/btn_arrow_prev.png" alt="이전"></a>
				<a class="btn-arrow-next" href="javascript:void(0);"><img src="../static/img/btn/btn_arrow_next.png" alt="다음"></a>
			</div>
		</div>
		<!-- // 메인 - 리스트 -->
		<!-- [D] 인스타그램_상세보기 팝업 -->
		<div class="layer-dimm-wrap pop-view-detail CLS_instagram"> <!-- .layer-class 이부분에 클래스 추가하여 사용합니다. -->
			<div class="dimm-bg"></div>
			<div class="layer-inner">
				<button type="button" class="btn-close">창 닫기 버튼</button>
				<div class="layer-content">
					<div class="img-area">
						<img src="" alt="" id="instagram_img">
					</div>
					<div class="cont-area">
						<div class="title">
							<h3><span class="pl-10"><!-- <img src="" alt="instagram"> --></span></h3>
							<!--  <button class="comp-like btn-like" title="선택 안됨"><span id="like_count"></button> <!-- // [D] 좋아요를 선택하면 버튼에 class="on" title="선택됨"을 추가 -->
						</div>
						<div class="cont-view">
							<div class="inner">
								<p id="content"></p>
								<p class="tag" id="instagram_tag">
		<!-- 							#hott #hottest #nike #airjordan #Jordan #shoes #fashion #item #ootd #dailylook #핫티 #나이키 #에어조던 #조던 #신발 #패션 #아이템 #데일리 #데일리룩 #데일리슈즈 #신스타그램 #슈스타그램 #daily #dailyshoes #shoestagram -->
								</p>
							</div>
						</div>
						<div class="goods-detail-related">
							<h3>관련 상품</h3>
							<ul class="related-list">
		<!--
								<li>
									<a href="javascript:;">
										<figure>
											<img src="../static/img/test/@test_instagram_wish01.jpg" alt="관심상품">
											<figcaption>
												# CONVERSE<br>
												CTAS 70 HI
											</figcaption>
										</figure>
									</a>
								</li> -->

								</li>
							</ul>
						</div> <!-- // .goods-detail-related -->
					</div> <!-- // .cont-area -->
					<!--
					<div class="btn-wrap">
						<a href="javascript:pagePrev();" class="view-prev">이전</a>
						<a href="javascript:pageNext();" class="view-next">다음</a>
					</div>-->
				</div>
			</div>
		</div>
		<!-- // [D] 인스타그램_상세보기 팝업 -->

	</div>
</main>

<!-- // main #container -->
<script type="text/javascript">
var memId = "<?=$_ShopInfo->getMemid()?>";
$(document).ready( function() {
    //$("iframe").css("width","100%");
    //$("iframe").css("height","100%");
	iframeResize();
	postingList();
	mainItemList('md');
	mainBottomItemList();

	$(window).on('resize', function(){
		iframeResize();
	})
});

function iframeResize(){
	var hh = $('.main-hero').height();$('iframe').css('height',hh+'px');
}

$(document).on('click', '.btn-close', function(){
	reset();
});

$(document).on('click', '.btn-view-detail', function(){
	$('.CLS_instagram').fadeIn();
	var idx = $(this).attr("idx");
	detailView(idx)

});

//핫포스팅
function postingList(){
	$.ajax({
		type: "POST",
		url: "../main/ajax_hotlist.php",
		data: "tab=posting",
		dataType:"HTML",
	    error:function(request,status,error){
	       alert("code:"+request.status+"\n"+"message:"+request.responseText+"\n"+"error:"+error);
	    }
	}).done(function(html){
		$(".hot-posting").html(html);
		$(".main-community-content").trigger("COMMUNITY_RESET");
	});
}

//핫리뷰
function reviewList(){

	$.ajax({
		type: "POST",
		url: "../main/ajax_hotlist.php",
		data: "tab=review&type=pc",
		dataType:"HTML",
	    error:function(request,status,error){
	       alert("code:"+request.status+"\n"+"message:"+request.responseText+"\n"+"error:"+error);
	    }
	}).done(function(html){
		$(".hot-review").html(html);
		$(".main-community-content").trigger("COMMUNITY_RESET");
	});
}

//핫트
function likeList(){
	$.ajax({
		type: "POST",
		url: "../main/ajax_hotlist.php",
		data: "tab=like&type=pc",
		dataType:"HTML",
	    error:function(request,status,error){
	       alert("code:"+request.status+"\n"+"message:"+request.responseText+"\n"+"error:"+error);
	    }
	}).done(function(html){
		$(".hot-like").html(html);
		$(".main-community-content").trigger("COMMUNITY_RESET");
	});
}

//메인 중단 상품
function mainItemList(category_type){
	$.ajax({
		type: "POST",
		url: "../main/ajax_product_mainitem.php",
		data: "category_type="+category_type+"&type=pc",
		dataType:"HTML",
	    error:function(request,status,error){
	       alert("code:"+request.status+"\n"+"message:"+request.responseText+"\n"+"error:"+error);
	    }
	}).done(function(html){
		$(".mdpick-list").html(html);
		$(".main-list-rec").trigger('resize');
	});
}

//메인 하단 상품
function mainBottomItemList(){
	$.ajax({
		type: "POST",
		data: "type=pc",
		url: "../main/ajax_product_mainitem_bottom.php",
		dataType:"HTML",
	    error:function(request,status,error){
	       alert("code:"+request.status+"\n"+"message:"+request.responseText+"\n"+"error:"+error);
	    }
	}).done(function(html){
		$(".bottom-item-list").html(html);
		$(".main-list-rec").trigger('resize');
	});
}

//인스타그램 상세정보
function detailView(idx){
	$.ajax({
		type: "POST",
		url: "../front/ajax_instagram_detail.php",
		data: "idx="+idx,
		dataType:"JSON"
	}).done(function(data){
		console.log(data);
		reset();
		var arrTag = data[0]['hash_tags'].split(",");
		var arrRelation = data[0]['relation_product'].split(",");
		var arrProdName = data[0]['productname'].split(",");
		var arrBrandName = data[0]['brandname'].split(",");
		var arrProdImage = data[0]['brandimage'].split(",");
		var html =  "";
		if(data[0]['hash_tags'] != ""){
    		$.each( arrTag, function( i, v ){
			  $(".tag").text("#"+v);
			});
		}
		if(data[0]['relation_product'] != ""){
    		$.each( arrRelation, function( i, v ){
    			html += '<li>';
    			html += '<a href="javascript:prod_detail(\''+v+'\');">';
    			html += '<figure>';
    			html += '<img src="<?=$productimgpath ?>'+arrProdImage[i]+'" alt="관심상품">';
    			html += '<figcaption># '+arrBrandName[i]+'<br>'+arrProdName[i]+' ';
    			html += '</figcaption>';
    			html += '</figure>';
    			html += '</a>';
    			html += '</li>';
				$(".related-list").html(html);
			});
		}
		$("#content").text(strip_tags(data[0]['content']));
		$("#instagram_img").attr("src","<?=$instaimgpath ?>"+data[0]['img_file']+"");

		if(data[0]['section'] == null){
			$(".title").append('<button class="comp-like btn-like detail-like like_i'+idx+'"  onclick="detailSaveLike(\''+idx+'\',\'off\',\'instagram\',\''+memId+'\',\'\')" id="likedetail_'+idx+'" title="선택 안됨"><span class="like_icount_'+idx+'"><strong>좋아요</strong>'+data[0]['hott_cnt']+'</span></button>');
		}else{
			$(".title").append('<button class="comp-like btn-like detail-like like_i'+idx+' on " onclick="detailSaveLike(\''+idx+'\',\'on\',\'instagram\',\''+memId+'\',\'\')" id="likedetail_'+idx+'" title="선택됨"><span class="like_icount_'+idx+'"><strong>좋아요</strong>'+data[0]['hott_cnt']+'</span></button>');
		}
    	$(".view-prev").attr("href","javascript:pagePrev(\""+data[0]['pre_idx']+"\")");
    	$(".view-next").attr("href","javascript:pageNext(\""+data[0]['next_idx']+"\")");

	});
}

</script>
<?php include_once($Dir."lib/bottom.php");?>

</body>

</html>