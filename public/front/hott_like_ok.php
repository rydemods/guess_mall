<?php 
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");

$hott_code = $_POST[hott_code];
$section = $_POST[section];

$like_id = $_ShopInfo->getMemid();
$regdt = date("YmdHis");

$sql = "Select count(*) as cnt from tblhott_like Where like_id = '".$like_id."' and section = '".$section."' and hott_code = '".$hott_code."' ";
list($cnt) = pmysql_fetch($sql, get_db_conn());

if($cnt > 0) {
    $msg = "이미 좋아요 하셨습니다.";
    msg($msg);
    exit;
} else {

    $sql = "insert into tblhott_like 
            (like_id, section, hott_code, regdt) 
            Values 
            ('".$like_id."', '".$section."', '".$hott_code."', '".$regdt."')
            ";
    pmysql_query($sql, get_db_conn());

    $msg = "좋아요 등록되었습니다.";
    msg($msg);
    exit;
}
?>