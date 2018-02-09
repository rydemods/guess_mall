<?php
if(basename($_SERVER['SCRIPT_NAME'])===basename(__FILE__)) {
	header("HTTP/1.0 404 Not Found");
	exit;
}

if(ord(RootPath)>0) {
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

$ref=$_REQUEST["ref"];
if (ord($ref)==0 && ord($_SERVER["HTTP_REFERER"])>0) {
	$ref=str_replace("http://","",($_SERVER["HTTP_REFERER"]));
}
if (ord($_ShopInfo->getShopurl())==0) {
	$sql = "SELECT * FROM tblshopinfo ";
	$result=pmysql_query($sql,get_db_conn());
	if ($row=pmysql_fetch_object($result)) {
		$_ShopInfo->setShopurl($shopurl);
		//$_ShopInfo->Save();	//save시키면 성인몰 또는 b2b쇼핑몰의 경우 바로 진입이 가능하기 때문.
	} else {
		error_msg("쇼핑몰 정보 등록이 안되었습니다.<br>쇼핑몰 설정을 먼저 하십시요", $Dir."install.php");
	}
	pmysql_free_result($result);
}
$_ShopInfo->setShopurl($shopurl);
$_ShopData=new ShopData($_ShopInfo);
$_data=$_ShopData->shopdata;
$_data->shopurl=$shopurl;
$_data->shopurl2=$shopurl2;
