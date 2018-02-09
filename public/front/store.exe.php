<?php
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");

$search_w		= $_POST["search_w"];
$vendor_code    = $_POST["vendor_code"];
$area_code      = $_POST["area_code"];
$category_code  = $_POST["category_code"];
$sno            = $_POST["sno"];

$list_num	= (int)$_POST["list_num"];
$page_num	= (int)$_POST["page_num"] ?: 1;

$arrWhere = array();
array_push($arrWhere, "view = '1'");

if ( $search_w != '' ) {
    array_push($arrWhere, "lower(name) LIKE lower('%".$search_w."%')");
}
if ( !empty($vendor_code) ) {
    array_push($arrWhere, "vendor = {$vendor_code}");
}
if ( !empty($area_code) ) {
    array_push($arrWhere, "area_code = {$area_code}");
}
if ( !empty($category_code) ) {
    array_push($arrWhere, "category = '{$category_code}'");
}

$where	= "";
if ( count($arrWhere) >= 1 ) {
    $where = " WHERE " . implode(" AND ", $arrWhere);
}

$sql  = "SELECT tblResult.*, ";
$sql .= "(SELECT brandname FROM tblproductbrand WHERE vender = tblResult.vendor) as com_name ";

if ( empty($sno) ) {
    $sql .= "FROM (SELECT * FROM tblstore " . $where . " ORDER BY sort asc, sno desc ) AS tblResult ";
} else {
    $sql .= "FROM (SELECT * FROM tblstore WHERE sno = {$sno} ) AS tblResult ";
}
$sql .= "LIMIT {$list_num} OFFSET " . ( $page_num - 1 ) * $list_num;

//error_log($sql);

$result = pmysql_query($sql, get_db_conn());

$cnt = 0;
$JSON .= "[ ";
while($res=pmysql_fetch_array($result)) {
    $JSON .= "{";
    $JSON .= "\"number\": \"".$ii."\", " ;
    $JSON .= "\"storeName\": \"".$res['name']."\", " ;
    $JSON .= "\"storeAddress\": \"".$res['address']."\", " ;
    $JSON .= "\"storeTel\": \"".$res['phone']."\", " ;
    $JSON .= "\"storeXY\": \"".$res['coordinate']."\", " ;
    if($res['filename']){//매장이미지 존재하는지 체크해서 넘겨줌. 없으면 다른 임시 이미지
        $JSON .= "\"filename\": \"".$res['map_file_name']."\", " ;
    }else{
        $JSON .= "\"filename\": \"".'h1_logo.jpg'."\", " ;
    }

    $JSON .= "\"storeOfficeHour\": \"" . $res['stime'] . "~" . $res['etime'] . "\", " ;
    $JSON .= "\"storeCategory\": \"" . $store_category[$res['category']] . "\", " ;
    $JSON .= "\"storeVendorName\": \"" . $res['com_name'] . "\", " ;
    $JSON .= "\"storeAreaCode\": \"" . $store_area[$res['area_code']] . "\" " ;
    $JSON .= "}";

    $JSON .= ",";
    $cnt++;
}

$JSON = trim($JSON, ",");

$JSON .= "]\n";
pmysql_free_result($result);

// 결과가 없을때를 처리한다.
if($cnt == 0) {
    echo("noRecord");
    exit;
}

Header("Cache-Control:no-cache");
Header("Pragma: no-cache");
header('Content-Type: application/json; charset=utf-8');
echo($JSON);
exit;
?>
