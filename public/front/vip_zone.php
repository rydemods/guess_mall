<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");

$qry = "SELECT vip_type FROM tblmember WHERE id= '{$_ShopInfo->memid}' ";
$res = pmysql_query($qry);
$row = pmysql_fetch_object($res);
if ($row->vip_type!="1") {
	alert_go('VIP ZONE을 이용할 수 없습니다.',"{$Dir}main/main.php");
}

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

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<meta http-equiv="Content-Type" content="text/html; charset=euc-kr" />
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
<HEAD>
<TITLE><?=$_data->shoptitle?> - VIP ZONE</TITLE>
<META http-equiv="CONTENT-TYPE" content="text/html; charset=EUC-KR">
<META name="description" content="<?=(ord($_data->shopdescription)?$_data->shopdescription:$_data->shoptitle)?>">
<META name="keywords" content="<?=$_data->shopkeyword?>">
<script type="text/javascript" src="<?=$Dir?>lib/lib.js.php"></script>
<SCRIPT LANGUAGE="JavaScript">
<!--
if($("#ID_priceDcPercent").val() > 0){
	$("#ID_priceDcPercentLayer").html("단독 "+$("#ID_priceDcPercent").val()+"% 할인");
}
function GoPage(block,gotopage) {
	document.form2.block.value=block;
	document.form2.gotopage.value=gotopage;
	document.form2.submit();
}

//-->
</SCRIPT>

</HEAD>

<?php include ($Dir.MainDir.$_data->menu_type.".php");?>

<body<?=($_data->layoutdata["MOUSEKEY"][0]=="Y"?" oncontextmenu=\"return false;\"":"")?><?=($_data->layoutdata["MOUSEKEY"][1]=="Y"?" ondragstart=\"return false;\" onselectstart=\"return false;\"":"")?> leftmargin="0" marginwidth="0" topmargin="0" marginheight="0"><?=($_data->layoutdata["MOUSEKEY"][2]=="Y"?"<meta http-equiv=\"ImageToolbar\" content=\"No\">":"")?>
<form name=form2 method=get action="<?=$_SERVER['PHP_SELF']?>">
<input type=hidden name=block value="<?=$block?>">
<input type=hidden name=gotopage value="<?=$gotopage?>">
</form>
<?
	
?>


<!-- 메인 컨텐츠 -->
<div class="main_wrap">
	<div class="vip_zone_wrap">
		<div class="vip_zone">
			<ul class="vip_goods_list">
				<?
				$sql = "SELECT * FROM tblproduct WHERE vip_product = 1";
				$paging = new Tem001_saveheels_Paging($sql,10,4,'GoPage',true);
				$t_count = $paging->t_count;
				$gotopage = $paging->gotopage;
				$sql = $paging->getSql($sql);
				$res = pmysql_query($sql);
				while($row=pmysql_fetch_object($res)){
					/*할인율 계산*/
					$SellpriceValue = $row->sellprice;
					if($SellpriceValue != $row->consumerprice && $row->consumerprice > 0){
						$priceDcPercent = floor(100 - ($SellpriceValue / $row->consumerprice * 100));
					}else{
						$priceDcPercent = 0;
					}
				?>
					<li>
						<a href="productdetail_vip.php?productcode=<?=$row->productcode?>">
						<img src="<?=$Dir.DataDir."shopimages/product/".$row->minimage?>" alt="" style="width:425px;height:425px;" /></a>
						<div class="goods_info">
							<div class="sale_per"><span><?=$priceDcPercent?><em>%</em></span></div>
							<dl>
								<dt><a href="productdetail_vip.php?productcode=<?=$row->productcode?>"><?=$row->productname?></a></dt>
								<dd>
									<span class="original"><?=number_format($row->consumerprice)?></span>
									<span class="ment">VIP특가 : </span><span class="dc"><?=number_format($row->sellprice)?></span>
								</dd>
							</dl>
						</div>
					</li>
					<input type = 'hidden' value = '<?=$priceDcPercent?>' id = 'ID_priceDcPercent'>
				<?}?>
			</ul>
		</div>
		
		<div class="page_vip pb_30">
		<?=$a_div_prev_page.$paging->a_prev_page.$paging->print_page.$paging->a_next_page.$a_div_next_page?>
		</div>
	</div>
</div><!-- //메인 컨텐츠 -->


<div id="create_openwin" style="display:none"></div>
<?php
include ($Dir."lib/bottom.php") 
?>

</div>
</BODY>
</HTML>
