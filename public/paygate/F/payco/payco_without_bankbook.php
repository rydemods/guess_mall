<?
	//-------------------------------------------------------------------------------
	// PAYCO 무통장입금 처리 통보 API 페이지 샘플 ( PHP )
	// payco_without_bankbook.php
	// 2015-03-25	PAYCO기술지원 <dl_payco_ts@nhnent.com>
	//-------------------------------------------------------------------------------

	//-------------------------------------------------------------------------------
	// 이 문서는 text/html 형태의 데이터를 반환합니다. ( OK 또는 ERROR 만 반환 )
	//-------------------------------------------------------------------------------
	header('Content-type: text/html; charset: UTF-8');
	include("payco_config.php");

	try {

		//-----------------------------------------------------------------------------
		// 오류가 발생했는지 기억할 변수와 결과를 담을 변수를 선언합니다.
		//-----------------------------------------------------------------------------
		$ErrBoolean = True;		// 미리 오류라고 가정
		$readValue	= stripslashes($_REQUEST["response"]);

		//-----------------------------------------------------------------------------
		// (로그) 호출 시점과 호출값을 파일에 기록합니다.
		//-----------------------------------------------------------------------------
		Write_Log("payco_without_bankbook.php is Called - response : $readValue");

		//-----------------------------------------------------------------------------
		// POST 값 중 response 값이 없으면 에러를 표시하고 API를 종료합니다.
		//-----------------------------------------------------------------------------
		if( $readValue == "" ){
			$resultValue = "Parameter is nothing.";
			Write_Log("payco_without_bankbook.php send Result : ERROR ($resultValue)");
			echo "ERROR";
			return;
		}

		//-----------------------------------------------------------------------------
		// Payco 에서 송신하는 값(response)을 JSON 형태로 변경
		// 데이터 확인에 필요한 값을 변수에 담아 처리합니다.
		//-----------------------------------------------------------------------------
		$Read_Data = json_decode($readValue, true);

		//-----------------------------------------------------------------------------
		// 이곳에 가맹점에서 필요한 데이터 처리를 합니다.
		//-----------------------------------------------------------------------------

		//-----------------------------------------------------------------------------
		// 수신 데이터 사용 예제( 주문서 찾기 )
		//-----------------------------------------------------------------------------
		$sellerOrderReferenceKey	= $Read_Data["sellerOrderReferenceKey"];			// 가맹점에서 발급하는 주문 연동 Key
		$reserveOrderNo				= $Read_Data["reserveOrderNo"];						// 주문예약번호
		$orderNo					= $Read_Data["orderNo"];							// 주문번호
		$memberName					= $Read_Data["memberName"];							// 주문자명

		//-----------------------------------------------------------------------------
		// ...
		// 기타 주문서 생성에 필요한 정보를 가지고 주문서를 조회합니다.
		// 예) 무통장 입금 확인 필드 업데이트
		//-----------------------------------------------------------------------------
		$paymentCompletionYn = $Read_Data["paymentCompletionYn"];			// 지급완료 값 ( Y/N )
		if( $paymentCompletionYn == "Y" ){
			//-------------------------------------------------------------------------
			//지급이 완료 되었다고 받았으면 지급 완료 처리
			//--------------------------------------------------------------------------
			$ErrBoolean = False;											// 정상처리를 위해 오류 표시를 해제
		}

		//-----------------------------------------------------------------------------
		// 결과값을 생성
		//-----------------------------------------------------------------------------
		if( $ErrBoolean == true ){
			$resultValue = "ERROR";											// 오류가 있으면 ERROR를 설정
		} else {
			$resultValue = "OK";											// 오류가 없으면 OK 설정
		}

		//-----------------------------------------------------------------------------
		// 오류일 경우 상세내역을 기록
		//-----------------------------------------------------------------------------
		if( $resultValue == "ERROR" ){
			Write_Log("payco_without_bankbook.php has item error : Couldn't find order.");		// 오류 상세 내역을 이곳에 표시합니다. ( DB 및 주문서 찾기등 오류)
		}

	} catch ( Exception $e ){
		$resultValue = "ERROR";
		Write_Log("payco_without_bankbook.php has logical error : Code - ".$e->getCode().", Description - ".$e->getMessage());
	}

	//---------------------------------------------------------------------------------
	// 결과를 PAYCO 쪽에 리턴
	//---------------------------------------------------------------------------------
	Write_Log("payco_without_bankbook.php send Result : $resultValue");		// 리턴 내역을 기록
	echo $resultValue;
?>