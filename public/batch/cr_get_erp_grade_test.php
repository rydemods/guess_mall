#!/usr/local/php/bin/php
<?php
#######################################################################################
# FileName          : cr_get_erp_grade_test.php
# Desc              : ERP에서 회원등급 정보 가져와서 쇼핑몰에 적용
# Last Updated      : 2016-12-14
# By                : JeongHo,Jeong
##!/usr/local/php/bin/php
#######################################################################################

$Dir="../";
include ($Dir."lib/init.php");
include ($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");

echo "START = ".date("Y-m-d H:i:s")."\r\n";

// 도메인 정보
$sql        = "SELECT shopurl FROM tblshopinfo LIMIT 1 ";
$row        = pmysql_fetch_object(pmysql_query($sql));
$shopurl    = $row->shopurl;
//exdebug($shopurl);

// 등급별 정보
$sql = "SELECT  group_code, group_name, group_level 
        FROM    tblmembergroup 
        ORDER BY group_code 
        ";
$ret = pmysql_query($sql);
$grade = array();
while($row = pmysql_fetch_object($ret)) {
    $grade[$row->group_code] = $row;
}

$conn = GetErpDBConn();

// ERP 에서 등급정보 가져오기
$sql = "Select  MEMBERNO, CHANGE_DATE, GRADE_TYPE, BEFORE_CODE, AFTER_CODE, REMARK 
        From    ".$erp_account.".IF_ONLINE_MEMBERGRADE 
        Where   RECVDT is NULL 
        Order by INSERTDT 
        ";
$smt = oci_parse($conn, $sql);
oci_execute($smt);
//IFLog($sql, "Grade");

while($data = oci_fetch_array($smt, OCI_BOTH+OCI_RETURN_NULLS+OCI_RETURN_LOBS)) {

    foreach($data as $k => $v)
    {
        $data[$k] = pmysql_escape_string($v);
    }
    $myarray = json_encode_kr($data);
    IFLog($myarray, "Grade");
    echo "json = ".$myarray."\r\n";

    $mem_sql = "select  m.id, m.name, m.email, m.news_yn, coalesce(m.act_point, 0) as act_point 
                from 	tblmember m 
                where	m.mem_seq = ".$data[MEMBERNO]." 
            ";
    list($id, $name, $email, $news_yn, $act_point) = pmysql_fetch($mem_sql, get_db_conn());

    $bf_group = str_pad($data[BEFORE_CODE],4,"0",STR_PAD_LEFT);
    $af_group = str_pad($data[AFTER_CODE],4,"0",STR_PAD_LEFT);

    if($bf_group != $af_group) {
        // =========================================================================
        // 등급 갱신 및 히스토리 저장
        // =========================================================================
        $u_query = "update tblmember set group_code = '".$af_group."' where id = '".$id."'";
        IFLog($u_query, "Grade");
        pmysql_query( $u_query, get_db_conn() );

        $h_query = "insert into tblmemberchange 
                    (mem_id, before_group, after_group, accrue_price, change_date) 
                    values 
                    ('".$id."', '".$grade[$bf_group]->group_name."', '".$grade[$af_group]->group_name."', '".$act_point."', '".date("Y-m-d")."')
                    ";
        //exdebug($h_query);
        pmysql_query( $h_query, get_db_conn() );

        //echo "shopname = ".$_data->shopname."<br>";
        //echo "shopurl = ".$shopurl."<br>";
        //echo "design_mail = ".$_data->design_mail."<br>";
        //echo "info_email = ".$_data->info_email."<br>";
        SendGradeMail($_data->shopname, $shopurl, $_data->design_mail, $_data->info_email, $id, $name, $bf_group, $af_group, $email, $news_yn);
    }
}

oci_free_statement($smt);
oci_close($conn);

echo "END = ".date("Y-m-d H:i:s")."\r\n";


?>
