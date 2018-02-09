<?php
/********************************************************************* 
// 파 일 명		: lib_getup_erp.php 
// 설     명		: ERP에서 가져온데이터를 가공하여 쇼핑몰에 적용한다.
// 작 성 자		: 김재수
// 수 정 자		: 
// 
// 
*********************************************************************/ 
?>
<?
/*include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/lib_erp.php");*/
//getUpErpProductUpdate("002001001000016238");

//상품 정보 업데이트
function getUpErpProductUpdate($productcode, $sizecd='') {	

		list($prodcd, $colorcd, $join_yn, $join_productcode) = pmysql_fetch(" SELECT prodcode, colorcode, join_yn, join_productcode FROM tblproduct WHERE productcode = '{$productcode}' ");
		if ($join_yn == 'Y') { // 결합상품이면
			$productcode_arr	= explode("|", $join_productcode);
			for($i=0;$i < count($productcode_arr);$i++) {
				list($j_prodcd, $j_colorcd) = pmysql_fetch(" SELECT prodcode, colorcode FROM tblproduct WHERE productcode = '{$productcode_arr[$i]}' ");
				if ($j_prodcd !='' && $j_colorcd !='') {
					getUpErpPriceUpdate($productcode_arr[$i], $j_prodcd, $j_colorcd);
				}
			}

			list($total_quantity) = pmysql_fetch(" SELECT quantity FROM tblproduct WHERE productcode IN ('".implode("','", $productcode_arr)."') order by quantity asc limit 1");

			$sql = "UPDATE tblproduct SET ";
			$sql.= "quantity			= '{$total_quantity}' ";
			$sql.= "WHERE productcode = '{$productcode}' ";

			//exdebug("product_update_sql = ".$sql);
			pmysql_query( $sql, get_db_conn() );
		} else {	
			if ($prodcd !='' && $colorcd !='') {
				getUpErpPriceUpdate($productcode, $prodcd, $colorcd, $sizecd);
			}
		}
	//}
}

//상품 기본정보 업데이트
function getUpErpPriceUpdate($productcode, $prodcd, $colorcd, $sizecd='') {	
	if ($prodcd !='' && $colorcd !='') {
		//$pricestock		= getErpPriceNStock($prodcd, $colorcd, $sizecd);
		//15ss상품 본사재고로 가져오도록 변경 2017-06-12
		list($season_yn)=pmysql_fetch("select count(*) as c_count from tblproduct where season_year='2015' and season='K' and productcode='".$productcode."'");	
		if($season_yn){
			$pricestock		= getErpPriceNStock($prodcd, $colorcd, $sizecd, "A1801B");
		}else{
			$pricestock		= getErpPriceNStock($prodcd, $colorcd, $sizecd);
		}
		//exdebug($pricestock);
		//exit;
		//$consumerprice	= $pricestock["tagprice"];		// 해당 상품 tagprice 구하기.(실시간) - consumerprice
		//$sellprice			= $pricestock["polprice"];		// 해당 상품 정책가 구하기.(실시간) - sellprice
		$sumqty			= $sizecd?$pricestock["sumqty"]:'';		

		//if (!$sellprice || $sellprice ==0)
			//$sellprice	= $consumerprice;
		
		//$sql = "UPDATE tblproduct SET ";
		//$sql.= "consumerprice		= (case when erp_price_yn = 'Y' then $consumerprice else consumerprice end), ";
		//$sql.= "sellprice			= (case when erp_price_yn = 'Y' then $sellprice else sellprice end) ";
		//$sql.= "WHERE productcode = '{$productcode}' ";

		//exdebug("product_update_sql = ".$sql);
		//pmysql_query( $sql, get_db_conn() );

		getUpErpSizeStockUpdate($productcode, $prodcd, $colorcd, $sizecd, $sumqty); // 옵션정보 업데이트
	}
}

//상품 옵션정보 업데이트
function getUpErpSizeStockUpdate($productcode, $prodcd, $colorcd, $sizecd='', $sumqty='') {
	if ($productcode !='' && $prodcd !='' && $colorcd !='') {
		if ($sizecd !='') { // 해당사이즈만
			$sizestock["size"][0]			= $sizecd;
			$sizestock["sumqty"][0]		= $sumqty;
		} else { // 전체 사이즈
			$sizestock = getErpProdStock($prodcd, $colorcd);		// 상품 사이즈별 재고 구하기(실시간) - option_quantity
		}
		//exdebug($sizestock);
		//exit;

		if (count($sizestock) > 0) {
			$prod_sizes			= $sizestock["size"];
			$prod_qtys				= $sizestock["sumqty"];

			if($_SERVER["REMOTE_ADDR"] == "218.234.32.36"){
				//exdebug($sizestock);
				//exit;
			}

			$total_option_quantity	= 0;
			$option_nums	= array();
			foreach($prod_sizes as $sizeKey => $sizeVal) {
				$option_code		= $sizeVal;
				$option_quantity	= $prod_qtys[$sizeKey];

				//list($option_num) = pmysql_fetch(" SELECT option_num FROM tblproduct_option WHERE productcode = '{$productcode}' AND option_code='{$option_code}' ");

				//if ($option_num) {
					$sql = "UPDATE tblproduct_option SET ";
					$sql.= "option_quantity			= '{$option_quantity}' ";
					$sql.= "WHERE productcode = '{$productcode}' AND option_code='{$option_code}' RETURNING option_num ";
					//exdebug("product_option_sql = ".$sql);
					$option_num = pmysql_fetch( pmysql_query( $sql, get_db_conn() ));
					$total_option_quantity += $option_quantity;
					if ($option_num[0]) $option_nums[]	= $option_num[0];
				//}
			}

			if ($sizecd =='' && count($option_nums) > 0) {
				$sql = "UPDATE tblproduct_option SET ";
				$sql.= "option_quantity			= '0' ";
				$sql.= "WHERE productcode = '{$productcode}' AND option_num NOT IN ('".implode("','",  $option_nums)."') ";
				//exdebug("product_option_sql = ".$sql);
				pmysql_query( $sql, get_db_conn() );
			}
			
			if ($sizecd !='') { // 해당사이즈만
				list($total_option_quantity) = pmysql_fetch(" SELECT SUM(option_quantity) as total_option_quantity FROM tblproduct_option WHERE productcode = '{$productcode}' ");
			}
			$sql = "UPDATE tblproduct SET ";
			$sql.= "quantity			= '{$total_option_quantity}' ";
			$sql.= "WHERE productcode = '{$productcode}' ";

			//exdebug("product_update_sql = ".$sql);
			pmysql_query( $sql, get_db_conn() );
			//exit;
		}
	}
}











?>