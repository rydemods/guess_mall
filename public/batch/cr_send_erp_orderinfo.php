<?php
#######################################################################################
# FileName          : cr_send_erp_orderinfo.php
# Desc              : 주문내역 ERP로 전송하기
# Last Updated      : 2016-09-06
# By                : JeongHo,Jeong
#######################################################################################

$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");

@set_time_limit(0);

#$conn = oci_connect("SMK_ONLINE", "SMK_ONLINE_0987", "1.209.88.42/ORA11", "KO16KSC5601");
$conn = oci_connect("SMK_ONLINE", "SMK_ONLINE_0987", "1.209.88.42/ORA11", "AL32UTF8");

echo "START = ".date("Y-m-d H:i:s")."<br>";

$ordercode = "2016120810485156273A";
//$ordercode = "2016090621504017240A";
//$oc_no = 526;
//$idxs = "4262|4263";

//$ordercode = "2016120710550413573A";
exdebug($ordercode);


// X1. 주문(결제)정보 전송 테스트.
// X2. 환불신청건 환불완료시 전송 테스트..2016-09-20
// X3. 반품 완료->환불완료 시 전송 테스트..2016-09-21
// X4. 교환 완료시 원본 취소 및 재주문 전송 테스트..2016-09-26
// 5. 왕복 배송비 결제 전송 테스트.
// 6. 배송정보 연동 테스트?? 2시간 간격 배치로 만들자..( recvdt값이 null인 부분을 가지고오면서 recvdt값 업데이트도 해주시면 될거같습니다.)
/*
3. 배송 중 처리
   - ERP 에서 IF_ONLINE_ORDER_RESULT 에 배송정보 생성
   - 쇼핑몰에서 주기적(2시간 간격)으로 배송준비중인 주문건들을 대상으로 IF_ONLINE_ORDER_RESULT 검색.
   - RECVDT 값이 null 이고, docid 와 itemno 가 일치하는 건들을 기준으로 검색해서 정보 가져와,
     쇼핑몰 주문의 배송상태 및 배송정보 갱신 처리.
*/

//sendErporder2($ordercode);                                     // 1.
//sendErporderCancel2($ordercode, $oc_no, $idxs);                // 2.
//sendErporderReturn2($ordercode, $oc_no, $idxs);                // 3.
sendErporderChange2($ordercode);

// 일반주문 전송
function sendErporder2($ordercode) {

    $conn = oci_connect("SMK_ONLINE", "SMK_ONLINE_0987", "1.209.88.42/ORA11", "AL32UTF8");

    sendErpOrderInfo2($ordercode, $conn);
    sendErpOrderinfoApp2($ordercode, $conn);

    oci_close($conn);
}

// 취소전송
function sendErporderCancel2($ordercode, $oc_no, $idxs) {

    //exit;
    $conn = oci_connect("SMK_ONLINE", "SMK_ONLINE_0987", "1.209.88.42/ORA11", "AL32UTF8");

    sendErpOrderInfoCancel2($ordercode, $oc_no, $idxs, $conn);
    sendErpOrderinfoAppCancel2($ordercode, $oc_no, $idxs, $conn);

    oci_close($conn);
}

// 반품 완료 -> 환불완료 시 전송
function sendErporderReturn2($ordercode, $oc_no, $idxs) {

    //exit;
    $conn = oci_connect("SMK_ONLINE", "SMK_ONLINE_0987", "1.209.88.42/ORA11", "AL32UTF8");

    sendErpOrderInfoReturn2($ordercode, $oc_no, $idxs, $conn);
    sendErpOrderinfoAppReturn2($ordercode, $oc_no, $idxs, $conn);

    oci_close($conn);
}

// 교환 완료 -> 교환완료 재주문 생성 시 전송
function sendErporderChange2($reordercode) {

    //exit;
    $conn = oci_connect("SMK_ONLINE", "SMK_ONLINE_0987", "1.209.88.42/ORA11", "AL32UTF8");

    sendErpOrderInfoChange2($reordercode, $conn);
    sendErpOrderinfoAppChange2($reordercode, $conn);

    oci_close($conn);
}


echo "END = ".date("Y-m-d H:i:s")."\r\n";
/*
1. companycd ??
2. shopcd ?? 
3. 보내는사람 주소..회원주문은 회원주소..그러나 비회원이면 주소정보 없음.
*/


