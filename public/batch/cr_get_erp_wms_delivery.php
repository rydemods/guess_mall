#!/usr/local/php/bin/php
<?php
#######################################################################################
# FileName          : cr_get_erp_wms_delivery.php
# Desc              : 2시간 간격으로 실행되어 ERP로부터 배송정보 가져오기
# Last Updated      : 2016-11-29
# By                : JeongHo,Jeong
##!/usr/local/php/bin/php
# [deco@deco1 batch]$ ./run_get_erp_wms_delivery.sh 
#######################################################################################

$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");

@set_time_limit(0);

//$conn = oci_connect("SMK_ONLINE", "SMK_ONLINE_0987", "1.209.88.42/ORA11", "AL32UTF8");
$conn = GetErpDBConn();

echo "START = ".date("Y-m-d H:i:s")."\r\n";

$sql = "SELECT  ordercode, idx, op_step, productcode, opt2_name 
        FROM    tblorderproduct 
        WHERE   1=1
        AND	    op_step = 2 
        AND	    store_code = '006740' 
        ORDER BY idx
        ";
//        and     ordercode = '2016112920413724639A'
$result = pmysql_query($sql, get_db_conn());
$status = "";
while($data = pmysql_fetch_array($result)){

    foreach($data as $k => $v)
    {
        $data[$k] = pmysql_escape_string($v);
    }

    $delivery = getErpWmsDelivery($data[ordercode], $data[idx], $conn); // wms 배송정보 가져오기
    //$delivery = getErpWmsDelivery('2016102113302523907A', '4831', $conn); // wms 배송정보 가져오기

    echo "ordercode = ".$data[ordercode]." / idx = ".$data[idx]." / shopcd = ".$delivery[shopcd]." / parcelcode = ".$delivery[parcelcode]." / waybillno = ".$delivery[waybillno]." / insertdt = ".$delivery[insertdt]."\r\n";

    if($delivery[waybillno] && $delivery[parcelcode]) {
        // 주문상태 update
        // 싱크커머스 호출
        // 주문상태변경
        $deliOpts = array(
            'ordercode'     => $data[ordercode],      // 주문코드
            'op_idx'        => $data[idx],      // 상세 idx
            'step'          => '3',     // 주문 step
            'exe_id'        => '||batch', // 입력자 
            'delivery_com'  => trim($delivery[parcelcode]),      // 배송회사 코드
            'delivery_num'  => $delivery[waybillno],      // 송장번호
            //'delivery_name' => '',      // 배송회사명
            'delivery_date' => $delivery[insertdt],      // 배송일
            'sync_type'     => 'M'          // 싱크커머스에서 중복으로 정보를 변경하지 않기위해 체크하는 부분 S 싱크 M 물류
        );
        //print_r( $deliOpts);
        $rtn = deliveryStatusUp( $deliOpts );

        // 도메인 정보
        $sql = "SELECT shopurl FROM tblshopinfo LIMIT 1 ";
        list($shopurl) = pmysql_fetch($sql);
        $tmp = explode("//", $shopurl);
        $shopurl = $tmp[1]."/";
        #echo $_data->shopname."\r\n";
        #echo $shopurl."\r\n";
        #echo $_data->design_mail."\r\n";
        #echo $_data->info_email."\r\n";
        SendDeliMail($_data->shopname, $shopurl, $_data->design_mail, $_data->info_email, $data[ordercode], trim($delivery[parcelcode]), $delivery[waybillno], 'N', $data[idx]);

        // ERP 배송정보 update (RECVDT), 실서버에서 주석 풀자.
        //UpdateErpRecvdt($data[ordercode], $data[idx], $conn);
    }

    echo "=============================================================================================================================="."\r\n";
}

pmysql_free_result($result);
oci_close($conn);

echo "END = ".date("Y-m-d H:i:s")."\r\n\r\n";
exit;

// 해당 상품 배송정보 구하기.(실시간)
function getErpWmsDelivery($ordercode, $idx, $conn) {

    global $erp_account;

    $sql = "SELECT *  
            FROM (
                  SELECT    ROW_NUMBER() OVER(PARTITION BY SHOPCD ORDER BY INSERTDT desc) rn, 
                            TO_CHAR(INSERTDT, 'YYYYMMDDHH24MISS') INSERTDT, SHOPCD, PARCELCODE, WAYBILLNO  
                  FROM	    ".$erp_account.".IF_ONLINE_ORDER_RESULT 
                  WHERE     1=1 
                  AND		DOCID = '".$ordercode."' 
                  AND		ITEMNO = ".$idx."  
                  AND		DOCTYPECD = 'NORM' 
                  AND		WAYBILLNO IS NOT NULL 
            ) a
            WHERE 1=1
            AND rn = 1
            ";
    $smt_delivery = oci_parse($conn, $sql);
    oci_execute($smt_delivery);
    //echo $sql."\r\n";

    $data = oci_fetch_assoc($smt_delivery);

    $deli_info = array();
	$deli_info[insertdt]    = $data['INSERTDT'];
	$deli_info[shopcd]      = $data['SHOPCD'];
    $deli_info[parcelcode]  = $data['PARCELCODE'];
    $deli_info[waybillno]   = $data['WAYBILLNO'];

    oci_free_statement($smt_delivery);

    return $deli_info;
}

function UpdateErpRecvdt($DOCID, $ITEMNO, $conn) {

    global $erp_account;

    $sql = "Update  ".$erp_account.".IF_ONLINE_ORDER_RESULT Set 
                    RECVDT = SYSDATE 
            Where   DOCID = '".$DOCID."' 
            AND     ITEMNO = '".$ITEMNO."' 
            AND		DOCTYPECD = 'NORM' 
            AND     RECVDT IS NULL 
            AND		WAYBILLNO IS NOT NULL 
            ";
    //echo $sql."\r\n";
    
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

