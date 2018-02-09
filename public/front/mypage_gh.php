<?php
$Dir="../";									// 사용할 변수 선언
include_once($Dir."lib/init.php");			// 정의한 변수를 사용하는 파일 - 중복없이 한번만 포함 ../lib/init.php를 포함한다.
include_once($Dir."lib/lib.php");			// 정의한 함수를 사용하는 파일 - 중복없이 한번만 포함 ../lib/lib.php를 포함한다.
include_once($Dir."lib/shopdata.php");		// 중복없이 한번만 ../lib/shopdata.php를 포함한다.

//exdebug($_ShopInfo);

if(strlen($_ShopInfo->getMemid())==0) {		//	$_ShopInfo ------ 멤버의 id의 길이가 0 이면 true
	Header("Location:".$Dir.FrontDir."login.php?chUrl=".getUrl());		
						// ../front/login.php? 에 GET방식으로 chUrl 이름의 getUrl 데이터를 전송
							// getUrl() --- 사용한 $_SERVER['Query_string']이 있으면  ?query 반환 , 아니면 최근 스크립트의 경로를 반환한다.
								//	$_SERVER['Query_string']란 URL에 전달한 변수와 값을 가져오는 예약 변수
	exit;																// 종료 - 더이상 하단의 정보는 적용안됨
}

foreach($_ShopInfo as $kk => $vv){
	echo nl2br("$kk --- $vv  \n ");
	
}

foreach($_data as $kkk => $vvv){
	echo nl2br("$kkk --- $vvv  \n ");
	
}

//print_r($_ShopInfo);
//print_r($_data);

$sql = "SELECT * FROM tblmember WHERE id='".$_ShopInfo->getMemid()."' ";// tblMember 테이블에서 멤버 ID 비교 - 모든 컬럼 반환
$result=pmysql_query($sql,get_db_conn());								// query 실행 ?
if($row=pmysql_fetch_object($result)) {									// query 실행 후 반환되는 row 여부 확인
	$_mdata=$row;														// 반환한 row를 $_mdata에 대입
	if($row->member_out=="Y") {							// 멤버가 out이라면
		$_ShopInfo->SetMemNULL();						//	setMemNull() 함수를 실행
		$_ShopInfo->Save();								//	Save() 함수를 실행
		alert_go('회원 아이디가 존재하지 않습니다.',$Dir.FrontDir."login.php");		// ../front/login.php로 - 상위 경로로
	}

	if($row->authidkey!=$_ShopInfo->getAuthidkey()) {	// row가 갖는 인증키와 쇼핑몰이 갖는 인증키 비교
		$_ShopInfo->SetMemNULL();						//	setMemNull() 함수를 실행
		$_ShopInfo->Save();								//	Save() 함수를 실행
		alert_go('처음부터 다시 시작하시기 바랍니다.',$Dir.FrontDir."login.php");	// ../front/login.php로 - 상위 경로로
	}
}
$staff_type = $row->staff_type;							// pmysql_fetch_object 로 실행한 row의 type 가져오기 - 사용하는곳 없음 ????
pmysql_free_result($result);							//

$selfcodefont_start = "<font class=\"prselfcode\">"; //진열코드 폰트 시작
$selfcodefont_end = "</font>"; //진열코드 폰트 끝

$cdate = date("YmdH");			// yyyy-mm-dd-hh 타입으로 현재 시간 얻기
if($_data->coupon_ok=="Y") {	//	2   세션 정보	- 쿠폰이 존재 여부 확인
	$sql = "SELECT COUNT(*) as cnt FROM tblcouponissue WHERE id='".$_ShopInfo->getMemid()."' AND used='N' AND (date_end>='{$cdate}' OR date_end='') ";		// 쿠폰 테이블에서 현재까지 사용안한 사용가능한 쿠폰의 수 찾는 query
	$result = pmysql_query($sql,get_db_conn());		// query 실행 ?
	$row = pmysql_fetch_object($result);			// query 수행하여 객체 타입으로 값 받음
	$coupon_cnt = $row->cnt;						// 쿠폰의 수를 변수에 할당
	pmysql_free_result($result);					// 
} else {
	$coupon_cnt=0;			// coupon_ok != y 라면 쿠폰은 0 개로 초기화
}

