<?

$query=urlencode($_POST['query']);

$url = "https://apis.daum.net/local/geo/addr2coord?apikey=395b18847fa4e74e9250ab429fa7d122&q=".$query."&output=json";

$ch = curl_init();

curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_HTTPHEADER);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_GET, $is_post);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

$response = curl_exec ($ch);

curl_close ($ch);

echo $response;
?>