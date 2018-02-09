#!/usr/local/php/bin/php
<?php
exit;
/**
* CJ대한통운 택배 추척 (crontab batch)
*  1.테이블 복사 [OPENDB]TB_TRACE_SEJUNG020 → [세정public]tblorderdelivery_cj
*  2.배송시작건중 싱크커머스[매장발송]건 배송중으로 상태변경API 호출 (최초 1회만 호출)
*  3.배송완료건(CRG_ST=91) 쇼핑몰 상태변경 처리
*
* 2016.12.05 CUST_USE_NO '요청일+시분초' 추가된 경우 처리
*/

$Dir = "../";
include($Dir."lib/init.php");
include($Dir."lib/lib.php");
include($Dir."batch/batch.lib.php");
include($Dir."conf/config.delivery_cj.php");

$ins_cols = implode(",",$cj_trace_cols);
$sync_stat_y = array();

try {

	if( $cj_dbconn = cj_dbconnect() ){
		## 1.테이블 복사
//		$query = "SELECT * FROM TB_TRACE_SHINWON020 WHERE CUST_ID = '30250545' AND EAI_PRGS_ST = '01' ORDER BY SERIAL";
		$query = "SELECT * FROM TB_TRACE_SHINWON020 WHERE CUST_ID = '30250545' AND CUST_USE_NO ='S_2017112122293593003A_49993_21223155'";
		$stid = oci_parse($cj_dbconn, $query);
		$return = oci_execute($stid);
		if( !$return ){
			$error = oci_error($stid);
			throw new Exception($error['message'], 1);
		}
		while($row = oci_fetch_array($stid, OCI_ASSOC)){
			$vals = array();
			foreach( $cj_trace_cols as $col ){
				$vals[] = $row[strtoupper($col)];
			}
			$ins_vals = implode("','",$vals);
			$sql = "insert into tblorderdelivery_cj ({$ins_cols}) values ('{$ins_vals}')";
			echo $sql;
			//pmysql_query($sql);
		}
		oci_free_statement($stid);
		
		//pmysql_query("update tblorderproduct set deli_closed='1' where ordercode='".$ordercode."' and idx='".$orderidxs."'");
		//pmysql_query("update tblorderinfo set deli_closed='1' where ordercode='".$ordercode."'");

		oci_close($cj_dbconn);
		
	}else{
		throw new Exception("CJ DB Connect Error.", 1);
	}
} catch(Exception $e) {
	batchlog( "[error] ".$e->getMessage() );
}

echo date("Ymdhis")."-배치실행\n";
?>
