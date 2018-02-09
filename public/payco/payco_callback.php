<?PHP
	//--------------------------------------------------------------------------------
	// PAYCO 주문완료시 호출되는 가맹점 SERVICE API 페이지 샘플 ( PHP EASYPAY / PAY1 )
	// payco_callback.php
	// 2016-03-31	PAYCO기술지원 <dl_payco_ts@nhnent.com>
	//--------------------------------------------------------------------------------
	

	//--------------------------------------------------------------------------------
	// 이 문서는 text/html 형태의 데이터를 반환합니다. ( OK 또는 ERROR 만 반환 )
	//--------------------------------------------------------------------------------
	header('Content-Type: text/html; charset=utf-8'); 
	include("payco_config.php");

	//--------------------------------------------------------------------------------
	// 오류가 발생했는지 기억할 변수와 결과를 담을 변수를 선언합니다.
	//--------------------------------------------------------------------------------
	$ErrBoolean = false;							   // 기본적으로 오류가 아닌것으로 설정
	
	//$readValue	= stripslashes($_REQUEST["response"]); // PHP 5.2 / PHP 함수 stripslashes() 사용하여, Payco 에서 송신하는 값(response)을 JSON 형태로 변경하기전 백슬래시 기호 제거
	$readValue	= $_REQUEST["response"];				   // PHP 5.3 이상에서 적용 / Payco 에서 전달하는 값(response)을 저장
	
	
	try{

		//-----------------------------------------------------------------------------
		// (로그) 호출 시점과 호출값을 파일에 기록합니다.
		//-----------------------------------------------------------------------------
		Write_Log("payco_callback.php is Called - response : $readValue");

		//-----------------------------------------------------------------------------
		// response 값이 없으면 에러(ERROR)를 돌려주고 로그를 기록한 뒤 API를 종료합니다.
		//-----------------------------------------------------------------------------	
		if( $readValue == "" ){
			$resultValue = "Parameter is nothing.";
			Write_Log("payco_callback.php send Result : $resultValue");
			echo "ERROR";
			return;
		}

		//-----------------------------------------------------------------------------
		// Payco 에서 송신하는 값(response)을 JSON 형태로 변경
		// 다차원배열의 값을 추출하여 1~2차원배열로 누적하여 저장 후 에, 데이터 확인에 필요한 값을 변수에 담아 처리합니다.
		//-----------------------------------------------------------------------------
		$Read_Data = json_decode($readValue, true);
		Write_Log("payco_callback.php send Result : ".json_encode($Read_Data));			// 다차원배열 값 확인 ( 디버그용 ) 
		
		//----------------------------------------------------------------------------
		// 수신 데이터 추출 예제 ( 종합 )
		//----------------------------------------------------------------------------
		/*		
		foreach ($Read_Data as $key => $value){
			switch ($key){
				case "deliveryPlace":
					$deliveryPlace = $Read_Data["deliveryPlace"];
					foreach ($deliveryPlace as $key => $value){
						Write_Log("deliveryPlace[$key] : ".$value);
		
					}		
				break;
				
				case "orderProducts":
					$orderProducts = $Read_Data["orderProducts"];
					foreach ($orderProducts as $key => $value){
						Write_Log("orderProducts[$key]");
						$orderProduct = $orderProducts[$key];
						foreach ($orderProduct as $key => $value){
							Write_Log("    $key : ".$value);
						}
					}
				break;
				
				case "paymentDetails":
					$paymentDetails = $Read_Data["paymentDetails"];
					foreach ($paymentDetails as $key => $value){
						Write_Log("paymentDetails[$key] : ");
						$paymentDetail = $paymentDetails[$key];
						foreach ($paymentDetail as $key => $value){
							switch ($paymentDetail["paymentMethodCode"]){
								case "31":
									if ($key=="cardSettleInfo"){
										Write_Log("    cardSettleInfo :");
										$cardSettleInfo = $paymentDetail["cardSettleInfo"];
										foreach ($cardSettleInfo as $key => $value){
											Write_Log("        $key : ".$value);
										}
									} else {
										Write_Log("    $key : ".$value);
									}
								break;
				
								case "75":
								case "76":
								case "77":
									if ($key=="couponSettleInfo"){
										Write_Log("    couponSettleInfo : ");
										$couponSettleInfo = $paymentDetail["couponSettleInfo"];
										foreach ($couponSettleInfo as $key => $value){
											Write_Log("        $key : ".$value);
										}
									} else {
										Write_Log("    $key : ".$value);
									}					
								break;
				
								case "98":
									Write_Log("    $key : ".$value);
								break;
				
								default:
								break;
							}
						}
					}
					
				break;
				
				default:
					Write_Log("$key : ".$value);
				break;
			}
		}
		
		*/
		
		
		//-----------------------------------------------------------------------------
		// 이곳에 가맹점에서 필요한 데이터 처리를 합니다.
		// 예) 재고 체크, 매출금액 확인, 주문서 생성 등등
		//-----------------------------------------------------------------------------
	
		//-----------------------------------------------------------------------------
		// 수신 데이터 사용 예제( 재고 체크 , 주문상품 List, Array(배열) 형태로 전달 받음 )
		//-----------------------------------------------------------------------------	

		$ItemCode = $Read_Data["orderProducts"][0]["orderProductNo"];						//상품 코드
		//Write_Log("payco_callback.php orderProducts > orderProductNo >>> : ".$ItemCode);
		
		//-----------------------------------------------------------------------------
		// ItemCode 로 DB 에서 재고 수량 체크
		// ( DB 에서 상품명과 재고를 읽어와 ItemName과 ItemStock 에 넣었다고 가정 )
		//-----------------------------------------------------------------------------
		
		$ItemName	= $Read_Data["orderProducts"][0]["sellerOrderProductReferenceKey"];
		//Write_Log("payco_callback.php orderProducts > sellerOrderProductReferenceKey >>> : ".$ItemName);
		
		$ItemStock	= 10;																	//연동 실패를 테스트 하시려면 값을 0 으로 설정하시고 정상으로 테스트 하시려면 1보다 큰 값을 넣으세요.
		if( $ItemStock < 1 ){ $ErrBoolean = true;	}										//재고가 1보다 작다면 오류로 설정
		
		// $ErrBoolean = true;
			
		
		
		//-----------------------------------------------------------------------------
		// 수신 데이터 사용 호출 예제( 주문서 데이터 )
		//-----------------------------------------------------------------------------		
		$sellerOrderReferenceKey		=  $Read_Data["sellerOrderReferenceKey"];			// 가맹점에서 발급했던 주문 연동 Key
		$orderCertifyKey				=  $Read_Data["orderCertifyKey"];					// 주문완료통보시 내려받은 인증값
		$reserveOrderNo					=  $Read_Data["reserveOrderNo"];					// PAYCO에서 발급한 주문예약번호
		$orderNo						=  $Read_Data["orderNo"];							// PAYCO에서 발급한 주문번호
		$memberName						=  $Read_Data["memberName"];						// 주문자명
		$totalOrderAmt					=  $Read_Data["totalOrderAmt"];						// 총 주문 금액
		$totalDeliveryFeeAmt			=  $Read_Data["totalDeliveryFeeAmt"];				// 총 배송비 금액
		$totalRemoteAreaDeliveryFeeAmt	=  $Read_Data["totalRemoteAreaDeliveryFeeAmt"];		// 총 추가배송비 금액
		$totalPaymentAmt				=  $Read_Data["totalPaymentAmt"];					// 총 결제 금액
		
		//-----------------------------------------------------------------------------
		// 수신 데이터 사용 호출 예제 2 ( 주문서 데이터 중 serviceUrlParam, String 형태로 전달받음> JSON Decode 처리하여 값추출 )
		//-----------------------------------------------------------------------------
		$serviceUrlParam				=  $Read_Data["serviceUrlParam"];
		
		$serviceUrlParam_decode			=  json_decode($serviceUrlParam, true);   // 배열로 변환
		$serviceUrlParam_CartNo_value   =  $serviceUrlParam_decode["cartNo"];    //  배열 값 추출하여 저장
				
		//Write_Log("serviceUrlParam_CartNo_value  >>> ".$serviceUrlParam_CartNo_value);	 // Cart_No 값을 로그파일에 기록 합니다.
		
		//-----------------------------------------------------------------------------------------------------------------------------
		// 기타 주문서 생성에 필요한 정보를 가지고 주문서를 작성합니다.
		// SERVICE API 가 처음 호출 되었을 때 PAYCO주문번호(orderNo)로 주문서가 이미 만들어져 있다면 오류(ERROR) 입니다.
		//
		// SERVICE API 가 "가승인" (가결제건) 으로 인해 재 호출 되었을 때, PAYCO주문번호(orderNo)로 주문서가 이미 만들어져 있다면 정상처리(OK)를 합니다.
		//------------------------------------------------------------------------------------------------------------------------------
		

		//------------------------------------------------------------------------------
		// 결과값을 생성
		//------------------------------------------------------------------------------
		if($ErrBoolean){ 
			$resultValue = "ERROR";		//오류가 있으면 ERROR를 설정
		} else {
			$resultValue = "OK";		//오류가 없으면 OK 설정
		}

		//--------------------------------------------------------------------------------
		//오류일 경우 상세내역을 기록하고 전체 취소 API( payco_cancel.php )를 호출 합니다.
		//--------------------------------------------------------------------------------
		if ($resultValue == "ERROR"){ 
			 Write_Log("payco_callback.php is Item Error : Item - ".$ItemName." 상품의 재고 부족으로 연동 오류 발생");		//오류 내용을 기록 합니다.
			//-----------------------------------------------------------------------------
			// 결제 취소 API 호출 ( PAYCO 에서 받은 결제정보를 이용해 전체 취소를 합니다. )
			// 취소 내역을 담을 JSON OBJECT를 선언합니다.
			//------------------------------------------------------------------------------
			$cancelOrder = array();
			$cancelOrder["sellerKey"]					= $sellerKey;					// 가맹점 코드. payco_config.php 에 설정
			$cancelOrder["sellerOrderReferenceKey"]		= $sellerOrderReferenceKey;		// 취소주문연동키. ( 파라메터로 넘겨 받은 값 )
			$cancelOrder["orderCertifyKey"]				= $orderCertifyKey;				// 주문완료통보시 내려받은 인증값				
			$cancelOrder["cancelTotalAmt"]				= $totalPaymentAmt;				// 주문서의 총 금액을 입력합니다. (전체취소, 부분취소 전부다)
			Write_Log("payco_callback.php is Item Error : try cancel : ".$cancelOrder);				

			$Result = payco_cancel(stripslashes(json_encode($cancelOrder)));

			//-----------------------------------------------------------------------------
			// 취소 결과가 오류인 경우 가결제건이 생성되니 PAYCO로 문의 부탁드립니다.
			//-----------------------------------------------------------------------------
		}
	} catch( Exception $e) {
		Write_Log("payco_callback.php Logical Error : Code - ".$e->getCode().", Description - ".$e->getMessage());
		echo "ERROR";
		return;
	}

	//-----------------------------------------------------------------------------
	// 결과값을 파일에 기록한다.( 디버그용 )
	//-----------------------------------------------------------------------------
	Write_Log("payco_callback.php send result : $resultValue");

	//-----------------------------------------------------------------------------
	// 결과를 PAYCO 쪽에 리턴 ( OK 또는 ERROR )
	// 결과 " OK " 이고, PAYCO파트너센터 에서 " 가승인 " 상태이면  payco_callback.php 내부 오류임. 디버깅 확인 ( 웹서버, DB, PHP )
	//-----------------------------------------------------------------------------
	echo $resultValue; 	
?>
