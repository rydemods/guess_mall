<?php
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");

$prductcode=$_POST["prductcode"];
$stime=$_POST["stime"];

#현제 진행중인 핫딜 가져오기
//list($now_sdate)=pmysql_fetch("select sdate from tblhotdeal where view_type='1' and productcode='".$prductcode."' order by sdate limit 1");

if(!$prductcode){
	$check="NO";
}else{
	#페이지를 들어왔을때 현제시간과 이벤트 시작시간을 체크하여 상태값 변경
	if(strtotime(date('Y-m-d H:i:s'))>=strtotime($stime)){
		#상품이 비노출상태이거나 상품의 존재유무 가져오기
		list($view_type)=pmysql_fetch("select display from tblproduct where productcode='".$prductcode."'");
		#상품이 없으면 튕김
		if(!$view_type){
			$check="NO";
		#상품이 비노출상태이면 노출상태로변경
		}else if($view_type=="N"){
			pmysql_fetch("update tblproduct set display='Y' where productcode='".$prductcode."'");
			$check="OK";
		}else{
			$check="OK";
		}
	}else{
		$check="NO";
	}
}

echo $check;
//echo $stime;

?>