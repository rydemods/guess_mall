<?php
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");

$member_id  = $_ShopInfo->getMemid();   // 로그인한 아이디
$bridx      = $_GET['bridx'];           // 브랜드 id
$mode       = $_GET['mode'];            // 위시리스트 등록여부(1:등록, 0:해제)

if ( $mode == "1" ) {
    // 위시리스트에 추가
    $sql    = "INSERT INTO tblbrandwishlist ( id, bridx, date ) VALUES ( '{$member_id}', {$bridx}, '" . date("YmdHis") . "' )";
} else {
    // 위시리스트에서 삭제
    $sql    = "DELETE FROM tblbrandwishlist WHERE id = '{$member_id}' AND bridx = {$bridx} ";
}
$result = pmysql_query($sql,get_db_conn());

if ( $result ) {
    echo "SUCCESS";
} else {
    echo "FAIL";
}
?>
