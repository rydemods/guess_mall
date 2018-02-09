<?php include_once('outline/header_m.php'); ?>
<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");

$imagepath = $Dir.DataDir."shopimages/lookbook/";
$imgPath = 'http://'.$_SERVER['HTTP_HOST'].'/data/shopimages/lookbook/';
$link_url   = "http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
$no = $_REQUEST['no'];
$sort = $_REQUEST["sort"];
$year = $_REQUEST["year"];

//검색 조건
$order = "";
if(!empty($sort)){
	if($sort == "latest"){
		$order .= " ORDER BY l.regdate desc";
	}else if($sort == "best"){
		$order .= " ORDER BY l.access desc";
	}else if($sort == "like"){
		$order .= " ORDER BY hott_cnt asc";
	}
}
if(!empty($year)){
	$where .= " AND regdate >= '".$year."0101000000' AND regdate <= '".$year."1231235959' " ;
}

//룩북 상세 리스트
$sql = "SELECT l.*, li.section,
			COALESCE((select COUNT( tl.hott_code )AS hott_cnt from tblhott_like tl WHERE tl.section = 'lookbook' AND l.no::varchar = tl.hott_code),0) AS hott_cnt
			FROM tbllookbook l ";
$sql .= "LEFT JOIN ( SELECT hott_code, section ,COUNT( hott_code )AS hott_cnt FROM tblhott_like WHERE section = 'lookbook' AND like_id = '".$_ShopInfo->getMemid()."' GROUP BY hott_code, section ) li on l.no::varchar = li.hott_code ";
$sql .= "WHERE l.no = ".$no."";
$result = pmysql_query($sql);
$row = pmysql_fetch_object( $result );
$info = $row;
$arrTag = explode(",",$info->tag);
$tmp_kakao_img = 'http://'.$_SERVER['HTTP_HOST'].'/front/'.$imagepath.$info->img_file;

//이전 정보
$prev_sql = "SELECT l.*, li.section,
			COALESCE((select COUNT( tl.hott_code )AS hott_cnt from tblhott_like tl WHERE tl.section = 'lookbook' AND l.no::varchar = tl.hott_code),0) AS hott_cnt
			FROM tbllookbook l ";
$prev_sql .= "LEFT JOIN ( SELECT hott_code, section ,COUNT( hott_code )AS hott_cnt FROM tblhott_like WHERE section = 'lookbook' AND like_id = '".$_ShopInfo->getMemid()."' GROUP BY hott_code, section ) li on l.no::varchar = li.hott_code ";
$prev_sql .= "WHERE l.no < ".$no." ORDER BY l.no DESC LIMIT 1";
$prev_result = pmysql_query($prev_sql);
$prev_row = pmysql_fetch_object( $prev_result );
$prev_info = $prev_row;

//다음 정보
$next_sql = "SELECT l.*, li.section,
			COALESCE((select COUNT( tl.hott_code )AS hott_cnt from tblhott_like tl WHERE tl.section = 'lookbook' AND l.no::varchar = tl.hott_code),0) AS hott_cnt
			FROM tbllookbook l ";
$next_sql .= "LEFT JOIN ( SELECT hott_code, section ,COUNT( hott_code )AS hott_cnt FROM tblhott_like WHERE section = 'lookbook' AND like_id = '".$_ShopInfo->getMemid()."' GROUP BY hott_code, section ) li on l.no::varchar = li.hott_code ";
$next_sql .= "WHERE l.no > ".$no." ORDER BY l.no ASC LIMIT 1";
$next_result = pmysql_query($next_sql);
$next_row = pmysql_fetch_object( $next_result );
$next_info = $next_row;

// 정규식을 이용해서 img 태그 전체 / src 값만 추출하기
preg_match_all("/<img[^>]*src=[\"']?([^>\"']+)[\"']?[^>]*>/i", $info->img, $detailImg);
preg_match_all("/<img[^>]*src=[\"']?([^>\"']+)[\"']?[^>]*>/i", $info->img_m, $detailImg2);

?>
<section class="top_title_wrap">
	<h2 class="page_local">
		<a href="javascript:history.back();" class="prev"></a>
		<span>LOOKBOOK</span>
		<a href="/m/shop.php" class="home"></a>
	</h2>
</section>

<!-- sns 공유하기 레이어팝업 -->
<div class="layer-dimm-wrap pop-sns_share">
	<div class="dimm-bg"></div>
	<div class="layer-content">
		<div class="sns_area">
			<a href="javascript:sendSns('facebook','<?=$link_url ?>','<?=$info->title ?>');" class="facebook"><img src="./static/img/btn/btn_sns_facebook.png" alt="facebook"></a>
			<a href="javascript:sendSns('twitter','<?=$link_url ?>','<?=$info->title ?>');" class="twitter"><img src="./static/img/btn/btn_sns_twitter.png" alt="twitter"></a>
			<a href="javascript:sendSns('band','<?=$link_url ?>','<?=$info->title ?>');"><img src="./static/img/btn/btn_sns_band.png" alt="band"></a>
			<a href="javascript:;" id="kakao-link"><img src="./static/img/btn/btn_sns_kakao.png" alt="kakaotalk"></a>
			<a href="javascript:sendSns('kakaostory','<?=$link_url ?>','<?=$info->title ?>');"  id="kakaostory-link"><img src="./static/img/btn/btn_sns_kakaostory.png" alt="kakaostory"></a>
		</div>
	</div>
</div>
<!-- //sns 공유하기 레이어팝업 -->

