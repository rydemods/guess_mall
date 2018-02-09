<?PHP
	//-----------------------------------------------------------------------------
	// PAYCO 주문 예약 페이지 샘플 ( PHP EASYPAY / PAY1 )
	// payco_reserve.php
	// 2016-02-19	PAYCO기술지원 <dl_payco_ts@nhnent.com>
	//-----------------------------------------------------------------------------
	header('Content-Type: text/html; charset=utf-8'); 
	include("payco_config.php");

	//-----------------------------------------------------------------------------
	// 이 문서는 json 형태의 데이터를 반환합니다.
	//-----------------------------------------------------------------------------
	// header("Content-Type:application/json"); 

	//---------------------------------------------------------------------------------
	// 이전 페이지에서 전달받은 고객 주문번호 설정, 장바구니 번호 설정
	//---------------------------------------------------------------------------------
	$customerOrderNumber = $_REQUEST["customerOrderNumber"];	
	$cartNo              = $_REQUEST["cartNo"];
	
	//-----------------------------------------------------------------------------
	// (로그) 호출 시점과 호출값을 파일에 기록합니다.
	//-----------------------------------------------------------------------------
	Write_Log("payco_reserve.php is Called - customerOrderNumber : $customerOrderNumber, cartNo : $cartNo");

	//---------------------------------------------------------------------------------
	// 상품정보 변수 선언 및 초기화
	//---------------------------------------------------------------------------------
	Global $cpId, $productId;

	//---------------------------------------------------------------------------------
	// 변수 초기화
	//---------------------------------------------------------------------------------
	$TotalProductPaymentAmt = 0	;		//주문 상품이 여러개일 경우 상품들의 총 금액을 저장할 변수
	$OrderNumber			= 0;		//주문 상품이 여러개일 경우 순번을 매길 변수

	//---------------------------------------------------------------------------------
	// 구매 상품을 변수에 셋팅 ( JSON 문자열을 생성 )
	//---------------------------------------------------------------------------------
	$ProductRows = array();				// (필수) 주문서에 담길 상품 목록 생성

	$tmpTotalTaxfreeAmt = 0;			// 면세상품합
	$tmpTotalTaxableAmt = 0;			// 과세상품합
	$tmpTotalVatAmt		= 0;			// 부가세합

	//---------------------------------------------------------------------------------
	// 상품정보 값 입력
	//---------------------------------------------------------------------------------
	$OrderNumber					= $OrderNumber + 1;									// 상품에 순번을 정하기 위해 값을 증가합니다.

	$orderQuantity					= 1;												// (필수) 주문수량 (1로 세팅)
	$productUnitPrice				= 100000;											// (필수) 상품 단가      ( 테스트용으로써 100,000원으로 설정. )
	$productUnitPaymentPrice		= 100000+2500;										// (필수) 상품 결제 단가 ( 테스트용으로써 100,000원으로 설정. 배송비 설정시 상품가격에 포함시킴 ex)2,500 )

	//상품단가(productAmt)는 원 상품단가이고 상품결제단가(productPaymentAmt)는 상품단가에서 할인등을 받은 금액입니다. 실제 결제에는 상품결제단가가 사용됩니다.
	$productAmt						= $productUnitPrice * $orderQuantity;				// (필수) 상품 결제금액(상품단가 * 수량)
	$productPaymentAmt				= $productUnitPaymentPrice * $orderQuantity; 		// (필수) 상품 결제금액(상품결제단가 * 수량)
	$TotalProductPaymentAmt			= $TotalProductPaymentAmt + $productPaymentAmt;		// 주문정보를 구성하기 위한 상품들 누적 결제 금액(상품 결제 금액)
	
	$iOption						= "신발사이즈";										    // 옵션 ( 최대 100 자리 )
	$sortOrdering					= $OrderNumber;										// (필수) 상품노출순서, 10자 이내
	$productName					= "아디다스 위네오 슈퍼 웨지";								// (필수) 상품명, 4000자 이내
	$orderConfirmUrl				= "";												// 주문완료 후 주문상품을 확인할 수 있는 url, 4000자 이내
	$orderConfirmMobileUrl			= "";												// 주문완료 후 주문상품을 확인할 수 있는 모바일 url, 1000자 이내
	$productImageUrl				= "";												// 이미지URL (배송비 상품이 아닌 경우는 필수), 4000자 이내, productImageUrl에 적힌 이미지를 썸네일해서 PAYCO 주문창에 보여줍니다.
	$sellerOrderProductReferenceKey = "ITEM_100001"	;									// 외부가맹점에서 관리하는 주문상품 연동 키(sellerOrderProductReferenceKey)는 주문 별로 고유한 key이어야 한다.(주문당 1건에 대한 상품을 보낼시엔 해당사항없음)
	$taxationType					= "TAXATION";										// 과세타입(기본값 : 과세),	DUTYFREE :면세,	SMALL : 영세,	TAXATION : 과세
	
	//---------------------------------------------------------------------------------------------------------------------------------
	// 주문서에 담길 부가 정보를 JSON 으로 작성 (필요시 사용) 
	// payExpiryYmdt			: 해당 주문예약건 만료 처리 일시 
	// virtualAccountExpiryYmd  : 가상계좌만료일시
	//
	// cancelMobileUrl			: 모바일 결제페이지에서 취소 버튼 클릭시 이동할 URL (결제창 이전 URL 등). 미입력시 메인 URL로 이동
	/// 모바일 결제페이지에서 취소 버튼 클릭시 이동할 URL (결제창 이전 URL 등)
	/// 1순위 : (앱결제인 경우) 주문예약 > customUrlSchemeUseYn 의 값이 Y인 경우 => "nebilres://orderCancel" 으로 이동
	/// 2순위 : 주문예약 > extraData > cancelMobileUrl 값이 있을시 => cancelMobileUrl 이동
	/// 3순위 : 주문예약시 전달받은 returnUrl 이동 + 실패코드(오류코드:2222)
	/// 4순위 : 가맹점 URL로 이동(가맹점등록시 받은 사이트URL)
	/// 5순위 : 이전 페이지로 이동 => history.Back();
	//
	// viewOptions			    : 화면UI옵션(showMobileTopGnbYn : 모바일 상단 GNB 노출여부 , iframeYn : Iframe 호출현재 iframeYN의 용도는 없으며, 차후 iframe 이슈 대응을 위한 필드로 iframe 사용인 경우는 Y로사용 )
	//---------------------------------------------------------------------------------------------------------------------------------
	 
	//$payExpiryYmdt			             	= "20171231180000";	             // 미적용시, 자동으로 만료시간 지정됨.
	$virtualAccountExpiryYmd					= "20171231180000";
	$cancelMobileUrl 							= "http://www.payco.com";
	
	$viewOptionsArry 							= array();                      
	$viewOptionsArry["showMobileTopGnbYn"]		= "N";
	$viewOptionsArry["iframeYn"]				= "N";
	//$viewOptions = json_encode($viewOptionsArry);                             // 배열 형태를 JSON 으로 Encode 금지. 주문예약 요청 JSON 형식에 맞지않는 역슬래시가 자동 추가됨.
		
	$extraDataArray								= array();
	//$extraDataArray["payExpiryYmdt"] 			= $payExpiryYmdt;
	$extraDataArray["virtualAccountExpiryYmd"] 	= $virtualAccountExpiryYmd;
	$extraDataArray["cancelMobileUrl"] 			= $cancelMobileUrl;			
	$extraDataArray["viewOptions"] 				= $viewOptionsArry; 			
	
	$extraData = addslashes(json_encode($extraDataArray));
		
	//Write_Log("payco_reserve.php is Called >>>> extraData : $extraData");
	
	
	//-----------------------------------------------------------------------------------------------------------------------------------------------------------
	// $tmpTotalTaxfreeAmt(면세상품 총액) / $tmpTotalTaxableAmt(과세상품 총액) / $tmpTotalVatAmt(부가세 총액) -> 일부 필요한 가맹점을위한 예제임 (필요시 사용)
	//------------------------------------------------------------------------------------------------------------------------------------------------------------

	// 면세상품일 경우
	if( $taxationType == "DUTYFREE"){
		$tmpTotalTaxfreeAmt = 0;

	// 과세상품일 경우
	} elseif( $taxationType == "TAXATION") {
		$tmpTotalTaxableAmt = round($TotalProductPaymentAmt/1.1);
		$tmpTotalVatAmt		= $TotalProductPaymentAmt - $tmpTotalTaxableAmt;
	// 복합상품일 경우
	}else{
		$tmpTotalTaxfreeAmt = 0;
		$tmpTotalTaxableAmt = round($TotalProductPaymentAmt/1.1);
		$tmpTotalVatAmt		= $TotalProductPaymentAmt - $tmpTotalTaxableAmt;
	}
	
	
	//---------------------------------------------------------------------------------
	// 상품값으로 읽은 변수들로 Json String 을 작성합니다.
	//---------------------------------------------------------------------------------
	try {
		$ProductsList = array();
		$ProductsList["cpId"]					= $cpId;
		$ProductsList["productId"]				= $productId;
		$ProductsList["productAmt"]				= $productAmt;
		$ProductsList["productPaymentAmt"]		= $productPaymentAmt;
		$ProductsList["orderQuantity"]			= $orderQuantity;
		$ProductsList["option"]					= urlencode($iOption);
		$ProductsList["sortOrdering"]			= $sortOrdering;
		$ProductsList["productName"]			= urlencode($productName);

		if ( $orderConfirmUrl					!= "") {		$ProductsList["orderConfirmUrl"]				= $orderConfirmUrl; 				};
		if ( $orderConfirmMobileUrl				!= "") {		$ProductsList["orderConfirmMobileUrl"]			= $orderConfirmMobileUrl;			};
		if ( $productImageUrl					!= "") {		$ProductsList["productImageUrl"]				= $productImageUrl;					};
		if ( $sellerOrderProductReferenceKey	!= "") {		$ProductsList["sellerOrderProductReferenceKey"] = $sellerOrderProductReferenceKey;	};			
		array_push($ProductRows, $ProductsList);

	} catch ( Exception $e ) {
		$Error_Return = array();
		$Error_Return["result"]		= "DB_RECORDSET_ERROR";
		$Error_Return["message"]	= $e->getMassage();
		$Error_Return["code"]		= $e->getLine();
		return json_encode($Error_Return);
	}

	//---------------------------------------------------------------------------------
	// 주문정보 변수 선언
	//---------------------------------------------------------------------------------
	Global $sellerKey,$AppWebPath;
	
	//---------------------------------------------------------------------------------
	// 주문정보 값 입력 ( 가맹점 수정 가능 부분 )
	//---------------------------------------------------------------------------------
	$sellerOrderReferenceKey		= $customerOrderNumber;														// (필수) 외부가맹점의 주문번호
	$sellerOrderReferenceKeyType	= "UNIQUE_KEY";																//  외부가맹점의 주문번호 타입 UNIQUE_KEY 유니크 키 - 기본값, DUPLICATE_KEY 중복 가능한 키( 외부가맹점의 주문번호가 중복 가능한 경우 사용)

	$iCurrency						= "KRW";																	// 통화(default=KRW)

	$totalPaymentAmt				= $TotalProductPaymentAmt;													// (필수) 총 결재 할 금액.

	$orderTitle						= "PAYCO샘플소스(PHP_EasyPay_PAY1) 상품 외 1 건";											// 주문 타이틀	
	
	$serviceUrl						= $AppWebPath."/payco_callback.php";											// 주문완료 시 PAYCO에서 호출할 가맹점의 Service API의 URL
	//$serviceUrl						= "http://210.206.104.164/payco_php/WebContent/easypay/pay1_patch009/payco_callback.php";											// 주문완료 시 PAYCO에서 호출할 가맹점의 Service API의 URL
	
	//---------------------------------------------------------------------------------------------------------------------------------
	//$serviceUrlParam 담길 값를 JSON 으로 작성 (필요시 사용)
	//---------------------------------------------------------------------------------------------------------------------------------
	$cartNoArray = array();
	$cartNoArray["cartNo"] = $cartNo;                      // 장바구니 번호
	$cartNoJSON = addslashes(json_encode($cartNoArray));   // {\"cartNo\":\"CartNo_12345\"}
	
	//주문완료 시 PAYCO에서 가맹점의 Service API 호출할때 같이 전달할 파라미터(payco_reserve.php 에서 payco_callback.php 로 전달할 값을 JSON 형태의 문자열로 전달)
	$serviceUrlParam                = $cartNoJSON;         
		
	$returnUrl						= $AppWebPath.'/payco_return.php';											// 주문완료 후 Redirect 되는 Url
	$returnUrlParam					= $cartNoJSON;        // {\"cartNo\":\"CartNo_12345\"}
	// 주문완료 후 Redirect 되는 Url 에 함께 전달되어야 하는 파라미터
	
	$nonBankbookDepositInformUrl	= $AppWebPath.'/payco_without_bankbook.php';									// 무통장입금완료통보 URL
	$orderMethod					= "EASYPAY";																// (필수) 주문유형(=결재유형) - 체크아웃형 : CHECKOUT - 간편결제형+가맹점 id 로그인 : EASYPAY_F , 간편결제형+가맹점 id 비로그인(PAYCO 회원구매) : EASYPAY
	$orderChannel					= "PC";																		// 주문채널 ( default : PC / MOBILE )
	$inAppYn						= "N";																		// 인앱결제 여부( Y/N ) ( default = N )
	$individualCustomNoInputYn		= "N";																		// 개인통관고유번호 입력 여부 ( Y/N ) ( default = N )
	$orderSheetUiType				= "GRAY";																	// 주문서 UI 타입 선택 ( 선택 가능값 : RED / GRAY )
	$payMode						= "PAY1";																	// 결제모드 ( PAY1 - 결제인증, 승인통합 / PAY2 - 결제인증, 승인분리 )
	
	//-----------------------------------------------------------------------------------------------------------------------------------------------------------
	// $tmpTotalTaxfreeAmt(면세상품 총액) / $tmpTotalTaxableAmt(과세상품 총액) / $tmpTotalVatAmt(부가세 총액) -> 일부 필요한 가맹점을위한 예제임 (필요시 사용)
	//------------------------------------------------------------------------------------------------------------------------------------------------------------
	$totalTaxfreeAmt				= $tmpTotalTaxfreeAmt;														// 면세금액(면세상품의 공급가액 합)					
	$totalTaxableAmt				= $tmpTotalTaxableAmt;														// 과세금액(과세상품의 공급가액 합)
	$totalVatAmt					= $tmpTotalVatAmt;															// 부가세(과세상품의 부가세 합)
	
	
	
	//---------------------------------------------------------------------------------
	// 설정한 주문정보들을 Json String 을 작성합니다.
	//---------------------------------------------------------------------------------		

	$json = array();
	try {
		$strJson = array();
		$strJson["sellerKey"]					= $sellerKey;
		$strJson["sellerOrderReferenceKey"]		= $sellerOrderReferenceKey;		
		$strJson["sellerOrderReferenceKeyType"] = $sellerOrderReferenceKeyType;
		
		$strJson["totalPaymentAmt"]			= $totalPaymentAmt;
		$strJson["orderTitle"]				= urlencode($orderTitle);
		$strJson["orderMethod"]				= $orderMethod;
		if ( $iCurrency						!= "") {		$strJson["currency"]					= $iCurrency;					};
		if ( $serviceUrl					!= "") {		$strJson["serviceUrl"]					= $serviceUrl;					};
		if ( $serviceUrlParam				!= "") {		$strJson["serviceUrlParam"]				= $serviceUrlParam;				};
		if ( $returnUrl						!= "") {		$strJson["returnUrl"]					= $returnUrl;					};
		if ( $returnUrlParam				!= "") {		$strJson["returnUrlParam"]				= $returnUrlParam;				};
		if ( $nonBankbookDepositInformUrl	!= "") {		$strJson["nonBankbookDepositInformUrl"] = $nonBankbookDepositInformUrl;	};		
		if ( $orderChannel					!= "") {		$strJson["orderChannel"]				= $orderChannel;				};
		if ( $inAppYn						!= "") {		$strJson["inAppYn"]						= $inAppYn;						};
		if ( $individualCustomNoInputYn		!= "") {		$strJson["individualCustomNoInputYn"]	= $individualCustomNoInputYn;	};
		if ( $orderSheetUiType				!= "") {		$strJson["orderSheetUiType"]			= $orderSheetUiType;			};
		if ( $payMode != "")					   {		$strJson["payMode"] = $payMode;											};
		
	//-----------------------------------------------------------------------------------------------------------------------------------------------------------
	// $tmpTotalTaxfreeAmt(면세상품 총액) / $tmpTotalTaxableAmt(과세상품 총액) / $tmpTotalVatAmt(부가세 총액) -> 일부 필요한 가맹점을위한 예제임 (필요시 사용)
	//------------------------------------------------------------------------------------------------------------------------------------------------------------
		$strJson["totalTaxfreeAmt"]			= $totalTaxfreeAmt;
		$strJson["totalTaxableAmt"]			= $totalTaxableAmt;
		$strJson["totalVatAmt"]				= $totalVatAmt;

		$strJson["extraData"]				= $extraData;
		$strJson["orderProducts"]			= $ProductRows;

		$res =  payco_reserve(urldecode(stripslashes(json_encode($strJson))));

		echo $res;
	} catch ( Exception $e ) {
		$Error_Return				= array();
		$Error_Return["result"]		= "RESERVE_ERROR";
		$Error_Return["message"]	= $e->getMassage();
		$Error_Return["code"]		= $e->getCode();
		Write_Log("payco_reserve.php Logical Error : Code - ".$e->getCode().", Description - ".$e->getMessage());
		return json_encode($Error_Return);
	}
?>