$s_year=(int)$_POST["s_year"];			// 어디선가 보낸 s 년 정보 
$s_month=(int)$_POST["s_month"];		// 어디선가 보낸 s 월 정보 
$s_day=(int)$_POST["s_day"];			// 어디선가 보낸 s 일 정보 

$e_year=(int)$_POST["e_year"];			// 어디선가 보낸 e 년 정보 
$e_month=(int)$_POST["e_month"];		// 어디선가 보낸 e 월 정보 
$e_day=(int)$_POST["e_day"];			// 어디선가 보낸 e 일 정보 

if($e_year==0) $e_year=(int)date("Y");			// e의 y가 0이면 현재 시간의 y 대입
if($e_month==0) $e_month=(int)date("m");		// e의 m가 0이면 현재 시간의 m 대입
if($e_day==0) $e_day=(int)date("d");			// e의 d가 0이면 현재 시간의 d 대입

$stime=strtotime("$e_year-$e_month-$e_day -1 month");	// e의 -1 month의 date 변수를 대입
if($s_year==0) $s_year=(int)date("Y",$stime);		// s의 y 가 0이면  y-(m-1)-d 의 y 을 대입
if($s_month==0) $s_month=(int)date("m",$stime);		// s의 m 가 0이면  y-(m-1)-d 의 m-1 을 대입
if($s_day==0) $s_day=(int)date("d",$stime);			// s의 d 가 0이면  y-(m-1)-d 의 d 을 대입

$ordgbn=$_POST["ordgbn"];
if(!strstr("ASCR",$ordgbn)) {	//  ASCR 가 없는지 확인
	$ordgbn="A";				// 없다면 A 
}


##### 최근 주문 정보
$sql = "SELECT receive_ok,ordercode,cast(substr(ordercode,0,9) as date) as ord_date, substr(ordercode,9,6) as ord_time, price, paymethod, pay_admin_proc, pay_flag, bank_date, deli_gbn, receipt_yn, 1 as ordertype ";	// 수신완료, 주문코드 , ordercode 0~9자리를 Date타입으로 변환하며 이름은 ord_date로 정한다 , ordercode 의 9~14 번째 데이터 정보를 가져오고 ord_time이란 명칭으로 정한다 , 가격, 결제방식, 결제 관리자 , 결제_flag , 은행일 ,  , 영수증_yn , 1 을 ordertype 명을  보여준다. 
$sql.= "FROM sales.tblorderinfo WHERE id='".$_ShopInfo->getMemid()."' ";	// tblorderinfo테이블에서 찾는 ID가 멤버 ID일 경우
#### 기간 지정
//$sql.= "AND ordercode >= '".$s_curdate."' AND ordercode <= '".$e_curdate."' ";
#### //기간 지정
if($ordgbn=="S") $sql.= "AND deli_gbn IN ('S','Y','N','X') ";
else if($ordgbn=="C") $sql.= "AND deli_gbn IN ('C','D') ";
else if($ordgbn=="R") $sql.= "AND deli_gbn IN ('R','E') ";
$sql.= "AND (del_gbn='N' OR del_gbn='A') ";
$sql.= " union SELECT receive_ok,ordercode,cast(substr(ordercode,0,9) as date) as ord_date, substr(ordercode,9,6) as ord_time, price, paymethod, pay_admin_proc, pay_flag, bank_date, deli_gbn, receipt_yn, 2 as ordertype ";// 수신완료, 주문코드 , 주문번호0~9번째 데이터를 Date타입으로 변환하여 ord_date란 명칭으로 사용 , ordercode의 9~14번째 데이터를 ord_time이란 명칭으로 사용, 가격, 결제방식, 결제_관리자_처리, 결제_flag , 은행일,  , 영수증_yn , 2 을 ordertype이란 명칭으로 사용하며 --- 전 table과 합친다 (union)
$sql.= "FROM tblorderinfo WHERE id='".$_ShopInfo->getMemid()."' ";			// tblordertainfo 테이블에서 찾는 ID가 멤버ID일 경우
#### 기간지정
//$sql.= "AND ordercode >= '".$s_curdate."' AND ordercode <= '".$e_curdate."' ";
#### //기간지정
if($ordgbn=="S") $sql.= "AND deli_gbn IN ('S','Y','N','X') ";
else if($ordgbn=="C") $sql.= "AND deli_gbn IN ('C','D') ";
else if($ordgbn=="R") $sql.= "AND deli_gbn IN ('R','E') ";
$sql.= "AND (del_gbn='N' OR del_gbn='A') ";

