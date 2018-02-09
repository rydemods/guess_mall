<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once("lib.inc.php");
include_once("shopdata.inc.php");

$referer = $_SERVER["HTTP_REFERER"];
//debug($referer);

if(strlen($_MShopInfo->getMemid())==0/* || strlen($referer)==0*/) {
	if ($_GET['memoutinfo'] !='') {
		header("Location:/m/mypage_memberout.php?memoutinfo=".$_GET['memoutinfo']);
	} else {
		header("Location:/m/index.php");
	}
	exit;
}

$id = $_MShopInfo->getMemid();

$sql = "UPDATE tblmember SET authidkey='logout' WHERE id='".$id."' ";
pmysql_query($sql,get_mdb_conn());

$_MShopInfo->SetMemNULL();
$_MShopInfo->Save();

$md5_id = md5($id);
setcookie($md5_id."_key", "", 0, "/".RootPath."m/", getCookieDomain());
setcookie("auto_login", "", 0, "/".RootPath."m/", getCookieDomain());

if ($_COOKIE["save_id"]!="Y") {
	setcookie("smart_id", "", 0, "/".RootPath."m/", getCookieDomain());
}

if ($_GET['memoutinfo'] !='') {
	header("Location:/m/mypage_memberout.php?memoutinfo=".$_GET['memoutinfo']);
} else {
	header("Location:/m/index.php");
}
?>