<html>
<head>
<title>다날 휴대폰 결제</title>
<meta http-equiv="X-UA-Compatible" content="IE=edge"/>
<meta http-equiv="Content-Type" content="text/html; charset=euc-kr" />
<meta name="viewport" content="user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0, width=device-width, target-densitydpi=medium-dpi;" />
</head>
<body>
<?php
	/*
	 * 특정 URL 지정
	 */
	//$nextURL = "http://www.danal.co.kr";

	/*
	 * 창 닫기 Script
	 * - Javascript:self.close(); 사용시에는 다날 결제창을 팝업으로 띄워주시기 바랍니다.
	 */
	//$nextURL = "Javascript:self.close();";

	/*
	 * 웹뷰 닫기 Script
	 * - 테스트 앱 소스 참고
	 */
//	$nextURL = "Javascript:window.TeleditApp.BestClose();";
    $nextURL = "/m/basket.php"
?>
<form name="BackURL" action="<?=$nextURL?>" method="post">
</form>
<script Language="Javascript">
	document.BackURL.submit();
</script>
</body>
</html>