$sql.= "ORDER BY ordercode DESC";	// 주문코드 내림차순

$paging = new Tem001_saveheels_Paging($sql,10,4,'GoPage',true);		// 어디선가 선언된 class or object ???????????????????????????
$t_count = $paging->t_count;	// 테이블 관련 count ?
$gotopage = $paging->gotopage;	// page 관련 번호 ?


$sql = $paging->getSql($sql);	// sql문 관련 query 반환 함수 ?
$result=pmysql_query($sql,get_db_conn());		// query 실행 ?
$cnt=0;											// 선언 및 0으로 초기화
while($row=pmysql_fetch_object($result)) {		// query 실행 결과물인 객체가 있으면
	$number = ($t_count-($setup[list_num] * ($gotopage-1))-$cnt);		// 3 세션 정보 setup - gotopage와 count값 cnt값으로 number 생성 ?

	$ord_time=substr($row->ord_time,0,2).":".substr($row->ord_time,2,2).":".substr($row->ord_time,4,2);	// ord_time의 0~1번째 데이터 : ord_time의 2~3번째 데이터 : ord_time의 4~5번째 데이터를 합쳐서 -- date(y-m-d) 형태의 String 타입으로 저장
	
	#### 결제 수단
	if (strstr("B", $row->paymethod[0])) $paymethod = "무통장입금";
	else if (strstr("V", $row->paymethod[0])) $paymethod = "실시간계좌이체";
	else if (strstr("O", $row->paymethod[0])) $paymethod = "가상계좌";
	else if (strstr("Q", $row->paymethod[0])) $paymethod = "가상계좌-<FONT COLOR=\"#FF0000\">매매보호</FONT>";
	else if (strstr("C", $row->paymethod[0])) $paymethod = "신용카드";
	else if (strstr("P", $row->paymethod[0])) $paymethod = "신용카드-<FONT COLOR=\"#FF0000\">매매보호</FONT>";
	else if (strstr("M", $row->paymethod[0])) $paymethod = "휴대폰";
	
	$row->paymethod_str = $paymethod;		// row의 변수에 data 값 (결제 방식)으로 반환
	#### //결제 수단
	
	
	#### 진행상태
	if ($row->deli_gbn=="C") $pay_proc = "주문취소";
	else if ($row->deli_gbn=="D") $pay_proc = "취소요청";
	else if ($row->deli_gbn=="E") $pay_proc = "환불대기";
	else if ($row->deli_gbn=="X") $pay_proc = "발송준비";
	else if ($row->deli_gbn=="Y") $pay_proc = "발송완료";
	else if ($row->deli_gbn=="N") {		// else if -- start
		if (strlen($row->bank_date)<12 && strstr("BOQ", $row->paymethod[0])) $pay_proc = "입금확인중";	//bank_date 길이가 12 미만, paymetho[0]에 BOQ란 데이터가 있을때
			else if ($row->pay_admin_proc=="C" && $row->pay_flag=="0000") $pay_proc = "결제취소";	
			else if (strlen($row->bank_date)>=12 || $row->pay_flag=="0000") $pay_proc = "발송준비";	// bank_date 길이가 12이상 이거나 pat_flag가 0000이면
			else $pay_proc = "결제확인중";
		} else if ($row->deli_gbn=="S") {
			$pay_proc = "발송준비";
		} else if ($row->deli_gbn=="R") {
			$pay_proc = "반송처리";
		} else if ($row->deli_gbn=="H") {
			$pay_proc = "발송완료 [정산보류]";
		}
	$row->pay_proc = $pay_proc;			// row의 변수게 data 값(상품 결제 상황)으로 반환

	#### //진행상태
	
	$ord_data[] = $row;					// 배열에 변경된 row정보들 대입
	
}										// else if -- end

