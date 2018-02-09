<?php // hspark
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include("access.php");

####################### 페이지 접근권한 check ###############
/*
$PageCode = "pr-1";
$MenuCode = "nomenu";
if (!$_usersession->isAllowedTask($PageCode)) {
	include("AccessDeny.inc.php");
	exit;
}
*/
#########################################################

$mode = $_POST['mode'];
$prcode = $_POST['prcode'];
$dcprice = $_POST['dcprice'];
$memo = htmlspecialchars($_POST['memo']);
$applydate = $_POST['applydate'];

switch($mode)
{
	case "regist":
	
	//상품 정보를 가져옴
	$sql = "SELECT * FROM tblproduct WHERE productcode = '{$prcode}'";
	$result = pmysql_query($sql,get_db_conn());
	$row=pmysql_fetch_object($result);
	$sellprice = ($row->sellprice)?$row->sellprice:0;
	
	//해당 날짜에 있는 정보를 지움
	//하루에 두개의 상품이 등록 될 수 없음
	
	$sql = "DELETE FROM tblproductoneday WHERE applydate='{$applydate}'";
	$result = pmysql_query($sql,get_db_conn());
	pmysql_free_result($result);
	
	$sql = "INSERT INTO tblproductoneday(
            		productcode, 
            		sellprice, 
            		dcprice, 
            		regdate, 
            		modifydate,
            		memo, 
            		applydate
            	)
    VALUES ('{$prcode}', 
    		{$sellprice},
    		{$dcprice},
    		now(),
    		now(), 
            '{$memo}',
            '{$applydate}');";

	$result = pmysql_query($sql,get_db_conn());
	if($result)$msg="오늘의 특가가 등록되었습니다.";
	break;
}
alert_go($msg,"market_onedayprice.php");

?>