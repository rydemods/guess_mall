<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");

//http://test-hott.ajashop.co.kr/partner/call_syndi.php?menu=lookbook&bbsno=11&type=reg

$menu = $_GET["menu"];      // forum or lookbook or magazine
$bbsno = $_GET["bbsno"];    // 해당 컨텐츠 일련번호
$type = $_GET["type"];      // reg or del

callNaver2($menu, $bbsno, $type);


function callNaver2($menu, $bbsno, $type) {
    //$auth_key = "AAAANl1jRvlJB5s+PK9t3jsVk6mrnFjqf2N5yO5rNXYk8abP0T2VYCUNXkKmlzAeLqWlTkjnn8Cgs6w41ndUV0nLP1o=";
    $auth_key = "AAAARoy72YAuPC/7nyZwYtBqd9SuQbOYRwFUsKrnNkhQTejrDHiYhpX+sErz/uM5B4DeFcDq5QicMSxLEKV7XXxXMPLC4n/qp9dH0OYU3LR1iR60";
    $url = "http://test-hott.ajashop.co.kr/partner/call_make_syndi.php?menu=".$menu."&bbsno=".$bbsno."&type=".$type;
    $ping_auth_header = "Authorization: Bearer $auth_key"; // Bearer 타입의 인증키 정보 
    $ping_url = urlencode($url); // 신디케이션 문서를 담고 있는 핑 URL 
    $ping_client_opt = array(
    CURLOPT_URL => "https://apis.naver.com/crawl/nsyndi/v2", // 네이버 신디케이션 서버 호출주소 
    CURLOPT_POST => true, /* POST 방식 */
    CURLOPT_POSTFIELDS => "ping_url=" . $ping_url, // 파라미터로 핑 URL 전달 
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_CONNECTTIMEOUT => 10,
    CURLOPT_TIMEOUT => 10,
    CURLOPT_HTTPHEADER =>
         array("Host: apis.naver.com", "Pragma: no-cache", "Accept: */*", $ping_auth_header) // 헤더에 인증키 정보 추가 
    );
    $ping = curl_init();
    curl_setopt_array($ping, $ping_client_opt);
    $r = curl_exec($ping);
    print_r($r);
    curl_close($ping);
}

?>