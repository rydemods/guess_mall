<?
/************************************************************************************************************
/ AGSCash_result.php 에서 넘겨받을 데이터 
/************************************************************************************************************/


$Retailer_id = trim($_POST["Retailer_id"]);		//상점아이디

$Ord_No = trim($_POST["Ord_No"]);				//주문번호

$Amtcash = trim($_POST["Amtcash"]);				//거래금액

$deal_won = trim($_POST["deal_won"]);			//공급가액

$Dealno = trim( $_POST["Dealno"] );			    //거래고유번호

$Cust_no = trim($_POST["Cust_no"]);				//회원아이디

$Cat_id = trim($_POST["Cat_id"]);				//단말기번호

$Resp_msg = trim($_POST["Resp_msg"]);			//응답메시지

$Alert_msg1 = trim($_POST["Alert_msg1"]);		//알림메세지1

$Alert_msg2 = trim($_POST["Alert_msg2"]);		//알림메세지2

$Adm_no = trim($_POST["Adm_no"]);				//승인번호

$Amttex = trim($_POST["Amttex"]);				//부가가치세

$Amtadd = trim($_POST["Amtadd"]);				//봉사료

$prod_nm = trim($_POST["prod_nm"]);				//상품명

$prod_set = trim($_POST["prod_set"]);			//상품갯수

$deal_won = trim($_POST["deal_won"]);	        //거래금액

$Gubun_cd = trim($_POST["Gubun_cd"]);			//거래자구분    01.소득공제용 02.지출증빙용

$Confirm_no = trim($_POST["Confirm_no"]);		//신분확인번호

$Pay_kind = trim($_POST["Pay_kind"]);			//결제종류

$Success = trim($_POST["Success"]);				//성공여부 y,n 으로 표시

$Pay_type = trim($_POST["Pay_type"]);	        //결제방식 1.무통장임금 2.계좌이체

$Org_adm_no = trim($_POST["Org_adm_no"]);	    //취소시 승인번호


/*************************************************************************************
/ 상품의 상세정보(상품명, 상품갯수, 주문자명등)은 상점에서 처리를 해야함
/*************************************************************************************/
?>

<HTML>
<HEAD>
<TITLE>현금소득공제영수증</TITLE>
<META HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=euc-kr">

<style type="text/css">
     
