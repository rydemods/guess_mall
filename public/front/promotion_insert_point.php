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
$vdate = date("YmdHis").rand(1,9);
$edate = date("Ymd", strtotime('+2 weeks'));

$gubun					= $_REQUEST['gubun'] ? $_REQUEST['gubun'] : $_GETT['gubun'] ;
$title						= $_REQUEST['title'] ? $_REQUEST['title'] : $_GET['title'] ;
$name						= $_REQUEST['name'] ? $_REQUEST['name'] : $_GET['name'] ;
$idx						= $_REQUEST['idx'] ? $_REQUEST['idx'] : $_GET['idx'] ;
$index					= $_REQUEST['index'] ? $_REQUEST['index'] : $_GET['index'] ;
$expire_date					= $_REQUEST['expire_date'] ? $_REQUEST['expire_date'] : $_GET['expire_date'] ;
$update				= $_REQUEST['update'] ? $_REQUEST['update'] : $_GET['update'] ;
$ticket					= $_REQUEST['ticket'] ? $_REQUEST['ticket'] : $_GETT['ticket'] ;
/* 룰렛 아이템 등록 */
if($gubun=="p"){ //포인트의 경우
	
	$point				= $_REQUEST['point'] ? $_REQUEST['point'] : $_GET['point'] ;

	//$sql = "insert into tblpoint_act (mem_id,regdt,body,point,expire_date,tot_point,rel_flag,rel_mem_id,rel_job) values('".$mem_id."', '".$vdate."','".$title."',".$point.",'".$edate."',".$point.", '@roulette','".$mem_id."','roulette-event-".$vdate."')";

	//pmysql_query($sql);
			//룰렛 발행 성공 업데이트


	$result = insert_point_act($mem_id, $point, $title."-".$name, '@roulette', $mem_id, 'roulette-event-'.$vdate, $expire_date);
	$sql = "update tblpromo set roulette_segment='".$update."' where idx='".$idx."'";
	pmysql_query($sql);

	$usql = "update tblpromo_roulett set success_yn = 'Y' where roulette_id='".$idx."' and member_id='".$mem_id."' and ticket='".$ticket."'  and success_yn = 'N' and regdate = '".$cdate."'";
	pmysql_query($usql);

	echo $result;

	
}else{ //쿠폰의 경우

}



?>