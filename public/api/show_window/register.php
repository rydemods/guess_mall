<?php
include_once($_SERVER[DOCUMENT_ROOT]."/api/config.php");

$resultTotArr = array();
$resultArr = array();

$code = 0;
$message = "success";

$num            = $_POST["num"];
$prodcode          = $_POST["style"];  // productcode >> style 변경 2016-08-29 유동혁
$img_url        = $_POST["img_url"];
$img_url2       = $_POST["img_url2"]; // 이미지 추가 2016-08-29 유동혁
$img_url3       = $_POST["img_url3"]; // 이미지 추가 2016-08-29 유동혁
$description    = $_POST["description"];
$company_code   = $_POST["company_code"];
$business_part  = $_POST["business_part"];
$store_code     = $_POST["store_code"];
$best_shop_yn     = $_POST["best_shop_yn"];
//$icon_url       = $_POST["icon_url"];
$hash_tag       = $_POST["hash_tag"]; // 해쉬태그 추가 2016-08-30 유동혁


/* 매장 아이콘은 필수가 아님
else if ( empty($icon_url) ) {
    $code       = 1;
    $message    = "매장 아이콘 이미지가 없습니다.";
}

*/
if ( empty($num) ) {
    $code       = 1;
    $message    = "쇼윈도우 번호가 없습니다.";
} else if ( empty($prodcode) ) {
    $code       = 1;
    $message    = "매칭된 상품 스타일이 없습니다.";
}  else if ( empty($img_url) ) {
    $code       = 1;
    $message    = "촬영한 이미지가 없습니다.";
} else if ( empty($description) ) {
    $code       = 1;
    $message    = "소개글이 없습니다.";
//} else if ( empty($company_code) ) {
//    $code       = 1;
//    $message    = "회사코드가 없습니다.";
//} else if ( empty($business_part) ) {
//    $code       = 1;
//    $message    = "사업부코드가 없습니다.";
} else if ( empty($store_code) ) {
    $code       = 1;
    $message    = "매장코드가 없습니다.";
} else {
    $sql  = "SELECT brandcd FROM tblproduct WHERE prodcode='".$prodcode."' ";
    list($brandcd) = pmysql_fetch(pmysql_query($sql));

    if ( empty($brandcd) ) {
        $code       = 1;
        $message    = "해당하는 상품이 없습니다.";
    } else {
        $brand_code = $brandcd;
        $icon_url = "https://dev-shinwon.synccommerce.co.kr/uploads/icon/shops/".$store_code."_logo.jpg";
		if( !$best_shop_yn ) $best_shop_yn = "N";
        $sql  = "INSERT INTO tblshowwindowproduct ";
        $sql .= "( num, productcode, icon_url, img_url, description, company_code, business_part, store_code, reg_date, mod_date, brand_code, ";
        $sql .= " img_url2, img_url3, hash_tag, best_shop_yn ) VALUES ( ";
        $sql .= " {$num}, '{$prodcode}', '{$icon_url}', '{$img_url}', '{$description}', '{$company_code}', '{$business_part}', '{$store_code}', ";
        $sql .= "now(), now(), '{$brand_code}', '{$img_url2}', '{$img_url3}', '{$hash_tag}', '{$best_shop_yn}' )";

        $result = pmysql_query($sql, get_db_conn());
        if($err=pmysql_error()) {
            if ( strpos($err,'duplicate key') !== FALSE ) {
                $code       = 1;
                $message    = "쇼윈도우 번호가 중복입니다.";
            }
        }
    }
}

$resultTotArr["result"]    = $resultArr;
$resultTotArr["code"]      = $code;
$resultTotArr["message"]   = $message;

echo json_encode($resultTotArr);
?>
