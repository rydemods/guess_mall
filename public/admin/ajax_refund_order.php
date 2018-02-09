<?php
/********************************************************************* 
// 파 일 명		: ajax_refund_order.php
// 설     명		: 가상계좌 환불신청
// 상세설명	:  반품 완료된 가상계좌 환불신청
// 작 성 자		: 2016-12-27 - daeyeob(김대엽)
// 
*********************************************************************/ 
?>
<?php
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");

$oc_no = $_POST["oc_no"];
$result = array();
if($oc_no){
	$sql = "UPDATE tblorder_cancel SET imagination_cancel = 'Y' WHERE oc_no = '{$oc_no}'";
	pmysql_query($sql, get_db_conn());
	if( !pmysql_error() ){
		$result = array( 'result' => 'success');
	}else{
		$result = array( 'result' => 'fail');
	}
}else{
	$result = array( 'result' => 'fail');
}

echo json_encode($result);



?>