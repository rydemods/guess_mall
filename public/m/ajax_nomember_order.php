<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");
include_once("lib.inc.php");
include_once("shopdata.inc.php");
//header("Content-Type: text/html; charset=EUC-KR");

/*
if(strlen($_ShopInfo->getMemid())==0) {
	Header("Location:".$Dir."m/login.php?chUrl=".getUrl());
	exit;
}
$sql = "SELECT * FROM tblmember WHERE id='".$_ShopInfo->getMemid()."' ";
$result=pmysql_query($sql,get_db_conn());
if($row=pmysql_fetch_object($result)) {
	$_mdata=$row;
	if($row->member_out=="Y") {
		$_ShopInfo->SetMemNULL();
		$_ShopInfo->Save();
		alert_go('ȸ�� ���̵� �������� �ʽ��ϴ�.',$Dir."m/login.php");
	}

	if($row->authidkey!=$_ShopInfo->getAuthidkey()) {
		$_ShopInfo->SetMemNULL();
		$_ShopInfo->Save();
		alert_go('ó������ �ٽ� �����Ͻñ� �ٶ��ϴ�.',$Dir."m/login.php");
	}
}
pmysql_free_result($result);
*/
$ordercode=$_REQUEST["ordercode"];	//�α����� ȸ���� ��ȸ��
$ordername=$_REQUEST["ordername"]; //��ȸ�� ��ȸ�� �ֹ��ڸ�

//�ѱ��� ���� ��ȯ
$ordername = urldecode($ordername);
//$ordername = mb_convert_encoding($ordername,"euc-kr","utf-8");

$row_count = 0;
if (ord($ordercode) && ord($ordername)) {	//��ȸ�� �ֹ���ȸ
	$sql = "SELECT * FROM tblorderinfo WHERE ordercode = '{$ordercode}' ";
	$sql.= "AND sender_name='{$ordername}' ";
	$result=pmysql_query($sql,get_db_conn());
	if($row=pmysql_fetch_object($result)) {
		$row_count = pmysql_num_rows($result);
	} else {
		##### ��ȸ�� �ֹ��� ���� ��� #####
		$row_count = -1;
	}
	//$row_count = pmysql_num_rows($result);
	pmysql_free_result($result);
}else{
	$row_count = -2;
}


echo $row_count;

?>
