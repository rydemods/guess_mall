<?php 
	$Dir="../";
	include_once($Dir."lib/init.php");
	include_once($Dir."lib/lib.php");
	include_once($Dir."lib/shopdata.php");

	header("Content-type: text/html; charset=euc-kr");

	$id = $_ShopInfo->getMemid();

	$sql = "SELECT 
					DISTINCT op.productcode, p.productname
				FROM 
					tblorderinfo oi
					left join tblorderproduct op on op.ordercode = oi.ordercode 
					left join tblproduct p on p.productcode = op.productcode 
					left join tblproductbrand pb on pb.bridx = p.brand 
					left join tblmember m on oi.id=m.id		
				WHERE 
					oi.sabangnet_order_id = '' 
					AND oi.id = '".$id."'
					AND 
							(
								(oi.deli_gbn='N' AND ((SUBSTR(oi.paymethod,1,1) IN ('B','O','Q') AND (oi.bank_date IS NULL OR oi.bank_date='')) OR (SUBSTR(oi.paymethod,1,1) IN ('C','P','M','V') AND oi.pay_flag!='0000' AND oi.pay_admin_proc='C')))
								OR
								(oi.deli_gbn='N' AND ((SUBSTR(oi.paymethod,1,1) IN ('B','O','Q') AND LENGTH(oi.bank_date)=14) OR (SUBSTR(oi.paymethod,1,1) IN ('C','P','M','V') AND oi.pay_admin_proc!='C' AND oi.pay_flag='0000')))
								OR 
								oi.deli_gbn='S' OR oi.deli_gbn='Y' 
							)
					
				ORDER BY 
					op.productcode
				DESC "; //AND length(op.productcode) = 18
	$result=pmysql_query($sql,get_db_conn());
	while($row=pmysql_fetch_object($result)){
		if(!$row->productname) continue;
?>
	<div><a href="javascript:;" class="btn_mypage_s CLS_settingProductCode" productcode = '<?=$row->productcode?>'>선택</a>&nbsp;<?=strcutDot($row->productname, 40)?></div>
<?
	}
?>