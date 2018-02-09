<?
header("Content-Type: text/html; charset=UTF-8");
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");
include_once($Dir."lib/basket.class.php");
include_once($Dir."lib/order.class.php");
include_once($Dir."lib/delivery.class.php");

#### PG 데이타 세팅 ####
$_ShopInfo->getPgdata();
########################

$ip = $_SERVER['REMOTE_ADDR'];
// 모바일 분기처리

$mobileBrower = '/(iPod|iPhone|Android|BlackBerry|SymbianOS|SCH-M\d+|Opera Mini|Windows CE|Nokia|SonyEricsson|webOS|PalmOS|iPad)/';
$location_basket = '';
if ( preg_match($mobileBrower, $_SERVER['HTTP_USER_AGENT']) ) {
    $location_basket = $Dir.'m/basket.php';
} else {
    $location_basket = $Dir.FrontDir.'basket.php';
}

$sslchecktype="";
if($_POST["ssltype"]=="ssl" && strlen($_POST["sessid"])==64) {
	$sslchecktype="ssl";
}
if($sslchecktype=="ssl") {
	$secure_data=getSecureKeyData($_POST["sessid"]);
	if(!is_array($secure_data)) {
		alert_go('보안인증 정보가 잘못되었습니다.',-2);
	}
	foreach($secure_data as $key=>$val) {
		${$key}=$val;
	}
} else {
	foreach($_POST as $key=>$val) {
		${$key}=$val;
	}
}

$receipt_yn = $paymethod!="B"?"Y":$receipt_yn;

$receipt_yn = $paymethod=="G"?"N":$receipt_yn; // 임직원 마일리지 결제일 경우 영수증 무로 처리

if ($paymethod=="B") $pay_data = $pay_data1;
else if (strstr("CP", $paymethod)) $pay_data = $pay_data2;
else if ($paymethod=="V") $pay_data = "실시간 계좌이체 결제중";

//주문실패시 refferer 확인
$sendReferer = parse_url( $_SERVER['HTTP_REFERER'] );
$mobile_path = $sendReferer['path'];
$sender_name=str_replace(" ","",$sender_name);
$sender_email=str_replace("'","",$sender_email);
$receiver_name=str_replace(" ","",$receiver_name);
$order_msg=str_replace("'","",$order_msg);
$sender_tel=str_replace("'","",$sender_tel);
$receiver_tel1=str_replace("'","",$receiver_tel1);
$receiver_tel2=str_replace("'","",$receiver_tel2);
$receiver_addr=str_replace("'","",$receiver_addr);
$rpost=$rpost1.$rpost2;
$overseas_code = strip_tags( $overseas_code );
if( $usereserve == '' ) $usereserve = 0;
if( $usepoint == '' ) $usepoint = 0;
$deli_type = $_POST["deli_type"];
$loc = mb_substr($raddr1,0,4,'utf-8');
//if( $dcoupon_price == '' ) $dcoupon_price = 0;
//$pmethod=$paymethod.$pg_type;

if (ord($paymethod)==0) {
	echo "
        <html></head><body onload=\"alert('결제방법이 선택되지 않았습니다.');parent.document.form1.process.value='N';
        parent.location.herf='".$location_basket."' ;\"></body></html>";
	exit;
}

if (ord($usereserve)>0 && !IsNumeric($usereserve)) {
	echo "<html></head><body onload=\"alert('포인트는 숫자만 입력하시기 바랍니다.');parent.document.form1.process.value='N';parent.location.herf='".$location_basket."';\"></body></html>";
	exit;
}

if (ord($usepoint)>0 && !IsNumeric($usepoint)) {
	echo "<html></head><body onload=\"alert('E포인트는 숫자만 입력하시기 바랍니다.');parent.document.form1.process.value='N';parent.location.herf='".$location_basket."';\"></body></html>";
	exit;
}

if(ord($_data->escrow_id)==0 && $paymethod=="Q") {
	echo "<html></head><body onload=\"alert('에스크로 정보가 존재하지 않습니다.');parent.document.form1.process.value='N';parent.location.herf='".$location_basket."';\"></body></html>";
	exit;
}

