<?php

if(strlen($Dir)==0) $Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/cache_main.php");
include_once($Dir."lib/timesale.class.php");
include_once($Dir."conf/config.php");
Header("Pragma: no-cache");
include_once($Dir."lib/shopdata.php");
/*
if(isdev()){
include_once($Dir."lib/eventpopup.php");
include_once($Dir."lib/eventlayer.php");
}
*/
/*$timesale=new TIMESALE();*/

$mainpagemark = "Y"; // 메인 페이지
$selfcodefont_start = "<font class=\"mainselfcode\">"; //진열코드 폰트 시작
$selfcodefont_end = "</font>"; //진열코드 폰트

//exdebug($_data);

//include_once($Dir.MainDir.$_data->main_type.".php");
// 오아니 담당자 IP =  $_SERVER["REMOTE_ADDR"]=="115.94.156.68"
$id=$_GET['id'];
if($id){
	include_once($id);
}else{
	include_once("tem_main001.php");
}

if($HTML_CACHE_EVENT=="OK") ob_end_flush();
?>
<script></script>
