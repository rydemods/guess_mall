<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="ko" xml:lang="ko">

<head>

	<meta http-equiv="Content-Type" content="text/html; charset=euc-kr">
	
	<title>�귿</title>

	<script type="text/javascript" src="js/embedFlash.js"></script>
	<script type="text/javascript">
		
		function goLogin(){
			alert("�α��� �ϼ���");
			location.href = "../login.php"; 			/*?chUrl= ������*/
		}
		
		function noChange(){
			alert("������ȸ�� �����ϴ�");
		}
		
		function goClose(){
			//alert("�˾� �ݱ�, ������ ���ΰ�ħ");			/* �������� �������ΰ�?*/
		}
	
		function resultError(_value){
			// setdata �� resultNum ���� 1 ~ 7 �� �ƴϸ� ȣ��
			alert("������ �߻��߽��ϴ�. ��� �� �ٽ� ������ �ּ���. errorCode : " + _value);
		}
	
	</script>

</head>

<body>

	<div>
		<script type="text/javascript">
			// �Ʒ� getUrl �� setUrl �κп� php ��θ� ��� �Ǵ� ����� �־���
			// ������ �ε� �� getdata�� �ҷ��� �÷��ø� �����ϰ�, START ��ư�� ������ setdata�� �ҷ��� �귿�� ��� ���� ������
			// �����ڿ��� �ҷ����� �̹��� ��δ� getdata �� ��ϵǰ�, �̹��� ������� psd �� ���̵� ���� ǥ��
			embed_flash("roulette.swf", "rouletteFlash", "500", "500", "getUrl=getdata.php&setUrl=setdata.php", "transparent");
		</script>
	</div>

</body>

</html>