<?PHP
	//-----------------------------------------------------------------------------
	// PAYCO 키값 체크 API 호출 샘플 ( PHP )
	// payco_key_check.php
	// 2015-03-25	PAYCO기술지원 <dl_payco_ts@nhnent.com>
	//-----------------------------------------------------------------------------

	//-----------------------------------------------------------------------------
	// 이 문서는 json 형태의 데이터를 반환합니다.
	//-----------------------------------------------------------------------------
	header('Content-Type: text/html; charset=utf-8'); 
	include("payco_config.php");

	$resultValue = array();

	//-----------------------------------------------------------------------------
	// 호출 시점과 호출값을 파일에 기록합니다.
	//-----------------------------------------------------------------------------
	Write_Log("payco_key_check.php is Called." );

	try {
		//-------------------------------------------------------------------------
		// 해당 상품 정보가 사용이 가능한 코드인지 PAYCO에 조회한다.
		// CP_ID, PRODUCT_ID 조회 ( JSON 형태로 구성 )
		//-------------------------------------------------------------------------
		$keyValue = array();
		$keyValue["sellerKey"] = $sellerKey;

		$codeItems = array();
		$codes = array();
		$codes["codeKind"]	= "CP_ID";
		$codes["codeValue"] = $cpId;
		array_push($codeItems, $codes);

		$codes = array();
		$codes["codeKind"]			= "PRODUCT_ID";
		$codes["codeValue"]			= $productId;
		$codes["upperCodeValue"]	= $cpId;
		array_push($codeItems, $codes);

		$keyValue["codes"] = $codeItems;

		//---------------------------------------------------------------------------
		// PAYCO 키값 체크 API 호출
		//---------------------------------------------------------------------------
		$Result = payco_keycheck(json_encode($keyValue));

	} catch ( exception $e) {
		//---------------------------------------------------------------------------
		// 작업 결과를 담을 JSON OBJECT를 선언합니다.
		//---------------------------------------------------------------------------
		$resultValue = array();
		$resultValue["result"]	= "ITEM_CHECK_ERROR";
		$resultValue["message"] = $e->getMessage();
		$resultValue["code"]	= $e->getCode();
		echo json_encode($resultValue);
		return;
	}

	//---------------------------------------------------------------------------------
	// 받는 쪽에서 결과를 분석하기 때문에 반환값을 그대로 전달
	//---------------------------------------------------------------------------------
	echo $Result
?>