<?PHP
	//--------------------------------------------------------------------------------
	// PAYCO 주문 취소 가능 체크 샘플 ( PHP )
	// payco_cancel_check.php
	// 2015-03-25	PAYCO기술지원 <dl_payco_ts@nhnent.com>
	//--------------------------------------------------------------------------------

	//--------------------------------------------------------------------------------
	// 이 문서는 json 형태의 데이터를 반환합니다.
	//--------------------------------------------------------------------------------
 	header('Content-Type: text/html; charset=utf-8'); 
	include("payco_config.php");

	//---------------------------------------------------------------------------------
	// 가맹점 주문 번호로 상품 불러오기
	// DB에 연결해서 가맹점 주문 번호로 해당 상품 목록을 불러옵니다.
	//---------------------------------------------------------------------------------
	$resultValue = array();	//결과 리턴용 JSON 변수 선언

	$cancelType						= strtoupper($_REQUEST["cancelType"]);					// 취소 Type 받기 - ALL 또는 PART
	$orderNo						= $_REQUEST["orderNo"];									// 주문번호
	$sellerOrderProductReferenceKey = $_REQUEST["sellerOrderProductReferenceKey"];			// 가맹점 주문 상품 연동 키 ( PART 취소 시 )
	$cancelTotalAmt					= $_REQUEST["cancelTotalAmt"];							// 총 주문 금액
	$cancelAmt						= $_REQUEST["cancelAmt"];								// 취소 상품 금액 ( PART 취소 시 )

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
	$cancelOrder["orderNo"]					= $orderNo;								// 주문번호
	$cancelOrder["cancelTotalAmt"]			= $cancelTotalAmt;						//주문서의 총 금액을 입력합니다. (전체취소, 부분취소 전부다)
	$cancelOrder["orderProducts"]			= $orderProducts;						//위에서 작성한 상품목록과 배송비상품을 입력
	
	//---------------------------------------------------------------------------------
	// 주문 결제 취소 가능 여부 API 호출 ( JSON 데이터로 호출 )
	//---------------------------------------------------------------------------------
	$Result = payco_cancel_check(stripslashes(json_encode($cancelOrder)));

	echo $Result;


?>