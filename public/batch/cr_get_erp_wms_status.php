#!/usr/local/php/bin/php
<?php
#######################################################################################
# FileName          : cr_get_erp_wms_status.php
# Desc              : 온라인 물류용 상태값 체크
# Desc2             : 2분마다 돌면서 b2c 매장코드의 주문정보를 가져와 erp에서 상태값을 받아온다.
# Last Updated      : 2016-11-28
# By                : JeongHo,Jeong
#   #!/usr/local/php/bin/php
#######################################################################################

$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");

@set_time_limit(0);

echo "START = ".date("Y-m-d H:i:s")."\r\n";

$sql = "SELECT  ordercode, idx, op_step, productcode, opt2_name 
        FROM    tblorderproduct 
        WHERE   1=1
        AND	    op_step = 1 
        AND	    store_code = '006740' 
        ORDER BY idx
        ";
//        and     ordercode = '2016112917214869522A'
$result = pmysql_query($sql, get_db_conn());
$status = "";
while($data = pmysql_fetch_array($result)){

    foreach($data as $k => $v)
    {
        $data[$k] = pmysql_escape_string($v);
    }

    $status = getErpWmsStatus($data[ordercode], $data[idx]); // wms 상태값 가져오기
    //$status = getErpWmsStatus('C0000134119', '134110'); // wms 상태값 가져오기

    echo "ordercode = ".$data[ordercode]." / idx = ".$data[idx]." / productcode = ".$data[productcode]." / opt2_name = ".$data[opt2_name]." / status = ".$status."\r\n";

    // status값이 2이면 배송준비중 처리.
    if($status == "2") {
        // 주문상태 update
        // 싱크커머스 호출
        // 주문상태변경
        $deliOpts = array(
            'ordercode'     => $data[ordercode],      // 주문코드
            'op_idx'        => $data[idx],      // 상세 idx
            'step'          => $status,     // 주문 step
            'exe_id'        => '||batch', // 입력자 
            'sync_type'     => 'M'          // 싱크커머스에서 중복으로 정보를 변경하지 않기위해 체크하는 부분 S 싱크 M 물류
        );
        //print_r( $deliOpts);
        $rtn = deliveryStatusUp( $deliOpts );
    }

    echo "=============================================================================================================================="."\r\n";
}

pmysql_free_result($result);


echo "END = ".date("Y-m-d H:i:s")."\r\n\r\n";

?>
