<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");

/*
$qry = "SELECT staff_type,staff_limit FROM tblmember WHERE id= '{$_ShopInfo->memid}' ";
$res = pmysql_query($qry);
$row = pmysql_fetch_object($res);
$staff_limit = $row->staff_limit; //총구매가능금액
*/

$searchVal = $_REQUEST["searchVal"];

if (!$_ShopInfo->getStaffType()){
	alert_go('STAFF ZONE을 이용할 수 없습니다.',"{$Dir}main/main.php");
}

$memberData = pmysql_fetch("SELECT * FROM tblmember WHERE id='".$_ShopInfo->getMemid()."' ");

$code = $_GET[category];
list($code_a,$code_b,$code_c,$code_d) = sscanf($code,'%3s%3s%3s%3s');
if(strlen($code_a)!=3) $code_a="000";
if(strlen($code_b)!=3) $code_b="000";
if(strlen($code_c)!=3) $code_c="000";
if(strlen($code_d)!=3) $code_d="000";
$code=$code_a.$code_b.$code_c.$code_d;

$likecode = $code_a;
if($code_b!="000") $likecode.=$code_b;
if($code_c!="000") $likecode.=$code_c;
if($code_d!="000") $likecode.=$code_d;

if($code_b=="000"){
	$link_num = "1";
}elseif($code_c=="000"){
	$link_num = "2";
}elseif($code_d=="000"){
	$link_num = "3";
}else{
	$link_num = "4";
}


/*이번달 구매한 금액*/
$curmonth = date("Ym");
$osql = "SELECT sum(price) as pricesum FROM tblorderinfo WHERE id='{$_ShopInfo->memid}' AND ordercode like '{$curmonth}%'";
$ores = pmysql_query($osql);
$orow = pmysql_fetch_object($ores);

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




if(!strstr($_cdata->type,"T")){
	$link_qry = "select c_productcode, c_date_{$link_num} from tblproductlink where c_category like '{$likecode}%' group by c_productcode, c_date_{$link_num} order by c_date_{$link_num} desc";
	$link_result=pmysql_query($link_qry);
	$lik=1;
	$lk_casewhen=array();
	$lk_in=array();
	while($link_data=pmysql_fetch_object($link_result)){
		$linkcode[]=$link_data->c_productcode;
		$lk_casewhen[]=" '".$link_data->c_productcode."' then ".$lik;
		$lk_in[]=$link_data->c_productcode;
		$lik++;
	}
}
//exdebug($link_qry);






# 현재 선택된 카테고리 정보
$sql = "SELECT * FROM tblproductcode WHERE code_a='{$code_a}' AND code_b='{$code_b}' AND code_c='{$code_c}' AND code_d='{$code_d}' order by cate_sort";
$result=pmysql_query($sql,get_db_conn());
if($row=pmysql_fetch_object($result)) {
	$_cdata = $row;
}

# 카테고리 정보
$sql = "SELECT * FROM tblproductcode WHERE group_code!='NO' AND (type!='T' AND type!='TX' AND type!='TM' AND type!='TMX') ORDER BY sequence DESC ";
$code_ii = 0;
$arrCodelist = array();
$result = pmysql_query($sql,get_db_conn());
while($row=pmysql_fetch_object($result)) {
	$code_idx = '';
	if($row->code_a == '004'){
		$code_idx = 1;
	}else if($row->code_a == '001'){
		$code_idx = 2;
	}else if($row->code_a == '002'){
		$code_idx = 3;
	}else if($row->code_a == '003'){
		$code_idx = 4;
	}
	if($code_idx){
		if($row->type=="L" || $row->type=="T" || $row->type=="LX" || $row->type=="TX") {
			$strcodelist.= "clist.code_a='{$row->code_a}';\n";
			$strcodelist.= "clist.code_b='{$row->code_b}';\n";
			$strcodelist.= "clist.code_c='{$row->code_c}';\n";
			$strcodelist.= "clist.code_d='{$row->code_d}';\n";
			$arrCodelist[$code_idx][name] = $row->code_name;
			$arrCodelist[$code_idx][code] = $row->code_a.$row->code_b.$row->code_c.$row->code_d;
		}
		if($row->type=="LM" || $row->type=="TM" || $row->type=="LMX" || $row->type=="TMX") {
			if ($row->code_c=="000" && $row->code_d=="000") {
				$arrCodelist[$code_idx][sub][$code_ii][name] = $row->code_name;
				$arrCodelist[$code_idx][sub][$code_ii][code] = $row->code_a.$row->code_b.$row->code_c.$row->code_d;
				$code_ii++;
			}
		}
	}
}
if(count($arrCodelist) > 0){
	# 배열 순서에 맞게 정렬
	arsort($arrCodelist);
}
pmysql_free_result($result);

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<meta http-equiv="Content-Type" content="text/html; charset=euc-kr" />
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
<HEAD>
<TITLE><?=$_data->shoptitle?> - STAFF ZONE</TITLE>
<META http-equiv="CONTENT-TYPE" content="text/html; charset=EUC-KR">
<META name="description" content="<?=(ord($_data->shopdescription)?$_data->shopdescription:$_data->shoptitle)?>">
<META name="keywords" content="<?=$_data->shopkeyword?>">
<script type="text/javascript" src="<?=$Dir?>lib/lib.js.php"></script>
<script type="text/javascript" src="../js/jquery-1.10.1.js" ></script>