// ERP 에 일반주문(결제)정보 전송
function sendErpOrderInfo2($ordercode, $conn) {

    //global $conn;

    $sql = "Select 	a.id, a.ordercode, b.idx, a.oi_step1, a.oi_step2, b.op_step, a.regdt, a.bank_date, 
                    a.sender_name, a.sender_tel2, sender_tel, a.paymethod, 
                    a.receiver_name, a.receiver_addr, a.receiver_tel1, a.receiver_tel2, a.order_msg2, 
                    a.oldordno, b.opt2_name, b.option_quantity, b.price, ((b.price+b.option_price)*b.option_quantity) as sum_price, 
                    b.deli_price, b.coupon_price, c.prodcode, c.colorcode, a.staff_order, b.delivery_type, b.reservation_date, b.store_code   
            From	tblorderinfo a 
            Join	tblorderproduct b on a.ordercode = b.ordercode 
            Join	tblproduct c on b.productcode = c.productcode 
            Join	tblsync_check d on d.idx = b.idx and d.order_type='I' and d.erp_product_yn='N' ";
    $sql .= "
            Where	a.ordercode = '".$ordercode."' 
            Order by b.idx asc 
            ";
    exdebug($sql);
    $result = pmysql_query($sql, get_db_conn());
    while($data = pmysql_fetch_array($result)){

        foreach($data as $k => $v)
        {
            $data[$k] = pmysql_escape_string($v);
        }

        $address = str_replace("\n"," ",trim($data[receiver_addr]));
		$address = str_replace("\r"," ",$address);
		$pos=strpos($address,"주소");
		if ($pos>0) {
			$post = trim(substr($address,0,$pos));
			$address = substr($address,$pos+9);
		}
		$zonecode = str_replace("우편번호 : ","",$post);
        //exdebug($zonecode);
        //exdebug($address);
        $r_address = "(".$zonecode.") ".$address;

        $if_div         = "I";
        $insertuser     = "hott_online";
        //$recvuser       = "SMK_ERP_SYSTEM";
        $companycd      = "10";
        $shopcd         = $data[store_code];
        $docid          = $ordercode;
        $itemno         = $data[idx];
        $doctypecd      = "NORM";
        //$takeorderdt    = substr($data[regdt], 0, 4)."-".substr($data[regdt], 4, 2)."-".substr($data[regdt], 6, 2);     // 전송일
        $takeorderdt    = date("Y-m-d");
        $send_nm        = $data[sender_name];
        $send_addr      = $r_address;
        $send_tel_no    = $data[sender_tel2];
        $send_mobile_no = $data[sender_tel];
        $remark         = getPaymethod($ordercode);
        if($data[paymethod][0] == "O") $remark .= " : ".$data[bank_date];
        $receive_nm     = $data[receiver_name];
        $receive_addr   = $r_address;
        $receive_tel_no = $data[receiver_tel1];
        $receive_mobile_no = $data[receiver_tel2];
        $message        = $data[order_msg2];
        $refdocid       = "";
        $refitemno      = "";
        $prodcd         = $data[prodcode];
        $colorcd        = $data[colorcode];
        $sizecd         = $data[opt2_name];
        $req_qty        = $data[option_quantity];
        $reqprc         = $data[price];     // 임직원구매일 경우는 할인된 금액이 들어감.
        $reqamt         = $data[sum_price];
        $deliveryamt    = $data[deli_price];
        //exdebug($deli_price);
        $reqsupamt      = round($reqamt / 1.1);
        $reqtaxamt      = round($reqsupamt * 0.1);
        $coupontemp     = getCouponInfo($docid, $itemno);
        if($coupontemp) {
            $couponinfo     = explode("^", $coupontemp);
            $couponid       = $couponinfo[0];
            $couponnm       = $couponinfo[1];
        } else {
            $couponid       = "";
            $couponnm       = "";
        }
        $couponamt      = $data[coupon_price];
        $deposityn      = "Y";
        if($data[staff_order] == "Y") {
            $eventgb        = "2";  //일반 3, 임직원2
            $customerno     = getErpStaffEmpNo($data[id]);
            $staffcardno    = getStaffCardNo($customerno);
        } else {
            $eventgb        = "3";  //일반 3, 임직원2
            $staffcardno    = "";
            $customerno     = "";
        }
        $docformcd = "1";
        if($data[delivery_type] == "0") $docformcd = "1";       // 매장발송
        elseif($data[delivery_type] == "1") $docformcd = "2";   // 매장픽업
        elseif($data[delivery_type] == "2") $docformcd = "3";   // 당일발송

        // 비회원은 null
        if(substr(trim($ordercode), -1) == "X") $memberid = "";
        else {
            //$memberid = $data[id];
            list($memberid) = pmysql_fetch("Select mem_seq From tblmember Where id = '".$data[id]."'");
        }

        $erp_sql = "insert into SMK_ERP.IF_HOTT_ONLINE_ORDER 
                    (
                        INSERTDT,
                        IF_DIV,
                        INSERTUSER,
                        COMPANYCD,
                        SHOPCD, 
                        DOCID,
                        ITEMNO,
                        DOCTYPECD,
                        TAKEORDERDT,
                        SEND_NM,
                        SEND_ADDR,
                        SEND_TEL_NO,
                        SEND_MOBILE_NO,
                        REMARK,
                        RECEIVE_NM,
                        RECEIVE_ADDR,
                        RECEIVE_TEL_NO,
                        RECEIVE_MOBILE_NO,
                        MESSAGE,
                        REFDOCID,
                        REFITEMNO,
                        PRODCD,
                        COLORCD,
                        SIZECD,
                        REQ_QTY,
                        REQPRC,
                        REQAMT,
                        REQSUPAMT,
                        REQTAXAMT,
                        COUPONID,
                        COUPONNM,
                        COUPONAMT,
                        DEPOSITYN,
                        EVENTGB,
                        CUSTOMERNO,
                        STAFFCARDNO, 
                        DELIVERYAMT, 
                        DOCFORMCD, 
                        MEMBERID 
                    )
                    values 
                    (
                        SYSDATE,
                        '".$if_div."',
                        '".$insertuser."',
                        '".$companycd."',
                        '".$shopcd."',
                        '".$docid."',
                         ".$itemno.",
                        '".$doctypecd."',
                        '".$takeorderdt."',
                        '".$send_nm."',
                        '".$send_addr."',
                        '".$send_tel_no."',
                        '".$send_mobile_no."',
                        '".$remark."',
                        '".$receive_nm."',
                        '".$receive_addr."',
                        '".$receive_tel_no."',
                        '".$receive_mobile_no."',
                        '".$message."',
                        '".$refdocid."',
                         ".(is_numeric($refitemno) ? $refitemno : 0).",
                        '".$prodcd."',
                        '".$colorcd."',
                        '".$sizecd."',
                         ".(is_numeric($req_qty) ? $req_qty : 0).",
                         ".(is_numeric($reqprc) ? $reqprc : 0).",
                         ".(is_numeric($reqamt) ? $reqamt : 0).",
                         ".(is_numeric($reqsupamt) ? $reqsupamt : 0).",
                         ".(is_numeric($reqtaxamt) ? $reqtaxamt : 0).",
                        '".$couponid."',
                        '".$couponnm."',
                         ".(is_numeric($couponamt) ? $couponamt : 0).",
                        '".$deposityn."',
                        '".$eventgb."',
                        '".$customerno."',
                        '".$staffcardno."', 
                        ".(is_numeric($deliveryamt) ? $deliveryamt : 0).", 
                        '".$docformcd."', 
                        '".$memberid."' 
                    )";
        exdebug($erp_sql);
        /*
        $smt_erp = oci_parse($conn,$erp_sql);
        $stid   = oci_execute($smt_erp);
        if(!$stid)
        {
            $error = oci_error();
            $bt = debug_backtrace();
            error_log("\r\n".date("Y-m-d H:i:s ").realpath($_SERVER['DOCUMENT_ROOT'].$_SERVER['SCRIPT_NAME']).$error.$bt[0]['line'],3,"/tmp/error_log_hott_erp");
            error_log($erp_sql."\r\n",3,"/tmp/error_log_hott_erp");
        }else{
            $usql = "update tblsync_check set erp_product_yn = 'Y' where ordercode = '".$ordercode."' and idx={$data[idx]} and order_type='I'";
            pmysql_query($usql);
        }
        */
    }
}

// ERP 에 일반 결제정보 전송
function sendErpOrderinfoApp2($ordercode, $conn) {

    //global $conn;

    $if_div         = "I";
    $insertuser     = "hott_online";
    $companycd      = "10";
    $docid          = $ordercode;
    $doctypecd      = "NORM";
    $takeorderdt    = date("Y-m-d");
    $terminalid = "";
    $vangamang = "";
    $bigo = "";

    //결제수단 정보 가져오자.
    //결제수단에 따른 참조 테이블 정의하자.
    $apparr = getPaymethodInfo($ordercode);
    //exdebug($apparr);

    $sql = "Select 	a.ordercode, b.idx, a.regdt, 
                    ((b.price+b.option_price)*b.option_quantity-b.coupon_price+b.deli_price) as sum_price 
            From	tblorderinfo a 
            Join	tblorderproduct b on a.ordercode = b.ordercode 
            Join	tblproduct c on b.productcode = c.productcode 
            Join	tblsync_check d on d.idx = b.idx and d.order_type='I' and d.erp_info_yn='N' ";
    $sql .= "
            Where	a.ordercode = '".$ordercode."' 
            Order by b.idx asc 
            ";
    exdebug($sql);
    $result = pmysql_query($sql, get_db_conn());
    $detailseq = 0;
    while($data = pmysql_fetch_array($result)){

        foreach($data as $k => $v)
        {
            $data[$k] = pmysql_escape_string($v);
        }

        $detailseq++;

        $erp_sql2 = "insert into SMK_ERP.IF_HOTT_ONLINE_ORDER_APP 
                    (
                        INSERTDT, 
                        IF_DIV, 
                        INSERTUSER, 
                        DOCID, 
                        DETAILSEQ, 
                        DOCTYPECD, 
                        TAKEORDERDT, 
                        DETAILGB, 
                        CARDNO, 
                        APPAMT, 
                        MONTHS, 
                        VALIDITYTERM, 
                        APPROVALNO, 
                        CARDCOMPANYCD, 
                        CARDCOMPANYNM, 
                        APPDT, 
                        APPTIME, 
                        TERMINALID, 
                        VANGAMANG, 
                        BIGO, 
                        ITEMNO 
                    )
                    values 
                    (
                        SYSDATE,
                        '" . $if_div . "',
                        '" . $insertuser . "',
                        '" . $docid . "',
                        '" . $detailseq . "',
                        '" . $doctypecd . "',
                        '" . $takeorderdt . "',
                        '" . $apparr[detailgb] . "',
                        '" . $apparr[cardno] . "',
                         " . (is_numeric($data[sum_price]) ? $data[sum_price] : 0) . ",
                        '" . $apparr[months] . "',
                        '" . $apparr[validityterm] . "',
                        '" . $apparr[approvalno] . "',
                        '" . $apparr[cardcompanycd] . "',
                        '" . $apparr[cardcompanynm] . "',
                        '" . $apparr[appdt] . "',
                        '" . $apparr[apptime] . "',
                        '" . $terminalid . "',
                        '" . $vangamang . "',
                        '" . $bigo . "', 
                        '" . $data[idx] . "' 
                    )";
        exdebug($erp_sql2);
        /*
        $smt_erp = oci_parse($conn, $erp_sql2);
        $stid = oci_execute($smt_erp);
        if (!$stid) {
            $error = oci_error();
            $bt = debug_backtrace();
            error_log("\r\n" . date("Y-m-d H:i:s ") . realpath($_SERVER['DOCUMENT_ROOT'] . $_SERVER['SCRIPT_NAME']) . $error . $bt[0]['line'], 3, "/tmp/error_log_hott_erp");
            error_log($erp_sql2 . "\r\n", 3, "/tmp/error_log_hott_erp");
        } else {
            $usql = "update tblsync_check set erp_info_yn = 'Y' where ordercode = '" . $ordercode . "' and idx={$data[idx]} and order_type='I'";
            pmysql_query($usql);
        }
        */
    }
}

