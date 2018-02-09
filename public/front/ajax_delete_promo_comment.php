<?php
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");

$board  = $_GET['board'];
$num    = $_GET['num'];

BeginTrans();

$flagResult = "SUCCESS";

try {
    $sql = "DELETE FROM tblboardcomment_promo WHERE board = '{$board}' AND num = {$num} ";
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
