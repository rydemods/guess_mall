<?php
	session_start();
	
	$Dir="../../";
	include_once($Dir."lib/init.php");
	include_once($Dir."lib/lib.php");
	include_once($Dir."lib/shopdata2.php");

	$returnMsg = "휴대폰 본인인증이 정상적으로 완료 되었습니다.";
	$returnCode  = "1";	

	$_SESSION[ipin][name] = iconv("UTF-8", "EUC-KR", "김재수");
	$_SESSION[ipin][dupinfo] = "MC0GCCqGSIb3DQIJAyEAl3N6abnFNYgfZN2k7reLfXY2q83Q4xsGiluFJxZl7G0=";
?>
<script>
alert('<?=$returnMsg?>');
opener.parent.ipin_chk('mobile');
window.close();
</script>