##### //최근 주문 정보

##### 최근 본 상품

$_prdt_list=trim($_COOKIE['ViewProduct'],',');	// cookie에서 갖고온 데이터의 양쪽의 ',' 제거
$prdt_list=explode(",",$_prdt_list);			// ,을 기준으로 나눠  array로 반환 
$prdt_no=count($prdt_list);						// array의 count 반환
if(ord($prdt_no)==0||!$_prdt_list) {			// $prdt_no가 0 이거나  prdt_list 값이 없으면  --  $prdt_no = 0 
	$prdt_no=0;
}

$tmp_product="";								// 변수 선언 및 초기화
for($i=0;$i<$prdt_no;$i++){						// prdt_list의 수 만큼 반복
	$tmp_product.="'{$prdt_list[$i]}',";		// 배열 형태의 String 타입으로 저장  --	{},{},{},
}	

$productall = array();							// 배열 변수 생성
$tmp_product=rtrim($tmp_product,',');			// 오른쪽 끝의 , 제거	--	{},{},{}
$sql_recent = "SELECT productcode,productname,tinyimage,quantity,consumerprice,sellprice FROM tblproduct "; // tblproduct란 테이블에서 상품코드, 상품명, 작은이미지이름, 수량, 소비자가격, 판매가격를 출력한다.
$sql_recent.= "WHERE productcode IN ({$tmp_product}) ";			// in({},{},{})  $tmp_product 중 1개라도 상품코드에 속하면 선택
$sql_recent.= "ORDER BY FIELD(productcode,{$tmp_product}) ";	// 정렬 순서를 productcode 먼저 내림차순 하고(배열의 값 $tmp_product 대로) 내림차순을 하는 query문
$res_recent=pmysql_query($sql_recent,get_db_conn());			// query 실행 ?


##### //최근 본 상품

##### 관심상품

$qry_wish = "WHERE a.id='".$_ShopInfo->getMemid()."' ";				// a.id가 멤버 ID 라면
$qry_wish.= "AND a.productcode=b.productcode AND b.display='Y' ";	// a,b의 상품코드가 같고 b의display가 Y라면
$qry_wish.= "AND (b.group_check='N' OR c.group_code='".$_ShopInfo->getMemgroup()."') ";		// group_check가 N이거나 group_code중 멤버의 그룹번호일 경우

