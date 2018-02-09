<?php
#######################################################################################
# FileName          : cr_get_erp_grade.php
# Desc              : ERP에서 회원등급 정보 가져와서 쇼핑몰에 적용(월 1회)
# Last Updated      : 2017-04-13
# By                : peter.Kim
##!/usr/local/php/bin/php
#######################################################################################

$Dir="../";
include ($Dir."lib/init.php");
include ($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");

echo "START = ".date("Y-m-d H:i:s")."\r\n";

// 등급별 정보
$sql = "SELECT  group_code, group_name, group_level 
			FROM    tblmembergroup 
			ORDER BY group_code 
 ";
$ret = pmysql_query($sql);
$grade_n = array();
$grade_c = array();
while($row = pmysql_fetch_object($ret)) {
    $grade_n[$row->group_name] = $row;
    $grade_c[$row->group_code] = $row;
}

$conn = GetErpDBConn();

// ERP 에서 등급정보 가져오기
$sql = "Select  MEMBER_ID, 
		CASE WHEN LEVEL_ID='D' AND RNK='1' THEN 'B3' ELSE LEVEL_ID||RNK END AS GROUP_CODE 
        From  TA_SP092 
        Where  1=1
		AND BYYMM = TO_CHAR(SYSDATE,'YYYYMM')
		AND LEVEL_GB = 'N'
		AND BRAND = 'E'
        ";
$smt = oci_parse($conn, $sql);
oci_execute($smt);

$cnt = 0;
while($data = oci_fetch_array($smt, OCI_BOTH+OCI_RETURN_NULLS+OCI_RETURN_LOBS)) {

    foreach($data as $k => $v)
    {
        $data[$k] = pmysql_escape_string($v);
    }
    $myarray = json_encode_kr($data);
    IFLog("json = ".$myarray, "Grade");
    echo "json = ".$myarray."\r\n";

    $erp_shopmem_id = trim($data[MEMBER_ID]);

    $mem_sql = "SELECT id, name, email, news_yn, coalesce(act_point, 0) as act_point, group_code 
						FROM tblmember
						WHERE erp_shopmem_id = '".$erp_shopmem_id."' 
	 ";
	 //exdebug($mem_sql);
    list($id, $name, $email, $news_yn, $act_point, $bf_group) = pmysql_fetch($mem_sql, get_db_conn());
	if ($id) {
		//exdebug($id);
		$group_code	= trim($data[GROUP_CODE]);
		$group_name	= $erp_group_code[$group_code];
		$af_group	= $grade_n[$group_name]->group_code;

		if($bf_group != $af_group) {
			// =========================================================================
			// 등급 갱신 및 히스토리 저장
			// =========================================================================
			$u_query = "update tblmember set group_code = '".$af_group."' where id = '".$id."'";
			IFLog("OK_u_query = ".$u_query, "Grade");
			echo "OK_u_query = ".$u_query."\r\n";
			//exdebug($u_query);
			pmysql_query( $u_query, get_db_conn() );

			$h_query = "insert into tblmemberchange (mem_id, before_group, after_group, accrue_price, change_date) values ('".$id."', '".$grade_c[$bf_group]->group_name."', '".$grade_c[$af_group]->group_name."', '".$act_point."', '".date("Y-m-d")."') ";
			IFLog("OK_h_query = ".$h_query, "Grade");
			echo "OK_h_query = ".$h_query."\r\n";
			//exdebug($h_query);
			pmysql_query( $h_query, get_db_conn() );
		} else {
		IFLog("FAIL = 등급 변동 없음", "Grade");
		echo "FAIL = 등급 변동 없음\r\n";
		//exdebug("FAIL = 등급 변동 없음");
		}
	} else {
		IFLog("FAIL = 회원이 존재하지 않음", "Grade");
		echo "FAIL = 회원이 존재하지 않음\r\n";
		//exdebug("FAIL = 회원이 존재하지 않음");
	}

    $cnt++;
    if( ($cnt%1000) == 0) {
		echo "cnt = ".$cnt."\r\n";
		sleep(10);
	}
}

oci_free_statement($smt);
oci_close($conn);

echo "END = ".date("Y-m-d H:i:s")."\r\n";


?>
