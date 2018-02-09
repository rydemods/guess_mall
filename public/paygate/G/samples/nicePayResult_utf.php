<?php

	/**************************
	 * 1. 라이브러리 인클루드 *
	 **************************/
	require("../lib/NicepayLite.php");
	
	/***************************************
	 * 2. NicepayLite 클래스의 인스턴스 생성 *
	 ***************************************/
	$nicepay = new NicepayLite;

	//로그를 저장할 디렉토리를 설정하십시요. 
	$nicepay->m_NicepayHome = "./log";	

	/**************************************
	* 3. 결제 요청 파라미터 설정	      *
	***************************************/
	$nicepay->m_GoodsName = $GoodsName;
	$nicepay->m_GoodsCnt = $m_GoodsCnt;
	$nicepay->m_Price = $Amt;
	$nicepay->m_Moid = $Moid;
	$nicepay->m_BuyerName = $BuyerName;
	$nicepay->m_BuyerEmail = $BuyerEmail;
	$nicepay->m_BuyerTel = $BuyerTel;
	$nicepay->m_MallUserID = $MallUserID;
	$nicepay->m_GoodsCl = $GoodsCl; 
	$nicepay->m_MID = $MID;
	$nicepay->m_MallIP = $MallIP;
	$nicepay->m_TrKey = $TrKey;
	$nicepay->m_EncryptedData = $EncryptedData;
	$nicepay->m_PayMethod = $PayMethod;
	$nicepay->m_TransType = $TransType;
	$nicepay->m_ActionType = "PYO";

	// 상점키를 설정하여 주십시요.
	$nicepay->m_LicenseKey = "33F49GnCMS1mFYlGXisbUDzVf2ATWCl9k3R++d5hDd3Frmuos/XLx8XhXpe+LDYAbpGKZYSwtlyyLOtS/8aD7A==";
    
	// UTF-8일 경우 아래와 같이 설정하십시요.
	$nicepay->m_charSet = "UTF8";

	$nicepay->m_NetCancelAmt = $Amt; //결제 금액에 맞게 수정 
	$nicepay->m_NetCancelPW = "123456";	// 결제 취소 패스워드 설정
		
	$nicepay->m_ssl = "true";

	// PG에 접속하여 승인 처리를 진행.
	$nicepay->startAction();
	
	/**************************************
	* 4. 결제 결과					      *
	***************************************/	
	$resultCode = $nicepay->m_ResultData["ResultCode"];	// 결과 코드


	$paySuccess = false;		// 결제 성공 여부
	if($PayMethod == "CARD"){	//신용카드
		if($resultCode == "3001") $paySuccess = true;	// 결과코드 (정상 :3001 , 그 외 에러)
	}else if($PayMethod == "BANK"){		//계좌이체
		if($resultCode == "4000") $paySuccess = true;	// 결과코드 (정상 :4000 , 그 외 에러)
	}else if($PayMethod == "CELLPHONE"){			//휴대폰
		if($resultCode == "A000") $paySuccess = true;	//결과코드 (정상 : A000, 그 외 비정상)
	}else if($PayMethod == "VBANK"){		//가상계좌
		if($resultCode == "4100") $paySuccess = true;	// 결과코드 (정상 :4100 , 그 외 에러)
	}

	if($paySuccess == true){
	   // 결제 성공시 DB처리 하세요.
	}else{
	   // 결제 실패시 DB처리 하세요.
	}
	
