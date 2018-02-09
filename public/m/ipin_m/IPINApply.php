<?
	include dirname(__FILE__)."/../../lib/library.php";
	include_once( dirname(__FILE__)."/../../conf/config.php" );
	include_once( dirname(__FILE__)."/../../conf/fieldset.php" );
	require_once( dirname(__FILE__)."/nice.nuguya.oivs.php" );

	if ($ipin['nice_useyn'] == 'y') {
		$sSiteCode	= $ipin['id'];		// IPIN 서비스 사이트 코드		(NICE신용평가정보에서 발급한 사이트코드)
		$sSitePw	= $ipin['SIKey'];	// IPIN 서비스 사이트 패스워드	(NICE신용평가정보에서 발급한 사이트패스워드)
		$sEncData	= "";					// 암호화 된 사용자 인증 정보
		$sDecData	= "";					// 복호화 된 사용자 인증 정보
		$sRtnMsg	= "";					// 처리결과 메세지
		$sType		= "RES";				// RES : 복호화

		$self_filename = basename($_SERVER['PHP_SELF']);
		$loc = strpos($_SERVER['PHP_SELF'], $self_filename);
		$loc = substr($_SERVER['PHP_SELF'], 0, $loc);
		$sModulePath = $_SERVER['DOCUMENT_ROOT'].$loc."IPINClient";

		$sCPRequest = $_SESSION['CPREQUEST'];
		$sEncData = $_POST['enc_data'];

		if ($sEncData != "") {
			$sDecData = exec("$sModulePath $sType $sSiteCode $sSitePw $sEncData");

			if ($sDecData == -9) $sRtnMsg = "입력값 오류 : 복호화 처리시, 필요한 파라미터값의 정보를 정확하게 입력해 주시기 바랍니다.";
			else if ($sDecData == -12) $sRtnMsg = "NICE신용평가정보에서 발급한 개발정보가 정확한지 확인해 보세요.";
			else {
				$arrData = split("\^", $sDecData);
				$iCount = count($arrData);

				if ($iCount >= 5) {
					$strResultCode = $arrData[0];		// 결과코드

					if ($strResultCode == 1) {
						$strCPRequest = $arrData[8];	// CP 요청번호

						if ($sCPRequest == $strCPRequest) {
							list($chkCount) = $db->fetch("select count(*) from ".GD_MEMBER." where dupeinfo='".$arrData[3]."'");
							if(!$chkCount) {
								$sRtnMsg = "사용자 인증 성공";
								$strDupInfo = $arrData[3];
							}
							else $sRtnMsg = "이미 가입됐습니다.";
						}
						else $sRtnMsg = "CP 요청번호 불일치 : 세션에 넣은 $sCPRequest 데이타를 확인해 주시기 바랍니다.";
					}
					else $sRtnMsg = "리턴값 확인 후, NICE신용평가정보 개발 담당자에게 문의해 주세요. [$strResultCode]";
				}
				else $sRtnMsg = "리턴값 확인 후, NICE신용평가정보 개발 담당자에게 문의해 주세요.";
			}
		}
		else $sRtnMsg = "처리할 암호화 데이타가 없습니다.";
	}
	else if ($ipin['useyn'] == 'y') {
		$athKeyStr = $ipin['athKeyStr'];

		$oivsObject = new OivsObject();
		$oivsObject->athKeyStr = $athKeyStr;

		$strRecvData 	= $_POST[ "SendInfo" ];
		$blRcv 		= $oivsObject->resolveClientData( $strRecvData );
		// 해킹방지를 위해 세션에 저장된 값과 비교 ..

		$ssOrderNo = $_SESSION["sess_OrderNo"];

		if( $ssOrderNo != $oivsObject->ordNo) $sRtnMsg = "세션정보가 존재하지 않습니다.";

		list($chkCount) = $db->fetch("select count(*) from ".GD_MEMBER." where dupeinfo='".$oivsObject->dupeInfo."'");
		if(!$chkCount) {
			$sRtnMsg = "사용자 인증 성공";
			$strDupInfo = $oivsObject->dupeInfo;
		}
		else $sRtnMsg = "이미 가입됐습니다.";
	}
?>
<script language="JavaScript">
	alert("<?=$sRtnMsg?>");
	<? if($strDupInfo) { ?>
	opener.parent.document.frmMember.dupeinfo.value = "<?=$strDupInfo?>";
	<? } ?>
	opener.parent.document.getElementById('ipinManual').style.display = 'none';
	self.close();
</script>