.font {Font-Family:"돋움,verdana"; FONT-SIZE:9pt ; color:#383434; TEXT-DECORATION: none; line-height: 14px}

.font02 {font-family: 돋움,verdana; font-size:14px; ; color:#02469A; TEXT-DECORATION: none;  font-weight: bold;}

.font03 {Font-Family:"돋움,verdana"; FONT-SIZE:14px; color:#383434; TEXT-DECORATION: none;  font-weight: bold;}

A:link {COLOR: #F75B09; TEXT-DECORATION: none}
A:visited {COLOR: #F75B09; TEXT-DECORATION: none}
A:hover {COLOR: #669E02; TEXT-DECORATION: underline}

.img 
{border:0px;}


.td_body01 {padding-left:5px; padding-top:3px; font-family: 돋움,verdana; font-size:12px; color:#515151; text-decoration:none;}

.td_title {font-family: 돋움,verdana; font-size:12px; color:#053961; text-decoration:none; border-top:solid 1px #B7CCDC; vertical-align:middle;}

.td_title02 {font-family: 돋움,verdana; font-size:12px; color:#053961; text-decoration:none; border-top:solid 1px #B7CCDC; border-right:solid 1px #B7CCDC; vertical-align:middle;}


.td_body02 {padding-left:10px; font-family: 돋움,verdana; font-size:12px; color:#2D3032; text-decoration:none; border-top:solid 1px #B7CCDC;  margin:0px; text-align:left; vertical-align:middle;}

.td_body03 {padding-left:10px; font-family: 돋움,verdana; font-size:12px; color:#2D3032; text-decoration:none; border-top:solid 1px #B7CCDC;  margin:0px;  vertical-align:middle;}


</style>
<script language=javascript>
<!--
//영수증 구분에 따른 이미지 표시 자바스크립트
function show_receipt(){
  if("<%=Gubun_cd%>"=="01"){
       Display1.style.display = "";
	Display2.style.display = "none";
		}else {
      Display1.style.display = "none";
		Display2.style.display = "";
    }
   //영수증 창 크기 설정
   resizeTo(470,740)
}

-->
</script>
<SCRIPT LANGUAGE="JavaScript">
<!--
function jsPrint(){
	window.print();
}
//-->
</SCRIPT>

</HEAD>

<BODY BGCOLOR=#FFFFFF LEFTMARGIN=0 TOPMARGIN=0 MARGINWIDTH=0 MARGINHEIGHT=0 onload="javascript:show_receipt();">
<TABLE WIDTH=440 BORDER=0 CELLPADDING=0 CELLSPACING=0>
<!---------현금영수증 구분에 따른 이미지 표시------>
	<TR id="Display1" STYLE="display:none;">
		<td><IMG SRC="images/cash_pay_01.gif"></td>
	</tr>
	<TR id="Display2" STYLE="display:'';">
		<td><IMG SRC="images/title_cash_pay_02.gif"></td>
	</tr>
<!---------현금영수증 구분에 따른 이미지 표시------>
	<tr>
		<td align=center valign=top>
			<!-------------본문 테이블 --------------->
			<TABLE WIDTH=400 BORDER=0 CELLPADDING=0 CELLSPACING=0>
				<tr><td height=20></td></tr>

				<tr>
					<td>
						<table width="400" border="0" cellpadding="0" cellspacing="1" bgcolor="B7CCDC">
							 <tr>
								<td>
									<!-------------상점명 및 정보 테이블  --------------->
									<!-------------상점정보는 고정으로 사용해되 됨 ------>
									<table width="400" border="0" cellpadding="2" cellspacing="2" bgcolor="#FFFFFF">

										 <tr bgcolor="#FFFFFF"> 
											<td width="120" height="22" bgcolor="E3E9EC" class=font style="padding-left:10px;"><font color=053961>상점명</td>
											<td bgcolor="ECF2F6" class=font style="padding-left:10px;">테스트 (주)  / www.test.co.kr</td>
										</tr>

										 <tr bgcolor="#FFFFFF"> 
											<td height="22" bgcolor="E3E9EC" class=font style="padding-left:10px;"><font color=053961>사업자등록번호</td>
											<td bgcolor="ECF2F6" class=font style="padding-left:10px;">123-45-67890</td>
										</tr>

										 <tr bgcolor="#FFFFFF"> 
											<td height="22" bgcolor="E3E9EC" class=font style="padding-left:10px;"><font color=053961>대표자명</td>
											<td bgcolor="ECF2F6" class=font style="padding-left:10px;">홍 길 동     </td>
										</tr>
										 <tr bgcolor="#FFFFFF"> 
											<td height="22" bgcolor="E3E9EC" class=font style="padding-left:10px;"><font color=053961>주 소</td>
											<td bgcolor="ECF2F6" class=font style="padding-left:10px;">서울시 강남구 삼성동 1번지</td>
										</tr>
										 <tr bgcolor="#FFFFFF"> 
											<td height="20" bgcolor="E3E9EC" class=font style="padding-left:10px;"><font color=053961>대표전화</td>
											<td bgcolor="ECF2F6" class=font style="padding-left:10px;">(02) 999 - 99999 </td>
										</tr>
									
									</table>
								   <!-------------상점명 및 정보 테이블 끝 --------------->


								</td>
							</tr>
						</table>
					</td>
				</tr>

				<tr><td height=20></td></tr>

				<tr>
					<td>
						<!-------------상품명, 수량, 금액, 합계 --------------->
						<table width="400" border="0" cellpadding="0" cellspacing="0">
							 <tr>
								<td height=2 colspan=3 bgcolor=B7CCDC></td>
							</tr>

							 <tr>
								<td width=35 height=27 class=td_title align=right style="padding-right:10px;"><img src="images/icon_arroe2.gif"></td>
								<td width=66 class=td_title02>상품명</td>
								<td width=299 class=td_body02><?=$prod_nm?></td>
							</tr>

							 <tr>
								<td height=27 class=td_title align=right style="padding-right:10px;"><img src="images/icon_arroe2.gif"></td>
								<td class=td_title02>수 량</td>
								<td class=td_body02><?=$prod_set?></td>
							</tr>

							 <tr>
								<td height=27 class=td_title align=right style="padding-right:10px;"><img src="images/icon_arroe2.gif"></td>
								<td class=td_title02>금 액</td>
								<td class=td_body02><?=$Amtcash?></td>
							</tr>

							 <tr>
								<td height=27 class=td_title align=right style="padding-right:10px;"><img src="images/icon_arroe2.gif"></td>
								<td class=td_title02 valign=top>합 계</td>
								<td class=td_body03 align=right style="padding-top:10px;padding-right:10px;padding-bottom:10px;">
<!------------------------------승인시 취소시 금액표시법 다름------------------------->
<!------------------------------취소시 금액 앞에 - 표시 예) -5000 -------------------->

<? if ($Pay_kind == "cash-appr" || $Pay_kind == "non-cash-appr")	{ ?>	
									<table border="0" cellpadding="0" cellspacing="0">
										<tr>
											<td width="100" class=font>과세물품가액 :</td>
											<td align=right class=font03><b><?=$deal_won?></b></td>
										</tr>
										<tr>
											<td width="100" class=font>부가세&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; :</td>
											<td align=right class=font03><b><?=$Amttex?></b></td>
										</tr>
											<tr>
											<td width="100" class=font>봉사료&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; :</td>
											<td align=right class=font03><b><?=$Amtadd?></b></td>
										</tr>
										<tr>
											<td colspan=2 align=right class=font03><b><?=$Amtcash?></b></font></td>
										</tr>
									</table>
	<?}  else 	{?>
	<!-------------------취소시표기 ----------------------------------->
								<table border="0" cellpadding="0" cellspacing="0">
										<tr>
											<td width="100" class=font>과세물품가액 :</td>
											<td align=right class=font03><b>- &nbsp;<?=$deal_won?></b></td>
										</tr>
										<tr>
											<td width="100" class=font>부가세&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; :</td>
											<td align=right class=font03><b>- &nbsp;<?=$Amttex?></b></td>
										</tr>
											<tr>
											<td width="100" class=font>봉사료&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; :</td>
											<td align=right class=font03><b>- &nbsp;<?=$Amtadd?></b></td>
										</tr>
										<tr>
											<td colspan=2 align=right class=font03><b>- &nbsp;<?=$Amtcash?></b></font></td>
										</tr>
									</table>
<?}?>
<!------------------------------승인시 취소시 금액표시법 다름------------------------->

								</td>
							</tr>

							 <tr>
								<td height=3 colspan=3 bgcolor=B7CCDC></td>
							</tr>
						</table>
						<!-------------상품명, 수량, 금액, 합계  끝-------------->
					</td>
				</tr>


				<tr><td height=20></td></tr>

				<tr>
					<td>
						<!-------------신분확인번호 테이블 시작  --------------->
						<table width="400" border="0" cellpadding="0" cellspacing="0">
							 <tr>
								<td colspan=3 background="images/bg_dot.gif" height=1></td>
							</tr>

							 <tr>
								<td colspan=3 height=2 bgcolor=#ffffff></td>
							</tr>
<!------------------------------영수증 구분에 따른 신분확인번호 표시 ----------------------------------------------------------->
<? if ($Gubun_cd == "01")	{ 
?>					 <tr bgcolor=#EFF2F4>
								<td width=30 height=30 align=center><img src="images/icon_nemo.gif"></td>
								<td><font class=font02>신분확인번호 :</font></td>
								<td align=right style="padding-right:10px;"><font class=font02>
							<?=$confirm_no = substr($Confirm_no,0,6);?>-*******</font></td>
							</tr>
<?}  else 	{?>					

	                  <tr bgcolor=#EFF2F4>
								<td width=30 height=30 align=center><img src="images/icon_nemo.gif"></td>
								<td><font class=font02>신분확인번호 :</font></td>
								<td align=right style="padding-right:10px;"><font class=font02>
								 <?=$confirm_no = substr($Confirm_no,0,2);?>-<?=$confirm_no =substr($Confirm_no,3,4);?>-*****</font></td>
							</tr>
<?}?>
<!------------------------------영수증 구분에 따른 신분확인번호 표시 ----------------------------------------------------------->
<!------------------------------영수증 발행과 취소에 따른 구분 표시 ---------------------------------------------------------->
							 <tr bgcolor=#EFF2F4>
								<td width=30 height=30 align=center><img src="images/icon_nemo.gif"></td>
								<td><font class=font02>
<?php
	if($Pay_kind == "cash-appr")
	{
		echo "현금영수증승인";
	}	
	else if($Pay_kind == "cash-cncl")
	{
		echo "현금영수증취소";
	}
	else if($Pay_kind == "cash-appr-temp")
	{
		echo "현금영수증임시저장(승인)";
	}
	else if($Pay_kind == "cash-cncl-temp")
	{
		echo "현금영수증임시저장(취소)";
	}
	else if($Pay_kind == "non-cash-appr")
	{
		echo "미등록상점현금영수증승인";
	}
	else if($Pay_kind == "non-cash-cncl")
	{
		echo "미등록상점현금영수증취소";
	}
?> :</td>
<!------------------------------영수증 발행과 취소에 따른 구분 표시 ---------------------------------------------------------->
								<td align=right style="padding-right:10px;"><font class=font02><?=$Adm_no?></font></td>
							</tr>

							 <tr>
								<td colspan=3 height=2 bgcolor=#ffffff></td>
							</tr>

							 <tr>
								<td colspan=3 background="images/bg_dot.gif" height=1></td>
							</tr>
						</table>
						<!-------------신분확인번호 테이블 끝  --------------->
					</td>
				</tr>

				<tr>
					<td>
						<!-------------현금영수증 문의 안내  --------------->
						<table width="400" border="0" cellpadding="0" cellspacing="0">

						<tr>
							<td width=10><img src="images/icon_arrow.gif"></td>
							<td width=375 height=28 class=td_body01 align=left><b>현금영수증 문의 &nbsp;&nbsp;<?=$Alert_msg1?>  , <?=$Alert_msg2?></b></td>
						</tr>

						</table>
						<!-------------현금영수증 문의 안내  끝--------------->
					</td>
				</tr>

				<tr><td height=20></td></tr>
			</table>

			<!----------본문 테이블 끝 ------------>


		</td>
	</tr>

	<tr>
		<td bgcolor=#E4E4E5 height=40 align=right>
						
						<!-------------하단 인쇄하기 창닫기 버튼  --------------->

						<table border="0" cellpadding="0" cellspacing="0">
							 <tr>
								<td><IMG SRC="images/btn_print.gif" onclick="javascript:jsPrint();"></td>
								<td width=20></td>
								<td><IMG SRC="images/btn_close.gif" onclick="self.close();"></td>
								<td width=20></td>
							</tr>
						</table>

					   <!-------------하단 인쇄하기 창닫기 버튼 끝 --------------->
		</td>
	</tr>
</table>

</BODY>
</HTML>