<div class="lookbook_detail">

    <div class="title">
		<h3 class="subject"><?=$info->title ?></h3>
		<a class="btn-sns_share" href="javascript:;"><img src="./static/img/btn/btn_sns_share.png" alt="sns공유하기"></a>
	</div><!-- //.title -->

	<!-- [D] 이미지 슬라이더 추가 -->
	<ul class="lookbook-visual-view">
		<?foreach ($detailImg[1] as $val){?>
		<li><a href="javascript:;"><img src="<?=$val?>" alt=""></a></li>
		<?} ?>
	</ul>
	<!-- // [D] 이미지 슬라이더 추가 -->

	<!--<div class="lookbook_slider">
		<ul class="lookbook_slide_view">
			<?foreach ($detailImg1[1] as $val){?>
			<li><img src="<?=$val?>" alt="룩북 메인배너"></li>
			<?} ?>
		</ul>
		<ul class="lookbook_slide_thumb">
			<?foreach( $detailImg2[1] as $key => $val){ ?>
			<li><img src="<?=$val?>" alt="룩북 메인배너 썸네일"></li>
			<?} ?>
		</ul>
	</div> //.lookbook_slider -->

    <div class="content">
		<p class="txt"><?=$info->content ?></p>

		<button class="like_l<?=$info->no ?> comp-like btn-like <?=$info->section ? 'on' : '' ?>" onclick="detailSaveLike('<?=$info->no ?>', '<?=$info->section?'on':'off' ?>', 'lookbook', '<?=$_ShopInfo->getMemid() ?>','')"    title="<?=$info->section ? '선택됨' : '선택 안됨'  ?>"><span class="like_lcount_<?=$info->no ?>"><strong>좋아요</strong><?=$info->hott_cnt?></span></button>

		<div class="detail_hashtag">
			<!-- (D) 선택된 li에 class="on" title="선택됨"을 추가합니다. -->
			<?if(!empty($info->tag)){ ?>
			<ul class="clear">
			<?foreach ($arrTag as $tag){?>
				<li><a href="javascript:;" class="CLS_magazineTag"><?=$tag ?></a></li>
			<?} ?>
			</ul>
			<?} ?>
		</div>
	</div><!-- //.content -->

	<!-- [D] 이전/다음 이미지 추가 -->
	<div class="page-controls">
		<ul>
			<?if($prev_info->no){?>
			<li>
				<a href="javascript:detail('<?=$prev_info->no ?>');">
					<figure>
						<div class="img">
							<img src="<?=$imagepath.$prev_info->img_m_file ?>" alt="이전 리스트 이미지">
						</div>
						<figcaption>PREVIOUS</figcaption>
					</figure>
				</a>
			</li>
			<?}else{ ?>
			<li>
			</li>
			<?} ?>
			<?if($next_info->no){ ?>
			<li>
				<a href="javascript:detail('<?=$next_info->no ?>');">
					<figure>
						<div class="img">
							<img src="<?=$imagepath.$next_info->img_m_file ?>" alt="다음 리스트 이미지">
						</div>
						<figcaption>NEXT</figcaption>
					</figure>
				</a>
			</li>
			<?}else{ ?>
			<li>
			</li>
			<?} ?>
		</ul>
	</div>
	<!-- // [D] 이전/다음 이미지 추가 -->

	<div class="posting-wrap">
		<h3>관련 포스팅</h3>
		<!-- // 메인 핫포스팅 넣어주세요 -->
		<div class="main-community-content on">
			<ul class="comp-posting posting-wrap posting-list">

			</ul>
		</div>
	</div>

</div><!-- //.lookbook_detail -->
<input type="hidden" id="link-code"value="<?=$no ?>">
<input type="hidden" id="link-menu"value="lookbook">
<input type="hidden" id="link-memid" value="<?=$_MShopInfo->getMemid()?>">
<script type="text/javascript">
$(document).ready( function() {
	postingList();
    var linkCode = $("#link-code").val();
    var linkMenu = $('#link-menu').val();

    $("#kakao-link").trigger("click");

	$("#kakao-link").on("click", function(){
		if($("#link-memid").val() != ""){
	        $.ajax({
	            type        : "POST",
	            url           : "../front/sns_point_insert_proc.php",
	            data        : { snstype : 'kakaotalk', code : linkCode, menu : linkMenu },
	        	async      : false
	        }).done(function(result){

	        });
		}
	});
	kakaotalkShare('<?=$_data->shoptitle?>','<?=$tmp_kakao_img?>', '<?=$info->title?>', 'http://<?=$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']?>')
// 	 $(".CLS_magazineTag").on("click", function() {
// 			$(this).closest("li").addClass("on");
// 			$(this).closest("li").attr("title","선택됨");
// 	 });
});

//룩북 상세
function detail(no){
	var year = $("input[name=year]").val();
	var sort = $("input[name=sort]").val();
	if(sort == ""){
		var url = "../m/lookbook_detail.php?no="+no+"&year="+year
	}else if(year == ""){
		var url = "../m/lookbook_detail.php?no="+no+"&sort="+sort
	}else{
		var url = "../m/lookbook_detail.php?no="+no+"&sort="+sort+"&year="+year
	}
	accessPlus(no,"tbllookbook","access","no");
	$(location).attr('href', url);
}

//관련 컨텐츠
function postingList(){
	var no = "<?=$no?>";
	var tag = [];
	tag = <? echo json_encode($arrTag)?>;
	$.ajax({
		type: "POST",
		url: "../front/ajax_posting_list2.php",
		data: {"no" : no, "menu_type" : "lookbook", "tag" : tag , "view_type" : "m"},
		dataType:"JSON",
		error:function(request,status,error){
			//alert("code:"+request.status+"\n"+"message:"+request.responseText+"\n"+"error:"+error);
		}
	}).done(function(data){
		$(".posting-list").html(data['posting_html']);

	});
}

</script>
<? include_once('outline/footer_m.php'); ?>
