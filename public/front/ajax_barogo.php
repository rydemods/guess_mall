<?
dvry_check();
/**
*  바로고 CURL 전송
* 2016-08-26 유동혁
*
*/
function sendDeliveryData( $arrData = '' ){
    $url = "http://api.barogo.co.kr/api_test.aspx"; // 테스트
    //$url = "http://openapi.barogo.co.kr/api.aspx"; // 실서버
    $ch  = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Accept: application/json','Content-Type: application/json'));
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $arrData);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $result = curl_exec($ch);
    curl_close($ch);
    return $result;
}
/**
* 배달 가능 조회 함수
*
* @author libe90
* @return json
* @since: 2016-05-20 libe90 >> 2016-08-26 유동혁
*/
function dvry_check( $opts ){
    $in_target_addr1 = $opts['addr1'];      // 주소 앞자리
    $in_target_addr2 = $opts['addr2'];      // 주소 뒷자리
    $in_target_gps_x = $opts['gpsX'];       // gps - x좌표
    $in_target_gps_y = $opts['gpsY'];       // gps - y좌표
    $shop_code       = $opts['shop_code'];  // 상점 바로고 코드
    $client_id       = $opts['card_id'];    // 회원 card_id
  
    $input_data = array(
        "header" => array(
            "VERSION"      => "1.0.0",
            "TRACE_NO"     => date('YmdHis').rand(100,999),
            "SERVICE_CODE" => "API_DVRY_CHECK_INFO",
            "COMP_CODE"    => "COMMERCE_LAB",
            "BRAND_CODE"   => "SYNC_COMMERCE",
        ),
        "body" => array(
            "in_SHOP_CODE"    => $shop_code,
            "in_TARGET_ADDR1" => urldecode( $in_target_addr1 ),
            "in_TARGET_ADDR2" => $in_target_addr2,
            "in_TARGET_GPS_X" => $in_target_gps_x,
            "in_TARGET_GPS_Y" => $in_target_gps_y,
        )
    );
    $res_data = sendDeliveryData( json_encode( $input_data ) ); // JSON_UNESCAPED_UNICODE >> php 5.4 이상일 경우 추가 2016-08-26 유동혁
    //$res_data = '{"header": {"VERSION": "1.4.1","TRACE_NO":"20160520152041566","RES_CODE": "0000","RES_MSG": "정상"},"body":{"IS_POSSIBLE":"Y","DVRY_CHARGE":"10000","DVRY_DISTANCE":"2.3"}}';
    return $res_data;
}


$optsArr['addr1'] = $_REQUEST['addr'];
$optsArr['addr2'] = $_REQUEST['addr2'];
$optsArr['gpsX'] = $_REQUEST['gpsX'];
$optsArr['gpsY'] = $_REQUEST['gpsY'];
$optsArr['shop_code'] = $_REQUEST['shop_code'];
$optsArr['card_id'] = $_REQUEST['card_id'];

echo dvry_check( $optsArr );
?>