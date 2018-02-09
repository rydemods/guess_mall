#!/usr/local/php/bin/php
<?php
#######################################################################################
# FileName          : cr_get_erp_delivery.php
# Desc              : 2시간 간격으로 실행되어 ERP로부터 배송정보 가져오기
# Last Updated      : 2016-09-27
# By                : JeongHo,Jeong
##!/usr/local/php/bin/php
# [deco@deco1 batch]$ ./run_get_erp_delivery.sh 
#######################################################################################

$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");

@set_time_limit(0);

#$conn = oci_connect("SMK_ONLINE", "SMK_ONLINE_0987", "1.209.88.42/ORA11", "KO16KSC5601");
$conn = oci_connect("SMK_ONLINE", "SMK_ONLINE_0987", "1.209.88.42/ORA11", "AL32UTF8");

echo "START = ".date("Y-m-d H:i:s")."\r\n";

$sql = "SELECT  DOCID, ITEMNO, SHOPCD, PARCELCODE, PARCELCODENM, WAYBILLNO 
        FROM    SMK_ERP.IF_ONLINE_ORDER_RESULT 
        WHERE   INSERTUSER = 'hott_online' 
        AND     RECVDT IS null 
        ORDER BY INSERTDT DESC
        ";
//AND     RECVDT IS null 
//        and     DOCID = '2016092219061955000A' and ITEMNO = '4332' 
$smt = oci_parse($conn, $sql);
oci_execute($smt);

$exe_id		= "||batch";	// 실행자 아이디|이름|타입

$cnt = 0;
while($data = oci_fetch_array($smt, OCI_BOTH+OCI_RETURN_NULLS+OCI_RETURN_LOBS)) {

    foreach($data as $k => $v)
    {
        $data[$k] = pmysql_escape_string($v);
    }

    echo "DOCID = ".$data[DOCID]." / ITEMNO = ".$data[ITEMNO]." / SHOPCD = ".$data[SHOPCD]." / PARCELCODE = ".$data[PARCELCODE]." / WAYBILLNO = ".$data[WAYBILLNO]."\r\n";

    // 에스크로 추가하자..dvcode_indb.php 참조하자.
    list($deli_name)=pmysql_fetch_array(pmysql_query("SELECT company_name FROM tbldelicompany WHERE code='".$data[PARCELCODE]."' "));
    
    $sql = "UPDATE  tblorderproduct SET 
                    deli_gbn    = 'Y', 
                    deli_date   ='".date("YmdHis")."', 
                    deli_com    = '".$data[PARCELCODE]."', 
                    deli_num    = '".$data[WAYBILLNO]."' 
            WHERE   ordercode = '".$data[DOCID]."' 
            AND     idx = ".$data[ITEMNO]." 
            AND     op_step < 40 
            ";
    echo $sql."\r\n";
    
    $ret = pmysql_query($sql,get_db_conn());
    if($err=pmysql_error()) echo $err."\r\n";
    else {

        // 신규상태 변경 추가 - (2016.04.15 - 김재수 추가)
        orderProductStepUpdate($exe_id, $data[DOCID], $data[ITEMNO], '3'); // 배송중

        $sql = "UPDATE  tblorderinfo SET deli_gbn='Y', deli_date='".date("YmdHis")."' ";
        $sql.= "WHERE   ordercode='".$data[DOCID]."' ";
        echo $sql."\r\n";
        pmysql_query($sql,get_db_conn());
        if($err=pmysql_error()) echo $err."\r\n";
                        
        // 신규상태 변경 추가
        orderStepUpdate($exe_id, $data[DOCID], '3', '0' ); // 배송중

        // 도메인 정보
        $sql = "SELECT shopurl FROM tblshopinfo LIMIT 1 ";
        list($shopurl) = pmysql_fetch($sql);
        $tmp = explode("//", $shopurl);
        $shopurl = $tmp[1]."/";
        //echo $_data->shopname."\r\n";
        //echo $shopurl."\r\n";
        //echo $_data->design_mail."\r\n";
        //echo $_data->info_email."\r\n";
        SendDeliMail($_data->shopname, $shopurl, $_data->design_mail, $_data->info_email, $data[DOCID], $data[PARCELCODE], $data[WAYBILLNO], 'N', $data[ITEMNO]);

        // ERP 배송정보 update (RECVDT)
        UpdateErpRecvdt($data[DOCID], $data[ITEMNO], $data[SHOPCD], $conn);
    }
    
    $cnt++;

    #if( ($cnt%1000) == 0) echo "cnt = ".$cnt."\r\n";
    echo "cnt = ".$cnt."-------------------------------------------------------------------------\r\n";
}

oci_free_statement($smt);
oci_close($conn);

pmysql_free_result($ret);

echo "END = ".date("Y-m-d H:i:s")."\r\n";


function UpdateErpRecvdt($DOCID, $ITEMNO, $SHOPCD, $conn) {

    $sql = "Update  SMK_ERP.IF_ONLINE_ORDER_RESULT Set 
                    RECVDT = SYSDATE 
            Where   DOCID = '".$DOCID."' 
            AND     ITEMNO = '".$ITEMNO."' 
            AND     SHOPCD = '".$SHOPCD."' 
            AND     INSERTUSER = 'hott_online' 
            AND     RECVDT IS null 
            ";
    echo $sql."\r\n";
    
    $smt_erp = oci_parse($conn,$sql);
    $stid   = oci_execute($smt_erp);
    if(!$stid) 
    { 
        $error = oci_error(); 
        $bt = debug_backtrace();
        error_log("\r\n".date("Y-m-d H:i:s ").realpath($_SERVER['DOCUMENT_ROOT'].$_SERVER['SCRIPT_NAME']).$error.$bt[0]['line'],3,"/tmp/error_log_hott_erp");
        error_log($sql."\r\n",3,"/tmp/error_log_hott_erp");
    }
    
}
?>