//고객정보 arr
//order class에 들어갈 정보
$info_obj = array(
	'sender_name'   =>$sender_name,		// 주문자 이름
	'sender_tel'    =>$sender_tel,		// 주문자 휴대전화 번호
    'sender_tel2'   =>$home_tel,        // 주문자 전화 번호
	'sender_email'  =>$sender_email,	// 주문자 이메일
	'receiver_name' =>$receiver_name,	// 받는이 이름
	'receiver_tel1' =>$receiver_tel1,	// 받는이 전화번호
	'receiver_tel2' =>$receiver_tel2,	// 받는이 휴대전화 번호
	'receiver_addr' =>$receiver_addr,	// 받는이 주소
	'post5'         =>$post5,           // 받는이 신 우편번호
	'loc'           =>$loc,				// 받는이 주소 앞자리
	'bank_sender'   =>$bank_sender,		// 입금자 이름
	'receipt_yn'    =>$receipt_yn,		// 영수증 신청관련 ??
	'order_msg'     =>$order_msg,		// 주문자 메세지 - 미사용
	'order_msg2'    =>$order_prmsg,		// 주문자 메세지2
	//'deli_type'     =>$deli_type,		// 선불 0 / 착불 1 -> 사용안함
	'overseas_code' =>$overseas_code,	// 해외배송 코드
	'paymethod'     =>$paymethod,		// 결제 방법
	'pay_data'      =>$pay_data,		// 주문관련정보
	//'total_sum'     =>( $total_sumprice + $total_deli_price + $total_deli_price_area - $dcoupon_price  )	// 주문 최종금액 ( 확인용 )
	'total_sum'     =>( $total_sumprice + $total_deli_price + $total_deli_price_area )	// 주문 최종금액 ( 확인용 )
);

$Order = new Order();
// 임직원 구매 세팅
$chk_staff_order = chk_staff_order( $staff_order );
if( $chk_staff_order == 0 ) { // 0 - 오류처리 1 - 일반구매 2 - 임직원 구매
	echo "<script>";
	echo "  alert('잘못된 구매 방법 입니다.'); ";
	echo "  parent.location.replace('".$location_basket."')";
	echo "</script>";
	exit;
} else if( $chk_staff_order == 2 ){
	$Order->set_staff_order('Y');
}

$Order->set_cooper_order( $cooper_order ); // 협력사 구분

$Order->order_setting( $basketidxs );
$_odata = $Order->get_order_object(); //주문에 들어가는 상품정보

$Delivery = new Delivery(); // 
$Delivery->get_product( $_odata ); // 상품정보를 세팅
$Delivery->set_deli_select( $deli_select ); // 선/착불 배정송보를 세팅
$Delivery->set_deli_item();

$vender_info  = $Delivery->get_vender();
$vender_deli  = $Delivery->get_vender_deli();
$free_deli    = $Delivery->get_free_deli();
$product_deli = $Delivery->get_product_deli();

