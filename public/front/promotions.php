<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");

$imagepath=$Dir.DataDir."shopimages/etc/main_logo.gif";
$flashpath=$Dir.DataDir."shopimages/etc/main_logo.swf";

if (file_exists($imagepath)) {
	$mainimg="<img src=\"".$imagepath."\" border=\"0\" align=\"absmiddle\">";
} else {
	$mainimg="";
}
if (file_exists($flashpath)) {
	if (preg_match("/\[MAINFLASH_(\d{1,4})X(\d{1,4})\]/",$_data->shop_intro,$match)) {
		$width=$match[1];
		$height=$match[2];
	}
	$mainflash="<script>flash_show('".$flashpath."','".$width."','".$height."');</script>";
} else {
	$mainflash="";
}
$pattern=array("(\[DIR\])","(\[MAINIMG\])","/\[MAINFLASH_(\d{1,4})X(\d{1,4})\]/");
$replace=array($Dir,$mainimg,$mainflash);
$shop_intro=preg_replace($pattern,$replace,$_data->shop_intro);


$mb_qry="select * from tblmainbannerimg order by banner_sort";


if (stripos($shop_intro,"<table")!==false || strlen($mainflash)>0)
	$main_banner=$shop_intro;
else
	$main_banner=nl2br($shop_intro);

$likecode = $_POST["code"];
$search_word = $_POST["search_word"];
$search_select = $_POST["search_select"];
$pidx = $_REQUEST["pidx"];
if($pidx==""){
	$isql = "SELECT idx FROM tblpromo WHERE display_type='A' OR display_type='M' ORDER BY display_seq ASC";
	$ires = pmysql_query($isql,get_db_conn());
	$irow = pmysql_fetch_array($ires);
	$pidx = $irow[0];
}


?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<meta http-equiv="Content-Type" content="text/html; charset=euc-kr" />
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
<HEAD>
<TITLE><?=$_data->shoptitle?> - 기획전</TITLE>
<META http-equiv="CONTENT-TYPE" content="text/html; charset=EUC-KR">
<META name="description" content="<?=(ord($_data->shopdescription)?$_data->shopdescription:$_data->shoptitle)?>">
<META name="keywords" content="<?=$_data->shopkeyword?>">
<script type="text/javascript" src="<?=$Dir?>lib/lib.js.php"></script>
<!--<link rel="stylesheet" href="../css/nexolve.css" />-->
</HEAD>

<?php include ($Dir.MainDir.$_data->menu_type.".php");?>

<body<?=($_data->layoutdata["MOUSEKEY"][0]=="Y"?" oncontextmenu=\"return false;\"":"")?><?=($_data->layoutdata["MOUSEKEY"][1]=="Y"?" ondragstart=\"return false;\" onselectstart=\"return false;\"":"")?> leftmargin="0" marginwidth="0" topmargin="0" marginheight="0"><?=($_data->layoutdata["MOUSEKEY"][2]=="Y"?"<meta http-equiv=\"ImageToolbar\" content=\"No\">":"")?>

<?
/*$lnb_flag = 2;
include ($Dir.MainDir."lnb.php");*/
?>

<script type="text/javascript">
$(function(){
	/*
	$('ul.tap_goods li , ul.goods_list_type_d > li').mouseenter(function(){
	$(this).find('ul.goods_quick_icon02 , ul.goods_quick_icon03 ').css('display','block');
	});
	$('ul.tap_goods li , ul.goods_list_type_d > li').mouseleave(function(){
	$(this).find('ul.goods_quick_icon02 , ul.goods_quick_icon03 ').css('display','none');
	});
	*/
	$('.new_goods4ea ul.list li').mouseenter(function(){
	$(this).find('.layer_goods_icon').show();
	});
	$('.new_goods16ea ul.list li').mouseenter(function(){
	$(this).find('.layer_goods_icon').show();
	});
	$('.layer_goods_icon').mouseleave(function(){
	$('.layer_goods_icon').hide();
	});


});


</script>

<form name=form1 id = 'ID_goodsviewfrm' method=post action="<?=$Dir.FrontDir?>basket.php">
	<input type="hidden" name="productcode"></input>
</form>

<form name="form2" method="post" action="<?=$_SERVER['PHP_SELF']?>" >
<input type="hidden" name="pidx" value="<?=$pidx?>">
</form>


