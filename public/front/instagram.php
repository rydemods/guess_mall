<?php
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");

$member['id']=$_ShopInfo->getMemid();
$member['name']=$_ShopInfo->getMemname();
//var_dump($member);

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<meta http-equiv="Content-Type" content="text/html; charset=euc-kr" />
<meta http-equiv="X-UA-Compatible" content="IE=edge" />
<HEAD>
<!--
<TITLE><?=$_data->shoptitle?> - 이용약관</TITLE>
-->
<TITLE><?=$_data->shoptitle?> - 인스타그램</TITLE>
<META http-equiv="CONTENT-TYPE" content="text/html; charset=EUC-KR">
<META name="description" content="<?=(ord($_data->shopdescription)?$_data->shopdescription:$_data->shoptitle)?>">
<META name="keywords" content="<?=$_data->shopkeyword?>">
<script type="text/javascript" src="<?=$Dir?>lib/lib.js.php"></script>
<?php include($Dir."lib/style.php")?>
</HEAD>

<!--php끝-->
<?php  include ($Dir.MainDir.$_data->menu_type.".php") ?>
<body<?=($_data->layoutdata["MOUSEKEY"][0]=="Y"?" oncontextmenu=\"return false;\"":"")?><?=($_data->layoutdata["MOUSEKEY"][1]=="Y"?" ondragstart=\"return false;\" onselectstart=\"return false;\"":"")?> leftmargin="0" marginwidth="0" topmargin="0" marginheight="0"><?=($_data->layoutdata["MOUSEKEY"][2]=="Y"?"<meta http-equiv=\"ImageToolbar\" content=\"No\">":"")?>
<script type="text/javascript" src="<?=$Dir?>js/instagramAPI.js"></script>
<script type="text/javascript">
var isFirst = true;  //처음인지

$(document).ready(function() {
	var $loader = $("#load_message");
	$loader.show();
	accessInstagramAPI();
	//goListScroll();
});

instaAPI=new instagramAPI('','1447933868',12);

function accessInstagramAPI() {
	instaAPI.getInstagramList();
}

function goListScroll(){

	var last = instaAPI.isLast();
	var load =  instaAPI.isLoaded();
	var $loader = $("#load_message");
	if(load && !last){
		$loader.show();
		accessInstagramAPI();
	}
}
</script>