// ERP 에 취소주문(환불)정보 전송
function sendErpOrderInfoCancel2($ordercode, $oc_no, $idxs, $conn) {

    //global $conn;

    $sql = "Select	b.id, b.ordercode, a.idx, b.oi_step1, b.oi_step2, a.op_step, d.rfindt as regdt, b.bank_date, 
                    b.sender_name, b.sender_tel2, b.sender_tel, b.paymethod, 
                    b.receiver_name, b.receiver_addr, b.receiver_tel1, b.receiver_tel2, b.order_msg2, 
                    b.oldordno, a.opt2_name, a.option_quantity, a.price, ((a.price+a.option_price)*a.option_quantity) as sum_price, 
                    a.deli_price, a.coupon_price, c.prodcode, c.colorcode, b.staff_order, a.delivery_type, a.reservation_date, a.store_code 
            From	tblorderproduct  a 
            Join	tblorderinfo b on a.ordercode = b.ordercode 
            Join	tblproduct c on a.productcode = c.productcode 
            join 	tblorder_cancel d on a.oc_no = d.oc_no 
            Where	a.ordercode = '".$ordercode."'  
            and 	d.oc_no = ".$oc_no."  
            and	    a.idx in ('".str_replace("|", "','", $idxs)."') 
            Order by a.idx asc 
            ";
    exdebug($sql);
    $result = pmysql_query($sql, get_db_conn());
    while($data = pmysql_fetch_array($result)){

        foreach($data as $k => $v)
        {
            $data[$k] = pmysql_escape_string($v);
        }

        $address = str_replace("\n"," ",trim($data[receiver_addr]));
		$address = str_replace("\r"," ",$address);
		$pos=strpos($address,"주소");
		if ($pos>0) {
			$post = trim(substr($address,0,$pos));
			$address = substr($address,$pos+9);
		}
		$zonecode = str_replace("우편번호 : ","",$post);
        //exdebug($zonecode);
        //exdebug($address);
        $r_address = "(".$zonecode.") ".$address;

        $if_div         = "I";
        $insertuser     = "hott_online";
        //$recvuser       = "SMK_ERP_SYSTEM";
        $companycd      = "10";
        $shopcd         = $data[store_code];
        $docid          = $ordercode;
        $itemno         = $data[idx];
        $doctypecd      = "CANCEL";
        //$takeorderdt    = substr($data[regdt], 0, 4)."-".substr($data[regdt], 4, 2)."-".substr($data[regdt], 6, 2);     // 전송일
        $takeorderdt    = date("Y-m-d");
        $send_nm        = $data[sender_name];
        $send_addr      = $r_address;
        $send_tel_no    = $data[sender_tel2];
        $send_mobile_no = $data[sender_tel];
        $remark         = getPaymethod($ordercode)." : 취소";
        $receive_nm     = $data[receiver_name];
        $receive_addr   = $r_address;
        $receive_tel_no = $data[receiver_tel1];
        $receive_mobile_no = $data[receiver_tel2];
        $message        = $data[order_msg2];
        $refdocid       = "";
        $refitemno      = "";
        $prodcd         = $data[prodcode];
        $colorcd        = $data[colorcode];
        $sizecd         = $data[opt2_name];
        $req_qty        = $data[option_quantity];
        $reqprc         = $data[price];     // 임직원구매일 경우는 할인된 금액이 들어감.
        $reqamt         = $data[sum_price];
        $deliveryamt    = $data[deli_price];
        //exdebug($deli_price);
        $reqsupamt      = round($reqamt / 1.1);
        $reqtaxamt      = round($reqsupamt * 0.1);
        $coupontemp     = getCouponInfo($docid, $itemno);
        if($coupontemp) {
            $couponinfo     = explode("^", $coupontemp);
            $couponid       = $couponinfo[0];
            $couponnm       = $couponinfo[1];
        } else {
            $couponid       = "";
            $couponnm       = "";
        }
        $couponamt      = $data[coupon_price];
        $deposityn      = "Y";
        if($data[staff_order] == "Y") {
            $eventgb        = "2";  //일반 3, 임직원2
            $customerno     = getErpStaffEmpNo($data[id]);
            $staffcardno    = getStaffCardNo($customerno);
        } else {
            $eventgb        = "3";  //일반 3, 임직원2
            $staffcardno    = "";
            $customerno     = "";
        }
        $docformcd = "1";
        if($data[delivery_type] == "0") $docformcd = "1";       // 매장발송
        elseif($data[delivery_type] == "1") $docformcd = "2";   // 매장픽업
        elseif($data[delivery_type] == "2") $docformcd = "3";   // 당일발송

        // 비회원은 null
        if(substr(trim($ordercode), -1) == "X") $memberid = "";
        else {
            //$memberid = $data[id];
            list($memberid) = pmysql_fetch("Select mem_seq From tblmember Where id = '".$data[id]."'");
        }

        $erp_sql = "insert into SMK_ERP.IF_HOTT_ONLINE_ORDER 
                    (
                        INSERTDT,
                        IF_DIV,
                        INSERTUSER,
                        COMPANYCD,
                        SHOPCD, 
                        DOCID,
                        ITEMNO,
                        DOCTYPECD,
                        TAKEORDERDT,
                        SEND_NM,
                        SEND_ADDR,
                        SEND_TEL_NO,
                        SEND_MOBILE_NO,
                        REMARK,
                        RECEIVE_NM,
                        RECEIVE_ADDR,
                        RECEIVE_TEL_NO,
                        RECEIVE_MOBILE_NO,
                        MESSAGE,
                        REFDOCID,
                        REFITEMNO,
                        PRODCD,
                        COLORCD,
                        SIZECD,
                        REQ_QTY,
                        REQPRC,
                        REQAMT,
                        REQSUPAMT,
                        REQTAXAMT,
                        COUPONID,
                        COUPONNM,
                        COUPONAMT,
                        DEPOSITYN,
                        EVENTGB,
                        CUSTOMERNO,
                        STAFFCARDNO, 
                        DELIVERYAMT, 
                        DOCFORMCD, 
                        MEMBERID 
                    )
                    values 
                    (
                        SYSDATE,
                        '".$if_div."',
                        '".$insertuser."',
                        '".$companycd."',
                        '".$shopcd."',
                        '".$docid."',
                         ".$itemno.",
                        '".$doctypecd."',
                        '".$takeorderdt."',
                        '".$send_nm."',
                        '".$send_addr."',
                        '".$send_tel_no."',
                        '".$send_mobile_no."',
                        '".$remark."',
                        '".$receive_nm."',
                        '".$receive_addr."',
                        '".$receive_tel_no."',
                        '".$receive_mobile_no."',
                        '".$message."',
                        '".$refdocid."',
                         ".(is_numeric($refitemno) ? $refitemno : 0).",
                        '".$prodcd."',
                        '".$colorcd."',
                        '".$sizecd."',
                         ".(is_numeric($req_qty) ? $req_qty : 0).",
                         ".(is_numeric($reqprc) ? $reqprc : 0).",
                         ".(is_numeric($reqamt) ? $reqamt : 0).",
                         ".(is_numeric($reqsupamt) ? $reqsupamt : 0).",
                         ".(is_numeric($reqtaxamt) ? $reqtaxamt : 0).",
                        '".$couponid."',
                        '".$couponnm."',
                         ".(is_numeric($couponamt) ? $couponamt : 0).",
                        '".$deposityn."',
                        '".$eventgb."',
                        '".$customerno."',
                        '".$staffcardno."', 
                        ".(is_numeric($deliveryamt) ? $deliveryamt : 0).", 
                        '".$docformcd."', 
                        '".$memberid."' 
                    )";
        exdebug($erp_sql);
        /*
        $smt_erp = oci_parse($conn,$erp_sql);
        $stid   = oci_execute($smt_erp);
        if(!$stid) 
        { 
            $error = oci_error(); 
            $bt = debug_backtrace();
            error_log("\r\n".date("Y-m-d H:i:s ").realpath($_SERVER['DOCUMENT_ROOT'].$_SERVER['SCRIPT_NAME']).$error.$bt[0]['line'],3,"/tmp/error_log_hott_erp");
            error_log($erp_sql."\r\n",3,"/tmp/error_log_hott_erp");
        }
        */
    }
}

