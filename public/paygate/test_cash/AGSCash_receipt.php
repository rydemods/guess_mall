<?
/************************************************************************************************************
/ AGSCash_result.php ���� �Ѱܹ��� ������ 
/************************************************************************************************************/


$Retailer_id = trim($_POST["Retailer_id"]);		//�������̵�

$Ord_No = trim($_POST["Ord_No"]);				//�ֹ���ȣ

$Amtcash = trim($_POST["Amtcash"]);				//�ŷ��ݾ�

$deal_won = trim($_POST["deal_won"]);			//���ް���

$Dealno = trim( $_POST["Dealno"] );			    //�ŷ�������ȣ

$Cust_no = trim($_POST["Cust_no"]);				//ȸ�����̵�

$Cat_id = trim($_POST["Cat_id"]);				//�ܸ����ȣ

$Resp_msg = trim($_POST["Resp_msg"]);			//����޽���

$Alert_msg1 = trim($_POST["Alert_msg1"]);		//�˸��޼���1

$Alert_msg2 = trim($_POST["Alert_msg2"]);		//�˸��޼���2

$Adm_no = trim($_POST["Adm_no"]);				//���ι�ȣ

$Amttex = trim($_POST["Amttex"]);				//�ΰ���ġ��

$Amtadd = trim($_POST["Amtadd"]);				//�����

$prod_nm = trim($_POST["prod_nm"]);				//��ǰ��

$prod_set = trim($_POST["prod_set"]);			//��ǰ����

$deal_won = trim($_POST["deal_won"]);	        //�ŷ��ݾ�

$Gubun_cd = trim($_POST["Gubun_cd"]);			//�ŷ��ڱ���    01.�ҵ������ 02.����������

$Confirm_no = trim($_POST["Confirm_no"]);		//�ź�Ȯ�ι�ȣ

$Pay_kind = trim($_POST["Pay_kind"]);			//��������

$Success = trim($_POST["Success"]);				//�������� y,n ���� ǥ��

$Pay_type = trim($_POST["Pay_type"]);	        //������� 1.�������ӱ� 2.������ü

$Org_adm_no = trim($_POST["Org_adm_no"]);	    //��ҽ� ���ι�ȣ


/*************************************************************************************
/ ��ǰ�� ������(��ǰ��, ��ǰ����, �ֹ��ڸ��)�� �������� ó���� �ؾ���
/*************************************************************************************/
?>

<HTML>
<HEAD>
<TITLE>���ݼҵ����������</TITLE>
<META HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=euc-kr">

<style type="text/css">
     
