<?
if(!$maincode){
	$Dir="../";
	include_once($Dir."lib/init.php");
	include_once($Dir."lib/lib.php");
	include_once("lib.inc.php");
	include_once("shopdata.inc.php");

	$qry = "SELECT vip_type FROM tblmember WHERE id= '{$_ShopInfo->memid}' ";
	$res = pmysql_query($qry);
	$row = pmysql_fetch_object($res);
	if ($row->vip_type!="1") {
		alert_go('VIP ZONE을 이용할 수 없습니다.',"../m/");
	}
	$dispLine = 4;				//최초 상품이 보일 라인수
	$limit = $dispLine;		//라인수에 따른 상품수

	include ("header.inc.php");
	$subTitle = "VIP ZONE";
	include ("sub_header.inc.php");
}

$tsql = "SELECT COUNT(*) as t_count FROM tblproduct  WHERE vip_product = 1";
$tres=pmysql_query($tsql,get_mdb_conn());
$trow=pmysql_fetch_object($tres);
$t_count = (int)$trow->t_count;
pmysql_free_result($tres);

?>

<section class="vip_zone_wrap">
	<h2><img src="img/vip_zone_top.jpg" alt="" /></h2>
	<ul class="vip_item_wrap" id="listUL">
		<?
		$sql = "SELECT * FROM tblproduct WHERE vip_product = 1 ";
		$sql.= "LIMIT {$limit} OFFSET 0";
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
			<div class="vip_item">
				<p class="pic"><a href="productdetail.php?pridx=<?=$row->pridx?>"><img src="<?=$Dir.DataDir."shopimages/product/".$row->minimage?>" alt=""  /></a></p>
				<div class="price_info">
					<p class="sale"><img src="img/sale_num/vip_sale_icon_<?=$priceDcPercent?>.gif" alt="" /></p>
					<div class="right_info">
						<ul class="info">
							<li class="name"><a href="productdetail.php?pridx=<?=$row->pridx?>"><?=$row->productname?></a></li>
							<li class="price"><span><?=number_format($row->consumerprice)?></span><?=number_format($row->sellprice)?></li>
						</ul>
					</div>
				</div>
			</div>
		</li>
		<?}?>
	</ul>
	<div class="more">
		<?php if($t_count>$limit): ?>
		<input type="button" value="더보기" onclick="morePrd();" /><p class="arrow">▼</p>
		<?php endif; ?>
	</div>
</section>

<script>
var displayLine = 4;	//노출되는 라인수
function morePrd(){
	$.post('ajax_vipzone.php',{display:4,offsetLine:displayLine},function(data){
		data = trim(data);
		if(data){
			//alert(data);
			
			$("#listUL").html($("#listUL").html()+data);
			displayLine+=4;
			
		}else{
			alert("더이상 상품이 없습니다.");
		}
	});
}
</script>
<?php
	if(!$maincode){
		include ("footer.inc.php"); 
	}
?>