// ERP 에 취소(환불) 결제정보 전송
function sendErpOrderinfoAppCancel2($ordercode, $oc_no, $idxs, $conn) {

    //global $conn;

    $if_div         = "I";
    $insertuser     = "hott_online";
    $companycd      = "10";
    $docid          = $ordercode;
    $doctypecd      = "CANCEL";
    $takeorderdt    = date("Y-m-d");
    $terminalid = "";
    $vangamang = "";
    $bigo = "";

    //결제수단 정보 가져오자.
    //결제수단에 따른 참조 테이블 정의하자.
    $apparr = getPaymethodInfo($ordercode);
    //exdebug($apparr);

    $sql = "Select	b.ordercode, a.idx, d.rfindt as regdt, 
                    ((a.price+a.option_price)*a.option_quantity-a.coupon_price+a.deli_price) as sum_price 
            From	tblorderproduct  a 
            Join	tblorderinfo b on a.ordercode = b.ordercode 
            Join	tblproduct c on a.productcode = c.productcode 
            join 	tblorder_cancel d on a.oc_no = d.oc_no 
            Where	a.ordercode = '".$ordercode."'  
            and 	d.oc_no = ".$oc_no."  
            and	    a.idx in ('".str_replace("|", "','", $idxs)."') 
            Order by a.idx asc 
            ";
    //exdebug($sql);
    $result = pmysql_query($sql, get_db_conn());
    $detailseq = 0;
    while($data = pmysql_fetch_array($result)){

        foreach($data as $k => $v)
        {
            $data[$k] = pmysql_escape_string($v);
        }
        
        // 취소/교환/환불시 결제상세 순번 가져오기 (2016.11.01 - 김재수 추가)
        $detailseq	= getErpOrderDetailSeq($docid, $doctypecd);

        $erp_sql2 = "insert into SMK_ERP.IF_HOTT_ONLINE_ORDER_APP 
                    (
                        INSERTDT, 
                        IF_DIV, 
                        INSERTUSER, 
                        DOCID, 
                        DETAILSEQ, 
                        DOCTYPECD, 
                        TAKEORDERDT, 
                        DETAILGB, 
                        CARDNO, 
                        APPAMT, 
                        MONTHS, 
                        VALIDITYTERM, 
                        APPROVALNO, 
                        CARDCOMPANYCD, 
                        CARDCOMPANYNM, 
                        APPDT, 
                        APPTIME, 
                        TERMINALID, 
                        VANGAMANG, 
                        BIGO, 
                        ITEMNO 
                    )
                    values 
                    (
                        SYSDATE,
                        '".$if_div."',
                        '".$insertuser."',
                        '".$docid."',
                        '".$detailseq."',
                        '".$doctypecd."',
                        '".$takeorderdt."',
                        '".$apparr[detailgb]."',
                        '".$apparr[cardno]."',
                         ".(is_numeric($data[sum_price]) ? $data[sum_price] : 0).", 
                        '".$apparr[months]."',
                        '".$apparr[validityterm]."',
                        '".$apparr[approvalno]."',
                        '".$apparr[cardcompanycd]."',
                        '".$apparr[cardcompanynm]."',
                        '".$apparr[appdt]."',
                        '".$apparr[apptime]."',
                        '".$terminalid."',
                        '".$vangamang."',
                        '".$bigo."', 
                        '".$data[idx]."' 
                    )";
        exdebug($erp_sql2);
        /*
        $smt_erp = oci_parse($conn,$erp_sql2);
        $stid   = oci_execute($smt_erp);
        if(!$stid) 
        { 
            $error = oci_error(); 
            $bt = debug_backtrace();
            error_log("\r\n".date("Y-m-d H:i:s ").realpath($_SERVER['DOCUMENT_ROOT'].$_SERVER['SCRIPT_NAME']).$error.$bt[0]['line'],3,"/tmp/error_log_hott_erp");
            error_log($erp_sql2."\r\n",3,"/tmp/error_log_hott_erp");
        }
        */
    }
}

