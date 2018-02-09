<?
	//--------------------------------------------------------------------------------
	// PAYCO 결제상세 조회(검증용) 페이지 샘플 ( PHP )
	// payco_detailForVerify.php
	// 2016-03-29	PAYCO기술지원 <dl_payco_ts@nhnent.com>
	//--------------------------------------------------------------------------------

	//--------------------------------------------------------------------------------
	// 이 문서는 json 형태의 데이터를 반환합니다.
	//--------------------------------------------------------------------------------
	header('Content-Type: text/html; charset=utf-8'); 
	include("payco_config.php");

	
		//-----------------------------------------------------------------------------
		// 결제상세 조회를 위한 값을 설정합니다.
		//-----------------------------------------------------------------------------
		
		$sellerKey					= $_REQUEST["sellerKey"]; 					// 가맹점키
		$reserveOrderNo				= $_REQUEST["reserveOrderNo"]; 				// PAYCO 주문예약번호
		$sellerOrderReferenceKey	= $_REQUEST["sellerOrderReferenceKey"]; 	// 외부가맹점에서 발급하는 주문 연동 Key */
		
        /*
		// 로컬 단독 실행 테스트용 ( http://xxx.xxx.xxx.xxx/php/checkout/pay1/payco_detailForVerify.php )  
		$sellerKey					= "S0FSJE" ; // 가맹점키
		$reserveOrderNo				= ""; 		 // PAYCO 주문예약번호
		$sellerOrderReferenceKey	= "";      	 // 외부가맹점에서 발급하는 주문 연동 Key
		*/
		
		//----------------------------------------------------------------------------
		// (로그) 호출 시점과 호출값을 파일에 기록합니다.
		//----------------------------------------------------------------------------
		Write_Log("payco_detailForVerify.php is Called - sellerKey : $sellerKey, reserveOrderNo : $reserveOrderNo, sellerOrderReferenceKey : $sellerOrderReferenceKey" );

		//----------------------------------------------------------------------------
		// 설정한 주문정보 변수들로 Json String 을 작성합니다.
		//----------------------------------------------------------------------------
		$detailForVerifyValue = array();
		$detailForVerifyValue["sellerKey"]					= $sellerKey;
		$detailForVerifyValue["reserveOrderNo"]				= $reserveOrderNo;
		$detailForVerifyValue["sellerOrderReferenceKey"]	= $sellerOrderReferenceKey;

		//----------------------------------------------------------------------------
		// 결제상세 조회 함수 호출 ( Array 형태의  데이터를 JSON 형태로 호출 )
		//----------------------------------------------------------------------------
		
		$VerifyValue = payco_detailForVerify(json_encode($detailForVerifyValue));
		//Write_Log("payco_detailForVerify.php is Called - Result[JSON] >>> $Result ");
		
		echo $VerifyValue; 		   	  			     // index.php Ajax 호출 function detailForVerify() 값 전달.
				
		
		
		//----------------------------------------------------------------------------
		// 결제상세 조회 결과 값의 사용 예제 사용을 위한 전체 결과 값 추출 
		//----------------------------------------------------------------------------
		
		// $Read_Data = json_decode($VerifyValue, true); // JSON -> 배열로 변환		
		
		/*
			echo '<pre>';
			print_r($Read_Data); 				 // 결제 상세조회 결과 배열 값 확인
			echo '</pre>';
		*/
			
		
		
		//----------------------------------------------------------------------------
		// 결제상세 조회 결과 값, 사용 호출 예제 1 ( (맨하단) 결제상세 조회 API > OUTPUT 변수명 참조 )
		//----------------------------------------------------------------------------
		
		/*
		 
		echo '<pre>';
		$Result = $Read_Data["result"];
		foreach ($Result as $key => $value){
			switch ($key){
				case "deliveryPlace":
					$deliveryPlace = $Result["deliveryPlace"];
					foreach ($deliveryPlace as $key => $value){
						echo 'deliveryPlace['.$key.'] : '.$value.'<br />';
						
					}
					break;
				case "orderProducts":
					$orderProducts = $Result["orderProducts"];
					foreach ($orderProducts as $key => $value){
						echo 'orderProducts['.$key.'] : <br />';
						$orderProduct = $orderProducts[$key];
						foreach ($orderProduct as $key => $value){
							echo '&nbsp;&nbsp;&nbsp;&nbsp;'.$key.' : '.$value.'<br />';
						}
					}
					break;
				case "paymentDetails":
					$paymentDetails = $Result["paymentDetails"];
					foreach ($paymentDetails as $key => $value){
						echo 'paymentDetails['.$key.'] : <br />';
						$paymentDetail = $paymentDetails[$key];
						foreach ($paymentDetail as $key => $value){
							switch ($paymentDetail["paymentMethodCode"]){
								case "31":
									if ($key=="cardSettleInfo"){
										echo '&nbsp;&nbsp;&nbsp;&nbsp;cardSettleInfo : <br />';
										$cardSettleInfo = $paymentDetail["cardSettleInfo"];
										foreach ($cardSettleInfo as $key => $value){
											echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.$key.' : '.$value.'<br />';
										}
									} else {
										echo '&nbsp;&nbsp;&nbsp;&nbsp;'.$key.' : '.$value.'<br />';
									}
									break;
								case "75":
								case "76":
								case "77":
									if ($key=="couponSettleInfo"){
										echo '&nbsp;&nbsp;&nbsp;&nbsp;couponSettleInfo : <br />';
										$couponSettleInfo = $paymentDetail["couponSettleInfo"];
										foreach ($couponSettleInfo as $key => $value){
											echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.$key.' : '.$value.'<br />';
										}
									} else {
										echo '&nbsp;&nbsp;&nbsp;&nbsp;'.$key.' : '.$value.'<br />';
									}
										break;
								case "98":
									echo '&nbsp;&nbsp;&nbsp;&nbsp;'.$key.' : '.$value.'<br />';
									break;
								default:
									break;
							}
						}
					}
					break;
				default:
					echo $key.' : '.$value.'<br />';
					break;		
			}
		}
		echo '</pre>';
		
		*/
		
		
		
		/*
		//----------------------------------------------------------------------------
		// 결제상세 조회 API > OUTPUT 변수명 상세 내역  ( 상세 내용은 PAYCO_연동_가이드_결제상세_조회(검증용)API.pdf 참조 )
		//----------------------------------------------------------------------------		
		 
		["sellerOrderReferenceKey"];					// 외부가맹점에서 발급하는 주문 연동 Key
		["reserveOrderNo"];								// 주문예약번호
		["orderNo"];									// 주문번호
		["memberName"];									// 주문자명(간편결제형만 일부 마스킹처리)
		["memberEmail"];								// 주문자EMAIL(일부 마스킹처리)
		["orderChannel"];								// 주문채널 (PC/MOBILE)
		["totalOrderAmt"];								// 총 주문금액
		["totalDeliveryFeeAmt"];						// 총 배송비
		["totalRemoteAreaDeliveryFeeAmt"];				// 총 도서산간비
		["totalPaymentAmt"];							// 총 결제금액
		["paymentCompletionYn"];						// 결제완료여부 (Y/N)
		
		["deliveryPlace"]								// 배송지 정보 ( 종합 )
		["recipient"];									// 배송지 정보_수령인
		["englishReceipent"];							// 배송지 정보_수령인 영문명
		["address1"];									// 배송지 정보_기본주소
		["address2"];									// 배송지 정보_상세주소
		["zipcode"];									// 배송지 정보_우편번호
		["deliveryMemo"];								// 배송지 정보_배송메모
		["telephone"];									// 배송지 정보_연락처
		["individualCustomUniqNo"];						// 배송지 정보_개인통관번호
				
		["orderProducts"] 								// 주문상품 List ( 종합 )
		["orderProductNo"];								// 주문상품 List_주문상품번호
		["cpId"];										// 주문상품 List_상점ID
		["productId"];									// 주문상품 List_상품ID
		["sellerOrderProductReferenceKey"];				// 주문상품 List_외부가맹점에서 발급한 주문상품연동Key
		["orderProductStatusCode"];						// 주문상품 List_주문상품 상태코드
		["orderProductStatusName"];						// 주문상품 List_주문상품 상태명
		["productKindCode"];							// 주문상품 List_상품타입
		["productPaymentAmt"];							// 주문상품 List_상품결제금액, 배송비 상품의 경우 (원 상품결제금액 + 추가 배송비) 로 변경 될 수 있음
		["originalProductPaymentAmt"];					// 주문상품 List_원 상품결제금액 , 배송비 상품의 경우 배송비 금액
		
		["paymentDetails"] 								// 결제내역 List    ( 종합 )
		["couponSettleInfo"] 							// 결제내역 List_쿠폰 ( 종합 ) ( 가이드 내용 없음 )
		["discountAmt"];					 	   	    // 결제내역 List_쿠폰_할인금액   ( 가이드 내용 없음 )
		["discountConditionAmt"]; 						// 결제내역 List_쿠폰_할인가능금액 기준(조건)  ( 가이드 내용 없음 )		
		["paymentTradeNo"]; 							// 결제내역 List_결제번호
		["paymentMethodCode"]; 							// 결제내역 List_결제수단코드
		["paymentMethodName"]; 							// 결제내역 List_결제수단명
		["paymentAmt"]; 								// 결제내역 List_결제금액
		["tradeYmdt"]; 									// 결제내역 List_결제일시 (yyyyMMddHHmmss)
		["pgAdmissionNo"]; 								// 결제내역 List_원천사승인번호
		["pgAdmissionYmdt"]; 							// 결제내역 List_원천사승인일시 (yyyyMMddHHmmss)
		["easyPaymentYn"]; 								// 결제내역 List_간편결제여부 (Y/N)
		
		["cardSettleInfo"] 								// 결제내역 List_신용카드 결제 정보 ( 종합 )
		["cardCompanyName"]; 							// 결제내역 List_신용카드 결제 정보_카드사명
		["cardCompanyCode"]; 							// 결제내역 List_신용카드 결제 정보_카드사코드
		["cardNo"]; 		 						 	// 결제내역 List_신용카드 결제 정보_카드번호
		["cardInstallmentMonthNumber"]; 				// 결제내역 List_신용카드 결제 정보_할부개월(MM)
		["cardAdmissionNo"]; 							// 결제내역 List_신용카드 결제 정보_카드사 승인번호
		["cardInterestFreeYn"]; 						// 결제내역 List_신용카드 결제 정보_무이자여부(Y/N)
		["corporateCardYn"]; 							// 결제내역 List_신용카드 결제 정보_법인카드여부(개인 N ,법인 Y)
		["partCancelPossibleYn"]; 						// 결제내역 List_신용카드 결제 정보_부분취소가능유무(Y/N)
				
		["cellphoneSettleInfo"] 						// 결제내역 List_핸드폰 결제 정보 ( 종합 )
		["companyName"]; 								// 결제내역 List_핸드폰 결제 정보_통신사명(코드)
		["cellphoneNo"]; 								// 결제내역 List_핸드폰 결제 정보_휴대폰번호		
		
		["realtimeAccountTransferSettleInfo"] 			// 결제내역 List_실시간계좌이체 결제정보 ( 종합 )
		["bankName"]; 									// 결제내역 List_실시간계좌이체 결제정보_은행명
		["bankCode"]; 									// 결제내역 List_실시간계좌이체 결제정보_은행코드

		["nonBankbookSettleInfo"] 						// 결제내역 List_무통장입금 결제정보 ( 종합 )
		["bankName"]; 									// 결제내역 List_무통장입금 결제정보_은행명
		["bankCode"]; 									// 결제내역 List_무통장입금 결제정보_은행코드
		["accountNo"]; 									// 결제내역 List_무통장입금 결제정보_계좌번호
		["paymentExpirationYmd"];						// 결제내역 List_무통장입금 결제정보_입금만료일
													
		["orderCertifyKey"];							// 주문인증키
		
		*/
		
		//-----------------------------------------------------------------------------
		// ...
		// 결제상세 조회 (검증용) 수신 데이터를 이용하여, 처리할 부분을 이곳에 작성합니다.
		// ...
		//-----------------------------------------------------------------------------
			
		
?>

		
		
		
