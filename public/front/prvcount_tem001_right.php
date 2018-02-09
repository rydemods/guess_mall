<?php 
	header("Content-Type: text/plain");
	header("Content-Type: text/html; charset=euc-kr");
	$Dir="../";
	include_once($Dir."lib/init.php");
	include_once($Dir."lib/lib.php");
	include_once($Dir."lib/shopdata.php");

	$sqlVcount = "SELECT * FROM tblproduct WHERE display = 'Y' order by vcnt desc limit 5 offset 0;";
	$resultVcount=pmysql_query($sqlVcount,get_db_conn());
	while($row=pmysql_fetch_object($resultVcount)) {
		$sellPrice = 0;
		$optcode = substr($row->option1,5,4);
		$miniq = 1;
		if (ord($row->etctype)) {
			$etctemp = explode("",$row->etctype);
			for ($i=0;$i<count($etctemp);$i++) {
				if (strpos($etctemp[$i],"MINIQ=")===0)			$miniq=substr($etctemp[$i],6);
			}
		}

		if(strlen($dicker=dickerview($row->etctype,number_format($row->sellprice),1))>0){
			$sellPrice = $dicker;
		} else if(strlen($optcode)==0 && strlen($row->option_price)>0) {
			$sellPrice = $row->sellprice;
		} else if(strlen($optcode)>0) {
			$sellPrice = $row->sellprice;
		} else if(strlen($row->option_price)==0) {
			if($row->assembleuse=="Y") {
				$sellPrice = ($miniq>1?$miniq*$row->sellprice:$row->sellprice);
			} else {
				$sellPrice = $row->sellprice;
			}
		}
?>
	<ul>
		<li>
			<a href="../front/productdetail.php?productcode=<?=$row->productcode?>">
				<?if (strlen($row->maximage)>0 && file_exists($Dir.DataDir."shopimages/product/".$row->maximage)) { ?>
					<img src="<?=$Dir.DataDir."shopimages/product/".urlencode($row->maximage)?>" width = '120'>
				<?} else {?>
					<img src="<?=$Dir?>images/no_img.gif" border="0" align="center" width = '120'>
				<?}?>
			</a>
		</li>
		<li class="name"><?=$row->productname?></li>
		<li class="price"><?=number_format($sellPrice)?>원</li>
	</ul>
<?
	}
?>