<?php include_once('outline/header_m.php'); ?>
<?php
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");

$instaimgpath = $Dir.DataDir."shopimages/instagram/";
$productimgpath = $Dir.DataDir."shopimages/product/";

$search_word = $_GET['search_word'];
$sort = $_GET["sort"] ? $_GET["sort"] : 'latest';
$sql = "SELECT  i.*, li.section,
						COALESCE((select COUNT( tl.hott_code )AS hott_cnt from tblhott_like tl WHERE tl.section = 'instagram' AND i.idx::varchar = tl.hott_code),0) AS hott_cnt
			FROM tblinstagram i
			LEFT JOIN ( SELECT hott_code, section FROM tblhott_like WHERE section = 'instagram' AND like_id = '".$_ShopInfo->getMemid()."' GROUP BY hott_code, section ) li on i.idx::varchar = li.hott_code
			WHERE i.display = 'Y' ";
if(!empty($search_word)){
	$sql .= "AND ( i.title iLIKE '%{$search_word}%' OR i.content iLIKE '%{$search_word}%' OR i.hash_tags = '%{$search_word}%')  ";
}

//검색 조건
$order = "";
if(!empty($sort)){
	if($sort == "latest"){
		$order .= " ORDER BY i.regdt desc";
	}else if($sort == "best"){
		$order .= " ORDER BY i.access desc";
	}else if($sort == "like"){
		$order .= " ORDER BY hott_cnt desc";
	}
}

$sql .=	$order;
$total_result = pmysql_query($sql);
$total_num = pmysql_num_rows($total_result);
$sql .= " LIMIT 5";
$result = pmysql_query($sql);

?>

<!-- [D] 2016. 퍼블 작업 -->
<section class="top_title_wrap">
	<h2 class="page_local">
		<a href="<?=$Dir.MDir ?>" class="prev"></a>
		<span>INSTAGRAM</span>
		<a href="/m/shop.php" class="home"></a>
	</h2>
</section>

<div class="instagram-wrap">
	<form name="searchForm" id="searchForm" method="GET" action="<?=$_SERVER['PHP_SELF']?>">
    <div class="sorting_area">
		<div class="searchbox clear">
			<input type="search" name="search_word" id="search_word" value="<?=$search_word ?>">
			<button type="submit" class="btn-def">검색</button>
		</div>
		<div class="list_sort">
			<ul class="clear">
				<li><a href="javascript:sortSelect('latest');">최신순</a></li>
				<li><a href="javascript:sortSelect('best');">인기순</a></li>
				<li><a href="javascript:sortSelect('like');">좋아요순</a></li>
			</ul>
		</div>
	</div><!-- //.sorting_area -->
		<input type="hidden" name="sort" value="<?=$sort ?>">
	</form>
	<div class="asymmetry_list">
		<ul class="instagram-list">
			<?
			while ( $row = pmysql_fetch_array($result) ) {
				$arrTag = explode(",",$row['hash_tags']);
			?>
			<li>
				<div class="name">
					<span></span> <!-- instagram id -->
					<?if($row['section']){ ?>
					<button class="comp-like btn-like like_i<?=$row['idx']?> on" onclick="detailSaveLike('<?=$row['idx']?>','on','instagram','<?=$_ShopInfo->getMemid()?>','<?=$brand ?>')" id="like_<?=$row['idx']?>" title="선택됨"><span  class="like_icount_<?=$row['idx']?>"><strong>좋아요</strong><?=$row['hott_cnt'] ?></span></button>
					<?}else{ ?>
					<button class="comp-like btn-like like_i<?=$row['idx']?>" onclick="detailSaveLike('<?=$row['idx']?>','off','instagram','<?=$_ShopInfo->getMemid()?>','<?=$brand ?>')" id="like_<?=$row['idx']?>" title="선택 안됨"><span class="like_icount_<?=$row['idx']?>"><strong>좋아요</strong><?=$row['hott_cnt'] ?></span></button>
					<?} ?>
				</div>
				<div class="cont-img"><img src="<?=$instaimgpath.$row['img_m_file']?>" alt=""></div>
				<div class="title">
					<p><?=strcutMbDot(strip_tags($row['content']),35) ?></p>
					<p class="tag">
					<?foreach($arrTag as $tag){?>
						<?="#".trim($tag)?>
					<?} ?>
					</p>
				</div>
				<div class="btnwrap mb-10">
					<ul class="ea1">
						<li class='hide'></li>
						<li><a href="instagram_view.php?ino=<?=$row['idx']?>" class="btn-def">상세보기</a></li>
					</ul>
				</div>
			</li>
			<?} ?>
		</ul>
	</div>	
	<?if($total_num > 5){ ?>
	<div class="btn_list_more mt-20 more_btn">
		<a href="javascript:;" class="more_view">더보기</a>
	</div>
	<?} ?>	
