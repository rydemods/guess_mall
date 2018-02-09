#!/usr/local/php/bin/php
<?php
//exit;
#######################################################################################
# FileName          : cr_auto_order_cancel.php
# Desc              : 매일 30분마다 실행되어 주문접수상태인 주문을 일괄 취소 (품절시, 3일 지난 주문)
# Last Updated      : 2016-10-25
# By                : 김재수
##!/usr/local/php/bin/php
# [deco@deco1 batch]$ ./run_auto_order_cancel.sh 
#######################################################################################

$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");

@set_time_limit(0);

if (!$_shopdata) {
	$_shopdata=new ShopData($_ShopInfo);
	$_shopdata=$_shopdata->shopdata;
	$_ShopInfo->getPgdata();
	$_shopdata->escrow_id	= $_data->escrow_id;
	$_shopdata->trans_id		= $_data->trans_id;
	$_shopdata->virtual_id		= $_data->virtual_id;
	$_shopdata->card_id		= $_data->card_id;
	$_shopdata->mobile_id	= $_data->mobile_id;
}

$exe_id		= "||batch";	// 실행자 아이디|이름|타입

echo "==================================================== ORDER CANCEL (".date("Y-m-d H:i:s").") ====================================================\r\n";
// 주문접수 상태인 주문을 가져온다.
$sql = "SELECT * FROM tblorderinfo where oi_step1='0' AND oi_step2='0' ORDER BY ordercode DESC ";
echo "sql = ".$sql."\r\n";
$ret = pmysql_query($sql);
while($_ord = pmysql_fetch_object($ret)) {

	$c_oi_step1			= $_ord->oi_step1;
	$c_oi_step2			= $_ord->oi_step2;
	$c_ordercode			= $_ord->ordercode;
	$c_paymethod		= $_ord->paymethod;

	$c_idxs					= "";
	$c_size_yn				= "N";
	$c_date_yn				= "N";
	$c_size_n_prod		= "";

	list($c_idxs)=pmysql_fetch_array(pmysql_query("select array_to_string(array_agg(idx),'|') from tblorderproduct where ordercode='{$c_ordercode}'"));


	$c_order_regdt		= substr($c_ordercode, 0, 14);
	$chk_dt					= date("Ymd235959", strtotime("-4 day", time()));  
	//$chk_dt					= date("Ymd235959");  

	if ($c_order_regdt <= $chk_dt) { // 3일전 주문접수건이면
		$c_date_yn				= "Y";
	} 
/*
	echo "-------------------------------------------- ORDER PRODUCT SIZE INFO --------------------------------------------\r\n";
	#상품정보
	$prod_sql="select op.idx, op.productcode, op.opt1_name, op.opt2_name, p.prodcode, p.colorcode from tblorderproduct op LEFT JOIN tblproduct p ON op.productcode=p.productcode WHERE op.ordercode= '".$c_ordercode."' ";
	$prod_result=pmysql_query($prod_sql);
	while($_prod=pmysql_fetch_object($prod_result)) {
		$c_idxs	.= $c_idxs?"|".$_prod->idx:$_prod->idx;
		if ($_prod->opt1_name == 'SIZE') { // SIZE 옵션일 경우

			echo "상품코드 : ".$_prod->productcode."\r\n";
			echo "ERP 상품코드 : ".$_prod->prodcode."\r\n";
			echo "ERP 색상코드 : ".$_prod->colorcode."\r\n";
			echo "ERP 사이즈코드 : ".$_prod->opt2_name."\r\n";
			if ($c_date_yn	== 'N') {
				// 상품 해당 사이즈의 총재고 구하기
				$sizeSum	= getErpProdSizeStock($_prod->prodcode, $_prod->colorcode, $_prod->opt2_name);
				$c_size_yn	= $c_size_yn=='N'&&$sizeSum<=0?$c_size_yn='Y':$c_size_yn;
				
				if ($sizeSum <= 0)
					$c_size_n_prod	.= $c_size_n_prod?" / ".$_prod->productcode:$_prod->productcode;

				echo "ERP 사이즈 총재고 : ".$sizeSum."\r\n";
			}
	 echo "----------------------------------------------------------------------------------------------------------------\r\n";
		}
	}
	pmysql_free_result($product_result);
*/
	$fail_cnt	= 0;
		
	/*if (strstr("OQ", $c_paymethod) && $c_oi_step1 == '0') {	
		//NICE에 발급계좌 해지로 보낸다.

		$pgid_info=GetEscrowType($_shopdata->escrow_id);
		$pg_type=$pgid_info["PG"];				
		$pg_type=trim($pg_type);

		// 발급계좌 해지로 보낸다.
		$query="sitecd={$pgid_info["ID"]}&sitekey={$pgid_info["KEY"]}&ordercode=".$c_ordercode;
		$cancel_data=SendSocketPost($_SERVER['HTTP_HOST'],str_replace($_SERVER['HTTP_HOST'],"",$_ShopInfo->getShopurl())."paygate/".$pg_type."/escrow_cancel.php",$query);
		$cancel_data=substr($cancel_data,strpos($cancel_data,"RESULT=")+7);
		if (substr($cancel_data,0,2)!="OK") {
			$tempdata=explode("|",$cancel_data);
			$errmsg="정산보류 정보를 에스크로 서버에 전달하지 못했습니다.\\n\\n잠시후 다시 실행하시기 바랍니다.";
			if(ord($tempdata[1])) $errmsg=$tempdata[1];
			if(ord($errmsg)) {
				$alert_text = $errmsg;
				$fail_cnt++;
			}
		} else {
			$tempdata=explode("|",$cancel_data);
			if(ord($tempdata[1])) $errmsg=$tempdata[1];
			if(ord($errmsg)) {
				//$alert_text = $errmsg;
			}
		}
	}*/

     echo "-------------------------------------------- ORDER CANCEL START --------------------------------------------\r\n";
	if ($fail_cnt == 0) {
		//if ($c_size_yn == 'Y' || $c_date_yn == 'Y') {
		if ($c_date_yn == 'Y') {
			// 주문취소를 한다.
			orderCancel($exe_id, $c_ordercode, $c_idxs, $c_oi_step1, $c_oi_step2, $c_oi_step1, $c_paymethod );
			
			echo "CANCEL INFO : {$exe_id} / {$c_ordercode} / {$c_idxs} / {$c_oi_step1} / {$c_oi_step2} / {$c_oi_step1} / {$c_paymethod} \r\n";

			$sqlOiCancel = "UPDATE tblorderinfo SET deli_gbn = 'C' WHERE ordercode = '".$c_ordercode."'";		
			pmysql_query($sqlOiCancel,get_db_conn());			
			echo "sqlOiCancel = ".$sqlOiCancel."\r\n";

			//상품들도 모두 주문취소로 변경한다.
			$sqlOpCancel = "UPDATE tblorderproduct SET deli_gbn = 'C' WHERE ordercode= '".$c_ordercode."'";		
			pmysql_query($sqlOpCancel,get_db_conn());	
			echo "sqlOpCancel = ".$sqlOpCancel."\r\n";
			echo $c_ordercode." : 해당 주문 취소 완료";
			//if ($c_size_yn == 'Y') echo " / (".$c_size_n_prod." : 재고없음)\r\n";
			if ($c_date_yn == 'Y') echo " / (4일전 주문건)\r\n";
			echo "-------------------------------------------- ORDER CANCEL OK --------------------------------------------\r\n";
		} else {
			echo $c_ordercode." : 해당 주문 정상주문건\r\n";
		}

	}else{
        echo $c_ordercode." : 해당 주문 PG 통신 에러\r\n";
        echo "-------------------------------------------- ORDER CANCEL FAIL --------------------------------------------\r\n";
	}
     echo "-------------------------------------------- ORDER CANCEL END --------------------------------------------\r\n\r\n\r\n\r\n\r\n";
}

echo "==================================================== ORDER CANCEL END(".date("Y-m-d H:i:s").") ====================================================\r\n\r\n\r\n\r\n\r\n";
pmysql_free_result($ret);
?>
