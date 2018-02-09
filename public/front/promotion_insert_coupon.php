<?php
/************************************************************
*
* 룰렛 이벤트 당첨 아이팀 등록
*
* 작성일자: 2017-09-06
*
*************************************************************/

$Dir="../";

include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");
include_once dirname(__FILE__)."/../lib/product.class.php";
include_once($Dir."lib/jungbo_code.php"); //정보고시 코드를 가져온다
include_once($Dir."lib/paging_ajax.php");
include_once($Dir."lib/coupon.class.php");


/*
$sql = "SELECT id FROM tblmember WHERE id='nuneun9' ";
$result = pmysql_query( $sql, get_db_conn() );
if( $row = pmysql_fetch_object( $result ) ) {
	//$this->order['ordercode'] = unique_id();
	//$this->order['id'] =  $row->id;
	$ordercode = unique_id();
	$id = $row->id;
	pmysql_free_result( $result );


echo $id;
}
$vdate = date("Ymd");
$sql = "insert into tblpoint_act (mem_id,regdt) values('nuneun9', '".$vdate."')";
$result = pmysql_query($sql, get_db_conn());
exit;
*/

$mem_id = $_ShopInfo->getMemid();
$cdate = date("Y-m-d");
$date_start = date("YmdH");
$date_end = date("YmdH", strtotime('+2 weeks'));

$gubun					= $_REQUEST['gubun'] ? $_REQUEST['gubun'] : $_GETT['gubun'] ;
$coupon_name		= $_REQUEST['title'] ? $_REQUEST['title'] : $_GET['title'] ;
$idx						= $_REQUEST['idx'] ? $_REQUEST['idx'] : $_GET['idx'] ;
$index					= $_REQUEST['index'] ? $_REQUEST['index'] : $_GET['index'] ;
$update				= $_REQUEST['update'] ? $_REQUEST['update'] : $_GET['update'] ;
$sum					= $_REQUEST['sum'] ? $_REQUEST['sum'] : $_GETT['sum'] ;
$ticket					= $_REQUEST['ticket'] ? $_REQUEST['ticket'] : $_GETT['ticket'] ;
$sale = explode('%',$sum);
$sale_money = $sale[0];
/* 룰렛 아이템 등록 */
//xdebug($_REQUEST);
if($gubun=="c"){ //포인트의 경우
	
	$point				= $_REQUEST['point'] ? $_REQUEST['point'] : $_GET['point'] ;

	//$sql = "insert into tblpoint_act (mem_id,regdt,body,point,expire_date,tot_point,rel_flag,rel_mem_id,rel_job) values('".$mem_id."', '".$vdate."','".$title."',".$point.",'".$edate."',".$point.", '@roulette','".$mem_id."','".$mem_id."')";

	//pmysql_query($sql);
	
	//쿠폰발행
//	$coupon_code=substr(ceil(date("sHi").date("ds")/10*8)."000",0,8);
//
//	$cisql = "
//	INSERT INTO tblcouponinfo(
//	coupon_code	,
//	coupon_name	,
//	sale_type ,
//	date_start	,
//	date_end	,
//	sale_money	,
//	amount_floor ,
//	productcode ,
//	use_con_type1 ,
//	description ,
//	use_point ,
//	coupon_is_mobile ,
//	time_type ,
//	issue_code ,
//	date
//	) VALUES (
//	'{$coupon_code}',
//	'{$coupon_name}',
//	2,
//	'{$date_start}',
//	'{$date_end}',
//	'{$sale_money}',
//	2,
//	'ALL',
//	'Y',
//	'{$coupon_name}',
//	'Y',
//	'A',
//	'D',
//	2,
//	'".date("YmdHis")."')";
//	pmysql_query($cisql);
//
//	$issuesql="INSERT INTO tblcouponissue (coupon_code,id,date_start,date_end,date) VALUES ('{$coupon_code}','{$mem_id}','{$date_start}','{$date_end}','".date("YmdHis")."')";
//	pmysql_query($issuesql);
//
//	$encrypt_coupon	= $_REQUEST['coupon'];
//	$decrypt_coupon	= decrypt_authkey($encrypt_coupon);
//	$exp_coupon			= explode("|", $decrypt_coupon);

	//exdebug($exp_coupon);

	$memid	= $_ShopInfo->getMemid();
	//echo $memid;
	//exit;

	#쿠폰 설정 
	$_CouponInfo = new CouponInfo( $_REQUEST['coupon_type'] ); 
	//exdebug($_CouponInfo);

	#쿠폰 확인 
	$msg = $_CouponInfo->search_coupon( $_REQUEST['coupon_code'], $memid ); 
	$resultData = '';
	$result = 1;
	if( $msg == '0' ){
		$alert_text	= "해당하는 쿠폰이 없습니다.";
	} else if( $msg == '4' ) {
		//$alert_text	= "이미 발급된 쿠폰 입니다.";
		$alert_text	= "쿠폰이 이미 발급되었습니다. MY PAGE>쿠폰에서 확인할 수 있습니다.";
	} else if( $msg == '5' ) {
		//$alert_text	= "같은 쿠폰의 사용하지 않은 쿠폰이 존재 합니다.";
		$alert_text	= "쿠폰이 이미 발급되었습니다. MY PAGE>쿠폰에서 확인할 수 있습니다.";
	} else if( $msg == '1' ) {
		$_CouponInfo->set_couponissue( $memid ); 
		if ($_CouponInfo->issue_type != '0') {
			#insert 설정 
			$return_data = $_CouponInfo->insert_couponissue(); 
			if( $return_data[0] === 0 ) {
				$result = 0;
				$alert_text	= "쿠폰이 발급되었습니다.";
			} else {
				$alert_text	= "쿠폰이 발급되지 않았습니다.\\n관리자에게 문의하세요.(code : 1)";
			}
		} else {
			$alert_text	= "쿠폰이 발급되지 않았습니다.\\n관리자에게 문의하세요.(code : 2)";
		}
	} else {
		$alert_text	= "쿠폰이 발급되지 않았습니다.\\n관리자에게 문의하세요.(code : 3)";
	}
	if ($result == 0) {
		$sql = "update tblpromo set roulette_segment='".$update."' where idx='".$idx."'";
		pmysql_query($sql);

		//룰렛 발행 성공 업데이트
		$usql = "update tblpromo_roulett set success_yn = 'Y' where roulette_id='".$idx."' and member_id='".$memid."' and ticket='".$ticket."'  and success_yn = 'N' and regdate = '".$cdate."'";
		pmysql_query($usql);
	}
	$resultData .= $result."|".$alert_text;
	echo $resultData;	
}else{ //쿠폰의 경우

}



?>