$sql_wish_cnt = "SELECT * ";	// 전체 찾음
$sql_wish_cnt.= "FROM tblwishlist a, tblproduct b ";				// 상품 테이블 , 위시 리스트 테이블에서 찾는다
$sql_wish_cnt.= "LEFT OUTER JOIN tblproductgroupcode c ON b.productcode=c.productcode ";	// left join - trlproduct 테이블과 tblproductgroupcode 테이블 간 productcode가 같다면 tblproduct의 값이 null일경우 null이라 포함하여 출력한다.
$sql_wish_cnt.= $qry_wish;	// where 조건절 추가
$res_wish_cnt = pmysql_query($sql_wish_cnt);	// query 실행 ?
$t_count_wish = pmysql_num_rows($res_wish_cnt);	// query를 실행한 결과의 row 수를 반환
//exdebug($sql_wish_cnt);
if ($t_count_wish) {							// 존재하는 row가 존재
	$sql_wish = "SELECT a.opt1_idx,a.opt2_idx,a.optidxs,b.productcode,b.productname,b.sellprice,b.sellprice as realprice, ";	// a의 opt1_idx , opt2_idx , optidxs , b의 productcode , 상품명 , 판매가격 , 판매가격을 realprice로 지정한다.
	$sql_wish.= "b.reserve,b.reservetype,b.addcode,b.minimage,b.maximage,b.tinyimage,b.option_price,b.option_quantity,b.option1,b.option2, ";	// b의 예약 , 예약 유형, addcode , minimage , 큰이미지 , 작은이미지 , 옵션_가격 , 옵션_수량 , option1, option2
	$sql_wish.= "b.etctype,a.wish_idx as wishidx,a.marks,a.memo,b.selfcode,b.consumerprice, b.pridx FROM tblwishlist a, tblproduct b ";	// b의 etctype , wish_idx 를 wishidx로 지정 , adml marks , memo, b의 selfcode , 구매자가격 , pridx 를 출력한다.  -- tblwishlist와 tblproduct 테이블에서
	$sql_wish.= "LEFT OUTER JOIN tblproductgroupcode c ON b.productcode=c.productcode ";	// tblproduct 테이블 b와 tblproductgroupcode 테이블 crks productcode가 같을 경우 tblproduct가 null인 경우에도 포함하여 출력한다.
	$sql_wish.= $qry_wish." ";				// where 조건절 추가
	$sql_wish.= "ORDER BY a.date DESC ";	// date 내림차순
	$sql_wish.= "LIMIT 2 ";					// 2개만 선택
    $res_wish=pmysql_query($sql_wish);		// query 실행 ?
} else {
    $res_wish=NULL;							// 해당되는 row가 없으면 NULL
}

##### //관심상품
/*
$toMonth = date("Y-m",strtotime ("-3 months"))."-01";
$fromMonth = date("Y-m",strtotime ("-1 months"))."-".date("t",date("m",strtotime ("-1 months")));
*/
$toMonth = date("Y-m",strtotime ("-2 months"))."-01";	// (현재시간 -2달)의 Date를  yyyy-mm 타입으로 변환)."-01"
$fromMonth = date("Y-m-d");								// 현재시간 yyyy-mm-dd 타입으로 

$s_curtime=strtotime($toMonth);							// yyyy-mm-01 을 time 형식으로 변환
$s_curdate=date("Ymd",$s_curtime);						// yyyy-mm-dd 형태로 변환
$e_curtime=strtotime($fromMonth);						// time 형식으로 변환
$e_curdate=date("Ymd",$e_curtime)."999999999999";		// time을 yyyy-mm-dd date 형식으로 변환하고 뒤에 9999999999를 붙임

$sql = "SELECT 
				SUM(price)		
			FROM 
				tblorderinfo 
			WHERE 
				id='".$_ShopInfo->getMemid()."'		
				AND ordercode >= '".$s_curdate."' AND ordercode <= '".$e_curdate."'	
				AND deli_gbn = 'Y' 
				AND receive_ok = '1'";	// ordercode가 2달전 ~ 현재 까지의 주문정보에 대한 총합을 구한다.
