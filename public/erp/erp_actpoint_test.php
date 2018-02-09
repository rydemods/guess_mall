<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");

//전체 활동포인트 ERP 전송
//$sql = "SELECT * FROM tblpoint_act ORDER BY regdt asc";
$sql = "SELECT * FROM tblpoint_act /*WHERE mem_id ='ssuya@commercelab.co.kr'*/ ORDER BY regdt asc limit 50";
$result = pmysql_query($sql,get_db_conn());
while ($row=pmysql_fetch_object($result)) {
	$mem_id		= $row->mem_id;
	$body			= $row->body;
	$rel_flag			= $row->rel_flag;
	$point			= $row->point;
	$regdt			= substr($row->regdt, 0, 8);
	
	$res	= erpActPointIns($mem_id, $body, $rel_flag, $point, $regdt);
	exdebug($res);
}

//개별 활동포인트 ERP 전송
$mem_id	= "kgty4@naver.com";
$body		= "주문 2016101415560923979A 배송완료(1건)에 의한 적립금 지급";
$rel_flag		= "";
$point		= "1190";
$regdt		= "20161014";

$res	= erpActPointIns($mem_id, $body, $rel_flag, $point, $regdt);
exdebug($res);
?>