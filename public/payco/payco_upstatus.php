<?PHP
	//-----------------------------------------------------------------------------
	// PAYCO 주문 상품 상태 변경 페이지 샘플 ( PHP )
	// payco_upstatus.php
	// 2015-03-25	PAYCO기술지원 <dl_payco_ts@nhnent.com>
	//-----------------------------------------------------------------------------

	//-----------------------------------------------------------------------------
	// 이 문서는 json 형태의 데이터를 반환합니다.
	//-----------------------------------------------------------------------------
	header('Content-Type: text/html; charset=utf-8'); 
	include("payco_config.php");

	try {
		//--------------------------------------------------------------------------
		// 주문 상태를 변경하기 위한 값을 설정합니다.
		//--------------------------------------------------------------------------
		$sellerOrderProductReferenceKey = $_REQUEST["sellerOrderProductReferenceKey"]; 				// 가맹점 상품 연동 키
		$orderProductStatus				= $_REQUEST["orderProductStatus"]; 							// 변경할 상태 값
		$orderNo						= $_REQUEST["orderNo"]; 									// 주문번호

		//---------------------------------------------------------------------------
		// (로그) 호출 시점과 호출값을 파일에 기록합니다.
		//---------------------------------------------------------------------------
		Write_Log("payco_upstatus.php is Called -  :  sellerOrderProductReferenceKey : $sellerOrderProductReferenceKey, orderProductStatus : $orderProductStatus , orderNo:$orderNo");

		//----------------------------------------------------------------------------
		// 설정한 주문정보 변수들로 Json String 을 작성합니다.
		//----------------------------------------------------------------------------
		$modifyValue = array();
		$modifyValue["sellerKey"]						= $sellerKey;
		$modifyValue["sellerOrderProductReferenceKey"]	= $sellerOrderProductReferenceKey;
		$modifyValue["orderProductStatus"]				= $orderProductStatus;
		$modifyValue["orderNo"]							= $orderNo;

		$modifyValueJSON = json_encode($modifyValue);
		
		Write_Log("payco_upstatus.php is Called - modifyValueJSON>>  :$modifyValue ");
		Write_Log("payco_upstatus.php is Called - modifyValueJSON>>  :$modifyValueJSON ");
		
		
		
		//----------------------------------------------------------------------------
		// 주문 상태변경 함수 호출 ( JSON 데이터를 String 형태로 전달 
		//----------------------------------------------------------------------------
		$Result = payco_upstatus(json_encode($modifyValue));
					
		
		
		
	} catch ( Exception $e ) {
		//-----------------------------------------------------------------------------
		// 작업 결과를 담을 JSON OBJECT를 선언합니다.
		//-----------------------------------------------------------------------------
		$resultValue = array();
		$resultValue["code"]		= $e->getCode();
		$resultValue["message"]		= $e->getMessage();
		Write_Log("payco_upstatus.php Logical Error : Code - ".$e->getCode().", Description - ".$e->getMessage());
		$Result = json_encode($resultValue);
	}

	//---------------------------------------------------------------------------------
	// 결과 그대로를 호출쪽에 반환
	//---------------------------------------------------------------------------------
	echo $Result;
?>