list($sumPrice) = pmysql_fetch($sql);	// query를 수행하면 array 형태로 반환
if(!$sumPrice) $sumPrice = 0;			// sumPrice가 null이나 0 이라면 0으로 초기화
$remainPrice = 0;						// 변수 선언 및 초기화
$nextLevel = 0;							// 변수 선언 및 초기화
foreach($arrGradeLevelUp as $gKey => $gVal){	// 4 세션 정보 - $arrGradeLevelUp란 배열의  (key,value)형식을 $gkey 와 $gVal란 명칭으로 선언
	if($gVal[0] <= $sumPrice && $sumPrice <= $gVal[1]){	// 현재 값이 sumprice 이하고 , 다음 값이 sumprice보다 이상 일 때
		if($gKey == '40'){				// 키 값이 40이면
			$gLevel = $gKey;			// gLevel에 40 대입
			$remainPrice = 0;			// 0 초기화 , 최대 계급인듯
		}else{
			$gLevel = $gKey+10;			// 키 값이 40이 아니면  + 10
			$remainPrice = ($gVal[1]+1) - $sumPrice;	// 상위 단계 값 - 현재까지의 주문 금액  = 등급 레벨업하는데 필요한 금액
		}
		list($next_groupname) = pmysql_fetch("SELECT group_name FROM tblmembergroup WHERE group_level = '".$gLevel."'");	// 멤버 그룹 테이블에서 그룹 레벨이 부여받은 $gLevel 인 select query를 실행 -- list타입으로 반환
		$nextLevel = $gKey;				// $gKey 값을 할당
		continue;						// foreach문 반복
	}
}
$time = time(); // 현재 시간 가져오기
$next_month = date("m", strtotime("+1 month", $time));	// 현재시간에 +1달인 Date에서 mm 달만 반환	== 다음 달
$levelMassage = "";										// 변수 선언 및 초기화
$next_month = reset(sscanf($next_month,'%d'));			// 정수 타입으로 변환하고, reset함수가 next_month가 가리키는 첫번째 값(= 다음 달)을 넘긴다
if($nextLevel < 40){					// 40 미만이면
	$levelMassage = "<strong>".number_format($remainPrice)."원</strong>을 더 구매하시면 <span>".$next_month."월</span>에 <br /> <span>".$next_groupname." 회원</span>이 되실 수 있습니다.";
}else{									// 40 이상이면
	$levelMassage = "총 <strong>".number_format($sumPrice)."원</strong>을 구매하여 <span>".$next_month."월</span>에 <span>".$next_groupname." 회원</span>이 되실 수 있습니다.";
}		//  number_format 숫자의 자릿수를 표시하여 출력한다
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">								<!-- 작성된 페이지가 XHTML로 작성되었음을 알려줍니다 -->
<meta http-equiv="Content-Type" content="text/html; charset=euc-kr" />	<!-- html은 euc-kr 인코딩 방식을 사용합니다 -->
<meta http-equiv="X-UA-Compatible" content="IE=edge" />					<!-- IE8 이상 버전에서만 지원하는 IE 전용 속성 -->
<HEAD>	<!-- HEAD 시작 -->
<TITLE><?=$_data->shoptitle?> - 마이페이지</TITLE>						<!-- title 지정-->
<META name="description" content="<?=(ord($_data->shopdescription)?$_data->shopdescription:$_data->shoptitle)?>">	<!-- 페이지의 설명 content는 검색엔진이 페이지에 대한 정보를 파악할 때 사용 -->
<META name="keywords" content="<?=$_data->shopkeyword?>">				<!-- 사용자가 페이지를 찾기위해 content의 정보를 검색할 수 있는 단어목록 -->
<script type="text/javascript" src="<?=$Dir?>lib/lib.js.php"></script>	<!-- javascript에 사용한 library 선언-->
<?php include($Dir."lib/style.php")?>									<!-- ../lib/style.php 포함-->
<SCRIPT LANGUAGE="JavaScript">	
function OrderDetailPop(ordercode) {				// 함수명 ordercode란 파라미터 받음
	document.form2.ordercode.value=ordercode;		// 파라미터 값을 이용해 해당 value값을 변경
	window.open("about:blank","orderpop","width=610,height=500,scrollbars=yes");		// 가로610,세로500,스크롤 가능한 orderpop이란 창이 open
	document.form2.submit();						// form2의 submit을 실행
}
function DeliSearch(deli_url){						// 함수명 deli_url란 파라미터 받음
	window.open(deli_url,"배송추적","toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizeble=yes,copyhistory=no,width=600,height=550");//// 가로600,세로550,툴바 X,메뉴바X,크기 조정가능, 기록복사 가능, 스크롤 가능한  deli_url 이란 창이 open
}
function DeliveryPop(ordercode) {					// 함수명 ordercode란 파라미터 받음
	document.form3.ordercode.value=ordercode;		// form3의 ordercode의 value 변경
	window.open("about:blank","delipop","width=600,height=370,scrollbars=no");	// 가로600,세로370,스크롤 가능한 delipop 창이 open
	document.form3.submit();						// form3 실행
}
function ViewPersonal(idx) {						// 함수명 idx란 파라미터 받음
	window.open("about:blank","mypersonalview","width=600,height=450,scrollbars=yes");	// 가로600,세로450,스크롤 가능한 mypersonalview 창이 open
	document.form4.idx.value=idx;					// form4에 있는 idx의 value 변경
	document.form4.submit();						// form4 실행
}
function OrderDetail(ordercode) {					// 함수명 ordercode란 파라미터 받음
	document.detailform.ordercode.value=ordercode;	// detailform의 ordercode의 value 변경
	document.detailform.submit();					// detailform 실행
}

