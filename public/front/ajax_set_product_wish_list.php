<?php
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");

$member_id  = $_ShopInfo->getMemid();   // 로그인한 아이디
$prodcode   = $_GET['prodcode'];        // 상품코드 
$mode       = $_GET['mode'];            // 위시리스트 등록여부(1:등록, 0:해제)

if ( $mode == "1" ) {
    // 위시리스트에 추가
    $sql    = "INSERT INTO tblwishlist ( id, productcode, date ) VALUES ( '{$member_id}', '{$prodcode}', '" . date("YmdHis") . "' )";
} else {
    // 위시리스트에서 삭제
    $sql    = "DELETE FROM tblwishlist WHERE id = '{$member_id}' AND productcode = '{$prodcode}' ";
}
$result = pmysql_query($sql,get_db_conn());

if ( $result ) {
    echo "SUCCESS";
} else {
    echo "FAIL";
}
?>