$coupon_flag = true;
if( count( $prcoupon_ci_no ) > 0 ){ // 상품쿠폰 체크
    $p_sql = "SELECT COUNT( * ) AS cnt FROM tblcouponissue WHERE id = '".$_ShopInfo->getMemid()."' AND used = 'N' AND ci_no IN (".implode( ',', $prcoupon_ci_no ).") ";
    $p_res = pmysql_query( $p_sql, get_db_conn() );
    $p_row = pmysql_fetch_object( $p_res );
    pmysql_free_result( $p_res );
    if( $p_row->cnt != count( $prcoupon_ci_no ) ) $coupon_flag = false;
}
/*
if( count( $bcoupon_ci_no ) > 0 ){ // 장바구니쿠폰 체크
    $b_sql = "SELECT COUNT( * ) AS cnt FROM tblcouponissue WHERE id = '".$_ShopInfo->getMemid()."' AND used = 'N' AND ci_no IN (".implode( ',', $bcoupon_ci_no ).") ";
    $b_res = pmysql_query( $b_sql, get_db_conn() );
    $b_row = pmysql_fetch_object( $b_res );
    pmysql_free_result( $b_res );
    if( $b_row->cnt != count( $bcoupon_ci_no ) ) $coupon_flag = false;
}
if( count( $dcoupon_ci_no ) > 0 ){ // 배송료 무료쿠폰 체크
    $d_sql = "SELECT COUNT( * ) AS cnt FROM tblcouponissue WHERE id = '".$_ShopInfo->getMemid()."' AND used = 'N' AND ci_no IN (".implode( ',', $dcoupon_ci_no ).") ";
    $d_res = pmysql_query( $d_sql, get_db_conn() );
    $d_row = pmysql_fetch_object( $d_res );
    pmysql_free_result( $d_res );
    if( $d_row->cnt != count( $dcoupon_ci_no ) ) $coupon_flag = false;
}
*/
if( $usereserve > 0 || $usepoint > 0 ){ // 마일리지 체크
    $r_sql = "SELECT reserve, staff_reserve, act_point FROM tblmember WHERE id ='".$_ShopInfo->getMemid()."' ";
    $r_res = pmysql_query( $r_sql, get_db_conn() );
    $r_row = pmysql_fetch_object( $r_res );
    pmysql_free_result( $r_res );

	//통합포인트를 erp에서 가져온다.
	$user_erp_reserve=getErpMeberPoint($_ShopInfo->getMemid());
	$erp_reserve = $user_erp_reserve[p_data]?$user_erp_reserve[p_data]:"0";

    if( ( $erp_reserve < $usereserve ) && $staff_order == 'N' ) $coupon_flag = false;
	else if( ( $r_row->act_point < $usepoint ) && $staff_order == 'N' ) $coupon_flag = false;
}

//if($staff_order == 'Y' || $cooper_order == 'Y'){
	if($staff_order == 'Y'){
		if( ( $usereserve>0 || $usepoint>0 ) && $staff_order == 'Y' ) $coupon_flag = false;
//		if( ( $usereserve>0 || $usepoint>0 ) && $cooper_order == 'Y' ) $coupon_flag = false;
}

if( $coupon_flag === false ){
    echo "<script>";
	echo "  alert('잘못된 방식의 쿠폰/포인트를 사용하셨습니다.'); ";
	echo "  parent.location.replace('".$location_basket."')";
	echo "</script>";
	exit;
}
# 2016-06-22 유효상품인지 체크 유동혁
/*$strBk = '';
$exp_basketidx = explode("|", $basketidxs );
foreach( $exp_basketidx as $expKey => $expVal){
    if( $expVal ) $arr_basketidx[] = $expVal;
}
$strBk = implode("', '", $arr_basketidx );
pmysql_query( " DELETE FROM tblorder_check WHERE basketidx IN ( '".$strBk."' ) AND paycode != '".$paycode."' ", get_db_conn() );
$orderChk_res = pmysql_query( " SELECT COUNT( * ) AS cnt FROM tblorder_check WHERE paycode = '".$paycode."' AND basketidx IN ( '".$strBk."' ) ", get_db_conn() );
$orderChk_row = pmysql_fetch_object( $orderChk_res );
pmysql_free_result( $orderChk_res );
if( $orderChk_row->cnt != count( $arr_basketidx ) ){
    echo "<script>";
	echo "  alert('잘못된 구매 방법 입니다.'); ";
	echo "  parent.location.replace('".$location_basket."')";
	echo "</script>";
	exit;
}*/
# 상품쿠폰 세팅
$prcoupon_data = array();
foreach( $prcoupon_bridx as $prcouponKey=>$prcouponVal ){

	$prcoupon_data = array(
		'basketidx'=>$prcouponVal,
		'ci_no'=>$prcoupon_ci_no[$prcouponKey]
	);

	$Order->product_coupon_set( $prcoupon_data );
}
/*
#장바구니쿠폰 세팅
foreach( $bcoupon_ci_no as $bcouponKey=>$bcouponVal ){
	$Order->basket_coupon_set( $bcouponVal );
}
#배송료 무료 쿠폰
if( strlen( $dcoupon_ci_no ) > 0  ){
    $Order->basket_coupon_set( $dcoupon_ci_no );
}
*/
#벤더별 배송비 정책
$Order->set_vender_info( $vender_info );
#상품별 배송료
$Order->product_delivery_set( $product_deli );
#벤더별 배송료
$Order->vender_delivery_set( $vender_deli );
#지역별 배송료
$Order->delivery_area_set( $total_deli_price_area );