</SCRIPT>	<!-- 아래에 선언한 form에서 호출할 함수, 함수 실행시 form에서 지정한 target의 이름과 같으면 새로운 window 화면이 호출된다 -->
</HEAD>		<!-- HEAD 종료 -->

<?php  include ($Dir.MainDir.$_data->menu_type.".php") // 메뉴   ?>		<!-- ../main/메뉴관련.php 포함 -->

<body<?=($_data->layoutdata["MOUSEKEY"][0]=="Y"?" oncontextmenu=\"return false;\"":"")?><?=($_data->layoutdata["MOUSEKEY"][1]=="Y"?" ondragstart=\"return false;\" onselectstart=\"return false;\"":"")?> leftmargin="0" marginwidth="0" topmargin="0" marginheight="0"><?=($_data->layoutdata["MOUSEKEY"][2]=="Y"?"<meta http-equiv=\"ImageToolbar\" content=\"No\">":"")?>
<!--  layout 정보에 따른 oncentextmenu 팝업메뉴 이벤트 막기  ,  ondragstart 글,이미지를 드래그할때 발생되는 이벤트 막기 , onselectstart 마우스 왼쪽을 클릭하여 텍스트 선택할때 발생되는 이벤트 막기  -- body의 외부와의 간격(왼쪽,넓이,위,높이)을 0으로 한다   leftmargin=0 , marginwidth=0 , topmargin=0, marginheight = 0 -->

