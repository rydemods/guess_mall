<?php
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");
include_once('outline/header_m.php'); 
include_once($Dir."lib/file.class.php");
include_once($Dir."conf/config.ap_point.php");				// 활동포인트 지급 정보
$ap_sns_point	= $pointSet['sns']['point'];	// sns 지급 포인트
$snsType = $_POST['snstype'];
$code = $_POST['code'];
$menu = $_POST['menu'];
$memId = $_ShopInfo->getMemid() ? $_ShopInfo->getMemid() : $_MShopInfo->getMemid();

//sns 공유 포인트 지급
if($snsType == "facebook"){
	// 오늘 sns 공유 시 적립받은 갯수를 체크한다.
	list($cp_cnt)=pmysql_fetch_array(pmysql_query("select COUNT(*) cp_cnt from tblpoint_act WHERE rel_flag='@sns_in_facebook_point' and rel_job = '".$menu.$code."' and regdt >= '".date("Ymd")."000000' AND regdt <= '".date("Ymd")."235959' AND mem_id = '".$_ShopInfo->getMemid()."' "));
	if ($cp_cnt == 0) {
		insert_point_act($memId, $ap_sns_point, 'sns 공유 포인트', '@sns_in_facebook_point', $memId, $menu.$code, 0);
	}
}else if($snsType == "twitter"){
	list($cp_cnt)=pmysql_fetch_array(pmysql_query("select COUNT(*) cp_cnt from tblpoint_act WHERE rel_flag='@sns_in_twitter_point' and rel_job = '".$menu.$code."' and regdt >= '".date("Ymd")."000000' AND regdt <= '".date("Ymd")."235959' AND mem_id = '".$_ShopInfo->getMemid()."' "));
	if ($cp_cnt == 0) {
		insert_point_act($memId, $ap_sns_point, 'sns 공유 포인트', '@sns_in_twitter_point', $memId, $menu.$code, 0);
	}
}else if($snsType == "band"){
	list($cp_cnt)=pmysql_fetch_array(pmysql_query("select COUNT(*) cp_cnt from tblpoint_act WHERE rel_flag='@sns_in_band_point' and rel_job = '".$menu.$code."' and regdt >= '".date("Ymd")."000000' AND regdt <= '".date("Ymd")."235959' AND mem_id = '".$_ShopInfo->getMemid()."' "));
	if ($cp_cnt == 0) {
		insert_point_act($memId, $ap_sns_point, 'sns 공유 포인트', '@sns_in_band_point', $memId, $menu.$code, 0);
	}
}else if($snsType == "kakaostory"){
	list($cp_cnt)=pmysql_fetch_array(pmysql_query("select COUNT(*) cp_cnt from tblpoint_act WHERE rel_flag='@sns_in_kakaostory_point' and rel_job = '".$menu.$code."' and regdt >= '".date("Ymd")."000000' AND regdt <= '".date("Ymd")."235959' AND mem_id = '".$_ShopInfo->getMemid()."' "));
	if ($cp_cnt == 0) {
		insert_point_act($memId, $ap_sns_point, 'sns 공유 포인트', '@sns_in_kakaostory_point', $memId, $menu.$code, 0);
	}		
}else if($snsType == "kakaotalk"){
	list($cp_cnt)=pmysql_fetch_array(pmysql_query("select COUNT(*) cp_cnt from tblpoint_act WHERE rel_flag='@sns_in_kakaotalk_point' and rel_job = '".$menu.$code."' and regdt >= '".date("Ymd")."000000' AND regdt <= '".date("Ymd")."235959' AND mem_id = '".$_ShopInfo->getMemid()."' "));
	if ($cp_cnt == 0) {
		insert_point_act($memId, $ap_sns_point, 'sns 공유 포인트', '@sns_in_kakaotalk_point', $memId, $menu.$code, 0);
	}
}

echo $cp_cnt;
?>