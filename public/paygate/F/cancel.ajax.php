<?php
//header("Content-Type: text/html; charset=UTF-8");
/*
신용카드/핸드폰 취소처리
부분취소 추가 (2016.02.16 - 김재수 추가)
*/
$Dir="../../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include("./inc/function.php");
include("./payco/payco_config.php");

Header("Pragma: no-cache");

$ordercode		= $_POST["ordercode"];		// 주문번호
$pc_type		= $_POST["pc_type"];		// 취소구분 (NULL, ALL : 전체취소 / PART : 부분취소)
$mod_mny		= $_POST["mod_mny"];		// 취소요청금액 (부분취소시)
$rem_mny		= $_POST["rem_mny"];		// 취소가능잔액 (부분취소시)
$ip				= $_SERVER['REMOTE_ADDR'];

function return_cancel_msg($msgType, $msg) {	
	$tmpMsgArray = array("type"=>$msgType, "msg"=>$msg);
	$msg = json_encode($tmpMsgArray);
	echo $msg;
	exit;
}

$sql = "SELECT * FROM tblpordercode WHERE ordercode='".$ordercode."' ";
$result=pmysql_query($sql,get_db_conn());
if($row=pmysql_fetch_object($result)) {
	$paymethod=$row->paymethod;
} else {
	$msgType	= "0";
	$msg	='해당 승인건이 존재하지 않습니다.';
	return_cancel_msg($msgType, $msg);
}
pmysql_free_result($result);

$tblname        = "tblppaycolog";
$tblpartname    = "tblppaycopartlog";

if ($pc_type == 'PART') { // 부분취소시
	if ($mod_mny =='' && $mod_mny == 0) { // 취소요청금액이 없을경우
		$msgType	= "0";
		$msg	='취소요청금액이 없습니다.';
		return_cancel_msg($msgType, $msg);
	} else {
		//부분취소가 있었을경우 이전 최종 취소가능금액을 구한다.
		$sql = "SELECT (rem_mny - mod_mny) as price FROM ".$tblpartname." WHERE ordercode='".$ordercode."' order by no desc limit 1 ";
		$result=pmysql_query($sql,get_db_conn());
		if($row=pmysql_fetch_object($result)) {
			$rem_mny=$row->price;
		}
		pmysql_free_result($result);
	}
}

//결제데이터 존재여부 확인
$sql = "SELECT * FROM ".$tblname." WHERE ordercode='".$ordercode."' ";
$result=pmysql_query($sql,get_db_conn());

$trans_code         = 0;
$_orderNo           = 0;
$_cancelTotalAmt    = 0;
$_orderCertifyKey   = 0;

