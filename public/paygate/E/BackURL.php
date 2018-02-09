<html>
<head>
<title>다날 휴대폰 결제</title>
<meta http-equiv="X-UA-Compatible" content="IE=edge"/>
<meta http-equiv="Content-Type" content="text/html; charset=euc-kr" />
</head>
<body>
<?php
	/*
	 * 특정 URL 지정
	 */
	//$nextURL = "http://www.danal.co.kr";

	/*
	 * 창 닫기 Script
	 */
//	$nextURL = "Javascript:self.close();";
	$nextURL = "/front/basket.php";
?>
<!--form name="BackURL" action="<?=$nextURL?>" method="post" target="_parent">
</form-->
<script Language="Javascript">
    alert("결제가 취소되었습니다.");
    opener.parent.document.location.href = "/front/basket.php";
    window.close();
</script>
</body>
</html>
