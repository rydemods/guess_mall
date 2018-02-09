<?php
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");
include_once($Dir."lib/file.class.php");

$keyword = $_GET['keyword'];

BeginTrans();

$flagResult = "SUCCESS";

try {
    $sql    = "INSERT INTO tblmykeyword (id, keyword, regdate) VALUES ('" . $_ShopInfo->getMemid() . "', '" . $keyword . "', now()) ";
    $result = pmysql_query($sql, get_db_conn());
    if ( empty($result) ) {
        throw new Exception('Insert Fail');
    }
} catch (Exception $e) {
    $flagResult = "FAIL";
    RollbackTrans();
}
CommitTrans();

echo $flagResult;
?>
