<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");
include_once($Dir."lib/product.class.php");

	$paymethod = $_REQUEST[paymethod];
	$receipt_yn = $_REQUEST[receipt_yn];

	$product = new PRODUCT();

	$sum_salemenoy=0;

	$sql = "SELECT b.vender FROM tblbasket a, tblproduct b WHERE a.tempkey='".$_ShopInfo->getTempkey()."' ";
	$sql.= "AND a.productcode=b.productcode GROUP BY b.vender ";
	$res=pmysql_query($sql,get_db_conn());

	$cnt=0;
	$sumprice = 0;
	$deli_price = 0;
	$reserve = 0;
	$arr_prlist=array();
	while($vgrp=pmysql_fetch_object($res)) {
		$_vender=NULL;
		if($vgrp->vender>0) {
			$sql = "SELECT deli_price, deli_pricetype, deli_mini, deli_limit FROM tblvenderinfo WHERE vender='".$vgrp->vender."' ";
			$res2=pmysql_query($sql,get_db_conn());
			if($_vender=pmysql_fetch_object($res2)) {
				if($_vender->deli_price==-9) {
					$_vender->deli_price=0;
					$_vender->deli_after="Y";
				}
				if ($_vender->deli_mini==0) $_vender->deli_mini=1000000000;
			}
			pmysql_free_result($res2);
			
		}
		

		$sql = "SELECT a.opt1_idx,a.opt2_idx,a.optidxs,a.quantity,b.productcode,b.productname,b.sellprice,b.membergrpdc, b.option_reserve, ";
		$sql.= "b.reserve,b.reservetype,b.addcode,b.tinyimage,b.option_price,b.option_quantity,b.option1,b.option2, ";
		$sql.= "b.etctype,b.deli_price,b.deli,b.sellprice*a.quantity as realprice, b.selfcode,a.assemble_list,a.assemble_idx,a.package_idx ";
		$sql.= "FROM tblbasket a, tblproduct b WHERE b.vender='".$vgrp->vender."' ";
		$sql.= "AND a.tempkey='".$_ShopInfo->getTempkey()."' ";
		$sql.= "AND a.productcode=b.productcode ";
		//$sql.= "AND a.ord_state=true ";
		$sql.= "ORDER BY a.date DESC ";

		$result=pmysql_query($sql,get_db_conn());

		$mem_dc_price=0;  //회원등급에 의한 할인가 
		$vender_sumprice = 0;
		$vender_delisumprice = 0;//해당 입점업체의 기본배송비 총 구매액
		$vender_deliprice = 0;
		$deli_productprice=0;
		$deli_init = false;

		while($row = pmysql_fetch_object($result)) {
			$sellprice = $row->sellprice;
			$option_price = $row->option_price;
			$tmp_option_price = explode(",", $option_price);
			if($tmp_option_price[$row->opt1_idx-1]) $sellprice = $tmp_option_price[$row->opt1_idx-1];
			$sellprice = $sellprice;

			$dc_data = $product->getProductDcRate($row->productcode);
			$salemoney = getProductDcPrice($sellprice,$dc_data[price])*$row->quantity;
			$sum_salemenoy+=$salemoney;
		}
	}
	echo $sum_salemenoy;

?>