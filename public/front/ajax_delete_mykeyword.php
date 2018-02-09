<?php
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");
include_once($Dir."lib/file.class.php");

BeginTrans();

$flagResult = "SUCCESS";

try {
    $sql    = "DELETE FROM tblmykeyword WHERE id = '" . $_ShopInfo->getMemid() . "' ";
    $result = pmysql_query($sql, get_db_conn());
    if ( empty($result) ) {
        throw new Exception('Update Fail');
    }
} catch (Exception $e) {
    $flagResult = "FAIL";
    RollbackTrans();
}
CommitTrans();

echo $flagResult;
?>