/*
//배송쿠폰을 확인하기 위하여 쿠폰 정보를 불러온다
$delivery_coupon = false;
$couponArr = $Order->get_basket_coupon();
foreach( $couponArr as $key=>$val ){
	foreach( $val as $couponKey=>$couponVal ){
		if( $couponVal['coupon_type'] == '9' ){
			$delivery_coupon = true;
		}
	}
}
# 무료배송
if( $delivery_coupon ){
    $Order->set_free_deli();
}
*/
/*
$vender_price = $Order->get_vender_deliprice(); //벤더별 배송비
$product_deliprice = $Order->get_product_deliprice(); //상품별 배송비
$_total_price = $Order->get_total_price(); //총가격
*/

# 포인트 사용
$Order->reserve_set( $usereserve, $usepoint, $total_deli_price_area );

$Order->set_orderinfo( $info_obj );
$retunData  = $Order->order_send(); //주문시작
$last_price = $Order->get_last_price(); //최종 결제금액
$orderData  = $Order->get_order(); //주문정보를 가져옴
$goodname   = $Order->get_goodname(); // pg에 들어갈 상품이름을 가져옴
$pg_type    = $orderData['pg_type']; //pg에 들어갈 pg_type 'A' KCP, 'B' ?, 'C' 올더게이트, ...
$pgid_info  = $orderData['pgid_info']; //pg에 입력된 정보들
$ordercode  = $orderData['ordercode']; //주문코드
$paymethod  = $orderData['paymethod']; // 마일리지 등 금액을 0원으로 만들었을때

if (!strstr("BG",$paymethod) && ord($pg_type)==0) {
	echo "<html></head><body onload=\"alert('선택하신 결제방법은 이용하실 수 없습니다.');parent.document.form1.process.value='N';parent.location.herf='".$location_basket."';\"></body></html>";
	exit;
}

if( !$retunData ){
	echo "<html></head><body onload=\"alert('주문이 실패되었습니다.');parent.document.form1.process.value='N';parent.location.herf='".$location_basket."';\"></body></html>";
	exit;
}

//기본 배송지 저장 추가
if($destinationt_type=="Y" && $_ShopInfo->getMemid()){
	$destinationt_qry = "SELECT no FROM tbldestination WHERE mem_id = '".$_ShopInfo->getMemid()."' AND base_chk = 'Y'";
	$destinationt_Res = pmysql_query( $destinationt_qry, get_db_conn());
	$destinationt_Row = pmysql_fetch_object($destinationt_Res);

	if($rpost1 && $rpost2) $postcode=$rpost1."-".$rpost2;
	else $postcode="";

	$mobile_number=$receiver_tel21.$receiver_tel22.$receiver_tel23;
	
	if($destinationt_Row->no){
		$destinationt_indb = "UPDATE tbldestination SET  destination_name='기본배송지', get_name='".$receiver_name."', mobile='".$mobile_number."', postcode='".$postcode."', postcode_new='".$post."', addr1='".$raddr1."', addr2='".$raddr2."', reg_date='".date("Y-m-d")."' WHERE no = ".$destinationt_Row->no."";
		pmysql_query( $destinationt_indb, get_db_conn());
	}else{
		$destinationt_indb = "insert into tbldestination (mem_id, destination_name, get_name, mobile, postcode, postcode_new, addr1, addr2, base_chk, reg_date) values ('".$_ShopInfo->getMemid()."', '기본배송지', '".$receiver_name."', '".$mobile_number."', '".$postcode."', '".$post."', '".$raddr1."', '".$raddr2."', 'Y', '".date("Y-m-d")."')";
		pmysql_query( $destinationt_indb, get_db_conn());
	}
}

if(!strstr("BG",$paymethod)) {
	########### 결제시스템 연결 시작 ##########
	include($Dir.FrontDir."paylist.php");
	exit;
	########### 결제시스템 연결 끝   ##########
}

########### 최종 마무리 ###########
include($Dir.FrontDir."payresult.php");
########### 최종 마무리 끝 ########

