<?php

function get_content($url) {
	$agent = 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.0)';
	$curlsession = curl_init ();
	curl_setopt ($curlsession, CURLOPT_URL, $url);
	curl_setopt ($curlsession, CURLOPT_HEADER, 0);
	curl_setopt ($curlsession, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt ($curlsession, CURLOPT_POST, 0);
	curl_setopt ($curlsession, CURLOPT_USERAGENT, $agent);
	curl_setopt ($curlsession, CURLOPT_REFERER, "");
	curl_setopt ($curlsession, CURLOPT_TIMEOUT, 3);
	$buffer = curl_exec ($curlsession);
	$cinfo = curl_getinfo($curlsession);
	curl_close($curlsession);
	if ($cinfo['http_code'] != 200)
	{
		return "";
	}
	return $buffer;
}

$kcpInfo=get_content("https://admin8.kcp.co.kr/html/popup/thismonth/html/kcp_pop_up.html");
$kcpInfo=str_replace("../","https://admin8.kcp.co.kr/html/popup/thismonth/", $kcpInfo);
$kcpInfo=str_replace("</head>","
<style>
	.tit_warp {height:70px}
	.tit_warp h1 img {width:250px}
	.event_tit div li:last-child {height:24px}
	.event_tit div li:last-child a {display:none;}
	.event_warp{background:#fafafa;padding:28px 20px 10px 20px}
</style>
</head>", $kcpInfo);
echo $kcpInfo;
exit;
?>
