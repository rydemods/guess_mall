<?php
$Dir="../";

include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include("access.php");


$ordcode=$_GET['ordcode'];
$ordcode=substr($ordcode,0,-1);
$ordcode_chk=str_replace(",","','",$ordcode);
$ordcode_arr=explode(",",$ordcode);


if(count($_POST[chkdeli_com]) > 0){

	foreach($_POST[chkdeli_com] as $cKey => $cVal){
		foreach($cVal as $vKey => $vVal){			
			if(strlen($vKey)==18 && in_array($cKey, $ordcode_arr)) {	// productcode,  ordercode 검증

				//exdebug($_POST[product_serial][$cKey][$vKey] );

					$sql = "UPDATE tblorderproduct SET deli_com='".trim($_POST[chkdeli_com][$cKey][$vKey])."', deli_num='".trim($_POST[chkdeli_num][$cKey][$vKey])."' ";

				if( $_POST[product_serial][$cKey][$vKey]){	// 배열여부 확인
					$sql.= " , product_serial ='";
					foreach( $_POST[product_serial][$cKey][$vKey] as $vv=>$kk){
						if( count($_POST[product_serial][$cKey][$vKey]) > 1){				// 배열 index 여러개
							$sql.= $kk."|"; 
						}else if (count($_POST[product_serial][$cKey][$vKey]) == 1){		// 배열에 index 1개
							$sql.= $kk ;
						}						
					}
					$sql.= "' ";
				}
					$sql.="WHERE ordercode='".$cKey."' AND productcode='".$vKey."' AND NOT (productcode LIKE 'COU%' OR productcode LIKE '999999%') ";
				//exdebug($sql); 
				pmysql_query($sql,get_db_conn());
			}
		}
	}

	msg("변경되었습니다.","order_list.php");
}else{
	msg("변경하실 주문을 선택해주세요.",-1);
}

?>
