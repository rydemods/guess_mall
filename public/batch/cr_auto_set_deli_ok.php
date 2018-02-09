#!/usr/local/php/bin/php
<?php
//exit;
#######################################################################################
# FileName          : cr_auto_set_deli_ok.php
# Desc              : 매일 자정에 돌면서 14일이 지나면 자동으로 '구매확정'을 시킨다.
# Last Updated      : 2016.03.10
# By                : moondding2
#######################################################################################

$Dir="../";
include ($Dir."lib/init.php");
include ($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");
include_once($Dir."conf/config.purchase_date.php");

// 오늘 기준으로 14일 전 날짜 구하기
//$stdDate = date("Ymd", strtotime("-2 week"));
list($order_day)=pmysql_fetch("select order_day from tblshopinfo ");
//사용안할경우 튕겨낸다.
if($order_day=="N") exit;

$dt = (int)$order_day;
$stdDate = date("Ymd", strtotime("-$dt days"));

$exe_id		= "||batch";	// 실행자 아이디|이름|타입

// 현재 배송중인 상품들
// 배송일 기준으로 변경..2016-08-12 jhjeong
//cj 미집하건 제외처리 2017-06-07
$sql = "Select	ordercode, idx, deli_date, op_step, order_conf_date
        From	tblorderproduct 
        Where 	1=1 
        /*And	    deli_date like '{$stdDate}%' */
		And	    deli_date <= '{$stdDate}235959'
        And	    (order_conf_date is null  OR order_conf_date = '') 
        And	    op_step = 3 
		order by idx asc
        ";  
		//and		idx not in ('6127','6194','6197','6205','6251','6256','6262','6465','6462','6506','6515','6548','6627','6629','6743','6744','6792','6830','6850','6853','6855','6877','6941','6954','7029')


$result = pmysql_query($sql);

while ( $row = pmysql_fetch_object($result) ) {
    $ordercode          = $row->ordercode;
    $productorder_idx   = $row->idx;

    list($m_id) = pmysql_fetch("select id from  tblorderinfo where ordercode = '".$ordercode."' ");

    list($deli_reserve)=pmysql_fetch_array(pmysql_query("select reserve from tblorderproduct WHERE ordercode='".trim($ordercode)."' AND idx IN ('".str_replace("|", "','", $productorder_idx)."')"));

    $sql = "UPDATE tblorderproduct SET receive_ok = '1' ,deli_gbn='F', order_conf = '1', order_conf_date = '" . date('YmdHis') . "' ";
    $sql.= "WHERE ordercode='{$ordercode}' AND idx='{$productorder_idx}' ";
    $sql.= "AND op_step < 40 ";

    pmysql_query($sql,get_db_conn());
    if( !pmysql_error() ){
        // 신규상태 변경 추가 - (2016.02.18 - 김재수 추가)
        orderProductStepUpdate($exe_id, $ordercode, $productorder_idx, '4'); // 배송완료

        //적립 예정 적립금을 지급한다. 통합포인트는 erp에서 관리2017-04-28
        //if ($deli_reserve != 0) insert_point($m_id, $deli_reserve, "주문 ".$ordercode." 배송완료(".count($productorder_idx)."건)에 의한 포인트 지급", '','',"admin-".uniqid(''), $return_point_term);

        //주문중 배송완료, 취소완료상태가 아닌경우
        list($op_idx_cnt)=pmysql_fetch_array(pmysql_query("select count(idx) as op_idx_cnt from tblorderproduct WHERE ordercode='".trim($ordercode)."' AND idx NOT IN ('".str_replace("|", "','", $productorder_idx)."') AND (op_step != '4' AND op_step != '44')"));

        if ($op_idx_cnt == 0) {
            $sql = "UPDATE tblorderinfo SET receive_ok = '1', deli_gbn = 'F', order_conf = '1', order_conf_date = '" . date('YmdHis') . "' ";
            $sql.= "WHERE ordercode='{$ordercode}' ";
            pmysql_query($sql,get_db_conn());
        }

		//배송완료시 erp로 전송
		sendErpOrderEndInfo($ordercode, $productorder_idx);

        $msg    = "구매확정 되었습니다.";
        $msgType = "1";
    } else {
        $msg = "구매확정 실패. 관리자에게 문의해주세요.";
        $msgType = "0";
    }

    echo "주문번호 : " . $ordercode . ", 주문idx : " . $productorder_idx . " ===> " . $msg . "\n";
}

?>
