<html>
<head>
<title>�ٳ� �޴��� ����</title>
<meta http-equiv="X-UA-Compatible" content="IE=edge"/>
<meta http-equiv="Content-Type" content="text/html; charset=euc-kr" />
<meta name="viewport" content="user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0, width=device-width, target-densitydpi=medium-dpi;" />
</head>
<body>
<?php
	/*
	 * Ư�� URL ����
	 */
	//$nextURL = "http://www.danal.co.kr";

	/*
	 * â �ݱ� Script
	 * - Javascript:self.close(); ���ÿ��� �ٳ� ����â�� �˾����� ����ֽñ� �ٶ��ϴ�.
	 */
	//$nextURL = "Javascript:self.close();";

	/*
	 * ���� �ݱ� Script
	 * - �׽�Ʈ �� �ҽ� ����
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
