<?
header("Content-Type:text/html;charset=EUC-KR");
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once("lib.inc.php");
include_once("shopdata.inc.php");


$offset = $_POST['offsetLine'];
$display = $_POST['display'];


$tsql = "SELECT COUNT(*) as t_count FROM tblproduct  WHERE vip_product = 1";
$tres=pmysql_query($tsql,get_mdb_conn());
$trow=pmysql_fetch_object($tres);
$t_count = (int)$trow->t_count;
pmysql_free_result($tres);


$sql = "SELECT * FROM tblproduct WHERE vip_product = 1 ";
$sql.= "LIMIT {$display} OFFSET {$offset}";
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