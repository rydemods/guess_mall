<?php
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");

// 현재 배송중이거나 배송완료인데 리뷰작성이 안된 리스트
$sql  = "SELECT tblResult.ordercode, tblResult.productcode, tblResult.idx, tblResult.productname ";
$sql .= "FROM ";
$sql .= "   ( ";
$sql .= "       SELECT a.*, b.regdt ";
$sql .= "       FROM tblorderproduct a LEFT JOIN tblorderinfo b ON a.ordercode = b.ordercode ";
$sql .= "       WHERE b.id = '" . $_ShopInfo->getMemid() . "' and ( (b.oi_step1 = 3 AND b.oi_step2 = 0) OR (b.oi_step1 = 4 AND b.oi_step2 = 0) ) ";
$sql .= "       ORDER BY a.idx DESC ";
$sql .= "   ) AS tblResult LEFT ";
$sql .= "OUTER JOIN tblproductreview tpr ON tblResult.productcode = tpr.productcode and tblResult.ordercode = tpr.ordercode and tblResult.idx = tpr.productorder_idx ";
$sql .= "WHERE tpr.productcode is null ";
$sql .= "ORDER BY tblResult.idx desc ";

$result = pmysql_query($sql);
$arrResult = array();
while ($row = pmysql_fetch_object($result)) {
    $arrOrderInfo = array($row->ordercode, $row->productcode, $row->idx, $row->productname);
    array_push($arrResult, implode("^^", $arrOrderInfo));
}
pmysql_free_result($result);

$htmlResult  = implode("||", $arrResult);

echo $htmlResult;
?>
