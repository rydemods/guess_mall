<?php

/*******************************************************************************
* AGSCash_ing.php 으로부터 넘겨받을 데이터 
********************************************************************************/

$Retailer_id = trim($_POST["Retailer_id"]);		//상점아이디

$Ord_No = trim($_POST["Ord_No"]);				//주문번호

$Amtcash = trim($_POST["Amtcash"]);				//거래금액

$deal_won = trim($_POST["deal_won"]);			//공급가액

$Dealno = trim( $_POST["Dealno"] );			    //거래고유번호

$Cust_no = trim($_POST["Cust_no"]);				//회원아이디

$Cat_id = trim($_POST["Cat_id"]);				//단말기번호

$Alert_msg1 = trim($_POST["Alert_msg1"]);		//알림메세지1

$Alert_msg2 = trim($_POST["Alert_msg2"]);		//알림메세지2

$rResMsg = trim($_POST["rResMsg"]);		        //에러메시지

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

$Email = trim($_POST["Email"]);					//이메일주소

/***************************************************************************************************
* 상품의 상세정보(상품명, 상품갯수, 주문자명등)은 상점에서 처리를 해야함
****************************************************************************************************/
?>
<html>
<head>
<title>올더게이트</title>
<style type="text/css">
<!--
body { font-family:"돋움"; font-size:9pt; color:#000000; font-weight:normal; letter-spacing:0pt; line-height:180%; }
td { font-family:"돋움"; font-size:9pt; color:#000000; font-weight:normal; letter-spacing:0pt; line-height:180%; }
.clsright { padding-right:10px; text-align:right; }
.clsleft { padding-left:10px; text-align:left; }
-->
</style>
<script language=javascript> // 성공값이 y일경우엔 영수증팝업 띄움
<!--
function show_receipt() // 영수증 출력 
	{
		if("<?=$Success?>"== "y"){
	     	
   		   document.cash_pay.submit();
			}
		else
		{
			alert("해당하는 결제내역이 없습니다");
		}
	}
	//영수증 출력끝
-->
</script>
</head>
<body topmargin=0 leftmargin=0 rightmargin=0 bottommargin=0 >
<table border=0 width=100% height=100% cellpadding=0 cellspacing=0>
	<tr>
		<td align=center>
		<table width=400 border=0 cellpadding=0 cellspacing=0>
			<tr>
				<td><hr></td>
			</tr>
			<tr>
				<td class=clsleft>현금영수증처리결과</td>
			</tr>
			<tr>
				<td><hr></td>
			</tr>
			<tr>
				<td>
				<table width=400 border=0 cellpadding=0 cellspacing=0>
					<tr>
						<td class=clsright>결제종류 : </td>
						<td class=clsleft>
<?php
	if($Pay_kind == "cash-appr")
	{
		echo "현금영수증발행요청";
	}	
	else if($Pay_kind == "cash-cncl")
	{
		echo "현금영수증취소요청";
	}
	else if($Pay_kind == "cash-appr-temp")
	{
		echo "현금영수증임시저장(승인)요청";
	}
	else if($Pay_kind == "cash-cncl-temp")
	{
		echo "현금영수증임시저장(취소)요청";
	}
	else if($Pay_kind == "non-cash-appr")
	{
		echo "미등록상점현금영수증발행요청";
	}
	else if($Pay_kind == "non-cash-cncl")
	{
		echo "미등록상점현금영수증취소요청";
	}
?></td>
					<tr>
						<td class=clsright>거래자구분 : </td>
						<td class=clsleft>
<?php
	if($Gubun_cd == "01")
	{
		echo "소득공제용";
	}	
	else if($Gubun_cd == "02")
	{
		echo "사업자지출증빙용";
	}
?></td>
					</tr>
					<tr>
						<td class=clsright>결제방식 : </td>
						<td class=clsleft>
<?php
	if($Pay_type == "1")
	{
		echo "무통장입금";
	}	
	else if($Pay_type == "2")
	{
		echo "계좌이체";
	}
?></td>
					<tr>
						<td class=clsright>회원아이디 : </td>
						<td class=clsleft><?=$Cust_no?></td>
					</tr>
					<tr>
						<td class=clsright>원주문번호 : </td>
						<td class=clsleft><?=$Ord_No?></td>
					</tr>
					<tr>
						<td class=clsright>주문번호 : </td>
						<td class=clsleft><?=$Dealno?></td>
					</tr>
					<tr>
						<td class=clsright>상품명 : </td>
						<td class=clsleft><?=$prod_nm?></td>
					</tr>
					<tr>
						<td class=clsright>상품수량 : </td>
						<td class=clsleft><?=$prod_set?></td>
					</tr>
					</tr>
						<tr>
						<td class=clsright>결제금액 : </td>
						<td class=clsleft><?=$Amtcash?></td>
					</tr>
					<tr>
						<td class=clsright>공급가액 : </td>
						<td class=clsleft><?=$deal_won?></td>
					</tr>
					<tr>
						<td class=clsright>부가세 : </td>
						<td class=clsleft><?=$Amttex?></td>
					</tr>
					<tr>
						<td class=clsright>봉사료 : </td>
						<td class=clsleft><?=$Amtadd?></td>
					</tr>
					<tr>
						<td class=clsright>이메일 : </td>
						<td class=clsleft><?=$Email?></td>
					</tr>
					<tr>
						<td class=clsright>신분확인번호 : </td>
						<td class=clsleft><?=$Confirm_no?></td>
					</tr>
					<tr>
						<td class=clsright>승인번호 : </td>
						<td class=clsleft><?=$Adm_no?></td>
					</tr>
					<tr>
						<td class=clsright>원승인번호 : </td>
						<td class=clsleft><?=$Org_adm_no?></td>
					
					<tr>
						<td class=clsright>성공여부 : </td>
						<td class=clsleft><?=$Success?></td>
					</tr>
					<tr>
						<td class=clsright>응답메시지 : </td>
						<td class=clsleft><?=$rResMsg?></td>
					</tr>
					<tr>
						<td class=clsright>알림메시지1 : </td>
						<td class=clsleft><?=$Alert_msg1?></td>
					</tr>
					<tr>
						<td class=clsright>알림메시지2 : </td>
						<td class=clsleft><?=$Alert_msg2?></td>
					</tr>
					    <tr>
						<td class=clsright>영수증 :</td>
						<td class=clsleft><? if ($Pay_kind == "cash-appr" || $Pay_kind == "cash-cncl" || $Pay_kind == "non-cash-appr" || $Pay_kind == "non-cash-cncl")	{ ?>	<input type="button" value="영수증" onclick="javascript:show_receipt();"> <?}?></td>
					</tr>
				    <tr>
						<td colspan=2>&nbsp;</td>
					</tr>
							
				</table>
				</td>
			</tr>
			<tr>
				<td><hr></td>
			</tr>
			<tr>
				<td class=clsleft>Copyright AEGIS ENTERPRISE.Co.,Ltd. All rights reserved.</td>
			</tr>
		</table>
		</td>
	</tr>
</table>
<!---------------------영수증 출력을 위해 넘겨줄 데이터------------------------->
<form name=cash_pay method=post action=AGSCash_receipt.php 	target="_blank">
<input type=hidden name=Retailer_id value="<?=$Retailer_id?>">
<input type=hidden name=Ord_No value="<?=$Ord_No?>">
<input type=hidden name=Cust_no value="<?=$Cust_no?>">
<input type=hidden name=Adm_no value="<?=$Adm_no?>">
<input type=hidden name=Success value="<?=$Success?>">
<input type=hidden name=Resp_msq value="<?=$rResMsg?>">
<input type=hidden name=Alert_msg1 value="<?=$Alert_msg1?>">
<input type=hidden name=Alert_msg2 value="<?=$Alert_msg2?>">
<input type=hidden name=deal_won value="<?=$deal_won?>">
<input type=hidden name=Amttex value="<?=$Amttex?>">
<input type=hidden name=Amtadd value="<?=$Amtadd?>">
<input type=hidden name=Amtcash value="<?=$Amtcash?>">
<input type=hidden name=prod_nm value="<?=$prod_nm?>">
<input type=hidden name=prod_set value="<?=$prod_set?>">
<input type=hidden name=Gubun_cd value="<?=$Gubun_cd?>">
<input type=hidden name=Pay_kind value="<?=$Pay_kind?>">
<input type=hidden name=Confirm_no value="<?=$Confirm_no?>">
<input type=hidden name=Org_adm_no value="<?=$Org_adm_no?>">
</body>
</html>