?>	
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>NICEPAY :: 결제 요청 결과</title>
<link rel="stylesheet" href="css/basic.css" type="text/css" />
<link rel="stylesheet" href="css/style.css" type="text/css" />
</head>
<body>
<br>
<table width="632" border="0" cellspacing="0" cellpadding="0" align="center">
  <tr>
  	<td >
  	  <table width="632" border="0" cellspacing="0" cellpadding="0" class="title">
        <tr>
          <td width="35">&nbsp;</td>
          <td>결제 요청 결과</td>
          <td>&nbsp;</td>
        </tr>
      </table>
    </td>
  </tr>
  <tr>
    <td align="left" valign="top" background="images/bodyMiddle.gif"><table width="632" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td width="35" height="10">&nbsp;</td> <!--상단여백 높이 10px -->
        <td width="562">&nbsp;</td>
        <td width="35">&nbsp;</td>
      </tr>
      <tr>
        <td height="30">&nbsp;</td>
        <td>결제 요청이 완료되었습니다.
        </td> 
        <td>&nbsp;</td>
      </tr>
      <tr>
        <td height="15">&nbsp;</td> <!--컨텐츠와 컨텐츠 사이 간격 15px-->
        <td>&nbsp;</td>
        <td>&nbsp;</td>
      </tr>
      <tr>
        <td height="30">&nbsp;</td> 
        <td class="bold"><img src="images/bullet.gif" /> 결제 내역입니다.
        <td>&nbsp;</td>
      </tr>
      <tr>
        <td >&nbsp;</td>
        <td ><table width="562" border="0" cellspacing="0" cellpadding="0" class="talbeBorder" >
          <tr>
            <!-- 테이블 일반의 높이는 30px // 홀수행셀의 경우 class="thead01" 사용 -->
            <td width="100" height="30" id="borderBottom" class="thead01">결과 내용</td> 
            <td id="borderBottom" >&nbsp;[<?=$nicepay->m_ResultData["ResultCode"]?>] <?=$nicepay->m_ResultData["ResultMsg"]?></td>
          </tr>
          <tr>
            <!-- 테이블 일반의 높이는 30px // 홀수행셀의 경우 class="thead02" 사용 -->
            <td width="100" height="30" id="borderBottom" class="thead02">결제 수단</td> 
            <td id="borderBottom" >&nbsp;<?=$PayMethod ?></td>
          </tr>
          <tr>
            <td width="100" height="30" id="borderBottom" class="thead01">상품명</td> 
            <td id="borderBottom" >&nbsp;<?=$nicepay->m_ResultData["GoodsName"]?></td>
          </tr>
          <tr>
            <td width="100" height="30" id="borderBottom" class="thead02">금액</td> 
            <td id="borderBottom" >&nbsp;<?=$nicepay->m_ResultData["Amt"]?> 원</td>
          </tr>
          <tr>
            <td width="100" height="30" id="borderBottom" class="thead01">거래아이디</td> 
            <td id="borderBottom" >&nbsp;<?=$nicepay->m_ResultData["TID"]?></td>
          </tr>
			 <?if($PayMethod == "CARD"){?>
			  <tr>
				<td width="100" height="30" id="borderBottom" class="thead02">카드사코드</td> 
				<td id="borderBottom" >&nbsp;<?=$nicepay->m_ResultData["CardCode"]?>&nbsp;</td>
			  </tr>
			  <tr>
				<td width="100" height="30" id="borderBottom" class="thead02">카드사명</td> 
				<td id="borderBottom" >&nbsp;<?=$nicepay->m_ResultData["CardName"]?>&nbsp;</td>
			  </tr>
			   <tr>
				<td width="100" height="30" id="borderBottom" class="thead01">할부개월</td> 
				<td id="borderBottom" >&nbsp;<?=$nicepay->m_ResultData["CardQuota"]?>&nbsp;</td>
			  </tr>
		   <?}else if($PayMethod == "BANK"){?>
		      <tr>
				<td width="100" height="30" id="borderBottom" class="thead02">은행 코드</td> 
				<td id="borderBottom" >&nbsp;<?=$nicepay->m_ResultData["BankCode"]?>&nbsp;</td>
			  </tr>
			  <tr>
				<td width="100" height="30" id="borderBottom" class="thead02">은행명</td> 
				<td id="borderBottom" >&nbsp;<?=$nicepay->m_ResultData["BankName"]?>&nbsp;</td>
			  </tr>
			   <tr>
				<td width="100" height="30" id="borderBottom" class="thead01">현금영수증 타입</td> 
				<td id="borderBottom" >&nbsp;<?=$nicepay->m_ResultData["RcptType"]?>&nbsp;&nbsp;(0:발행안함,1:소득공제,2:지출증빙)</td>
			  </tr>
			   <tr>
				<td width="100" height="30" id="borderBottom" class="thead02">현금영수증 승인번호</td> 
				<td id="borderBottom" >&nbsp;<?=$nicepay->m_ResultData["RcptAuthCode"]?>&nbsp;</td>
			  </tr>
		  <?}else if($PayMethod== "CELLPHONE"){?>
			  <tr>
				<td width="100" height="30" id="borderBottom" class="thead02">이통사 구분</td> 
				<td id="borderBottom" >&nbsp;<?=$nicepay->m_ResultData["Carrier"]?>&nbsp;</td>
			  </tr>
			   <tr>
				<td width="100" height="30" id="borderBottom" class="thead01">휴대폰 번호</td> 
				<td id="borderBottom" >&nbsp;<?=$nicepay->m_ResultData["DstAddr"]?>&nbsp;</td>
			  </tr>
		  <?}else if($PayMethod == "VBANK"){?>
		       <tr>
				<td width="100" height="30" id="borderBottom" class="thead02">입금 은행 코드</td> 
				<td id="borderBottom" >&nbsp;<?=$nicepay->m_ResultData["VbankBankCode"]?>&nbsp;</td>
			  </tr>
			  <tr>
				<td width="100" height="30" id="borderBottom" class="thead02">입금 은행명</td> 
				<td id="borderBottom" >&nbsp;<?=$nicepay->m_ResultData["VbankBankName"]?>&nbsp;</td>
			  </tr>
			   <tr>
				<td width="100" height="30" id="borderBottom" class="thead01">입금 계좌</td> 
				<td id="borderBottom" >&nbsp;<?=$nicepay->m_ResultData["VbankNum"]?>&nbsp;</td>
			  </tr>
			  <tr>
				<td width="100" height="30" id="borderBottom" class="thead02">입금 기한</td> 
				<td id="borderBottom" >&nbsp;<?=$nicepay->m_ResultData["VbankExpDate"]?>&nbsp;</td>
			  </tr>

		  <?}?>


        </table></td>
        <td height="15">&nbsp;</td>
      </tr>
      <tr>
        <td height="15"></td>  <!--컨텐츠와 컨텐츠 사이 간격 15px-->
        <td >&nbsp;</td>
        <td>&nbsp;</td>
      </tr>
      <tr>
        <td></td>
        <td class="comment">테스트 아이디인경우 당일 오후 11시 30분에 취소됩니다.        
        </td>
        <td>&nbsp;</td>
      </tr>
      <tr>
        <td height="10"></td>  <!--하단여백 높이 10px -->
        <td >&nbsp;</td>
        <td>&nbsp;</td>
      </tr>  
    </table></td>
  </tr>
  <tr>
    <td><img src="images/bodyBottom.gif" /></td>
  </tr>
</table>
</body>
</html>

	
