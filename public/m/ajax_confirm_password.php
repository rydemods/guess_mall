<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");
include_once("lib.inc.php");
include_once("shopdata.inc.php");

if(strlen($_ShopInfo->getMemid())==0) {
	Header("Location:login.php?chUrl=".getUrl());
	exit;
}

if(!$_POST['mode']) {
	Header("Location:login.php?chUrl=".getUrl());
	exit;
}

$id = $_ShopInfo->getMemid();
$confirm_pw = $_POST['password'];

$sql = "SELECT * FROM tblmember WHERE id='".$_ShopInfo->getMemid()."' ";
$result=pmysql_query($sql,get_db_conn());
if($row=pmysql_fetch_object($result)) {
	$passwd=$row->passwd;
	if($passwd == md5($confirm_pw)){
		echo count($result);
	}else{
		echo "-1";
	}
}else{
	echo "-2";	//회원정보가 없을때
}

?>