</div><!-- //.instagram-wrap -->
<!-- //[D] 2016. 퍼블 작업 -->

<!-- 관련상품 레이어 팝업 -->
<div class="layer-dimm-wrap pop-related"> <!-- .layer-class 이부분에 클래스 추가하여 사용합니다. -->
	<div class="dimm-bg"></div>
	<div class="layer-inner">
		<h3 class="layer-title">관련상품</h3>
		<button type="button" class="btn-close">창 닫기 버튼</button>
		<div class="layer-content">
			<div class="product-list">
				<div class="goods-list">
					<div class="goods-list-item">
					<!-- (D) 별점은 .star-score에 width:n%로 넣어줍니다. -->
						<ul id="relation_list">
							<li class="grid-sizer"></li>


						</ul>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<!-- // 관련상품 레이어 팝업 -->

<script type="text/javascript">
var memId = "<?=$_ShopInfo->getMemid()?>";
var page_num	= 1;
$(document).ready(function() {

	//레이어 팝업
	$('.btn-related').click(function(){
		$('.pop-related').fadeIn();
	});

	//더 보기
	$(".more_view").on("click",function(){
		$(".more_btn").hide();
		$.ajax({
			type: "POST",
			url: "../front/ajax_instagram_more.php",
			data: "page="+(page_num+1)+"&type=mobile&search_word=<?=$search_word?>&sort=<?=$sort?>",
			contentType : "application/x-www-form-urlencoded; charset=UTF-8",
			error:function(request,status,error){
				//alert("code:"+request.status+"\n"+"message:"+request.responseText+"\n"+"error:"+error);
			}
		}).done(function(data){  
			 var arrData = data.split("|||");
			 console.log(arrData[1]);
			$(".instagram-list").append(arrData[0]);
			if(arrData[1] == ""){
				$(".more_btn").hide();
			}else{
				$(".more_btn").show();
			}
			page_num++;
			/*$('.btn-related').click(function(){
				$('.pop-related').fadeIn();
			});

			$(".btn-related").on("click",function(){
				 var code = $(this).attr("idx");
				 relatedView(code);
			});*/			
		});
    });

});


//관련상품 보기
function relatedView(code){
	
	$.ajax({
		type: "POST",
		url: "../m/ajax_relation_product.php",
		data: "code="+code+"&type=mobile",
		dataType:"HTML",
	    error:function(request,status,error){
	       //alert("code:"+request.status+"\n"+"message:"+request.responseText+"\n"+"error:"+error);
	    }
	}).done(function(html){
		console.log(html);
		$("#relation_list").empty("");
		$("#relation_list").append("<li class='grid-sizer'></li>");
		$("#relation_list").append(html);
// 		$(".main-community-content").trigger("COMMUNITY_RESET");

	});
}

//정렬 순 검색
function sortSelect(val){
	$("input[name=sort]").val(val);
	$("form[name='searchForm']").submit();
}

</script>

<? include_once('outline/footer_m.php'); ?>
