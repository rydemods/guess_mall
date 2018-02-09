<?php
include_once($_SERVER[DOCUMENT_ROOT]."/api/config.php");

$resultTotArr = array();
$resultArr = array();

$code = 0;
$message = "success";

$method         = $_POST["method"];
$num            = $_POST["num"];
$style          = $_POST["style"]; // productcode >> style 변경 2016-08-29 유동혁
$img_url        = $_POST["img_url"];
$img_url2       = $_POST["img_url2"]; // 이미지 추가 2016-08-29 유동혁
$img_url3       = $_POST["img_url3"]; // 이미지 추가 2016-08-29 유동혁
$description    = $_POST["description"];
$company_code   = $_POST["company_code"];
$business_part  = $_POST["business_part"];
$store_code     = $_POST["store_code"];
$best_shop_yn     = $_POST["best_shop_yn"];
$icon_url       = $_POST["icon_url"];
$hash_tag       = $_POST["hash_tag"]; // hash_tag 추가

if ( empty($num) ) {
    $code       = 1;
    $message    = "쇼윈도우 번호가 없습니다.";
} else if ( empty($method) ) {
    $code       = 1;
    $message    = "method값이 없습니다.";
} else if ( $method == "DEL" ) {
    $sql  = "SELECT COUNT(*) FROM tblshowwindowproduct WHERE num = {$num} ";
    list($cnt) = pmysql_fetch(pmysql_query($sql));

    if ( $cnt == 0 ) {
        $code       = 1;
        $message    = "해당하는 상품이 없습니다.";
    } else {

        $sql  = "DELETE FROM tblshowwindowproduct WHERE num = {$num} ";
        $result = pmysql_query($sql, get_db_conn());
        if($err=pmysql_error()) {
            $code       = 1;
            $message    = "업데이트 작업이 실패했습니다.";
        }
    } 
} else if ( $method == "MOD" ) {

    $arrUpdateField = array();
    if ( !empty($style) ) {
        array_push($arrUpdateField, "productcode = '{$style}'");
    } 
    if ( !empty($img_url) ) {
        array_push($arrUpdateField, "img_url = '{$img_url}'");
    } 
    if ( !empty($description) ) {
        array_push($arrUpdateField, "description = '{$description}'");
    } 
    if ( !empty($store_code) ) {
        array_push($arrUpdateField, "store_code = '{$store_code}'");
    } 
    if ( !empty($company_code) ) {
        array_push($arrUpdateField, "company_code = '{$company_code}'");
    } 
    if ( !empty($business_part) ) {
        array_push($arrUpdateField, "business_part = '{$business_part}'");
    } 
    if ( !empty($icon_url) ) {
        array_push($arrUpdateField, "icon_url = '{$icon_url}'");
    } 
    // 이미지 2, 3 추가 2016-08-29 유동혁
    if ( !empty($img_url2) ) {
        array_push($arrUpdateField, "img_url2 = '{$img_url2}'");
    }
    if ( !empty($img_url3) ) {
        array_push($arrUpdateField, "img_url3 = '{$img_url3}'");
    }
    // hash_tag 추가
    if ( !empty($hash_tag) ) {
        array_push($arrUpdateField, "hash_tag = '{$hash_tag}'");
    }
    if ( !empty($best_shop_yn) ) {
        array_push($arrUpdateField, "best_shop_yn = '{$best_shop_yn}'");
    } 

    if ( count($arrUpdateField) == 0 ) {
        $code       = 1;
        $message    = "업데이트 할 내용이 없습니다.";
    } else {
        $sql  = "UPDATE tblshowwindowproduct ";
        $sql .= "SET " . implode(",", $arrUpdateField) . " ";
        $sql .= ", mod_date = now() ";
        $sql .= "WHERE num = {$num} ";

        $result = pmysql_query($sql, get_db_conn());
        if($err=pmysql_error()) {
            $code       = 1;
            $message    = "업데이트 작업이 실패했습니다.";
        }
    }
} else {
    $code       = 1;
    $message    = "method값이 유효하지 않습니다.";
}

$resultTotArr["result"]    = $resultArr;
$resultTotArr["code"]      = $code;
$resultTotArr["message"]   = $message;

echo json_encode($resultTotArr);
?>
