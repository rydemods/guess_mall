<?php // hspark
if(basename($_SERVER['SCRIPT_NAME'])===basename(__FILE__)) {
	header("HTTP/1.0 404 Not Found");
	exit;
}
session_start();
if(@$_REQUEST['DEBUG']) $_SESSION['DEBUG']=1;

include_once($Dir."lib/mail.php");
include_once($Dir."lib/libsms.php");
@include_once($Dir."data/bizConfig.php");
@include_once($Dir."conf/config.php");
@include_once($Dir."conf/sabangnet_code.php");
@include_once($Dir.'lib/types_array.php' );
@include_once($Dir.'lib/lib_erp.php' );
@include_once($Dir.'lib/lib_getup_erp.php' );
include_once($Dir."lib/sync.class.php");	//2016-10-06 libe90 싱크커머스 전송용 class파일
include_once($Dir."alimtalk/alimtalk.class.php");

//주문취소시 돌려주는 포인트의 유호기간
//$return_point_term	= 30;
list($return_point_term)=pmysql_fetch_array(pmysql_query("SELECT reserve_term FROM tblshopinfo limit 1"));
//exdebug($return_point_term);

$arrayCustomerHeadTitle = array("1"=>"로그인", "2"=>"회원가입", "3"=>"구매관련", "4"=>"배송관련", "5"=>"결제관련","6"=>"매장관련","7"=>"기타");

$arrayColorCode = array("0"=>"black", "1"=>"white", "2"=>"red", "3"=>"orange", "4"=>"yellow", "5"=>"green", "6"=>"blue", "7"=>"purple", "8"=>"pink", "9"=>"brown", "10"=>"gray");

// '1주일' 제외
//$arrSearchDate = array('TODAY'=>'오늘', '1WEEK'=>'1주일', '1MONTH'=>'1개월', '3MONTH'=>'3개월', '6MONTH'=>'6개월', '12MONTH'=>'12개월');
//$arrSearchDate = array('1MONTH'=>'1개월', '3MONTH'=>'3개월', '6MONTH'=>'6개월', '9MONTH'=>'9개월', '12MONTH'=>'12개월');
$arrSearchDate = array('1MONTH'=>'1개월', '3MONTH'=>'3개월', '6MONTH'=>'6개월', '12MONTH'=>'12개월');

$arrSearchDate2 = array('TODAY'=>'오늘', '1WEEK'=>'최근 1주일', '2WEEK'=>'최근 2주일', '3WEEK'=>'최근 3주일','1MONTH'=>'최근 1개월', '3MONTH'=>'최근 3개월', '6MONTH'=>'최근 6개월');

$arrMemberOutReason = array('1'=>'상품 품질 불만', '2'=>'주문취소,반품,교환불만', '3'=>'쇼핑몰속도불만', '4'=>'배송지연', '5'=>'이용빈도낮음', '6'=>'기타');

# 고정값으로 변하지 않는 등급 금액 조건
$arrGradeLevelUp = array('10'=>array('0', '29999'), '20'=>array('30000', '299999'), '30'=>array('300000', '699999'), '40'=>array('700000', '99999999999'));

$ordersteparr = array("1"=>"주문접수","2"=>"결제확인","3"=>"배송준비","4"=>"배송중","5"=>"주문취소","6"=>"결제(카드)실패","7"=>"반송","8"=>"취소요청","9"=>"환불대기","10"=>"환불","11"=>"배송완료","12"=>"구매확정");
$arrDeliveryType = array("0" => "택배발송", "1" => "매장픽업", "2"=>"매장발송", "3" => "당일수령");
// $arrDeliveryType = array("0" => "택배발송");

# 발송 상태 코드
$arrChainCode = array("0"=>"일반택배", "1"=>"매장픽업", "2"=>"매장발송", "3"=>"당일수령");
$arrChainCode2 = array("0"=>"택배(본사발송)", "1"=>"매장픽업", "2"=>"택배(매장발송)", "3"=>"당일수령");

#사이즈 체크
$product_size=array("XS"=>array("74"),"S"=>array("55","78","95","225","230"),"M"=>array("66","82","84","100","103","235","240"),"L"=>array("77","86","105","245"),"XL"=>array("88","90","110"),"XXL"=>array("88","94","96"));

#브랜드별 대표카테고리 체크
$brand_main_cate=array("307"=>"001004","306"=>"001004","305"=>"001004","304"=>"001001","303"=>"001001","302"=>"001001","301"=>"001001");

#싱크 본사코드
$sync_bon_code="A1801B";
#카드사 array
$KCP_CARD = array(
	'신한'=>'CCLG',
	'광주'=>'CCKJ',
	'현대'=>'CCDI',
	'수협'=>'CCSU',
	'롯데'=>'CCLO',
	'전북'=>'CCJB',
	'외환'=>'CCKE',
	'제주'=>'CCCJ',
	'삼성'=>'CCSS',
	'KDB 산은'=>'CCKD',
	'국민'=>'CCKM',
	'저축'=>'CCSB',
	'비씨'=>'CCBC',
	'신협'=>'CCCU',
	'농협'=>'CCNH',
	'우체국'=>'CCPB',
	'하나 SK'=>'CCHN',
	'MG 새마을'=>'CCSM'
);

# kcp 환불은행 코드
$kcp_bank_code = array(
	"02" => "산업은행",
	"03" => "기업은행",
	"04" => "국민은행",
	"05" => "외환은행",
	"07" => "수협",
	"11" => "농협",
	"20" => "우리은행",
	"23" => "SC제일은행",
	"27" => "한국시티은행",
	"31" => "대구은행",
	"32" => "부산은행",
	"34" => "광주은행",
	"35" => "제주은행",
	"37" => "전북은행",
	"39" => "경남은행",
	"45" => "새마을금고",
	"48" => "신협",
	"54" => "HSBC",
	"64" => "산림조합",
	"71" => "우체국",
	"81" => "하나은행",
	"88" => "신한은행"
);

### 매장검색
$store_area = array(1=>"서울","경기","인천","강원","충남","대전","충북","부산","울산","대구","경북","경남","전남","광주","전북","제주","세종");
//$store_category = array(1=>"백화점", "대리점","상설점");
$store_category = array(1=>"직영점", "백화점", "대리점");
$store_vwflag = array("숨김","노출");

### 프로모션 종류
$arrPromotionList = array(
	0 => "타임세일",
    1 => "일반기획전",
    2 => "댓글",
    3 => "포토",
    //4 => "출석체크",
);

### pc, 모바일, app 구분
$arr_mobile = array("0"=>"PC", "1"=>"MOBILE", "2"=>"APP");
$arr_mobile2 = array("0"=>"PC", "1"=>"모바일", "2"=>"모바일<font style='font-size:11px;color:red'>(앱)</font>");
/*
sabangnet_code.php 로 대체
$arraySabangnetShopCode = array("shop0001" => "옥션",
															"shop0002" => "지마켓",
															"shop0003" => "11번가",
															 "shop0004 " => " 인터파크",
															"shop0005" => "CJOshopping",
															"shop0007" => "GS shop",
															"shop0006" => "현대홈쇼핑",
															"shop0008" => "롯데홈쇼핑",
															"shop0009" => "농수산홈쇼핑",
															"shop0010" => "롯데닷컴",
															"shop0011" => "신세계몰",
															"shop0012" => "d&shop",
															"shop0013" => "AKmall",
															"shop0014" => "QOOK쇼핑",
															"shop0015" => "NH쇼핑",
															"shop0016" => "패션플러스",
															"shop0017" => "오가게",
															"shop0018" => "iSTYLE24",
															"shop0020" => "하프클럽",
															"shop0021" => "이지웰",
															"shop0022" => "이마트",
															"shop0023" => "1300K",
															"shop0024" => "WIZWID",
															"shop0025" => "MakeShop",
															"shop0026" => "cafe24",
															"shop0027" => "PLAYER",
															"shop0028" => "도서11번가",
															"shop0029" => "YES24",
															"shop0030" => "고도몰",
															"shop0031" => "2001-OUTLET",
															"shop0032" => "와와닷컴",
															"shop0033" => "상록몰",
															"shop0034" => "오셀러",
															"shop0035" => "FoodMart",
															"shop0036" => "동원몰",
															"shop0037" => "패션밀",
															"shop0038" => "팔도오일장",
															"shop0039" => "Homeplus",
															"shop0040" => "세일코리아",
															"shop0041" => "네이버 체크아웃",
															"shop0042" => "텐바이텐",
															"shop0043" => "두바이",
															"shop0044" => "체크아이몰",
															"shop0045" => "홀리스퀘어",
															"shop0046" => "NH마켓",
															"shop9999" => "자사운영쇼핑몰");
*/

//$arpm=array("B"=>"무통장","V"=>"계좌이체","O"=>"가상계좌","Q"=>"가상계좌(매매보호)","C"=>"신용카드","P"=>"신용카드(매매보호)","M"=>"핸드폰","Y"=>"PAYCO");

$arpm	= array(
	"Y"=>"PAYCO",
	"C"=>"신용카드",
	/*"P"=>"신용카드(매매보호)",*/
	"V"=>"계좌이체",
	"B"=>"무통장",
	"O"=>"가상계좌",
	"Q"=>"가상계좌(매매보호)",
	/*"G"=>"임직원 포인트",*/
	"M"=>"핸드폰"
);

/**
 *  주문 스텝
**/
$oi_step1			= array(
				0	=> "주문접수",
				1	=> "결제완료",
				2	=> "배송준비중",
				3	=> "배송중",
				4	=> "배송완료",
				);
$oi_step2		= array(
				0	=> "",
				40	=> "취소신청",
				41	=> "취소접수",
				42	=> "취소진행",
				44	=> "취소완료",
				50	=> "결제시도",
				51	=> "PG확인요망",
				54	=> "결제실패",
				);

$op_step		= array(
				0	=> "주문접수",
				1	=> "결제완료",
				2	=> "배송준비중",
				3	=> "배송중",
				4	=> "배송완료",
				40	=> "취소신청",
				41	=> "취소접수",
				42	=> "취소진행",
				44	=> "취소완료",
				50	=> "결제시도",
				51	=> "PG확인요망",
				54	=> "결제실패",
				);

$o_step		= array(
				0	=> array(
						0	=> "주문접수",
						40	=> "취소신청",
						41	=> "취소접수",
						42	=> "취소진행",
						44	=> "취소완료",
						50	=> "결제시도",
						51	=> "PG확인요망",
						54	=> "결제실패",
						),
				1	=> array(
						0	=> "결제완료",
						40	=> "환불신청",
						41	=> "환불접수",
						44	=> "환불완료",
						#44	=> "취소완료",
						),
				2	=> array(
						0	=> "배송준비중",
						40	=> "환불신청",
						41	=> "환불접수",
						44	=> "환불완료",
						#44	=> "취소완료",
						),
				3	=> array(
						0	=> "배송중",
						40	=> "반품신청",
						41	=> "반품접수",
						42	=> "환불접수",
						#44	=> "환불완료",
						44	=> "반품완료",
						),
				4	=> array(
						0	=> "배송완료",
						40	=> "반품신청",
						41	=> "반품접수",
						42	=> "환불접수",
						#44	=> "환불완료",
						44	=> "반품완료",
						),
				);


#주문취소 사유 array (2016.01.27 - 김재수) - 핫티에서 사용할수 있도록 변경 (2016.10.10 - 김재수)
$oc_code	= array(
			1	=>'단순변심',
			2	=>'취소 후 재주문',
			3	=>'기타',
			4	=>'단순변심',
			5	=>'제품불량',
			6	=>'오배송',
			7	=>'배송지연',
			8	=>'주문실수',
			9	=>'상품정보오류',
			10	=>'기타',
			11	=>'사이즈교환',
			12	=>'제품불량',
			13	=>'오배송',
			14	=>'기타'
);
#반품 사유 array(2016-10-14 : 김대엽)
$return_oc_code	= array(
			4	=> array(
						'name'			=> "단순변심",
						'detail_code'	=> ""
				),
			5	=> array(
						'name'			=> "제품불량",
						'detail_code'	=> array(
											//		1	=> "갑피불량",
											//		2	=> "인솔불량",
													3	=> "재봉불량",
													4	=> "오염",
											//		5	=> "스크레치",
											//		6	=> "접착불량",
													7	=> "로고불량",
											//		8	=> "뒤축불량",
													9	=> "TAG없음",
													10	=> "기타"
													)
				),
			6	=> array(
						'name'			=> "오배송",
						'detail_code'	=> array(
													1	=> "사이즈오배송",
													2	=> "제품과 박스상이",
													3	=> "다른제품 배송",
													4	=> "기타"
													)
				),
			7	=> array(
						'name'			=> "배송지연",
						'detail_code'	=> array(
													1	=> "출고처",
													2	=> "택배사",
													3	=> "기타"
													)
				),
			8	=> array(
						'name'			=> "주문실수",
						'detail_code'	=> ""
				),
			9	=> array(
						'name'			=> "상품정보오류",
						'detail_code'	=> ""
				),
			10	=> array(
						'name'			=> "기타",
						'detail_code'	=> ""
				)
);

#취소(환불) 사유 array(2016-10-14 : 김대엽)
$cancel_oc_code	= array(
			1	=> array(
						'name'			=> "단순변심",
						'detail_code'	=> ""
				),
			2	=> array(
						'name'			=> "취소 후 재주문",
						'detail_code'	=> ""
				),
			3	=> array(
						'name'			=> "기타",
						'detail_code'	=> ""
				)
);

#교환 사유 array(2016-10-14 : 김대엽)
$exchange_oc_code	= array(
			11	=> array(
						'name'			=> "사이즈교환",
						'detail_code'	=> ""
				),
			12	=> array(
						'name'			=> "제품불량",
						'detail_code'	=> array(
												//	1	=> "갑피불량",
												//	2	=> "인솔불량",
													3	=> "재봉불량",
													4	=> "오염",
												//	5	=> "스크레치",
												//	6	=> "접착불량",
													7	=> "로고불량",
												//	8	=> "뒤축불량",
													9	=> "TAG없음",
													10	=> "기타"
													)
				),
			13	=> array(
						'name'			=> "오배송",
						'detail_code'	=> array(
													1	=> "사이즈오배송",
													2	=> "제품과 박스상이",
													3	=> "다른제품 배송",
													4	=> "기타"
													)
				),
			14	=> array(
						'name'			=> "기타",
						'detail_code'	=> ""
				)
);

#택배비 추가(2016-10-14 - 김대엽)
$delivery_fee_type	= array(
		1	=> "동봉(5천원)",
		2	=> "선불+2500원 동봉",
		4	=> "신원부담",
		3	=> "계좌이체 (5천원) [ (주)신원]"
);
#주문 취소/반품/교환 사유 array (2016.10.05 - 김재수)
$oc_reason_code	= array(
			'refund'			=> array(
										1	=> array(
													'name'			=> "단순변심",
													'detail_code'	=> ""
											),
										2	=> array(
													'name'			=> "취소 후 재주문",
													'detail_code'	=> ""
											),
										15	=> array(
													'name'			=> "O2O배송 지연",
													'detail_code'	=> ""
											),
										3	=> array(
													'name'			=> "기타",
													'detail_code'	=> ""
											)
										),
			'regoods'		=> array(
										4	=> array(
													'name'			=> "단순변심",
													'detail_code'	=> ""
											),
										5	=> array(
													'name'			=> "제품불량",
													'detail_code'	=> array(
																	//			1	=> "갑피불량",
																	//			2	=> "인솔불량",
																				3	=> "재봉불량",
																				4	=> "오염",
																	//			5	=> "스크레치",
																	//			6	=> "접착불량",
																				7	=> "로고불량",
																	//			8	=> "뒤축불량",
																				9	=> "TAG없음",
																				10	=> "기타"
																				)
											),
										6	=> array(
													'name'			=> "오배송",
													'detail_code'	=> array(
																				1	=> "사이즈오배송",
																				2	=> "제품과 박스상이",
																				3	=> "다른제품 배송",
																				4	=> "기타"
																				)
											),
										7	=> array(
													'name'			=> "배송지연",
													'detail_code'	=> array(
																				1	=> "출고처",
																				2	=> "택배사",
																				3	=> "기타"
																				)
											),
										8	=> array(
													'name'			=> "주문실수",
													'detail_code'	=> ""
											),
										9	=> array(
													'name'			=> "상품정보오류",
													'detail_code'	=> ""
											),
										10	=> array(
													'name'			=> "기타",
													'detail_code'	=> ""
											)
										),
			'rechange'		=> array(
										11	=> array(
													'name'			=> "사이즈교환",
													'detail_code'	=> ""
											),
										12	=> array(
													'name'			=> "제품불량",
													'detail_code'	=> array(
																	//			1	=> "갑피불량",
																	//			2	=> "인솔불량",
																				3	=> "재봉불량",
																				4	=> "오염",
																	//			5	=> "스크레치",
																	//			6	=> "접착불량",
																				7	=> "로고불량",
																	//			8	=> "뒤축불량",
																				9	=> "TAG없음",
																				10	=> "기타"
																				)
											),
										13	=> array(
													'name'			=> "오배송",
													'detail_code'	=> array(
																				1	=> "사이즈오배송",
																				2	=> "제품과 박스상이",
																				3	=> "다른제품 배송",
																				4	=> "기타"
																				)
											),
										14	=> array(
													'name'			=> "기타",
													'detail_code'	=> ""
											)
										)
);

#환불계좌 은행 array (2016.01.27 - 김재수)
$oc_bankcode	= array(
					"BK39" => "경남은행",
					"BK34" => "광주은행",
					"BK04" => "국민은행",
					"BK03" => "기업은행",
					"BK11" => "농협",
					"BK31" => "대구은행",
					"BK32" => "부산은행",
					"BK45" => "새마을금고",
					"BK07" => "수협",
					"BK88" => "신한은행",
					"BK48" => "신협",                        
					"BK20" => "우리은행",
					"BK71" => "우체국",
					"BK35" => "제주은행",
					"BK81" => "KEB하나은행",
					"BK27" => "한국시티은행",
					"BK54" => "HSBC",
					"BK23" => "SC제일은행",
					"BK02" => "산업은행",
					"BK37" => "전북은행",
					"B209" => "동양증권",
					"B218" => "현대증권",
					"B230" => "미래에셋증권",
					"B243" => "한국투자증권",
					"B247" => "우리투자증권",
					"B262" => "하이투자증권",
					"B263" => "HMC투자증권",
					"B266" => "SK증권",
					"B267" => "대신증권",
					"B270" => "하나대투증권",
					"B278" => "신한금융투자",
					"B279" => "동부증권",
					"B280" => "유진투자증권",
					"B287" => "메리츠",
					"B291" => "신영증권",
					"B240" => "삼성증권",
					"B269" => "한화증권",
					"B238" => "대우증권"
);
/*
$oc_bankcode	= array(
					"001" => "한국은행",
                    "002" => "산업은행",
                    "003" => "기업은행",
                    "004" => "국민은행",
                    "005" => "외환은행",
                    "007" => "수협중앙회",
                    "008" => "수출입은행",
                    "011" => "농협중앙회",
                    "012" => "농협회원조합",
                    "020" => "우리은행",
                    "023" => "SC제일은행",
                    "027" => "한국씨티은행",
                    "031" => "대구은행",
                    "032" => "부산은행",
                    "034" => "광주은행",
                    "035" => "제주은행",
                    "037" => "전북은행",
                    "039" => "경남은행",
                    "045" => "새마을금고연합회",
                    "048" => "신협중앙회",
                    "050" => "상호저축은행",
                    "052" => "모건스탠리은행",
                    "054" => "HSBC은행",
                    "055" => "도이치은행",
                    "056" => "에이비엔암로은행",
                    "057" => "제이피모간체이스은행",
                    "058" => "미즈호코퍼레이트은행",
                    "059" => "미쓰비시도쿄UFJ은행",
                    "060" => "BOA",
                    "071" => "정보통신부 우체국",
                    "076" => "신용보증기금",
                    "077" => "기술신용보증기금",
                    "081" => "하나은행",
                    "088" => "신한은행",
                    "093" => "한국주택금융공사",
                    "094" => "서울보증보험",
                    "095" => "경찰청",
                    "099" => "금융결제원",
                    "209" => "동양종합금융증권",
                    "218" => "현대증권",
                    "230" => "미래에셋증권",
                    "238" => "대우증권",
                    "240" => "삼성증권",
                    "243" => "한국투자증권",
                    "247" => "우리투자증권",
                    "261" => "교보증권",
                    "262" => "하이투자증권",
                    "263" => "에이치엠씨투자증권",
                    "264" => "키움증권",
                    "265" => "이트레이드증권",
                    "266" => "에스케이증권",
                    "267" => "대신증권",
                    "268" => "솔로몬투자증권",
                    "269" => "한화증권",
                    "270" => "하나대투증권",
                    "278" => "굿모닝신한증권",
                    "279" => "동부증권",
                    "280" => "유진투자증권",
                    "287" => "메리츠증권",
                    "289" => "엔에이치투자증권",
                    "290" => "부국증권"
);
*/
$prod_view_code	= array(
		"20" => "20개",
		"40" => "40개",
		"60" => "60개",
		"80" => "80개"
);

$prod_view_mcode	= array(
		"10" => "10개",
		"20" => "20개",
		"40" => "40개",
		"60" => "60개",
		"80" => "80개"
);

// 이메일 도메인 정보
$email_domain_arr	= array("naver.com","daum.net","gmail.com","nate.com","yahoo.co.kr","lycos.co.kr","empas.com","hotmail.com","msn.com","hanmir.com","chol.net","korea.com","netsgo.com","dreamwiz.com","hanafos.com","freechal.com","hitel.net");

// 그룹별 코드정보
$erp_group_code	= array(
								"A1"=>"DIAMOND", 
								"A2"=>"GOLD", 
								"B1"=>"SILVER", 
								"B2"=>"BRONZE", 
								"B3"=>"WELCOME", 
								"O1"=>"DORMANT"
							  );
//직업 정보
$erp_job_cd_arr	= array(
						"01"=>"주부",
						"02"=>"자영업",
						"03"=>"사무직",
						"04"=>"생산/기술직",
						"05"=>"판매직",
						"06"=>"보험업",
						"07"=>"은행/증권업",
						"08"=>"전문직",
						"09"=>"공무원",
						"10"=>"농축산업",
						"11"=>"학생",
						"12"=>"기타"
						);

# 물류 store_code 2016-11-28 유동혁
$mStoreCode = array( '006740' );

# insert 컬럼 수정 2015 11 16 유동혁
# orderinfotemp -> oprderinfo insert columns
$orInsertArr = array(
	'ordercode', 'tempkey', 'id', 'price', 'deli_price',
	'dc_price', 'reserve', 'paymethod', 'bank_date', 'pay_flag',
	'pay_auth_no', 'pay_admin_proc', 'pay_data', 'escrow_result', 'deli_gbn',
	'deli_date', 'sender_name', 'sender_email', 'sender_tel', 'receiver_name',
	'receiver_tel1', 'receiver_tel2', 'receiver_addr', 'order_msg', 'ip',
	'del_gbn', 'partner_id ', 'loc', 'bank_sender', 'mem_reserve',
	'receive_ok', 'tot_price_dc', 'enuri_price', 'receipt_yn', 'order_msg2',
	'deli_type', 'overseas_code', 'post5', 'is_mobile', 'deli_ori_price', 'deli_select',
    'sender_tel2', 'staff_order', 'point', 'pg_ordercode', 'cooper_order', 'staff_price', 'cooper_price', 'timesale_price'
);
# orderinfotemp -> oprderinfo insert columns
$prInsertArr = array(
	'vender', 'ordercode', 'tempkey', 'productcode', 'productname',
	'opt1_name', 'opt2_name', 'package_idx', 'assemble_idx', 'addcode',
	'quantity', 'price', 'reserve', 'date', 'selfcode',
	'productbisiness', 'deli_gbn', 'deli_com', 'deli_num', 'deli_date',
	'order_prmsg', 'assemble_info', 'order_check', 'option_price', 'option_quantity',
	'option_type', 'idx', 'coupon_price', 'deli_price', 'use_point', 'basketidx',
	'text_opt_subject', 'text_opt_content', 'option_price_text', 'deli_ori_price', 'deli_select',
    'rate', 'staff_order', 'self_goods_code', 'delivery_type', 'reservation_date', 'store_code',
	'ori_price', 'ori_option_price', 'use_epoint', 'pr_code', 'cooper_order', 'staff_price', 'cooper_price', 'timesale_price', 'timesale_detail', 'gps_x', 'gps_y'
);

// ===================================================================================================
// 관리자페이지 '회원관리'에서 엑셀다운로드시 사용
// by 최문성 ( 2016.04.22 )
// ===================================================================================================

$arr_admin_member_excel_info = array(
    array("아이디", "id"),
    array("이름", "name"),
    array("성별", "CASE WHEN gender = '1' THEN '남자' WHEN gender = '2' THEN '여자' ELSE '-' END"),
    array("생년월일", "birth"),
    array("양력음력구분", "CASE WHEN lunar = '1' THEN '양력' WHEN lunar = '0' THEN '음력' ELSE '-' END"),
    array("실명인증여부", "CASE WHEN auth_type = 'ipin' THEN '아이핀 인증' WHEN auth_type = 'mobile' THEN '휴대폰 인증' ELSE '-' END"),
    array("회원등급", "( select group_name from tblmembergroup where group_code = tblmember.group_code )"),
    array("회원등급코드", "group_code"),
    array("우편번호", "home_post"),
    array("주소(동/읍/면)", "split_part(home_addr, '↑=↑', 1)"),
    array("주소(번지 미만)", "split_part(home_addr, '↑=↑', 2)"),
    array("전화번호", "home_tel"),
    array("휴대폰번호", "mobile"),
    array("이메일", "email"),
    array("SMS 수신여부", "CASE WHEN news_yn = 'Y' THEN 'Y' WHEN news_yn = 'S' THEN 'Y' ELSE 'N' END"),
    array("e메일 수신여부", "CASE WHEN news_yn = 'Y' THEN 'Y' WHEN news_yn = 'M' THEN 'Y' ELSE 'N' END"),
    array("카카오톡 수신여부", "kko_yn"),
    array("통합포인트", "to_char(reserve, '999,999,999')"),
    array("E포인트", "to_char(act_point, '999,999,999')"),
    array("회원 가입일", "substring(date,0,5)||'-'||substring(date,5,2)||'-'||substring(date,7,2)"),
    array("가입시간", "substring(date,9,2)||':'||substring(date,11,2)||':'||substring(date,13,2)"),
    array("누적주문건수", "
    (
        select to_char(count(*), '999,999,999')
        from            
        (               
            SELECT a.ordercode, min(a.id) as id, min(a.price) as price, min(a.deli_price) as deli_price, min(a.dc_price) as dc_price, min(a.reserve) as reserve, min(a.paymethod) as paymethod, 
            min(a.sender_name) as sender_name, min(a.receiver_name) as receiver_name, min(a.oi_step1) as oi_step1, min(a.oi_step2) as oi_step2, min(a.redelivery_type) as redelivery_type, 
            min(productname) as productname, (select count(*) from tblorderproduct op where op.ordercode = a.ordercode) prod_cnt 
            FROM tblorderinfo a 
            join tblorderproduct b on a.ordercode = b.ordercode 
            join tblproductbrand v on b.vender = v.vender 
            WHERE 1=1 
            AND a.id = tblmember.id
            GROUP BY a.ordercode 
        ) z
    )
    "),
    array("실결제금액", "
    (
        select to_char(COALESCE(sum(z.price), 0), '999,999,999')
        from            
        (               
            SELECT a.ordercode, min(a.id) as id, min(a.price) as price, min(a.deli_price) as deli_price, min(a.dc_price) as dc_price, min(a.reserve) as reserve, min(a.paymethod) as paymethod, 
            min(a.sender_name) as sender_name, min(a.receiver_name) as receiver_name, min(a.oi_step1) as oi_step1, min(a.oi_step2) as oi_step2, min(a.redelivery_type) as redelivery_type, 
            min(productname) as productname, (select count(*) from tblorderproduct op where op.ordercode = a.ordercode) prod_cnt 
            FROM tblorderinfo a 
            join tblorderproduct b on a.ordercode = b.ordercode 
            join tblproductbrand v on b.vender = v.vender 
            WHERE 1=1 
            AND a.id = tblmember.id 
            AND a.oi_step1 > 0
            GROUP BY a.ordercode 
        ) z
    )
    "),
    array("총 실주문건수", "
    ( 
        select to_char(
        (
        select count(*)
                from            
                (               
                    SELECT a.ordercode, min(a.id) as id, min(a.price) as price, min(a.deli_price) as deli_price, min(a.dc_price) as dc_price, min(a.reserve) as reserve, min(a.paymethod) as paymethod, 
                    min(a.sender_name) as sender_name, min(a.receiver_name) as receiver_name, min(a.oi_step1) as oi_step1, min(a.oi_step2) as oi_step2, min(a.redelivery_type) as redelivery_type, 
                    min(productname) as productname, (select count(*) from tblorderproduct op where op.ordercode = a.ordercode) prod_cnt 
                    FROM tblorderinfo a 
                    join tblorderproduct b on a.ordercode = b.ordercode 
                    join tblproductbrand v on b.vender = v.vender 
                    WHERE 1=1 
                    AND a.id = tblmember.id
                    GROUP BY a.ordercode 
                ) z
        )
        - 
        (
            select count(*)
                from            
                (               
                    SELECT a.ordercode, min(a.id) as id, min(a.price) as price, min(a.deli_price) as deli_price, min(a.dc_price) as dc_price, min(a.reserve) as reserve, min(a.paymethod) as paymethod, 
                    min(a.sender_name) as sender_name, min(a.receiver_name) as receiver_name, min(a.oi_step1) as oi_step1, min(a.oi_step2) as oi_step2, min(a.redelivery_type) as redelivery_type, 
                    min(productname) as productname, (select count(*) from tblorderproduct op where op.ordercode = a.ordercode) prod_cnt 
                    FROM tblorderinfo a 
                    join tblorderproduct b on a.ordercode = b.ordercode 
                    join tblproductbrand v on b.vender = v.vender 
                    WHERE 1=1 
                    AND a.id = tblmember.id
                    AND ( (a.oi_step1 = 0 And a.oi_step2 = 44) OR (a.oi_step1 > 0 And b.op_step = 44 And ((coalesce(b.opt1_change, '') = '' And coalesce(b.opt2_change, '') = ''))) )
                    GROUP BY a.ordercode  
                ) z
        ), '999,999,999')
    )
    "),
    array("총 구매금액", "
    ( 
        select to_char(COALESCE(sum(z.price), 0), '999,999,999')
        from        
        (           
            SELECT a.ordercode, min(a.id) as id, min(a.price) as price, min(a.deli_price) as deli_price, min(a.dc_price) as dc_price, min(a.reserve) as reserve, min(a.paymethod) as paymethod, 
            min(a.sender_name) as sender_name, min(a.receiver_name) as receiver_name, min(a.oi_step1) as oi_step1, min(a.oi_step2) as oi_step2, min(a.redelivery_type) as redelivery_type, 
            min(productname) as productname, (select count(*) from tblorderproduct op where op.ordercode = a.ordercode) prod_cnt 
            FROM tblorderinfo a 
            join tblorderproduct b on a.ordercode = b.ordercode 
            join tblproductbrand v on b.vender = v.vender 
            WHERE 1=1   
            AND a.id = tblmember.id
            GROUP BY a.ordercode 
        ) z 
    )
    "),
    /*array("총 적립금", "
    (
        SELECT to_char(
            COALESCE(reserve + 
                COALESCE(
                ( 
                    SELECT sum(op.reserve) as save_point 
                    FROM tblorderproduct op LEFT JOIN tblorderinfo oi ON op.ordercode=oi.ordercode 
                    WHERE id = tblmember.id AND op.op_step in(1,2,3) AND op.reserve > 0 
                    GROUP BY oi.id
                ), 0) 
            , 0)
        , '999,999,999')
    )
    "),*/
    array("최종 주문일", "( select substring(max(deli_date),0,5)||'-'||substring(max(deli_date),5,2)||'-'||substring(max(deli_date),7,2) from tblorderinfo where id = tblmember.id )"),
    array("접속 IP", "ip"),
    array("총 방문횟수", "to_char(logincnt, '999,999,999')"),
    array("최종 접속일", "CASE WHEN logindate <> '' THEN substring(logindate,0,5)||'-'||substring(logindate,5,2)||'-'||substring(logindate,7,2) ELSE '' END"),
    /*array("사용가능 적립금", "to_char(reserve, '999,999,999')"),
    array("지역", "split_part(split_part(home_addr, '↑=↑', 1), ' ', 1)"),
    array("총 사용 적립금", "( select to_char(COALESCE(sum(reserve), 0), '999,999,999') from tblorderinfo where id = tblmember.id )"),*/
);

class DBConn {
	public $con_str="";
	public $connect="";
}
function pmysql_connect($hostname, $user_id, $password ) {
	$dbconn = new DBConn();
	$dbconn->con_str = "host=$hostname user=$user_id password=$password";
	$dbconn->connect = @pg_connect($dbconn->con_str." dbname=postgres");
	return $dbconn;
}
function pmysql_error() {
	return @pg_last_error();
}
function pmysql_errno() {
	//$code =@pg_result_error_field(pg_get_result(),PGSQL_DIAG_SQLSTATE);
	//return ($code=='23505')?1062:$code;
	$error = pg_last_error();
	if(strpos($error,'duplicate key value violates')>0) return 1062;
	if(strpos($error,'중복된 키 값이')>0) return 1062;
	if($error!="") return 1;
	return 0;
}
function pmysql_select_db( $dbname, $connect ) {
	$connect->connect = @pg_connect($connect->con_str." dbname=$dbname");
	return ($connect->connect!==FALSE);
}
function pmysql_query( $query, $connect=NULL ) {

	$injectionCode = "--";
	if(strstr($query, $injectionCode)){
		$query = str_replace($injectionCode, "－－", $query);
	}

	$time[] = microtime();
	if(is_null($connect))
		$connect = get_db_conn();
	$result = @pg_query($connect->connect,$query);
	if(($error = pg_last_error()) && (strpos($error,'duplicate key value violates') || strpos($error,'중복된 키 값이')>0)===FALSE) {
		$bt = debug_backtrace();
        error_log("\r\n".date("Y-m-d H:i:s ").realpath($_SERVER['DOCUMENT_ROOT'].$_SERVER['SCRIPT_NAME']).$error.$bt[0]['line'],3,"/tmp/error_log_sinwon.log");
		error_log($query."\r\n",3,"/tmp/error_log_sinwon.log");
	}
	log_sql("pmysql_query", $query, $time);
	return $result;
}

function pmysql_fetch($res){
	if (!is_resource($res)) $res = pmysql_query($res);
	return  @pmysql_fetch_array($res);
}

function pmysql_fetch_object($result) {
	if($result===FALSE) return FALSE;
	return @pg_fetch_object($result);
}
function pmysql_fetch_row($result) {
	if($result===FALSE) return FALSE;
	return @pg_fetch_row($result);
}
function pmysql_fetch_array($result) {
	if($result===FALSE) return FALSE;
	return @pg_fetch_array($result);
}
function pmysql_free_result($result) {
	if($result===FALSE) return FALSE;
	return @pg_free_result($result);
}
function pmysql_num_rows($result) {
	if($result===FALSE) return FALSE;
	return @pg_num_rows($result);
}
function pmysql_close($connect) {
	return @pg_close($connect->connect);
}
function pmysql_result($result,$row,$field=0) {
	$arr = pmysql_fetch_array($result);
	if(is_array($arr))
		return $arr[$row][0];
	else
		return FALSE;
}
function pmysql_escape_string($data) {
	return pg_escape_string($data);
}

function dump_str($var) {
	ob_start();
	var_dump($var);
	$dump = ob_get_contents();
	ob_end_clean();
	return $dump;
}

/**
 * Determine if a given string begins with a given value.
 *
 * @param  string  $haystack
 * @param  string  $needle
 * @return bool
 */
function starts_with($haystack, $needle)
{
	return strpos($haystack, $needle) === 0;
}

/**
 * Determine if a given string ends with a given value.
 *
 * @param  string  $haystack
 * @param  string  $needle
 * @return bool
 */
function ends_with($haystack, $needle)
{
	return $needle == substr($haystack, strlen($haystack) - strlen($needle));
}

/**
 * Determine if a given string contains a given sub-string.
 *
 * @param  string        $haystack
 * @param  string|array  $needle
 * @return bool
 */
function str_contains($haystack, $needle)
{
	foreach ((array) $needle as $n)
	{
		if (strpos($haystack, $n) !== false) return true;
	}

	return false;
}

/**
 * Cap a string with a single instance of the given string.
 *
 * @param  string  $value
 * @param  string  $cap
 * @return string
 */
function str_finish($value, $cap)
{
	return rtrim($value, $cap).$cap;
}

function render($path) {
	$GLOBALS['__path'] = $path;
	extract($GLOBALS);
	$__contents = file_get_contents($__path);
	ob_start();

	// We'll include the view contents for parsing within a catcher
	// so we can avoid any WSOD errors. If an exception occurs we
	// will throw it out to the exception handler.
	try
	{
		eval('?>'.$__contents);
	}

	// If we caught an exception, we'll silently flush the output
	// buffer so that no partially rendered views get thrown out
	// to the client and confuse the user with junk.
	catch (Exception $e)
	{
		ob_get_clean(); throw $e;
	}

	$content = ob_get_clean();
	return($content);
}


############################## 암호화 파일 체크 ##################################
function setFileZendCheck() {
	global $Dir;
	if($f=@file($Dir.AuthkeyDir.".shopaccess")) {
		$authkey=trim($f[0]);

		$ZendError=true;
		if(strlen($authkey)==64) {
			$fp=@fopen ($Dir.str_replace(RootPath,"",ltrim($_SERVER['SCRIPT_NAME'],'/')),"r");
			if($fp) {
				$temp=@fgets($fp,5);

				if($temp=="Zend") { //$temp => Zend일 경우 암호화
					$ZendError=false;
				}
			}
			@fclose($fp);
		} else {
			$ZendError=false;
		}
		if($ZendError) {
			error_msg("쇼핑몰 파일타입이 올바르지 않습니다.","-1");
		}
	} else {
		error_msg("도메인 인증파일이 존재하지 않습니다.","-1");
	}
}


############################## 사용 가능 도메인 체크 ##################################
function getUseShopDomain() {
	global $Dir;
	$retval="";	//X:파일이 존재하지 않음, O:인증성공, A:잘못된 인증키, D:도메인 불일치, E:사용기간 만료, F:허용IP가 아님, R:통신불가
	if($f=@file($Dir.AuthkeyDir.".shopaccess")) {
		$authkey=trim($f[0]);
		if(ord($authkey)) {
			$host=_IncomuUrl;
			$path="/incomushop/getuseshopdomain.html";
			$query="&authkey={$authkey}&domain=".(ord($_SERVER['HTTP_HOST'])?$_SERVER['HTTP_HOST']:getUriDomain($_SERVER['REQUEST_URI']))."&serverip=".$_SERVER['SERVER_ADDR'];

			$resdata=SendSocketPost($host,$path,$query);
			if(strpos($resdata,"[OK]")===0) {
				$retval=$resdata[4];
			} else {
				$retval="R";
			}
		} else {
			$retval="A";
		}
	} else {
		$retval="X";
	}

	if(!strstr("XOADEFR",$retval)) {
		$retval="R";
	}

	return $retval;
}

############################## 주문시도건 복구(쿠폰,적립금,재고량) 처리 ##################################
function temporder_restore() {
	GLOBAL $Dir;
	if(file_exists($Dir.DataDir."retemp")) {	//data 폴더의 쓰레기 파일들 일정시간 지나면 삭제
		$filecreatetime=(time()-filemtime($Dir.DataDir."retemp"))/60;
		if($filecreatetime>10) {
			$sdate=date("YmdHi",strtotime('-1 hour -5 min'));
			$edate=date("YmdHi",strtotime('-7 day'));

			$randkey="PROC".rand(1000000, 9999999);
			$sql = "UPDATE tblorderinfotemp SET ";
			$sql.= "pay_data	= '{$randkey}||'||pay_data ";
			$sql.= "WHERE (ordercode>='{$edate}' AND ordercode<='{$sdate}') ";
			$sql.= "AND (del_gbn='' OR del_gbn is NULL) AND pay_data NOT LIKE 'PROC%' ";
			pmysql_query($sql,get_db_conn());

			$sql = "SELECT * FROM tblorderinfotemp WHERE (ordercode>='{$edate}' AND ordercode<='{$sdate}') ";
			$sql.= "AND (del_gbn='' OR del_gbn is NULL) AND pay_data LIKE '{$randkey}%' ";
			$result=@pmysql_query($sql,get_db_conn());
			while($data=@pmysql_fetch_object($result)) {
				$ordercode=$data->ordercode;
				@pmysql_query("UPDATE tblorderinfotemp SET del_gbn='R', pay_data=REPLACE(pay_data,'{$randkey}||','') WHERE ordercode='{$ordercode}'",get_db_conn());
			}
			@pmysql_free_result($result);
			@unlink($Dir.DataDir."retemp");
		}
	} else {
		file_put_contents($Dir.DataDir."retemp","OK");
	}
}


############################## 도메인만 가져오기 ##################################
function getUriDomain($url) {
	if(ord($url)) {
		$temp = str_replace("http://", "", $url);
		$result = @explode("/", $temp);
		return $result[0];
	}
}

function getCookieDomain() {
	$http_host = $_SERVER['HTTP_HOST'];
	if ($_SERVER['SERVER_PORT'] != 80) {
		$http_host= str_replace(':' . $_SERVER['SERVER_PORT'], '', $http_host);
	}
	$domain_explode=explode(".",$http_host);
	if($domain_explode[0]=="www")
	{
		@array_shift($domain_explode);
		return ".".@implode(".",$domain_explode);
	}
	else
		return $http_host;
}

###########################################################
function decrypt_authkey($str) {
	return decrypt_md5($str,"*ghkddnjsrl*");
}

function getAdminMainNotice() {
	$host=_SellerUrl;						#차후 우리회사 독립형 판매 사이트 도메인
	$path="/incomushop/getshopadminmainnotice.html";	#페이지두 낭중에 추가하자~
	$query="";

	$resdata=SendSocketPost($host,$path,$query);
	return $resdata;

	//$resdata	=> OK|배열데이터
}

function getSmshost($path) {
	$host="sms.ajashop.co.kr";
	$path="";
	return array($host,$path);
}
###########################################################

function decrypt_md5($hex_buf,$key="") {
        if(ord($key)==0) $key=enckey;
        $len = strlen($hex_buf);
        $buf = '';
        $ret_buf = '';
        $buf = pack("H*",$hex_buf);
        $key1 = pack("H*", md5($key));
        while($buf) {
                $m = substr($buf, 0, 16);
                $buf = substr($buf, 16);

                $c = "";
                $len_m = strlen($m);
                $len_key1 = strlen($key1);
                for($i=0;$i<16;$i++) {
                        $m1 = ($len_m>$i) ? $m{$i} : 0;
                        $m2 = ($len_key1>$i) ? $key1{$i} : 0;
                        if($len_m>$i)
                        $c .= $m1^$m2;
                }
                $ret_buf .= $m = $c;
                $key1 = pack("H*",md5($key.$key1.$m));
        }
        $ret_buf=rtrim($ret_buf,'0');
        return($ret_buf);
}

function encrypt_md5($buf,$key="") {
        if(ord($key)==0) $key=enckey;
        $key1 = pack("H*",md5($key));
        while($buf) {
                $m = substr($buf, 0, 16);
                $buf = substr($buf, 16);

                $c = "";
                $len_m = strlen($m);
                for($i=0;$i < 16 ;$i++) {
                        if($len_m>$i)
                        $c .= $m{$i}^$key1{$i};
                }
                $ret_buf .= $c;
                $key1 = pack("H*",md5($key.$key1.$m));
        }
        $len = strlen($ret_buf);
        for($i=0; $i<$len; $i++)
                $hex_data .= sprintf("%02x", ord(substr($ret_buf, $i, 1)));
        return($hex_data);
}

function get_db_conn() {
	
	global $DB_CONN, $Dir;
	if (!$DB_CONN) {
		
		$f=@file($Dir.DataDir."config.php") or error_msg("config.php파일이 없습니다.<br>DB설정을 먼저 하십시요",$Dir."install.php");
		for($i=1;$i<=4;$i++) $f[$i]=trim($f[$i]);

		$DB_CONN = @pmysql_connect($f[1],$f[2],$f[3]) or error_msg("DB 접속 에러가 발생하였습니다.");
		$status = @pmysql_select_db($f[4],$DB_CONN) or error_msg("DB Select 에러가 발생하였습니다.");
		

		if (!$status) {
		   error_msg("DB Select 에러가 발생하였습니다.");
			
		}
	}
	
	return $DB_CONN;
}

########### SQL CACHE ##################
function WriteCache(&$var, $file) {
	$filename = DirPath.DataDir."cache/sql/".$file;
	$success = false;
	file_put_contents($filename,serialize($var));
	$success=true;
	return $success;
}

function ReadCache(&$var, $file, $delmin=240) {
	$filename = DirPath.DataDir."cache/sql/".$file;
	$success = false;

	if(file_exists($filename)) {
		$filecreatetime=(time()-filemtime($filename))/60;
		if($filecreatetime<=$delmin) {
			$szdata = file_get_contents($filename);
			$var=unserialize($szdata);
			$success=true;
		} else {
			DeleteCache($file);
		}
	}
	return $success;
}

function DeleteCache($file) {
	$filename = DirPath.DataDir."cache/sql/".$file;
	if(file_exists($filename)) {
		@unlink($filename);
	}
}

function get_db_cache($SQL, &$var, $file, $delmin=240, $refresh=false) {
	global $DB_CONN;
	$var=array();
	$ret=true;

	if($refresh || !ReadCache($var, $file, $delmin)) {
		if (!$DB_CONN) $DB_CONN = get_db_conn();
		$res=pmysql_query($SQL, $DB_CONN);
		if($err=pmysql_error())
			trigger_error($err, E_USER_ERROR);
		while($rec=pmysql_fetch_object($res)){
			$var[]=$rec;
		}
		pmysql_free_result($res);
		$ret = WriteCache($var, $file);
	}
	return $ret;
}

function delete_cache_file($type, $str="") {
	if($type=="main") {
		if(is_dir($_SERVER['DOCUMENT_ROOT']."/".RootPath.DataDir."cache/main")) {
			$match=$_SERVER['DOCUMENT_ROOT']."/".RootPath.DataDir."cache/main/*_main.php_";
		}
	} elseif($type=="product") {
		if(is_dir($_SERVER['DOCUMENT_ROOT']."/".RootPath.DataDir."cache/product")) {
			$match=$_SERVER['DOCUMENT_ROOT']."/".RootPath.DataDir."cache/product/*_product*";
			if(strlen($str)) $match.=$str."*";
		}
	} elseif($type=="productb") {
		if(is_dir($_SERVER['DOCUMENT_ROOT']."/".RootPath.DataDir."cache/product")) {
			$match=$_SERVER['DOCUMENT_ROOT']."/".RootPath.DataDir."cache/product/*_productb*";
			if(ord($str)) $match.=$str."*";
		}
	}
	if(ord($match)) {
		$match=str_replace("..","",$match);
		$match=str_replace(" ","",$match);
		$matches=glob($match);
		if(is_array($matches)) {
			foreach($matches as $cachefile) {
				@unlink($cachefile);
			}
		}
	}
}

function getSellerdomain() {
	$resdata="";
	if($f=@file(DirPath.AuthkeyDir.".seller")) {
		$sellerid=trim($f[0]);
		if(ord($sellerid)) {
			$resdata_decrypt=decrypt_authkey($sellerid);
			$resdata = @explode("|", $resdata_decrypt);
		}
	}

	return $resdata[1];
}

function alert_go($message=null,$location=null,$frame=null) {

	if(is_null($location)) $location = $_SERVER['PHP_SELF'];

	echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8">';
	echo '<script> ';
	if($message) echo "alert('{$message}');";
	if($location==='c'){
		echo "window.close();";
		echo "opener.location.href=opener.location";
	}elseif(is_int($location))
		echo "history.go($location);";
	elseif($frame)
		echo "{$frame}.location.href='{$location}';";
	else
		echo "location.href='{$location}';";

	echo '</script>';

	exit;
}

class UserSession {
	var $id					= "";
	var $authkey			= "";

	var $shopdata			= "";

	var $allowanyip			= false;
	var $ipaddresses		= Array();
	var $roleidx			= 0;
	var $allowalltasks		= false;
	var $taskcodes			= Array();

	function __construct($id, $authkey) {
		$sql = "SELECT * FROM tblsecurityadmin WHERE id = '{$id}' ";
		if(_DEMOSHOP!="OK" && $id!="guest") {
			$sql.= "AND authkey='{$authkey}' ";
		}

		$result = pmysql_query($sql,get_db_conn());
		$row=pmysql_fetch_object($result);
		pmysql_free_result($result);
		if ($row) {
			$this->id = $id;
			$this->authkey = $authkey;

			$sql = "SELECT * FROM tblshopinfo ";
			$result=pmysql_query($sql,get_db_conn());
			if(!$row2=pmysql_fetch_object($result)) {
				error_msg("쇼핑몰 정보 등록이 안되었습니다.<br>쇼핑몰 설정을 먼저 하십시요",DirPath."install.php");
			}

			$this->shopdata = $row2;

			$this->shopdata->escrow_id="";
			$this->shopdata->trans_id="";
			$this->shopdata->virtual_id="";
			$this->shopdata->card_id="";
			$this->shopdata->mobile_id="";
			if($f=@file(DirPath.AuthkeyDir."pg")) {
				for($i=0;$i<count($f);$i++) {
					$f[$i]=trim($f[$i]);
					if (strpos($f[$i],"escrow_id:::")===0) $this->shopdata->escrow_id=decrypt_authkey(substr($f[$i],12));
					elseif (strpos($f[$i],"trans_id:::")===0) $this->shopdata->trans_id=decrypt_authkey(substr($f[$i],11));
					elseif (strpos($f[$i],"virtual_id:::")===0) $this->shopdata->virtual_id=decrypt_authkey(substr($f[$i],13));
					elseif (strpos($f[$i],"card_id:::")===0) $this->shopdata->card_id=decrypt_authkey(substr($f[$i],10));
					elseif (strpos($f[$i],"mobile_id:::")===0) $this->shopdata->mobile_id=decrypt_authkey(substr($f[$i],12));
				}
			}


			$ETCTYPE=array();
			if (ord($row2->etctype)) {
				$etctemp = explode("",$row2->etctype);
				$etccnt = count($etctemp);
				for ($etci=0;$etci<$etccnt;$etci++) {
					$etctemp2 = explode("=",$etctemp[$etci]);
					$ETCTYPE[$etctemp2[0]]=$etctemp2[1];
				}
			}
			$this->shopdata->ETCTYPE=$ETCTYPE;


			//접근 가능한 IP인지 확인
			$sql = "SELECT ipidx FROM tblsecurityadminip WHERE id='{$this->id}' ORDER BY ipidx ASC LIMIT 1";
			$result = pmysql_query($sql,get_db_conn());
			if ($row = pmysql_fetch_object($result)) {
				if ($row->ipidx == 0)
					$this->allowanyip = true;
			}
			pmysql_free_result($result);

			if (!$this->allowanyip) {
				$sql = "SELECT b.ipaddress as ipaddress1 FROM tblsecurityadminip a,tblsecurityiplist b ";
				$sql.= "WHERE a.id = '{$this->id}' AND a.ipidx = b.idx AND b.disabled = 0";
				$result = pmysql_query($sql,get_db_conn());
				$j = 1;
				while($row = pmysql_fetch_object($result)) {
					$this->ipaddresses[$j] = $row->ipaddress1;
					$j++;
				}
				pmysql_free_result($result);
			}

			$sql = "SELECT a.idx as roleidx FROM tblsecurityrole a, tblsecurityadminrole b ";
			$sql.= "WHERE a.idx = b.roleidx AND a.disabled = 0 ";
			$sql.= "AND b.id = '{$this->id}' ";
			$result = pmysql_query($sql,get_db_conn());
			$row = pmysql_fetch_object($result);
			if ($row->roleidx)
				$this->roleidx = (int)$row->roleidx;

			pmysql_free_result($result);

			if ($this->roleidx > 0) {
				$sql = "SELECT taskidx FROM tblsecurityroletask ";
				$sql.= "WHERE roleidx = {$this->roleidx} ORDER BY taskidx ASC LIMIT 1";
				$result = pmysql_query($sql,get_db_conn());
				if ($row = pmysql_fetch_object($result)) {
					if ($row->taskidx == 0)
						$this->allowalltasks = true;
				}
				pmysql_free_result($result);
			}

			if (!$this->allowalltasks && $this->roleidx > 0) {
				$sql = "SELECT b.taskcode, b.taskgroupidx, c.taskgroupcode, c.taskgroupname ";
				$sql.= "FROM tblsecurityroletask a, tblsecuritytask b, tblsecuritytaskgroup c ";
				$sql.= "WHERE a.roleidx = {$this->roleidx} AND a.taskidx = b.idx ";
				$sql.= "AND b.taskgroupidx = c.idx ";
				$sql.= "ORDER BY b.taskgroupidx, b.taskcode ASC ";
				$result = pmysql_query($sql,get_db_conn());
				while($row = pmysql_fetch_object($result)) {
					$this->taskcodes[$row->taskcode] = true;
				}
				pmysql_free_result($result);
			}
		} else {
			echo "<script>\n";
			echo "	alert(\"정상적인 경로로 다시 접속하시기 바랍니다.\");\n";
			echo "	if (opener) {\n";
			echo "		opener.parent.location.href=\"logout.php\";\n";
			echo "		window.close();\n";
			echo "	} else {\n";
			echo "		parent.location.href=\"logout.php\";\n";
			echo "	}\n";
			echo "</script>\n";
			exit;
		}
	}

	function isallowedip($ip) {
		if ($this->allowanyip)
			return true;
		else
			return (boolean)array_search($ip, $this->ipaddresses);
	}

	function isallowedtask($taskcode) {
		if ($this->allowalltasks)
			return true;
		if ($this->allowalltasks) {
			$taskcodess = substr($taskcode,0,2);
			return true;
		} else {
			return (boolean)$this->taskcodes[$taskcode];
		}
	}

	function getallowalltasks() {
		return (boolean)$this->allowalltasks;
	}

	function getshopdata() {
		return (object)$this->shopdata;
	}
}

class ShopInfo {
	var $id				= "";
	var $name			= "";
	var $email			= "";
	var $authkey		= "";
	var $nickname		= "";

	var $memlevel		= "";
	var $wsmember		= "";

	var $counterid		= "";
	var $counterauthkey	= "";

	var $shopurl		= "";
	var $shopurl2		= "";
	var $refurl			= "";
	var $authidkey		= "";
	var $memid			= "";
	var $memgroup		= "";
	var $memname		= "";
	var $mememail		= "";
	var $memreserve		= 0;
	var $boardadmin		= "";	//array serialize data

	var $tempkey		= "";	//장바구니 인증키
	var $tempkeySelect		= "";	//장바구니 인증키2
	var $gifttempkey	= "";	//사은품 관련 키
	var $oldtempkey		= "";	//결제창 띄울경우 기존 장바구니 인증키
	var $okpayment		= "";	//결제시 새로고침 방지 쿠키
	var $staff_type		= "";		# 스태프 유무
	var $staff_yn		= "";		# 임직원 유무
	var $cooper_yn		= "";		# 협력업체 유무
	var $staffcardno		= "";		# ERP 임직원 STAFFCARDNO
	var $checksns		= "";		# sns 구분
	var $checksnslogin		= "";		# sns 로그인 구분
	var $checksnsaccess		= "";		# sns 접속타입 (PC, MOBILE) 구분
	var $checksnschurl		= "";		# sns 모바일에서 로그인시 이동 URL

	var $searchkey		= "";	//검색인증 구분키

	// 저장 정보 추가 (2015.10.29 - 김재수)
	var $referrerurl		= "";	// 이전URL 정보
	var $affiliatetype		= "";	// 제휴업체 구분
	var $affiliateno		= "";	// 제휴업체 번호
	var $affiliatename	= "";	// 제휴업체 이름
	var $affiliateimg		= "";	// 제휴업체 이미지

	function __construct($_sinfo) {
		if ($_sinfo) {
			$savedata=unserialize(decrypt_md5($_sinfo));

			$this->id			= $savedata["id"];
			$this->name			= $savedata["name"];
			$this->email		= $savedata["email"];
			$this->authkey		= $savedata["authkey"];
			$this->nickname		= $savedata["nickname"];

			$this->shopurl		= $savedata["shopurl"];
			$this->shopurl2		= $savedata["shopurl2"];
			$this->refurl		= $savedata["refurl"];
			$this->authidkey	= $savedata["authidkey"];
			$this->memid		= $savedata["memid"];
			$this->memgroup		= $savedata["memgroup"];
			$this->memname		= $savedata["memname"];
			$this->memreserve	= $savedata["memreserve"];
			$this->mememail		= $savedata["mememail"];
			$this->boardadmin	= $savedata["boardadmin"];
			$this->gifttempkey	= $savedata["gifttempkey"];
			$this->oldtempkey	= $savedata["oldtempkey"];
			$this->okpayment	= $savedata["okpayment"];
			$this->memlevel		= $savedata["memlevel"];
			$this->wsmember		= $savedata["wsmember"];
			$this->staff_type	= $savedata["staff_type"];
			$this->staff_yn	= $savedata["staff_yn"];
			$this->cooper_yn	= $savedata["cooper_yn"];
			$this->staffcardno	= $savedata["staffcardno"];
			$this->checksns	= $savedata["checksns"];
			$this->checksnslogin	= $savedata["checksnslogin"];
			$this->checksnsaccess	= $savedata["checksnsaccess"];
			$this->checksnschurl	= $savedata["checksnschurl"];

			// 저장 정보 추가 (2015.10.29 - 김재수)
			$this->referrerurl		= $savedata["referrerurl"];
			$this->affiliatetype	= $savedata["affiliatetype"];
			$this->affiliateno		= $savedata["affiliateno"];
			$this->affiliatename	= $savedata["affiliatename"];
			$this->affiliateimg	= $savedata["affiliateimg"];
		}
	}

	function Save() {
		$savedata["id"]			= $this->getId();
		$savedata["name"]		= $this->getName();
		$savedata["email"]		= $this->getEmail();
		$savedata["authkey"]	= $this->getAuthkey();
		$savedata["nickname"]	= $this->getNickName();
		$savedata["shopurl"]	= $this->getShopurl();
		$savedata["shopurl2"]	= $this->getShopurl2();
		$savedata["refurl"]		= $this->getRefurl();
		$savedata["authidkey"]	= $this->getAuthidkey();
		$savedata["memid"]		= $this->getMemid();
		$savedata["memgroup"]	= $this->getMemgroup();
		$savedata["memname"]	= $this->getMemname();
		$savedata["memreserve"]	= $this->getMemreserve();
		$savedata["mememail"]	= $this->getMememail();
		$savedata["boardadmin"]	= $this->getBoardadmin();
		$savedata["gifttempkey"]= $this->getGifttempkey();
		$savedata["oldtempkey"]	= $this->getOldtempkey();
		$savedata["okpayment"]	= $this->getOkpayment();

		$savedata["memlevel"]	= $this->getMemlevel();
		$savedata["wsmember"]	= $this->getWsmember();
		$savedata["staff_type"]	= $this->getStaffType();
		$savedata["staff_yn"]	= $this->getStaffYn();
		$savedata["cooper_yn"]	= $this->getCooperYn();
		$savedata["staffcardno"]	= $this->getStaffCardNo();
		$savedata["checksns"]	= $this->getCheckSns();
		$savedata["checksnslogin"]	= $this->getCheckSnsLogin();
		$savedata["checksnsaccess"]	= $this->getCheckSnsAccess();
		$savedata["checksnschurl"]	= $this->getCheckSnsChurl();

		// 저장 정보 추가 (2015.10.29 - 김재수)
		$savedata["referrerurl"]		= $this->getReferrerUrl();
		$savedata["affiliatetype"]		= $this->getAffiliateType();
		$savedata["affiliateno"]		= $this->getAffiliateNo();
		$savedata["affiliatename"]	= $this->getAffiliateName();
		$savedata["affiliateimg"]		= $this->getAffiliateImg();

		$_sinfo = encrypt_md5(serialize($savedata));
		setcookie("_sinfo", $_sinfo, 0, "/".RootPath, getCookieDomain());
	}

	function SetMemNULL() {
		$this->setAuthidkey("");
		$this->setMemid("");
		$this->setMemgroup("");
		$this->setMemname("");
		$this->setMemreserve("");
		$this->setMememail("");
		$this->setMemlevel("");
		$this->setStaffType("");
		$this->setStaffYn("");
		$this->setCooperYn("");
		$this->setStaffCardNo("");
		$this->setCheckSns("");
		$this->setCheckSnsLogin("");
		$this->setCheckSnsAccess("");
		$this->setCheckSnsChurl("");

		// 저장 정보 추가 (2015.11.02 - 김재수)
		$this->setReferrerUrl("");
		$this->setAffiliateType("1");
		$this->setAffiliateNo("");
		$this->setAffiliateName("");
		$this->setAffiliateImg("");
	}

	function setId($id)					{$this->id = $id;}
	function setName($name)				{$this->name = $name;}
	function setEmail($email)			{$this->email = $email;}
	function setNickName($nickname)		{$this->nickname = $nickname;}
	function setAuthkey($authkey)		{$this->authkey = $authkey;}
	function setShopurl($shopurl)		{$this->shopurl = $shopurl;}
	function setShopurl2($shopurl2)		{$this->shopurl2 = $shopurl2;}
	function setRefurl($refurl)			{$this->refurl = $refurl;}
	function setAuthidkey($authidkey)	{$this->authidkey = $authidkey;}
	function setMemid($memid)			{$this->memid = $memid;}
	function setMemgroup($memgroup)		{$this->memgroup = $memgroup;}
	function setMemname($memname)		{$this->memname = $memname;}
	function setMemreserve($memreserve)	{$this->memreserve = $memreserve;}
	function setMememail($mememail)		{$this->mememail = $mememail;}
	function setBoardadmin($boardadmin)	{$this->boardadmin = $boardadmin;}
	function setGifttempkey($gifttempkey){$this->gifttempkey = $gifttempkey;}
	function setOldtempkey($oldtempkey) {$this->oldtempkey = $oldtempkey;}
	function setOkpayment($okpayment)	{$this->okpayment = $okpayment;}
	function setMemlevel($memlevel)		{$this->memlevel = $memlevel;}
	function setWsmember($wsmember)		{$this->wsmember = $wsmember;}
	function setStaffType($staff_type)		{$this->staff_type = $staff_type;}
	function setStaffYn($staff_yn)		{$this->staff_yn = $staff_yn;}
	function setCooperYn($cooper_yn)		{$this->cooper_yn = $cooper_yn;}
	function setStaffCardNo($staffcardno)		{$this->staffcardno = $staffcardno;}
	function setCheckSns($checksns)		{$this->checksns = $checksns;}
	function setCheckSnsLogin($checksnslogin)		{$this->checksnslogin = $checksnslogin;}
	function setCheckSnsAccess($checksnsaccess)		{$this->checksnsaccess = $checksnsaccess;}
	function setCheckSnsChurl($checksnschurl)		{$this->checksnschurl = $checksnschurl;}
	

	// 저장 정보 추가 (2015.10.29 - 김재수)
	function setReferrerUrl($referrerurl)		{$this->referrerurl = $referrerurl;}
	function setAffiliateType($affiliatetype)		{$this->affiliatetype = $affiliatetype;}
	function setAffiliateNo($affiliateno)		{$this->affiliateno = $affiliateno;}
	function setAffiliateName($affiliatename)		{$this->affiliatename = $affiliatename;}
	function setAffiliateImg($affiliateimg)		{$this->affiliateimg = $affiliateimg;}


	function getId()			{return $this->id;}
	function getName()			{return $this->name;}
	function getEmail()			{return $this->email;}
	function getAuthkey()		{return $this->authkey;}
	function getNickName()		{return $this->nickname;}
	function getShopurl()		{return $this->shopurl;}
	function getShopurl2()		{return $this->shopurl2;}
	function getRefurl()		{return $this->refurl;}
	function getAuthidkey()		{return $this->authidkey;}
	function getMemid()			{return $this->memid;}
	function getMemgroup()		{return $this->memgroup;}
	function getMemname()		{return $this->memname;}
	function getMemreserve()	{return $this->memreserve;}
	function getMememail()		{return $this->mememail;}
	function getBoardadmin()	{return $this->boardadmin;}
	function getGifttempkey()	{return $this->gifttempkey;}
	function getOldtempkey()	{return $this->oldtempkey;}
	function getOkpayment()		{return $this->okpayment;}
	function getMemlevel()		{return $this->memlevel;}
	function getWsmember()		{return $this->wsmember;}
	function getStaffType()		{return $this->staff_type;}
	function getStaffYn()		{return $this->staff_yn;}
	function getCooperYn()		{return $this->cooper_yn;}
	function getStaffCardNo()		{return $this->staffcardno;}
	function getCheckSns()		{return $this->checksns;}
	function getCheckSnsLogin()		{return $this->checksnslogin;}
	function getCheckSnsAccess()		{return $this->checksnsaccess;}
	function getCheckSnsChurl()		{return $this->checksnschurl;}

	// 저장 정보 추가 (2015.10.29 - 김재수)
	function getReferrerUrl()		{return $this->referrerurl;}
	function getAffiliateType()		{return $this->affiliatetype;}
	function getAffiliateNo()		{return $this->affiliateno;}
	function getAffiliateName()		{return $this->affiliatename;}
	function getAffiliateImg()		{return $this->affiliateimg;}

	//쇼핑몰 방문자수 확인
	function getShopCount() {
		$sql = "SELECT * FROM tblshopcount ";
		$result=pmysql_query($sql,get_db_conn());
		if($row=pmysql_fetch_object($result)) {
			$count=(int)$row->count;
		} else {
			$count=0;
		}
		pmysql_free_result($result);
		return $count;
	}

	function getTempkey() {
		$basketauthkey = session_id();
		$this->tempkey = $basketauthkey; 
		return $this->tempkey;
/*
		if(strlen($this->tempkey)!=32) {
			$basketauthkey=$_COOKIE["basketauthkey"];
			$this->tempkey=$basketauthkey;
			if($basketauthkey=="") {
				$this->setTempkey(0);
			}
		}
		return $this->tempkey;
*/
	}

	function setTempkey($time,$isNULL="") {
		if($isNULL) {
			$basketauthkey="";
			setcookie("basketauthkey", $basketauthkey, 0, "/".RootPath, getCookieDomain());
		} else {
			$basketauthkey = md5(uniqid(rand(),1));
			if($time>0 && $time!=0) {
				setcookie("basketauthkey", $basketauthkey, time()+3600*$time, "/".RootPath, getCookieDomain());
			} else {
				setcookie("basketauthkey", $basketauthkey, 0, "/".RootPath, getCookieDomain());
			}
		}
		$this->tempkey=$basketauthkey;
	}

	function getTempkeySelectItem() {
		if(strlen($this->tempkeySelect)!=32) {
			$basketauthkey=$_COOKIE["basketauthkeySelectItem"];
			$this->tempkeySelect=$basketauthkey;
		}
		return $this->tempkeySelect;
	}
	function setTempkeySelectItem($time,$isNULL="") {
		if($isNULL) {
			$basketauthkey="";
			setcookie("basketauthkeySelectItem", $basketauthkey, 0, "/".RootPath, getCookieDomain());
		} else {
			$basketauthkey = md5(uniqid(rand(),1));
			if($time>0 && $time!=0) {
				setcookie("basketauthkeySelectItem", $basketauthkey, time()+3600*$time, "/".RootPath, getCookieDomain());
			} else {
				setcookie("basketauthkeySelectItem", $basketauthkey, 0, "/".RootPath, getCookieDomain());
			}
		}
		$this->tempkeySelect=$basketauthkey;
	}

	function getPgdata() {
		global $_data;
		$_data->escrow_id="";
		$_data->trans_id="";
		$_data->virtual_id="";
		$_data->card_id="";
		$_data->mobile_id="";
		if($f=@file(DirPath.AuthkeyDir."pg")) {
			for($i=0;$i<count($f);$i++) {
				$f[$i]=trim($f[$i]);
				if (strpos($f[$i],"escrow_id:::")===0) $_data->escrow_id=decrypt_authkey(substr($f[$i],12));
				elseif (strpos($f[$i],"trans_id:::")===0) $_data->trans_id=decrypt_authkey(substr($f[$i],11));
				elseif (strpos($f[$i],"virtual_id:::")===0) $_data->virtual_id=decrypt_authkey(substr($f[$i],13));
				elseif (strpos($f[$i],"card_id:::")===0) $_data->card_id=decrypt_authkey(substr($f[$i],10));
				elseif (strpos($f[$i],"mobile_id:::")===0) $_data->mobile_id=decrypt_authkey(substr($f[$i],12));
			}
		}
	}

	function adminLogin() {
		global $shopurl,$mem_id,$mem_pw,$ssllogintype,$sessid,$history;
		$connect_ip = $_SERVER['REMOTE_ADDR'];

		if (ord($mem_id) && (ord($mem_pw) || ($ssllogintype=="ssl"))) {
			$flag	= false;
			$disabled = 0;
			$currenttime = time();
            // mysql 의 password 방식 알고리즘을 php로 구현함. 2015-10-15 jhjeong
            $shadata = "*".strtoupper(SHA1(unhex(SHA1($mem_pw))));
			$sql = "SELECT id, passwd, expirydate, adminname, adminemail, disabled FROM tblsecurityadmin ";
			$sql.= "WHERE id='{$mem_id}' ";
			if($ssllogintype=="ssl") $sql.= "AND authkey='{$sessid}' ";
			//else $sql.= "AND passwd=md5('{$mem_pw}') ";
            else $sql.= "AND passwd = '{$shadata}' ";
			$result = pmysql_query($sql,get_db_conn());
			if ($row = pmysql_fetch_object($result)) {
				$id = $row->id;
				$name = $row->adminname;
				$email = $row->adminemail;
				$passwd = $row->passwd;
				$expirydate = (int)$row->expirydate;
				$disabled = (int)$row->disabled;

				if ($expirydate == 0) {
					$flag = true;
				} else {
					if ($expirydate > time()) $flag = true;
					else $flag = false;
				}
				if ($disabled == 1) $flag = false;
				if($ssllogintype!="ssl") {
					if ($flag) {
						$flag = false;
						//if (md5($mem_pw) == $passwd) $flag = true;
                        if($shadata == $passwd) $flag = true;
					}
				}
				if ($flag) {
					if(_DEMOSHOP!="OK") {
						# 도메인 인증키 불필요해짐 2015 11 26 유동혁
						/*
						$useshop=getUseShopDomain();
						if($useshop=="X") {
							error_msg("도메인 인증파일이 존재하지 않습니다.","http://"._IncomuUrl);
						} elseif($useshop=="A") {
							error_msg("도메인 인증키가 잘못되었습니다.","http://"._IncomuUrl);
						} elseif($useshop=="D") {
							error_msg("도메인 인증키가 잘못되었습니다.","http://"._IncomuUrl);
						} elseif($useshop=="E") {
							error_msg("도메인 인증키가 잘못되었습니다.","http://"._IncomuUrl);
						} elseif($useshop=="F") {
							error_msg("쇼핑몰을 운영할 수 있는 IP정보가 잘못되었습니다.","http://"._IncomuUrl);
						}
						*/
						if($useshop=="F") {
							error_msg("쇼핑몰을 운영할 수 있는 IP정보가 잘못되었습니다.","http://"._IncomuUrl);
						}
					}
					$authkey = md5(uniqid(""));
					$this->setShopurl($shopurl);
					$this->setId($id);
					$this->setName($name);
					$this->setEmail($email);
					$this->setAuthkey($authkey);
					$this->Save();

					$sql = "UPDATE tblsecurityadmin SET authkey='{$authkey}', lastlogintime='".time()."' ";
					$sql.= "WHERE id='{$id}'";
					$update = pmysql_query($sql,get_db_conn());
					$log_content = "로그인 : $id";
					ShopManagerLog($id,$connect_ip,$log_content);
				} else {
					error_msg("로그인 정보가 올바르지 않습니다.<br>다시 확인하시기 바랍니다...",$history);
				}
			} else {
				error_msg("로그인 정보가 올바르지 않습니다.<br>다시 확인하시기 바랍니다..",$history);
			}
			pmysql_free_result($result);
		} else {
			$id = $this->getId();
			$authkey = $this->getAuthkey();
			$sql = "SELECT * FROM tblsecurityadmin WHERE id='{$id}' AND authkey='{$authkey}' ";
			$result = pmysql_query($sql,get_db_conn());
			$rows = pmysql_num_rows($result);
			if ($rows <= 0) {
				$this->setId("");
				$this->setName("");
				$this->setEmail("");
				$this->setAuthkey("");
				$this->Save();
				error_msg("정상적인 경로로 다시 접속하시기 바랍니다.",$history);
			}
		}
	}

	function getSellerid() {
		$resdata="";
		if($f=@file(DirPath.AuthkeyDir.".seller")) {
			$sellerid=trim($f[0]);
			if(ord($sellerid)) {
				$resdata_decrypt=decrypt_authkey($sellerid);
				$resdata = @explode("|", $resdata_decrypt);
			}
		}
		return $resdata[0];
	}
}


class ShopData extends ShopInfo {
	var $shopdata		= "";

	function __construct($_ShopInfo) {
		global $ref;
		//$this=$_ShopInfo;

		$sql = "SELECT * FROM tblshopinfo ";
		$result=pmysql_query($sql,get_db_conn());
		if($row=pmysql_fetch_object($result)) {
			pmysql_free_result($result);
			$this->shopdata=$row;

			$this->shopdata->onetop_type=$row->top_type;
			if ($row->frame_type=="Y") $this->shopdata->top_type="top";

			$this->shopdata->deli_basefee=$this->shopdata->deli_basefee+0;
			if($row->deli_setperiod<2) $this->shopdata->deli_setperiod=1;
			if($row->deli_basefee==-9) {
				$this->shopdata->deli_basefee=0;
				$this->shopdata->deli_after="Y";
			}
			if ($row->deli_miniprice==0) $this->shopdata->deli_miniprice=1000000000;
			else $this->shopdata->deli_miniprice = $row->deli_miniprice;
			if (ord($row->deli_type)==0) $this->shopdata->deli_type=0;
			if (ord($row->reserve_join)==0) $this->shopdata->reserve_join=0;

			$this->shopdata->primg_minisize2 = 250;

			$this->shopdata->countpath="<img src=\"".DirPath.FrontDir."counter.php?ref=".urlencode($ref)."\" width=0 height=0>";

			//$this->shopdata->countpath="<iframe src=\"".DirPath.FrontDir."counter.php?ref=".urlencode($ref)."\" width=150 height=200></iframe>";
			######## 쇼핑몰 레이아웃 관련(SHOPWIDTH, MAINUSED, MOUSEKEY, SHOPBGTYPE, BGCOLOR, BACKGROUND #########
			$layoutdata=array();
			if(ord($row->layoutdata)) {
				$laytemp=explode("",$row->layoutdata);
				$laycnt=count($laytemp);
				for ($layi=0;$layi<$laycnt;$layi++) {
					$laytemp2=explode("=",$laytemp[$layi]);
					if(isset($laytemp2[1])) {
						$layoutdata[$laytemp2[0]]=$laytemp2[1];
					} else {
						$layoutdata[$laytemp2[0]]="";
					}
				}
			}
			$this->shopdata->layoutdata=$layoutdata;
			######################################################################################################


			$ETCTYPE=array();
			if (ord($row->etctype)) {
				$etctemp = explode("",$row->etctype);
				$etccnt = count($etctemp);
				for ($etci=0;$etci<$etccnt;$etci++) {
					$etctemp2 = explode("=",$etctemp[$etci]);
					if(isset($etctemp2[1])) {
						$ETCTYPE[$etctemp2[0]]=$etctemp2[1];
					} else {
						$ETCTYPE[$etctemp2[0]]="";
					}
				}
			}
			$this->shopdata->ETCTYPE=$ETCTYPE;
			$this->shopdata->count=$this->getShopCount();
			$this->shopdata->visitor=$this->shopdata->count;

			$this->shopdata->primg_minisize2 = 250;
			$this->shopdata->ssl_pagelist = array();

			if($row->ssl_type=="Y" && ord($row->ssl_page)) {
				$temp=explode("|",$row->ssl_page);
				$cnt=count($temp);
				for ($i=0;$i<$cnt;$i++) {
					if (strpos($temp[$i],"ADMIN=")===0) $this->shopdata->ssl_pagelist["ADMIN"]=substr($temp[$i],6);	#관리자 로그인페이지
					elseif (strpos($temp[$i],"PLOGN=")===0) $this->shopdata->ssl_pagelist["PLOGN"]=substr($temp[$i],6);	#파트너 로그인페이지
					elseif (strpos($temp[$i],"VLOGN=")===0) $this->shopdata->ssl_pagelist["VLOGN"]=substr($temp[$i],6);	#입점업체 로그인페이지
					elseif (strpos($temp[$i],"LOGIN=")===0) $this->shopdata->ssl_pagelist["LOGIN"]=substr($temp[$i],6);	#회원 로그인페이지
					elseif (strpos($temp[$i],"MJOIN=")===0) $this->shopdata->ssl_pagelist["MJOIN"]=substr($temp[$i],6);	#회원가입
					elseif (strpos($temp[$i],"MEDIT=")===0) $this->shopdata->ssl_pagelist["MEDIT"]=substr($temp[$i],6);	#회원정보수정
					elseif (strpos($temp[$i],"MLOST=")===0) $this->shopdata->ssl_pagelist["MLOST"]=substr($temp[$i],6);	#ID/PW찾기
					elseif (strpos($temp[$i],"ORDER=")===0) $this->shopdata->ssl_pagelist["ORDER"]=substr($temp[$i],6);	#주문페이지
					elseif (strpos($temp[$i],"ADULT=")===0) $this->shopdata->ssl_pagelist["ADULT"]=substr($temp[$i],6);	#성인인증
				}
			}
			// 인기 검색어 
			if(ord($row->search_info)) {
				$temp=explode("=",$row->search_info);
				$cnt = count($temp);
			}

			$this->shopdata->search_info=array();
			if($cnt>0) {
				$this->shopdata->search_info["autosearch"]="";
				$this->shopdata->search_info["bestkeyword"]="";
				$this->shopdata->search_info["bestauto"]="";
				$this->shopdata->search_info["keyword"]="";
				for ($i=0;$i<$cnt;$i++) {
					if (strpos($temp[$i],"AUTOSEARCH=")===0) $this->shopdata->search_info["autosearch"]=substr($temp[$i],11);	#자동완성기능 사용여부(Y/N)
					elseif (strpos($temp[$i],"BESTKEYWORD=")===0) $this->shopdata->search_info["bestkeyword"]=substr($temp[$i],12);	#인기검색어기능 사용여부(Y/N)
					elseif (strpos($temp[$i],"BESTAUTO=")===0) $this->shopdata->search_info["bestauto"]=substr($temp[$i],9);	#인기검색어 자동추출인지 수동등록인지(Y/N)
					elseif (strpos($temp[$i],"KEYWORD=")===0) $this->shopdata->search_info["keyword"]=substr($temp[$i],8);	#인기검색어 수동등록 리스트
				}
			}
			if(ord($this->shopdata->search_info["autosearch"])==0) $this->shopdata->search_info["autosearch"]="N";
			if(ord($this->shopdata->search_info["bestkeyword"])==0) $this->shopdata->search_info["bestkeyword"]="Y";
			if(ord($this->shopdata->search_info["bestauto"])==0) $this->shopdata->search_info["bestauto"]="Y";
			
			// 기본 검색어
// 			echo "search_default_keyword : ".$row->search_default_keyword;
			$this->shopdata->search_info["defaultkeyword"] = $row->search_default_keyword;

			if(ord($this->shopdata->ETCTYPE["SELFCODEVIEW"])) {
				$this->shopdata->ETCTYPE["SELFCODELOCAT"]="";
				$this->shopdata->ETCTYPE["SELFCODEBR"]="";

				if($this->shopdata->ETCTYPE["SELFCODEVIEW"]=="Y" || $this->shopdata->ETCTYPE["SELFCODEVIEW"]=="Z") {
					$this->shopdata->ETCTYPE["SELFCODELOCAT"]="Y";
				} elseif($this->shopdata->ETCTYPE["SELFCODEVIEW"]=="N" || $this->shopdata->ETCTYPE["SELFCODEVIEW"]=="M") {
					$this->shopdata->ETCTYPE["SELFCODELOCAT"]="N";
				}

				if($this->shopdata->ETCTYPE["SELFCODEVIEW"]=="Y" || $this->shopdata->ETCTYPE["SELFCODEVIEW"]=="N") {
					$this->shopdata->ETCTYPE["SELFCODEBR"]="<br>";
				}

				if(ord($this->shopdata->ETCTYPE["SELFCODEF"])) {
					$this->shopdata->ETCTYPE["SELFCODEF"] = str_replace(" ", "&nbsp;", @htmlspecialchars($this->shopdata->ETCTYPE["SELFCODEF"]));
				}

				if(ord($this->shopdata->ETCTYPE["SELFCODEB"])) {
					$this->shopdata->ETCTYPE["SELFCODEB"] = str_replace(" ", "&nbsp;", @htmlspecialchars($this->shopdata->ETCTYPE["SELFCODEB"]));
				}
			}
		} else {
			pmysql_free_result($result);

			//쇼핑몰 정보 등록이 안되었으니까 error 페이지 함수 호출
			error_msg("쇼핑몰 정보 등록이 안되었습니다.<br>쇼핑몰 설정을 먼저 하십시요",DirPath."install.php");
		}
	}
}



class PartnerInfo {
	var $joindate		= "";
	var $partner_id		= "";
	var $partner_authkey= "";

	function __construct($_pinfo) {
		if ($_pinfo) {
			$savedata=unserialize(decrypt_md5($_pinfo));

			$this->joindate			= $savedata["joindate"];
			$this->partner_id		= $savedata["partner_id"];
			$this->partner_authkey	= $savedata["partner_authkey"];
		}
	}

	function Save() {
		$savedata["joindate"]		= $this->getJoindate();
		$savedata["partner_id"]		= $this->getPartnerid();
		$savedata["partner_authkey"]= $this->getpartnerauthkey();

		$_pinfo = encrypt_md5(serialize($savedata));
		setcookie("_pinfo", $_pinfo, 0, "/".RootPath.PartnerDir, getCookieDomain());
	}

	function setJoindate($joindate)		{$this->joindate = $joindate;}
	function setPartnerid($partner_id)	{$this->partner_id = $partner_id;}
	function setPartnerauthkey($partner_authkey)		{$this->partner_authkey = $partner_authkey;}


	function getJoindate()		{return $this->joindate;}
	function getPartnerid()			{return $this->partner_id;}
	function getpartnerauthkey()		{return $this->partner_authkey;}
}

function DemoShopCheck($errormsg, $url="") {
	global $MenuCode;
	if(_DEMOSHOP=="OK") {
		if($_SERVER['REMOTE_ADDR']!=_ALLOWIP) {
			$errormsg=str_replace("<br>","\\n",$errormsg);
			$errormsg=str_replace("\"","\\\"",$errormsg);
			if($url=="window.close()") {
				alert_go($errormsg,'c');
			} elseif($url=="history.go(-1)" || $url=="history.back()") {
				alert_go($errormsg,-1);
			} elseif(ord($url)) {
				alert_go($errormsg,$url);
			} else {
				include("AccessDeny.inc.html");
				exit;
			}
		}
	}
}
// 2016-05-19 tblreserve => tblpoint 유동혁
function SetReserve($id, $reserve, $content, $orderdata="") {
	if(ord($reserve) && $reserve!=0) {
		if($reserve>0) $yn="Y";
		elseif($reserve<0) $yn="N";
		$date=date("YmdHis");
		$sql = "INSERT INTO tblreserve(
		id	,
		reserve	,
		reserve_yn,
		content	,
		orderdata,
		date) VALUES (
		'{$id}',
		{$reserve},
		'{$yn}',
		'{$content}',
		'{$orderdata}',
		'{$date}')";
		if(pmysql_query($sql,get_db_conn())) {
			$sql = "UPDATE tblmember SET ";
			$sql.= "reserve=reserve+$reserve ";
			$sql.= "WHERE id = '{$id}' ";
			if(pmysql_query($sql,get_db_conn())) {
				return true;
			}
		}
	}
}
// 등급별 reserve
function getReserveConversion($reserve,$reservetype,$sellprice,$reservshow) {
	global $_ShopInfo, $_data;

	$_data->ETCTYPE["MEM"]=(isset($_data->ETCTYPE["MEM"])?$_data->ETCTYPE["MEM"]:"");

	if($_data->ETCTYPE["MEM"]=="Y" && ord($_ShopInfo->getMemid())==0 && $reservshow=="Y") {
		return 0;
	} else {
		$sellprice = (int)$sellprice;
		if($reservetype=="Y") {
			//등급별 추가 적립금 % 적립금만 추가된다
			if( $_ShopInfo->getMemgroup() != '' ){
				$sql = "SELECT group_addreserve FROM tblmembergroup WHERE group_code = '".$_ShopInfo->getMemgroup()."' ";
				$result = pmysql_query( $sql, get_db_conn() );
				$row = pmysql_fetch_object( $result );
				pmysql_free_result( $result );
				$addreserve = (float) str_replace( '%', '', $row->group_addreserve );
				$reserve = $reserve + $addreserve;
			}



			if($sellprice>0 && $reserve>0) {
				$pr_reserve=$sellprice*$reserve*0.01;
				if($_data->point_cut) $ban_per=$_data->point_cut*10;
				else $ban_per="1";

				if($_data->point_updown=="D") $band_preserve= floor($pr_reserve/$ban_per)*$ban_per;
				else if($_data->point_updown=="U") $band_preserve= ceil($pr_reserve/$ban_per)*$ban_per;
				else $band_preserve= round($pr_reserve/$ban_per)*$ban_per;
			
				//return @round($sellprice*$reserve*0.01);
				return $band_preserve;
			} else {
				return 0;
			}
		} else {
			return $reserve;
		}
	}
}

function viewselfcode($productname,$selfcode) {
	GLOBAL $_data,$selfcodefont_start,$selfcodefont_end;

	if(ord($selfcode)) {
		$selfcode = $selfcodefont_start.$_data->ETCTYPE["SELFCODEF"].$selfcode.$_data->ETCTYPE["SELFCODEB"].$selfcodefont_end;

		if($_data->ETCTYPE["SELFCODELOCAT"]=="Y") {
			return $selfcode.$_data->ETCTYPE["SELFCODEBR"].$productname;
		} elseif($_data->ETCTYPE["SELFCODELOCAT"]=="N") {
			return $productname.$_data->ETCTYPE["SELFCODEBR"].$selfcode;
		} else {
			return $productname;
		}
	} else {
		return $productname;
	}
}

function viewproductname($productname,$icon,$selfcode,$addcode=""){
	global $Dir,$iconyes,$_ShopInfo;

	$productname = viewselfcode($productname,$selfcode);

	$oriicon=$icon;
	$icoi = strpos(" ".$icon,"ICON=");
	if($icoi>0){
		if(!is_array($iconyes)) {
			getUsericon();
		}
		$icon = substr($icon,strpos($icon,"ICON="));
		$icon = substr($icon,5,strpos($icon,"")-5);
		$num=strlen($icon) ;
		$iconname="";
		for($i=0;$i<$num;$i+=2){
			$temp=$icon[$i].$icon[$i+1];
			if(preg_match("/^(U)[1-6]$/",$temp) && $iconyes[$temp]=="Y") {
				$iconname.=" <img src=\"".$Dir.DataDir."shopimages/etc/icon{$temp}.gif\" align=absmiddle border=0 style='width:30px;height:11px;float:left'>";
			} elseif(strlen($temp) && !preg_match("/^(U)[1-6]$/",$temp)) {
				$iconname.=" <img src=\"{$Dir}images/common/icon{$temp}.gif\" align=absmiddle border=0 style='width:30px;height:11px;float:left'>";
			}
		}
		$iconname.="<br>";
		//return $productname.(strlen($addcode)?" - ".$addcode:"")."</a>".$iconname;
		//return $productname.(strlen($addcode)?" - ".$addcode:"").$iconname;
		return $iconname.$productname.(ord($addcode)?" - ".$addcode:"");
	} else {
		//return $productname.(ord($addcode)?" - ".$addcode:"")."</a>";
		return $productname.(ord($addcode)?" - ".$addcode:"");
	}
}


function viewicon($icon){
	global $Dir,$iconyes,$_ShopInfo;
	$oriicon=$icon;
	$icoi = strpos(" ".$icon,"ICON=");
	if($icoi>0){
		if(!is_array($iconyes)) {
			getUsericon();
		}
		$icon = substr($icon,strpos($icon,"ICON="));
		$icon = substr($icon,5,strpos($icon,"")-5);
		$num=strlen($icon) ;
		$iconname="";
		for($i=0;$i<$num;$i+=2){
			$temp=$icon[$i].$icon[$i+1];
			if(preg_match("/^(U)[1-6]$/",$temp) && $iconyes[$temp]=="Y") {
				$iconname.=" <img src=\"".$Dir.DataDir."shopimages/etc/icon{$temp}.gif\" align=absmiddle border=0>";
			} elseif(strlen($temp) && !preg_match("/^(U)[1-6]$/",$temp)) {
				$iconname.=" <img src=\"{$Dir}images/common/icon{$temp}.gif\" align=absmiddle border=0>";
			}
		}
		//$iconname.="<br>";
		//return $productname.(strlen($addcode)?" - ".$addcode:"")."</a>".$iconname;
		//return $productname.(strlen($addcode)?" - ".$addcode:"").$iconname;
		return $iconname;
	}
}

function getUsericon() {
	global $iconyes, $Dir;
	if(!is_array($iconyes)) {
		$filepath=$Dir.DataDir."shopimages/etc/";
		$icon = array("U1","U2","U3","U4","U5","U6");
		$iconnum = count($icon);
		for($i=0;$i<$iconnum;$i++){
			if(file_exists($filepath."icon{$icon[$i]}.gif")) {
				$iconyes[$icon[$i]]="Y";
			} else {
				$iconyes[$icon[$i]]="N";
			}
		}
	}
}

function soldout($temp=0){
	global $_ShopInfo, $Dir;
	if(file_exists($Dir.DataDir."shopimages/etc/soldout.gif")){
		return "<img src=\"".$Dir.DataDir."shopimages/etc/soldout.gif\" align=absmiddle border=0>";
	} else {
		if($temp==1) {
			return "<b><font color=red style=\"font-size:9pt\">품절</font></b>";
		} else {
			return " <font color=red style=\"font-size:9pt\">(품절)</font>";
		}
	}
}

function dickerview($etctype,$price=0,$ectype=0) {
	global $_ShopInfo, $Dir;
	global $_data;

	$_data->ETCTYPE["MEM"]=(isset($_data->ETCTYPE["MEM"])?$_data->ETCTYPE["MEM"]:"");
	$_data->ETCTYPE["MEMIMG"]=(isset($_data->ETCTYPE["MEMIMG"])?$_data->ETCTYPE["MEMIMG"]:"");
	$_data->ETCTYPE["SELL"]=(isset($_data->ETCTYPE["SELL"])?$_data->ETCTYPE["SELL"]:"");

	if($_data->ETCTYPE["MEM"]=="Y" && ord($_ShopInfo->getMemid())==0) {
		if ($_data->ETCTYPE["MEMIMG"]=="Y" && file_exists($Dir.DataDir."shopimages/etc/priceicon.gif")) {
			return "<img src=\"".$Dir.DataDir."shopimages/etc/priceicon.gif\" border=0 align=absmiddle>";
		} elseif ($_data->ETCTYPE["MEMIMG"]=="N") {
			return "<img width=1 height=0 border=0>";
		} elseif (ord($_data->ETCTYPE["MEMIMG"]) && $_data->ETCTYPE["MEMIMG"]!="Y") {
			return "<img src=\"{$Dir}images/common/priceicon{$_data->ETCTYPE['MEMIMG']}.gif\" border=0 align=absmiddle>";
		} else {
			return "<font color=red>회원공개</font>";
		}
	}
	$dicker_pos=strpos($etctype,"DICKER=");
	if ($dicker_pos===false) {
		if(ord($_data->ETCTYPE["SELL"])==0 && $ectype==0) {
			return "<img src=\"{$Dir}images/common/won_icon.gif\" border=0 align=absmiddle> ".$price;
		} else {
			if($ectype==1) return;
			$type=explode(",",$_data->ETCTYPE["SELL"]);
			if(ord($type[0])) $price="<b>{$price}</b>";
			if(ord($type[1])) $price="<font color={$type[1]}>{$price}</font>";
			return "<img src=\"{$Dir}images/common/won_icon.gif\" border=0 align=absmiddle> ".$price;
		}
	} else {
		$f_dicker=substr($etctype,$dicker_pos+7);
		$dicker_pos2=strpos($f_dicker,"");
		return str_replace("$","&#036;",substr($f_dicker,0,$dicker_pos2));
	}
}

function dickerview_tem001($etctype,$price=0,$ectype=0) {
	global $_ShopInfo, $Dir;
	global $_data;

	$_data->ETCTYPE["MEM"]=(isset($_data->ETCTYPE["MEM"])?$_data->ETCTYPE["MEM"]:"");
	$_data->ETCTYPE["MEMIMG"]=(isset($_data->ETCTYPE["MEMIMG"])?$_data->ETCTYPE["MEMIMG"]:"");
	$_data->ETCTYPE["SELL"]=(isset($_data->ETCTYPE["SELL"])?$_data->ETCTYPE["SELL"]:"");

	if($_data->ETCTYPE["MEM"]=="Y" && ord($_ShopInfo->getMemid())==0) {
		if ($_data->ETCTYPE["MEMIMG"]=="Y" && file_exists($Dir.DataDir."shopimages/etc/priceicon.gif")) {
			//return "<img src=\"".$Dir.DataDir."shopimages/etc/priceicon.gif\" border=0 align=absmiddle>";
		} elseif ($_data->ETCTYPE["MEMIMG"]=="N") {
			return "<img width=1 height=0 border=0>";
		} elseif (ord($_data->ETCTYPE["MEMIMG"]) && $_data->ETCTYPE["MEMIMG"]!="Y") {
			return "<img src=\"{$Dir}images/common/priceicon{$_data->ETCTYPE['MEMIMG']}.gif\" border=0 align=absmiddle>";
		} else {
			return "<font color=red>회원공개</font>";
		}
	}
	$dicker_pos=strpos($etctype,"DICKER=");
	if ($dicker_pos===false) {
		if(ord($_data->ETCTYPE["SELL"])==0 && $ectype==0) {
			return $price;
		} else {
			if($ectype==1) return;
			$type=explode(",",$_data->ETCTYPE["SELL"]);
			if(ord($type[0])) $price="<b>{$price}</b>";
			if(ord($type[1])) $price="<font color={$type[1]}>{$price}</font>";
			return $price;
		}
	} else {
		$f_dicker=substr($etctype,$dicker_pos+7);
		$dicker_pos2=strpos($f_dicker,"");
		return str_replace("$","&#036;",substr($f_dicker,0,$dicker_pos2));
	}
}

//에스크로 설정정보 읽어옴
function GetEscrowType($escrow_info) {
	$val = array();
	$list = explode("|",$escrow_info);
	for ($i=0;$i<count($list); $i++) {
		$data = explode("=",$list[$i]);
		$val[$data[0]] = $data[1];
	}
	return $val;
}

//tblshopdetail 테이블의 etcfield값 get
function getEtcfield($etcfield,$key) {
	$val="";
	$arrayetc=explode("=",$etcfield);
	$cnt=count($arrayetc);
	for($i=0;$i<$cnt;$i++) {
		if (substr($arrayetc[$i],0,strlen($key)+1)==($key."=")) {
			$val=substr($arrayetc[$i],strlen($key)+1);
			break;
		}
	}
	return $val;
}

function setEtcfield($etcfield,$key,$val) {
	$etcvalue="";
	$isfind=false;
	$arrayetc=explode("=",$etcfield);
	$cnt=count($arrayetc);
	for($i=0;$i<$cnt;$i++) {
		if (substr($arrayetc[$i],0,strlen($key)+1)==($key."=")) {
			if(strlen($val)) {
				$etcvalue.=$key."={$val}=";
			}
			$isfind=true;
		} else {
			if(strlen($arrayetc[$i])) $etcvalue.=$arrayetc[$i]."=";
		}
	}
	if(!$isfind && ord($val)) {
		$etcvalue=$key."={$val}=";
	}
	$sql = "UPDATE tblshopinfo SET etcfield='{$etcvalue}' ";
	pmysql_query($sql,get_db_conn());
	DeleteCache("tblshopinfo.cache");

	return $etcvalue;
}

// 관리자 로그
function ShopManagerLog($id,$ip,$content,$date="") {
	if (strlen($date)!=14) {
		$date=date("YmdHis");
	}
	$sql = "INSERT INTO tblsecurityadminlog(
	id	,
	date	,
	ip	,
	content) VALUES (
	'{$id}',
	'{$date}',
	'{$ip}',
	'{$content}')";
	pmysql_query($sql,get_db_conn());
}

function getEncKey($str) {
	$t_total = strlen($str);
	$sum_len = ord($str{$t_total - 3}) + ord($str{$t_total - 2});
	$id1 = $sum_len % 11;
	$id2 = $sum_len % 6;
	$key = $sum_len.$id1.$id2;

	return $key;
}

function getVenderUsed() {
	global $_ShopInfo;
	$resdata=array();
	//echo encrypt_md5("OK|*|*|".$_ShopInfo->getShopurl(),"*ghkddnjsrl*");
	if($f=@file(DirPath.AuthkeyDir."vender")) {
		for($i=0;$i<count($f);$i++) {
			$f[$i]=trim($f[$i]);
			if (strpos($f[$i],"VENDERPKG:::")===0) {
				$tempdata=explode("|",decrypt_authkey(substr($f[$i],12)));

				if ($tempdata[3] == $_ShopInfo->getShopurl()) {
					$resdata["OK"]=$tempdata[0];
					$resdata["COUNT"]=$tempdata[1];
					$resdata["DATE"]=$tempdata[2];
					$resdata["DOMAIN"]=$tempdata[3];
					if(strpos($_ShopInfo->getShopurl(),"www.")!==0) {
						if(strpos($resdata["DOMAIN"],"www.")===0) {
							$resdata["DOMAIN"]=substr($resdata["DOMAIN"],4);
						}
					}
					return $resdata;
					break;
				}
			}
		}
	}
}

function getSmscount($id,$authkey) {
	list($host,$path)=getSmshost($path);
	$path=$path."/process/getsmscount.html";
	$enckey=getEncKey($id);
	$query="&shopid={$id}&authkey={$authkey}&enckey=".$enckey;

	$resdata=SendSocketPost($host,$path,$query);
	return $resdata;
}

function getSmspaylist() {
	list($host,$path)=getSmshost($path);
	$path=$path."/process/getpaylist.html";

	$resdata=SendSocketPost($host,$path,"");
	return $resdata;
}

function getSmssendlist($id,$authkey,$query) {
	list($host,$path)=getSmshost($path);
	$path=$path."/process/getsendlist.html";
	$enckey=getEncKey($id);
	$query="&shopid={$id}&authkey={$authkey}&enckey={$enckey}&".$query;

	$resdata=SendSocketPost($host,$path,$query);
	return $resdata;
}

function getSmsfillinfo($id,$authkey,$query) {
	list($host,$path)=getSmshost($path);
	$path=$path."/process/getfillinfo.html";
	$enckey=getEncKey($id);
	$query="&shopid={$id}&authkey={$authkey}&enckey={$enckey}&".$query;

	$resdata=SendSocketPost($host,$path,$query);
	return $resdata;
}

function SendSMS($shopid, $authkey, $totellist, $tonamelist, $fromtel, $date, $msg, $etcmsg) {
	#smsID, sms인증키, 받는사람핸드폰, 받는사람명, 보내는사람(회신전화번호), 발송일, 메세지, etc메세지(예:개별 메세지 전송)
	/*exdebug($shopid);
	exdebug($authkey);
	exdebug($totellist);
	exdebug($tonamelist);
	exdebug($fromtel);
	exdebug($date);
	exdebug($msg);
	exdebug($etcmsg);*/

	$cmd_mode = 'sms_send';
	$esntl_key = getDuoEmployEsntlKey();
	$key = getDuoKey();

	$totellist = str_replace("-", "", $totellist);
	$returnTel = str_replace("-", "", getReturnTel());

    // 오류 처리
    if( strlen( trim( str_replace( "||", "", $totellist ) ) ) <= 0 ) {
        return false;
    }
    if( strlen( trim( $returnTel ) ) <= 0 ){
        return false;
    }
    if( strlen( trim( $msg ) ) <= 0 ){
        return false;
    }

	$send_object_count = count(explode("||", $totellist));
	$data = array(
		"key" => $key,
		"esntl_key" => $esntl_key,
		"cmd_mode" => $cmd_mode,
		"hashdata" => md5($cmd_mode.$key),
		"dest_phone" => $totellist,
		"dest_name" => '',
		"send_phone" => $returnTel,
		"msg_body"  => $msg,
		"send_object_count" => $send_object_count,
		"trans_code" => 'SMS'
	); //"msg_body"  => mb_convert_encoding($msg,"utf-8","euc-kr"),

	if($date){
		$arrDate = explode(" ", $date);
		if(count($arrDate) > 1){
			$data[msg_reserve_date] = $arrDate[0];
			$data[msg_reserve_time] = $arrDate[1];
		}
	}
	$temp = duo_sms_send($data);
	# 테스트용 주석
	/*
	if( isdev() ){
		$temp = duo_sms_send($data);
	}
	*/
	duoSmsLog($temp,$totellist,$fromtel,$msg,$etcmsg,$section='S');
	return $temp;

	//return duo_sms_send($data);
	/*
	if(ord($shopid) && ord($authkey)) {
		list($host,$path)=getSmshost($path);
		$path=$path."/process/sendsms.html";
		$service=$_SERVER['HTTP_HOST'];

		$enckey=getEncKey($shopid);
		$query="&tran_id={$shopid}&authkey={$authkey}&enckey={$enckey}&tran_refkey={$service}&tran_phone={$totellist}&tran_callback={$fromtel}&tran_date={$date}&name=".urlencode($tonamelist)."&tran_msg=".urlencode($msg)."&tran_etc1={$etcmsg}&tran_etc2=".$_SERVER['SERVER_ADDR'];

		$resdata=SendSocketPost($host,$path,$query);
		SmsLog($totellist,$fromtel,$msg,$etcmsg,$resdata);
		return $resdata;
	}

	#SMS 발송 가능 횟수는 SMS서버에서 확인 후 메세지를 리턴한다.
	#return "[SMS]문자메세지를 발송하였습니다.";
	*/
}


function SendMMS($shopid, $authkey, $totellist, $tonamelist, $fromtel, $date, $msg, $etcmsg, $fileData='', $subject='') {
	#smsID, sms인증키, 받는사람핸드폰, 받는사람명, 보내는사람(회신전화번호), 발송일, 메세지, etc메세지(예:개별 메세지 전송)
	/*exdebug($shopid);
	exdebug($authkey);
	exdebug($totellist);
	exdebug($tonamelist);
	exdebug($fromtel);
	exdebug($date);
	exdebug($msg);
	exdebug($etcmsg);*/
	$cmd_mode = 'sms_send';
	$esntl_key = getDuoEmployEsntlKey();
	$key = getDuoKey();

	$totellist = str_replace("-", "", $totellist);
	$returnTel = $fromtel ? $fromtel : getReturnTel();
	$returnTel = str_replace("-", "", $returnTel);

	if($fileData['goods_img']['name']){
		# 이미지 업로드
		$ext = end(explode(".", $fileData['goods_img']['name']));
		$file = "temp_".str_pad(rand(0, 1000), 4, '0', STR_PAD_LEFT)."_".strtotime("now");
		$real_name = $file;

		$fileTrans = $fileData['goods_img']['tmp_name'].";filename=".$fileData['goods_img']['name'].";type=".$fileData['goods_img']['type'];
		$data = array(
			"key" => 'smspduometissms',
			"esntl_key" => $esntl_key,
			"cmd_mode" => 'imageUpload',
			"real_name" => $real_name,
			'del_img_url' => '',
			'goods_img' =>  '@' . $fileTrans,
			"hashdata" => md5('imageUploadsmspduometissms')
		);
		$returnUpload = duo_sms_send($data);

		if($returnUpload[result] == 'true'){
			$trans_code = "MMS";
		}else{
			$trans_code = "LMS";
			return $returnUpload;
		}
	}else{
		$trans_code = "LMS";
	}


	# SMS 전송
	$send_object_count = count(explode("||", $totellist));
	$sendData = array(
		"key" => $key,
		"esntl_key" => $esntl_key,
		"cmd_mode" => $cmd_mode,
		"hashdata" => md5($cmd_mode.$key),
		"dest_phone" => $totellist,
		"dest_name" => '',
		"send_phone" => $returnTel,
		"subject"  => $subject,
		"msg_body"  => $msg,
		"send_object_count" => $send_object_count,
		"trans_code" => $trans_code
	);
    /*
        "subject"  => mb_convert_encoding($subject,"utf-8","euc-kr"),
		"msg_body"  => mb_convert_encoding($msg,"utf-8","euc-kr"),
    */

	if($returnUpload[result] == 'true'){
		$sendData['msg_file'] = $returnUpload[goods_img_url];
		$sendData['instant_use'] = 'Y';
	}


	if($date){
		$arrDate = explode(" ", $date);
		if(count($arrDate) > 1){
			$sendData[msg_reserve_date] = $arrDate[0];
			$sendData[msg_reserve_time] = $arrDate[1];
		}
	}
	$temp = duo_sms_send($sendData);
	duoSmsLog($temp,$totellist,$fromtel,$msg,$etcmsg,$section='L');
	return $temp;
	//return duo_sms_send($sendData);
	/*
	if(ord($shopid) && ord($authkey)) {
		list($host,$path)=getSmshost($path);
		$path=$path."/process/sendsms.html";
		$service=$_SERVER['HTTP_HOST'];

		$enckey=getEncKey($shopid);
		$query="&tran_id={$shopid}&authkey={$authkey}&enckey={$enckey}&tran_refkey={$service}&tran_phone={$totellist}&tran_callback={$fromtel}&tran_date={$date}&name=".urlencode($tonamelist)."&tran_msg=".urlencode($msg)."&tran_etc1={$etcmsg}&tran_etc2=".$_SERVER['SERVER_ADDR'];

		$resdata=SendSocketPost($host,$path,$query);
		SmsLog($totellist,$fromtel,$msg,$etcmsg,$resdata);
		return $resdata;
	}

	#SMS 발송 가능 횟수는 SMS서버에서 확인 후 메세지를 리턴한다.
	#return "[SMS]문자메세지를 발송하였습니다.";
	*/
}

function SmsLog($totellist,$fromtel,$msg,$etcmsg,$resdata){

	$totelarr = explode(",",$totellist);
	for($i=0;$i<count($totelarr);$i++){
		$sql = "INSERT INTO tblsmslog(msg, send_date, from_tel_no, to_tel_no, etc_msg, res_msg)";
		$sql.= " VALUES('".$msg."',now(),'".$fromtel."','".$totelarr[$i]."','".$etcmsg."','".$resdata."')";
		//debug($sql);
		$result=pmysql_query($sql,get_db_conn());
	}
}

/**
 *  section 필드 추가
 *  SMS : S, LMS, MMS : L
 *  2016-06-10 jhjeong
 **/
function duoSmsLog($resdata,$totellist,$fromtel,$msg,$etcmsg,$section='S'){

	//exdebug($resdata);
	//exdebug($resdata[result]);
	//exdebug($resdata[send_object_count]);
	//exdebug(mb_convert_encoding($resdata[msg],'EUC-KR','UTF-8')); //resdata
	//exdebug($resdata[goods_img_url]);
	$status = "";
	if($resdata[result] == "true"){
		$status = "Y";
	}else if($resdata[result] == "false"){
		$status = "N";
	}
	$totelarr = explode("||",$totellist);
	//$res_msg = mb_convert_encoding($resdata[msg],'EUC-KR','UTF-8');
	$res_msg = $resdata[msg];

	for($i=0;$i<count($totelarr);$i++){
		$sql = "INSERT INTO tblsmslog(msg, send_date, from_tel_no, to_tel_no, etc_msg, res_msg, status, section)";
		$sql.= " VALUES('".$msg."',now(),'".$fromtel."','".$totelarr[$i]."','".$etcmsg."','".$res_msg."','".$status."','".$section."')";
	}
	$result=pmysql_query($sql,get_db_conn());
	return;
}


function getRemoteImageData($host,$path,$ext,$port=80) {
	$fp = @fsockopen($host, $port, $errno, $errstr, 3);
	if(!$fp) {
		@fclose($fp);
		return "ERROR : $errstr ($errno)";
	} else {
		$cmd = "GET $path HTTP/1.1\n";
		fputs($fp, $cmd);
		$cmd = "Host: $host\n";
		fputs($fp, $cmd);
		$cmd = "Content-type: image/$ext\n";
		fputs($fp, $cmd);
		$cmd = "Connection: close\n\n";
		fputs($fp, $cmd);
		while($currentHeader = fgets($fp,4096)) {
			if($currentHeader == "\r\n") {
				break;
			}
		}
		$strLine = "";
		while(!feof($fp)) {
			$strLine .= fgets($fp, 4096);
		}
		fclose($fp);
		return $strLine;
	}
}

function getSecureKeyData($key) {
	if (file_exists(DirPath.DataDir."ssl/{$key}.temp")) {
		$secure_data = file_get_contents(DirPath.DataDir."ssl/{$key}.temp");
		$secure_data=unserialize($secure_data);
		@unlink(DirPath.DataDir."ssl/{$key}.temp");
		return $secure_data;
	}
}

function delProductMultiImg($type,$code,$productcode) {
	global $Dir;
	include ($Dir."lib/prmultiprocess.php");
}

function SendSocketPost($host,$path,$query,$port=80) {

	$opts = array('http' =>
	    array(
		'method'  => 'POST',
		'header'  => 'Content-type: application/x-www-form-urlencoded',
		'content' => $query
	    )
	);
	if($port!=80) $host .= ":{$port}";
	$context = stream_context_create($opts);
	//exdebug("http://{$host}/{$path}");
	//exdebug($query);

	$result = @file_get_contents("http://{$host}/{$path}", false, $context);

	return $result;
}

function SendSocketGet($host,$path,$query,$port=80) {
	$opts = array('http' =>
	    array(
		'method'  => 'GET',
		'header'  => 'Content-type: application/x-www-form-urlencoded',
		'content' => $query
	    )
	);
	if($port!=80) $host .= ":{$port}";
	$context = stream_context_create($opts);

	$result = file_get_contents("http://{$host}/{$path}", false, $context);

	return $result;
}

function ismail($strEmail) {
	return filter_var($strEmail, FILTER_VALIDATE_EMAIL) ? true : false;
}

function IsAlphaNumeric($data) {
	return (preg_match("/^[a-zA-Z0-9]+$/",$data)) ? true : false;
}

function IsNumeric($data) {
	return (preg_match("/^[0-9]+$/",$data)) ? true : false;
}

function getMailHeader($send_name,$send_email) {
	$mailheaders  = "From: $send_name <$send_email>\r\n";
	$mailheaders .= "X-Mailer:SendMail\r\n";
	$mailheaders .= "MIME-Version: 1.0\r\n";
	$mailheaders .= "Content-Type: text/html; charset=utf-8\r\n";
	return $mailheaders;
}

function sendmail($to, $subject, $body, $header) {

	// 기존 주석처리 됨
	//mail($to,$subject,$body,$header);
// 	echo "to=".$to."<br>";
// 	echo "subject=".$subject."<br>";
// 	echo "body=".$body."<br>";
// 	echo "header=".$header."<br>";
// 	exit();
	mail($to,$subject,$body,$header);
}

function getMailData($sender_name,$sender_email,$message,$file,&$bodytext,&$mailheaders) {
	$boundary = "--------" . uniqid("part");

	$mailheaders  = "From: $sender_name <$sender_email>\r\n";
	$mailheaders .= "X-Mailer:SendMail\r\n";
	$mailheaders .= "MIME-Version: 1.0\r\n";

	if ($file && $file["size"]>0) {	// 첨부파일 있으면...
		$mailheaders .= "Content-Type: Multipart/mixed; boundary=\"$boundary\"";
		$bodytext  = "This is a multi-part message in MIME format.\r\n";
		$bodytext .= "\r\n--$boundary\r\n";
		$bodytext .= "Content-Type: text/html; charset=UTF-8\r\n";
		$bodytext .= "Content-Transfer-Encoding: 8bit\r\n\r\n";
		$bodytext .= $message . "\r\n\r\n";

		$filename = basename($file["name"]);
		$file = file_get_contents($filename);

		if ($upfile["type"]=="") {
			$upfile["type"] = "application/octet-stream";
		}

		$bodytext .= "\r\n--$boundary\r\n";
		$bodytext .= "Content-Type: {$upfile['type']}; name=\"$filename\"\r\n";
		$bodytext .= "Content-Transfer-Encoding: base64\r\n";
		$bodytext .= "Content-Disposition: attachment; filename=\"$filename\"\r\n\r\n";
		$bodytext .= chunk_split(base64_encode($file))."\r\n";
		$bodytext .= "\r\n--{$boundary}--\r\n";
	} else {
		$mailheaders .= "Content-Type: text/html; charset=euc-kr\r\n";
		$bodytext .= $message . "\r\n\r\n";
	}
}

function getDirList($path) {
	global $dirlist;

	$directory = dir($path);
	while($entry = $directory->read()) {
		if ($entry != "." && $entry != "..") {
			if (is_dir($path."/".$entry)) {
				$dirlist[]=$path."/".$entry;
				getDirList($path."/".$entry);
			}
		}
	}
	$directory->close();
}

function getFileList($path) {
	$filelist=array();
	$directory = dir($path);
	while($entry = $directory->read()) {
		if ($entry != "." && $entry != "..") {
			if (!is_dir($path."/".$entry)) {
				$filelist[]=$entry;
			}
		}
	}
	$directory->close();

	return $filelist;
}

function proc_rmdir($path) {
	global $rmdirlist;
	$rmdirlist[]=$path;
	$directory = dir($path);
	while($entry = $directory->read()) {
		if ($entry != "." && $entry != "..") {
			if (is_dir($path."/".$entry)) {
				proc_rmdir($path."/".$entry);
			} else {
				@unlink($path."/".$entry);
			}
		}
	}
	$directory->close();

	for($i=0;$i<count($rmdirlist);$i++) {
		if(is_dir($rmdirlist[$i])) {
			@rmdir($rmdirlist[$i]);
		}
	}
}

function proc_matchfiledel($match) {
	if(strlen($match)) {
		$match=str_replace(" ","",$match);
		$matches=glob($match);
		if(is_array($matches)) {
			foreach($matches as $delfile) {
				@unlink($delfile);
			}
		}
	}
}

function getStringCut($strValue,$lenValue)
{
	// substr -> mb_substr 2015 11 26 유동혁
	preg_match('/^([\x00-\x7e]|.{2})*/', mb_substr($strValue,0,$lenValue,'UTF-8'), $retrunValue);
	return $retrunValue[0];
}

function titleCut($len_title,$title) {
	if(strlen($title)>$len_title)
		$n_title = getStringCut($title,$len_title) . "...";
	else
		$n_title = $title;
	return $n_title;
}

function len_title($title,$len_title) {
	return titleCut($len_title,$title);
}

function unique_id() {
	$now = (string)microtime();
	$now = explode(" ", $now);
	$unique_id = $now[1].str_replace(".", "", $now[0]);
	$now='';

	$tm = date("YmdHis",substr($unique_id,0,10)).substr($unique_id,11,5)."A";
	return $tm;
}

//번호만 추출
function check_num($str){
	return preg_replace("/[^0-9]/","",$str);
}

//전화번호 정비
function replace_tel($tel) {
	$tel2="";
	if(substr($tel,0,2)=="02") {
		$tel2="02-";
		$num=2;
	} else {
		if (strlen($tel)<=8) {
			$tel2="02-";
			$num=0;
		} else {
			$tel2=substr($tel,0,3)."-";
			$num=3;
		}
	}
	if(strlen($tel)-$num==7) $tel2.=substr($tel,$num,3)."-".substr($tel,$num+3,4);
	else $tel2.=substr($tel,$num,4)."-".substr($tel,$num+4,4);
	return $tel2;
}

//핸드폰 번호 체크
function check_mobile_head($tel){
	$tel2=check_num($tel);
	if(ord($tel2)){
		if(preg_match("/^(010|011|016|017|018|019)/",$tel2))
			return $tel2;
	}
	return 0;
}


//사업자등록번호 체크 함수
function chkBizNo($val) {
	if (strlen($val) == 10) {
		$bizID = $val;
		$checkID = Array(1, 3, 7, 1, 3, 7, 1, 3, 5, 1);
		$chkSum = 0;

		for ($i=0; $i<=7; $i++) $chkSum += $checkID[$i] * $bizID[$i];

		$c2 = "0" . ($checkID[8] * $bizID[8]);
		$c2 = substr($c2, strlen($c2) - 2, strlen($c2));

		$chkSum += floor($c2[0]) + floor($c2[1]);

		$remainder = (10 - ($chkSum % 10)) % 10 ;

		if (floor($bizID[9]) != $remainder) {
			return false;
		} else {
			return true;
		}
	} else {
		return false;
	}
}

//주민등록번호 체크 함수
function chkResNo($val) {
	if (strlen($val)==13) {
		$calStr1="234567892345";
		$biVal=0;
		$restCal="";

		for($i=0;$i<=11;$i++) {
			$biVal = $biVal + ($val[$i] * $calStr1[$i]);
		}

		$restCal = 11 - ($biVal % 11);

		if ($restCal == 11) {
			$restCal = 1;
		}

		if ($restCal == 10) {
			$restCal = 0;
		}

		if ($restCal == $val[12]) {
			return true;
		} else {
			return false;
		}
	} else {
		return false;
	}
}

//나이계산 (13자리)
function getAgeResno($resno) {
	$age=0;
	$gbn=$resno[6];
	if($gbn=="3" || $gbn=="4") {
		$year="20".substr($resno,0,2);
		$age=date("Y")-$year;
	} elseif ($gbn=="1" || $gbn=="2") {
		$year="19".substr($resno,0,2);
		$age=date("Y")-$year;
	}
	return $age;
}

function getUrl() {
	$file = $_SERVER['SCRIPT_NAME'];
	$query = $_SERVER['QUERY_STRING'];
	$chUrl = $file;

	if($query) $chUrl.="?".$query;
	return urlencode($chUrl);
}

function getTitle($title) {
	$title = stripslashes($title);
	$title = str_replace(array("\"","\'","'"),array("＂","`","`"), $title);	// " 문자의 변환
	//$title = str_replace("<","&lt",$title);
	//$title = str_replace(">","&gt",$title);
	return $title;
}

function getStripHide($msg) {
	$msg = str_replace(array("<!--","-->"),array("&lt;!--","--&gt;"),$msg);
	return $msg;
}

function isNull($str) {
	$tmp=str_replace(array("　","\n"),"",$str);
	$tmp=strip_tags($tmp);
	$tmp=str_replace(array("&nbsp;"," "),"",$tmp);
	if(preg_match("/[[:^space:]]/",$tmp)) return 0;
	return 1;
}

function autoLink($str) {
	// http url
	$homepage_pattern = "/([^\"\=\>])(mms|http|HTTP|ftp|FTP|telnet|TELNET)\:\/\/(.[^ \n\<\"]+)/";
	$str = preg_replace($homepage_pattern,"\\1<a href=\\2://\\3 target=_blank>\\2://\\3</a>", " ".$str);

	// mail
	$email_pattern = "/([ \n]+)([a-z0-9\_\-\.]+)@([a-z0-9\_\-\.]+)/";
	$str = preg_replace($email_pattern,"\\1<a href=mailto:\\2@\\3>\\2@\\3</a>", " ".$str);

	return $str;
}

function buffer_process($buffer) {
	$data = str_replace("\r","",$buffer);
	$data = explode("\n",str_replace("'","\\'",$data));
	$buffer='';
	for ($i=0;$i<sizeof($data);$i++) {
		$temp.= "document.writeln('{$data[$i]}');\n";
	}
	return $temp;
}

function backup_save_sql($sql) {
	$savepatten=array("\n","^M");
	$savereplace=array("","\\r\\n");
	$savetemp = str_replace($savepatten,$savereplace,$sql)."; /** ".date("H").":".date("i")."**/\n";
	$file = DirPath.DataDir."backup/".date("Y")."_".date("m")."_".date("d")."_"."00______";
	if(!is_file($file)){
		$f = fopen($file,"a+");
		fclose($f);
		chmod($file,0777);
	}
	file_put_contents($file,$savetemp,FILE_APPEND);
}

function backup_save_logs($title, $content) {
	if(strlen($title) > 0){
		$savepatten=array("\n","^M");
		$savereplace=array("","\\r\\n");
		$savetemp = str_replace($savepatten,$savereplace,$content)."; /** ".date("H").":".date("i")."**/\n";
		$file = DirPath.DataDir."backup/".$title."_".date("Y")."_".date("m")."_".date("d")."_".".txt";
		if(!is_file($file)){
			$f = fopen($file,"a+");
			fclose($f);
			chmod($file,0777);
		}
		file_put_contents($file,$savetemp,FILE_APPEND);
	}
}

function getSearchBestKeyword($target,$maxkeylen,$str,$keygbn=",",$keystyle="") {
	$data="";
	$yy=0;
	if(strlen($str)) {
		$tempbest=explode(",",$str);
		$_ = array();
		foreach($tempbest as $tempbestname) {
			$tempbestname2 = titleCut($maxkeylen,$tempbestname);
			$yy += strlen($tempbestname2);
			if($yy>$maxkeylen) break;
			$_[] = "<A HREF=\"".DirPath.FrontDir."productsearch.php?search=".urlencode($tempbestname)."\" {$target} {$keystyle}>{$tempbestname2}</A>";
		}
		$data = implode($keygbn,$_);
	}
	return $data;
}

function return_vat($vatprice) {
	$vatprice = (int)$vatprice;
	if($vatprice>0) {
		return @round(($vatprice/10)/10)*10;
	} else {
		return 0;
	}
}

function setDeliLimit($totalprice,$delilimit,$msguse="N") {
	$deli_limit_exp = explode("=",$delilimit);
	for($i=0; $i<count($deli_limit_exp); $i++) {
		$deli_limit_exp2=explode("",$deli_limit_exp[$i]);
		if(strlen($deli_limit_exp2[1])) {
			if($deli_limit_exp2[0]<=$totalprice && $totalprice<$deli_limit_exp2[1]) {
				$delilmitprice = (int)$deli_limit_exp2[2];
				if($msguse=="Y") {
					$delilmitprice.= "".number_format($deli_limit_exp2[0])." 이상 ".number_format($deli_limit_exp2[1])." 미만";
				}
				break;
			} else {
				$delilmitprice="";
			}
		} else {
			if($deli_limit_exp2[0]<=$totalprice) {
				$delilmitprice = (int)$deli_limit_exp2[2];
				if($msguse=="Y") {
					$delilmitprice.= "".number_format($deli_limit_exp2[0])." 이상";
				}
				break;
			} else {
				$delilmitprice="";
			}
		}
	}
	return $delilmitprice;
}

function is_blank($str) {
	$temp=str_replace("　","",$str);
	$temp=str_replace("\n","",$temp);
	$temp=strip_tags($temp);
	$temp=str_replace("&nbsp;","",$temp);
	$temp=str_replace(" ","",$temp);
	if(preg_match("/[[:^space:]]/",$temp)) return 0;
	return 1;
}

function get_message($msg) {
	$pos = strpos($msg,"\\n");
	if ($pos<1) $pos = strlen($msg);
	$line = substr("************************************************************************",0,$pos+6);
	$temp = $line."\\n\\n";
	$temp.= $msg;
	$temp.= "\\n\\n";
	$temp.= $line;

	return $temp;
}

function error_msg($msg,$url="") {
	global $Dir;

	include ($Dir."error.php");

	exit;
}

///////////////////////////////////// vender 서비스 파일 체크 시작 /////////////////////////////////////

function setUseVender() {
	GLOBAL $_ShopInfo;
	$usevender=true;
	$vauthkey=getVenderUsed();
	if($vauthkey["OK"]!="OK") {
		$usevender=false;
	} elseif($vauthkey["DOMAIN"]!=$_ShopInfo->getShopurl()) {
		$usevender=false;
	} elseif($vauthkey["DATE"]!="*" && $vauthkey["DATE"]<date("Ymd")) {
		$usevender=false;
	}
	return $usevender;
}

function setVenderUsed() {
	GLOBAL $_ShopInfo;
	$vender_used="";
	$vauthkey=getVenderUsed();
	if($vauthkey["OK"]=="OK") {
		if($vauthkey["DOMAIN"]!=$_ShopInfo->getShopurl()) {
			$vender_used="<a style=\"cursor:hand\" onclick=\"alert('입점기능 인증키의 도메인정보가 잘못되어 이용하실 수 없습니다.')\"><font color=red>사용불가</font></a>";
		} elseif($vauthkey["DATE"]!="*" && $vauthkey["DATE"]<date("Ymd")) {
			$vender_used="<a style=\"cursor:hand\" onclick=\"alert('입점기능 사용기간이 만료되어 이용하실 수 없습니다.')\"><font color=red>사용기간 만료</font></a>";
		} else {
			if($vauthkey["DATE"]!="*") {
				$vender_used_date=substr($vauthkey["DATE"],0,4)."/".substr($vauthkey["DATE"],4,2)."/".substr($vauthkey["DATE"],6,2);
				if($vauthkey["COUNT"]!="*") {
					$vender_used="<a style=\"cursor:hand\" onclick=\"alert('입점기능 이용 기간은 {$vender_used_date} 까지 이용 가능하며\\n\\n입점 가능 업체수는 {$vauthkey['COUNT']}개 업체 입니다.')\"><font class=\"font_orange4\">{$vender_used_date}</font></a>";
				} else {
					$vender_used="<a style=\"cursor:hand\" onclick=\"alert('입점기능 이용 기간은 {$vender_used_date} 까지 이용 가능하며\\n\\n입점업체수는 무제한 입점 가능합니다.')\"><font class=\"font_orange4\">{$vender_used_date}</font></a>";
				}
			} else {
				if($vauthkey["COUNT"]!="*") {
					$vender_used="<a style=\"cursor:hand\" onclick=\"alert('기간 제한 없이 이용 가능합니다. (입점제한 : {$vauthkey['COUNT']}업체)')\"><font class=\"font_orange4\">{$vauthkey['COUNT']}개 사용가능</font></a>";
				} else {
					$vender_used="<a style=\"cursor:hand\" onclick=\"alert('입점기능 및 미니샵을 이용하시는데 아무런 제약 없이 이용 가능합니다.')\"><font class=\"font_orange4\">무제한</font></a>";
				}
			}
		}
	} else {
		$vender_used="<a style=\"cursor:hand\" onclick=\"alert('입점기능 및 미니샵은 몰인몰(E-market) 버전에서만 사용하실 수 있습니다.')\"><font color=red>사용불가</font></a>";
	}
	return $vender_used;
}


function setVenderCountUpdate($prdt_allcnt, $prdt_cnt, $arrvender_yy) {
	$sql ="UPDATE tblvenderstorecount SET prdt_allcnt='{$prdt_allcnt}', prdt_cnt='{$prdt_cnt}' ";
	$sql.="WHERE vender='{$arrvender_yy}' ";
	@pmysql_query($sql,get_db_conn());
}

function setVenderCountUpdateMin($vender, $vdisp) {
	$sql ="UPDATE tblvenderstorecount SET ";
	if($vdisp=="Y") {
		$sql.="prdt_cnt=prdt_cnt-1, ";
	}
	$sql.="prdt_allcnt=prdt_allcnt-1 ";
	$sql.="WHERE vender='{$vender}' ";
	@pmysql_query($sql,get_db_conn());
}

function setVenderCountUpdateRan($vender, $display) {
	$sql ="UPDATE tblvenderstorecount SET ";
	if($display=="Y") {
		$sql.="prdt_cnt=prdt_cnt+1 ";
	} else {
		$sql.="prdt_cnt=prdt_cnt-1 ";
	}
	$sql.="WHERE vender='{$vender}' ";
	@pmysql_query($sql,get_db_conn());
}

function setVenderThemeDelete($prcodelist, $arrvender_yy) {
	$sql = "DELETE FROM tblvenderthemeproduct WHERE vender='{$arrvender_yy}' ";
	$sql.= "AND productcode IN ('{$prcodelist}') ";
	@pmysql_query($sql,get_db_conn());
}

function setVenderThemeDeleteNor($prcode, $vender) {
	$sql = "DELETE FROM tblvenderthemeproduct WHERE vender='{$vender}' ";
	$sql.= "AND productcode='{$prcode}' ";
	@pmysql_query($sql,get_db_conn());
}

function setVenderThemeDeleteLike($likecode, $arrvender_yy) {
	$sql = "DELETE FROM tblvenderthemeproduct WHERE vender='{$arrvender_yy}' ";
	$sql.= "AND productcode LIKE '{$likecode}%' ";
	@pmysql_query($sql,get_db_conn());
}

function setVenderThemeSpecialUpdate($vender, $prarr_IN_kk, $prarr_OUT_kk) {
	$sql = "UPDATE tblvenderthemeproduct SET productcode='{$prarr_IN_kk}' ";
	$sql.= "WHERE vender='{$vender}' AND productcode='{$prarr_OUT_kk}' ";
	@pmysql_query($sql,get_db_conn());

	$sql = "UPDATE tblvenderspecialcode SET ";
	$sql.= "special_list = replace(special_list,'{$prarr_OUT_kk}','{$prarr_IN_kk}') ";
	$sql.= "WHERE vender='{$vender}' ";
	@pmysql_query($sql,get_db_conn());

	$sql = "UPDATE tblvenderspecialmain SET ";
	$sql.= "special_list = replace(special_list,'{$prarr_OUT_kk}','{$prarr_IN_kk}') ";
	$sql.= "WHERE vender='{$vender}' ";
	@pmysql_query($sql,get_db_conn());
}

function setVenderDesignDelete($str_code_a, $arrvender_yy) {
	$sql = "DELETE FROM tblvendercodedesign WHERE vender='{$arrvender_yy}' ";
	$sql.= "AND code IN ('{$str_code_a}') AND tgbn='10' ";
	@pmysql_query($sql,get_db_conn());
}

function setVenderDesignDeleteNor($tmpcode_a, $vender) {
	$sql = "DELETE FROM tblvendercodedesign WHERE vender='{$vender}' ";
	$sql.= "AND code='{$tmpcode_a}' AND tgbn='10' ";
	pmysql_query($sql,get_db_conn());
}

function setVenderDesignInsert($vender, $prarr_IN_kk) {
	$sql = "INSERT INTO tblvendercodedesign(
	vender	,
	code	,
	tgbn	,
	hot_used,
	hot_dispseq) VALUES (
	'{$vender}',
	'".substr($prarr_IN_kk,0,3)."',
	'10',
	'1',
	'118')";
	@pmysql_query($sql,get_db_conn());
}
////////////////////////////////////////////////////////////////////////////////////////////////////////

//////////////////////////////// 결제창 띄울 경우 장바구니 기타사항 복구 ///////////////////////////////
// 2016-05-19 basket.class.php 이전 => 사용안함
function basket_restore() {
	global $_ShopInfo;
	$oldtempkey = $_ShopInfo->getOldtempkey();
	$curtempkey = $_ShopInfo->gettempkey();
	if(ord($oldtempkey) && ord($curtempkey)) {
		$sql="SELECT COUNT(*) AS oldbasketcount FROM tblbasket WHERE tempkey='{$oldtempkey}' ";
		$result=pmysql_query($sql,get_db_conn());
		if($row=pmysql_fetch_object($result)) {
			$oldbasketcount=$row->oldbasketcount;
		}
		pmysql_free_result($result);

		if($oldbasketcount>0) {
			$sql = "SELECT * FROM tblorderinfotemp WHERE tempkey='{$oldtempkey}' ";
			$result=pmysql_query($sql,get_db_conn());
			$data=pmysql_fetch_object($result);
			pmysql_free_result($result);
			if($data && ord($data->del_gbn)==0 && substr($data->ordercode,0,12)<=date("YmdHis")) {
				pmysql_query("UPDATE tblorderinfotemp SET del_gbn='R' WHERE ordercode='{$data->ordercode}'",get_db_conn());
			}
			pmysql_query("UPDATE tblbasket SET tempkey='{$curtempkey}' WHERE tempkey='{$oldtempkey}'",get_db_conn()); //장바구니 복원
		}
		$_ShopInfo->setOldtempkey("");
		$_ShopInfo->Save();
	}
}

function get_totaldays($year,$month) {
	if($time = strtotime("$year-$month-01"))
		$date = (int)date('t',$time);
	else
		$date = 0;
	return $date;
}

function WriteEngine($engine, $file) {
	$filename = DirPath.DataDir."shopimages/etc/".$file;

	$success = (file_put_contents($filename,serialize($engine))!==FALSE);
	return $success;
}

function ReadEngine($file) {
	$filename = DirPath.DataDir."shopimages/etc/".$file;

	if(file_exists($filename)) {
		$szdata = file_get_contents($filename);
		$engine=unserialize($szdata);
	}
	return $engine;
}


function msg($msg,$code=null,$target='')
{
	echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8">';
	echo "<script>alert('$msg')</script>";
		switch (getType($code)){
			case "null":
				return;
			case "string":
				if ($code=="close") echo "<script>window.close()</script>";
				else go($code,$target);
				exit;
			case "integer":
			if ($code) echo "<script>history.go($code)</script>";
			exit;
		}
}

### 페이지 이동
function go($url,$target='')
{
	if ($target) $target .= ".";
	echo "<script>{$target}location.replace('$url')</script>";
	exit;
}

function getCodeLoc($code) {
	$code_loc = "";
	$sql = "SELECT code_name FROM tblproductcode WHERE code_a='".substr($code,0,3)."' ";
	if(substr($code,3,3)!="000") {
		$sql.= "AND (code_b='".substr($code,3,3)."' OR code_b='000') ";
		if(substr($code,6,3)!="000") {
			$sql.= "AND (code_c='".substr($code,6,3)."' OR code_c='000') ";
			if(substr($code,9,3)!="000") {
				$sql.= "AND (code_d='".substr($code,9,3)."' OR code_d='000') ";
			} else {
				$sql.= "AND code_d='000' ";
			}
		} else {
			$sql.= "AND code_c='000' ";
		}
	} else {
		$sql.= "AND code_b='000' AND code_c='000' ";
	}
	$sql.= "ORDER BY code_a,code_b,code_c,code_d ASC ";
	$result=pmysql_query($sql,get_db_conn());
	$_=array();
	while($row=pmysql_fetch_object($result)) {
		$_[] = $row->code_name;
	}
	$code_loc = implode(" > ",$_);
	pmysql_free_result($result);
	return $code_loc;
}


function getCodeLoc2($code,$color1="9E9E9E",$color2="9E9E9E") {
	global $Dir;
	$code_loc = "<A HREF=\"".$Dir.MainDir."main.php\">홈</A>&nbsp;>&nbsp;";
	$sql = "SELECT code_a,code_b,code_c,code_d,code_name,type FROM tblproductcode ";
	$sql.= "WHERE code_a='".substr($code,0,3)."' ";
	if(substr($code,3,3)!="000") {
		$sql.= "AND (code_b='".substr($code,3,3)."' OR code_b='000') ";
		if(substr($code,6,3)!="000") {
			$sql.= "AND (code_c='".substr($code,6,3)."' OR code_c='000') ";
			if(substr($code,9,3)!="000") {
				$sql.= "AND (code_d='".substr($code,9,3)."' OR code_d='000') ";
			} else {
				$sql.= "AND code_d='000' ";
			}
		} else {
			$sql.= "AND code_c='000' ";
		}
	} else {
		$sql.= "AND code_b='000' AND code_c='000' ";
	}
	$sql.= "ORDER BY code_a,code_b,code_c,code_d ASC ";
	$result=pmysql_query($sql,get_db_conn());
	$_=array();
	while($row=pmysql_fetch_object($result)) {
		$tmpcode=$row->code_a.$row->code_b.$row->code_c.$row->code_d;
		if($row->code_b == "000"){
			$searchCategory = "AND code_b||code_c||code_d = '000000000'";
		}else if($row->code_c == "000"){
			$searchCategory = "AND code_a = '".$row->code_a."' AND code_b != '000' AND code_c||code_d = '000000'";
		}else if($row->code_d == "000"){
			$searchCategory = "AND code_a||code_b = '".$row->code_a.$row->code_b."' AND code_c != '000' AND code_d = '000'";
		}

		$sqlSubCategory = "SELECT code_a,code_b,code_c,code_d,code_name,type FROM tblproductcode WHERE 1=1 ".$searchCategory." ORDER BY cate_sort ASC";
		$resultSubCategory=pmysql_query($sqlSubCategory,get_db_conn());
		$categoryLoop = array();
		while($rowSubCategory=pmysql_fetch_array($resultSubCategory)) {
			$categoryLoop[] = $rowSubCategory;
		}
		$strCategoryList = "";
		if(count($categoryLoop)>0){
			$strCategoryList = "<ul class='aList'>";
			foreach($categoryLoop as $cv){
				$strCategoryList .= "<li><a href='".$Dir.FrontDir."productlist.php?code=".$cv['code_a'].$cv['code_b'].$cv['code_c'].$cv['code_d']."'>".$cv['code_name']."</a></li>";
			}
			$strCategoryList .= "</ul>";
		}
		if($code==$tmpcode) {
			$_[] ="
						<div class='select_type ta_l' style='width:150px;z-index:9;'>
							<span class='ctrl'><span class='arrow'></span></span>
							<button type='button' class='myValue'>{$row->code_name}</button>
							".$strCategoryList."
						</div>";
		} else {
			$_[] = "
						<div class='select_type ta_l' style='width:150px;z-index:9;'>
							<span class='ctrl'><span class='arrow'></span></span>
							<button type='button' class='myValue'>{$row->code_name}</button>
							".$strCategoryList."
						</div>
			";
		}
	}
	$code_loc .= implode("&nbsp;>&nbsp;",$_);
	pmysql_free_result($result);
	return $code_loc;
}

function getCodeLoc3($code) {
	$code_loc = "";
	$sql = "SELECT code_name FROM tblproductcode WHERE code_a='".substr($code,0,3)."' ";
	if(substr($code,3,3)!="000") {
		$sql.= "AND (code_b='".substr($code,3,3)."' OR code_b='000') ";
		if(substr($code,6,3)!="000") {
			$sql.= "AND (code_c='".substr($code,6,3)."' OR code_c='000') ";
			if(substr($code,9,3)!="000") {
				$sql.= "AND (code_d='".substr($code,9,3)."' OR code_d='000') ";
			} else {
				$sql.= "AND code_d='000' ";
			}
		} else {
			$sql.= "AND code_c='000' ";
		}
	} else {
		$sql.= "AND code_b='000' AND code_c='000' ";
	}
	$sql.= "ORDER BY code_a,code_b,code_c,code_d ASC ";
	$result=pmysql_query($sql,get_db_conn());
	$_=array();
	while($row=pmysql_fetch_object($result)) {
		$_[] = $row->code_name;
	}
	$code_loc = $_;
	pmysql_free_result($result);
	return $code_loc;
}

function getBCodeLoc($brandcode,$code="",$color1="9E9E9E",$color2="9E9E9E") {
	global $Dir;
	$sql = "SELECT brandname FROM tblproductbrand ";
	$sql.= "WHERE bridx='{$brandcode}' ";
	$result=pmysql_query($sql,get_db_conn());
	$brow=pmysql_fetch_object($result);

	if(ord($code)) {
		$code_loc = "<A HREF=\"".$Dir.MainDir."main.php\"><FONT COLOR=\"{$color1}\">홈</FONT></A> <FONT COLOR=\"{$color1}\">></FONT> <A HREF=\"".$Dir.FrontDir."productblist.php?brandcode={$brandcode}\"><FONT COLOR=\"{$color1}\">브랜드 : {$brow->brandname}</FONT></A>";
		$sql = "SELECT code_a,code_b,code_c,code_d,code_name,type FROM tblproductcode ";
		$sql.= "WHERE code_a='".substr($code,0,3)."' ";
		if(substr($code,3,3)!="000") {
			$sql.= "AND (code_b='".substr($code,3,3)."' OR code_b='000') ";
			if(substr($code,6,3)!="000") {
				$sql.= "AND (code_c='".substr($code,6,3)."' OR code_c='000') ";
				if(substr($code,9,3)!="000") {
					$sql.= "AND (code_d='".substr($code,9,3)."' OR code_d='000') ";
				} else {
					$sql.= "AND code_d='000' ";
				}
			} else {
				$sql.= "AND code_c='000' ";
			}
		} else {
			$sql.= "AND code_b='000' AND code_c='000' ";
		}
		$sql.= "ORDER BY code_a,code_b,code_c,code_d ASC ";
		$result=pmysql_query($sql,get_db_conn());
		$i=0;
		while($row=pmysql_fetch_object($result)) {
			$tmpcode=$row->code_a.$row->code_b.$row->code_c.$row->code_d;
			$code_loc.= " <FONT COLOR=\"{$color1}\">></FONT> ";
			if($code==$tmpcode) {
				$code_loc.="<A HREF=\"".$Dir.FrontDir."productblist.php?brandcode={$brandcode}&code={$tmpcode}\"><FONT COLOR=\"{$color2}\"><B>{$row->code_name}</B></FONT></A>";
			} else {
				$code_loc.="<A HREF=\"".$Dir.FrontDir."productblist.php?brandcode={$brandcode}&code={$tmpcode}\"><FONT COLOR=\"{$color1}\">{$row->code_name}</FONT></A>";
			}
		}
		pmysql_free_result($result);
	} else {
		$code_loc = "<li class=\"home\"><A HREF=\"".$Dir.MainDir."main.php\"><FONT COLOR=\"{$color1}\">홈</FONT></A></li> <FONT COLOR=\"{$color1}\">></FONT> <A HREF=\"".$Dir.FrontDir."productblist.php?brandcode={$brandcode}\"><FONT COLOR=\"{$color1}\"><B>브랜드 : {$brow->brandname}</FONT></B></A>";
	}
	return $code_loc;
}

define("_IncomuShopVersionNo", "1.4.0");			#독립형 쇼핑몰 Version
define("_IncomuShopVersionDate", "2011/04/26");		#Version 업데이트 날짜
define("_IncomuUrl", "www.incomu.com");				#솔루션 배포 회사 도메인
if(ord(getSellerdomain("DOMAIN"))) {
	define("_SellerUrl", getSellerdomain("DOMAIN"));#리셀러 배포 회사 도메인
} else {
	define("_SellerUrl", _IncomuUrl);				#리셀러 배포 회사 도메인
}
define("_InfoDomain", "www.incomu.com");			#회사 도메인
define("_DEMOSHOP", "");							#데모 쇼핑몰에서만 사용


setFileZendCheck();
temporder_restore();

$_ShopInfo = new Shopinfo(isset($_COOKIE['_sinfo'])?$_COOKIE['_sinfo']:null);

$_vscriptname=ltrim(str_replace(RootPath,"",$_SERVER['SCRIPT_NAME']),'/');

if(ord($_vscriptname)) {
	switch($_vscriptname) {
		// 관리자모드 admin 벤더 관련 일반페이지
		case AdminDir."vender_calendar.php":
		case AdminDir."vender_counsel.php":
		case AdminDir."vender_mailsend.php":
		case AdminDir."vender_notice.php":
		case AdminDir."vender_orderadjust.php":
		case AdminDir."vender_orderlist.php":
		case AdminDir."vender_prdtallsoldout.php":
		case AdminDir."vender_prdtallupdate.php":
		case AdminDir."vender_prdtlist.php":
		case AdminDir."vender_smssend.php":
		case AdminDir."vender_infomodify.php":
		case AdminDir."vender_management.php":
		case AdminDir."vender_new.php":
			$vauthkey=getVenderUsed();
			if($vauthkey["OK"]!="OK") {
				//입점기능 사용 불가능
				alert_go('입점기능 및 미니샵은 몰인몰(E-market) 버전에서만 사용하실 수 있습니다.',-1);
			} elseif($vauthkey["DOMAIN"]!=$_ShopInfo->getShopurl()) {
				//도메인 정보가 올바르지 않음
				alert_go('입점기능 인증키의 도메인정보가 잘못되어 이용하실 수 없습니다.',-1);
			} elseif($vauthkey["DATE"]!="*" && $vauthkey["DATE"]<date("Ymd")) {
				//사용기간이 만료되었습니다.
				alert_go('입점기능 사용기간이 만료되어 이용하실 수 없습니다.',-1);
			} elseif($_vscriptname!=AdminDir."vender_infomodify.php" && $_vscriptname!=AdminDir."vender_management.php" && $vauthkey["COUNT"]!="*") {
				if($_vscriptname==AdminDir."vender_new.php" && (ord($vauthkey["COUNT"])==0 || $vauthkey["COUNT"]==0)) {
					alert_go('입점업체 신규등록을 하실 수 없습니다.',-1);
				}
				$vendercount_result=@pmysql_query("SELECT COUNT(*) as cnt FROM tblvenderinfo ",@get_db_conn());
				$vendercount_row=@pmysql_fetch_object($vendercount_result);
				@pmysql_free_result($vendercount_result);
				if($_vscriptname==AdminDir."vender_new.php" && $vendercount_row->cnt>=$vauthkey["COUNT"]) {
					alert_go("본 쇼핑몰은 {$vauthkey["COUNT"]}업체 까지 서비스 가능합니다.\\n\\n입점업체 정리 후 이용하시기 바랍니다.",'vender_management.php');
				} elseif ($vendercount_row->cnt>$vauthkey["COUNT"]) {
					alert_go("본 쇼핑몰은 {$vauthkey["COUNT"]}업체 까지 서비스 가능합니다.\\n\\n입점업체 정리 후 이용하시기 바랍니다.",'vender_management.php');
				}
			}
			break;
		// 관리자모드 admin 벤더 관련 새창페이지
		case AdminDir."vender_branddup.php":
		case AdminDir."vender_calendar.detail.php":
		case AdminDir."vender_counsel_pop.php":
		case AdminDir."vender_detailpop.php":
		case AdminDir."vender_findpop.php":
		case AdminDir."vender_iddup.php":
		case AdminDir."vender_infopop.php":
		case AdminDir."vender_orderdetail.php":
			$vauthkey=getVenderUsed();
			if($vauthkey["OK"]!="OK") {
				//입점기능 사용 불가능
				alert_go('입점기능 및 미니샵은 몰인몰(E-market) 버전에서만 사용하실 수 있습니다.','c');
			} elseif($vauthkey["DOMAIN"]!=$_ShopInfo->getShopurl()) {
				//도메인 정보가 올바르지 않음
				alert_go('입점기능 인증키의 도메인정보가 잘못되어 이용하실 수 없습니다.','c');
			} elseif($vauthkey["DATE"]!="*" && $vauthkey["DATE"]<date("Ymd")) {
				//사용기간이 만료되었습니다.
				alert_go('입점기능 사용기간이 만료되어 이용하실 수 없습니다.','c');
			}
			break;
		// 사용자모드 front 벤더 관련 페이지
		case "minishop.php":
		case "minishop":
		case FrontDir."minishop.php":
		case FrontDir."minishop.notice.php":
		case FrontDir."minishop.productlist.php":
		case FrontDir."minishop.productdetail.php":
		case FrontDir."minishop.productsearch.php":
		case FrontDir."minishop.regist.pop.php":
			include_once($Dir."lib/shopdata.php");
			$vauthkey=getVenderUsed();
			if($vauthkey["OK"]!="OK" || $vauthkey["DOMAIN"]!=$_ShopInfo->getShopurl()) {
				if($_vscriptname==FrontDir."minishop.regist.pop.php") {
					echo "<html></head><body onload=\"window.close()\"></body></html>";exit;
				} else {
					header("Location:".$Dir);exit;
				}
			}
			break;
		// 사용자모드 vender 벤더 관련 페이지
		case VenderDir."login.php":
		case VenderDir."loginproc.php":
			include_once($Dir."lib/shopdata2.php");
			$vauthkey=getVenderUsed();
			if($vauthkey["OK"]!="OK") {
				//입점기능 사용 불가능
				alert_go("본 쇼핑몰에서는 입점기능을 사용하실 수 없습니다.\\n\\n쇼핑몰에 문의하시기 바랍니다.",$Dir);
			} elseif($vauthkey["DOMAIN"]!=$_ShopInfo->getShopurl()) {
				//도메인 정보가 올바르지 않음
				alert_go("본 쇼핑몰에서는 입점기능을 사용하실 수 없습니다.\\n\\n쇼핑몰에 문의하시기 바랍니다.",$Dir);
			} elseif($vauthkey["DATE"]!="*" && $vauthkey["DATE"]<date("Ymd")) {
				//사용기간이 만료되었습니다.
				alert_go("입점기능 사용기간이 만료되어 이용하실 수 없습니다.\\n\\n쇼핑몰에 문의하시기 바랍니다.",$Dir);
			}
			break;
		default :
			break;
	}
}


if(ord($_vscriptname)) {
	switch($_vscriptname) {
		case FrontDir."basket.php":
		case FrontDir."mypage.php":
		case FrontDir."mypage_coupon.php":
		case FrontDir."mypage_reserve.php":
			basket_restore();
			break;
		default :
			break;
	}
}

function utf8encode($str){
	return iconv('euc-kr','utf-8',$str);
}
### 디버그 함수
function debug($data)
{
	//print "<xmp style=\"font:8pt 'Courier New';background:#000000;color:#00ff00;padding:10\">";
	//print_r($data);
	//print "</xmp>";

    echo "<div style='background:#ffffff;text-align:left;'>";
    echo "<pre>"; print_r($data); echo "</pre>";
    echo "</div>";
}

### 디버그 함수
function sdebug($data)
{
	if($_SERVER["REMOTE_ADDR"] == '218.234.32.9'){
		print "<xmp style=\"font:8pt 'Courier New';background:#000000;color:#00ff00;padding:10\">";
		print_r($data);
		print "</xmp>";
	}
}

//허용 ip 셋팅
function isdev(){
	global $_SERVER;

	// 허용 ip 셋팅
	/*
	$agree_ip_arr[]="218.234.32.4";
    $agree_ip_arr[]="218.234.32.5";
	$agree_ip_arr[]="218.234.32.7";
	$agree_ip_arr[]="218.234.32.8";
	$agree_ip_arr[]="218.234.32.12";
	$agree_ip_arr[]="218.234.32.10";
	$agree_ip_arr[]="218.234.32.28";
	$agree_ip_arr[]="218.234.32.9";
	$agree_ip_arr[]="218.234.32.11";
	$agree_ip_arr[]="218.234.32.14";
	$agree_ip_arr[]="218.234.32.6";
	$agree_ip_arr[]="218.234.32.13";
	$agree_ip_arr[]="218.234.32.17";
	$agree_ip_arr[]="218.234.32.61";
	$agree_ip_arr[]="218.234.32.98";
	$agree_ip_arr[]="110.70.54.18";
	$agree_ip_arr[]="218.234.32.106";
	$agree_ip_arr[]="218.234.32.91";//박준호
	$agree_ip_arr[]="218.234.32.70"; //김재수 과장님
	*/
	$agree_ip_arr[]="218.234.32";
	$agree_flag = 0;
	foreach($agree_ip_arr as $agree_ip){
		/*
		if($_SERVER['REMOTE_ADDR']==$agree_ip){
			$agree_flag = 1;
		}
		*/
		# 218.234.32 IP로 변경 2015 11 24 유동혁
		if(substr($_SERVER['REMOTE_ADDR'],0,10) == substr($agree_ip,0,10)){
			$agree_flag = 1;
		}
	}

	if($agree_flag){
		return true;
	}
}

function exdebug($str){
	global $_SERVER;
	if(isdev()){
		debug($str);
	}
}


function get_microtime($old,$new)
{
	$old = explode(" ", $old);
	$new = explode(" ", $new);
	$time[msec] = $new[0] - $old[0];
	$time[sec]  = $new[1] - $old[1];
	if($time[msec] < 0) {
		$time[msec] = 1.0 + $time[msec];
		$time[sec]--;
	}
	$ret = $time[sec] + $time[msec];
	return $ret;
}

function log_txt_tmp($txt){
    $f = fopen("/tmp/sinwon_kcp_return_".session_id().".txt","a+");
    fwrite($f,$txt."\r\n");
    fwrite($f,"========================================================================"."\r\n");
    fclose($f);
    chmod("/tmp/sinwon_kcp_return_".session_id().".txt",0777);
}

function log_txt($txt){
	if($_SESSION['DEBUG']) {
		$f = fopen("/tmp/".session_id().".sql","a+");
		fwrite($f,$txt."\r\n");
		fclose($f);
		chmod("/tmp/".session_id().".sql",0777);
	}
}
function log_sql($fname, $query, $time){
	if($_SESSION['DEBUG']) {
		$time[] = microtime();
		$timediff = get_microtime($time[0],$time[1]);
		$f = fopen("/tmp/".session_id().".sql","a+");
		fwrite($f,"-- ".$fname."\r\n".$query."\r\n");
		if ($timediff > 0.05)
			fwrite($f,"===== [slow query] ".$timediff." ============================================================================="."\r\n\r\n");
		else
			fwrite($f,"\r\n");
		fclose($f);
		chmod("/tmp/".session_id().".sql",0777);
	}
}



function strcut_utf8($str, $len, $checkmb=false, $tail='') {
	/**
	 * UTF-8 Format
	 * 0xxxxxxx = ASCII, 110xxxxx 10xxxxxx or 1110xxxx 10xxxxxx 10xxxxxx
	 * latin, greek, cyrillic, coptic, armenian, hebrew, arab characters consist of 2bytes
	 * BMP(Basic Mulitilingual Plane) including Hangul, Japanese consist of 3bytes
	 **/
	preg_match_all('/[\xE0-\xFF][\x80-\xFF]{2}|./', $str, $match); // target for BMP

	$m = $match[0];
	$slen = strlen($str); // length of source string
	$tlen = strlen($tail); // length of tail string
	$mlen = count($m); // length of matched characters

	if ($slen <= $len) return $str;
	if (!$checkmb && $mlen <= $len) return $str;

	$ret = array();
	$count = 0;
	for ($i=0; $i < $len; $i++) {
		$count += ($checkmb && strlen($m[$i]) > 1)?2:1;
		if ($count + $tlen > $len) break;
		$ret[] = $m[$i];
	}

	return join('', $ret).$tail;
}

function strcutDot($str,$len)
{
	if (strlen($str) > $len){
		$len = $len-2;
		for ($pos=$len;$pos>0 && ord($str[$pos-1])>=127;$pos--);
		if (($len-$pos)%2 == 0) $str = mb_strcut($str, 0, $len, 'utf-8') . "..";
		else $str = mb_strcut($str, 0, $len+1, 'utf-8') . "..";
	}
	return $str;
}

### 문자열 자르기 함수 ... 추가
function strcutMbDot($str,$len)
{
	if (mb_strlen($str) > $len){
		$len = $len-2;
		for ($pos=$len;$pos>0 && ord($str[$pos-1])>=127;$pos--);
		if (($len-$pos)%2 == 0) $str = mb_substr($str, 0, $len, 'UTF-8') . "..";
		else $str = mb_substr($str, 0, $len+1, 'UTF-8') . "..";
	}
	return $str;
}

### 문자열 자르기 함수
### strcutMbDot는 이미 쓰는데가 있어서 하나 새로 만듬
### 일단 문자열을 자른뒤에 원래 문자열과 다를 경우에만 뒤에 '...' 추가
function strcutMbDot2($str,$len)
{
    $org_str = $str;

    $len = $len-2;
    for ($pos=$len;$pos>0 && ord($str[$pos-1])>=127;$pos--);
    if (($len-$pos)%2 == 0) $str = mb_substr($str, 0, $len, 'UTF-8');
    else $str = mb_substr($str, 0, $len+1, 'UTF-8');

    if ( $org_str != $str ) {
        $str .= "...";
    }

	return $str;
}

### datetime -> timestamp
function toTimeStamp($datetime){
	$y = substr($datetime,0,4);	$m = substr($datetime,5,2);	$d = substr($datetime,8,2);
	$h = substr($datetime,11,2);	$i = substr($datetime,14,2);	$s = substr($datetime,17,2);
	return mktime($h, $i, $s, $m, $d, $y);
}

function arrayNoticeLoop() {
	$sql = "SELECT * FROM tblboard where board='notice' AND pos = 0 AND depth = 0 ORDER BY thread , pos limit 5 offset 0 ";

	$result=pmysql_query($sql,get_db_conn());
	while($row=pmysql_fetch($result)) {
		$arrayLoop[] = $row;
	}

	/*
	$sql = "SELECT date,subject,access FROM tblnotice ORDER BY date DESC limit 5 offset 0 ";
	$result=pmysql_query($sql,get_db_conn());
	while($row=pmysql_fetch($result)) {
		$arrayLoop[] = $row;
	}
	*/
	return $arrayLoop;
}

function arrayBoardLoop($id, $num) {
	$sql = "SELECT * FROM tblboard where board='".$id."' AND pos = 0 AND depth = 0 ORDER BY thread , pos limit ".$num." offset 0 ";

	$result=pmysql_query($sql,get_db_conn());
	while($row=pmysql_fetch($result)) {
		$arrayLoop[] = $row;
	}

	return $arrayLoop;
}


function BeginTrans() {
	pmysql_query("BEGIN WORK");
}

function CommitTrans() {
	pmysql_query("COMMIT");
}

function RollbackTrans() {
	pmysql_query("ROLLBACK");
}


#### 도매등급 회원의 사이트 이동 처리 ####



//IF($_SERVER[REMOTE_ADDR]=='218.234.32.11'){
list($resetday_check)=pmysql_fetch_array(pmysql_query("select  resetday from tblmember WHERE id='".$_ShopInfo->getMemid()."'"));
/*
if($_ShopInfo->getMemid() && $_ShopInfo->wsmember=="Y" && $resetday_check!='1970-01-01 00:00:00' && strpos($_SERVER[DOCUMENT_ROOT],"soapschool/public")){

	setLogOut();
	alert_go("회원님의 도매등급입니다. 도매 전용 쇼핑몰로 이동합니다.", "http://ecofactoryk.co.kr");
	exit;
}else if($_ShopInfo->getMemid() && strpos($_SERVER[DOCUMENT_ROOT],"soapschool2/public")){

	if($_ShopInfo->wsmember=="N"){
		setLogOut();
		alert_go("회원님은 일반등급입니다. 일반 전용 쇼핑몰로 이동합니다.", "http://soapschool.co.kr");
		exit;

	}else if($_ShopInfo->wsmember=="Y" && $resetday_check=='1970-01-01 00:00:00'){
		setLogOut();
		alert_go("회원님은 도매 승인이 되지않았습니다. 일반 전용 쇼핑몰로 이동합니다.", "http://soapschool.co.kr");
		exit;
	}

}
*/


if($_ShopInfo->getMemid() && $_ShopInfo->wsmember=="Y" && strpos($_SERVER[DOCUMENT_ROOT],"soapschool/public")){

	setLogOut();
	alert_go("회원님의 도매등급입니다. 도매 전용 쇼핑몰로 이동합니다.", "http://ecofactoryk.co.kr");
	exit;
}else if($_ShopInfo->getMemid() && $_ShopInfo->wsmember=="N" && strpos($_SERVER[DOCUMENT_ROOT],"soapschool2/public")){
	setLogOut();
	alert_go("회원님은 일반등급입니다. 일반 전용 쇼핑몰로 이동합니다.", "http://soapschool.co.kr");
	exit;
}
/*

}else{


	if($_ShopInfo->getMemid() && $_ShopInfo->wsmember=="Y" && strpos($_SERVER[DOCUMENT_ROOT],"soapschool/public")){

		setLogOut();
		alert_go("회원님의 도매등급입니다. 도매 전용 쇼핑몰로 이동합니다.", "http://ecofactoryk.co.kr");
		exit;
	}else if($_ShopInfo->getMemid() && $_ShopInfo->wsmember=="N" && strpos($_SERVER[DOCUMENT_ROOT],"soapschool2/public")){
		setLogOut();
		alert_go("회원님은 일반등급입니다. 일반 전용 쇼핑몰로 이동합니다.", "http://soapschool.co.kr");
		exit;
	}

}
*/

#### 도매회원 이동을 위한 로그아웃 처리 ####
function setLogOut(){
	global $_ShopInfo;
	$sql = "UPDATE tblmember SET authidkey='logout' WHERE id='".$_ShopInfo->getMemid()."' ";
	pmysql_query($sql,get_db_conn());
	$_ShopInfo->SetMemNULL();
	$_ShopInfo->Save();
}

function round_half_down($num,$precision)
{
    $offset = '0.';
    for($i=0; $i < $precision; $i++)
    {
        $offset = $offset.'0';
    }

    $offset =  floatval($offset.'1');
    $num = $num - $offset;
    $num = round($num, $precision);

    return $num;
}

function getProductDcPrice($price,$dc_rate){
	$dc_price = $price;
	if(strpos($dc_rate,"%")){
		//$dc_price = round($dc_price*$dc_rate/100,-1,PHP_ROUND_HALF_DOWN);
		$dc_price = round_half_down($dc_price*$dc_rate/100, -1);
		return $dc_price;
	}else{
		$dc_price=$ $dc_rate;
	}
	return $dc_price;
}
//오늘의 특가, 타임세일 상품 가격
// 2016-05-19 사용안함 - 유동혁
function getSpeDcPrice($productcode=''){
	if($productcode){
		$today_date = date("Y-m-d H:i:s");
		$sql_time = "SELECT s_price FROM tbl_timesale_list ";
		$sql_time.= "WHERE productcode='{$productcode}' ";
		$sql_time.= "AND sdate<='{$today_date}' AND edate>='{$today_date}' ";
		list($price) = pmysql_fetch(pmysql_query($sql_time));

		if(!$price){
			$today_date = date("Y-m-d");
			$sql_one = "SELECT dcprice FROM tblproductoneday ";
			$sql_one.= "WHERE productcode='{$productcode}' ";
			$sql_one.= "AND applydate='{$today_date}' ";
			list($price) = pmysql_fetch(pmysql_query($sql_one));
		}
	}
	return $price;
}

function getProductSalePrice($price,$dc_rate){
	$dc_price = $price;
	if(strpos($dc_rate,"%")){
		//$dc_price = $dc_price - round($dc_price*$dc_rate/100,-1,PHP_ROUND_HALF_DOWN);
		$dc_price = $dc_price - round_half_down($dc_price*$dc_rate/100, -1);
		return $dc_price;
	}else{
		$dc_price=$dc_price - $dc_rate;
	}
	return $dc_price;
}

//할인율 구하는 함수
function getDcRate($consumer=0,$sell=0){
	if($consumer&&$sell){
		$rate = floor((($consumer-$sell)/$consumer)*100);
	}elseif($consumer){
		$rate = "100";
	}
	return $rate;
}

# 주문 수량 복구
function order_recovery_quantity($ordercode, $idx=''){ //수량복구 실행 //상품코드 추가 (2015.11.21 - 김재수)
	$sql = "SELECT a.productcode,a.productname,a.opt1_name,a.opt2_name,a.quantity, ";
	$sql.= "a.package_idx, a.assemble_idx, a.assemble_info, ";
	$sql.= "a.opt1_name, a.opt2_name, a.option_quantity, a.option_type, b.quantity AS pr_quantity ";
	$sql.= "FROM tblorderproduct a, tblproduct b "; //b.option_quantity,b.option_ea,b.option1,b.option2
	$sql.= "WHERE a.productcode=b.productcode AND a.ordercode='{$ordercode}' ";
	if ( $idx ) $sql.= "AND a.idx='{$idx}' "; // 주문 인덱스가 있을 경우 유동혁 2016-02-04
	$sql.= "AND NOT ( a.productcode LIKE '999%' OR a.productcode LIKE 'COU%') ";   //2014-04-30 김태영 추가.
	$result=pmysql_query($sql,get_db_conn());
	$message="";

	//로그 저장
	$savetemp = "====================".date("Y-m-d H:i:s")."====================\n";
	$savetemp.= "주문 코드 : ".$ordercode."\n";
	while ($row=pmysql_fetch_object($result)) {
		//상품 로그
		$savetemp.= "상품 코드 : ".$row->productcode."\n";
		$savetemp.= "상품명    : ".$row->productname."\n";
		$savetemp.= "상품 옵션1: ".$row->opt1_name."\n";
		$savetemp.= "상품 옵션2: ".$row->opt2_name."\n";

		$prSelectSql = "SELECT quantity FROM tblproduct WHERE productcode = '".$row->productcode."' ";
		$prSelectRes = pmysql_query( $prSelectSql, get_db_conn() );
		$prSelectRow = pmysql_fetch_object( $prSelectRes );
		if( $prSelectRow->quantity < 999999999 ){ // 수량이 무제한이 아니면
			# 옵션 수량 복구
			# 2015 11 21 유동혁
			if( strlen($row->opt1_name) > 0 && $row->option_quantity > 0 ){

				//$tmpOption1Code = explode( '::', $row->opt1_name );
				//$tmpOption2Code = explode( '::', $row->opt2_name );

				if( $row->option_type == '1'){
					$tmpOption2Code = explode( chr(30), $row->opt2_name );

					#옵션 수량 체크
					foreach( $tmpOption2Code as $opt2Key=>$opt2Val ){
						$optionSelectSql = "SELECT option_quantity FROM tblproduct_option WHERE productcode = '".$row->productcode."' AND option_code = '".$opt2Val."' ";
						$optionSelectRes = pmysql_query( $optionSelectSql, get_db_conn() );
						$optionSelectRow = pmysql_fetch_object( $optionSelectRes );
						$savetemp.= "옵션 수량 : [".$opt2Val." 변경전] ".$optionSelectRow->option_quantity."\n";

						$optionQuantityDownSql = "UPDATE tblproduct_option SET option_quantity = option_quantity + {$row->option_quantity} ";
						$optionQuantityDownSql.= "WHERE productcode = '".$row->productcode."' AND option_code = '".$opt2Val."' ";
						pmysql_query( $optionQuantityDownSql, get_db_conn() );
						$savetemp.= "옵션 수량 : [".$opt2Val." 변경후] ".($optionSelectRow->option_quantity - $row->option_quantity)."\n";
						//unset($tmpOption1Code);
						unset($tmpOption2Code);
						pmysql_free_result( $optionSelectRes );
					}

				} else {
					//$tmpOpCode = $row->opt1_name.chr(30).$row->opt2_name;
					$tmpOpCode = $row->opt2_name;
					#옵션 수량 체크
					$optionSelectSql = "SELECT option_quantity FROM tblproduct_option WHERE productcode = '".$row->productcode."' AND option_code = '".$tmpOpCode."' ";
					$optionSelectRes = pmysql_query( $optionSelectSql, get_db_conn() );
					$optionSelectRow = pmysql_fetch_object( $optionSelectRes );
					$savetemp.= "옵션 수량 : [변경전] ".$optionSelectRow->option_quantity."\n";

					$optionQuantityDownSql = "UPDATE tblproduct_option SET option_quantity = option_quantity + {$row->option_quantity} ";
					$optionQuantityDownSql.= "WHERE productcode = '".$row->productcode."' AND option_code = '".$tmpOpCode."' ";
					pmysql_query( $optionQuantityDownSql, get_db_conn() );
					$savetemp.= "옵션 수량 : [변경후] ".($optionSelectRow->option_quantity - $row->option_quantity)."\n";
					//unset($tmpOption1Code);
					//unset($tmpOption2Code);
					pmysql_free_result( $optionSelectRes );
				}
			}
			# 상품 수량 복구
			$savetemp.= "상품 수량 : [변경전] ".$prSelectRow->quantity."\n";

			$sql = "UPDATE tblproduct SET quantity=quantity+".$row->quantity." WHERE productcode='{$row->productcode}'";
			pmysql_query($sql,get_db_conn());

			$savetemp.= "상품 수량 : [변경후] ".( $row->quantity + $prSelectRow->quantity )."\n";
			$savetemp.= "\n";
		} else {
			$savetemp.= "무제한 수량";
		}
		pmysql_free_result( $prSelectRes );
	}
	pmysql_free_result($result);
	// 수량복구 로그
	$savetemp.= "\n";
	$file = DirPath.DataDir."backup/order_recovery_quantity_".date("Y")."_".date("m")."_".date("d")."_"."00______.txt";
	if(!is_file($file)){
		$f = fopen($file,"a+");
		fclose($f);
		chmod($file,0777);
	}
	file_put_contents($file,$savetemp,FILE_APPEND);
}

#주문수량 처리
# 2015 11 21 유동혁
function order_quantity( $ordercode, $idx='' ){
	$sql = "SELECT a.productcode,a.productname,a.opt1_name,a.opt2_name,a.quantity, ";
	$sql.= "a.package_idx, a.assemble_idx, a.assemble_info, ";
	$sql.= "a.opt1_name, a.opt2_name, a.option_quantity, a.option_type, b.quantity AS pr_quantity ";
	$sql.= "FROM tblorderproduct a, tblproduct b "; //b.option_quantity,b.option_ea,b.option1,b.option2
	$sql.= "WHERE a.productcode=b.productcode AND a.ordercode='{$ordercode}' ";
	if ( $idx ) $sql.= "AND a.idx='{$idx}' "; // 상품코드가 있을 경우 (2015.11.21 - 김재수)
	$sql.= "AND NOT ( a.productcode LIKE '999%' OR a.productcode LIKE 'COU%') ";   //2014-04-30 김태영 추가.
	$result=pmysql_query($sql,get_db_conn());
	$message="";

	//로그 저장
	$savetemp = "====================".date("Y-m-d H:i:s")."====================\n";
	$savetemp.= "주문 코드 : ".$ordercode."\n";
	while ($row=pmysql_fetch_object($result)) {

		//상품 로그
		$savetemp.= "상품 코드 : ".$row->productcode."\n";
		$savetemp.= "상품명    : ".$row->productname."\n";
		$savetemp.= "상품 옵션1: ".$row->opt1_name."\n";
		$savetemp.= "상품 옵션2: ".$row->opt2_name."\n";

		$prSelectSql = "SELECT quantity FROM tblproduct WHERE productcode = '".$row->productcode."' ";
		$prSelectRes = pmysql_query( $prSelectSql, get_db_conn() );
		$prSelectRow = pmysql_fetch_object( $prSelectRes );
		if( $prSelectRow->quantity < 999999999 ){ // 수량이 무제한이 아니면
			# 옵션 수량
			# 2015 11 21 유동혁
			if( strlen($row->opt1_name) > 0 && $row->option_quantity > 0 ){

				if( $row->option_type == '1'){
					//$tmpOption1Code = explode( '@#', $row->opt1_name );
					$tmpOption2Code = explode( chr(30), $row->opt2_name );

					#옵션 수량 체크
					foreach( $tmpOption2Code as $opt2Key=>$opt2Val ){
						$optionSelectSql = "SELECT option_quantity FROM tblproduct_option WHERE productcode = '".$row->productcode."' AND option_code = '".$opt2Val."' ";
						$optionSelectRes = pmysql_query( $optionSelectSql, get_db_conn() );
						$optionSelectRow = pmysql_fetch_object( $optionSelectRes );
						$savetemp.= "옵션 수량 : [".$opt2Val." 변경전] ".$optionSelectRow->option_quantity."\n";

						$optionQuantityDownSql = "UPDATE tblproduct_option SET option_quantity = option_quantity - {$row->option_quantity} ";
						$optionQuantityDownSql.= "WHERE productcode = '".$row->productcode."' AND option_code = '".$opt2Val."' ";
						pmysql_query( $optionQuantityDownSql, get_db_conn() );
						$savetemp.= "옵션 수량 : [".$opt2Val." 변경후] ".($optionSelectRow->option_quantity - $row->option_quantity)."\n";
						//unset($tmpOption1Code);
						unset($tmpOption2Code);
						pmysql_free_result( $optionSelectRes );
					}
				} else {
					//$tmpOpCode = $row->opt1_name.chr(30).$row->opt2_name;
					$tmpOpCode = $row->opt2_name;
					#옵션 수량 체크
					$optionSelectSql = "SELECT option_quantity FROM tblproduct_option WHERE productcode = '".$row->productcode."' AND option_code = '".$tmpOpCode."' ";
					$optionSelectRes = pmysql_query( $optionSelectSql, get_db_conn() );
					$optionSelectRow = pmysql_fetch_object( $optionSelectRes );
					$savetemp.= "옵션 수량 : [변경전] ".$optionSelectRow->option_quantity."\n";

					$optionQuantityDownSql = "UPDATE tblproduct_option SET option_quantity = option_quantity - {$row->option_quantity} ";
					$optionQuantityDownSql.= "WHERE productcode = '".$row->productcode."' AND option_code = '".$tmpOpCode."' ";
					pmysql_query( $optionQuantityDownSql, get_db_conn() );
					$savetemp.= "옵션 수량 : [변경후] ".($optionSelectRow->option_quantity - $row->option_quantity)."\n";
					//unset($tmpOption1Code);
					//unset($tmpOption2Code);
					pmysql_free_result( $optionSelectRes );
				}

			}
			# 상품 수량 차감
			$savetemp.= "상품 수량 : [변경전] ".$prSelectRow->quantity."\n";

			$sql = "UPDATE tblproduct SET quantity=quantity-".$row->quantity." WHERE productcode='{$row->productcode}'";
			pmysql_query($sql,get_db_conn());

			$savetemp.= "상품 수량 : [변경후] ".( $prSelectRow->quantity - $row->quantity )."\n";
			$savetemp.= "\n";
		}  else {
			$savetemp.= "무제한 수량";
		}
		pmysql_free_result( $prSelectRes );

	}
	pmysql_free_result($result);
	// 수량차감 로그
	$savetemp.= "\n";
	$file = DirPath.DataDir."backup/order_quantity_".date("Y")."_".date("m")."_".date("d")."_"."00______.txt";
	if(!is_file($file)){
		$f = fopen($file,"a+");
		fclose($f);
		chmod($file,0777);
	}
	file_put_contents($file,$savetemp,FILE_APPEND);

}

//오늘본상품
function today_product(){
	global $_COOKIE;

    // 쿠키값 가져오기
    $cookieVal  = trim($_COOKIE['ViewProduct'],',');    //(,상품코드1||본시각,상품코드2||본시각,상품코드3||본시각,) 형식으로
	$prdt_list  = explode(",", $cookieVal);

    // 상품코드만 찾아내기
    $arrProdCode = array();
    for ($i = 0; $i < count($prdt_list); $i++) {
        $arrTmp = explode("||", $prdt_list[$i]);
        array_push($arrProdCode, $arrTmp[0]);
    }

    // 상품리스트를 구해서 return
    $productall = get_product_list($arrProdCode, true);

    return $productall;
}

function get_product_list( $prdt_list, $mode = false ) { //pridx추가 (원재) / 최저가, 교육 할인가 추가 (2015.11.04 - 김재수)

	$productall = array();
    if ( count($prdt_list) == 0 ) {
        return $productall;
    }

    // 상품 중복을 제거한다.
    $prdt_list = array_unique($prdt_list);

	$prdt_no=count($prdt_list);
	if(ord($prdt_no)==0) {
		$prdt_no=0;
	}

	$tmp_product="";

    foreach ( $prdt_list as $key => $val ) {
		$tmp_product.="'{$val}',";
    }

	$tmp_product=rtrim($tmp_product,',');

    $sql  = "SELECT tblResult.*, ( SELECT brandname FROM tblproductbrand WHERE bridx = tblResult.brand ) as brandname ";
	$sql .= "FROM ( SELECT * FROM tblproduct ";
	$sql .= "WHERE productcode IN ({$tmp_product}) ";
    if ( $mode ) {
        $sql .= "AND display = 'Y' ";
    }
	$sql .= "ORDER BY FIELD(productcode,{$tmp_product}) ) AS tblResult ";

	$result=pmysql_query($sql,get_db_conn());
	$jj=0;
	while($row=pmysql_fetch_object($result)) {
		$productall[$jj]["code"]=$row->productcode;
		$productall[$jj]["name"]=$row->productname;
		$productall[$jj]["image"]=$row->tinyimage;
		$productall[$jj]["quantity"]=$row->quantity;
		$productall[$jj]["pridx"]=$row->pridx;
		$productall[$jj]["consumerprice"]=$row->consumerprice;	// 최저가
		$productall[$jj]["sellprice"]=$row->sellprice;			// 교육 할인가
		$productall[$jj]["brandname"]=$row->brandname;	        // 브랜드명

		$productall[$jj]["icon"]=$row->icon;    	            // 아이콘
		$productall[$jj]["soldout"]=$row->soldout;	            // 품절여부
		$productall[$jj]["over_minimage"]=$row->over_minimage;	// 롤오버이미지
		$jj++;
	}

	return $productall;
}


function mainBannerList($banner_name='',$banner_category=''){

	global $Dir;

	$banner_url = $Dir.DataDir."shopimages/mainbanner/";
	$mb_qry = "select * from tblmainbannerimg where banner_hidden='1' ";
	if($banner_name){
		$mb_qry.= "AND banner_name='{$banner_name}' ";
	}
	if($banner_category){
		$mb_qry.= "AND banner_category like '{$banner_category}%' ";
	}
	$mb_qry.= "order by banner_sort";

	$mb_result=pmysql_query($mb_qry);
	while($mb_data=pmysql_fetch_object($mb_result)){
		if(file_exists($banner_url.$mb_data->banner_img)){

				$mainbanner[$mb_data->banner_name][$mb_data->banner_sort]["banner_img"]=$banner_url.$mb_data->banner_img;
				$mainbanner[$mb_data->banner_name][$mb_data->banner_sort]["banner_title"]=$mb_data->banner_title;
				$mainbanner[$mb_data->banner_name][$mb_data->banner_sort]["banner_hidden"]=$mb_data->banner_hidden;
				$mainbanner[$mb_data->banner_name][$mb_data->banner_sort]["banner_category"]=$mb_data->banner_category;
				$mainbanner[$mb_data->banner_name][$mb_data->banner_sort]["banner_link"]=$mb_data->banner_link;
				$mainbanner[$mb_data->banner_name][$mb_data->banner_sort]["banner_number"]=$mb_data->banner_number;
				$mainbanner[$mb_data->banner_name][$mb_data->banner_sort]["banner_t_link"]=$mb_data->banner_t_link;
				$mainbanner[$mb_data->banner_name][$mb_data->banner_sort]["banner_link_m"]=$mb_data->banner_link_m;
				$mainbanner[$mb_data->banner_name][$mb_data->banner_sort]["banner_no"]=$mb_data->no;		}
	}

	return $mainbanner;
}



function homeBannerList($banner_name='',$banner_category=''){

	global $Dir;

	$banner_url = $Dir.DataDir."shopimages/homebanner/";
	$mb_qry = "select * from tblhomebannerimg where banner_hidden='1' ";
	if($banner_name){
		$mb_qry.= "AND banner_name='{$banner_name}' ";
	}
	$mb_qry.= "order by banner_sort";

	$mb_result=pmysql_query($mb_qry);
	while($mb_data=pmysql_fetch_object($mb_result)){
		if(file_exists($banner_url.$mb_data->banner_img)){
			$mainbanner[$mb_data->banner_name][$mb_data->banner_sort]["banner_img"]=$banner_url.$mb_data->banner_img;
			$mainbanner[$mb_data->banner_name][$mb_data->banner_sort]["banner_img_title_on"]=$banner_url.$mb_data->banner_img_title_on;
			$mainbanner[$mb_data->banner_name][$mb_data->banner_sort]["banner_img_title_out"]=$banner_url.$mb_data->banner_img_title_out;
			$mainbanner[$mb_data->banner_name][$mb_data->banner_sort]["banner_hidden"]=$mb_data->banner_hidden;
			$mainbanner[$mb_data->banner_name][$mb_data->banner_sort]["banner_link"]=$mb_data->banner_link;
			$mainbanner[$mb_data->banner_name][$mb_data->banner_sort]["banner_number"]=$mb_data->banner_number;
			$mainbanner[$mb_data->banner_name][$mb_data->banner_sort]["banner_no"]=$mb_data->no;
		}
	}

	return $mainbanner;
}


function brandMainTopBanner($brandcd=''){

	global $Dir;
	if($brandcd){
		$banner_url = $Dir.DataDir."/shopimages/mainbanner/";
		$mb_qry="SELECT * from tblmainbannerimg
					WHERE banner_hidden='1'
					AND banner_name='listmain_rolling'
					AND banner_category='{$brandcd}' order by banner_sort";

		$mb_result=pmysql_query($mb_qry);
		while($mb_data=pmysql_fetch_object($mb_result)){
			if(file_exists($banner_url.$mb_data->banner_img)&&$mb_data->banner_img){

					$mainbanner[$mb_data->banner_name][$mb_data->banner_sort]["banner_img"]=$banner_url.$mb_data->banner_img;
					$mainbanner[$mb_data->banner_name][$mb_data->banner_sort]["banner_title"]=$mb_data->banner_title;
					$mainbanner[$mb_data->banner_name][$mb_data->banner_sort]["banner_hidden"]=$mb_data->banner_hidden;
					$mainbanner[$mb_data->banner_name][$mb_data->banner_sort]["banner_category"]=$mb_data->banner_category;
					$mainbanner[$mb_data->banner_name][$mb_data->banner_sort]["banner_link"]=$mb_data->banner_link;
					$mainbanner[$mb_data->banner_name][$mb_data->banner_sort]["banner_number"]=$mb_data->banner_number;
					$mainbanner[$mb_data->banner_name][$mb_data->banner_sort]["banner_t_link"]=$mb_data->banner_t_link;
					$mainbanner[$mb_data->banner_name][$mb_data->banner_sort]["banner_no"]=$mb_data->no;
			}
		}
	}

	return $mainbanner;
}

function cateMainTopBanner($catecd=''){

	global $Dir;
	if($catecd){
		$banner_url = $Dir.DataDir."shopimages/mainbanner/";
		$mb_qry="SELECT * from tblmainbannerimg
					WHERE banner_hidden='1'
					AND banner_name='listmain_rolling'
					AND banner_category like '{$catecd}%' order by banner_sort";

		$mb_result=pmysql_query($mb_qry);
		while($mb_data=pmysql_fetch_object($mb_result)){
			if(file_exists($banner_url.$mb_data->banner_img)&&$mb_data->banner_img){

					$mainbanner[$mb_data->banner_name][$mb_data->banner_sort]["banner_img"]=$banner_url.$mb_data->banner_img;
					$mainbanner[$mb_data->banner_name][$mb_data->banner_sort]["banner_title"]=$mb_data->banner_title;
					$mainbanner[$mb_data->banner_name][$mb_data->banner_sort]["banner_hidden"]=$mb_data->banner_hidden;
					$mainbanner[$mb_data->banner_name][$mb_data->banner_sort]["banner_category"]=$mb_data->banner_category;
					$mainbanner[$mb_data->banner_name][$mb_data->banner_sort]["banner_link"]=$mb_data->banner_link;
					$mainbanner[$mb_data->banner_name][$mb_data->banner_sort]["banner_number"]=$mb_data->banner_number;
					$mainbanner[$mb_data->banner_name][$mb_data->banner_sort]["banner_t_link"]=$mb_data->banner_t_link;
					$mainbanner[$mb_data->banner_name][$mb_data->banner_sort]["banner_no"]=$mb_data->no;
			}
		}
	}

	return $mainbanner;
}

# 배너 가져오기 2016-02-12 유동혁
function get_banner( $banner_no = '', $banner_category = '' )
{
	global $Dir;

	$banner_url = $Dir.DataDir."shopimages/mainbanner/";

	$mb_qry = "SELECT no, banner_no, banner_img, banner_sort, ";
	$mb_qry.= "banner_date, banner_title, banner_link, banner_hidden, ";
	$mb_qry.= "banner_number, banner_name, banner_category, banner_t_link, ";
	$mb_qry.= "banner_n_link, banner_type, banner_target, banner_img_m, ";
	$mb_qry.= "banner_subname, banner_title_color, banner_name_color, banner_subname_color, ";
	$mb_qry.= "banner_subname2, banner_subname_color2 ";
	$mb_qry.=  "FROM tblmainbannerimg WHERE banner_hidden='1' ";
	if( strlen( $banner_no ) > 0 ){
		$mb_qry.= "AND banner_no='{$banner_no}' ";
	}
	if( strlen( $banner_category ) > 0 ){
		$mb_qry.= "AND banner_category like '{$banner_category}%' ";
	}
	$mb_qry.= "order by banner_sort";

	$mb_result = pmysql_query( $mb_qry );
	while( $mb_data=pmysql_fetch_object( $mb_result ) ){
		if( file_exists( $banner_url.$mb_data->banner_img ) ) {
			$mb_data->banner_img = $banner_url.$mb_data->banner_img;
			if( file_exists( $banner_url.$mb_data->banner_img_m ) ) {
				$mb_data->banner_img_m = $banner_url.$mb_data->banner_img_m;
			} else {
				$mb_data->banner_img_m = $banner_url.$mb_data->banner_img;
			}
			$mainbanner[$mb_data->banner_no][$mb_data->banner_sort] = (array) $mb_data;
		}
	}
	return $mainbanner;

}

function top_category(){

	$cate_a="select * from tblproductcode where code_b='000' order by code_a";
	$cate_a_res=pmysql_query($cate_a);

	while($cate_a_row=pmysql_fetch_array($cate_a_res)){

		$qry="select code_a,code_b,code_name from tblproductcode where group_code='' and code_a='".$cate_a_row[code_a]."' and code_b!='000' and code_c='000' order by cate_sort";

		$res=pmysql_query($qry);

		while($row=pmysql_fetch_array($res)){
			$data[$row[code_a]][]=$row;
		}
	}

	return $data;

}

##### 해당 카테고리의 상품들

##### 메인진열 상품들
function main_disp_goods(){
	$qry="select * from tblspecialmain order by special";
	$res=pmysql_query($qry);

	while($row=pmysql_fetch_array($res)){
		$prd_code=explode(",",$row['special_list']);

		for($i=0;$i<count($prd_code);$i++){
			$pqry="select * from tblproduct where productcode='".$prd_code[$i]."' ";
			//debug($pqry);
			$pres=pmysql_query($pqry);
			$prow=pmysql_fetch_array($pres);

			##### 쿠폰에 의한 가격 할인
			$cou_data = couponDisPrice($prow['productcode']);
			if($cou_data['coumoney']){
				$m_nomal_price = $prow['sellprice'];
				$prow['sellprice'] = $prow['sellprice']-$cou_data['coumoney'];
				$prow['dc_type'] = $cou_data['goods_sale_type'];
			}
			##### 쿠폰에 의한 가격 할인

			#####즉시적립금 할인 적용가 150901원재

			if($prow['reserve']>0){
				$ReserveConversionPrice = 0;
				$ReserveConversionPrice = getReserveConversion($prow['reserve'], $prow['reservetype'],$m_nomal_price,'Y');
				$prow['sellprice'] = $prow['sellprice'] - $ReserveConversionPrice;
			}

				#####//즉시적립금 할인 적용가

			##### 오늘의 특가, 타임세일에 의한 가격
			$spesell = getSpeDcPrice($prow['productcode']);
			if($spesell){
				$prow['sellprice'] = $spesell;
			}
			##### //오늘의 특가, 타임세일에 의한 가격


			$data[$row['special']][]=$prow;
		}
	}
	return $data;
}

##### 모바일 진열 상품들
function mobile_disp_goods(){
	$qry="select * from tblnesignmobile order by special";
	$res=pmysql_query($qry);

	while($row=pmysql_fetch_array($res)){
		$prd_code=explode(",",$row['special_list']);

		for($i=0;$i<count($prd_code);$i++){
			$pqry="select * from tblproduct where productcode='".$prd_code[$i]."' ";
			//debug($pqry);
			$pres=pmysql_query($pqry);
			$prow=pmysql_fetch_array($pres);

			##### 쿠폰에 의한 가격 할인
			$cou_data = couponDisPrice($prow['productcode']);
			if($cou_data['coumoney']){
				$prow['sellprice'] = $prow['sellprice']-$cou_data['coumoney'];
				$prow['dc_type'] = $cou_data['goods_sale_type'];
			}
			##### 쿠폰에 의한 가격 할인

			##### 오늘의 특가, 타임세일에 의한 가격
			$spesell = getSpeDcPrice($prow['productcode']);
			if($spesell){
				$prow['sellprice'] = $spesell;
			}
			##### //오늘의 특가, 타임세일에 의한 가격


			$data[$row['special']][]=$prow;
		}
	}
	return $data;
}
##### 브랜드 카테고리 진열상품
function brand_disp_goods($brandcd=''){
	if(!$brandcd)$brandcd="004001";
	$where = "where code like '{$brandcd}%'";
	$qry="select * from tblspecialcode {$where} order by special";
	$res=pmysql_query($qry);

	while($row=pmysql_fetch_array($res)){
		$prd_code=explode(",",$row['special_list']);

		for($i=0;$i<count($prd_code);$i++){
			$pqry="select * from tblproduct where productcode='".$prd_code[$i]."' ";
			//debug($pqry);
			$pres=pmysql_query($pqry);
			$prow=pmysql_fetch_array($pres);

			##### 쿠폰에 의한 가격 할인
			$cou_data = couponDisPrice($prow['productcode']);
			if($cou_data['coumoney']){
				$prow['sellprice'] = $prow['sellprice']-$cou_data['coumoney'];
				$prow['dc_type'] = $cou_data['goods_sale_type'];
			}
			##### 쿠폰에 의한 가격 할인

			##### 오늘의 특가, 타임세일에 의한 가격
			$spesell = getSpeDcPrice($prow['productcode']);
			if($spesell){
				$prow['sellprice'] = $spesell;
			}
			##### //오늘의 특가, 타임세일에 의한 가격


			$data[$row['special']][]=$prow;
		}
	}
	return $data;
}

##### 카테고리별 진열상품
function specail_disp_goods($catecode=''){
	if($catecode){
		$where = "where code like '{$catecode}%'";
		$qry="select * from tblspecialcode {$where} order by special";
		$res=pmysql_query($qry);

		while($row=pmysql_fetch_array($res)){
			$prd_code=explode(",",$row['special_list']);

			for($i=0;$i<count($prd_code);$i++){
				$pqry="select * from tblproduct where productcode='".$prd_code[$i]."' ";
				//debug($pqry);
				$pres=pmysql_query($pqry);
				$prow=pmysql_fetch_array($pres);

				##### 쿠폰에 의한 가격 할인
				$cou_data = couponDisPrice($prow['productcode']);
				if($cou_data['coumoney']){
					$prow['sellprice'] = $prow['sellprice']-$cou_data['coumoney'];
					$prow['dc_type'] = $cou_data['goods_sale_type'];
				}
				##### 쿠폰에 의한 가격 할인

				##### 오늘의 특가, 타임세일에 의한 가격
				$spesell = getSpeDcPrice($prow['productcode']);
				if($spesell){
					$prow['sellprice'] = $spesell;
				}
				##### //오늘의 특가, 타임세일에 의한 가격


				$data[$row['special']][]=$prow;
			}
		}
	}
	return $data;
}

##### 하위 카테고리에 있는 특별 진열 상품도 가져옴
##### 해당 카테고리에 속한 진열 상품이 있을경우 먼저 가져오고
##### 하위 카테고리에 속한 진열 상품은 카테고리별 우선순위부터 가져온다.
##### 예) A 카테고리에 1,2,3,4 가 있고, A1카테고리에 1,2,3,4, 가 있고 A2 카테고리에 1,2,3,4가 있을 경우
##### A1,A2,A3,A4,A-1,B-1,A-2,B-2,.... 의 순서로 상품을 가져옴
function special_disp_goods_sub($catecode=''){
	global $item_cate;

	####아이템별, 브랜드별 검색을 위한 where절
	if($item_cate)	{
		$prd_where = "itemcate={$item_cate}";
	}
	/*
	if($brand){
		$prd_where_arr[] = "brand={$brand}";
	}
	if(count($prd_where_arr)){
		$prd_where = implode(" AND ",$prd_where_arr);
	}
	*/

	if($catecode){
		### 해당 카테고리의 특별 진열 상품정보
		$full_catecode = str_pad($catecode,12,"0",STR_PAD_RIGHT);
		$where = "where code = '{$full_catecode}'";
		$qry="select * from tblspecialcode {$where} order by special";
		$res=pmysql_query($qry);

		while($row=pmysql_fetch_array($res)){
			$prd_code=explode(",",$row['special_list']);

			for($i=0;$i<count($prd_code);$i++){
				$pqry = "SELECT * FROM tblproduct where productcode='".$prd_code[$i]."' ";
				$pqry.= "AND (quantity != 0 OR quantity = null) AND display='Y'";
				if($prd_where){
					$pqry.= "AND ".$prd_where." ";
				}
				$pres=pmysql_query($pqry);
				if(pmysql_num_rows($pres)){
					$prow=pmysql_fetch_array($pres);

					##### 쿠폰에 의한 가격 할인
					$cou_data = couponDisPrice($prow['productcode']);
					if($cou_data['coumoney']){
						$prow['sellprice'] = $prow['sellprice']-$cou_data['coumoney'];
						$prow['dc_type'] = $cou_data['goods_sale_type'];
					}
					##### 쿠폰에 의한 가격 할인

					##### 오늘의 특가, 타임세일에 의한 가격
					$spesell = getSpeDcPrice($prow['productcode']);
					if($spesell){
						$prow['sellprice'] = $spesell;
					}
					##### //오늘의 특가, 타임세일에 의한 가격

					$data[$row['special']][]=$prow;
				}
			}
		}


		### 하위 카테고리의 특별 진열 상품 정보
		$where = "where code like '{$catecode}%' AND code != '{$full_catecode}'";
		$qry="select * from tblspecialcode {$where} order by special";
		$res=pmysql_query($qry);

		while($row=pmysql_fetch_array($res)){
			$prd_code=explode(",",$row['special_list']);

			for($i=0;$i<count($prd_code);$i++){
				$pqry = "select * from tblproduct where productcode='".$prd_code[$i]."' ";
				$pqry.= "AND (quantity != 0 OR quantity = null) AND display='Y'";
				if($prd_where){
					$pqry.= "AND ".$prd_where." ";
				}
				$pres=pmysql_query($pqry);
				if(pmysql_num_rows($pres)){
					$prow=pmysql_fetch_array($pres);

					##### 쿠폰에 의한 가격 할인
					$cou_data = couponDisPrice($prow['productcode']);
					if($cou_data['coumoney']){
						$prow['sellprice'] = $prow['sellprice']-$cou_data['coumoney'];
					}
					##### 쿠폰에 의한 가격 할인

					##### 오늘의 특가, 타임세일에 의한 가격
					$spesell = getSpeDcPrice($prow['productcode']);
					if($spesell){
						$prow['sellprice'] = $spesell;
					}
					##### //오늘의 특가, 타임세일에 의한 가격

					$temp_data[$row['special']][$i][]=$prow;
				}
			}
		}
		if($temp_data){
			foreach($temp_data as $special=>$v){
				foreach($v as $cnt=>$v2){
					foreach($v2 as $k=>$v3){
						$data[$special][]=$v3;
					}
				}
			}
		}
	}
	return $data;
}


function best_review_main(){

	//and a.productcode = b.productcode
	$qry="SELECT b.minimage, a.id,a.name,a.reserve,a.display,a.content,a.date,a.productcode,b.productname,b.tinyimage,b.selfcode,a.upfile,
	b.assembleuse, a.best_type, a.marks FROM tblproductreview a, tblproduct b WHERE best_type='1' AND a.productcode = b.productcode
	ORDER BY a.date DESC, marks desc limit 4";

	$res=pmysql_query($qry);

	$loop=0;
	$num=0;
	while($row=pmysql_fetch_array($res)){

		$data[$num][]=$row;

		/*$loop++;
		if($loop==3){
			$loop=0;
			$num++;
		}*/
	}
	return $data;
}

function brand_review_main($brandcd=''){

	//and a.productcode = b.productcode
	$brandcd = ($brandcd)?$brandcd:"004001";
	$data = cate_review_main($brandcd);
	return $data;
}

function cate_review_main($catecd=''){

	//and a.productcode = b.productcode
	if($catecd){
		$where = "AND a.productcode like '".$catecd."%' ";
	}

	$qry="SELECT a.subject, b.minimage, a.id,a.name,a.reserve,a.display,a.content,a.date,a.productcode,a.upfile,b.productname,b.tinyimage,b.selfcode,
	b.assembleuse, a.best_type, a.marks FROM tblproductreview a, tblproduct b WHERE a.productcode = b.productcode {$where}
	ORDER BY a.date DESC, marks desc limit 4";

	$res=pmysql_query($qry);

	while($row=pmysql_fetch_array($res)){

		$data[] = $row;
	}
	return $data;
}

function cate_review_main_best($catecd=''){

	//and a.productcode = b.productcode

	if($catecd){
		$sql_link = "SELECT c_productcode FROM tblproductlink ";
		$sql_link.= "WHERE c_category LIKE '{$catecd}%' ";
		$res_link = pmysql_query($sql_link);
		while($row = pmysql_fetch_array($res_link)){
			$link_data[] = $row['c_productcode'];
		}

		if($link_data){
			$wherein_arr = "'".implode("','",$link_data)."'";
		}
	}



	if($wherein_arr){
		$where = "AND a.productcode IN (".$wherein_arr.") ";
	}

	$qry="SELECT a.subject, b.minimage, a.id,a.name,a.reserve,a.display,a.content,a.date,a.productcode,a.upfile,b.productname,b.tinyimage,b.selfcode,
	b.assembleuse, a.best_type, a.marks FROM tblproductreview a, tblproduct b WHERE a.productcode = b.productcode AND best_type=1 {$where}
	ORDER BY a.date DESC, marks desc limit 4";

	$res=pmysql_query($qry);

	while($row=pmysql_fetch_array($res)){

		$data[] = $row;
	}
	return $data;

}

#####리뷰의 별마크 표시
function review_mark($mark){
	$mark_str = "";
	for($i=0;$i<$mark;$i++){
		$mark_str.="★";
	}
	for($i=0;$i<(5-$mark);$i++){
		$mark_str.="☆";
	}
	return $mark_str;
}

function smartSearchBrand(){

	$mb_qry="select * from tblproductbrand order by bridx";

	$mb_result=pmysql_query($mb_qry);
	while($mb_data=pmysql_fetch_array($mb_result)){
		$smartSearchBrand[]=$mb_data;
	}

	return $smartSearchBrand;
}

function smartSearchCategory($category, $category_step){
	if($category_step == '1'){
		$categoryWhere = " AND code_b||code_c||code_d = '000000000'";
	}else if($category_step == '2'){
		$categoryWhere = " AND code_a = '".$category."' AND code_b != '000' AND code_c||code_d = '000000'";
	}else if($category_step == '3'){
		$categoryWhere = " AND code_a||code_b = '".$category."' AND code_c != '000' AND code_d = '000'";
	}else if($category_step == '4'){
		$categoryWhere = " AND code_a||code_b||code_c = '".$category."' AND code_d != '000'";
	}

	$mb_qry="	SELECT
							code_name,
							code_a,
							CASE
								WHEN
									code_b||code_c||code_d = '000000000'
								THEN code_a
								WHEN
									code_c||code_d = '000000'
								THEN code_a||code_b
								WHEN
									code_d = '000'
								THEN
									code_a||code_b||code_c
								ELSE code_a||code_b||code_c||code_d
							END AS category
						FROM
							tblproductcode
						WHERE
							group_code!='NO'
							AND (type!='T' AND type!='TX' AND type!='TM' AND type!='TMX')".$categoryWhere."
							AND code_a != '019'
						ORDER BY
							cate_sort
						ASC";
						// 고객의 요청에 따라 coad_a가 019인 것은 카테고리에 안나오게 처리 2015 01 22
	$mb_result=pmysql_query($mb_qry);
	while($mb_data=pmysql_fetch_array($mb_result)){
		$smartSearchCategory[]=$mb_data;
	}

	return $smartSearchCategory;
}


function cutStringDot($content, $length){
	$content_len	 = strlen($content);
	if($content_len > $length){
		$substr = substr($content, 0, $length);
		preg_match('/^([\x00-\x7e]|.{2})*/', $substr, $content);
		$content = $content[0].'...';
	}
	return $content;
}

function getMaxImageForXn($code){
	$Dir = "../";
	$max_image = pmysql_fetch(pmysql_query("SELECT maximage FROM tblproduct WHERE productcode = '{$code}' "));

	if(file_exists($Dir.$max_image[0]) && $max_image[0] != ""){
		return $Dir.$max_image[0];
	}elseif(file_exists($Dir.DataDir."shopimages/product/".$max_image[0]) && $max_image[0] != ""){
		return $Dir.DataDir."shopimages/product/".$max_image[0];
	}else{
		return $Dir."images/no_img.gif";
	}
}

function getTinyImageForXn($code){
	$Dir = "../";
	$max_image = pmysql_fetch(pmysql_query("SELECT tinyimage FROM tblproduct WHERE productcode = '{$code}' "));

	if(file_exists($Dir.$max_image[0]) && $max_image[0] != ""){
		return $Dir.$max_image[0];
	}elseif(file_exists($Dir.DataDir."shopimages/product/".$max_image[0]) && $max_image[0] != ""){
		return $Dir.DataDir."shopimages/product/".$max_image[0];
	}else{
		return $Dir."images/no_img.gif";
	}
}

/*function recovery_coupon($ordercode, $m_id){
	$sql = "SELECT productcode FROM tblorderproduct WHERE ordercode='".$ordercode."' AND productcode LIKE 'COU%' ";
	$result=pmysql_query($sql,get_db_conn());
	while($row=pmysql_fetch_object($result)){
		$coupon_code=substr($row->productcode,3,-1);

		$resultRecoveryCount = pmysql_query("SELECT issue_recovery_no FROM tblcouponissue WHERE id='".$m_id."' AND coupon_code='".$coupon_code."'", get_db_conn());
		$rowRecoveryCount = pmysql_fetch_object($resultRecoveryCount);

		if($rowRecoveryCount->issue_recovery_no == 0 || !$rowRecoveryCount->issue_recovery_no){
			pmysql_query("UPDATE tblcouponissue SET used='N', issue_recovery_no=issue_recovery_no+1 WHERE id='".$m_id."' AND coupon_code='".$coupon_code."'", get_db_conn());
		}
	}
}*/

function recovery_coupon($ordercode, $m_id, $productcode=''){
	$sql  = "SELECT productcode FROM tblorderproduct WHERE ordercode='".$ordercode."' ";
	if($productcode) $sql .= "AND productcode='".$productcode."' ";
	$sql .= "AND coupon_price > 0 ";
	$result=pmysql_query($sql,get_db_conn());
	while($row=pmysql_fetch_object($result)){
		$resultRecoveryCode = pmysql_query("SELECT coupon_code FROM tblcoupon_order WHERE ordercode='".$ordercode."' AND productcode='".$row->productcode."'", get_db_conn());
		$rowRecoveryCode = pmysql_fetch_object($resultRecoveryCode);

		$coupon_code=$rowRecoveryCode->coupon_code;

		$resultRecoveryCount = pmysql_query("SELECT issue_recovery_no FROM tblcouponissue WHERE id='".$m_id."' AND coupon_code='".$coupon_code."'", get_db_conn());
		$rowRecoveryCount = pmysql_fetch_object($resultRecoveryCount);

		if($rowRecoveryCount->issue_recovery_no == 0 || !$rowRecoveryCount->issue_recovery_no){
			pmysql_query("UPDATE tblcouponissue SET used='N', issue_recovery_no=issue_recovery_no+1 WHERE id='".$m_id."' AND coupon_code='".$coupon_code."'", get_db_conn());
		}
	}
}


function use_coupon($ordercode, $m_id, $productcode=''){
	$sql  = "SELECT productcode FROM tblorderproduct WHERE ordercode='".$ordercode."' ";
	if($productcode) $sql .= "AND productcode='".$productcode."' ";
	$sql .= "AND coupon_price > 0 ";
	$result=pmysql_query($sql,get_db_conn());
	while($row=pmysql_fetch_object($result)){
		$resultRecoveryCode = pmysql_query("SELECT coupon_code FROM tblcoupon_order WHERE ordercode='".$ordercode."' AND productcode='".$row->productcode."'", get_db_conn());
		$rowRecoveryCode = pmysql_fetch_object($resultRecoveryCode);

		$coupon_code=$rowRecoveryCode->coupon_code;

		$resultRecoveryCount = pmysql_query("SELECT issue_recovery_no FROM tblcouponissue WHERE id='".$m_id."' AND coupon_code='".$coupon_code."'", get_db_conn());
		$rowRecoveryCount = pmysql_fetch_object($resultRecoveryCount);

		if($rowRecoveryCount->issue_recovery_no == 1 || !$rowRecoveryCount->issue_recovery_no){
			pmysql_query("UPDATE tblcouponissue SET used='Y', issue_recovery_no=issue_recovery_no-1 WHERE id='".$m_id."' AND coupon_code='".$coupon_code."'", get_db_conn());
		}
	}
}

/**
* 함수명 : getInsertSql
* Parameter :
* 	- string $table : insert 할 테이블명
* 	- array $param : 키와 실제값
* 		=> ex) $param[""]
*/

function getInsertSql(){
	return ;
}


/**
* 함수명 :exchageRate
* 제품의 달러 가격을 원 가격으로 변환시키는 함수
* parameter :
* 	- int $dollar : 제품의 달러금액*
*/

function exchageRate($dollar){
	$sql = "SELECT won FROM tblexchangerate ORDER BY reg_date DESC OFFSET 0 LIMIT 1";
	$res = pmysql_query($sql,get_db_conn());
	$row = pmysql_fetch_object($res);

	$won = floor($dollar * $row->won);
	//10원단위 절삭
	$won = floor($won/100)*100;

	pmysql_free_result($res);

	return $won;
}
/**
*
* 함수명 : exchagePrice
* 제품의 구매 숫자에 따라 가격이 달라짐
* parameter :
* 	- string prcode : 상품코드
*   - int count : 상품 구매 갯수
*   - string group_code : 회원 등급코드
*
*/

function exchagePrice($prcode,$count,$group_code){
	if($cnt<=0) $cnt = 0;
	$sql = "
		SELECT price FROM tblmembergroup_sale
		WHERE productcode='".$prcode."' AND group_code='".$group_code."'
		AND (min_num <= ".$count." AND max_num >= ".$count.")
		ORDER BY min_num DESC
		LIMIT 1
	";
	$res = pmysql_query($sql,get_db_conn());
	$row = pmysql_fetch_object($res);

	$price = $row->price;
	pmysql_free_result($res);

	if($price == null) $price = 0;
	return exchageRate($price);
}

function euckrToUtf8($arr,$mode=null){
	$returnArr;
	foreach( $arr as $k => $v ){
		$returnArr[$k] = trim(mb_convert_encoding(trim($v),"euc-kr","utf-8"));
	}
	return $returnArr;
}
####새로 추가한 쿠폰 적용가 20150610원재
//장바구니 쿠폰에 한해서 적용됩니다.
function couponprice($price){
				$today = date('YmdH');
				$sellprice=$price;
				$sellprice_if;
				$coupon_sql = "select * from tblcouponinfo";
				$coupon_sql.= " where productcode='ALL' or productcode='CATEGORY' ";
				//$coupon_sql.= " AND  '{$today}' >= date_start AND '{$today}' <= date_end";
				$coupon_res=pmysql_query($coupon_sql,get_db_conn());
				while($coupon_row=pmysql_fetch_object($coupon_res)){
					$coupon[]=$coupon_row;
				}
				if($coupon){
					for($i=0; $i<count($coupon); $i++){//쿠폰 적용가를 계산해서 가격이 낮은쪽으로 적용

						if($coupon[$i]->date_start <= $today  && $coupon[$i]->date_end >= $today){
						//발급일 기준 n일 쿠폰도 차후에 가져오기 위해 여기서 조건을 줘서 유효한 날짜 쿠폰들을 가져옴
							if($coupon[$i]->sale_type=='4'){//표시된 가격으로 직접 할인
								$sellprice_if=$price - $coupon[$i]->sale_money;
							}
							if($coupon[$i]->sale_type=='2'){//%로 할인
							//최대 할인값 오버해서 적용되는지 체크하고 오버하면은 최대할인가로 적용됨
								if( ($coupon[$i]->sale_max_money!=0) && ($price * ($coupon[$i]->sale_money)/100) <= ($coupon[$i]->sale_max_money)) {
									$sellprice_if=$price - ($price * $coupon[$i]->sale_money)/100;
								}else{
									$sellprice_if=$price - $coupon[$i]->sale_max_money;
								}

							}

							if($sellprice>$sellprice_if){//적용가능한 쿠폰들을 비교해서 최대 할인가를 적용합니다
								$sellprice=$sellprice_if.",".$coupon[$i]->sale_type.",".$coupon[$i]->sale_money;

							}
						}

					}

				}
				return $sellprice=explode(",",$sellprice);
				pmysql_free_result($coupon_res);


}

function family_dis(){//기획전(패밀리세일)전용 가격 산출

}

## unhex 구현.2015-10-15 jhjeong
function unhex($hex) {
    for($i=0;$i<strlen($hex);$i+=2)
        $str .= chr(hexdec(substr($hex,$i,2)));

    return $str;
}

// 세션변수 생성 (2015.10.27 추가 - 김재수)
function set_session($session_name, $value)
{
    if (PHP_VERSION < '5.3.0')
        session_register($session_name);
    // PHP 버전별 차이를 없애기 위한 방법
    $session_name = $_SESSION[$session_name] = $value;
}


// 세션변수값 얻음 (2015.10.27 추가 - 김재수)
function get_session($session_name)
{
    return isset($_SESSION[$session_name]) ? $_SESSION[$session_name] : '';
}

function cut_str($msg,$cut_size,$cut_add) {
	if($cut_size<=0) return $msg;
	if(ereg("\[re\]",$msg)) $cut_size=$cut_size+4;
		$max_size = $cut_size;
	$i=0;
	while(1) {
	 if (ord($msg[$i])>127)
		$i+=3;
	 else
		$i++;

	 if (strlen($msg) < $i)
		return $msg;

	 if ($max_size == 0)
		return substr($msg,0,$i).$cut_add;
	 else
		$max_size--;
	}
}

// 알림 및 창닫기 함수 (2015.10.29 추가 - 김재수)
function alert_close($message=null,$location=null,$frame=null) {

	echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8">';
	echo '<script> ';
	if($message) echo "alert('{$message}');";
	if($location) {
		if ($frame) {
			echo "{$frame}.location.href='{$location}';";
		} else {
			echo 'if (window.opener == null){';
			echo "location.href='{$location}';";
			echo '} else {';
			echo "opener.location.href='{$location}';";
			echo '}';
		}
	}

	echo "window.close();";
	echo '</script>';

	exit;
}

// selectbox selected ( 2015 10 28 추가 - 유동혁 )
function get_selected($field, $value)
{
    return ($field==$value) ? ' selected="selected"' : '';
}

// 입력 폼 안내문 ( 2015 10 28 추가 - 유동혁 )
function help($help="")
{
    $str  = '<span class="frm_info">'.str_replace("\n", "<br>", $help).'</span>';
    return $str;
}

// 리퍼러 도메인 체크 (2015.10.27 추가 - 김재수)
//@include_once($Dir."lib/referrer.php");


/**
 * 포인트 관련 함수들 추가
 * 2015-11-10 by jheong
 * mem_id : 회원아이디, point : 지급포인트, body : 내역, rel_flag : 관련구분(@login:로그인, @admin:관리자, @event:프로모션, @order:주문관련)
 * rel_mem_id : 작업자 ID, rel_job : 관련 작업(login, event : 날짜, admin:관리자ID, 기타 : 공란)
 * expire : 만료일 지정(0: 기본값사용, 1~ : 1일~)
**/
// 포인트 부여
function insert_point($mem_id, $point, $body='', $rel_flag='', $rel_mem_id='', $rel_job='', $expire=0)
{
	
    global $_data;

    // 포인트 사용을 하지 않는다면 return
    if ($_data->reserve_maxuse < 0) { return 0; }

    // 포인트가 없다면 업데이트 할 필요 없음
    if ($point == 0) { return 0; }

    // 회원아이디가 없다면 업데이트 할 필요 없음
    if ($mem_id == '') { return 0; }
    $mb = pmysql_fetch(" select id from tblmember where id = '$mem_id' ");
    //echo " select id from tblmember where id = '$mem_id' "."<br>";
    if (!$mb['id']) { return 0; }

    // 회원포인트
    $mb_point = get_point_sum($mem_id);
    //echo "mb_point = ".$mb_point."<br>";

    // 이미 등록된 내역이라면 건너뜀
    if ($rel_flag || $rel_mem_id || $rel_job)
    {
        $sql = " select count(*) as cnt from tblpoint
                  where mem_id = '$mem_id'
                    and rel_flag = '$rel_flag'
                    and rel_mem_id = '$rel_mem_id'
                    and rel_job = '$rel_job' ";
        $row = pmysql_fetch($sql);
        //echo "sql2 = ".$sql."<br>";
        if ($row['cnt'])
            return -1;
    }

    // 포인트 건별 생성
    // expire : 1 => 만료
    $expire_date = '99991231';
    //echo "reserve_term = ".$_data->reserve_term."<br>";
    if($_data->reserve_term > 0) {
        if($expire > 0) {
            //$expire_date = date('Ymd', strtotime('+'.($expire - 1).' days', time()));
			$lastdate	= date("t",strtotime('+'.($expire - 1).' days', time()));
			$expire_date = date('Ym', strtotime('+'.($expire - 1).' days', time())).$lastdate;
        } else {
            //$expire_date = date('Ymd', strtotime('+'.($_data->reserve_term - 1).' days', time()));
			$lastdate	= date("t",strtotime('+'.($_data->reserve_term - 1).' days', time()));
			$expire_date = date('Ym', strtotime('+'.($_data->reserve_term - 1).' days', time())).$lastdate;
		}
    }

	//룰렛이벤트 경우
	if($rel_flag=='@roulette'){
		if($_data->reserve_term > 0) {
			if($expire > 0) {
				$expire_date = date('Ymd', strtotime('+'.($expire - 1).' days', time()));
			} else {
				$expire_date = date('Ymd', strtotime('+'.($_data->reserve_term - 1).' days', time()));
			}
		}
	}

    $expire_chk = 0;
    if($point < 0) {
        $expire_chk = 1;
        $expire_date = date("Ymd");
    }
    $tot_point = $mb_point + $point;

    $sql = "insert into tblpoint (mem_id, regdt, body, point, use_point, tot_point, expire_chk, expire_date, rel_flag ,rel_mem_id, rel_job)
            values 
            ('$mem_id', '".date("YmdHis")."', '".addslashes($body)."', '$point', '0', '$tot_point', '$expire_chk', '$expire_date', '$rel_flag', '$rel_mem_id', '$rel_job') 
            ";
    pmysql_query($sql);
    //echo "sql3 = ".$sql."<br>";

    // 포인트를 사용한 경우 포인트 내역에 사용금액 기록
    if($point < 0) {
        insert_use_point($mem_id, $point);
    }

    // 포인트 UPDATE
    $sql = " update tblmember set reserve = '$tot_point' where id = '$mem_id' ";
    pmysql_query($sql);
    //echo "sql4 = ".$sql."<br>";

    return 1;
}

// 특정 회원에서 쿠폰을 발급
function insert_coupon( $coupon_code, $id, $date_start, $date_end ) {
    // ======================================================================
    // 쿠폰 사용 마감일자가 없는 경우
    // ======================================================================
    if ( empty($date_end) ) {
        $sql = "
            SELECT            
            (  
            CASE  
              WHEN time_type = 'D' THEN date_end  
              WHEN time_type = 'P' THEN  
                ( 
                  CASE  
                    WHEN to_char( ( now()::date + abs( date_start::int ) + 1 ) - interval '1 hour' , 'YYYYMMDDHH24' ) < date_end  
                      THEN to_char( ( now()::date + abs( date_start::int ) + 1 ) - interval '1 hour' , 'YYYYMMDDHH24' )  
                    ELSE date_end  
                  END 
                ) 
              END 
            ) AS date_end 
            FROM tblcouponinfo where coupon_code = '{$coupon_code}' ";

        list($date_end) = pmysql_fetch_array(pmysql_query($sql));
    }

    $sql  = "SELECT max(issue_recovery_no) as no FROM tblcouponissue WHERE coupon_code = '{$coupon_code}' AND id = '{$id}' ";
    $result = pmysql_query($sql);
    $row = pmysql_fetch_object($result);
    pmysql_free_result($result);

    $issue_recovery_no = 0;
    if ( $row->no != "" ) {
        $issue_recovery_no = $row->no + 1;
    }

    // tblcouponissue 에 등록
    $sql  = "INSERT INTO tblcouponissue ";
    $sql .= "( coupon_code, id, date_start, date_end, used, date, issue_recovery_no ) ";
    $sql .= "VALUES ";
    $sql .= "( '{$coupon_code}', '{$id}', '{$date_start}', '{$date_end}', 'N', '" . date("YmdHis") . "', {$issue_recovery_no} )";
    pmysql_query($sql);

    // tblcouponinfo의 해당쿠폰의 issue_no를 하나 증가
    $sql  = "UPDATE tblcouponinfo SET issue_no = issue_no + 1 WHERE coupon_code = '{$coupon_code}' ";
    pmysql_query($sql);

}

// 주중/주말의 마지막 날짜를 구한다.
// 쿠폰 발급시 사용
// mode : 0(주중), 1(주말), 2(완료)
function getLastDate($mode, $coupon_code) {
    // 오늘 날짜
    $year = date('Y');
    $month = date('m');
    $day = date('d');

    // 오늘 요일 번호
    $w = date('w',mktime(0,0,0,$month,$day,$year));

    // 쿠폰정보를 조회한다.
    $sql  = "SELECT * FROM tblcouponinfo WHERE coupon_code = '{$coupon_code}' ";
    $result = pmysql_query($sql);
    $row = pmysql_fetch_object($result);
    pmysql_free_result($result);

    $day_diff = -1;

    if ( $mode == 0 ) {
        // 주중
        $day_diff = 5 - (int)$w;
    } elseif ( $mode == 1 ) {
        // 주말
        $day_diff = 7 - (int)$w;
    } elseif ( $mode == 2 ) {
        // 완료
        if ( $row->time_type == "P" ) {
            $day_diff = (int)str_replace("-", "", $row->date_start);
        } elseif ( $row->time_type == "D" ) {
            $end_date = $row->date_end;
        }
    }

    // 일요일인 경우
    if ( $day_diff == 7 ) { $day_diff = 0; }

    if ( $day_diff > -1 ) {
        $end_date = date("Ymd",strtotime("+{$day_diff} day")) . "23";
    }

    // 쿠폰 사용 가능 날짜가 쿠폰의 유효기간을 지난 경우는 유효기간 마지막 날짜로 셋팅
    if ( $end_date >= $row->date_end ) {
        $end_date = $row->date_end;
    }

    return $end_date;
}

// 사용포인트 입력
function insert_use_point($mem_id, $point, $pid=0)
{
    global $_data;

    if($_data->reserve_term)
        $sql_order = " order by expire_date asc, pid asc ";
    else
        $sql_order = " order by pid asc ";

    $point1 = abs($point);
    $sql = " select pid, point, use_point
                from tblpoint
                where mem_id = '$mem_id'
                  and pid <> '$pid'
                  and expire_chk = '0'
                  and point > use_point
                $sql_order ";
    $result = pmysql_query($sql);
    //echo "sql5 = ".$sql."<br>";

    for($i=0; $row=pmysql_fetch_array($result); $i++) {
        $point2 = $row['point'];
        $point3 = $row['use_point'];
        //echo "point1 = ".$point1."<br>";
        //echo "point2 = ".$point2."<br>";
        //echo "point3 = ".$point3."<br>";

        if(($point2 - $point3) > $point1) {
            $sql = " update tblpoint
                        set use_point = use_point + '$point1'
                        where pid = '{$row['pid']}' ";
            pmysql_query($sql);
            //echo "sql6 = ".$sql."<br>";
            break;
        } else {
            $point4 = $point2 - $point3;
            $sql = " update tblpoint
                        set use_point = use_point + '$point4',
                            expire_chk = '99'
                        where pid = '{$row['pid']}' ";
            pmysql_query($sql);
            //echo "sql7 = ".$sql."<br>";
            $point1 -= $point4;
        }
    }
}

/*
// 사용포인트 삭제
function delete_use_point($mem_id, $point)
{
    global $_data;

    if($_data->reserve_term)
        $sql_order = " order by expire_date desc, pid desc ";
    else
        $sql_order = " order by pid desc ";

    $point1 = abs($point);
    $sql = " select pid, use_point, expire_chk, expire_date
                from tblpoint
                where mem_id = '$mem_id'
                  and expire_chk <> '1'
                  and use_point > 0
                $sql_order ";
    $result = pmysql_query($sql);
    echo "sql8 = ".$sql."<br>";
    for($i=0; $row=pmysql_fetch_array($result); $i++) {
        $point2 = $row['use_point'];

        $expire_chk = $row['expire_chk'];
        if($row['expire_chk'] == 99 && ($row['expire_date'] == '99991231' || $row['expire_date'] >= date("Ymd")))
            $expire_chk = 0;

        if($point2 > $point1) {
            $sql = " update tblpoint
                        set use_point = use_point - '$point1',
                            expire_chk = '$expire_chk'
                        where pid = '{$row['pid']}' ";
            pmysql_query($sql);
            echo "sql9 = ".$sql."<br>";
            break;
        } else {
            $sql = " update tblpoint
                        set use_point = '0',
                            expire_chk = '$expire_chk'
                        where pid = '{$row['pid']}' ";
            pmysql_query($sql);
            echo "sql10 = ".$sql."<br>";
            $point1 -= $point2;
        }
    }
}
*/
/*
// 소멸포인트 삭제
function delete_expire_point($mem_id, $point)
{
    global $_data;

    $point1 = abs($point);
    $sql = " select pid, use_point, expire_chk, expire_date
                from tblpoint
                where mem_id = '$mem_id'
                  and expire_chk = '1'
                  and point >= 0
                  and use_point > 0
                order by expire_date desc, pid desc ";
    $result = pmysql_query($sql);
    echo "sql11 = ".$sql."<br>";

    for($i=0; $row=pmysql_fetch_array($result); $i++) {
        $point2 = $row['use_point'];
        $expire_chk = '0';
        $expire_date = '99991231';
        if($_data->reserve_term > 0)
            $expire_date = date('Ymd', strtotime('+'.($_data->reserve_term - 1).' days', time()));

        if($point2 > $point1) {
            $sql = " update tblpoint
                        set use_point = use_point - '$point1',
                            expire_chk = '$expire_chk',
                            expire_date = '$expire_date'
                        where pid = '{$row['pid']}' ";
            pmysql_query($sql);
            echo "sql12 = ".$sql."<br>";
            break;
        } else {
            $sql = " update tblpoint
                        set use_point = '0',
                            expire_chk = '$expire_chk',
                            expire_date = '$expire_date'
                        where pid = '{$row['pid']}' ";
            pmysql_query($sql);
            echo "sql13 = ".$sql."<br>";
            $point1 -= $point2;
        }
    }
}
*/

// 포인트 내역 합계
function get_point_sum($mem_id)
{
    global $_data;

    if($_data->reserve_term > 0) {
        // 소멸포인트가 있으면 내역 추가
        $expire_point = get_expire_point($mem_id);
        //echo "expire_point = ".$expire_point."<br>";
        if($expire_point > 0) {
            $mb = get_member($mem_id, 'reserve');
            $body = '포인트 소멸';
            $rel_flag = '@expire';
            $rel_mem_id = $mem_id;
            $rel_job = 'expire'.'-'.uniqid('');
            $point = $expire_point * (-1);
            $tot_point = $mb['reserve'] + $point;
            $expire_date = date("Ymd");
            $expire_chk = 1;

            $sql = "insert into tblpoint (mem_id, regdt, body, point, use_point, tot_point, expire_chk, expire_date, rel_flag ,rel_mem_id, rel_job)
            values 
            ('$mem_id', '".date("YmdHis")."', '".addslashes($body)."', '$point', '0', '$tot_point', '$expire_chk', '$expire_date', '$rel_flag', '$rel_mem_id', '$rel_job') 
            ";
            pmysql_query($sql);
            //echo "sql14 = ".$sql."<br>";

            // 포인트를 사용한 경우 포인트 내역에 사용금액 기록
            if($point < 0) {
                insert_use_point($mem_id, $point);
            }
        }

        // 유효기간이 있을 때 기간이 지난 포인트 => expired 체크
        $sql = " update tblpoint
                    set expire_chk = '1'
                    where mem_id = '$mem_id'
                      and expire_chk <> '1'
                      and expire_date <> '99991231'
                      and expire_date < '".date("Ymd")."' ";
        pmysql_query($sql);
        //echo "sql15 = ".$sql."<br>";
    }

    // 포인트합
    $sql = " select COALESCE(sum(point),0) as sum_point
                from tblpoint
                where mem_id = '$mem_id' ";
    $row = pmysql_fetch($sql);
    //echo "sql16 = ".$sql."<br>";

    return $row['sum_point'];
}

// 소멸 포인트
function get_expire_point($mem_id)
{
    global $_data;

    if($_data->reserve_term == 0)
        return 0;

    $sql = " select COALESCE(sum(point - use_point),0) as sum_point
                from tblpoint
                where mem_id = '$mem_id'
                  and expire_chk = '0'
                  and expire_date <> '99991231'
                  and expire_date < '".date("Ymd")."' ";
    $row = pmysql_fetch($sql);
    //echo "sql17 = ".$sql."<br>";

    return $row['sum_point'];
}

/*
// 포인트 삭제
function delete_point($mem_id, $rel_flag, $rel_mem_id, $rel_job)
{
    //global $_data;

    $result = false;
    if ($rel_flag || $rel_mem_id || $rel_job)
    {
        // 포인트 내역정보
        $sql = " select * from tblpoint
                    where mem_id = '$mem_id'
                      and rel_flag = '$rel_flag'
                      and rel_mem_id = '$rel_mem_id'
                      and rel_job = '$rel_job' ";
        $row = pmysql_fetch($sql);
        echo "sql18 = ".$sql."<br>";

        if($row['point'] < 0) {
            $mem_id = $row['mem_id'];
            $point = abs($row['point']);

            delete_use_point($mem_id, $point);
        } else {
            if($row['use_point'] > 0) {
                insert_use_point($row['mem_id'], $row['use_point'], $row['pid']);
            }
        }

        $result = pmysql_query(" delete from tblpoint
                     where mem_id = '$mem_id'
                       and rel_flag = '$rel_flag'
                       and rel_mem_id = '$rel_mem_id'
                       and rel_job = '$rel_job' ", false);

        // tot_point에 반영
        $sql = " update tblpoint
                    set tot_point = tot_point - '{$row['point']}'
                    where mem_id = '$mem_id'
                      and pid > '{$row['pid']}' ";
        pmysql_query($sql);
        echo "sql19 = ".$sql."<br>";

        // 포인트 내역의 합을 구하고
        $sum_point = get_point_sum($mem_id);

        // 포인트 UPDATE
        $sql = " update tblmember set mb_point = '$sum_point' where mem_id = '$mem_id' ";
        $result = pmysql_query($sql);
        echo "sql20 = ".$sql."<br>";
    }

    return $result;
}
*/

// 회원 정보를 얻는다.
function get_member($mem_id, $fields='*', $emailOpt='1')
{
    //global $_data;

	if($emailOpt == 1)
	    return pmysql_fetch(" select $fields from tblmember where id = TRIM('$mem_id') ");
	else if($emailOpt == 2)
	    return pmysql_fetch(" select $fields from tblmember where email = TRIM('$mem_id') ");
}



// AP포인트 부여
function insert_point_act($mem_id, $point, $body='', $rel_flag='', $rel_mem_id='', $rel_job='', $expire=0)
{
    global $_data;

    // 포인트 사용을 하지 않는다면 return
    if ($_data->reserve_maxuse < 0) { return 0; }

    // 포인트가 없다면 업데이트 할 필요 없음
    if ($point == 0) { return 0; }

    // 회원아이디가 없다면 업데이트 할 필요 없음
    if ($mem_id == '') { return 0; }
    $mb = pmysql_fetch(" select id from tblmember where id = '$mem_id' ");
    if (!$mb['id']) { return 0; }

    // 회원포인트
    $mb_point = get_point_act_sum($mem_id);

    // 이미 등록된 내역이라면 건너뜀
    if ($rel_flag || $rel_mem_id || $rel_job)
    {
        $sql = " select count(*) as cnt from tblpoint_act
                  where mem_id = '$mem_id'
                    and rel_flag = '$rel_flag'
                    and rel_mem_id = '$rel_mem_id'
                    and rel_job = '$rel_job' ";
        $row = pmysql_fetch($sql);
        if ($row['cnt'])
            return -1;
    }
	
    // 포인트 건별 생성
    // expire : 1 => 만료
    $expire_date = '99991231';
    if($_data->reserve_term > 0) {
        if($expire > 0) {
			$lastdate	= date("t",strtotime('+'.($expire - 1).' days', time()));
			$expire_date = date('Ym', strtotime('+'.($expire - 1).' days', time())).$lastdate;
        } else {
			$lastdate	= date("t",strtotime('+'.($_data->reserve_term - 1).' days', time()));
			$expire_date = date('Ym', strtotime('+'.($_data->reserve_term - 1).' days', time())).$lastdate;
		}
    }

    $expire_chk = 0;
    if($point < 0) {
        $expire_chk = 1;
        $expire_date = date("Ymd");
    }
    $tot_point = $mb_point + $point;

    $sql = "insert into tblpoint_act (mem_id, regdt, body, point, use_point, tot_point, expire_chk, expire_date, rel_flag ,rel_mem_id, rel_job)
            values 
            ('$mem_id', '".date("YmdHis")."', '".addslashes($body)."', '$point', '0', '$tot_point', '$expire_chk', '$expire_date', '$rel_flag', '$rel_mem_id', '$rel_job') 
            ";
	
    pmysql_query($sql);

	// AP포인트 ERP 전송 (김재수 - 2016.12.20 추가)
	erpTotalPointIns("actpoint", $mem_id, addslashes($body), $rel_flag, $rel_job, $point, date("Ymd"));

    // 포인트를 사용한 경우 포인트 내역에 사용금액 기록
    if($point < 0) {
        insert_use_point_act($mem_id, $point);
    }

    // 포인트 UPDATE
    $sql = " update tblmember set act_point = '$tot_point' where id = '$mem_id' ";
    pmysql_query($sql);
    //echo "sql4 = ".$sql."<br>";

    return 1;
}

// 사용 AP포인트 입력
function insert_use_point_act($mem_id, $point, $pid=0)
{
    global $_data;

    if($_data->reserve_term)
        $sql_order = " order by expire_date asc, pid asc ";
    else
        $sql_order = " order by pid asc ";

    $point1 = abs($point);
    $sql = " select pid, point, use_point
                from tblpoint_act
                where mem_id = '$mem_id'
                  and pid <> '$pid'
                  and expire_chk = '0'
                  and point > use_point
                $sql_order ";
    $result = pmysql_query($sql);
    //echo "sql5 = ".$sql."<br>";

    for($i=0; $row=pmysql_fetch_array($result); $i++) {
        $point2 = $row['point'];
        $point3 = $row['use_point'];
        //echo "point1 = ".$point1."<br>";
        //echo "point2 = ".$point2."<br>";
        //echo "point3 = ".$point3."<br>";

        if(($point2 - $point3) > $point1) {
            $sql = " update tblpoint_act
                        set use_point = use_point + '$point1'
                        where pid = '{$row['pid']}' ";
            pmysql_query($sql);
            //echo "sql6 = ".$sql."<br>";
            break;
        } else {
            $point4 = $point2 - $point3;
            $sql = " update tblpoint_act
                        set use_point = use_point + '$point4',
                            expire_chk = '99'
                        where pid = '{$row['pid']}' ";
            pmysql_query($sql);
            //echo "sql7 = ".$sql."<br>";
            $point1 -= $point4;
        }
    }
}

// AP포인트 내역 합계
function get_point_act_sum($mem_id)
{
    global $_data;

    if($_data->reserve_term > 0) {
        // 소멸포인트가 있으면 내역 추가
        $expire_point = get_expire_point_act($mem_id);
        //echo "expire_point = ".$expire_point."<br>";
        if($expire_point > 0) {
            $mb = get_member($mem_id, 'act_point');
            $body = '포인트 소멸';
            $rel_flag = '@expire';
            $rel_mem_id = $mem_id;
            $rel_job = 'expire'.'-'.uniqid('');
            $point = $expire_point * (-1);
            $tot_point = $mb['act_point'] + $point;
            $expire_date = date("Ymd");
            $expire_chk = 1;

            $sql = "insert into tblpoint_act (mem_id, regdt, body, point, use_point, tot_point, expire_chk, expire_date, rel_flag ,rel_mem_id, rel_job)
            values 
            ('$mem_id', '".date("YmdHis")."', '".addslashes($body)."', '$point', '0', '$tot_point', '$expire_chk', '$expire_date', '$rel_flag', '$rel_mem_id', '$rel_job') 
            ";
            pmysql_query($sql);
            //echo "sql14 = ".$sql."<br>";

			// AP포인트 ERP 전송 (김재수 - 2016.12.20 추가)
			erpTotalPointIns("actpoint", $mem_id, addslashes($body), $rel_flag, $rel_job, $point, date("Ymd"));

            // 포인트를 사용한 경우 포인트 내역에 사용금액 기록
            if($point < 0) {
                insert_use_point_act($mem_id, $point);
            }
        }

        // 유효기간이 있을 때 기간이 지난 포인트 => expired 체크
        $sql = " update tblpoint_act
                    set expire_chk = '1'
                    where mem_id = '$mem_id'
                      and expire_chk <> '1'
                      and expire_date <> '99991231'
                      and expire_date < '".date("Ymd")."' ";
        pmysql_query($sql);
        //echo "sql15 = ".$sql."<br>";
    }

    // 포인트합
    $sql = " select COALESCE(sum(point),0) as sum_point
                from tblpoint_act
                where mem_id = '$mem_id' ";
    $row = pmysql_fetch($sql);
    //echo "sql16 = ".$sql."<br>";

    return $row['sum_point'];
}

// 소멸 AP포인트
function get_expire_point_act($mem_id)
{
    global $_data;

    if($_data->reserve_term == 0)
        return 0;

    $sql = " select COALESCE(sum(point - use_point),0) as sum_point
                from tblpoint_act
                where mem_id = '$mem_id'
                  and expire_chk = '0'
                  and expire_date <> '99991231'
                  and expire_date < '".date("Ymd")."' ";
    $row = pmysql_fetch($sql);
    //echo "sql17 = ".$sql."<br>";

    return $row['sum_point'];
}

/**
 * 포인트 관련 함수들 추가 End
**/


/**
 *  deli_gbn : 주문상태값, paymethod : 결제수단, bank_date : 입금일, pay_flag : 결제리턴값
 *  $ordersteparr = array("1"=>"주문접수","2"=>"결제확인","3"=>"배송준비","4"=>"배송중","5"=>"주문취소","6"=>"결제(카드)실패","7"=>"반송","8"=>"취소요청","9"=>"환불대기","10"=>"환불","11"=>"배송완료")
**/

function GetOrderState($deli_gbn, $paymethod="", $bank_date="", $pay_flag="", $pay_admin_proc="", $receive_ok='0', $order_conf='0') {

    //echo $deli_gbn;
    //echo $paymethod;
    //echo $bank_date;
    //echo $pay_flag;
    //echo $pay_admin_proc;
    $ret_flag = "";
    $pay_B = $pay_C = false;

    $pay_arr1 = array('B','O','Q');         // 무통장, 가상계좌 등
    $pay_arr2 = array('C','P','M','V');     // 카드 등
    if($paymethod) {
        $paymethod_f = substr($paymethod, 0, 1);
    }

    if(in_array($paymethod_f,$pay_arr1)) $pay_B = true;
    if(in_array($paymethod_f,$pay_arr2)) $pay_C = true;

    if($deli_gbn == "N") {

        if( ($pay_B && !$bank_date) || ($pay_C && $pay_flag != "0000" && $pay_admin_proc == "C") ) {
            $ret_flag = "1";
        }

        if( ($pay_B && $bank_date) || ($pay_C && $pay_flag == "0000" && $pay_admin_proc != "C") ) {
            $ret_flag = "2";
        }

        if( ($pay_C && $pay_flag == "N" && $pay_admin_proc == "N") ) {
            $ret_flag = "6";
        }
    } else if($deli_gbn == "S") $ret_flag = "3";
    else if($deli_gbn == "Y"){
		if( $receive_ok == '1' ){
			if( $order_conf=='0' ){
				$ret_flag = "11";
			} else {
				$ret_flag = "12";
			}
		} else {
			$ret_flag = "4";
		}
	} else if($deli_gbn == "C") $ret_flag = "5";
    else if($deli_gbn == "R") $ret_flag = "7";
    else if($deli_gbn == "D") $ret_flag = "8";
    else if($deli_gbn == "E") $ret_flag = "9";
    else if( ($pay_B && !$bank_date) || ($pay_C && $pay_flag == "0000" && $pay_admin_proc == "C") ) {
        $ret_flag = "10";
    } // else if($deli_gbn == "F") $ret_flag = "11"; 사용 안함

    return $ret_flag;
}




//레이어 팝업 카테고리로 상품 불러오기 (2016.01.18 - 김재수)
function T_codeListScript(){
	$sql = "SELECT * FROM tblproductcode WHERE group_code!='NO' ";
	$sql.= "AND (type!='T' AND type!='TX' AND type!='TM' AND type!='TMX') ";
	$sql.= "order by code_a asc, cate_sort asc";
	$result  = pmysql_query($sql,get_db_conn());
	$i=0;
	$ii=0;
	$iii=0;
	$iiii=0;
	$strcodelist = "";
	$strcodelist.= "<script>\n";
	$selcode_name="";

	while($row=pmysql_fetch_object($result)) {
		$strcodelist.= "var clist2=new T_CodeList();\n";
		$strcodelist.= "clist2.t_code_a='{$row->code_a}';\n";
		$strcodelist.= "clist2.t_code_b='{$row->code_b}';\n";
		$strcodelist.= "clist2.t_code_c='{$row->code_c}';\n";
		$strcodelist.= "clist2.t_code_d='{$row->code_d}';\n";
		$strcodelist.= "clist2.type='{$row->type}';\n";
		$strcodelist.= "clist2.code_name='{$row->code_name}';\n";
		if($row->type=="L" || $row->type=="T" || $row->type=="LX" || $row->type=="TX") {
			$strcodelist.= "t_lista[{$i}]=clist2;\n";
			$i++;
		}
		if($row->type=="LM" || $row->type=="TM" || $row->type=="LMX" || $row->type=="TMX") {
			if ($row->code_c=="000" && $row->code_d=="000") {
				$strcodelist.= "t_listb[{$ii}]=clist2;\n";
				$ii++;
			} else if ($row->code_d=="000") {
				$strcodelist.= "t_listc[{$iii}]=clist2;\n";
				$iii++;
			} else if ($row->code_d!="000") {
				$strcodelist.= "t_listd[{$iiii}]=clist2;\n";
				$iiii++;
			}
		}
		$strcodelist.= "clist2=null;\n\n";
	}
	pmysql_free_result($result);
	$strcodelist.= "T_CodeInit();\n";
	$strcodelist.= "</script>\n";

	echo $strcodelist;

	echo "<select name='t_code_a' id='t_code_a' style=\"width:170px;\" onchange=\"T_SearchChangeCate(this,1)\">\n";
	echo "<option value=\"\">〓〓 1차 카테고리 〓〓</option>\n";
	echo "</select>\n";

	echo "<select name='t_code_b' id='t_code_b' style=\"width:170px;\" onchange=\"T_SearchChangeCate(this,2)\">\n";
	echo "<option value=\"\">〓〓 2차 카테고리 〓〓</option>\n";
	echo "</select>\n";

	echo "<select name='t_code_c' id='t_code_c' style=\"width:170px;\" onchange=\"T_SearchChangeCate(this,3)\">\n";
	echo "<option value=\"\">〓〓 3차 카테고리 〓〓</option>\n";
	echo "</select>\n";

	echo "<select name='t_code_d' id='t_code_d' style=\"width:170px;\">\n";
	echo "<option value=\"\">〓〓 4차 카테고리 〓〓</option>\n";
	echo "</select>\n";

	echo "<script>T_SearchCodeInit(\"".$code_a."\",\"".$code_b."\",\"".$code_c."\",\"".$code_d."\");</script>";
}



#상품 이미지 프린트 2016-01-21 유동혁
# sql           : 상품 정보 가져오는 쿼리
# type          : 화면에 보여주는 패턴
# arrProdOrder  : 상품 노출 순서(지정된 productcode순으로 나열)
/*
function productlist_print( $sql, $type = 'W_001', $arrProdOrder = array() ){
	global $Dir, $list_types, $list_key;
	$img_path = $Dir.'data/shopimages/product/';
	$pr_link = $Dir.'front/productdetail.php?productcode=';
	//해당 상품이 담길 array
	$product_arr = array();
	$result = pmysql_query( $sql, get_db_conn() );
	while( $row = pmysql_fetch_object( $result ) ){
		$brandname = brand_name( $row->brand );

		$product_arr[] = array(
			'[CODE]'=>$row->productcode,					//상품코드
			'[NAME]'=>$row->productname,					//상품명
			'[SELLPRICE]'=>number_format( $row->sellprice ), //할인가
			'[CONSUMERPRICE]'=>number_format( $row->consumerprice ), //판매가
			'[BAND]'=>$brandname,							//브랜드명
			'[MAXIMAGE]'=>$img_path.$row->maximage,			//큰이미지
			'[MINIMAGE]'=>$img_path.$row->minimage,			//중간이미지
			'[TINYIMAGE]'=>$img_path.$row->tinyimage,		//작은이미지
			'[COMMENT]'=>$row->mdcomment,					//코맨트
			'[REVIEW]'=>$row->review_cnt,					//리뷰수
			'[PRODUCTLINK]'=>$pr_link.$row->productcode,	//상품링크
			'[ICON]'=>'',
			'[WISH]'=>''
		);
	}
	$tags = product_html_tags( $product_arr, $type );
	pmysql_free_result( $result );

	return $tags;

}
*/

# 상품 이미지 경로를 만들어준다.
# 경로가 외부 경로일 경우는 그것을 그대로 리턴해 준다.
# img_path      : 이미지 경로
# img_name      : 이미지 파일명
# mobile_flag   : isMobile값을 직접 전달(ajax페이지에서는 이 방법으로 해야 함)
function getProductImage( $img_path, $img_name, $mobile_flag = null ) {
    global $isMobile;

    if(strpos($img_name, "http://") === false) {
        if(strlen($img_name)!=0 && file_exists($img_path.$img_name)){
            $img_name = $img_path.$img_name;

/*
            if ( strpos($img_path,'/product/') !== false) {
                // 이미지 정보를 변경했을때 바로 변경된 이미지를 가져옴 (상품이미지인 경우에만)
                $img_name .= '?v='.date('YmdHi');
            }
*/
        } else {
            if ( $isMobile || ( $mobile_flag != null && $mobile_flag ) ) {
               $img_name = "../images/common/noimage.gif";
            } else {
               $img_name = "../images/common/noimage.gif";
            }
        }
    }

    return $img_name;
}
# DB에 파일은 있지만 경로에 파일이 없는 경우 빈값으로 리턴
function getProductImage2( $img_path, $img_name, $mobile_flag = null ) {
	global $isMobile;

	if(strpos($img_name, "http://") === false) {
		if( is_file($img_path.$img_name)){
			$img_name = $img_path.$img_name;

			/*
			if ( strpos($img_path,'/product/') !== false) {
			// 이미지 정보를 변경했을때 바로 변경된 이미지를 가져옴 (상품이미지인 경우에만)
			$img_name .= '?v='.date('YmdHi');
			}
			*/
		} else {
			if ( $isMobile || ( $mobile_flag != null && $mobile_flag ) ) {
				$img_name = "";
			} else {
				$img_name = "";
			}
		}
	}

	return $img_name;
}

#댓글 회원정보 프로필 이미지
#정보가 없을 경우 default 이미지를 리턴
function getCommentImage( $img_path, $img_name, $mobile_flag = null ) {
	global $isMobile;

	if(strpos($img_name, "http://") === false) {
		if( is_file($img_path.$img_name)){
			$img_name = $img_path.$img_name;
		} else {
			if ( $isMobile || ( $mobile_flag != null && $mobile_flag ) ) {
				$img_name = "../images/common/img_default_forum_m.png";
			} else {
				$img_name = "../images/common/img_default_forum_front.png";
			}
		}
	}

	return $img_name;
}

#같은 상품 색상 옵션
function getColorProduct($productcode, $prodcode, $type=''){
	global $Dir;
	$img_path = $Dir.'data/shopimages/product/';
	$prod_sql = "SELECT pridx, productcode, prodcode, color_code, minimage FROM tblproduct WHERE prodcode = '".$prodcode."' AND prodcode != '' AND display = 'Y'";
	if($type == "detail"){
		//$prod_sql .= " ORDER BY CASE WHEN productcode = '".$productcode."' THEN 0 END limit 3";
		$prod_sql .= " AND productcode != '".$productcode."' limit 3";
	}else{
		$prod_sql .= " AND productcode <> '".$productcode."'";
	}
 	//exdebug($prod_sql);
	$result = pmysql_query($prod_sql);
    $arrColorProd = "";
    unset($arrColorProd);
    while ( $row = pmysql_fetch_array($result) ) {
    	$arrColorProd[] = $row;
    }
    $colorProdHtml = "";
	if($arrColorProd){
		foreach( $arrColorProd as $v ){
			if($v['minimage']){
				if($type == "detail"){
					$colorProdHtml .= "<li id='color_".$v['productcode']."'><a href='productdetail.php?productcode=".$v['productcode']."'><img src=\"".$img_path.$v['minimage']."\" alt=\"".$v['color_code']."\"></a></li>";
				}else{
					$colorProdHtml .= "<li><a href='javascript:prod_detail(\"".$v['productcode']."\");'><img src=\"".$img_path.$v['minimage']."\" alt=\"".$v['color_code']."\"></a></li>";
				}
			}else{
				$colorProdHtml .= "<li><a href='javascript:prod_detail(\"".$v['productcode']."\");'><img src=\"v".date('His')."\" alt=''></a></li>";
			}

		}
	}
    return $colorProdHtml;
}

#같은 상품 색상 옵션(Text)
function getColorProductText($productcode, $prodcode){
	global $Dir;
	$img_path = $Dir.'data/shopimages/product/';
	$prod_sql = "SELECT pridx, productcode, prodcode, color_code, minimage FROM tblproduct WHERE prodcode = '".$prodcode."' AND prodcode != '' AND display = 'Y'";
	//$prod_sql .= " ORDER BY CASE WHEN productcode = '".$productcode."' THEN 0 END limit 3";
	$prod_sql .= " AND productcode != '".$productcode."' limit 3";
	$result = pmysql_query($prod_sql);
	$arrColorProd = "";
	while ( $row = pmysql_fetch_array($result) ) {
		$arrColorProd[] = $row;
	}
	$i = 0;
	$numItems = count($arrColorProd);
	$colorProdText = "";
	if ($numItems > 0) {
		$colorProdText = "<p>";
		foreach( $arrColorProd as $k=>$v ){
			if($v['minimage']){
				if(++$i == $numItems){
					$colorProdText .= $v['color_code'];
				}else{
					$colorProdText .= $v['color_code']." / ";
				}
			}else{
				$colorProdText .= "";
			}

		}
		$colorProdText .= "</p>";
	}
	return $colorProdText;
}

#상품 이미지 프린트 2016-01-21 유동혁
# sql           : 상품 정보 가져오는 쿼리
# type          : 화면에 보여주는 패턴
# arrProdOrder  : 상품 노출 순서(지정된 productcode순으로 나열)
# listnum       : 보여주고 싶은 상품 갯수 지정 (null이면 types_array에 지정한 대로 노출)
# icon_info     : 대카테고리 WEEKLY BEST의 순위아이콘으로 사용
# cate_code     : 카테고리별 상품 리스트에 사용한 code값.
function productlist_print( $sql, $type = 'W_001', $arrProdOrder = array(), $listnum = null, $icon_info = null, $cate_code = null) {
	global $Dir, $list_types, $list_key, $_ShopInfo;
	$img_path = $Dir.'data/shopimages/product/';

    if ( $type == "W_015" || $type == "W_016" || $type == "W_017" || $type == "W_018" || $type == "MO_001" || $type == "MO_002" || $type == "MO_003" || $type == "SMO_001" ) {
        $pr_link = $Dir.'m/productdetail.php?productcode=';
    } else {
        $pr_link = $Dir.'front/productdetail.php?productcode=';
    }

	$arrProdOrder_cnt = count( $arrProdOrder );
	//해당 상품이 담길 array
	$product_arr = array();
	$result = pmysql_query( $sql, get_db_conn() );
	$key = -1;
	$product_cnt = 1;

	while( $row = pmysql_fetch_object( $result ) ){

		if( strlen( $row->brand ) > 0 ){
			
			$brandname = brand_name( $row->brand );
		} else {
			$brandname = '';
		}
		
		if( $arrProdOrder_cnt > 0 ){ // 상품 진열순서가 존재할 경우 해당 상품만 진열을 한다
			if( array_search( $row->productcode, $arrProdOrder ) !== false ) {
				$key = array_search( $row->productcode, $arrProdOrder );
			} else {
				$key = null;
			}
		} else { //상품 진열순서가 존재하지 않을경우 진열 순서를 0번부터 진행한다
			$key++;
		}

		if( $key !== null ) {
            // 위시리스트 관련
            $wishListClassOn = "";
            $member_id = $_ShopInfo->getMemid();
            if ( !empty($member_id) ) {
                // 로그인한 상태인 경우
                $wish_sql  = "SELECT count(*) FROM tblwishlist ";
                $wish_sql .= "WHERE id = '{$member_id}' and productcode = '{$row->productcode}' ";
                $wish_row = pmysql_fetch_object(pmysql_query($wish_sql));

                if ( $wish_row->count == 1 ) {
                    // 위시 리스트에 있는 경우
                    $wishListClassOn = "on";
                }
            }

            // 아이콘 관련
            //  icon은 2자리씩 자르면 됩니다.
            //  <img src=\"{$Dir}images/common/icon{번호}.gif\" border=0 align=absmiddle>

            /*
            $icon_html = "";
            if ( !empty($row->icon) ) {
                $iconLen = strlen($row->icon);
                $loopCnt = $iconLen / 2;
                for ( $i = 0; $i < $loopCnt; $i++ ) {
                    $iconCode = substr($row->icon, $i*2, 2);
                    $icon_html .= "<span><img src=\"{$Dir}images/common/icon{$iconCode}.gif\" border=0 align=absmiddle></span>";
                }
            }
            */

            $icon_html = getIconHtml($row->icon, $type);

            $mdcommentcolor = $row->mdcommentcolor;
            if ( empty($mdcommentcolor) ) {
                $sub_sql = "SELECT mdcommentcolor FROM tblproduct WHERE productcode = '" . $row->productcode . "' ";
                $sub_row = pmysql_fetch_object(pmysql_query($sub_sql));

                $mdcommentcolor = $sub_row->mdcommentcolor;
            }
			//할인상품 금액 변경
			$row->sellprice=timesale_price($row->productcode);
			$consumer_class	= "";
			if ($row->consumerprice <= 0 || $row->consumerprice == $row->sellprice){
				$consumer_class	= "hide";
			}

            $prod_link = $pr_link.$row->productcode;

            // 카테고리코드가 같이 넘어온 경우는 아래와 같이 해당 값을 연결
            if ( !is_null($cate_code) && $cate_code != "" ) {
                $prod_link .= "&code={$cate_code}";
            }
            // 롤오버 이미지를 사용할 경우
            $image_tags = '<img class="thumb-hover-on" src="'.getProductImage( $img_path, $row->minimage ).'" alt="상품 썸네일" >';
            $image_tags.= '<img class="thumb-hover-off" src="'.getProductImage( $img_path, $row->over_minimage ).'" alt="상품 썸네일" >';
            if(strpos($row->over_minimage, "http://") === false) {
                if( strlen($row->over_minimage) == 0 || !file_exists( $img_path.$row->over_minimage ) ){
                    $image_tags = '<img src="'.getProductImage( $img_path, $row->minimage ).'" alt="상품 썸네일" >';
                }
            }
            //soldout icon 표시
            //$sellprice = number_format( $row->sellprice );
			$sellprice = number_format( $row->sellprice );
            $consumerprice = number_format( $row->consumerprice );
			$soldout_class="";
			/*
            if( ( $row->quantity <= 0 || $row->soldout == 'Y' ) && strlen( $row->soldout ) > 0 && strlen( $row->quantity ) > 0 ){ // soldout icon 추가
                $sellprice = "<img src=\"{$Dir}images/common/icon_soldout.gif\">";
				//$sellprice = "<span><img src=\"{$Dir}images/common/icon_soldout.gif\" border=0 align=absmiddle></span>";
                $consumerprice = '';
				$soldout_class="hide";
				$consumer_class	= "hide";
            }*/


			/*
            if($row->section){
            	$likeHtml = "<button class=\"comp-like btn-like like_p_button".$row->productcode."\" title=\"선택됨\"  onclick='detailSaveLike(\"".$row->productcode."\",\"on\",\"product\",\"".$_ShopInfo->getMemid()."\",\"".$row->brand."\" )'><span><i class=\"icon-like on like_p".$row->productcode."\">좋아요</i></span><span class=\"like_pcount_".$row->productcode."\">".$row->hott_cnt."</span></button>";
            }else{
            	$likeHtml = "<button class=\"comp-like btn-like like_p_button".$row->productcode."\" title=\"선택안됨\"  onclick='detailSaveLike(\"".$row->productcode."\",\"off\",\"product\",\"".$_ShopInfo->getMemid()."\",\"".$row->brand."\" )'><span><i class=\"icon-like like_p".$row->productcode."\">좋아요</i></span><span class=\"like_pcount_".$row->productcode."\">".$row->hott_cnt."</span></button>";																												
            }
			*/

			if($row->section){
            	$likeHtml = "<button class=\"comp-like btn-like like_p_button".$row->productcode."\" title=\"선택됨\"  onclick=\"like.clickLike('product','".$row->productcode."','".$row->prodcode."')\"><span><i id=\"like_".$row->productcode."\"  class=\"icon-like on like_p".$row->productcode."\">좋아요</i></span><span id=\"like_cnt_".$row->productcode."\">".$row->hott_cnt."</span></button>";
            }else{
            	$likeHtml = "<button class=\"comp-like btn-like like_p_button".$row->productcode."\" title=\"선택안됨\" onclick=\"like.clickLike('product','".$row->productcode."','".$row->prodcode."')\"><span><i id=\"like_".$row->productcode."\"  class=\"icon-like like_p".$row->productcode."\">좋아요</i></span><span id=\"like_cnt_".$row->productcode."\">".$row->hott_cnt."</span></button>";																												
            }
			//"like.clickLike(\'product\',\'m'+i+'\',\''+list[i].productcode+'\')"
			
			$size_html="";
			$size_arr = explode ("@#", $row->sizecd);

			if($row->sizecd){
			$size_html="<div class='opt'>";
				foreach( $size_arr as $size ){ $size_html.="<span>".$size."</span>"; }
			$size_html.="</div>";
			}

			$prcode_color="";
			$prcode_sql="select pc.color_code from tblproduct p left join tblproduct_color pc on (p.color_code=pc.color_name) where prodcode='".$row->prodcode."' AND p.display='Y' order by productcode ";

			$prcode_result=pmysql_query($prcode_sql);
			while($prcode_data=pmysql_fetch_object($prcode_result)){
				if($prcode_data->color_code){
					if($type == "MO_001"  || $type == "MO_002" || $type == "MO_003" || $type == "SMO_001"){
						$prcode_color.="<span class='colorchip' style=\"background:".$prcode_data->color_code.";border:1px solid #000000;\"></span>";
					}else{
						$prcode_color.="<span style=\"background:".$prcode_data->color_code.";border:1px solid #000000;\"></span>";
					}
				}
				if( ( $prcode_data->quantity <= 0 || $prcode_data->soldout == 'Y' ) && strlen( $prcode_data->soldout ) > 0 && strlen( $prcode_data->quantity ) > 0 && $soldout_yn!="N"){ // soldout icon 추가
					$soldout_yn="Y";
				}else{
					$soldout_yn="N";
				}
			}
			/*
			if( $soldout_yn=="Y" ){ // soldout icon 추가
                $sellprice = "<img src=\"{$Dir}images/common/icon_soldout.gif\">";
				//$sellprice = "<span><img src=\"{$Dir}images/common/icon_soldout.gif\" border=0 align=absmiddle></span>";
                $consumerprice = '';
				$soldout_class="hide";
				$consumer_class	= "hide";
            }
*/
			//$prcode_color.="</div>";
			//상품명에 상품코드가 같이 들어가있어서 한칸띄움 2017-05-15
			$proname=str_replace("(","<br>(",$row->productname);
			//$proname=explode("(",$row->productname);
			$procode_outname=explode("(",$row->productname);



			$product_arr[$key] = array(
				'[CODE]'=>$row->productcode,                                                //상품코드
				'[NAME]'=>$proname,                                    //상품명
				'[SELLPRICE]'=>$sellprice,                                                  //할인가
				'[CONSUMERPRICE]'=>$consumerprice,                                          //판매가
				'[CONSUMER_CLASS]'=>$consumer_class,                                        //판매가가 0이거나 할인가가 같으면 hide
				'[BAND]'=>$brandname,                                                       //브랜드명
				'[MAXIMAGE]'=>getProductImage($img_path, $row->maximage),                   //큰이미지
				'[MINIMAGE]'=>getProductImage($img_path, $row->minimage),                   //중간이미지
				'[TINYIMAGE]'=>getProductImage($img_path, $row->tinyimage),                 //작은이미지
				'[COMMENT]'=>$row->mdcomment,                                               //코맨트
				'[REVIEW]'=>$row->review_cnt,                                               //리뷰수(누적)
//				'[PRODUCTLINK]'=>$pr_link.$row->productcode,                                //상품링크
				'[PRODUCTLINK]'=>$prod_link,                                                //상품링크
				'[ICON]'=>$icon_html,                                                       //아이콘 노출
				'[WISH]'=>$wishListClassOn,                                                 //위시리스트에 담겨있으면 'on'
//				'[PRODUCTLINK_REVIEW]'=>$pr_link.$row->productcode.'#tab-product-review',   //상품리뷰링크
				'[PRODUCTLINK_REVIEW]'=>$prod_link.'#tab-product-review',                   //상품리뷰링크
				'[COMMENT_COLOR]'=>$mdcommentcolor,                                         //MD코멘트 색깔
                '[OVERIMAGE]'=>getProductImage($img_path, $row->over_minimage),             //롤오버 이미지
                '[ROLL_OVER_IMG]'=>$image_tags,                                              //이미지 롤오버
                '[REVIEW_MARK]'=>$row->marks * 20,										//리뷰 마크 사이즈
				'[REVIEW_CNT]'=>$row->marks_total_cnt,									//리뷰수(현재)
               	'[LIKE]'=>$likeHtml,                                         //좋아요
               	'[COLOR_PROD]'=>getColorProduct($row->productcode, $row->prodcode),
				'[C_CODE]'=>$row->c_category,
				'[SIZE_HTML]'=>$size_html,													//사이즈 
				'[HOT_CNT]'=>$row->hott_cnt,													//좋아요 수
				'[PRODUCT_CNT]'=>$product_cnt,													//상품카운트
				'[PRCODE_COLOR]'=>$prcode_color,													//상품별 컬러
				'[SOLDOUT_CLASS]'=>$soldout_class,												//솔드아웃이면 hide
				'[PRODUCT_OUTNAME]'=>$procode_outname[0]										//메인페이지 상품명 전용 상품코드 제거 

			);
			$product_cnt++;
		}
	}

	ksort($product_arr); // array를 넣기전 순서를 sort한다
	$tags = product_html_tags( $product_arr, $type, $listnum, $icon_info );
	pmysql_free_result( $result );

	return $tags;

}

#브랜드 정보 2016-01-21 유동혁
function brand_name( $bridx ){
	$sql = "SELECT brandname FROM tblproductbrand WHERE bridx = '".$bridx."' ";
	$result = pmysql_query( $sql, get_db_conn() );
	$row = pmysql_fetch_object( $result );
	$brandname = $row->brandname;
	pmysql_free_result( $result );
	return $brandname;
}

#리뷰 댓글수 구하기 2016-02-05 최문성
function get_review_comment_count( $pnum ){
	$sql = "SELECT count(*) FROM tblproductreview_comment where pnum = {$pnum}";
	$result = pmysql_query( $sql, get_db_conn() );
	$row = pmysql_fetch_object( $result );
	$count = $row->count;
	pmysql_free_result( $result );

	return $count;
}

#HTML 상품 리스트 2016-01-21 유동혁
function product_html_tags( $arr, $type, $listnum, $icon_info ){
	global $Dir, $list_types, $list_key;

	$img_path = $Dir.'data/shopimages/product/';

    // 컨텐츠 리스트 길이
    if ( is_null($listnum) ) {
        $content_length = $list_types[$type]['content_length'];
    } else {
        $content_length = $listnum;
    }

	$content_class = $list_types[$type]['content_class'];   //컨텐츠의 대표 클레스
	$img_width = $list_types[$type]['img_width'];           //상품이미지 width
	$img_height = $list_types[$type]['img_height'];         //상품이미지 height
	$arr_cnt = count( $arr ); //들어갈 값의 길이
	$tag_cnt = ceil( $arr_cnt / $content_length );          //테그별 길이
	$arr_lastcnt = $arr_cnt % $content_length;              //마지막 컨텐츠 길이
	$tags = array(); //리턴할 테그 내용
	$use_cnt = 0; // 내용의 cnt
	$array_key = array_keys( $arr ); //array의 키값
	//if($type == 'W_001') { exdebug( $arr ); exdebug( $tmp ); }

	for( $i=0; $i < $tag_cnt ; $i++ ){ //테그별
		$content = ''; //컨텐츠 내용
		$content = ''; //컨텐츠 내용
		for( $j=0; $j < $content_length; $j++ ){ //컨텐츠별
			$temp_content = '';
			if( ( $i == $tag_cnt -1 ) && ( $arr_lastcnt <= $j && $arr_lastcnt != 0 ) ){
				continue; //해당 컨텐츠 내용에 들어갈 값이 없으면 컨티뉴
			} else {
				$temp_content = str_replace( $list_key, $arr[$array_key[$use_cnt]], $list_types[$type]['content'] );    // 컨텐츠를 변환
				$temp_content = str_replace( '[CONTENT_CLASS]', $content_class[$j], $temp_content );                    // 컨텐츠별 클레서 변환

                if ( $type == "W_006" ) {
                    if ( !is_null($icon_info) && !empty($icon_info[$j]) ) {
                        $temp_content = str_replace( '[WEEKLY_BEST_ICON]', "/data/shopimages/best_weekly/".$icon_info[$j], $temp_content );
                    } else {
                        $temp_content = str_replace( '[WEEKLY_BEST_ICON]', "", $temp_content );
                    }
                }
				$temp_content = str_replace( '[IMG_WIDTH]', $img_width[$j], $temp_content );                            // 상품이미지 width 지정
				$temp_content = str_replace( '[IMG_HEIGHT]', $img_height[$j], $temp_content );                          // 상품이미지 height 지정
				$content.= $temp_content;
			}
			$use_cnt++;
		}
		if ( $type == "W_015" ) {
			//tag 제거
			$tags_temp = $content;
		}else{
			$tags_temp = str_replace( '[CONTENT]', $content, $list_types[$type]['tag'] ); //테그안에 컨텐츠를 넣는다
		}


		if ($type=="W_009" && $arr_cnt <= 5) {
			$list_id	= "-none";
		} else {
			$list_id	= "";
		}

		$tags[] = str_replace( '[LIST_ID]', $list_id, $tags_temp ); //테그안에 컨텐츠를 넣는다
	}

	return $tags;

}

/**
 * 금액대별 쪼개기
 * total : ex) 쿠폰할인가 3000
 * arr : ex) 상품가 배열 array(60000, 30000, 50000)
**/
function allot($total,$arr) {
        $res = array();
        $sum = array_sum($arr);
        foreach($arr as $v)
                $res[] = round($v*$total/$sum);
        $res[0] += $total - array_sum($res);
        return $res;
}


//취소관련 함수 파일을 불러온다.
# 2016-02-01 김재수
@include_once($Dir.'lib/libcancel.php' );
@include_once( $Dir.'lib/libcoupon.php' );

//옵션1의 항목을 가져온다
# 2016-01-27 유동혁
function find_option1 ( $options ){
	$opt1 = array();
	foreach( $options as $oVal ) {
		$option = explode( chr(30), $oVal->option_code );
		if( array_search( $option[0], $opt1 ) === false ) $opt1[] = $option[0];
	}

	return $opt1;

}

//벤더명을 가져온다
#2016-01-27 유동혁
function get_vender_name( $vender ){

	global $_data;

	$returnRow = '';
	if( $vender != '0' ){
		$sql = "SELECT brandname, brandname2 FROM tblproductbrand WHERE vender = '".$vender."'"; //tblvenderinfo
		$result = pmysql_query( $sql, get_db_conn() );
		$row = pmysql_fetch_row( $result );
		pmysql_free_result( $result );
		if( strlen(  $row[0] ) > 0 ){
			$returnRow = $row[0];
		} else {
			$returnRow = $row[1];
		}

	} else {
		$returnRow = $_data->shoptitle;
	}
	return $returnRow;

}

//브랜드명을 가져온다
#2016-08-10 김재수
function get_brand_name( $brand ){

	global $_data;

	$returnRow = '';
	if( $brand != '0' ){
		$sql = "SELECT brandname, brandname2 FROM tblproductbrand WHERE bridx = '".$brand."'";
		$result = pmysql_query( $sql, get_db_conn() );
		$row = pmysql_fetch_row( $result );
		pmysql_free_result( $result );
		if( strlen(  $row[0] ) > 0 ){
			$returnRow = $row[0];
		} else {
			$returnRow = $row[1];
		}

	} else {
		$returnRow = $_data->shoptitle;
	}
	return $returnRow;

}

//상품별을 벤더별로 정렬기준을 바꿈
# 2016-01-27 유동혁
function ProductToVender_Sort( $productArr ){
	$venderArr = array();
	foreach( $productArr as $product ){
		$venderArr[ $product['vender'] ][] = $product;
	}
	return $venderArr;
}

//상품별을 브랜드별로 정렬기준을 바꿈
# 2016-08-10 김재수 추가
function ProductToBrand_Sort( $productArr ){
	$brandArr = array();
	foreach( $productArr as $product ){
		//$brandArr[ $product['brand'] ][] = $product;
		$brandArr[ 0 ][] = $product;
	}
	return $brandArr;
}

function ProductToDelivery_Sort( $productArr ){
	$brandArr = array();
	foreach( $productArr as $product ){
		$brandArr[ $product['delivery_type'] ][] = $product;
	}
	return $brandArr;
}
// 프로모션 리스트 구하는 쿼리 생성
function GetPromotionList( $view_type, $keyword, $view_type_val, $view_type_code ) {

    $arrPromotionList = array();

    if ( !empty($view_type_val) && !empty($view_type_code) ) {

        if ( $view_type_val == "C" ) {
            // 카테고리인 경우에만

            $sql  = "SELECT b.idx, c.special_list ";
            $sql .= "FROM tblpromotion a left join tblpromo b on a.promo_idx = b.idx ";
            $sql .= "LEFT JOIN tblspecialpromo c ON a.seq = c.special::integer ";
            $sql .= "WHERE b.display_type in ('A', 'P') AND b.hidden = 1 AND b.event_type = '1' AND c.special_list <> '' ";

            $result = pmysql_query($sql);
            while ( $row = pmysql_fetch_array($result) ) {
                $bridx = $row['idx'];
                $special_list = $row['special_list'];

                $product_list = "'" . str_replace(",", "','", $special_list) . "'";

/*
                if ( $view_type_val == "B" ) {
                    // 브랜드에 해당하는 상품이 있는 기획전을 구한다.
                    $sub_sql  = "SELECT COUNT(*) FROM tblproduct WHERE brand = {$view_type_code} AND productcode IN ( {$product_list} ) ";
                } elseif ( $view_type_val == "C" ) {
                    // 카테고리에 해당하는 상품이 있는 기획전을 구한다.
                    $sub_sql  = "SELECT COUNT(*) ";
                    $sub_sql .= "FROM tblproduct a, tblproductlink b ";
                    $sub_sql .= "WHERE b.c_category like '{$view_type_code}%' AND b.c_maincate = 1 ";
                    $sub_sql .= "AND a.productcode IN ( {$product_list} ) AND a.productcode = b.c_productcode ";
                }
*/

                // 카테고리에 해당하는 상품이 있는 기획전을 구한다.
                $sub_sql  = "SELECT COUNT(*) ";
                $sub_sql .= "FROM tblproduct a, tblproductlink b ";
                $sub_sql .= "WHERE b.c_category like '{$view_type_code}%' AND b.c_maincate = 1 ";
                $sub_sql .= "AND a.productcode IN ( {$product_list} ) AND a.productcode = b.c_productcode ";

                $sub_row = pmysql_fetch_object(pmysql_query($sub_sql));
                if ( $sub_row->count > 0 ) {
                    array_push($arrPromotionList, $bridx);
                }
            }
        }
    }

    // 중복제거
    $arrPromotionList = array_unique($arrPromotionList);

    $sql  = "SELECT * FROM tblpromo ";
    if ( $view_type_val == "C" ) {
        // 카테고리 지정
        if ( count($arrPromotionList) > 0 ) {
            $wherePromotionIdx = "'" . implode("','", $arrPromotionList) . "'";
            $sql .= "WHERE event_type <> '4' AND idx in ( {$wherePromotionIdx} ) AND display_type in ('A', 'P') AND hidden = 1 ";
        } else {
            $sql .= "WHERE event_type <> '4' AND display_type in ('A', 'P') AND hidden = 1 ";   // '전시상태'가 모두 or PC인 경우만
        }
    } elseif ( $view_type_val == "B" )  {
        // 브랜드 지정
        if ( empty($view_type_code) ) {
            $view_type_code = "";
        }

        if ( $view_type_code != "" ) {
            $sql .= "WHERE event_type <> '4' AND bridx_list like '%,{$view_type_code},%' AND display_type in ('A', 'P') AND hidden = 1 ";
        } else {
            $sql .= "WHERE event_type <> '4' AND display_type in ('A', 'P') AND hidden = 1 ";
        }
    } else {
        $sql .= "WHERE event_type <> '4' AND display_type in ('A', 'P') AND hidden = 1 ";
    }

    if ( $view_type === "A" || $view_type === "R" ) {
        // 전체 or 진행중 이벤트
        $sql .= "AND current_date >= start_date ";  // 2016-03-11 jhjeong . 진행중만 가져오기 위해.
        $sql .= "AND current_date <= end_date ";
    } else if ( $view_type === "E" ) {
        // 종료된 이벤트
        $sql .= "AND current_date > end_date ";
    } else if ( $view_type === "W" ) {
        // 당첨자 발표 (이벤트가 종료했으면서 당첨자 발표가 있는 경우)
        //$sql .= "AND current_date >= publication_date and winner_list_content <> '' ";
        $sql .= "AND winner_list_content <> '' ";   // 2016-03-11 jhjeong. 발표일 입력안하는 경우도 있음.
    }

    if ( $keyword != "" ) {
        $tmp_keyword = strtolower($keyword);
        $tmp_keyword = str_replace("'", "''", $tmp_keyword);

        $sql .= "AND lower(title) like '%{$tmp_keyword}%' ";
    }

    // ============================================================
    // 프로모션 시작일이 오래된 순으로 정렬
    // 시작일이 같은 경우 등록순으로 정렬
    // by 최문성 ( 요청 : 조경복과장님 )
    // date : 2016-05-04
    // 2016-05-10 : 시작일 가장 최근것부터 나오게 수정 요청하여 재수정 (요청 : 조경복) by JeongHo, Jeong
    // ============================================================
    //$sql .= "ORDER BY start_date asc, idx::integer desc ";
    $sql .= "ORDER BY start_date desc, idx::integer desc ";

    return $sql;
}

// 프로모션 상세페이지에서 이전/다음용 html 만들기
function GetPromotionViewMore($isMobile) {

    $idx        = $_REQUEST['idx'];
    $view_type  = $_REQUEST['view_type'];
    $view_mode  = $_REQUEST['view_mode'];
    $keyword    = $_REQUEST['keyword'];

    $sql  = "SELECT prev, next, ";
    $sql .= "(select title from tblpromo where idx = tblResult.prev) as prev_title, ";
    $sql .= "(select title from tblpromo where idx = tblResult.next) as next_title ";
    $sql .= "FROM ( ";
    $sql .= "   SELECT ";
    $sql .= "       idx, ";
    $sql .= "       lag(idx) over (ORDER BY rdate desc, idx desc) as prev, ";
    $sql .= "       lead(idx) over (ORDER BY rdate desc, idx desc) as next ";
    $sql .= "   FROM tblpromo ";
    $sql .= "   WHERE event_type <> '4' AND display_type in ('A', 'P') AND hidden = 1 ";

    if ( $view_type === "R" ) {
        // 진행중 이벤트
        $sql .= "AND current_date <= end_date ";
    } else if ( $view_type === "E" ) {
        // 종료된 이벤트
        $sql .= "AND current_date > end_date ";
    } else if ( $view_type === "W" ) {
        // 당첨자 발표 (이벤트가 종료했으면서 당첨자 발표가 있는 경우)
        $sql .= "AND current_date >= publication_date and winner_list_content <> '' ";
    }
    $sql .= ") as tblResult ";
    $sql .= "WHERE idx = '{$idx}' ";

    $result = pmysql_query($sql);
    $row    = pmysql_fetch_object($result);

    // HTML 생성

    if ( !is_null($isMobile) && $isMobile == true ) {
        $linkUrl = "/m/promotion_detail.php?view_mode={$view_mode}&view_type={$view_type}";
        $view_more_html = '
        <div class="btnwrap promo-detail-btn">
            <div class="box">';

        if ( !empty($row->prev) ) {
            $view_more_html .= '<a class="btn-def" href="' . $linkUrl . '&idx=' . $row->prev . '">이전</a>';
        }

        $view_more_html .= '<a class="btn-def" href="/m/promotion.php">목록</a>';

        if ( !empty($row->next) ) {
            $view_more_html .= '<a class="btn-def" href="' . $linkUrl . '&idx=' . $row->next . '">다음</a>';
        }

        $view_more_html .= '
            </div>
        </div>';
    } else {
        $linkUrl = "/front/promotion_detail.php?view_mode={$view_mode}&view_type={$view_type}";

        $view_more_html = '<ul class="view-move">';

        if ( !empty($row->prev) ) {
            $view_more_html .= '<li><span>이전글</span><a href="' . $linkUrl . '&idx=' . $row->prev . '">' . $row->prev_title . '</a></li>';
        }
        if ( !empty($row->next) ) {
            $view_more_html .= '<li><span>다음글</span><a href="' . $linkUrl . '&idx=' . $row->next . '">' . $row->next_title . '</a></li>';
        }

        $view_more_html .= '</ul>';
    }

    return $view_more_html;
}

// 포토이벤트 상세페이지에서 이전/다음용 html 만들기
function GetPhotoEventViewMore() {

    $num        = $_REQUEST['num'];
    $idx        = $_REQUEST['idx'];
    $event_type = $_REQUEST['event_type'];
    $view_type  = $_REQUEST['view_type'];
    $view_mode  = $_REQUEST['view_mode'];
    $keyword    = $_REQUEST['keyword'];

    $sql  = "SELECT ";
    $sql .= "   prev, next, ";
    $sql .= "   (select title from tblboard_promo where num = tblResult.prev) as prev_title, ";
    $sql .= "   (select title from tblboard_promo where num = tblResult.next) as next_title ";
    $sql .= "FROM ( ";
    $sql .= "   SELECT num, ";
    $sql .= "       lag(num) over (ORDER BY num desc) as prev, ";
    $sql .= "       lead(num) over (ORDER BY num desc) as next ";
    $sql .= "   FROM tblboard_promo ";
    $sql .= "   WHERE board = 'photo' ";
    $sql .= ") as tblResult WHERE num = {$num} ";

    $result = pmysql_query($sql);
    $row    = pmysql_fetch_object($result);

    // HTML 생성
    $view_more_html = '<ul class="view-move">';

    $linkUrl = "?event_type={$event_type}&view_mode={$view_mode}&view_type={$view_type}&idx={$idx}";
    if ( !empty($row->prev) ) {
        $view_more_html .= '<li><span>이전글</span><a href="' . $linkUrl . '&num=' . $row->prev . '">' . $row->prev_title . '</a></li>';
    }
    if ( !empty($row->next) ) {
        $view_more_html .= '<li><span>다음글</span><a href="' . $linkUrl . '&num=' . $row->next . '">' . $row->next_title . '</a></li>';
    }

    $view_more_html .= '</ul>';

    return $view_more_html;
}

// DECO&E용 로케이션 2016-02-01 유동혁
// 두번째 파라미터로 직접 카테고리 코드를 주면 해당 카테고리로 생성
function getDecoCodeLoc( $code, $cate_code = null ) {

    if ( $cate_code == null ) {
        // 일단 코드값으로 카테고리 정보를 구한다.
        $sql  = "SELECT c_category FROM tblproductlink ";
        $sql .= "WHERE c_maincate = 1 AND c_productcode = '{$code}' LIMIT 1";
        list($tmp_code) = pmysql_fetch($sql);

        // 만약 조회한 카테고리값이 있으면 해당 값으로 사용
        if ( !empty($tmp_code) ) {
            $code = $tmp_code;
        }
    } else {
        $code = $cate_code;
    }

	$code_loc = "";
	$sql = "SELECT code_name, code_a||code_b||code_c||code_d as category FROM tblproductcode WHERE code_a='".substr($code,0,3)."' ";
	if(substr($code,3,3)!="000") {
		$sql.= "AND (code_b='".substr($code,3,3)."' OR code_b='000') ";
		if(substr($code,6,3)!="000") {
			$sql.= "AND (code_c='".substr($code,6,3)."' OR code_c='000') ";
			if(substr($code,9,3)!="000") {
				$sql.= "AND (code_d='".substr($code,9,3)."' OR code_d='000') ";
			} else {
				$sql.= "AND code_d='000' ";
			}
		} else {
			$sql.= "AND code_c='000' ";
		}
	} else {
		$sql.= "AND code_b='000' AND code_c='000' ";
	}
	$sql.= "ORDER BY code_a,code_b,code_c,code_d ASC ";
	$result=pmysql_query($sql,get_db_conn());
	$_=array();
	while($row=pmysql_fetch_object($result)) {
		$_[] = $row;
	}
	$code_loc = $_;
	pmysql_free_result($result);
	return $code_loc;
}

/*
    출석체크에 사용할 달력을 그리는 함수
*/
function draw_calendar($mem_id, $idx, $year, $month, $weekly_icon, $weekend_icon, $isMobile) {

    // 출석체크한 내용 조회
    $arrDays = array();
    if ( $mem_id != "" ) {
        // 로그인 한경우에만 조회
        $sql  = "SELECT * FROM tblattendancerecord WHERE id = '{$mem_id}' AND promo_idx = {$idx} ";
        $sql .= "ORDER BY idx asc ";
        $result = pmysql_query($sql);

        // 출석체크한 날짜들로 배열 인덱스를 지정한다.
        while ($row = pmysql_fetch_array($result) ) {
            $arrDays[$row['day']] = "";
        }
    }

    $today = ltrim(date("d"), "0");                              // 오늘 일자
    $running_day = date('w',mktime(0,0,0,$month,1,$year));      // 해당월의 첫날의 요일번호(0:일요일 ~ 6:토요일)
    $days_in_month = date('t',mktime(0,0,0,$month,1,$year));    // 해당월의 마지막 날

    $calendar = '';

    // 달력 앞 부분을 채운다. (빈 공간)
    for($x = 0; $x < $running_day; $x++):
        $calendar.= '<li></li>';
    endfor;

    // 일별로 생성
    for($list_day = 1; $list_day <= $days_in_month; $list_day++) {
        $calendar.= '<li>';

        if ( $running_day >= 1 && $running_day <= 5 ) {
            // 주중
            $iconUrl = '/data/shopimages/timesale/' . $weekly_icon;

            if ( $list_day == $today ) {
                // 오늘 날짜인 경우

                if ( isset($arrDays[$list_day]) ) {
                    // 출석체크를 한 날인경우
                    $pos = -108;
                    $addClass = "ok";
                } else {
                    $pos = 0;
                    $addClass = "today";
                }

            } elseif ( isset($arrDays[$list_day]) ) {
                // 출석체크를 한 날인경우
                $pos = -108;
                $addClass = "ok";
            } else {
                $pos = -216;
                $addClass = "";
            }

            $calendar.= '<button class="check ' . $addClass . '" type="button" style="background:url(\'' . $iconUrl . '\') ' . $pos . 'px 0 no-repeat"></button><span class="day">' . $list_day;
        } else {
            // 주말
            $iconUrl = '/data/shopimages/timesale/' . $weekend_icon;

            if ( $list_day == $today ) {
                // 오늘 날짜인 경우

                if ( isset($arrDays[$list_day]) ) {
                    // 출석체크를 한 날인경우
                    $pos = -260;
                    $addClass = "ok";
                } else {
                    $pos = -130;
                    $addClass = "today";
                }
            } elseif ( isset($arrDays[$list_day]) ) {
                // 출석체크를 한 날인경우
                $pos = -260;
                $addClass = "ok";
            } else {
                $pos = 0;
                $addClass = "";
            }

            if ( $running_day == 6 ) {
                $addDayClass = "sat";   // 토요일
            } else {
                $addDayClass = "sun";   // 일요일
            }

            $calendar.= '<button class="check-holiday ' . $addClass . '" type="button" style="background:url(\'' . $iconUrl . '\') ' . $pos . 'px 0 no-repeat"></button><span class="day ' . $addDayClass . '">' . $list_day;
        }

        $calendar .= '</li>';

        $running_day++;

        // 일요일은 7이 아니라 0으로 셋팅
        if ( $running_day == 7 ) { $running_day = 0; }
    }

    // 나머지 남은 공간을 채운다.
    for ( $list_day = 0; $list_day < 7 - $running_day; $list_day++ ) {
        $calendar.= '<li></li>';
    }

    return $calendar;
}
#상품 아이콘 가져오기 2016-02-02 유동혁
function get_viewIcon( $icon ){
	global $Dir;

	$iconname = '';

	if( strlen( $icon ) > 0 ){
		$num = strlen( $icon );
		for( $i = 0; $i < $num; $i += 2 ){
			$temp = $icon[$i].$icon[$i+1];
			if( preg_match( "/^(U)[1-6]$/", $temp ) ) {
				$iconname .= " <img src=\"".$Dir.DataDir."shopimages/etc/icon{$temp}.gif\" align=absmiddle border=0>";
			} elseif ( strlen( $temp ) && !preg_match("/^(U)[1-6]$/", $temp ) ) {
				$iconname .= " <img src=\"{$Dir}images/common/icon{$temp}.gif\" align=absmiddle border=0>";
			}
		}
	}

	return $iconname;
}

#상품 아이콘 가져오기 2016-02-02 유동혁
function get_mobile_viewIcon( $icon ){
	global $Dir;

	$iconname = '';

	if( strlen( $icon ) > 0 ){
		$num = strlen( $icon );
		for( $i = 0; $i < $num; $i += 2 ){
			$temp = $icon[$i].$icon[$i+1];
			if( preg_match( "/^(U)[1-6]$/", $temp ) ) {
				$iconname .= " <li><span class='tag-def' ><img src=\"".$Dir.DataDir."shopimages/etc/icon{$temp}.gif\" align=absmiddle border=0></span></li>";
			} elseif ( strlen( $temp ) && !preg_match("/^(U)[1-6]$/", $temp ) ) {
				$iconname .= " <li><span class='tag-def' ><img src=\"{$Dir}images/common/icon{$temp}.gif\" align=absmiddle border=0></span></li>";
			}
		}
	}

	return $iconname;
}

#옵션정보 불러오기 ( 항목명 불러오기 )
# 2016-02-02 유동혁 뎁스별 정보
function get_option_code( $productcode, $option_type = 0, $opt_depth = 0 ) {

	$option_sql = "SELECT option_code FROM tblproduct_option WHERE productcode = '".$productcode."' AND option_type = '".$option_type."' ORDER BY option_num ASC ";
	$option_res = pmysql_query( $option_sql, get_db_conn() );
	$opt = array();
	$returnArr = array();
	while( $option_row = pmysql_fetch_object( $option_res ) ) {
		$val = explode(chr(30), $option_row->option_code);
		$key = $opt_depth;

		if(!strlen($val[$key]))
			continue;

		$continue = false;
		foreach($opt as $v) {
			if(strval($v) === strval($val[$key])) {
				$continue = true;
				break;
			}
		}
		if($continue)
			continue;

		$opt[] = strval($val[$key]);

		$returnArr[$val[$key]]['code'] = $val[$key];
	}
	pmysql_free_result( $option_res );

	return $returnArr;
}

// 옵션의 재고 (창고재고수량 - 주문대기수량)
function get_option_quantity($productcode, $option_code, $type)
{

    $sql = " SELECT option_quantity
                FROM tblproduct_option
                WHERE productcode = '".$productcode."' AND option_code = '".$option_code."' AND option_type = '".$type."' AND option_use = '1' ";

    $row = pmysql_fetch($sql);
    $jaego = (int)$row['option_quantity'];

    // 재고에서 빼지 않았고 주문인것만
    $sql = " SELECT SUM(quantity) AS sum_qty
               FROM tblorderproduct
              WHERE productcode = '".$productcode."'
                AND opt1_name||opt2_name = '".$option_code."'
                AND deli_gbn in ('N', 'H', 'S') ";
    $row = pmysql_fetch($sql);
    $daegi = (int)$row['sum_qty'];

    return $jaego - $daegi;
}

# depth별 옵션정보 2016-02-18 유동혁
/**
* 함수명 :get_option
* depth별 옵션정보를 가져온다
* parameter :
* 	- string $productcode : 상품코드
* 	- string $option_code : 옵션코드
* 	- int $option_depth : 옵션 depth
* return :
*	- array( '옵션코드' => array( code=>'옵션명',price=>'옵션가',qty=>'수량',soldout=>'품절유무 1 - 품절, 0 - 판매가능' ) )
*/
function get_option( $productcode, $option_code = '', $option_depth = 0, $option_type='' ){

	list($opt1, $quantity) = pmysql_fetch_array(pmysql_query( "SELECT option1, quantity FROM tblproduct WHERE productcode = '".$productcode."'" ));
	$tmpOpt1 = explode( ',', $opt1 );
	$sel_count = count( $tmpOpt1 );

	$sql = " SELECT * FROM tblproduct_option
					WHERE option_type = '0'
					  AND productcode = '".$productcode."'
					  AND option_use = '1' ";
	if ($option_type != 'all') $sql .= " AND option_code like '".$option_code."%' ";
	//$sql .= "ORDER BY option_num asc ";
    $sql .= "ORDER BY option_code asc ";    // erp연동시 나중에 작은사이즈가 추가되서 사이즈별 정렬을 위해..2016-10-11
	//exdebug($sql);
	$result = pmysql_query($sql);

	$opt = array();
	$returnArr = array();
	for($i=0; $row=pmysql_fetch_array($result); $i++) {

		$val = explode(chr(30), $row['option_code']);
		$key = $option_depth;

		if(!strlen($val[$key]))
			continue;

		$continue = false;
		foreach($opt as $v) {
			if(strval($v) === strval($val[$key])) {
				$continue = true;
				break;
			}
		}
		if($continue)
			continue;

		$opt[] = strval($val[$key]);

		if($key + 1 < $sel_count) {
			$returnArr[$val[$key]]['option_code'] = $row['option_code'];
			$returnArr[$val[$key]]['code'] = $val[$key];
			$returnArr[$val[$key]]['price'] = '';
			$returnArr[$val[$key]]['qty'] = '';
			$returnArr[$val[$key]]['soldout'] = '0';
		} else {
			$returnArr[$val[$key]]['option_code'] = $row['option_code'];
			$returnArr[$val[$key]]['code'] = $val[$key];
			$returnArr[$val[$key]]['price'] = $row['option_price'];

			if ($option_type !='all') {
				$io_stock_qty = get_option_quantity($productcode, $row['option_code'], $row['option_type']);
			} else {
				$io_stock_qty = $row['option_quantity'];
			}
			if ($quantity < 999999999 ) { // 수량이 무한이 아닐 경우
				$returnArr[$val[$key]]['qty'] = $io_stock_qty;

			if($io_stock_qty < 1)
				$returnArr[$val[$key]]['soldout'] = '1';
			else
				$returnArr[$val[$key]]['soldout'] = '0';
			} else {
				$returnArr[$val[$key]]['qty'] = $quantity;
				$returnArr[$val[$key]]['soldout'] = '0';
			}
		}
	}

	return $returnArr;
}
# 독립형 옵션정보 2016-02-29 유동혁
/**
* 함수명 :get_option
* 독립형 옵션정보를 가져온다
* parameter :
* 	- string $productcode : 상품코드
* return :
*	- array(  )
*/
function get_alone_option( $productcode ){

	$sql = "SELECT  option_code, option_price, option_quantity, option_quantity_noti, option_type, option_use, option_tf ";
	$sql.= "FROM tblproduct_option WHERE productcode ='".$productcode."' AND option_type = 1 ";
	$result = pmysql_query( $sql, get_db_conn() );
	$options = array();
	while( $row = pmysql_fetch_object( $result ) ){
		$tmp_option = explode( chr(30), $row->option_code );
		$options[$tmp_option[0]][$tmp_option[1]] = $row;
	}
	pmysql_free_result( $result );

	return $options;
}

# 독립형 옵션정보 2016-02-29 유동혁
/**
* 함수명 :get_option
* 독립형 옵션정보를 가져온다
* parameter :
* 	- string $productcode : 상품코드
* return :
*	- array(  )
*/
function mobile_get_alone_option( $productcode, $option_code = '' ){

	$sql = "SELECT  option_code, option_price, option_quantity, option_quantity_noti, option_type, option_use, option_tf ";
	$sql.= "FROM tblproduct_option WHERE productcode ='".$productcode."' AND option_code LIKE '".$option_code."%' ";
    $sql.= "AND option_type = 1 AND option_use = 1 ";
	$result = pmysql_query( $sql, get_db_conn() );
	$options = array();
	while( $row = pmysql_fetch_object( $result ) ){
		$tmp_option = explode( chr(30), $row->option_code );
		$options[] =  array(
                            'code'       => $tmp_option[1],
                            'price'      => $row->option_price,
                            'option_code'=> $row->option_code,
                            'qty'        => $row->option_quantity
                        );
	}
	pmysql_free_result( $result );

	return $options;
}

# 페이스북용 메타테그 2016-02-11 유동혁
function FacebookShare( $productcode ){

	global $Dir, $_data, $_ShopInfo;
	$imgPath = 'data/shopimages/product/';

	$meta_tags = '';
	$sql = "SELECT productname, maximage, mdcomment FROM tblproduct WHERE productcode ='".$productcode."' ";
	$result = pmysql_query( $sql, get_db_conn() );

	$row = pmysql_fetch_object( $result );
	if( $row ){
        $primg = getProductImage( $imgPath, $row->maximage );
        $imglink = '';
        if(strpos($primg, "http://") === false) {
            $imglink = 'http://'.$_SERVER['HTTP_HOST'].'/'.$imgPath.$row->maximage;
        } else {
            $imglink = $primg;
        }
		$meta_tags = '<meta property="og:url"           content="http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'].'" />';
		$meta_tags.= '<meta property="og:type"          content="website" />';
		$meta_tags.= '<meta property="og:title"         content="'.$_data->shoptitle.'" />';
		$meta_tags.= '<meta property="og:description"   content="'.$row->productname.'" />';
		$meta_tags.= '<meta property="og:image"         content="'.$imglink.'" />';
	}
	pmysql_free_result( $result );

	return $meta_tags;

}

# 트위터용 메타테그 2016-02-11 유동혁
function TwitterShare( $productcode ){

	global $Dir, $_data, $_ShopInfo;
	$imgPath = 'data/shopimages/product/';

	$meta_tags = '';
	$sql = "SELECT productname, maximage, mdcomment FROM tblproduct WHERE productcode ='".$productcode."' ";
	$result = pmysql_query( $sql, get_db_conn() );

	$row = pmysql_fetch_object( $result );
	if( $row ){
        $primg = getProductImage( $imgPath, $row->maximage );
        $imglink = '';
        if(strpos($primg, "http://") === false) {
            $imglink = 'http://'.$_SERVER['HTTP_HOST'].'/'.$imgPath.$row->maximage;
        } else {
            $imglink = $primg;
        }
		$meta_tags.= '<meta name="twitter:card" content="summary_large_image">';
		$meta_tags.= '<meta name="twitter:site" content="@'.$_data->shoptitle.'">';
		$meta_tags.= '<meta name="twitter:title" content="'.$_data->shoptitle.'">';
		$meta_tags.= '<meta name="twitter:description" content="'.$row->productname.'" >';
		$meta_tags.= '<meta name="twitter:image" content="'.$imglink.'">';
	}
	pmysql_free_result( $result );

	return $meta_tags;

}

### 날짜 출력형식
function toDate($date,$div)
{
 return sprintf("%04d{$div}%02d{$div}%02d",substr($date,0,4),substr($date,4,2),substr($date,6,2));
}

### 배열 null 제거 함수
function array_notnull($arr)
{
 if (!is_array($arr)) return;
 foreach ($arr as $k=>$v) if ($v=="") unset($arr[$k]);
 return $arr;
}


/**
* 함수명 : MakeHeaderPreviewList
* 헤더부분 장바구니, 위시리스트, 오늘본상품용 HTML 만들기
* parameter :
* 	- string $className     : 상품리스트 class명
*   - integer $count        : 상품수
*	- string $products      : 상품리스트
*	- string $more_url      : 더보기 링크
* return :
*	- string                : html source
*/
function MakeHeaderPreviewList( $className, $count, $products, $more_url ) {

    global $Dir;
    global $_ShopInfo;

    $total_count        = 8;        // 최대 노출 건수
    $page_per_count     = 4;        // 페이지당 노출 건수
    $total_page         = 2;        // 최대 페이지 수
    $total_prod_count   = $count;   // 해당 상품 수
    $total_page_count   = ceil(count($products) / $page_per_count);    // 총 노출 페이지 수
    if ( $total_page_count >= $total_page ) { $total_page_count = $total_page; }

    $product_list_html = '';

    $arrIdx = 0;
    for ( $i = 0; $i < $total_page_count; $i++ ) {
        $product_list_html .= '<ul>';
        for ( $j = 0; $j < $page_per_count; $j++ ) {
            if ( isset($products[$arrIdx]) ) {
                $prod = $products[$arrIdx];

                $imgPath = getProductImage($Dir."data/shopimages/product/", $prod['image']);

                $product_list_html .= '
                    <li> 
                        <a href="/front/productdetail.php?productcode=' . $prod['code'] . '"> 
                        <span class="img"><img src="' . $imgPath . '" alt="" width="110" height="110"></span>
                        <div class="info_con">';

/*
                $product_list_html .= '<div class="label">';
                $product_list_html .= getIconHtml($prod['icon']);
                $product_list_html .= '</div>';
*/

                $product_list_html .= '
                            <span class="cate">' . $prod['brandname'] . '</span> 
                            <span class="name">' . $prod['name'] . '</span> 
                            <span class="price">';

                $consumer_class = "";
                if ( $prod['consumerprice'] <= 0 || $prod['consumerprice'] == $prod['sellprice'] ){
                    $consumer_class = "hide";
                }

                $sellprice = '<strong>' . number_format( $prod['sellprice'] ) . '</strong>';
                $consumerprice = number_format( $prod['consumerprice'] );
                if( ( $prod['quantity'] <= 0 || $prod['soldout'] == 'Y' ) && strlen( $prod['soldout'] ) > 0 && strlen( $prod['quantity'] ) > 0 ){
                    // soldout icon 추가
                    $sellprice = "<span><img src=\"{$Dir}images/common/icon_soldout.gif\" border=0 align=absmiddle></span>";
                    $consumerprice = '';
                }

                $product_list_html .= '<del class="' . $consumer_class . '">' . $consumerprice . '</del>';
                $product_list_html .= $sellprice;
                $product_list_html .= '</span>';
                $product_list_html .= '
                           </div>
                        </a>
                    </li>';

                $arrIdx++;
            }
        }

        $product_list_html .= '</ul>';
    }

    if ( !empty($more_url) ) {
//        $moreUrlLink = "javascript:location='{$more_url}';";
        $moreUrlLink = $more_url;
    } else {
        $moreUrlLink = "javascript:;";
    }

    $title = strtoupper($className);
    if ( $className == "basket" ) {
        $title = "BAG";
    }

    $rolling_products_html = '
            <li class="' . $className . '" ids="' . $total_page_count . '">
                <a href="' . $moreUrlLink . '"><span class="count">' . number_format($total_prod_count) . '</span><p>' . $title . '</p></a>
                <div class="list_wrap">';

    if ( !empty($more_url) ) {
//        $rolling_products_html .= '<button type="button" class="more" onClick="' . $moreUrlLink . '"><span>SEE MORE</span></button>';
        $rolling_products_html .= '<a class="more" href="' . $moreUrlLink . '">SEE MORE</a>';
    }

    // 내용이 없는 경우
    if ( empty($product_list_html) ) {

        if ( $className == "basket" ) {
            $msg = "장바구니에 담긴 상품이 없습니다.";
        } elseif ( $className == "wish" ) {
            if ( strlen($_ShopInfo->getMemid()) > 0 ) {
                $msg = "위시리스트에 담긴 상품이 없습니다.";
            } else {
                $msg = "관심상품은 회원전용입니다. <br>로그인 후 이용해 주세요.";
            }
        } elseif ( $className == "view" ) {
            $msg = "최근 본 상품이 없습니다.";
        }

        $product_list_html = '
            <div class="gnb-goods-none">
                <img src="../static/img/icon/gnb_goods_none.gif" alt="">
                <p>' . $msg . '</p>
            </div>
        ';
    }

    $rolling_products_html .= '
                    <div class="gnb-4ea-rolling" id="page-rolling-' . $className . '">
                        ' . $product_list_html . '
                    </div>';

    if ( $total_page_count >= 1 ) {
        $rolling_products_html .= '<div class="page-num" id="page-' . $className . '"><strong>1</strong> / ' . $total_page_count . '</div>';
    }

    $rolling_products_html .= '
                </div>
            </li>';

    return $rolling_products_html;
}


# 상품의 메인카테고리 정보를 받아온다 2016-02-18 유동혁
/**
* 함수명 :PossibleCoupon
* 특정 상품의 사용가능 쿠폰확인
* parameter :
* 	- string $productcode : 상품코드
* return :
*	- array( 카테고리정보 ) or bool false
*/
function ProductMainCate( $productcode ){

	$cate = array();

	$sql = "
		SELECT
		a.*, b.c_maincate, b.c_category
		FROM tblproductcode a
		,tblproductlink b
		WHERE a.code_a||a.code_b||a.code_c||a.code_d = b.c_category
		AND c_maincate = 1
		AND group_code = ''
		AND c_productcode = '{$productcode}'
	";
	$result=pmysql_query($sql,get_db_conn());
	while($row=pmysql_fetch_object($result)){
		if($row->c_maincate == 1){
			$cate[] = $row;
		}
		$cateProduct[] = $row;
	}

	if( count( $cate ) > 0 ){
		return $cate[0];
	} else {
		return false;
	}
}


// 화면에 표시할 아이디 생성(앞 3자리만 빼고 나머지를 *로 표시)
function setIDEncryp($id) {
    $len = strlen($id);
    $id = str_pad(substr($id, 0, 3), $len, "*", STR_PAD_RIGHT);
    return $id;
}

// 화면에 표시할 이메일 아이디 생성(@앞자리만)
function setEmailEncryp($id) {
	$id_arr	= explode("@",$id);
    $id		= $id_arr[0];
    return $id;
}

// 상품에 사용할 아이콘 HTML 생성
function getIconHtml($icon, $type=null) {
    global $Dir;
    global $_ShopInfo;
    global $isMobile;

    $icon_html = "";
    if ( !empty($icon) ) {
        $iconLen = strlen($icon);
        $loopCnt = $iconLen / 2;
        for ( $i = 0; $i < $loopCnt; $i++ ) {
            $iconCode = substr($icon, $i*2, 2);
            if ( $type == "W_015" || $type == "W_016" || $type == "W_017" || $type == "W_018" ) {
                // 모바일
                $icon_html .= "<span class=\"img\"><img src=\"{$Dir}images/common/icon{$iconCode}.gif\" border=0 align=absmiddle></span>";
            } else if ( $type == "MO_001" || $type == "MO_002" || $type == "MO_003" || $type == "SMO_001") {
                // 모바일
                $icon_html .= "<span class=\"tag\"><img src=\"{$Dir}images/common/icon{$iconCode}.gif\"></span>";
            } else {
                // PC버젼
                $icon_html .= "<span class=\"img\"><img src=\"{$Dir}images/common/icon{$iconCode}.gif\" border=0 align=absmiddle></span>";
            }
        }
    }

    return $icon_html;
}

// 정가 표시
function getConsumerPriceHtml( $consumerprice, $quantity, $soldout ) {
    global $Dir;
    global $_ShopInfo;

    $consumerprice = number_format( $consumerprice );
    if( ( $quantity <= 0 || $soldout == 'Y' ) && strlen( $soldout ) > 0 && strlen( $quantity ) > 0 ){ // soldout icon 추가
        $sellprice = "<span><img src=\"{$Dir}images/common/icon_soldout.gif\" border=0 align=absmiddle></span>";
        $consumerprice = '';
    }

    return $consumerprice;
}

// ========================================================
// 모바일버젼에서 사용하는 내용들
// ========================================================

// 상단 검색 옵션 select box (공통)
/*
function getSearchOptForMobile( $sort ) {
    $arrSelected = array();
    $arrSelected['order'] = "";
    $arrSelected['best'] = "";
    $arrSelected['sale'] = "";
    $arrSelected['rcnt_desc'] = "";
    $arrSelected['price'] = "";
    $arrSelected['price_desc'] = "";

    $arrSelected[$sort] = "selected";

    $selectHtml = '
        <div class="select-def">
            <select onchange="javascript:changeSort_Mobile(this);">
                <option value="order" ' . $arrSelected['order'] . '>NEW</option>
                <option value="best" ' . $arrSelected['best'] . ' >BEST</option>
                <option value="sale" ' . $arrSelected['sale'] . ' >SALE</option>
                <option value="rcnt_desc" ' . $arrSelected['rcnt_desc'] . ' >REVIEW</option>
                <option value="price" ' . $arrSelected['price'] . ' >LOWPRICE</option>
                <option value="price_desc" ' . $arrSelected['price_desc'] . ' >HIGHPRICE</option>
            </select>
        </div>
    ';

    return $selectHtml;
}
*/

// 상품리스트 상단, 브랜드 메인 상단 카테고리 select box html
function makeCategorySelectHtml( $cate_code, $rep_code_a ) {

    list($code_a,$code_b,$code_c,$code_d) = sscanf($cate_code,'%3s%3s%3s%3s');
    if(strlen($code_a)!=3) $code_a="000";
    if(strlen($code_b)!=3) $code_b="000";
    if(strlen($code_c)!=3) $code_c="000";
    if(strlen($code_d)!=3) $code_d="000";

    $arrCategoryHtml = array();

    if ( empty($rep_code_a) ) {
        $arrCategoryHtml[0] = "";   // 상품리스트인 경우는 ALL이 있으면 안됨. 적어도 1차 카테고리 코드는 있어야 함.

        // 1차
        $sql  = "SELECT code_a,code_b,code_c,code_d, code_a||code_b||code_c||code_d as cate_code,code_name,idx ";
        $sql .= "FROM tblproductcode ";
        $sql .= "WHERE code_b = '000' ";
        $sql .= "AND group_code !='NO' AND display_list is NULL ";
        $sql .= "ORDER BY cate_sort ASC";
        $result = pmysql_query($sql);
        while ( $row = pmysql_fetch_object($result) ) {
            $selected = "";
            if ( $row->code_a == $code_a ) {
                $selected = "selected";
            }

            $arrCategoryHtml[0] .= "<option value='{$row->cate_code}' {$selected}>{$row->code_name}</option>";
        }
        pmysql_free_result($result);
    } elseif ( $rep_code_a == "ss" ) {
        // 스마트서치인 경우
        // 1차
        $sql  = "SELECT code_a,code_b,code_c,code_d, code_a||code_b||code_c||code_d as cate_code,code_name,idx ";
        $sql .= "FROM tblproductcode ";
        $sql .= "WHERE code_b = '000' ";
        $sql .= "AND group_code !='NO' AND display_list is NULL ";
        $sql .= "ORDER BY cate_sort ASC";
        $result = pmysql_query($sql);

        $arrCategoryHtml[0]  = "<option value=''>ALL</option>";
        while ( $row = pmysql_fetch_object($result) ) {
            $selected = "";
            if ( $row->code_a == $code_a ) {
                $selected = "selected";
            }

            $arrCategoryHtml[0] .= "<option value='{$row->cate_code}' {$selected}>{$row->code_name}</option>";
        }
        pmysql_free_result($result);

    } else {
        $sql  = "SELECT code_a, code_a||code_b||code_c||code_d as cate_code, code_name ";
        $sql .= "FROM tblproductcode ";
        $sql .= "WHERE code_a = '{$rep_code_a}' AND code_b = '000' ";
        $sql .= "AND group_code !='NO' AND display_list is NULL ";
        $sql .= "ORDER BY cate_sort ASC";
        list($_code_a,$_cate_code, $_code_name) = pmysql_fetch($sql);

        $arrCategoryHtml[0]  = "<option value=''>ALL</option>";

        $selected = "";
        if ( $_code_a == $code_a ) {
            $selected = "selected";
        }
        $arrCategoryHtml[0] .= "<option value='{$_cate_code}' {$selected}>{$_code_name}</option>";
    }
    $arrCategoryHtml[1] = "<option value='{$code_a}'>ALL</option>";
    $arrCategoryHtml[2] = "<option value='{$code_a}{$code_b}'>ALL</option>";
    $arrCategoryHtml[3] = "<option value='{$code_a}{$code_b}{$code_c}'>ALL</option>";

    // 2차
    if ( $code_a != "000" ) {
        $sql  = "SELECT code_a,code_b,code_c,code_d, code_a||code_b||code_c||code_d as cate_code,code_name,idx ";
        $sql .= "FROM tblproductcode ";
        $sql .= "WHERE code_a = '{$code_a}' AND code_b <> '000' AND code_c = '000' ";
        $sql .= "AND group_code !='NO' AND display_list is NULL ";
        $sql .= "ORDER BY cate_sort ASC";
        $result = pmysql_query($sql);
        while ( $row = pmysql_fetch_object($result) ) {
            $selected = "";
            if ( $row->code_b == $code_b ) {
                $selected = "selected";
            }

            $arrCategoryHtml[1] .= "<option value='{$row->cate_code}' {$selected}>{$row->code_name}</option>";
        }
        pmysql_free_result($result);
    }

    // 3차
    if ( $code_b != "000" ) {
        $sql  = "SELECT code_a,code_b,code_c,code_d, code_a||code_b||code_c||code_d as cate_code,code_name,idx ";
        $sql .= "FROM tblproductcode ";
        $sql .= "WHERE code_a = '{$code_a}' AND code_b = '{$code_b}' AND code_c <> '000' AND code_d = '000' ";
        $sql .= "AND group_code !='NO' AND display_list is NULL ";
        $sql .= "ORDER BY cate_sort ASC";
        $result = pmysql_query($sql);
        while ( $row = pmysql_fetch_object($result) ) {
            $selected = "";
            if ( $row->code_c == $code_c ) {
                $selected = "selected";
            }

            $arrCategoryHtml[2] .= "<option value='{$row->cate_code}' {$selected}>{$row->code_name}</option>";
        }
        pmysql_free_result($result);
    }

    // 4차
    if ( $code_c != "000" ) {
        $sql  = "SELECT code_a,code_b,code_c,code_d, code_a||code_b||code_c||code_d as cate_code,code_name,idx ";
        $sql .= "FROM tblproductcode ";
        $sql .= "WHERE code_a = '{$code_a}' AND code_b = '{$code_b}' AND code_c = '{$code_c}' AND code_d <> '000' ";
        $sql .= "AND group_code !='NO' AND display_list is NULL ";
        $sql .= "ORDER BY cate_sort ASC";
        $result = pmysql_query($sql);
        while ( $row = pmysql_fetch_object($result) ) {
            $selected = "";
            if ( $row->code_d == $code_d ) {
                $selected = "selected";
            }

            $arrCategoryHtml[3] .= "<option value='{$row->cate_code}' {$selected}>{$row->code_name}</option>";
        }
        pmysql_free_result($result);
    }

    $categoryHtml = '
        <div class="container">
            <div class="select-def">
                <select onChange="javascript:changeCategory(this);" class="SEARCH_SELECT" >' . $arrCategoryHtml[0] . '</select>
            </div>
            <div class="box">
                <div class="select-def">
                    <select onChange="javascript:changeCategory(this);" class="SEARCH_SELECT" >' . $arrCategoryHtml[1] . '</select>
                </div>
            </div>
        </div>
        <div class="container">
            <div class="select-def">
                <select onChange="javascript:changeCategory(this);" class="SEARCH_SELECT">' . $arrCategoryHtml[2] . '</select>
            </div>
            <div class="box">
                <div class="select-def">
                    <select onChange="javascript:changeCategory(this);" class="SEARCH_SELECT">' . $arrCategoryHtml[3] . '</select>
                </div>
            </div>
        </div>';

    return $categoryHtml;
}

function option_slice( $content, $option_type = 0 ){

    if( $option_type == 0 ) {
        $tmp_content = explode( chr(30), $content );
    } else {
        $tmp_content = explode( '@#', $content );
    }

    return $tmp_content;

}

// 협력사 포인트 지급 / 차감 2016-05-10 유동혁
function insert_cooper_point( $mem_id, $point, $body='', $rel_flag='', $rel_mem_id='', $rel_job='', $expire=0  ) {
    global $_data;

    // 포인트가 없다면 업데이트 할 필요 없음
    if ($point == 0) { return 0; }

    // 회원아이디가 없다면 업데이트 할 필요 없음
    if ($mem_id == '') { return 0; }
    $mb = pmysql_fetch(" SELECT id FROM tblmember WHERE id = '$mem_id' AND cooper_yn = 'Y' ");
    if (!$mb['id']) { return 0; }

    // 회원포인트
    $mb_point = get_point_cooper_sum( $mem_id );

    // 이미 등록된 내역이라면 건너뜀
    if ($rel_flag || $rel_mem_id || $rel_job)
    {
        $sql = " SELECT count(*) as cnt FROM tblpoint_cooper
                  WHERE mem_id = '$mem_id'
                    AND rel_flag = '$rel_flag'
                    AND rel_mem_id = '$rel_mem_id'
                    AND rel_job = '$rel_job' ";
        $row = pmysql_fetch( $sql );
        if ($row['cnt'])
            return -1;
    }

    // 포인트 건별 생성
    // expire : 1 => 만료
    $expire_date = '99991231';
   
    $expire_chk = 0;
    if($point < 0) {
        $expire_chk = 1;
        $expire_date = date("Ymd");
    }
    $tot_point = $mb_point + $point;

    $sql = "INSERT INTO tblpoint_cooper (mem_id, regdt, body, point, use_point, tot_point, expire_chk, expire_date, rel_flag ,rel_mem_id, rel_job)
            VALUES 
            ('$mem_id', '".date("YmdHis")."', '".addslashes($body)."', '$point', '0', '$tot_point', '$expire_chk', '$expire_date', '$rel_flag', '$rel_mem_id', '$rel_job') 
            ";
    pmysql_query($sql);
    //echo "sql3 = ".$sql."<br>";

	// 협력사 포인트 ERP 전송 (김재수 - 2017.04.12 추가)
	erpTotalPointIns("cooperpoint", $mem_id, addslashes($body), $rel_flag, $rel_job, $point, date("Ymd"));

    // 포인트를 사용한 경우 포인트 내역에 사용금액 기록
    if($point < 0) {
        insert_use_cooper_point( $mem_id, $point );
    }

    // 포인트 UPDATE
    $sql = " UPDATE tblmember SET cooper_reserve = '$tot_point' WHERE id = '$mem_id' ";
    pmysql_query($sql);
    //echo "sql4 = ".$sql."<br>";

    return 1;
}

// 협력사 포인트 내역 합계 2016-05-10 유동혁
function get_point_cooper_sum( $mem_id )
{
    global $_data;
    // 포인트합
    $sql = " SELECT COALESCE( SUM( point ), 0 ) AS sum_point
                FROM tblpoint_cooper
                WHERE mem_id = '$mem_id' ";
    $row = pmysql_fetch($sql);
    //echo "sql16 = ".$sql."<br>";

    return $row['sum_point'];
}

// 협력사 사용포인트 입력 2016-05-10 유동혁
function insert_use_cooper_point( $mem_id, $point, $pid = 0 )
{
    global $_data;

    if($_data->reserve_term)
        $sql_order = " ORDER BY expire_date ASC, pid ASC ";
    else
        $sql_order = " ORDER BY pid ASC ";

    $point1 = abs($point);
    $sql = " SELECT pid, point, use_point
                FROM tblpoint_cooper
                WHERE mem_id = '$mem_id'
                  AND pid <> '$pid'
                  AND expire_chk = '0'
                  AND point > use_point
                $sql_order ";
    $result = pmysql_query($sql);
    //echo "sql5 = ".$sql."<br>";

    for($i=0; $row=pmysql_fetch_array($result); $i++) {
        $point2 = $row['point'];
        $point3 = $row['use_point'];
        //echo "point1 = ".$point1."<br>";
        //echo "point2 = ".$point2."<br>";
        //echo "point3 = ".$point3."<br>";

        if(($point2 - $point3) > $point1) {
            $sql = " UPDATE tblpoint_cooper
                        SET use_point = use_point + '$point1'
                        WHERE pid = '{$row['pid']}' ";
            pmysql_query($sql);
            //echo "sql6 = ".$sql."<br>";
            break;
        } else {
            $point4 = $point2 - $point3;
            $sql = " UPDATE tblpoint_cooper
                        SET use_point = use_point + '$point4',
                            expire_chk = '99'
                        WHERE pid = '{$row['pid']}' ";
            pmysql_query($sql);
            //echo "sql7 = ".$sql."<br>";
            $point1 -= $point4;
        }
    }
}


// 임직원 포인트 지급 / 차감 2016-05-10 유동혁
function insert_staff_point( $mem_id, $point, $body='', $rel_flag='', $rel_mem_id='', $rel_job='', $expire=0  ) {
    global $_data;

    // 포인트 사용을 하지 않는다면 return
    //if ($_data->reserve_maxuse < 0) { return 0; }

    // 포인트가 없다면 업데이트 할 필요 없음
    if ($point == 0) { return 0; }

    // 회원아이디가 없다면 업데이트 할 필요 없음
    if ($mem_id == '') { return 0; }
    $mb = pmysql_fetch(" SELECT id FROM tblmember WHERE id = '$mem_id' AND staff_yn = 'Y' ");
    //echo " select id from tblmember where id = '$mem_id' "."<br>";
    if (!$mb['id']) { return 0; }

    // 회원포인트
    $mb_point = get_point_staff_sum( $mem_id );
    //echo "mb_point = ".$mb_point."<br>";

    // 이미 등록된 내역이라면 건너뜀
    if ($rel_flag || $rel_mem_id || $rel_job)
    {
        $sql = " SELECT count(*) as cnt FROM tblpoint_staff
                  WHERE mem_id = '$mem_id'
                    AND rel_flag = '$rel_flag'
                    AND rel_mem_id = '$rel_mem_id'
                    AND rel_job = '$rel_job' ";
        $row = pmysql_fetch( $sql );
        //echo "sql2 = ".$sql."<br>";
        if ($row['cnt'])
            return -1;
    }

    // 포인트 건별 생성
    // expire : 1 => 만료
    $expire_date = '99991231';
    /* 소멸 포인트가 존재하지 않음
    if($_data->reserve_term > 0) {
        if($expire > 0) {
            //$expire_date = date('Ymd', strtotime('+'.($expire - 1).' days', time()));
			$lastdate	= date("t",strtotime('+'.($expire - 1).' days', time()));
			$expire_date = date('Ym', strtotime('+'.($expire - 1).' days', time())).$lastdate;
        } else {
            //$expire_date = date('Ymd', strtotime('+'.($_data->reserve_term - 1).' days', time()));
			$lastdate	= date("t",strtotime('+'.($_data->reserve_term - 1).' days', time()));
			$expire_date = date('Ym', strtotime('+'.($_data->reserve_term - 1).' days', time())).$lastdate;
		}
    }
    */
    $expire_chk = 0;
    if($point < 0) {
        $expire_chk = 1;
        $expire_date = date("Ymd");
    }
    $tot_point = $mb_point + $point;

    $sql = "INSERT INTO tblpoint_staff (mem_id, regdt, body, point, use_point, tot_point, expire_chk, expire_date, rel_flag ,rel_mem_id, rel_job)
            VALUES 
            ('$mem_id', '".date("YmdHis")."', '".addslashes($body)."', '$point', '0', '$tot_point', '$expire_chk', '$expire_date', '$rel_flag', '$rel_mem_id', '$rel_job') 
            ";
    pmysql_query($sql);
    //echo "sql3 = ".$sql."<br>";

	// 임직원 포인트 ERP 전송 (김재수 - 2017.04.12 추가)
	erpTotalPointIns("staffpoint", $mem_id, addslashes($body), $rel_flag, $rel_job, $point, date("Ymd"));

    // 포인트를 사용한 경우 포인트 내역에 사용금액 기록
    if($point < 0) {
        insert_use_staff_point( $mem_id, $point );
    }

    // 포인트 UPDATE
    $sql = " UPDATE tblmember SET staff_reserve = '$tot_point' WHERE id = '$mem_id' ";
    pmysql_query($sql);
    //echo "sql4 = ".$sql."<br>";

    return 1;
}

// 임직원 포인트 내역 합계 2016-05-10 유동혁
function get_point_staff_sum( $mem_id )
{
    global $_data;
    // 소멸 포인트가 존재하지 않음
    /*
    if($_data->reserve_term > 0) {
        // 소멸포인트가 있으면 내역 추가
        $expire_point = get_expire_point_staff( $mem_id );
        //echo "expire_point = ".$expire_point."<br>";
        if($expire_point > 0) {
            $mb = get_member( $mem_id, 'reserve' );
            $body = '포인트 소멸';
            $rel_flag = '@expire';
            $rel_mem_id = $mem_id;
            $rel_job = 'expire'.'-'.uniqid('');
            $point = $expire_point * (-1);
            $tot_point = $mb['reserve'] + $point;
            $expire_date = date("Ymd");
            $expire_chk = 1;

            $sql = "INSERT INTO tblpoint_staff (mem_id, regdt, body, point, use_point, tot_point, expire_chk, expire_date, rel_flag ,rel_mem_id, rel_job)
            VALUES
            ('$mem_id', '".date("YmdHis")."', '".addslashes($body)."', '$point', '0', '$tot_point', '$expire_chk', '$expire_date', '$rel_flag', '$rel_mem_id', '$rel_job')
            ";
            pmysql_query($sql);
            //echo "sql14 = ".$sql."<br>";

            // 포인트를 사용한 경우 포인트 내역에 사용금액 기록
            if($point < 0) {
                insert_use_staff_point($mem_id, $point);
            }
        }

        // 유효기간이 있을 때 기간이 지난 포인트 => expired 체크
        $sql = " UPDATE tblpoint_staff
                    SET expire_chk = '1'
                    WHERE mem_id = '$mem_id'
                      AND expire_chk <> '1'
                      AND expire_date <> '99991231'
                      AND expire_date < '".date("Ymd")."' ";
        pmysql_query($sql);
        //echo "sql15 = ".$sql."<br>";
    }
     */
	$ryear = date("Y").'0101000000';
	// 포인트합
    $sql = " SELECT COALESCE( SUM( point ), 0 ) AS sum_point
                FROM tblpoint_staff
                WHERE mem_id = '$mem_id' and regdt > '$ryear'";
    $row = pmysql_fetch($sql);
    //echo "sql16 = ".$sql."<br>";

    return $row['sum_point'];
}

// 임직원 소멸 포인트 ( 현제 사용 안함 )  2016-05-10 유동혁
function get_expire_point_staff( $mem_id )
{
    global $_data;

    if($_data->reserve_term == 0)
        return 0;

    $sql = " SELECT COALESCE( SUM( point - use_point ), 0 ) AS sum_point
                FROM tblpoint_staff
                WHERE mem_id = '$mem_id'
                  AND expire_chk = '0'
                  AND expire_date <> '99991231'
                  AND expire_date < '".date("Ymd")."' ";
    $row = pmysql_fetch($sql);
    //echo "sql17 = ".$sql."<br>";

    return $row['sum_point'];
}

// 임직원 사용포인트 입력 2016-05-10 유동혁
function insert_use_staff_point( $mem_id, $point, $pid = 0 )
{
    global $_data;

    if($_data->reserve_term)
        $sql_order = " ORDER BY expire_date ASC, pid ASC ";
    else
        $sql_order = " ORDER BY pid ASC ";

    $point1 = abs($point);
    $sql = " SELECT pid, point, use_point
                FROM tblpoint_staff
                WHERE mem_id = '$mem_id'
                  AND pid <> '$pid'
                  AND expire_chk = '0'
                  AND point > use_point
                $sql_order ";
    $result = pmysql_query($sql);
    //echo "sql5 = ".$sql."<br>";

    for($i=0; $row=pmysql_fetch_array($result); $i++) {
        $point2 = $row['point'];
        $point3 = $row['use_point'];
        //echo "point1 = ".$point1."<br>";
        //echo "point2 = ".$point2."<br>";
        //echo "point3 = ".$point3."<br>";

        if(($point2 - $point3) > $point1) {
            $sql = " UPDATE tblpoint_staff
                        SET use_point = use_point + '$point1'
                        WHERE pid = '{$row['pid']}' ";
            pmysql_query($sql);
            //echo "sql6 = ".$sql."<br>";
            break;
        } else {
            $point4 = $point2 - $point3;
            $sql = " UPDATE tblpoint_staff
                        SET use_point = use_point + '$point4',
                            expire_chk = '99'
                        WHERE pid = '{$row['pid']}' ";
            pmysql_query($sql);
            //echo "sql7 = ".$sql."<br>";
            $point1 -= $point4;
        }
    }
}

// 회원 마일리지 / 임직원 마일리지 적립 및 차감 분기 function 2016-05-10 유동혁
function insert_order_point( $ordercode, $mem_id, $point='0', $body='', $rel_flag='', $rel_mem_id='', $rel_job='', $expire=0, $idx='', $epoint='0' ) {

    // 임직원 구매 정보를 확인함
    $qry_join  = '';
    $qry_and = '';
    if( $idx != '' ){
        $qry_join = " JOIN tblorderproduct op ON ( oi.ordercode = op.ordercode ) ";
        $qry_and = " AND idx = '".$idx."' ";
    }

    $sql    = "SELECT oi.staff_order, oi.cooper_order ";
    $sql   .= "FROM tblorderinfo oi ".$qry_join;
    $sql   .= "WHERE oi.ordercode ='".$ordercode."' AND oi.id ='".$mem_id."' ".$qry_and;

    $result = pmysql_query( $sql, get_db_conn() );
    $row    = pmysql_fetch_object( $result );
    pmysql_free_result( $result );

    //리턴받을 값 초기화
    $return_data = 0;

    if( $row->staff_order == 'Y' ){
        // 임직원 포인트 처리
        $return_data = insert_staff_point( $mem_id, $point, $body, $rel_flag, $rel_mem_id, $rel_job, $expire );
    }else if( $row->cooper_order == 'Y' ){
        // 협력사 포인트 처리
        $return_data = insert_cooper_point( $mem_id, $point, $body, $rel_flag, $rel_mem_id, $rel_job, $expire );
		if($point)$return_data = "1";
		if($epoint)$return_data = insert_point_act( $mem_id, $epoint, $body, $rel_flag, $rel_mem_id, $rel_job, $expire );
	} else if( $row->staff_order == 'N' && $row->cooper_order == 'N') {
        // 일반 포인트 처리
		$mem_auth_type	= getAuthType($mem_id);
		if ($mem_auth_type != 'sns') { // 정회원일 경우에만 지급
			//if($point)$return_data = insert_point( $mem_id, $point, $body, $rel_flag, $rel_mem_id, $rel_job, $expire );
			if($point)$return_data = "1";
			if($epoint)$return_data = insert_point_act( $mem_id, $epoint, $body, $rel_flag, $rel_mem_id, $rel_job, $expire );
		}
    } else {
        $return_data = -2;
    }

    return $return_data;

}

# 임직원 구매 확인 2016-05-10 유동혁
/**
* 함수명 :chk_staff_order
* 임직원 구매 확인
* parameter :
* 	- string $staff_order : 임직원 구매 ( Y - 임직원 구매, N - 일반구매 )
* return :
*	- int $resturn_data : 구매type 확인값 0 - 잘못된 구매, 1 - 일반구매, 2 - 임직원 구매,
*/
function chk_staff_order ( $staff_order ){
    global $_ShopInfo;

    $return_data = 0;

    if( $staff_order == 'Y' ){
        if( $_ShopInfo->staff_yn == 'Y' ){
            $return_data = 2;
        } else {
            $return_data = 0;
        }
    } else {
        $return_data = 1;
    }

    return $return_data;
}

function chk_cooper_order ( $chk_cooper_order ){
    global $_ShopInfo;

    $return_data = 0;

    if( $chk_cooper_order == 'Y' ){
        if( $_ShopInfo->cooper_yn == 'Y' ){
            $return_data = 2;
        } else {
            $return_data = 0;
        }
    } else {
        $return_data = 1;
    }

    return $return_data;
}

/**
 * 금액대별 쪼개기
 * reserve   : ex) 쿠폰할인가 3000
 * price_arr : ex) 상품가 배열 array(60000, 30000, 50000)
 * deli_arr  : ex) 배송료 배열 array(    0,  2500,     0)
**/

function rate_allot( $reserve, $price_arr, $deli_arr, $point=0 ) {

    if( count( $price_arr ) != count( $deli_arr ) ) return false;
    $return_data       = array();
    //$total_amt         = array_sum($price_arr) + array_sum($deli_arr);
	$total_amt         = array_sum($price_arr);
    $total_res         = array();
	$total_poi         = array();


    for( $i = 0; $i < count( $price_arr ); $i++ ) {

        //$total_res[] = round( ( $price_arr[$i] + $deli_arr[$i] ) * $reserve / $total_amt );
		//$total_poi[] = round( ( $price_arr[$i] + $deli_arr[$i] ) * $point / $total_amt );
		$total_res[] = round( ( $price_arr[$i] ) * $reserve / $total_amt );
		$total_poi[] = round( ( $price_arr[$i] ) * $point / $total_amt );

		//$total_res[] = round( ( $price_arr[$i] + $deli_arr[$i] ) * $reserve / $total_amt );
    }

	$total_max_res = max( $total_res );
    $total_max_key = array_search( $total_max_res, $total_res );
    $total_res[$total_max_key] = $total_max_res + ( $reserve - array_sum( $total_res ) );

	$total_max_poi = max( $total_poi );
    $total_max_keyp = array_search( $total_max_poi, $total_poi );
    $total_poi[$total_max_keyp] = $total_max_poi + ( $point - array_sum( $total_poi ) );

	
    $point_res = array();
    $epoint_res = array();
    for( $j = 0; $j < $i; $j++ ){
		//$point_res[] = round( $price_arr[$j] * $total_res[$j] / ( $price_arr[$j] + $deli_arr[$j] ) );
		//$epoint_res[] = round( $price_arr[$j] * $total_poi[$j] / ( $price_arr[$j] + $deli_arr[$j] ) );

		$point_res[] = round( $price_arr[$j] * $total_res[$j] / ( $price_arr[$j] ) );
		$epoint_res[] = round( $price_arr[$j] * $total_poi[$j] / ( $price_arr[$j] ) );
        
    }

	/*
    $price_res = array();
    $price_per = array();
    $deli_res  = array();
    $deli_per  = array();
    for( $j = 0; $j < $i; $j++ ){
		
        $price_res[] = round( $price_arr[$j] * $total_res[$j] / ( $price_arr[$j] + $arr_reserve[$j] ) );
        $price_per[] = round( ( ($price_arr[$j] - $total_poi[$j] ) / $total_amt ) * 100 );
		$price_res_p[] = round( $price_arr[$j] * $total_poi[$j] / ( $price_arr[$j] + $arr_point[$j] ) );
        $price_per[] = round( ( ($price_arr[$j] - $total_poi[$j] ) / $total_amt ) * 100 );
		

		$deli_res3[]  = round( $deli_arr[$j] * $total_res3[$j] / ( $price_arr[$j] + $deli_arr[$j] ) );
        $price_res3[] = round( $price_arr[$j] * $total_res3[$j] / ( $price_arr[$j] + $deli_arr[$j] ) );

		
		//$price_res[] = round( $price_arr[$j] * $total_res[$j] / ( $price_arr[$j] + $deli_arr[$j] ) );
        //$price_per[] = round( ( $price_arr[$j] / $total_amt ) * 100 );
        //$deli_res[]  = round( $deli_arr[$j] * $total_res[$j] / ( $price_arr[$j] + $deli_arr[$j] ) );
        //$deli_per[]  = round( ( $deli_arr[$j] / $total_amt ) * 100 );
		
    }

    $max_price_per = max( $price_per );
    $max_price_key = array_search( $max_price_per, $price_per );
    $max_deli_per  = max( $deli_per );
    $max_deli_key  = array_search( $max_deli_per, $deli_per );

    if( $price_per[$max_price_key] > $deli_per[$max_deli_key] ){
        $price_per[$max_price_key] = $max_price_per + ( 100 - array_sum( $price_per ) - array_sum( $deli_per ) );
    } else {
        $deli_per[$max_deli_key] = $max_deli_per + ( 100 - array_sum( $price_per ) - array_sum( $deli_per ) );
    }

    $return_data = array(
        'op_price'      => $price_arr,
        'op_reserve'    => $price_res,
        'op_rate'       => $price_per,
        'deli_price'    => $deli_arr,
        'deli_reserve'  => $deli_res,
        'deli_rate'     => $deli_per,
		'point_reserve' => $total_poi
    );
*/
	$return_data = array(
        'op_point'     => $point_res,
		'op_epoint' => $epoint_res
    );

    return $return_data;
}
/*
function rate_allot( $reserve, $price_arr, $deli_arr ) {

    if( count( $price_arr ) != count( $deli_arr ) ) return false;
    $return_data       = array();
    $total_amt         = array_sum($price_arr) + array_sum($deli_arr);
    $total_res         = array();

    for( $i = 0; $i < count( $price_arr ); $i++ ) {
        $total_res[] = round( ( $price_arr[$i] + $deli_arr[$i] ) * $reserve / $total_amt );
    }

    $total_max_res = max( $total_res );
    $total_max_key = array_search( $total_max_res, $total_res );
    $total_res[$total_max_key] = $total_max_res + ( $reserve - array_sum( $total_res ) );

    $price_res = array();
    $price_per = array();
    $deli_res  = array();
    $deli_per  = array();

    for( $j = 0; $j < $i; $j++ ){
        $price_res[] = round( $price_arr[$j] * $total_res[$j] / ( $price_arr[$j] + $deli_arr[$j] ) );
        $price_per[] = round( ( $price_arr[$j] / $total_amt ) * 100 );
        $deli_res[]  = round( $deli_arr[$j] * $total_res[$j] / ( $price_arr[$j] + $deli_arr[$j] ) );
        $deli_per[]  = round( ( $deli_arr[$j] / $total_amt ) * 100 );
    }

    $max_price_per = max( $price_per );
    $max_price_key = array_search( $max_price_per, $price_per );
    $max_deli_per  = max( $deli_per );
    $max_deli_key  = array_search( $max_deli_per, $deli_per );

    if( $price_per[$max_price_key] > $deli_per[$max_deli_key] ){
        $price_per[$max_price_key] = $max_price_per + ( 100 - array_sum( $price_per ) - array_sum( $deli_per ) );
    } else {
        $deli_per[$max_deli_key] = $max_deli_per + ( 100 - array_sum( $price_per ) - array_sum( $deli_per ) );
    }

    $return_data = array(
        'op_price'      => $price_arr,
        'op_reserve'    => $price_res,
        'op_rate'       => $price_per,
        'deli_price'    => $deli_arr,
        'deli_reserve'  => $deli_res,
        'deli_rate'     => $deli_per
    );

    return $return_data;
}
*/
// 접속 경로
function get_join_rote(){

    $mobileBrower = '/(iPod|iPhone|Android|BlackBerry|SymbianOS|SCH-M\d+|Opera Mini|Windows CE|Nokia|SonyEricsson|webOS|PalmOS|iPad)/';
    $app          = get_session( "ACCESS" );
    $srcipt_uri   = parse_url( $_SERVER['SCRIPT_URI'] );
    $join_rate    = '';
    if( $app == 'app' ) {
        $join_rate = 'T'; // app 접속
    } else if( preg_match( $mobileBrower, $_SERVER['HTTP_USER_AGENT'] ) ){
        $join_rate = 'M'; // mobile 접속
    } else {
        $join_rate = 'P'; // pc 접속
    }

    return $join_rate;

}


// 주문상태별 상태값 리턴
function GetStatusOrder($check, $oi_step1, $oi_step2="", $op_stepp="", $redelivery_type="", $order_conf="") {

    global $o_step, $op_step;

    $status = "";
    if($check == "o") { //주문별

        $status = $o_step[$oi_step1][$oi_step2];
        if($redelivery_type == "G" && $oi_step2 == "40") $status = "교환신청";
        if($redelivery_type == "G" && $oi_step2 == "41") $status = "교환접수";
        if($redelivery_type == "G" && $oi_step2 == "44") $status = "교환완료";
		if($order_conf == "1") $status = "구매확정";

    } else if($check == "p") {  //상품별
        if($op_stepp >= 40) $status = $o_step[$oi_step1][$op_stepp];
        else $status = $op_step[$op_stepp];
		if($redelivery_type == "G" && $op_stepp == "40") $status = "교환신청";
        if($redelivery_type == "G" && $op_stepp == "41") $status = "교환접수";
        if($redelivery_type == "G" && $op_stepp == "44") $status = "교환완료";
        if($redelivery_type == "N" && $op_stepp == "44") $status = "취소완료";
		if($order_conf == "1") $status = "구매확정";
    }

    return $status;
}

//핸드폰 번호 - 추가
function addMobile($num){
	return preg_replace("/(^02.{0}|^01.{1}|[0-9]{3})([0-9]+)([0-9]{4})/", "$1-$2-$3", $num);
}

// 최근본상품 30개 넘는거 검색
function Get_Over_Recent_Product($mem_id, $offset) {

    $sql = "select rno from tblproduct_recent where mem_id = '".$mem_id."' order by regdt desc limit 100 offset ".$offset." ";
    $result = pmysql_query( $sql, get_db_conn() );
    while($row = pmysql_fetch_object($result)){
        $var[] = $row;
    }
    pmysql_free_result( $result );

    return $var;
}

//인증방식을 가져온다.
function getAuthType($id) {
	list($auth_type)=pmysql_fetch_array(pmysql_query("SELECT auth_type FROM tblmember WHERE id='{$id}'"));

	return $auth_type;
}

//전체 카테고리를 array 배열로 가져온다 (2016.08.24 - 김재수 추가)
function getAllCategoryList() {
	//1차 카테고리를 가져온다.
	$cateList_sql = "
		SELECT code_a,code_b,code_c,code_d, code_a||code_b||code_c||code_d as cate_code,code_name,idx
		FROM tblproductcode
		WHERE ( group_code !='NO' AND is_hidden = 'N' ) AND display_list is NULL
		ORDER BY code_a,code_b,code_c,code_d ASC , cate_sort ASC";

		$cateList_res = pmysql_query($cateList_sql,get_db_conn());

		while($cateList_row = pmysql_fetch_object($cateList_res)){
			if ($cateList_row->code_d !='000') {
				$cate_info[$cateList_row->code_a]['code_b'][$cateList_row->code_b]['code_c'][$cateList_row->code_c]['code_d'][$cateList_row->code_d]['name']	= $cateList_row->code_name;
			} else if ($cateList_row->code_c !='000') {
				$cate_info[$cateList_row->code_a]['code_b'][$cateList_row->code_b]['code_c'][$cateList_row->code_c]['name']	= $cateList_row->code_name;
			} else if ($cateList_row->code_b !='000') {
				$cate_info[$cateList_row->code_a]['code_b'][$cateList_row->code_b]['name']	= $cateList_row->code_name;
			} else {
				$cate_info[$cateList_row->code_a]['name']	= $cateList_row->code_name;
			}
		}

		return $cate_info;
}

//전체 브랜드를 object 배열로 가져온다 (2016.08.24 - 김재수 추가)
function getAllBrandList() {
	// ===============================================================================
	// 브랜드 리스트 ( 나이키 최우선으로 나오기 위해 rno 추가...2016-09-30)
	// ===============================================================================
	$sql  = "SELECT tblResult.bridx, tblResult.brandname, tblResult.brandname2, tblResult.vender, tblResult.logo_img ";
	$sql .= "FROM ( ";
	$sql .= "   SELECT bridx, brandname, brandname2, vender,logo_img, brand_sort";
	$sql .= "   FROM tblproductbrand ";
	$sql .= "   WHERE display_yn = 1 order by brand_sort asc";
	$sql .= ") as tblResult ";

	// 업체 승인이 "승인"인 것들만 조회
	$sql .= "LEFT JOIN tblvenderinfo tvi ON tblResult.vender = tvi.vender ";
	$sql .= "WHERE tvi.disabled = 0 ";
	//$sql .= "ORDER BY rno asc, tblResult.brandname asc ";
	$sql .= "ORDER BY tblResult.brand_sort asc ";

	//echo $sql;


	$result = pmysql_query($sql);

	$brandList = array();
	while ( $row = pmysql_fetch_object($result) ) {
		$brandList[]	= $row;
	}
	pmysql_free_result($result);

	return $brandList;
}

// 기본 쿼리중 전체 상품수 및 카테고리별 상품수를 구한다. (통합검색에서 사용 2016.08.25-김재수 추가)
function getCateProductCnt($sql_qry){
	$count_info		= array();
	$def_res			= pmysql_query("SELECT sp.productcode,pl.c_category FROM (".$sql_qry.") as sp LEFT JOIN tblproductlink pl ON sp.productcode = pl.c_productcode ");
	$count_info['all_t_count']	= pmysql_num_rows($def_res);
	$all_cp_count	= array();
	if ( pmysql_num_rows($def_res) > 0 ) {
		while ( $def_row = pmysql_fetch_object($def_res) ) {
			list($code_a,$code_b,$code_c,$code_d) = sscanf($def_row->c_category,'%3s%3s%3s%3s');
			$code_a		= $code_a!='000'?$code_a:'';
			$code_b		= $code_b!='000'?$code_b:'';
			$code_c		= $code_c!='000'?$code_c:'';
			$code_d		= $code_d!='000'?$code_d:'';

			if ($code_a) {
				$cate_a_code							= $code_a;
				$all_cp_count[$cate_a_code]	= $all_cp_count[$cate_a_code]!=''?$all_cp_count[$cate_a_code]+1:1;
			}
			if ($code_b) {
				$cate_b_code							= $code_a.$code_b;
				$all_cp_count[$cate_b_code]	= $all_cp_count[$cate_b_code]!=''?$all_cp_count[$cate_b_code]+1:1;
			}
			if ($code_c) {
				$cate_c_code							= $code_a.$code_b.$code_c;
				$all_cp_count[$cate_c_code]	= $all_cp_count[$cate_c_code]!=''?$all_cp_count[$cate_c_code]+1:1;
			}
			if ($code_d) {
				$cate_d_code							= $code_a.$code_b.$code_c.$code_d;
				$all_cp_count[$cate_d_code]	= $all_cp_count[$cate_d_code]!=''?$all_cp_count[$cate_d_code]+1:1;
			}
		}
	}
	$count_info['all_c_count']	= $all_cp_count;
	pmysql_free_result($subresult);
	return $count_info;
}

#상품 이름 가져오기
function getProductName($productcode){

	$sql = "SELECT productname FROM tblproduct WHERE productcode = '".$productcode."' AND display = 'Y'";
	$result = pmysql_query( $sql, get_db_conn() );
	if($row=pmysql_fetch_object($result)) {
		$productname = $row->productname;
	}
	pmysql_free_result( $result );
	return $productname;
}

/* Return Color Data Function */
function dataColor(){
    ### 컬러
    $sql = "select * from tblproduct_color order by sort asc, cno asc";
    $result = pmysql_query($sql);

    while($color_data = pmysql_fetch_object($result)){
        $color_loop[] = $color_data;
    }

    return $color_loop;
}
//호감/비호감 type별 갯수
function totalFeeling($num, $section, $feeling_type){
	/*$section = 각 게시물 타입 ex) 리뷰 : review, 댓글 : comment
		$feeling_type = 호감 : good, 비호감 : bad
	*/
	$sql = "SELECT * FROM tblgood_feeling WHERE code = '{$num}' AND feeling_type = '{$feeling_type}' AND section = '{$section}'";
	$result = pmysql_query($sql);
	$count = pmysql_num_rows( $result );

	return $count;
}

//조회수 증가(조회수 컬럼이 있는 테이블만)
function accessPlus($table, $column, $seqColumn, $no){
	$sql = "UPDATE ".$table." SET ".$column."=".$column."+1";
	$sql.= " WHERE ".$seqColumn." = '{$no}' ";
	pmysql_query($sql);
}

//매장 코드 넘기면 매장데이터 리턴
function getStoreData($storeCode){
    $sql = "SELECT * FROM tblstore WHERE store_code = '".$storeCode."'";
    $data =  pmysql_fetch_array(pmysql_query($sql));

    return $data;
}

// 시간이 지나면 일반 배송제외하곤 삭제
function delDeliveryTypeData(){
	GLOBAL $_ShopInfo;

	if ( strlen($_ShopInfo->getMemid()) > 0 ) {
		$whereQueryId = "id = '" . $_ShopInfo->getMemid() . "' ";
	} else {
		$whereQueryId = "tempkey='".$_ShopInfo->getTempkey()."' AND id = '' ";
	}
	$msg = "";
    list($count0) =  pmysql_fetch_array(pmysql_query("SELECT count(*) FROM tblbasket WHERE (delivery_type = '1' AND replace(reservation_date, '-', '') < '".date('Ymd')."') AND ".$whereQueryId));
	if($count0 > 0){
		# 매장 픽업 예약일이 오늘보다 낮으면 삭제
		$delSql0 = "DELETE FROM tblbasket WHERE (delivery_type = '1' AND replace(reservation_date, '-', '') < '".date('Ymd')."') AND ".$whereQueryId;
		pmysql_query($delSql0);
		$msg = "매장픽업 예약일이 지난 상품을 삭제 했습니다.";
	}

	if( 22 < date('H') ){
		list($count1) =  pmysql_fetch_array(pmysql_query("SELECT count(*) FROM tblbasket WHERE (delivery_type = '1' AND reservation_date = '".date('Y-m-d')."') AND ".$whereQueryId));
		if($count1 > 0){
			# 매장 픽업 23시가 지난 후 오늘 날짜와 같으면 삭제
			$delSql1 = "DELETE FROM tblbasket WHERE (delivery_type = '1' AND reservation_date = '".date('Y-m-d')."') AND ".$whereQueryId;
			pmysql_query($delSql1);
			$msg = "매장픽업 예약일이 지난 상품을 삭제 했습니다.";
		}
	}

	if( 14 < date('H') ){
		list($count2) =  pmysql_fetch_array(pmysql_query("SELECT count(*) FROM tblbasket WHERE (delivery_type = '2') AND ".$whereQueryId));
		if($count2 > 0){
			# 당일 수령 15시가 지나면 삭제
			$delSql2 = "DELETE FROM tblbasket WHERE (delivery_type = '2') AND ".$whereQueryId;
			pmysql_query($delSql2);
			if($msg){
				$msg .= "\\r";
			}
			$msg .= "당일수령 주문 가능시간이 지난 상품을 삭제 했습니다.";
		}
	}
	if($msg) msg($msg);
}

// 시간이 지나면 일반 배송제외하고 체크하여 값 리턴
function gelDeliveryTypeFlagReturn(){
	GLOBAL $_ShopInfo;

	$count0 = $count1 = $count2 = 0;

	if ( strlen($_ShopInfo->getMemid()) > 0 ) {
		$whereQueryId = "id = '" . $_ShopInfo->getMemid() . "' ";
	} else {
		$whereQueryId = "tempkey='".$_ShopInfo->getTempkey()."' AND id = '' ";
	}


    list($count0) =  pmysql_fetch_array(pmysql_query("SELECT count(*) FROM tblbasket WHERE (delivery_type = '1' AND replace(reservation_date, '-', '') < '".date('Ymd')."') AND ".$whereQueryId));
	if( 22 < date('H') ){
		list($count1) =  pmysql_fetch_array(pmysql_query("SELECT count(*) FROM tblbasket WHERE (delivery_type = '1' AND reservation_date = '".date('Y-m-d')."') AND ".$whereQueryId));
	}

	if( 14 < date('H') ){
		list($count2) =  pmysql_fetch_array(pmysql_query("SELECT count(*) FROM tblbasket WHERE (delivery_type = '2') AND ".$whereQueryId));
	}

	if($count0 > 0 || $count1 > 0 || $count2 > 0){
		return false;
	}else{
		return true;
	}
}

// 등급변경 및 메일 발송하기
function ChangeGrade($id) {

    global $_data;

    // 도메인 정보
    $sql        = "SELECT shopurl FROM tblshopinfo LIMIT 1 ";
    $row        = pmysql_fetch_object(pmysql_query($sql));
    $shopurl    = $row->shopurl;
    //exdebug($shopurl);

    // 등급별 정보
    $sql = "SELECT  group_code, group_name, group_level, group_ap_s, group_ap_e 
            FROM    tblmembergroup 
            ORDER BY group_code 
            ";
    $ret = pmysql_query($sql);
    $grade = array();
    $M_grade_sql = "(case ";
    while($row = pmysql_fetch_object($ret)) {

        //echo $row->group_code."<br>";
        $grade[$row->group_code] = $row;
        $M_grade_sql .= "when coalesce(m.act_point, 0) >= ".$row->group_ap_s." and coalesce(m.act_point, 0) <= ".$row->group_ap_e." then '".$row->group_code."' ";
    }
    $M_grade_sql .= "end) as af_group ";

    $sql = "select  m.id, m.name, m.email, m.news_yn, coalesce(NULLIF(m.group_code, ''), '0001') as bf_group, coalesce(m.act_point, 0) as act_point, 
            ".$M_grade_sql."  
            from 	tblmember m 
            where	m.id = '".$id."' 
            ";
    list($id, $name, $email, $news_yn, $bf_group, $act_point, $af_group) = pmysql_fetch($sql, get_db_conn());

    if($bf_group != $af_group) {
        // =========================================================================
        // 등급 갱신 및 히스토리 저장
        // =========================================================================
        $u_query = "update tblmember set group_code = '".$af_group."' where id = '".$id."'";
        pmysql_query( $u_query, get_db_conn() );

        $h_query = "insert into tblmemberchange 
                    (mem_id, before_group, after_group, accrue_price, change_date) 
                    values 
                    ('".$id."', '".$grade[$bf_group]->group_name."', '".$grade[$af_group]->group_name."', '".$act_point."', '".date("Y-m-d")."')
                    ";
        pmysql_query( $h_query, get_db_conn() );

        #SendGradeMail($_data->shopname, $shopurl, $_data->design_mail, $_data->info_email, $id, $name, $bf_group, $af_group, $email, $news_yn);
    }
}

/**
 *  네이버 신디케이션 호출 함수
 *  $menu : 'forum' or 'magazine' or 'lookbook' or 'product'
 *  $bbsno : 해당 게시글의 일련번호
 *  $type : 등록/수정일때는 'reg', 삭제일때는 'del'
**/
function callNaver($menu, $bbsno, $type) {
    //$auth_key = "AAAANl1jRvlJB5s+PK9t3jsVk6mrnFjqf2N5yO5rNXYk8abP0T2VYCUNXkKmlzAeLqWlTkjnn8Cgs6w41ndUV0nLP1o=";
    //$auth_key = "AAAARoy72YAuPC/7nyZwYtBqd9SuQbOYRwFUsKrnNkhQTejrDHiYhpX+sErz/uM5B4DeFcDq5QicMSxLEKV7XXxXMPLC4n/qp9dH0OYU3LR1iR60";
    $url = "http://test-hott.ajashop.co.kr/partner/call_make_syndi.php?menu=".$menu."&bbsno=".$bbsno."&type=".$type;
    $ping_auth_header = "Authorization: Bearer $auth_key"; /* Bearer 타입의 인증키 정보 */
    $ping_url = urlencode($url); /* 신디케이션 문서를 담고 있는 핑 URL */
    $ping_client_opt = array(
    CURLOPT_URL => "https://apis.naver.com/crawl/nsyndi/v2", /* 네이버 신디케이션 서버 호출주소 */
    CURLOPT_POST => true, /* POST 방식 */
    CURLOPT_POSTFIELDS => "ping_url=" . $ping_url, /* 파라미터로 핑 URL 전달 */
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_CONNECTTIMEOUT => 10,
    CURLOPT_TIMEOUT => 10,
    CURLOPT_HTTPHEADER =>
         array("Host: apis.naver.com", "Pragma: no-cache", "Accept: */*", $ping_auth_header) /* 헤더에 인증키 정보 추가 */
    );
    $ping = curl_init();
    curl_setopt_array($ping, $ping_client_opt);
    $r = curl_exec($ping);
    //print_r($r);
    curl_close($ping);
}

# json_encode 한글 (로그)
function json_encode_kr($arr){
	//convmap since 0x80 char codes so it takes all multibyte codes (above ASCII 127). So such characters are being "hidden" from normal json_encoding
	array_walk_recursive($arr, function (&$item, $key) { if (is_string($item)) $item = mb_encode_numericentity($item, array (0x80, 0xffff, 0, 0xffff), 'UTF-8'); });
	return mb_decode_numericentity(json_encode($arr), array (0x80, 0xffff, 0, 0xffff), 'UTF-8');
}


# 배송중, 배송준비중 상태 변경 2016-11-28 유동혁
function deliveryStatusUp( $opts ){

    global $_ShopInfo;

    //실행자 이름을 가져온다 (2016.10.07 - 김재수 추가)
    /*
    if ($_ShopInfo->getMemname()) {
        $reg_name	= $_ShopInfo->getMemname();
    } else {
        list($reg_name)=pmysql_fetch_array(pmysql_query("select sender_name from tblorderinfo WHERE ordercode='".trim($ordercode)."' "));
    }
    $exe_id = $_ShopInfo->getMemid()."|".$reg_name."|user";	// 실행자 아이디|이름|타입
    */
    $exe_id = '||api';

    $deliOpts = array(
        'ordercode'     => '',      // 주문코드
        'op_idx'        => '',      // 상세 idx
        'step'          => '2',     // 주문 step
        'exe_id'        => $exe_id, // 입력자 
        'delivery_com'  => '',      // 배송회사 코드
        'delivery_num'  => '',      // 송장번호
        //'delivery_name' => '',      // 배송회사명
        'delivery_date' => '',      // 배송일
        'sync_type'     => 'M'      // 물류 또는 싱크커머스에서 넘겼는지 체크해주는 값 M 물류 S 싱크커머스
    );

    $deliOpts = array_merge( $deliOpts, $opts );

    $code    = 1;   // 반환코드
    $msg     = '';  // 비고
    //$textDir = $_SERVER[DOCUMENT_ROOT].'/data/backup/deliinfoup_logs_'.date("Ym").'/';  // 텍스트로그 위치
    $textDir =  DirPath.DataDir.'backup/deliinfoup_logs_'.date("Ym").'/';  // 텍스트로그 위치
    $outText = '';  //로그내용

    try{

        if( $deliOpts['ordercode'] == '' ){
            throw new Exception('주문코드가 없습니다.', 0 );
        }

        if( $deliOpts['op_idx'] == '' ){
            throw new Exception('주문상세 번호가 없습니다.', 0 );
        }

        # 배송준비중 상태변경
        if( $deliOpts['step'] == '2' ){

            //$syncStatus = 'S'; // 배송준비중
            orderProductStepUpdate($deliOpts['exe_id'], $deliOpts['ordercode'], $deliOpts['op_idx'], '2', '', '', '', '', '', '' , $deliOpts['sync_type'] );

            //현재 주문의 상태값을 가져온다.
            list($old_step1, $old_step2)=pmysql_fetch_array(pmysql_query("select oi_step1, oi_step2 from tblorderinfo WHERE ordercode='".trim($deliOpts['ordercode'])."'"));

            if ($old_step1 == '1' && $old_step2 == '0') {
                //주문을 배송 준비중으로 변경한다.
                $sql2 = "UPDATE tblorderinfo SET oi_step1 = '2', oi_step2 = '0', deli_gbn='S' WHERE ordercode='".$deliOpts['ordercode']."'";
                pmysql_query( $sql2, get_db_conn() );
                // 상점코드를 싱크에서 넘겨준다
                // 입찰기능이 생기면 store_code를 update 시키는 부분을 제거해야 한다 2016-10-13 유동혁
                //$sql3 = "UPDATE tblorderproduct SET store_code = '".$store_code."' WHERE idx = '".$op_idx."' ";
                //pmysql_query($sql3,get_db_conn());
            }

        # 배송중 상태변경
        } elseif( $deliOpts['step'] == '3' ) {

            if( $deliOpts['delivery_com'] == '' ){
                throw new Exception('배송회사 코드가 없습니다.', 0 );
            }

            if( $deliOpts['delivery_num'] == '' ){
                throw new Exception('송장번호가 없습니다.', 0 );
            }

            if( $deliOpts['delivery_date'] == '' ){
                throw new Exception('배송일이 없습니다.', 0 );
            }

            //$syncStatus = 'Y'; // 배송중

            # 배송중 상태변경
            $sql = "UPDATE tblorderproduct SET deli_com='".$deliOpts['delivery_com']."', deli_num='".$deliOpts['delivery_num']."', deli_gbn = 'Y', deli_date='".$deliOpts['delivery_date']."' ";
            $sql.= "WHERE ordercode='".$deliOpts['ordercode']."' AND idx='".$deliOpts['op_idx']."' ";
            $sql.= "AND op_step < 40 ";
            if( pmysql_query( $sql, get_db_conn() ) ) {

                # 상세 상태 변경 추가
                orderProductStepUpdate($deliOpts['exe_id'], $deliOpts['ordercode'], $deliOpts['op_idx'], '3', '', '', '', '', '', '' , $deliOpts['sync_type'] );

                # 주문 상태변경 추가
                $sql = "UPDATE tblorderinfo SET deli_gbn = 'Y', deli_date='".$deliOpts['delivery_date']."' ";
                $sql.= "WHERE ordercode='".$deliOpts['ordercode']."' ";
                pmysql_query($sql,get_db_conn());

                # 주문 상태 변경 추가
                orderStepUpdate( $deliOpts['exe_id'], $deliOpts['ordercode'], '3', '0' ); // 배송중

                // 상점코드를 싱크에서 넘겨준다
                // 입찰기능이 생기면 store_code를 update 시키는 부분을 제거해야 한다 2016-10-13 유동혁
                //$sql3 = "UPDATE tblorderproduct SET store_code = '".$store_code."' WHERE idx = '".$idx."' ";
                //pmysql_query($sql3,get_db_conn());

            }

        }


    } catch( Exception $e ) {
        $code = $e->getCode();
        $msg  = $e->getMessage();
    }

    # 로그작성
    $outText  = "=========================".date("Y-m-d H:i:s")."=============================".PHP_EOL;
    $outText .= "    function deliveryStatusUp >> ".PHP_EOL;
    $outText .= "    ordercode     : ".$deliOpts['ordercode'].PHP_EOL;
    $outText .= "    op_idx        : ".$deliOpts['op_idx'].PHP_EOL;
    $outText .= "    step          : ".$deliOpts['step'].PHP_EOL;
    $outText .= "    exe_id        : ".$deliOpts['exe_id'].PHP_EOL;
    $outText .= "    delivery_com  : ".$deliOpts['delivery_com'].PHP_EOL;
    $outText .= "    delivery_num  : ".$deliOpts['delivery_num'].PHP_EOL;
    $outText .= "    delivery_name : ".$deliOpts['delivery_name'].PHP_EOL;
    $outText .= "    sync_type     : ".$deliOpts['sync_type'].PHP_EOL;
    if( $code < 1 ){
        $outText .= "    ERR           : error".PHP_EOL;
    }
    $outText .= "    code          : ".$code.PHP_EOL;
    $outText .= "    MSG           : ".$msg.PHP_EOL;
    $outText .= PHP_EOL;

    if( !is_dir( $textDir ) ){
        mkdir( $textDir, 0700 );
        chmod( $textDir, 0777 );
    }
    $upQrt_f = fopen($textDir.'deliinfoup_'.date("Ymd").'.txt','a');
    fwrite( $upQrt_f, $outText );
    fclose( $upQrt_f );
    chmod( $textDir."deliinfoup_".date("Ymd").".txt",0777 );

    return $code;

}

/**
* function shopSslChange
* 로그인 페이지는 https로 처리한다
* 작성자 : 유동혁
* 날짜   : 2016-12-08
*/
function shopSslChange(){
    // ssl 적용 url
    $sslUrl = array( 
        '/front/member_switch.php',
        '/front/login.php',
        '/front/member_agree.php',
        '/front/member_certi.php',
        '/front/member_join.php',
        '/front/mypage_usermodify.php',
        '/front/order.php',

        '/m/member_switch.php',
        '/m/login.php',
        '/m/member_agree.php',
        '/m/member_certi.php',
        '/m/member_chkid.php',
        '/m/member_join.php',
        '/m/mypage_usermodify.php',
        '/m/order.php'
    );

/*
    if( array_search( $_SERVER['SCRIPT_NAME'], $sslUrl ) !== false ){
        if( empty( $_SERVER['HTTPS'] ) || $_SERVER['HTTPS'] == "off" ){
            $redirect_url = "https://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
			//exdebug($redirect_url);
			//exit;
            header("Location: $redirect_url");
            exit;
        }
    } else {
        if( $_SERVER['HTTPS'] == "on" ){
            $redirect_url = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
            header("Location: $redirect_url");
            exit;
        }
    }
*/



}

function IFLog($log="", $filename=""){

	$dir = DirPath.DataDir."/if_log/";

    $log_msg = "=================================================================================================="."\n";
    $log_msg .= "[".date('Y-m-d H:i:s')."] ".$log."\n";

	error_log($log_msg, 3, $tmp = $dir."IFLog_".$filename."_".date("Ymd").".log");
	@chmod( $tmp, 0777 );
}

/*
# ERP 포인트 타입설정
# body : 몰내 포인트 설명
# rel_flag : 몰내 포인트 코드값
# rel_job : 몰내 포인트 내용값
# point : AP 포인트값
*/
function erpPointTypeSet($body, $rel_flag, $rel_job, $point) {
	$rel_flag			= str_replace("@", "",$rel_flag);
	$rel_flag_arr	= explode("_", $rel_flag);
	$rel_job_arr	= explode("|", $rel_job);
	$order_arr		= explode("_", $rel_job_arr[1]);
	$body_arr		= explode(" ", $body);

	if ($rel_flag == 'board_del_point' && $body == '댓글 삭제 포인트 환원')
		$rel_flag = 'comment_del_point';

	$returnData		= array();
	$returnData	['point_type']		= "";
	$returnData	['point_name']	= "";
	$returnData	['order_no']		= "";
	$returnData	['order_idx']		= "";
	$returnData	['reason']			= "";

	if (
		$rel_flag == "join"
	) {																						// 회원가입
		$returnData	['point_type']		= "1";
		$returnData	['point_name']	= "회원가입";
	} else if (
		$rel_flag == "login_point"
	) {																						// 로그인
		$returnData	['point_type']		= "2";
		$returnData	['point_name']	= "로그인";
	} else if (
		$rel_flag == "board_in_point" || 
		$rel_flag == "board_del_point"
	) {																						// 게시물 작성(입력, 삭제)
		$returnData	['point_type']		= "3";
		$returnData	['point_name']	= "게시글 작성";
		if ($rel_flag == "board_in_point") 
			$returnData	['reason']			= "게시글 입력";
		else if ($rel_flag == "board_del_point") 
			$returnData	['reason']			= "게시글 삭제";
	} else if (
		$rel_flag == "comment_del_m_point" || 
		$rel_flag == "comment_del_point" || 
		$rel_flag == "comment_in_m_point" || 
		$rel_flag == "comment_in_point"
	) {																						// 댓글
		$returnData	['point_type']		= "4";
		$returnData	['point_name']	= "댓글 작성";
		if ($rel_flag_arr[1] == "in") 
			$returnData	['reason']			= "댓글 입력";
		else if ($rel_flag_arr[1] == "del") 
			$returnData	['reason']			= "댓글 삭제";
	} else if (
		$rel_flag == "like_minus_point" || 
		$rel_flag == "like_minus_point_forum_list" || 
		$rel_flag == "like_minus_point_instagram" || 
		$rel_flag == "like_minus_point_lookbook" || 
		$rel_flag == "like_minus_point_magazine" || 
		$rel_flag == "like_minus_point_product" || 
		$rel_flag == "like_minus_point_storestory" || 
		$rel_flag == "like_plus_point" || 
		$rel_flag == "like_plus_point_forum_list" || 
		$rel_flag == "like_plus_point_instagram" || 
		$rel_flag == "like_plus_point_lookbook" || 
		$rel_flag == "like_plus_point_magazine" || 
		$rel_flag == "like_plus_point_product" || 
		$rel_flag == "like_plus_point_storestory"
	) {																						// 좋아요
		$returnData	['point_type']		= "5";
		$returnData	['point_name']	= "좋아요 클릭";
		$returnData	['reason']			= $rel_flag_arr[3]?strtoupper($rel_flag_arr[3])." ":"";

		if ($rel_flag_arr[1] == "minus")
			$returnData	['reason']		.= "좋아요 클릭 취소";
		else if ($rel_flag_arr[1] == "plus")
			$returnData	['reason']		.= "좋아요 클릭";
	} else if (
		$rel_flag == "order" || 
		$rel_flag == "order_cancel" || 
		$rel_flag == "order_end" || 
		$rel_flag == "order_end_cancel"
	) {																						// 주문
			$returnData	['point_type']		= "6";
			$returnData	['point_name']	= "주문";
			$returnData	['order_no']		= $order_arr[0];
			$returnData	['order_idx']		= $order_arr[1];
			if ($rel_flag == "order") 
				$returnData	['reason']			= "사용";
			else if ($rel_flag == "order_cancel")
				$returnData	['reason']			= "취소";
			else if ($rel_flag == "order_end")
				$returnData	['reason']			= "적립";
			else if ($rel_flag == "order_end_cancel")
				$returnData	['reason']			= "적립 취소";
			else 
				$returnData	['reason']			= "사용";
	} else if (
		$rel_flag == "event" || 
		$rel_flag == "review" || 
		$rel_flag == "review_del"
	) {																						// 구매후기
		if ($body_arr[0] == "텍스트리뷰") {										// 일반(텍스트) 구매후기
			$returnData	['point_type']		= "7";
			$returnData	['point_name']	= "리뷰 작성";
			$returnData	['reason']			= "텍스트 리뷰 작성";
		} else if ($body_arr[0] == "포토리뷰") {								// 포토 구매후기
			$returnData	['point_type']		= "7";
			$returnData	['point_name']	= "리뷰 작성";
			$returnData	['reason']			= "포토 리뷰 작성";
		} else {// 20170824 오류 수정
			$returnData	['point_type']		= "7";
			$returnData	['point_name']	= "리뷰 작성 보상";
			$returnData	['reason']			= "3번째 이내";
		}

		if ($rel_flag != "review_del")
			$returnData	['reason']	.= "입력";
		else
			$returnData	['reason']	.= "삭제";
	} else if (
		$rel_flag == "bestreview_out_point" || 
		$rel_flag == "bestreview_in_point" 
	) {																						// 베스트 리뷰(선정, 취소)
		$returnData	['point_type']		= "8";
		$returnData	['point_name']	= "베스트 리뷰";

		if ($rel_flag == "bestreview_in_point") 
			$returnData	['reason']			= "베스트 리뷰 선정";
		else if ($rel_flag == "bestreview_out_point") 
			$returnData	['reason']			= "베스트 리뷰 취소";
	} else if (
		$rel_flag == "sns_in_band_point" || 
		$rel_flag == "sns_in_facebook_point" || 
		$rel_flag == "sns_in_kakaostory_point" || 
		$rel_flag == "sns_in_kakaotalk_point" || 
		$rel_flag == "sns_in_twitter_point"
	) {																						// SNS 공유
		$returnData	['point_type']		= "9";
		$returnData	['point_name']	= "SNS 공유";
		$returnData	['reason']			= $rel_flag_arr[2]?strtoupper($rel_flag_arr[2])." SNS 공유":"SNS 공유";
	} else if (
		$rel_flag == "expire"
	) {																						// 포인트 소멸	
		$returnData	['point_type']		= "99";
		$returnData	['point_name']	= "포인트 소멸";
	} else if (
		$rel_flag == "recomment_from"
	) {																						// 추천인
		$returnData	['point_type']		= "100";
		$returnData	['point_name']	= "추천아이디 등록";
		$returnData	['reason']			= $body;
	} else {
		if ($rel_flag_arr[0] == "feeling") {										// 호감/비호감
			if ($rel_flag_arr[1] == 'up') {											// 호감
				$returnData	['point_type']		= "10";
				$returnData	['point_name']	= "호감";
				$returnData	['reason']			= $rel_flag_arr[3]?strtoupper($rel_flag_arr[3])." ":"";
			} else if ($rel_flag_arr[1] == 'down') {							//비호감
				$returnData	['point_type']		= "10";
				$returnData	['point_name']	= "비호감";				
				$returnData	['reason']			= $rel_flag_arr[3]?strtoupper($rel_flag_arr[3])." ":"";
			}
			if ($point >= 0)
				$returnData	['reason']			.= "클릭";
			else
				$returnData	['reason']			.= "클릭 취소";
		} else {																// 기타 포인트
			$returnData	['point_type']		= "11";
			$returnData	['point_name']	= "기타 포인트 증정";
			$returnData	['reason']			= $body;
		}
	}
	return (object)$returnData;
}

/*
# ERP 포인트 타입설정 + 포인트 ERP 전송
# type : 포인트 타입
# mem_id : 회원 아이디
# body : 몰내 포인트 설명
# rel_flag : 몰내 포인트 코드값
# rel_job : 몰내 포인트 내용값
# point : 포인트값
# regdt : 등록일
*/
function erpTotalPointIns($type, $mem_id, $body, $rel_flag, $rel_job, $point, $regdt=null) {	

	$res	= erpPointTypeSet($body, $rel_flag, $rel_job, $point);

	$point_type		= $res->point_type;
	$point_name	= $res->point_name;
	$order_no		= $res->order_no;
	$order_idx		= $res->order_idx;
	$reason			= $res->reason;

	list($erp_mem_id) = pmysql_fetch_array(pmysql_query("SELECT erp_shopmem_id FROM tblmember WHERE id ='{$mem_id}' AND member_out='N' "));

	$log	= array(
		'body' => $body,
		'rel_flag' => $rel_flag,
		'regdt' => $regdt,
		'mem_id' => $mem_id,
		'point' => $point,
		'point_type' => $point_type,
		'point_name' => $point_name,
		'order_no' => $order_no,
		'order_idx' => $order_idx,
		'reason' => $reason,
		'erp_mem_id' => $erp_mem_id,
	);
	
	erpTotalPointLog($type, "erp_".$type."_request", $log, $mem_id);

	$status		= "";
	$message	= "";

	if ($erp_mem_id) {
		if ($type == 'actpoint') {
			$res	= sendErpPointAct($mem_id, $point, $point_type, $point_name, $regdt, $order_no, $order_idx, $reason);
		} else if ($type == 'cooperpoint') {
			$res	= sendErpPointCooper($mem_id, $point, $point_type, $point_name, $regdt, $order_no, $order_idx, $reason);
		} else if ($type == 'staffpoint') {
			$res	= sendErpPointStaff($mem_id, $erp_mem_id, $point, $point_type, $point_name, $regdt, $order_no, $order_idx, $reason);
		}
		$res_status		= $res->res_status;
		$res_message	= $res->res_message;
	} else {
		$res_status		= "N";
		$res_message	= "실패 (사유 : 회원이 존재하지 않습니다.)";
	}

	$returnData	= array(
		'res_status' => $res_status,
		'res_message' => $res_message
	);
	erpTotalPointLog($type, "erp_".$type."_response", $returnData, $mem_id);
	return  (object)$returnData;
}

/*
# ERP 활동포인트 로그
# type : 포인트 타입
# log_type : 로그 타입
# log : 로그 내역
# mem_id : 회원 아이디
*/
function erpTotalPointLog($type, $log_type, $log, $mem_id=null) {

	// 로그를 남긴다.-S--------------------------------------------------------------------------------------//
	$textDir = $_SERVER[DOCUMENT_ROOT].'/data/backup/erp_'.$type.'_logs_'.date("Ym").'/';
	$outText = "";
	if ($log_type == "erp_".$type."_request")  $outText .= "\n=========================".date("Y-m-d H:i:s")."=============================\n";
	$outText.= "[".$log_type."] (".date("Y-m-d H:i:s").") - ";
	$outText.= "mem_id : ".($mem_id)." / ";
	$outText.= "log : ".json_encode_kr($log)."\n";

	if(!is_dir($textDir)){
		mkdir($textDir, 0700);
		chmod($textDir, 0777);
	}
	$upQrt_f = fopen($textDir.'erp_'.$type.'_logs_'.date("Ymd").'.txt','a');
	fwrite($upQrt_f, $outText );
	fclose($upQrt_f);
	chmod($textDir."erp_".$type."_logs_".date("Ymd").".txt",0777);
	// 로그를 남긴다.-E--------------------------------------------------------------------------------------//

}

/*
 # 반품 환불로그(가상계좌)
 # ordercode : 주문코드
 # resultcode : 결과코드
 # resultmsg : 결과메시지
 # tid : 거래아이디
 */
function imaginationAccountRefoundLog($ordercode, $resultcode,$resultmsg,$tid ) {

	// 로그를 남긴다.-S--------------------------------------------------------------------------------------//
	$textDir = $_SERVER[DOCUMENT_ROOT].'/data/backup/imagination_account_refound_logs_'.date("Ym").'/';
	$outText = "";
	$outText .= "\n=========================".date("Y-m-d H:i:s")."=============================\n";
	$outText.= "[ORDERCODE : ".$ordercode."]\n";
	$outText.= "[TID : ".$tid."]\n";
	$outText.= "[RESULTCODE : ".$resultcode."]\n";
	$outText.= "[RESULTMSG : ".$resultmsg."]";
	$outText .= "\n================================END==================================\n";

	if(!is_dir($textDir)){
		mkdir($textDir, 0700);
		chmod($textDir, 0777);
	}
	$upQrt_f = fopen($textDir.'imagination_account_refound_logs_'.date("Ymd").'.txt','a');
	fwrite($upQrt_f, $outText );
	fclose($upQrt_f);
	chmod($textDir."imagination_account_refound_logs_".date("Ymd").".txt",0777);
	// 로그를 남긴다.-E--------------------------------------------------------------------------------------//
}

/*
 # 상품 쿠폰정보 조회 
 # productcode : 상품코드
 */
function getProductCouponInfo($productcode) {
	#쿠폰 설정
	$_CouponInfo = new CouponInfo( '6' );
	// 받을 수 있는 쿠폰 목록 찾기 ( 셋팅에서 다운로드로 맞추어 놓음 )
	$_CouponInfo->search_coupon();
	// 해당 상품에서 사용 가능한지 확인 
	$_CouponInfo->check_coupon_product( $productcode, 0 );
	//exdebug( $_CouponInfo->infoData );
	$possibleCoupon      =  $_CouponInfo->infoData;
	
	return $possibleCoupon;
}

/*
 # 상품정보 조회 
 # productcode : 상품코드
 */
function getProductInfo($productcode){
	global $_ShopInfo, $Dir;
	
	$_pdata = "";
	$sql = "SELECT pr.*, COALESCE((select COUNT( tl.hott_code )AS hott_cnt from tblhott_like tl WHERE tl.section = 'product' AND pr.productcode = tl.hott_code),0) AS hott_cnt ";
	$sql.= "FROM tblproduct pr ";
	$sql.= "WHERE pr.productcode='{$productcode}' ";
	$result=pmysql_query($sql,get_db_conn());
	if($row=pmysql_fetch_object($result)) {
		$_pdata=$row;
		$_pdata->brand += 0;
		$sql = "SELECT * FROM tblproductbrand ";
		$sql.= "WHERE bridx='{$_pdata->brand}' ";
		$bresult=pmysql_query($sql,get_db_conn());
		$brow=pmysql_fetch_object($bresult);
		$_pdata->brandcode = $_pdata->brand;
		$_pdata->brand = $brow->brandname;
		$_pdata->staff_rate = $brow->staff_rate;
		$_pdata_prcontent = stripslashes($_pdata->pr_content);
		if( strlen($detail_filter) > 0 ) {
			$_pdata_prcontent = preg_replace($filterpattern,$filterreplace,$_pdata_prcontent);
			$_pdata->prcontent = $_pdata_prcontent;
		}
		pmysql_free_result($result);
	}
	
	// 
	$_pdata->group_productcode = getGroupProductcode($_pdata->productcode);
	
	// 좋아요
	$like_sql = "SELECT p.productcode, li.section,
						COALESCE((select COUNT( tl.hott_code )AS hott_cnt from tblhott_like tl WHERE tl.section = 'product' AND p.productcode = tl.hott_code),0) AS hott_cnt
			FROM tblproduct p
			LEFT JOIN ( SELECT hott_code, section FROM tblhott_like WHERE section = 'product' AND like_id = '".$_ShopInfo->getMemid()."' GROUP BY hott_code, section ) li on p.productcode = li.hott_code";
	$like_sql .= " WHERE p.productcode = '".$productcode."' AND p.display = 'Y'";
	$result = pmysql_query( $like_sql, get_db_conn() );
	$like_row = pmysql_fetch_object( $result );
	$_pdata->like_info['section'] = $like_row->section;
	
	// 멀티이미지
	$sql = "SELECT * FROM tblmultiimages ";
	$sql.= "WHERE productcode = '{$productcode}' ";
	$result=pmysql_query($sql,get_db_conn());
	if($row=pmysql_fetch_object($result)){
		$mulimg_name = array ("01"=>&$row->primg01,"02"=>&$row->primg02,"03"=>&$row->primg03,"04"=>&$row->primg04,"05"=>&$row->primg05,"06"=>&$row->primg06,"07"=>&$row->primg07,"08"=>&$row->primg08,"09"=>&$row->primg09,"10"=>&$row->primg10);
	}
	$i = 0;
	foreach($mulimg_name as $img){
		if($img){
			$_pdata->etc_image[$i] = $img;
			$i++;
		}
	}
	
	// 색상
	if($_pdata->prodcode){
		$prod_color_sql = "
			select ta.productcode, ta.productname, ta.prodcode, ta.color_code, tb.cno, tb.color_name, tb.color_code as color_rgb, tb.color_img
			from tblproduct ta left outer join tblproduct_color tb
			on ta.color_code = tb.color_name
			where ta.prodcode = '".$_pdata->prodcode."'
			and tb.cno is not null
			order by cno desc";
		$prod_color_result = pmysql_query($prod_color_sql,get_db_conn());
		$j = 0;
		while($prod_color_row = pmysql_fetch_array($prod_color_result)){
			$_pdata->color[$j] = $prod_color_row;
			$j++;
		}	
	}
	
	// 쿠폰
	/*$coupon_sql = "
		select tb.*
		from tblcouponproduct ta 
			left outer join tblcouponinfo tb on ta.coupon_code = tb.coupon_code
			left outer join tblproduct tc on ta.productcode = tc.productcode
		where 1=1
			and ta.productcode = '".$productcode."'
			and tc.pridx is not null";
	$coupon_result = pmysql_query($coupon_sql,get_db_conn());
	$k = 0;
	while($coupon_row = pmysql_fetch_array($coupon_result)){
		$_pdata->coupon[$k] = $coupon_row;
		$k++;
	}*/

	$_pdata->coupon	= getProductCouponInfo($productcode);
	//exdebug($_pdata->coupon);
	
	
	// 배송료 
	$sql = "SELECT deli_type,deli_basefee,deli_basefeetype,deli_miniprice,deli_setperiod,deli_limit, deli_select, ";
	$sql.= "order_msg,deli_area,deli_area_limit FROM tblshopinfo ";
	$result=pmysql_query($sql,get_db_conn());
	if ($data=pmysql_fetch_object($result)) {
		$_pdata->deli_miniprice = $data->deli_miniprice;
	}
	pmysql_free_result($result);
	
	// 할인가 퍼센트 계산
	$_pdata->price_percent = round( ( ( $_pdata->consumerprice - $_pdata->sellprice ) / $_pdata->consumerprice ) * 100 );
	
	// 적립금/적립율
	$reservetype_txt = "";
	$point_value = 0;
	$reservetype = $_pdata->reservetype;
	// 단위
	if($reservetype == "Y")			{
		$reservetype_txt = "%";
		$point_value = round($_pdata->sellprice*$_pdata->reserve/100);
	}else if($reservetype == "N")	{
		$reservetype_txt = "원";
		$point_value = $_pdata->reserve;
	}
	$reserve_info = array();
	$reserve_info['reserv_txt'] 	= $reservetype_txt;
	$reserve_info['reserv_value'] 	= $_pdata->reserve;
	$reserve_info['point_value'] 	= $point_value;
	$_pdata->reserve_info = $reserve_info;
	
	return $_pdata;
}




// prodcode를 기준으로 처음에 등록된 productcode 위민트 170205
function getGroupProductcode($productcode){
	$group_productcode = "";
	$sql = "
		select tb.productcode
		from (select * from tblproduct where productcode = '".$productcode."') ta 
		left outer join tblproduct tb
		on ta.prodcode = tb.prodcode
		order by tb.regdate asc limit 1";
	$result = pmysql_query($sql,get_db_conn());
	if($row=pmysql_fetch_object($result)) {
		$group_productcode = $row->productcode;
	}
	return $group_productcode;
}

function getProdcode($productcode){
	$prodcode = "";
	$sql = "select prodcode from tblproduct where productcode = '".$productcode."'";
	$result = pmysql_query($sql,get_db_conn());
	if($row=pmysql_fetch_object($result)) {
		$prodcode = $row->prodcode;
	}
	return $prodcode;
}

function Category_list($code_a, $code_b="000", $code_c="000", $code_d="000") {
	$code_loc = "";
	$not_cate="";
	$cate_array="";

	//카테고리 제외
	$not_cate[]="002";
	
	if (!$code_a) $cate_array="code_b='".$code_b."'";
	elseif ($code_a && $code_b=="000") $cate_array="code_a='".$code_a."' and code_b!='".$code_b."' and code_c='".$code_c."'";
	elseif ($code_a && $code_b!="000" && $code_c=="000") $cate_array="code_a='".$code_a."' and code_b='".$code_b."' and code_c!='".$code_c."' and code_d='".$code_d."'";
	elseif ($code_a && $code_b!="000" && $code_c!="000" && $code_d=="000") $cate_array="code_a='".$code_a."' and code_b='".$code_b."' and code_c='".$code_c."' and code_d!='".$code_d."'";

	$sql = "SELECT * FROM tblproductcode where ".$cate_array." and is_hidden='N' and code_a not in ('".implode("', '",$not_cate)."')";
	$sql.= " ORDER BY cate_sort ASC";

	//echo $sql;
	$result=pmysql_query($sql,get_db_conn());
	$_=array();
	while($row=pmysql_fetch_object($result)) {
		$_[] = $row;
	}
	$code_loc = $_;
	pmysql_free_result($result);
	return $code_loc;
}

//브랜드 세션추가
/*//=====================
$bridx="브랜드 번호";
$deltype="세션삭제여부 1:삭제";
//=====================*/
function brand_session($bridx, $deltype="0"){
	if($bridx){
		unset($_SESSION[brand_session_no]);
		$_SESSION[brand_session_no]=$bridx;
	}
	if($deltype)unset($_SESSION[brand_session_no]);

}

function timesale_price($productcode){
	if($productcode){
		$sql="select *, to_char(sdate,'YYYYMMDDHH24MISS') as sdt, to_char(edate,'YYYYMMDDHH24MISS') as edt, to_char(modifydate,'YYYYMMDD') as mdt from tblproduct p left join tblproduct_timesale pt on(pt.timesale_sno=p.timesale_code) where p.productcode='".$productcode."'";
		$result=pmysql_query($sql);
		$data=pmysql_fetch_object($result);
		
		$sellprice	= $data->sellprice;
		//신상품할인
		if($data->timesale_type=="1"){
			$time_day=date("Ymd", strtotime('-'.$data->newday.' day'));
			//상품등록일 후 몇일 체크
			if($data->mdt >= $time_day){
				//원단위 할인
				if($data->rate_type=="1"){
					$sellprice	= $data->sellprice-$data->price_rate;
				//퍼센트 할인
				}else if($data->rate_type=="2"){
					$sellprice	= round( ( (100 - $data->price_rate) / 100 ) * $data->sellprice );
				}
			}
		//기간할인
		}else if($data->timesale_type=="2"){
			//날짜조건
			if($data->sdt<=date("YmdHis") && $data->edt>=date("YmdHis")){
				$stime=substr($data->order_time,"0","4");
				$etime=substr($data->order_time,"4","4");
				//시간조건
				if($stime<=date("Hi") && $etime>=date("Hi")){
					//참고 array("일", "월", "화", "수", "목", "금", "토");
					//요일 계산 
					$yo_num=array("0"=>"6","1"=>"0","2"=>"1","3"=>"2","4"=>"3","5"=>"4","6"=>"5");
					$yo_cut=substr($data->week, $yo_num[date("w")], "1");
					//요일이 체크되있을경우
					if($yo_cut){
						//원단위 할인
						if($data->rate_type=="1"){
							$sellprice	= $data->sellprice-$data->price_rate;
						//퍼센트 할인
						}else if($data->rate_type=="2"){
							$sellprice	= round( ( (100 - $data->price_rate) / 100 ) * $data->sellprice );
						}
					}
				}
			}
		}		
		return $sellprice;
	}
}
function getSearchListData($sql,$unique=true){
	$result = pmysql_query( $sql, get_db_conn() );
	$arrGetSearchHashListData = $arrGetSearchBrandListData = $arrGetSearchListData = array();
	while( $row = pmysql_fetch_object($result) ) {
		$tempArr = explode(",", $row->keyword);
		foreach($tempArr as $v){
			array_push($arrGetSearchHashListData, $v);
		}
		$arrGetSearchBrandListData[$row->vender] = $row->brandname;
	}
	if($unique) {
		$arrGetSearchHashListData = array_unique(array_filter($arrGetSearchHashListData));
		$arrGetSearchBrandListData = array_unique(array_filter($arrGetSearchBrandListData));

		# 섞고
		shuffle($arrGetSearchHashListData);
		# 10개만 잘라서 출력
		$arrGetSearchHashListData = array_slice($arrGetSearchHashListData, 0, 10);
	}
	$arrGetSearchListData['HASH'] = $arrGetSearchHashListData;
	$arrGetSearchListData['VENDER'] = $arrGetSearchBrandListData;

	return $arrGetSearchListData;
}
if(strpos($_SERVER["REQUEST_URI"],'/m/') === false)
{
include_once($Dir."lib/cache_main.php");
}

// 2017-08-08  초기 ERP P 값이 없을 경우 체크
function getErpcheckinfo($ordercode,$idx) {

   $conn = oci_connect("swonline", "commercelab", "125.128.119.220/SWERP", "US7ASCII"); 

    $sql = "SELECT ORDER_STEP
			FROM  TA_OM010
			WHERE ORDER_NO = '".$ordercode."'        
			AND ORDER_DETAIL_NO = '".$idx."'
            ";
    $smt_step = oci_parse($conn, $sql);
    oci_execute($smt_step);
    //echo $sql."\r\n";

	while (($row = oci_fetch_row($smt_step)) != false) {
		$erp_step =  $row[0];
	}

	oci_free_statement($smt_step);
	oci_close($conn);

    return $erp_step;

}

?>
