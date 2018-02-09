<?php


$apikey = "hHbuUdjG9Z00UYgo";
$secret ="srfG207P0GpSdXdMmcQDmUPvovY1JP5V";

$HMAC_message = "POST/api/delivery/track{'deliver_id':3309}";
$hmac = hash_hmac("sha256", $HMAC_message, $secret);
//echo $hmac;
//exit; 


$ch = curl_init();

curl_setopt($ch, CURLOPT_URL, "http://api-test.meshprime.com/api/delivery/track");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
curl_setopt($ch, CURLOPT_HEADER, FALSE);
curl_setopt($ch, CURLOPT_POST, TRUE);
curl_setopt($ch, CURLOPT_POSTFIELDS, "{
 \"deliver_id\":3309,
}");
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
  "Content-Type:application/json; charset=utf-8",
  "hmac:hHbuUdjG9Z00UYgo:".$hmac					
));

$response = curl_exec($ch);
echo $response;
curl_close($ch);

//var_dump($response);
?>