<table border="0" cellpadding="0" cellspacing="0" width="100%">	 <!-- 겉 테이블 시작 100% 넓이, cell 간 여백 0 , cell과 내용간 패딩 0 , 테두리 없음 -->
<?php
$leftmenu="Y";	// 변수 생성 및 초기화
if($_data->design_mypage=="U") {	// 해당 U 코드일 경우
	$sql="SELECT body,leftmenu FROM tbldesignnewpage WHERE type='mypage'";	// 디자인뉴페이지에서 mypage type을 선택
	$result=pmysql_query($sql,get_db_conn());	// query 실행 ?
	if($row=pmysql_fetch_object($result)) {		// query 실행 결과물을 object 타입으로 결과 반환
		$body=$row->body;			// 변수 할당
		$body=str_replace("[DIR]",$Dir,$body);	// body의 정보 중에 [DIR]을 ../로 대체한다
		$leftmenu=$row->leftmenu;	// 변수 할당
		$newdesign="Y";				// 코드값 할당
	}
	pmysql_free_result($result);	// 
}
if($_data->design_mypage=="001" || $_data->design_mypage=="002" || $_data->design_mypage=="003"){	
if ($leftmenu!="N") {
	echo "<tr>\n";				// 외부 TR 시작
	if ($_data->title_type=="Y" && file_exists($Dir.DataDir."design/mypage_title.gif")) {	// 파일이 존재하면
		echo "<td><img src=\"".$Dir.DataDir."design/mypage_title.gif\" border=\"0\" alt=\"마이페이지\"></td>\n";										// ../data/design/mypage_title.gif를 가져오고 , 경계값은 없고 , 마이페이지란 이미지 설명글을 갖는다.
	} else {		// 파일이 없으면
		echo "<td>\n";			// 외부 TD 시작
		echo "<TABLE WIDTH=100% BORDER=0 CELLPADDING=0 CELLSPACING=0>\n";	// 내부 테이블 , 넓이 100%, 경계값 없고, cell 간격0, cell과 내용간 간격 0
		echo "<TR>\n";			// 내부 TR 시작
		echo "	<TD><IMG SRC={$Dir}images/{$_data->icon_type}/mypage_title_head.gif ALT=></TD>\n";	// ../image/____/mypage_title_head.gif 이미지를 가져온다.
		echo "	<TD width=100% valign=top background={$Dir}images/{$_data->icon_type}/mypage_title_bg.gif></TD>\n"; // TD에 배경 이미지 출력
		echo "	<TD width=40><IMG SRC={$Dir}images/{$_data->icon_type}/mypage_title_tail.gif ALT=></TD>\n";	// TD안에 ../images/____/mypage_title_tail.gif 이미지를 출력 , 이미지 설명글은 없다
		echo "</TR>\n";			// 내부 TR 끝
		echo "</TABLE>\n";		// 내부 테이블 끝
		echo "</td>\n";			// 외부 TD 끝
	}
	echo "</tr>\n";				// 외부 TR 끝
}
}
echo "<tr>\n";					// 외부 TR_1 시작
echo "	<td align=\"center\">\n";	// 외부 TD_1 시작 , 중앙 정렬
include ($Dir.TempletDir."mypage/mypage{$_data->design_mypage}.php");	// ../templet/mypage/mypage___.php 포함
echo "	</td>\n";					// 외부 TD_1 끝
echo "</tr>\n";					// 외부 TR_1 끝
?>

<!-- Form에서 submit버튼 선택시 onsubmit에서 js함수를 호출 , target에서 정의한 명칭의 창을 open  -->	
<!-- 현재는 기능 x  -->   
<form name=form2 method=post action="<?=$Dir.FrontDir?>orderdetailpop.php" target="orderpop"> <!-- post형식 이름-form2 submit되면 orderdetailpop.php로 이동-->
<input type=hidden name=ordercode>	<!-- 안보이는 type , ordercode란 name을 갖는 input 태그 -->
</form>								<!-- form2 종료-->
<form name=form3 method=post action="<?=$Dir.FrontDir?>deliverypop.php" target="delipop"><!-- post형식 이름-form3 submit되면 deliverypop.php로 이동-->
<input type=hidden name=ordercode>	<!-- 안보이는 type , ordercode란 name을 갖는 input 태그 -->
</form>								<!-- form3 종료-->
<form name=form4 action="<?=$Dir.FrontDir?>mypage_personalview.php" method=post target="mypersonalview"><!-- post형식 이름-form4 submit되면 mypage_personalview.php로 이동-->
<input type=hidden name=idx>		<!-- 안보이는 type , idx란 name을 갖는 input 태그 -->
</form>								<!-- form4 종료-->
<form name=detailform method=post action="<?=$Dir.FrontDir?>mypage_orderlist_view.php"><!-- post형식 이름-detailform submit되면 mypage_orderlist_view.php로 이동-->
<input type=hidden name=ordercode>	<!-- 안보이는 type , ordercode란 name을 갖는 input 태그 -->
</form>								<!-- detailform 종료-->
</table>							<!-- 외부 테이블 끝-->
<?=$onload?>						<!-- 출력 --어떤 데이터인지는 모르겠음... -->
<?php  include ($Dir."lib/bottom.php") ?>	<!-- ../lib/bottom.php 포함 -->
</BODY>		<!-- BODY 종료 -->
</HTML>		<!-- HTML 종료-->