<?php
$Dir="../../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
/********************************************************************************
*
* ������Ʈ : AGSMobile V1.0
* (�� �� ������Ʈ�� ������ �� �ȵ���̵忡�� �̿��Ͻ� �� ������ �Ϲ� �������������� ������ �Ұ��մϴ�.)
*
* ���ϸ� : AGS_pay_cancel.php
* ������������ : 2010/10/6
*
* �ô�����Ʈ ����â���� ���ϵ� �����͸� �޾Ƽ� ���ϰ�����û�� �մϴ�.
*
* Copyright AEGIS ENTERPRISE.Co.,Ltd. All rights reserved.
*
* �ʿ��Ͻ� ��� AGS_pay.html �������� �ҽ� �������� �ڿ� GET ������� �Ķ���͸� �ٿ��� �ѱ�ø� ó���� ���� �մϴ�.
* ����) http://www.allthegate.com/testmall/AGS_pay_cancel.php?param=content1&param=content2
*
*******************************************************************************/

echo "<script>alert('���� ���� ��� �ϼ˽��ϴ�.'); opener.parent.location.href='http://".$_SERVER[HTTP_HOST]."/m/'; window.close();</script>";
exit;
	
?>
<html>
<head>
</head>
<body>

���� ���� ��� �ϼ̽��ϴ�.
</body> 
</html>