<script language="javascript">
<!--
	if($("#ID_priceDcPercent").val() > 0){
		$("#ID_priceDcPercentLayer").html("단독 "+$("#ID_priceDcPercent").val()+"% 할인");
	}
	function GoPage(block,gotopage) {
		document.form2.block.value=block;
		document.form2.gotopage.value=gotopage;
		document.form2.submit();
	}

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


	$(function(){
		$('.family_nav > li').mouseenter(function(){
			$(this).children('.family_nav > li > a').css('font-weight', 'bold') 
			$(this).children('.family_sub').show()
		})
		
		$('.family_nav > li').mouseleave(function(){
			$(this).children('.family_nav > li > a').css('font-weight', 100)
			$(this).children('.family_sub').hide()
		})	
				
		$('ul.tap_goods>li').mouseenter(function(){
			$(this).find('ul.goods_quick_icon').css('display','block');
		});
		$('ul.tap_goods>li').mouseleave(function(){
			$(this).find('ul.goods_quick_icon').css('display','none');
		});
		
		//상세보기 장바구니 보이게
		$('.new_goods4ea ul.list li').mouseenter(function(){
			$(this).find('.layer_goods_icon').show();
		});
		//상세보기 장바구니 안보이게
		$('.layer_goods_icon').mouseleave(function(){
			$('.layer_goods_icon').hide();
		});
	})
//-->
</script>

</HEAD>

<?php include ($Dir.MainDir.$_data->menu_type.".php");?>

<style>
	.selectedCategory{
		border:1px solid #000;
	}
</style>

<body<?=($_data->layoutdata["MOUSEKEY"][0]=="Y"?" oncontextmenu=\"return false;\"":"")?><?=($_data->layoutdata["MOUSEKEY"][1]=="Y"?" ondragstart=\"return false;\" onselectstart=\"return false;\"":"")?> leftmargin="0" marginwidth="0" topmargin="0" marginheight="0"><?=($_data->layoutdata["MOUSEKEY"][2]=="Y"?"<meta http-equiv=\"ImageToolbar\" content=\"No\">":"")?>
<form name=form2 method=get action="<?=$_SERVER['PHP_SELF']?>">
<input type=hidden name=block value="<?=$block?>">
<input type=hidden name=gotopage value="<?=$gotopage?>">
<input type="hidden" name="category" value="<?=$code?>">
<input type="hidden" name="searchVal" value="<?=$searchVal?>">
</form>

