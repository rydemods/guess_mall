<?
header("Content-Type:text/html;charset=utf-8");
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once("lib.inc.php");
include_once("shopdata.inc.php");

$id = $_REQUEST["id"];
$ordercode = $_REQUEST["ordercode"];
$modreserve = $_REQUEST["modreserve"];
$tot_reserve = $_REQUEST["tot_reserve"];
$date = date("YmdHis");

$sql = "UPDATE tblorderinfo SET reserve={$modreserve} WHERE ordercode='{$ordercode}' ";
pmysql_query($sql,get_db_conn());
/*$sql2 = "INSERT INTO tblreserve (id, reserve, reserve_yn, content, date) VALUES ( '{$id}', {$tot_reserve}, 	'Y', '관리자 임의 적립금처리(주문서 상세처리)', 	'{$date}')";
pmysql_query($sql2,get_db_conn());
$sql3 = "UPDATE tblmember SET reserve=reserve+{$tot_reserve} WHERE id='{$id}' ";
pmysql_query($sql3,get_db_conn());*/

//적립금을 수정한다.(2015.11.25 - 김재수 추가)
if ($tot_reserve != 0) insert_point($id, $tot_reserve, "관리자 임의 적립금처리(주문서 상세처리)", '','','', $return_point_term);

echo "<script>alert('적립금 처리가 완료되었습니다.'); location.href= window.close();</script>";

?>