// ERP 에 반품주문(환불)정보 전송
function sendErpOrderInfoReturn2($ordercode, $oc_no, $idxs, $conn) {

    //global $conn;

    $sql = "Select	b.id, b.ordercode, a.idx, b.oi_step1, b.oi_step2, a.op_step, d.rfindt as regdt, b.bank_date, 
                    b.sender_name, b.sender_tel2, b.sender_tel, b.paymethod, 
                    b.receiver_name, b.receiver_addr, b.receiver_tel1, b.receiver_tel2, b.order_msg2, 
                    b.oldordno, a.opt2_name, a.option_quantity, a.price, ((a.price+a.option_price)*a.option_quantity) as sum_price, 
                    a.deli_price, a.coupon_price, c.prodcode, c.colorcode, b.staff_order, a.delivery_type, a.reservation_date, a.store_code 
            From	tblorderproduct  a 
            Join	tblorderinfo b on a.ordercode = b.ordercode 
            Join	tblproduct c on a.productcode = c.productcode 
            join 	tblorder_cancel d on a.oc_no = d.oc_no 
            Where	a.ordercode = '".$ordercode."'  
            and 	d.oc_no = ".$oc_no."  
            and	    a.idx in ('".str_replace("|", "','", $idxs)."') 
            Order by a.idx asc 
            ";
    //exdebug($sql);
    $result = pmysql_query($sql, get_db_conn());
    while($data = pmysql_fetch_array($result)){

        foreach($data as $k => $v)
        {
            $data[$k] = pmysql_escape_string($v);
        }

        $address = str_replace("\n"," ",trim($data[receiver_addr]));
		$address = str_replace("\r"," ",$address);
		$pos=strpos($address,"주소");
		if ($pos>0) {
			$post = trim(substr($address,0,$pos));
			$address = substr($address,$pos+9);
		}
		$zonecode = str_replace("우편번호 : ","",$post);
        //exdebug($zonecode);
        //exdebug($address);
        $r_address = "(".$zonecode.") ".$address;

        $if_div         = "I";
        $insertuser     = "hott_online";
        //$recvuser       = "SMK_ERP_SYSTEM";
        $companycd      = "10";
        $shopcd         = $data[store_code];
        $docid          = $ordercode;
        $itemno         = $data[idx];
        $doctypecd      = "RETURN";
        //$takeorderdt    = substr($data[regdt], 0, 4)."-".substr($data[regdt], 4, 2)."-".substr($data[regdt], 6, 2);     // 전송일
        $takeorderdt    = date("Y-m-d");
        $send_nm        = $data[sender_name];
        $send_addr      = $r_address;
        $send_tel_no    = $data[sender_tel2];
        $send_mobile_no = $data[sender_tel];
        $remark         = getPaymethod($ordercode)." : 취소";
        $receive_nm     = $data[receiver_name];
        $receive_addr   = $r_address;
        $receive_tel_no = $data[receiver_tel1];
        $receive_mobile_no = $data[receiver_tel2];
        $message        = $data[order_msg2];
        $refdocid       = $ordercode;
        $refitemno      = $data[idx];
        $prodcd         = $data[prodcode];
        $colorcd        = $data[colorcode];
        $sizecd         = $data[opt2_name];
        $req_qty        = $data[option_quantity];
        $reqprc         = $data[price];     // 임직원구매일 경우는 할인된 금액이 들어감.
        $reqamt         = $data[sum_price];
        $deliveryamt    = $data[deli_price];
        //exdebug($deli_price);
        $reqsupamt      = round($reqamt / 1.1);
        $reqtaxamt      = round($reqsupamt * 0.1);
        $coupontemp     = getCouponInfo($docid, $itemno);
        if($coupontemp) {
            $couponinfo     = explode("^", $coupontemp);
            $couponid       = $couponinfo[0];
            $couponnm       = $couponinfo[1];
        } else {
            $couponid       = "";
            $couponnm       = "";
        }
        $couponamt      = $data[coupon_price];
        $deposityn      = "Y";
        if($data[staff_order] == "Y") {
            $eventgb        = "2";  //일반 3, 임직원2
            $customerno     = getErpStaffEmpNo($data[id]);
            $staffcardno    = getStaffCardNo($customerno);
        } else {
            $eventgb        = "3";  //일반 3, 임직원2
            $staffcardno    = "";
            $customerno     = "";
        }
        $docformcd = "1";
        if($data[delivery_type] == "0") $docformcd = "1";       // 매장발송
        elseif($data[delivery_type] == "1") $docformcd = "2";   // 매장픽업
        elseif($data[delivery_type] == "2") $docformcd = "3";   // 당일발송

        // 비회원은 null
        if(substr(trim($ordercode), -1) == "X") $memberid = "";
        else {
            //$memberid = $data[id];
            list($memberid) = pmysql_fetch("Select mem_seq From tblmember Where id = '".$data[id]."'");
        }

        $erp_sql = "insert into SMK_ERP.IF_HOTT_ONLINE_ORDER 
                    (
                        INSERTDT,
                        IF_DIV,
                        INSERTUSER,
                        COMPANYCD,
                        SHOPCD, 
                        DOCID,
                        ITEMNO,
                        DOCTYPECD,
                        TAKEORDERDT,
                        SEND_NM,
                        SEND_ADDR,
                        SEND_TEL_NO,
                        SEND_MOBILE_NO,
                        REMARK,
                        RECEIVE_NM,
                        RECEIVE_ADDR,
                        RECEIVE_TEL_NO,
                        RECEIVE_MOBILE_NO,
                        MESSAGE,
                        REFDOCID,
                        REFITEMNO,
                        PRODCD,
                        COLORCD,
                        SIZECD,
                        REQ_QTY,
                        REQPRC,
                        REQAMT,
                        REQSUPAMT,
                        REQTAXAMT,
                        COUPONID,
                        COUPONNM,
                        COUPONAMT,
                        DEPOSITYN,
                        EVENTGB,
                        CUSTOMERNO,
                        STAFFCARDNO, 
                        DELIVERYAMT, 
                        DOCFORMCD, 
                        MEMBERID 
                    )
                    values 
                    (
                        SYSDATE,
                        '".$if_div."',
                        '".$insertuser."',
                        '".$companycd."',
                        '".$shopcd."',
                        '".$docid."',
                         ".$itemno.",
                        '".$doctypecd."',
                        '".$takeorderdt."',
                        '".$send_nm."',
                        '".$send_addr."',
                        '".$send_tel_no."',
                        '".$send_mobile_no."',
                        '".$remark."',
                        '".$receive_nm."',
                        '".$receive_addr."',
                        '".$receive_tel_no."',
                        '".$receive_mobile_no."',
                        '".$message."',
                        '".$refdocid."',
                         ".(is_numeric($refitemno) ? $refitemno : 0).",
                        '".$prodcd."',
                        '".$colorcd."',
                        '".$sizecd."',
                         ".(is_numeric($req_qty) ? $req_qty : 0).",
                         ".(is_numeric($reqprc) ? $reqprc : 0).",
                         ".(is_numeric($reqamt) ? $reqamt : 0).",
                         ".(is_numeric($reqsupamt) ? $reqsupamt : 0).",
                         ".(is_numeric($reqtaxamt) ? $reqtaxamt : 0).",
                        '".$couponid."',
                        '".$couponnm."',
                         ".(is_numeric($couponamt) ? $couponamt : 0).",
                        '".$deposityn."',
                        '".$eventgb."',
                        '".$customerno."',
                        '".$staffcardno."', 
                        ".(is_numeric($deliveryamt) ? $deliveryamt : 0).", 
                        '".$docformcd."', 
                        '".$memberid."' 
                    )";
        exdebug($erp_sql);
        /*
        $smt_erp = oci_parse($conn,$erp_sql);
        $stid   = oci_execute($smt_erp);
        if(!$stid) 
        { 
            $error = oci_error(); 
            $bt = debug_backtrace();
            error_log("\r\n".date("Y-m-d H:i:s ").realpath($_SERVER['DOCUMENT_ROOT'].$_SERVER['SCRIPT_NAME']).$error.$bt[0]['line'],3,"/tmp/error_log_hott_erp");
            error_log($erp_sql."\r\n",3,"/tmp/error_log_hott_erp");
        }
        */
    }
}

// ERP 에 반품(환불) 결제정보 전송
function sendErpOrderinfoAppReturn2($ordercode, $oc_no, $idxs, $conn) {

    //global $conn;

    $table = "";

    $if_div         = "I";
    $insertuser     = "hott_online";
    $companycd      = "10";
    $docid          = $ordercode;
    $doctypecd      = "RETURN";
    $takeorderdt    = date("Y-m-d");
    $terminalid = "";
    $vangamang = "";
    $bigo = "";

    //결제수단 정보 가져오자.
    //결제수단에 따른 참조 테이블 정의하자.
    $apparr = getPaymethodInfo($ordercode);
    //exdebug($apparr);
			
    $sql = "Select	b.ordercode, a.idx, d.rfindt as regdt, 
                    ((a.price+a.option_price)*a.option_quantity-a.coupon_price+a.deli_price) as sum_price 
        From	tblorderproduct  a 
        Join	tblorderinfo b on a.ordercode = b.ordercode 
        Join	tblproduct c on a.productcode = c.productcode 
        join 	tblorder_cancel d on a.oc_no = d.oc_no 
        Where	a.ordercode = '".$ordercode."'  
        and 	d.oc_no = ".$oc_no."  
        and	    a.idx in ('".str_replace("|", "','", $idxs)."') 
        Order by a.idx asc 
        ";
    //exdebug($sql);
    $result = pmysql_query($sql, get_db_conn());
    while($data = pmysql_fetch_array($result)){

        foreach($data as $k => $v)
        {
            $data[$k] = pmysql_escape_string($v);
        }

		// 취소/교환/환불시 결제상세 순번 가져오기 (2016.11.01 - 김재수 추가)
		$detailseq	= getErpOrderDetailSeq($docid, $doctypecd);

        $erp_sql2 = "insert into SMK_ERP.IF_HOTT_ONLINE_ORDER_APP 
                    (
                        INSERTDT, 
                        IF_DIV, 
                        INSERTUSER, 
                        DOCID, 
                        DETAILSEQ, 
                        DOCTYPECD, 
                        TAKEORDERDT, 
                        DETAILGB, 
                        CARDNO, 
                        APPAMT, 
                        MONTHS, 
                        VALIDITYTERM, 
                        APPROVALNO, 
                        CARDCOMPANYCD, 
                        CARDCOMPANYNM, 
                        APPDT, 
                        APPTIME, 
                        TERMINALID, 
                        VANGAMANG, 
                        BIGO, 
                        ITEMNO 
                    )
                    values 
                    (
                        SYSDATE,
                        '".$if_div."',
                        '".$insertuser."',
                        '".$docid."',
                        '".$detailseq."',
                        '".$doctypecd."',
                        '".$takeorderdt."',
                        '".$apparr[detailgb]."',
                        '".$apparr[cardno]."',
                         ".(is_numeric($data[sum_price]) ? $data[sum_price] : 0).", 
                        '".$apparr[months]."',
                        '".$apparr[validityterm]."',
                        '".$apparr[approvalno]."',
                        '".$apparr[cardcompanycd]."',
                        '".$apparr[cardcompanynm]."',
                        '".$apparr[appdt]."',
                        '".$apparr[apptime]."',
                        '".$terminalid."',
                        '".$vangamang."',
                        '".$bigo."', 
                        '".$data[idx]."' 
                    )";
        exdebug($erp_sql2);
        /*
        $smt_erp = oci_parse($conn,$erp_sql2);
        $stid   = oci_execute($smt_erp);
        if(!$stid) 
        { 
            $error = oci_error(); 
            $bt = debug_backtrace();
            error_log("\r\n".date("Y-m-d H:i:s ").realpath($_SERVER['DOCUMENT_ROOT'].$_SERVER['SCRIPT_NAME']).$error.$bt[0]['line'],3,"/tmp/error_log_hott_erp");
            error_log($erp_sql2."\r\n",3,"/tmp/error_log_hott_erp");
        }
        */
    }
}

