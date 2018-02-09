#!/usr/local/php/bin/php
<?php
//exit;
/**
* CJ대한통운 택배 추척 (crontab batch)
*  1.테이블 복사 [OPENDB]TB_TRACE_SEJUNG020 → [세정public]tblorderdelivery_cj
*  2.배송시작건중 싱크커머스[매장발송]건 배송중으로 상태변경API 호출 (최초 1회만 호출)
*  3.배송완료건(CRG_ST=91) 쇼핑몰 상태변경 처리
*
* 2016.12.05 CUST_USE_NO '요청일+시분초' 추가된 경우 처리
*/
//$Dir = $_SERVER["HOME"]."/public/";
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
		$num = 0;
		$query = "SELECT * FROM TB_TRACE_SHINWON020 WHERE CUST_ID = '30250545' AND EAI_PRGS_ST = '01' ORDER BY SERIAL";
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
			if( chk_sync_deli($row['CUST_USE_NO']) ) $sync_stat_y[$row['INVC_NO']] = $row['CUST_USE_NO'];
			BeginTrans();
			$sql = "insert into tblorderdelivery_cj ({$ins_cols}) values ('{$ins_vals}')";
			pmysql_query($sql);
			$logfile = fopen("/data/WWWROOT/shinwon/public/batch/log/cj/cj_shinwon_".date("Ymd").".txt","a+");
			fwrite( $logfile,"************************************************\r\n");
			fwrite( $logfile,"sql : ".$sql."\r\n");
			fwrite( $logfile,"************************************************\r\n");
			fclose( $logfile );
			chmod("/data/WWWROOT/shinwon/public/batch/log/cj/cj_shinwon_".date("Ymd").".txt",0777);
			if( $err = pmysql_error() ){
				batchlog("[error] {$sql}\n{$err}");
			}else{
				$query1 = "UPDATE TB_TRACE_SHINWON020 SET EAI_PRGS_ST = '03', MODI_EMP_ID = 'SHINWON', MODI_DTIME = SYSDATE WHERE SERIAL = {$row['SERIAL']}";
				$stid1 = oci_parse($cj_dbconn, $query1);
				$return1 = oci_execute($stid1);
				if( $return1 ){
					CommitTrans();
					if( $row['CRG_ST']=="91" ) $op_step_4[$row['INVC_NO']] = true;
				}else{
					RollbackTrans();
					$error = oci_error($stid1);
					batchlog("[error][".$row['SERIAL']."] ".$error['message']);
				}
				oci_free_statement($stid1);
			}
			$num++;
		}
		oci_free_statement($stid);
		

		## 2.싱크커머스 상태변경API (배송중+운송장번호)
		foreach($sync_stat_y as $deli_num => $cust_use_no){
			$orderidxs = array();
			if( substr($cust_use_no,0,2)=="S_" ){
				if( substr_count($cust_use_no,'_') == 2 )
					list($prefix,$ordercode,$orderidxs[0]) = explode("_",$cust_use_no);
				else
					list($prefix,$ordercode,$orderidxs[0],$reqdate) = explode("_",$cust_use_no); // 요청일+시분초 추가됨
			}else
				list($ordercode,$orderidxs[0]) = explode("_",$cust_use_no);
			for ( $i=0; $i < count($orderidxs); $i++ ) {
				$Sync = new Sync();
				$arrayData = array(
					'ordercode'    => $ordercode,
					'delivery_num' => $deli_num,
					'delivery_com' => "01  ", //char(3)
					'delivery_name'=> "CJ대한통운택배",
					'sync_status'  => "Y",   //Y:배송중,반송신청
					'sync_idx'     => $orderidxs[$i]
				);
				$rtn = $Sync->StatusChange($arrayData);
				if( $rtn == "fail" ) batchlog("[error] SyncCommerce API(StatusChange) failed ".json_encode_kr($arrayData));
			}
		}

		## 2.배송중상태의 주문건들 Sync로 전송
		
		$query = "SELECT * FROM TB_TRACE_SHINWON020 WHERE CUST_ID = '30250545' AND CRG_ST in ('11','91') ORDER BY SERIAL";
		$stid = oci_parse($cj_dbconn, $query);
		$return = oci_execute($stid);
		if( !$return ){
			$error = oci_error($stid);
			throw new Exception($error['message'], 1);
		}
		while($row = oci_fetch_array($stid, OCI_ASSOC)){
			if( substr($row[CUST_USE_NO],0,2)=="S_" ){
				if( substr_count($row[CUST_USE_NO],'_') == 2 )
					list($prefix,$ordercode,$orderidxs) = explode("_",$row[CUST_USE_NO]);
				else
					list($prefix,$ordercode,$orderidxs,$reqdate) = explode("_",$row[CUST_USE_NO]); // 요청일+시분초 추가됨
			}else{
				list($ordercode,$orderidxs) = explode("_",$row[CUST_USE_NO]);
			}
			$addwhere="";
			if($row[CRG_ST]=="11"){
				$op_step="2";
				$sync_status="W";
			}else if($row[CRG_ST]=="91"){
				$op_step="3";
				$sync_status="V";
				$addwhere=" and deli_closed!='1' ";
			}

			if($orderidxs=="17953") continue;

			$or_query="select * from tblorderproduct where ordercode='".$ordercode."' and idx='".$orderidxs."' and op_step='".$op_step."'".$addwhere;
			$or_result=pmysql_query($or_query);
			$or_num=pmysql_num_rows($or_result);

			if($or_num){
				$Sync = new Sync();
				$arrayData = array(
					'ordercode'    => $ordercode,
					'sync_status'  => $sync_status,   //Y:배송중,반송신청
					'sync_idx'     => $orderidxs
				);
				$rtn = $Sync->StatusChange($arrayData);
				if( $rtn == "fail" ){
					batchlog("[error] SyncCommerce API(StatusChange) failed ".json_encode_kr($arrayData));
				}else{
					if($row[CRG_ST]=="91"){
						pmysql_query("update tblorderproduct set deli_closed='1' where ordercode='".$ordercode."' and idx='".$orderidxs."'");
						pmysql_query("update tblorderinfo set deli_closed='1' where ordercode='".$ordercode."'");
					}
				}
			}
		}
		oci_free_statement($stid);
		oci_close($cj_dbconn);
		
		## 3.쇼핑몰 주문상태변경(배송중[3]→배송완료[4])
		/*
		if( $op_step_4 )
		foreach(array_keys($op_step_4) as $deli_num){
			$sql = "SELECT ordercode, idx FROM tblorderproduct WHERE op_step = 3 AND deli_num = '{$deli_num}'";
			$res = pmysql_query($sql);
			while ( $row = pmysql_fetch_object($res) ) {
				orderProductStepUpdate($row->ordercode, $row->idx, '4');
			}
			pmysql_free_result($res);
		}*/

	}else{
		throw new Exception("CJ DB Connect Error.", 1);
	}
} catch(Exception $e) {
	batchlog( "[error] ".$e->getMessage() );
}

## 최초 한번만 싱크커머스API 호출
function chk_sync_deli($cust_use_no){
	global $sync_stat_y;
	if( strpos($cust_use_no,"_")===false ) return false;
	if( in_array($cust_use_no,$sync_stat_y) ) return false;
	list($cnt) = pmysql_fetch("select count(*) from tblorderdelivery_cj where cust_use_no='{$cust_use_no}'");
	if( $cnt>0 ) return false;
	else return true;
}
echo date("Ymdhis")."-배치실행\n";
?>
