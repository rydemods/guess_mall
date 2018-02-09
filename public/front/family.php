<?


$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");
//exdebug($_ShopInfo);

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
	$isql = "SELECT idx FROM tblfamily_list WHERE display_type='A' OR display_type='M' ORDER BY display_seq ASC";
	$ires = pmysql_query($isql,get_db_conn());
	$irow = pmysql_fetch_array($ires);
	$pidx = $irow[0];
	
}
//exdebug($pidx);

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<meta http-equiv="Content-Type" content="text/html; charset=euc-kr" />
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
<HEAD>
<TITLE><?=$_data->shoptitle?> - 기획전</TITLE>
<META http-equiv="CONTENT-TYPE" content="text/html; charset=EUC-KR">
	<META name="description"
		content="<?=(ord($_data->shopdescription)?$_data->shopdescription:$_data->shoptitle)?>">
		<META name="keywords" content="<?=$_data->shopkeyword?>">
			<script type="text/javascript" src="<?=$Dir?>lib/lib.js.php"></script>
			<!--<link rel="stylesheet" href="../css/nexolve.css" />-->

</HEAD>

<?php include ($Dir.MainDir.$_data->menu_type.".php");?>

<body
	<?=($_data->layoutdata["MOUSEKEY"][0]=="Y"?" oncontextmenu=\"return false;\"":"")?>
	<?=($_data->layoutdata["MOUSEKEY"][1]=="Y"?" ondragstart=\"return false;\" onselectstart=\"return false;\"":"")?>
	leftmargin="0" marginwidth="0" topmargin="0" marginheight="0"><?=($_data->layoutdata["MOUSEKEY"][2]=="Y"?"<meta http-equiv=\"ImageToolbar\" content=\"No\">":"")?>

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

	<form name=form1 id='ID_goodsviewfrm' method=post
		action="<?=$Dir.FrontDir?>basket.php">
		<input type="hidden" name="productcode"></input>
	</form>


	<form name="form2" method="post" action="<?=$_SERVER['PHP_SELF']?>">
		<input type="hidden" name="pidx" value="<?=$pidx?>">
	
	</form>


	<!-- 메인 컨텐츠 -->
	<div class="containerBody sub_skin">
		<div class="goods_list_wrap" style="margin: 0px">

			<div class="promotion_select">
				<div class="select_type open ta_l" style="width: 350px;">
					<span class="ctrl"><span class="arrow"></span></span>
					<button type="button" class="myValue">다른기획전으로 바로가기</button>
					<ul class="aList">
						<?
						$asql = "SELECT * FROM tblfamily_list WHERE display_type='A' OR display_type='P' ORDER BY display_seq ASC";
						$ares = pmysql_query($asql,get_db_conn());
						while($arow = pmysql_fetch_array($ares)){
						?>
							<li><a href="family.php?pidx=<?=$arow["idx"]?>"><?=$arow["title"]?></a></li>
						<?}   ?>	
					</ul>
				</div>
			</div>

			<?		
			$imgsql = "SELECT * FROM tblfamily_list where idx='{$pidx}'";		
			$imgres = pmysql_query($imgsql,get_db_conn());
			$imgrow = pmysql_fetch_array($imgres);	
			$_test1 = explode(",",$imgrow[member_type]);//2는  null 1은 숫자가 2가지
		
			//var_dump ($_test1);
			//exdebug($imgrow[member]);
					
			$chk_member = 0;
			if($imgrow[member] != 0) { //0은 비회원 1은 회원  특정 페이지를 보기위한 조건 
				foreach($_test1 as $val) { // 회원인데 특정 페이지 값이 볼수있는 조건이 되면 ++이 실행됨 
					if($_ShopInfo->memgroup == $val){	//회원인데 볼수있는 조건이 안되면 밑에줄에 ==0이 되서 오류창이뜸					
							$chk_member ++;
					}
				}			
				if($chk_member ==0){
				
					echo("<script>location.href='/';</script>");
				}
			}
				

		#######################
		/*foreach($_test1 as $val){
			if($_ShopInfo->memgroup == $val){
				//echo ("<script>alert('일치!');</script>");
			}else{
				echo "<script>location.href(-1);</script>
			}
		}*/
		#######################

		//for(i=0; i<$_test1.length; i++ )
		
			$imgsql = "SELECT * FROM tblfamily_list where idx='{$pidx}'";
			//echo "@@@@".$_ShopInfo->memid;
			$imgres = pmysql_query($imgsql,get_db_conn());
			$imgrow = pmysql_fetch_array($imgres);
			?>
			<div class="promotion_banner">
				<img src="../data/shopimages/timesale/<?=$imgrow["banner_img"]?>"
					alt="" usemap="#<?=$pidx?>" />
			<?if($pidx==8){?>
			<map name="<?=$pidx?>">
					<area shape="rect" coords="80,932,372,1232"
						href="http://nasign.ajashop.co.kr/front/productdetail.php?productcode=001001004000000028">
						<area shape="rect" coords="405,932,696,1232"
							href="http://nasign.ajashop.co.kr/front/productdetail.php?productcode=001001004000000002">
							<area shape="rect" coords="730,932,1021,1232"
								href="http://nasign.ajashop.co.kr/front/productdetail.php?productcode=001001006000000044">
				
				</map>
			<?}?>
			<?if($pidx==14){?>
			<map name="<?=$pidx?>">
					<area shape="rect" coords="34,1374,279,1388"
						href="http://www.minigold.co.kr/event/event.php?b_id=003&idx=102&page=1&mode=view&state=">
				
				</map>
			<?}?>
			</div>

			<!-- 프로모션 리스트 -->
			<?
			$sql = "SELECT * FROM tblfamily a, tblfamily_product b
			WHERE cast(a.seq as varchar) = b.special AND a.promo_idx = '{$pidx}' ORDER BY display_Seq ASC";

			$res = pmysql_query($sql,get_db_conn());
			while($row = pmysql_fetch_array($res)){
				$arr[] = $row;
			} 
		?>
			
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
				<h3 class="promotion">
					<span class="total"><span id="<?=$arr[$i]["seq"]?>" class="ngb_14"><?=$arr[$i]["title"]?></span></span>
				</h3>
				<!--<ul class="list">-->
				<?	
				$promo_prcode = explode(",",$arr[$i]["special_list"]);
		
				$jj = 0;
				# 배열중 공백값이 존재하는 값은 제거
				$promo_prcode = array_filter($promo_prcode);
				//exdebug($promo_prcode);
				//exdebug(count($promo_prcode));
				$count = 0;
		
				for($ii=0;$ii<count($promo_prcode);$ii++){//기획전에 등록된 상품들 가져오기 기획전 상품중 display y인 상품만 체크해서 배열에 넣어준후 그 개수대로 for문을 다시 한번 돌리게 수정 20150626원재
					$psql = "SELECT * FROM tblproduct WHERE productcode = '{$promo_prcode[$ii]}'";
					$psql .= "AND display='Y'";
					//exdebug($psql);
					$pres = pmysql_query($psql);
					$prow_temp= pmysql_fetch_object($pres);
					if($prow_temp){
						$prow[] = $prow_temp;
						$count ++;
						
					}
					
				}
				echo "<ul class='list'>";
				for($i2=0; $i2<$count; $i2++){//위에서 가져온 prow count만큼 재차 뿌려줌 20150626원재
					##### 쿠폰에 의한 가격 할인
					$cou_data = couponDisPrice($prow[$i2]->productcode);
					if($cou_data['coumoney']){
						$p_nomal_price = $prow[$i2]->sellprice;
						$prow[$i2]->sellprice = $prow[$i2]->sellprice-$cou_data['coumoney'];
						$prow[$i2]->dc_type = $cou_data["goods_sale_type"];
					}
					##### 쿠폰에 의한 가격 할인

					#####즉시적립금 할인 적용가 150901원재
					if($prow[$i2]->reserve){
						$ReserveConversionPrice = 0;
						$ReserveConversionPrice = getReserveConversion($prow[$i2]->reserve, $prow[$i2]->reservetype,$p_nomal_price,'Y');
						$prow[$i2]->sellprice = $prow[$i2]->sellprice - $ReserveConversionPrice;
					}
					#####//즉시적립금 할인 적용가

					##### 오늘의 특가, 타임세일에 의한 가격
					$spesell = getSpeDcPrice($prow[$i2]->productcode);
					if($spesell){
						$prow[$i2]->sellprice = $spesell;
					}
					##### //오늘의 특가, 타임세일에 의한 가격

					$dc_rate = getDcRate($prow[$i2]->consumerprice,$prow[$i2]->sellprice);

					$imgsrc = getMaxImageForXn($prow[$i2]->productcode);
					//exdebug($prow[$i2]->productcode);
				?>
				
				
					
				
					
					<li><span><?=viewicon($prow[$i2]->etctype)?></span>
					<div class="goods_A">
						<a href="#">
							<p class="img190">
								<img src="<?=$imgsrc?>" width="190" height="190" alt="">
							
							</p> <span class="subject"><?=$prow[$i2]->productname?></span> <span
							class="price"> <del><?=number_format($prow[$i2]->consumerprice)?>원</del>
									<?=number_format($prow[$i2]->sellprice)?>원
								</span> <span></span>
						</a>
					</div>
						<?if($prow[$i2]->option1)$option_chk=3; else $option_chk=1;?>
						<!--
						<div class="layer_goods_icon" style="display: none;" onclick="javascript:layer_goods_link('0','<?=$Dir.FrontDir."productdetail.php?productcode=".$prow[$i2]->productcode?>')">
							<p class="icon">
								<a href="javascript:layer_goods_link('2',<?=$Dir.FrontDir."productdetail.php?productcode=".$prow[$i2]->productcode?>')" class="view" title="상세보기"></a>
								<a href="javascript:layer_goods_link('<?=$option_chk?>','<?=$prow[$i2]->productcode?>')" class="cart" alt="<?=$prow[$i2]->productcode?>" title="장바구니"></a>
							</p>
						</div>
						-->
					<div class="layer_goods_icon"
						link_url="<?=$Dir.FrontDir."productdetail.php?productcode=".$prow[$i2]->productcode?>">
						<p class="icon">
							<a href="javascript:;"
								link_url="<?=$Dir.FrontDir."productdetail.php?productcode=".$prow[$i2]->productcode?>"
								class="view" title="상세보기"></a> <a href="javascript:;"
								option_chk="<?=$option_chk?>"
								cart_chk="<?=$prow[$i2]->productcode?>" class="cart"
								title="장바구니"></a>
						</p>
					</div></li>
						
						
					
					<?}?>
					</ul>
					<?unset($prow);?>	
				<!--</ul>-->
				<?}?>

			</div>
			<!-- //프로모션 리스트 -->
		</div>
	</div>
	<!-- //메인 컨텐츠 -->
	<div style="margin-top: 30px;"></div>

	<div id="create_openwin" style="display: none"></div>
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

	<div id="overDiv"
		style="position: absolute; top: 0px; left: 0px; z-index: 100; display: none;"
		class="alpha_b60"></div>
	<div class="popup_preview_warp"
		style="margin-left: 50%; left: -459px; display: none;"></div>
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

$(document).ready(function(){

  $(".layer_goods_icon").on("click",function(e){
    	var target = e.target
    	if($(target).attr("class") == "cart" || $(target).attr("class") == "view" ) return; 
    	location.href = $(this).attr("link_url");
    });
    
    $(".cart").on("click",function(e){
    	var chkOption = $(this).attr("option_chk");
    	var chkLink = $(this).attr("cart_chk");
    	if(chkOption == 1){
			CheckForm('',chkLink);
		}else if(chkOption == 3){
	    	$("#productlist_basket").attr("action","../front/productlist_basket.php");
	    	$("#productlist_basket").attr("target","basketOpen");
	    	$("#productcode2").val(chkLink);
			window.open("","basketOpen","width=440,height=420,scrollbars=no,resizable=no, status=no,");
			$("#productlist_basket").submit();
		} 
    });
    
    $(".view").on("click",function(){
    	location.href = $(this).attr("link_url");
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

	<form name="productlist_basket" id="productlist_basket">
		<input type="hidden" name="productcode2" id="productcode2">
	
	</form>


	<form name="back" action="../front/productdetail.php">
		<input type="hidden" name="back2" value="1">
	
	</form>

	</div>
</BODY>
</HTML>
