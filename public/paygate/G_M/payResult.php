<?

require_once dirname(__FILE__).'/lib/nicepay/web/NicePayWEB.php';
require_once dirname(__FILE__).'/lib/nicepay/core/Constants.php';
require_once dirname(__FILE__).'/lib/nicepay/web/NicePayHttpServletRequestWrapper.php';


/** 1. Request Wrapper 클래스를 등록한다.  */ 
$httpRequestWrapper = new NicePayHttpServletRequestWrapper($_REQUEST);
$_REQUEST = $httpRequestWrapper->getHttpRequestMap();

/** 2. 소켓 어댑터와 연동하는 Web 인터페이스 객체를 생성한다.*/
$nicepayWEB = new NicePayWEB();

/** 2-1. 로그 디렉토리 설정 */
$nicepayWEB->setParam("NICEPAY_LOG_HOME","./log");

/** 2-2. 어플리케이션 로그 모드 설정(0: DISABLE, 1: ENABLE) */
$nicepayWEB->setParam("APP_LOG","1");

/** 2-3. 암호화플래그 설정(N: 평문, S:암호화) */
$nicepayWEB->setParam("EncFlag","S");

/** 2-4. 서비스모드 설정(결제 서비스 : PY0 , 취소 서비스 : CL0) */
$nicepayWEB->setParam("SERVICE_MODE", "PY0");

/** 2-5. 통화구분 설정(현재 KRW(원화) 가능)  */
$nicepayWEB->setParam("Currency", "KRW");

/** 2-6. 결제수단 설정 (신용카드결제 : CARD, 계좌이체: BANK, 가상계좌이체 : VBANK, 휴대폰결제 : CELLPHONE ) */
$payMethod = $_REQUEST['PayMethod'];
$nicepayWEB->setParam("PayMethod",$_REQUEST['PayMethod']);

/** 2-7 라이센스키 설정 
	상점 ID에 맞는 상점키를 설정하십시요.
	*/
$nicepayWEB->setParam("LicenseKey","33F49GnCMS1mFYlGXisbUDzVf2ATWCl9k3R++d5hDd3Frmuos/XLx8XhXpe+LDYAbpGKZYSwtlyyLOtS/8aD7A==");

/** 3. 결제 요청 */
$responseDTO = $nicepayWEB->doService($_REQUEST);

/** 4. 결제 결과 */
$resultCode = $responseDTO->getParameter("ResultCode"); // 결과코드 (정상 :3001 , 그 외 에러)
$resultMsg = $responseDTO->getParameter("ResultMsg");   // 결과메시지
$authDate = $responseDTO->getParameter("AuthDate");   // 승인일시 YYMMDDHH24mmss
$authCode = $responseDTO->getParameter("AuthCode");   // 승인번호
$buyerName = $responseDTO->getParameter("BuyerName");   // 구매자명
$mallUserID = $responseDTO->getParameter("MallUserID");   // 회원사고객ID
$goodsName = $responseDTO->getParameter("GoodsName");   // 상품명
$mallUserID = $responseDTO->getParameter("MallUserID");  // 회원사ID
$mid = $responseDTO->getParameter("MID");  // 상점ID
$tid = $responseDTO->getParameter("TID");  // 거래ID
$moid = $responseDTO->getParameter("Moid");  // 주문번호
$amt = $responseDTO->getParameter("Amt");  // 금액

$cardQuota = $responseDTO->getParameter("CardQuota");   // 할부개월
$cardCode = $responseDTO->getParameter("CardCode");   // 결제카드사코드
$cardName = $responseDTO->getParameter("CardName");   // 결제카드사명

$bankCode = $responseDTO->getParameter("BankCode");   // 은행코드
$bankName = $responseDTO->getParameter("BankName");   // 은행명
$rcptType = $responseDTO->getParameter("RcptType"); //현금 영수증 타입 (0:발행되지않음,1:소득공제,2:지출증빙)
$rcptAuthCode = $responseDTO->getParameter("RcptAuthCode");   // 현금영수증 승인번호

$carrier = $responseDTO->getParameter("Carrier");       // 이통사구분
$dstAddr = $responseDTO->getParameter("DstAddr");       // 휴대폰번호