<!-- 메인 컨텐츠 -->
<div class="main_wrap">

	<div class="family_zone_wrap">
		<div class="family_zone_top_wrap">
			<div class="family_zone_top">
				<!-- <ul class="staff_money">
					<li><?=$memberData[name]?>님<br />안녕하세요</li>
					<li>
						<span>총 구매가능 금액 : <strong><?=number_format($memberData[staff_limit_max])?>원</strong></span>
						<span>현재 구매가능 금액 : <strong><?=number_format($memberData[staff_limit])?>원</strong></span>
					</li>
				</ul> -->
				
				<!-- 사내구매 GNB -->
				<div class = 'remainMoney'>
					[<?=$memberData[name]?>]님의 총 구매가능 금액은 <?=number_format($memberData[staff_limit_max])?>원이며, 현재 구매가능 금액은 <?=number_format($memberData[staff_limit])?>원입니다.
					<br>입금계좌 : 00은행 000-000-0000000(<b>예금주</b>: 내자인)
				</div>
				
				<!-- 사내구매 GNB END -->
			</div>
		</div>

		<?
			#$sql = "SELECT * FROM tblproduct WHERE staff_product = '1' AND display='Y' AND sabangnet_flag='N' ";

			$qry = "WHERE 1=1 AND staff_product = '1' AND display='Y' ";
			
			/*
			if($likecode != '000' && $likecode){
				$qry .= " AND a.productcode like '".$likecode."%' ";
			}
			*/
			if($likecode != '000' && $likecode){
				$qry .= " AND a.productcode in ('".implode("','",$lk_in)."') ";
			}
			$tmp_sort=explode("_",$sort);
			if($tmp_sort[0]=="reserve") {
				$addsortsql=",CASE WHEN a.reservetype='N' THEN CAST(a.reserve AS FLOAT)*1 ELSE CAST(a.reserve AS FLOAT)*a.sellprice*0.01 END AS reservesort ";
			}
			$sql = "SELECT a.productcode, a.productname, a.sellprice, a.quantity, a.reserve, a.reservetype, a.production, a.option1, a.option2, a.option_quantity, ";
			if($_cdata->sort=="date2") $sql.="CASE WHEN a.quantity<=0 THEN '11111111111111' ELSE a.date END as date, ";
			$sql.= "a.maximage, a.minimage, a.etctype, a.option_price, a.consumerprice, a.tag, a.selfcode ";
			$sql.= $addsortsql;
			$sql.= "FROM view_tblproduct AS a ";
			$sql.= "LEFT OUTER JOIN tblproductgroupcode b ON a.productcode=b.productcode ";
			$sql.= $qry." ";
			
			if($searchVal != ""){
				$sql .= " AND lower(a.productname) LIKE lower('%{$searchVal}%') ";
			}
			
			$sql.= "AND (a.group_check='N' OR b.group_code='".$_ShopInfo->getMemgroup()."') ";
			if(strlen($not_qry)>0) {
				$sql.= $not_qry." ";
			}

			$sql.= "ORDER BY a.start_no desc ";
			if(count($lk_casewhen)>0) $sql.= " ,case a.productcode when ".implode(" when ",$lk_casewhen)." end ";

			$paging = new Tem001_saveheels_Paging($sql,10,16,'GoPage',true);
			$t_count = $paging->t_count;
			$gotopage = $paging->gotopage;
			$sql = $paging->getSql($sql);
			//exdebug($sql);
			$res = pmysql_query($sql);
		?>

		<!-- 상품리스트 뿌려지는 공간 -->
		<div class="containerBody">

			<div class="family_gnb">
					<ul class="family_nav">
						<?foreach($arrCodelist as $cKey => $cVal){?>
							<?
								$classAddStep1 = '';
								if($code_a == substr($cVal[code], 0, 3)){
									$classAddStep1 = " style = 'border:1px solid #000;font-weight:bold;'";
								}
							?>
							<li class = 'family_nav_sub'>
								<a href="./staff_zone.php?category=<?=$cVal[code]?>" <?=$classAddStep1?>><?=strtoupper($cVal[name])?></a>
								<?if(count($cVal[sub]) > 0){?>
									<ul class="family_sub">
										<?foreach($cVal[sub] as $sKey => $sVal){?>
											<?
												$classAddStep2 = '';
												if($code_a.$code_b == substr($sVal[code], 0, 6)){
													$classAddStep2 = " style = 'color:#000000;text-decoration: underline;'";
												}
											?>
											<li><a href="./staff_zone.php?category=<?=$sVal[code]?>" <?=$classAddStep2?>><?=$sVal[name]?></a></li> 
										<?}?>
									</ul>
								<?}?>
							</li>
						<?}?>                        
					</ul>
				</div>


		<div class="goods_list_wrap">
		
			<div class="goods_list_title" style = 'padding-top:20px;'>
				<h3 class="title" style="float: left;"><?=$_cdata->code_name ? strtoupper($_cdata->code_name) : "모든 카테고리" ?> <span> (Total <em><?=$t_count?></em>)</span></h3>
				
				<div class="header_search" >
					<div class="search" style="float: right; right: 3%; position:relative" >
					<style>
						.staff_search {
							display: block;
							width: 40px;
							height: 40px;
							position: absolute;
							top: -5px;
							right: -5px;
							background: url(../img/icon/icon.png) 10px -160px no-repeat;
						}
					</style>
						<input style="width:250px; height:24px" type="text" id="staff_searchVal" name="staff_searchVal" value="<?=$searchVal?>" onkeyup="javascript:forEnter_staff(event);">
						<a class="staff_search" href="#"></a>
					</div>
				</div>
				
			</div>
			
			<?if($t_count > 0){?>
			<div class="new_goods4ea">
				<!--<ul class="tap_goods">-->
				<ul class="list">
					<?
						while($row=pmysql_fetch_object($res)){
							/*할인율 계산*/
							$SellpriceValue = $row->sellprice;
							if($SellpriceValue != $row->consumerprice && $row->consumerprice > 0){
								$priceDcPercent = floor(100 - ($SellpriceValue / $row->consumerprice * 100));
							}else{
								$priceDcPercent = 0;
							}

							//상품 이미지
							if (strlen($row->minimage)>0 && file_exists($Dir.DataDir."shopimages/product/".$row->minimage)) {
								$imgsrc = $Dir.DataDir."shopimages/product/".urlencode($row->minimage);
							}else{
								$imgsrc = $Dir."images/no_img.gif";
							}
					?>
					<li>
						<span><!--<img src="../img/icon/new.png" alt="" />--><?=viewicon($row->etctype)?></span>
						<div class="goods_A">						
							<a href="#">						
								<p class="img190"><img src="<?=$imgsrc?>" width="190" height="190" alt="" /></p>
								<span class="subject"><?=$row->productname?></span>
								<span class="price"><?=number_format($row->sellprice)?>원</span>
							</a>	
						</div>
						<div class="layer_goods_icon">
							<p class="icon">
								<a href="<?=$Dir.FrontDir."productdetail.php?productcode=".$row->productcode?>" class="view" title="상세보기"></a>
								<a href="javascript:CheckForm('','<?=$row->productcode?>')" class="cart" alt="<?=$row->productcode?>"  title="장바구니"></a>
							</p>
						</div>
					</li>
					<?
						}
					?>
				</ul>
			</div>

				<div class="paging goods_list">
					<?=$a_div_prev_page.$paging->a_prev_page.$paging->print_page.$paging->a_next_page.$a_div_next_page?>
				</div>
			<?}else{?>
				<div style = 'font-size:16px;font-weight:bold;padding-top:40px;margin-bottom: 40px;text-align: center;'>등록된 상품이 없습니다.</div>
			<?}?>
		</div>
		</div>
		<!-- 상품리스트 뿌려지는 공간 END-->



	</div>

</div><!-- //메인 컨텐츠 -->

<form name=form1 id = 'ID_goodsviewfrm' method=post action="<?=$Dir.FrontDir?>basket.php">
	<input type="hidden" name="productcode"></input> 
</form>

<div id="create_openwin" style="display:none"></div>
<?php
include ($Dir."lib/bottom.php") 
?>

</div>
</BODY>

<script>
$('.staff_search').click(function(){
	var searchVal = $('#staff_searchVal').val();
	if(searchVal == ""){
		alert('검색어를 입력하세요.');
		$('#staff_searchVal').focus();
	}else{
		location.replace('/front/staff_zone.php?searchVal='+searchVal);
	}
	
})

function forEnter_staff(e){
	if(event.keyCode == 13){
		$('.staff_search').trigger('click');
	}
}
</script>
</HTML>
