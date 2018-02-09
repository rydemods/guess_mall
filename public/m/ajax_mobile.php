<?
Header("Pragma: no-cache");
header("Content-Type:text/html;charset=EUC-KR");
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/cache_main.php");
include_once($Dir."lib/timesale.class.php");
include_once($Dir."conf/config.php");
include_once($Dir."lib/shopdata.php");
include_once("lib.inc.php");
include_once("shopdata.inc.php");
include_once($Dir."lib/product.class.php");

$catmobile=$_REQUEST['catmobile'];
$offset = $_REQUEST['offsetLine'];
$display = $_REQUEST['displayLine'];


$mobilegoods= mobile_disp_goods();
$todaygoods=$mobilegoods[1];
$newgoods=$mobilegoods[2];
$goods;
if($catmobile==1){
	$goods=$todaygoods;
}elseif($catmobile==2){
	$goods=$newgoods;
}

if(is_file($Dir.DataDir."shopimages/product/".$goods[$offset][maximage])){
	
?>
	<ul>
	<?for($i=0; $i<$display; $i++){?>
		<li>
			<div class="goods_wrap">
				<?if(is_file($Dir.DataDir."shopimages/product/".$goods[$offset+$i][maximage])){?>
				<a href="productdetail.php?pridx=<?=$goods[$offset+$i][pridx]?>">
				<img src="<?=$Dir.DataDir."shopimages/product/".$goods[$offset+$i][maximage]?>" alt="" />
				<div class="infobox">
					<span class="name"><?=$goods[$offset+$i][productname];?></span>
					<div class="pricebox">
						<strong>
							<del><?=number_format($goods[$offset+$i][consumerprice])?></del><br>
							<?=number_format($goods[$offset+$i][sellprice])?>
							
						</strong>
					</div>
				<?}?>
				</div>
				</a>
			</div>
		</li>
	<?}?>
		</ul>
<?}?>