$vbankBankCode = $responseDTO->getParameter("VbankBankCode");   // 가상계좌은행코드
$vbankBankName = $responseDTO->getParameter("VbankBankName");   // 가상계좌은행명
$vbankNum = $responseDTO->getParameter("VbankNum");   // 가상계좌번호
$vbankExpDate = $responseDTO->getParameter("VbankExpDate");   // 가상계좌입금예정일

$mallReserved = $_REQUEST['MallReserved'];

/** 위의 응답 데이터 외에도 전문 Header와 개별부 데이터 Get 가능 */

$paySuccess = false;		// 결제 성공 여부
if($payMethod == "CARD"){	//신용카드
	if($resultCode == "3001") $paySuccess = true;	// 결과코드 (정상 :3001 , 그 외 에러)
}else if($payMethod == "BANK"){		//계좌이체
	if($resultCode == "4000") $paySuccess = true;	// 결과코드 (정상 :4000 , 그 외 에러)
}else if($payMethod == "CELLPHONE"){			//휴대폰
	if($resultCode == "A000") $paySuccess = true;	//결과코드 (정상 : A000, 그 외 비정상)
}else if($payMethod == "VBANK"){		//가상계좌
	if($resultCode == "4100") $paySuccess = true;	// 결과코드 (정상 :4100 , 그 외 에러)
}

if($paySuccess == true){
   // 결제 성공시 DB처리 하세요.
}else{
   // 결제 실패시 DB처리 하세요.
}

?>
<html>
<head><title>NicePAy</title>
<link href="./css/__smart.css" rel="stylesheet" type="text/css"/>
<meta http-equiv="Content-Type" content="text/html; charset=euc-kr;"/>
<meta name="viewport" content="initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0, user-scalable=no"/>
</head>
<body>
        <div class="selectList">
                <ul>
                        <li class="selectBar">
                                <label>· 결제수단</label>
                                <span><?=$payMethod?></span>
                        </li>
                        <li class="selectBar">
                                <label>· 상점ID</label>
                                <span><?=$mid ?></span>
                        </li>
                        <li class="selectBar">
                                <label>· 금액</label>
                                <span><?=$amt ?></span>
                        </li>
                        <li class="selectBar">
                                <label>· 구매자명</label>
                                <span><?=$buyerName ?></span>
                        </li>
                        <li class="selectBar">
                                <label>· 상품명</label>
                                <span><?=$goodsName ?></span>
                        </li>
                       
                        <li class="selectBar">
                                <label>· 거래번호</label>
                                <span><?=$tid ?></span>
                        </li>
                        <li class="selectBar">
                                <label>· 주문번호</label>
                                <span><?=$moid ?></span>
                        </li>
                        <li class="selectBar">
                                <label>· 승인일자</label>
                                <span><?=$authDate ?></span>
                        </li>
                       
                        <li class="selectBar">
                                <label>· 결과코드</label>
                                <span><?=$resultCode ?></span>
                        </li>
                        <li class="selectBar">
                                <label>· 결과메시지</label>
                                <span><?=$resultMsg ?></span>
                        </li>
						
                        <li class="selectBar">
                                <label>· 상점예비번호</label>
                                <span><?=$mallReserved ?></span>
                        </li>
						<?if($payMethod == "CARD"){?>
							<li class="selectBar">
									<label>· 결제카드사코드</label>
									<span><?=$cardCode ?></span>
							</li>
							<li class="selectBar">
									<label>· 결제카드사명</label>
									<span><?=$cardName ?></span>
							</li>
							 <li class="selectBar">
									<label>· 승인번호</label>
									<span><?=$authCode ?></span>
							</li>
							<li class="selectBar">
									<label>· 할부개월수</label>
									<span><?=$cardQuota ?></span>
							</li>
						<?}else if($payMethod == "BANK"){?>
							<li class="selectBar">
                                <label>· 은행명</label>
                                <span><?=$bankName ?></span>
							</li>
						<?}else if($payMethod == "CELLPHONE"){?>
							<li class="selectBar">
                                <label>· 휴대폰번호</label>
                                <span><?=$dstAddr ?></span>
							</li>
						<?}else if($payMethod=="VBANK"){?>
							<li class="selectBar">
                                <label>· 가상계좌번호</label>
                                <span><?=$vbankNum ?></span>
							</li>
							<li class="selectBar">
									<label>· 가상계좌은행명</label>
									<span><?=$vbankBankName ?></span>
							</li>
							
						<?}?>
                       
                       
                      
                </ul>
        </div>
</body>
</html>
