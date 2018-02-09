<?php 
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");

// 전체 리뷰 카운트 reset
$sql  = "UPDATE tblproduct SET review_cnt = 0 ";
$result = pmysql_query($sql);

// 현재 등록되어 있는 리뷰를 기준으로 리뷰 카운트 조회
$sql  = "select productcode, count(*) as cnt from tblproductreview group by productcode ";
$result = pmysql_query($sql);

$arrResult = array();
while ($row = pmysql_fetch_object($result)) {
    $arrResult[$row->productcode] = $row->cnt;
}
pmysql_free_result($result);

// 일괄 리뷰 카운트 업데이트
foreach ($arrResult as $productcode => $review_cnt) {
    $sql = "UPDATE tblproduct SET review_cnt = {$review_cnt} WHERE productcode = '{$productcode}' ";
    $result = pmysql_query($sql);
}


?>