.font {Font-Family:"����,verdana"; FONT-SIZE:9pt ; color:#383434; TEXT-DECORATION: none; line-height: 14px}

.font02 {font-family: ����,verdana; font-size:14px; ; color:#02469A; TEXT-DECORATION: none;  font-weight: bold;}

.font03 {Font-Family:"����,verdana"; FONT-SIZE:14px; color:#383434; TEXT-DECORATION: none;  font-weight: bold;}

A:link {COLOR: #F75B09; TEXT-DECORATION: none}
A:visited {COLOR: #F75B09; TEXT-DECORATION: none}
A:hover {COLOR: #669E02; TEXT-DECORATION: underline}

.img 
{border:0px;}


.td_body01 {padding-left:5px; padding-top:3px; font-family: ����,verdana; font-size:12px; color:#515151; text-decoration:none;}

.td_title {font-family: ����,verdana; font-size:12px; color:#053961; text-decoration:none; border-top:solid 1px #B7CCDC; vertical-align:middle;}

.td_title02 {font-family: ����,verdana; font-size:12px; color:#053961; text-decoration:none; border-top:solid 1px #B7CCDC; border-right:solid 1px #B7CCDC; vertical-align:middle;}


.td_body02 {padding-left:10px; font-family: ����,verdana; font-size:12px; color:#2D3032; text-decoration:none; border-top:solid 1px #B7CCDC;  margin:0px; text-align:left; vertical-align:middle;}

.td_body03 {padding-left:10px; font-family: ����,verdana; font-size:12px; color:#2D3032; text-decoration:none; border-top:solid 1px #B7CCDC;  margin:0px;  vertical-align:middle;}


</style>
<script language=javascript>
<!--
//������ ���п� ���� �̹��� ǥ�� �ڹٽ�ũ��Ʈ
function show_receipt(){
  if("<%=Gubun_cd%>"=="01"){
       Display1.style.display = "";
	Display2.style.display = "none";
		}else {
      Display1.style.display = "none";
		Display2.style.display = "";
    }
   //������ â ũ�� ����
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
<!---------���ݿ����� ���п� ���� �̹��� ǥ��------>
	<TR id="Display1" STYLE="display:none;">
		<td><IMG SRC="images/cash_pay_01.gif"></td>
	</tr>
	<TR id="Display2" STYLE="display:'';">
		<td><IMG SRC="images/title_cash_pay_02.gif"></td>
	</tr>
<!---------���ݿ����� ���п� ���� �̹��� ǥ��------>
	<tr>
		<td align=center valign=top>
			<!-------------���� ���̺� --------------->
			<TABLE WIDTH=400 BORDER=0 CELLPADDING=0 CELLSPACING=0>
				<tr><td height=20></td></tr>

				<tr>
					<td>
						<table width="400" border="0" cellpadding="0" cellspacing="1" bgcolor="B7CCDC">
							 <tr>
								<td>
									<!-------------������ �� ���� ���̺�  --------------->
									<!-------------���������� �������� ����ص� �� ------>
									<table width="400" border="0" cellpadding="2" cellspacing="2" bgcolor="#FFFFFF">

										 <tr bgcolor="#FFFFFF"> 
											<td width="120" height="22" bgcolor="E3E9EC" class=font style="padding-left:10px;"><font color=053961>������</td>
											<td bgcolor="ECF2F6" class=font style="padding-left:10px;">�׽�Ʈ (��)  / www.test.co.kr</td>
										</tr>

										 <tr bgcolor="#FFFFFF"> 
											<td height="22" bgcolor="E3E9EC" class=font style="padding-left:10px;"><font color=053961>����ڵ�Ϲ�ȣ</td>
											<td bgcolor="ECF2F6" class=font style="padding-left:10px;">123-45-67890</td>
										</tr>

										 <tr bgcolor="#FFFFFF"> 
											<td height="22" bgcolor="E3E9EC" class=font style="padding-left:10px;"><font color=053961>��ǥ�ڸ�</td>
											<td bgcolor="ECF2F6" class=font style="padding-left:10px;">ȫ �� ��     </td>
										</tr>
										 <tr bgcolor="#FFFFFF"> 
											<td height="22" bgcolor="E3E9EC" class=font style="padding-left:10px;"><font color=053961>�� ��</td>
											<td bgcolor="ECF2F6" class=font style="padding-left:10px;">����� ������ �Ｚ�� 1����</td>
										</tr>
										 <tr bgcolor="#FFFFFF"> 
											<td height="20" bgcolor="E3E9EC" class=font style="padding-left:10px;"><font color=053961>��ǥ��ȭ</td>
											<td bgcolor="ECF2F6" class=font style="padding-left:10px;">(02) 999 - 99999 </td>
										</tr>
									
									</table>
								   <!-------------������ �� ���� ���̺� �� --------------->


								</td>
							</tr>
						</table>
					</td>
				</tr>

				<tr><td height=20></td></tr>

				<tr>
					<td>
						<!-------------��ǰ��, ����, �ݾ�, �հ� --------------->
						<table width="400" border="0" cellpadding="0" cellspacing="0">
							 <tr>
								<td height=2 colspan=3 bgcolor=B7CCDC></td>
							</tr>

							 <tr>
								<td width=35 height=27 class=td_title align=right style="padding-right:10px;"><img src="images/icon_arroe2.gif"></td>
								<td width=66 class=td_title02>��ǰ��</td>
								<td width=299 class=td_body02><?=$prod_nm?></td>
							</tr>

							 <tr>
								<td height=27 class=td_title align=right style="padding-right:10px;"><img src="images/icon_arroe2.gif"></td>
								<td class=td_title02>�� ��</td>
								<td class=td_body02><?=$prod_set?></td>
							</tr>

							 <tr>
								<td height=27 class=td_title align=right style="padding-right:10px;"><img src="images/icon_arroe2.gif"></td>
								<td class=td_title02>�� ��</td>
								<td class=td_body02><?=$Amtcash?></td>
							</tr>

							 <tr>
								<td height=27 class=td_title align=right style="padding-right:10px;"><img src="images/icon_arroe2.gif"></td>
								<td class=td_title02 valign=top>�� ��</td>
								<td class=td_body03 align=right style="padding-top:10px;padding-right:10px;padding-bottom:10px;">
<!------------------------------���ν� ��ҽ� �ݾ�ǥ�ù� �ٸ�------------------------->
<!------------------------------��ҽ� �ݾ� �տ� - ǥ�� ��) -5000 -------------------->

<? if ($Pay_kind == "cash-appr" || $Pay_kind == "non-cash-appr")	{ ?>	
									<table border="0" cellpadding="0" cellspacing="0">
										<tr>
											<td width="100" class=font>������ǰ���� :</td>
											<td align=right class=font03><b><?=$deal_won?></b></td>
										</tr>
										<tr>
											<td width="100" class=font>�ΰ���&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; :</td>
											<td align=right class=font03><b><?=$Amttex?></b></td>
										</tr>
											<tr>
											<td width="100" class=font>�����&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; :</td>
											<td align=right class=font03><b><?=$Amtadd?></b></td>
										</tr>
										<tr>
											<td colspan=2 align=right class=font03><b><?=$Amtcash?></b></font></td>
										</tr>
									</table>
	<?}  else 	{?>
	<!-------------------��ҽ�ǥ�� ----------------------------------->
								<table border="0" cellpadding="0" cellspacing="0">
										<tr>
											<td width="100" class=font>������ǰ���� :</td>
											<td align=right class=font03><b>- &nbsp;<?=$deal_won?></b></td>
										</tr>
										<tr>
											<td width="100" class=font>�ΰ���&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; :</td>
											<td align=right class=font03><b>- &nbsp;<?=$Amttex?></b></td>
										</tr>
											<tr>
											<td width="100" class=font>�����&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; :</td>
											<td align=right class=font03><b>- &nbsp;<?=$Amtadd?></b></td>
										</tr>
										<tr>
											<td colspan=2 align=right class=font03><b>- &nbsp;<?=$Amtcash?></b></font></td>
										</tr>
									</table>
<?}?>
<!------------------------------���ν� ��ҽ� �ݾ�ǥ�ù� �ٸ�------------------------->

								</td>
							</tr>

							 <tr>
								<td height=3 colspan=3 bgcolor=B7CCDC></td>
							</tr>
						</table>
						<!-------------��ǰ��, ����, �ݾ�, �հ�  ��-------------->
					</td>
				</tr>


				<tr><td height=20></td></tr>

				<tr>
					<td>
						<!-------------�ź�Ȯ�ι�ȣ ���̺� ����  --------------->
						<table width="400" border="0" cellpadding="0" cellspacing="0">
							 <tr>
								<td colspan=3 background="images/bg_dot.gif" height=1></td>
							</tr>

							 <tr>
								<td colspan=3 height=2 bgcolor=#ffffff></td>
							</tr>
<!------------------------------������ ���п� ���� �ź�Ȯ�ι�ȣ ǥ�� ----------------------------------------------------------->
<? if ($Gubun_cd == "01")	{ 
?>					 <tr bgcolor=#EFF2F4>
								<td width=30 height=30 align=center><img src="images/icon_nemo.gif"></td>
								<td><font class=font02>�ź�Ȯ�ι�ȣ :</font></td>
								<td align=right style="padding-right:10px;"><font class=font02>
							<?=$confirm_no = substr($Confirm_no,0,6);?>-*******</font></td>
							</tr>
<?}  else 	{?>					

	                  <tr bgcolor=#EFF2F4>
								<td width=30 height=30 align=center><img src="images/icon_nemo.gif"></td>
								<td><font class=font02>�ź�Ȯ�ι�ȣ :</font></td>
								<td align=right style="padding-right:10px;"><font class=font02>
								 <?=$confirm_no = substr($Confirm_no,0,2);?>-<?=$confirm_no =substr($Confirm_no,3,4);?>-*****</font></td>
							</tr>
<?}?>
<!------------------------------������ ���п� ���� �ź�Ȯ�ι�ȣ ǥ�� ----------------------------------------------------------->
<!------------------------------������ ����� ��ҿ� ���� ���� ǥ�� ---------------------------------------------------------->
							 <tr bgcolor=#EFF2F4>
								<td width=30 height=30 align=center><img src="images/icon_nemo.gif"></td>
								<td><font class=font02>
<?php
	if($Pay_kind == "cash-appr")
	{
		echo "���ݿ���������";
	}	
	else if($Pay_kind == "cash-cncl")
	{
		echo "���ݿ��������";
	}
	else if($Pay_kind == "cash-appr-temp")
	{
		echo "���ݿ������ӽ�����(����)";
	}
	else if($Pay_kind == "cash-cncl-temp")
	{
		echo "���ݿ������ӽ�����(���)";
	}
	else if($Pay_kind == "non-cash-appr")
	{
		echo "�̵�ϻ������ݿ���������";
	}
	else if($Pay_kind == "non-cash-cncl")
	{
		echo "�̵�ϻ������ݿ��������";
	}
?> :</td>
<!------------------------------������ ����� ��ҿ� ���� ���� ǥ�� ---------------------------------------------------------->
								<td align=right style="padding-right:10px;"><font class=font02><?=$Adm_no?></font></td>
							</tr>

							 <tr>
								<td colspan=3 height=2 bgcolor=#ffffff></td>
							</tr>

							 <tr>
								<td colspan=3 background="images/bg_dot.gif" height=1></td>
							</tr>
						</table>
						<!-------------�ź�Ȯ�ι�ȣ ���̺� ��  --------------->
					</td>
				</tr>

				<tr>
					<td>
						<!-------------���ݿ����� ���� �ȳ�  --------------->
						<table width="400" border="0" cellpadding="0" cellspacing="0">

						<tr>
							<td width=10><img src="images/icon_arrow.gif"></td>
							<td width=375 height=28 class=td_body01 align=left><b>���ݿ����� ���� &nbsp;&nbsp;<?=$Alert_msg1?>  , <?=$Alert_msg2?></b></td>
						</tr>

						</table>
						<!-------------���ݿ����� ���� �ȳ�  ��--------------->
					</td>
				</tr>

				<tr><td height=20></td></tr>
			</table>

			<!----------���� ���̺� �� ------------>


		</td>
	</tr>

	<tr>
		<td bgcolor=#E4E4E5 height=40 align=right>
						
						<!-------------�ϴ� �μ��ϱ� â�ݱ� ��ư  --------------->

						<table border="0" cellpadding="0" cellspacing="0">
							 <tr>
								<td><IMG SRC="images/btn_print.gif" onclick="javascript:jsPrint();"></td>
								<td width=20></td>
								<td><IMG SRC="images/btn_close.gif" onclick="self.close();"></td>
								<td width=20></td>
							</tr>
						</table>

					   <!-------------�ϴ� �μ��ϱ� â�ݱ� ��ư �� --------------->
		</td>
	</tr>
</table>

</BODY>
</HTML>