<script type="text/javascript">
$(document).ready(function(){

	var list=$("#layer-slide-wrap ul li");
	var $list=list.length;

	$(".next").on("click",function(){
		$("#layer-slide-wrap ul.photo-list").animate({
		left:"-=940px"},500,function(){
			$("#layer-slide-wrap ul.photo-list li").eq(0).appendTo($("ul.photo-list"));
			$("ul").css("left","0px")
				});//innerfunc
	});//next
	$(".prev").on("click",function(){
		$("#layer-slide-wrap ul.photo-list li").eq($list-1).prependTo($("ul.photo-list"));
		$("ul").css("left","-940px")
		$("#layer-slide-wrap ul.photo-list").animate({left:"0px"},500)//innerfunc
	});//prev

	$('.layer-photoList p.close').click(function(){
		$('.layer_photoList_wrap').hide();
	});

	$(".more_view").on("click",function(){
		var id = $(this).attr("id");
		alert(id);
// 			$("#more"+id).html('<a href="" class="more" id="">더보기</a>');

// 			$.ajax({
// 				type: "POST",
// 				url: "../front/ajax_instagram_more.php",
// 				data: "idx="+ id,
// 				dataType:"HTML",
// 			}).done(function(html){
// 				console.log(html);
// 			});

// 	});

});

function storyBegins(){
	storyBeginsURL = "<?=$Dir.FrontDir?>"+"storybegins/";
	window.open(storyBeginsURL,"stPop",'height=' + screen.height + ',width=' + screen.width + "fullscreen=yes,scrollbars=no,resizable=no");
}

</script>

<!-- [D] 2016.인스타그램_리스트 퍼블 추가 -->
<div id="contents" class="bg">
	<div class="inner">
		<main class="instagram_wrap">
			<h3>INSTAGRAM</h3>
			<div class="search-form-wrap">
				<div class="form-wrap">
					<form>
						<fieldset class="instagram_search_form">
							<legend>매장검색</legend>
							<input type="text" title="매장검색 검색" name="searchVal" id="searchVal" onclick="this.value='';" value="">
							<button type="submit">검색</button>
						</fieldset>
					</form>
					<div class="my-comp-select" style="width:150px;">
						<select name="" class="required_value" id="">
							<option value="">전체</option>
						</select>
					</div>
				</div>
			</div>
			<section class="asymmetry_main">
				<div class="asymmetry_list">
					<ul class="comp-posting">
						<li>
							<figure>
								<a href="javascript:void(0);" class="btn-view-detail"><img src="../static/img/test/@test_main_community1.jpg" alt=""></a>
								<figcaption>
									<a href="javascript:void(0);">
										<p class="id">@apple15</p>
										<p class="cont">머리부터 발끝까지 핫티 최고 예~ </p>
										<p class="tag">#hott #hottest #nike #airjor</p>
									</a>
									<button class="comp-like btn-like on" title="선택됨"><span><strong>좋아요</strong>159</span></button>
								</figcaption>
							</figure>
						</li>
						<li>
							<figure>
								<a href="javascript:void(0);" class="btn-view-detail"><img src="../static/img/test/@test_main_community2.jpg" alt=""></a>
								<figcaption>
									<a href="javascript:void(0);">
										<p class="id">@dkdfssdf</p>
										<p class="cont">트레이닝 컬렉션으로 계보를 잇는 NikeLab과 크리에이티브 디렉터 리카르도 티시의 콜라보레이션. </p>
										<p class="tag">#나이키 #아디다스 #뉴발란스</p>
									</a>
									<button class="comp-like btn-like"><span><strong>좋아요</strong>55</span></button>
								</figcaption>
							</figure>
						</li>
						<li>
							<figure>
								<a href="javascript:void(0);" class="btn-view-detail"><img src="../static/img/test/@test_main_community3.jpg" alt=""></a>
								<figcaption>
									<a href="javascript:void(0);">
										<p class="id">@stra</p>
										<p class="cont">트레이닝 컬렉션으로 계보를 잇는 NikeLab과 크리에이티브 디렉터 리카르도 티시의 콜라보레이션. </p>
										<p class="tag">#나이키 #아디다스 #뉴발란스</p>
									</a>
									<button class="comp-like btn-like"><span><strong>좋아요</strong>55</span></button>
								</figcaption>
							</figure>
						</li>
						<li>
							<figure>
								<a href="javascript:void(0);" class="btn-view-detail"><img src="../static/img/test/@test_main_community4.jpg" alt=""></a>
								<figcaption>
									<a href="javascript:void(0);">
										<p class="id">@jungmihyun</p>
										<p class="cont">트레이닝 컬렉션으로 계보를 잇는 NikeLab과 크리에이티브 디렉터 리카르도 티시의 콜라보레이션. </p>
										<p class="tag">#나이키 #아디다스 #뉴발란스</p>
									</a>
									<button class="comp-like btn-like"><span><strong>좋아요</strong>55</span></button>
								</figcaption>
							</figure>
						</li>
						<li>
							<figure>
								<a href="javascript:void(0);" class="btn-view-detail"><img src="../static/img/test/@test_main_community7.jpg" alt=""></a>
								<figcaption>
									<a href="javascript:void(0);">
										<p class="id">@fraan</p>
										<p class="cont">트레이닝 컬렉션으로 계보를 잇는 NikeLab과 크리에이티브 디렉터 리카르도 티시의 콜라보레이션. </p>
										<p class="tag">#나이키 #아디다스 #뉴발란스</p>
									</a>
									<button class="comp-like btn-like on" title="선택됨"><span><strong>좋아요</strong>159</span></button>
								</figcaption>
							</figure>
						</li>
						<li>
							<figure>
								<a href="javascript:void(0);" class="btn-view-detail"><img src="../static/img/test/@test_main_community5.jpg" alt=""></a>
								<figcaption>
									<a href="javascript:void(0);">
										<p class="id">@fsldflsdf</p>
										<p class="cont">트레이닝 컬렉션으로 계보를 잇는 NikeLab과 크리에이티브 디렉터 리카르도 티시의 콜라보레이션. </p>
										<p class="tag">#나이키 #아디다스 #뉴발란스</p>
									</a>
									<button class="comp-like btn-like"><span><strong>좋아요</strong>55</span></button>
								</figcaption>
							</figure>
						</li>
						<li>
							<figure>
								<a href="javascript:void(0);" class="btn-view-detail"><img src="../static/img/test/@test_main_community4.jpg" alt=""></a>
								<figcaption>
									<a href="javascript:void(0);">
										<p class="id">@fsldflsdf</p>
										<p class="cont">트레이닝 컬렉션으로 계보를 잇는 NikeLab과 크리에이티브 디렉터 리카르도 티시의 콜라보레이션. </p>
										<p class="tag">#나이키 #아디다스 #뉴발란스</p>
									</a>
									<button class="comp-like btn-like"><span><strong>좋아요</strong>55</span></button>
								</figcaption>
							</figure>
						</li>
						<li>
							<figure>
								<a href="javascript:void(0);" class="btn-view-detail"><img src="../static/img/test/@test_main_community3.jpg" alt=""></a>
								<figcaption>
									<a href="javascript:void(0);">
										<p class="id">@qorlwer</p>
										<p class="cont">트레이닝 컬렉션으로 계보를 잇는 NikeLab과 크리에이티브 디렉터 리카르도 티시의 콜라보레이션. </p>
										<p class="tag">#나이키 #아디다스 #뉴발란스</p>
									</a>
									<button class="comp-like btn-like"><span><strong>좋아요</strong>55</span></button>
								</figcaption>
							</figure>
						</li>
						<li>
							<figure>
								<a href="javascript:void(0);" class="btn-view-detail"><img src="../static/img/test/@test_main_community2.jpg" alt=""></a>
								<figcaption>
									<a href="javascript:void(0);">
										<p class="id">@xmgdfgdf</p>
										<p class="cont">트레이닝 컬렉션으로 계보를 잇는 NikeLab과 크리에이티브 디렉터 리카르도 티시의 콜라보레이션. </p>
										<p class="tag">#나이키 #아디다스 #뉴발란스</p>
									</a>
									<button class="comp-like btn-like"><span><strong>좋아요</strong>55</span></button>
								</figcaption>
							</figure>
						</li>
					</ul>
				</div>
				<div class="btn_list_more mt-50" id="more6">
					<a href="" class="more_view" id="6">더보기</a>
				</div>
			</section>
		</main>
	</div>
</div>
<!-- // [D] 2016.인스타그램_리스트 퍼블 추가 -->

<!-- [D] 2016.인스타그램_상세보기 팝업 -->
<div class="layer-dimm-wrap pop-view-detail"> <!-- .layer-class 이부분에 클래스 추가하여 사용합니다. -->
	<div class="dimm-bg"></div>
	<div class="layer-inner">
		<button type="button" class="btn-close">창 닫기 버튼</button>
		<div class="layer-content">
			<div class="img-area">
				<img src="../static/img/test/@test_instagram_view01.jpg" alt="">
			</div>
			<div class="cont-area">
				<div class="title">
					<h3>@youlive22<span class="pl-10"><img src="../static/img/btn/btn_instagram.jpg" alt="instagram"></span></h3>
					<button class="comp-like btn-like" title="선택 안됨"><span><strong>좋아요</strong>159</span></button> <!-- // [D] 좋아요를 선택하면 버튼에 class="on" title="선택됨"을 추가 -->
				</div>
				<div class="cont-view">
					<div class="inner">
						머리부터 발끝까지 신원 최고 예~
						<p class="tag">
							#hott #hottest #nike #airjordan #Jordan #shoes #fashion #item #ootd #dailylook #핫티 #나이키 #에어조던 #조던 #신발 #패션 #아이템 #데일리 #데일리룩 #데일리슈즈 #신스타그램 #슈스타그램 #daily #dailyshoes #shoestagram
						</p>
					</div>
				</div>
				<div class="goods-detail-related">
					<h3>관련 상품</h3>
					<ul class="related-list">
						<li>
							<a href="#">
								<figure>
									<img src="../static/img/test/@test_instagram_wish01.jpg" alt="관심상품">
									<figcaption>
										# CONVERSE<br>
										CTAS 70 HI
									</figcaption>
								</figure>
							</a>
						</li>
						<li>
							<a href="#">
								<figure>
									<img src="../static/img/test/@test_instagram_wish02.jpg" alt="관심상품">
									<figcaption>
										# CONVERSE<br>
										CTAS 70 HI
									</figcaption>
								</figure>
							</a>
						</li>
						<li>
							<a href="#">
								<figure>
									<img src="../static/img/test/@test_instagram_wish03.jpg" alt="관심상품">
									<figcaption>
										# CONVERSE<br>
										CTAS 70 HI
									</figcaption>
								</figure>
							</a>
						</li>
					</ul>
					<!-- 관련상품이 없을 경우
					<div class="no-content">
						<p>관련 상품이 없습니다.</p>
					</div>
					// 관련상품이 없을 경우 -->
				</div> <!-- // .goods-detail-related -->
			</div> <!-- // .cont-area -->
			<div class="btn-wrap">
				<a href="#" class="view-prev">이전</a>
				<a href="#" class="view-next">다음</a>
			</div>
		</div>
	</div>
</div>
<!-- // [D] 2016.인스타그램_상세보기 팝업 -->

<!-- 기존 소스 백업 -->
<div id='tempPotoList' style="display:none">
</div>
<!-- 레이어팝업 -->
<div class="layer_photoList_wrap" style="display:none"><!-- 임시로 block 넣음. 개발 후 삭제해 주세요 -->
	<div class="layer-photoList">
		<p class="close">닫기</p>
		<div id="layer-slide-wrap">
			<button class="prev">왼쪽</button>
			<button class="next">오른쪽</button>
			<div class="inner">
				<ul class="photo-list">

				</ul>
			</div>
		</div>
	</div>
</div><!-- //레이어팝업 -->


<div class="line_map hhg-head hide">
	<div class="container">
		<div><em>&gt;</em><a>HAPPY HUNTING GROUND</a><em>&gt;</em><span><a>PLAY</a></span></div>
		<h3 class="hhg-title">HAPPY HUNTING GROUND</h3>
		<p class="hhg-subtitle">원하는 모든걸 얻을 수 있는 오야니 행복사냥터</p>
		<ul class="hhg-menu">
			<li><a href="javascript:storyBegins();" >story begins</a></li>
			<li><a href="<?$Dir.FrontDir?>special_interest.php">special interest</a></li>
			<li><a href="<?$Dir.FrontDir?>color_we_love.php">color we love</a></li>
			<li><a class="on">play</a></li>
			<li><a href="<?$Dir.FrontDir?>instagram_tags.php">#tags</a></li>
			<li><a href="<?$Dir.FrontDir?>logo_art.php">logo art project</a></li>
		</ul>
	</div>
</div>

<!-- start contents -->
<div class="containerBody sub_skin hide">

	<div id="hhw-wrapper">

		<h4 class="content-title">play</h4>

		<ul class="list-hhg-gallery" >

		</ul>
		<!-- 기존 상품리스트에서 사용된 페이징 사용해주세요 -->
		<!-- <div class="paging paging goods_list"><a class="on">1</a><a href="#">2</a></div> -->
		<div class="btn">
			<div class="more_ico" id="load_message"><img src="./images/ani_load.gif" alt=""/></div>
			<a href="javascript:goListScroll();" class="more more_list" id="more_list">더보기</a>
			<a href="instagram.php" class="btn_A go_first" id="go_first">처음으로</a>
		</div>
	</div>

</div>

<?php
include ($Dir."lib/bottom.php")
?>
</BODY>