<!-- 메인 컨텐츠 -->
<div class="containerBody">
	<div class="goods_list_wrap">

			<div class="promotion_select">
				<div class="select_type open ta_l" style="width:350px;">
					<span class="ctrl"><span class="arrow"></span></span>
					<button type="button" class="myValue">다른기획전으로 바로가기</button>
					<ul class="aList">
						<?
						$asql = "SELECT * FROM tblpromo WHERE display_type='A' OR display_type='P' ORDER BY display_seq ASC";
						$ares = pmysql_query($asql,get_db_conn());
						while($arow = pmysql_fetch_array($ares)){
						?>
							<li><a href="promotion.php?pidx=<?=$arow["idx"]?>"><?=$arow["title"]?></a></li>
						<?}?>
					</ul>
				</div>
			</div>

			<div>&nbsp;</div>

			<?
			$imgsql = "SELECT * FROM tblpromo where idx='{$pidx}'";
			$imgres = pmysql_query($imgsql,get_db_conn());
			$imgrow = pmysql_fetch_array($imgres);
			?>
			<div class="promotion_banner"><img src="../data/shopimages/timesale/<?=$imgrow["banner_img"]?>" alt="" /></div>

			<!-- 프로모션 리스트 -->
			<?
			$sql = "SELECT * FROM tblpromotion a, tblspecialpromo b
			WHERE cast(a.seq as varchar) = b.special AND a.promo_idx = '{$pidx}' ORDER BY display_Seq ASC";
			$res = pmysql_query($sql,get_db_conn());
			while($row = pmysql_fetch_array($res)){
				$arr[] = $row;
			}?>
			<div class="promotion_tap_wrap">
				<ul class="promotion_tap">
					<? for($i=0;$i<count($arr);$i++){?>
					<li><a href="#<?=$arr[$i]["seq"]?>" class="scroll"><?=$arr[$i]["title"]?></a></li>
					<?}?>
				</ul>
			</div>

			<div class="new_goods16ea" style="text-align: left">
				<?
				for($i=0;$i<count($arr);$i++){
				?>
				<h3 style="margin-top: 30px;"><span class="total"><span id="<?=$arr[$i]["seq"]?>" class="ngb_14"><?=$arr[$i]["title"]?></span></span></h3>
				<div class="promotion_line"></div>
				<!--<ul class="list">-->
				<?
				$promo_prcode = explode(",",$arr[$i]["special_list"]);
				$jj = 0;
				for($ii=0;$ii<count($promo_prcode);$ii++){
					$psql = "SELECT productname, productcode,etctype, consumerprice, sellprice, reserve, minimage FROM tblproduct WHERE productcode = '{$promo_prcode[$ii]}'";
					$pres = pmysql_query($psql);
					$prow = pmysql_fetch_object($pres);

					##### 쿠폰에 의한 가격 할인
					$cou_data = couponDisPrice($prow->productcode);
					if($cou_data['coumoney']){
						$prow->sellprice = $prow->sellprice-$cou_data['coumoney'];
						$prow->dc_type = $cou_data["goods_sale_type"];
					}
					##### 쿠폰에 의한 가격 할인

					##### 오늘의 특가, 타임세일에 의한 가격
					$spesell = getSpeDcPrice($prow->productcode);
					if($spesell){
						$prow->sellprice = $spesell;
					}
					##### //오늘의 특가, 타임세일에 의한 가격

					$dc_rate = getDcRate($prow->consumerprice,$prow->sellprice);

					$imgsrc = getMaxImageForXn($prow->productcode);

				?>
					<?if($jj%5==0){?>
				<ul class="list">
					<?}?>
					<li>
						<span><?=viewicon($prow->etctype)?></span>
						<div class="goods_A">
							<a href="#">
								<p class="img190"><img src="<?=$imgsrc?>" width="190" height="190" alt=""></p>
								<span class="subject"><?=$prow->productname?></span>
								<span class="price"><?=number_format($prow->sellprice)?>원</span>
							</a>
						</div>
						<div class="layer_goods_icon" style="display: none;">
							<p class="icon">
								<a href="<?=$Dir.FrontDir."productdetail.php?productcode=".$prow->productcode?>" class="view" title="상세보기"></a>
								<a href="javascript:CheckForm('','<?=$prow->productcode?>')" class="cart" alt="<?=$prow->productcode?>" title="장바구니"></a>
							</p>
						</div>
					</li>
						<?$jj++;
						if($jj%5==0){?>
						</ul>
						<?}else if(count($promo_prcode)==$jj){?>
						</ul>
						<?}?>
					<?}?>
				<!--</ul>-->
				<?}?>
			</div><!-- //프로모션 리스트 -->
	</div>
</div><!-- //메인 컨텐츠 -->
<div style="margin-top: 30px;"></div>

<div id="create_openwin" style="display:none"></div>
<?php
include ($Dir."lib/bottom.php")
?>
<script>
function goProductDetail(locationUrl){
	location.href = locationUrl;
}

$(".scroll").click(function(event){
	event.preventDefault();
	var full_url = this.href;
	var parts = full_url.split("#");
	var trgt = parts[1];
	var target_offset = $("#"+trgt).offset();
	var target_top = target_offset.top;
	$('html, body').animate({scrollTop:target_top}, 500);
});

</script>

<div id="overDiv" style="position:absolute;top:0px;left:0px;z-index:100;display:none;" class="alpha_b60" ></div>
<div class="popup_preview_warp" style="margin-left: 50%;left: -459px;display:none;" ></div>
<script type="text/javascript">

	$(window).scroll(function() {
		$('div.popup_preview_warp').css({'top':$(window).scrollTop()+100,'z-index':'210'});
	});

	$(function(){

		$('div.goods5 ul.list li a').mouseenter(function(){
			$(this).find('p.preview_btn').css('display','block');
		});
		$('div.goods5 ul.list li a').mouseleave(function(){
			$(this).find('p.preview_btn').css('display','none');
		});
		$('p.preview_btn').click(function(){
			$('div.popup_preview_warp').html("<img src='../images/common/loading_img.gif'>");
			$('#overDiv').css({'width':$(document).width(),'height':$(document).height()})
			$('#overDiv').show();

			var prcode = $(this).attr("alt");
			$.post("ajax_preview_for_list.php",{productcode:prcode},function(data){
				if(data){
				$('div.popup_preview_warp').html(data);
				}
			});
			$('div.popup_preview_warp').show();
			$('div.popup_preview_warp').css({'top':$(window).scrollTop()+100,'z-index':'210'});

		});
	});

function CheckForm(gbn,temp2) {


	if(gbn=="ordernow") {
		document.form1.ordertype.value="ordernow";
	}

	if (gbn != "ordernow"){
		document.form1.action="../front/confirm_basket.php";
		document.form1.target="confirmbasketlist";
		document.form1.productcode.value= temp2;
		window.open("about:blank","confirmbasketlist","width=401,height=309,scrollbars=no,resizable=no, status=no,");
		document.form1.submit();
	}

}


</script>
</div>
</BODY>
</HTML>
