<?php // hspark
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");

if(ord($_ShopInfo->getId())==0){
	echo "<script>alert('정상적인 경로로 접근하시기 바랍니다.');</script>";
	exit;
}

$to=$_POST["to"];
$from=$_POST["from"];
$rname=$_POST["rname"];
$subject=$_POST["subject"];
$body=stripslashes($_POST["body"]);
$upfile=$_FILES["upfile"];

if (ord($to) && ord($from) && ord($subject) && ord($body)) {
	sendMailForm($rname,$from,$body,$upfile,$bodytext,$mailheaders);
	$tolist=explode(",",$to);
	for($i=0;$i<count($tolist);$i++) {
		$tomail=trim($tolist[$i]);
		if(ismail($tomail)) {
            if( $subject ) {
                $subject = "=?utf-8?b?".base64_encode($subject)."?=";
            }
			mail($tomail, $subject, $bodytext, $mailheaders);
		}
	}
	echo "<html></head><body onload=\"alert('메일 발송이 완료되었습니다.');\"></body></html>";
	exit;
}