if($row=pmysql_fetch_object($result)) {
	$trans_code         = $row->trans_code;
    $_orderCertifyKey   = $trans_code;              // PAYCO orderCertifyKey
	$_orderNo           = $row->pay_data;           // PAYCO orderNo
    $_cancelTotalAmt    = $row->price;              // 주문취소할 금액

	if ($pc_type == 'PART' && $rem_mny =='') $rem_mny=$row->price; // 이전 최종 취소가능금액이 없을 경우 결제금액으로 한다.
	if($row->ok=="C") {	//이미 취소처리된 건
		$msgType	= "1";
		$msg	='해당 결제건은 이미 취소처리되었습니다. 쇼핑몰에 재반영됩니다.';
		return_cancel_msg($msgType, $msg);		
	}
} else {
	$msgType	= "0";
	$msg	='해당 승인건이 존재하지 않습니다.';
	return_cancel_msg($msgType, $msg);		
}
pmysql_free_result($result);

	//---------------------------------------------------------------------------------
	// 가맹점 주문 번호로 상품 불러오기
	// DB에 연결해서 가맹점 주문 번호로 해당 상품 목록을 불러옵니다.
	//---------------------------------------------------------------------------------
	$resultValue = array();	//결과 리턴용 JSON 변수 선언

	$cancelType						= strtoupper($pc_type)?:"ALL";	                        // 취소 Type 받기 - ALL 또는 PART
	$orderNo						= $_orderNo;								        	// 주문번호
	$cancelTotalAmt					= $_cancelTotalAmt;		            					// 총 주문 금액
    
    $cancelAmt						= "";       
    if ( $cancelType == "PART" ) {
//        $sellerOrderProductReferenceKey = $_REQUEST["sellerOrderProductReferenceKey"];			// 가맹점 주문 상품 연동 키 ( PART 취소 시 ) => payco_config.php에 정의되어 있음
        $cancelAmt						= $mod_mny;								// 취소 상품 금액 ( PART 취소 시 )
    }

	//-----------------------------------------------------------------------------
	// (로그) 호출 시점과 호출값을 파일에 기록합니다.
	//-----------------------------------------------------------------------------
	 Write_Log("payco_cancel.php is Called - cancelType : $cancelType , orderNo : $orderNo, sellerOrderProductReferenceKey : $sellerOrderProductReferenceKey, cancelTotalAmt : $cancelTotalAmt, cancelAmt : $cancelAmt , deliveryfee_cancel : $deliveryfee_cancel");

	//---------------------------------------------------------------------------------------------------------------------
	// orderNo, cancelTotalAmt 값이 없으면 로그를 기록한 뒤 JSON 형태로 오류를 돌려주고 API를 종료합니다.
	//---------------------------------------------------------------------------------------------------------------------
	if($orderNo == ""){
		$resultValue["result"]	= "주문번호가 전달되지 않았습니다.";
		$resultValue["message"] = "orderNo is Nothing.";
		$resultValue["code"]	= 9999;		
//		echo json_encode($resultValue);
//		return;

        return_cancel_msg("0", $resultValue["result"]);
	}
	if($cancelTotalAmt == ""){
		$resultValue["result"]	= "총 주문금액이 전달되지 않았습니다.";
		$resultValue["message"] = "cancelTotalAmt is Nothing.";
		$resultValue["code"]	= 9999;		
//		echo json_encode($resultValue);
//		return;

        return_cancel_msg("0", $resultValue["result"]);
	}

	//----------------------------------------------------------------------------------
	// 상품정보 변수 선언 및 초기화
	//----------------------------------------------------------------------------------
	Global $cpId, $productId;

	//-----------------------------------------------------------------------------------
	// 취소 내역을 담을 JSON OBJECT를 선언합니다.
	//-----------------------------------------------------------------------------------
	$cancelOrder = array();

	//-----------------------------------------------------------------------------------
	// 전체 취소 = "ALL", 부분취소 = "PART"
	//------------------------------------------------------------------------------------
	if($cancelType == "ALL"){
		//---------------------------------------------------------------------------------
		// 파라메터로 값을 받을 경우 필요가 없는 부분이며
		// 주문 키값으로만 DB에서 데이터를 불러와야 한다면 이 부분에서 작업하세요.
		//---------------------------------------------------------------------------------

	}else if($cancelType == "PART"){ 
		//-----------------------------------------------------------------------------------------------------------------------
		// sellerOrderProductReferenceKey, cancelAmt 값이 없으면 로그를 기록한 뒤 JSON 형태로 오류를 돌려주고 API를 종료합니다.
		//-----------------------------------------------------------------------------------------------------------------------	
		if($sellerOrderProductReferenceKey == ""){
			$resultValue["result"]	= "취소주문연동키 값이 전달되지 않았습니다.";
			$resultValue["message"] = "sellerOrderProductReferenceKey is Nothing.";
			$resultValue["code"]	= 9999;		
//			echo json_encode($resultValue);
//			return;

            return_cancel_msg("0", $resultValue["result"]);
		}
		if($cancelAmt == ""){
			$resultValue["result"]	= "취소상품 금액이 전달되지 않았습니다.";
			$resultValue["message"] = "cancelAmt is Nothing.";
			$resultValue["code"]	= 9999;		
//			echo json_encode($resultValue);
//			return;

            return_cancel_msg("0", $resultValue["result"]);
		}

		//---------------------------------------------------------------------------------
		// 주문상품 데이터 불러오기
		// 파라메터로 값을 받을 경우 받은 값으로만 작업을 하면 됩니다.
		// 주문 키값으로만 DB에서 취소 상품 데이터를 불러와야 한다면 이 부분에서 작업하세요.
		//---------------------------------------------------------------------------------
		$orderProducts = array();

		//---------------------------------------------------------------------------------
		// 취소 상품값으로 읽은 변수들로 Json String 을 작성합니다.
		//---------------------------------------------------------------------------------		
		$orderProduct = array();
		$orderProduct["cpId"]							= $cpId;							// 상점 ID , payco_config.php 에 설정		
		$orderProduct["productId"]						= $productId;						// 상품 ID , payco_config.php 에 설정
		$orderProduct["productAmt"]						= $cancelAmt;						// 취소 상품 금액 ( 파라메터로 넘겨 받은 금액 - 필요서 DB에서 불러와 대입 )
		$orderProduct["sellerOrderProductReferenceKey"] = $sellerOrderProductReferenceKey;	// 취소 상품 연동 키 ( 파라메터로 넘겨 받은 값 - 필요서 DB에서 불러와 대입 )				
		array_push($orderProducts, $orderProduct);

	}else{
		//---------------------------------------------------------------------------------
		// 취소타입이 잘못되었음. ( ALL과 PART 가 아닐경우 )
		//---------------------------------------------------------------------------------			
		$resultValue["result"]	= "CANCEL_TYPE_ERROR";
		$resultValue["message"] = "취소 요청 타입이 잘못되었습니다.";
		$resultValue["code"]	= 9999;		
//		echo json_encode($resultValue);
//		return;

        return_cancel_msg("0", $resultValue["result"]);
	}

	//---------------------------------------------------------------------------------
	// 설정한 주문정보 변수들로 Json String 을 작성합니다.
	//---------------------------------------------------------------------------------

	$cancelOrder["sellerKey"]				= $sellerKey;							//가맹점 코드. payco_config.php 에 설정
	$cancelOrder["orderNo"]					= $orderNo;								// 주문번호
    if($cancelType == "PART"){ 
    	$cancelOrder["cancelTotalAmt"]			= $cancelAmt;       				//주문서의 총 금액을 입력합니다. (전체취소, 부분취소 전부다)
    } else {
    	$cancelOrder["cancelTotalAmt"]			= $cancelTotalAmt;						//주문서의 총 금액을 입력합니다. (전체취소, 부분취소 전부다)
    }
	$cancelOrder["orderProducts"]			= $orderProducts;						//위에서 작성한 상품목록과 배송비상품을 입력
	
	//---------------------------------------------------------------------------------
	// 주문 결제 취소 가능 여부 API 호출 ( JSON 데이터로 호출 )
	//---------------------------------------------------------------------------------
	$Result = payco_cancel_check(stripslashes(json_encode($cancelOrder)));

    $Result = json_decode($Result);

    if ( $Result->code == "0" && $Result->result->cancelPossibleYn == "Y" ) {
        // ================================================================================
        // 결제취소 가능
        // ================================================================================

        //---------------------------------------------------------------------------------
        // 가맹점 주문 번호로 상품 불러오기
        // DB에 연결해서 가맹점 주문 번호로 해당 상품 목록을 불러옵니다.
        //---------------------------------------------------------------------------------
        $resultValue = array();	//결과 리턴용 JSON 변수 선언

        $orderCertifyKey				= $_orderCertifyKey;							// 주문완료통보시 내려받은 인증값

        $cancelAmt						= "";
        if ( $cancelType == "PART" ) {
            $cancelAmt                  = $mod_mny;								// 취소 상품 금액 ( PART 취소 시 )
        }
        $requestMemo					= $_REQUEST["requestMemo"];								// 취소처리 요청메모

        $totalCancelTaxfreeAmt			= $_REQUEST["totalCancelTaxfreeAmt"];					// 총 취소할 면세금액
        $totalCancelTaxableAmt			= $_REQUEST["totalCancelTaxableAmt"];					// 총 취소할 과세금액
        $totalCancelVatAmt				= $_REQUEST["totalCancelVatAmt"];						// 총 취소할 부가세
        $totalCancelPossibleAmt			= $_REQUEST["totalCancelPossibleAmt"];					// 총 취소가능금액(현재기준): 취소가능금액 검증
        $cancelDetailContent			= $_REQUEST["cancelDetailContent"];						// 취소사유

        //-----------------------------------------------------------------------------
        // (로그) 호출 시점과 호출값을 파일에 기록합니다.
        //-----------------------------------------------------------------------------
         Write_Log("payco_cancel.php is Called - cancelType : $cancelType , sellerOrderProductReferenceKey : $sellerOrderProductReferenceKey, cancelTotalAmt : $cancelTotalAmt, cancelAmt : $cancelAmt ,  requestMemo : $requestMemo , orderNo : $orderNo, totalCancelTaxfreeAmt : $totalCancelTaxfreeAmt, totalCancelTaxableAmt : $totalCancelTaxableAmt, totalCancelVatAmt : $totalCancelVatAmt, totalCancelPossibleAmt : $totalCancelPossibleAmt  orderCertifyKey : $orderCertifyKey");

        //---------------------------------------------------------------------------------------------------------------------
        // orderNo, cancelTotalAmt 값이 없으면 로그를 기록한 뒤 JSON 형태로 오류를 돌려주고 API를 종료합니다.
        //---------------------------------------------------------------------------------------------------------------------
        if($orderNo == ""){
            $resultValue["result"]	= "주문번호가 전달되지 않았습니다.";
            $resultValue["message"] = "orderNo is Nothing.";
            $resultValue["code"]	= 9999;		
            echo json_encode($resultValue);
            return;
        }
        if($cancelTotalAmt == ""){
            $resultValue["result"]	= "총 주문금액이 전달되지 않았습니다.";
            $resultValue["message"] = "cancelTotalAmt is Nothing.";
            $resultValue["code"]	= 9999;		
            echo json_encode($resultValue);
            return;
        }

        //----------------------------------------------------------------------------------
        // 상품정보 변수 선언 및 초기화
        //----------------------------------------------------------------------------------
        Global $cpId, $productId;

        //-----------------------------------------------------------------------------------
        // 취소 내역을 담을 JSON OBJECT를 선언합니다.
        //-----------------------------------------------------------------------------------
        $cancelOrder = array();

        //-----------------------------------------------------------------------------------
        // 전체 취소 = "ALL", 부분취소 = "PART"
        //------------------------------------------------------------------------------------
        if($cancelType == "ALL"){
            //---------------------------------------------------------------------------------
            // 파라메터로 값을 받을 경우 필요가 없는 부분이며
            // 주문 키값으로만 DB에서 데이터를 불러와야 한다면 이 부분에서 작업하세요.
            //---------------------------------------------------------------------------------

        }else if($cancelType == "PART"){ 
            //-----------------------------------------------------------------------------------------------------------------------
            // sellerOrderProductReferenceKey, cancelAmt 값이 없으면 로그를 기록한 뒤 JSON 형태로 오류를 돌려주고 API를 종료합니다.
            //-----------------------------------------------------------------------------------------------------------------------	
            if($sellerOrderProductReferenceKey == ""){
                $resultValue["result"]	= "취소주문연동키 값이 전달되지 않았습니다.";
                $resultValue["message"] = "sellerOrderProductReferenceKey is Nothing.";
                $resultValue["code"]	= 9999;		
                echo json_encode($resultValue);
                return;
            }
            if($cancelAmt == ""){
                $resultValue["result"]	= "취소상품 금액이 전달되지 않았습니다.";
                $resultValue["message"] = "cancelAmt is Nothing.";
                $resultValue["code"]	= 9999;		
                echo json_encode($resultValue);
                return;
            }

            //---------------------------------------------------------------------------------
            // 주문상품 데이터 불러오기
            // 파라메터로 값을 받을 경우 받은 값으로만 작업을 하면 됩니다.
            // 주문 키값으로만 DB에서 취소 상품 데이터를 불러와야 한다면 이 부분에서 작업하세요.
            //---------------------------------------------------------------------------------
            $orderProducts = array();

            //---------------------------------------------------------------------------------
            // 취소 상품값으로 읽은 변수들로 Json String 을 작성합니다.
            //---------------------------------------------------------------------------------		
            $orderProduct = array();
            $orderProduct["cpId"]							= $cpId;							// 상점 ID , payco_config.php 에 설정		
            $orderProduct["productId"]						= $productId;						// 상품 ID , payco_config.php 에 설정
            $orderProduct["productAmt"]						= $cancelAmt;						// 취소 상품 금액 ( 파라메터로 넘겨 받은 금액 - 필요서 DB에서 불러와 대입 )
            $orderProduct["sellerOrderProductReferenceKey"] = $sellerOrderProductReferenceKey;	// 취소 상품 연동 키 ( 파라메터로 넘겨 받은 값 - 필요서 DB에서 불러와 대입 )
            $orderProduct["cancelDetailContent"]			= urlencode($cancelDetailContent);	// 취소 상세 사유			

            array_push($orderProducts, $orderProduct);
        
        }else{
            //---------------------------------------------------------------------------------
            // 취소타입이 잘못되었음. ( ALL과 PART 가 아닐경우 )
            //---------------------------------------------------------------------------------			
            $resultValue["result"]	= "CANCEL_TYPE_ERROR";
            $resultValue["message"] = "취소 요청 타입이 잘못되었습니다.";
            $resultValue["code"]	= 9999;		
            echo json_encode($resultValue);
            return;
        }

        //---------------------------------------------------------------------------------
        // 설정한 주문정보 변수들로 Json String 을 작성합니다.
        //---------------------------------------------------------------------------------

        $cancelOrder["sellerKey"]				= $sellerKey;							//가맹점 코드. payco_config.php 에 설정
        $cancelOrder["orderCertifyKey"]			= $orderCertifyKey;						//주문완료통보시 내려받은 인증값

        if($cancelType == "PART"){ 
            $cancelOrder["cancelTotalAmt"]			= $cancelAmt;						//주문서의 총 금액을 입력합니다. (전체취소, 부분취소 전부다)
        } else {
            $cancelOrder["cancelTotalAmt"]			= $cancelTotalAmt;				    //주문서의 총 금액을 입력합니다. (전체취소, 부분취소 전부다)
        }
        $cancelOrder["orderProducts"]			= $orderProducts;						//위에서 작성한 상품목록과 배송비상품을 입력
        $cancelOrder["orderNo"]					= $orderNo;								// 주문번호
        
        //---------------------------------------------------------------------------------
        // 주문 결제 취소 가능 여부 API 호출 ( JSON 데이터로 호출 )
        //---------------------------------------------------------------------------------
        $Result = payco_cancel(urldecode(stripslashes(json_encode($cancelOrder))));
        $Result = json_decode($Result);

        // 로그를 남긴다.-S--------------------------------------------------------------------------------------//
        $textDir = $_SERVER[DOCUMENT_ROOT].'/data/backup/payco/cancel_logs_'.date("Ym").'/';
        $outText = '========================='.date("Y-m-d H:i:s")."=============================\n";
        $outText.= " 결과 코드(Result) : ".$Result->code."\n";
        $outText.= " 결과 메시지(ErrMsg) : ".$Result->message."\n";
        $outText.= " pc_type     : ".$pc_type."\n";
        $outText.= " ordercode     : ".$ordercode."\n";
        $outText.= " mod_mny     : ".$mod_mny."\n";
        $outText.= " rem_mny     : ".$rem_mny."\n";
        $outText.= "\n";
        if(!is_dir($textDir)){
            mkdir($textDir, 0700);
            chmod($textDir, 0777);
        }
        $upQrt_f = fopen($textDir.'cancel_pg_'.date("Ymd").'.txt','a');
        fwrite($upQrt_f, $outText );
        fclose($upQrt_f);
        chmod($textDir."cancel_pg_".date("Ymd").".txt",0777);
        // 로그를 남긴다.-E--------------------------------------------------------------------------------------//

        $res_cd         = $Result->code;
        $res_msg        = $Result->message;

        if ( $Result->code == "0" ) {
            // 결제 취소가 된 경우

            if ($pc_type == 'PART') { // 부분취소
                //부분취소 내역로그를 추가 합니다.
                $sql = "INSERT INTO ".$tblpartname."(
                ordercode	,
                trans_code	,
                mod_mny	,
                rem_mny,
                res_cd,
                res_msg,
                ok,
                canceldate,
                ip) VALUES (
                '{$ordercode}',
                '{$trans_code}',
                '{$mod_mny}',
                '{$rem_mny}',
                '{$res_cd}',
                '{$res_msg}',
                'C',
                '".date("YmdHis")."',
                '{$ip}')";
            } else {// 전체취소
                //업데이트
                $sql = "UPDATE ".$tblname." SET ";
                $sql.= "ok			= 'C', ";
                $sql.= "canceldate	= '".date("YmdHis")."' ";
                $sql.= "WHERE ordercode='".$ordercode."' ";
            }

            pmysql_query($sql,get_db_conn());
            if (pmysql_errno()) {
                if(strlen(AdminMail)>0) {
                    @mail(AdminMail,"[PAYCO] ".$tblname." 취소 update 실패!","$sql - ".pmysql_error());
                }
                $msgType	= "0";
                $msg	    = '취소는 정상 처리되었으나 상점DB에 반영이 안되었습니다.\\n\\n관리자에게 문의하시기 바랍니다.';
                return_cancel_msg($msgType, $msg);		
            }

            $msgType	= "1";
            $msg	    = '승인취소가 정상적으로 처리되었습니다.\\n\\n관리페이지에서 취소여부를 꼭 확인하시기 바랍니다.';
            return_cancel_msg($msgType, $msg);	
        } else {
            $msgType	= "0";
            $msg	='취소처리가 아래와 같은 사유로 실패하였습니다.\\n\\n실패사유 : ' . $Result->message;
            return_cancel_msg($msgType, $msg);		
        }

    } else {
//        return_cancel_msg("0", $Result->code . " : " . $Result->message);
        return_cancel_msg("0", $Result->result->cancelImpossibleReason);
    }
