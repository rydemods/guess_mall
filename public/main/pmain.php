<?php

if(strlen($Dir)==0) $Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/cache_main.php");
include_once($Dir."lib/timesale.class.php");
include_once($Dir."conf/config.php");
Header("Pragma: no-cache");
include_once($Dir."lib/shopdata.php");
include_once($Dir."lib/eventpopup.php");

$timesale=new TIMESALE();

$mainpagemark = "Y"; // 메인 페이지
$selfcodefont_start = "<font class=\"mainselfcode\">"; //진열코드 폰트 시작
$selfcodefont_end = "</font>"; //진열코드 폰트

//include_once($Dir.MainDir.$_data->main_type.".php");
include_once("tem_pmain001.php");

if($HTML_CACHE_EVENT=="OK") ob_end_flush();
?>

