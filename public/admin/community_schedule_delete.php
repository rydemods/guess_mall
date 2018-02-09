<?php // hspark
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");

if(ord($_ShopInfo->getId())==0){
	echo "<script>alert('정상적인 경로로 접근하시기 바랍니다.');window.close();</script>";
	exit;
}

extract($_REQUEST);

$CurrentTime = time();

if ($sid && $return_page && $year && $month && $day) {
	$sql = "DELETE FROM tblschedule WHERE idx = '{$sid}' ";
	$delete = pmysql_query($sql,get_db_conn());

	if ($delete) {
		alert_go('해당 일정을 삭제 하였습니다.',"{$return_page}?year={$year}&month={$month}&day={$day}");
	} else {
		alert_go('일정 삭제중 오류가 발생하였습니다.',-1);
	}
} else {
	alert_go('잘못된 경로의 일정입니다.',-1);
}
