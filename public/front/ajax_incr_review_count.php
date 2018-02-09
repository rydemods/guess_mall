<?php

// ===========================================================================
// FileName     : ajax_incr_review_count.php
// Desc         : 리뷰 뷰 카운트를 1 증가시킨다.
// By           : 최문성
// Last Updated : 2016.02.25
// ===========================================================================

$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");
include_once($Dir."lib/file.class.php");

$review_num         = $_GET['review_num'];

BeginTrans();

$flagResult = "SUCCESS";

try {

    $sql  = "UPDATE tblproductreview SET ";
    $sql .= "hit = hit + 1 ";
    $sql .= "WHERE num = {$review_num} ";

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