// ERP 에 재주문정보 전송
function sendErpOrderInfoChange2($reordercode, $conn) {

    //global $conn;

    $sql = "Select 	a.id, a.ordercode, b.idx, a.oi_step1, a.oi_step2, b.op_step, a.regdt, a.bank_date, 
                    a.sender_name, a.sender_tel2, sender_tel, a.paymethod, 
                    a.receiver_name, a.receiver_addr, a.receiver_tel1, a.receiver_tel2, a.order_msg2, 
                    a.oldordno, b.opt2_name, b.option_quantity, b.price, ((b.price+b.option_price)*b.option_quantity) as sum_price, 
                    b.deli_price, b.coupon_price, c.prodcode, c.colorcode, a.staff_order, b.delivery_type, b.reservation_date, b.store_code    
            From	tblorderinfo a 
            Join	tblorderproduct b on a.ordercode = b.ordercode 
            Join	tblproduct c on b.productcode = c.productcode 
            Join	tblsync_check d on d.idx = b.idx and d.order_type='I' and d.erp_product_yn='N' ";
    $sql .= "
            Where	a.ordercode = '".$reordercode."' 
            Order by b.idx asc 
            ";
    exdebug($sql);
    $result = pmysql_query($sql, get_db_conn());
    while($data = pmysql_fetch_array($result)){

        foreach($data as $k => $v)
        {
            $data[$k] = pmysql_escape_string($v);
        }

        $address = str_replace("\n"," ",trim($data[receiver_addr]));
		$address = str_replace("\r"," ",$address);
		$pos=strpos($address,"주소");
		if ($pos>0) {
			$post = trim(substr($address,0,$pos));
			$address = substr($address,$pos+9);
		}
		$zonecode = str_replace("우편번호 : ","",$post);
        //exdebug($zonecode);
        //exdebug($address);
        $r_address = "(".$zonecode.") ".$address;

        list($old_opidx) = pmysql_fetch("select idx from tblorderproduct where ordercode = '".$data[oldordno]."' and opt2_change = '".$data[opt2_name]."' and redelivery_type = 'G'");

        $if_div         = "I";
        $insertuser     = "hott_online";
        //$recvuser       = "SMK_ERP_SYSTEM";
        $companycd      = "10";
        $shopcd         = $data[store_code];
        $docid          = $reordercode;
        $itemno         = $data[idx];
        $doctypecd      = "CHANGE";
        //$takeorderdt    = substr($data[regdt], 0, 4)."-".substr($data[regdt], 4, 2)."-".substr($data[regdt], 6, 2);     // 전송일
        $takeorderdt    = date("Y-m-d");
        $send_nm        = $data[sender_name];
        $send_addr      = $r_address;
        $send_tel_no    = $data[sender_tel2];
        $send_mobile_no = $data[sender_tel];
        $remark         = getPaymethod($data[oldordno])." : 교환";
        $receive_nm     = $data[receiver_name];
        $receive_addr   = $r_address;
        $receive_tel_no = $data[receiver_tel1];
        $receive_mobile_no = $data[receiver_tel2];
        $message        = $data[order_msg2];
        $refdocid       = $data[oldordno];
        $refitemno      = $old_opidx;
        $prodcd         = $data[prodcode];
        $colorcd        = $data[colorcode];
        $sizecd         = $data[opt2_name];
        $req_qty        = $data[option_quantity];
        $reqprc         = $data[price];     // 임직원구매일 경우는 할인된 금액이 들어감.
        $reqamt         = $data[sum_price];
        $deliveryamt    = $data[deli_price];
        //exdebug($deli_price);
        $reqsupamt      = round($reqamt / 1.1);
        $reqtaxamt      = round($reqsupamt * 0.1);
        //$coupontemp     = getCouponInfo($docid, $itemno);
        $coupontemp     = getCouponInfo($refdocid, $refitemno);
        if($coupontemp) {
            $couponinfo     = explode("^", $coupontemp);
            $couponid       = $couponinfo[0];
            $couponnm       = $couponinfo[1];
        } else {
            $couponid       = "";
            $couponnm       = "";
        }
        $couponamt      = $data[coupon_price];
        $deposityn      = "Y";
        if($data[staff_order] == "Y") {
            $eventgb        = "2";  //일반 3, 임직원2
            $customerno     = getErpStaffEmpNo($data[id]);
            $staffcardno    = getStaffCardNo($customerno);
        } else {
            $eventgb        = "3";  //일반 3, 임직원2
            $staffcardno    = "";
            $customerno     = "";
        }
        $docformcd = "1";
        if($data[delivery_type] == "0") $docformcd = "1";       // 매장발송
        elseif($data[delivery_type] == "1") $docformcd = "2";   // 매장픽업
        elseif($data[delivery_type] == "2") $docformcd = "3";   // 당일발송

        // 비회원은 null
        if(substr(trim($reordercode), -1) == "X") $memberid = "";
        else {
            //$memberid = $data[id];
            list($memberid) = pmysql_fetch("Select mem_seq From tblmember Where id = '".$data[id]."'");
        }

        $erp_sql = "insert into SMK_ERP.IF_HOTT_ONLINE_ORDER 
                    (
                        INSERTDT,
                        IF_DIV,
                        INSERTUSER,
                        COMPANYCD,
                        SHOPCD, 
                        DOCID,
                        ITEMNO,
                        DOCTYPECD,
                        TAKEORDERDT,
                        SEND_NM,
                        SEND_ADDR,
                        SEND_TEL_NO,
                        SEND_MOBILE_NO,
                        REMARK,
                        RECEIVE_NM,
                        RECEIVE_ADDR,
                        RECEIVE_TEL_NO,
                        RECEIVE_MOBILE_NO,
                        MESSAGE,
                        REFDOCID,
                        REFITEMNO,
                        PRODCD,
                        COLORCD,
                        SIZECD,
                        REQ_QTY,
                        REQPRC,
                        REQAMT,
                        REQSUPAMT,
                        REQTAXAMT,
                        COUPONID,
                        COUPONNM,
                        COUPONAMT,
                        DEPOSITYN,
                        EVENTGB,
                        CUSTOMERNO,
                        STAFFCARDNO, 
                        DELIVERYAMT, 
                        DOCFORMCD, 
                        MEMBERID 
                    )
                    values 
                    (
                        SYSDATE,
                        '".$if_div."',
                        '".$insertuser."',
                        '".$companycd."',
                        '".$shopcd."',
                        '".$docid."',
                         ".$itemno.",
                        '".$doctypecd."',
                        '".$takeorderdt."',
                        '".$send_nm."',
                        '".$send_addr."',
                        '".$send_tel_no."',
                        '".$send_mobile_no."',
                        '".$remark."',
                        '".$receive_nm."',
                        '".$receive_addr."',
                        '".$receive_tel_no."',
                        '".$receive_mobile_no."',
                        '".$message."',
                        '".$refdocid."',
                         ".(is_numeric($refitemno) ? $refitemno : 0).",
                        '".$prodcd."',
                        '".$colorcd."',
                        '".$sizecd."',
                         ".(is_numeric($req_qty) ? $req_qty : 0).",
                         ".(is_numeric($reqprc) ? $reqprc : 0).",
                         ".(is_numeric($reqamt) ? $reqamt : 0).",
                         ".(is_numeric($reqsupamt) ? $reqsupamt : 0).",
                         ".(is_numeric($reqtaxamt) ? $reqtaxamt : 0).",
                        '".$couponid."',
                        '".$couponnm."',
                         ".(is_numeric($couponamt) ? $couponamt : 0).",
                        '".$deposityn."',
                        '".$eventgb."',
                        '".$customerno."',
                        '".$staffcardno."', 
                        ".(is_numeric($deliveryamt) ? $deliveryamt : 0).", 
                        '".$docformcd."', 
                        '".$memberid."' 
                    )";
        exdebug($erp_sql);
        /*
        $smt_erp = oci_parse($conn,$erp_sql);
        $stid   = oci_execute($smt_erp);
        if(!$stid) 
        { 
            $error = oci_error(); 
            $bt = debug_backtrace();
            error_log("\r\n".date("Y-m-d H:i:s ").realpath($_SERVER['DOCUMENT_ROOT'].$_SERVER['SCRIPT_NAME']).$error.$bt[0]['line'],3,"/tmp/error_log_hott_erp");
            error_log($erp_sql."\r\n",3,"/tmp/error_log_hott_erp");
        }else{
            $usql = "update tblsync_check set erp_product_yn = 'Y' where ordercode = '".$reordercode."' and idx={$data[idx]} and order_type='I'";
            pmysql_query($usql);
        }
        */
    }
}

