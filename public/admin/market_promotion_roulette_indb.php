<?php
$Dir="../";
/*
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");
include_once($Dir."lib/basket.class.php");
include_once($Dir."lib/delivery.class.php");
*/
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");
include_once dirname(__FILE__)."/../lib/product.class.php";
include_once($Dir."lib/jungbo_code.php"); //정보고시 코드를 가져온다
include_once($Dir."lib/paging_ajax.php");
include_once($Dir."lib/coupon.class.php");


$vdate = date("Ymd");
$start_date = $_REQUEST['date']; // 이벤트 시작일
$mode = $_REQUEST['mode'];
/* 룰렛이벤트 기 등록건 조회
   mode : 
         date : 신규 등록하는 룰렛이벤트 시작일과 기 등록된 룰렛 이벤트 종료일이 겹치는 경우의 이벤트 COUNT
*/

if($mode=="date"){
	//기등록건 조회
	list($roulette_cnt)=pmysql_fetch_array(pmysql_query("select count(*) cnt from tblpromo where event_type='5' and hidden = 1 and end_date >= '".$start_date."' "));
	
	$msg = ($roulette_cnt > 0 ? "현재 진행 중인 프로모션이 있습니다.\n 중복되는 프로모션을 확인해주세요." : $roulette_cnt);
	
	echo $msg;
}
?>