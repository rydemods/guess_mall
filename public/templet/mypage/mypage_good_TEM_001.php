<?
## 좋아요 전체 갯수구하기
$sql = "Select count(*) as cnt From tblhott_like Where like_id = '".$_ShopInfo->getMemid()."'";
list($cnt_all) = pmysql_fetch($sql, get_db_conn());
$instaimgpath = $Dir.DataDir."shopimages/instagram/";
$productimgpath = $Dir.DataDir."shopimages/product/";
//exdebug($cnt_all);
?>

<div id="contents">
	 <!-- 네비게이션 -->
	<div class="top-page-local">
		<ul>
			<li><a href="/">HOME</a></li>
			<li><a href="<?=$Dir?>front/mypage.php">마이 페이지</a></li>
			<li class="on">좋아요</li>
		</ul>
	</div>
	<!-- //네비게이션-->
	<div class="inner">
		<main class="mypage_wrap"><!-- 페이지 성격에 맞게 클래스 구분 -->

			<!-- LNB -->
			<? include  "mypage_TEM01_left.php";  ?>
			<!-- //LNB -->

			<article class="mypage_content">
				<!-- 상품리스트 -->
				<section class="mypage_main like-list-item">
				    <div class="title_type1"><h3>좋아요</h3></div>
					<ul class="my-tab-menu">
						<li class="on"><a href="javascript:void(0);">ALL (<span id="cnt_all">0</span>)</a></li>
						<li class=""><a href="javascript:void(0);">상품</a></li>
						<li class=""><a href="javascript:void(0);">매거진</a></li>
						<li class=""><a href="javascript:void(0);">룩북</a></li>
						<li class=""><a href="javascript:void(0);">인스타그램</a></li>
						<li class=""><a href="javascript:void(0);">포럼</a></li>
						<li class=""><a href="javascript:void(0);">STORE STORY</a></li>
					</ul>
				    <!-- ALL컨텐츠 영역 -->
					<div class="tab-menu-content on" id="tab_menu_all">
					</div>
					<!-- // ALL컨텐츠 영역 -->

					<!-- 상품컨텐츠 영역 -->
					<div class="tab-menu-content" id="tab_menu_pdt">
					</div>
					<!-- // 상품컨텐츠 영역 -->

					<!-- 매거진 영역 -->
					<div class="tab-menu-content" id="tab_menu_mgz">
					</div>
					<!-- // 매거진 영역 -->

					<!-- 룩북 영역 -->
					<div class="tab-menu-content" id="tab_menu_lbk">
					</div>
					<!-- // 룩북 영역 -->

					<!-- 인스타 영역 -->
					<div class="tab-menu-content" id="tab_menu_ins">
					</div>
					<!-- // 인스타 영역 -->

					<!-- 포럼 영역 -->
					<div class="tab-menu-content" id="tab_menu_frm">
					</div>
					<!-- // 포럼 영역 -->


					<!-- STORE STORY 영역 -->
					<div class="tab-menu-content" id="tab_menu_sts">
					</div>
					<!-- // STORE STORY 영역 -->
				</section>
                <!-- 상품리스트 -->
			</article>
		</main>
	</div>
</div><!-- //#contents -->

<!-- [D] 인스타그램_상세보기 팝업 -->
<div class="layer-dimm-wrap pop-view-detail CLS_instagram"> <!-- .layer-class 이부분에 클래스 추가하여 사용합니다. -->
	<div class="dimm-bg"></div>
	<div class="layer-inner">
		<button type="button" class="btn-close">창 닫기 버튼</button>
		<div class="layer-content">
			<div class="img-area">
				<img src="../static/img/test/@test_instagram_view01.jpg" alt="" id="instagram_img">
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

<script type="text/javascript">
var memId = "<?=$_ShopInfo->getMemid()?>";
var idx = 0;
$(document).ready(function() {

    // 최초 로딩시
    getLikeList(0, 0, 1);

    $('.my-tab-menu li').on('click', function(e){
        idx = $('.my-tab-menu li').index($(this));
        getLikeList(idx, 0, 1);
    });

	$(document).on('click', '.btn-view-detail', function(){
		$('.CLS_instagram').fadeIn();
	});

	$(document).on('click', '.btn-close', function(){
		reset();
	});
});

function getLikeList(idx, block, gotopage){

    //console.log( idx );
    var n = 0;
    $.ajax({
        type: "get",
        url: "ajax_hott_like_list.php",
        data: "section="+idx+"&block="+block+"&gotopage="+gotopage,
        //data: param,
        dataType: "html",
        async: false,
        cache: false,
        success: function(data) {
            //console.log(data);
            $('.tab-menu-content').eq(idx).html(data);

            // ALL 탭 영역의 카운트값을 따로 안구하고 li 갯수 구해서 세팅.
            n = $('#tab_menu_all li').size();
            //console.log(n);
            $('#cnt_all').text(n);
        },
        error: function(result) {
            alert(result.status + " : " + result.description);
            //alert("오류 발생!! 조금 있다가 다시 해주시기 바랍니다.");
        }
    });
}

function GoPage(qblock, qgotopage) {
    //console.log( idx );
    getLikeList(idx, qblock, qgotopage);
}

//인스타그램 상세정보
function detailView(idx){
	$.ajax({
		type: "POST",
		url: "ajax_instagram_detail.php",
		data: "idx="+idx,
		dataType:"JSON"
	}).done(function(data){
		console.log(data);
		reset();
		var tag = "";
		if(data != null){
			if(data[0]['hash_tags'] != 0){
				var arrTag = data[0]['hash_tags'].split(",");
	    		$.each( arrTag, function( i, v ){
	    			tag += " #"+$.trim(v);
	  			  $(".tag").text(tag);
	  		    });
			}
			if(data[0]['relation_product'] != 0){
				var arrRelation = data[0]['relation_product'].split(",");
			}
			if(data[0]['productname'] != 0){
				var arrProdName = data[0]['productname'].split(",");
			}
			if(data[0]['brandname'] != 0){
				var arrBrandName = data[0]['brandname'].split(",");
			}
			if(data[0]['brandimage'] != 0){
				var arrProdImage = data[0]['brandimage'].split(",");
			}	
			var html =  "";
			if(data[0]['relation_product'] != ""){
	    		$.each( arrProdName, function( i, v ){
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
			$("#content").html(data[0]['content']); // HTML 로 보이도록 수정 (2016.11.02 - peter.Kim)
			$("#instagram_img").attr("src","<?=$instaimgpath ?>"+data[0]['img_file']+"");
	
			if(data[0]['section'] == null){
				$(".title").append('<button class="comp-like btn-like detail-like like_i'+idx+'"  onclick="detailSaveLike(\''+idx+'\',\'off\',\'instagram\',\''+memId+'\',\'\')" id="likedetail_'+idx+'" title="선택 안됨"><span class="like_icount_'+idx+'"><strong>좋아요</strong>'+data[0]['hott_cnt']+'</span></button>');
			}else{
				$(".title").append('<button class="comp-like btn-like detail-like like_i'+idx+' on " onclick="detailSaveLike(\''+idx+'\',\'on\',\'instagram\',\''+memId+'\',\'\')" id="likedetail_'+idx+'" title="선택됨"><span class="like_icount_'+idx+'"><strong>좋아요</strong>'+data[0]['hott_cnt']+'</span></button>');
			}
	    	$(".view-prev").attr("href","javascript:pagePrev(\""+data[0]['pre_idx']+"\")");
	    	$(".view-next").attr("href","javascript:pageNext(\""+data[0]['next_idx']+"\")");
		}
	});
}

</script>