// ERP 에 재주문 결제정보 전송
function sendErpOrderinfoAppChange2($reordercode, $conn) {

    //global $conn;

    $table = "";

    $if_div         = "I";
    $insertuser     = "hott_online";
    $companycd      = "10";
    $docid          = $reordercode;
    $doctypecd      = "CHANGE";
    $takeorderdt    = date("Y-m-d");
    $terminalid     = "";
    $vangamang      = "";
    $bigo           = "";

    list($ordercode) = pmysql_fetch("Select oldordno From tblorderinfo Where ordercode = '".$reordercode."'");
    //list($reamt) = pmysql_fetch("Select (price+option_price)*option_quantity+deli_price-coupon_price-use_point From tblorderproduct Where ordercode = '".$reordercode."'");
    //exdebug($reamt);

    //결제수단 정보 가져오자.
    //결제수단에 따른 참조 테이블 정의하자.
    $apparr = getPaymethodInfo($ordercode);
    //exdebug($apparr);

    $sql = "Select 	a.ordercode, b.idx, 
                    a.oldordno, ((b.price+b.option_price)*b.option_quantity-b.coupon_price+b.deli_price) as sum_price 
            From	tblorderinfo a 
            Join	tblorderproduct b on a.ordercode = b.ordercode 
            Join	tblproduct c on b.productcode = c.productcode 
            Join	tblsync_check d on d.idx = b.idx and d.order_type='I' and d.erp_info_yn='N' ";
    $sql .= "
            Where	a.ordercode = '".$reordercode."' 
            Order by b.idx asc 
            ";
    exdebug($sql);
    $result = pmysql_query($sql, get_db_conn());
    while($data = pmysql_fetch_array($result)){

        foreach($data as $k => $v)
        {
            $data[$k] = pmysql_escape_string($v);
        }
			
		// 취소/교환/환불시 결제상세 순번 가져오기 (2016.11.01 - 김재수 추가)
		$detailseq	= getErpOrderDetailSeq($docid, $doctypecd);

        $erp_sql2 = "insert into SMK_ERP.IF_HOTT_ONLINE_ORDER_APP 
                    (
                        INSERTDT, 
                        IF_DIV, 
                        INSERTUSER, 
                        DOCID, 
                        DETAILSEQ, 
                        DOCTYPECD, 
                        TAKEORDERDT, 
                        DETAILGB, 
                        CARDNO, 
                        APPAMT, 
                        MONTHS, 
                        VALIDITYTERM, 
                        APPROVALNO, 
                        CARDCOMPANYCD, 
                        CARDCOMPANYNM, 
                        APPDT, 
                        APPTIME, 
                        TERMINALID, 
                        VANGAMANG, 
                        BIGO, 
                        ITEMNO 
                    )
                    values 
                    (
                        SYSDATE,
                        '".$if_div."',
                        '".$insertuser."',
                        '".$docid."',
                        '".$detailseq."',
                        '".$doctypecd."',
                        '".$takeorderdt."',
                        '".$apparr[detailgb]."',
                        '".$apparr[cardno]."',
                         " . (is_numeric($data[sum_price]) ? $data[sum_price] : 0) . ",
                        '".$apparr[months]."',
                        '".$apparr[validityterm]."',
                        '".$apparr[approvalno]."',
                        '".$apparr[cardcompanycd]."',
                        '".$apparr[cardcompanynm]."',
                        '".$apparr[appdt]."',
                        '".$apparr[apptime]."',
                        '".$terminalid."',
                        '".$vangamang."',
                        '".$bigo."', 
                        '".$data[idx]."' 
                    )";
        exdebug($erp_sql2);
        /*
        $smt_erp = oci_parse($conn,$erp_sql2);
        $stid   = oci_execute($smt_erp);
        if(!$stid) 
        { 
            $error = oci_error(); 
            $bt = debug_backtrace();
            error_log("\r\n".date("Y-m-d H:i:s ").realpath($_SERVER['DOCUMENT_ROOT'].$_SERVER['SCRIPT_NAME']).$error.$bt[0]['line'],3,"/tmp/error_log_hott_erp");
            error_log($erp_sql2."\r\n",3,"/tmp/error_log_hott_erp");
        } else {
            $usql = "update tblsync_check set erp_info_yn = 'Y' where ordercode = '" . $reordercode . "' and idx={$data[idx]} and order_type='I'";
            pmysql_query($usql);
        }
        */
    }
}

function getMemberNo2($id) {

    list($mem_seq) = pmysql_fetch("Select mem_seq From tblmember Where id = '".$id."'");

    return $mem_seq;
}

function getStaffCardNo2($id) {

    list($staffcardno) = pmysql_fetch("Select staffcardno From tblmember Where id = '".$id."'");

    return $staffcardno;
}

// 임직원 EmpNo 구하기(실시간)
function getErpStaffEmpNo2($staffno) {

    $conn = oci_connect("SMK_ONLINE", "SMK_ONLINE_0987", "1.209.88.42/ORA11", "AL32UTF8");

    $sql = "SELECT * FROM SMK_ERP.IF_ONLINE_STAFFCARDLIMIT_V a WHERE a.STAFFCARDNO = '".$staffno."'";
    $smt_stock = oci_parse($conn, $sql);
    oci_execute($smt_stock);
    //exdebug($sql);

    $data = oci_fetch_assoc($smt_stock);
	$empno = $data['EMPNO'];

    oci_free_statement($smt_stock);
    oci_close($conn);

    return $empno;
}

function getPaymethod2($ordercode) {

    list($paymethod) = pmysql_fetch("Select paymethod From tblorderinfo Where ordercode = '".$ordercode."'");

    $arrPay = array("V"=>"abank","O"=>"vbank","Q"=>"vbank(escrow)","C"=>"card","M"=>"mobile","Y"=>"payco");

    return $arrPay[$paymethod[0]];
}

// IF_HOTT_ONLINE_ORDER_APP.DETAILGB
function getErpDetailgb2($val) {

    $arrDetail = array(
					'abank' => '11',  //계좌이체
					'vbank' => '12',  //가상계좌
					'card'  => '20',  //신용카드
					'mobile' => '21'   //모바일
                    );

    return $arrDetail[$val];
}

function getCouponInfo2($ordercode, $opidx) {

    $sql = "Select  a.coupon_code, b.coupon_name From tblcoupon_order a Join tblcouponinfo b on a.coupon_code = b.coupon_code Where a.ordercode = '".$ordercode."' And op_idx = ".$opidx." ";
    list($coupon_code, $coupon_name) = pmysql_fetch($sql);

    return $coupon_code."^".$coupon_name;
}

