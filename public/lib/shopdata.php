<?php
/********************************************************************* 
// 파 일 명		: shopdata.php 
// 설     명		: 쇼핑몰 정보
// 상세설명	: 쇼핑몰 정보를 불러온다. 로그인 프로세스를 불러온다.
// 작 성 자		: hspark
// 수 정 자		: 2015.10.28 - 김재수
// 
// 
*********************************************************************/ 
?>
<?php
if(basename($_SERVER['SCRIPT_NAME'])===basename(__FILE__)) {
	header("HTTP/1.0 404 Not Found");
	exit;
}

if(!class_exists('Paging',false)) {
	include_once('../lib/paging.php');
}

if(ord(RootPath)) {
	$hostscript=$_SERVER["HTTP_HOST"].$_SERVER["SCRIPT_NAME"];
	$pathnum=@strpos($hostscript,RootPath);
	$shopurl=substr($hostscript,0,$pathnum).RootPath;
} else {
	$shopurl=$_SERVER["HTTP_HOST"].'/';
}
$shopurl2=$_SERVER["HTTP_HOST"].$_SERVER["SCRIPT_NAME"];

/*
if($_SERVER["HTTPS"]=="on") {
	//http로 리다이렉트한다.
	header("Location:http://".$shopurl);
	exit;
}
*/
$old_shopurl=$_ShopInfo->getShopurl();

$ref=(isset($_REQUEST["ref"])?$_REQUEST["ref"]:"");
if (ord($ref)==0 && ord($_SERVER["HTTP_REFERER"])>0) {
	$ref=str_replace("http://","",strtolower($_SERVER["HTTP_REFERER"]));
}
if (ord($_ShopInfo->getShopurl())==0) {
	$sql = "SELECT * FROM tblshopinfo ";
	$result=pmysql_query($sql,get_db_conn());
	if ($row=pmysql_fetch_object($result)) {
		$_ShopInfo->setShopurl($shopurl);
		$_ShopInfo->setShopurl2($shopurl2);
		$_ShopInfo->Save();
	} else {
		error_msg("쇼핑몰 정보 등록이 안되었습니다.<br>쇼핑몰 설정을 먼저 하십시요", $Dir."install.php");
	}
	pmysql_free_result($result);
}



$_ShopInfo->setShopurl($shopurl);
$_ShopInfo->setShopurl2($shopurl2);

$_ShopData=new ShopData($_ShopInfo);
$_data=$_ShopData->shopdata;
$_data->shopurl=$shopurl;

//기본 쇼핑몰 타입을 정한다. - 이부분도 삭제해야함
if ($_ShopInfo->getAffiliateType() == '') {
	$_ShopInfo->setAffiliateType("1");
	$_ShopInfo->Save();
}

$_REQUEST["id"]=(isset($_REQUEST["id"])?$_REQUEST["id"]:"");
$_REQUEST["passwd"]=(isset($_REQUEST["passwd"])?$_REQUEST["passwd"]:"");
$_REQUEST["type"]=(isset($_REQUEST["type"])?$_REQUEST["type"]:"");
$_REQUEST["chUrl"]=(isset($_REQUEST["chUrl"])?$_REQUEST["chUrl"]:"");

if ((ord($_REQUEST["id"])>0 && ord($_REQUEST["passwd"])>0) || $_REQUEST["sns_login"] || $_REQUEST["type"]=="logout" || $_REQUEST["type"]=="exit") {
	include($Dir."lib/loginprocess.php");
	exit;
}

if($_data->adult_type=="Y" || (($_data->adult_type=="M" || $_data->adult_type=="B") && (ord($_REQUEST["id"])==0 || ord($_REQUEST["passwd"])==0) && ord($_ShopInfo->getMemid())==0)) {
	if($old_shopurl!=$_ShopInfo->getShopurl() || ord($old_shopurl)==0) {
		$_ShopInfo->setShopurl("");
		$_ShopInfo->SetMemNULL();
		$_ShopInfo->Save();
		echo "<script>location.href='".$Dir."'</script>";
		exit;
	}
}

