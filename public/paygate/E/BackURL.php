<html>
<head>
<title>�ٳ� �޴��� ����</title>
<meta http-equiv="X-UA-Compatible" content="IE=edge"/>
<meta http-equiv="Content-Type" content="text/html; charset=euc-kr" />
</head>
<body>
<?php
	/*
	 * Ư�� URL ����
	 */
	//$nextURL = "http://www.danal.co.kr";

	/*
	 * â �ݱ� Script
	 */
//	$nextURL = "Javascript:self.close();";
	$nextURL = "/front/basket.php";
?>
<!--form name="BackURL" action="<?=$nextURL?>" method="post" target="_parent">
</form-->
<script Language="Javascript">
    alert("������ ��ҵǾ����ϴ�.");
    opener.parent.document.location.href = "/front/basket.php";
    window.close();
</script>
</body>
</html>
