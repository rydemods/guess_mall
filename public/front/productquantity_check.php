<?php
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");

$productcode=$_POST["productcode"];
$optsize=$_POST["optsize"];
$quantity=$_POST["quantity"];
$mode=$_POST["page_type"];
$ordbasketid=$_POST["ordbasketid"];

if($mode=="detail"){
	$query="select * from tblproduct where productcode='".$productcode."'";

	$result=pmysql_query($query);
	$data=pmysql_fetch_object($result);

	$maket_stock=getErpPriceNStock($data->prodcode, $data->colorcode, $optsize, $sync_bon_code);

	if($quantity > $maket_stock[sumqty]){
		echo $maket_stock[sumqty]?$maket_stock[sumqty]:"0";
	}else{
		echo "OK";
	}
}else if($mode=="basket"){
	$ex_basketid=str_replace("|","','",$ordbasketid);
	$query="select sum(b.quantity) as quantity, max(b.productcode) as productcode, max(b.opt2_idx) as opt2_idx, max(p.prodcode) as prodcode, max(p.colorcode) as colorcode, max(p.productname) as productname from tblbasket b left join tblproduct p on (b.productcode=p.productcode) where basketidx in ('".$ex_basketid."') group by basketgrpidx";
	$result=pmysql_query($query);
	$nosumqty_message="";
	$nocheck="";
	while($data=pmysql_fetch_object($result)){
		$maket_stock=getErpPriceNStock($data->prodcode, $data->colorcode, $data->opt2_idx, $sync_bon_code);
		if($data->quantity > $maket_stock[sumqty]){
			$market_stock_sumqty=$maket_stock[sumqty]?$maket_stock[sumqty]:"0";
			$nosumqty_message.=$data->productname."/".$data->opt2_idx.iconv("EUC-KR","UTF-8"," 상품의 임직원 구매 가능수량은 ").$market_stock_sumqty.iconv("EUC-KR","UTF-8","개입니다.\n");
			$nocheck="NO";
		}
	}
	if($nocheck=="NO"){
		echo $nosumqty_message;
	}else{
		echo "OK";
	}
	
}


?>