// ERP order_app 용 정보 구하기.
function getPaymethodInfo2($ordercode) {

    $app_arr = array();

    $paymethod = getPaymethod2($ordercode);
    if($paymethod == "abank") $table = "tblptranslog";
    elseif($paymethod == "vbank") $table = "tblpvirtuallog";
    elseif($paymethod == "card") $table = "tblpcardlog";
    elseif($paymethod == "mobile") $table = "tblpmobilelog";
    elseif($paymethod == "payco") $table = "tblppaycolog";

    $sql = "Select * From ".$table." Where ordercode = '".$ordercode."'";
    //exdebug($sql);
    $ret = pmysql_query($sql);
    while($row = pmysql_fetch_array($ret)){

        $app_arr[detailgb]      = getErpDetailgb2($paymethod);
        $app_arr[appamt]        = $row[price];
        if($paymethod == "abank") {
            $app_arr[months]        = 0;
            $app_arr[validityterm]  = "";
            $app_arr[approvalno]    = $row[trans_code];
            $app_arr[cardno]        = "";
            $app_arr[cardcompanycd] = $row[bank_code];
            $app_arr[cardcompanynm] = $row[bank_name];
        } elseif($paymethod == "vbank") {
            $app_arr[months]        = "";
            $app_arr[validityterm]  = "";
            $app_arr[approvalno]    = "";
            $app_arr[cardno]        = $row[account];
            $app_arr[cardcompanycd] = $row[bank_code];
            $app_arr[cardcompanynm] = trim(str_replace($row[account], "", $row[pay_data]));
        } elseif($paymethod == "card") {
            $app_arr[months]        = "";
            $app_arr[validityterm]  = "";
            $tmp = explode(":", $row[pay_data]);
            $app_arr[approvalno]    = trim($tmp[1]);
            $app_arr[cardno]        = "";
            $app_arr[cardcompanycd] = $row[cardcode];
            $app_arr[cardcompanynm] = $row[cardname];
        } elseif($paymethod == "mobile") {
            $app_arr[months]        = 0;
            $app_arr[validityterm]  = "";
            $app_arr[approvalno]    = $row[pay_data];
            $app_arr[cardno]        = "";
            $app_arr[cardcompanycd] = "";   // 현재는 SKT : 011, KT : 016, LGT : 019
            $app_arr[cardcompanynm] = $row[cardname];
        } elseif($paymethod == "payco") {
            $app_arr[months]        = "";
            $app_arr[validityterm]  = "";
            $app_arr[approvalno]    = $row[pay_data];
            $app_arr[cardno]        = "";
            $app_arr[cardcompanycd] = $row[cardcode];
            $app_arr[cardcompanynm] = $row[cardname];
        }
        $app_arr[appdt]         = substr($row[okdate], 0, 4)."-".substr($row[okdate], 4, 2)."-".substr($row[okdate], 6, 2);
        $app_arr[apptime]       = substr($row[okdate], -6);
    }

    return $app_arr;
}

/*
$prodcd = 'M20325';
$colorcd = 'RUNWHI/RUNWHI/NEWNAV';
$sizecd = '260';
$shopcd = '008810';
$basketinfo = getErpPriceNStock2($prodcd, $colorcd, $sizecd, $shopcd);
exdebug($basketinfo);
function getErpPriceNStock2($prodcd, $colorcd, $sizecd='', $shopcd='') {

    $conn = oci_connect("SMK_ONLINE", "SMK_ONLINE_0987", "1.209.88.42/ORA11", "AL32UTF8");

    if($shopcd) $subsql = "AND  a.SHOPCD = '".$shopcd."' ";
    else $subsql = "";

    $sql = "SELECT MAX(SIZECD) AS SIZECD, SUM(SUMQTY) AS SUMQTY, SUM(polprice) AS  POLPRICE, SUM(tagprice) AS TAGPRICE
            FROM 
            ( 
                SELECT  a.SIZECD, SUM(a.AVAILQTY) AS SUMQTY, 0 AS polprice, 0 AS tagprice  
                FROM    SMK_ERP.HOTT_ON_STOCK_V a 
                WHERE   a.PRODCD = '".$prodcd."' AND a.COLORCD = '".$colorcd."' AND a.SIZECD = '".$sizecd."' 
                ".$subsql." 
                GROUP BY a.SIZECD 
                UNION ALL          
                SELECT '' AS sizecd, 0 AS sumqty, polprice, 0 AS tagprice    
                FROM (
                      SELECT    ROW_NUMBER() over(PARTITION BY prodcd, colorcd ORDER BY insertdt desc) rn, 
                                POLPRICE 
                      FROM	    SMK_ERP.IF_HOTT_ONLINE_PROD_PRICE 
                      WHERE     1=1 
                      AND       RECVDT is NULL 
                      AND		PRODCD = '".$prodcd."' 
                      AND		COLORCD = '".$colorcd."'  
                ) a
                WHERE 1=1
                AND rn = 1            
                UNION ALL            
                SELECT '' AS sizecd, 0 AS sumqty, 0 AS polprice, tagprice   
                FROM (
                      SELECT    ROW_NUMBER() over(PARTITION BY prodcd, colorcd ORDER BY insertdt desc) rn, 
                                TAGPRICE 
                      FROM	    SMK_ERP.IF_HOTT_ONLINE_PRODINFO 
                      WHERE     1=1 
                      AND       RECVDT is NULL 
                      AND	    USEYN = 'Y' 
                      AND       IF_DIV <> 'D' 
                      AND		PRODCD = '".$prodcd."' 
                      AND		COLORCD = '".$colorcd."'  
                ) a
                WHERE 1=1
                AND rn = 1
            ) z  
            ";
    $smt_stock = oci_parse($conn, $sql);
    oci_execute($smt_stock);
    //exdebug($sql);

    $size_stock = array();
    while($data = oci_fetch_array($smt_stock, OCI_BOTH+OCI_RETURN_NULLS+OCI_RETURN_LOBS)) {

        $size_stock[size]   = $data[SIZECD];
        $size_stock[sumqty] = $data[SUMQTY];
        $size_stock[polprice] = $data[POLPRICE];
        $size_stock[tagprice] = $data[TAGPRICE];
    }
    oci_free_statement($smt_stock);
    oci_close($conn);

    return $size_stock;
}
*/


//sendErpDeliveryInfo2('2016101221414979013A', '4609', '07', '222222222', '008810');
// ERP 배송정보 전송
function sendErpDeliveryInfo2($ordercode, $idx, $deli_cd, $deli_num, $shopcd='') {

    $conn = oci_connect("SMK_ONLINE", "SMK_ONLINE_0987", "1.209.88.42/ORA11", "AL32UTF8");

    list($oldordno) = pmysql_fetch("Select oldordno From tblorderinfo Where ordercode = '".$ordercode."'");

    if($shopcd) $shopcd = $shopcd;
    else {
        list($shopcd) = pmysql_fetch("Select store_code From tblorderproduct Where ordercode = '".$ordercode."' and idx = ".$idx."");
    }
    
    list($shopnm) = pmysql_fetch("Select name From tblstore Where store_code = '".$shopcd."'");

    $insertuser     = "hott_online";
    $companycd      = "10";
    $docid          = $ordercode;
    $itemno         = $idx;
    if($oldordno) $doctypecd = "CHONORD";   // 교환주문
    else $doctypecd = "NORM";               // 일반주문.
    //exdebug("/".$deli_cd."/");
    $parcelcode     = str_pad(trim($deli_cd), 2, "0", STR_PAD_LEFT);
    //exdebug("/".$parcelcode."/");
    list($parcelcodenm) = pmysql_fetch("Select company_name From tbldelicompany where code = '".$parcelcode."'");
    $waybillno      = $deli_num;

    $erp_sql = "insert into SMK_ERP.IF_HOTT_ON_ORDER_RESULT 
                (
                    INSERTDT, 
                    IF_DIV, 
                    INSERTUSER, 
                    COMPANYCD, 
                    DOCID, 
                    ITEMNO, 
                    DOCTYPECD, 
                    SHOPCD, 
                    SHOPNM, 
                    PARCELCODE, 
                    PARCELCODENM, 
                    WAYBILLNO
                )
                values 
                (
                    SYSDATE,
                    'I',
                    '".$insertuser."',
                    '".$companycd."',
                    '".$docid."',
                     ".$itemno.",
                    '".$doctypecd."',
                    '".$shopcd."',
                    '".$shopnm."',
                    '".$parcelcode."',
                    '".$parcelcodenm."',
                    '".$waybillno."' 
                ) 
                ";
    exdebug($erp_sql);
    /*
    $smt_erp = oci_parse($conn,$erp_sql);
    $stid   = oci_execute($smt_erp);
    if(!$stid)
    {
        $error = oci_error();
        $bt = debug_backtrace();
        error_log("\r\n".date("Y-m-d H:i:s ").realpath($_SERVER['DOCUMENT_ROOT'].$_SERVER['SCRIPT_NAME']).$error.$bt[0]['line'],3,"/tmp/error_log_hott_erp");
        error_log($query."\r\n",3,"/tmp/error_log_hott_erp");
    }
    */
    oci_close($conn);
}

?>
