<?php
	include dirname(__FILE__)."/../../lib/library.php";
	include_once( dirname(__FILE__)."/../../conf/fieldset.php" );
	require_once( dirname(__FILE__)."/nice.nuguya.oivs.php" );

	//session_start();
	//========================================================================================
	//=====	�� Ű��Ʈ�� 80�ڸ� ���� ��
	//========================================================================================
	$athKeyStr = $ipin[athKeyStr];

	$oivsObject = new OivsObject();
	$oivsObject->athKeyStr = $athKeyStr;

	$strRecvData 	= $_POST[ "SendInfo" ];
	$blRcv 		= $oivsObject->resolveClientData( $strRecvData );
	// ��ŷ������ ���� ���ǿ� ����� ���� �� ..

	$ssOrderNo = $_SESSION["sess_OrderNo"];

	if( $ssOrderNo != $oivsObject->ordNo){
		echo ("���������� �������� �ʽ��ϴ�.");
		exit;
	}
	$sess_OrderNo = "";
	session_register("sess_OrderNo");

	$ssCallType = $_SESSION["sess_callType"];

	$year = date('Y');

	//debug($oivsObject);
	//debug($ssCallType);

	list($chkCount) = $db->fetch("select count(*) from ".GD_MEMBER." where dupeinfo='".$oivsObject->dupeInfo."'");

//!!!!!!!!!!!!!!!!!����!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
//��翡�� �����Ͽ� ��ȣȭ�� ������
//SSL�̳� ������ ���ȸ���� ����Ǿ� ���� ���� ȯ�濡��
//�������� �����ϴ� ���� ���ȿ� �ɰ��� ������ �߱��� �� �ֽ��ϴ�.

//���� ȯ���� ���߾����� ���� ��ü������, �ʿ��� ����
//���� ������� �ٸ� ������ �̿��Ͽ� ����� �ֽñ� �ٶ��ϴ�.
//�� ������ �ؼ����� �ʾƼ� �߻��ϴ� ���Ȼ�� ���ؼ���
//��翡�� å���� ���� �ʻ����, ���Ǹ� ��￩ �ֽñ� �ٶ��ϴ�.

?>

<html>
	<script type="text/javascript">

		function loadAction()
		{
			var strRetCd = "<? echo $oivsObject->retCd; ?>";		// '1' �̾�� �Ѵ�.
			var strRetDtlCd = "<? echo $oivsObject->retDtlCd; ?>";	// 'A" �̾�� �Ѵ�.
			var strMsg = "<? echo $oivsObject->message; ?>";

			var strName = "<? echo $oivsObject->niceNm; ?>";
			var birthday = "<? echo $oivsObject->birthday; ?>";
			var sex = "<? if ($oivsObject->sex == '1') echo 'M'; else 'W'; ?>";
			var dupeInfo = "<? echo $oivsObject->dupeInfo; ?>";
			var foreigner = "<? echo $oivsObject->foreigner; ?>";
			var paKey = "<? echo $oivsObject->paKey; ?>";

			var dupeCount = "<? echo $chkCount ?>";

			var minoryn = "<? echo $ipin[minoryn]; ?>";

			var year = "<? echo $year; ?>";

			var age = year-birthday.substring( 0, 4);
			// ȣ�������� ã�´�.
			var callType = "<? echo $ssCallType; ?>";

			//	�ѱ��ſ������� ���� ����ڵ忡 �ش��ϴ� �޽����� �޾ƿ´�.
			//	(�ٸ� �޽����� ������ �޴��� ������ �����Ͽ�  strRetCd, strRetDtlCd �� �޽����� ������ �ش�.
			//strProcessMessage = getMessage( strRetCd, strRetDtlCd );

			// ���̵� ã�⿡�� ȣ���� ���, opener.parent �� act ������Ʈ�� �ִ�.
			if (callType == "findid" || callType == "findpwd") {
				opener.parent.document.fm.action = '';
				opener.parent.document.fm.target = '';
				opener.parent.document.fm.rncheck.value = 'ipin';
				opener.parent.document.fm.dupeinfo.value = dupeInfo;
				opener.parent.document.fm.submit();
			}
			else {
				// default ȸ������
				if (dupeCount > 0) {
				alert( "�̹� ������ �Ǿ� �ֽ��ϴ�.");
				}
				else {
					if ( minoryn == 'y' && strRetCd == "1" && age < 20 ){ // �Ǹ��������� & ������������
						opener.parent.document.frmAgree['name']. value = '';
						alert( '�������� ����' ); //��� �޽��� ���
					}
					else if ( strRetCd == "1" && strRetDtlCd == "A") // ��������������
					{
						alert( "������������ ����ó�� �Ǿ����ϴ�." ); //��� �޽��� ���
						opener.parent.document.frmAgree.action = '';
						opener.parent.document.frmAgree.target = '';
						opener.parent.document.frmAgree.rncheck.value = 'ipin';
						opener.parent.document.frmAgree.nice_nm.value = strName;
						opener.parent.document.frmAgree.pakey.value = paKey;
						opener.parent.document.frmAgree.birthday.value = birthday;
						opener.parent.document.frmAgree.sex.value = sex;
						opener.parent.document.frmAgree.dupeinfo.value = dupeInfo;
						opener.parent.document.frmAgree.foreigner.value = foreigner;
						opener.parent.document.frmAgree.submit();
					}
					else // �Ǹ���������
					{
					//	����� ���� �Ǹ�Ƚ����ܰ� ���ǵ��� ������ ó���Ѵ�.
						opener.parent.document.frmAgree['name']. value = '';
						alert( '������������ �����߽��ϴ�. ' + strMsg); //��� �޽��� ���
					}
				}
			}
			self.close();
		}

	</script>

	<body onload="javascript:loadAction();"></body>
</html>
