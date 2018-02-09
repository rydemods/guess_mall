<?php 
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");

$ordercode = $_POST['ordercode'];   // 주문코드
$idxs      = $_POST['idxs'];        // op_idx

$code = 1;
$msg  = '';
$tmpIdx = '';

$bSucc = true;

try{

    if( $ordercode == '' ){
        throw new Exception('주문 코드가 없습니다.', 0 );
    }

    if( $idxs == '' ){
        throw new Exception('주문상세 코드가 없습니다.', 0 );
    } else {
        $tmpIdx = str_replace( '|', ',', $idxs );
    }

    $sql ="
        SELECT op_step, store_code, idx
        FROM tblorderproduct
        WHERE ordercode = '".$ordercode."'
        AND idx IN (".$tmpIdx.")
    ";
    $res = pmysql_query( $sql, get_db_conn() );

    while( $row = pmysql_fetch_object( $res ) ){

        # 물류정보일 경우 ERP 정보를 가져와 UPDATE한다
        if( $row->op_step == '1' ){
            if( array_search( $row->store_code, $mStoreCode ) !== false ){
                $status = getErpWmsStatus( $ordercode, $row->idx ); // wms 상태값 가져오기
                if( $status == '2' ){
                    $deliOpts = array(
                        'ordercode'     => $ordercode,      // 주문코드
                        'op_idx'        => $row->idx,      // 상세 idx
                        'step'          => $status,     // 주문 step
                        'sync_status'   => 'M'      // 물류 또는 싱크커머스에서 넘겼는지 체크해주는 값 M 물류 S 싱크커머스
                    );
                    $rtn = deliveryStatusUp( $deliOpts );
                    if( $rtn == 1 ){
                        $bSucc = false;
                    }
                }
            }
        }elseif($row->op_step == '2') {
            // 이미 배송준비중으로 변경되었을 때.
            $bSucc = false;
        }
    }
    pmysql_free_result( $res );

    if( $bSucc === false ){
        throw new Exception('주문이 배송중비중 상태입니다.', 0 );
    }

} catch( Exception $e ) {
    $code = $e->getCode();
    $msg  = $e->getMessage();
}

echo json_encode( array( 'msg'=>$msg, 'code'=>$code ) );

